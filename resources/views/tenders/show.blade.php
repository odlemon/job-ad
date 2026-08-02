@extends('layouts.app')

@section('title', $tender->title)

@section('content')
@php
    $categoryName = $tender->category?->name ?: ($tender->sector ?? 'General');
    $requirements = is_array($tender->requirements) ? $tender->requirements : [];
    $requiredDocs = is_array($tender->required_documents) ? $tender->required_documents : [];
    $eligibility = is_array($tender->eligibility_criteria) ? $tender->eligibility_criteria : [];
    $overviewReqs = count($requirements) > 0 ? $requirements : array_slice($eligibility, 0, 3);
    $attachments = is_array($tender->attachments) ? $tender->attachments : [];
    $uploads = app(\App\Services\RemoteUploadService::class);

    $formatMoney = function ($value) {
        $n = (float) $value;
        if ($n >= 1000000) {
            return '$' . rtrim(rtrim(number_format($n / 1000000, 1), '0'), '.') . 'M';
        }
        if ($n >= 1000) {
            return '$' . rtrim(rtrim(number_format($n / 1000, 1), '0'), '.') . 'K';
        }
        return '$' . number_format($n);
    };

    if ($tender->budget_min && $tender->budget_max) {
        $budgetRange = $formatMoney($tender->budget_min) . ' - ' . $formatMoney($tender->budget_max);
    } elseif ($tender->amount) {
        $budgetRange = $formatMoney($tender->amount);
    } else {
        $budgetRange = 'N/A';
    }

    $deadline = $tender->submission_deadline ?? $tender->end_date;
    $daysLeft = $deadline ? (int) now()->startOfDay()->diffInDays($deadline, false) : null;
    $published = $tender->published_date ?? $tender->created_at;
    $daysSincePublish = $published ? (int) $published->diffInDays(now()) : 99;

    if ($daysLeft !== null && $daysLeft >= 0 && $daysLeft <= 7) {
        $statusLabel = 'Closing Soon';
        $statusClass = 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400';
    } elseif ($daysSincePublish <= 7) {
        $statusLabel = 'New';
        $statusClass = 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400';
    } else {
        $statusLabel = 'Open';
        $statusClass = 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400';
    }

    $attachmentUrls = [];
    foreach ($attachments as $att) {
        $url = is_array($att) ? ($att['url'] ?? null) : null;
        $url = $uploads->resolveUrl($url);
        if ($url) {
            $attachmentUrls[] = $url;
        }
    }

    $summary = $tender->summary ?: \Illuminate\Support\Str::limit(strip_tags((string) $tender->description), 280);
@endphp

<div id="tender-detail-page"
     class="min-h-screen bg-gray-50 dark:bg-gray-900 transition-colors duration-200"
     data-attachment-urls='@json($attachmentUrls)'
     data-tender-ref="{{ $tender->reference_number ?? '' }}"
     data-tender-title="{{ $tender->title }}">
    <div class="max-w-7xl mx-auto px-4 py-8">
        <a href="{{ route('tenders.index') }}" wire:navigate
           class="inline-flex items-center gap-2 text-blue-600 dark:text-cyan-400 hover:text-blue-700 dark:hover:text-cyan-300 mb-6 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Back to all tenders
        </a>

        {{-- Header --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md border border-gray-200 dark:border-gray-700 p-6 mb-6">
            <div class="flex flex-wrap gap-2 mb-4">
                <span class="px-3 py-1 bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400 text-sm font-medium rounded-full">{{ $categoryName }}</span>
                <span class="px-3 py-1 text-sm font-medium rounded-full {{ $statusClass }}">{{ $statusLabel }}</span>
                @if($tender->tender_type)
                    <span class="px-3 py-1 bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400 text-sm font-medium rounded-full">{{ $tender->tender_type }}</span>
                @endif
                @if($daysLeft !== null && $daysLeft >= 0 && $daysLeft <= 7)
                    <span class="px-3 py-1 bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400 text-sm font-medium rounded-full flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Closing in {{ $daysLeft }} {{ $daysLeft === 1 ? 'day' : 'days' }}
                    </span>
                @endif
            </div>
            <h1 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white mb-3">{{ $tender->title }}</h1>
            <div class="flex items-center gap-2 text-gray-600 dark:text-gray-400">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                <span class="font-medium">Reference:</span>
                <span>{{ $tender->reference_number ?? 'N/A' }}</span>
            </div>
        </div>

        <div class="grid lg:grid-cols-3 gap-6">
            {{-- Main tabs --}}
            <div class="lg:col-span-2">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div class="border-b border-gray-200 dark:border-gray-700">
                        <div class="flex overflow-x-auto" role="tablist">
                            <button type="button" data-tab="overview" class="tender-tab px-6 py-4 font-medium transition-colors whitespace-nowrap">Overview</button>
                            <button type="button" data-tab="submission" class="tender-tab px-6 py-4 font-medium transition-colors whitespace-nowrap">Submission Details</button>
                            <button type="button" data-tab="attachments" class="tender-tab px-6 py-4 font-medium transition-colors whitespace-nowrap">Attachments</button>
                            <button type="button" data-tab="dates" class="tender-tab px-6 py-4 font-medium transition-colors whitespace-nowrap">Important Dates</button>
                        </div>
                    </div>

                    <div class="p-6">
                        {{-- Overview --}}
                        <div id="tender-panel-overview" class="tender-panel space-y-6">
                            <div>
                                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">Tender Summary</h3>
                                <p class="text-gray-700 dark:text-gray-300 leading-relaxed">{{ $summary }}</p>
                            </div>
                            @if($tender->scope_of_work)
                            <div>
                                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">Scope of Work</h3>
                                <p class="text-gray-700 dark:text-gray-300 leading-relaxed">{{ $tender->scope_of_work }}</p>
                            </div>
                            @endif
                            @if(count($overviewReqs) > 0)
                            <div>
                                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">Requirements</h3>
                                <ul class="space-y-2">
                                    @foreach($overviewReqs as $req)
                                    <li class="flex items-start gap-2 text-gray-700 dark:text-gray-300">
                                        <svg class="w-5 h-5 text-green-600 dark:text-green-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        <span>{{ $req }}</span>
                                    </li>
                                    @endforeach
                                </ul>
                            </div>
                            @endif
                        </div>

                        {{-- Submission --}}
                        <div id="tender-panel-submission" class="tender-panel space-y-6 hidden">
                            @if($tender->submission_method)
                            <div>
                                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">Submission Method</h3>
                                <p class="text-gray-700 dark:text-gray-300 leading-relaxed">{{ $tender->submission_method }}</p>
                            </div>
                            @endif
                            @if(count($requiredDocs) > 0)
                            <div>
                                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">Required Documents</h3>
                                <ul class="space-y-2">
                                    @foreach($requiredDocs as $doc)
                                    <li class="flex items-start gap-2 text-gray-700 dark:text-gray-300">
                                        <svg class="w-5 h-5 text-blue-600 dark:text-cyan-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                        <span>{{ $doc }}</span>
                                    </li>
                                    @endforeach
                                </ul>
                            </div>
                            @endif
                            @if(count($eligibility) > 0)
                            <div>
                                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">Eligibility Criteria</h3>
                                <ul class="space-y-2">
                                    @foreach($eligibility as $el)
                                    <li class="flex items-start gap-2 text-gray-700 dark:text-gray-300">
                                        <svg class="w-5 h-5 text-green-600 dark:text-green-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        <span>{{ $el }}</span>
                                    </li>
                                    @endforeach
                                </ul>
                            </div>
                            @endif
                            @if(!$tender->submission_method && count($requiredDocs) === 0 && count($eligibility) === 0)
                            <p class="text-gray-500 dark:text-gray-400">No submission details published for this tender yet.</p>
                            @endif
                        </div>

                        {{-- Attachments --}}
                        <div id="tender-panel-attachments" class="tender-panel space-y-4 hidden">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Downloadable Documents</h3>
                                @if(count($attachmentUrls) > 0)
                                <button type="button" data-download-all
                                        class="px-4 py-2 bg-gradient-to-r from-blue-600 to-teal-500 hover:from-blue-700 hover:to-teal-600 text-white rounded-lg font-medium transition-all duration-200 hover:shadow-lg flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                    Download All
                                </button>
                                @endif
                            </div>
                            @if(count($attachments) > 0)
                            <div class="space-y-3">
                                @foreach($attachments as $att)
                                @php
                                    $attName = is_array($att) ? ($att['name'] ?? 'Document') : (string) $att;
                                    $attType = is_array($att) ? ($att['type'] ?? 'File') : 'File';
                                    $attSize = is_array($att) ? ($att['size'] ?? '') : '';
                                    $attUrl = $uploads->resolveUrl(is_array($att) ? ($att['url'] ?? null) : null);
                                    $isPdf = strtoupper((string) $attType) === 'PDF' || str_ends_with(strtolower($attName), '.pdf');
                                @endphp
                                <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg border border-gray-200 dark:border-gray-600 hover:border-blue-500 dark:hover:border-cyan-400 transition-colors">
                                    <div class="flex items-center gap-3 min-w-0">
                                        <div class="w-10 h-10 {{ $isPdf ? 'bg-red-100 dark:bg-red-900/30' : 'bg-green-100 dark:bg-green-900/30' }} rounded-lg flex items-center justify-center flex-shrink-0">
                                            <svg class="w-5 h-5 {{ $isPdf ? 'text-red-600 dark:text-red-400' : 'text-green-600 dark:text-green-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                        </div>
                                        <div class="min-w-0">
                                            <p class="font-medium text-gray-900 dark:text-white truncate">{{ $attName }}</p>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $attType }}@if($attSize) • {{ $attSize }}@endif</p>
                                        </div>
                                    </div>
                                    @if($attUrl)
                                    <a href="{{ $attUrl }}" target="_blank" rel="noopener" download
                                       class="p-2 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition-colors flex-shrink-0">
                                        <svg class="w-5 h-5 text-blue-600 dark:text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                    </a>
                                    @else
                                    <span class="p-2 text-gray-300 dark:text-gray-600 cursor-not-allowed flex-shrink-0" title="File not available">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                    </span>
                                    @endif
                                </div>
                                @endforeach
                            </div>
                            @else
                            <p class="text-gray-500 dark:text-gray-400">No attachments available for this tender.</p>
                            @endif
                        </div>

                        {{-- Dates --}}
                        <div id="tender-panel-dates" class="tender-panel space-y-4 hidden">
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Key Dates & Timeline</h3>
                            <div class="space-y-4">
                                @if($tender->published_date || $tender->created_at)
                                <div class="flex gap-4 p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg border border-gray-200 dark:border-gray-600">
                                    <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <svg class="w-6 h-6 text-blue-600 dark:text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    </div>
                                    <div class="flex-1">
                                        <p class="font-medium text-gray-900 dark:text-white">Published Date</p>
                                        <p class="text-gray-600 dark:text-gray-400">{{ ($tender->published_date ?? $tender->created_at)->format('l, F j, Y') }}</p>
                                    </div>
                                </div>
                                @endif
                                @if($tender->clarification_deadline)
                                <div class="flex gap-4 p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg border border-gray-200 dark:border-gray-600">
                                    <div class="w-12 h-12 bg-yellow-100 dark:bg-yellow-900/30 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    </div>
                                    <div class="flex-1">
                                        <p class="font-medium text-gray-900 dark:text-white">Clarification Deadline</p>
                                        <p class="text-gray-600 dark:text-gray-400">{{ $tender->clarification_deadline->format('l, F j, Y') }}</p>
                                    </div>
                                </div>
                                @endif
                                @if($deadline)
                                <div class="flex gap-4 p-4 bg-red-50 dark:bg-red-900/20 rounded-lg border-2 border-red-200 dark:border-red-800">
                                    <div class="w-12 h-12 bg-red-100 dark:bg-red-900/50 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    </div>
                                    <div class="flex-1">
                                        <p class="font-medium text-gray-900 dark:text-white">Submission Deadline</p>
                                        <p class="text-red-600 dark:text-red-400 font-semibold">{{ $deadline->format('l, F j, Y') }}</p>
                                        @if($daysLeft !== null && $daysLeft >= 0)
                                            <p class="text-sm text-red-600 dark:text-red-400 mt-1">{{ $daysLeft }} {{ $daysLeft === 1 ? 'day' : 'days' }} remaining</p>
                                        @endif
                                    </div>
                                </div>
                                @endif
                                @if(!$tender->published_date && !$tender->created_at && !$tender->clarification_deadline && !$deadline)
                                <p class="text-gray-500 dark:text-gray-400">No key dates published for this tender yet.</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Sidebar --}}
            <div class="lg:col-span-1">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md border border-gray-200 dark:border-gray-700 p-6 sticky top-24">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Tender Information</h3>
                    <div class="space-y-4">
                        <div class="pb-4 border-b border-gray-200 dark:border-gray-700">
                            <div class="flex items-start gap-3">
                                <svg class="w-5 h-5 text-green-600 dark:text-green-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Budget Range</p>
                                    <p class="font-semibold text-gray-900 dark:text-white">{{ $budgetRange }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="pb-4 border-b border-gray-200 dark:border-gray-700">
                            <div class="flex items-start gap-3">
                                <svg class="w-5 h-5 text-blue-600 dark:text-cyan-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                <div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Sector</p>
                                    <p class="font-semibold text-gray-900 dark:text-white">{{ $tender->sector ?? 'N/A' }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="pb-4 border-b border-gray-200 dark:border-gray-700">
                            <div class="flex items-start gap-3">
                                <svg class="w-5 h-5 text-purple-600 dark:text-purple-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                <div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Procuring Entity</p>
                                    <p class="font-semibold text-gray-900 dark:text-white">{{ $tender->procuring_entity ?? $tender->entity_name ?? 'N/A' }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="pb-4 border-b border-gray-200 dark:border-gray-700">
                            <div class="flex items-start gap-3">
                                <svg class="w-5 h-5 text-teal-600 dark:text-teal-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Country/Region</p>
                                    <p class="font-semibold text-gray-900 dark:text-white">{{ $tender->country_region ?? 'N/A' }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="pb-4 border-b border-gray-200 dark:border-gray-700">
                            <div class="flex items-start gap-3">
                                <svg class="w-5 h-5 text-red-600 dark:text-red-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                <div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Location</p>
                                    <p class="font-semibold text-gray-900 dark:text-white">{{ $tender->location ?? 'N/A' }}</p>
                                </div>
                            </div>
                        </div>

                        <button type="button" data-download-all
                                class="w-full bg-gradient-to-r from-blue-600 to-teal-500 hover:from-blue-700 hover:to-teal-600 text-white px-6 py-3 rounded-lg font-semibold transition-all duration-200 hover:shadow-lg flex items-center justify-center gap-2 mt-6">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                            Download All Documents
                        </button>
                        <button type="button" data-clarify-open
                                class="w-full border-2 border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:border-blue-600 dark:hover:border-cyan-400 hover:text-blue-600 dark:hover:text-cyan-400 px-6 py-3 rounded-lg font-semibold transition-all duration-200 flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Request Clarification
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Clarification modal --}}
    <div id="tender-clarify-modal" class="fixed inset-0 z-50 hidden items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/50" data-clarify-close></div>
        <div class="relative bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-gray-200 dark:border-gray-700 w-full max-w-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-1">Request Clarification</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                Regarding <span class="font-medium text-gray-800 dark:text-gray-200">{{ $tender->reference_number ?? $tender->title }}</span>
                @if($tender->clarification_deadline)
                    · Deadline {{ $tender->clarification_deadline->format('M j, Y') }}
                @endif
            </p>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1" for="clarify-message">Your question</label>
            <textarea id="clarify-message" rows="5" class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-white px-3 py-2 mb-4 focus:ring-2 focus:ring-blue-500 outline-none" placeholder="Type your clarification request…"></textarea>
            <div class="flex justify-end gap-3">
                <button type="button" data-clarify-close class="px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 font-medium hover:bg-gray-50 dark:hover:bg-gray-700">Cancel</button>
                <button type="button" data-clarify-submit class="px-4 py-2 rounded-lg bg-gradient-to-r from-blue-600 to-teal-500 text-white font-medium hover:from-blue-700 hover:to-teal-600">Open email</button>
            </div>
        </div>
    </div>
</div>
@endsection
