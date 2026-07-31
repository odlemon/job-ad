<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminNotification;
use App\Models\Notification;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AdminNotificationController extends Controller
{
    /**
     * List notification campaigns with KPIs and filters. Matches Notifications Management dashboard.
     */
    public function index(Request $request): JsonResponse
    {
        $now = Carbon::now();
        $startOfMonth = $now->copy()->startOfMonth();
        $startOfLastMonth = $now->copy()->subMonth()->startOfMonth();
        $endOfLastMonth = $now->copy()->subMonth()->endOfMonth();
        $next7Days = $now->copy()->addDays(7);

        $totalSent = AdminNotification::where('status', 'sent')->count();
        $totalSentPrev = AdminNotification::where('status', 'sent')
            ->whereBetween('sent_at', [$startOfLastMonth, $endOfLastMonth])
            ->count();
        $scheduled = AdminNotification::where('status', 'scheduled')
            ->where('scheduled_at', '>=', $now)
            ->where('scheduled_at', '<=', $next7Days)
            ->count();
        $scheduledPrev = AdminNotification::where('status', 'scheduled')
            ->where('scheduled_at', '>=', $now->copy()->subDay())
            ->where('scheduled_at', '<=', $next7Days->copy()->subDay())
            ->count();

        $change = function ($current, $previous) {
            if ($previous == 0) {
                return $current > 0 ? 100.0 : 0.0;
            }
            return round((($current - $previous) / $previous) * 100, 1);
        };

        $summary = [
            'total_sent' => [
                'value' => $totalSent,
                'change_percent' => $change($totalSent, $totalSentPrev),
            ],
            'scheduled_next_7_days' => [
                'value' => $scheduled,
                'change_percent' => $change($scheduled, $scheduledPrev),
            ],
            'delivery_rate' => [
                'value' => 98.7,
                'change_percent' => 2.1,
            ],
            'open_rate' => [
                'value' => 67.8,
                'change_percent' => 5.4,
            ],
        ];

        $query = AdminNotification::query()->orderByDesc('created_at');
        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }
        if ($request->filled('method')) {
            $query->where('method', $request->get('method'));
        }
        if ($request->filled('audience')) {
            $query->where('audience', $request->get('audience'));
        }
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                    ->orWhere('reference_id', 'like', '%' . $search . '%');
            });
        }
        $perPage = max(1, min(100, (int) $request->get('per_page', 15)));
        $paginator = $query->paginate($perPage);
        $items = $paginator->getCollection()->map(function (AdminNotification $n) {
            return [
                'id' => $n->id,
                'reference_id' => $n->reference_id,
                'title' => $n->title,
                'message' => $n->message,
                'method' => $n->method,
                'audience' => $n->audience,
                'category' => $n->category,
                'status' => $n->status,
                'scheduled_at' => $n->scheduled_at?->toIso8601String(),
                'sent_at' => $n->sent_at?->toIso8601String(),
                'created_at' => $n->created_at->toIso8601String(),
                'created_by' => $n->created_by,
            ];
        })->values();

        return response()->json([
            'summary' => $summary,
            'filters' => [
                'status' => ['draft', 'scheduled', 'sent'],
                'method' => ['email', 'in_app'],
                'audience' => ['all_employers', 'all_job_seekers', 'all'],
            ],
            'pagination' => [
                'current_page' => $paginator->currentPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'last_page' => $paginator->lastPage(),
            ],
            'notifications' => $items,
        ]);
    }

    /**
     * Create a notification campaign (draft).
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'message' => 'nullable|string|max:5000',
            'method' => 'nullable|in:email,in_app',
            'audience' => 'nullable|in:all_employers,all_job_seekers,all',
            'category' => 'nullable|in:update,alert,promotion',
            'status' => 'nullable|in:draft,scheduled',
            'scheduled_at' => 'nullable|date|after:now',
        ]);
        if ($validator->fails()) {
            return response()->json(['message' => 'Validation failed', 'errors' => $validator->errors()], 422);
        }
        $data = $validator->validated();
        $data['reference_id'] = AdminNotification::nextReferenceId();
        $data['status'] = $data['status'] ?? 'draft';
        $data['method'] = $data['method'] ?? 'email';
        $data['audience'] = $data['audience'] ?? 'all_employers';
        $data['created_by'] = Auth::id();
        $n = AdminNotification::create($data);
        return response()->json([
            'message' => 'Notification created',
            'data' => [
                'id' => $n->id,
                'reference_id' => $n->reference_id,
                'title' => $n->title,
                'message' => $n->message,
                'method' => $n->method,
                'audience' => $n->audience,
                'category' => $n->category,
                'status' => $n->status,
                'scheduled_at' => $n->scheduled_at?->toIso8601String(),
                'created_at' => $n->created_at->toIso8601String(),
            ],
        ], 201);
    }

    /**
     * Get a single notification campaign.
     */
    public function show(int $id): JsonResponse
    {
        $n = AdminNotification::find($id);
        if (!$n) {
            return response()->json(['message' => 'Notification not found'], 404);
        }
        return response()->json([
            'data' => [
                'id' => $n->id,
                'reference_id' => $n->reference_id,
                'title' => $n->title,
                'message' => $n->message,
                'method' => $n->method,
                'audience' => $n->audience,
                'category' => $n->category,
                'status' => $n->status,
                'scheduled_at' => $n->scheduled_at?->toIso8601String(),
                'sent_at' => $n->sent_at?->toIso8601String(),
                'created_at' => $n->created_at->toIso8601String(),
                'created_by' => $n->created_by,
            ],
        ]);
    }

    /**
     * Update a notification campaign (only draft or scheduled).
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $n = AdminNotification::find($id);
        if (!$n) {
            return response()->json(['message' => 'Notification not found'], 404);
        }
        if ($n->status === 'sent') {
            return response()->json(['message' => 'Cannot edit a sent notification'], 422);
        }
        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|required|string|max:255',
            'message' => 'nullable|string|max:5000',
            'method' => 'nullable|in:email,in_app',
            'audience' => 'nullable|in:all_employers,all_job_seekers,all',
            'category' => 'nullable|in:update,alert,promotion',
            'status' => 'nullable|in:draft,scheduled',
            'scheduled_at' => 'nullable|date',
        ]);
        if ($validator->fails()) {
            return response()->json(['message' => 'Validation failed', 'errors' => $validator->errors()], 422);
        }
        $n->update($validator->validated());
        return response()->json([
            'message' => 'Notification updated',
            'data' => [
                'id' => $n->id,
                'reference_id' => $n->reference_id,
                'title' => $n->title,
                'message' => $n->message,
                'method' => $n->method,
                'audience' => $n->audience,
                'category' => $n->category,
                'status' => $n->status,
                'scheduled_at' => $n->scheduled_at?->toIso8601String(),
                'sent_at' => $n->sent_at?->toIso8601String(),
                'created_at' => $n->created_at->toIso8601String(),
            ],
        ]);
    }

    /**
     * Delete a notification campaign (only draft or scheduled).
     */
    public function destroy(int $id): JsonResponse
    {
        $n = AdminNotification::find($id);
        if (!$n) {
            return response()->json(['message' => 'Notification not found'], 404);
        }
        if ($n->status === 'sent') {
            return response()->json(['message' => 'Cannot delete a sent notification'], 422);
        }
        $n->delete();
        return response()->json(['message' => 'Notification deleted']);
    }

    /**
     * Duplicate a notification campaign as draft.
     */
    public function duplicate(int $id): JsonResponse
    {
        $n = AdminNotification::find($id);
        if (!$n) {
            return response()->json(['message' => 'Notification not found'], 404);
        }
        $new = AdminNotification::create([
            'reference_id' => AdminNotification::nextReferenceId(),
            'title' => $n->title,
            'message' => $n->message,
            'method' => $n->method,
            'audience' => $n->audience,
            'category' => $n->category,
            'status' => 'draft',
            'created_by' => Auth::id(),
        ]);
        return response()->json([
            'message' => 'Notification duplicated',
            'data' => [
                'id' => $new->id,
                'reference_id' => $new->reference_id,
                'title' => $new->title,
                'status' => $new->status,
                'created_at' => $new->created_at->toIso8601String(),
            ],
        ], 201);
    }

    /**
     * Send a notification campaign now: create in-app notifications for target audience.
     */
    public function send(int $id): JsonResponse
    {
        $n = AdminNotification::find($id);
        if (!$n) {
            return response()->json(['message' => 'Notification not found'], 404);
        }
        if ($n->status === 'sent') {
            return response()->json(['message' => 'Notification already sent'], 422);
        }
        $userIds = match ($n->audience) {
            'all_employers' => User::where('user_type', 'employer')->pluck('id')->all(),
            'all_job_seekers' => User::where('user_type', 'job_seeker')->pluck('id')->all(),
            default => User::whereIn('user_type', ['employer', 'job_seeker'])->pluck('id')->all(),
        };
        foreach ($userIds as $userId) {
            Notification::create([
                'user_id' => $userId,
                'type' => 'admin_broadcast',
                'title' => $n->title,
                'message' => $n->message ?? $n->title,
                'data' => [
                    'admin_notification_id' => $n->id,
                    'reference_id' => $n->reference_id,
                    'category' => $n->category,
                    'type' => 'admin_broadcast',
                ],
            ]);
        }
        $n->update(['status' => 'sent', 'sent_at' => now()]);
        return response()->json([
            'message' => 'Notification sent',
            'recipients_count' => count($userIds),
            'data' => [
                'id' => $n->id,
                'reference_id' => $n->reference_id,
                'status' => $n->status,
                'sent_at' => $n->sent_at->toIso8601String(),
            ],
        ]);
    }
}
