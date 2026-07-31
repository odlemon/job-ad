<?php

namespace App\Services\Employer;

use App\Models\Employer;
use App\Models\JobAdvertisement;
use App\Models\JobApplication;
use App\Repositories\Contracts\JobAdvertisementRepositoryInterface;
use App\Repositories\Contracts\JobApplicationRepositoryInterface;
use Carbon\Carbon;

class EmployerDashboardService
{
    public function __construct(
        private JobAdvertisementRepositoryInterface $jobAdvertisementRepository,
        private JobApplicationRepositoryInterface $jobApplicationRepository
    ) {
    }

    /**
     * Get dashboard data for an employer.
     */
    public function getDashboardData(Employer $employer): array
    {
        $companyId = $employer->company_id;

        if (! $companyId) {
            return $this->getEmptyDashboardData($employer);
        }

        $activeJobsCount = JobAdvertisement::query()
            ->where('company_id', $companyId)
            ->where('status', 'published')
            ->count();

        $totalApplications = $this->jobApplicationRepository->countByCompanyId($companyId);

        $totalViews = (int) JobAdvertisement::query()
            ->where('company_id', $companyId)
            ->sum('views_count');

        $conversionRate = $totalViews > 0
            ? round(($totalApplications / $totalViews) * 100, 1)
            : 0;

        $appCountsByJob = JobApplication::query()
            ->whereHas('jobAdvertisement', fn ($q) => $q->where('company_id', $companyId))
            ->selectRaw('job_advertisement_id, COUNT(*) as c')
            ->groupBy('job_advertisement_id')
            ->pluck('c', 'job_advertisement_id');

        $today = Carbon::today();
        $todayCountsByJob = JobApplication::query()
            ->whereHas('jobAdvertisement', fn ($q) => $q->where('company_id', $companyId))
            ->where('created_at', '>=', $today)
            ->selectRaw('job_advertisement_id, COUNT(*) as c')
            ->groupBy('job_advertisement_id')
            ->pluck('c', 'job_advertisement_id');

        $recentJobs = $this->jobAdvertisementRepository->getRecentByCompanyId($companyId, 4)
            ->map(function ($job) use ($appCountsByJob, $todayCountsByJob) {
                return [
                    'id' => $job->id,
                    'title' => $job->title,
                    'posted_at' => $job->created_at,
                    'posted_days_ago' => Carbon::parse($job->created_at)->diffInDays(now()),
                    'applications_count' => (int) ($appCountsByJob[$job->id] ?? 0),
                    'views_count' => $job->views_count ?? 0,
                    'today_activity' => (int) ($todayCountsByJob[$job->id] ?? 0),
                    'status' => $job->status,
                ];
            });

        $recentApplicants = $this->jobApplicationRepository->getRecentByCompanyId($companyId, 4)
            ->map(function ($application) {
                $jobSeeker = $application->jobSeeker;
                $name = $jobSeeker
                    ? trim(($jobSeeker->first_name ?? '').' '.($jobSeeker->last_name ?? ''))
                    : ($application->first_name.' '.$application->last_name);

                return [
                    'id' => $application->id,
                    'name' => $name,
                    'initials' => $this->getInitials($name),
                    'job_title' => $application->jobAdvertisement->title ?? 'N/A',
                    'time_ago' => Carbon::parse($application->created_at)->diffForHumans(),
                    'status' => $application->status ?? 'new',
                ];
            });

        $statusCounts = JobApplication::query()
            ->whereHas('jobAdvertisement', fn ($q) => $q->where('company_id', $companyId))
            ->selectRaw('status, COUNT(*) as c')
            ->groupBy('status')
            ->pluck('c', 'status');

        $pendingReviews = (int) (
            ($statusCounts['pending'] ?? 0)
            + ($statusCounts['applied'] ?? 0)
            + ($statusCounts['reviewing'] ?? 0)
            + ($statusCounts['in_review'] ?? 0)
        );
        $shortlisted = (int) (
            ($statusCounts['shortlisted'] ?? 0)
            + ($statusCounts['interview'] ?? 0)
        );

        $weeklyImpressions = $totalViews;

        return [
            'employer' => [
                'id' => $employer->employer_id,
                'company_name' => $employer->company_name,
                'coin_balance' => $employer->coin_balance ?? 0,
            ],
            'metrics' => [
                'active_jobs' => [
                    'value' => $activeJobsCount,
                    'trend' => $this->calculateTrend($activeJobsCount, $activeJobsCount),
                ],
                'total_applications' => [
                    'value' => $totalApplications,
                    'trend' => $this->calculateTrend($totalApplications, $totalApplications),
                ],
                'total_views' => [
                    'value' => $this->formatNumber($totalViews),
                    'trend' => $this->calculateTrend($totalViews, $totalViews),
                ],
                'conversion_rate' => [
                    'value' => $conversionRate,
                    'trend' => $this->calculateTrend($conversionRate, $conversionRate),
                ],
            ],
            'recent_jobs' => $recentJobs,
            'recent_applicants' => $recentApplicants,
            'summary' => [
                'pending_reviews' => $pendingReviews,
                'shortlisted' => $shortlisted,
                'weekly_impressions' => $this->formatNumber($weeklyImpressions),
            ],
        ];
    }

    private function getEmptyDashboardData(Employer $employer): array
    {
        return [
            'employer' => [
                'id' => $employer->employer_id,
                'company_name' => $employer->company_name ?? 'Your Company',
                'coin_balance' => $employer->coin_balance ?? 0,
            ],
            'metrics' => [
                'active_jobs' => ['value' => 0, 'trend' => 0],
                'total_applications' => ['value' => 0, 'trend' => 0],
                'total_views' => ['value' => '0', 'trend' => 0],
                'conversion_rate' => ['value' => 0, 'trend' => 0],
            ],
            'recent_jobs' => [],
            'recent_applicants' => [],
            'summary' => [
                'pending_reviews' => 0,
                'shortlisted' => 0,
                'weekly_impressions' => '0',
            ],
        ];
    }

    private function getInitials(string $name): string
    {
        $parts = array_filter(explode(' ', trim($name)));
        if (empty($parts)) {
            return '??';
        }

        $initials = '';
        foreach ($parts as $part) {
            $initials .= strtoupper(substr($part, 0, 1));
            if (strlen($initials) >= 2) {
                break;
            }
        }

        return $initials;
    }

    private function formatNumber(int $number): string
    {
        if ($number >= 1000) {
            return number_format($number / 1000, 1).'K';
        }

        return (string) $number;
    }

    private function calculateTrend(int|float $current, int|float $previous): float
    {
        if ($previous == 0) {
            return $current > 0 ? 100 : 0;
        }

        return round((($current - $previous) / $previous) * 100, 1);
    }
}
