<?php

namespace App\Services;

use App\Models\Company;
use App\Models\CompanyReview;
use App\Models\FollowedCompany;
use App\Models\JobAdvertisement;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;

class CompanyPublicService
{
    public function __construct(
        protected RemoteUploadService $uploads
    ) {}

    public function mediaUrl(?string $path): ?string
    {
        if (! $path) {
            return null;
        }
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        return rtrim($this->uploads->getMediaBaseUrl(), '/') . '/' . ltrim($path, '/');
    }

    public function industries()
    {
        return Cache::remember('public_company_industries', 3600, function () {
            return Company::query()
                ->where('is_active', true)
                ->whereNotNull('industry')
                ->where('industry', '!=', '')
                ->distinct()
                ->orderBy('industry')
                ->pluck('industry')
                ->values();
        });
    }

    public function listQuery(array $filters): Builder
    {
        $query = Company::query()
            ->where('is_active', true)
            ->select([
                'id', 'name', 'slug', 'logo', 'industry', 'location', 'size',
                'verified_at', 'created_at',
            ])
            ->withCount([
                'jobAdvertisements as jobs_count' => fn ($q) => $q->where('status', 'published'),
                'reviews as reviews_count',
            ])
            ->withAvg('reviews as avg_rating', 'rating');

        if (! empty($filters['search'])) {
            $s = $filters['search'];
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', '%' . $s . '%')
                    ->orWhere('industry', 'like', '%' . $s . '%')
                    ->orWhere('location', 'like', '%' . $s . '%');
            });
        }

        $industries = $filters['industries'] ?? $filters['industry'] ?? null;
        if (is_string($industries) && $industries !== '') {
            $industries = array_values(array_filter(array_map('trim', explode(',', $industries))));
        }
        if (is_array($industries) && count($industries) > 0) {
            $query->whereIn('industry', $industries);
        }

        $jobs = $filters['jobs'] ?? null;
        if (in_array($jobs, ['available', '1-10', '11-20', '21-30', '30+'], true)) {
            $published = 'published';
            match ($jobs) {
                'available' => $query->whereHas('jobAdvertisements', fn ($q) => $q->where('status', $published)),
                '1-10' => $query->whereRaw(
                    '(select count(*) from job_advertisements where companies.id = job_advertisements.company_id and status = ? and job_advertisements.deleted_at is null) between 1 and 10',
                    [$published]
                ),
                '11-20' => $query->whereRaw(
                    '(select count(*) from job_advertisements where companies.id = job_advertisements.company_id and status = ? and job_advertisements.deleted_at is null) between 11 and 20',
                    [$published]
                ),
                '21-30' => $query->whereRaw(
                    '(select count(*) from job_advertisements where companies.id = job_advertisements.company_id and status = ? and job_advertisements.deleted_at is null) between 21 and 30',
                    [$published]
                ),
                '30+' => $query->whereRaw(
                    '(select count(*) from job_advertisements where companies.id = job_advertisements.company_id and status = ? and job_advertisements.deleted_at is null) > 30',
                    [$published]
                ),
                default => null,
            };
        }

        $sort = $filters['sort'] ?? 'jobs';
        match ($sort) {
            'rating' => $query->orderByDesc('avg_rating')->orderByDesc('reviews_count'),
            'name' => $query->orderBy('name'),
            'newest' => $query->orderByDesc('created_at'),
            default => $query->orderByDesc('jobs_count')->orderBy('name'),
        };

        return $query;
    }

    public function paginateList(array $filters, int $perPage = 24): LengthAwarePaginator
    {
        $perPage = max(1, min(48, $perPage));

        return $this->listQuery($filters)->paginate($perPage)->withQueryString();
    }

    public function mapListItem(Company $c): array
    {
        return [
            'id' => $c->id,
            'name' => $c->name,
            'slug' => $c->slug,
            'logo_url' => $this->mediaUrl($c->logo),
            'industry' => $c->industry,
            'location' => $c->location,
            'size' => $c->size,
            'verified' => (bool) $c->verified_at,
            'jobs_count' => (int) ($c->jobs_count ?? $c->job_advertisements_count ?? 0),
            'reviews_count' => (int) ($c->reviews_count ?? 0),
            'avg_rating' => $c->avg_rating !== null ? round((float) $c->avg_rating, 1) : 0,
            'url' => route('companies.show', $c->slug ?: $c->id),
        ];
    }

    public function resolve(string $idOrSlug): ?Company
    {
        $query = Company::query()->where('is_active', true);

        if (ctype_digit($idOrSlug)) {
            return (clone $query)->where('id', (int) $idOrSlug)->first()
                ?? (clone $query)->where('slug', $idOrSlug)->first();
        }

        return $query->where('slug', $idOrSlug)->first();
    }

    public function isFollowing(?User $user, int $companyId): bool
    {
        if (! $user || $user->user_type !== 'job_seeker') {
            return false;
        }
        $seeker = $user->jobSeeker;
        if (! $seeker) {
            return false;
        }

        return FollowedCompany::query()
            ->where('company_id', $companyId)
            ->where('seeker_id', $seeker->seeker_id)
            ->exists();
    }

    public function mapDetail(Company $company, ?User $user = null): array
    {
        $company->loadCount([
            'jobAdvertisements as jobs_count' => fn ($q) => $q->where('status', 'published'),
            'followers as followers_count',
            'reviews as reviews_count',
        ]);
        $company->loadAvg('reviews as avg_rating', 'rating');

        $gallery = collect($company->gallery_images ?? [])
            ->map(fn ($img) => is_string($img) ? $this->mediaUrl($img) : ($img['url'] ?? null))
            ->filter()
            ->values()
            ->all();

        return [
            'id' => $company->id,
            'name' => $company->name,
            'slug' => $company->slug,
            'logo_url' => $this->mediaUrl($company->logo),
            'cover_url' => $this->mediaUrl($company->cover_image),
            'industry' => $company->industry,
            'location' => $company->location,
            'size' => $company->size,
            'founded_year' => $company->founded_year,
            'registration_number' => $company->registration_number,
            'verified' => (bool) $company->verified_at,
            'description' => $company->description,
            'website' => $company->website,
            'email' => $company->email,
            'phone' => $company->phone,
            'working_hours' => $company->working_hours,
            'workplace_description' => $company->workplace_description,
            'culture_benefits' => $company->culture_benefits,
            'benefits' => $company->benefits ?? [],
            'company_values' => $company->company_values ?? [],
            'faqs' => $company->faqs ?? [],
            'gallery' => $gallery,
            'linkedin' => $company->linkedin,
            'twitter' => $company->twitter,
            'facebook' => $company->facebook,
            'instagram' => $company->instagram,
            'jobs_count' => (int) ($company->jobs_count ?? 0),
            'followers_count' => (int) ($company->followers_count ?? 0),
            'reviews_count' => (int) ($company->reviews_count ?? 0),
            'avg_rating' => $company->avg_rating !== null ? round((float) $company->avg_rating, 1) : 0,
            'is_following' => $this->isFollowing($user, $company->id),
            'url' => route('companies.show', $company->slug ?: $company->id),
        ];
    }

    public function jobsFor(Company $company, int $page = 1, int $perPage = 10): LengthAwarePaginator
    {
        $perPage = max(1, min(30, $perPage));

        return JobAdvertisement::query()
            ->where('company_id', $company->id)
            ->where('status', 'published')
            ->with(['category:id,name'])
            ->latest('published_at')
            ->paginate($perPage, ['*'], 'page', $page);
    }

    public function mapJob(JobAdvertisement $job): array
    {
        return [
            'id' => $job->id,
            'title' => $job->title,
            'slug' => $job->slug ?? null,
            'location' => $job->location,
            'job_type' => $job->employment_type ?? $job->job_type ?? null,
            'category' => $job->category?->name,
            'published_at' => $job->published_at?->toDateString(),
            'url' => url('/jobs/' . $job->id),
        ];
    }

    public function reviewsFor(Company $company, int $page = 1, int $perPage = 10, string $sort = 'newest'): LengthAwarePaginator
    {
        $perPage = max(1, min(50, $perPage));
        $query = CompanyReview::query()->where('company_id', $company->id);

        match ($sort) {
            'highest' => $query->orderByDesc('rating')->orderByDesc('created_at'),
            'lowest' => $query->orderBy('rating')->orderByDesc('created_at'),
            'helpful' => $query->orderByDesc('helpful_count')->orderByDesc('created_at'),
            default => $query->latest('created_at'),
        };

        return $query->paginate($perPage, ['*'], 'page', $page);
    }

    public function mapReview(CompanyReview $r): array
    {
        return [
            'id' => $r->id,
            'rating' => (int) $r->rating,
            'role' => $r->role,
            'location' => $r->location,
            'employment_status' => $r->employment_status,
            'good_things' => $r->good_things,
            'challenges' => $r->challenges,
            'helpful_count' => (int) ($r->helpful_count ?? 0),
            'created_at' => $r->created_at?->format('M Y'),
            'created_at_raw' => $r->created_at?->toIso8601String(),
        ];
    }

    public function reviewStats(int $companyId): array
    {
        $stats = CompanyReview::query()
            ->where('company_id', $companyId)
            ->selectRaw('COUNT(*) as reviews_count')
            ->selectRaw('AVG(rating) as avg_rating')
            ->selectRaw('AVG(work_life_balance) as work_life_balance')
            ->selectRaw('AVG(benefits_perks) as benefits_perks')
            ->selectRaw('AVG(work_environment_culture) as work_environment_culture')
            ->selectRaw('AVG(career_growth_development) as career_growth_development')
            ->selectRaw('AVG(management_leadership) as management_leadership')
            ->selectRaw('AVG(employee_support_wellbeing) as employee_support_wellbeing')
            ->first();

        $reviewsCount = (int) ($stats->reviews_count ?? 0);
        $avgRating = $reviewsCount > 0 ? round((float) ($stats->avg_rating ?? 0), 1) : 0;

        $starDistribution = [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0];
        if ($reviewsCount > 0) {
            $rows = CompanyReview::query()
                ->where('company_id', $companyId)
                ->selectRaw('rating, COUNT(*) as total')
                ->groupBy('rating')
                ->pluck('total', 'rating');
            foreach ($rows as $rating => $total) {
                $key = (int) $rating;
                if (isset($starDistribution[$key])) {
                    $starDistribution[$key] = (int) $total;
                }
            }
        }

        $categoryLabels = [
            'work_life_balance' => 'Work-Life Balance',
            'career_growth_development' => 'Career Growth & Development',
            'benefits_perks' => 'Benefits & Perks',
            'management_leadership' => 'Management & Leadership',
            'work_environment_culture' => 'Work Environment & Culture',
            'employee_support_wellbeing' => 'Employee Support & Well-Being',
        ];
        $categoryAverages = [];
        $categoryCounts = [];
        foreach (array_keys($categoryLabels) as $key) {
            $avg = $stats->{$key} ?? null;
            $categoryAverages[$key] = $avg !== null ? round((float) $avg, 1) : null;
            $categoryCounts[$key] = $avg !== null ? $reviewsCount : 0;
        }

        return compact('reviewsCount', 'avgRating', 'starDistribution', 'categoryLabels', 'categoryAverages', 'categoryCounts');
    }
}
