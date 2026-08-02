@extends('layouts.employer')

@section('content')
<div class="min-h-screen bg-white dark:bg-gray-800">
    @include('partials.employer-navbar')

    <div class="flex">
        @include('partials.employer-sidebar')

        <main class="flex-1 p-6 ml-64 w-0 min-w-0">
            <div class="w-full">
                {{-- Back link --}}
                <a href="{{ route('employer.jobs.index') }}" class="inline-flex items-center text-blue-600 hover:text-blue-800 font-medium text-sm mb-2">
                    <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    Back to Job Listings
                </a>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $job->title }}</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Job Statistics & Performance</p>

                {{-- Six KPI cards (3x2), icon on right --}}
                <div class="grid grid-cols-2 md:grid-cols-3 gap-4 mt-6">
                    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 p-5 shadow-sm dark:shadow-none flex items-center justify-between" style="border-radius: 0;">
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Total Views</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white mt-0.5">{{ number_format($stats['views']) }}</p>
                        </div>
                        <div class="w-10 h-10 flex items-center justify-center flex-shrink-0" style="background-color: rgba(59, 130, 246, 0.15); border-radius: 4px;">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        </div>
                    </div>
                    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 p-5 shadow-sm dark:shadow-none flex items-center justify-between" style="border-radius: 0;">
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Applications</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white mt-0.5">{{ number_format($stats['applications']) }}</p>
                        </div>
                        <div class="w-10 h-10 flex items-center justify-center flex-shrink-0" style="background-color: rgba(34, 197, 94, 0.15); border-radius: 4px;">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        </div>
                    </div>
                    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 p-5 shadow-sm dark:shadow-none flex items-center justify-between" style="border-radius: 0;">
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Shares</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white mt-0.5">{{ number_format($stats['shares']) }}</p>
                        </div>
                        <div class="w-10 h-10 flex items-center justify-center flex-shrink-0" style="background-color: rgba(147, 51, 234, 0.15); border-radius: 4px;">
                            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/></svg>
                        </div>
                    </div>
                    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 p-5 shadow-sm dark:shadow-none flex items-center justify-between" style="border-radius: 0;">
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Messages</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white mt-0.5">{{ number_format($stats['messages']) }}</p>
                        </div>
                        <div class="w-10 h-10 flex items-center justify-center flex-shrink-0" style="background-color: rgba(234, 179, 8, 0.2); border-radius: 4px;">
                            <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                        </div>
                    </div>
                    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 p-5 shadow-sm dark:shadow-none flex items-center justify-between" style="border-radius: 0;">
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Saved by Users</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white mt-0.5">{{ number_format($stats['saved']) }}</p>
                        </div>
                        <div class="w-10 h-10 flex items-center justify-center flex-shrink-0" style="background-color: rgba(239, 68, 68, 0.15); border-radius: 4px;">
                            <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/></svg>
                        </div>
                    </div>
                    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 p-5 shadow-sm dark:shadow-none flex items-center justify-between" style="border-radius: 0;">
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Invitations Sent</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white mt-0.5">{{ number_format($stats['invitations_sent']) }}</p>
                        </div>
                        <div class="w-10 h-10 flex items-center justify-center flex-shrink-0" style="background-color: rgba(14, 165, 233, 0.15); border-radius: 4px;">
                            <svg class="w-5 h-5 text-sky-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                        </div>
                    </div>
                </div>

                {{-- Posted Date + Status (horizontal, icon on right) --}}
                <div class="flex flex-wrap gap-4 mt-4">
                    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 px-4 py-3 flex items-center justify-between flex-1 min-w-[200px]" style="border-radius: 0;">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Posted Date: <strong class="text-gray-900 dark:text-white">{{ $job->created_at->format('Y-m-d') }}</strong></span>
                        <svg class="w-5 h-5 text-gray-400 flex-shrink-0 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </div>
                    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 px-4 py-3 flex items-center justify-between flex-1 min-w-[200px]" style="border-radius: 0;">
                        <span class="inline-flex items-center px-2.5 py-0.5 text-xs font-medium rounded-full {{ $job->status === 'published' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">{{ $job->status === 'published' ? 'active' : $job->status }}</span>
                        <svg class="w-5 h-5 text-gray-400 flex-shrink-0 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                </div>

                {{-- Job Details: left (Location, Salary) and right (Type, Promotion Status) --}}
                <div class="mt-6 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 p-5 shadow-sm dark:shadow-none" style="border-radius: 0;">
                    <h3 class="text-base font-bold mb-4" style="color: #1A202C;">Job Details</h3>
                    <div class="grid grid-cols-2 gap-x-12 gap-y-4 text-sm">
                        <div class="space-y-4">
                            <div>
                                <dt class="text-sm" style="color: #6B7280;">Location</dt>
                                <dd class="font-semibold mt-0.5" style="color: #1A202C;">{{ $job->location ?: ($job->is_remote ? 'Remote' : '—') }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm" style="color: #6B7280;">Salary Range</dt>
                                <dd class="font-semibold mt-0.5" style="color: #1A202C;">
                                    @if($job->hide_salary) Negotiable
                                    @elseif($job->salary_min || $job->salary_max)
                                        @php
                                            $min = $job->salary_min ? ($job->salary_min >= 1000 ? '$' . number_format($job->salary_min / 1000, 0) . 'k' : '$' . number_format($job->salary_min)) : '—';
                                            $max = $job->salary_max ? ($job->salary_max >= 1000 ? '$' . number_format($job->salary_max / 1000, 0) . 'k' : '$' . number_format($job->salary_max)) : '—';
                                        @endphp
                                        {{ $min }} - {{ $max }}
                                    @else — @endif
                                </dd>
                            </div>
                        </div>
                        <div class="space-y-4">
                            <div>
                                <dt class="text-sm" style="color: #6B7280;">Type</dt>
                                <dd class="font-semibold mt-0.5" style="color: #1A202C;">{{ ucfirst(str_replace('_', ' ', $job->employment_type ?? 'Full-time')) }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm" style="color: #6B7280;">Promotion Status</dt>
                                <dd class="font-semibold mt-0.5" style="color: #1A202C;">{{ $isPromoted ? 'Promoted' : 'Not promoted' }}</dd>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Applicants --}}
                <div class="mt-6 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 shadow-sm dark:shadow-none overflow-hidden rounded-md">
                    <div class="flex items-center justify-between px-5 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-bold" style="color: #1A202C;">Applicants</h3>
                        <a href="{{ route('employer.jobs.applicants', $job->id) }}" class="text-sm font-medium hover:underline" style="color: #2563eb;">View All Applicants →</a>
                    </div>
                    <div class="p-5 space-y-5">
                        @forelse($applications as $app)
                            @php
                                $initials = strtoupper(substr($app->first_name ?? '', 0, 1) . substr($app->last_name ?? '', 0, 1));
                                $statusLabels = ['pending' => 'NEW', 'shortlisted' => 'SHORTLISTED', 'hired' => 'SELECTED', 'rejected' => 'REJECTED', 'reviewing' => 'INTERVIEW', 'interview_requested' => 'INTERVIEW'];
                                $statusLabel = $statusLabels[$app->status] ?? strtoupper($app->status ?? '');
                                $statusBadgeClass = $app->status === 'pending' ? 'text-white' : ($app->status === 'shortlisted' ? 'bg-orange-100 text-orange-800' : 'text-white');
                                $statusBadgeStyle = $app->status === 'pending' ? 'background-color: #3B82F6;' : ($app->status === 'shortlisted' ? '' : ($app->status === 'hired' ? 'background-color: #059669;' : ($app->status === 'rejected' ? 'background-color: #DC2626;' : 'background-color: #9333EA;')));
                                $experience = 'N/A';
                                if ($app->jobSeeker && $app->jobSeeker->experiences->isNotEmpty()) {
                                    $y = 0;
                                    foreach ($app->jobSeeker->experiences as $e) {
                                        if (!empty($e->start_date)) { $end = !empty($e->end_date) ? $e->end_date : now()->format('Y-m-d'); $y += \Carbon\Carbon::parse($e->start_date)->diffInYears(\Carbon\Carbon::parse($end)); }
                                    }
                                    $experience = $y ? $y . ' years in ' . ($app->jobSeeker->experiences->first()->job_title ?? 'Software Development') : 'N/A';
                                }
                                $education = 'N/A';
                                if ($app->jobSeeker && $app->jobSeeker->educations->isNotEmpty()) {
                                    $ed = $app->jobSeeker->educations->first();
                                    $education = trim(($ed->degree ?? '') . ' ' . ($ed->field_of_study ?? '') . ', ' . ($ed->institution ?? ''), ' ,') ?: 'N/A';
                                }
                                $locStr = $app->jobSeeker && ($app->jobSeeker->city || $app->jobSeeker->country) ? trim(($app->jobSeeker->city ?? '') . ', ' . ($app->jobSeeker->country ?? ''), ' ,') : '';
                            @endphp
                            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 p-5 shadow-sm dark:shadow-none rounded-md flex items-start gap-4">
                                {{-- Left: avatar --}}
                                <div class="w-12 h-12 flex items-center justify-center text-white font-bold text-sm flex-shrink-0 rounded-md" style="background-color: #1A202C;">{{ $initials ?: '?' }}</div>
                                {{-- Center: name, contact, experience, buttons --}}
                                <div class="flex-1 min-w-0">
                                    <div class="flex flex-wrap items-center gap-2 mb-2">
                                        <span class="font-bold" style="color: #1A202C;">{{ $app->first_name }} {{ $app->last_name }}</span>
                                        <span class="px-2.5 py-0.5 text-xs font-semibold rounded-md {{ $statusBadgeClass }}" style="{{ $statusBadgeStyle }}">{{ $statusLabel }}</span>
                                    </div>
                                    <div class="flex flex-wrap items-center gap-4 text-sm mb-2" style="color: #6B7280;">
                                        <span class="inline-flex items-center gap-1.5">
                                            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                            {{ $app->email }}
                                        </span>
                                        @if($app->phone)
                                        <span class="inline-flex items-center gap-1.5">
                                            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                            {{ $app->phone }}
                                        </span>
                                        @endif
                                        <span class="inline-flex items-center gap-1.5">
                                            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                            Applied {{ $app->created_at->format('Y-m-d') }}
                                        </span>
                                    </div>
                                    <div class="flex items-start gap-4 text-sm mb-3">
                                        <div class="flex items-start gap-1.5">
                                            <svg class="w-4 h-4 flex-shrink-0 mt-0.5" style="color: #6B7280;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                            <div><span class="block text-xs" style="color: #6B7280;">Experience</span><span class="font-medium" style="color: #1A202C;">{{ $experience }}</span></div>
                                        </div>
                                    </div>
                                    <div class="flex flex-wrap gap-2 items-center">
                                        <a href="{{ route('employer.applications.show', $app->id) }}?from=job_applicants&job_id={{ $job->id }}" class="inline-flex items-center gap-1.5 px-4 py-2 text-white text-sm font-medium rounded-md hover:opacity-90 transition" style="background-color: #1A202C;">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            View Profile
                                        </a>
                                        <a href="{{ route('employer.jobs.applicants', $job->id) }}" class="inline-flex items-center gap-1.5 px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-md hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                                            Shortlist
                                        </a>
                                        <a href="{{ route('employer.jobs.applicants', $job->id) }}" class="inline-flex items-center gap-1.5 px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-md hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                            Reject
                                        </a>
                                        <a href="{{ route('employer.jobs.applicants', $job->id) }}" class="inline-flex items-center gap-1.5 px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-md hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            Select
                                        </a>
                                        <a href="{{ route('employer.applications.index', ['job_id' => $job->id]) }}" class="inline-flex items-center gap-1.5 px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-md hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6L12 2 6 4 2 5v14a2 2 0 002 2z"/></svg>
                                            Pool
                                        </a>
                                    </div>
                                </div>
                                {{-- Right: Education and Location (side-by-side with left content) --}}
                                <div class="flex flex-col gap-4 text-sm min-w-[180px] flex-shrink-0">
                                    <div class="flex items-start gap-1.5">
                                        <svg class="w-4 h-4 flex-shrink-0 mt-0.5" style="color: #6B7280;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/></svg>
                                        <div><span class="block text-xs" style="color: #6B7280;">Education</span><span class="font-medium" style="color: #1A202C;">{{ $education }}</span></div>
                                    </div>
                                    @if($locStr)
                                    <div class="flex items-start gap-1.5">
                                        <svg class="w-4 h-4 flex-shrink-0 mt-0.5" style="color: #6B7280;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                        <div><span class="block text-xs" style="color: #6B7280;">Location</span><span class="font-medium" style="color: #1A202C;">{{ $locStr }}</span></div>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="border border-gray-200 dark:border-gray-700 p-8 text-center rounded-md" style="color: #6B7280;">No applicants yet.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>
@endsection
