<?php

namespace App\Services\JobSeeker;

use App\Models\JobSeeker;
use App\Models\FollowedCompany;
use App\Repositories\Contracts\FollowedCompanyRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class FollowedCompanyService
{
    public function __construct(
        private FollowedCompanyRepositoryInterface $repository
    ) {
    }

    /**
     * Get all followed companies for a job seeker.
     */
    public function getBySeeker(JobSeeker $jobSeeker): Collection
    {
        return $this->repository->getBySeekerId($jobSeeker->seeker_id);
    }

    /**
     * Get paginated followed companies for a job seeker.
     */
    public function getPaginatedBySeeker(JobSeeker $jobSeeker, int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->paginateBySeekerId($jobSeeker->seeker_id, $perPage);
    }

    /**
     * Check if company is followed by seeker.
     */
    public function isCompanyFollowed(JobSeeker $jobSeeker, int $companyId): bool
    {
        return $this->repository->findBySeekerAndCompany($jobSeeker->seeker_id, $companyId) !== null;
    }

    /**
     * Follow a company.
     */
    public function followCompany(JobSeeker $jobSeeker, int $companyId): FollowedCompany
    {
        // Business logic: Check if already following
        $existing = $this->repository->findBySeekerAndCompany($jobSeeker->seeker_id, $companyId);
        if ($existing) {
            return $existing;
        }

        return $this->repository->create([
            'seeker_id' => $jobSeeker->seeker_id,
            'company_id' => $companyId,
            'followed_at' => now(),
        ]);
    }

    /**
     * Unfollow a company.
     */
    public function unfollowCompany(JobSeeker $jobSeeker, int $companyId): bool
    {
        return $this->repository->deleteBySeekerAndCompany($jobSeeker->seeker_id, $companyId);
    }
}
