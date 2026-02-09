<?php

namespace App\Repositories;

use App\Models\JobCategory;
use App\Repositories\Contracts\JobCategoryRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class JobCategoryRepository implements JobCategoryRepositoryInterface
{
    public function all(): Collection
    {
        return JobCategory::withCount('jobAdvertisements')->get();
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return JobCategory::orderBy('sort_order')->paginate($perPage);
    }

    public function find(int $id): ?JobCategory
    {
        return JobCategory::find($id);
    }

    public function create(array $data): JobCategory
    {
        return JobCategory::create($data);
    }

    public function update(JobCategory $category, array $data): JobCategory
    {
        $category->update($data);
        return $category->fresh();
    }

    public function delete(JobCategory $category): bool
    {
        return $category->delete();
    }

    public function findBySlug(string $slug): ?JobCategory
    {
        return JobCategory::where('slug', $slug)->first();
    }

    public function getActive(): Collection
    {
        return JobCategory::where('is_active', true)
            ->orderBy('sort_order')
            ->get();
    }
}
