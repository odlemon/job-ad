<?php

namespace App\Http\Controllers\JobSeeker;

use App\Http\Controllers\Controller;
use App\Models\JobAdvertisement;
use App\Models\JobReport;
use App\Models\SupportTicket;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class SupportController extends Controller
{
    public function __construct(
        private NotificationService $notificationService
    ) {
    }

    public function index(): View
    {
        return view('job-seeker.support');
    }

    public function bootstrap(): JsonResponse
    {
        $user = Auth::user();

        return response()->json([
            'tickets' => $this->ticketsPayload($user->id),
            'faqs' => $this->faqs(),
            'support_email' => 'support@scoop.app',
            'user' => [
                'name' => $user->name,
                'email' => $user->email,
            ],
        ]);
    }

    public function tickets(): JsonResponse
    {
        return response()->json([
            'data' => $this->ticketsPayload(Auth::id()),
        ]);
    }

    public function showTicket(int $id): JsonResponse
    {
        $ticket = SupportTicket::where('user_id', Auth::id())->where('id', $id)->firstOrFail();

        return response()->json(['ticket' => $this->formatTicket($ticket)]);
    }

    public function storeTicket(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'subject' => 'required|string|max:160',
            'message' => 'required|string|max:5000',
            'priority' => 'nullable|in:low,medium,high',
            'channel' => 'nullable|in:ticket,live_chat,email',
        ]);

        $user = Auth::user();
        $ticket = SupportTicket::create([
            'user_id' => $user->id,
            'subject' => $validated['subject'],
            'message' => $validated['message'],
            'priority' => $validated['priority'] ?? 'medium',
            'channel' => $validated['channel'] ?? 'ticket',
            'status' => 'open',
        ]);

        $priorityLabel = ucfirst($ticket->priority);
        $this->notificationService->notifyAdmins(
            'support_ticket',
            'New support ticket #'.$ticket->id,
            "{$user->name} submitted a {$priorityLabel} priority ticket: {$ticket->subject}",
            [
                'ticket_id' => $ticket->id,
                'user_id' => $user->id,
                'user_name' => $user->name,
                'user_email' => $user->email,
                'priority' => $ticket->priority,
                'channel' => $ticket->channel,
                'subject' => $ticket->subject,
            ]
        );

        return response()->json([
            'message' => 'Support ticket submitted',
            'ticket' => $this->formatTicket($ticket),
        ], 201);
    }

    public function reportJob(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'job_reference' => 'required|string|max:500',
            'category' => 'required|in:scam,inappropriate,duplicate,other',
            'details' => 'nullable|string|max:5000',
        ]);

        $jobId = $this->resolveJobId($validated['job_reference']);
        if (! $jobId) {
            return response()->json([
                'message' => 'Could not find that job. Use a job ID or a Scoop job URL.',
                'errors' => ['job_reference' => ['Invalid job reference']],
            ], 422);
        }

        $job = JobAdvertisement::with('company')->find($jobId);
        if (! $job) {
            return response()->json(['message' => 'Job not found'], 404);
        }

        $user = Auth::user();
        $category = $validated['category'];
        $details = trim((string) ($validated['details'] ?? ''));
        $reason = ucfirst($category).($details !== '' ? ': '.$details : '');

        $payload = [
            'user_id' => $user->id,
            'job_advertisement_id' => $job->id,
            'reason' => $reason,
        ];
        if (\Illuminate\Support\Facades\Schema::hasColumn('job_reports', 'category')) {
            $payload['category'] = $category;
        }
        if (\Illuminate\Support\Facades\Schema::hasColumn('job_reports', 'details')) {
            $payload['details'] = $details !== '' ? $details : null;
        }
        if (\Illuminate\Support\Facades\Schema::hasColumn('job_reports', 'status')) {
            $payload['status'] = 'pending';
        }

        $report = JobReport::create($payload);

        $this->notificationService->notifyAdmins(
            'job_report',
            'Suspicious job report #'.$report->id,
            "{$user->name} reported job \"{$job->title}\" as {$category}",
            [
                'report_id' => $report->id,
                'job_id' => $job->id,
                'job_title' => $job->title,
                'company' => $job->company->name ?? null,
                'category' => $category,
                'user_id' => $user->id,
                'user_name' => $user->name,
            ]
        );

        return response()->json([
            'message' => 'Report submitted. Our team will review it shortly.',
            'report' => [
                'id' => $report->id,
                'job_id' => $job->id,
                'job_title' => $job->title,
                'category' => $category,
            ],
        ], 201);
    }

    private function ticketsPayload(int $userId): array
    {
        return SupportTicket::where('user_id', $userId)
            ->latest()
            ->limit(50)
            ->get()
            ->map(fn (SupportTicket $t) => $this->formatTicket($t))
            ->values()
            ->all();
    }

    private function formatTicket(SupportTicket $t): array
    {
        return [
            'id' => $t->id,
            'subject' => $t->subject,
            'message' => $t->message,
            'status' => $t->status,
            'priority' => $t->priority,
            'channel' => $t->channel,
            'admin_response' => $t->admin_response,
            'created_at' => optional($t->created_at)->toIso8601String(),
            'updated_at' => optional($t->updated_at)->toIso8601String(),
            'resolved_at' => optional($t->resolved_at)->toIso8601String(),
            'date' => optional($t->created_at)->format('M j, Y'),
            'time_ago' => optional($t->created_at)->diffForHumans(),
        ];
    }

    private function resolveJobId(string $reference): ?int
    {
        $reference = trim($reference);
        if ($reference === '') {
            return null;
        }
        if (ctype_digit($reference)) {
            return (int) $reference;
        }
        if (preg_match('/(?:jobs|job)[\/\-=]?(\d+)/i', $reference, $m)) {
            return (int) $m[1];
        }
        if (preg_match('/\bid[=:]?\s*(\d+)/i', $reference, $m)) {
            return (int) $m[1];
        }

        return null;
    }

    private function faqs(): array
    {
        return [
            [
                'q' => 'How long does it take to hear back on a support ticket?',
                'a' => 'Most tickets are reviewed within 1 business day. High-priority issues are triaged first.',
            ],
            [
                'q' => 'How do I update my profile or resume?',
                'a' => 'Go to My Profile from the sidebar to edit personal details, skills, experience, and upload documents. You can also use Career Tools → Resume Builder.',
            ],
            [
                'q' => 'Why was my application withdrawn or rejected?',
                'a' => 'Employers update application status as they review candidates. Check Job Applications for notes and interview requests. If something looks wrong, open a support ticket.',
            ],
            [
                'q' => 'How do I report a scam or suspicious job?',
                'a' => 'Use the “Report Suspicious Job” form on this page with the job ID or URL and a clear reason. Our admins are notified immediately.',
            ],
            [
                'q' => 'Can I delete my account?',
                'a' => 'Yes. Open a support ticket with subject “Account deletion request” and we will guide you through the process securely.',
            ],
        ];
    }
}
