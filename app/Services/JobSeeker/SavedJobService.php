<?php

namespace App\Services\JobSeeker;

use App\Models\JobSeeker;
use App\Models\SavedJob;
use App\Repositories\Contracts\SavedJobRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class SavedJobService
{
    public function __construct(
        private SavedJobRepositoryInterface $repository
    ) {
    }

    /**
     * Get all saved jobs for a job seeker.
     */
    public function getBySeeker(JobSeeker $jobSeeker): Collection
    {
        return $this->repository->getBySeekerId($jobSeeker->seeker_id);
    }

    /**
     * Get paginated saved jobs for a job seeker.
     */
    public function getPaginatedBySeeker(JobSeeker $jobSeeker, int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->paginateBySeekerId($jobSeeker->seeker_id, $perPage);
    }

    /**
     * Check if job is saved by seeker.
     */
    public function isJobSaved(JobSeeker $jobSeeker, int $jobId): bool
    {
        return $this->repository->findBySeekerAndJob($jobSeeker->seeker_id, $jobId) !== null;
    }

    /**
     * Save a job for a job seeker.
     */
    public function saveJob(JobSeeker $jobSeeker, int $jobId): SavedJob
    {
        // Business logic: Check if already saved
        $existing = $this->repository->findBySeekerAndJob($jobSeeker->seeker_id, $jobId);
        if ($existing) {
            return $existing;
        }

        return $this->repository->create([
            'seeker_id' => $jobSeeker->seeker_id,
            'job_id' => $jobId,
            'saved_at' => now(),
        ]);
    }

    /**
     * Unsave a job for a job seeker.
     */
    public function unsaveJob(JobSeeker $jobSeeker, int $jobId): bool
    {
        return $this->repository->deleteBySeekerAndJob($jobSeeker->seeker_id, $jobId);
    }
}
