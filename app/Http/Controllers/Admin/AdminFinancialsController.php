<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Admin: Financials Management – dashboard stats, revenue by category, and transactions list.
 */
class AdminFinancialsController extends Controller
{
    /**
     * Dashboard: summary stats (total revenue, this month, pending) + revenue by category + all transactions (paginated).
     */
    public function dashboard(Request $request): JsonResponse
    {
        $now = now();
        $startOfMonth = $now->copy()->startOfMonth();
        $endOfMonth = $now->copy()->endOfMonth();

        // Total revenue all time (completed only)
        $totalRevenueAllTime = (float) Payment::where('status', 'completed')->sum('amount');

        // This month revenue (completed, paid_at or created_at in current month)
        $thisMonthRevenue = (float) Payment::where('status', 'completed')
            ->where(function ($q) use ($startOfMonth, $endOfMonth) {
                $q->whereBetween('paid_at', [$startOfMonth, $endOfMonth])
                    ->orWhere(function ($q2) use ($startOfMonth, $endOfMonth) {
                        $q2->whereNull('paid_at')->whereBetween('created_at', [$startOfMonth, $endOfMonth]);
                    });
            })
            ->sum('amount');

        // Pending payments
        $pendingQuery = Payment::where('status', 'pending');
        $pendingAmount = (float) (clone $pendingQuery)->sum('amount');
        $pendingCount = (clone $pendingQuery)->count();

        $stats = [
            'total_revenue_all_time' => $totalRevenueAllTime,
            'this_month_revenue' => $thisMonthRevenue,
            'pending_payments' => $pendingAmount,
            'pending_count' => $pendingCount,
            'currency' => 'SCR',
        ];

        // Revenue by category: total (all time) and this month per category
        $categories = ['job_ads', 'tender_ads', 'website_ads', 'course_ads', 'coins', 'lpo'];
        $revenueByCategory = [];
        foreach ($categories as $cat) {
            $total = (float) Payment::where('category', $cat)->where('status', 'completed')->sum('amount');
            $thisMonth = (float) Payment::where('category', $cat)->where('status', 'completed')
                ->where(function ($q) use ($startOfMonth, $endOfMonth) {
                    $q->whereBetween('paid_at', [$startOfMonth, $endOfMonth])
                        ->orWhere(function ($q2) use ($startOfMonth, $endOfMonth) {
                            $q2->whereNull('paid_at')->whereBetween('created_at', [$startOfMonth, $endOfMonth]);
                        });
                })
                ->sum('amount');
            $revenueByCategory[] = [
                'category' => $cat,
                'label' => $this->categoryLabel($cat),
                'total' => $total,
                'this_month' => $thisMonth,
            ];
        }

        // All transactions (paginated)
        $perPage = max(1, min(100, (int) $request->get('per_page', 10)));
        $transactions = Payment::orderByDesc('created_at')->paginate($perPage);
        $items = $transactions->getCollection()->map(fn ($p) => $this->paymentToTransactionItem($p));

        return response()->json([
            'stats' => $stats,
            'revenue_by_category' => $revenueByCategory,
            'transactions' => $items,
            'pagination' => [
                'current_page' => $transactions->currentPage(),
                'per_page' => $transactions->perPage(),
                'total' => $transactions->total(),
                'last_page' => $transactions->lastPage(),
            ],
        ]);
    }

    /**
     * Transactions list, optionally filtered by category (job_ads, tender_ads, website_ads, course_ads, coins, lpo).
     */
    public function transactions(Request $request): JsonResponse
    {
        $perPage = max(1, min(100, (int) $request->get('per_page', 10)));
        $query = Payment::orderByDesc('created_at');

        $type = $request->get('type');
        if ($type && in_array($type, Payment::CATEGORIES, true)) {
            $query->where('category', $type);
        }

        if ($request->filled('search')) {
            $s = $request->get('search');
            $query->where(function ($q) use ($s) {
                $q->where('transaction_id', 'like', '%' . $s . '%')
                    ->orWhere('payer_name', 'like', '%' . $s . '%')
                    ->orWhere('description', 'like', '%' . $s . '%');
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }

        $paginator = $query->paginate($perPage);
        $items = $paginator->getCollection()->map(fn ($p) => $this->paymentToTransactionItem($p));

        return response()->json([
            'transactions' => $items,
            'pagination' => [
                'current_page' => $paginator->currentPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'last_page' => $paginator->lastPage(),
            ],
        ]);
    }

    private function categoryLabel(string $category): string
    {
        return match ($category) {
            'job_ads' => 'Job Ads',
            'tender_ads' => 'Tender Ads',
            'website_ads' => 'Website Ads',
            'course_ads' => 'Course Ads',
            'coins' => 'Coins',
            'lpo' => 'LPO (Local Purchase Order)',
            default => $category,
        };
    }

    private function paymentMethodLabel(string $method): string
    {
        return match ($method) {
            'credit_card' => 'Credit Card',
            'bank_transfer' => 'Bank Transfer',
            'lpo' => 'LPO',
            'coin' => 'Coins',
            default => $method ?? '—',
        };
    }

    private function paymentToTransactionItem(Payment $p): array
    {
        $date = $p->paid_at ?? $p->created_at;
        return [
            'id' => $p->id,
            'transaction_id' => $p->transaction_id,
            'category' => $p->category,
            'category_label' => $this->categoryLabel($p->category),
            'payer_name' => $p->payer_name,
            'description' => $p->description,
            'payment_method' => $p->payment_method,
            'payment_method_label' => $this->paymentMethodLabel($p->payment_method ?? ''),
            'amount' => (float) $p->amount,
            'currency' => $p->currency ?? 'SCR',
            'status' => $p->status,
            'date' => $date?->format('Y-m-d'),
            'time' => $date?->format('h:i A'),
            'datetime' => $date?->toIso8601String(),
        ];
    }
}
