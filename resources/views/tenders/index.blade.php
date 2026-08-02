@extends('layouts.app')

@section('title', 'Active Tenders')

@section('content')
<div id="tenders-page" class="min-h-screen bg-gray-50 dark:bg-gray-900 transition-colors duration-200">
    {{-- Hero --}}
    <div class="bg-gradient-to-br from-blue-600 via-teal-500 to-cyan-500 dark:from-blue-900 dark:via-teal-800 dark:to-cyan-800 text-white py-16 px-4">
        <div class="max-w-7xl mx-auto">
            <h1 class="text-4xl md:text-5xl font-bold text-center mb-4">Active Tenders</h1>
            <p class="text-xl text-center text-white/90 mb-8 max-w-3xl mx-auto">
                Browse current procurement opportunities from government and private sector buyers.
            </p>
            <div class="max-w-4xl mx-auto bg-white dark:bg-gray-800 rounded-lg shadow-lg p-2">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-gray-400 ml-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input type="text" id="tender-search"
                           placeholder="Search tenders… (title, reference number, category)"
                           class="flex-1 px-4 py-3 text-gray-900 dark:text-white bg-transparent outline-none">
                    <button type="button" id="tender-search-btn"
                            class="bg-gradient-to-r from-blue-600 to-teal-500 hover:from-blue-700 hover:to-teal-600 text-white px-8 py-3 rounded-md font-medium transition-all duration-200 hover:shadow-lg">
                        Search
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Sticky filters --}}
    <div class="sticky top-16 z-40 bg-white dark:bg-gray-800 shadow-md border-b border-gray-200 dark:border-gray-700 transition-colors duration-200">
        <div class="max-w-7xl mx-auto px-4 py-4">
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-7 gap-3">
                <div class="relative">
                    <select id="filter-category" class="w-full px-4 py-2 pr-8 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md text-sm text-gray-900 dark:text-white appearance-none cursor-pointer hover:border-blue-500 dark:hover:border-cyan-400 transition-colors">
                        <option value="">All Categories</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat }}">{{ $cat }}</option>
                        @endforeach
                    </select>
                    <svg class="absolute right-2 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-500 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </div>
                <div class="relative">
                    <select id="filter-sector" class="w-full px-4 py-2 pr-8 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md text-sm text-gray-900 dark:text-white appearance-none cursor-pointer hover:border-blue-500 dark:hover:border-cyan-400 transition-colors">
                        <option value="">All Sectors</option>
                        @foreach($sectors as $s)
                            <option value="{{ $s }}">{{ $s }}</option>
                        @endforeach
                    </select>
                    <svg class="absolute right-2 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-500 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </div>
                <div class="relative">
                    <select id="filter-type" class="w-full px-4 py-2 pr-8 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md text-sm text-gray-900 dark:text-white appearance-none cursor-pointer hover:border-blue-500 dark:hover:border-cyan-400 transition-colors">
                        <option value="">All Types</option>
                        @foreach($types as $tp)
                            <option value="{{ $tp }}">{{ $tp }}</option>
                        @endforeach
                    </select>
                    <svg class="absolute right-2 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-500 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </div>
                <div class="relative">
                    <select id="filter-budget" class="w-full px-4 py-2 pr-8 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md text-sm text-gray-900 dark:text-white appearance-none cursor-pointer hover:border-blue-500 dark:hover:border-cyan-400 transition-colors">
                        <option value="">All Budgets</option>
                        <option value="under500k">Under $500K</option>
                        <option value="500k-2m">$500K - $2M</option>
                        <option value="2m-5m">$2M - $5M</option>
                        <option value="above5m">Above $5M</option>
                    </select>
                    <svg class="absolute right-2 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-500 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </div>
                <div class="relative">
                    <select id="filter-deadline" class="w-full px-4 py-2 pr-8 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md text-sm text-gray-900 dark:text-white appearance-none cursor-pointer hover:border-blue-500 dark:hover:border-cyan-400 transition-colors">
                        <option value="">All Deadlines</option>
                        <option value="7days">Next 7 days</option>
                        <option value="30days">Next 30 days</option>
                        <option value="3months">Next 3 months</option>
                    </select>
                    <svg class="absolute right-2 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-500 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </div>
                <div class="relative">
                    <select id="filter-location" class="w-full px-4 py-2 pr-8 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md text-sm text-gray-900 dark:text-white appearance-none cursor-pointer hover:border-blue-500 dark:hover:border-cyan-400 transition-colors">
                        <option value="">All Locations</option>
                        @foreach($locations as $loc)
                            <option value="{{ $loc }}">{{ $loc }}</option>
                        @endforeach
                    </select>
                    <svg class="absolute right-2 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-500 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </div>
                <div class="relative">
                    <select id="filter-sort" class="w-full px-4 py-2 pr-8 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md text-sm text-gray-900 dark:text-white appearance-none cursor-pointer hover:border-blue-500 dark:hover:border-cyan-400 transition-colors">
                        <option value="newest">Newest</option>
                        <option value="closing">Closing Soon</option>
                        <option value="highest">Highest Value</option>
                        <option value="az">A–Z</option>
                    </select>
                    <svg class="absolute right-2 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-500 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </div>
            </div>
        </div>
    </div>

    {{-- Results --}}
    <div class="max-w-7xl mx-auto px-4 py-8">
        <div id="tender-count" class="mb-6 text-sm text-gray-600 dark:text-gray-400">
            Showing 0 of 0 tenders
        </div>

        <div id="tenders-grid" class="grid md:grid-cols-2 gap-6 mb-8">
            @foreach($tenders as $t)
                @php
                    $categoryName = $t->category?->name ?: ($t->sector ?? 'General');
                    $budgetMax = (float) ($t->budget_max ?? $t->amount ?? 0);
                    $budgetMin = (float) ($t->budget_min ?? 0);
                    if ($budgetMax < 500000) {
                        $budgetBand = 'under500k';
                    } elseif ($budgetMax < 2000000) {
                        $budgetBand = '500k-2m';
                    } elseif ($budgetMax < 5000000) {
                        $budgetBand = '2m-5m';
                    } else {
                        $budgetBand = 'above5m';
                    }

                    if ($t->budget_min && $t->budget_max) {
                        $budgetLabel = '$' . number_format((float) $t->budget_min / 1000) . 'K - $' . number_format((float) $t->budget_max / 1000) . 'K';
                        if ($t->budget_max >= 1000000) {
                            $budgetLabel = '$' . number_format((float) $t->budget_min / 1000000, 1) . 'M - $' . number_format((float) $t->budget_max / 1000000, 1) . 'M';
                        }
                    } elseif ($t->amount) {
                        $budgetLabel = '$' . number_format((float) $t->amount);
                    } else {
                        $budgetLabel = 'N/A';
                    }

                    $deadline = $t->submission_deadline ?? $t->end_date;
                    $deadlineFormatted = $deadline ? $deadline->format('M j, Y') : 'N/A';
                    $deadlineSort = $deadline ? $deadline->format('Y-m-d') : '9999-12-31';
                    $daysLeft = $deadline ? (int) now()->startOfDay()->diffInDays($deadline, false) : null;
                    $published = $t->published_date ?? $t->created_at;
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

                    $summary = $t->summary ?: \Illuminate\Support\Str::limit(strip_tags((string) $t->description), 140);
                    $firstAtt = is_array($t->attachments) && count($t->attachments) > 0 ? $t->attachments[0] : null;
                    $firstUrl = null;
                    if ($firstAtt && is_array($firstAtt)) {
                        $firstUrl = $firstAtt['url'] ?? null;
                        if ($firstUrl && ! str_starts_with($firstUrl, 'http')) {
                            $mbu = app(\App\Services\RemoteUploadService::class)->getMediaBaseUrl();
                            $firstUrl = rtrim($mbu, '/') . '/' . ltrim($firstUrl, '/');
                        }
                    }
                @endphp
                <article
                    class="tender-card bg-white dark:bg-gray-800 rounded-xl shadow-md hover:shadow-xl transition-all duration-300 overflow-hidden border border-gray-200 dark:border-gray-700"
                    data-title="{{ strtolower($t->title) }}"
                    data-ref="{{ strtolower($t->reference_number ?? '') }}"
                    data-category="{{ $categoryName }}"
                    data-sector="{{ $t->sector }}"
                    data-type="{{ $t->tender_type }}"
                    data-budget-band="{{ $budgetBand }}"
                    data-budget-max="{{ $budgetMax }}"
                    data-deadline-sort="{{ $deadlineSort }}"
                    data-days-left="{{ $daysLeft ?? 9999 }}"
                    data-location="{{ $t->location }}"
                    data-created="{{ $t->created_at?->format('Y-m-d') ?? '' }}"
                    data-published="{{ $published?->format('Y-m-d') ?? '' }}"
                >
                    <div class="p-6">
                        <div class="mb-4">
                            <div class="flex flex-wrap gap-2 mb-3">
                                <span class="px-3 py-1 bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400 text-xs font-medium rounded-full">{{ $categoryName }}</span>
                                <span class="px-3 py-1 text-xs font-medium rounded-full {{ $statusClass }}">{{ $statusLabel }}</span>
                                @if($daysLeft !== null && $daysLeft >= 0 && $daysLeft <= 7)
                                    <span class="px-3 py-1 bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400 text-xs font-medium rounded-full">
                                        Closing in {{ $daysLeft }} {{ $daysLeft === 1 ? 'day' : 'days' }}
                                    </span>
                                @endif
                            </div>
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2 line-clamp-2 hover:text-blue-600 dark:hover:text-cyan-400 transition-colors">
                                <a href="{{ route('tenders.show', $t->slug ?? $t->id) }}" wire:navigate>{{ $t->title }}</a>
                            </h3>
                            <p class="text-gray-600 dark:text-gray-400 text-sm line-clamp-2">{{ $summary }}</p>
                        </div>

                        <div class="grid grid-cols-2 gap-3 mb-4 pb-4 border-b border-gray-200 dark:border-gray-700">
                            <div class="flex items-start gap-2">
                                <svg class="w-4 h-4 text-blue-600 dark:text-cyan-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                <div>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Deadline</p>
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $deadlineFormatted }}</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-2">
                                <svg class="w-4 h-4 text-green-600 dark:text-green-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <div>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Budget</p>
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $budgetLabel }}</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-2">
                                <svg class="w-4 h-4 text-red-600 dark:text-red-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                <div>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Location</p>
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $t->location ?? 'N/A' }}</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-2">
                                <svg class="w-4 h-4 text-purple-600 dark:text-purple-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                                <div>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Reference</p>
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $t->reference_number ?? 'N/A' }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="flex gap-3">
                            <a href="{{ route('tenders.show', $t->slug ?? $t->id) }}" wire:navigate
                               class="flex-1 bg-gradient-to-r from-blue-600 to-teal-500 hover:from-blue-700 hover:to-teal-600 text-white px-4 py-2.5 rounded-lg font-medium transition-all duration-200 hover:shadow-lg flex items-center justify-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                View Tender
                            </a>
                            @if($firstUrl)
                                <a href="{{ $firstUrl }}" target="_blank" rel="noopener"
                                   class="px-4 py-2.5 border-2 border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg font-medium hover:border-blue-600 dark:hover:border-cyan-400 hover:text-blue-600 dark:hover:text-cyan-400 transition-all duration-200 flex items-center justify-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                    Download
                                </a>
                            @else
                                <button type="button" disabled
                                        class="px-4 py-2.5 border-2 border-gray-200 dark:border-gray-700 text-gray-400 rounded-lg font-medium flex items-center justify-center gap-2 cursor-not-allowed opacity-60"
                                        title="No documents available">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                    Download
                                </button>
                            @endif
                        </div>
                    </div>
                </article>
            @endforeach
        </div>

        <div id="tenders-pagination" class="flex justify-center items-center gap-2 hidden"></div>

        <div id="tenders-empty" class="text-center py-12 {{ $tenders->isEmpty() ? '' : 'hidden' }}">
            <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">No tenders found</h3>
            <p class="text-gray-600 dark:text-gray-400">Try adjusting your filters or search query</p>
        </div>
    </div>
</div>
@endsection
