<?php

namespace App\Http\Controllers\Employer;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\StreamedResponse;

class EmployerInvoiceController extends Controller
{
    /** Used only to backfill tax for older payments that have no tax_amount. */
    private const LEGACY_TAX_RATE = 0.09;

    private function employerOrFail()
    {
        $employer = Auth::user()?->employer;
        if (!$employer || !$employer->company_id) {
            return null;
        }

        return $employer;
    }

    private function invoiceNumber(Payment $payment): string
    {
        $year = optional($payment->paid_at ?? $payment->created_at)->format('Y') ?? date('Y');

        return 'INV-' . $year . '-' . str_pad((string) $payment->id, 3, '0', STR_PAD_LEFT);
    }

    private function paymentMethodLabel(Payment $payment, $employer): string
    {
        if (in_array($payment->payment_method, ['credit_card', 'card'], true)) {
            $brand = $payment->card_brand ?: $employer->billing_card_brand;
            $last4 = $payment->card_last4 ?: $employer->billing_card_last4;
            if ($brand && $last4) {
                return $brand . ' **** ' . $last4;
            }

            return 'Credit Card';
        }
        if ($payment->payment_method === 'lpo') {
            return 'LPO';
        }
        if ($payment->payment_method === 'coin') {
            return 'Coins';
        }
        if ($payment->payment_method === 'bank_transfer') {
            return 'Bank Transfer';
        }

        return $payment->payment_method ? ucwords(str_replace('_', ' ', $payment->payment_method)) : '—';
    }

    private function statusLabel(Payment $payment): string
    {
        return match ($payment->status) {
            'completed' => 'paid',
            'pending' => 'pending',
            'failed' => 'failed',
            default => $payment->status ?: 'pending',
        };
    }

    private function moneyParts(Payment $payment): array
    {
        $total = round((float) $payment->amount, 2);
        $currency = $payment->currency ?: 'SCR';

        if ($payment->tax_amount !== null) {
            $tax = round((float) $payment->tax_amount, 2);
            $base = round($total - $tax, 2);
        } else {
            $base = round($total / (1 + self::LEGACY_TAX_RATE), 2);
            $tax = round($total - $base, 2);
        }

        return [
            'amount' => $base,
            'tax' => $tax,
            'total' => $total,
            'currency' => $currency,
        ];
    }

    private function mapInvoice(Payment $payment, $employer): array
    {
        $money = $this->moneyParts($payment);
        $date = optional($payment->paid_at ?? $payment->created_at)->format('Y-m-d');

        return [
            'id' => $payment->id,
            'invoice_id' => $this->invoiceNumber($payment),
            'transaction_id' => $payment->transaction_id,
            'date' => $date,
            'description' => $payment->description ?: 'Payment',
            'payment_method' => $this->paymentMethodLabel($payment, $employer),
            'amount' => $money['amount'],
            'tax' => $money['tax'],
            'total' => $money['total'],
            'currency' => $money['currency'],
            'status' => $this->statusLabel($payment),
            'raw_status' => $payment->status,
            'coins_amount' => $payment->coins_amount,
            'category' => $payment->category,
        ];
    }

    private function baseQuery($employer)
    {
        return Payment::query()
            ->where('company_id', $employer->company_id)
            ->whereIn('status', ['completed', 'pending', 'failed'])
            ->orderByDesc('paid_at')
            ->orderByDesc('id');
    }

    private function applyFilters($query, Request $request)
    {
        $search = trim((string) $request->query('q', ''));
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('transaction_id', 'like', '%' . $search . '%')
                    ->orWhere('description', 'like', '%' . $search . '%')
                    ->orWhere('payer_name', 'like', '%' . $search . '%')
                    ->orWhere('id', $search);

                if (preg_match('/INV-\d+-0*(\d+)/i', $search, $m)) {
                    $q->orWhere('id', (int) $m[1]);
                }
            });
        }

        $range = $request->query('range', 'all');
        if ($range === '30') {
            $query->where(function ($q) {
                $q->where('paid_at', '>=', now()->copy()->subDays(30))
                    ->orWhere(function ($q2) {
                        $q2->whereNull('paid_at')->where('created_at', '>=', now()->copy()->subDays(30));
                    });
            });
        } elseif ($range === '90') {
            $query->where(function ($q) {
                $q->where('paid_at', '>=', now()->copy()->subDays(90))
                    ->orWhere(function ($q2) {
                        $q2->whereNull('paid_at')->where('created_at', '>=', now()->copy()->subDays(90));
                    });
            });
        } elseif ($range === 'year') {
            $query->where(function ($q) {
                $q->whereYear('paid_at', now()->year)
                    ->orWhere(function ($q2) {
                        $q2->whereNull('paid_at')->whereYear('created_at', now()->year);
                    });
            });
        }

        return $query;
    }

    public function index(Request $request)
    {
        $employer = $this->employerOrFail();
        if (!$employer) {
            return redirect()->route('employer.dashboard')
                ->with('error', 'Please set up your company profile first.');
        }

        $allQuery = $this->baseQuery($employer);
        $totalInvoices = (clone $allQuery)->count();
        $completedQuery = (clone $allQuery)->where('status', 'completed');
        $totalSpent = (float) (clone $completedQuery)->sum('amount');
        $lastPayment = (clone $completedQuery)->first();
        $lastPaymentTotal = $lastPayment ? (float) $lastPayment->amount : 0;
        $currency = $lastPayment?->currency
            ?: ((clone $allQuery)->value('currency'))
            ?: 'SCR';

        $filtered = $this->applyFilters($this->baseQuery($employer), $request)->get();
        $invoices = $filtered->map(fn (Payment $p) => $this->mapInvoice($p, $employer));

        $hasCard = filled($employer->billing_card_brand) && filled($employer->billing_card_last4);

        return view('employer.invoices.index', [
            'invoices' => $invoices,
            'totalInvoices' => $totalInvoices,
            'totalSpent' => $totalSpent,
            'lastPaymentTotal' => $lastPaymentTotal,
            'currency' => $currency,
            'filters' => [
                'q' => $request->query('q', ''),
                'range' => $request->query('range', 'all'),
            ],
            'hasCard' => $hasCard,
            'cardBrand' => $employer->billing_card_brand,
            'cardLast4' => $employer->billing_card_last4,
            'cardExp' => $employer->billing_card_exp,
        ]);
    }

    public function export(Request $request): StreamedResponse
    {
        $employer = $this->employerOrFail();
        abort_unless($employer, 403);

        $rows = $this->applyFilters($this->baseQuery($employer), $request)->get()
            ->map(fn (Payment $p) => $this->mapInvoice($p, $employer));

        $filename = 'invoices-' . now()->format('Y-m-d') . '.csv';

        return response()->streamDownload(function () use ($rows) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Invoice ID', 'Date', 'Description', 'Payment Method', 'Amount', 'Tax', 'Total', 'Currency', 'Status']);
            foreach ($rows as $row) {
                fputcsv($out, [
                    $row['invoice_id'],
                    $row['date'],
                    $row['description'],
                    $row['payment_method'],
                    number_format($row['amount'], 2, '.', ''),
                    number_format($row['tax'], 2, '.', ''),
                    number_format($row['total'], 2, '.', ''),
                    $row['currency'],
                    $row['status'],
                ]);
            }
            fclose($out);
        }, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }

    public function show(int $id)
    {
        $employer = $this->employerOrFail();
        abort_unless($employer, 403);

        $payment = $this->baseQuery($employer)->where('id', $id)->firstOrFail();
        $invoice = $this->mapInvoice($payment, $employer);

        if (request()->expectsJson() || request()->wantsJson()) {
            return response()->json(['invoice' => $invoice]);
        }

        return view('employer.invoices.pdf', [
            'invoice' => $invoice,
            'companyName' => $employer->company_name ?? ($employer->company->name ?? 'Company'),
        ]);
    }

    public function download(int $id)
    {
        $employer = $this->employerOrFail();
        abort_unless($employer, 403);

        $payment = $this->baseQuery($employer)->where('id', $id)->firstOrFail();
        $invoice = $this->mapInvoice($payment, $employer);

        $pdf = Pdf::loadView('employer.invoices.pdf', [
            'invoice' => $invoice,
            'companyName' => $employer->company_name ?? ($employer->company->name ?? 'Company'),
        ]);

        return $pdf->download($invoice['invoice_id'] . '.pdf');
    }

    public function updatePaymentMethod(Request $request)
    {
        $employer = $this->employerOrFail();
        abort_unless($employer, 403);

        $validated = $request->validate([
            'billing_card_brand' => 'required|string|max:32',
            'billing_card_last4' => 'required|digits:4',
            'billing_card_exp' => ['required', 'string', 'regex:/^(0[1-9]|1[0-2])\/\d{4}$/'],
        ]);

        $employer->billing_card_brand = $validated['billing_card_brand'];
        $employer->billing_card_last4 = $validated['billing_card_last4'];
        $employer->billing_card_exp = $validated['billing_card_exp'];
        $employer->save();

        if ($request->expectsJson() || $request->wantsJson()) {
            return response()->json([
                'message' => 'Payment method updated.',
                'card' => [
                    'brand' => $employer->billing_card_brand,
                    'last4' => $employer->billing_card_last4,
                    'exp' => $employer->billing_card_exp,
                ],
            ]);
        }

        return redirect()->route('employer.invoices.index')
            ->with('success', 'Payment method updated.');
    }
}
