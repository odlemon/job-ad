<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminActivity;
use App\Models\TenderAd;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Admin: dashboard (stats + list), show, approve, reject, request edits.
 */
class AdminTenderController extends Controller
{
    /**
     * Dashboard: stats for low cards, status counts for tabs, and paginated tender list.
     */
    public function dashboard(Request $request): JsonResponse
    {
        $baseQuery = TenderAd::query();

        // Stats for the six low cards
        $totalTenderAds = (clone $baseQuery)->count();
        $pendingApproval = (clone $baseQuery)->where('status', 'pending_approval')->count();
        $activeTenders = (clone $baseQuery)->where('status', 'active')->count();
        $flagged = (clone $baseQuery)->where('status', 'flagged')->count();
        $expired = (clone $baseQuery)->where(function ($q) {
            $q->where('status', 'expired')
                ->orWhere(function ($q2) {
                    $q2->whereIn('status', ['active', 'pending_approval'])->whereNotNull('submission_deadline')->where('submission_deadline', '<', now()->toDateString());
                });
        })->count();
        $totalBudgetValue = (clone $baseQuery)->selectRaw('COALESCE(SUM(COALESCE(budget_max, budget_min, amount, 0)), 0) as total')->value('total') ?? 0;
        $applicationsTotal = (clone $baseQuery)->sum('applications_count');

        $stats = [
            'total_tender_ads' => $totalTenderAds,
            'pending_approval' => $pendingApproval,
            'active_tenders' => $activeTenders,
            'flagged' => $flagged,
            'expired' => $expired,
            'total_budget_value' => (float) $totalBudgetValue,
            'applications' => (int) $applicationsTotal,
        ];

        // Status counts for filter tabs (same counts, keyed for UI)
        $status_counts = [
            'pending_approval' => $pendingApproval,
            'approved' => $activeTenders,
            'flagged' => $flagged,
            'expired' => $expired,
            'all_tenders' => $totalTenderAds,
        ];

        // Paginated list with filters
        $perPage = max(1, min(100, (int) $request->get('per_page', 15)));
        $query = TenderAd::with(['category', 'creator'])->orderByDesc('created_at');

        $statusFilter = $request->get('status', 'all_tenders');
        if ($statusFilter && $statusFilter !== 'all_tenders') {
            if ($statusFilter === 'approved') {
                $query->where('status', 'active');
            } elseif ($statusFilter === 'expired') {
                $query->where(function ($q) {
                    $q->where('status', 'expired')
                        ->orWhere(function ($q2) {
                            $q2->whereIn('status', ['active', 'pending_approval'])
                                ->whereNotNull('submission_deadline')
                                ->where('submission_deadline', '<', now()->toDateString());
                        });
                });
            } else {
                $query->where('status', $statusFilter);
            }
        }

        if ($request->filled('search')) {
            $s = $request->get('search');
            $query->where(function ($q) use ($s) {
                $q->where('title', 'like', '%' . $s . '%')
                    ->orWhere('entity_name', 'like', '%' . $s . '%')
                    ->orWhere('reference_number', 'like', '%' . $s . '%');
            });
        }

        $paginator = $query->paginate($perPage);
        $tenders = $paginator->getCollection()->map(fn (TenderAd $t) => $this->tenderToDashboardItem($t));

        return response()->json([
            'stats' => $stats,
            'status_counts' => $status_counts,
            'tenders' => $tenders,
            'pagination' => [
                'current_page' => $paginator->currentPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'last_page' => $paginator->lastPage(),
            ],
        ]);
    }

    public function index(Request $request): JsonResponse
    {
        $perPage = max(1, min(100, (int) $request->get('per_page', 15)));
        $query = TenderAd::with(['category', 'creator'])->orderByDesc('created_at');

        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }
        if ($request->filled('search')) {
            $s = $request->get('search');
            $query->where(function ($q) use ($s) {
                $q->where('title', 'like', '%' . $s . '%')
                    ->orWhere('entity_name', 'like', '%' . $s . '%')
                    ->orWhere('reference_number', 'like', '%' . $s . '%');
            });
        }

        $paginator = $query->paginate($perPage);
        $items = $paginator->getCollection()->map(fn (TenderAd $t) => $this->tenderToArray($t));

        return response()->json([
            'tenders' => $items,
            'pagination' => [
                'current_page' => $paginator->currentPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'last_page' => $paginator->lastPage(),
            ],
        ]);
    }

    public function show(int $id): JsonResponse
    {
        $tender = TenderAd::with(['category', 'creator'])->find($id);
        if (!$tender) {
            return response()->json(['message' => 'Tender not found'], 404);
        }
        return response()->json(['data' => $this->tenderToArray($tender)]);
    }

    public function approve(Request $request, int $id): JsonResponse
    {
        $tender = TenderAd::find($id);
        if (!$tender) {
            return response()->json(['message' => 'Tender not found'], 404);
        }
        if ($tender->status === 'active') {
            return response()->json(['message' => 'Tender is already active'], 422);
        }

        $tender->update(['status' => 'active']);
        $this->logActivity($request, 'tender_approved', 'Approved tender: ' . $tender->title . ' (ID ' . $tender->id . ')');

        return response()->json([
            'message' => 'Tender approved',
            'data' => $this->tenderToArray($tender->fresh(['category', 'creator'])),
        ]);
    }

    public function reject(Request $request, int $id): JsonResponse
    {
        $tender = TenderAd::find($id);
        if (!$tender) {
            return response()->json(['message' => 'Tender not found'], 404);
        }
        if ($tender->status === 'rejected') {
            return response()->json(['message' => 'Tender is already rejected'], 422);
        }

        $reason = $request->input('reason');

        $tender->update(['status' => 'rejected']);
        $this->logActivity($request, 'tender_rejected', 'Rejected tender: ' . $tender->title . ' (ID ' . $tender->id . ')' . ($reason ? ' — Reason: ' . $reason : ''));

        return response()->json([
            'message' => 'Tender rejected',
            'data' => $this->tenderToArray($tender->fresh(['category', 'creator'])),
        ]);
    }

    /**
     * Request edits: set status to changes_requested and optionally store admin message.
     */
    public function requestEdits(Request $request, int $id): JsonResponse
    {
        $tender = TenderAd::find($id);
        if (!$tender) {
            return response()->json(['message' => 'Tender not found'], 404);
        }

        $message = $request->input('message');
        $updates = ['status' => 'changes_requested'];
        if (\Schema::hasColumn($tender->getTable(), 'edit_request_message')) {
            $updates['edit_request_message'] = $message;
        }
        $tender->update($updates);
        $this->logActivity($request, 'tender_request_edits', 'Requested edits for tender: ' . $tender->title . ' (ID ' . $tender->id . ')' . ($message ? ' — ' . $message : ''));

        return response()->json([
            'message' => 'Edit request sent',
            'data' => $this->tenderToArray($tender->fresh(['category', 'creator'])),
        ]);
    }

    private function logActivity(Request $request, string $action, string $description): void
    {
        $user = $request->user();
        if ($user && class_exists(AdminActivity::class)) {
            AdminActivity::create([
                'admin_id' => $user->id,
                'action' => $action,
                'description' => $description,
            ]);
        }
    }

    /**
     * Shape for dashboard list row (low cards + list item with engagement stats and action hints).
     */
    private function tenderToDashboardItem(TenderAd $t): array
    {
        $deadline = $t->submission_deadline ?? $t->end_date;
        return [
            'id' => $t->id,
            'title' => $t->title,
            'reference_number' => $t->reference_number ? 'Ref: ' . $t->reference_number : null,
            'status' => $t->status,
            'display_status' => $this->displayStatus($t->status),
            'is_featured' => (bool) ($t->is_featured ?? false),
            'organization' => $t->entity_name ?? $t->procuring_entity,
            'location' => $t->location,
            'budget_min' => $t->budget_min ? (float) $t->budget_min : null,
            'budget_max' => $t->budget_max ? (float) $t->budget_max : null,
            'currency' => $t->currency ?? 'USD',
            'budget_display' => $this->formatBudgetRange($t),
            'deadline' => $deadline?->format('Y-m-d'),
            'views_count' => (int) $t->views_count,
            'applications_count' => (int) $t->applications_count,
            'shares_count' => (int) ($t->shares_count ?? 0),
            'saved_count' => (int) ($t->saved_count ?? 0),
        ];
    }

    private function displayStatus(string $status): string
    {
        return match ($status) {
            'pending_approval' => 'pending approval',
            'changes_requested' => 'changes requested',
            'active' => 'approved',
            'rejected' => 'rejected',
            'expired' => 'expired',
            'draft' => 'draft',
            default => $status,
        };
    }

    private function formatBudgetRange(TenderAd $t): ?string
    {
        if ($t->budget_min && $t->budget_max) {
            $c = $t->currency ?? 'USD';
            return $c . ' ' . number_format((float) $t->budget_min, 0) . ' - ' . number_format((float) $t->budget_max, 0);
        }
        if ($t->amount) {
            $c = $t->currency ?? 'USD';
            return $c . ' ' . number_format((float) $t->amount, 0);
        }
        return null;
    }

    private function tenderToArray(TenderAd $t): array
    {
        return [
            'id' => $t->id,
            'title' => $t->title,
            'slug' => $t->slug,
            'reference_number' => $t->reference_number,
            'tender_type' => $t->tender_type,
            'category_id' => $t->category_id,
            'category' => $t->category ? ['id' => $t->category->id, 'name' => $t->category->name] : null,
            'status' => $t->status,

            'overview' => [
                'description' => $t->description,
                'summary' => $t->summary,
                'scope_of_work' => $t->scope_of_work,
                'requirements' => $t->requirements ?? [],
            ],

            'tender_information' => [
                'budget_range' => $t->budget_min && $t->budget_max
                    ? '$' . number_format((float) $t->budget_min) . ' - $' . number_format((float) $t->budget_max)
                    : ($t->amount ? '$' . number_format((float) $t->amount) : null),
                'budget_min' => $t->budget_min ? (float) $t->budget_min : null,
                'budget_max' => $t->budget_max ? (float) $t->budget_max : null,
                'currency' => $t->currency,
                'sector' => $t->sector,
                'procuring_entity' => $t->procuring_entity,
                'entity_name' => $t->entity_name,
                'country_region' => $t->country_region,
                'location' => $t->location,
            ],

            'submission_details' => [
                'submission_method' => $t->submission_method,
                'required_documents' => $t->required_documents ?? [],
                'eligibility_criteria' => $t->eligibility_criteria ?? [],
            ],

            'attachments' => $t->attachments ?? [],

            'important_dates' => [
                'published_date' => $t->published_date?->toDateString(),
                'clarification_deadline' => $t->clarification_deadline?->toDateString(),
                'submission_deadline' => $t->submission_deadline?->toDateString(),
                'start_date' => $t->start_date?->toDateString(),
                'end_date' => $t->end_date?->toDateString(),
            ],

            'performance' => [
                'views_count' => (int) $t->views_count,
                'applications_count' => (int) $t->applications_count,
            ],

            'created_by' => $t->created_by,
            'creator' => $t->creator ? ['id' => $t->creator->id, 'name' => $t->creator->name] : null,
            'created_at' => $t->created_at?->toIso8601String(),
            'updated_at' => $t->updated_at?->toIso8601String(),
        ];
    }
}
