@extends('layouts.employer')

@section('content')
{{-- Critical layout/colors inline: Tailwind v4 often purges grid/flex utilities used only in Blade --}}
<style>
.camp-page { background: #f9fafb; min-height: 100vh; }
.camp-main { padding: 2rem; margin-left: 16rem; width: 100%; flex: 1; min-width: 0; }
.camp-kpis { display: grid; grid-template-columns: repeat(6, minmax(0, 1fr)); gap: 1rem; }
@media (max-width: 1280px) { .camp-kpis { grid-template-columns: repeat(3, minmax(0, 1fr)); } }
@media (max-width: 768px) { .camp-kpis { grid-template-columns: repeat(2, minmax(0, 1fr)); } .camp-main { margin-left: 0; padding: 1rem; } }
.camp-kpi { background: #fff; border: 1px solid #e5e7eb; border-radius: 0.25rem; padding: 1.5rem; box-shadow: 0 1px 2px rgba(0,0,0,.04); }
.camp-kpi-top { display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem; }
.camp-kpi-val { font-size: 1.875rem; font-weight: 700; color: #111827; line-height: 1.2; }
.camp-kpi-label { font-size: 0.875rem; color: #4b5563; margin: 0; }
.camp-panel { background: #fff; border: 1px solid #e5e7eb; border-radius: 0.25rem; box-shadow: 0 1px 2px rgba(0,0,0,.04); overflow: hidden; }
.camp-tabs { display: flex; align-items: center; gap: 0.5rem; padding: 0.5rem; overflow-x: auto; border-bottom: 1px solid #e5e7eb; }
.camp-tab { padding: 0.5rem 1rem; border-radius: 0.25rem; font-size: 0.875rem; font-weight: 500; white-space: nowrap; border: 0; cursor: pointer; background: transparent; color: #4b5563; }
.camp-tab.is-active { background: #111827; color: #fff; }
.camp-filters { padding: 1rem; border-bottom: 1px solid #e5e7eb; background: #f9fafb; display: flex; flex-wrap: wrap; gap: 0.75rem; align-items: center; }
.camp-input, .camp-select { padding: 0.5rem 1rem; border: 1px solid #e5e7eb; border-radius: 0.25rem; font-size: 0.875rem; background: #fff; color: #111827; }
.camp-search-wrap { position: relative; flex: 1; min-width: 200px; }
.camp-search-wrap svg { position: absolute; left: 0.75rem; top: 50%; transform: translateY(-50%); width: 1rem; height: 1rem; color: #9ca3af; }
.camp-search-wrap input { width: 100%; padding-left: 2.5rem; }
.camp-reset { padding: 0.5rem 1rem; border: 0; background: transparent; color: #db2777; font-size: 0.875rem; font-weight: 500; cursor: pointer; border-radius: 0.25rem; }
.camp-card { padding: 1.5rem; border-bottom: 1px solid #e5e7eb; }
.camp-card:last-child { border-bottom: 0; }
.camp-card:hover { background: #f9fafb; }
.camp-row { display: flex; gap: 1.5rem; align-items: flex-start; }
.camp-body { flex: 1; min-width: 0; }
.camp-title { font-size: 1.125rem; font-weight: 600; color: #111827; margin: 0 0 0.75rem; }
.camp-meta { display: flex; flex-wrap: wrap; align-items: center; gap: 0.75rem; margin-bottom: 0.75rem; font-size: 0.875rem; color: #4b5563; }
.camp-badge { display: inline-block; padding: 0.25rem 0.75rem; background: #2563eb; color: #fff; font-size: 0.75rem; font-weight: 600; border-radius: 0.375rem; }
.camp-dates { font-size: 0.875rem; color: #4b5563; margin-bottom: 1.5rem; line-height: 1.5; }
.camp-dates strong { color: #111827; font-weight: 500; }
.camp-stats { display: grid; grid-template-columns: repeat(6, minmax(0, 1fr)); gap: 0.75rem; }
@media (max-width: 1024px) { .camp-stats { grid-template-columns: repeat(3, minmax(0, 1fr)); } }
@media (max-width: 640px) { .camp-stats { grid-template-columns: repeat(2, minmax(0, 1fr)); } .camp-row { flex-direction: column; } .camp-actions { width: 100% !important; min-width: 0 !important; } }
.camp-stat { background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 0.5rem; padding: 1rem; }
.camp-stat-label { font-size: 0.75rem; font-weight: 500; color: #6b7280; margin: 0 0 0.5rem; }
.camp-stat-val { font-size: 1.5rem; font-weight: 700; color: #111827; margin: 0; line-height: 1.2; }
.camp-actions { display: flex; flex-direction: column; gap: 0.5rem; width: 200px; min-width: 200px; flex-shrink: 0; }
.camp-btn { display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem; width: 100%; padding: 0.625rem 1rem; border-radius: 0.5rem; font-size: 0.875rem; font-weight: 500; text-decoration: none; cursor: pointer; border: 1px solid #d1d5db; background: #fff; color: #374151; box-sizing: border-box; }
.camp-btn:hover { background: #f9fafb; }
.camp-btn-pink { background: #db2777; border-color: #db2777; color: #fff; }
.camp-btn-pink:hover { background: #be185d; }
.camp-banner { margin-top: 1.5rem; padding: 2rem; border-radius: 0.25rem; color: #fff; background: linear-gradient(to right, #7c3aed, #a855f7); box-shadow: 0 10px 15px -3px rgba(124,58,237,.25); }
.camp-banner h2 { font-size: 1.5rem; font-weight: 700; margin: 0 0 0.5rem; }
.camp-banner p { color: #ede9fe; margin: 0 0 1rem; }
.camp-banner a { display: inline-flex; padding: 0.75rem 1.5rem; background: #fff; color: #7c3aed; border-radius: 0.25rem; font-weight: 500; text-decoration: none; }
</style>
<div class="camp-page">
    @include('partials.employer-navbar')

    <div class="flex">
        @include('partials.employer-sidebar')

        <main class="camp-main">
            <div style="display:flex;flex-direction:column;gap:1.5rem;width:100%;">
                <div style="display:flex;align-items:center;justify-content:space-between;gap:1rem;flex-wrap:wrap;">
                    <div>
                        <h1 style="font-size:1.5rem;font-weight:700;color:#111827;margin:0;">Job Campaigns</h1>
                        <p style="color:#4b5563;margin:0.25rem 0 0;">Monitor and optimize your job campaigns</p>
                    </div>
                    <a href="{{ route('employer.campaigns.create') }}" class="camp-btn camp-btn-pink" style="width:auto;background:linear-gradient(to right,#2563eb,#06b6d4);border:0;">
                        <svg style="width:1.25rem;height:1.25rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Create Campaign
                    </a>
                </div>

                @if(session('success'))
                    <div style="padding:1rem;border-radius:0.25rem;background:#ecfdf5;color:#065f46;border:1px solid #a7f3d0;font-size:0.875rem;">{{ session('success') }}</div>
                @endif
                @if(session('error'))
                    <div style="padding:1rem;border-radius:0.25rem;background:#fef2f2;color:#991b1b;border:1px solid #fecaca;font-size:0.875rem;">{{ session('error') }}</div>
                @endif

                <div class="camp-kpis">
                    @php
                        $kpis = [
                            ['label' => 'Active Job Listing', 'value' => $stats['active_job_listing'] ?? 0, 'icon' => 'megaphone', 'color' => '#2563eb'],
                            ['label' => 'Total Views', 'value' => $stats['total_views'] ?? 0, 'icon' => 'eye', 'color' => '#7c3aed'],
                            ['label' => 'Total Clicks', 'value' => $stats['total_clicks'] ?? 0, 'icon' => 'chart', 'color' => '#059669'],
                            ['label' => 'Applications', 'value' => $stats['total_applications'] ?? 0, 'icon' => 'users', 'color' => '#ea580c'],
                            ['label' => 'Shares', 'value' => $stats['total_shares'] ?? 0, 'icon' => 'share', 'color' => '#0891b2'],
                            ['label' => 'Saved', 'value' => $stats['total_saved'] ?? 0, 'icon' => 'bookmark', 'color' => '#db2777'],
                        ];
                    @endphp
                    @foreach($kpis as $kpi)
                        <div class="camp-kpi">
                            <div class="camp-kpi-top">
                                @if($kpi['icon'] === 'megaphone')
                                    <svg style="width:2rem;height:2rem;color:{{ $kpi['color'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7z"/></svg>
                                @elseif($kpi['icon'] === 'eye')
                                    <svg style="width:2rem;height:2rem;color:{{ $kpi['color'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                @elseif($kpi['icon'] === 'chart')
                                    <svg style="width:2rem;height:2rem;color:{{ $kpi['color'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                                @elseif($kpi['icon'] === 'users')
                                    <svg style="width:2rem;height:2rem;color:{{ $kpi['color'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                                @elseif($kpi['icon'] === 'share')
                                    <svg style="width:2rem;height:2rem;color:{{ $kpi['color'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.368-2.684z"/></svg>
                                @else
                                    <svg style="width:2rem;height:2rem;color:{{ $kpi['color'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/></svg>
                                @endif
                                <span class="camp-kpi-val">{{ number_format($kpi['value']) }}</span>
                            </div>
                            <p class="camp-kpi-label">{{ $kpi['label'] }}</p>
                        </div>
                    @endforeach
                </div>

                <div class="camp-panel">
                    <div class="camp-tabs">
                        @foreach(['scheduled' => 'SCHEDULED', 'active' => 'ACTIVE', 'paused' => 'PAUSED', 'expired' => 'EXPIRED', 'draft' => 'DRAFT'] as $key => $label)
                            <button type="button" class="campaign-tab camp-tab {{ ($statusTab ?? 'active') === $key ? 'is-active' : '' }}" data-status="{{ $key }}">{{ $label }} ({{ $statusCounts[$key] ?? 0 }})</button>
                        @endforeach
                    </div>

                    <div class="camp-filters">
                        <div class="camp-search-wrap">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            <input type="text" id="campaign-search" class="camp-input" value="{{ $filters['search'] ?? '' }}" placeholder="Search Jobs">
                        </div>
                        <select id="campaign-posted-by" class="camp-select">
                            <option value="all">Posted By</option>
                            <option value="me">Me</option>
                            <option value="team">Team</option>
                        </select>
                        <input type="text" id="campaign-location" class="camp-input" style="width:10rem;" value="{{ $filters['location'] ?? '' }}" placeholder="Location">
                        <button type="button" id="campaign-reset" class="camp-reset">Reset</button>
                        <div style="display:flex;align-items:center;gap:0.5rem;margin-left:auto;">
                            <span style="font-size:0.875rem;color:#4b5563;">Sort by</span>
                            <select id="campaign-sort" class="camp-select">
                                <option value="most_recent">Most recent</option>
                                <option value="expiring_soon">Expiring soon</option>
                                <option value="title">Title</option>
                            </select>
                        </div>
                    </div>

                    <div id="campaign-list-empty" class="{{ ($hasAnyCampaigns ?? false) && $jobs->isNotEmpty() ? 'hidden' : '' }}" style="padding:3rem;text-align:center;color:#6b7280;">No campaigns found</div>

                    <div id="campaign-list">
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
                            <div class="campaign-card camp-card" data-campaign-status="{{ $campaignStatus }}" data-job-title="{{ strtolower(e($job->title)) }}" data-job-location="{{ strtolower(e($jobLocation)) }}">
                                <div class="camp-row">
                                    <div class="camp-body">
                                        <h3 class="camp-title">{{ $job->title }}</h3>
                                        <div class="camp-meta">
                                            <span style="display:inline-flex;align-items:center;gap:0.35rem;">
                                                <svg style="width:1rem;height:1rem;color:#9ca3af;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                                {{ $jobLocation ?: 'Multiple locations' }}
                                            </span>
                                            @if($primaryCampaign)
                                                <span class="camp-badge">Featured</span>
                                            @endif
                                        </div>
                                        <div class="camp-dates">
                                            <div>Posted on: <strong>{{ $job->created_at->format('d M Y') }}</strong>, by {{ $companyName }}</div>
                                            @if($primaryCampaign && $primaryCampaign->ends_at)
                                                <div>Expiring on: <strong>{{ $primaryCampaign->ends_at->format('d M Y') }}</strong></div>
                                            @endif
                                        </div>
                                        <div class="camp-stats">
                                            <div class="camp-stat"><p class="camp-stat-label">Views</p><p class="camp-stat-val">{{ number_format($campaignStats['views']) }}</p></div>
                                            <div class="camp-stat"><p class="camp-stat-label">Applications</p><p class="camp-stat-val">{{ number_format($campaignStats['applications']) }}</p></div>
                                            <div class="camp-stat"><p class="camp-stat-label">Shares</p><p class="camp-stat-val">{{ number_format($campaignStats['shares']) }}</p></div>
                                            <div class="camp-stat"><p class="camp-stat-label">Messages</p><p class="camp-stat-val">{{ number_format($campaignStats['messages']) }}</p></div>
                                            <div class="camp-stat"><p class="camp-stat-label">Saved this Job</p><p class="camp-stat-val">{{ number_format($campaignStats['saved']) }}</p></div>
                                            <div class="camp-stat"><p class="camp-stat-label">Invitation Sent</p><p class="camp-stat-val">{{ number_format($campaignStats['invitation_sent']) }}</p></div>
                                        </div>
                                    </div>
                                    <div class="camp-actions">
                                        <a href="{{ route('employer.jobs.applicants', $job->id) }}" class="camp-btn camp-btn-pink">
                                            <svg style="width:1rem;height:1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                                            View applicants
                                        </a>
                                        <button type="button" class="camp-btn" onclick="openInviteModal({{ $job->id }}, {{ json_encode($job->title) }})">
                                            <svg style="width:1rem;height:1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                                            Invite applicants
                                        </button>
                                        @if($primaryCampaign)
                                            @php $currentType = $primaryCampaign->campaignType; @endphp
                                            <button type="button" class="camp-btn" onclick="openExtendModal({{ $primaryCampaign->id }}, {{ json_encode($job->title) }}, {{ $currentType ? $currentType->id : 'null' }}, {{ json_encode($currentType->name ?? '') }})">
                                                <svg style="width:1rem;height:1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>
                                                Extend listing
                                            </button>
                                        @endif
                                        <a href="{{ route('employer.jobs.edit', $job->id) }}" class="camp-btn">
                                            <svg style="width:1rem;height:1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                            Edit job
                                        </a>
                                        @if($primaryCampaign)
                                            <button type="button" class="campaign-pause-btn camp-btn" data-campaign-id="{{ $primaryCampaign->id }}" data-current-status="{{ $primaryCampaign->status }}">
                                                <svg class="campaign-pause-icon" style="width:1rem;height:1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                <span class="campaign-pause-text">{{ $primaryCampaign->status === 'paused' ? 'Resume listing' : 'Pause listing' }}</span>
                                            </button>
                                            <form action="{{ route('employer.campaigns.share', $primaryCampaign->id) }}" method="post" class="share-form" data-campaign-id="{{ $primaryCampaign->id }}" style="margin:0;">
                                                @csrf
                                                <button type="submit" class="camp-btn">
                                                    <svg style="width:1rem;height:1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.368-2.684z"/></svg>
                                                    Share job post
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="camp-banner">
                    <div style="display:flex;align-items:center;justify-content:space-between;gap:1rem;flex-wrap:wrap;">
                        <div style="flex:1;">
                            <h2>Maximize Your Reach</h2>
                            <p>Promote your jobs to reach thousands of qualified candidates faster</p>
                            <a href="{{ route('employer.campaigns.create') }}">Learn More About Campaigns</a>
                        </div>
                        <svg style="width:8rem;height:8rem;opacity:0.2;flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>
{{-- Extend listing modal (add days + upgrade campaign type) --}}
<div id="extend-modal" class="fixed inset-0 z-50 hidden items-center justify-center p-4" style="background: rgba(0,0,0,0.35); backdrop-filter: blur(4px);">
    <div class="bg-white dark:bg-gray-800 shadow-xl w-full max-w-md p-6 rounded-md" style="border: 1px solid #e5e7eb;">
        <h3 class="text-lg font-bold mb-1" style="color: #1A202C;">Extend listing</h3>
        <p id="extend-job-title" class="text-sm mb-1" style="color: #6B7280;"></p>
        <p id="extend-current-plan" class="text-xs mb-4" style="color: #9CA3AF;">Current plan: <span id="extend-current-plan-name"></span></p>
        <form id="extend-form">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1" style="color: #374151;">Upgrade to</label>
                <select id="extend-campaign-type" name="campaign_type_id" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 rounded-md">
                    <option value="">Keep current plan</option>
                    @foreach($campaignTypes ?? [] as $type)
                        <option value="{{ $type->id }}">{{ $type->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1" style="color: #374151;">Add days (optional)</label>
                <input type="number" id="extend-days" name="days" min="0" max="90" value="0" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 rounded-md">
            </div>
            <div class="flex gap-2 justify-end">
                <button type="button" onclick="closeExtendModal()" class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium hover:bg-gray-50 dark:hover:bg-gray-700 rounded-md">Cancel</button>
                <button type="submit" id="extend-submit-btn" class="px-4 py-2 rounded-md text-sm font-medium text-white" style="background-color: #3B82F6;">Update listing</button>
            </div>
        </form>
    </div>
</div>

{{-- Invite applicants modal (talent pool) --}}
<div id="invite-modal" class="fixed inset-0 z-50 hidden items-center justify-center p-4" style="background: rgba(0,0,0,0.35); backdrop-filter: blur(4px);">
    <div class="bg-white dark:bg-gray-800 shadow-xl w-full max-w-lg max-h-[90vh] flex flex-col rounded-md" style="border: 1px solid #e5e7eb;">
        <div class="p-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between flex-shrink-0">
            <div>
                <h3 class="text-lg font-bold" style="color: #1A202C;">Invite applicants</h3>
                <p id="invite-modal-job-title" class="text-sm mt-0.5" style="color: #6B7280;"></p>
            </div>
            <button type="button" onclick="closeInviteModal()" class="p-2 text-gray-500 dark:text-gray-400 hover:text-gray-700 rounded-md">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div id="invite-modal-body" class="p-4 overflow-y-auto flex-1 min-h-0">
            <div id="invite-modal-loading" class="hidden py-8 text-center text-gray-500 dark:text-gray-400">
                <span class="inline-block w-8 h-8 border-2 border-gray-300 dark:border-gray-600 border-t-blue-600 rounded-full animate-spin"></span>
                <p class="mt-2 text-sm">Loading talent pool...</p>
            </div>
            <div id="invite-modal-empty" class="hidden py-8 text-center text-gray-500 dark:text-gray-400 text-sm">
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
            btn.classList.toggle('is-active', isActive);
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
            this.insertAdjacentHTML('beforeend', '<span class="inline-block w-4 h-4 border-2 border-gray-300 dark:border-gray-600 border-t-blue-600 rounded-full animate-spin ml-1"></span>');

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
            li.className = 'flex items-center justify-between gap-3 p-3 border border-gray-200 dark:border-gray-700 rounded-md bg-gray-50 dark:bg-gray-900';
            li.setAttribute('data-application-id', a.id);
            var invited = a.invited === true;
            var rightCell = invited
                ? '<span class="text-green-600 text-sm font-medium flex-shrink-0">Invited</span>'
                : '<button type="button" class="invite-applicant-btn flex-shrink-0 p-2 rounded-md text-gray-500 dark:text-gray-400 hover:bg-pink-50 hover:text-pink-600 transition" title="Send invite email" data-application-id="' + a.id + '">' +
                '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg></button>';
            if (invited) li.setAttribute('data-invited', '1');
            li.innerHTML = '<div class="min-w-0"><p class="font-medium text-gray-900 dark:text-white truncate">' + (a.first_name || '') + ' ' + (a.last_name || '') + '</p><p class="text-sm text-gray-500 dark:text-gray-400 truncate">' + (a.email || '') + '</p></div>' + rightCell;
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
                this.innerHTML = '<span class="inline-block w-5 h-5 border-2 border-gray-300 dark:border-gray-600 border-t-pink-500 rounded-full animate-spin"></span>';
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
        var card = f.closest('.campaign-card');
        fetch(f.action, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: new FormData(f)
        })
            .then(function(r) { return r.json(); })
            .then(function(d) {
                if (card) {
                    var stats = card.querySelectorAll('.camp-stat-val');
                    // Shares is 3rd stat tile (0 Views, 1 Apps, 2 Shares)
                    if (stats && stats[2]) {
                        var n = parseInt(String(stats[2].textContent).replace(/,/g, ''), 10) || 0;
                        stats[2].textContent = (n + 1).toLocaleString();
                    }
                }
                if (d.url) {
                    if (navigator.clipboard && navigator.clipboard.writeText) {
                        navigator.clipboard.writeText(d.url).then(function() {
                            if (window.showSuccessToast) window.showSuccessToast('Share link copied');
                            else alert('Share link copied');
                        }).catch(function() {
                            if (window.showSuccessToast) window.showSuccessToast(d.url);
                            else prompt('Copy share link:', d.url);
                        });
                    } else if (window.showSuccessToast) {
                        window.showSuccessToast(d.url);
                    } else {
                        prompt('Copy share link:', d.url);
                    }
                } else if (d.message && window.showSuccessToast) {
                    window.showSuccessToast(d.message);
                }
            })
            .catch(function() {
                if (window.showSuccessToast) window.showSuccessToast('Share failed. Try again.');
                else alert('Share failed. Try again.');
            });
    });
});
</script>
@endsection