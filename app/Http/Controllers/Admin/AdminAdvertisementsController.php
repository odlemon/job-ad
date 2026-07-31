<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JobAdvertisement;
use App\Models\JobCampaign;
use App\Models\TenderAd;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Advertisements Management: job ads (job campaigns) + tender ads in one overview.
 */
class AdminAdvertisementsController extends Controller
{
    /**
     * Combined: job campaigns (job ads from all employers) + tender ads (from all employers).
     * Use for admin dashboard when both lists are needed in one call.
     */
    public function all(Request $request): JsonResponse
    {
        $jobQuery = JobAdvertisement::with(['company', 'category', 'campaigns.campaignType'])
            ->orderByDesc('created_at');
        if ($request->filled('search')) {
            $s = $request->get('search');
            $jobQuery->where(function ($q) use ($s) {
                $q->where('title', 'like', '%' . $s . '%')
                    ->orWhereHas('company', fn ($cq) => $cq->where('name', 'like', '%' . $s . '%'));
            });
        }
        if ($request->filled('status')) {
            $status = $request->get('status');
            if ($status === 'active') {
                $jobQuery->whereNotNull('published_at');
            } elseif ($status === 'draft') {
                $jobQuery->whereNull('published_at')->where('status', 'draft');
            } elseif ($status === 'closed') {
                $jobQuery->where('status', 'closed');
            }
        }
        $jobAds = $jobQuery->get();
        $jobCampaigns = $jobAds->map(fn ($j) => $this->jobAdToArray($j));

        $tenderQuery = TenderAd::with(['category', 'creator'])->orderByDesc('created_at');
        if ($request->filled('search')) {
            $s = $request->get('search');
            $tenderQuery->where(function ($q) use ($s) {
                $q->where('title', 'like', '%' . $s . '%')
                    ->orWhere('entity_name', 'like', '%' . $s . '%')
                    ->orWhere('reference_number', 'like', '%' . $s . '%');
            });
        }
        if ($request->filled('status')) {
            $tenderQuery->where('status', $request->get('status'));
        }
        $tenderAds = $tenderQuery->get()->map(fn ($t) => $this->tenderAdToArray($t));

        return response()->json([
            'job_campaigns' => $jobCampaigns->values()->all(),
            'tender_ads' => $tenderAds->values()->all(),
            'meta' => [
                'job_campaigns_count' => $jobCampaigns->count(),
                'tender_ads_count' => $tenderAds->count(),
            ],
        ]);
    }

    /**
     * Job campaigns (job ads) only — from all employers.
     */
    public function jobCampaigns(Request $request): JsonResponse
    {
        $perPage = max(1, min(100, (int) $request->get('per_page', 15)));
        $page = max(1, (int) $request->get('page', 1));

        $query = JobAdvertisement::with(['company', 'category', 'campaigns.campaignType'])
            ->orderByDesc('created_at');
        if ($request->filled('search')) {
            $s = $request->get('search');
            $query->where(function ($q) use ($s) {
                $q->where('title', 'like', '%' . $s . '%')
                    ->orWhereHas('company', fn ($cq) => $cq->where('name', 'like', '%' . $s . '%'));
            });
        }
        if ($request->filled('status')) {
            $status = $request->get('status');
            if ($status === 'active') {
                $query->whereNotNull('published_at');
            } elseif ($status === 'draft') {
                $query->whereNull('published_at')->where('status', 'draft');
            } elseif ($status === 'closed') {
                $query->where('status', 'closed');
            }
        }

        $paginator = $query->paginate($perPage);
        $items = $paginator->getCollection()->map(fn ($j) => $this->jobAdToArray($j));

        return response()->json([
            'job_campaigns' => $items->values()->all(),
            'pagination' => [
                'current_page' => $paginator->currentPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'last_page' => $paginator->lastPage(),
            ],
        ]);
    }

    /**
     * Job campaigns dashboard: summary stats, status tab counts, and filterable job list.
     * For the admin "Job Campaigns" screen with Active Job Listing, Total Views, Clicks, etc.
     */
    public function jobCampaignsDashboard(Request $request): JsonResponse
    {
        $now = Carbon::now();

        // Aggregate stats (from job ads + campaigns)
        $activeJobListings = JobAdvertisement::whereNotNull('published_at')
            ->where('status', '!=', 'closed')
            ->count();

        $totalViews = (int) JobAdvertisement::sum('views_count') + (int) JobCampaign::sum('views_count');
        $totalClicks = (int) JobCampaign::sum('clicks_count');
        $applications = (int) JobAdvertisement::sum('applications_count');
        $shares = (int) JobCampaign::sum('shares_count');
        $saved = (int) JobCampaign::sum('saved_count');

        $stats = [
            'active_job_listings' => $activeJobListings,
            'total_views' => $totalViews,
            'total_clicks' => $totalClicks,
            'applications' => $applications,
            'shares' => $shares,
            'saved' => $saved,
        ];

        // Status tab counts
        $adApproval = JobAdvertisement::whereNull('published_at')->where('status', 'draft')->count();
        $flagged = 0; // placeholder until flagged column exists
        $allAds = JobAdvertisement::count();
        $scheduled = JobCampaign::where('status', 'pending')->where('launched_at', '>', $now)->count();
        $active = JobAdvertisement::whereNotNull('published_at')->where('status', '!=', 'closed')->count();
        $paused = JobCampaign::where('status', 'paused')->count();
        $expired = (int) JobAdvertisement::where('status', 'closed')
            ->orWhereHas('campaigns', fn ($cq) => $cq->where('ends_at', '<', $now))
            ->count();
        $draft = JobAdvertisement::whereNull('published_at')->where('status', 'draft')->count();

        $status_counts = [
            'ad_approval' => $adApproval,
            'flagged' => $flagged,
            'all_ads' => $allAds,
            'scheduled' => $scheduled,
            'active' => $active,
            'paused' => $paused,
            'expired' => $expired,
            'draft' => $draft,
        ];

        // List of job ads for the table (filter by status tab)
        $statusFilter = $request->get('status', 'all_ads'); // ad_approval | flagged | all_ads | scheduled | active | paused | expired | draft
        $search = $request->get('search');
        $postedBy = $request->get('posted_by'); // company_id optional
        $location = $request->get('location');
        $perPage = max(1, min(100, (int) $request->get('per_page', 10)));
        $page = max(1, (int) $request->get('page', 1));

        $query = JobAdvertisement::with(['company', 'category', 'campaigns.campaignType'])
            ->orderByDesc('created_at');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                    ->orWhereHas('company', fn ($cq) => $cq->where('name', 'like', '%' . $search . '%'));
            });
        }
        if ($postedBy) {
            $query->where('company_id', $postedBy);
        }
        if ($location) {
            $query->where('location', 'like', '%' . $location . '%');
        }

        switch ($statusFilter) {
            case 'ad_approval':
                $query->whereNull('published_at')->where('status', 'draft');
                break;
            case 'all_ads':
                break;
            case 'scheduled':
                $query->whereHas('campaigns', fn ($q) => $q->where('status', 'pending')->where('launched_at', '>', $now));
                break;
            case 'active':
                $query->whereNotNull('published_at')->where('status', '!=', 'closed');
                break;
            case 'paused':
                $query->whereHas('campaigns', fn ($q) => $q->where('status', 'paused'));
                break;
            case 'expired':
                $query->where(function ($q) use ($now) {
                    $q->where('status', 'closed')
                        ->orWhereHas('campaigns', fn ($cq) => $cq->where('ends_at', '<', $now));
                });
                break;
            case 'draft':
                $query->whereNull('published_at')->where('status', 'draft');
                break;
            case 'flagged':
                // No flag column; return empty or all for now
                $query->whereRaw('0 = 1');
                break;
        }

        $paginator = $query->paginate($perPage);
        $jobs = $paginator->getCollection()->map(fn ($j) => $this->jobAdToDashboardItem($j));

        return response()->json([
            'stats' => $stats,
            'status_counts' => $status_counts,
            'jobs' => $jobs->values()->all(),
            'pagination' => [
                'current_page' => $paginator->currentPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'last_page' => $paginator->lastPage(),
            ],
        ]);
    }

    private function jobAdToDashboardItem(JobAdvertisement $j): array
    {
        $primaryCampaign = $j->campaigns->sortByDesc('launched_at')->first();
        $campaignTypeName = $primaryCampaign && $primaryCampaign->campaignType
            ? $primaryCampaign->campaignType->name
            : null;
        $expiringAt = $primaryCampaign && $primaryCampaign->ends_at
            ? $primaryCampaign->ends_at->toIso8601String()
            : null;

        $displayStatus = 'draft';
        if ($j->published_at) {
            if ($j->status === 'closed') {
                $displayStatus = 'expired';
            } elseif ($primaryCampaign) {
                if ($primaryCampaign->ends_at && $primaryCampaign->ends_at->isPast()) {
                    $displayStatus = 'expired';
                } elseif ($primaryCampaign->status === 'paused') {
                    $displayStatus = 'paused';
                } elseif ($primaryCampaign->status === 'pending' && $primaryCampaign->launched_at && $primaryCampaign->launched_at->isFuture()) {
                    $displayStatus = 'scheduled';
                } else {
                    $displayStatus = 'active';
                }
            } else {
                $displayStatus = 'active';
            }
        } else {
            $displayStatus = 'ad_approval';
        }

        return [
            'id' => $j->id,
            'title' => $j->title,
            'slug' => $j->slug,
            'company' => $j->company ? ['id' => $j->company->id, 'name' => $j->company->name] : null,
            'location' => $j->location,
            'campaign_type' => $campaignTypeName,
            'posted_at' => $j->created_at?->toIso8601String(),
            'posted_by' => $j->company?->name,
            'expiring_at' => $expiringAt,
            'display_status' => $displayStatus,
            'status' => $j->published_at ? 'published' : ($j->status ?? 'draft'),
            'views_count' => (int) $j->views_count,
            'applications_count' => (int) $j->applications_count,
        ];
    }

    private function jobAdToArray(JobAdvertisement $j): array
    {
        $campaigns = $j->campaigns ? $j->campaigns->map(function ($c) {
            return [
                'id' => $c->id,
                'campaign_type_id' => $c->campaign_type_id,
                'campaign_type' => $c->campaignType ? ['id' => $c->campaignType->id, 'name' => $c->campaignType->name] : null,
                'status' => $c->status,
                'duration_days' => (int) $c->duration_days,
                'launched_at' => $c->launched_at?->toIso8601String(),
                'ends_at' => $c->ends_at?->toIso8601String(),
                'views_count' => (int) $c->views_count,
                'clicks_count' => (int) $c->clicks_count,
            ];
        })->values()->all() : [];

        return [
            'id' => $j->id,
            'title' => $j->title,
            'slug' => $j->slug,
            'status' => $j->published_at ? 'published' : ($j->status ?? 'draft'),
            'company_id' => $j->company_id,
            'company' => $j->company ? ['id' => $j->company->id, 'name' => $j->company->name] : null,
            'category_id' => $j->category_id,
            'category' => $j->category ? ['id' => $j->category->id, 'name' => $j->category->name] : null,
            'location' => $j->location,
            'employment_type' => $j->employment_type,
            'views_count' => (int) $j->views_count,
            'applications_count' => (int) $j->applications_count,
            'published_at' => $j->published_at?->toIso8601String(),
            'created_at' => $j->created_at?->toIso8601String(),
            'updated_at' => $j->updated_at?->toIso8601String(),
            'campaigns' => $campaigns,
        ];
    }

    private function tenderAdToArray(TenderAd $t): array
    {
        return [
            'id' => $t->id,
            'title' => $t->title,
            'slug' => $t->slug,
            'reference_number' => $t->reference_number,
            'tender_type' => $t->tender_type,
            'status' => $t->status,
            'entity_name' => $t->entity_name,
            'category_id' => $t->category_id,
            'category' => $t->category ? ['id' => $t->category->id, 'name' => $t->category->name] : null,
            'location' => $t->location,
            'views_count' => (int) $t->views_count,
            'applications_count' => (int) $t->applications_count,
            'submission_deadline' => $t->submission_deadline?->toDateString(),
            'created_by' => $t->created_by,
            'creator' => $t->creator ? ['id' => $t->creator->id, 'name' => $t->creator->name ?? ''] : null,
            'created_at' => $t->created_at?->toIso8601String(),
            'updated_at' => $t->updated_at?->toIso8601String(),
        ];
    }

    /**
     * Overview for Advertisements Management screen.
     * Summary KPIs + list of ads (job ads and/or tender ads) with type filter.
     */
    public function overview(Request $request): JsonResponse
    {
        $now = Carbon::now();
        $startOfLastMonth = $now->copy()->subMonth()->startOfMonth();
        $endOfLastMonth = $now->copy()->subMonth()->endOfMonth();

        $change = function ($current, $previous) {
            if ($previous == 0) {
                return $current > 0 ? 100.0 : 0.0;
            }
            return round((($current - $previous) / $previous) * 100, 1);
        };

        $totalJobAds = JobAdvertisement::count();
        $activeJobAds = JobAdvertisement::whereNotNull('published_at')->count();
        $activeJobAdsPrev = JobAdvertisement::whereNotNull('published_at')
            ->where('published_at', '<', $startOfLastMonth)->count();

        $totalTenderAds = TenderAd::count();
        $activeTenderAds = TenderAd::where('status', 'active')->count();
        $activeTenderAdsPrev = TenderAd::where('status', 'active')
            ->where('updated_at', '>=', $startOfLastMonth)
            ->where('updated_at', '<=', $endOfLastMonth)
            ->count();

        $totalAds = $totalJobAds + $totalTenderAds;
        $pendingJob = JobAdvertisement::whereNull('published_at')->where('status', 'draft')->count();
        $pendingTender = TenderAd::where('status', 'pending_approval')->count();
        $pendingApproval = $pendingJob + $pendingTender;

        $summary = [
            'total_ads' => [
                'value' => $totalAds,
                'change_percent' => 0,
            ],
            'active_job_ads' => [
                'value' => $activeJobAds,
                'change_percent' => $change($activeJobAds, $activeJobAdsPrev),
            ],
            'active_tender_ads' => [
                'value' => $activeTenderAds,
                'change_percent' => $change($activeTenderAds, (int) $activeTenderAdsPrev),
            ],
            'pending_approval' => [
                'value' => $pendingApproval,
                'change_percent' => 0,
            ],
        ];

        $typeFilter = $request->get('type', 'all'); // all | job_ads | tender_ads
        $search = $request->get('search');
        $statusFilter = $request->get('status'); // active | pending_approval | rejected | expired | all
        $perPage = max(1, min(100, (int) $request->get('per_page', 15)));
        $page = max(1, (int) $request->get('page', 1));

        $items = collect();

        if ($typeFilter === 'all' || $typeFilter === 'job_ads') {
            $jobQuery = JobAdvertisement::with(['company', 'category'])->orderByDesc('created_at');
            if ($search) {
                $jobQuery->where(function ($q) use ($search) {
                    $q->where('title', 'like', '%' . $search . '%')
                        ->orWhereHas('company', fn ($cq) => $cq->where('name', 'like', '%' . $search . '%'));
                });
            }
            if ($statusFilter === 'active') {
                $jobQuery->whereNotNull('published_at');
            } elseif ($statusFilter === 'pending_approval') {
                $jobQuery->whereNull('published_at')->where('status', 'draft');
            } elseif ($statusFilter === 'expired') {
                $jobQuery->where('status', 'closed');
            }
            $jobs = $jobQuery->limit(500)->get();
            foreach ($jobs as $j) {
                $items->push([
                    'id' => $j->id,
                    'type' => 'job_ad',
                    'title' => $j->title,
                    'company_or_entity' => $j->company?->name,
                    'category' => $j->category?->name,
                    'views_count' => (int) $j->views_count,
                    'applications_count' => (int) $j->applications_count,
                    'start_date' => $j->created_at?->toDateString(),
                    'end_date' => null,
                    'amount' => null,
                    'currency' => null,
                    'status' => $j->published_at ? 'active' : ($j->status === 'closed' ? 'expired' : 'pending_approval'),
                    'created_at' => $j->created_at?->toIso8601String(),
                ]);
            }
        }

        if ($typeFilter === 'all' || $typeFilter === 'tender_ads') {
            $tenderQuery = TenderAd::with('category')->orderByDesc('created_at');
            if ($search) {
                $tenderQuery->where(function ($q) use ($search) {
                    $q->where('title', 'like', '%' . $search . '%')
                        ->orWhere('entity_name', 'like', '%' . $search . '%')
                        ->orWhere('reference_number', 'like', '%' . $search . '%');
                });
            }
            if ($statusFilter === 'active') {
                $tenderQuery->where('status', 'active');
            } elseif ($statusFilter === 'pending_approval') {
                $tenderQuery->where('status', 'pending_approval');
            } elseif ($statusFilter === 'rejected') {
                $tenderQuery->where('status', 'rejected');
            } elseif ($statusFilter === 'expired') {
                $tenderQuery->where('status', 'expired');
            }
            $tenders = $tenderQuery->limit(500)->get();
            foreach ($tenders as $t) {
                $items->push([
                    'id' => $t->id,
                    'type' => 'tender_ad',
                    'title' => $t->title,
                    'company_or_entity' => $t->entity_name,
                    'category' => $t->category?->name,
                    'views_count' => (int) $t->views_count,
                    'applications_count' => (int) $t->applications_count,
                    'start_date' => $t->start_date?->toDateString(),
                    'end_date' => $t->end_date?->toDateString(),
                    'amount' => $t->amount ? (float) $t->amount : null,
                    'currency' => $t->currency,
                    'status' => $t->status,
                    'created_at' => $t->created_at?->toIso8601String(),
                ]);
            }
        }

        $items = $items->sortByDesc('created_at')->values();
        $total = $items->count();
        $items = $items->slice(($page - 1) * $perPage, $perPage)->values();

        return response()->json([
            'summary' => $summary,
            'filters' => [
                'type' => ['all', 'job_ads', 'tender_ads'],
                'status' => ['all', 'active', 'pending_approval', 'rejected', 'expired'],
            ],
            'pagination' => [
                'current_page' => $page,
                'per_page' => $perPage,
                'total' => $total,
                'last_page' => $perPage > 0 ? (int) ceil($total / $perPage) : 1,
            ],
            'advertisements' => $items,
        ]);
    }
}
