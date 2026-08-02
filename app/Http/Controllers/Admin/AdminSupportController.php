<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JobReport;
use App\Models\SupportTicket;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

/**
 * Admin support tickets + job reports API for the external admin frontend.
 */
class AdminSupportController extends Controller
{
    public function __construct(
        private NotificationService $notificationService
    ) {
    }

    public function ticketsIndex(Request $request): JsonResponse
    {
        $query = SupportTicket::with(['user:id,name,email,user_type', 'assignee:id,name,email'])
            ->latest();

        if ($status = $request->get('status')) {
            if ($status !== 'all') {
                $query->where('status', $status);
            }
        }
        if ($priority = $request->get('priority')) {
            if ($priority !== 'all') {
                $query->where('priority', $priority);
            }
        }
        if ($search = trim((string) $request->get('q', ''))) {
            $query->where(function ($q) use ($search) {
                $q->where('subject', 'like', "%{$search}%")
                    ->orWhere('message', 'like', "%{$search}%")
                    ->orWhere('id', $search)
                    ->orWhereHas('user', function ($uq) use ($search) {
                        $uq->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        $perPage = min(50, max(5, (int) $request->get('per_page', 20)));
        $page = $query->paginate($perPage);

        $openCount = SupportTicket::whereIn('status', ['open', 'in_progress'])->count();
        $highCount = SupportTicket::where('priority', 'high')->whereIn('status', ['open', 'in_progress'])->count();

        return response()->json([
            'summary' => [
                'open' => $openCount,
                'high_priority' => $highCount,
                'total' => SupportTicket::count(),
            ],
            'pagination' => [
                'current_page' => $page->currentPage(),
                'per_page' => $page->perPage(),
                'total' => $page->total(),
                'last_page' => $page->lastPage(),
            ],
            'tickets' => collect($page->items())->map(fn (SupportTicket $t) => $this->formatTicket($t))->values(),
        ]);
    }

    public function ticketsShow(int $id): JsonResponse
    {
        $ticket = SupportTicket::with(['user:id,name,email,user_type', 'assignee:id,name,email'])->findOrFail($id);

        return response()->json(['ticket' => $this->formatTicket($ticket, true)]);
    }

    public function ticketsUpdate(Request $request, int $id): JsonResponse
    {
        $ticket = SupportTicket::with('user')->findOrFail($id);
        $validated = $request->validate([
            'status' => 'nullable|in:open,in_progress,resolved,closed',
            'priority' => 'nullable|in:low,medium,high',
            'admin_response' => 'nullable|string|max:5000',
            'assigned_to' => 'nullable|integer|exists:users,id',
        ]);

        $previousResponse = $ticket->admin_response;
        $previousStatus = $ticket->status;

        if (array_key_exists('status', $validated) && $validated['status']) {
            $ticket->status = $validated['status'];
            if (in_array($validated['status'], ['resolved', 'closed'], true)) {
                $ticket->resolved_at = $ticket->resolved_at ?: now();
            } elseif (in_array($validated['status'], ['open', 'in_progress'], true)) {
                $ticket->resolved_at = null;
            }
        }
        if (array_key_exists('priority', $validated) && $validated['priority']) {
            $ticket->priority = $validated['priority'];
        }
        if (array_key_exists('admin_response', $validated)) {
            $ticket->admin_response = $validated['admin_response'];
        }
        if (array_key_exists('assigned_to', $validated)) {
            $ticket->assigned_to = $validated['assigned_to'];
        } elseif (! $ticket->assigned_to) {
            $ticket->assigned_to = Auth::id();
        }

        $ticket->save();
        $ticket->load(['user:id,name,email,user_type', 'assignee:id,name,email']);

        $responseChanged = array_key_exists('admin_response', $validated)
            && filled($ticket->admin_response)
            && $ticket->admin_response !== $previousResponse;
        $statusChanged = $ticket->status !== $previousStatus;

        if (($responseChanged || $statusChanged) && $ticket->user) {
            $msg = $responseChanged
                ? 'Support replied to your ticket #'.$ticket->id.': '.$ticket->subject
                : 'Your support ticket #'.$ticket->id.' is now '.str_replace('_', ' ', $ticket->status);

            $this->notificationService->create([
                'user_id' => $ticket->user_id,
                'type' => 'support_ticket_update',
                'title' => 'Support ticket update',
                'message' => $msg,
                'data' => [
                    'ticket_id' => $ticket->id,
                    'status' => $ticket->status,
                    'type' => 'support_ticket_update',
                ],
            ]);
        }

        return response()->json([
            'message' => 'Ticket updated',
            'ticket' => $this->formatTicket($ticket, true),
        ]);
    }

    public function reportsIndex(Request $request): JsonResponse
    {
        $query = JobReport::with([
            'user:id,name,email',
            'jobAdvertisement:id,title,company_id,status',
            'jobAdvertisement.company:id,name',
        ])->latest();

        if ($status = $request->get('status')) {
            if ($status !== 'all' && Schema::hasColumn('job_reports', 'status')) {
                $query->where('status', $status);
            }
        }

        $perPage = min(50, max(5, (int) $request->get('per_page', 20)));
        $page = $query->paginate($perPage);

        return response()->json([
            'pagination' => [
                'current_page' => $page->currentPage(),
                'per_page' => $page->perPage(),
                'total' => $page->total(),
                'last_page' => $page->lastPage(),
            ],
            'reports' => collect($page->items())->map(fn (JobReport $r) => $this->formatReport($r))->values(),
        ]);
    }

    public function reportsUpdate(Request $request, int $id): JsonResponse
    {
        $report = JobReport::with(['user:id,name,email', 'jobAdvertisement.company'])->findOrFail($id);
        $validated = $request->validate([
            'status' => 'nullable|in:pending,reviewing,resolved,dismissed',
            'admin_notes' => 'nullable|string|max:5000',
        ]);

        if (Schema::hasColumn('job_reports', 'status') && ! empty($validated['status'])) {
            $report->status = $validated['status'];
        }
        if (Schema::hasColumn('job_reports', 'admin_notes') && array_key_exists('admin_notes', $validated)) {
            $report->admin_notes = $validated['admin_notes'];
        }
        $report->save();

        return response()->json([
            'message' => 'Report updated',
            'report' => $this->formatReport($report->fresh(['user:id,name,email', 'jobAdvertisement.company'])),
        ]);
    }

    private function formatTicket(SupportTicket $t, bool $detailed = false): array
    {
        $data = [
            'id' => $t->id,
            'subject' => $t->subject,
            'status' => $t->status,
            'priority' => $t->priority,
            'channel' => $t->channel,
            'created_at' => optional($t->created_at)->toIso8601String(),
            'updated_at' => optional($t->updated_at)->toIso8601String(),
            'resolved_at' => optional($t->resolved_at)->toIso8601String(),
            'user' => $t->user ? [
                'id' => $t->user->id,
                'name' => $t->user->name,
                'email' => $t->user->email,
                'user_type' => $t->user->user_type,
            ] : null,
            'assigned_to' => $t->assigned_to,
            'assignee' => $t->assignee ? [
                'id' => $t->assignee->id,
                'name' => $t->assignee->name,
                'email' => $t->assignee->email,
            ] : null,
            'has_response' => filled($t->admin_response),
        ];

        if ($detailed) {
            $data['message'] = $t->message;
            $data['admin_response'] = $t->admin_response;
        } else {
            $data['message_preview'] = \Illuminate\Support\Str::limit($t->message, 140);
        }

        return $data;
    }

    private function formatReport(JobReport $r): array
    {
        $job = $r->jobAdvertisement;

        return [
            'id' => $r->id,
            'category' => $r->category,
            'reason' => $r->reason,
            'details' => $r->details,
            'status' => $r->status ?? 'pending',
            'admin_notes' => $r->admin_notes,
            'created_at' => optional($r->created_at)->toIso8601String(),
            'user' => $r->user ? [
                'id' => $r->user->id,
                'name' => $r->user->name,
                'email' => $r->user->email,
            ] : null,
            'job' => $job ? [
                'id' => $job->id,
                'title' => $job->title,
                'status' => $job->status,
                'company' => $job->company->name ?? null,
            ] : null,
        ];
    }
}
