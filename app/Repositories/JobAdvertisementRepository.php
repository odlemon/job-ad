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

        // Filter by category (supports single id or list from Scoop CSV categories)
        if (isset($filters['category_id']) && $filters['category_id'] !== '' && $filters['category_id'] !== []) {
            $categoryIds = is_array($filters['category_id'])
                ? $filters['category_id']
                : [$filters['category_id']];
            $categoryIds = array_values(array_filter($categoryIds, fn ($id) => $id !== null && $id !== ''));
            if (count($categoryIds) === 1) {
                $query->where('category_id', $categoryIds[0]);
            } elseif (count($categoryIds) > 1) {
                $query->whereIn('category_id', $categoryIds);
            }
        }

        // Filter by location
        if (isset($filters['location']) && !empty($filters['location'])) {
            $locations = is_array($filters['location'])
                ? $filters['location']
                : array_map('trim', explode(',', (string) $filters['location']));
            $locations = array_values(array_filter($locations));

            if (!empty($locations)) {
                $query->where(function ($q) use ($locations) {
                    foreach ($locations as $loc) {
                        $q->orWhere('location', 'like', "%{$loc}%");
                    }
                });
            }
        }

        // Filter by contract / job type (CSV from Scoop filters)
        if (isset($filters['contract_type']) && !empty($filters['contract_type'])) {
            $types = is_array($filters['contract_type'])
                ? $filters['contract_type']
                : array_map('trim', explode(',', (string) $filters['contract_type']));
            $types = array_values(array_filter($types));

            if (count($types) === 1) {
                $query->where(function ($q) use ($types) {
                    $q->where('employment_type', $types[0])
                        ->orWhere('employment_type', 'like', '%'.$types[0].'%');
                });
            } elseif (count($types) > 1) {
                $query->where(function ($q) use ($types) {
                    foreach ($types as $type) {
                        $q->orWhere('employment_type', $type)
                            ->orWhere('employment_type', 'like', '%'.$type.'%');
                    }
                });
            }
        }

        // Filter by employment type
        if (isset($filters['employment_type']) && !empty($filters['employment_type'])) {
            $employmentTypes = is_array($filters['employment_type'])
                ? $filters['employment_type']
                : array_map('trim', explode(',', (string) $filters['employment_type']));
            $employmentTypes = array_values(array_filter($employmentTypes));

            if (count($employmentTypes) === 1) {
                $query->where('employment_type', $employmentTypes[0]);
            } elseif (count($employmentTypes) > 1) {
                $query->whereIn('employment_type', $employmentTypes);
            }
        }

        // Filter by experience/job tags (comma separated)
        if (isset($filters['experience_tags']) && !empty($filters['experience_tags'])) {
            $tags = is_array($filters['experience_tags'])
                ? $filters['experience_tags']
                : array_map('trim', explode(',', (string) $filters['experience_tags']));
            $tags = array_values(array_filter($tags));

            if (!empty($tags)) {
                $query->where(function ($q) use ($tags) {
                    foreach ($tags as $tag) {
                        $normalized = strtolower($tag);
                        if ($normalized === 'open to everyone') {
                            $q->orWhereNull('experience_level')
                                ->orWhere('experience_level', '')
                                ->orWhere('experience_level', 'like', '%open to everyone%');
                        } elseif ($normalized === 'work experience') {
                            $q->orWhere('experience_level', 'like', '%work experience%')
                                ->orWhere('experience_level', 'like', '%mid%')
                                ->orWhere('experience_level', 'like', '%senior%')
                                ->orWhere('experience_level', 'like', '%entry%');
                        } else {
                            $q->orWhere('experience_level', 'like', '%' . $tag . '%');
                        }
                    }
                });
            }
        }

        // Filter by remote option
        if (isset($filters['is_remote']) && $filters['is_remote'] !== '') {
            $isRemote = filter_var($filters['is_remote'], FILTER_VALIDATE_BOOLEAN);
            $query->where('is_remote', $isRemote);
        }

        // Filter by education level (Scoop / job search — supports CSV)
        if (isset($filters['education']) && $filters['education'] !== '' && $filters['education'] !== []) {
            $levels = is_array($filters['education'])
                ? $filters['education']
                : array_map('trim', explode(',', (string) $filters['education']));
            $levels = array_values(array_filter($levels));

            if (! empty($levels)) {
                $query->where(function ($q) use ($levels) {
                    foreach ($levels as $education) {
                        $q->orWhere('education_level', 'like', "%{$education}%")
                            ->orWhere('education_level', $education);
                    }
                });
            }
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
