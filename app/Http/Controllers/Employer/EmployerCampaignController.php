<?php

namespace App\Http\Controllers\Employer;

use App\Http\Controllers\Controller;
use App\Models\CampaignType;
use App\Models\CoinTransaction;
use App\Models\JobAdvertisement;
use App\Models\JobApplication;
use App\Models\JobCampaign;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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

        // Load jobs with campaigns; use withCount instead of hydrating all applications
        $jobsQuery = JobAdvertisement::query()
            ->where('company_id', $companyId)
            ->whereHas('campaigns')
            ->with([
                'campaigns' => fn ($q) => $q->with('campaignType')->orderByDesc('launched_at'),
                'company',
            ])
            ->withCount('applications')
            ->orderByDesc('created_at');

        $jobs = $jobsQuery->get();
        $statusTab = $request->get('status', 'active');

        // Auto-expire first so counts are accurate
        JobCampaign::query()
            ->whereHas('jobAdvertisement', fn ($q) => $q->where('company_id', $companyId))
            ->where('status', 'active')
            ->where('ends_at', '<', now())
            ->update(['status' => 'expired']);

        $statusRows = JobCampaign::query()
            ->whereHas('jobAdvertisement', fn ($q) => $q->where('company_id', $companyId))
            ->selectRaw('status, COUNT(*) as c')
            ->groupBy('status')
            ->pluck('c', 'status');

        $statusCounts = [
            'scheduled' => (int) ($statusRows['pending'] ?? 0),
            'active' => (int) ($statusRows['active'] ?? 0),
            'paused' => (int) ($statusRows['paused'] ?? 0),
            'expired' => (int) ($statusRows['expired'] ?? 0),
            'draft' => (int) ($statusRows['draft'] ?? 0),
        ];

        $kpi = JobCampaign::query()
            ->whereHas('jobAdvertisement', fn ($q) => $q->where('company_id', $companyId))
            ->selectRaw('SUM(views_count) as total_views')
            ->selectRaw('SUM(clicks_count) as total_clicks')
            ->selectRaw('SUM(shares_count) as total_shares')
            ->selectRaw('SUM(saved_count) as total_saved')
            ->selectRaw("COUNT(DISTINCT CASE WHEN status = 'active' THEN job_advertisement_id END) as active_job_listing")
            ->first();

        $jobIdsWithCampaigns = JobCampaign::query()
            ->whereHas('jobAdvertisement', fn ($q) => $q->where('company_id', $companyId))
            ->distinct()
            ->pluck('job_advertisement_id');

        $totalApplications = $jobIdsWithCampaigns->isEmpty()
            ? 0
            : JobApplication::whereIn('job_advertisement_id', $jobIdsWithCampaigns)->count();

        $coinBalance = $employer->coin_balance ?? 0;
        $hasAnyCampaigns = $jobIdsWithCampaigns->isNotEmpty();
        $sort = $request->get('sort', 'most_recent');

        $campaignTypes = CampaignType::orderBy('sort_order')->get();

        return view('employer.campaigns.index', [
            'jobs' => $jobs,
            'campaignTypes' => $campaignTypes,
            'hasAnyCampaigns' => $hasAnyCampaigns,
            'statusCounts' => $statusCounts,
            'statusTab' => $statusTab,
            'stats' => [
                'active_job_listing' => (int) ($kpi->active_job_listing ?? 0),
                'total_views' => (int) ($kpi->total_views ?? 0),
                'total_clicks' => (int) ($kpi->total_clicks ?? 0),
                'total_applications' => $totalApplications,
                'total_shares' => (int) ($kpi->total_shares ?? 0),
                'total_saved' => (int) ($kpi->total_saved ?? 0),
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

        $jobs = JobAdvertisement::with([
            'company',
            'category',
            'campaigns' => fn ($q) => $q->where('status', 'active')->with('campaignType'),
        ])
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

        $paymentMethod = $validated['payment_method'];
        $totalCoinsNeeded = 0;
        $prepared = [];

        foreach ($validated['campaigns'] as $c) {
            $job = JobAdvertisement::where('id', $c['job_id'])
                ->where('company_id', $employer->company_id)
                ->first();
            if (!$job) {
                continue;
            }

            $type = CampaignType::find($c['campaign_type_id']);
            if (!$type) {
                continue;
            }

            $prepared[] = [
                'job' => $job,
                'type' => $type,
                'duration_days' => (int) $c['duration_days'],
            ];
            if ($paymentMethod === 'coin') {
                $totalCoinsNeeded += (int) $type->coins_price;
            }
        }

        if (empty($prepared)) {
            if ($request->expectsJson() || $request->wantsJson()) {
                return response()->json(['message' => 'No valid campaigns to launch.'], 422);
            }
            return redirect()->back()->with('error', 'No valid campaigns to launch.');
        }

        if ($paymentMethod === 'coin') {
            $balance = (int) ($employer->coin_balance ?? 0);
            if ($balance < $totalCoinsNeeded) {
                if ($request->expectsJson() || $request->wantsJson()) {
                    return response()->json([
                        'message' => 'Insufficient coins. Required: ' . $totalCoinsNeeded . ', balance: ' . $balance . '.',
                    ], 402);
                }
                return redirect()->back()->with('error', 'Insufficient coins.');
            }
        }

        $created = 0;
        DB::transaction(function () use ($employer, $prepared, $paymentMethod, $totalCoinsNeeded, &$created) {
            foreach ($prepared as $item) {
                $campaign = JobCampaign::create([
                    'job_advertisement_id' => $item['job']->id,
                    'campaign_type_id' => $item['type']->id,
                    'duration_days' => $item['duration_days'],
                    'status' => 'active',
                    'payment_method' => $paymentMethod,
                    'launched_at' => now(),
                    'ends_at' => now()->addDays($item['duration_days']),
                ]);
                $created++;

                if ($paymentMethod === 'coin') {
                    CoinTransaction::create([
                        'employer_id' => $employer->employer_id,
                        'type' => CoinTransaction::TYPE_SPEND,
                        'amount' => (int) $item['type']->coins_price,
                        'description' => 'Campaign: ' . $item['type']->name . ' for ' . $item['job']->title,
                        'reference_type' => JobCampaign::class,
                        'reference_id' => $campaign->id,
                    ]);
                }
            }

            if ($paymentMethod === 'coin' && $totalCoinsNeeded > 0) {
                $employer->refresh();
                $employer->coin_balance = (int) ($employer->coin_balance ?? 0) - $totalCoinsNeeded;
                $employer->save();
            }
        });

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

            CoinTransaction::create([
                'employer_id' => $employer->employer_id,
                'type' => CoinTransaction::TYPE_SPEND,
                'amount' => $totalCoins,
                'description' => 'Campaign extend/upgrade',
                'reference_type' => JobCampaign::class,
                'reference_id' => $campaign->id,
            ]);
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
