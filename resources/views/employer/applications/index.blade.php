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
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Applicant Tracking</h1>
                        <p class="text-gray-600 dark:text-gray-400 mt-1">Review and manage job applications</p>
                    </div>
                    <button type="button" onclick="exportApplications()"
                            class="flex items-center gap-2 px-4 py-2 border border-gray-200 dark:border-gray-600 rounded font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors text-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        Export
                    </button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div class="bg-white dark:bg-gray-800 p-4 rounded border border-gray-200 dark:border-gray-700 shadow-sm">
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Total Applications</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($stats['all']) }}</p>
                    </div>
                    <div class="bg-white dark:bg-gray-800 p-4 rounded border border-gray-200 dark:border-gray-700 shadow-sm">
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">New Today</p>
                        <p class="text-2xl font-bold text-blue-600">{{ number_format($stats['new_today'] ?? 0) }}</p>
                    </div>
                    <div class="bg-white dark:bg-gray-800 p-4 rounded border border-gray-200 dark:border-gray-700 shadow-sm">
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Shortlisted</p>
                        <p class="text-2xl font-bold text-emerald-600">{{ number_format($stats['shortlisted'] ?? 0) }}</p>
                    </div>
                    <div class="bg-white dark:bg-gray-800 p-4 rounded border border-gray-200 dark:border-gray-700 shadow-sm">
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">In Interview</p>
                        <p class="text-2xl font-bold text-violet-600">{{ number_format($stats['reviewing'] ?? 0) }}</p>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded border border-gray-200 dark:border-gray-700 shadow-sm">
                    <div class="border-b border-gray-200 dark:border-gray-700">
                        <div id="statusTabs" class="flex items-center gap-1 p-2 overflow-x-auto">
                            @php
                                $tabs = [
                                    ['all', 'All Applications', $stats['all'] ?? 0, false],
                                    ['rejected', 'Rejected', $stats['rejected'] ?? 0, false],
                                    ['shortlisted', 'Shortlisted', $stats['shortlisted'] ?? 0, false],
                                    ['reviewing', 'Interview', $stats['reviewing'] ?? 0, false],
                                    ['hired', 'Selected', $stats['hired'] ?? 0, false],
                                    ['talent_pool', 'Talent Pool', $stats['talent_pool'] ?? 0, true],
                                ];
                            @endphp
                            @foreach($tabs as [$tabStatus, $tabLabel, $tabCount, $isPool])
                                @php $active = $currentStatus === $tabStatus; @endphp
                                <button type="button" onclick="filterByStatus('{{ $tabStatus }}')" data-status="{{ $tabStatus }}"
                                    class="status-tab-btn flex items-center gap-2 px-4 py-2 rounded text-sm font-medium transition-colors whitespace-nowrap
                                    @if($isPool)
                                        {{ $active ? 'bg-blue-600 text-white' : 'bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-400 hover:bg-blue-100 dark:hover:bg-blue-900/30 border border-blue-200 dark:border-blue-800' }}
                                    @else
                                        {{ $active ? 'bg-gray-900 dark:bg-gray-100 text-white dark:text-gray-900' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700' }}
                                    @endif">
                                    <span>{{ $tabLabel }}</span>
                                    <span class="px-2 py-0.5 rounded-full text-xs
                                        @if($isPool)
                                            {{ $active ? 'bg-white/20 text-white' : 'bg-blue-200 dark:bg-blue-800 text-blue-800 dark:text-blue-200' }}
                                        @else
                                            {{ $active ? 'bg-white/20 dark:bg-gray-900/20 text-white dark:text-gray-900' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300' }}
                                        @endif">{{ $tabCount }}</span>
                                </button>
                            @endforeach
                        </div>
                    </div>

                    <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                        <div class="flex flex-col sm:flex-row gap-4">
                            <div class="flex-1 relative">
                                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                <input type="text" id="searchInput" value="{{ request('search') }}" placeholder="Search applicants..."
                                       class="w-full pl-10 pr-4 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded text-sm text-gray-900 dark:text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div class="flex gap-2 flex-wrap">
                                <select id="jobFilter" class="px-4 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded text-sm text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="">All Jobs</option>
                                    @foreach($jobs as $job)
                                        <option value="{{ $job->id }}" {{ $currentJobId == $job->id ? 'selected' : '' }}>{{ $job->title }}</option>
                                    @endforeach
                                </select>
                                <select id="statusFilter" class="px-4 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded text-sm text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="all" {{ $currentStatus === 'all' ? 'selected' : '' }}>All Status</option>
                                    <option value="pending" {{ $currentStatus === 'pending' ? 'selected' : '' }}>New</option>
                                    <option value="reviewing" {{ $currentStatus === 'reviewing' ? 'selected' : '' }}>Interview</option>
                                    <option value="shortlisted" {{ $currentStatus === 'shortlisted' ? 'selected' : '' }}>Shortlisted</option>
                                    <option value="rejected" {{ $currentStatus === 'rejected' ? 'selected' : '' }}>Rejected</option>
                                    <option value="hired" {{ $currentStatus === 'hired' ? 'selected' : '' }}>Accepted</option>
                                </select>
                                <button type="button" class="flex items-center gap-2 px-4 py-2 border border-gray-200 dark:border-gray-600 rounded text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                                    More Filters
                                </button>
                            </div>
                        </div>
                    </div>

                    <div id="bulkActionBar" class="hidden px-4 py-2.5 bg-blue-50 dark:bg-blue-900/20 border-b border-blue-100 dark:border-blue-800 flex items-center gap-4">
                        <span id="selectedCount" class="text-sm font-medium text-blue-700 dark:text-blue-300">0 selected</span>
                        <button type="button" onclick="exportSelected()" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-600 text-white rounded text-xs font-semibold hover:bg-blue-700 transition">Export Selected</button>
                        <button type="button" onclick="clearSelection()" class="text-xs text-blue-600 hover:text-blue-800 font-medium">Clear</button>
                    </div>

                    <div id="applicationsList" class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($applications as $application)
                        @php
                            $initials = strtoupper(substr($application->first_name, 0, 1) . substr($application->last_name, 0, 1));
                            $statusColors = [
                                'pending' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400',
                                'applied' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400',
                                'reviewing' => 'bg-violet-100 text-violet-700 dark:bg-violet-900/30 dark:text-violet-400',
                                'in_review' => 'bg-violet-100 text-violet-700 dark:bg-violet-900/30 dark:text-violet-400',
                                'shortlisted' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400',
                                'rejected' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
                                'hired' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400',
                                'offered' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400',
                            ];
                            $statusLabels = [
                                'pending' => 'new', 'applied' => 'new', 'reviewing' => 'interview', 'in_review' => 'interview',
                                'shortlisted' => 'shortlisted', 'rejected' => 'rejected', 'hired' => 'accepted', 'offered' => 'accepted',
                            ];
                            $statusColor = $statusColors[$application->status] ?? 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300';
                            $statusLabel = $statusLabels[$application->status] ?? $application->status;
                            $experience = 'N/A';
                            try {
                                if ($application->jobSeeker && $application->jobSeeker->relationLoaded('experiences') && $application->jobSeeker->experiences) {
                                    $totalYears = 0;
                                    foreach ($application->jobSeeker->experiences as $exp) {
                                        if ($exp && isset($exp->start_date) && $exp->start_date) {
                                            $start = new DateTime($exp->start_date);
                                            $end = (isset($exp->end_date) && $exp->end_date) ? new DateTime($exp->end_date) : new DateTime();
                                            $totalYears += $start->diff($end)->y;
                                        }
                                    }
                                    $experience = $totalYears > 0 ? $totalYears . ' years' : 'N/A';
                                }
                            } catch (\Exception $e) { $experience = 'N/A'; }
                            $jobSeeker = $application->jobSeeker;
                            $profilePhoto = $jobSeeker?->profile_photo;
                            $photoUrl = null;
                            if ($profilePhoto) {
                                $photoUrl = (str_starts_with($profilePhoto, 'http://') || str_starts_with($profilePhoto, 'https://'))
                                    ? $profilePhoto : asset('storage/' . $profilePhoto);
                            }
                            $jobTitle = $application->jobAdvertisement->title ?? '';
                            $jobLoc = ($application->jobAdvertisement->is_remote ?? false) ? 'Remote' : ($application->jobAdvertisement->location ?? 'Not specified');
                            $jobCode = 'JOB-' . str_pad($application->jobAdvertisement->id ?? 0, 3, '0', STR_PAD_LEFT);
                            $inPool = (bool) ($application->in_talent_pool ?? false);
                        @endphp
                        <div class="p-4 hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors cursor-pointer" onclick="openApplicationModal({{ $application->id }})">
                            <div class="flex items-center gap-4">
                                <input type="checkbox" value="{{ $application->id }}" onclick="event.stopPropagation(); toggleSelect(this)" class="app-checkbox w-4 h-4 text-blue-600 rounded border-gray-300 dark:border-gray-600 focus:ring-blue-500 cursor-pointer flex-shrink-0">
                                @if($photoUrl)
                                    <img src="{{ $photoUrl }}" alt="" class="w-12 h-12 rounded object-cover flex-shrink-0" style="background: linear-gradient(to bottom right, #2563eb, #06b6d4);" onerror="this.onerror=null;this.outerHTML='<div class=\'w-12 h-12 rounded flex items-center justify-center text-white font-bold flex-shrink-0\' style=\'background:linear-gradient(to bottom right,#2563eb,#06b6d4)\'>{{ $initials }}</div>';">
                                @else
                                    <div class="w-12 h-12 rounded flex items-center justify-center text-white font-bold flex-shrink-0" style="background: linear-gradient(to bottom right, #2563eb, #06b6d4);">{{ $initials }}</div>
                                @endif
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 mb-1 flex-wrap">
                                        <h3 class="font-semibold text-gray-900 dark:text-white">{{ $application->first_name }} {{ $application->last_name }}</h3>
                                        @if($currentStatus === 'all')
                                            <span class="px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColor }}">{{ $statusLabel }}</span>
                                        @endif
                                        @if($inPool)
                                            <span class="flex items-center gap-1 px-2 py-0.5 bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400 rounded-full text-xs font-medium">In Pool</span>
                                        @endif
                                    </div>
                                    <div class="flex items-center gap-4 text-sm text-gray-600 dark:text-gray-400 flex-wrap">
                                        <span class="font-medium text-gray-700 dark:text-gray-300">{{ $jobTitle }}</span>
                                        <span class="flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                            {{ $jobCode }}
                                        </span>
                                        <span class="flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                            {{ $jobLoc }}
                                        </span>
                                    </div>
                                </div>
                                <div class="flex items-center gap-6 text-sm text-gray-600 dark:text-gray-400 flex-shrink-0">
                                    <div class="flex items-center gap-1.5">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        <span>{{ $application->created_at->format('Y-m-d') }}</span>
                                    </div>
                                    <div class="flex items-center gap-1.5">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                        <span>{{ $experience }}</span>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2 flex-shrink-0">
                                    <button type="button" onclick="event.stopPropagation(); toggleTalentPool({{ $application->id }}, this)" title="{{ $inPool ? 'Remove from Talent Pool' : 'Add to Talent Pool' }}"
                                            class="p-2 border rounded transition-colors {{ $inPool ? 'border-amber-500 bg-amber-50 dark:bg-amber-900/20 text-amber-600' : 'border-gray-300 dark:border-gray-600 text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                                        <svg class="w-4 h-4 {{ $inPool ? 'fill-current' : '' }}" fill="{{ $inPool ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                                    </button>
                                    <button type="button" onclick="event.stopPropagation(); exportSingle({{ $application->id }})" class="p-2 border border-gray-300 dark:border-gray-600 rounded hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors" title="Export">
                                        <svg class="w-4 h-4 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    </button>
                                    <button type="button" onclick="event.stopPropagation(); openApplicationModal({{ $application->id }})" class="px-4 py-2 bg-blue-600 text-white rounded text-sm font-medium hover:bg-blue-700 transition-colors">View</button>
                                    <button type="button" onclick="event.stopPropagation(); openApplicationModal({{ $application->id }})" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded transition-colors">
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="p-12 text-center">
                            <p class="text-gray-500 dark:text-gray-400">No applicants found matching your filters.</p>
                        </div>
                    @endforelse
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>
<!-- Applicant Profile Modal (matches Applicant Profile design) -->
<div id="applicationModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4" style="background: rgba(0,0,0,0.4); backdrop-filter: blur(2px);">
    <div class="relative bg-white dark:bg-gray-800 rounded-xl shadow-2xl w-full max-w-6xl max-h-[90vh] flex flex-col overflow-hidden">
        <!-- Modal Header (dark gray bar) -->
        <div class="px-6 py-4 flex justify-between items-center flex-shrink-0 border-b border-gray-200 dark:border-gray-700" style="background-color: #F7F8F9;">
            <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Applicant Profile</h3>
            <div class="flex items-center space-x-3">
                <button type="button" id="applicationModalDownloadBtn" class="p-2 text-gray-500 dark:text-gray-400 hover:text-gray-700 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition" title="Download Resume" style="display: none;">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </button>
                <button type="button" onclick="closeApplicationModal()" class="p-2 text-gray-500 dark:text-gray-400 hover:text-gray-700 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition" title="Close">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Modal Body (white, scrollable) -->
        <div id="applicationModalContent" class="p-6 overflow-y-auto flex-1 bg-white dark:bg-gray-800">
            <div class="text-center py-8">
                <div class="spinner mx-auto mb-4"></div>
                <p class="text-gray-500 dark:text-gray-400">Loading application details...</p>
            </div>
        </div>

        <!-- Modal Footer -->
        <div id="applicationModalFooter" class="border-t border-gray-200 dark:border-gray-700 px-6 py-4 flex items-center justify-between flex-shrink-0" style="display: none; background-color: #F7F8F9;">
            <button type="button" onclick="closeApplicationModal()" class="px-5 py-2.5 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition font-medium text-sm">
                Close
            </button>
            <div class="flex items-center space-x-3">
                <a id="applicationModalDownloadResume" href="#" target="_blank" class="inline-flex items-center px-5 py-2.5 bg-gray-700 text-white rounded-lg hover:bg-gray-600 transition font-medium text-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"></path>
                    </svg>
                    Download Resume
                </a>
                <button type="button" id="applicationModalSendMessage" class="px-5 py-2.5 bg-gradient-to-r from-blue-500 to-cyan-400 text-white rounded-lg hover:from-blue-600 hover:to-cyan-500 shadow-md transition font-medium text-sm">
                    Send Message
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function filterByStatus(status) {
        const url = new URL(window.location.href);
        if (status === 'all') {
            url.searchParams.delete('status');
        } else if (status === 'talent_pool') {
            // Talent pool not implemented yet, show empty for now
            url.searchParams.set('status', 'talent_pool');
        } else {
            url.searchParams.set('status', status);
        }
        window.location.href = url.toString();
    }

    function openApplicationModal(applicationId) {
        const modal = document.getElementById('applicationModal');
        const content = document.getElementById('applicationModalContent');
        const footer = document.getElementById('applicationModalFooter');
        if (footer) footer.style.display = 'none';
        
        modal.classList.remove('hidden');
        content.innerHTML = `
            <div class="text-center py-8">
                <div class="spinner mx-auto mb-4"></div>
                <p class="text-gray-500 dark:text-gray-400">Loading application details...</p>
            </div>
        `;

        // Fetch application details
        fetch(`/employer/applications/${applicationId}`, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.application) {
                populateApplicationModal(data.application);
            } else {
                content.innerHTML = '<div class="text-red-600">Failed to load application details.</div>';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            content.innerHTML = '<div class="text-red-600">An error occurred while loading the application.</div>';
        });
    }

    function populateApplicationModal(application) {
        try {
            const content = document.getElementById('applicationModalContent');
            
            if (!application) {
                content.innerHTML = '<div class="text-red-600">Application data is missing.</div>';
                return;
            }
            
            const statusColors = {
                'pending': 'bg-yellow-100 text-yellow-800',
                'reviewing': 'bg-blue-100 text-blue-800',
                'shortlisted': 'bg-purple-100 text-purple-800',
                'interview_requested': 'bg-indigo-100 text-indigo-800',
                'rejected': 'bg-red-100 text-red-800',
                'hired': 'bg-green-100 text-green-800',
            };
            
            const statusColor = statusColors[application.status] || 'bg-gray-100 text-gray-800';
            
            // Handle resume URL - can be full URL or relative path
            let resumeUrl = null;
            if (application.resume_path) {
                if (application.resume_path.startsWith('http://') || application.resume_path.startsWith('https://')) {
                    // Already a full URL, use it directly
                    resumeUrl = application.resume_path;
                } else {
                    // Relative path, prepend base URL
                    resumeUrl = `{{ rtrim(env('MEDIA_BASE_URL', rtrim(env('APP_URL', 'http://127.0.0.1'), '/') . '/uploads'), '/') }}/${application.resume_path}`;
                }
            }

            const jobSeeker = application.job_seeker || application.jobSeeker || {};
            // Handle profile photo URL - can be full URL or relative path
            let profilePhoto = null;
            if (jobSeeker.profile_photo || jobSeeker.profilePhoto) {
                const photo = jobSeeker.profile_photo || jobSeeker.profilePhoto;
                if (photo.startsWith('http://') || photo.startsWith('https://')) {
                    profilePhoto = photo;
                } else {
                    profilePhoto = `{{ asset('storage/') }}/${photo}`;
                }
            }

        const jobTitle = application.job_advertisement ? application.job_advertisement.title : '';
        const companyName = application.job_advertisement && application.job_advertisement.company ? application.job_advertisement.company.name : '';
        const subtitle = jobTitle + (companyName ? ' | ' + companyName : '');
        const initials = (application.first_name || '').charAt(0) + (application.last_name || '').charAt(0);
        const dobFormatted = jobSeeker.date_of_birth ? new Date(jobSeeker.date_of_birth).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' }) : '';
        const age = jobSeeker.date_of_birth ? Math.floor((new Date() - new Date(jobSeeker.date_of_birth)) / (365.25 * 24 * 60 * 60 * 1000)) : '';
        const expYears = jobSeeker.experiences && Array.isArray(jobSeeker.experiences) ? jobSeeker.experiences.reduce((y, e) => {
            if (!e || !e.start_date) return y;
            const end = e.end_date ? new Date(e.end_date) : new Date();
            return y + Math.max(0, (end - new Date(e.start_date)) / (365.25 * 24 * 60 * 60 * 1000));
        }, 0) : 0;
        const experienceText = expYears >= 1 ? Math.round(expYears) + ' years experience' : (expYears > 0 ? 'Less than 1 year' : '');
        const skillColors = ['bg-purple-500 text-white', 'bg-green-200 text-gray-800', 'bg-blue-200 text-gray-800', 'bg-green-200 text-gray-800', 'bg-blue-200 text-gray-800', 'bg-green-200 text-gray-800'];
        const licenseDate = jobSeeker.license_issued_date ? new Date(jobSeeker.license_issued_date).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' }) : '';
        const driverLicenseText = jobSeeker.driving_license ? ('Yes' + (licenseDate ? ' (' + licenseDate + ')' : '')) : '';
        const linkedinUrl = jobSeeker.linkedin_url || '';
        const portfolioUrl = jobSeeker.website_url || '';
        const githubUrl = jobSeeker.github_url || '';
        const matchScore = application.match_score != null ? application.match_score : (application.match_score_percent != null ? application.match_score_percent : null);

        content.innerHTML = `
            <div class="space-y-6 text-gray-800 dark:text-gray-100">
                <!-- Applicant Summary Card -->
                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-sm dark:shadow-none p-6">
                    <!-- Top row: avatar + info + match score -->
                    <div class="flex items-start gap-5">
                        <!-- Avatar -->
                        <div class="flex-shrink-0">
                            ${profilePhoto ? `
                                <img src="${profilePhoto}" alt="${application.first_name} ${application.last_name}" class="w-[88px] h-[88px] rounded-xl object-cover border border-gray-200 dark:border-gray-700" onerror="this.onerror=null; this.outerHTML='<div style=\\'width:88px;height:88px\\' class=\\'rounded-xl flex items-center justify-center text-white font-bold text-3xl flex-shrink-0\\' style=\\'background:linear-gradient(135deg,#2563eb 0%,#3b82f6 50%,#60a5fa 100%)\\'>${initials}</div>';">
                            ` : `
                                <div style="width:88px;height:88px;background:linear-gradient(135deg,#2563eb 0%,#3b82f6 50%,#60a5fa 100%)" class="rounded-xl flex items-center justify-center text-white font-bold text-3xl flex-shrink-0">${initials}</div>
                            `}
                        </div>
                        <!-- Name, subtitle, details -->
                        <div class="flex-1 min-w-0">
                            <h4 class="text-[22px] font-bold text-gray-900 dark:text-white leading-tight">${application.first_name} ${application.last_name}</h4>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">${subtitle || 'Applicant'}</p>
                            <!-- 2-col details: Row1=email|phone, Row2=location|experience, Row3=gender|dob, Row4=license -->
                            <div class="grid grid-cols-2 gap-x-12 gap-y-2.5 mt-4 text-sm text-gray-700 dark:text-gray-300">
                                <div class="flex items-center gap-2.5"><svg class="w-[18px] h-[18px] text-gray-500 dark:text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/></svg>${application.email || '—'}</div>
                                <div class="flex items-center gap-2.5"><svg class="w-[18px] h-[18px] text-gray-500 dark:text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z"/></svg>${application.phone || '—'}</div>
                                <div class="flex items-center gap-2.5"><svg class="w-[18px] h-[18px] text-gray-500 dark:text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/></svg>${jobSeeker.location || '—'}</div>
                                <div class="flex items-center gap-2.5"><svg class="w-[18px] h-[18px] text-gray-500 dark:text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 14.15v4.25c0 1.094-.787 2.036-1.872 2.18-2.087.277-4.216.42-6.378.42s-4.291-.143-6.378-.42c-1.085-.144-1.872-1.086-1.872-2.18v-4.25m16.5 0a2.18 2.18 0 00.75-1.661V8.706c0-1.081-.768-2.015-1.837-2.175a48.114 48.114 0 00-3.413-.387m4.5 8.006c-.194.165-.42.295-.673.38A23.978 23.978 0 0112 15.75c-2.648 0-5.195-.429-7.577-1.22a2.016 2.016 0 01-.673-.38m0 0A2.18 2.18 0 013 12.489V8.706c0-1.081.768-2.015 1.837-2.175a48.111 48.111 0 013.413-.387m7.5 0V5.25A2.25 2.25 0 0013.5 3h-3a2.25 2.25 0 00-2.25 2.25v.894m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>${experienceText || '—'}</div>
                                <div class="flex items-center gap-2.5"><svg class="w-[18px] h-[18px] text-gray-500 dark:text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/></svg>${jobSeeker.gender ? (jobSeeker.gender).replace(/^\w/, c => c.toUpperCase()) : '—'}</div>
                                <div class="flex items-center gap-2.5"><svg class="w-[18px] h-[18px] text-gray-500 dark:text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/></svg>${dobFormatted ? dobFormatted + (age ? ' (' + age + ' years)' : '') : '—'}</div>
                                ${driverLicenseText ? `<div class="flex items-center gap-2.5 col-span-2"><svg class="w-[18px] h-[18px] text-green-600 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>Driver's License: ${driverLicenseText}</div>` : ''}
                            </div>
                            <!-- Social link buttons -->
                            <div class="flex flex-wrap gap-3 mt-5">
                                ${linkedinUrl ? `<a href="${linkedinUrl}" target="_blank" rel="noopener" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium text-white bg-[#0A66C2] hover:bg-[#004182] transition"><svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>LinkedIn</a>` : ''}
                                ${portfolioUrl ? `<a href="${portfolioUrl}" target="_blank" rel="noopener" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium text-white bg-gray-800 hover:bg-gray-700 transition"><svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9.004 9.004 0 008.716-6.747M12 21a9.004 9.004 0 01-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 017.843 4.582M12 3a8.997 8.997 0 00-7.843 4.582m15.686 0A11.953 11.953 0 0112 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0121 12c0 .778-.099 1.533-.284 2.253m0 0A17.919 17.919 0 0112 16.5c-3.162 0-6.133-.815-8.716-2.247m0 0A9.015 9.015 0 013 12c0-1.605.42-3.113 1.157-4.418"/></svg>Portfolio</a>` : ''}
                                ${githubUrl ? `<a href="${githubUrl}" target="_blank" rel="noopener" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium text-white bg-gray-800 hover:bg-gray-700 transition"><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.006-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z"/></svg>GitHub</a>` : ''}
                            </div>
                        </div>
                        <!-- Match Score (top right) -->
                        <div class="flex-shrink-0 text-center ml-2">
                            <div class="bg-gray-800 rounded-lg px-3 py-2 inline-flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z"/></svg>
                                <span class="text-white font-bold text-sm">${matchScore != null ? matchScore + '%' : '—'}</span>
                            </div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Match Score</p>
                        </div>
                    </div>
                </div>

                <!-- Key info cards: Salary, Availability, Work Authorization -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div class="bg-gray-100 dark:bg-gray-800 rounded-xl p-4">
                        <div class="flex items-center gap-2 text-gray-500 dark:text-gray-400 mb-1"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg><span class="text-xs font-medium">Salary Expectation</span></div>
                        <p class="text-sm font-bold text-gray-900 dark:text-white">${jobSeeker.expected_salary || 'Not specified'}</p>
                    </div>
                    <div class="bg-gray-100 dark:bg-gray-800 rounded-xl p-4">
                        <div class="flex items-center gap-2 text-gray-500 dark:text-gray-400 mb-1"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg><span class="text-xs font-medium">Availability</span></div>
                        <p class="text-sm font-bold text-gray-900 dark:text-white">${jobSeeker.availability || jobSeeker.employment_status ? (jobSeeker.employment_status || '').replace('_', ' ') : 'Not specified'}</p>
                    </div>
                    <div class="bg-gray-100 dark:bg-gray-800 rounded-xl p-4">
                        <div class="flex items-center gap-2 text-gray-500 dark:text-gray-400 mb-1"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg><span class="text-xs font-medium">Work Authorization</span></div>
                        <p class="text-sm font-bold text-gray-900 dark:text-white">${jobSeeker.work_authorization || 'Not specified'}</p>
                    </div>
                </div>

                <!-- About -->
                ${jobSeeker.bio ? `
                    <div class="border-b border-gray-200 dark:border-gray-700 pb-6">
                        <h5 class="flex items-center gap-2 text-lg font-bold text-gray-900 dark:text-white mb-3">
                            <svg class="w-5 h-5 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z"/></svg>
                            About
                        </h5>
                        <p class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed whitespace-pre-wrap">${jobSeeker.bio}</p>
                    </div>
                ` : ''}

                <!-- Work Experience -->
                ${jobSeeker && jobSeeker.experiences && Array.isArray(jobSeeker.experiences) && jobSeeker.experiences.length > 0 ? `
                    <div class="border-b border-gray-200 dark:border-gray-700 pb-6">
                        <h5 class="flex items-center gap-2 text-lg font-bold text-gray-900 dark:text-white mb-4">
                            <svg class="w-5 h-5 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 14.15v4.25c0 1.094-.787 2.036-1.872 2.18-2.087.277-4.216.42-6.378.42s-4.291-.143-6.378-.42c-1.085-.144-1.872-1.086-1.872-2.18v-4.25m16.5 0a2.18 2.18 0 00.75-1.661V8.706c0-1.081-.768-2.015-1.837-2.175a48.114 48.114 0 00-3.413-.387m4.5 8.006c-.194.165-.42.295-.673.38A23.978 23.978 0 0112 15.75c-2.648 0-5.195-.429-7.577-1.22a2.016 2.016 0 01-.673-.38m0 0A2.18 2.18 0 013 12.489V8.706c0-1.081.768-2.015 1.837-2.175a48.111 48.111 0 013.413-.387m7.5 0V5.25A2.25 2.25 0 0013.5 3h-3a2.25 2.25 0 00-2.25 2.25v.894m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
                            Work Experience
                        </h5>
                        <div class="space-y-4">
                            ${jobSeeker.experiences.map((exp, i) => {
                                if (!exp) return '';
                                const startDate = exp.start_date ? new Date(exp.start_date).toLocaleDateString('en-US', { year: 'numeric', month: 'short' }) : '';
                                const endDate = exp.end_date ? new Date(exp.end_date).toLocaleDateString('en-US', { year: 'numeric', month: 'short' }) : (exp.is_current ? 'Present' : '');
                                const isCurrent = exp.is_current || !exp.end_date;
                                return `
                                    <div class="border border-gray-200 dark:border-gray-700 border-l-[3px] border-l-blue-500 bg-gray-50 dark:bg-gray-900 rounded-lg pl-5 pr-4 py-4">
                                        <div class="flex items-start justify-between gap-2">
                                            <p class="text-base font-bold text-gray-900 dark:text-white">${exp.job_title || 'Position'}</p>
                                            ${isCurrent ? '<span class="px-3 py-1 text-xs font-semibold rounded-md bg-green-500 text-white whitespace-nowrap">Current</span>' : ''}
                                        </div>
                                        <p class="text-sm text-gray-700 dark:text-gray-300 mt-1">${exp.company_name || ''}</p>
                                        <div class="flex items-center gap-4 mt-1.5 text-xs text-gray-500 dark:text-gray-400">
                                            <span class="flex items-center gap-1"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/></svg>${startDate}${endDate ? ' - ' + endDate : ''}</span>
                                            ${exp.location ? `<span class="flex items-center gap-1"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/></svg>${exp.location}</span>` : ''}
                                        </div>
                                        ${exp.description ? `<p class="text-sm text-gray-600 dark:text-gray-400 mt-3 leading-relaxed">${exp.description}</p>` : ''}
                                    </div>
                                `;
                            }).join('')}
                        </div>
                    </div>
                ` : ''}

                <!-- Education -->
                ${jobSeeker && jobSeeker.educations && Array.isArray(jobSeeker.educations) && jobSeeker.educations.length > 0 ? `
                    <div class="border-b border-gray-200 dark:border-gray-700 pb-6">
                        <h5 class="flex items-center gap-2 text-lg font-bold text-gray-900 dark:text-white mb-4">
                            <svg class="w-5 h-5 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.342M6.75 15a.75.75 0 100-1.5.75.75 0 000 1.5zm0 0v-3.675A55.378 55.378 0 0112 8.443m-7.007 11.55A5.981 5.981 0 006.75 15.75v-1.5"/></svg>
                            Education
                        </h5>
                        <div class="space-y-4">
                            ${jobSeeker.educations.map(edu => {
                                if (!edu) return '';
                                const startDate = edu.start_date ? new Date(edu.start_date).toLocaleDateString('en-US', { year: 'numeric', month: 'short' }) : '';
                                const endDate = edu.end_date ? new Date(edu.end_date).toLocaleDateString('en-US', { year: 'numeric', month: 'short' }) : (edu.is_current ? 'Present' : '');
                                return `
                                    <div class="border border-gray-200 dark:border-gray-700 border-l-[3px] border-l-green-500 bg-gray-50 dark:bg-gray-900 rounded-lg pl-5 pr-4 py-4 flex items-start justify-between gap-4">
                                        <div class="min-w-0 flex-1">
                                            <p class="text-base font-bold text-gray-900 dark:text-white">${edu.degree || 'Degree'}</p>
                                            <p class="text-sm text-gray-700 dark:text-gray-300 mt-0.5">${edu.institution_name || ''}</p>
                                            <div class="flex items-center gap-1.5 mt-1.5 text-xs text-gray-500 dark:text-gray-400">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/></svg>
                                                ${startDate}${endDate ? ' - ' + endDate : ''}
                                            </div>
                                            ${edu.field_of_study ? `<p class="text-sm text-gray-600 dark:text-gray-400 mt-2 leading-relaxed">${edu.field_of_study}</p>` : ''}
                                            ${edu.description ? `<p class="text-sm text-gray-600 dark:text-gray-400 mt-1 leading-relaxed">${edu.description}</p>` : ''}
                                        </div>
                                        ${edu.gpa ? `<div class="flex-shrink-0 text-right"><span class="text-xs text-gray-500 dark:text-gray-400 block">GPA</span><span class="text-xl font-bold text-gray-900 dark:text-white">${edu.gpa}</span></div>` : ''}
                                    </div>
                                `;
                            }).join('')}
                        </div>
                    </div>
                ` : ''}

                <!-- Skills -->
                ${jobSeeker && jobSeeker.skills && Array.isArray(jobSeeker.skills) && jobSeeker.skills.length > 0 ? `
                    <div class="border-b border-gray-200 dark:border-gray-700 pb-6">
                        <h5 class="flex items-center gap-2 text-lg font-bold text-gray-900 dark:text-white mb-3">
                            <svg class="w-5 h-5 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/></svg>
                            Skills
                        </h5>
                        <div class="flex flex-wrap gap-3">
                            ${jobSeeker.skills.map((skill, i) => {
                                const name = (skill && (skill.skill_name || skill.name)) || 'Skill';
                                const prof = skill && skill.proficiency_level ? skill.proficiency_level : '';
                                const years = skill && skill.years_experience ? skill.years_experience + 'y' : '';
                                const extra = [prof, years].filter(Boolean).join(' \u2022 ');
                                const isSolid = (i % 3 === 0);
                                if (isSolid) {
                                    return '<span class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm" style="background:rgba(16,185,129,0.18);backdrop-filter:blur(8px);border:1px solid rgba(16,185,129,0.2);"><span class="font-semibold text-emerald-700">' + name + '</span>' + (extra ? '<span class="text-xs text-emerald-500">' + extra + '</span>' : '') + '</span>';
                                } else {
                                    return '<span class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm" style="background:rgba(16,185,129,0.08);backdrop-filter:blur(8px);border:1px solid rgba(16,185,129,0.12);"><span class="font-semibold text-emerald-600">' + name + '</span>' + (extra ? '<span class="text-xs text-emerald-400">' + extra + '</span>' : '') + '</span>';
                                }
                            }).join('')}
                        </div>
                    </div>
                ` : ''}

                <!-- Languages -->
                ${jobSeeker && jobSeeker.languages && Array.isArray(jobSeeker.languages) && jobSeeker.languages.length > 0 ? `
                    <div class="border-b border-gray-200 dark:border-gray-700 pb-6">
                        <h5 class="flex items-center gap-2 text-lg font-bold text-gray-900 dark:text-white mb-3">
                            <svg class="w-5 h-5 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 21l5.25-11.25L21 21m-9-3h7.5M3 5.621a48.474 48.474 0 016-.371m0 0c1.12 0 2.233.038 3.334.114M9 5.25V3m3.334 2.364C11.176 10.658 7.69 15.08 3 17.502m9.334-12.138c.896.061 1.785.147 2.666.257m-4.589 8.495a18.023 18.023 0 01-3.827-5.802"/></svg>
                            Languages
                        </h5>
                        <div class="grid grid-cols-2 gap-3">
                            ${jobSeeker.languages.map(lang => `
                                <div class="flex items-center gap-3 bg-gray-50 dark:bg-gray-900 rounded-xl px-4 py-3 border border-gray-100 dark:border-gray-700">
                                    <div class="w-9 h-9 rounded-lg bg-green-100 flex items-center justify-center flex-shrink-0">
                                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 21l5.25-11.25L21 21m-9-3h7.5M3 5.621a48.474 48.474 0 016-.371m0 0c1.12 0 2.233.038 3.334.114M9 5.25V3m3.334 2.364C11.176 10.658 7.69 15.08 3 17.502m9.334-12.138c.896.061 1.785.147 2.666.257m-4.589 8.495a18.023 18.023 0 01-3.827-5.802"/></svg>
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-gray-900 dark:text-white">${(lang && (lang.language_name || lang.name)) || 'Language'}</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">${(lang && lang.proficiency_level) || ''}</p>
                                    </div>
                                </div>
                            `).join('')}
                        </div>
                    </div>
                ` : ''}

                <!-- Certifications -->
                ${jobSeeker && jobSeeker.certifications && Array.isArray(jobSeeker.certifications) && jobSeeker.certifications.length > 0 ? `
                    <div class="border-b border-gray-200 dark:border-gray-700 pb-6">
                        <h5 class="flex items-center gap-2 text-lg font-bold text-gray-900 dark:text-white mb-4">
                            <svg class="w-5 h-5 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 01-1.043 3.296 3.745 3.745 0 01-3.296 1.043A3.745 3.745 0 0112 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 01-3.296-1.043 3.745 3.745 0 01-1.043-3.296A3.745 3.745 0 013 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 011.043-3.296 3.746 3.746 0 013.296-1.043A3.746 3.746 0 0112 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 013.296 1.043 3.746 3.746 0 011.043 3.296A3.745 3.745 0 0121 12z"/></svg>
                            Certifications
                        </h5>
                        <div class="space-y-4">
                            ${jobSeeker.certifications.map(cert => {
                                if (!cert) return '';
                                const issueDate = cert.issue_date ? new Date(cert.issue_date).toLocaleDateString('en-US', { year: 'numeric', month: 'short' }) : '';
                                const expiryDate = cert.expiry_date ? new Date(cert.expiry_date).toLocaleDateString('en-US', { year: 'numeric', month: 'short' }) : '';
                                const certUrl = cert.certificate_url || cert.url || '';
                                return `
                                    <div class="border border-gray-200 dark:border-gray-700 border-l-[3px] border-l-green-400 bg-gray-50 dark:bg-gray-900 rounded-lg pl-5 pr-4 py-4 flex gap-4 items-start">
                                        <div class="w-10 h-10 rounded-xl bg-amber-100 flex items-center justify-center flex-shrink-0">
                                            <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <p class="text-sm font-bold text-gray-900 dark:text-white">${cert.certification_name || cert.name || 'Certification'}</p>
                                            <p class="text-sm text-gray-600 dark:text-gray-400">${cert.issuing_organization || ''}</p>
                                            ${issueDate || expiryDate ? `<p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Issued: ${issueDate || 'N/A'}${expiryDate ? '&nbsp;&nbsp;&nbsp;Expires: ' + expiryDate : ''}</p>` : ''}
                                            ${certUrl ? `<a href="${certUrl}" target="_blank" rel="noopener" class="inline-flex items-center gap-1.5 text-sm text-blue-600 hover:text-blue-800 hover:underline mt-1.5"><svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/></svg>View Certificate</a>` : ''}
                                        </div>
                                    </div>
                                `;
                            }).join('')}
                        </div>
                    </div>
                ` : ''}

                <!-- Hobbies & Interests -->
                ${jobSeeker && jobSeeker.hobbies && Array.isArray(jobSeeker.hobbies) && jobSeeker.hobbies.length > 0 ? `
                    <div class="border-b border-gray-200 dark:border-gray-700 pb-6">
                        <h5 class="flex items-center gap-2 text-lg font-bold text-gray-900 dark:text-white mb-3">
                            <svg class="w-5 h-5 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z"/></svg>
                            Hobbies & Interests
                        </h5>
                        <div class="flex flex-wrap gap-2.5">
                            ${jobSeeker.hobbies.map(hobby => {
                                const label = typeof hobby === 'string' ? hobby : (hobby.name || hobby.label || '');
                                return label ? '<span class="px-4 py-1.5 rounded-lg text-sm font-medium border" style="color:#e8467c;border-color:#fbb6ce;background:rgba(253,242,248,0.7);">' + label + '</span>' : '';
                            }).join('')}
                        </div>
                    </div>
                ` : ''}

                <!-- References -->
                ${jobSeeker && jobSeeker.references && Array.isArray(jobSeeker.references) && jobSeeker.references.length > 0 ? `
                    <div class="border-b border-gray-200 dark:border-gray-700 pb-6">
                        <h5 class="flex items-center gap-2 text-lg font-bold text-gray-900 dark:text-white mb-4">
                            <svg class="w-5 h-5 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/></svg>
                            References
                        </h5>
                        <div class="space-y-4">
                            ${jobSeeker.references.map(ref => {
                                if (!ref) return '';
                                const name = ref.reference_name || ref.name || 'Reference';
                                const titleCompany = [ref.title, ref.company].filter(Boolean).join(' at ');
                                const relationship = ref.relationship || '';
                                const email = ref.email || '';
                                const phone = ref.phone || '';
                                return '<div class="border border-gray-200 dark:border-gray-700 border-l-[3px] border-l-blue-400 bg-gray-50 dark:bg-gray-900 rounded-lg pl-5 pr-4 py-4 flex gap-4 items-start">' +
                                    '<div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center flex-shrink-0">' +
                                        '<svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/></svg>' +
                                    '</div>' +
                                    '<div class="min-w-0 flex-1">' +
                                        '<p class="text-sm font-bold text-gray-900 dark:text-white">' + name + '</p>' +
                                        (titleCompany ? '<p class="text-sm text-gray-600 dark:text-gray-400">' + titleCompany + '</p>' : '') +
                                        (relationship ? '<p class="text-xs text-gray-500 dark:text-gray-400">' + relationship + '</p>' : '') +
                                        (email || phone ? '<div class="mt-2 space-y-1">' +
                                            (email ? '<p class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400"><svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/></svg>' + email + '</p>' : '') +
                                            (phone ? '<p class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400"><svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z"/></svg>' + phone + '</p>' : '') +
                                        '</div>' : '') +
                                    '</div>' +
                                '</div>';
                            }).join('')}
                        </div>
                    </div>
                ` : ''}

                <!-- Attached Documents -->
                ${(function() {
                    const docs = [];
                    if (resumeUrl) {
                        const resumeName = application.resume_path ? application.resume_path.split('/').pop() : 'Resume';
                        docs.push({ name: resumeName, type: 'Resume', url: resumeUrl });
                    }
                    if (jobSeeker.cv_file_path) {
                        const cvPath = jobSeeker.cv_file_path;
                        const cvUrl = cvPath.startsWith('http') ? cvPath : '{{ rtrim(env("MEDIA_BASE_URL", rtrim(env("APP_URL", "http://127.0.0.1"), "/") . "/uploads"), "/") }}/' + cvPath;
                        const cvName = cvPath.split('/').pop();
                        if (!resumeUrl || cvName !== (application.resume_path || '').split('/').pop()) {
                            docs.push({ name: cvName, type: 'CV', url: cvUrl });
                        }
                    }
                    if (jobSeeker.certifications && Array.isArray(jobSeeker.certifications)) {
                        jobSeeker.certifications.forEach(cert => {
                            const certUrl = cert.certificate_url || cert.url || '';
                            if (certUrl) {
                                docs.push({ name: (cert.certification_name || cert.name || 'Certificate') + '.pdf', type: 'Certificate', url: certUrl });
                            }
                        });
                    }
                    if (docs.length === 0) return '';
                    return '<div class="border-b border-gray-200 dark:border-gray-700 pb-6">' +
                        '<h5 class="flex items-center gap-2 text-lg font-bold text-gray-900 dark:text-white mb-4">' +
                            '<svg class="w-5 h-5 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>' +
                            'Attached Documents' +
                        '</h5>' +
                        '<div class="grid grid-cols-2 gap-3">' +
                            docs.map(doc => {
                                return '<div class="flex items-center gap-3 border border-gray-200 dark:border-gray-700 rounded-lg px-4 py-3 bg-gray-50 dark:bg-gray-900">' +
                                    '<div class="w-10 h-10 rounded-lg bg-blue-50 flex items-center justify-center flex-shrink-0">' +
                                        '<svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>' +
                                    '</div>' +
                                    '<div class="min-w-0 flex-1">' +
                                        '<p class="text-sm font-semibold text-gray-900 dark:text-white truncate">' + doc.name + '</p>' +
                                        '<p class="text-xs text-gray-500 dark:text-gray-400">' + doc.type + '</p>' +
                                    '</div>' +
                                    '<div class="flex items-center gap-1.5 flex-shrink-0">' +
                                        '<a href="' + doc.url + '" download class="p-1.5 text-gray-400 hover:text-blue-600 rounded-md hover:bg-blue-50 transition" title="Download"><svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/></svg></a>' +
                                        '<a href="' + doc.url + '" target="_blank" class="p-1.5 text-gray-400 hover:text-blue-600 rounded-md hover:bg-blue-50 transition" title="Open"><svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"/></svg></a>' +
                                    '</div>' +
                                '</div>';
                            }).join('') +
                        '</div>' +
                    '</div>';
                })()}

                <!-- Cover Letter -->
                ${application.cover_letter ? `
                    <div class="border-b border-gray-200 dark:border-gray-700 pb-6">
                        <h5 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Cover Letter</h5>
                        <p class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed whitespace-pre-wrap">${application.cover_letter}</p>
                    </div>
                ` : ''}

                <!-- Update Status -->
                <div id="applicationModalStatusSection" class="border-b border-gray-200 dark:border-gray-700 pb-6">
                    <h5 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">Update Status</h5>
                    <div id="applicationModalStatusRadios" class="flex flex-wrap gap-2">
                        ${['pending','reviewing','shortlisted','interview_requested','hired','rejected'].map(s => `
                            <label class="inline-flex items-center gap-2 px-3 py-2 border rounded-lg cursor-pointer text-sm font-medium transition ${application.status === s ? 'bg-blue-50 border-blue-500 text-blue-700' : 'border-gray-200 text-gray-700 dark:text-gray-300 hover:bg-gray-50'}">
                                <input type="radio" name="status_${application.id}" value="${s}" ${application.status === s ? 'checked' : ''} onchange="updateApplicationStatus(${application.id}, '${s}')" class="w-4 h-4 text-blue-600 focus:ring-blue-500 status-radio">
                                ${(s.charAt(0).toUpperCase() + s.slice(1).replace('_', ' '))}
                            </label>
                        `).join('')}
                    </div>
                    <div id="applicationModalStatusLoading" class="hidden mt-2 text-sm text-blue-600 flex items-center gap-2"><span class="spinner w-4 h-4"></span> Updating...</div>
                </div>
                <!-- Request Interview -->
                <div class="border-b border-gray-200 dark:border-gray-700 pb-6">
                    <button type="button" id="requestInterviewBtn" onclick="showRequestInterviewForm()" class="px-4 py-2 bg-gradient-to-r from-blue-500 to-cyan-400 text-white rounded-lg hover:from-blue-600 hover:to-cyan-500 shadow-md transition font-medium text-sm flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/></svg>
                        Request Interview
                    </button>
                    <div id="requestInterviewFormContainer" style="display:none" class="mt-4 p-4 border border-gray-200 dark:border-gray-700 rounded-xl bg-gray-50 dark:bg-gray-900" data-application-id="">
                        <h6 class="text-sm font-semibold text-gray-800 dark:text-gray-100 mb-3">Schedule interview</h6>
                        <form id="requestInterviewForm" onsubmit="submitRequestInterview(event)" class="space-y-3">
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Date</label>
                                    <input type="date" name="scheduled_date" required class="w-full border border-gray-200 dark:border-gray-700 rounded-lg px-3 py-2 text-sm">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Time</label>
                                    <input type="time" name="scheduled_time" required class="w-full border border-gray-200 dark:border-gray-700 rounded-lg px-3 py-2 text-sm">
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Location (or video link)</label>
                                <input type="text" name="location" placeholder="e.g. Office, or Zoom link" class="w-full border border-gray-200 dark:border-gray-700 rounded-lg px-3 py-2 text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Notes</label>
                                <textarea name="notes" rows="2" placeholder="Optional instructions for the candidate" class="w-full border border-gray-200 dark:border-gray-700 rounded-lg px-3 py-2 text-sm"></textarea>
                            </div>
                            <div class="flex items-center gap-2">
                                <button type="submit" id="requestInterviewSubmitBtn" class="px-4 py-2 bg-gradient-to-r from-blue-500 to-cyan-400 text-white rounded-lg hover:from-blue-600 hover:to-cyan-500 shadow-md text-sm font-medium flex items-center gap-2">Send Request</button>
                                <button type="button" onclick="hideRequestInterviewForm()" class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 text-sm font-medium">Cancel</button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Applied On + Interview Status -->
                <div class="space-y-3">
                    <div class="flex flex-wrap items-center gap-4 bg-blue-50 rounded-xl px-4 py-3 border border-blue-100">
                        <div class="flex items-center gap-2 text-gray-700 dark:text-gray-300">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/></svg>
                            <span class="text-sm font-medium">Applied on ${new Date(application.created_at).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' })}</span>
                        </div>
                    </div>
                    ${application.interview_scheduled_at ? `
                    <div class="bg-indigo-50 border border-indigo-200 rounded-xl px-4 py-3 flex flex-col gap-1.5">
                        <div class="flex items-center justify-between gap-2">
                            <div class="flex items-center gap-2 text-indigo-800">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                <span class="text-sm font-semibold">Interview scheduled</span>
                            </div>
                            <span class="px-3 py-1 rounded-full text-xs font-semibold ${application.interview_status === 'accepted' ? 'bg-emerald-100 text-emerald-800' : (application.interview_status === 'declined' ? 'bg-red-100 text-red-800' : 'bg-indigo-100 text-indigo-800')}">
                                ${application.interview_status === 'accepted' ? 'Accepted' : (application.interview_status === 'declined' ? 'Declined' : 'Pending response')}
                            </span>
                        </div>
                        <div class="text-xs text-indigo-900">
                            <div><span class="font-medium">Date & Time:</span> ${new Date(application.interview_scheduled_at).toLocaleString('en-US', { year: 'numeric', month: 'short', day: 'numeric', hour: 'numeric', minute: '2-digit' })}</div>
                            ${application.interview_location ? `<div class="mt-0.5"><span class="font-medium">Location:</span> ${application.interview_location}</div>` : ''}
                            ${application.interview_notes ? `<div class="mt-0.5"><span class="font-medium">Notes:</span> ${application.interview_notes}</div>` : ''}
                            ${application.interview_status === 'declined' && application.interview_response_reason ? `<div class="mt-1.5 text-xs text-red-800"><span class="font-medium">Candidate reason:</span> ${application.interview_response_reason}</div>` : ''}
                        </div>
                    </div>
                    ` : ''}
                </div>
            </div>
        `;

        // Show footer and wire download (PDF = all applicant data)
        const footer = document.getElementById('applicationModalFooter');
        const downloadResumeLink = document.getElementById('applicationModalDownloadResume');
        const headerDownloadBtn = document.getElementById('applicationModalDownloadBtn');
        if (footer) footer.style.display = 'flex';
        const pdfUrl = `/employer/applications/${application.id}/pdf`;
        if (downloadResumeLink) {
            downloadResumeLink.href = pdfUrl;
            downloadResumeLink.style.display = 'inline-flex';
            downloadResumeLink.onclick = function(e) { handleDownloadPdf(e, application.id, this); };
        }
        if (headerDownloadBtn) {
            headerDownloadBtn.style.display = 'block';
            headerDownloadBtn.onclick = function(e) { e.preventDefault(); handleDownloadPdf(e, application.id, this); };
        }
        const formContainer = document.getElementById('requestInterviewFormContainer');
        if (formContainer) formContainer.dataset.applicationId = application.id;
        const requestBtn = document.getElementById('requestInterviewBtn');
        if (requestBtn) {
            const hasActiveInterview = !!application.interview_scheduled_at && application.interview_status !== 'declined';
            if (hasActiveInterview) {
                requestBtn.disabled = true;
                requestBtn.classList.add('opacity-60', 'cursor-not-allowed');
                requestBtn.classList.remove('hover:from-blue-600', 'hover:to-cyan-500', 'shadow-md');
                requestBtn.innerHTML = `
                    <span class="w-4 h-4 rounded-full border border-white border-opacity-60 mr-2"></span>
                    <span>Interview Requested</span>
                `;
            } else {
                requestBtn.disabled = false;
                requestBtn.classList.remove('opacity-60', 'cursor-not-allowed');
                requestBtn.classList.add('hover:from-blue-600', 'hover:to-cyan-500', 'shadow-md');
                requestBtn.innerHTML = `
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/>
                    </svg>
                    <span>Request Interview</span>
                `;
            }
        }
        const sendMsgBtn = document.getElementById('applicationModalSendMessage');
        if (sendMsgBtn && application.email) {
            sendMsgBtn.onclick = function() { window.location.href = 'mailto:' + application.email; };
        }
        } catch (error) {
            console.error('Error populating application modal:', error);
            const content = document.getElementById('applicationModalContent');
            content.innerHTML = `
                <div class="text-red-600 p-4">
                    <p class="font-medium mb-2">Error loading application details</p>
                    <p class="text-sm">${error.message || 'An unexpected error occurred'}</p>
                </div>
            `;
        }
    }

    function toggleSelect(checkbox) {
        const bar = document.getElementById('bulkActionBar');
        const checked = document.querySelectorAll('.app-checkbox:checked');
        const countEl = document.getElementById('selectedCount');
        if (checked.length > 0) {
            bar.classList.remove('hidden');
            bar.classList.add('flex');
            countEl.textContent = checked.length + ' selected';
        } else {
            bar.classList.add('hidden');
            bar.classList.remove('flex');
        }
    }

    function clearSelection() {
        document.querySelectorAll('.app-checkbox:checked').forEach(cb => cb.checked = false);
        const bar = document.getElementById('bulkActionBar');
        bar.classList.add('hidden');
        bar.classList.remove('flex');
    }

    const spinnerSvg = '<svg class="w-[18px] h-[18px] animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>';

    function exportSingle(id) {
        const btn = event.currentTarget;
        const orig = btn.innerHTML;
        btn.innerHTML = spinnerSvg;
        btn.disabled = true;
        window.location.href = `/employer/applications/export?ids=${id}`;
        setTimeout(() => { btn.innerHTML = orig; btn.disabled = false; }, 2000);
    }

    function exportSelected() {
        const ids = Array.from(document.querySelectorAll('.app-checkbox:checked')).map(cb => cb.value).join(',');
        if (!ids) return;
        const btn = event.currentTarget;
        const orig = btn.innerHTML;
        btn.innerHTML = spinnerSvg + ' <span>Exporting...</span>';
        btn.disabled = true;
        window.location.href = `/employer/applications/export?ids=${ids}`;
        setTimeout(() => { btn.innerHTML = orig; btn.disabled = false; }, 2000);
    }

    function toggleTalentPool(applicationId, btn) {
        const origHtml = btn.innerHTML;
        btn.innerHTML = spinnerSvg;
        btn.disabled = true;

        fetch(`/employer/applications/${applicationId}/talent-pool`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        })
        .then(r => r.json())
        .then(data => {
            btn.disabled = false;
            const isIn = data.in_talent_pool;
            btn.innerHTML = '<svg class="w-[18px] h-[18px]" fill="' + (isIn ? 'currentColor' : 'none') + '" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>';
            if (isIn) {
                btn.className = 'p-1.5 rounded-lg transition text-blue-600 bg-blue-50';
                btn.title = 'Remove from Talent Pool';
            } else {
                btn.className = 'p-1.5 rounded-lg transition text-gray-400 hover:text-blue-600 hover:bg-blue-50';
                btn.title = 'Add to Talent Pool';
            }
            const countEl = document.getElementById('talentPoolCount');
            if (countEl) {
                let c = parseInt(countEl.textContent) || 0;
                countEl.textContent = isIn ? c + 1 : Math.max(0, c - 1);
            }
            if (typeof window.showInfoToast === 'function') {
                window.showInfoToast(data.message, 2000);
            }
        })
        .catch(err => {
            btn.innerHTML = origHtml;
            btn.disabled = false;
            console.error('Talent pool toggle failed:', err);
        });
    }

    function closeApplicationModal() {
        document.getElementById('applicationModal').classList.add('hidden');
        hideRequestInterviewForm();
    }

    function handleDownloadPdf(e, applicationId, btn) {
        e.preventDefault();
        const orig = btn.innerHTML;
        btn.innerHTML = '<span class="spinner-sm inline-block mr-2 align-middle"></span><span>Downloading...</span>';
        btn.disabled = true;
        window.location.href = `/employer/applications/${applicationId}/pdf`;
        setTimeout(function() { btn.innerHTML = orig; btn.disabled = false; }, 2500);
    }

    function showRequestInterviewForm() {
        const container = document.getElementById('requestInterviewFormContainer');
        const requestBtn = document.getElementById('requestInterviewBtn');
        if (requestBtn && requestBtn.disabled) return;
        if (container) container.style.display = 'block';
    }
    function hideRequestInterviewForm() {
        const container = document.getElementById('requestInterviewFormContainer');
        if (container) container.style.display = 'none';
    }
    function submitRequestInterview(e) {
        e.preventDefault();
        const container = document.getElementById('requestInterviewFormContainer');
        const applicationId = container && container.dataset.applicationId ? container.dataset.applicationId : null;
        if (!applicationId) return;
        const form = document.getElementById('requestInterviewForm');
        const fd = new FormData(form);
        const submitBtn = document.getElementById('requestInterviewSubmitBtn');
        const origBtn = submitBtn ? submitBtn.innerHTML : '';
        if (submitBtn) { submitBtn.disabled = true; submitBtn.innerHTML = '<span class="spinner-sm inline-block mr-2 align-middle"></span><span>Sending...</span>'; }
        fetch(`/employer/applications/${applicationId}/request-interview`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                scheduled_date: fd.get('scheduled_date'),
                scheduled_time: fd.get('scheduled_time'),
                location: fd.get('location') || '',
                notes: fd.get('notes') || ''
            })
        })
        .then(r => r.json())
        .then(data => {
            if (submitBtn) { submitBtn.disabled = false; submitBtn.innerHTML = origBtn; }
            if (data.application) {
                hideRequestInterviewForm();
                populateApplicationModal(data.application);
                if (typeof window.showSuccessToast === 'function') window.showSuccessToast('Interview request sent. Applicant has been notified.');
                updateTableRowStatus(data.application);
            } else {
                if (typeof window.showErrorToast === 'function') window.showErrorToast(data.message || 'Failed to send request');
            }
        })
        .catch(err => {
            if (submitBtn) { submitBtn.disabled = false; submitBtn.innerHTML = origBtn; }
            if (typeof window.showErrorToast === 'function') window.showErrorToast('Request failed');
            console.error(err);
        });
    }

    async function updateApplicationStatus(applicationId, status) {
        const label = (status || '').replace(/_/g, ' ');
        const confirmed = await window.showConfirmDialog(
            'Change status to "' + (label.charAt(0).toUpperCase() + label.slice(1)) + '"?',
            {
                title: 'Update status',
                confirmText: 'Update status',
                cancelText: 'Cancel',
                danger: false
            }
        );
        if (!confirmed) return;

        const statusSection = document.getElementById('applicationModalStatusSection');
        const statusLoading = document.getElementById('applicationModalStatusLoading');
        const radios = document.querySelectorAll('.status-radio');
        if (statusSection) statusSection.classList.add('relative');
        if (statusLoading) { statusLoading.classList.remove('hidden'); statusLoading.classList.add('flex'); }
        if (radios.length) radios.forEach(r => { r.disabled = true; });

        fetch(`/employer/applications/${applicationId}/status`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ status: status })
        })
        .then(response => {
            // Check if response is ok
            if (!response.ok) {
                return response.json().then(err => Promise.reject(err));
            }
            return response.json();
        })
        .then(data => {
            if (statusLoading) { statusLoading.classList.add('hidden'); statusLoading.classList.remove('flex'); }
            if (radios.length) radios.forEach(r => { r.disabled = false; });
            if (data.application) {
                try { populateApplicationModal(data.application); } catch (err) { console.error(err); }
                try { updateTableRowStatus(data.application); } catch (err) { console.error(err); }
                if (typeof window.showSuccessToast === 'function') window.showSuccessToast('Application status updated successfully!');
            } else {
                if (typeof window.showErrorToast === 'function') window.showErrorToast('Failed to update status: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            if (statusLoading) { statusLoading.classList.add('hidden'); statusLoading.classList.remove('flex'); }
            if (radios.length) radios.forEach(r => { r.disabled = false; });
            console.error('Error:', error);
            if (typeof window.showErrorToast === 'function') window.showErrorToast(error.message || 'An error occurred while updating the status.');
        });
    }

    function updateTableRowStatus(application) {
        // Find the table row for this application
        const rows = document.querySelectorAll('tbody tr');
        
        rows.forEach(row => {
            const viewDetailsBtn = row.querySelector('button[onclick*="openApplicationModal"]');
            if (viewDetailsBtn && viewDetailsBtn.getAttribute('onclick').includes(`openApplicationModal(${application.id})`)) {
                // Update the status badge in the table
                const statusCell = row.querySelector('td:nth-child(5)');
                if (statusCell) {
                    const statusColors = {
                        'pending': 'bg-yellow-100 text-yellow-800',
                        'reviewing': 'bg-blue-100 text-blue-800',
                        'shortlisted': 'bg-purple-100 text-purple-800',
                        'interview_requested': 'bg-indigo-100 text-indigo-800',
                        'rejected': 'bg-red-100 text-red-800',
                        'hired': 'bg-green-100 text-green-800',
                    };
                    const statusColor = statusColors[application.status] || 'bg-gray-100 text-gray-800';
                    const statusText = (application.status || '').replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase());
                    
                    statusCell.innerHTML = `<span class="px-3 py-1 text-xs font-medium rounded ${statusColor}">${statusText}</span>`;
                }
            }
        });
    }


    // Close modal when clicking outside
    document.getElementById('applicationModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeApplicationModal();
        }
    });

    // Real-time search and filtering
    let searchTimeout;
    let currentFilters = {
        search: document.getElementById('searchInput').value,
        job_id: document.getElementById('jobFilter').value,
        status: document.getElementById('statusFilter').value
    };

    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    function loadApplications() {
        const searchInput = document.getElementById('searchInput');
        const jobFilter = document.getElementById('jobFilter');
        const statusFilter = document.getElementById('statusFilter');
        const applicationsList = document.getElementById('applicationsList');
        
        // Show loading state
        applicationsList.className = 'divide-y divide-gray-100 dark:divide-gray-700';
        applicationsList.innerHTML = `
            <div class="p-12 text-center">
                <div class="text-gray-500 dark:text-gray-400">
                    <div class="spinner mx-auto mb-4"></div>
                    <p class="text-lg font-medium">Loading applications...</p>
                </div>
            </div>
        `;
        
        const params = new URLSearchParams({
            search: searchInput.value,
            job_id: jobFilter.value || '',
            status: statusFilter.value || 'all'
        });
        
        fetch(`/employer/applications/data?${params.toString()}`, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.applications && data.applications.length > 0) {
                renderApplications(data.applications);
            } else {
                applicationsList.innerHTML = `
                    <div class="p-12 text-center">
                        <div class="text-gray-500 dark:text-gray-400">
                            <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <p class="text-lg font-medium">No applications found</p>
                            <p class="text-sm mt-1">Try adjusting your filters</p>
                        </div>
                    </div>
                `;
            }
        })
        .catch(error => {
            console.error('Error loading applications:', error);
            applicationsList.innerHTML = `
                <div class="p-12 text-center">
                    <div class="text-red-500">
                        <p class="text-lg font-medium">Error loading applications</p>
                        <p class="text-sm mt-1">Please try again</p>
                    </div>
                </div>
            `;
        });
    }

    function renderApplications(applications) {
        const applicationsList = document.getElementById('applicationsList');
        const statusColors = {
            'pending': 'bg-blue-50 text-blue-600 border border-blue-200',
            'reviewing': 'bg-purple-50 text-purple-600 border border-purple-200',
            'shortlisted': 'bg-green-50 text-green-600 border border-green-200',
            'interview_requested': 'bg-indigo-50 text-indigo-600 border border-indigo-200',
            'rejected': 'bg-red-50 text-red-600 border border-red-200',
            'hired': 'bg-green-50 text-green-600 border border-green-200',
        };
        const statusLabels = {
            'pending': 'new',
            'reviewing': 'interview',
            'shortlisted': 'shortlisted',
            'interview_requested': 'interview requested',
            'rejected': 'rejected',
            'hired': 'selected',
        };
        
        applicationsList.className = 'divide-y divide-gray-100 dark:divide-gray-700';
        applicationsList.innerHTML = applications.map(application => {
            const statusColor = statusColors[application.status] || 'bg-gray-50 text-gray-600 border border-gray-200';
            const statusLabel = statusLabels[application.status] || application.status;
            
            const inTalentPool = application.in_talent_pool || false;

            // Handle profile photo
            const jobSeeker = application.job_seeker || application.jobSeeker || {};
            let profilePhoto = null;
            let initials = application.initials || (application.first_name ? (application.first_name.charAt(0) + (application.last_name || '').charAt(0)).toUpperCase() : 'JD');
            
            if (jobSeeker && (jobSeeker.profile_photo || jobSeeker.profilePhoto)) {
                const photo = jobSeeker.profile_photo || jobSeeker.profilePhoto;
                if (photo.startsWith('http://') || photo.startsWith('https://')) {
                    profilePhoto = photo;
                } else {
                    profilePhoto = `{{ asset('storage/') }}/${photo}`;
                }
            }
            
            const gradients = ['from-blue-500 to-blue-600', 'from-purple-500 to-purple-600', 'from-rose-500 to-rose-600', 'from-amber-500 to-amber-600', 'from-teal-500 to-teal-600', 'from-indigo-500 to-indigo-600'];
            const gIdx = Math.abs((application.first_name + application.last_name).split('').reduce((a, c) => a + c.charCodeAt(0), 0)) % gradients.length;
            const avatarGradient = gradients[gIdx];

            const avatarHtml = profilePhoto 
                ? `<img src="${profilePhoto}" alt="${application.first_name} ${application.last_name}" class="w-11 h-11 rounded-xl object-cover flex-shrink-0" onerror="this.onerror=null; this.outerHTML='<div class=\\'w-11 h-11 rounded-xl bg-gradient-to-br ${avatarGradient} flex items-center justify-center text-white font-bold text-sm flex-shrink-0\\'>${initials}</div>';" />`
                : `<div class="w-11 h-11 rounded-xl bg-gradient-to-br ${avatarGradient} flex items-center justify-center text-white font-bold text-sm flex-shrink-0">${initials}</div>`;
            
            let resumeUrl = null;
            if (application.resume_path) {
                if (application.resume_path.startsWith('http://') || application.resume_path.startsWith('https://')) {
                    resumeUrl = application.resume_path;
                } else {
                    resumeUrl = `{{ rtrim(env('MEDIA_BASE_URL', rtrim(env('APP_URL', 'http://127.0.0.1'), '/') . '/uploads'), '/') }}/${application.resume_path}`;
                }
            }

            const jobId = application.job_id ? 'JOB-' + String(application.job_id).padStart(3, '0') : 'JOB-000';
            
            return `
                <div class="px-5 py-3.5 hover:bg-gray-50 dark:hover:bg-gray-700 transition cursor-pointer" onclick="openApplicationModal(${application.id})">
                    <div class="flex items-center gap-4">
                        <input type="checkbox" value="${application.id}" onclick="event.stopPropagation(); toggleSelect(this)" class="app-checkbox w-4 h-4 text-blue-600 rounded border-gray-300 dark:border-gray-600 focus:ring-blue-500 cursor-pointer flex-shrink-0">
                        ${avatarHtml}
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2.5 mb-0.5">
                                <h3 class="text-sm font-bold text-gray-900 dark:text-white">${application.first_name} ${application.last_name}</h3>
                                <span class="px-2 py-0.5 text-[11px] font-semibold rounded-full ${statusColor} whitespace-nowrap">${statusLabel}</span>
                            </div>
                            <div class="flex items-center gap-1.5 text-xs text-gray-500 dark:text-gray-400">
                                <span class="font-medium text-gray-700 dark:text-gray-300">${application.job_title}</span>
                                <span class="text-gray-300">|</span>
                                <svg class="w-3.5 h-3.5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                                <span>${jobId}</span>
                                <span class="text-gray-300">|</span>
                                <svg class="w-3.5 h-3.5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/></svg>
                                <span>${application.location}</span>
                            </div>
                        </div>
                        <div class="flex items-center gap-5 flex-shrink-0">
                            <div class="flex items-center gap-1.5 text-xs text-gray-500 dark:text-gray-400">
                                <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/></svg>
                                <span>${application.applied_date}</span>
                            </div>
                            <div class="flex items-center gap-1.5 ml-1">
                                <button onclick="event.stopPropagation(); toggleTalentPool(${application.id}, this)" class="p-1.5 rounded-lg transition ${inTalentPool ? 'text-blue-600 bg-blue-50' : 'text-gray-400 hover:text-blue-600 hover:bg-blue-50'}" title="${inTalentPool ? 'Remove from Talent Pool' : 'Add to Talent Pool'}">
                                    <svg class="w-[18px] h-[18px]" fill="${inTalentPool ? 'currentColor' : 'none'}" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                                </button>
                                <button onclick="event.stopPropagation(); exportSingle(${application.id})" class="p-1.5 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition" title="Export to Excel">
                                    <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/></svg>
                                </button>
                            </div>
                            <button onclick="event.stopPropagation(); openApplicationModal(${application.id})" class="px-4 py-1.5 bg-blue-500 text-white rounded-md hover:bg-blue-600 transition font-semibold text-xs whitespace-nowrap">
                                View
                            </button>
                            <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
                        </div>
                    </div>
                </div>
            `;
        }).join('');
    }

    function exportApplications() {
        const searchInput = document.getElementById('searchInput');
        const jobFilter = document.getElementById('jobFilter');
        const statusFilter = document.getElementById('statusFilter');
        
        const params = new URLSearchParams({
            search: searchInput.value,
            job_id: jobFilter.value || '',
            status: statusFilter.value || 'all'
        });
        
        window.location.href = `/employer/applications/export?${params.toString()}`;
    }

    // Real-time search with debouncing
    document.getElementById('searchInput').addEventListener('input', debounce(function() {
        loadApplications();
    }, 500));

    // Filter change handlers
    document.getElementById('jobFilter').addEventListener('change', function() {
        loadApplications();
    });

    document.getElementById('statusFilter').addEventListener('change', function() {
        loadApplications();
        // Update status tab styling
        const status = this.value;
        document.querySelectorAll('.status-tab-btn').forEach(btn => {
            btn.classList.remove('bg-blue-600', 'text-white');
            btn.classList.add('bg-white', 'text-gray-700', 'border', 'border-gray-200');
            const badge = btn.querySelector('span:last-child');
            if (badge) {
                badge.classList.remove('bg-blue-500');
                badge.classList.add('bg-gray-100');
            }
        });
        
        const activeBtn = document.querySelector(`[data-status="${status}"]`);
        if (activeBtn) {
            activeBtn.classList.add('bg-blue-600', 'text-white');
            activeBtn.classList.remove('bg-white', 'text-gray-700', 'border', 'border-gray-200');
            const badge = activeBtn.querySelector('span:last-child');
            if (badge) {
                badge.classList.add('bg-blue-500');
                badge.classList.remove('bg-gray-100');
            }
        }
    });

    // Update status tab filter
    function filterByStatus(status) {
        document.getElementById('statusFilter').value = status;
        loadApplications();
        
        // Update active tab styling
        document.querySelectorAll('.status-tab-btn').forEach(btn => {
            btn.classList.remove('bg-blue-600', 'text-white');
            btn.classList.add('bg-white', 'text-gray-700', 'border', 'border-gray-200');
            const badge = btn.querySelector('span:last-child');
            if (badge) {
                badge.classList.remove('bg-blue-500');
                badge.classList.add('bg-gray-100');
            }
        });
        
        const activeBtn = document.querySelector(`[data-status="${status}"]`);
        if (activeBtn) {
            activeBtn.classList.add('bg-blue-600', 'text-white');
            activeBtn.classList.remove('bg-white', 'text-gray-700', 'border', 'border-gray-200');
            const badge = activeBtn.querySelector('span:last-child');
            if (badge) {
                badge.classList.add('bg-blue-500');
                badge.classList.remove('bg-gray-100');
            }
        }
    }
</script>
@endpush
@endsection