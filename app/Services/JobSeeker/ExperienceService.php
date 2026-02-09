<?php

namespace App\Services\JobSeeker;

use App\Models\JobSeeker;
use App\Models\JobSeekerExperience;
use Illuminate\Database\Eloquent\Collection;

class ExperienceService
{
    /**
     * Get all experiences for a job seeker.
     */
    public function getBySeeker(JobSeeker $jobSeeker): Collection
    {
        return $jobSeeker->experiences()->orderBy('start_date', 'desc')->get();
    }

    /**
     * Create a new experience.
     */
    public function create(JobSeeker $jobSeeker, array $data): JobSeekerExperience
    {
        $data['seeker_id'] = $jobSeeker->seeker_id;
        return JobSeekerExperience::create($data);
    }

    /**
     * Update an experience.
     */
    public function update(JobSeekerExperience $experience, array $data): JobSeekerExperience
    {
        $experience->update($data);
        return $experience->fresh();
    }

    /**
     * Delete an experience.
     */
    public function delete(JobSeekerExperience $experience): bool
    {
        return $experience->delete();
    }
}
