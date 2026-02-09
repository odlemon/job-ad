<?php

namespace App\Repositories\Contracts;

use App\Models\FollowedCompany;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface FollowedCompanyRepositoryInterface
{
    public function getBySeekerId(int $seekerId): Collection;
    public function paginateBySeekerId(int $seekerId, int $perPage = 15): LengthAwarePaginator;
    public function find(int $id): ?FollowedCompany;
    public function findBySeekerAndCompany(int $seekerId, int $companyId): ?FollowedCompany;
    public function create(array $data): FollowedCompany;
    public function delete(FollowedCompany $followedCompany): bool;
    public function deleteBySeekerAndCompany(int $seekerId, int $companyId): bool;
}
