<?php

namespace App\Http\Controllers\JobSeeker;

use App\Http\Controllers\Controller;
use App\Models\JobSeekerEducation;
use App\Services\JobSeeker\EducationService;
use App\Services\JobSeeker\JobSeekerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class EducationController extends Controller
{
    public function __construct(
        private EducationService $educationService,
        private JobSeekerService $jobSeekerService
    ) {
    }

    public function index(): JsonResponse
    {
        $user = Auth::user();
        $jobSeeker = $this->jobSeekerService->getByUserId($user->id);

        if (!$jobSeeker) {
            return response()->json(['message' => 'Job seeker profile not found'], 404);
        }

        $educations = $this->educationService->getBySeeker($jobSeeker);

        return response()->json(['data' => $educations]);
    }

    public function store(Request $request): JsonResponse
    {
        $user = Auth::user();
        $jobSeeker = $this->jobSeekerService->getByUserId($user->id);

        if (!$jobSeeker) {
            return response()->json(['message' => 'Job seeker profile not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'degree' => 'required|string|max:255',
            'institution' => 'required|string|max:255',
            'location' => 'nullable|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
            'gpa' => 'nullable|numeric|min:0|max:4',
            'gpa_scale' => 'nullable|string|max:10',
            'description' => 'nullable|string|max:5000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $education = $this->educationService->create($jobSeeker, $validator->validated());

        return response()->json([
            'message' => 'Education added successfully',
            'data' => $education,
        ], 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $user = Auth::user();
        $jobSeeker = $this->jobSeekerService->getByUserId($user->id);

        if (!$jobSeeker) {
            return response()->json(['message' => 'Job seeker profile not found'], 404);
        }

        $education = JobSeekerEducation::where('seeker_id', $jobSeeker->seeker_id)
            ->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'degree' => 'sometimes|required|string|max:255',
            'institution' => 'sometimes|required|string|max:255',
            'location' => 'nullable|string|max:255',
            'start_date' => 'sometimes|required|date',
            'end_date' => 'nullable|date|after:start_date',
            'gpa' => 'nullable|numeric|min:0|max:4',
            'gpa_scale' => 'nullable|string|max:10',
            'description' => 'nullable|string|max:5000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $education = $this->educationService->update($education, $validator->validated());

        return response()->json([
            'message' => 'Education updated successfully',
            'data' => $education,
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $user = Auth::user();
        $jobSeeker = $this->jobSeekerService->getByUserId($user->id);

        if (!$jobSeeker) {
            return response()->json(['message' => 'Job seeker profile not found'], 404);
        }

        $education = JobSeekerEducation::where('seeker_id', $jobSeeker->seeker_id)
            ->findOrFail($id);

        $this->educationService->delete($education);

        return response()->json(['message' => 'Education deleted successfully']);
    }
}
