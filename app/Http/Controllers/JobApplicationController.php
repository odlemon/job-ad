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
        
        // Get paginated applications for the user
        $applications = $this->service->getByUserIdPaginated($user->id, 15);
        
        // Count by status
        $allApplications = $this->service->getByUserId($user->id);
        $stats = [
            'pending' => $allApplications->where('status', 'pending')->count(),
            'reviewing' => $allApplications->where('status', 'reviewing')->count(),
            'shortlisted' => $allApplications->where('status', 'shortlisted')->count(),
            'hired' => $allApplications->where('status', 'hired')->count(),
            'rejected' => $allApplications->where('status', 'rejected')->count(),
        ];
        
        return view('job-seeker.applications', [
            'applications' => $applications,
            'stats' => $stats,
        ]);
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
            'notes' => 'nullable|string',
        ]);
        
        $application = $this->service->update($application, $validated);
        
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
