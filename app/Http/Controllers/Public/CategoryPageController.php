<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class CategoryPageController extends Controller
{
    public function index(): View
    {
        // Plain arrays + file cache: DB cache + Eloquent serialization was a soft-nav bottleneck.
        $categories = Cache::store('file')->remember('categories_page_rows_v1', 600, function () {
            return DB::table('job_categories as c')
                ->leftJoin('job_advertisements as j', function ($join) {
                    $join->on('j.category_id', '=', 'c.id')
                        ->where('j.status', '=', 'published');
                })
                ->where('c.is_active', 1)
                ->whereNull('c.deleted_at')
                ->groupBy('c.id', 'c.name', 'c.slug', 'c.icon', 'c.sort_order')
                ->orderBy('c.sort_order')
                ->orderBy('c.name')
                ->selectRaw('c.id, c.name, c.slug, c.icon, COUNT(j.id) as jobs_count')
                ->get()
                ->map(static fn ($row) => [
                    'id' => (int) $row->id,
                    'name' => $row->name,
                    'slug' => $row->slug,
                    'icon' => $row->icon,
                    'jobs_count' => (int) $row->jobs_count,
                ])
                ->all();
        });

        $totalJobs = (int) array_sum(array_column($categories, 'jobs_count'));

        $palette = [
            ['color' => 'text-blue-600 dark:text-blue-400', 'bg' => 'bg-blue-50 dark:bg-blue-900/20'],
            ['color' => 'text-green-600 dark:text-green-400', 'bg' => 'bg-green-50 dark:bg-green-900/20'],
            ['color' => 'text-purple-600 dark:text-purple-400', 'bg' => 'bg-purple-50 dark:bg-purple-900/20'],
            ['color' => 'text-orange-600 dark:text-orange-400', 'bg' => 'bg-orange-50 dark:bg-orange-900/20'],
            ['color' => 'text-pink-600 dark:text-pink-400', 'bg' => 'bg-pink-50 dark:bg-pink-900/20'],
            ['color' => 'text-cyan-600 dark:text-cyan-400', 'bg' => 'bg-cyan-50 dark:bg-cyan-900/20'],
            ['color' => 'text-red-600 dark:text-red-400', 'bg' => 'bg-red-50 dark:bg-red-900/20'],
            ['color' => 'text-indigo-600 dark:text-indigo-400', 'bg' => 'bg-indigo-50 dark:bg-indigo-900/20'],
            ['color' => 'text-teal-600 dark:text-teal-400', 'bg' => 'bg-teal-50 dark:bg-teal-900/20'],
            ['color' => 'text-amber-600 dark:text-amber-400', 'bg' => 'bg-amber-50 dark:bg-amber-900/20'],
        ];

        return view('categories.index', [
            'categories' => $categories,
            'totalJobs' => $totalJobs,
            'palette' => $palette,
        ]);
    }
}
