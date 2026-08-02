<?php

namespace App\Http\Controllers\JobSeeker;

use App\Http\Controllers\Controller;
use App\Models\JobAdvertisement;
use App\Models\JobApplication;
use App\Services\JobSeeker\FollowedCompanyService;
use App\Services\JobSeeker\JobSeekerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class FollowedCompanyController extends Controller
{
    public function __construct(
        private FollowedCompanyService $followedCompanyService,
        private JobSeekerService $jobSeekerService
    ) {
    }

    /**
     * In-portal Company Engagement page (Bolt).
     */
    public function page(): View
    {
        return view('job-seeker.followed-companies');
    }

    /**
     * Get all followed companies for authenticated job seeker.
     */
    public function index(Request $request): JsonResponse
    {
        $user = Auth::user();
        $jobSeeker = $this->jobSeekerService->getByUserId($user->id);

        if (!$jobSeeker) {
            return response()->json([
                'message' => 'Job seeker profile not found',
            ], 404);
        }

        $perPage = min((int) $request->get('per_page', 50), 100);
        $followedCompanies = $this->followedCompanyService->getPaginatedBySeeker($jobSeeker, $perPage);

        $items = collect($followedCompanies->items())->map(function ($follow) {
            $company = $follow->company;
            if (!$company) {
                return null;
            }

            $logo = $company->logo;
            if ($logo && ! str_starts_with($logo, 'http://') && ! str_starts_with($logo, 'https://')) {
                $logo = asset('storage/' . ltrim($logo, '/'));
            }

            return [
                'follow_id' => $follow->follow_id ?? $follow->id,
                'followed_at' => optional($follow->followed_at)->toIso8601String(),
                'company_id' => $company->id,
                'name' => $company->name,
                'slug' => $company->slug,
                'industry' => $company->industry,
                'size' => $company->size,
                'location' => $company->location,
                'description' => $company->description,
                'website' => $company->website,
                'logo' => $logo,
                'jobs_count' => (int) ($company->jobs_count ?? 0),
                'reviews_count' => (int) ($company->reviews_count ?? 0),
                'rating' => $company->reviews_avg_rating !== null
                    ? round((float) $company->reviews_avg_rating, 1)
                    : null,
                'url' => $company->slug
                    ? url('/companies/' . $company->slug)
                    : url('/companies/' . $company->id),
            ];
        })->filter()->values();

        $companyIds = $items->pluck('company_id')->all();

        $newOpenings = 0;
        if ($companyIds !== []) {
            $newOpenings = JobAdvertisement::query()
                ->whereIn('company_id', $companyIds)
                ->where('status', 'published')
                ->where(function ($q) {
                    $q->where('published_at', '>=', now()->subDays(30))
                        ->orWhere(function ($q2) {
                            $q2->whereNull('published_at')
                                ->where('created_at', '>=', now()->subDays(30));
                        });
                })
                ->count();
        }

        // Employers who reviewed the seeker's applications ≈ profile/application views
        $profileViews = JobApplication::query()
            ->where('seeker_id', $jobSeeker->seeker_id)
            ->where(function ($q) {
                $q->whereNotNull('reviewed_at')
                    ->orWhereIn('status', ['reviewing', 'in_review', 'interview', 'shortlisted', 'interview_requested', 'offered', 'hired', 'rejected']);
            })
            ->count();

        return response()->json([
            'stats' => [
                'following' => (int) $followedCompanies->total(),
                'new_openings' => $newOpenings,
                'profile_views' => $profileViews,
            ],
            'data' => $items,
            'meta' => [
                'current_page' => $followedCompanies->currentPage(),
                'last_page' => $followedCompanies->lastPage(),
                'per_page' => $followedCompanies->perPage(),
                'total' => $followedCompanies->total(),
            ],
        ]);
    }

    /**
     * Check if a company is followed.
     */
    public function check(int $companyId): JsonResponse
    {
        $user = Auth::user();
        $jobSeeker = $this->jobSeekerService->getByUserId($user->id);

        if (!$jobSeeker) {
            return response()->json([
                'message' => 'Job seeker profile not found',
            ], 404);
        }

        $isFollowed = $this->followedCompanyService->isCompanyFollowed($jobSeeker, $companyId);

        return response()->json([
            'is_followed' => $isFollowed,
        ]);
    }

    /**
     * Follow a company.
     */
    public function store(Request $request): JsonResponse
    {
        $user = Auth::user();
        $jobSeeker = $this->jobSeekerService->getByUserId($user->id);

        if (!$jobSeeker) {
            return response()->json([
                'message' => 'Job seeker profile not found',
            ], 404);
        }

        $request->validate([
            'company_id' => 'required|exists:companies,id',
        ]);

        $followedCompany = $this->followedCompanyService->followCompany($jobSeeker, $request->company_id);

        return response()->json([
            'message' => 'Company followed successfully',
            'followed_company' => $followedCompany,
        ], 201);
    }

    /**
     * Unfollow a company.
     */
    public function destroy(int $companyId): JsonResponse
    {
        $user = Auth::user();
        $jobSeeker = $this->jobSeekerService->getByUserId($user->id);

        if (!$jobSeeker) {
            return response()->json([
                'message' => 'Job seeker profile not found',
            ], 404);
        }

        $deleted = $this->followedCompanyService->unfollowCompany($jobSeeker, $companyId);

        if (!$deleted) {
            return response()->json([
                'message' => 'Company not found in followed companies',
            ], 404);
        }

        return response()->json([
            'message' => 'Company unfollowed successfully',
        ]);
    }
}
