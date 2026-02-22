<?php

namespace App\Repositories;

use App\Models\JobAdvertisement;
use App\Repositories\Contracts\JobAdvertisementRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class JobAdvertisementRepository implements JobAdvertisementRepositoryInterface
{
    public function all(): Collection
    {
        return JobAdvertisement::with(['company', 'category'])->get();
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return JobAdvertisement::with(['company', 'category'])
            ->where('status', 'published')
            ->where(function ($q) {
                $q->whereNull('published_at')
                    ->orWhere('published_at', '<=', now());
            })
            ->orderBy('published_at', 'desc')
            ->paginate($perPage);
    }

    public function find(int $id): ?JobAdvertisement
    {
        return JobAdvertisement::with(['company', 'category'])->find($id);
    }

    public function create(array $data): JobAdvertisement
    {
        return JobAdvertisement::create($data);
    }

    public function update(JobAdvertisement $job, array $data): JobAdvertisement
    {
        $job->update($data);
        return $job->fresh(['company', 'category']);
    }

    public function delete(JobAdvertisement $job): bool
    {
        return $job->delete();
    }

    public function findBySlug(string $slug): ?JobAdvertisement
    {
        return JobAdvertisement::with(['company', 'category'])
            ->where('slug', $slug)
            ->first();
    }

    public function getPublished(): Collection
    {
        return JobAdvertisement::with(['company', 'category'])
            ->where('status', 'published')
            ->where('published_at', '<=', now())
            ->orderBy('published_at', 'desc')
            ->get();
    }

    public function incrementViews(JobAdvertisement $job): void
    {
        $job->increment('views_count');
    }

    public function search(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        $query = JobAdvertisement::with(['company', 'category'])
            ->where('status', 'published')
            ->where(function ($q) {
                $q->whereNull('published_at')
                    ->orWhere('published_at', '<=', now());
            });

        // Search by keyword (title, description)
        if (isset($filters['keyword']) && !empty($filters['keyword'])) {
            $keyword = $filters['keyword'];
            $query->where(function ($q) use ($keyword) {
                $q->where('title', 'like', "%{$keyword}%")
                    ->orWhere('description', 'like', "%{$keyword}%");
            });
        }

        // Filter by category
        if (isset($filters['category_id']) && !empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        // Filter by location
        if (isset($filters['location']) && !empty($filters['location'])) {
            $query->where('location', 'like', "%{$filters['location']}%");
        }

        // Filter by contract type (using employment_type field)
        if (isset($filters['contract_type']) && !empty($filters['contract_type'])) {
            $query->where('employment_type', $filters['contract_type']);
        }

        // Filter by employment type
        if (isset($filters['employment_type']) && !empty($filters['employment_type'])) {
            $query->where('employment_type', $filters['employment_type']);
        }

        // Filter by remote option
        if (isset($filters['is_remote']) && $filters['is_remote'] !== '') {
            $isRemote = filter_var($filters['is_remote'], FILTER_VALIDATE_BOOLEAN);
            $query->where('is_remote', $isRemote);
        }

        // Filter by salary range
        if (isset($filters['salary_min']) && !empty($filters['salary_min'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('salary_max', '>=', $filters['salary_min'])
                    ->orWhereNull('salary_max');
            });
        }

        if (isset($filters['salary_max']) && !empty($filters['salary_max'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('salary_min', '<=', $filters['salary_max'])
                    ->orWhereNull('salary_min');
            });
        }

        // Handle sorting
        $sortBy = $filters['sort'] ?? 'latest';
        switch ($sortBy) {
            case 'oldest':
                $query->orderBy('published_at', 'asc');
                break;
            case 'salary_high':
                $query->orderByRaw('CAST(salary_max AS UNSIGNED) DESC');
                break;
            case 'salary_low':
                $query->orderByRaw('CAST(salary_min AS UNSIGNED) ASC');
                break;
            case 'latest':
            default:
                $query->orderBy('published_at', 'desc');
                break;
        }

        return $query->paginate($perPage);
    }

    public function getSimilarJobs(JobAdvertisement $job, int $limit = 5): Collection
    {
        return JobAdvertisement::with(['company', 'category'])
            ->where('status', 'published')
            ->where('id', '!=', $job->id)
            ->where(function ($q) use ($job) {
                // Similar by category
                if ($job->category_id) {
                    $q->where('category_id', $job->category_id);
                }
                // Or similar by location
                if ($job->location) {
                    $q->orWhere('location', 'like', "%{$job->location}%");
                }
            })
            ->where(function ($q) {
                $q->whereNull('published_at')
                    ->orWhere('published_at', '<=', now());
            })
            ->orderBy('published_at', 'desc')
            ->limit($limit)
            ->get();
    }

    public function getOtherCompanyJobs(int $companyId, int $excludeJobId, int $limit = 5): Collection
    {
        return JobAdvertisement::with(['company', 'category'])
            ->where('company_id', $companyId)
            ->where('id', '!=', $excludeJobId)
            ->where('status', 'published')
            ->where(function ($q) {
                $q->whereNull('published_at')
                    ->orWhere('published_at', '<=', now());
            })
            ->orderBy('published_at', 'desc')
            ->limit($limit)
            ->get();
    }

    public function getByCompanyId(int $companyId): Collection
    {
        return JobAdvertisement::with(['company', 'category'])
            ->where('company_id', $companyId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getActiveByCompanyId(int $companyId): Collection
    {
        return JobAdvertisement::with(['company', 'category'])
            ->where('company_id', $companyId)
            ->where('status', 'published')
            ->where(function ($q) {
                $q->whereNull('published_at')
                    ->orWhere('published_at', '<=', now());
            })
            ->orderBy('published_at', 'desc')
            ->get();
    }

    public function getRecentByCompanyId(int $companyId, int $limit = 5): Collection
    {
        return JobAdvertisement::with(['company', 'category'])
            ->where('company_id', $companyId)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }
}
