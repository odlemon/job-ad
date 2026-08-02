@extends('layouts.app')

@section('title', 'Companies')

@section('content')
@php
    $selectedIndustries = collect(explode(',', (string) ($filters['industry'] ?? '')))
        ->map(fn ($v) => trim($v))
        ->filter()
        ->values()
        ->all();
    $jobsValue = $filters['jobs'] ?? 'all';
    if ($jobsValue === 'available') {
        $jobsValue = '1-10';
    }
    if ($jobsValue === '' || $jobsValue === null) {
        $jobsValue = 'all';
    }
    $hasActiveFilters = ($filters['search'] ?? '') !== ''
        || count($selectedIndustries) > 0
        || ($jobsValue !== 'all');
@endphp
<div id="companies-page" class="min-h-screen bg-gray-50 dark:bg-gray-900 transition-colors duration-200"
     data-api-url="{{ url('/api/public/companies') }}"
     data-initial-search="{{ e($filters['search'] ?? '') }}"
     data-initial-industry="{{ e(implode(',', $selectedIndustries)) }}"
     data-initial-jobs="{{ e($jobsValue) }}">

    {{-- Bolt hero (inline colors so the banner never washes out if Tailwind purges gradient stops) --}}
    <div class="companies-hero relative overflow-hidden"
         style="background: linear-gradient(to bottom right, #1e3a8a, #1e40af, #1e3a8a);">
        <div class="absolute inset-0 overflow-hidden pointer-events-none" aria-hidden="true">
            <div class="absolute top-16 right-6 sm:right-12 md:right-24 w-20 h-20 md:w-24 md:h-24 rounded-full"
                 style="background:#ec4899; opacity:0.9;"></div>
            <div class="absolute -top-8 -right-8 w-64 h-64 md:w-80 md:h-80 rounded-full"
                 style="background:rgba(29,78,216,0.35); filter:blur(48px);"></div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14 md:py-16 relative z-10">
            <div class="grid md:grid-cols-2 gap-10 lg:gap-12 items-center">
                <div class="min-w-0">
                    <h1 class="text-3xl sm:text-4xl font-bold mb-3" style="color:#ffffff;">Find the right company for you</h1>
                    <p class="text-base sm:text-lg mb-8" style="color:#dbeafe;">Everything you need to know about a company, all in one place</p>

                    <div class="flex flex-col sm:flex-row sm:items-stretch bg-white rounded-lg shadow-md overflow-visible relative">
                        <div class="relative flex-1 min-w-0">
                            <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            <input type="text" id="company-search" value="{{ $filters['search'] ?? '' }}"
                                   placeholder="Search by company name"
                                   class="w-full h-11 pl-10 pr-3 text-sm text-gray-900 placeholder-gray-400 bg-transparent border-0 outline-none focus:ring-0">
                        </div>

                        <div class="hidden sm:block w-px self-stretch bg-gray-200 my-2.5" aria-hidden="true"></div>

                        <div class="relative filter-dropdown border-t sm:border-t-0 border-gray-100" id="industry-dropdown">
                            <button type="button" id="industry-filter-btn"
                                    class="inline-flex items-center gap-1.5 h-11 px-4 text-sm font-medium text-gray-800 hover:bg-gray-50 transition-colors whitespace-nowrap w-full sm:w-auto justify-between sm:justify-start">
                                <span class="inline-flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                                    Industry
                                    <span id="industry-count-badge" class="{{ count($selectedIndustries) ? '' : 'hidden' }} inline-flex items-center justify-center w-4 h-4 text-xs font-bold text-white bg-blue-600 rounded-full">{{ count($selectedIndustries) ?: '' }}</span>
                                </span>
                                <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </button>
                            <div id="industry-menu" class="hidden absolute left-0 sm:left-auto sm:right-0 mt-1 w-64 bg-white border border-gray-200 rounded-lg shadow-xl z-50 max-h-96 overflow-y-auto filter-dropdown">
                                <div class="p-3">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-sm font-semibold text-gray-900">Select Industries</span>
                                        <button type="button" id="industry-clear" class="text-xs text-blue-600 hover:text-blue-700 font-medium {{ count($selectedIndustries) ? '' : 'hidden' }}">Clear all</button>
                                    </div>
                                    <div class="space-y-1" id="industry-options">
                                        @foreach($industries as $ind)
                                            <label class="flex items-center gap-2 p-2 hover:bg-gray-50 rounded cursor-pointer">
                                                <input type="checkbox" value="{{ $ind }}" class="industry-checkbox w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                                                       {{ in_array($ind, $selectedIndustries, true) ? 'checked' : '' }}>
                                                <span class="text-sm text-gray-700">{{ $ind }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="hidden sm:block w-px self-stretch bg-gray-200 my-2.5" aria-hidden="true"></div>

                        <div class="relative filter-dropdown border-t sm:border-t-0 border-gray-100" id="jobs-dropdown">
                            <button type="button" id="jobs-filter-btn"
                                    class="inline-flex items-center gap-1.5 h-11 px-4 text-sm font-medium text-gray-800 hover:bg-gray-50 transition-colors whitespace-nowrap w-full sm:w-auto justify-between sm:justify-start rounded-b-lg sm:rounded-none sm:rounded-r-lg">
                                <span class="inline-flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                                    Jobs Available
                                </span>
                                <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </button>
                            <div id="jobs-menu" class="hidden absolute left-0 sm:left-auto sm:right-0 mt-1 w-48 bg-white border border-gray-200 rounded-lg shadow-xl z-50 filter-dropdown">
                                <div class="p-2" id="jobs-options">
                                    @foreach([
                                        'all' => 'All Jobs',
                                        '1-10' => '1-10 jobs',
                                        '11-20' => '11-20 jobs',
                                        '21-30' => '21-30 jobs',
                                        '30+' => '30+ jobs',
                                    ] as $val => $label)
                                        <button type="button" data-jobs-value="{{ $val }}"
                                                class="jobs-option w-full text-left px-3 py-2 rounded text-sm hover:bg-gray-50 transition-colors {{ $jobsValue === $val ? 'bg-blue-50 text-blue-600 font-medium' : 'text-gray-700' }}">
                                            {{ $label }}
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <button type="button" id="clear-filters-btn"
                                class="{{ $hasActiveFilters ? '' : 'hidden' }} absolute -bottom-10 left-0 text-sm underline underline-offset-2"
                                style="color:#dbeafe;">
                            Clear all filters
                        </button>
                    </div>
                </div>

                <div class="hidden md:flex justify-center lg:justify-end items-center relative">
                    <div class="relative z-10 shrink-0 rounded-full overflow-hidden shadow-2xl"
                         style="width:16rem;height:16rem;border:8px solid rgba(255,255,255,0.12);">
                        <img src="https://images.pexels.com/photos/3756681/pexels-photo-3756681.jpeg?auto=compress&cs=tinysrgb&w=600"
                             alt="Professional"
                             width="256"
                             height="256"
                             class="block"
                             style="width:100%;height:100%;object-fit:cover;">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="mb-6">
            <p id="company-count" class="text-gray-600 dark:text-gray-400">
                Showing <span class="font-semibold text-gray-900 dark:text-white">{{ method_exists($companies, 'total') ? $companies->total() : $companies->count() }}</span> companies
            </p>
        </div>

        <div id="companies-grid" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-5">
            @foreach($companies as $company)
                @php
                    $logoUrl = null;
                    if ($company->logo) {
                        $logoUrl = str_starts_with($company->logo, 'http')
                            ? $company->logo
                            : rtrim($mediaBaseUrl, '/') . '/' . ltrim($company->logo, '/');
                    }
                    $jobsCount = (int) ($company->jobs_count ?? $company->job_advertisements_count ?? 0);
                @endphp
                <a href="{{ route('companies.show', $company->slug ?: $company->id) }}" wire:navigate
                   class="company-card bg-white dark:bg-gray-800 rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 p-6 flex flex-col items-center justify-center cursor-pointer group border border-gray-100 dark:border-gray-700 hover:border-blue-300 dark:hover:border-blue-600 hover:-translate-y-1">
                    <div class="w-20 h-20 rounded-xl flex items-center justify-center mb-4 group-hover:scale-105 transition-transform duration-300 shadow-sm overflow-hidden"
                         style="background: linear-gradient(to bottom right, #eff6ff, #f1f5f9);">
                        @if($logoUrl)
                            <img src="{{ $logoUrl }}" alt="{{ $company->name }}" class="w-full h-full object-cover" loading="lazy">
                        @else
                            <span class="text-4xl font-bold text-blue-600 dark:text-blue-400">{{ strtoupper(substr($company->name, 0, 1)) }}</span>
                        @endif
                    </div>
                    <h3 class="text-base font-bold text-gray-900 dark:text-white text-center mb-2 line-clamp-2 min-h-[3rem] flex items-center justify-center">{{ $company->name }}</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 text-center font-medium">{{ $jobsCount }} {{ \Illuminate\Support\Str::plural('job', $jobsCount) }}</p>
                </a>
            @endforeach
        </div>

        <div id="companies-empty" class="text-center py-20 {{ $companies->count() ? 'hidden' : '' }}">
            <svg class="w-16 h-16 text-gray-300 dark:text-gray-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
            <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">No companies found</h3>
            <p class="text-gray-600 dark:text-gray-400">Try adjusting your search terms</p>
        </div>

        <div id="companies-pagination" class="mt-10 flex justify-center items-center gap-2 flex-wrap"
             data-page="{{ method_exists($companies, 'currentPage') ? $companies->currentPage() : 1 }}"
             data-last-page="{{ method_exists($companies, 'lastPage') ? $companies->lastPage() : 1 }}">
        </div>
    </main>
</div>
@endsection
