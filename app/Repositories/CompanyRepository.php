<?php

namespace App\Repositories;

use App\Models\Company;
use App\Repositories\Contracts\CompanyRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class CompanyRepository implements CompanyRepositoryInterface
{
    public function all(): Collection
    {
        return Company::withCount('jobAdvertisements')->get();
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return Company::paginate($perPage);
    }

    public function find(int $id): ?Company
    {
        return Company::find($id);
    }

    public function create(array $data): Company
    {
        return Company::create($data);
    }

    public function update(Company $company, array $data): Company
    {
        $company->update($data);
        return $company->fresh();
    }

    public function delete(Company $company): bool
    {
        return $company->delete();
    }

    public function findBySlug(string $slug): ?Company
    {
        return Company::where('slug', $slug)->first();
    }
}
