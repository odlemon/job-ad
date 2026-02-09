<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\JobCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class CategoryController extends Controller
{
    /**
     * Get popular categories with job counts (cached for 1 hour).
     */
    public function popular(): JsonResponse
    {
        $categories = Cache::remember('popular_categories', 3600, function () {
            return JobCategory::query()
                ->where('is_active', true)
                ->withCount(['jobAdvertisements' => function ($query) {
                    $query->where('status', 'published');
                }])
                ->orderByDesc('job_advertisements_count')
                ->limit(6)
                ->get(['id', 'name', 'slug', 'icon']);
        });

        return response()->json([
            'data' => $categories,
        ]);
    }

    /**
     * Get all active categories (cached for 1 hour).
     */
    public function index(): JsonResponse
    {
        $categories = Cache::remember('all_categories', 3600, function () {
            return JobCategory::query()
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->get(['id', 'name', 'slug']);
        });

        return response()->json([
            'data' => $categories,
        ]);
    }
}
