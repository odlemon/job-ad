@extends('layouts.app')

@section('title', $company->name)

@section('content')
@php
    $mediaBaseUrl = rtrim($mediaBaseUrl ?? app(\App\Services\RemoteUploadService::class)->getMediaBaseUrl(), '/');
    $abs = function (?string $path) use ($mediaBaseUrl) {
        if (! $path) return null;
        return str_starts_with($path, 'http') ? $path : $mediaBaseUrl . '/' . ltrim($path, '/');
    };
    $coverUrl = $abs($company->cover_image);
    $logoUrl = $abs($company->logo);

    $avgRating = (float) ($avgRating ?? 0);
    $reviewsCount = (int) ($reviewsCount ?? 0);
    $filledStars = (int) floor($avgRating);
    $hasHalfStar = ($avgRating - $filledStars) >= 0.5;

    $toneMap = [
        'blue' => ['wrap' => 'bg-blue-50 dark:bg-blue-900/10', 'icon' => 'bg-blue-100 dark:bg-blue-900/30', 'iconText' => 'text-blue-600 dark:text-blue-400'],
        'green' => ['wrap' => 'bg-green-50 dark:bg-green-900/10', 'icon' => 'bg-green-100 dark:bg-green-900/30', 'iconText' => 'text-green-600 dark:text-green-400'],
        'purple' => ['wrap' => 'bg-purple-50 dark:bg-purple-900/10', 'icon' => 'bg-purple-100 dark:bg-purple-900/30', 'iconText' => 'text-purple-600 dark:text-purple-400'],
        'orange' => ['wrap' => 'bg-orange-50 dark:bg-orange-900/10', 'icon' => 'bg-orange-100 dark:bg-orange-900/30', 'iconText' => 'text-orange-600 dark:text-orange-400'],
        'cyan' => ['wrap' => 'bg-cyan-50 dark:bg-cyan-900/10', 'icon' => 'bg-cyan-100 dark:bg-cyan-900/30', 'iconText' => 'text-cyan-600 dark:text-cyan-400'],
        'pink' => ['wrap' => 'bg-pink-50 dark:bg-pink-900/10', 'icon' => 'bg-pink-100 dark:bg-pink-900/30', 'iconText' => 'text-pink-600 dark:text-pink-400'],
    ];
    $tones = array_keys($toneMap);

    $benefits = is_array($company->benefits) ? $company->benefits : [];
    if (count($benefits) === 0 && $company->culture_benefits) {
        $raw = $company->culture_benefits;
        $labels = is_array($raw) ? $raw : (json_decode((string) $raw, true) ?: array_filter(array_map('trim', explode(',', (string) $raw))));
        foreach ($labels as $label) {
            if (is_string($label) && $label !== '') $benefits[] = ['title' => $label, 'description' => ''];
            elseif (is_array($label) && !empty($label['title'])) $benefits[] = $label;
        }
    }

    $values = is_array($company->company_values) ? $company->company_values : [];
    $valueGradients = [
        'from-blue-50 to-cyan-50 dark:from-blue-900/20 dark:to-cyan-900/20',
        'from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20',
        'from-purple-50 to-pink-50 dark:from-purple-900/20 dark:to-pink-900/20',
    ];

    $faqs = is_array($company->faqs) ? $company->faqs : [];
    $gallery = collect($company->gallery_images ?? [])->map(fn ($img) => is_string($img) ? $abs($img) : $abs($img['url'] ?? null))->filter()->values();

    $cultureItems = [];
    $rawCulture = $company->culture_benefits;
    if (is_string($rawCulture) && str_starts_with(trim($rawCulture), '[')) {
        $decoded = json_decode($rawCulture, true);
        if (is_array($decoded)) $cultureItems = $decoded;
    } elseif (is_array($rawCulture)) {
        $cultureItems = $rawCulture;
    }
    if (count($cultureItems) === 0) {
        $cultureItems = [
            ['title' => 'Collaborative Environment', 'description' => "We work together, share knowledge, and support each other's growth"],
            ['title' => 'Innovation-Driven', 'description' => 'We encourage creative thinking and welcome new ideas from everyone'],
            ['title' => 'Inclusive & Diverse', 'description' => 'We celebrate diversity and create opportunities for all backgrounds'],
            ['title' => 'Results-Oriented', 'description' => 'We focus on delivering impactful outcomes while maintaining quality standards'],
        ];
    }

    $jobsTotal = (int) ($jobsMeta['total'] ?? $company->job_advertisements_count ?? $openJobs->count());
    $starDistribution = $starDistribution ?? [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0];
    $maxBar = max(1, max($starDistribution));
    $jobTypes = $openJobs->pluck('employment_type')->filter()->unique()->values();
    $eduLevels = $openJobs->pluck('education_level')->filter()->unique()->values();

    $websiteHref = $company->website
        ? (str_starts_with($company->website, 'http') ? $company->website : 'https://'.$company->website)
        : null;
@endphp

<div id="company-detail-page"
     class="min-h-screen bg-gray-50 dark:bg-gray-900"
     data-company-id="{{ $company->id }}"
     data-company-slug="{{ $company->slug }}"
     data-company-name="{{ e($company->name) }}"
     data-company-email="{{ e($company->email ?? '') }}"
     data-jobs-api="{{ url('/api/public/companies/' . ($company->slug ?: $company->id) . '/jobs') }}"
     data-reviews-api="{{ url('/api/public/companies/' . ($company->slug ?: $company->id) . '/reviews') }}"
     data-follow-url="{{ route('companies.follow', $company) }}"
     data-unfollow-url="{{ route('companies.unfollow', $company) }}"
     data-is-following="{{ !empty($isFollowing) ? '1' : '0' }}"
     data-is-seeker="{{ !empty($isAuthenticatedSeeker) ? '1' : '0' }}"
     data-jobs-last-page="{{ $jobsMeta['last_page'] ?? 1 }}"
     data-reviews-last-page="{{ $reviewsMeta['last_page'] ?? 1 }}">

    {{-- Banner --}}
    <div class="relative h-64 bg-gradient-to-r from-blue-600 to-cyan-500 overflow-hidden">
        @if($coverUrl)
            <img src="{{ $coverUrl }}" alt="{{ $company->name }}" class="w-full h-full object-cover">
        @else
            <div class="w-full h-full bg-gradient-to-r from-blue-600 to-cyan-500"></div>
        @endif
        <div class="absolute inset-0 bg-black/20"></div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Header card --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg -mt-20 relative z-10 p-6">
            <div class="flex flex-col md:flex-row gap-6">
                <div class="flex-shrink-0">
                    <div class="w-32 h-32 bg-white dark:bg-gray-700 rounded-xl shadow-lg flex items-center justify-center text-6xl border-4 border-white dark:border-gray-800 overflow-hidden">
                        @if($logoUrl)
                            <img src="{{ $logoUrl }}" alt="{{ $company->name }}" class="w-full h-full object-cover">
                        @else
                            <span class="text-5xl font-bold text-blue-600 dark:text-cyan-400">{{ strtoupper(mb_substr($company->name, 0, 1)) }}</span>
                        @endif
                    </div>
                </div>

                <div class="flex-1">
                    <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4 mb-4">
                        <div>
                            <div class="flex items-center gap-3 mb-2 flex-wrap">
                                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">{{ $company->name }}</h1>
                                @if($company->verified_at)
                                    <span class="px-3 py-1 bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 text-xs font-semibold rounded-full">Verified</span>
                                @endif
                            </div>

                            <div class="flex flex-wrap items-center gap-3 text-sm text-gray-600 dark:text-gray-400 mb-3">
                                @if($company->registration_number)
                                    <span>Company SSM No {{ $company->registration_number }}</span>
                                @endif
                                @if($company->size)
                                    <span>•</span>
                                    <span>{{ $company->size }} Employees</span>
                                @endif
                                @if($company->industry)
                                    <span>•</span>
                                    <span>{{ $company->industry }}</span>
                                @endif
                            </div>

                            <div class="flex items-center gap-4 mb-3">
                                <div class="flex items-center gap-2">
                                    <div class="flex items-center gap-1">
                                        @for($i = 1; $i <= 5; $i++)
                                            @if($i <= $filledStars)
                                                <svg class="w-4 h-4 fill-yellow-400 text-yellow-400" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                            @elseif($i === $filledStars + 1 && $hasHalfStar)
                                                <svg class="w-4 h-4 fill-yellow-400 text-yellow-400 opacity-50" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                            @else
                                                <svg class="w-4 h-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                                            @endif
                                        @endfor
                                    </div>
                                    <span class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($avgRating, 1) }}</span>
                                    <span class="text-sm text-gray-500 dark:text-gray-400">({{ $reviewsCount }} {{ $reviewsCount === 1 ? 'review' : 'reviews' }})</span>
                                </div>
                            </div>

                            <div class="flex items-center gap-4 text-sm text-gray-600 dark:text-gray-400">
                                <div class="flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    <span class="font-semibold" id="followers-count">{{ number_format($company->followers_count ?? 0) }}</span>
                                    <span>Followers</span>
                                </div>
                                <div class="flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m8 0H8m8 0a2 2 0 012 2v6a2 2 0 01-2 2H8a2 2 0 01-2-2V8a2 2 0 012-2"/></svg>
                                    <span class="font-semibold">{{ $jobsTotal }}</span>
                                    <span>Job {{ \Illuminate\Support\Str::plural('Vacancy', $jobsTotal) }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="flex flex-wrap gap-3">
                            <button type="button" id="send-cv-btn" class="px-6 py-2.5 bg-gradient-to-r from-blue-600 to-cyan-500 text-white font-medium rounded-lg hover:shadow-lg transition-all duration-200 flex items-center gap-2">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z"/></svg>
                                Send CV
                            </button>
                            <button type="button" id="follow-btn" class="px-6 py-2.5 font-medium rounded-lg border-2 transition-all duration-200 flex items-center gap-2 {{ !empty($isFollowing) ? 'bg-blue-50 dark:bg-blue-900/20 border-blue-600 text-blue-600 dark:text-blue-400' : 'bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:border-blue-600 dark:hover:border-cyan-400' }}">
                                <svg id="follow-heart" class="w-4 h-4 {{ !empty($isFollowing) ? 'fill-current' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                                <span id="follow-btn-label">{{ !empty($isFollowing) ? 'Following' : 'Follow' }}</span>
                            </button>
                            <button type="button" id="share-btn" class="px-4 py-2.5 bg-white dark:bg-gray-700 border-2 border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 font-medium rounded-lg hover:border-blue-600 dark:hover:border-cyan-400 transition-all duration-200" title="Share">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/></svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="border-t border-gray-200 dark:border-gray-700 mt-6 pt-4">
                <nav class="flex gap-1 overflow-x-auto">
                    @foreach([
                        'profile' => ['label' => 'Profile', 'count' => null],
                        'workplace' => ['label' => 'Our Workplace', 'count' => null],
                        'jobs' => ['label' => 'Jobs', 'count' => $jobsTotal],
                        'reviews' => ['label' => 'Reviews', 'count' => $reviewsCount],
                        'photos' => ['label' => 'Photos', 'count' => null],
                        'qa' => ['label' => 'Q&A', 'count' => null],
                    ] as $key => $tab)
                        <button type="button" data-company-tab="{{ $key }}"
                                class="company-tab px-6 py-3 font-medium text-sm rounded-lg transition-colors whitespace-nowrap {{ $key === 'profile' ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400' : 'text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-cyan-400 hover:bg-gray-50 dark:hover:bg-gray-700' }}">
                            {{ $tab['label'] }}
                            @if($tab['count'] !== null && $tab['count'] > 0)
                                <span class="ml-2 px-2 py-0.5 bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-300 text-xs rounded-full">{{ $tab['count'] }}</span>
                            @endif
                        </button>
                    @endforeach
                </nav>
            </div>
        </div>

        {{-- Tab content card --}}
        <div class="mt-6 pb-12">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">

                {{-- PROFILE --}}
                <div id="tab-profile" class="company-tab-panel">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Company Profile</h2>
                    <div class="grid md:grid-cols-2 gap-6 mb-8">
                        @if($company->location)
                        <div class="flex items-start gap-3">
                            <div class="p-2 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                                <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            </div>
                            <div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">Location</div>
                                <div class="font-semibold text-gray-900 dark:text-white">{{ $company->location }}</div>
                            </div>
                        </div>
                        @endif
                        @if($company->website)
                        <div class="flex items-start gap-3">
                            <div class="p-2 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                                <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/></svg>
                            </div>
                            <div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">Website</div>
                                <a href="{{ $websiteHref }}" target="_blank" rel="noopener" class="font-semibold text-blue-600 dark:text-blue-400 hover:underline break-all">{{ $company->website }}</a>
                            </div>
                        </div>
                        @endif
                        @if($company->email)
                        <div class="flex items-start gap-3">
                            <div class="p-2 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                                <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            </div>
                            <div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">Company Email</div>
                                <a href="mailto:{{ $company->email }}" class="font-semibold text-gray-900 dark:text-white break-all">{{ $company->email }}</a>
                            </div>
                        </div>
                        @endif
                        <div class="flex items-start gap-3">
                            <div class="p-2 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                                <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                            </div>
                            <div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">Company Size</div>
                                <div class="font-semibold text-gray-900 dark:text-white">{{ $company->size ?: 'Not specified' }}</div>
                            </div>
                        </div>
                        @if($company->phone)
                        <div class="flex items-start gap-3">
                            <div class="p-2 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                                <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                            </div>
                            <div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">Contact Number</div>
                                <div class="font-semibold text-gray-900 dark:text-white">{{ $company->phone }}</div>
                            </div>
                        </div>
                        @endif
                        @if($company->working_hours)
                        <div class="flex items-start gap-3">
                            <div class="p-2 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                                <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">Work Hours</div>
                                <div class="font-semibold text-gray-900 dark:text-white">{{ $company->working_hours }}</div>
                            </div>
                        </div>
                        @endif
                    </div>

                    @if($company->linkedin || $company->twitter || $company->facebook || $company->instagram)
                    <div class="mb-8">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Connect With Us</h3>
                        <div class="flex gap-3">
                            @if($company->linkedin)
                            <a href="{{ $company->linkedin }}" target="_blank" rel="noopener" class="flex items-center justify-center w-12 h-12 bg-blue-50 dark:bg-blue-900/20 hover:bg-blue-100 dark:hover:bg-blue-900/30 rounded-lg transition-colors" aria-label="LinkedIn">
                                <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="currentColor" viewBox="0 0 24 24"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 01-2.063-2.065 2.064 2.064 0 112.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
                            </a>
                            @endif
                            @if($company->twitter)
                            <a href="{{ $company->twitter }}" target="_blank" rel="noopener" class="flex items-center justify-center w-12 h-12 bg-sky-50 dark:bg-sky-900/20 hover:bg-sky-100 dark:hover:bg-sky-900/30 rounded-lg transition-colors" aria-label="Twitter">
                                <svg class="w-6 h-6 text-sky-600 dark:text-sky-400" fill="currentColor" viewBox="0 0 24 24"><path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723 10.1 10.1 0 01-3.127 1.184 4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/></svg>
                            </a>
                            @endif
                            @if($company->facebook)
                            <a href="{{ $company->facebook }}" target="_blank" rel="noopener" class="flex items-center justify-center w-12 h-12 bg-blue-50 dark:bg-blue-900/20 hover:bg-blue-100 dark:hover:bg-blue-900/30 rounded-lg transition-colors" aria-label="Facebook">
                                <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                            </a>
                            @endif
                            @if($company->instagram)
                            <a href="{{ $company->instagram }}" target="_blank" rel="noopener" class="flex items-center justify-center w-12 h-12 bg-pink-50 dark:bg-pink-900/20 hover:bg-pink-100 dark:hover:bg-pink-900/30 rounded-lg transition-colors" aria-label="Instagram">
                                <svg class="w-6 h-6 text-pink-600 dark:text-pink-400" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
                            </a>
                            @endif
                        </div>
                    </div>
                    @endif

                    <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-4">About {{ $company->name }}</h3>
                        <p class="text-gray-700 dark:text-gray-300 leading-relaxed whitespace-pre-line">{{ $company->description ?: 'No description available for this company.' }}</p>
                    </div>
                </div>

                {{-- WORKPLACE --}}
                <div id="tab-workplace" class="company-tab-panel hidden space-y-8">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Our Workplace</h2>
                        <p class="text-gray-700 dark:text-gray-300 leading-relaxed">
                            {{ $company->workplace_description ?: "Discover what makes {$company->name} a great place to work. We're committed to creating an environment where our team members can thrive, grow, and make meaningful contributions." }}
                        </p>
                    </div>

                    @if(count($benefits) > 0)
                    <div class="border-t border-gray-200 dark:border-gray-700 pt-8">
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-6">Perks & Benefits</h3>
                        <div class="grid md:grid-cols-2 gap-4">
                            @foreach($benefits as $bi => $b)
                                @php
                                    $title = is_array($b) ? ($b['title'] ?? 'Benefit') : (string) $b;
                                    $desc = is_array($b) ? ($b['description'] ?? '') : '';
                                    $tone = is_array($b) && !empty($b['tone']) ? $b['tone'] : $tones[$bi % count($tones)];
                                    $t = $toneMap[$tone] ?? $toneMap['blue'];
                                @endphp
                                <div class="flex items-start gap-3 p-4 {{ $t['wrap'] }} rounded-lg">
                                    <div class="p-2 {{ $t['icon'] }} rounded-lg">
                                        <svg class="w-5 h-5 {{ $t['iconText'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    </div>
                                    <div>
                                        <h4 class="font-semibold text-gray-900 dark:text-white mb-1">{{ $title }}</h4>
                                        @if($desc)<p class="text-sm text-gray-600 dark:text-gray-400">{{ $desc }}</p>@endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    @if(count($values) > 0)
                    <div class="border-t border-gray-200 dark:border-gray-700 pt-8">
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-6">Company Values</h3>
                        <div class="grid md:grid-cols-3 gap-6">
                            @foreach($values as $vi => $v)
                                @php
                                    $title = is_array($v) ? ($v['title'] ?? 'Value') : (string) $v;
                                    $desc = is_array($v) ? ($v['description'] ?? '') : '';
                                    $grad = $valueGradients[$vi % 3];
                                    $iconTone = ['text-blue-600 dark:text-blue-400','text-green-600 dark:text-green-400','text-purple-600 dark:text-purple-400'][$vi % 3];
                                    $iconBg = ['bg-blue-100 dark:bg-blue-900/30','bg-green-100 dark:bg-green-900/30','bg-purple-100 dark:bg-purple-900/30'][$vi % 3];
                                @endphp
                                <div class="text-center p-6 bg-gradient-to-br {{ $grad }} rounded-lg">
                                    <div class="inline-flex p-3 {{ $iconBg }} rounded-full mb-4">
                                        <svg class="w-8 h-8 {{ $iconTone }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
                                    </div>
                                    <h4 class="font-bold text-gray-900 dark:text-white mb-2">{{ $title }}</h4>
                                    @if($desc)<p class="text-sm text-gray-600 dark:text-gray-400">{{ $desc }}</p>@endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <div class="border-t border-gray-200 dark:border-gray-700 pt-8">
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Who We Are</h3>
                        <p class="text-gray-700 dark:text-gray-300 leading-relaxed mb-4">
                            At {{ $company->name }}, we're more than just a company – we're a community of passionate individuals dedicated to making a difference. Our diverse team brings together unique perspectives, skills, and experiences to create innovative solutions and deliver exceptional results.
                        </p>
                        <p class="text-gray-700 dark:text-gray-300 leading-relaxed">
                            We believe in empowering our employees to reach their full potential while maintaining a collaborative and inclusive work environment. Every team member plays a vital role in our success.
                        </p>
                    </div>

                    <div class="border-t border-gray-200 dark:border-gray-700 pt-8">
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Team Culture</h3>
                        <p class="text-gray-700 dark:text-gray-300 leading-relaxed mb-4">
                            Our culture is built on mutual respect, open communication, and a shared commitment to excellence. We foster an environment where everyone feels valued, heard, and inspired to contribute their best work.
                        </p>
                        <div class="grid md:grid-cols-2 gap-4 mt-6">
                            @foreach($cultureItems as $item)
                                @php
                                    $ct = is_array($item) ? ($item['title'] ?? '') : '';
                                    $cd = is_array($item) ? ($item['description'] ?? '') : (string) $item;
                                @endphp
                                <div class="flex items-start gap-3">
                                    <div class="flex-shrink-0 w-2 h-2 bg-blue-600 dark:bg-blue-400 rounded-full mt-2"></div>
                                    <p class="text-gray-700 dark:text-gray-300">
                                        @if($ct)<strong class="text-gray-900 dark:text-white">{{ $ct }}:</strong> @endif{{ $cd }}
                                    </p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- JOBS --}}
                <div id="tab-jobs" class="company-tab-panel hidden">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Available Positions</h2>
                    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 p-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                            <span class="text-gray-900 dark:text-white font-medium">Stay updated on new opportunities at {{ $company->name }}</span>
                        </div>
                        <button type="button" id="jobs-alert-btn" class="px-4 py-2 rounded-lg font-medium transition-colors bg-blue-600 text-white hover:bg-blue-700">Enable Alerts</button>
                    </div>

                    <div class="mb-6 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                        <p class="text-gray-600 dark:text-gray-400"><span id="jobs-visible-count">{{ $openJobs->count() }}</span> job {{ $openJobs->count() === 1 ? 'position' : 'positions' }} available</p>
                        <div class="flex flex-wrap items-center gap-3">
                            <div class="flex items-center gap-2">
                                <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Job Type:</label>
                                <select id="company-jobs-type-filter" class="px-3 py-1.5 text-sm rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300">
                                    <option value="">All</option>
                                    @foreach($jobTypes as $type)<option value="{{ $type }}">{{ $type }}</option>@endforeach
                                </select>
                            </div>
                            <div class="flex items-center gap-2">
                                <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Education:</label>
                                <select id="company-jobs-education-filter" class="px-3 py-1.5 text-sm rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300">
                                    <option value="">All</option>
                                    @foreach($eduLevels as $edu)<option value="{{ $edu }}">{{ $edu }}</option>@endforeach
                                </select>
                            </div>
                            <div class="flex items-center gap-2">
                                <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Sort:</label>
                                <select id="company-jobs-sort" class="px-3 py-1.5 text-sm rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300">
                                    <option value="default">Default</option>
                                    <option value="a-z">A to Z</option>
                                    <option value="z-a">Z to A</option>
                                </select>
                            </div>
                            <div class="flex items-center gap-2">
                                <button type="button" id="company-jobs-view-list" class="p-2 rounded-lg bg-blue-600 text-white" aria-label="List view">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h10M4 18h6"/></svg>
                                </button>
                                <button type="button" id="company-jobs-view-grid" class="p-2 rounded-lg bg-gray-200 dark:bg-gray-700 text-gray-600 dark:text-gray-400" aria-label="Card view">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h6v6H4zM14 6h6v6h-6zM4 14h6v6H4zM14 14h6v6h-6z"/></svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div id="company-jobs-list" class="space-y-4" data-view="list">
                        @forelse($openJobs as $job)
                            @php
                                $salaryText = null;
                                if ($job->salary_min && $job->salary_max && !($job->hide_salary ?? false)) {
                                    $salaryText = ($job->currency ?: 'SCR') . ' ' . number_format($job->salary_min) . ' - ' . number_format($job->salary_max);
                                }
                            @endphp
                            <div class="company-job-card bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 hover:shadow-lg transition-shadow overflow-hidden"
                                 data-title="{{ strtolower($job->title) }}"
                                 data-job-type="{{ strtolower($job->employment_type ?? '') }}"
                                 data-education="{{ strtolower($job->education_level ?? '') }}">
                                <div class="p-5">
                                    <div class="flex items-start justify-between gap-4 mb-2">
                                        <a href="{{ route('jobs.show', $job->id) }}" wire:navigate class="text-lg font-semibold text-pink-600 dark:text-pink-400 hover:text-pink-700 dark:hover:text-pink-300">{{ $job->title }}</a>
                                    </div>
                                    @if($salaryText)
                                        <div class="text-pink-600 dark:text-pink-400 font-semibold text-sm mb-3">{{ $salaryText }}</div>
                                    @endif
                                    <p class="text-gray-600 dark:text-gray-400 text-sm mb-3 line-clamp-2">{{ \Illuminate\Support\Str::limit(strip_tags($job->description ?? ''), 140) }}</p>
                                    <div class="flex flex-wrap gap-2 mb-3">
                                        @if($job->location)
                                            <span class="px-3 py-1 bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 text-gray-700 dark:text-gray-300 text-xs rounded-full">{{ $job->location }}</span>
                                        @endif
                                        @if($job->employment_type)
                                            <span class="px-3 py-1 bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 text-gray-700 dark:text-gray-300 text-xs rounded-full">{{ $job->employment_type }}</span>
                                        @endif
                                        @if($job->category)
                                            <span class="px-3 py-1 bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 text-gray-700 dark:text-gray-300 text-xs rounded-full">{{ $job->category->name }}</span>
                                        @endif
                                    </div>
                                </div>
                                <a href="{{ route('jobs.show', $job->id) }}" data-apply-job wire:navigate class="company-job-apply block w-full bg-blue-600 hover:bg-blue-700 text-center text-sm font-semibold text-white py-2.5">Apply Now</a>
                            </div>
                        @empty
                            <p class="text-gray-600 dark:text-gray-400" id="jobs-empty">This company currently has no open positions.</p>
                        @endforelse
                    </div>
                    <div class="mt-6 text-center">
                        <button type="button" id="jobs-load-more" class="px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 {{ ($jobsMeta['last_page'] ?? 1) > 1 ? '' : 'hidden' }}">Load more jobs</button>
                    </div>
                </div>

                {{-- REVIEWS --}}
                <div id="tab-reviews" class="company-tab-panel hidden">
                    <div class="flex flex-wrap items-center justify-between gap-3 mb-6">
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Reviews</h2>
                        <div class="flex items-center gap-2">
                            <select id="reviews-sort" class="text-sm border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-1.5 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200">
                                <option value="newest">Newest</option>
                                <option value="highest">Highest rating</option>
                                <option value="lowest">Lowest rating</option>
                                <option value="helpful">Most helpful</option>
                            </select>
                            @if(!empty($canAddReview))
                                <button type="button" id="open-review-modal" class="px-3 py-1.5 rounded-lg bg-blue-600 text-white text-sm font-medium hover:bg-blue-700">Add review</button>
                            @elseif(!auth()->check())
                                <button type="button" id="guest-review-btn" class="px-3 py-1.5 rounded-lg bg-blue-600 text-white text-sm font-medium hover:bg-blue-700">Add review</button>
                            @endif
                        </div>
                    </div>

                    <div class="rounded-xl border border-gray-200 dark:border-gray-700 p-6 mb-6">
                        <div class="flex flex-col lg:flex-row gap-8">
                            <div class="lg:w-1/2">
                                <div class="text-center mb-4">
                                    <span class="text-4xl font-bold text-gray-900 dark:text-white">{{ number_format($avgRating, 1) }}</span>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ $reviewsCount }} ratings in total</p>
                                </div>
                                <div class="space-y-2">
                                    @foreach([5,4,3,2,1] as $stars)
                                    <div class="flex items-center gap-3">
                                        <span class="text-xs w-4">{{ $stars }}</span>
                                        <div class="flex-1 h-3 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                                            <div class="h-full rounded-full bg-pink-500" style="width: {{ $maxBar > 0 ? (($starDistribution[$stars] ?? 0) / $maxBar) * 100 : 0 }}%"></div>
                                        </div>
                                        <span class="text-xs w-8 text-right">{{ $starDistribution[$stars] ?? 0 }}</span>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            @if(!empty($categoryLabels))
                            <div class="lg:w-1/2 grid sm:grid-cols-2 gap-4">
                                @foreach($categoryLabels as $key => $label)
                                    <div>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $label }}</p>
                                        <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ isset($categoryAverages[$key]) && $categoryAverages[$key] !== null ? number_format($categoryAverages[$key], 1) : '—' }}</p>
                                    </div>
                                @endforeach
                            </div>
                            @endif
                        </div>
                    </div>

                    <div id="company-reviews-list" class="space-y-4">
                        @forelse($reviews as $review)
                            <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 bg-gray-50 dark:bg-gray-700/50">
                                <div class="flex items-center gap-2 mb-2">
                                    <div class="flex text-yellow-400">
                                        @for($i = 1; $i <= 5; $i++)
                                            <svg class="w-3.5 h-3.5 {{ $i <= $review->rating ? 'fill-current' : 'text-gray-300 fill-current' }}" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                        @endfor
                                    </div>
                                    <span class="text-xs text-gray-500 dark:text-gray-400">{{ $review->created_at?->format('M Y') }}</span>
                                </div>
                                @if($review->role || $review->location || $review->employment_status)
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">{{ collect([$review->role, $review->location, $review->employment_status])->filter()->implode(' · ') }}</p>
                                @endif
                                @if($review->good_things)
                                    <p class="text-sm text-gray-700 dark:text-gray-300 mb-1"><span class="font-semibold">The good things:</span> {{ $review->good_things }}</p>
                                @endif
                                @if($review->challenges)
                                    <p class="text-sm text-gray-700 dark:text-gray-300"><span class="font-semibold">The challenges:</span> {{ $review->challenges }}</p>
                                @endif
                            </div>
                        @empty
                            <p class="text-gray-600 dark:text-gray-400" id="reviews-empty">No reviews yet.</p>
                        @endforelse
                    </div>
                    <div class="mt-6 text-center">
                        <button type="button" id="reviews-load-more" class="px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 {{ ($reviewsMeta['last_page'] ?? 1) > 1 ? '' : 'hidden' }}">Load more reviews</button>
                    </div>
                </div>

                {{-- PHOTOS --}}
                <div id="tab-photos" class="company-tab-panel hidden">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-3">Company Photos</h2>
                    @if($gallery->isNotEmpty())
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                            @foreach($gallery as $photo)
                                <img src="{{ $photo }}" alt="{{ $company->name }}" class="w-full h-40 object-cover rounded-lg" loading="lazy">
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12">
                            <div class="text-gray-400 text-6xl mb-4">📷</div>
                            <p class="text-gray-600 dark:text-gray-400">No photos available</p>
                        </div>
                    @endif
                </div>

                {{-- Q&A --}}
                <div id="tab-qa" class="company-tab-panel hidden">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-3">Questions & Answers</h2>
                    @if(count($faqs) > 0)
                        <div class="space-y-2.5">
                            @foreach($faqs as $entry)
                                @php
                                    $q = is_array($entry) ? ($entry['question'] ?? '') : '';
                                    $a = is_array($entry) ? ($entry['answer'] ?? '') : '';
                                @endphp
                                @if($q)
                                <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-3.5 bg-gray-50 dark:bg-gray-700/50 hover:shadow-md transition-shadow">
                                    <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-1.5">{{ $q }}</h3>
                                    <p class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed">{{ $a }}</p>
                                </div>
                                @endif
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12">
                            <div class="text-gray-400 text-6xl mb-4">❓</div>
                            <p class="text-gray-600 dark:text-gray-400">No Q&A available</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @if(!empty($canAddReview))
    <div id="add-review-modal" class="fixed inset-0 z-50 hidden" aria-modal="true" role="dialog">
        <div class="fixed inset-0 bg-black/50" id="add-review-modal-backdrop"></div>
        <div class="fixed inset-0 flex items-center justify-center p-4 overflow-y-auto">
            <div class="relative bg-white dark:bg-gray-800 rounded-xl shadow-xl max-w-lg w-full max-h-[90vh] overflow-y-auto">
                <div class="sticky top-0 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 px-6 py-4 flex items-center justify-between rounded-t-xl">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Add your review</h3>
                    <button type="button" id="add-review-modal-close" class="p-2 rounded-lg text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-700" aria-label="Close">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <form id="add-review-form" class="p-6 space-y-4" data-action="{{ route('companies.reviews.store', $company) }}">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Overall rating *</label>
                        <div class="flex items-center gap-1" id="review-rating-stars">
                            @for($i = 1; $i <= 5; $i++)
                                <button type="button" class="review-star p-0.5 text-gray-200 hover:text-yellow-400" data-rating="{{ $i }}">
                                    <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                </button>
                            @endfor
                        </div>
                        <input type="hidden" name="rating" id="review-rating-input" value="" required>
                    </div>
                    <input type="hidden" name="role" value="{{ e($reviewRole ?? '') }}">
                    <input type="hidden" name="location" value="{{ e($reviewLocation ?? '') }}">
                    <input type="hidden" name="employment_status" value="{{ e($reviewEmploymentStatus ?? '') }}">
                    <div>
                        <label for="review-good-things" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">The good things</label>
                        <textarea name="good_things" id="review-good-things" rows="3" class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 dark:bg-gray-900 dark:text-white"></textarea>
                    </div>
                    <div>
                        <label for="review-challenges" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">The challenges</label>
                        <textarea name="challenges" id="review-challenges" rows="3" class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 dark:bg-gray-900 dark:text-white"></textarea>
                    </div>
                    <button type="submit" class="w-full px-4 py-2.5 rounded-lg bg-blue-600 text-white text-sm font-medium hover:bg-blue-700">Submit review</button>
                </form>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
