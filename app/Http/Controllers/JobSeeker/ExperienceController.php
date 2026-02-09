<?php

namespace App\Http\Controllers\JobSeeker;

use App\Http\Controllers\Controller;
use App\Models\JobSeekerExperience;
use App\Services\JobSeeker\ExperienceService;
use App\Services\JobSeeker\JobSeekerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ExperienceController extends Controller
{
    public function __construct(
        private ExperienceService $experienceService,
        private JobSeekerService $jobSeekerService
    ) {
    }

    /**
     * Get all experiences for authenticated job seeker.
     */
    public function index(): JsonResponse
    {
        $user = Auth::user();
        $jobSeeker = $this->jobSeekerService->getByUserId($user->id);

        if (!$jobSeeker) {
            return response()->json(['message' => 'Job seeker profile not found'], 404);
        }

        $experiences = $this->experienceService->getBySeeker($jobSeeker);

        return response()->json([
            'data' => $experiences,
        ]);
    }

    /**
     * Store a new experience.
     */
    public function store(Request $request): JsonResponse
    {
        $user = Auth::user();
        $jobSeeker = $this->jobSeekerService->getByUserId($user->id);

        if (!$jobSeeker) {
            return response()->json(['message' => 'Job seeker profile not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'job_title' => 'required|string|max:255',
            'company_name' => 'required|string|max:255',
            'location' => 'nullable|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
            'is_current' => 'nullable|boolean',
            'description' => 'nullable|string|max:5000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $experience = $this->experienceService->create($jobSeeker, $validator->validated());

        return response()->json([
            'message' => 'Experience added successfully',
            'data' => $experience,
        ], 201);
    }

    /**
     * Update an experience.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $user = Auth::user();
        $jobSeeker = $this->jobSeekerService->getByUserId($user->id);

        if (!$jobSeeker) {
            return response()->json(['message' => 'Job seeker profile not found'], 404);
        }

        $experience = JobSeekerExperience::where('seeker_id', $jobSeeker->seeker_id)
            ->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'job_title' => 'sometimes|required|string|max:255',
            'company_name' => 'sometimes|required|string|max:255',
            'location' => 'nullable|string|max:255',
            'start_date' => 'sometimes|required|date',
            'end_date' => 'nullable|date|after:start_date',
            'is_current' => 'nullable|boolean',
            'description' => 'nullable|string|max:5000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $experience = $this->experienceService->update($experience, $validator->validated());

        return response()->json([
            'message' => 'Experience updated successfully',
            'data' => $experience,
        ]);
    }

    /**
     * Delete an experience.
     */
    public function destroy(int $id): JsonResponse
    {
        $user = Auth::user();
        $jobSeeker = $this->jobSeekerService->getByUserId($user->id);

        if (!$jobSeeker) {
            return response()->json(['message' => 'Job seeker profile not found'], 404);
        }

        $experience = JobSeekerExperience::where('seeker_id', $jobSeeker->seeker_id)
            ->findOrFail($id);

        $this->experienceService->delete($experience);

        return response()->json([
            'message' => 'Experience deleted successfully',
        ]);
    }
}
