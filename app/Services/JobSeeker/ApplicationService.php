<?php

namespace App\Services\JobSeeker;

use App\Models\JobApplication;
use App\Models\JobSeeker;
use App\Repositories\Contracts\JobApplicationRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class ApplicationService
{
    public function __construct(
        private JobApplicationRepositoryInterface $repository
    ) {
    }

    /**
     * Get all applications for a job seeker.
     */
    public function getBySeeker(JobSeeker $jobSeeker): Collection
    {
        return $this->repository->getBySeekerId($jobSeeker->seeker_id);
    }

    /**
     * Get paginated applications for a job seeker.
     */
    public function getPaginatedBySeeker(JobSeeker $jobSeeker, int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->paginateBySeekerId($jobSeeker->seeker_id, $perPage);
    }

    /**
     * Get application by ID.
     */
    public function getById(int $id): ?JobApplication
    {
        return $this->repository->find($id);
    }

    /**
     * Submit a new job application.
     */
    public function apply(JobSeeker $jobSeeker, array $data): JobApplication
    {
        // Business logic: Check if already applied to this job
        $existing = $this->repository->getBySeekerId($jobSeeker->seeker_id)
            ->where('job_advertisement_id', $data['job_advertisement_id'])
            ->first();

        if ($existing) {
            throw new \Exception('You have already applied to this job.');
        }

        // Business logic: Set default status if not provided
        if (!isset($data['status'])) {
            $data['status'] = 'pending';
        }

        // Business logic: Link to job seeker
        $data['seeker_id'] = $jobSeeker->seeker_id;
        $data['user_id'] = $jobSeeker->user_id;

        // Business logic: Use job seeker's info if not provided
        if (!isset($data['first_name'])) {
            $data['first_name'] = $jobSeeker->first_name;
        }
        if (!isset($data['last_name'])) {
            $data['last_name'] = $jobSeeker->last_name;
        }
        if (!isset($data['email'])) {
            $data['email'] = $jobSeeker->user->email;
        }

        $application = $this->repository->create($data);

        // Business logic: Increment application count on job advertisement
        // This will be handled in the JobAdvertisementService later

        return $application;
    }

    /**
     * Update application status.
     */
    public function updateStatus(JobApplication $application, string $status, ?int $reviewedBy = null): JobApplication
    {
        $data = [
            'status' => $status,
        ];

        // Business logic: Set reviewed_at when status changes from pending
        if ($application->status === 'pending' && $status !== 'pending') {
            $data['reviewed_at'] = now();
            if ($reviewedBy) {
                $data['reviewed_by'] = $reviewedBy;
            }
        }

        return $this->repository->update($application, $data);
    }

    /**
     * Check if a job seeker has already applied to a job.
     */
    public function hasApplied(string $seekerId, int $jobAdvertisementId): bool
    {
        return $this->repository->getBySeekerId($seekerId)
            ->where('job_advertisement_id', $jobAdvertisementId)
            ->exists();
    }

    /**
     * Withdraw application.
     * Note: We'll delete the application instead of changing status to 'withdrawn'
     * since 'withdrawn' is not in the enum. Alternatively, we could add it to the enum.
     */
    public function withdraw(JobApplication $application): bool
    {
        // For now, we'll just delete the application
        // In the future, we might want to add 'withdrawn' to the status enum
        return $this->repository->delete($application);
    }
}
