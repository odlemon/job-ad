<?php

namespace App\Http\Controllers;

use App\Services\JobApplicationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JobApplicationController extends Controller
{
    public function __construct(
        private JobApplicationService $service
    ) {
    }

    /**
     * Display a listing of the user's job applications.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // Full list for Bolt-style client filters (seekers rarely have huge volumes)
        $applications = $this->service->getByUserId($user->id);

        $stats = [
            'Applied' => 0,
            'In Review' => 0,
            'Interview' => 0,
            'Offered' => 0,
            'Rejected' => 0,
        ];

        foreach ($applications as $application) {
            $label = self::boltStatusLabel((string) $application->status);
            $stats[$label] = ($stats[$label] ?? 0) + 1;
        }

        return view('job-seeker.applications', [
            'applications' => $applications,
            'stats' => $stats,
            'totalCount' => $applications->count(),
        ]);
    }

    /**
     * Map DB status values to Bolt tracker labels.
     */
    public static function boltStatusLabel(string $status): string
    {
        return match (true) {
            in_array($status, ['applied', 'pending'], true) => 'Applied',
            in_array($status, ['reviewing', 'in_review'], true) => 'In Review',
            in_array($status, ['interview', 'shortlisted', 'interview_requested'], true) => 'Interview',
            in_array($status, ['offered', 'hired'], true) => 'Offered',
            $status === 'rejected' => 'Rejected',
            default => 'Applied',
        };
    }

    /**
     * Display the specified job application.
     */
    public function show(Request $request, int $id)
    {
        $user = Auth::user();
        $application = $this->service->getById($id);
        
        if (!$application) {
            if ($request->expectsJson() || $request->wantsJson()) {
                return response()->json(['message' => 'Application not found'], 404);
            }
            abort(404, 'Application not found');
        }
        
        // Ensure the application belongs to the user
        if ($application->user_id !== $user->id) {
            if ($request->expectsJson() || $request->wantsJson()) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }
            abort(403, 'Unauthorized');
        }
        
        // Load relationships
        $application->load(['jobAdvertisement.company', 'jobAdvertisement.category']);
        
        if ($request->expectsJson() || $request->wantsJson()) {
            return response()->json([
                'application' => $application,
            ]);
        }
        
        return view('job-seeker.application-detail', [
            'application' => $application,
        ]);
    }

    /**
     * Update notes for the specified job application.
     */
    public function updateNotes(Request $request, int $id)
    {
        $user = Auth::user();
        $application = $this->service->getById($id);
        
        if (!$application) {
            return response()->json(['message' => 'Application not found'], 404);
        }
        
        // Ensure the application belongs to the user
        if ($application->user_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        
        $validated = $request->validate([
            'notes' => 'nullable|string|max:5000',
            'note' => 'nullable|string|max:5000',
        ]);

        $notes = $validated['notes'] ?? $validated['note'] ?? null;
        $application = $this->service->update($application, ['notes' => $notes]);

        return response()->json(['message' => 'Notes updated successfully', 'application' => $application], 200);
    }

    /**
     * Delete the specified job application.
     */
    public function destroy(int $id)
    {
        $user = Auth::user();
        $application = $this->service->getById($id);
        
        if (!$application) {
            return response()->json(['message' => 'Application not found'], 404);
        }
        
        // Ensure the application belongs to the user
        if ($application->user_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        
        $this->service->delete($application);
        
        return response()->json(['message' => 'Application deleted successfully'], 200);
    }

    /**
     * Handle job seeker's response to an interview request.
     */
    public function interviewResponse(Request $request, int $id)
    {
        $user = Auth::user();
        $application = $this->service->getById($id);

        if (!$application) {
            return response()->json(['message' => 'Application not found'], 404);
        }

        if ($application->user_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'response' => 'required|in:accepted,declined',
            'reason' => 'nullable|string|max:2000',
        ]);

        $application->interview_status = $validated['response'];
        $application->interview_response_reason = $validated['response'] === 'declined'
            ? ($validated['reason'] ?? null)
            : null;
        $application->save();

        // Notify employer about the response
        $application->load('jobAdvertisement.company.employer');
        $job = $application->jobAdvertisement;
        $employer = $job?->company?->employer;
        if ($employer && $employer->user_id) {
            app(\App\Services\NotificationService::class)->create([
                'user_id' => $employer->user_id,
                'type' => 'interview_response',
                'title' => 'Interview response from candidate',
                'message' => sprintf(
                    '%s %s has %s the interview for %s.',
                    $application->first_name,
                    $application->last_name,
                    $validated['response'] === 'accepted' ? 'accepted' : 'declined',
                    $job->title
                ),
                'data' => [
                    'application_id' => $application->id,
                    'job_title' => $job->title,
                    'company_name' => $job->company->name,
                    'response' => $validated['response'],
                    'reason' => $application->interview_response_reason,
                    'type' => 'interview_response',
                ],
            ]);
        }

        return response()->json([
            'message' => 'Interview response saved successfully',
            'application' => $application,
        ], 200);
    }
}
