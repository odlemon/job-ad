<?php

namespace App\Http\Controllers\JobSeeker;

use App\Http\Controllers\Controller;
use App\Models\JobSeekerLanguage;
use App\Services\JobSeeker\JobSeekerService;
use App\Services\JobSeeker\LanguageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class LanguageController extends Controller
{
    public function __construct(
        private LanguageService $languageService,
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

        $languages = $this->languageService->getBySeeker($jobSeeker)
            ->map(fn ($l) => \App\Support\ScoopNestedPresenter::language($l))
            ->values();

        return response()->json(['data' => $languages]);
    }

    public function store(Request $request): JsonResponse
    {
        $user = Auth::user();
        $jobSeeker = $this->jobSeekerService->getByUserId($user->id);

        if (!$jobSeeker) {
            return response()->json(['message' => 'Job seeker profile not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'language' => 'required_without:name|string|max:255',
            'name' => 'required_without:language|string|max:255',
            'proficiency_level' => 'required_without:level|in:basic,conversational,fluent,native,beginner,intermediate,advanced',
            'level' => 'required_without:proficiency_level|in:basic,conversational,fluent,native,beginner,intermediate,advanced',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $languageName = $request->input('language', $request->input('name'));
        $level = $request->input('proficiency_level', $request->input('level'));
        // Map Scoop levels to DB enum where needed
        $levelMap = [
            'beginner' => 'basic',
            'intermediate' => 'conversational',
            'advanced' => 'fluent',
        ];
        $level = $levelMap[$level] ?? $level;

        $existing = JobSeekerLanguage::where('seeker_id', $jobSeeker->seeker_id)
            ->where('language', $languageName)
            ->first();

        if ($existing) {
            return response()->json([
                'message' => 'Language already exists',
                'errors' => ['language' => ['This language is already added']],
            ], 422);
        }

        $language = $this->languageService->create($jobSeeker, [
            'language' => $languageName,
            'proficiency_level' => $level,
        ]);

        return response()->json([
            'message' => 'Language added successfully',
            'data' => \App\Support\ScoopNestedPresenter::language($language),
        ], 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $user = Auth::user();
        $jobSeeker = $this->jobSeekerService->getByUserId($user->id);

        if (!$jobSeeker) {
            return response()->json(['message' => 'Job seeker profile not found'], 404);
        }

        $language = JobSeekerLanguage::where('seeker_id', $jobSeeker->seeker_id)
            ->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'language' => 'sometimes|required|string|max:255',
            'proficiency_level' => 'sometimes|required|in:basic,conversational,fluent,native',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $language = $this->languageService->update($language, $validator->validated());

        return response()->json([
            'message' => 'Language updated successfully',
            'data' => $language,
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $user = Auth::user();
        $jobSeeker = $this->jobSeekerService->getByUserId($user->id);

        if (!$jobSeeker) {
            return response()->json(['message' => 'Job seeker profile not found'], 404);
        }

        $language = JobSeekerLanguage::where('seeker_id', $jobSeeker->seeker_id)
            ->findOrFail($id);

        $this->languageService->delete($language);

        return response()->json(['message' => 'Language deleted successfully']);
    }
}
