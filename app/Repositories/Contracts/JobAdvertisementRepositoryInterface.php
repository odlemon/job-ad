<?php

namespace App\Repositories\Contracts;

use App\Models\JobAdvertisement;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface JobAdvertisementRepositoryInterface
{
    public function all(): Collection;
    public function paginate(int $perPage = 15): LengthAwarePaginator;
    public function find(int $id): ?JobAdvertisement;
    public function create(array $data): JobAdvertisement;
    public function update(JobAdvertisement $job, array $data): JobAdvertisement;
    public function delete(JobAdvertisement $job): bool;
    public function findBySlug(string $slug): ?JobAdvertisement;
    public function getPublished(): Collection;
    public function search(array $filters, int $perPage = 15): LengthAwarePaginator;
    public function incrementViews(JobAdvertisement $job): void;
    public function getSimilarJobs(JobAdvertisement $job, int $limit = 5): Collection;
    public function getOtherCompanyJobs(int $companyId, int $excludeJobId, int $limit = 5): Collection;
    public function getByCompanyId(int $companyId): Collection;
    public function getActiveByCompanyId(int $companyId): Collection;
    public function getRecentByCompanyId(int $companyId, int $limit = 5): Collection;
}
