<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\ScoopJobPresenter;
use App\Services\JobAdvertisementService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class JobSearchController extends Controller
{
    public function __construct(
        private JobAdvertisementService $service
    ) {
    }

    /**
     * Search and filter jobs (public endpoint).
     */
    public function search(Request $request): JsonResponse
    {
        $filters = [
            'keyword' => $request->get('keyword', $request->get('query')),
            'category_id' => $request->get('category_id'),
            'location' => $request->get('location'),
            'contract_type' => $request->get('contract_type', $request->get('job_type')),
            'employment_type' => $request->get('employment_type'),
            'experience_tags' => $request->get('experience_tags'),
            'salary_min' => $request->get('salary_min'),
            'salary_max' => $request->get('salary_max'),
            'is_remote' => $request->get('is_remote'),
            'sort' => $request->get('sort', 'newest'),
            'education' => $request->get('education'),
        ];

        // Remove empty filters (but keep is_remote if it's explicitly set)
        $filters = array_filter($filters, function ($value, $key) {
            if ($key === 'is_remote') {
                return $value !== null && $value !== '';
            }
            return $value !== null && $value !== '';
        }, ARRAY_FILTER_USE_BOTH);

        // Map Scoop sort=newest → repository latest
        if (($filters['sort'] ?? null) === 'newest') {
            $filters['sort'] = 'latest';
        }

        // Support CSV category/location/job_type names from Scoop
        if ($request->filled('category') && ! isset($filters['category_id'])) {
            $names = array_filter(array_map('trim', explode(',', (string) $request->get('category'))));
            if ($names) {
                $ids = \App\Models\JobCategory::whereIn('name', $names)->pluck('id');
                if ($ids->isNotEmpty()) {
                    $filters['category_id'] = $ids->all();
                }
            }
        }

        $perPage = min((int) $request->get('per_page', $request->get('limit', 15)), 50);
        $jobs = $this->service->search($filters, $perPage);
        $seekerId = $this->optionalSeekerId($request);

        $data = ScoopJobPresenter::jobs($jobs->items(), $seekerId);

        return response()->json([
            'data' => $data,
            'meta' => [
                'current_page' => $jobs->currentPage(),
                'last_page' => $jobs->lastPage(),
                'total' => $jobs->total(),
                'per_page' => $jobs->perPage(),
            ],
        ]);
    }

    /**
     * Get published jobs (public endpoint) - job list cached; is_saved is per-user and never cached.
     */
    public function published(Request $request): JsonResponse
    {
        $perPage = min((int) $request->get('per_page', $request->get('limit', 15)), 50);
        $page = (int) $request->get('page', 1);

        $cacheKey = "published_jobs_scoop_page_{$page}_per_{$perPage}";

        $jobs = Cache::remember($cacheKey, 1800, function () use ($perPage) {
            return $this->service->getPaginated($perPage);
        });

        $seekerId = $this->optionalSeekerId($request);
        $data = ScoopJobPresenter::jobs($jobs->items(), $seekerId);

        return response()->json([
            'data' => $data,
            'meta' => [
                'current_page' => $jobs->currentPage(),
                'last_page' => $jobs->lastPage(),
                'total' => $jobs->total(),
                'per_page' => $jobs->perPage(),
            ],
        ]);
    }

    /**
     * Get a single job by ID.
     */
    public function show(Request $request, int $id): JsonResponse
    {
        $job = $this->service->getById($id);

        if (! $job || $job->status !== 'published') {
            return response()->json(['message' => 'Job not found'], 404);
        }

        $job->load(['company', 'category']);
        $this->service->incrementViews($job);

        $seekerId = $this->optionalSeekerId($request);

        $similarJobs = $this->service->getSimilarJobs($job, 4);
        $otherCompanyJobs = $job->company_id
            ? $this->service->getOtherCompanyJobs($job->company_id, $job->id, 3)
            : collect();

        $allJobs = collect([$job])
            ->merge($similarJobs)
            ->merge($otherCompanyJobs)
            ->values();
        $presented = ScoopJobPresenter::jobs($allJobs, $seekerId);
        $byId = collect($presented)->keyBy('id');

        return response()->json([
            'data' => $byId->get($job->id),
            'similar_jobs' => collect($similarJobs)->map(fn ($j) => $byId->get($j->id))->filter()->values(),
            'other_company_jobs' => collect($otherCompanyJobs)->map(fn ($j) => $byId->get($j->id))->filter()->values(),
        ]);
    }

    /**
     * Resolve the current job seeker from a Bearer token even on public routes.
     */
    private function optionalSeekerId(Request $request): ?int
    {
        /** @var User|null $user */
        $user = $request->user('sanctum')
            ?? Auth::guard('sanctum')->user()
            ?? Auth::user();

        if (! $user || $user->user_type !== 'job_seeker') {
            return null;
        }

        return $user->jobSeeker?->seeker_id;
    }
}
