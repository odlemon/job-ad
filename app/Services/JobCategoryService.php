<?php

namespace App\Services;

use App\Models\JobCategory;
use App\Repositories\Contracts\JobCategoryRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;

class JobCategoryService
{
    public function __construct(
        private JobCategoryRepositoryInterface $repository
    ) {
    }

    public function getAll(): Collection
    {
        return $this->repository->all();
    }

    public function getActive(): Collection
    {
        return $this->repository->getActive();
    }

    public function getPaginated(int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->paginate($perPage);
    }

    public function getById(int $id): ?JobCategory
    {
        return $this->repository->find($id);
    }

    public function getBySlug(string $slug): ?JobCategory
    {
        return $this->repository->findBySlug($slug);
    }

    public function create(array $data): JobCategory
    {
        // Business logic: Auto-generate slug if not provided
        if (!isset($data['slug']) && isset($data['name'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        return $this->repository->create($data);
    }

    public function update(JobCategory $category, array $data): JobCategory
    {
        // Business logic: Auto-update slug if name changed
        if (isset($data['name']) && $data['name'] !== $category->name) {
            $data['slug'] = Str::slug($data['name']);
        }

        return $this->repository->update($category, $data);
    }

    public function delete(JobCategory $category): bool
    {
        // Business logic: Check if category has jobs before deletion
        // This is a template - add actual validation logic here
        return $this->repository->delete($category);
    }
}
