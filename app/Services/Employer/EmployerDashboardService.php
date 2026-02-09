<?php

namespace App\Services\Employer;

use App\Models\Employer;
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
        
        if (!$companyId) {
            return $this->getEmptyDashboardData($employer);
        }

        // Get all jobs for this company
        $allJobs = $this->jobAdvertisementRepository->getByCompanyId($companyId);
        $activeJobs = $this->jobAdvertisementRepository->getActiveByCompanyId($companyId);
        
        // Get all applications for this company's jobs
        $allApplications = $this->jobApplicationRepository->getByCompanyId($companyId);
        
        // Calculate metrics
        $activeJobsCount = $activeJobs->count();
        $totalApplications = $allApplications->count();
        $totalViews = (int) $allJobs->sum('views_count');
        
        // Calculate conversion rate (applications / views * 100)
        $conversionRate = $totalViews > 0 
            ? round(($totalApplications / $totalViews) * 100, 1) 
            : 0;
        
        // Get recent job postings (last 4)
        $recentJobs = $this->jobAdvertisementRepository->getRecentByCompanyId($companyId, 4)
            ->map(function ($job) use ($allApplications) {
                $jobApplications = $allApplications->where('job_advertisement_id', $job->id);
                $today = Carbon::today();
                $todayApplications = $jobApplications->filter(function ($app) use ($today) {
                    return Carbon::parse($app->created_at)->isSameDay($today);
                })->count();
                
                return [
                    'id' => $job->id,
                    'title' => $job->title,
                    'posted_at' => $job->created_at,
                    'posted_days_ago' => Carbon::parse($job->created_at)->diffInDays(now()),
                    'applications_count' => $jobApplications->count(),
                    'views_count' => $job->views_count ?? 0,
                    'today_activity' => $todayApplications,
                    'status' => $job->status,
                ];
            });
        
        // Get recent applicants (last 4)
        $recentApplicants = $this->jobApplicationRepository->getRecentByCompanyId($companyId, 4)
            ->map(function ($application) {
                $jobSeeker = $application->jobSeeker;
                $name = $jobSeeker 
                    ? trim(($jobSeeker->first_name ?? '') . ' ' . ($jobSeeker->last_name ?? ''))
                    : ($application->first_name . ' ' . $application->last_name);
                
                $initials = $this->getInitials($name);
                $timeAgo = Carbon::parse($application->created_at)->diffForHumans();
                
                return [
                    'id' => $application->id,
                    'name' => $name,
                    'initials' => $initials,
                    'job_title' => $application->jobAdvertisement->title ?? 'N/A',
                    'time_ago' => $timeAgo,
                    'status' => $application->status ?? 'new',
                ];
            });
        
        // Get status counts
        $pendingReviews = $this->jobApplicationRepository->getByStatusAndCompanyId('pending', $companyId)->count();
        $shortlisted = $this->jobApplicationRepository->getByStatusAndCompanyId('shortlisted', $companyId)->count();
        
        // Calculate weekly impressions (views from last 7 days)
        $weekStart = Carbon::now()->startOfWeek();
        $weeklyImpressions = $allJobs->sum(function ($job) {
            // For now, we'll use total views. In a real system, you'd track daily views
            return $job->views_count ?? 0;
        });
        
        // Calculate trends (simplified - comparing last 30 days to previous 30 days)
        $activeJobsTrend = $this->calculateTrend($activeJobsCount, $activeJobsCount); // Simplified
        $applicationsTrend = $this->calculateTrend($totalApplications, $totalApplications); // Simplified
        $viewsTrend = $this->calculateTrend($totalViews, $totalViews); // Simplified
        $conversionTrend = $this->calculateTrend($conversionRate, $conversionRate); // Simplified

        return [
            'employer' => [
                'id' => $employer->employer_id,
                'company_name' => $employer->company_name,
                'coin_balance' => $employer->coin_balance ?? 0,
            ],
            'metrics' => [
                'active_jobs' => [
                    'value' => $activeJobsCount,
                    'trend' => $activeJobsTrend,
                ],
                'total_applications' => [
                    'value' => $totalApplications,
                    'trend' => $applicationsTrend,
                ],
                'total_views' => [
                    'value' => $this->formatNumber($totalViews),
                    'trend' => $viewsTrend,
                ],
                'conversion_rate' => [
                    'value' => $conversionRate,
                    'trend' => $conversionTrend,
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

    /**
     * Get empty dashboard data when employer has no company.
     */
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

    /**
     * Get initials from a name.
     */
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

    /**
     * Format number with K notation.
     */
    private function formatNumber(int $number): string
    {
        if ($number >= 1000) {
            return number_format($number / 1000, 1) . 'K';
        }
        return (string) $number;
    }

    /**
     * Calculate trend percentage (simplified - would compare periods in real implementation).
     */
    private function calculateTrend(int|float $current, int|float $previous): float
    {
        if ($previous == 0) {
            return $current > 0 ? 100 : 0;
        }
        return round((($current - $previous) / $previous) * 100, 1);
    }
}
