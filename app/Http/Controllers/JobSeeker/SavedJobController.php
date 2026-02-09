<?php

namespace App\Http\Controllers\JobSeeker;

use App\Http\Controllers\Controller;
use App\Services\JobSeeker\JobSeekerService;
use App\Services\JobSeeker\SavedJobService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SavedJobController extends Controller
{
    public function __construct(
        private SavedJobService $savedJobService,
        private JobSeekerService $jobSeekerService
    ) {
    }

    /**
     * Get all saved jobs for authenticated job seeker.
     */
    public function index(Request $request): JsonResponse
    {
        $user = Auth::user();
        $jobSeeker = $this->jobSeekerService->getByUserId($user->id);

        if (!$jobSeeker) {
            return response()->json([
                'message' => 'Job seeker profile not found',
            ], 404);
        }

        $perPage = $request->get('per_page', 15);
        $savedJobs = $this->savedJobService->getPaginatedBySeeker($jobSeeker, $perPage);

        return response()->json($savedJobs);
    }

    /**
     * Check if a job is saved.
     */
    public function check(int $jobId): JsonResponse
    {
        $user = Auth::user();
        $jobSeeker = $this->jobSeekerService->getByUserId($user->id);

        if (!$jobSeeker) {
            return response()->json([
                'message' => 'Job seeker profile not found',
            ], 404);
        }

        $isSaved = $this->savedJobService->isJobSaved($jobSeeker, $jobId);

        return response()->json([
            'is_saved' => $isSaved,
        ]);
    }

    /**
     * Save a job.
     */
    public function store(Request $request): JsonResponse
    {
        $user = Auth::user();
        $jobSeeker = $this->jobSeekerService->getByUserId($user->id);

        if (!$jobSeeker) {
            return response()->json([
                'message' => 'Job seeker profile not found',
            ], 404);
        }

        $request->validate([
            'job_id' => 'required|exists:job_advertisements,id',
        ]);

        $savedJob = $this->savedJobService->saveJob($jobSeeker, $request->job_id);

        return response()->json([
            'message' => 'Job saved successfully',
            'saved_job' => $savedJob,
        ], 201);
    }

    /**
     * Unsave a job.
     */
    public function destroy(int $jobId): JsonResponse
    {
        $user = Auth::user();
        $jobSeeker = $this->jobSeekerService->getByUserId($user->id);

        if (!$jobSeeker) {
            return response()->json([
                'message' => 'Job seeker profile not found',
            ], 404);
        }

        $deleted = $this->savedJobService->unsaveJob($jobSeeker, $jobId);

        if (!$deleted) {
            return response()->json([
                'message' => 'Job not found in saved jobs',
            ], 404);
        }

        return response()->json([
            'message' => 'Job unsaved successfully',
        ]);
    }
}
