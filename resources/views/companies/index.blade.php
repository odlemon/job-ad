@extends('layouts.app')

@section('content')
<style>
    .company-hero {
        background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 50%, #3b82f6 100%);
    }
    .company-card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .company-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 24px rgba(0,0,0,0.08);
    }
</style>

<!-- Hero Section -->
<section class="company-hero relative overflow-hidden">
    <div class="absolute inset-0 opacity-10">
        <div class="absolute top-10 right-20 w-64 h-64 bg-white rounded-full blur-3xl"></div>
        <div class="absolute bottom-10 left-10 w-48 h-48 bg-cyan-300 rounded-full blur-2xl"></div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 relative z-10">
        <div class="flex flex-col lg:flex-row items-center justify-between gap-10">
            <div class="lg:w-1/2">
                <h1 class="text-3xl md:text-4xl font-bold text-white leading-tight">Find the right company for you</h1>
                <p class="mt-3 text-blue-100 text-lg">Everything you need to know about a company, all in one place.</p>

                <!-- Search & Filters -->
                <div class="mt-8 flex flex-wrap items-center gap-3">
                    <div class="relative flex-1 min-w-[200px]">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        <input
                            type="text"
                            id="company-search"
                            value="{{ $filters['search'] ?? '' }}"
                            placeholder="Search by company name..."
                            class="w-full pl-10 pr-4 py-2.5 rounded-lg bg-white/90 backdrop-blur text-gray-900 placeholder-gray-500 border-0 focus:ring-2 focus:ring-white text-sm"
                        >
                    </div>
                    <select id="industry-filter" class="py-2.5 px-4 rounded-lg bg-white/90 backdrop-blur text-gray-700 border-0 focus:ring-2 focus:ring-white text-sm cursor-pointer">
                        <option value="">Industry</option>
                        @foreach($industries as $ind)
                            <option value="{{ $ind }}" {{ ($filters['industry'] ?? '') === $ind ? 'selected' : '' }}>{{ $ind }}</option>
                        @endforeach
                    </select>
                    <select id="jobs-filter" class="py-2.5 px-4 rounded-lg bg-white/90 backdrop-blur text-gray-700 border-0 focus:ring-2 focus:ring-white text-sm cursor-pointer">
                        <option value="">Jobs Available</option>
                        <option value="available" {{ ($filters['jobs'] ?? '') === 'available' ? 'selected' : '' }}>Has Open Jobs</option>
                    </select>
                </div>
            </div>
            <div class="hidden lg:block lg:w-1/3">
                <img src="https://img.freepik.com/free-photo/business-woman-working-laptop_23-2148994703.jpg" alt="Companies" class="w-64 h-64 object-cover rounded-full border-4 border-white/30 shadow-2xl mx-auto">
            </div>
        </div>
    </div>
</section>

<!-- Companies Grid -->
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <p id="company-count" class="text-sm text-gray-600 mb-6">Showing <span class="font-semibold">{{ method_exists($companies, 'total') ? $companies->total() : $companies->count() }}</span> companies</p>

    <div id="companies-grid" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-5">
        @forelse($companies as $company)
            @php
                $logoUrl = null;
                if ($company->logo) {
                    if (str_starts_with($company->logo, 'http')) {
                        $logoUrl = $company->logo;
                    } elseif (str_starts_with($company->logo, 'company-logos/')) {
                        $logoUrl = $mediaBaseUrl . '/' . $company->logo;
                    } else {
                        $logoUrl = $mediaBaseUrl . '/' . $company->logo;
                    }
                }
            @endphp
            <a href="{{ route('companies.show', $company->slug ?: $company->id) }}" class="company-card bg-white border border-gray-100 rounded-xl p-5 flex flex-col items-center text-center cursor-pointer" data-name="{{ strtolower($company->name) }}" data-industry="{{ strtolower($company->industry ?? '') }}" data-jobs="{{ $company->job_advertisements_count }}">
                <div class="w-16 h-16 rounded-xl bg-gray-50 border border-gray-100 flex items-center justify-center overflow-hidden mb-3">
                    @if($logoUrl)
                        <img src="{{ $logoUrl }}" alt="{{ $company->name }}" class="w-full h-full object-contain p-1">
                    @else
                        <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    @endif
                </div>
                <h3 class="text-sm font-bold text-gray-900 truncate w-full" title="{{ $company->name }}">{{ \Illuminate\Support\Str::limit($company->name, 20) }}</h3>
                <p class="text-xs text-blue-600 mt-1">{{ $company->job_advertisements_count }} {{ \Illuminate\Support\Str::plural('job', $company->job_advertisements_count) }}</p>
            </a>
        @empty
            <div class="col-span-full text-center py-16">
                <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                <p class="text-gray-500 text-lg font-medium">No companies found</p>
                <p class="text-gray-400 text-sm mt-1">Try adjusting your search or filters.</p>
            </div>
        @endforelse
    </div>
    @if(method_exists($companies, 'links'))
        <div class="mt-8">{{ $companies->links() }}</div>
    @endif
</section>

@push('scripts')
<script>
(function() {
    const searchInput = document.getElementById('company-search');
    const industryFilter = document.getElementById('industry-filter');
    const jobsFilter = document.getElementById('jobs-filter');
    const grid = document.getElementById('companies-grid');
    const countEl = document.getElementById('company-count');

    function filterCards() {
        const search = (searchInput.value || '').toLowerCase().trim();
        const industry = (industryFilter.value || '').toLowerCase();
        const jobsAvail = jobsFilter.value;

        const cards = grid.querySelectorAll('.company-card');
        let visible = 0;

        cards.forEach(card => {
            const name = card.dataset.name || '';
            const cardIndustry = card.dataset.industry || '';
            const jobCount = parseInt(card.dataset.jobs || '0', 10);

            let show = true;
            if (search && !name.includes(search)) show = false;
            if (industry && cardIndustry !== industry) show = false;
            if (jobsAvail === 'available' && jobCount === 0) show = false;

            card.style.display = show ? '' : 'none';
            if (show) visible++;
        });

        if (countEl) {
            countEl.innerHTML = `Showing <span class="font-semibold">${visible}</span> companies`;
        }
    }

    let debounceTimer;
    searchInput.addEventListener('input', function() {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(filterCards, 200);
    });
    industryFilter.addEventListener('change', filterCards);
    jobsFilter.addEventListener('change', filterCards);
})();
</script>
@endpush
@endsection
