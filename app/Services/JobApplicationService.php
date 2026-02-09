<?php

namespace App\Services;

use App\Models\JobApplication;
use App\Repositories\Contracts\JobApplicationRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class JobApplicationService
{
    public function __construct(
        private JobApplicationRepositoryInterface $repository
    ) {
    }

    public function getAll(): Collection
    {
        return $this->repository->all();
    }

    public function getPaginated(int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->paginate($perPage);
    }

    public function getById(int $id): ?JobApplication
    {
        return $this->repository->find($id);
    }

    public function getByJobAdvertisement(int $jobId): Collection
    {
        return $this->repository->getByJobAdvertisement($jobId);
    }

    public function getByStatus(string $status): Collection
    {
        return $this->repository->getByStatus($status);
    }

    public function create(array $data): JobApplication
    {
        // Business logic: Set default status if not provided
        if (!isset($data['status'])) {
            $data['status'] = 'pending';
        }

        // Business logic: Increment application count on job advertisement
        // This is a template - add actual logic here after creating the application

        return $this->repository->create($data);
    }

    public function update(JobApplication $application, array $data): JobApplication
    {
        // Business logic: Set reviewed_at when status changes from pending
        if (isset($data['status']) && $application->status === 'pending' && $data['status'] !== 'pending') {
            $data['reviewed_at'] = now();
        }

        return $this->repository->update($application, $data);
    }

    public function delete(JobApplication $application): bool
    {
        // Business logic: Decrement application count on job advertisement
        // This is a template - add actual logic here before deletion

        return $this->repository->delete($application);
    }
}
