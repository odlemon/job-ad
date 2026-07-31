<?php

namespace App\Repositories\Contracts;

use App\Models\JobApplication;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface JobApplicationRepositoryInterface
{
    public function all(): Collection;
    public function paginate(int $perPage = 15): LengthAwarePaginator;
    public function find(int $id): ?JobApplication;
    public function create(array $data): JobApplication;
    public function update(JobApplication $application, array $data): JobApplication;
    public function delete(JobApplication $application): bool;
    public function getByJobAdvertisement(int $jobId): Collection;
    public function getByStatus(string $status): Collection;
    public function getBySeekerId(int $seekerId): Collection;
    public function paginateBySeekerId(int $seekerId, int $perPage = 15, ?array $statuses = null): LengthAwarePaginator;
    public function getByUserId(int $userId): Collection;
    public function paginateByUserId(int $userId, int $perPage = 15): LengthAwarePaginator;
    public function getByCompanyId(int $companyId): Collection;
    public function getRecentByCompanyId(int $companyId, int $limit = 5): Collection;
    public function getByStatusAndCompanyId(string $status, int $companyId): Collection;
    public function countByCompanyId(int $companyId): int;
}
