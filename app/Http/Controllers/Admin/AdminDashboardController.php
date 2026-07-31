<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JobAdvertisement;
use App\Models\JobApplication;
use App\Models\JobCampaign;
use App\Models\JobCategory;
use App\Models\User;
use App\Models\Employer;
use App\Models\JobSeeker;
use App\Models\Company;
use App\Models\TenderAd;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    /**
     * Single endpoint that returns all admin dashboard data.
     */
    public function index(Request $request): JsonResponse
    {
        $now = Carbon::now();
        $startOfMonth = $now->copy()->startOfMonth();
        $endOfLastMonth = $now->copy()->subMonth()->endOfMonth();
        $startOfLastMonth = $now->copy()->subMonth()->startOfMonth();
        $last14Days = $now->copy()->subDays(13)->startOfDay();

        // ---- KPIs (with % change vs previous period) ----
        $totalJobSeekers = User::where('user_type', 'job_seeker')->count();
        $totalJobSeekersPrev = User::where('user_type', 'job_seeker')->where('created_at', '<', $startOfMonth)->count();
        $totalEmployers = User::where('user_type', 'employer')->count();
        $totalEmployersPrev = User::where('user_type', 'employer')->where('created_at', '<', $startOfMonth)->count();

        $activeJobAds = JobAdvertisement::whereNotNull('published_at')->count();
        $activeJobAdsPrev = JobAdvertisement::whereNotNull('published_at')->where('published_at', '<', $startOfMonth)->count();

        // Tender ads (admin-created, fall under advertisements)
        $activeTenderAds = TenderAd::where('status', 'active')->count();
        $activeTenderAdsPrev = TenderAd::where('status', 'active')->where('updated_at', '<', $startOfMonth)->count();

        $pendingApprovals = Employer::whereNull('verified_at')->count();
        $pendingApprovalsPrev = Employer::whereNull('verified_at')->where('created_at', '<', $startOfMonth)->count();

        // Revenue: no payments table; stub as 0
        $revenueThisMonth = 0;
        $revenueLastMonth = 0;

        $change = function ($current, $previous) {
            if ($previous == 0) {
                return $current > 0 ? 100.0 : 0.0;
            }
            return round((($current - $previous) / $previous) * 100, 1);
        };

        $kpis = [
            'total_job_seekers' => [
                'value' => $totalJobSeekers,
                'change_percent' => $change($totalJobSeekers, $totalJobSeekersPrev),
            ],
            'total_employers' => [
                'value' => $totalEmployers,
                'change_percent' => $change($totalEmployers, $totalEmployersPrev),
            ],
            'active_job_ads' => [
                'value' => $activeJobAds,
                'change_percent' => $change($activeJobAds, $activeJobAdsPrev),
            ],
            'active_tender_ads' => [
                'value' => $activeTenderAds,
                'change_percent' => $change($activeTenderAds, $activeTenderAdsPrev),
            ],
            'pending_approvals' => [
                'value' => $pendingApprovals,
                'change_percent' => $change($pendingApprovals, $pendingApprovalsPrev),
            ],
            'revenue_this_month' => [
                'value' => $revenueThisMonth,
                'change_percent' => $change($revenueThisMonth, $revenueLastMonth),
                'currency' => 'USD',
            ],
        ];

        // ---- Daily job applications (last 14 days) ----
        $dailyApplications = JobApplication::query()
            ->where('created_at', '>=', $last14Days)
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('count', 'date')
            ->all();

        $dailyLabels = [];
        $dailyCounts = [];
        for ($i = 13; $i >= 0; $i--) {
            $d = $now->copy()->subDays($i)->format('Y-m-d');
            $dailyLabels[] = 'Day ' . (14 - $i);
            $dailyCounts[] = (int) ($dailyApplications[$d] ?? 0);
        }

        $daily_job_applications = [
            'labels' => $dailyLabels,
            'data' => $dailyCounts,
        ];

        // ---- Active categories (category name + ad count) ----
        // Use whereHas instead of HAVING so SQLite is supported (HAVING on non-aggregate is invalid in SQLite)
        $activeCategories = JobCategory::query()
            ->whereHas('jobAdvertisements', fn ($q) => $q->whereNotNull('published_at'))
            ->withCount(['jobAdvertisements' => function ($q) {
                $q->whereNotNull('published_at');
            }])
            ->orderByDesc('job_advertisements_count')
            ->limit(10)
            ->get()
            ->map(fn ($c) => ['name' => $c->name, 'count' => $c->job_advertisements_count])
            ->values()
            ->all();

        // ---- User flow (job views, job clicks, applications) ----
        $totalJobViews = (int) JobAdvertisement::whereNotNull('published_at')->sum('views_count');
        $totalClicks = (int) JobCampaign::sum('clicks_count');
        $totalApplications = JobApplication::count();
        // If no campaign clicks, use views as proxy for "clicks" so the flow has a value
        if ($totalClicks === 0 && $totalJobViews > 0) {
            $totalClicks = (int) round($totalJobViews * 0.65);
        }

        $user_flow = [
            'job_views' => $totalJobViews,
            'job_clicks' => $totalClicks,
            'applications' => $totalApplications,
        ];

        // ---- Recent signups (last 5 users, exclude admin) ----
        $recentSignups = User::where('user_type', '!=', 'admin')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get(['id', 'name', 'email', 'user_type', 'created_at'])
            ->map(fn ($u) => [
                'name' => $u->name,
                'email' => $u->email,
                'signed_up_at' => $u->created_at->toIso8601String(),
                'user_type' => $u->user_type === 'job_seeker' ? 'Job Seeker' : 'Employer',
            ])
            ->values()
            ->all();

        // ---- Recent payments (stub: no payments table) ----
        $recent_payments = [];

        // ---- Ads about to expire (campaigns ending in next 14 days) ----
        $expiringCampaigns = JobCampaign::where('ends_at', '>=', $now)
            ->where('ends_at', '<=', $now->copy()->addDays(14))
            ->with('jobAdvertisement.company')
            ->orderBy('ends_at')
            ->limit(10)
            ->get();

        $ads_about_to_expire = $expiringCampaigns->map(function ($c) use ($now) {
            $job = $c->jobAdvertisement;
            $company = $job?->company;
            $daysLeft = $c->ends_at ? (int) $now->diffInDays($c->ends_at, false) : 0;
            return [
                'title' => $job?->title ?? 'N/A',
                'company_name' => $company?->name ?? 'N/A',
                'views' => (int) ($job?->views_count ?? 0),
                'applications' => (int) ($job?->applications_count ?? 0),
                'days_until_expiry' => max(0, $daysLeft),
                'type' => 'Job Ad',
                'job_id' => $job?->id,
            ];
        })->values()->all();

        return response()->json([
            'kpis' => $kpis,
            'daily_job_applications' => $daily_job_applications,
            'active_categories' => $activeCategories,
            'user_flow' => $user_flow,
            'recent_signups' => $recentSignups,
            'recent_payments' => $recent_payments,
            'ads_about_to_expire' => $ads_about_to_expire,
        ]);
    }

    /**
     * Job seekers management overview (summary stats + paginated list).
     */
    public function jobSeekersOverview(Request $request): JsonResponse
    {
        $now = Carbon::now();
        $startOfMonth = $now->copy()->startOfMonth();
        $startOfLastMonth = $now->copy()->subMonth()->startOfMonth();
        $endOfLastMonth = $now->copy()->subMonth()->endOfMonth();

        $change = function ($current, $previous) {
            if ($previous == 0) {
                return $current > 0 ? 100.0 : 0.0;
            }
            return round((($current - $previous) / $previous) * 100, 1);
        };

        // Summary cards
        $totalJobSeekers = User::where('user_type', 'job_seeker')->count();
        $totalJobSeekersPrev = User::where('user_type', 'job_seeker')
            ->whereBetween('created_at', [$startOfLastMonth, $endOfLastMonth])
            ->count();

        $activeUsers = User::where('user_type', 'job_seeker')
            ->where('is_active', true)
            ->count();
        $activeUsersPrev = User::where('user_type', 'job_seeker')
            ->where('is_active', true)
            ->whereBetween('created_at', [$startOfLastMonth, $endOfLastMonth])
            ->count();

        $pendingVerification = User::where('user_type', 'job_seeker')
            ->where(function ($q) {
                $q->whereNull('is_verified')->orWhere('is_verified', false);
            })
            ->count();
        $pendingVerificationPrev = User::where('user_type', 'job_seeker')
            ->where(function ($q) {
                $q->whereNull('is_verified')->orWhere('is_verified', false);
            })
            ->whereBetween('created_at', [$startOfLastMonth, $endOfLastMonth])
            ->count();

        $suspendedUsers = User::where('user_type', 'job_seeker')
            ->where('is_active', false)
            ->count();
        $suspendedUsersPrev = User::where('user_type', 'job_seeker')
            ->where('is_active', false)
            ->whereBetween('created_at', [$startOfLastMonth, $endOfLastMonth])
            ->count();

        $summary = [
            'total_job_seekers' => [
                'value' => $totalJobSeekers,
                'change_percent' => $change($totalJobSeekers, $totalJobSeekersPrev),
            ],
            'active_users' => [
                'value' => $activeUsers,
                'change_percent' => $change($activeUsers, $activeUsersPrev),
            ],
            'pending_verification' => [
                'value' => $pendingVerification,
                'change_percent' => $change($pendingVerification, $pendingVerificationPrev),
            ],
            'suspended_banned' => [
                'value' => $suspendedUsers,
                'change_percent' => $change($suspendedUsers, $suspendedUsersPrev),
            ],
        ];

        $perPage = max(1, min(100, (int) $request->get('per_page', 10)));
        $search = $request->get('search');
        $status = $request->get('status'); // active, suspended, all

        $jobSeekerQuery = JobSeeker::query()
            ->with(['user'])
            ->withCount('applications')
            ->withMax('applications', 'created_at');

        if ($search) {
            $jobSeekerQuery->where(function ($q) use ($search) {
                $q->where('first_name', 'like', '%' . $search . '%')
                    ->orWhere('last_name', 'like', '%' . $search . '%')
                    ->orWhereHas('user', function ($uq) use ($search) {
                        $uq->where('email', 'like', '%' . $search . '%')
                            ->orWhere('phone', 'like', '%' . $search . '%');
                    });
            });
        }

        if ($status && $status !== 'all') {
            $jobSeekerQuery->whereHas('user', function ($uq) use ($status) {
                if ($status === 'active') {
                    $uq->where('is_active', true);
                } elseif ($status === 'suspended') {
                    $uq->where('is_active', false);
                }
            });
        }

        $jobSeekers = $jobSeekerQuery
            ->orderByDesc('created_at')
            ->paginate($perPage);

        $items = $jobSeekers->getCollection()->map(function (JobSeeker $js) {
            $user = $js->user;
            $name = $js->full_name ?: ($user?->name ?? 'N/A');
            $initials = collect(explode(' ', $name))
                ->filter()
                ->map(fn ($part) => mb_substr($part, 0, 1))
                ->take(2)
                ->implode('');

            $status = 'active';
            if ($user && $user->is_active === false) {
                $status = 'suspended';
            } elseif ($user && (!$user->is_verified && !$user->email_verified_at)) {
                $status = 'pending';
            }

            return [
                'id' => $js->seeker_id,
                'name' => $name,
                'initials' => $initials,
                'joined_at' => $user?->created_at?->toIso8601String(),
                'contact' => [
                    'email' => $user?->email,
                    'phone' => $js->phone ?: $user?->phone,
                    'location' => $js->location,
                ],
                'activity' => [
                    'applications_count' => $js->applications_count,
                    'last_application_at' => $js->applications_max_created_at
                        ? Carbon::parse($js->applications_max_created_at)->toIso8601String()
                        : null,
                    'last_login_at' => $user?->last_login
                        ? (is_string($user->last_login) ? $user->last_login : $user->last_login->toIso8601String())
                        : null,
                ],
                'verification' => [
                    'kyc_status' => $user && $user->is_verified ? 'verified' : 'pending',
                    'email_verified' => (bool) $user?->email_verified_at,
                ],
                'status' => $status,
            ];
        })->values();

        $recentApplications = JobApplication::with(['jobSeeker', 'jobAdvertisement'])
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        $recentActivity = $recentApplications->map(function (JobApplication $app) {
            $seekerName = $app->jobSeeker?->full_name
                ?? trim(($app->first_name ?? '') . ' ' . ($app->last_name ?? ''))
                ?: 'Unknown Applicant';
            $jobTitle = $app->jobAdvertisement?->title ?? 'Unknown Position';

            return [
                'type' => 'application',
                'message' => $seekerName . ' applied to ' . $jobTitle,
                'created_at' => $app->created_at?->toIso8601String(),
            ];
        })->values();

        $quickActions = [
            [
                'key' => 'approve_pending_accounts',
                'label' => 'Approve Pending Accounts',
            ],
            [
                'key' => 'review_kyc_submissions',
                'label' => 'Review KYC Submissions',
            ],
            [
                'key' => 'view_support_tickets',
                'label' => 'View Support Tickets',
            ],
        ];

        return response()->json([
            'summary' => $summary,
            'filters' => [
                'status' => ['all', 'active', 'suspended'],
            ],
            'pagination' => [
                'current_page' => $jobSeekers->currentPage(),
                'per_page' => $jobSeekers->perPage(),
                'total' => $jobSeekers->total(),
                'last_page' => $jobSeekers->lastPage(),
            ],
            'job_seekers' => $items,
            'quick_actions' => $quickActions,
            'recent_activity' => $recentActivity,
        ]);
    }

    /**
     * Employers management overview (summary stats + paginated list).
     */
    public function employersOverview(Request $request): JsonResponse
    {
        $now = Carbon::now();
        $startOfMonth = $now->copy()->startOfMonth();
        $startOfLastMonth = $now->copy()->subMonth()->startOfMonth();
        $endOfLastMonth = $now->copy()->subMonth()->endOfMonth();

        $change = function ($current, $previous) {
            if ($previous == 0) {
                return $current > 0 ? 100.0 : 0.0;
            }
            return round((($current - $previous) / $previous) * 100, 1);
        };

        // Summary cards
        $totalEmployers = User::where('user_type', 'employer')->count();
        $totalEmployersPrev = User::where('user_type', 'employer')
            ->whereBetween('created_at', [$startOfLastMonth, $endOfLastMonth])
            ->count();

        $activeCompanies = Company::where('is_active', true)->count();
        $activeCompaniesPrev = Company::where('is_active', true)
            ->whereBetween('created_at', [$startOfLastMonth, $endOfLastMonth])
            ->count();

        $pendingVerification = Employer::whereNull('verified_at')->count();
        $pendingVerificationPrev = Employer::whereNull('verified_at')
            ->whereBetween('created_at', [$startOfLastMonth, $endOfLastMonth])
            ->count();

        $suspendedEmployers = User::where('user_type', 'employer')
            ->where('is_active', false)
            ->count();
        $suspendedEmployersPrev = User::where('user_type', 'employer')
            ->where('is_active', false)
            ->whereBetween('created_at', [$startOfLastMonth, $endOfLastMonth])
            ->count();

        $summary = [
            'total_employers' => [
                'value' => $totalEmployers,
                'change_percent' => $change($totalEmployers, $totalEmployersPrev),
            ],
            'active_companies' => [
                'value' => $activeCompanies,
                'change_percent' => $change($activeCompanies, $activeCompaniesPrev),
            ],
            'pending_verification' => [
                'value' => $pendingVerification,
                'change_percent' => $change($pendingVerification, $pendingVerificationPrev),
            ],
            'suspended_banned' => [
                'value' => $suspendedEmployers,
                'change_percent' => $change($suspendedEmployers, $suspendedEmployersPrev),
            ],
        ];

        $perPage = max(1, min(100, (int) $request->get('per_page', 10)));
        $search = $request->get('search');
        $status = $request->get('status'); // active, pending_verification, suspended, all

        $employerQuery = Employer::query()->with(['user', 'company']);

        if ($search) {
            $employerQuery->where(function ($q) use ($search) {
                $q->where('company_name', 'like', '%' . $search . '%')
                    ->orWhereHas('company', function ($cq) use ($search) {
                        $cq->where('name', 'like', '%' . $search . '%')
                            ->orWhere('email', 'like', '%' . $search . '%');
                    })
                    ->orWhereHas('user', function ($uq) use ($search) {
                        $uq->where('email', 'like', '%' . $search . '%')
                            ->orWhere('phone', 'like', '%' . $search . '%');
                    });
            });
        }

        if ($status && $status !== 'all') {
            $employerQuery->whereHas('user', function ($uq) use ($status) {
                if ($status === 'active') {
                    $uq->where('is_active', true);
                } elseif ($status === 'suspended') {
                    $uq->where('is_active', false);
                }
            });

            if ($status === 'pending_verification') {
                $employerQuery->whereNull('verified_at');
            }
        }

        $employers = $employerQuery
            ->orderByDesc('created_at')
            ->paginate($perPage);

        $companyIds = $employers->getCollection()->pluck('company_id')->filter()->unique()->values();

        $jobStats = JobAdvertisement::query()
            ->selectRaw('company_id, COUNT(*) as total_ads, MAX(published_at) as last_posted_at')
            ->whereIn('company_id', $companyIds)
            ->groupBy('company_id')
            ->get()
            ->keyBy('company_id');

        $items = $employers->getCollection()->map(function (Employer $employer) use ($jobStats) {
            $user = $employer->user;
            $company = $employer->company;
            $companyName = $company?->name ?? $employer->company_name ?? 'N/A';
            $stats = $jobStats->get($employer->company_id);

            $status = 'active';
            if ($user && $user->is_active === false) {
                $status = 'suspended';
            } elseif ($employer->verified_at === null) {
                $status = 'pending_verification';
            }

            return [
                'id' => $employer->employer_id,
                'company_name' => $companyName,
                'contact' => [
                    'email' => $company?->email ?? $user?->email,
                    'phone' => $company?->phone ?? $user?->phone,
                    'location' => $company?->location,
                ],
                'job_ads' => [
                    'total' => (int) ($stats->total_ads ?? 0),
                    'last_posted_at' => $stats && $stats->last_posted_at
                        ? Carbon::parse($stats->last_posted_at)->toIso8601String()
                        : null,
                ],
                'status' => $status,
                'verification' => [
                    'kyc_status' => $employer->verified_at ? 'verified' : 'pending',
                ],
            ];
        })->values();

        $recentJobs = JobAdvertisement::with('company')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        $recentActivity = $recentJobs->map(function (JobAdvertisement $job) {
            $companyName = $job->company?->name ?? 'Unknown Company';

            return [
                'type' => 'job_posted',
                'message' => $companyName . ' posted new job "' . $job->title . '"',
                'created_at' => $job->created_at?->toIso8601String(),
            ];
        })->values();

        $quickActions = [
            [
                'key' => 'approve_pending_companies',
                'label' => 'Approve Pending Companies',
            ],
            [
                'key' => 'review_job_postings',
                'label' => 'Review Job Postings',
            ],
            [
                'key' => 'view_support_tickets',
                'label' => 'View Support Tickets',
            ],
            [
                'key' => 'bulk_credit_top_up',
                'label' => 'Bulk Credit Top Up',
            ],
        ];

        return response()->json([
            'summary' => $summary,
            'filters' => [
                'status' => ['all', 'active', 'pending_verification', 'suspended'],
            ],
            'pagination' => [
                'current_page' => $employers->currentPage(),
                'per_page' => $employers->perPage(),
                'total' => $employers->total(),
                'last_page' => $employers->lastPage(),
            ],
            'employers' => $items,
            'quick_actions' => $quickActions,
            'recent_activity' => $recentActivity,
        ]);
    }
}
