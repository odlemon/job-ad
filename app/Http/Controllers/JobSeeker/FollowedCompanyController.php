<?php

namespace App\Http\Controllers\JobSeeker;

use App\Http\Controllers\Controller;
use App\Services\JobSeeker\JobSeekerService;
use App\Services\JobSeeker\FollowedCompanyService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FollowedCompanyController extends Controller
{
    public function __construct(
        private FollowedCompanyService $followedCompanyService,
        private JobSeekerService $jobSeekerService
    ) {
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

        $perPage = $request->get('per_page', 15);
        $followedCompanies = $this->followedCompanyService->getPaginatedBySeeker($jobSeeker, $perPage);

        return response()->json($followedCompanies);
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
