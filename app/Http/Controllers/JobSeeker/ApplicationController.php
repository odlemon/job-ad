<?php

namespace App\Http\Controllers\JobSeeker;

use App\Http\Controllers\Controller;
use App\Services\JobSeeker\ApplicationService;
use App\Services\JobSeeker\JobSeekerService;
use App\Services\NotificationService;
use App\Services\RemoteUploadService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ApplicationController extends Controller
{
    public function __construct(
        private ApplicationService $applicationService,
        private JobSeekerService $jobSeekerService,
        private RemoteUploadService $uploadService,
        private NotificationService $notificationService
    ) {
    }

    /**
     * Get all applications for authenticated job seeker.
     */
    public function index(Request $request): JsonResponse
    {
        $user = Auth::user();

        if (!$user || $user->user_type !== 'job_seeker') {
            return response()->json([
                'message' => 'Unauthorized',
            ], 403);
        }

        $jobSeeker = $this->jobSeekerService->getByUserId($user->id);

        if (!$jobSeeker) {
            return response()->json([
                'message' => 'Job seeker profile not found',
            ], 404);
        }

        $perPage = (int) $request->get('per_page', $request->get('limit', 15));
        $status = $request->get('status');
        $statuses = null;
        if ($status) {
            $statusMap = [
                'applied' => ['pending', 'submitted', 'applied'],
                'in_review' => ['reviewing', 'in_review', 'shortlisted'],
                'interview' => ['interview', 'interviewing'],
                'offered' => ['offered', 'hired', 'accepted'],
                'rejected' => ['rejected', 'declined'],
            ];
            $statuses = $statusMap[$status] ?? [$status];
        }

        $applications = $this->applicationService->getPaginatedBySeeker($jobSeeker, $perPage, $statuses);
        $items = collect($applications->items());
        $jobModels = $items->pluck('jobAdvertisement')->filter()->values();
        $presented = collect(\App\Support\ScoopJobPresenter::jobs($jobModels, $jobSeeker->seeker_id))->keyBy('id');

        $data = $items->map(function ($app) use ($presented) {
            $job = $app->jobAdvertisement;
            return [
                'id' => $app->id,
                'status' => $app->status,
                'status_message' => $app->status_message ?? $app->notes,
                'applied_at' => optional($app->created_at)?->toIso8601String(),
                'updated_at' => optional($app->updated_at)?->toIso8601String(),
                'invite_sent_at' => optional($app->invite_sent_at)?->toIso8601String(),
                'job' => $job ? $presented->get($job->id) : null,
                'job_advertisement' => $job,
            ];
        })->values();

        return response()->json([
            'data' => $data,
            'meta' => [
                'current_page' => $applications->currentPage(),
                'last_page' => $applications->lastPage(),
                'total' => $applications->total(),
                'per_page' => $applications->perPage(),
            ],
        ]);
    }

    /**
     * Check if user has already applied to a job.
     */
    public function check(int $jobId): JsonResponse
    {
        $user = Auth::user();
        $jobSeeker = $this->jobSeekerService->getByUserId($user->id);
        
        if (!$jobSeeker) {
            return response()->json(['has_applied' => false], 200);
        }
        
        $hasApplied = $this->applicationService->hasApplied($jobSeeker->seeker_id, $jobId);
        
        return response()->json(['has_applied' => $hasApplied], 200);
    }

    /**
     * Get a specific application.
     */
    public function show(int $id): JsonResponse
    {
        $user = Auth::user();

        if (!$user || $user->user_type !== 'job_seeker') {
            return response()->json([
                'message' => 'Unauthorized',
            ], 403);
        }

        $application = $this->applicationService->getById($id);

        if (!$application) {
            return response()->json([
                'message' => 'Application not found',
            ], 404);
        }

        // Check if application belongs to authenticated job seeker
        $jobSeeker = $this->jobSeekerService->getByUserId($user->id);
        if ($application->seeker_id !== $jobSeeker->seeker_id) {
            return response()->json([
                'message' => 'Unauthorized',
            ], 403);
        }

        return response()->json([
            'application' => $application,
        ]);
    }

    /**
     * Submit a new job application.
     */
    public function store(Request $request): JsonResponse
    {
        $user = Auth::user();

        if (!$user || $user->user_type !== 'job_seeker') {
            return response()->json([
                'message' => 'Unauthorized',
            ], 403);
        }

        $jobSeeker = $this->jobSeekerService->getByUserId($user->id);

        if (!$jobSeeker) {
            return response()->json([
                'message' => 'Job seeker profile not found',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'job_advertisement_id' => 'required|exists:job_advertisements,id',
            'additional_info' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            // Build application data from job seeker profile
            $applicationData = [
                'job_advertisement_id' => $request->input('job_advertisement_id'),
                'additional_info' => $request->input('additional_info', []),
            ];

            // Use job seeker's profile information
            $applicationData['first_name'] = $jobSeeker->first_name;
            $applicationData['last_name'] = $jobSeeker->last_name;
            $applicationData['email'] = $jobSeeker->user->email;
            $applicationData['phone'] = $jobSeeker->phone ?? $jobSeeker->user->phone;
            
            // Use job seeker's CV if available
            if ($jobSeeker->cv_file_path) {
                $applicationData['resume_path'] = $jobSeeker->cv_file_path;
            }

            $application = $this->applicationService->apply($jobSeeker, $applicationData);
            
            // Load relationships for notification
            $application->load(['jobAdvertisement.company']);
            
            // Create notification for employer
            if ($application->jobAdvertisement && $application->jobAdvertisement->company_id) {
                // Find employer associated with this company
                $employer = \App\Models\Employer::where('company_id', $application->jobAdvertisement->company_id)->first();
                if ($employer && $employer->user_id) {
                    $this->notificationService->notifyApplicationReceived(
                        $employer->user_id,
                        $application->id,
                        $application->jobAdvertisement->title,
                        "{$application->first_name} {$application->last_name}"
                    );
                }
            }

            return response()->json([
                'message' => 'Application submitted successfully',
                'application' => $application,
            ], 201);
        } catch (\Exception $e) {
            $statusCode = 400;
            $message = $e->getMessage();
            
            // Check if it's a duplicate application error
            if (str_contains(strtolower($message), 'already applied')) {
                $statusCode = 422;
            }
            
            return response()->json([
                'message' => $message,
                'error' => $message,
            ], $statusCode);
        }
    }

    /**
     * Withdraw an application.
     */
    public function withdraw(int $id): JsonResponse
    {
        $user = Auth::user();

        if (!$user || $user->user_type !== 'job_seeker') {
            return response()->json([
                'message' => 'Unauthorized',
            ], 403);
        }

        $application = $this->applicationService->getById($id);

        if (!$application) {
            return response()->json([
                'message' => 'Application not found',
            ], 404);
        }

        // Check if application belongs to authenticated job seeker
        $jobSeeker = $this->jobSeekerService->getByUserId($user->id);
        if ($application->seeker_id !== $jobSeeker->seeker_id) {
            return response()->json([
                'message' => 'Unauthorized',
            ], 403);
        }

        $deleted = $this->applicationService->withdraw($application);

        if (!$deleted) {
            return response()->json([
                'message' => 'Failed to withdraw application',
            ], 500);
        }

        return response()->json([
            'message' => 'Application withdrawn successfully',
        ]);
    }
}
