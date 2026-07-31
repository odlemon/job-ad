<?php

namespace App\Support;

use App\Models\CompanyReview;
use App\Models\FollowedCompany;
use App\Models\JobAdvertisement;
use App\Models\JobApplication;
use App\Models\SavedJob;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class ScoopJobPresenter
{
    /**
     * Present many jobs with batched company/seeker lookups (avoids N+1).
     *
     * @param  iterable<JobAdvertisement>  $jobs
     * @return list<array<string, mixed>>
     */
    public static function jobs(iterable $jobs, ?int $seekerId = null): array
    {
        $collection = $jobs instanceof \Illuminate\Database\Eloquent\Collection
            ? $jobs->values()
            : \Illuminate\Database\Eloquent\Collection::make(
                Collection::make($jobs)->filter()->values()->all()
            );

        if ($collection->isEmpty()) {
            return [];
        }

        $collection->loadMissing(['company', 'category']);

        if ($seekerId === null) {
            $seekerId = self::resolveSeekerId();
        }

        $companyIds = $collection->pluck('company_id')->filter()->unique()->values()->all();
        $jobIds = $collection->pluck('id')->filter()->map(fn ($id) => (int) $id)->values()->all();

        $reviewStats = [];
        if ($companyIds !== []) {
            $reviewStats = CompanyReview::query()
                ->whereIn('company_id', $companyIds)
                ->selectRaw('company_id, COUNT(*) as reviews_count, AVG(rating) as avg_rating')
                ->groupBy('company_id')
                ->get()
                ->keyBy('company_id')
                ->all();
        }

        $jobsCountByCompany = [];
        if ($companyIds !== []) {
            $jobsCountByCompany = JobAdvertisement::query()
                ->whereIn('company_id', $companyIds)
                ->where('status', 'published')
                ->selectRaw('company_id, COUNT(*) as jobs_count')
                ->groupBy('company_id')
                ->pluck('jobs_count', 'company_id')
                ->all();
        }

        $savedJobIds = [];
        $applicationStatusByJob = [];
        $followedCompanyIds = [];
        if ($seekerId && $jobIds !== []) {
            $savedJobIds = SavedJob::where('seeker_id', $seekerId)
                ->whereIn('job_id', $jobIds)
                ->pluck('job_id')
                ->mapWithKeys(fn ($id) => [(int) $id => true])
                ->all();

            $applicationStatusByJob = JobApplication::where('seeker_id', $seekerId)
                ->whereIn('job_advertisement_id', $jobIds)
                ->pluck('status', 'job_advertisement_id')
                ->mapWithKeys(fn ($status, $id) => [(int) $id => $status])
                ->all();
        }

        if ($seekerId && $companyIds !== []) {
            $followedCompanyIds = FollowedCompany::where('seeker_id', $seekerId)
                ->whereIn('company_id', $companyIds)
                ->pluck('company_id')
                ->mapWithKeys(fn ($id) => [(int) $id => true])
                ->all();
        }

        return $collection->map(function (JobAdvertisement $job) use (
            $seekerId,
            $reviewStats,
            $jobsCountByCompany,
            $savedJobIds,
            $applicationStatusByJob,
            $followedCompanyIds
        ) {
            return self::presentFromMaps(
                $job,
                $seekerId,
                $reviewStats,
                $jobsCountByCompany,
                $savedJobIds,
                $applicationStatusByJob,
                $followedCompanyIds
            );
        })->values()->all();
    }

    /**
     * @param  array<int, true>|null  $savedJobIds
     */
    public static function job(JobAdvertisement $job, ?int $seekerId = null, ?array $savedJobIds = null): array
    {
        return self::jobs([$job], $seekerId)[0]
            ?? self::presentFromMaps($job, $seekerId, [], [], $savedJobIds ?? [], [], []);
    }

    private static function resolveSeekerId(): ?int
    {
        $user = Auth::guard('sanctum')->user() ?? Auth::user();
        if ($user && $user->user_type === 'job_seeker') {
            return $user->jobSeeker?->seeker_id;
        }

        return null;
    }

    /**
     * @param  array<int|string, object>  $reviewStats
     * @param  array<int|string, int|string>  $jobsCountByCompany
     * @param  array<int, true>  $savedJobIds
     * @param  array<int, string|null>  $applicationStatusByJob
     * @param  array<int, true>  $followedCompanyIds
     */
    private static function presentFromMaps(
        JobAdvertisement $job,
        ?int $seekerId,
        array $reviewStats,
        array $jobsCountByCompany,
        array $savedJobIds,
        array $applicationStatusByJob,
        array $followedCompanyIds = []
    ): array {
        $job->loadMissing(['company', 'category']);
        $company = $job->company;
        $companyId = $company?->id;

        $review = $companyId ? ($reviewStats[$companyId] ?? null) : null;
        $reviewCount = (int) ($review->reviews_count ?? 0);
        $avgRating = $reviewCount > 0
            ? (float) round((float) ($review->avg_rating ?? 0), 1)
            : 0.0;

        $jobsCount = $companyId ? (int) ($jobsCountByCompany[$companyId] ?? 0) : 0;
        $isSaved = isset($savedJobIds[(int) $job->id]);
        $applicationStatus = $applicationStatusByJob[(int) $job->id] ?? null;
        $isFollowing = $companyId ? isset($followedCompanyIds[(int) $companyId]) : false;

        return [
            'id' => $job->id,
            'title' => $job->title,
            'employer_name' => $company?->name,
            'employer_id' => $company?->id,
            'verified' => (bool) ($company?->is_verified ?? $company?->is_active ?? false),
            'location' => $job->location,
            'salary_min' => $job->salary_min,
            'salary_max' => $job->salary_max,
            'salary_currency' => $job->salary_currency ?? 'SCR',
            'category' => $job->category ? [
                'id' => $job->category->id,
                'name' => $job->category->name,
            ] : null,
            'job_type' => [
                'id' => 1,
                'name' => $job->employment_type ?? $job->contract_type ?? 'Full-time',
            ],
            'positions_available' => $job->positions_available ?? 1,
            'created_at' => optional($job->created_at)?->toIso8601String(),
            'expiry_date' => optional($job->application_deadline ?? $job->expires_at)?->toIso8601String(),
            'description' => $job->description,
            'is_saved' => $isSaved,
            'has_applied' => $applicationStatus !== null,
            'application_status' => $applicationStatus,
            'is_following' => $isFollowing,
            'company' => $company ? [
                'id' => $company->id,
                'name' => $company->name,
            ] : null,
            'employer' => $company ? [
                'id' => $company->id,
                'name' => $company->name,
                'size' => $company->size,
                'industry' => $company->industry,
                'rating' => $avgRating,
                'reviews_count' => $reviewCount,
                'jobs_count' => $jobsCount,
                'location' => $company->location ?? $company->city,
                'website' => $company->website,
                'email' => $company->email,
                'phone' => $company->phone,
                'working_hours' => $company->working_hours,
                'about_us' => $company->description ?? $company->about,
                'logo_url' => $company->logo ?? $company->logo_url,
                'cover_url' => $company->cover_image ?? $company->cover_url,
                'is_following' => $isFollowing,
            ] : null,
        ];
    }
}
