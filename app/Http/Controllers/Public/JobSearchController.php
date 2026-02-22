<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Services\JobAdvertisementService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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
        $perPage = min($request->get('per_page', 15), 50); // Limit max per page
        
        $filters = [
            'keyword' => $request->get('keyword'),
            'category_id' => $request->get('category_id'),
            'location' => $request->get('location'),
            'contract_type' => $request->get('contract_type'),
            'employment_type' => $request->get('employment_type'),
            'salary_min' => $request->get('salary_min'),
            'salary_max' => $request->get('salary_max'),
            'is_remote' => $request->get('is_remote'),
            'sort' => $request->get('sort'),
        ];

        // Remove empty filters (but keep is_remote if it's explicitly set)
        $filters = array_filter($filters, function ($value, $key) {
            if ($key === 'is_remote') {
                return $value !== null && $value !== '';
            }
            return $value !== null && $value !== '';
        }, ARRAY_FILTER_USE_BOTH);

        $jobs = $this->service->search($filters, $perPage);

        return response()->json($jobs);
    }

    /**
     * Get published jobs (public endpoint) - cached for 30 minutes.
     */
    public function published(Request $request): JsonResponse
    {
        $perPage = min($request->get('per_page', 15), 50);
        $page = $request->get('page', 1);
        
        // Cache key based on pagination
        $cacheKey = "published_jobs_page_{$page}_per_{$perPage}";
        
        $jobs = Cache::remember($cacheKey, 1800, function () use ($perPage) {
            return $this->service->getPaginated($perPage);
        });

        return response()->json($jobs);
    }

    /**
     * Get a single job by ID.
     */
    public function show(int $id): JsonResponse
    {
        // Cache job details for 30 minutes
        $cacheKey = "job_detail_{$id}";
        
        $result = Cache::remember($cacheKey, 1800, function () use ($id) {
            $job = $this->service->getById($id);

            if (!$job || $job->status !== 'published') {
                return null;
            }

            // Get similar jobs and other company jobs
            $similarJobs = $this->service->getSimilarJobs($job, 4);
            $otherCompanyJobs = $job->company_id 
                ? $this->service->getOtherCompanyJobs($job->company_id, $job->id, 3)
                : collect();

            return [
                'data' => $job->load(['company', 'category']),
                'similar_jobs' => $similarJobs,
                'other_company_jobs' => $otherCompanyJobs,
            ];
        });

        if (!$result) {
            return response()->json([
                'message' => 'Job not found',
            ], 404);
        }

        // Increment views (outside cache)
        $job = $this->service->getById($id);
        if ($job) {
            $this->service->incrementViews($job);
        }

        return response()->json($result);
    }
}
