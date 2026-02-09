<?php

namespace App\Repositories\Contracts;

use App\Models\SavedJob;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface SavedJobRepositoryInterface
{
    public function getBySeekerId(int $seekerId): Collection;
    public function paginateBySeekerId(int $seekerId, int $perPage = 15): LengthAwarePaginator;
    public function find(int $id): ?SavedJob;
    public function findBySeekerAndJob(int $seekerId, int $jobId): ?SavedJob;
    public function create(array $data): SavedJob;
    public function delete(SavedJob $savedJob): bool;
    public function deleteBySeekerAndJob(int $seekerId, int $jobId): bool;
}
