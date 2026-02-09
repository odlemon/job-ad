<?php

namespace App\Services\JobSeeker;

use App\Models\JobSeeker;
use App\Models\JobSeekerReference;
use Illuminate\Database\Eloquent\Collection;

class ReferenceService
{
    public function getBySeeker(JobSeeker $jobSeeker): Collection
    {
        return $jobSeeker->references()->orderBy('reference_name')->get();
    }

    public function create(JobSeeker $jobSeeker, array $data): JobSeekerReference
    {
        $data['seeker_id'] = $jobSeeker->seeker_id;
        return JobSeekerReference::create($data);
    }

    public function update(JobSeekerReference $reference, array $data): JobSeekerReference
    {
        $reference->update($data);
        return $reference->fresh();
    }

    public function delete(JobSeekerReference $reference): bool
    {
        return $reference->delete();
    }
}
