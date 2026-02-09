<?php

namespace App\Http\Controllers\JobSeeker;

use App\Http\Controllers\Controller;
use App\Services\JobSeeker\CategoryPreferenceService;
use App\Services\JobSeeker\JobSeekerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CategoryPreferenceController extends Controller
{
    public function __construct(
        private CategoryPreferenceService $categoryPreferenceService,
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

        $preferences = $this->categoryPreferenceService->getBySeeker($jobSeeker);

        return response()->json(['data' => $preferences]);
    }

    public function store(Request $request): JsonResponse
    {
        $user = Auth::user();
        $jobSeeker = $this->jobSeekerService->getByUserId($user->id);

        if (!$jobSeeker) {
            return response()->json(['message' => 'Job seeker profile not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'category_id' => 'required|exists:job_categories,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $preference = $this->categoryPreferenceService->add($jobSeeker, $request->category_id);

            return response()->json([
                'message' => 'Category preference added successfully',
                'data' => $preference->load('category'),
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    public function sync(Request $request): JsonResponse
    {
        $user = Auth::user();
        $jobSeeker = $this->jobSeekerService->getByUserId($user->id);

        if (!$jobSeeker) {
            return response()->json(['message' => 'Job seeker profile not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'category_ids' => 'required|array|max:6',
            'category_ids.*' => 'exists:job_categories,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $this->categoryPreferenceService->sync($jobSeeker, $request->category_ids);

        $preferences = $this->categoryPreferenceService->getBySeeker($jobSeeker);

        return response()->json([
            'message' => 'Category preferences updated successfully',
            'data' => $preferences,
        ]);
    }

    public function destroy(int $categoryId): JsonResponse
    {
        $user = Auth::user();
        $jobSeeker = $this->jobSeekerService->getByUserId($user->id);

        if (!$jobSeeker) {
            return response()->json(['message' => 'Job seeker profile not found'], 404);
        }

        try {
            $this->categoryPreferenceService->remove($jobSeeker, $categoryId);

            return response()->json(['message' => 'Category preference removed successfully']);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 404);
        }
    }
}
