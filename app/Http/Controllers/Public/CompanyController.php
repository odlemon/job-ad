<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\CompanyReview;
use App\Models\JobApplication;
use App\Models\JobAdvertisement;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\Rule;

class CompanyController extends Controller
{
    /**
     * Public companies listing page.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $industry = $request->input('industry');

        $query = Company::query()
            ->where('is_active', true)
            ->withCount([
                'jobAdvertisements' => function ($q) {
                    $q->where('status', 'published');
                },
                'followers',
            ]);

        if ($search) {
            $query->where('name', 'like', '%' . $search . '%');
        }

        if ($industry) {
            $query->where('industry', $industry);
        }

        if ($request->input('jobs') === 'available') {
            $query->having('job_advertisements_count', '>', 0);
        }

        $companies = $query
            ->orderByDesc('job_advertisements_count')
            ->paginate(24)
            ->withQueryString();

        $industries = Cache::remember('public_company_industries', 3600, function () {
            return Company::query()
                ->where('is_active', true)
                ->whereNotNull('industry')
                ->where('industry', '!=', '')
                ->distinct()
                ->orderBy('industry')
                ->pluck('industry')
                ->values();
        });

        if ($request->wantsJson()) {
            return response()->json([
                'data' => $companies->items(),
                'industries' => $industries,
                'total' => $companies->total(),
                'meta' => [
                    'current_page' => $companies->currentPage(),
                    'last_page' => $companies->lastPage(),
                    'per_page' => $companies->perPage(),
                ],
            ]);
        }

        $mediaBaseUrl = app(\App\Services\RemoteUploadService::class)->getMediaBaseUrl();

        return view('companies.index', [
            'companies' => $companies,
            'industries' => $industries,
            'mediaBaseUrl' => $mediaBaseUrl,
            'filters' => [
                'search' => $search,
                'industry' => $industry,
                'jobs' => $request->input('jobs'),
            ],
        ]);
    }

    /**
     * Public company detail page.
     */
    public function show(Company $company)
    {
        if (!$company->is_active) {
            abort(404);
        }

        $company->loadCount(['jobAdvertisements' => function ($q) {
            $q->where('status', 'published');
        }, 'followers']);

        $openJobs = JobAdvertisement::query()
            ->where('company_id', $company->id)
            ->where('status', 'published')
            ->with(['category:id,name'])
            ->latest('published_at')
            ->take(5)
            ->get();

        $stats = CompanyReview::query()
            ->where('company_id', $company->id)
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
                ->where('company_id', $company->id)
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

        // Only hydrate recent reviews for the list UI (not all rows)
        $reviews = CompanyReview::query()
            ->where('company_id', $company->id)
            ->latest('created_at')
            ->limit(50)
            ->get();

        $mediaBaseUrl = app(\App\Services\RemoteUploadService::class)->getMediaBaseUrl();
        $user = Auth::user();
        $canAddReview = false;
        $reviewIneligibleReason = null;
        $jobIds = collect();
        if ($user && $user->jobSeeker) {
            $seeker = $user->jobSeeker;
            $seekerId = $seeker->seeker_id;
            // Eligible statuses: shortlisted, interview requested, or hired for a job at this company (include alternate DB values)
            $eligibleStatuses = [
                'shortlisted', 'interview_requested', 'hired',
                'interview', 'offered',
            ];
            // Case-insensitive status check (DB may store with different casing)
            $statusWhereRaw = "(LOWER(TRIM(status)) IN ('" . implode("','", array_map(function ($s) {
                return strtolower(addslashes($s));
            }, $eligibleStatuses)) . "'))";
            // Job IDs for this company (include soft-deleted jobs)
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
            } elseif (!$hasEligibleApplication) {
                $reviewIneligibleReason = __('You can add a review once you have been shortlisted, invited to interview, or hired at this company.');
            } else {
                $canAddReview = true;
            }
        }

        // Pre-fill review form from profile when user can add review
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
            'openJobs' => $openJobs,
            'mediaBaseUrl' => $mediaBaseUrl,
            'reviews' => $reviews,
            'reviewsCount' => $reviewsCount,
            'avgRating' => $avgRating,
            'starDistribution' => $starDistribution,
            'categoryLabels' => $categoryLabels,
            'categoryAverages' => $categoryAverages,
            'categoryCounts' => $categoryCounts,
            'canAddReview' => $canAddReview,
            'reviewIneligibleReason' => $reviewIneligibleReason,
            'reviewRole' => $reviewRole,
            'reviewLocation' => $reviewLocation,
            'reviewEmploymentStatus' => $reviewEmploymentStatus,
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
        if (!$user || !$user->jobSeeker) {
            return response()->json($data);
        }
        $seekerId = $user->jobSeeker->seeker_id;
        $jobIds = \App\Models\JobAdvertisement::query()->where('company_id', $company->id)->withTrashed()->pluck('id')->toArray();
        $data['company_job_ids'] = $jobIds;
        $allApplications = \App\Models\JobApplication::query()
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
        if (!$user || !$user->jobSeeker) {
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
        if (!$hasEligibleApplication) {
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

        $seeker = $user->jobSeeker;
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
     * Get featured companies with job counts (cached for 1 hour).
     */
    public function featured(): JsonResponse
    {
        $companies = Cache::remember('featured_companies', 3600, function () {
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
