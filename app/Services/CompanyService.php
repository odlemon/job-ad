<?php

namespace App\Services;

use App\Models\Company;
use App\Repositories\Contracts\CompanyRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;

class CompanyService
{
    public function __construct(
        private CompanyRepositoryInterface $repository
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

    public function getById(int $id): ?Company
    {
        return $this->repository->find($id);
    }

    public function getBySlug(string $slug): ?Company
    {
        return $this->repository->findBySlug($slug);
    }

    public function create(array $data): Company
    {
        // Business logic: Auto-generate slug if not provided
        if (!isset($data['slug']) && isset($data['name'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        return $this->repository->create($data);
    }

    public function update(Company $company, array $data): Company
    {
        // Business logic: Auto-update slug if name changed
        if (isset($data['name']) && $data['name'] !== $company->name) {
            $data['slug'] = Str::slug($data['name']);
        }

        return $this->repository->update($company, $data);
    }

    public function delete(Company $company): bool
    {
        // Business logic: Check if company has active jobs before deletion
        // This is a template - add actual validation logic here
        return $this->repository->delete($company);
    }
}
