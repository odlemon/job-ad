@extends('layouts.app')

@section('content')
<style>
    .tenders-hero {
        background: linear-gradient(to right, #007bff 0%, #00b4d8 50%, #00e0b7 100%);
    }
    .tender-card {
        transition: box-shadow 0.2s ease;
    }
    .tender-card:hover {
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
    }
    .btn-view-tender {
        background: linear-gradient(to right, #007bff, #00e0b7);
    }
    .btn-view-tender:hover {
        opacity: 0.95;
    }
    .badge-category {
        background-color: #e6f7ff;
        color: #0066cc;
    }
    .badge-status {
        background-color: #f3f4f6;
        color: #374151;
    }
    .tenders-search-bar {
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08), 0 2px 8px rgba(0, 0, 0, 0.04);
    }
    .tenders-search-btn {
        background: linear-gradient(to right, #4285F4 0%, #66BB6A 100%);
    }
    .tenders-search-btn:hover {
        opacity: 0.95;
    }
    .tenders-search-bar .placeholder-grey::placeholder {
        color: #6D6D6D;
    }
</style>

<!-- Banner -->
<section class="tenders-hero relative pt-20 pb-32 md:pt-24 md:pb-40">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-4xl md:text-5xl font-bold text-white">Active Tenders</h1>
        <p class="mt-4 text-white/95 text-base md:text-lg max-w-2xl mx-auto">Browse current procurement opportunities from government and private sector buyers.</p>

        <div class="mt-10 max-w-3xl mx-auto flex rounded-2xl overflow-hidden bg-white tenders-search-bar">
            <div class="relative flex-1 flex items-center min-w-0">
                <svg class="absolute left-4 w-5 h-5 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input type="text" id="tender-search" placeholder="Search tenders... (title, reference number, category)" class="placeholder-grey w-full pl-12 pr-4 py-3.5 text-gray-900 border-0 focus:ring-0 focus:outline-none text-sm bg-transparent">
            </div>
            <button type="button" id="tender-search-btn" class="tenders-search-btn px-8 py-3.5 font-bold text-white text-sm transition shrink-0 border-l border-gray-200/80">Search</button>
        </div>
    </div>
</section>

<!-- Filters -->
<section class="bg-gray-100 border-b border-gray-200 -mt-12 relative z-10">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
        <div class="flex flex-wrap items-center gap-3">
            <select id="filter-entity" class="px-4 py-2.5 rounded-lg bg-white border border-gray-200 text-gray-800 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:outline-none cursor-pointer">
                <option value="">All Categories</option>
                @foreach($tenders->pluck('category')->filter()->unique('id') as $cat)
                <option value="{{ $cat->name }}">{{ $cat->name }}</option>
                @endforeach
            </select>
            <select id="filter-sector" class="px-4 py-2.5 rounded-lg bg-white border border-gray-200 text-gray-800 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:outline-none cursor-pointer">
                <option value="">All Sectors</option>
                @foreach($sectors as $s)
                <option value="{{ $s }}">{{ $s }}</option>
                @endforeach
            </select>
            <select id="filter-type" class="px-4 py-2.5 rounded-lg bg-white border border-gray-200 text-gray-800 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:outline-none cursor-pointer">
                <option value="">All Types</option>
                @foreach($types as $tp)
                <option value="{{ $tp }}">{{ $tp }}</option>
                @endforeach
            </select>
            <select id="filter-budget" class="px-4 py-2.5 rounded-lg bg-white border border-gray-200 text-gray-800 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:outline-none cursor-pointer">
                <option value="">All Budgets</option>
                <option value="Under1M">Under $1M</option>
                <option value="1M-2M">$1M - $2M</option>
                <option value="2M-5M">$2M - $5M</option>
                <option value="Over5M">Over $5M</option>
            </select>
            <select id="filter-deadline" class="px-4 py-2.5 rounded-lg bg-white border border-gray-200 text-gray-800 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:outline-none cursor-pointer">
                <option value="">All Deadlines</option>
                @foreach($deadlines as $d)
                <option value="{{ $d }}">{{ $d }}</option>
                @endforeach
            </select>
            <select id="filter-location" class="px-4 py-2.5 rounded-lg bg-white border border-gray-200 text-gray-800 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:outline-none cursor-pointer">
                <option value="">All Locations</option>
                @foreach($locations as $loc)
                <option value="{{ $loc }}">{{ $loc }}</option>
                @endforeach
            </select>
            <select id="filter-sort" class="px-4 py-2.5 rounded-lg bg-white border border-gray-200 text-gray-800 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:outline-none cursor-pointer ml-auto">
                <option value="newest">Newest</option>
                <option value="oldest">Oldest first</option>
                <option value="deadline-asc">Deadline (soonest)</option>
                <option value="deadline-desc">Deadline (latest)</option>
            </select>
        </div>
    </div>
</section>

<!-- Content -->
<section class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-10 bg-white min-h-screen">
    <p id="tender-count" class="text-sm text-gray-600 mb-6">Showing 1-{{ $tenders->count() }} of {{ $tenders->count() }} tenders</p>

    <div id="tenders-grid" class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @foreach($tenders as $t)
        @php
            $budgetRange = ($t->budget_min && $t->budget_max)
                ? '$' . number_format((float)$t->budget_min / 1000000, 1) . 'M - $' . number_format((float)$t->budget_max / 1000000, 1) . 'M'
                : ($t->amount ? '$' . number_format((float)$t->amount) : 'N/A');

            $budgetMax = (float)($t->budget_max ?? $t->amount ?? 0);
            if ($budgetMax < 1000000) $budgetBand = 'Under1M';
            elseif ($budgetMax < 2000000) $budgetBand = '1M-2M';
            elseif ($budgetMax < 5000000) $budgetBand = '2M-5M';
            else $budgetBand = 'Over5M';

            $deadlineFormatted = $t->submission_deadline ? $t->submission_deadline->format('M d, Y') : 'N/A';
            $deadlineSort = $t->submission_deadline ? $t->submission_deadline->format('Y-m-d') : '9999-12-31';
            $categoryName = $t->category ? $t->category->name : ($t->sector ?? 'General');
        @endphp
        <article
            class="tender-card bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden flex flex-col"
            data-title="{{ strtolower($t->title) }}"
            data-ref="{{ strtolower($t->reference_number ?? '') }}"
            data-category="{{ strtolower($categoryName) }}"
            data-entity="{{ $t->entity_name }}"
            data-sector="{{ $t->sector }}"
            data-type="{{ $t->tender_type }}"
            data-budget-band="{{ $budgetBand }}"
            data-deadline="{{ $deadlineFormatted }}"
            data-deadline-sort="{{ $deadlineSort }}"
            data-location="{{ $t->location }}"
            data-created="{{ $t->created_at ? $t->created_at->format('Y-m-d') : '' }}"
        >
            <div class="p-5 flex-1 flex flex-col">
                <div class="flex flex-wrap gap-2 mb-3">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium badge-category">{{ $categoryName }}</span>
                    @if($t->tender_type)
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium badge-status">{{ $t->tender_type }}</span>
                    @endif
                </div>
                <h2 class="text-lg font-bold text-gray-900 mb-2">{{ $t->title }}</h2>
                <p class="text-sm text-gray-600 mb-4 flex-1 line-clamp-3">{{ $t->description }}</p>

                <div class="grid grid-cols-2 gap-x-4 gap-y-3 text-sm mb-4">
                    <div class="flex items-center gap-2 min-w-0">
                        <svg class="w-4 h-4 text-gray-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        <div class="min-w-0">
                            <span class="text-gray-500 block text-xs leading-tight">Deadline</span>
                            <span class="text-gray-900 font-medium block leading-tight">{{ $deadlineFormatted }}</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 min-w-0">
                        <svg class="w-4 h-4 text-gray-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        <div class="min-w-0">
                            <span class="text-gray-500 block text-xs leading-tight">Location</span>
                            <span class="text-gray-900 font-medium block leading-tight break-words">{{ $t->location ?? 'N/A' }}</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 min-w-0">
                        <svg class="w-4 h-4 text-gray-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <div class="min-w-0">
                            <span class="text-gray-500 block text-xs leading-tight">Budget</span>
                            <span class="text-gray-900 font-medium block leading-tight">{{ $budgetRange }}</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 min-w-0">
                        <svg class="w-4 h-4 text-gray-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                        <div class="min-w-0">
                            <span class="text-gray-500 block text-xs leading-tight">Reference</span>
                            <span class="text-gray-900 font-medium block leading-tight">{{ $t->reference_number ?? 'N/A' }}</span>
                        </div>
                    </div>
                </div>

                <div class="flex gap-3 mt-auto pt-2 border-t border-gray-100">
                    <a href="{{ route('tenders.show', $t->slug ?? $t->id) }}" class="btn-view-tender flex-1 inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg text-white text-sm font-medium">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        View Tender
                    </a>
                    @php
                        $firstAtt = is_array($t->attachments) && count($t->attachments) > 0 ? $t->attachments[0] : null;
                        $firstUrl = null;
                        if ($firstAtt && is_array($firstAtt)) {
                            $firstUrl = $firstAtt['url'] ?? null;
                            if ($firstUrl && !str_starts_with($firstUrl, 'http')) {
                                $mbu = app(\App\Services\RemoteUploadService::class)->getMediaBaseUrl();
                                $firstUrl = rtrim($mbu, '/') . '/' . ltrim($firstUrl, '/');
                            }
                        }
                    @endphp
                    @if($firstUrl)
                    <a href="{{ $firstUrl }}" target="_blank" download class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg border border-gray-300 bg-white text-gray-700 text-sm font-medium hover:bg-gray-50 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        Download
                    </a>
                    @else
                    <span class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg border border-gray-200 bg-gray-50 text-gray-400 text-sm font-medium cursor-not-allowed" title="No documents uploaded yet">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        Download
                    </span>
                    @endif
                </div>
            </div>
        </article>
        @endforeach
    </div>

    @if($tenders->isEmpty())
    <div class="text-center py-16">
        <p class="text-gray-500 text-lg">No active tenders available at the moment.</p>
    </div>
    @endif
</section>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    var grid = document.getElementById('tenders-grid');
    var countEl = document.getElementById('tender-count');
    var cards = grid ? Array.from(grid.querySelectorAll('.tender-card')) : [];
    var searchInput = document.getElementById('tender-search');
    var searchBtn = document.getElementById('tender-search-btn');
    var filterEntity = document.getElementById('filter-entity');
    var filterSector = document.getElementById('filter-sector');
    var filterType = document.getElementById('filter-type');
    var filterBudget = document.getElementById('filter-budget');
    var filterDeadline = document.getElementById('filter-deadline');
    var filterLocation = document.getElementById('filter-location');
    var filterSort = document.getElementById('filter-sort');
    var total = cards.length;

    function getQ() { return (searchInput && searchInput.value) ? searchInput.value.toLowerCase().trim() : ''; }

    function matches(card) {
        var q = getQ();
        if (q) {
            var t = (card.getAttribute('data-title') || '');
            var r = (card.getAttribute('data-ref') || '');
            var c = (card.getAttribute('data-category') || '');
            if (!t.includes(q) && !r.includes(q) && !c.includes(q)) return false;
        }
        var v;
        v = filterEntity && filterEntity.value;
        if (v && (card.getAttribute('data-category') || '').toLowerCase() !== v.toLowerCase()) return false;
        v = filterSector && filterSector.value;
        if (v && card.getAttribute('data-sector') !== v) return false;
        v = filterType && filterType.value;
        if (v && card.getAttribute('data-type') !== v) return false;
        v = filterBudget && filterBudget.value;
        if (v && card.getAttribute('data-budget-band') !== v) return false;
        v = filterDeadline && filterDeadline.value;
        if (v && card.getAttribute('data-deadline') !== v) return false;
        v = filterLocation && filterLocation.value;
        if (v && card.getAttribute('data-location') !== v) return false;
        return true;
    }

    function run() {
        var visible = cards.filter(matches);
        var sortVal = filterSort ? filterSort.value : 'newest';
        visible.sort(function (a, b) {
            if (sortVal === 'deadline-asc') return (a.getAttribute('data-deadline-sort') || '').localeCompare(b.getAttribute('data-deadline-sort') || '');
            if (sortVal === 'deadline-desc') return (b.getAttribute('data-deadline-sort') || '').localeCompare(a.getAttribute('data-deadline-sort') || '');
            if (sortVal === 'oldest') return (a.getAttribute('data-created') || '').localeCompare(b.getAttribute('data-created') || '');
            return (b.getAttribute('data-created') || '').localeCompare(a.getAttribute('data-created') || '');
        });
        cards.forEach(function (c) { c.style.display = 'none'; c.remove(); });
        visible.forEach(function (c) { grid.appendChild(c); c.style.display = ''; });
        if (countEl) countEl.textContent = visible.length === 0 ? 'Showing 0 of ' + total + ' tenders' : 'Showing 1-' + visible.length + ' of ' + total + ' tenders';
    }

    [searchInput].forEach(function (el) { if (el) { el.addEventListener('input', run); el.addEventListener('keydown', function (e) { if (e.key === 'Enter') run(); }); }});
    if (searchBtn) searchBtn.addEventListener('click', run);
    [filterEntity, filterSector, filterType, filterBudget, filterDeadline, filterLocation, filterSort].forEach(function (el) { if (el) el.addEventListener('change', run); });
});
</script>
@endpush
@endsection
