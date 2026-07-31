<?php

namespace App\Http\Controllers\Employer;

use App\Http\Controllers\Controller;
use App\Models\CampaignType;
use App\Models\JobAdvertisement;
use App\Models\JobCampaign;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmployerCampaignController extends Controller
{
    /**
     * List all campaigns for this employer – dashboard with KPIs, tabs, filters.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $employer = $user->employer;

        if (!$employer || !$employer->company_id) {
            return redirect()->route('employer.dashboard')
                ->with('error', 'Please set up your company profile first.');
        }

        $companyId = $employer->company_id;

        // Load all jobs with campaigns (filtering done client-side for real-time tab/search)
        $jobsQuery = JobAdvertisement::query()
            ->where('company_id', $companyId)
            ->whereHas('campaigns')
            ->with([
                'campaigns' => fn($q) => $q->with('campaignType')->orderByDesc('launched_at'),
                'company',
                'applications',
            ])
            ->orderByDesc('created_at');

        $jobs = $jobsQuery->get();
        $statusTab = $request->get('status', 'active');

        // Status counts for tabs (all company campaigns)
        $campaignsBase = JobCampaign::query()
            ->whereHas('jobAdvertisement', fn($q) => $q->where('company_id', $companyId));
        $statusCounts = [
            'scheduled' => (clone $campaignsBase)->where('status', 'pending')->count(),
            'active' => (clone $campaignsBase)->where('status', 'active')->count(),
            'paused' => (clone $campaignsBase)->where('status', 'paused')->count(),
            'expired' => (clone $campaignsBase)->where('status', 'expired')->count(),
            'draft' => (clone $campaignsBase)->where('status', 'draft')->count(),
        ];

        // Auto-expire: mark campaigns as expired if ends_at passed
        JobCampaign::query()
            ->whereHas('jobAdvertisement', fn($q) => $q->where('company_id', $companyId))
            ->where('status', 'active')
            ->where('ends_at', '<', now())
            ->update(['status' => 'expired']);
        $statusCounts['expired'] = (clone $campaignsBase)->where('status', 'expired')->count();
        $statusCounts['active'] = (clone $campaignsBase)->where('status', 'active')->count();

        // KPI aggregates (all company jobs with campaigns)
        $allCampaigns = JobCampaign::with('jobAdvertisement')
            ->whereHas('jobAdvertisement', fn($q) => $q->where('company_id', $companyId))
            ->get();
        $activeJobListingCount = $allCampaigns->where('status', 'active')->unique('job_advertisement_id')->count();
        $totalViews = $allCampaigns->sum('views_count');
        $totalClicks = $allCampaigns->sum('clicks_count');
        $totalApplications = \App\Models\JobApplication::whereIn('job_advertisement_id', $allCampaigns->pluck('job_advertisement_id'))->count();
        $totalShares = $allCampaigns->sum('shares_count');
        $totalSaved = $allCampaigns->sum('saved_count');

        $coinBalance = $employer->coin_balance ?? 0;
        $hasAnyCampaigns = JobCampaign::whereHas('jobAdvertisement', fn($q) => $q->where('company_id', $companyId))->exists();
        $sort = $request->get('sort', 'most_recent');

        $campaignTypes = CampaignType::orderBy('sort_order')->get();

        return view('employer.campaigns.index', [
            'jobs' => $jobs,
            'campaignTypes' => $campaignTypes,
            'hasAnyCampaigns' => $hasAnyCampaigns,
            'statusCounts' => $statusCounts,
            'statusTab' => $statusTab,
            'stats' => [
                'active_job_listing' => $activeJobListingCount,
                'total_views' => $totalViews,
                'total_clicks' => $totalClicks,
                'total_applications' => $totalApplications,
                'total_shares' => $totalShares,
                'total_saved' => $totalSaved,
            ],
            'coinBalance' => $coinBalance,
            'filters' => [
                'search' => $request->get('search'),
                'location' => $request->get('location'),
                'sort' => $sort,
            ],
        ]);
    }

    /**
     * Show the create campaign page (select jobs and campaign types).
     */
    public function create(Request $request)
    {
        $user = Auth::user();
        $employer = $user->employer;

        if (!$employer || !$employer->company_id) {
            return redirect()->route('employer.dashboard')
                ->with('error', 'Please set up your company profile first.');
        }

        $jobs = JobAdvertisement::with(['company', 'category'])
            ->where('company_id', $employer->company_id)
            ->orderBy('created_at', 'desc')
            ->get();

        $campaignTypes = CampaignType::orderBy('sort_order')->get();
        $coinBalance = $employer->coin_balance ?? 0;
        $preselectedJobId = $request->query('job') ? (int) $request->query('job') : null;

        return view('employer.campaigns.create', [
            'jobs' => $jobs,
            'campaignTypes' => $campaignTypes,
            'coinBalance' => $coinBalance,
            'preselectedJobId' => $preselectedJobId,
        ]);
    }

    /**
     * Store (launch) one or more campaigns. Coin system not yet active – always allow.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $employer = $user->employer;

        if (!$employer || !$employer->company_id) {
            return redirect()->back()->with('error', 'Please set up your company profile first.');
        }

        $validated = $request->validate([
            'campaigns' => 'required|array',
            'campaigns.*.job_id' => 'required|exists:job_advertisements,id',
            'campaigns.*.campaign_type_id' => 'required|exists:campaign_types,id',
            'campaigns.*.duration_days' => 'required|integer|min:1',
            'payment_method' => 'required|in:coin,card,lpo',
        ]);

        $created = 0;
        foreach ($validated['campaigns'] as $c) {
            $job = JobAdvertisement::where('id', $c['job_id'])
                ->where('company_id', $employer->company_id)
                ->first();
            if (!$job) continue;

            $type = CampaignType::find($c['campaign_type_id']);
            if (!$type) continue;

            JobCampaign::create([
                'job_advertisement_id' => $job->id,
                'campaign_type_id' => $type->id,
                'duration_days' => (int) $c['duration_days'],
                'status' => 'active',
                'payment_method' => $validated['payment_method'],
                'launched_at' => now(),
                'ends_at' => now()->addDays((int) $c['duration_days']),
            ]);
            $created++;
        }

        if ($request->expectsJson() || $request->wantsJson()) {
            return response()->json([
                'message' => $created . ' campaign(s) launched successfully.',
            ]);
        }
        return redirect()->route('employer.campaigns.index')
            ->with('success', $created . ' campaign(s) launched successfully.');
    }

    /**
     * Pause or resume a campaign.
     */
    public function togglePause(Request $request, int $id)
    {
        $user = Auth::user();
        $employer = $user->employer;
        if (!$employer || !$employer->company_id) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }
            return redirect()->route('employer.dashboard')->with('error', 'Please set up your company profile first.');
        }

        $campaign = JobCampaign::with('jobAdvertisement')
            ->where('id', $id)
            ->whereHas('jobAdvertisement', fn($q) => $q->where('company_id', $employer->company_id))
            ->firstOrFail();

        $campaign->status = $campaign->status === 'paused' ? 'active' : 'paused';
        $campaign->save();

        if ($request->expectsJson()) {
            return response()->json(['message' => $campaign->status === 'paused' ? 'Campaign paused.' : 'Campaign resumed.', 'status' => $campaign->status]);
        }
        return redirect()->back()->with('success', $campaign->status === 'paused' ? 'Campaign paused.' : 'Campaign resumed.');
    }

    /**
     * Extend campaign (add days) and/or upgrade campaign type. Deducts coins from employer.
     * Formulas:
     * - Extra days: coins = ceil((type.coins_price / type.duration_days) * days) using effective type after upgrade.
     * - Upgrade: coins = max(0, new_type.coins_price - old_type.coins_price).
     */
    public function extend(Request $request, int $id)
    {
        $user = Auth::user();
        $employer = $user->employer;
        if (!$employer || !$employer->company_id) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }
            return redirect()->route('employer.dashboard')->with('error', 'Please set up your company profile first.');
        }

        $validated = $request->validate([
            'days' => 'nullable|integer|min:0|max:90',
            'campaign_type_id' => 'nullable|exists:campaign_types,id',
        ]);

        $campaign = JobCampaign::with(['jobAdvertisement', 'campaignType'])
            ->where('id', $id)
            ->whereHas('jobAdvertisement', fn($q) => $q->where('company_id', $employer->company_id))
            ->firstOrFail();

        $days = (int) ($validated['days'] ?? 0);
        $newTypeId = isset($validated['campaign_type_id']) && $validated['campaign_type_id'] !== ''
            ? (int) $validated['campaign_type_id']
            : null;

        $oldType = $campaign->campaignType;
        $newType = $newTypeId ? CampaignType::find($newTypeId) : null;
        $upgraded = $newTypeId && $newTypeId !== (int) $campaign->campaign_type_id;

        // Calculate coins to deduct
        $coinsForUpgrade = 0;
        if ($upgraded && $oldType && $newType) {
            $coinsForUpgrade = max(0, $newType->coins_price - $oldType->coins_price);
        }

        $effectiveType = $upgraded ? $newType : $oldType;
        $coinsForDays = 0;
        if ($days > 0 && $effectiveType && $effectiveType->duration_days > 0) {
            $pricePerDay = $effectiveType->coins_price / $effectiveType->duration_days;
            $coinsForDays = (int) ceil($pricePerDay * $days);
        }

        $totalCoins = $coinsForUpgrade + $coinsForDays;

        if ($totalCoins > 0) {
            $balance = (int) ($employer->coin_balance ?? 0);
            if ($balance < $totalCoins) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'message' => 'Insufficient coins. Required: ' . $totalCoins . ', balance: ' . $balance . '.',
                    ], 402);
                }
                return redirect()->back()->with('error', 'Insufficient coins. Required: ' . $totalCoins . ', your balance: ' . $balance . '.');
            }
            $employer->coin_balance = $balance - $totalCoins;
            $employer->save();
        }

        if ($upgraded) {
            $campaign->campaign_type_id = $newTypeId;
            $campaign->save();
        }

        if ($days > 0) {
            $campaign->ends_at = $campaign->ends_at->addDays($days);
            $campaign->duration_days += $days;
            $campaign->save();
        }

        $campaign->load('campaignType');
        $message = [];
        if ($upgraded) {
            $message[] = 'Campaign upgraded.';
        }
        if ($days > 0) {
            $message[] = 'Listing extended by ' . $days . ' day(s).';
        }
        if ($totalCoins > 0) {
            $message[] = $totalCoins . ' coin(s) deducted.';
        }
        if (empty(array_filter([$upgraded, $days > 0, $totalCoins > 0]))) {
            $message[] = 'No changes made.';
        }

        if ($request->expectsJson()) {
            return response()->json([
                'message' => implode(' ', $message),
                'ends_at' => $campaign->ends_at->format('Y-m-d'),
                'campaign_type' => $campaign->campaignType ? ['id' => $campaign->campaignType->id, 'name' => $campaign->campaignType->name] : null,
                'coins_deducted' => $totalCoins,
                'new_balance' => (int) $employer->coin_balance,
            ]);
        }
        return redirect()->back()->with('success', implode(' ', $message));
    }

    /**
     * Increment share count and return share URL (for Share button).
     */
    public function share(Request $request, int $id)
    {
        $user = Auth::user();
        $employer = $user->employer;
        if (!$employer || !$employer->company_id) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }
            return redirect()->route('employer.dashboard')->with('error', 'Please set up your company profile first.');
        }

        $campaign = JobCampaign::with('jobAdvertisement')
            ->where('id', $id)
            ->whereHas('jobAdvertisement', fn($q) => $q->where('company_id', $employer->company_id))
            ->firstOrFail();

        $campaign->increment('shares_count');
        $job = $campaign->jobAdvertisement;
        $shareUrl = route('jobs.show', $job->id);

        if ($request->expectsJson()) {
            return response()->json(['url' => $shareUrl, 'message' => 'Share count updated.']);
        }
        return redirect()->back()->with('success', 'Share link copied.');
    }
}
