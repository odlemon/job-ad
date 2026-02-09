<?php

namespace App\Repositories\Contracts;

use App\Models\JobCategory;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface JobCategoryRepositoryInterface
{
    public function all(): Collection;
    public function paginate(int $perPage = 15): LengthAwarePaginator;
    public function find(int $id): ?JobCategory;
    public function create(array $data): JobCategory;
    public function update(JobCategory $category, array $data): JobCategory;
    public function delete(JobCategory $category): bool;
    public function findBySlug(string $slug): ?JobCategory;
    public function getActive(): Collection;
}
