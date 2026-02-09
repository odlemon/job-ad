<?php

namespace App\Repositories;

use App\Models\SavedJob;
use App\Repositories\Contracts\SavedJobRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class SavedJobRepository implements SavedJobRepositoryInterface
{
    public function getBySeekerId(int $seekerId): Collection
    {
        return SavedJob::with(['job', 'job.company', 'job.category'])
            ->where('seeker_id', $seekerId)
            ->orderBy('saved_at', 'desc')
            ->get();
    }

    public function paginateBySeekerId(int $seekerId, int $perPage = 15): LengthAwarePaginator
    {
        return SavedJob::with(['job', 'job.company', 'job.category'])
            ->where('seeker_id', $seekerId)
            ->orderBy('saved_at', 'desc')
            ->paginate($perPage);
    }

    public function find(int $id): ?SavedJob
    {
        return SavedJob::with(['jobSeeker', 'job'])->find($id);
    }

    public function findBySeekerAndJob(int $seekerId, int $jobId): ?SavedJob
    {
        return SavedJob::where('seeker_id', $seekerId)
            ->where('job_id', $jobId)
            ->first();
    }

    public function create(array $data): SavedJob
    {
        return SavedJob::create($data);
    }

    public function delete(SavedJob $savedJob): bool
    {
        return $savedJob->delete();
    }

    public function deleteBySeekerAndJob(int $seekerId, int $jobId): bool
    {
        return SavedJob::where('seeker_id', $seekerId)
            ->where('job_id', $jobId)
            ->delete() > 0;
    }
}
