<?php

namespace App\Http\Controllers\Employer;

use App\Http\Controllers\Controller;
use App\Models\JobAdvertisement;
use App\Models\JobApplication;
use App\Models\JobCampaign;
use App\Models\JobShare;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class EmployerAnalyticsController extends Controller
{
    public function index(Request $request)
    {
        $employer = Auth::user()?->employer;
        if (!$employer?->company_id) {
            return redirect()->route('employer.dashboard')
                ->with('error', 'Please set up your company profile first.');
        }

        $range = $request->get('range', '30');
        $data = $this->buildAnalytics($employer->company_id, $range);

        return view('employer.analytics.index', [
            'range' => $range,
            'metrics' => $data['metrics'],
            'topJobs' => $data['topJobs'],
            'trafficSources' => $data['trafficSources'],
            'funnel' => $data['funnel'],
            'insights' => $data['insights'],
        ]);
    }

    public function export(Request $request): StreamedResponse
    {
        $employer = Auth::user()?->employer;
        abort_unless($employer?->company_id, 403);

        $range = $request->get('range', '30');
        $data = $this->buildAnalytics($employer->company_id, $range);

        $filename = 'analytics-report-' . now()->format('Y-m-d') . '.csv';

        return response()->streamDownload(function () use ($data, $range) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Analytics Report', 'Range', $range, 'Generated', now()->toDateTimeString()]);
            fputcsv($out, []);
            fputcsv($out, ['Metric', 'Value', 'Change']);
            foreach ($data['metrics'] as $m) {
                fputcsv($out, [$m['label'], $m['raw_value'], $m['change']]);
            }
            fputcsv($out, []);
            fputcsv($out, ['Top Jobs', 'Views', 'Applications', 'Conversion %']);
            foreach ($data['topJobs'] as $job) {
                fputcsv($out, [$job['title'], $job['views'], $job['applications'], $job['conversion_rate']]);
            }
            fputcsv($out, []);
            fputcsv($out, ['Traffic Source', 'Count', 'Percentage']);
            foreach ($data['trafficSources'] as $src) {
                fputcsv($out, [$src['source'], $src['count'], $src['percentage']]);
            }
            fclose($out);
        }, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }

    private function buildAnalytics(int $companyId, string $range): array
    {
        [$start, $prevStart, $prevEnd] = $this->rangeBounds($range);

        $jobIds = JobAdvertisement::query()
            ->where('company_id', $companyId)
            ->pluck('id');

        if ($jobIds->isEmpty()) {
            return [
                'metrics' => [
                    ['label' => 'Total Impressions', 'value' => '0', 'raw_value' => 0, 'change' => 'All time', 'trend' => 'up', 'show_trend' => false],
                    ['label' => 'Total Clicks', 'value' => '0', 'raw_value' => 0, 'change' => 'All time', 'trend' => 'up', 'show_trend' => false],
                    ['label' => 'Click-through Rate', 'value' => '0%', 'raw_value' => 0, 'change' => 'Clicks / impressions', 'trend' => 'up', 'show_trend' => false],
                    ['label' => 'Applications', 'value' => '0', 'raw_value' => 0, 'change' => 'No change', 'trend' => 'up', 'show_trend' => false],
                    ['label' => 'Conversion Rate', 'value' => '0%', 'raw_value' => 0, 'change' => 'Apps / impressions', 'trend' => 'up', 'show_trend' => false],
                    ['label' => 'Avg. Days to Apply', 'value' => '—', 'raw_value' => '—', 'change' => 'Post → apply', 'trend' => 'up', 'show_trend' => false],
                ],
                'topJobs' => [],
                'trafficSources' => [
                    ['source' => 'Organic Listings', 'count' => 0, 'percentage' => 0],
                    ['source' => 'Campaigns', 'count' => 0, 'percentage' => 0],
                    ['source' => 'Shares', 'count' => 0, 'percentage' => 0],
                    ['source' => 'Saves', 'count' => 0, 'percentage' => 0],
                ],
                'funnel' => [
                    ['label' => 'Impressions', 'value' => 0, 'display' => '0', 'height' => 'h-32', 'from' => '#2563eb', 'to' => '#60a5fa'],
                    ['label' => 'Clicks', 'value' => 0, 'display' => '0', 'height' => 'h-28', 'from' => '#0891b2', 'to' => '#22d3ee'],
                    ['label' => 'Applications', 'value' => 0, 'display' => '0', 'height' => 'h-24', 'from' => '#059669', 'to' => '#34d399'],
                    ['label' => 'Shortlisted', 'value' => 0, 'display' => '0', 'height' => 'h-20', 'from' => '#7c3aed', 'to' => '#a78bfa'],
                    ['label' => 'Interviews', 'value' => 0, 'display' => '0', 'height' => 'h-16', 'from' => '#ea580c', 'to' => '#fb923c'],
                ],
                'insights' => [
                    ['title' => 'Peak Performance', 'body' => 'Post jobs to start collecting insights', 'tone' => 'blue'],
                    ['title' => 'Salary Visibility', 'body' => 'Add salary ranges to compare engagement', 'tone' => 'emerald'],
                ],
            ];
        }

        $appsQuery = JobApplication::query()
            ->whereIn('job_advertisement_id', $jobIds);

        $appsInRange = (clone $appsQuery)
            ->when($start, fn ($q) => $q->where('created_at', '>=', $start))
            ->count();

        $appsPrev = 0;
        if ($start && $prevStart && $prevEnd) {
            $appsPrev = (clone $appsQuery)
                ->whereBetween('created_at', [$prevStart, $prevEnd])
                ->count();
        }

        $shortlisted = (clone $appsQuery)
            ->when($start, fn ($q) => $q->where('created_at', '>=', $start))
            ->whereIn('status', ['shortlisted', 'interview', 'hired', 'offered'])
            ->count();

        $interviews = (clone $appsQuery)
            ->when($start, fn ($q) => $q->where('created_at', '>=', $start))
            ->whereIn('status', ['reviewing', 'in_review', 'interview_requested'])
            ->count();

        $jobViews = (int) JobAdvertisement::query()
            ->where('company_id', $companyId)
            ->sum('views_count');

        $campaignAgg = JobCampaign::query()
            ->whereIn('job_advertisement_id', $jobIds)
            ->selectRaw('COALESCE(SUM(views_count),0) as views, COALESCE(SUM(clicks_count),0) as clicks, COALESCE(SUM(shares_count),0) as shares, COALESCE(SUM(saved_count),0) as saved')
            ->first();

        $campaignViews = (int) ($campaignAgg->views ?? 0);
        $clicks = (int) ($campaignAgg->clicks ?? 0);
        $shares = (int) ($campaignAgg->shares ?? 0);
        $saved = (int) ($campaignAgg->saved ?? 0);

        // Impressions: prefer job page views; fall back to campaign views if jobs have none
        $impressions = $jobViews > 0 ? $jobViews : $campaignViews;
        if ($campaignViews > $jobViews) {
            $impressions = max($jobViews, $campaignViews);
        }

        $ctr = $impressions > 0 ? round(($clicks / $impressions) * 100, 1) : 0.0;
        $conversion = $impressions > 0 ? round(($appsInRange / $impressions) * 100, 1) : 0.0;

        $driver = DB::connection()->getDriverName();
        $avgDaysExpr = $driver === 'sqlite'
            ? 'AVG(JULIANDAY(job_applications.created_at) - JULIANDAY(job_advertisements.published_at)) as avg_days'
            : 'AVG(TIMESTAMPDIFF(HOUR, job_advertisements.published_at, job_applications.created_at) / 24) as avg_days';

        $avgDaysToApply = (clone $appsQuery)
            ->when($start, fn ($q) => $q->where('job_applications.created_at', '>=', $start))
            ->join('job_advertisements', 'job_advertisements.id', '=', 'job_applications.job_advertisement_id')
            ->whereNotNull('job_advertisements.published_at')
            ->selectRaw($avgDaysExpr)
            ->value('avg_days');

        $avgDaysLabel = $avgDaysToApply !== null
            ? (round((float) $avgDaysToApply, 1) . 'd')
            : '—';

        $appsChange = $this->percentChange($appsInRange, $appsPrev);

        $metrics = [
            [
                'label' => 'Total Impressions',
                'value' => $this->formatCompact($impressions),
                'raw_value' => $impressions,
                'change' => 'All time',
                'trend' => 'up',
                'show_trend' => false,
            ],
            [
                'label' => 'Total Clicks',
                'value' => $this->formatCompact($clicks),
                'raw_value' => $clicks,
                'change' => 'All time',
                'trend' => 'up',
                'show_trend' => false,
            ],
            [
                'label' => 'Click-through Rate',
                'value' => $ctr . '%',
                'raw_value' => $ctr,
                'change' => 'Clicks / impressions',
                'trend' => 'up',
                'show_trend' => false,
            ],
            [
                'label' => 'Applications',
                'value' => number_format($appsInRange),
                'raw_value' => $appsInRange,
                'change' => $appsChange['label'],
                'trend' => $appsChange['trend'],
                'show_trend' => $appsChange['show'],
            ],
            [
                'label' => 'Conversion Rate',
                'value' => $conversion . '%',
                'raw_value' => $conversion,
                'change' => 'Apps / impressions',
                'trend' => 'up',
                'show_trend' => false,
            ],
            [
                'label' => 'Avg. Days to Apply',
                'value' => $avgDaysLabel,
                'raw_value' => $avgDaysLabel,
                'change' => 'Post → apply',
                'trend' => 'up',
                'show_trend' => false,
            ],
        ];

        $topJobs = JobAdvertisement::query()
            ->where('company_id', $companyId)
            ->withCount([
                'applications as applications_in_range' => function ($q) use ($start) {
                    if ($start) {
                        $q->where('created_at', '>=', $start);
                    }
                },
            ])
            ->orderByDesc('views_count')
            ->limit(4)
            ->get()
            ->map(function (JobAdvertisement $job) {
                $views = (int) ($job->views_count ?? 0);
                $apps = (int) ($job->applications_in_range ?? 0);
                $rate = $views > 0 ? round(($apps / $views) * 100, 1) : 0.0;

                return [
                    'id' => $job->id,
                    'title' => $job->title,
                    'views' => $views,
                    'applications' => $apps,
                    'conversion_rate' => $rate,
                    'trend' => $rate >= 10 ? 'up' : 'down',
                ];
            })
            ->values()
            ->all();

        $organicViews = max(0, $jobViews - $campaignViews);
        $sourceParts = [
            ['source' => 'Organic Listings', 'count' => $organicViews > 0 ? $organicViews : $jobViews],
            ['source' => 'Campaigns', 'count' => $campaignViews],
            ['source' => 'Shares', 'count' => $shares > 0 ? $shares : (int) JobShare::query()->whereIn('job_id', $jobIds)->count()],
            ['source' => 'Saves', 'count' => $saved],
        ];
        $sourceTotal = array_sum(array_column($sourceParts, 'count'));
        $trafficSources = array_map(function ($row) use ($sourceTotal) {
            $pct = $sourceTotal > 0 ? (int) round(($row['count'] / $sourceTotal) * 100) : 0;
            return [
                'source' => $row['source'],
                'count' => $row['count'],
                'percentage' => $pct,
            ];
        }, $sourceParts);

        $funnel = [
            ['label' => 'Impressions', 'value' => $impressions, 'display' => $this->formatCompact($impressions), 'height' => 'h-32', 'from' => '#2563eb', 'to' => '#60a5fa'],
            ['label' => 'Clicks', 'value' => $clicks, 'display' => $this->formatCompact($clicks), 'height' => 'h-28', 'from' => '#0891b2', 'to' => '#22d3ee'],
            ['label' => 'Applications', 'value' => $appsInRange, 'display' => number_format($appsInRange), 'height' => 'h-24', 'from' => '#059669', 'to' => '#34d399'],
            ['label' => 'Shortlisted', 'value' => $shortlisted, 'display' => number_format($shortlisted), 'height' => 'h-20', 'from' => '#7c3aed', 'to' => '#a78bfa'],
            ['label' => 'Interviews', 'value' => $interviews, 'display' => number_format($interviews), 'height' => 'h-16', 'from' => '#ea580c', 'to' => '#fb923c'],
        ];

        $peakDay = (clone $appsQuery)
            ->when($start, fn ($q) => $q->where('created_at', '>=', $start))
            ->selectRaw($driver === 'sqlite'
                ? "strftime('%w', created_at) as dow, COUNT(*) as c"
                : 'DAYOFWEEK(created_at) as dow, COUNT(*) as c')
            ->groupBy('dow')
            ->orderByDesc('c')
            ->first();

        $dayNames = $driver === 'sqlite'
            ? ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday']
            : [1 => 'Sunday', 2 => 'Monday', 3 => 'Tuesday', 4 => 'Wednesday', 5 => 'Thursday', 6 => 'Friday', 7 => 'Saturday'];

        $peakLabel = 'Not enough data yet';
        if ($peakDay && isset($peakDay->dow)) {
            $dow = (int) $peakDay->dow;
            $peakLabel = ($dayNames[$dow] ?? 'weekdays') . ' (most applications)';
        }

        $withSalary = JobAdvertisement::query()
            ->where('company_id', $companyId)
            ->where(function ($q) {
                $q->whereNotNull('salary_min')->orWhereNotNull('salary_max');
            })
            ->withCount(['applications as apps_c' => function ($q) use ($start) {
                if ($start) {
                    $q->where('created_at', '>=', $start);
                }
            }])
            ->get();
        $withoutSalary = JobAdvertisement::query()
            ->where('company_id', $companyId)
            ->whereNull('salary_min')
            ->whereNull('salary_max')
            ->withCount(['applications as apps_c' => function ($q) use ($start) {
                if ($start) {
                    $q->where('created_at', '>=', $start);
                }
            }])
            ->get();

        $avgWith = $withSalary->avg('apps_c') ?? 0;
        $avgWithout = $withoutSalary->avg('apps_c') ?? 0;
        $salaryLift = $avgWithout > 0
            ? (int) round((($avgWith - $avgWithout) / $avgWithout) * 100)
            : ($avgWith > 0 ? 100 : 0);

        $insights = [
            [
                'title' => 'Peak Performance',
                'body' => $peakLabel,
                'tone' => 'blue',
            ],
            [
                'title' => 'Salary Visibility',
                'body' => $salaryLift > 0
                    ? "Jobs with salary ranges get about {$salaryLift}% more applications on average"
                    : ($avgWith > 0 || $avgWithout > 0
                        ? 'Salary-listed jobs are performing similarly to hidden-salary posts'
                        : 'Add salary ranges to compare engagement'),
                'tone' => 'emerald',
            ],
        ];

        return compact('metrics', 'topJobs', 'trafficSources', 'funnel', 'insights');
    }

    private function rangeBounds(string $range): array
    {
        return match ($range) {
            '7' => [now()->subDays(7), now()->subDays(14), now()->subDays(7)],
            '90' => [now()->subDays(90), now()->subDays(180), now()->subDays(90)],
            'all' => [null, null, null],
            default => [now()->subDays(30), now()->subDays(60), now()->subDays(30)],
        };
    }

    private function percentChange(int $current, int $previous): array
    {
        if ($previous <= 0) {
            if ($current <= 0) {
                return ['label' => 'No change', 'trend' => 'up', 'show' => false];
            }
            return ['label' => 'New', 'trend' => 'up', 'show' => true];
        }
        $pct = round((($current - $previous) / $previous) * 100, 1);
        return [
            'label' => ($pct >= 0 ? '+' : '') . $pct . '%',
            'trend' => $pct >= 0 ? 'up' : 'down',
            'show' => true,
        ];
    }

    private function formatCompact(int|float $n): string
    {
        $n = (float) $n;
        if ($n >= 1000000) {
            return round($n / 1000000, 1) . 'M';
        }
        if ($n >= 1000) {
            return round($n / 1000, 1) . 'K';
        }
        return number_format((int) $n);
    }
}
