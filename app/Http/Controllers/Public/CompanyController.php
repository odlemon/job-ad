<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\CompanyReview;
use App\Models\JobApplication;
use App\Models\JobAdvertisement;
use App\Services\CompanyPublicService;
use App\Services\JobSeeker\FollowedCompanyService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class CompanyController extends Controller
{
    public function __construct(
        protected CompanyPublicService $companies,
        protected FollowedCompanyService $followedCompanyService
    ) {}

    /**
     * Public companies listing page.
     */
    public function index(Request $request)
    {
        $filters = [
            'search' => $request->input('search'),
            'industry' => $request->input('industry'),
            'jobs' => $request->input('jobs'),
            'sort' => $request->input('sort', 'jobs'),
        ];

        $companies = $this->companies->paginateList($filters, (int) $request->input('per_page', 24));
        $industries = $this->companies->industries();
        $mediaBaseUrl = app(\App\Services\RemoteUploadService::class)->getMediaBaseUrl();

        if ($request->wantsJson()) {
            return response()->json([
                'data' => collect($companies->items())->map(fn (Company $c) => $this->companies->mapListItem($c))->values(),
                'industries' => $industries,
                'total' => $companies->total(),
                'meta' => [
                    'current_page' => $companies->currentPage(),
                    'last_page' => $companies->lastPage(),
                    'per_page' => $companies->perPage(),
                ],
            ]);
        }

        return view('companies.index', [
            'companies' => $companies,
            'industries' => $industries,
            'mediaBaseUrl' => $mediaBaseUrl,
            'filters' => $filters,
        ]);
    }

    /**
     * Lean public list API.
     */
    public function apiIndex(Request $request): JsonResponse
    {
        $filters = [
            'search' => $request->input('search'),
            'industry' => $request->input('industry'),
            'jobs' => $request->input('jobs'),
            'sort' => $request->input('sort', 'jobs'),
        ];
        $companies = $this->companies->paginateList($filters, (int) $request->input('per_page', 24));

        return response()->json([
            'data' => collect($companies->items())->map(fn (Company $c) => $this->companies->mapListItem($c))->values(),
            'industries' => $this->companies->industries(),
            'meta' => [
                'current_page' => $companies->currentPage(),
                'last_page' => $companies->lastPage(),
                'per_page' => $companies->perPage(),
                'total' => $companies->total(),
            ],
        ]);
    }

    /**
     * Lean public company detail by id or slug.
     */
    public function apiShow(string $idOrSlug): JsonResponse
    {
        $company = $this->companies->resolve($idOrSlug);
        if (! $company) {
            return response()->json(['message' => 'Company not found'], 404);
        }

        return response()->json([
            'data' => $this->companies->mapDetail($company, Auth::user()),
        ]);
    }

    /**
     * Paginated published jobs for a company (id or slug).
     */
    public function apiJobs(Request $request, string $idOrSlug): JsonResponse
    {
        $company = $this->companies->resolve($idOrSlug);
        if (! $company) {
            return response()->json(['message' => 'Company not found'], 404);
        }

        $paginator = $this->companies->jobsFor(
            $company,
            max(1, (int) $request->input('page', 1)),
            (int) $request->input('per_page', 10)
        );

        return response()->json([
            'data' => collect($paginator->items())->map(fn ($j) => $this->companies->mapJob($j))->values(),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
            ],
        ]);
    }

    /**
     * Paginated reviews for a company (id or slug).
     */
    public function apiReviews(Request $request, string $idOrSlug): JsonResponse
    {
        $company = $this->companies->resolve($idOrSlug);
        if (! $company) {
            return response()->json(['message' => 'Company not found'], 404);
        }

        $paginator = $this->companies->reviewsFor(
            $company,
            max(1, (int) $request->input('page', 1)),
            (int) $request->input('per_page', 10),
            (string) $request->input('sort', 'newest')
        );
        $stats = $this->companies->reviewStats($company->id);

        return response()->json([
            'data' => collect($paginator->items())->map(fn ($r) => $this->companies->mapReview($r))->values(),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'avg_rating' => $stats['avgRating'],
                'reviews_count' => $stats['reviewsCount'],
                'star_distribution' => $stats['starDistribution'],
                'category_averages' => $stats['categoryAverages'],
                'category_labels' => $stats['categoryLabels'],
            ],
        ]);
    }

    /**
     * Public company detail page.
     */
    public function show(Company $company)
    {
        if (! $company->is_active) {
            abort(404);
        }

        $company->loadCount([
            'jobAdvertisements as job_advertisements_count' => fn ($q) => $q->where('status', 'published'),
            'followers',
        ]);

        $openJobs = $this->companies->jobsFor($company, 1, 10);
        $stats = $this->companies->reviewStats($company->id);
        $reviews = $this->companies->reviewsFor($company, 1, 20);

        $mediaBaseUrl = app(\App\Services\RemoteUploadService::class)->getMediaBaseUrl();
        $user = Auth::user();
        $isFollowing = $this->companies->isFollowing($user, $company->id);

        $canAddReview = false;
        $reviewIneligibleReason = null;
        $jobIds = collect();
        if ($user && $user->jobSeeker) {
            $seeker = $user->jobSeeker;
            $seekerId = $seeker->seeker_id;
            $eligibleStatuses = [
                'shortlisted', 'interview_requested', 'hired',
                'interview', 'offered',
            ];
            $statusWhereRaw = "(LOWER(TRIM(status)) IN ('" . implode("','", array_map(function ($s) {
                return strtolower(addslashes($s));
            }, $eligibleStatuses)) . "'))";
            $jobIds = JobAdvertisement::query()->where('company_id', $company->id)->withTrashed()->pluck('id');
            $hasEligibleApplication = false;
            if ($jobIds->isNotEmpty()) {
                $hasEligibleApplication = JobApplication::query()
                    ->whereIn('job_advertisement_id', $jobIds)
                    ->where(function ($q) use ($seekerId, $user) {
                        $q->where('seeker_id', $seekerId);
                        if ($user->id) {
                            $q->orWhere('user_id', $user->id);
                        }
                    })
                    ->whereRaw($statusWhereRaw)
                    ->exists();
            }
            $alreadyReviewed = CompanyReview::query()
                ->where('company_id', $company->id)
                ->where('seeker_id', $seekerId)
                ->exists();
            if ($alreadyReviewed) {
                $reviewIneligibleReason = __('You have already submitted a review for this company.');
            } elseif (! $hasEligibleApplication) {
                $reviewIneligibleReason = __('You can add a review once you have been shortlisted, invited to interview, or hired at this company.');
            } else {
                $canAddReview = true;
            }
        }

        $reviewRole = null;
        $reviewLocation = null;
        $reviewEmploymentStatus = null;
        if ($canAddReview && $user && $user->jobSeeker) {
            $seeker = $user->jobSeeker;
            $eligibleApp = JobApplication::query()
                ->whereIn('job_advertisement_id', $jobIds->isEmpty() ? [0] : $jobIds->toArray())
                ->where(function ($q) use ($seeker, $user) {
                    $q->where('seeker_id', $seeker->seeker_id)->orWhere('user_id', $user->id);
                })
                ->whereRaw("(LOWER(TRIM(status)) IN ('shortlisted','interview_requested','hired','interview','offered'))")
                ->with('jobAdvertisement:id,title')
                ->latest()
                ->first();
            $reviewRole = $eligibleApp?->jobAdvertisement?->title
                ?? $seeker->experiences()->orderByDesc('start_date')->value('job_title');
            $reviewLocation = trim($seeker->address ?? '') ?: trim($seeker->location ?? '');
            $employmentMap = [
                'currently_employed' => 'Currently Employed',
                'unemployed' => 'Unemployed',
                'student' => 'Student',
                'self_employed' => 'Self Employed',
                'retired' => 'Retired',
            ];
            $reviewEmploymentStatus = $seeker->employment_status
                ? ($employmentMap[$seeker->employment_status] ?? ucfirst(str_replace('_', ' ', $seeker->employment_status)))
                : null;
        }

        return view('companies.show', [
            'company' => $company,
            'openJobs' => collect($openJobs->items()),
            'jobsMeta' => [
                'current_page' => $openJobs->currentPage(),
                'last_page' => $openJobs->lastPage(),
                'total' => $openJobs->total(),
            ],
            'mediaBaseUrl' => $mediaBaseUrl,
            'reviews' => collect($reviews->items()),
            'reviewsMeta' => [
                'current_page' => $reviews->currentPage(),
                'last_page' => $reviews->lastPage(),
                'total' => $reviews->total(),
            ],
            'reviewsCount' => $stats['reviewsCount'],
            'avgRating' => $stats['avgRating'],
            'starDistribution' => $stats['starDistribution'],
            'categoryLabels' => $stats['categoryLabels'],
            'categoryAverages' => $stats['categoryAverages'],
            'categoryCounts' => $stats['categoryCounts'],
            'canAddReview' => $canAddReview,
            'reviewIneligibleReason' => $reviewIneligibleReason,
            'reviewRole' => $reviewRole,
            'reviewLocation' => $reviewLocation,
            'reviewEmploymentStatus' => $reviewEmploymentStatus,
            'isFollowing' => $isFollowing,
            'isAuthenticatedSeeker' => (bool) ($user && $user->jobSeeker),
        ]);
    }

    /**
     * Debug: see why review eligibility check passes or fails (remove in production).
     */
    public function debugReviewEligibility(Company $company): JsonResponse
    {
        $user = Auth::user();
        $data = [
            'user_id' => $user?->id,
            'user_email' => $user?->email,
            'has_job_seeker' => $user && (bool) $user->jobSeeker,
            'seeker_id' => $user?->jobSeeker?->seeker_id,
            'company' => ['id' => $company->id, 'name' => $company->name, 'slug' => $company->slug],
        ];
        if (! $user || ! $user->jobSeeker) {
            return response()->json($data);
        }
        $seekerId = $user->jobSeeker->seeker_id;
        $jobIds = JobAdvertisement::query()->where('company_id', $company->id)->withTrashed()->pluck('id')->toArray();
        $data['company_job_ids'] = $jobIds;
        $allApplications = JobApplication::query()
            ->whereIn('job_advertisement_id', $jobIds)
            ->where(function ($q) use ($seekerId, $user) {
                $q->where('seeker_id', $seekerId)->orWhere('user_id', $user->id);
            })
            ->with('jobAdvertisement:id,company_id,title')
            ->get(['id', 'job_advertisement_id', 'seeker_id', 'user_id', 'status', 'email']);
        $data['applications_at_company'] = $allApplications->map(fn ($a) => [
            'id' => $a->id,
            'job_advertisement_id' => $a->job_advertisement_id,
            'seeker_id' => $a->seeker_id,
            'user_id' => $a->user_id,
            'status' => $a->status,
            'status_lower' => strtolower(trim($a->status ?? '')),
        ]);
        $eligibleStatuses = ['shortlisted', 'interview_requested', 'hired', 'interview', 'offered'];
        $data['eligible_statuses'] = $eligibleStatuses;
        $data['has_eligible_status'] = $allApplications->contains(fn ($a) => in_array(strtolower(trim($a->status ?? '')), $eligibleStatuses));
        $data['can_add_review'] = $allApplications->isNotEmpty() && $data['has_eligible_status'];

        return response()->json($data);
    }

    /**
     * Store a company review (job seeker only).
     */
    public function storeReview(Request $request, Company $company): JsonResponse
    {
        $user = Auth::user();
        if (! $user || ! $user->jobSeeker) {
            return response()->json(['message' => 'You must be logged in as a job seeker to submit a review.'], 403);
        }

        $seeker = $user->jobSeeker;
        $seekerId = $seeker->seeker_id;
        $eligibleStatuses = [
            'shortlisted', 'interview_requested', 'hired',
            'interview', 'offered',
        ];
        $statusWhereRaw = "(LOWER(TRIM(status)) IN ('" . implode("','", array_map(function ($s) {
            return strtolower(addslashes($s));
        }, $eligibleStatuses)) . "'))";
        $jobIds = JobAdvertisement::query()->where('company_id', $company->id)->withTrashed()->pluck('id');
        $hasEligibleApplication = false;
        if ($jobIds->isNotEmpty()) {
            $hasEligibleApplication = JobApplication::query()
                ->whereIn('job_advertisement_id', $jobIds)
                ->where(function ($q) use ($seekerId, $user) {
                    $q->where('seeker_id', $seekerId);
                    if ($user->id) {
                        $q->orWhere('user_id', $user->id);
                    }
                })
                ->whereRaw($statusWhereRaw)
                ->exists();
        }
        if (! $hasEligibleApplication) {
            return response()->json([
                'message' => 'You can add a review only after you have been shortlisted, invited to interview, or hired at this company.',
            ], 403);
        }

        $alreadyReviewed = CompanyReview::query()
            ->where('company_id', $company->id)
            ->where('seeker_id', $seekerId)
            ->exists();
        if ($alreadyReviewed) {
            return response()->json(['message' => 'You have already submitted a review for this company.'], 422);
        }

        $validated = $request->validate([
            'rating' => ['required', 'integer', Rule::in([1, 2, 3, 4, 5])],
            'work_life_balance' => ['nullable', 'numeric', 'min:1', 'max:5'],
            'benefits_perks' => ['nullable', 'numeric', 'min:1', 'max:5'],
            'work_environment_culture' => ['nullable', 'numeric', 'min:1', 'max:5'],
            'career_growth_development' => ['nullable', 'numeric', 'min:1', 'max:5'],
            'management_leadership' => ['nullable', 'numeric', 'min:1', 'max:5'],
            'employee_support_wellbeing' => ['nullable', 'numeric', 'min:1', 'max:5'],
            'role' => ['nullable', 'string', 'max:255'],
            'location' => ['nullable', 'string', 'max:255'],
            'employment_status' => ['nullable', 'string', 'max:255'],
            'good_things' => ['nullable', 'string', 'max:5000'],
            'challenges' => ['nullable', 'string', 'max:5000'],
        ]);

        $employmentMap = [
            'currently_employed' => 'Currently Employed',
            'unemployed' => 'Unemployed',
            'student' => 'Student',
            'self_employed' => 'Self Employed',
            'retired' => 'Retired',
        ];
        $role = $validated['role'] ?? null;
        $location = $validated['location'] ?? null;
        $employmentStatus = $validated['employment_status'] ?? null;
        if (empty(trim($role ?? ''))) {
            $eligibleApp = JobApplication::query()
                ->whereIn('job_advertisement_id', JobAdvertisement::where('company_id', $company->id)->withTrashed()->pluck('id'))
                ->where(fn ($q) => $q->where('seeker_id', $seeker->seeker_id)->orWhere('user_id', $user->id))
                ->whereRaw("(LOWER(TRIM(status)) IN ('shortlisted','interview_requested','hired','interview','offered'))")
                ->with('jobAdvertisement:id,title')
                ->latest()
                ->first();
            $role = $eligibleApp?->jobAdvertisement?->title
                ?? $seeker->experiences()->orderByDesc('start_date')->value('job_title');
        }
        if (empty(trim($location ?? ''))) {
            $location = trim($seeker->address ?? '') ?: trim($seeker->location ?? '');
        }
        if (empty(trim($employmentStatus ?? ''))) {
            $employmentStatus = $seeker->employment_status
                ? ($employmentMap[$seeker->employment_status] ?? ucfirst(str_replace('_', ' ', $seeker->employment_status)))
                : null;
        }
        $review = CompanyReview::query()->create([
            'company_id' => $company->id,
            'seeker_id' => $seeker->seeker_id,
            'rating' => (int) $validated['rating'],
            'work_life_balance' => isset($validated['work_life_balance']) ? (float) $validated['work_life_balance'] : null,
            'benefits_perks' => isset($validated['benefits_perks']) ? (float) $validated['benefits_perks'] : null,
            'work_environment_culture' => isset($validated['work_environment_culture']) ? (float) $validated['work_environment_culture'] : null,
            'career_growth_development' => isset($validated['career_growth_development']) ? (float) $validated['career_growth_development'] : null,
            'management_leadership' => isset($validated['management_leadership']) ? (float) $validated['management_leadership'] : null,
            'employee_support_wellbeing' => isset($validated['employee_support_wellbeing']) ? (float) $validated['employee_support_wellbeing'] : null,
            'role' => $role,
            'location' => $location,
            'employment_status' => $employmentStatus,
            'good_things' => $validated['good_things'] ?? null,
            'challenges' => $validated['challenges'] ?? null,
        ]);

        return response()->json([
            'message' => 'Thank you! Your review has been submitted.',
            'review' => [
                'id' => $review->id,
                'rating' => $review->rating,
                'role' => $review->role,
                'location' => $review->location,
                'employment_status' => $review->employment_status,
                'good_things' => $review->good_things,
                'challenges' => $review->challenges,
                'created_at' => $review->created_at->format('M Y'),
            ],
        ], 201);
    }

    /**
     * Follow a company (web session / job seeker).
     */
    public function follow(Company $company): JsonResponse
    {
        $user = Auth::user();
        if (! $user || ! $user->jobSeeker) {
            return response()->json(['message' => 'You must be logged in as a job seeker to follow companies.'], 403);
        }

        $this->followedCompanyService->followCompany($user->jobSeeker, $company->id);
        $company->loadCount('followers');

        return response()->json([
            'message' => 'Following',
            'is_following' => true,
            'followers_count' => (int) $company->followers_count,
        ]);
    }

    /**
     * Unfollow a company (web session / job seeker).
     */
    public function unfollow(Company $company): JsonResponse
    {
        $user = Auth::user();
        if (! $user || ! $user->jobSeeker) {
            return response()->json(['message' => 'You must be logged in as a job seeker.'], 403);
        }

        $this->followedCompanyService->unfollowCompany($user->jobSeeker, $company->id);
        $company->loadCount('followers');

        return response()->json([
            'message' => 'Unfollowed',
            'is_following' => false,
            'followers_count' => (int) $company->followers_count,
        ]);
    }

    /**
     * Get featured companies with job counts (cached for 1 hour).
     */
    public function featured(): JsonResponse
    {
        $companies = \Illuminate\Support\Facades\Cache::remember('featured_companies', 3600, function () {
            return Company::query()
                ->where('is_active', true)
                ->withCount(['jobAdvertisements' => function ($query) {
                    $query->where('status', 'published');
                }])
                ->orderByDesc('job_advertisements_count')
                ->limit(10)
                ->get(['id', 'name', 'slug', 'logo', 'location']);
        });

        return response()->json([
            'data' => $companies,
        ]);
    }
}
