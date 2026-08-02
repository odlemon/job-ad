<?php

namespace App\Repositories;

use App\Models\FollowedCompany;
use App\Repositories\Contracts\FollowedCompanyRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class FollowedCompanyRepository implements FollowedCompanyRepositoryInterface
{
    public function getBySeekerId(int $seekerId): Collection
    {
        return FollowedCompany::with(['company' => function ($query) {
            $query->withCount([
                'jobAdvertisements as jobs_count' => function ($jobs) {
                    $jobs->where('status', 'published');
                },
                'reviews as reviews_count',
            ])->withAvg('reviews', 'rating');
        }])
            ->where('seeker_id', $seekerId)
            ->orderBy('followed_at', 'desc')
            ->get();
    }

    public function paginateBySeekerId(int $seekerId, int $perPage = 15): LengthAwarePaginator
    {
        return FollowedCompany::with(['company' => function ($query) {
            $query->withCount([
                'jobAdvertisements as jobs_count' => function ($jobs) {
                    $jobs->where('status', 'published');
                },
                'reviews as reviews_count',
            ])->withAvg('reviews', 'rating');
        }])
            ->where('seeker_id', $seekerId)
            ->orderBy('followed_at', 'desc')
            ->paginate($perPage);
    }

    public function find(int $id): ?FollowedCompany
    {
        return FollowedCompany::with(['jobSeeker', 'company'])->find($id);
    }

    public function findBySeekerAndCompany(int $seekerId, int $companyId): ?FollowedCompany
    {
        return FollowedCompany::where('seeker_id', $seekerId)
            ->where('company_id', $companyId)
            ->first();
    }

    public function create(array $data): FollowedCompany
    {
        return FollowedCompany::create($data);
    }

    public function delete(FollowedCompany $followedCompany): bool
    {
        return $followedCompany->delete();
    }

    public function deleteBySeekerAndCompany(int $seekerId, int $companyId): bool
    {
        return FollowedCompany::where('seeker_id', $seekerId)
            ->where('company_id', $companyId)
            ->delete() > 0;
    }
}
