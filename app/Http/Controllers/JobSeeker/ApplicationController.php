<?php

namespace App\Http\Controllers\JobSeeker;

use App\Http\Controllers\Controller;
use App\Services\JobSeeker\ApplicationService;
use App\Services\JobSeeker\JobSeekerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ApplicationController extends Controller
{
    public function __construct(
        private ApplicationService $applicationService,
        private JobSeekerService $jobSeekerService
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

        $perPage = $request->get('per_page', 15);
        $applications = $this->applicationService->getPaginatedBySeeker($jobSeeker, $perPage);

        return response()->json($applications);
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
            'cover_letter' => 'nullable|string',
            'resume_path' => 'nullable|string|max:255',
            'additional_info' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $application = $this->applicationService->apply($jobSeeker, $request->only([
                'job_advertisement_id',
                'cover_letter',
                'resume_path',
                'additional_info',
            ]));

            return response()->json([
                'message' => 'Application submitted successfully',
                'application' => $application,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 400);
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
