@extends('layouts.app')

@section('content')
@php
    $mediaBaseUrl = $mediaBaseUrl ?? env('MEDIA_BASE_URL', 'http://31.220.82.129/uploads');

    $coverUrl = null;
    if ($company->cover_image) {
        $coverUrl = str_starts_with($company->cover_image, 'http')
            ? $company->cover_image
            : rtrim($mediaBaseUrl, '/') . '/' . ltrim($company->cover_image, '/');
    }

    $logoUrl = null;
    if ($company->logo) {
        $logoUrl = str_starts_with($company->logo, 'http')
            ? $company->logo
            : $mediaBaseUrl . '/' . $company->logo;
    }
@endphp

<!-- Banner -->
<section class="relative">
    <div class="h-52 md:h-60 w-full overflow-hidden">
        @if($coverUrl)
            <img src="{{ $coverUrl }}" alt="{{ $company->name }} cover" class="w-full h-full object-cover">
        @else
            <div class="w-full h-full bg-gradient-to-r from-blue-700 via-blue-500 to-indigo-500"></div>
        @endif
    </div>

    <!-- Company Header Card -->
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 -mt-14 md:-mt-16 relative z-10">
        <div class="bg-white rounded-xl shadow-lg border border-gray-100 px-6 sm:px-8 pt-5 pb-4">
            <!-- Top row: logo + info + buttons -->
            <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">
                <div class="flex items-start gap-4">
                    <!-- Logo -->
                    <div class="w-[72px] h-[72px] rounded-xl bg-gray-50 border border-gray-200 flex items-center justify-center overflow-hidden flex-shrink-0 -mt-2 shadow-md">
                        @if($logoUrl)
                            <img src="{{ $logoUrl }}" alt="{{ $company->name }}" class="w-full h-full object-cover">
                        @else
                            <svg class="w-9 h-9 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        @endif
                    </div>
                    <!-- Company Info -->
                    <div class="pt-0.5">
                        <div class="flex items-center gap-2 flex-wrap">
                            <h1 class="text-xl font-bold text-gray-900">{{ $company->name }}</h1>
                            @if($company->verified_at)
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-green-500 text-white">Verified</span>
                            @endif
                        </div>
                        <p class="mt-0.5 text-xs text-gray-500">
                            @if($company->size){{ $company->size }} Employees @endif
                            @if($company->size && $company->industry)&middot; @endif
                            {{ $company->industry ?? '' }}
                        </p>
                        <!-- Stars -->
                        <div class="mt-1.5 flex items-center gap-1.5">
                            @for($i = 0; $i < 5; $i++)
                                <svg class="w-4 h-4 {{ $i < 4 ? 'text-yellow-400' : 'text-yellow-300' }}" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            @endfor
                            <span class="text-lg font-bold text-gray-900 ml-1">{{ number_format($avgRating ?? 0, 1) }}</span>
                            <span class="text-xs text-gray-400">({{ $reviewsCount ?? 0 }} reviews)</span>
                        </div>
                        <!-- Followers / Jobs -->
                        <div class="mt-1.5 flex items-center gap-4 text-xs text-gray-500">
                            <span class="flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                {{ number_format($company->followers_count ?? 0) }} Followers
                            </span>
                            <span class="flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m8 0H8m8 0a2 2 0 012 2v6a2 2 0 01-2 2H8a2 2 0 01-2-2V8a2 2 0 012-2"/></svg>
                                {{ $company->job_advertisements_count }} Job {{ \Illuminate\Support\Str::plural('Vacancy', $company->job_advertisements_count) }}
                            </span>
                        </div>
                    </div>
                </div>
                <!-- Action Buttons -->
                <div class="flex items-center gap-2 flex-shrink-0 self-start md:pt-1">
                    <button class="inline-flex items-center gap-1.5 px-5 py-2 rounded-lg bg-gradient-to-r from-blue-500 to-cyan-400 text-white text-sm font-semibold shadow hover:from-blue-600 hover:to-cyan-500 transition">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z"/></svg>
                        Send CV
                    </button>
                    <button class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-semibold text-gray-700 bg-white hover:bg-gray-50 hover:text-blue-600 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                        Follow
                    </button>
                    <button class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-white text-gray-500 hover:bg-gray-50 hover:text-blue-600 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/></svg>
                    </button>
                </div>
            </div>

            <!-- Tabs -->
            <div class="mt-4 border-t border-gray-100 pt-3 -mx-6 sm:-mx-8 px-6 sm:px-8">
                <div class="flex items-center gap-1 text-sm overflow-x-auto">
                    @php
                        $tabMeta = [
                            'profile' => ['label' => 'Profile', 'count' => null],
                            'workplace' => ['label' => 'Our Workplace', 'count' => null],
                            'jobs' => ['label' => 'Jobs', 'count' => $company->job_advertisements_count],
                            'reviews' => ['label' => 'Reviews', 'count' => $reviewsCount ?? 0],
                            'photos' => ['label' => 'Photos', 'count' => null],
                            'qa' => ['label' => 'Q&A', 'count' => null],
                        ];
                    @endphp
                    @foreach($tabMeta as $key => $tab)
                        <button
                            type="button"
                            class="company-tab inline-flex items-center gap-1.5 px-4 py-1.5 rounded-md text-[13px] font-medium whitespace-nowrap transition
                                {{ $loop->first ? 'bg-blue-50 text-blue-600' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-700' }}"
                            data-tab="{{ $key }}"
                        >
                            {{ $tab['label'] }}
                            @if(!is_null($tab['count']))
                                <span class="inline-flex items-center justify-center min-w-[20px] h-5 px-1.5 rounded text-[11px] font-semibold {{ $loop->first ? 'bg-blue-100 text-blue-600' : 'bg-gray-100 text-gray-500' }}" data-tab-badge="{{ $key }}">{{ $tab['count'] }}</span>
                            @endif
                        </button>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Main Content -->
<section class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 mt-6 mb-16">
    <!-- Profile Tab -->
    <div id="tab-profile" class="company-tab-panel space-y-5">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 md:p-8">
            <h2 class="text-lg font-bold text-gray-900 mb-5">Company Profile</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-y-6 gap-x-10">
                <!-- Location -->
                <div class="flex items-start gap-3">
                    <div class="w-9 h-9 rounded-full bg-green-50 flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 font-medium">Location</p>
                        <p class="text-sm font-semibold text-gray-900 mt-0.5">{{ $company->location ?: 'Not specified' }}</p>
                    </div>
                </div>
                <!-- Website -->
                <div class="flex items-start gap-3">
                    <div class="w-9 h-9 rounded-full bg-blue-50 flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9"/></svg>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 font-medium">Website</p>
                        @if($company->website)
                            <a href="{{ $company->website }}" target="_blank" rel="noopener" class="text-sm font-semibold text-blue-600 hover:underline mt-0.5 block break-all">{{ $company->website }}</a>
                        @else
                            <p class="text-sm font-semibold text-gray-900 mt-0.5">Not specified</p>
                        @endif
                    </div>
                </div>
                <!-- Company Email -->
                <div class="flex items-start gap-3">
                    <div class="w-9 h-9 rounded-full bg-purple-50 flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 font-medium">Company Email</p>
                        @if($company->email)
                            <a href="mailto:{{ $company->email }}" class="text-sm font-semibold text-blue-600 hover:underline mt-0.5 block">{{ $company->email }}</a>
                        @else
                            <p class="text-sm font-semibold text-gray-900 mt-0.5">Not specified</p>
                        @endif
                    </div>
                </div>
                <!-- Company Size -->
                <div class="flex items-start gap-3">
                    <div class="w-9 h-9 rounded-full bg-orange-50 flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 font-medium">Company Size</p>
                        <p class="text-sm font-semibold text-gray-900 mt-0.5">{{ $company->size ?: 'Not specified' }}</p>
                    </div>
                </div>
                <!-- Contact Number -->
                <div class="flex items-start gap-3">
                    <div class="w-9 h-9 rounded-full bg-blue-50 flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 font-medium">Contact Number</p>
                        @if($company->phone)
                            <a href="tel:{{ $company->phone }}" class="text-sm font-semibold text-blue-600 hover:underline mt-0.5 block">{{ $company->phone }}</a>
                        @else
                            <p class="text-sm font-semibold text-gray-900 mt-0.5">Not specified</p>
                        @endif
                    </div>
                </div>
                <!-- Work Hours -->
                <div class="flex items-start gap-3">
                    <div class="w-9 h-9 rounded-full bg-green-50 flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 font-medium">Work Hours</p>
                        <p class="text-sm font-semibold text-gray-900 mt-0.5">{{ $company->working_hours ?: 'Not specified' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Connect With Us -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 md:p-8">
            <h2 class="text-lg font-bold text-gray-900 mb-4">Connect With Us</h2>
            @if($company->linkedin || $company->facebook || $company->twitter || ($company->instagram ?? null))
                <div class="flex flex-wrap items-center gap-3">
                    @if($company->linkedin)
                        <a href="{{ $company->linkedin }}" target="_blank" rel="noopener"
                           class="inline-flex items-center justify-center w-10 h-10 rounded-lg border border-blue-200 bg-white text-[#0A66C2] hover:bg-blue-50 transition"
                           aria-label="LinkedIn">
                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M4.98 3.5C4.98 4.88 3.87 6 2.49 6S.02 4.88.02 3.5 1.12 1 2.49 1s2.49 1.12 2.49 2.5zM.24 8.09h4.5V24H.24V8.09zM8.49 8.09h4.32v2.17h.06c.6-1.14 2.07-2.35 4.26-2.35 4.56 0 5.4 3 5.4 6.91V24h-4.51v-7.33c0-1.75-.03-4-2.43-4s-2.8 1.9-2.8 3.87V24H8.49V8.09z"/>
                            </svg>
                        </a>
                    @endif
                    @if($company->twitter)
                        <a href="{{ $company->twitter }}" target="_blank" rel="noopener"
                           class="inline-flex items-center justify-center w-10 h-10 rounded-lg border border-sky-200 bg-white text-[#1DA1F2] hover:bg-sky-50 transition"
                           aria-label="Twitter">
                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M22.46 6c-.77.35-1.6.58-2.46.69a4.3 4.3 0 001.88-2.37 8.59 8.59 0 01-2.72 1.04 4.28 4.28 0 00-7.3 3.9A12.14 12.14 0 013.16 4.9a4.27 4.27 0 001.32 5.7 4.2 4.2 0 01-1.94-.54v.05a4.28 4.28 0 003.43 4.2 4.27 4.27 0 01-1.93.07 4.28 4.28 0 004 2.97A8.58 8.58 0 012 19.54a12.06 12.06 0 006.56 1.92c7.88 0 12.18-6.53 12.18-12.18l-.01-.56A8.69 8.69 0 0022.46 6z"/>
                            </svg>
                        </a>
                    @endif
                    @if($company->facebook)
                        <a href="{{ $company->facebook }}" target="_blank" rel="noopener"
                           class="inline-flex items-center justify-center w-10 h-10 rounded-lg border border-blue-200 bg-white text-[#1877F2] hover:bg-blue-50 transition"
                           aria-label="Facebook">
                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M22 12a10 10 0 10-11.5 9.87v-6.99H7.07V12h3.43V9.41c0-3.38 2.01-5.25 5.08-5.25 1.47 0 3 .26 3 .26v3.31h-1.69c-1.67 0-2.19 1.04-2.19 2.1V12h3.73l-.6 2.88h-3.13v6.99A10 10 0 0022 12z"/>
                            </svg>
                        </a>
                    @endif
                    @if($company->instagram ?? null)
                        <a href="{{ $company->instagram }}" target="_blank" rel="noopener"
                           class="inline-flex items-center justify-center w-10 h-10 rounded-lg border border-pink-200 bg-white text-[#E4405F] hover:bg-pink-50 transition"
                           aria-label="Instagram">
                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12s.014 3.668.072 4.948c.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24s3.668-.014 4.948-.072c4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948s-.014-3.667-.072-4.947c-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/>
                            </svg>
                        </a>
                    @endif
                </div>
            @else
                <p class="text-sm text-gray-400">No social links added yet.</p>
            @endif
        </div>

        <!-- About -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 md:p-8">
            <h2 class="text-lg font-bold text-gray-900 mb-3">About {{ $company->name }}</h2>
            <p class="text-sm text-gray-600 leading-relaxed whitespace-pre-line">{{ $company->description ?: 'This company has not added a description yet.' }}</p>
        </div>
    </div>

    <!-- Our Workplace Tab -->
    <div id="tab-workplace" class="company-tab-panel hidden space-y-5">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 md:p-8">
            <h2 class="text-lg font-bold text-gray-900">Our Workplace</h2>
            <p class="mt-2 text-sm text-gray-600 leading-relaxed">
                Discover what makes {{ $company->name }} a great place to work. We're committed to creating an environment where our team members can thrive, grow, and make meaningful contributions.
            </p>

            <div class="mt-6 border-t border-gray-100 pt-6">
                <!-- Perks & Benefits -->
                @php
                    // Decode selected benefits from company profile
                    $selectedBenefits = [];
                    $rawBenefits = $company->culture_benefits;
                    if (is_array($rawBenefits)) {
                        $selectedBenefits = $rawBenefits;
                    } elseif (is_string($rawBenefits) && $rawBenefits !== '') {
                        $decoded = json_decode($rawBenefits, true);
                        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                            $selectedBenefits = $decoded;
                        } else {
                            $selectedBenefits = array_filter(array_map('trim', explode(',', $rawBenefits)));
                        }
                    }

                    $benefitDefinitions = [
                        'Competitive Salary' => [
                            'description' => 'Industry leading compensation packages with performance bonuses.',
                            'bg' => 'from-blue-50 to-blue-100',
                            'iconBg' => 'bg-blue-100',
                            'iconColor' => 'text-blue-600',
                        ],
                        'Health Insurance' => [
                            'description' => 'Comprehensive medical, dental, and vision coverage.',
                            'bg' => 'from-green-50 to-green-100',
                            'iconBg' => 'bg-green-100',
                            'iconColor' => 'text-green-600',
                        ],
                        'Learning & Development' => [
                            'description' => 'Training programs, workshops, and educational assistance.',
                            'bg' => 'from-purple-50 to-purple-100',
                            'iconBg' => 'bg-purple-100',
                            'iconColor' => 'text-purple-600',
                        ],
                        'Work-Life Balance' => [
                            'description' => 'Flexible working hours and remote work options.',
                            'bg' => 'from-orange-50 to-orange-100',
                            'iconBg' => 'bg-orange-100',
                            'iconColor' => 'text-orange-600',
                        ],
                        'Paid Time Off' => [
                            'description' => 'Generous vacation days, sick leave, and public holidays.',
                            'bg' => 'from-cyan-50 to-cyan-100',
                            'iconBg' => 'bg-cyan-100',
                            'iconColor' => 'text-cyan-600',
                        ],
                        'Employee Wellness' => [
                            'description' => 'Gym memberships, wellness programs, and mental health support.',
                            'bg' => 'from-pink-50 to-pink-100',
                            'iconBg' => 'bg-pink-100',
                            'iconColor' => 'text-pink-600',
                        ],
                    ];

                    $activeBenefits = [];
                    foreach ($selectedBenefits as $label) {
                        if (isset($benefitDefinitions[$label])) {
                            $activeBenefits[$label] = $benefitDefinitions[$label];
                        }
                    }
                    // Fallback: if none selected, show all defaults
                    if (empty($activeBenefits)) {
                        $activeBenefits = $benefitDefinitions;
                    }
                    $activeBenefits = array_slice($activeBenefits, 0, 6);
                @endphp

                <h3 class="text-sm font-semibold text-gray-800 mb-3">Perks &amp; Benefits</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    @foreach($activeBenefits as $label => $meta)
                        <div class="rounded-xl bg-gradient-to-r {{ $meta['bg'] }} px-4 py-3 flex items-start gap-3 shadow-[0_4px_12px_rgba(15,23,42,0.04)]">
                            <div class="mt-0.5 flex h-9 w-9 items-center justify-center rounded-full {{ $meta['iconBg'] }}">
                                <svg class="w-4 h-4 {{ $meta['iconColor'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-900">{{ $label }}</p>
                                <p class="text-xs text-gray-600 mt-0.5">{{ $meta['description'] }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Company Values -->
                <div class="mt-8">
                    <h3 class="text-sm font-semibold text-gray-800 mb-3">Company Values</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                        <div class="rounded-xl bg-blue-50 px-4 py-4 flex flex-col gap-2">
                            <div class="flex items-center gap-2">
                                <div class="h-9 w-9 rounded-full bg-blue-100 flex items-center justify-center">
                                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <p class="text-sm font-semibold text-gray-900">Integrity</p>
                            </div>
                            <p class="text-xs text-gray-600">
                                We conduct business with honesty, transparency, and ethical principles.
                            </p>
                        </div>
                        <div class="rounded-xl bg-green-50 px-4 py-4 flex flex-col gap-2">
                            <div class="flex items-center gap-2">
                                <div class="h-9 w-9 rounded-full bg-green-100 flex items-center justify-center">
                                    <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                </div>
                                <p class="text-sm font-semibold text-gray-900">Excellence</p>
                            </div>
                            <p class="text-xs text-gray-600">
                                We strive for excellence in everything we do and continuously improve.
                            </p>
                        </div>
                        <div class="rounded-xl bg-purple-50 px-4 py-4 flex flex-col gap-2">
                            <div class="flex items-center gap-2">
                                <div class="h-9 w-9 rounded-full bg-purple-100 flex items-center justify-center">
                                    <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536M9 11l-3 3 3 3m6-6l3 3-3 3M9 5h6"/>
                                    </svg>
                                </div>
                                <p class="text-sm font-semibold text-gray-900">Innovation</p>
                            </div>
                            <p class="text-xs text-gray-600">
                                We embrace creativity and innovation to stay ahead of the curve.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Who We Are -->
                <div class="mt-8 border-t border-gray-100 pt-6">
                    <h3 class="text-sm font-semibold text-gray-800 mb-2">Who We Are</h3>
                    <p class="text-sm text-gray-600 leading-relaxed whitespace-pre-line">
                        {{ $company->workplace_description ?: ($company->description ?: 'We are a team of passionate individuals dedicated to making a difference. Our diverse team brings together unique perspectives, skills, and experiences to create innovative solutions and deliver exceptional results.') }}
                    </p>
                </div>

                <!-- Team Culture -->
                <div class="mt-8">
                    <h3 class="text-sm font-semibold text-gray-800 mb-3">Team Culture</h3>
                    <p class="text-sm text-gray-600 leading-relaxed">
                        Our culture is built on mutual respect, open communication, and a shared commitment to excellence. We foster an environment where everyone feels valued, heard, and inspired to contribute their best work.
                    </p>
                    <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-700">
                        <ul class="space-y-1.5 list-disc list-inside">
                            <li><span class="font-semibold">Collaborative Environment:</span> We work together, share knowledge, and support each other's growth.</li>
                            <li><span class="font-semibold">Inclusive &amp; Diverse:</span> We celebrate diversity and create opportunities for all backgrounds.</li>
                        </ul>
                        <ul class="space-y-1.5 list-disc list-inside">
                            <li><span class="font-semibold">Innovation-Driven:</span> We encourage creative thinking and welcome new ideas from everyone.</li>
                            <li><span class="font-semibold">Results-Oriented:</span> We focus on delivering impactful outcomes while maintaining high quality standards.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Jobs Tab -->
    <div id="tab-jobs" class="company-tab-panel hidden space-y-5">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 md:p-8">
            <h2 class="text-lg font-bold text-gray-900 mb-4">Available Positions</h2>

            <!-- Alert banner -->
            <div class="mb-5 flex flex-col md:flex-row md:items-center md:justify-between gap-3 rounded-lg border border-blue-200 bg-blue-50 px-4 py-3">
                <div class="flex items-start gap-3">
                    <div class="mt-0.5 flex h-8 w-8 items-center justify-center rounded-full bg-white">
                        <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M12 2a10 10 0 100 20 10 10 0 000-20z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-900">Stay updated on new opportunities at {{ $company->name }}</p>
                        <p class="text-xs text-gray-600 mt-0.5">Enable alerts to be notified when new positions are posted.</p>
                    </div>
                </div>
                <button class="inline-flex items-center justify-center px-4 py-2 rounded-lg bg-blue-600 text-white text-xs font-semibold shadow-sm hover:bg-blue-700 transition">
                    Enable Alerts
                </button>
            </div>

            @if($openJobs->isEmpty())
                <p class="text-sm text-gray-400">This company currently has no open positions.</p>
            @else
                <!-- Filters row -->
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-4 text-xs">
                    <p class="text-gray-600">
                        <span class="font-semibold">{{ $openJobs->count() }}</span> job positions available
                    </p>
                    <div class="flex flex-wrap items-center gap-2">
                        <div class="flex items-center gap-1">
                            <span class="text-gray-500 mr-1">Job Type:</span>
                            <select id="company-jobs-type-filter" class="border border-gray-200 rounded-md px-2.5 py-1.5 text-xs text-gray-700 bg-white focus:outline-none focus:ring-1 focus:ring-blue-500">
                                <option value="">All</option>
                                @foreach($openJobs->pluck('employment_type')->filter()->unique() as $type)
                                    <option value="{{ $type }}">{{ $type }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex items-center gap-1">
                            <span class="text-gray-500 mr-1">Education:</span>
                            <select id="company-jobs-education-filter" class="border border-gray-200 rounded-md px-2.5 py-1.5 text-xs text-gray-700 bg-white focus:outline-none focus:ring-1 focus:ring-blue-500">
                                <option value="">All</option>
                                @foreach($openJobs->pluck('education_level')->filter()->unique() as $edu)
                                    <option value="{{ $edu }}">{{ $edu }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex items-center gap-1">
                            <span class="text-gray-500 mr-1">Sort:</span>
                            <select id="company-jobs-sort" class="border border-gray-200 rounded-md px-2.5 py-1.5 text-xs text-gray-700 bg-white focus:outline-none focus:ring-1 focus:ring-blue-500">
                                <option value="default">Default</option>
                                <option value="newest">Newest</option>
                                <option value="oldest">Oldest</option>
                            </select>
                        </div>
                        <div class="flex items-center gap-0 rounded-lg border border-gray-200 overflow-hidden">
                            <button type="button" id="company-jobs-view-list" class="jobs-view-btn inline-flex items-center justify-center w-9 h-8 rounded-none bg-blue-600 text-white border-r border-gray-200" title="List view">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h10M4 18h6" />
                                </svg>
                            </button>
                            <button type="button" id="company-jobs-view-grid" class="jobs-view-btn inline-flex items-center justify-center w-9 h-8 rounded-none border-0 text-gray-500 bg-white hover:bg-gray-50" title="Grid view">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h6v6H4zM14 6h6v6h-6zM4 14h6v6H4zM14 14h6v6h-6z" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Jobs list (toggle list/grid via JS) -->
                <div id="company-jobs-list" class="company-jobs-container space-y-4" data-view="list">
                    @foreach($openJobs as $job)
                        @php
                            $salaryText = null;
                            if ($job->salary_min && $job->salary_max && !$job->hide_salary) {
                                $salaryText = ($job->currency ?: 'SCR') . ' ' . number_format($job->salary_min) . ' - ' . number_format($job->salary_max) . ' per month';
                            }
                        @endphp
                        <div class="job-card flex flex-col border border-gray-200 rounded-xl overflow-hidden bg-white shadow-[0_6px_18px_rgba(15,23,42,0.04)]"
                             data-job-type="{{ strtolower($job->employment_type ?? '') }}"
                             data-education="{{ strtolower($job->education_level ?? '') }}"
                             data-posted="{{ optional($job->published_at)->timestamp ?? $job->created_at->timestamp }}">
                            <div class="px-5 py-4 flex-1 flex flex-col">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="min-w-0 flex-1">
                                        <h3 class="text-sm font-semibold text-red-600 mb-0.5">{{ $job->title }}</h3>
                                        @if($salaryText)
                                            <p class="text-sm font-semibold text-gray-900">{{ $salaryText }}</p>
                                        @endif
                                        <p class="mt-1 text-xs text-gray-600 line-clamp-2">{{ \Illuminate\Support\Str::limit(strip_tags($job->description), 120) }}</p>
                                    </div>
                                    <button class="flex-shrink-0 inline-flex items-center justify-center w-8 h-8 rounded-full border border-gray-200 text-gray-400 hover:text-blue-600 hover:border-blue-400 transition" type="button">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5v14l7-5 7 5V5a2 2 0 00-2-2H7a2 2 0 00-2 2z" />
                                        </svg>
                                    </button>
                                </div>

                                <div class="mt-3 flex flex-wrap items-center gap-2 text-[11px]">
                                    @if($job->location)
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full bg-gray-100 text-gray-700">
                                            {{ $job->location }}
                                        </span>
                                    @endif
                                    @if($job->employment_type)
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full bg-gray-100 text-gray-700">
                                            {{ $job->employment_type }}
                                        </span>
                                    @endif
                                    @if($job->experience_level)
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full bg-gray-100 text-gray-700">
                                            {{ $job->experience_level }}
                                        </span>
                                    @endif
                                    @if($job->work_environment)
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full bg-gray-100 text-gray-700">
                                            {{ $job->work_environment }}
                                        </span>
                                    @endif
                                </div>

                                <div class="mt-3 flex flex-wrap items-center justify-between gap-2 text-[11px] text-gray-500">
                                    <div class="space-x-4">
                                        <span>Posted {{ optional($job->published_at ?? $job->created_at)->format('M j, Y') }}</span>
                                        @if($job->expires_at ?? null)
                                            <span>Expiring on {{ optional($job->expires_at)->format('M j, Y') }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <a href="{{ route('jobs.show', $job->id) }}" class="block w-full bg-blue-600 hover:bg-blue-700 text-center text-xs font-semibold text-white py-2.5 mt-auto">
                                Apply Now
                            </a>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <!-- Reviews Tab -->
    <div id="tab-reviews" class="company-tab-panel hidden">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 md:p-8">
            <h2 class="text-lg font-bold text-gray-900 mb-1">Reviews <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-sm font-medium bg-gray-100 text-gray-700 ml-1">{{ $reviewsCount ?? 0 }}</span></h2>

            @php
                $reviewsCount = $reviewsCount ?? 0;
                $avgRating = $avgRating ?? 0;
                $starDistribution = $starDistribution ?? [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0];
                $categoryLabels = $categoryLabels ?? [];
                $categoryAverages = $categoryAverages ?? [];
                $categoryCounts = $categoryCounts ?? [];
                $reviews = $reviews ?? collect();
                $maxBar = max(1, max($starDistribution));
                $canAddReview = $canAddReview ?? false;
                $reviewIneligibleReason = $reviewIneligibleReason ?? null;
            @endphp

            <!-- Summary: 50/50 layout - left: overall + distribution, right: categories -->
            <div class="rounded-xl border border-gray-200 bg-white p-6 md:p-8 mb-6 shadow-sm">
                <div class="flex flex-col lg:flex-row gap-8 lg:gap-0">
                    <!-- Left half: Overall rating + distribution bars (bars expand to fill space) -->
                    <div class="flex flex-col gap-6 lg:w-1/2 lg:pr-8 lg:border-r lg:border-gray-200">
                        <div class="text-center">
                            <span class="text-4xl font-bold text-gray-900">{{ number_format($avgRating, 1) }}</span>
                            <div class="relative flex items-center justify-center gap-0.5 mt-1" aria-hidden="true">
                                <div class="flex gap-0.5 text-gray-200">
                                    @for($i = 1; $i <= 5; $i++)
                                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                    @endfor
                                </div>
                                <div class="absolute top-0 left-0 flex gap-0.5 text-yellow-400 overflow-hidden" style="width: {{ min(100, ($avgRating / 5) * 100) }}%;">
                                    @for($i = 1; $i <= 5; $i++)
                                        <svg class="w-6 h-6 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                    @endfor
                                </div>
                            </div>
                            <p class="text-sm text-gray-500 mt-1">{{ $reviewsCount }} ratings in total</p>
                        </div>
                        <div class="space-y-2">
                            @foreach([5, 4, 3, 2, 1] as $stars)
                                <div class="flex items-center gap-3">
                                    <span class="text-xs font-medium text-gray-700 w-4 flex-shrink-0">{{ $stars }}</span>
                                    <div class="flex-1 min-w-0 h-3 bg-gray-200 rounded-full overflow-hidden">
                                        <div class="h-full rounded-full" style="width: {{ $maxBar > 0 ? (($starDistribution[$stars] ?? 0) / $maxBar) * 100 : 0 }}%; background-color: #E73A99;"></div>
                                    </div>
                                    <span class="text-xs text-gray-600 tabular-nums w-8 text-right flex-shrink-0">{{ $starDistribution[$stars] ?? 0 }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    @if(count($categoryLabels) > 0)
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-5 lg:w-1/2 lg:pl-8">
                            @foreach($categoryLabels as $key => $label)
                                @if(isset($categoryAverages[$key]) && $categoryAverages[$key] !== null)
                                    @php
                                        $catAvg = $categoryAverages[$key];
                                        $catCount = $categoryCounts[$key] ?? 0;
                                        $catPct = min(100, ($catAvg / 5) * 100);
                                    @endphp
                                    <div class="flex flex-col gap-1">
                                        <div class="relative inline-flex items-center gap-0.5 w-fit">
                                            <div class="flex gap-0.5 text-gray-200">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                                @endfor
                                            </div>
                                            <div class="absolute top-0 left-0 flex gap-0.5 text-yellow-400 overflow-hidden" style="width: {{ $catPct }}%;">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                                @endfor
                                            </div>
                                        </div>
                                        <div class="flex items-baseline gap-1">
                                            <span class="text-sm font-bold text-gray-900">{{ number_format($catAvg, 1) }}</span>
                                            <span class="text-sm text-gray-500">({{ $catCount }} ratings)</span>
                                        </div>
                                        <span class="text-sm text-gray-600">{{ $label }}</span>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    @endif
                </div>
                <p class="text-xs text-gray-500 mt-6 pt-4 border-t border-gray-100">Ratings for {{ $company->name }} are shared as-is from employees in line with our community guidelines.</p>
            </div>

            <!-- Reviews list header -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
                <div class="flex flex-wrap items-center gap-3">
                    <h3 class="text-base font-semibold text-gray-900">Reviews</h3>
                    @if($canAddReview)
                        <button type="button" id="add-review-btn" data-can-add="1" class="inline-flex items-center justify-center px-4 py-2 rounded-lg text-white text-sm font-medium transition hover:opacity-90" style="background-color: #007BFF;">Add Review</button>
                    @elseif($reviewIneligibleReason)
                        <p id="review-ineligible-message" class="text-sm text-amber-700 bg-amber-50 border border-amber-200 rounded-lg px-4 py-2.5 max-w-md">{{ $reviewIneligibleReason }}</p>
                    @else
                        <button type="button" id="add-review-btn" data-can-add="0" data-ineligible-reason="" class="inline-flex items-center justify-center px-4 py-2 rounded-lg text-white text-sm font-medium transition hover:opacity-90" style="background-color: #007BFF;">Add Review</button>
                    @endif
                </div>
                <div class="flex items-center gap-2 text-xs text-gray-500">
                    <span>Showing {{ $reviews->count() }} reviews sorted by</span>
                    <select id="reviews-sort" class="border border-gray-200 rounded-md px-2 py-1.5 text-gray-700 bg-white focus:outline-none focus:ring-1 focus:ring-blue-500">
                        <option value="recent">Most recent</option>
                        <option value="highest">Highest rating</option>
                        <option value="lowest">Lowest rating</option>
                    </select>
                </div>
            </div>

            <!-- Disclaimer: light yellow box per design (#FFF3CD bg, #FFE08C border) -->
            <div class="flex gap-3 p-4 rounded-lg bg-amber-50 border border-amber-200 mb-6" style="background-color: #FFF3CD; border-color: #FFE08C;">
                <svg class="w-5 h-5 flex-shrink-0 mt-0.5" style="color: #F59E0B;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                <p class="text-sm text-gray-700 leading-relaxed" style="color: #92400E;">Disclaimer: JobHub is not liable for any reviews posted by users. Neither the company nor JobHub verifies the identity of individuals writing comments. All reviews are informational and shared from a job seeker's perspective. We do not condone vulgar, offensive, or inappropriate comments, and we reserve the right to remove any content that violates our community guidelines.</p>
            </div>

            <!-- Individual reviews -->
            <div id="reviews-list" class="space-y-5">
                @forelse($reviews as $review)
                    <div class="review-card rounded-xl border border-gray-100 bg-white p-5 shadow-sm" data-rating="{{ $review->rating }}" data-created="{{ $review->created_at->timestamp }}">
                        <div class="flex items-center gap-2 mb-3">
                            <span class="text-lg font-bold text-gray-900">{{ number_format($review->rating, 1) }}</span>
                            <div class="flex items-center gap-0.5">
                                @for($i = 1; $i <= 5; $i++)
                                    <svg class="w-4 h-4 {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-200' }}" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                @endfor
                            </div>
                        </div>
                        <p class="text-sm font-semibold text-gray-900">{{ $review->role ?? 'Former employee' }}</p>
                        <p class="text-xs text-gray-500 mt-0.5">{{ $review->created_at->format('M Y') }}</p>
                        <p class="text-xs text-gray-500 mt-0.5">{{ $review->location }}</p>
                        <p class="text-xs text-gray-500">{{ $review->employment_status }}</p>

                        @if($review->good_things)
                            <p class="text-xs font-semibold text-gray-700 mt-3">The good things</p>
                            <p class="text-sm text-gray-600 mt-0.5">{{ $review->good_things }}</p>
                        @endif
                        @if($review->challenges)
                            <p class="text-xs font-semibold text-gray-700 mt-3">The challenges</p>
                            <p class="text-sm text-gray-600 mt-0.5">{{ $review->challenges }}</p>
                        @endif

                        <div class="flex items-center gap-4 mt-4 pt-3 border-t border-gray-100 text-xs text-gray-500">
                            <button type="button" class="inline-flex items-center gap-1 hover:text-blue-600 transition">Helpful? <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg></button>
                            <button type="button" class="hover:text-red-600 transition">Report</button>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-gray-500 py-6">No reviews yet. Be the first to share your experience.</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Photos Tab -->
    <div id="tab-photos" class="company-tab-panel hidden">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 md:p-8">
            <h2 class="text-lg font-bold text-gray-900 mb-4">Photos</h2>
            @php
                $gallery = is_array($company->gallery_images) ? $company->gallery_images : (is_string($company->gallery_images) ? json_decode($company->gallery_images, true) : []);
                $gallery = $gallery ?: [];
            @endphp
            @if(count($gallery) > 0)
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                    @foreach($gallery as $idx => $img)
                        @php
                            $imgUrl = str_starts_with($img, 'http') ? $img : $mediaBaseUrl . '/' . $img;
                        @endphp
                        <div class="aspect-square rounded-lg overflow-hidden bg-gray-100">
                            <img src="{{ $imgUrl }}" alt="Photo {{ $idx + 1 }}" class="w-full h-full object-cover">
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-sm text-gray-400">No photos uploaded yet.</p>
            @endif
        </div>
    </div>

    <!-- Q&A Tab -->
    <div id="tab-qa" class="company-tab-panel hidden">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 md:p-8">
            <h2 class="text-lg font-bold text-gray-900 mb-5">Questions & Answers</h2>

            @php
                $qaEntries = [
                    [
                        'question' => 'What makes ' . $company->name . ' different from other companies in the industry?',
                        'answer' => 'We focus on quality, sustainability, and our people. We source carefully, maintain high standards in everything we do, and invest in training and development. We also prioritise creating a welcoming environment where both customers and team members feel valued.',
                    ],
                    [
                        'question' => 'Do you offer career development opportunities?',
                        'answer' => 'Yes! We invest heavily in our team through comprehensive training programs, certifications, and clear career progression paths. Many of our managers and leads started in entry-level roles and grew within the company.',
                    ],
                    [
                        'question' => 'What are your working hours like?',
                        'answer' => 'We offer flexible scheduling with morning, afternoon, and evening shifts to accommodate different lifestyles. Part-time and full-time positions are available. Specific hours depend on the role and location.',
                    ],
                    [
                        'question' => 'What benefits do employees receive?',
                        'answer' => 'Employees enjoy competitive salaries, health insurance where applicable, staff discounts, performance-based bonuses, and paid time off. We also provide opportunities for ongoing education and development programs.',
                    ],
                ];
            @endphp

            <div class="space-y-4">
                @foreach($qaEntries as $entry)
                    <div class="rounded-lg border border-gray-100 bg-gray-50/80 px-5 py-4 shadow-sm">
                        <p class="text-sm font-semibold text-gray-900 mb-2">{{ $entry['question'] }}</p>
                        <p class="text-sm text-gray-600 leading-relaxed">{{ $entry['answer'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Add Review Modal (job seeker only) -->
    <div id="add-review-modal" class="fixed inset-0 z-50 hidden" aria-modal="true" role="dialog">
        <div class="fixed inset-0 bg-black/50" id="add-review-modal-backdrop"></div>
        <div class="fixed inset-0 flex items-center justify-center p-4 overflow-y-auto">
            <div class="relative bg-white rounded-xl shadow-xl max-w-lg w-full max-h-[90vh] overflow-y-auto">
                <div class="sticky top-0 bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between rounded-t-xl">
                    <h3 class="text-lg font-semibold text-gray-900">Add your review</h3>
                    <button type="button" id="add-review-modal-close" class="p-2 rounded-lg text-gray-500 hover:bg-gray-100 hover:text-gray-700 transition" aria-label="Close">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <form id="add-review-form" class="p-6 space-y-4" data-action="{{ route('companies.reviews.store', $company) }}">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Overall rating <span class="text-red-500">*</span></label>
                        <div class="flex items-center gap-1" id="review-rating-stars">
                            @for($i = 1; $i <= 5; $i++)
                                <button type="button" class="review-star p-0.5 text-gray-200 hover:text-yellow-400 focus:outline-none" data-rating="{{ $i }}" aria-label="{{ $i }} star{{ $i > 1 ? 's' : '' }}">
                                    <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                </button>
                            @endfor
                        </div>
                        <input type="hidden" name="rating" id="review-rating-input" value="" required>
                        <p class="text-xs text-red-500 mt-1 hidden" id="review-rating-error">Please select a rating.</p>
                    </div>
                    <input type="hidden" name="role" id="review-role" value="{{ e($reviewRole ?? '') }}">
                    <input type="hidden" name="location" id="review-location" value="{{ e($reviewLocation ?? '') }}">
                    <input type="hidden" name="employment_status" id="review-employment-status" value="{{ e($reviewEmploymentStatus ?? '') }}">
                    <div>
                        <label for="review-good-things" class="block text-sm font-medium text-gray-700 mb-1">The good things</label>
                        <textarea name="good_things" id="review-good-things" rows="3" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-gray-900 focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="What did you enjoy?"></textarea>
                    </div>
                    <div>
                        <label for="review-challenges" class="block text-sm font-medium text-gray-700 mb-1">The challenges</label>
                        <textarea name="challenges" id="review-challenges" rows="3" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-gray-900 focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="What could be improved?"></textarea>
                    </div>
                    <div class="flex gap-3 pt-2">
                        <button type="submit" id="add-review-submit" class="flex-1 px-4 py-2.5 rounded-lg bg-blue-600 text-white text-sm font-medium hover:bg-blue-700 transition">Submit review</button>
                        <button type="button" id="add-review-cancel" class="px-4 py-2.5 rounded-lg border border-gray-300 text-gray-700 text-sm font-medium hover:bg-gray-50 transition">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const tabs = document.querySelectorAll('.company-tab');
    const panels = document.querySelectorAll('.company-tab-panel');

    function activateTab(key) {
        tabs.forEach(btn => {
            const isActive = btn.dataset.tab === key;

            btn.classList.remove('bg-blue-50', 'text-blue-600', 'text-gray-500');

            if (isActive) {
                btn.classList.add('bg-blue-50', 'text-blue-600');
            } else {
                btn.classList.add('text-gray-500');
            }

            const badge = btn.querySelector('[data-tab-badge]');
            if (badge) {
                badge.classList.remove('bg-blue-100', 'text-blue-600', 'bg-gray-100', 'text-gray-500');
                if (isActive) {
                    badge.classList.add('bg-blue-100', 'text-blue-600');
                } else {
                    badge.classList.add('bg-gray-100', 'text-gray-500');
                }
            }
        });

        panels.forEach(panel => {
            panel.classList.toggle('hidden', panel.id !== 'tab-' + key);
        });
    }

    tabs.forEach(btn => {
        btn.addEventListener('click', () => activateTab(btn.dataset.tab));
    });

    activateTab('profile');

    // Jobs tab: list vs grid view toggle
    const jobsContainer = document.getElementById('company-jobs-list');
    const viewListBtn = document.getElementById('company-jobs-view-list');
    const viewGridBtn = document.getElementById('company-jobs-view-grid');

    if (jobsContainer && viewListBtn && viewGridBtn) {
        function setJobsView(view) {
            jobsContainer.dataset.view = view;
            jobsContainer.classList.remove('space-y-4', 'grid', 'grid-cols-1', 'md:grid-cols-2', 'gap-4');
            if (view === 'list') {
                jobsContainer.classList.add('space-y-4');
                viewListBtn.classList.add('bg-blue-600', 'text-white');
                viewListBtn.classList.remove('bg-white', 'text-gray-500');
                viewGridBtn.classList.add('bg-white', 'text-gray-500');
                viewGridBtn.classList.remove('bg-blue-600', 'text-white');
            } else {
                jobsContainer.classList.add('grid', 'grid-cols-1', 'md:grid-cols-2', 'gap-4');
                viewGridBtn.classList.add('bg-blue-600', 'text-white');
                viewGridBtn.classList.remove('bg-white', 'text-gray-500');
                viewListBtn.classList.add('bg-white', 'text-gray-500');
                viewListBtn.classList.remove('bg-blue-600', 'text-white');
            }
        }

        viewListBtn.addEventListener('click', function () { setJobsView('list'); });
        viewGridBtn.addEventListener('click', function () { setJobsView('grid'); });
    }

    // Reviews sort (client-side)
    const reviewsSort = document.getElementById('reviews-sort');
    const reviewsList = document.getElementById('reviews-list');
    if (reviewsSort && reviewsList) {
        const cards = Array.from(reviewsList.querySelectorAll('.review-card'));
        reviewsSort.addEventListener('change', function () {
            const order = reviewsSort.value;
            cards.sort(function (a, b) {
                const ratingA = parseInt(a.dataset.rating || '0', 10);
                const ratingB = parseInt(b.dataset.rating || '0', 10);
                const createdA = parseInt(a.dataset.created || '0', 10);
                const createdB = parseInt(b.dataset.created || '0', 10);
                if (order === 'highest') return ratingB - ratingA;
                if (order === 'lowest') return ratingA - ratingB;
                return createdB - createdA; // recent first
            });
            cards.forEach(function (card) { reviewsList.appendChild(card); });
        });
    }

    // Add Review: open modal only if job seeker; otherwise prompt login
    const addReviewBtn = document.getElementById('add-review-btn');
    const addReviewModal = document.getElementById('add-review-modal');
    const addReviewForm = document.getElementById('add-review-form');
    const reviewRatingInput = document.getElementById('review-rating-input');
    const reviewRatingError = document.getElementById('review-rating-error');
    const reviewStars = document.querySelectorAll('.review-star');

    function openAddReviewModal() {
        if (!addReviewModal) return;
        addReviewModal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeAddReviewModal() {
        if (!addReviewModal) return;
        addReviewModal.classList.add('hidden');
        document.body.style.overflow = '';
    }

    if (addReviewBtn) {
        addReviewBtn.addEventListener('click', function () {
            const canAdd = addReviewBtn.getAttribute('data-can-add') === '1';
            if (!canAdd) {
                // Not logged in: show login modal and remember to come back and open review form
                sessionStorage.setItem('loginRedirect', window.location.pathname + '?open_review=1');
                if (typeof openAuthModal === 'function') {
                    openAuthModal('login');
                } else {
                    alert('Please log in as a job seeker to submit a review.');
                }
                return;
            }
            openAddReviewModal();
        });
    }

    // If we landed here after login with open_review=1, open the Add Review modal
    (function () {
        const params = new URLSearchParams(window.location.search);
        if (params.get('open_review') === '1' && addReviewBtn && addReviewBtn.getAttribute('data-can-add') === '1') {
            openAddReviewModal();
            if (window.history && window.history.replaceState) {
                window.history.replaceState({}, document.title, window.location.pathname);
            }
        }
    })();

    document.getElementById('add-review-modal-close')?.addEventListener('click', closeAddReviewModal);
    document.getElementById('add-review-modal-backdrop')?.addEventListener('click', closeAddReviewModal);
    document.getElementById('add-review-cancel')?.addEventListener('click', closeAddReviewModal);

    // Star rating in modal
    let selectedRating = 0;
    reviewStars.forEach(function (star) {
        star.addEventListener('click', function () {
            selectedRating = parseInt(star.getAttribute('data-rating'), 10);
            reviewRatingInput.value = selectedRating;
            if (reviewRatingError) reviewRatingError.classList.add('hidden');
            reviewStars.forEach(function (s, i) {
                const r = i + 1;
                s.classList.remove('text-yellow-400', 'text-gray-200');
                s.classList.add(r <= selectedRating ? 'text-yellow-400' : 'text-gray-200');
            });
        });
    });

    if (addReviewForm) {
        addReviewForm.addEventListener('submit', function (e) {
            e.preventDefault();
            if (!reviewRatingInput.value) {
                if (reviewRatingError) reviewRatingError.classList.remove('hidden');
                return;
            }
            const submitBtn = document.getElementById('add-review-submit');
            const url = addReviewForm.getAttribute('data-action');
            const formData = new FormData(addReviewForm);
            submitBtn.disabled = true;
            submitBtn.textContent = 'Submitting…';

            fetch(url, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
                credentials: 'same-origin',
            })
                .then(function (res) {
                    return res.json().then(function (data) {
                        if (!res.ok) {
                            throw new Error(data.message || 'Something went wrong.');
                        }
                        return data;
                    });
                })
                .then(function (data) {
                    closeAddReviewModal();
                    addReviewForm.reset();
                    selectedRating = 0;
                    reviewRatingInput.value = '';
                    reviewStars.forEach(function (s) {
                        s.classList.remove('text-yellow-400');
                        s.classList.add('text-gray-200');
                    });
                    if (data.review && reviewsList) {
                        var r = data.review;
                        var starsHtml = '';
                        for (var i = 1; i <= 5; i++) {
                            starsHtml += '<svg class="w-4 h-4 ' + (i <= r.rating ? 'text-yellow-400' : 'text-gray-200') + '" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>';
                        }
                        var goodThings = (r.good_things || '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
                        var challenges = (r.challenges || '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
                        var card = document.createElement('div');
                        card.className = 'review-card rounded-xl border border-gray-100 bg-white p-5 shadow-sm';
                        card.dataset.rating = r.rating;
                        card.dataset.created = Math.floor(Date.now() / 1000);
                        card.innerHTML = '<div class="flex items-start justify-between gap-3 mb-3"><div class="flex items-center gap-2"><span class="text-lg font-bold text-gray-900">' + r.rating + '.0</span><div class="flex items-center gap-0.5">' + starsHtml + '</div></div><span class="text-xs text-gray-500">' + (r.created_at || '') + '</span></div><p class="text-sm font-semibold text-gray-900">' + (r.role ? r.role.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;') : 'Former employee') + '</p><p class="text-xs text-gray-500 mt-0.5">' + (r.location || '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;') + '</p><p class="text-xs text-gray-500">' + (r.employment_status || '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;') + '</p>' + (r.good_things ? '<p class="text-xs font-semibold text-gray-700 mt-3">The good things</p><p class="text-sm text-gray-600 mt-0.5">' + goodThings + '</p>' : '') + (r.challenges ? '<p class="text-xs font-semibold text-gray-700 mt-3">The challenges</p><p class="text-sm text-gray-600 mt-0.5">' + challenges + '</p>' : '') + '<div class="flex items-center gap-4 mt-4 pt-3 border-t border-gray-100 text-xs text-gray-500"><button type="button" class="inline-flex items-center gap-1 hover:text-blue-600 transition">Helpful?</button><button type="button" class="hover:text-red-600 transition">Report</button></div>';
                        var emptyMsg = reviewsList.querySelector('p.text-gray-500');
                        if (emptyMsg && emptyMsg.textContent.indexOf('No reviews yet') !== -1) emptyMsg.remove();
                        reviewsList.insertBefore(card, reviewsList.firstChild);
                        var tabBadge = document.querySelector('[data-tab-badge="reviews"]');
                        if (tabBadge) { var n = parseInt(tabBadge.textContent, 10) || 0; tabBadge.textContent = n + 1; }
                        var reviewsHeader = document.querySelector('#tab-reviews h2 span');
                        if (reviewsHeader) { var m = parseInt(reviewsHeader.textContent, 10) || 0; reviewsHeader.textContent = m + 1; }
                    } else {
                        window.location.reload();
                    }
                })
                .catch(function (err) {
                    alert(err.message || 'Could not submit review. Please try again.');
                })
                .finally(function () {
                    submitBtn.disabled = false;
                    submitBtn.textContent = 'Submit review';
                });
        });
    }
});
</script>
@endpush
@endsection
