<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class CompanyController extends Controller
{
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
