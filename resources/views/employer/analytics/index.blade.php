@extends('layouts.employer')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    @include('partials.employer-navbar')

    <div class="flex">
        @include('partials.employer-sidebar')

        <main class="flex-1 min-w-0 p-8 ml-64 w-full">
            <div class="w-full max-w-none space-y-6">
                <div class="flex items-center justify-between gap-4 flex-wrap">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Analytics & Insights</h1>
                        <p class="text-gray-600 dark:text-gray-400 mt-1">Track performance across all your job postings</p>
                    </div>
                    <div class="flex gap-2 flex-wrap">
                        <select id="analytics-range" onchange="window.location.href='{{ route('employer.analytics.index') }}?range='+this.value"
                                class="px-4 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded text-sm text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="7" {{ $range === '7' ? 'selected' : '' }}>Last 7 days</option>
                            <option value="30" {{ $range === '30' ? 'selected' : '' }}>Last 30 days</option>
                            <option value="90" {{ $range === '90' ? 'selected' : '' }}>Last 90 days</option>
                            <option value="all" {{ $range === 'all' ? 'selected' : '' }}>All time</option>
                        </select>
                        <a href="{{ route('employer.analytics.export', ['range' => $range]) }}"
                           class="flex items-center gap-2 px-4 py-2 border border-gray-200 dark:border-gray-600 rounded font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors text-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            Export Report
                        </a>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($metrics as $metric)
                        <div class="bg-white dark:bg-gray-800 p-6 rounded border border-gray-200 dark:border-gray-700 shadow-sm">
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">{{ $metric['label'] }}</p>
                            <div class="flex items-end justify-between gap-2">
                                <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ $metric['value'] }}</p>
                                <div class="flex items-center gap-1 text-sm font-medium {{ ($metric['show_trend'] ?? false) ? ($metric['trend'] === 'up' ? 'text-emerald-600' : 'text-red-600') : 'text-gray-500 dark:text-gray-400' }}">
                                    @if($metric['show_trend'] ?? false)
                                        @if($metric['trend'] === 'up')
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>
                                        @else
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg>
                                        @endif
                                    @endif
                                    <span>{{ $metric['change'] }}</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div class="bg-white dark:bg-gray-800 rounded border border-gray-200 dark:border-gray-700 shadow-sm">
                        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                            <h2 class="text-lg font-bold text-gray-900 dark:text-white">Top Performing Jobs</h2>
                        </div>
                        <div class="p-6 space-y-4">
                            @forelse($topJobs as $job)
                                <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded">
                                    <div class="flex items-center justify-between mb-3 gap-2">
                                        <h3 class="font-semibold text-gray-900 dark:text-white">{{ $job['title'] }}</h3>
                                        <span class="flex items-center gap-1 text-sm font-medium {{ $job['trend'] === 'up' ? 'text-emerald-600' : 'text-red-600' }}">
                                            @if($job['trend'] === 'up')
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>
                                            @else
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg>
                                            @endif
                                            {{ $job['conversion_rate'] }}%
                                        </span>
                                    </div>
                                    <div class="grid grid-cols-3 gap-4 text-sm">
                                        <div>
                                            <p class="text-gray-500 dark:text-gray-400 mb-1">Views</p>
                                            <p class="font-semibold text-gray-900 dark:text-white">{{ number_format($job['views']) }}</p>
                                        </div>
                                        <div>
                                            <p class="text-gray-500 dark:text-gray-400 mb-1">Applications</p>
                                            <p class="font-semibold text-gray-900 dark:text-white">{{ number_format($job['applications']) }}</p>
                                        </div>
                                        <div>
                                            <p class="text-gray-500 dark:text-gray-400 mb-1">Conv. Rate</p>
                                            <p class="font-semibold text-emerald-600">{{ $job['conversion_rate'] }}%</p>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <p class="text-sm text-gray-500 dark:text-gray-400 text-center py-6">No job postings yet.</p>
                            @endforelse
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 rounded border border-gray-200 dark:border-gray-700 shadow-sm">
                        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                            <h2 class="text-lg font-bold text-gray-900 dark:text-white">Traffic Sources</h2>
                        </div>
                        <div class="p-6">
                            <div class="space-y-6">
                                @foreach($trafficSources as $src)
                                    <div>
                                        <div class="flex items-center justify-between mb-2">
                                            <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $src['source'] }}</span>
                                            <span class="text-sm text-gray-600 dark:text-gray-400">{{ number_format($src['count']) }} visits</span>
                                        </div>
                                        <div class="relative">
                                            <div class="h-2 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                                                <div class="h-full rounded-full" style="width: {{ max($src['percentage'], 0) }}%; background: linear-gradient(to right, #2563eb, #06b6d4);"></div>
                                            </div>
                                            <span class="absolute -top-1 text-xs font-bold text-blue-600" style="left: {{ min(max($src['percentage'], 0), 92) }}%;">{{ $src['percentage'] }}%</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                                <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-4">Performance Insights</h3>
                                <div class="space-y-3">
                                    @foreach($insights as $insight)
                                        @php
                                            $bg = $insight['tone'] === 'emerald' ? 'bg-emerald-50 dark:bg-emerald-900/20' : 'bg-blue-50 dark:bg-blue-900/20';
                                            $iconBg = $insight['tone'] === 'emerald' ? '#059669' : '#2563eb';
                                        @endphp
                                        <div class="flex items-start gap-3 p-3 {{ $bg }} rounded">
                                            <div class="w-8 h-8 rounded flex items-center justify-center flex-shrink-0" style="background-color: {{ $iconBg }};">
                                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                                            </div>
                                            <div>
                                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $insight['title'] }}</p>
                                                <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">{{ $insight['body'] }}</p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded border border-gray-200 dark:border-gray-700 shadow-sm">
                    <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                        <h2 class="text-lg font-bold text-gray-900 dark:text-white">Application Funnel</h2>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                            @foreach($funnel as $step)
                                <div class="text-center">
                                    <div class="w-full {{ $step['height'] }} rounded flex items-end justify-center pb-4"
                                         style="background: linear-gradient(to top, {{ $step['from'] }}, {{ $step['to'] }});">
                                        <span class="text-2xl font-bold text-white">{{ $step['display'] }}</span>
                                    </div>
                                    <p class="mt-3 text-sm font-medium text-gray-900 dark:text-white">{{ $step['label'] }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>
@endsection
