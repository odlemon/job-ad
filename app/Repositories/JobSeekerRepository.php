<?php

namespace App\Repositories;

use App\Models\JobSeeker;
use App\Repositories\Contracts\JobSeekerRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class JobSeekerRepository implements JobSeekerRepositoryInterface
{
    public function all(): Collection
    {
        return JobSeeker::with(['user'])->get();
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return JobSeeker::with(['user'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function find(int $id): ?JobSeeker
    {
        return JobSeeker::with(['user', 'applications', 'savedJobs'])->find($id);
    }

    public function findByUserId(int $userId): ?JobSeeker
    {
        // Hot path for Scoop APIs — do not hydrate applications/savedJobs bags
        return JobSeeker::where('user_id', $userId)->first();
    }

    public function create(array $data): JobSeeker
    {
        return JobSeeker::create($data);
    }

    public function update(JobSeeker $jobSeeker, array $data): JobSeeker
    {
        $jobSeeker->update($data);
        return $jobSeeker->fresh(['user', 'applications', 'savedJobs']);
    }

    public function delete(JobSeeker $jobSeeker): bool
    {
        return $jobSeeker->delete();
    }
}
