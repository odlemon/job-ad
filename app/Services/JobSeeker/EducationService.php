<?php

namespace App\Services\JobSeeker;

use App\Models\JobSeeker;
use App\Models\JobSeekerEducation;
use Illuminate\Database\Eloquent\Collection;

class EducationService
{
    public function getBySeeker(JobSeeker $jobSeeker): Collection
    {
        return $jobSeeker->educations()->orderBy('start_date', 'desc')->get();
    }

    public function create(JobSeeker $jobSeeker, array $data): JobSeekerEducation
    {
        $data['seeker_id'] = $jobSeeker->seeker_id;
        return JobSeekerEducation::create($data);
    }

    public function update(JobSeekerEducation $education, array $data): JobSeekerEducation
    {
        $education->update($data);
        return $education->fresh();
    }

    public function delete(JobSeekerEducation $education): bool
    {
        return $education->delete();
    }
}
