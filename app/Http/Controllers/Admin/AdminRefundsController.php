<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\RefundRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Admin: Refunds Management – dashboard stats, list, add refund, view, approve, reject, revert, reports.
 */
class AdminRefundsController extends Controller
{
    /**
     * Dashboard: summary stats (total refunds, pending, completed, success rate) + paginated list.
     */
    public function dashboard(Request $request): JsonResponse
    {
        $stats = $this->computeStats();
        $perPage = max(1, min(100, (int) $request->get('per_page', 10)));
        $query = RefundRequest::with(['company', 'employer.user'])->orderByDesc('created_at');

        $this->applyFilters($query, $request);
        $paginator = $query->paginate($perPage);
        $items = $paginator->getCollection()->map(fn ($r) => $this->refundToItem($r));

        return response()->json([
            'stats' => $stats,
            'refunds' => $items,
            'pagination' => [
                'current_page' => $paginator->currentPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'last_page' => $paginator->lastPage(),
            ],
        ]);
    }

    /**
     * List companies for the Add Refund modal dropdown (id + name). Call when opening the modal.
     */
    public function companies(): JsonResponse
    {
        $companies = Company::orderBy('name')->get(['id', 'name']);
        return response()->json([
            'companies' => $companies->map(fn ($c) => ['id' => $c->id, 'name' => $c->name])->values()->all(),
        ]);
    }

    /**
     * List refund requests (filtered, paginated). Use when only the table is needed.
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = max(1, min(100, (int) $request->get('per_page', 10)));
        $query = RefundRequest::with(['company', 'employer.user'])->orderByDesc('created_at');
        $this->applyFilters($query, $request);
        $paginator = $query->paginate($perPage);
        $items = $paginator->getCollection()->map(fn ($r) => $this->refundToItem($r));

        return response()->json([
            'refunds' => $items,
            'pagination' => [
                'current_page' => $paginator->currentPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'last_page' => $paginator->lastPage(),
            ],
        ]);
    }

    /**
     * View single refund request details.
     */
    public function show(int $id): JsonResponse
    {
        $refund = RefundRequest::with(['company', 'employer.user', 'processor'])->find($id);
        if (!$refund) {
            return response()->json(['message' => 'Refund request not found'], 404);
        }
        return response()->json([
            'refund' => $this->refundToDetail($refund),
        ]);
    }

    /**
     * Add refund (admin creates a new refund request on behalf of employer).
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'employer_id' => 'nullable|integer',
            'company_id' => 'nullable|exists:companies,id',
            'amount' => 'required|numeric|min:0',
            'currency' => 'nullable|string|max:3',
            'coins_equivalent' => 'nullable|integer|min:0',
            'payment_method' => 'nullable|string|in:card,mobile_money,bank',
            'type' => 'required|string|in:job,advertisement,coins,tender',
            'reason' => 'nullable|string|max:2000',
        ]);

        $validated['request_id'] = RefundRequest::generateRequestId();
        $validated['status'] = 'pending';
        $validated['currency'] = $validated['currency'] ?? 'SCR';

        $refund = RefundRequest::create($validated);
        $refund->load(['company', 'employer.user']);

        return response()->json([
            'message' => 'Refund request created',
            'refund' => $this->refundToItem($refund),
        ], 201);
    }

    /**
     * Approve a pending refund (moves to approved/processing).
     */
    public function approve(Request $request, int $id): JsonResponse
    {
        $refund = RefundRequest::find($id);
        if (!$refund) {
            return response()->json(['message' => 'Refund request not found'], 404);
        }
        if (!in_array($refund->status, ['pending'], true)) {
            return response()->json(['message' => 'Only pending refunds can be approved'], 422);
        }

        $refund->update([
            'status' => 'approved',
            'processed_at' => now(),
            'processed_by' => $request->user()?->id,
            'admin_notes' => $request->input('admin_notes', $refund->admin_notes),
        ]);
        $refund->load(['company', 'employer.user']);

        return response()->json([
            'message' => 'Refund approved',
            'refund' => $this->refundToItem($refund),
        ]);
    }

    /**
     * Reject a pending refund.
     */
    public function reject(Request $request, int $id): JsonResponse
    {
        $refund = RefundRequest::find($id);
        if (!$refund) {
            return response()->json(['message' => 'Refund request not found'], 404);
        }
        if (!in_array($refund->status, ['pending'], true)) {
            return response()->json(['message' => 'Only pending refunds can be rejected'], 422);
        }

        $refund->update([
            'status' => 'rejected',
            'processed_at' => now(),
            'processed_by' => $request->user()?->id,
            'admin_notes' => $request->input('admin_notes', $refund->admin_notes),
        ]);
        $refund->load(['company', 'employer.user']);

        return response()->json([
            'message' => 'Refund rejected',
            'refund' => $this->refundToItem($refund),
        ]);
    }

    /**
     * Revert: set approved/processing back to pending (e.g. cancel or resubmit).
     */
    public function revert(Request $request, int $id): JsonResponse
    {
        $refund = RefundRequest::find($id);
        if (!$refund) {
            return response()->json(['message' => 'Refund request not found'], 404);
        }
        if (!in_array($refund->status, ['approved', 'processing'], true)) {
            return response()->json(['message' => 'Only approved or processing refunds can be reverted'], 422);
        }

        $refund->update([
            'status' => 'pending',
            'processed_at' => null,
            'processed_by' => null,
        ]);
        $refund->load(['company', 'employer.user']);

        return response()->json([
            'message' => 'Refund reverted to pending',
            'refund' => $this->refundToItem($refund),
        ]);
    }

    /**
     * View reports: aggregated refund stats (e.g. for export or reports page).
     */
    public function reports(Request $request): JsonResponse
    {
        $stats = $this->computeStats();
        $byStatus = RefundRequest::selectRaw('status, COUNT(*) as count, COALESCE(SUM(amount), 0) as total_amount')
            ->groupBy('status')
            ->get()
            ->keyBy('status');
        $byType = RefundRequest::selectRaw('type, COUNT(*) as count, COALESCE(SUM(amount), 0) as total_amount')
            ->groupBy('type')
            ->get();

        return response()->json([
            'stats' => $stats,
            'by_status' => $byStatus->map(fn ($r) => [
                'status' => $r->status,
                'count' => (int) $r->count,
                'total_amount' => (float) $r->total_amount,
            ])->values()->all(),
            'by_type' => $byType->map(fn ($r) => [
                'type' => $r->type,
                'count' => (int) $r->count,
                'total_amount' => (float) $r->total_amount,
            ])->values()->all(),
        ]);
    }

    private function computeStats(): array
    {
        $totalAmount = (float) RefundRequest::whereIn('status', ['completed', 'approved'])->sum('amount');
        $pending = RefundRequest::where('status', 'pending');
        $pendingCount = (clone $pending)->count();
        $pendingAmount = (float) (clone $pending)->sum('amount');
        $completedCount = RefundRequest::whereIn('status', ['completed', 'approved'])->count();
        $rejectedCount = RefundRequest::where('status', 'rejected')->count();
        $totalProcessed = $completedCount + $rejectedCount;
        $successRate = $totalProcessed > 0 ? round(($completedCount / $totalProcessed) * 100, 1) : 0;

        return [
            'total_refunds_amount' => $totalAmount,
            'pending_count' => $pendingCount,
            'pending_amount' => $pendingAmount,
            'completed_count' => $completedCount,
            'success_rate_percent' => $successRate,
            'currency' => 'SCR',
        ];
    }

    private function applyFilters($query, Request $request): void
    {
        if ($request->filled('status') && $request->get('status') !== 'all') {
            $query->where('status', $request->get('status'));
        }
        if ($request->filled('search')) {
            $s = $request->get('search');
            $query->where(function ($q) use ($s) {
                $q->where('request_id', 'like', '%' . $s . '%')
                    ->orWhereHas('company', fn ($q2) => $q2->where('name', 'like', '%' . $s . '%')->orWhere('email', 'like', '%' . $s . '%'))
                    ->orWhereHas('employer.company', fn ($q2) => $q2->where('name', 'like', '%' . $s . '%')->orWhere('email', 'like', '%' . $s . '%'));
            });
        }
    }

    private function refundToItem(RefundRequest $r): array
    {
        $company = $r->company;
        $employer = $r->employer;
        $companyName = $company?->name ?? '—';
        $contactEmail = $company?->email ?? $employer?->user?->email ?? '—';

        return [
            'id' => $r->id,
            'request_id' => $r->request_id,
            'employer_name' => $companyName,
            'employer_email' => $contactEmail,
            'amount' => (float) $r->amount,
            'currency' => $r->currency ?? 'SCR',
            'coins_equivalent' => $r->coins_equivalent ? (int) $r->coins_equivalent : null,
            'payment_method' => $r->payment_method,
            'payment_method_label' => $this->paymentMethodLabel($r->payment_method),
            'type' => $r->type,
            'type_label' => $this->typeLabel($r->type),
            'status' => $r->status,
            'date' => $r->created_at?->format('n/j/Y g:i:s A'),
            'created_at' => $r->created_at?->toIso8601String(),
        ];
    }

    private function refundToDetail(RefundRequest $r): array
    {
        $item = $this->refundToItem($r);
        $item['reason'] = $r->reason;
        $item['admin_notes'] = $r->admin_notes;
        $item['processed_at'] = $r->processed_at?->toIso8601String();
        $item['processed_by'] = $r->processor ? ['id' => $r->processor->id, 'name' => $r->processor->name] : null;
        $item['company_id'] = $r->company_id;
        $item['employer_id'] = $r->employer_id;
        return $item;
    }

    private function paymentMethodLabel(?string $method): string
    {
        return match ($method) {
            'card' => 'Card',
            'mobile_money' => 'Mobile Money',
            'bank' => 'Bank',
            default => $method ?? '—',
        };
    }

    private function typeLabel(?string $type): string
    {
        return match ($type) {
            'job' => 'Job',
            'advertisement' => 'Advertisement',
            'coins' => 'Coins',
            'tender' => 'Tender',
            default => $type ?? '—',
        };
    }
}
