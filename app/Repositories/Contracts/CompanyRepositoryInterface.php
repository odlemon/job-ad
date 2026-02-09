<?php

namespace App\Repositories\Contracts;

use App\Models\Company;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface CompanyRepositoryInterface
{
    public function all(): Collection;
    public function paginate(int $perPage = 15): LengthAwarePaginator;
    public function find(int $id): ?Company;
    public function create(array $data): Company;
    public function update(Company $company, array $data): Company;
    public function delete(Company $company): bool;
    public function findBySlug(string $slug): ?Company;
}
