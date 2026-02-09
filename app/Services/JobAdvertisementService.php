<?php

namespace App\Services;

use App\Models\JobAdvertisement;
use App\Repositories\Contracts\JobAdvertisementRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;

class JobAdvertisementService
{
    public function __construct(
        private JobAdvertisementRepositoryInterface $repository
    ) {
    }

    public function getAll(): Collection
    {
        return $this->repository->all();
    }

    public function getPublished(): Collection
    {
        return $this->repository->getPublished();
    }

    public function getPaginated(int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->paginate($perPage);
    }

    public function getById(int $id): ?JobAdvertisement
    {
        return $this->repository->find($id);
    }

    public function getBySlug(string $slug): ?JobAdvertisement
    {
        return $this->repository->findBySlug($slug);
    }

    public function create(array $data): JobAdvertisement
    {
        // Business logic: Auto-generate slug if not provided
        if (!isset($data['slug']) && isset($data['title'])) {
            $data['slug'] = Str::slug($data['title']);
        }

        // Business logic: Set published_at if status is published
        if (isset($data['status']) && $data['status'] === 'published' && !isset($data['published_at'])) {
            $data['published_at'] = now();
        }

        return $this->repository->create($data);
    }

    public function update(JobAdvertisement $job, array $data): JobAdvertisement
    {
        // Business logic: Auto-update slug if title changed
        if (isset($data['title']) && $data['title'] !== $job->title) {
            $data['slug'] = Str::slug($data['title']);
        }

        // Business logic: Set published_at when status changes to published
        if (isset($data['status']) && $data['status'] === 'published' && $job->status !== 'published') {
            $data['published_at'] = now();
        }

        return $this->repository->update($job, $data);
    }

    public function delete(JobAdvertisement $job): bool
    {
        // Business logic: Check if job has applications before deletion
        // This is a template - add actual validation logic here
        return $this->repository->delete($job);
    }

    public function incrementViews(JobAdvertisement $job): void
    {
        $this->repository->incrementViews($job);
    }

    public function getSimilarJobs(JobAdvertisement $job, int $limit = 5): Collection
    {
        return $this->repository->getSimilarJobs($job, $limit);
    }

    public function getOtherCompanyJobs(int $companyId, int $excludeJobId, int $limit = 5): Collection
    {
        return $this->repository->getOtherCompanyJobs($companyId, $excludeJobId, $limit);
    }

    public function search(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->search($filters, $perPage);
    }
}
