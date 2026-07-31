<?php

namespace App\Http\Controllers\JobSeeker;

use App\Http\Controllers\Controller;
use App\Models\JobSeekerSkill;
use App\Services\JobSeeker\JobSeekerService;
use App\Services\JobSeeker\SkillService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class SkillController extends Controller
{
    public function __construct(
        private SkillService $skillService,
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

        $skills = $this->skillService->getBySeeker($jobSeeker)
            ->map(fn ($s) => \App\Support\ScoopNestedPresenter::skill($s))
            ->values();

        return response()->json(['data' => $skills]);
    }

    public function store(Request $request): JsonResponse
    {
        $user = Auth::user();
        $jobSeeker = $this->jobSeekerService->getByUserId($user->id);

        if (!$jobSeeker) {
            return response()->json(['message' => 'Job seeker profile not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'skill_name' => 'required_without:name|string|max:255',
            'name' => 'required_without:skill_name|string|max:255',
            'proficiency_level' => 'required_without:level|in:beginner,intermediate,advanced,expert',
            'level' => 'required_without:proficiency_level|in:beginner,intermediate,advanced,expert',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $skillName = $request->input('skill_name', $request->input('name'));
        $level = $request->input('proficiency_level', $request->input('level'));

        // Check if skill already exists
        $existing = JobSeekerSkill::where('seeker_id', $jobSeeker->seeker_id)
            ->where('skill_name', $skillName)
            ->first();

        if ($existing) {
            return response()->json([
                'message' => 'Skill already exists',
                'errors' => ['skill_name' => ['This skill is already added']],
            ], 422);
        }

        $skill = $this->skillService->create($jobSeeker, [
            'skill_name' => $skillName,
            'proficiency_level' => $level,
        ]);

        return response()->json([
            'message' => 'Skill added successfully',
            'data' => \App\Support\ScoopNestedPresenter::skill($skill),
        ], 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $user = Auth::user();
        $jobSeeker = $this->jobSeekerService->getByUserId($user->id);

        if (!$jobSeeker) {
            return response()->json(['message' => 'Job seeker profile not found'], 404);
        }

        $skill = JobSeekerSkill::where('seeker_id', $jobSeeker->seeker_id)
            ->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'skill_name' => 'sometimes|required|string|max:255',
            'proficiency_level' => 'sometimes|required|in:beginner,intermediate,advanced,expert',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $skill = $this->skillService->update($skill, $validator->validated());

        return response()->json([
            'message' => 'Skill updated successfully',
            'data' => $skill,
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $user = Auth::user();
        $jobSeeker = $this->jobSeekerService->getByUserId($user->id);

        if (!$jobSeeker) {
            return response()->json(['message' => 'Job seeker profile not found'], 404);
        }

        $skill = JobSeekerSkill::where('seeker_id', $jobSeeker->seeker_id)
            ->findOrFail($id);

        $this->skillService->delete($skill);

        return response()->json(['message' => 'Skill deleted successfully']);
    }
}
