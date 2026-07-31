@extends('layouts.employer')

@section('content')
<div class="min-h-screen bg-white">
    @include('partials.employer-navbar')

    <div class="flex">
        @include('partials.employer-sidebar')

        <main class="flex-1 p-6 ml-64 w-0 min-w-0">
            <div class="w-full">
                {{-- Header (tight spacing with content below) --}}
                <div class="flex items-start justify-between mb-4">
                    <div>
                        <h1 class="text-2xl font-bold" style="color: #1A202C;">Job Campaigns</h1>
                        <p class="text-sm mt-1" style="color: #6B7280;">Monitor and optimize your job campaigns</p>
                    </div>
                    <a href="{{ route('employer.campaigns.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 text-white font-medium text-sm transition shadow-md rounded-md bg-gradient-to-r from-blue-500 to-cyan-400 hover:from-blue-600 hover:to-cyan-500">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Create Campaign
                    </a>
                </div>

                @if(session('success'))
                    <div class="mb-3 p-4 rounded-md bg-green-50 text-green-800 border border-green-200">{{ session('success') }}</div>
                @endif
                @if(session('error'))
                    <div class="mb-3 p-4 rounded-md bg-red-50 text-red-800 border border-red-200">{{ session('error') }}</div>
                @endif

                @if(!($hasAnyCampaigns ?? false))
                    {{-- Empty state: purple "Maximize Your Reach" banner (no campaigns at all) --}}
                    <div class="bg-white border border-gray-200 overflow-hidden mb-4 rounded-md">
                        <div class="relative overflow-hidden px-8 py-10 md:py-14 rounded-md" style="background: linear-gradient(135deg, #6B21A8 0%, #7F35E0 50%, #9333EA 100%);">
                            <div class="relative z-10 max-w-xl">
                                <h2 class="text-2xl md:text-3xl font-bold text-white mb-2">Maximize Your Reach</h2>
                                <p class="text-white/90 text-base mb-6">Promote your jobs to reach thousands of qualified candidates faster.</p>
                                <a href="{{ route('employer.campaigns.create') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-white font-semibold text-sm shadow-md transition hover:bg-gray-50 rounded-md" style="color: #7F35E0;">Learn More About Campaigns</a>
                            </div>
                            <div class="absolute right-8 top-1/2 -translate-y-1/2 w-40 h-40 opacity-20">
                                <svg class="w-full h-full text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 18l6-6 4 4 8-12"/></svg>
                            </div>
                        </div>
                    </div>
                    <p class="text-sm text-gray-500 text-center">Create your first campaign to see your dashboard here.</p>
                @else
                    {{-- KPI cards (larger, slight radius) --}}
                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3 mb-4">
                        @php
                            $kpis = [
                                ['label' => 'Active Job Listing', 'value' => $stats['active_job_listing'], 'icon' => 'megaphone', 'color' => '#3B82F6'],
                                ['label' => 'Total Views', 'value' => $stats['total_views'], 'icon' => 'eye', 'color' => '#9333EA'],
                                ['label' => 'Total Clicks', 'value' => $stats['total_clicks'], 'icon' => 'chart', 'color' => '#22C55E'],
                                ['label' => 'Applications', 'value' => $stats['total_applications'], 'icon' => 'users', 'color' => '#F97316'],
                                ['label' => 'Shares', 'value' => $stats['total_shares'], 'icon' => 'share', 'color' => '#0EA5E9'],
                                ['label' => 'Saved', 'value' => $stats['total_saved'], 'icon' => 'bookmark', 'color' => '#EF4444'],
                            ];
                        @endphp
                        @foreach($kpis as $kpi)
                            <div class="bg-white border border-gray-200 p-5 shadow-sm rounded-md">
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 flex items-center justify-center flex-shrink-0 rounded-md" style="background-color: {{ $kpi['color'] }}20;">
                                        @if($kpi['icon'] === 'megaphone')
                                            <svg class="w-6 h-6" style="color: {{ $kpi['color'] }};" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7z"/></svg>
                                        @elseif($kpi['icon'] === 'eye')
                                            <svg class="w-6 h-6" style="color: {{ $kpi['color'] }};" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        @elseif($kpi['icon'] === 'chart')
                                            <svg class="w-6 h-6" style="color: {{ $kpi['color'] }};" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                                        @elseif($kpi['icon'] === 'users')
                                            <svg class="w-6 h-6" style="color: {{ $kpi['color'] }};" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                                        @elseif($kpi['icon'] === 'share')
                                            <svg class="w-6 h-6" style="color: {{ $kpi['color'] }};" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/></svg>
                                        @else
                                            <svg class="w-6 h-6" style="color: {{ $kpi['color'] }};" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/></svg>
                                        @endif
                                    </div>
                                    <div>
                                        <p class="text-3xl font-bold" style="color: #1A202C;">{{ number_format($kpi['value']) }}</p>
                                        <p class="text-sm font-medium mt-0.5" style="color: #6B7280;">{{ $kpi['label'] }}</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Status tabs (real-time, no reload). Selected = black --}}
                    <div class="flex flex-wrap gap-0 p-0 bg-gray-100 mb-4 rounded-md overflow-hidden">
                        @foreach(['scheduled' => 'SCHEDULED', 'active' => 'ACTIVE', 'paused' => 'PAUSED', 'expired' => 'EXPIRED', 'draft' => 'DRAFT'] as $key => $label)
                            <button type="button" class="campaign-tab px-4 py-2.5 rounded-md text-sm font-medium transition border-0 cursor-pointer {{ ($statusTab ?? 'active') === $key ? 'text-white' : 'text-gray-600 hover:bg-gray-200' }}" style="{{ ($statusTab ?? 'active') === $key ? 'background-color: #000000;' : '' }}" data-status="{{ $key }}">{{ $label }} ({{ $statusCounts[$key] ?? 0 }})</button>
                        @endforeach
                    </div>

                    {{-- Filters (search = real-time, no form submit) --}}
                    <div class="flex flex-wrap items-center gap-3 mb-4">
                        <div class="relative flex-1 min-w-[200px]">
                            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            <input type="text" id="campaign-search" value="{{ $filters['search'] ?? '' }}" placeholder="Search Jobs" class="w-full pl-10 pr-4 py-2 border border-gray-300 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 rounded-md">
                        </div>
                        <input type="text" id="campaign-location" value="{{ $filters['location'] ?? '' }}" placeholder="Location" class="px-4 py-2 border border-gray-300 text-sm w-40 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 rounded-md">
                        <button type="button" id="campaign-reset" class="text-sm font-medium text-gray-500 hover:text-gray-700">Reset</button>
                        <div class="flex items-center gap-2">
                            <label class="text-sm text-gray-600">Sort by</label>
                            <select id="campaign-sort" class="px-3 py-2 border border-gray-300 text-sm focus:ring-2 focus:ring-blue-500 rounded-md">
                                <option value="most_recent">Most recent</option>
                                <option value="expiring_soon">Expiring soon</option>
                                <option value="title">Title</option>
                            </select>
                        </div>
                    </div>

                    {{-- Campaign list (one card per job). data-* for real-time filter --}}
                    <div id="campaign-list-empty" class="hidden bg-white border border-gray-200 p-12 text-center rounded-md">
                        <p class="text-gray-500">No campaigns match your filters. Try another status or search.</p>
                    </div>
                    <div id="campaign-list" class="space-y-4">
                        @foreach($jobs as $job)
                            @php
                                $primaryCampaign = $job->campaigns->first();
                                $campaignStatus = $primaryCampaign ? $primaryCampaign->status : 'active';
                                $campaignStats = [
                                    'views' => $primaryCampaign ? ($primaryCampaign->views_count ?? 0) : 0,
                                    'applications' => $job->applications_count ?? 0,
                                    'shares' => $primaryCampaign ? ($primaryCampaign->shares_count ?? 0) : 0,
                                    'messages' => $primaryCampaign ? ($primaryCampaign->messages_count ?? 0) : 0,
                                    'saved' => $primaryCampaign ? ($primaryCampaign->saved_count ?? 0) : 0,
                                    'invitation_sent' => $primaryCampaign ? ($primaryCampaign->invitation_sent_count ?? 0) : 0,
                                ];
                                $companyName = $job->company->name ?? 'Employer';
                                $jobLocation = $job->location ?: ($job->is_remote ? 'Remote' : '');
                            @endphp
                            <div class="campaign-card bg-white border border-gray-200 shadow-sm overflow-hidden rounded-md" data-campaign-status="{{ $campaignStatus }}" data-job-title="{{ strtolower(e($job->title)) }}" data-job-location="{{ strtolower(e($jobLocation)) }}">
                                <div class="p-6 flex flex-col md:flex-row md:items-start md:justify-between gap-4">
                                    <div class="flex-1 min-w-0">
                                        <h3 class="text-lg font-bold" style="color: #1A202C;">{{ $job->title }}</h3>
                                        <div class="flex flex-wrap items-center gap-2 mt-1">
                                            <span class="flex items-center gap-1 text-sm" style="color: #6B7280;">
                                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                                {{ $job->location ?: ($job->is_remote ? 'Remote' : 'Multiple locations') }}
                                            </span>
                                            <span class="inline-flex items-center px-2.5 py-0.5 text-xs font-medium text-white rounded-md" style="background-color: #0EA5E9;">Featured</span>
                                        </div>
                                        <p class="text-sm mt-2" style="color: #6B7280;">Posted on: {{ $job->created_at->format('d M Y') }}, by {{ $companyName }}</p>
                                        @if($primaryCampaign && $primaryCampaign->ends_at)
                                            <p class="text-sm" style="color: #6B7280;">Expiring on: {{ $primaryCampaign->ends_at->format('d M Y') }}</p>
                                        @endif
                                        {{-- Stats row --}}
                                        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-6 gap-3 mt-4">
                                            <div class="border border-gray-200 p-3 rounded-md bg-white"><p class="text-xl font-bold" style="color: #1A202C;">{{ $campaignStats['views'] }}</p><p class="text-xs" style="color: #6B7280;">Views</p></div>
                                            <div class="border border-gray-200 p-3 rounded-md bg-white"><p class="text-xl font-bold" style="color: #1A202C;">{{ $campaignStats['applications'] }}</p><p class="text-xs" style="color: #6B7280;">Applications</p></div>
                                            <div class="border border-gray-200 p-3 rounded-md bg-white"><p class="text-xl font-bold" style="color: #1A202C;">{{ $campaignStats['shares'] }}</p><p class="text-xs" style="color: #6B7280;">Shares</p></div>
                                            <div class="border border-gray-200 p-3 rounded-md bg-white"><p class="text-xl font-bold" style="color: #1A202C;">{{ $campaignStats['messages'] }}</p><p class="text-xs" style="color: #6B7280;">Messages</p></div>
                                            <div class="border border-gray-200 p-3 rounded-md bg-white"><p class="text-xl font-bold" style="color: #1A202C;">{{ $campaignStats['saved'] }}</p><p class="text-xs" style="color: #6B7280;">Saved this Job</p></div>
                                            <div class="border border-gray-200 p-3 rounded-md bg-white"><p class="text-xl font-bold" style="color: #1A202C;">{{ $campaignStats['invitation_sent'] }}</p><p class="text-xs" style="color: #6B7280;">Invitation Sent</p></div>
                                        </div>
                                    </div>
                                    {{-- Action buttons: sharp corners --}}
                                    <div class="flex flex-col gap-2 w-full md:w-44 flex-shrink-0">
                                        <a href="{{ route('employer.jobs.applicants', $job->id) }}" class="inline-flex items-center justify-center gap-2 w-full px-4 py-2.5 text-sm font-medium text-white rounded-md" style="background-color: #EC4899;">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                                            View applicants
                                        </a>
                                        <button type="button" onclick="openInviteModal({{ $job->id }}, '{{ addslashes($job->title) }}')" class="inline-flex items-center justify-center gap-2 w-full px-4 py-2 border border-gray-300 text-sm font-medium hover:bg-gray-50 rounded-md" style="color: #374151;">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                                            Invite applicants
                                        </button>
                                        @if($primaryCampaign)
                                            @php
                                                $currentType = $primaryCampaign->campaignType;
                                            @endphp
                                            <button type="button" onclick="openExtendModal({{ $primaryCampaign->id }}, '{{ addslashes($job->title) }}', {{ $currentType ? $currentType->id : 'null' }}, '{{ addslashes($currentType->name ?? '') }}')" class="inline-flex items-center justify-center gap-2 w-full px-4 py-2 border border-gray-300 text-sm font-medium hover:bg-gray-50 rounded-md" style="color: #374151;">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>
                                                Extend listing
                                            </button>
                                        @endif
                                        <a href="{{ route('employer.jobs.edit', $job->id) }}" class="inline-flex items-center justify-center gap-2 w-full px-4 py-2 border border-gray-300 text-sm font-medium hover:bg-gray-50 rounded-md" style="color: #374151;">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                            Edit job
                                        </a>
                                        @if($primaryCampaign)
                                            <button type="button" class="campaign-pause-btn inline-flex items-center justify-center gap-2 w-full px-4 py-2 border border-gray-300 text-sm font-medium hover:bg-gray-50 rounded-md" style="color: #374151;" data-campaign-id="{{ $primaryCampaign->id }}" data-current-status="{{ $primaryCampaign->status }}">
                                                <svg class="campaign-pause-icon w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                <span class="campaign-pause-text">{{ $primaryCampaign->status === 'paused' ? 'Resume listing' : 'Pause listing' }}</span>
                                            </button>
                                            <form action="{{ route('employer.campaigns.share', $primaryCampaign->id) }}" method="post" class="inline share-form" data-campaign-id="{{ $primaryCampaign->id }}">
                                                @csrf
                                                <button type="submit" class="inline-flex items-center justify-center gap-2 w-full px-4 py-2 border border-gray-300 text-sm font-medium hover:bg-gray-50 rounded-md" style="color: #374151;">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/></svg>
                                                    Share job post
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </main>
    </div>
</div>

{{-- Extend listing modal (add days + upgrade campaign type) --}}
<div id="extend-modal" class="fixed inset-0 z-50 hidden items-center justify-center p-4" style="background: rgba(0,0,0,0.35); backdrop-filter: blur(4px);">
    <div class="bg-white shadow-xl w-full max-w-md p-6 rounded-md" style="border: 1px solid #e5e7eb;">
        <h3 class="text-lg font-bold mb-1" style="color: #1A202C;">Extend listing</h3>
        <p id="extend-job-title" class="text-sm mb-1" style="color: #6B7280;"></p>
        <p id="extend-current-plan" class="text-xs mb-4" style="color: #9CA3AF;">Current plan: <span id="extend-current-plan-name"></span></p>
        <form id="extend-form">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1" style="color: #374151;">Upgrade to</label>
                <select id="extend-campaign-type" name="campaign_type_id" class="w-full px-4 py-2 border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 rounded-md">
                    <option value="">Keep current plan</option>
                    @foreach($campaignTypes ?? [] as $type)
                        <option value="{{ $type->id }}">{{ $type->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1" style="color: #374151;">Add days (optional)</label>
                <input type="number" id="extend-days" name="days" min="0" max="90" value="0" class="w-full px-4 py-2 border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 rounded-md">
            </div>
            <div class="flex gap-2 justify-end">
                <button type="button" onclick="closeExtendModal()" class="px-4 py-2 border border-gray-300 text-sm font-medium hover:bg-gray-50 rounded-md">Cancel</button>
                <button type="submit" id="extend-submit-btn" class="px-4 py-2 rounded-md text-sm font-medium text-white" style="background-color: #3B82F6;">Update listing</button>
            </div>
        </form>
    </div>
</div>

{{-- Invite applicants modal (talent pool) --}}
<div id="invite-modal" class="fixed inset-0 z-50 hidden items-center justify-center p-4" style="background: rgba(0,0,0,0.35); backdrop-filter: blur(4px);">
    <div class="bg-white shadow-xl w-full max-w-lg max-h-[90vh] flex flex-col rounded-md" style="border: 1px solid #e5e7eb;">
        <div class="p-4 border-b border-gray-200 flex items-center justify-between flex-shrink-0">
            <div>
                <h3 class="text-lg font-bold" style="color: #1A202C;">Invite applicants</h3>
                <p id="invite-modal-job-title" class="text-sm mt-0.5" style="color: #6B7280;"></p>
            </div>
            <button type="button" onclick="closeInviteModal()" class="p-2 text-gray-500 hover:text-gray-700 rounded-md">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div id="invite-modal-body" class="p-4 overflow-y-auto flex-1 min-h-0">
            <div id="invite-modal-loading" class="hidden py-8 text-center text-gray-500">
                <span class="inline-block w-8 h-8 border-2 border-gray-300 border-t-blue-600 rounded-full animate-spin"></span>
                <p class="mt-2 text-sm">Loading talent pool...</p>
            </div>
            <div id="invite-modal-empty" class="hidden py-8 text-center text-gray-500 text-sm">
                No applicants in the talent pool for this job. Add applicants from the job’s applicant list to the talent pool first.
            </div>
            <ul id="invite-modal-list" class="space-y-2 hidden"></ul>
        </div>
    </div>
</div>

<script>
(function() {
    // Tab key -> DB status (scheduled tab = pending status)
    var statusMap = { scheduled: 'pending', active: 'active', paused: 'paused', expired: 'expired', draft: 'draft' };
    var currentTab = '{{ $statusTab ?? "active" }}';

    function getSearch() { return (document.getElementById('campaign-search') && document.getElementById('campaign-search').value) || ''; }
    function getLocation() { return (document.getElementById('campaign-location') && document.getElementById('campaign-location').value) || ''; }

    function applyFilters() {
        var search = getSearch().toLowerCase().trim();
        var location = getLocation().toLowerCase().trim();
        var statusFilter = statusMap[currentTab];
        var cards = document.querySelectorAll('.campaign-card');
        var visible = 0;
        cards.forEach(function(card) {
            var cardStatus = (card.getAttribute('data-campaign-status') || '').toLowerCase();
            var title = (card.getAttribute('data-job-title') || '').toLowerCase();
            var loc = (card.getAttribute('data-job-location') || '').toLowerCase();
            var matchStatus = statusFilter ? cardStatus === statusFilter : true;
            var matchSearch = !search || title.indexOf(search) !== -1;
            var matchLocation = !location || loc.indexOf(location) !== -1;
            var show = matchStatus && matchSearch && matchLocation;
            card.style.display = show ? '' : 'none';
            if (show) visible++;
        });
        var emptyEl = document.getElementById('campaign-list-empty');
        var listEl = document.getElementById('campaign-list');
        if (emptyEl && listEl) {
            if (visible === 0) {
                emptyEl.classList.remove('hidden');
                listEl.classList.add('hidden');
            } else {
                emptyEl.classList.add('hidden');
                listEl.classList.remove('hidden');
            }
        }
    }

    function setActiveTab(tabKey) {
        currentTab = tabKey;
        document.querySelectorAll('.campaign-tab').forEach(function(btn) {
            var isActive = (btn.getAttribute('data-status') || '') === tabKey;
            btn.style.backgroundColor = isActive ? '#000000' : '';
            btn.style.color = isActive ? '#ffffff' : '';
            btn.classList.toggle('text-white', isActive);
            btn.classList.toggle('text-gray-600', !isActive);
        });
        applyFilters();
    }

    var searchEl = document.getElementById('campaign-search');
    var locationEl = document.getElementById('campaign-location');
    var resetEl = document.getElementById('campaign-reset');
    if (searchEl) {
        var searchTimeout;
        searchEl.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(applyFilters, 180);
        });
    }
    if (locationEl) {
        var locationTimeout;
        locationEl.addEventListener('input', function() {
            clearTimeout(locationTimeout);
            locationTimeout = setTimeout(applyFilters, 180);
        });
    }
    if (resetEl) {
        resetEl.addEventListener('click', function() {
            if (searchEl) searchEl.value = '';
            if (locationEl) locationEl.value = '';
            applyFilters();
        });
    }
    document.querySelectorAll('.campaign-tab').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var status = this.getAttribute('data-status');
            if (status) setActiveTab(status);
        });
    });

    // Initial filter on load (for pre-filled search/location and current tab)
    if (document.querySelector('.campaign-card')) {
        applyFilters();
    }

    // Pause/Resume listing: AJAX, no reload, loading spinner on button
    document.querySelectorAll('.campaign-pause-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var campaignId = this.getAttribute('data-campaign-id');
            var currentStatus = (this.getAttribute('data-current-status') || '').toLowerCase();
            if (!campaignId) return;
            var card = this.closest('.campaign-card');
            var icon = this.querySelector('.campaign-pause-icon');
            var textEl = this.querySelector('.campaign-pause-text');
            var origHtml = this.innerHTML;
            this.disabled = true;
            if (icon) icon.style.display = 'none';
            if (textEl) textEl.textContent = '';
            this.insertAdjacentHTML('beforeend', '<span class="inline-block w-4 h-4 border-2 border-gray-300 border-t-blue-600 rounded-full animate-spin ml-1"></span>');

            var url = '{{ url("/employer/campaigns") }}/' + campaignId + '/pause';
            fetch(url, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(function(r) { return r.json(); })
            .then(function(d) {
                var newStatus = (d.status || (currentStatus === 'paused' ? 'active' : 'paused')).toLowerCase();
                if (card) card.setAttribute('data-campaign-status', newStatus);
                btn.setAttribute('data-current-status', newStatus);
                if (textEl) textEl.textContent = newStatus === 'paused' ? 'Resume listing' : 'Pause listing';
                if (icon) { icon.style.display = ''; }
                var spinner = btn.querySelector('.animate-spin');
                if (spinner) spinner.remove();
                btn.disabled = false;
                applyFilters();
                if (window.showSuccessToast && d.message) window.showSuccessToast(d.message);
            })
            .catch(function() {
                btn.disabled = false;
                btn.innerHTML = origHtml;
            });
        });
    });
})();

var extendModalCampaignId = null;
function openExtendModal(campaignId, jobTitle, currentTypeId, currentTypeName) {
    extendModalCampaignId = campaignId;
    document.getElementById('extend-job-title').textContent = jobTitle || '';
    document.getElementById('extend-current-plan-name').textContent = currentTypeName || '—';
    var typeSelect = document.getElementById('extend-campaign-type');
    typeSelect.value = (currentTypeId && currentTypeId !== 'null') ? String(currentTypeId) : '';
    document.getElementById('extend-days').value = '0';
    document.getElementById('extend-modal').classList.remove('hidden');
    document.getElementById('extend-modal').classList.add('flex');
}
function closeExtendModal() {
    document.getElementById('extend-modal').classList.add('hidden');
    document.getElementById('extend-modal').classList.remove('flex');
    extendModalCampaignId = null;
}
document.getElementById('extend-modal').addEventListener('click', function(e) {
    if (e.target === this) closeExtendModal();
});
document.getElementById('extend-form').addEventListener('submit', function(e) {
    e.preventDefault();
    if (!extendModalCampaignId) return;
    var form = this;
    var submitBtn = document.getElementById('extend-submit-btn');
    var days = document.getElementById('extend-days').value;
    var campaignTypeId = document.getElementById('extend-campaign-type').value;
    var origBtnText = submitBtn.textContent;
    submitBtn.disabled = true;
    submitBtn.textContent = 'Updating…';
    var url = '{{ url("/employer/campaigns") }}/' + extendModalCampaignId + '/extend';
    var body = new FormData();
    body.append('_token', form.querySelector('input[name="_token"]').value);
    body.append('days', days || '0');
    body.append('campaign_type_id', campaignTypeId || '');
    fetch(url, {
        method: 'POST',
        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
        body: body
    })
    .then(function(r) { return r.json().then(function(d) { return { ok: r.ok, data: d }; }); })
    .then(function(res) {
        submitBtn.disabled = false;
        submitBtn.textContent = origBtnText;
        if (res.ok) {
            closeExtendModal();
            if (window.showSuccessToast && res.data.message) window.showSuccessToast(res.data.message);
            else alert(res.data.message);
            window.location.reload();
        } else {
            var msg = (res.data && res.data.message) || 'Update failed.';
            if (window.showSuccessToast) window.showSuccessToast(msg);
            else alert(msg);
        }
    })
    .catch(function() {
        submitBtn.disabled = false;
        submitBtn.textContent = origBtnText;
        if (window.showSuccessToast) window.showSuccessToast('Request failed. Try again.');
        else alert('Request failed. Try again.');
    });
});

// Invite applicants modal
var inviteModalJobId = null;
function openInviteModal(jobId, jobTitle) {
    inviteModalJobId = jobId;
    document.getElementById('invite-modal-job-title').textContent = jobTitle || '';
    document.getElementById('invite-modal').classList.remove('hidden');
    document.getElementById('invite-modal').classList.add('flex');
    document.getElementById('invite-modal-loading').classList.remove('hidden');
    document.getElementById('invite-modal-empty').classList.add('hidden');
    document.getElementById('invite-modal-list').classList.add('hidden');
    document.getElementById('invite-modal-list').innerHTML = '';

    var url = '{{ url("/employer/jobs") }}/' + jobId + '/talent-pool';
    fetch(url, {
        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        document.getElementById('invite-modal-loading').classList.add('hidden');
        var applicants = data.applicants || [];
        if (applicants.length === 0) {
            document.getElementById('invite-modal-empty').classList.remove('hidden');
            return;
        }
        var listEl = document.getElementById('invite-modal-list');
        listEl.classList.remove('hidden');
        applicants.forEach(function(a) {
            var li = document.createElement('li');
            li.className = 'flex items-center justify-between gap-3 p-3 border border-gray-200 rounded-md bg-gray-50';
            li.setAttribute('data-application-id', a.id);
            var invited = a.invited === true;
            var rightCell = invited
                ? '<span class="text-green-600 text-sm font-medium flex-shrink-0">Invited</span>'
                : '<button type="button" class="invite-applicant-btn flex-shrink-0 p-2 rounded-md text-gray-500 hover:bg-pink-50 hover:text-pink-600 transition" title="Send invite email" data-application-id="' + a.id + '">' +
                '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg></button>';
            if (invited) li.setAttribute('data-invited', '1');
            li.innerHTML = '<div class="min-w-0"><p class="font-medium text-gray-900 truncate">' + (a.first_name || '') + ' ' + (a.last_name || '') + '</p><p class="text-sm text-gray-500 truncate">' + (a.email || '') + '</p></div>' + rightCell;
            listEl.appendChild(li);
        });
        listEl.querySelectorAll('.invite-applicant-btn').forEach(function(btn) {
            btn.addEventListener('click', function() {
                var appId = this.getAttribute('data-application-id');
                if (!appId) return;
                var row = this.closest('li');
                if (row.getAttribute('data-invited') === '1') return;
                this.disabled = true;
                var origHtml = this.innerHTML;
                this.innerHTML = '<span class="inline-block w-5 h-5 border-2 border-gray-300 border-t-pink-500 rounded-full animate-spin"></span>';
                var inviteUrl = '{{ url("/employer/applications") }}/' + appId + '/invite';
                fetch(inviteUrl, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'X-Requested-With': 'XMLHttpRequest',
                        'Content-Type': 'application/json'
                    }
                })
                .then(function(r) {
                    return r.json().then(function(d) {
                        if (!r.ok) {
                            var errMsg = (d.error || d.message || 'Failed to send invite email');
                            throw new Error(errMsg);
                        }
                        return d;
                    });
                })
                .then(function(d) {
                    row.setAttribute('data-invited', '1');
                    btn.outerHTML = '<span class="text-green-600 text-sm font-medium flex-shrink-0">Invited</span>';
                    if (window.showSuccessToast && d.message) window.showSuccessToast(d.message);
                })
                .catch(function(err) {
                    btn.disabled = false;
                    btn.innerHTML = origHtml;
                    var msg = err && err.message ? err.message : 'Failed to send invite email';
                    if (window.showSuccessToast) window.showSuccessToast(msg);
                    else alert(msg);
                });
            });
        });
    })
    .catch(function() {
        document.getElementById('invite-modal-loading').classList.add('hidden');
        document.getElementById('invite-modal-empty').classList.remove('hidden');
        document.getElementById('invite-modal-empty').textContent = 'Failed to load talent pool. Try again.';
    });
}
function closeInviteModal() {
    document.getElementById('invite-modal').classList.add('hidden');
    document.getElementById('invite-modal').classList.remove('flex');
    inviteModalJobId = null;
}
document.getElementById('invite-modal').addEventListener('click', function(e) {
    if (e.target === this) closeInviteModal();
});

document.querySelectorAll('.share-form').forEach(function(form) {
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        var f = this;
        fetch(f.action, { method: 'POST', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' }, body: new FormData(f) })
            .then(function(r) { return r.json(); })
            .then(function(d) {
                if (d.url) {
                    if (navigator.share) {
                        navigator.share({ title: 'Job', url: d.url }).catch(function() { navigator.clipboard.writeText(d.url); if (window.showSuccessToast) window.showSuccessToast('Link copied to clipboard'); });
                    } else {
                        navigator.clipboard.writeText(d.url);
                        if (window.showSuccessToast) window.showSuccessToast('Link copied to clipboard');
                    }
                }
            });
    });
});
</script>
@endsection
