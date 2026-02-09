<?php

namespace App\Services\JobSeeker;

use App\Models\JobSeeker;
use App\Models\JobSeekerSkill;
use Illuminate\Database\Eloquent\Collection;

class SkillService
{
    public function getBySeeker(JobSeeker $jobSeeker): Collection
    {
        return $jobSeeker->skills()->orderBy('skill_name')->get();
    }

    public function create(JobSeeker $jobSeeker, array $data): JobSeekerSkill
    {
        $data['seeker_id'] = $jobSeeker->seeker_id;
        return JobSeekerSkill::create($data);
    }

    public function update(JobSeekerSkill $skill, array $data): JobSeekerSkill
    {
        $skill->update($data);
        return $skill->fresh();
    }

    public function delete(JobSeekerSkill $skill): bool
    {
        return $skill->delete();
    }

    public function sync(JobSeeker $jobSeeker, array $skills): void
    {
        // Delete existing skills
        $jobSeeker->skills()->delete();

        // Create new skills
        foreach ($skills as $skillData) {
            $skillData['seeker_id'] = $jobSeeker->seeker_id;
            JobSeekerSkill::create($skillData);
        }
    }
}
