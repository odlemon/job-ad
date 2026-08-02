@extends('layouts.job-seeker')

@section('content')
@php
    use App\Http\Controllers\JobApplicationController;
    $statusMeta = [
        'Applied' => [
            'color' => '#2563eb',
            'bg' => '#dbeafe',
            'darkBg' => 'rgba(30,58,138,.3)',
            'icon' => 'clock',
        ],
        'In Review' => [
            'color' => '#ca8a04',
            'bg' => '#fef9c3',
            'darkBg' => 'rgba(113,63,18,.3)',
            'icon' => 'eye',
        ],
        'Interview' => [
            'color' => '#9333ea',
            'bg' => '#f3e8ff',
            'darkBg' => 'rgba(88,28,135,.3)',
            'icon' => 'calendar',
        ],
        'Offered' => [
            'color' => '#16a34a',
            'bg' => '#dcfce7',
            'darkBg' => 'rgba(20,83,45,.3)',
            'icon' => 'check',
        ],
        'Rejected' => [
            'color' => '#dc2626',
            'bg' => '#fee2e2',
            'darkBg' => 'rgba(127,29,29,.3)',
            'icon' => 'x',
        ],
    ];
    $statOrder = ['Applied', 'In Review', 'Interview', 'Offered', 'Rejected'];
@endphp

<style>
.jat-main { flex:1; overflow-y:auto; }
.jat-wrap { max-width:80rem; margin:0 auto; padding:2rem 1rem; }
@media (min-width:640px){ .jat-wrap{ padding-left:1.5rem; padding-right:1.5rem; } }
@media (min-width:1024px){ .jat-wrap{ padding-left:2rem; padding-right:2rem; } }
.jat-stack { display:flex; flex-direction:column; gap:1.5rem; }

.jat-panel { background:#fff; border-radius:.75rem; padding:1.5rem; box-shadow:0 1px 2px rgba(0,0,0,.04); border:1px solid #e5e7eb; }
.dark .jat-panel { background:#1f2937; border-color:#374151; }
.jat-title { margin:0 0 1rem; font-size:1.5rem; font-weight:700; color:#111827; }
.dark .jat-title { color:#fff; }

.jat-stats { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:1rem; }
@media (min-width:768px){ .jat-stats{ grid-template-columns:repeat(5,minmax(0,1fr)); } }
.jat-stat {
    padding:1rem; border-radius:.5rem; border:1px solid #e5e7eb; background:transparent;
    cursor:pointer; text-align:left; transition:all .15s; width:100%;
}
.dark .jat-stat { border-color:#374151; }
.jat-stat:hover { border-color:#d1d5db; }
.dark .jat-stat:hover { border-color:#4b5563; }
.jat-stat.is-active { border-color:#2563eb; background:#eff6ff; }
.dark .jat-stat.is-active { border-color:#22d3ee; background:rgba(30,58,138,.2); }
.jat-stat-top { display:flex; align-items:center; gap:.5rem; margin-bottom:.5rem; }
.jat-stat-top svg { width:1rem; height:1rem; flex-shrink:0; }
.jat-stat-label { font-size:.75rem; font-weight:500; color:#4b5563; }
.dark .jat-stat-label { color:#9ca3af; }
.jat-stat-val { margin:0; font-size:1.5rem; font-weight:700; color:#111827; }
.dark .jat-stat-val { color:#fff; }

.jat-tabs { display:flex; flex-wrap:wrap; align-items:center; gap:.75rem; }
.jat-tab {
    padding:.5rem 1rem; border-radius:.5rem; font-weight:500; font-size:.875rem; cursor:pointer;
    border:1px solid #e5e7eb; background:#fff; color:#374151; transition:all .15s;
}
.dark .jat-tab { background:#1f2937; border-color:#374151; color:#d1d5db; }
.jat-tab:hover { border-color:#d1d5db; }
.dark .jat-tab:hover { border-color:#4b5563; }
.jat-tab.is-active {
    border:0; color:#fff;
    background:linear-gradient(to right,#2563eb,#06b6d4);
}

.jat-list { display:flex; flex-direction:column; gap:1rem; }
.jat-card {
    background:#fff; border-radius:.75rem; padding:1.5rem; box-shadow:0 1px 2px rgba(0,0,0,.04);
    border:1px solid #e5e7eb; transition:box-shadow .15s;
}
.dark .jat-card { background:#1f2937; border-color:#374151; }
.jat-card:hover { box-shadow:0 4px 6px -1px rgba(0,0,0,.08); }
.jat-card-row { display:flex; align-items:flex-start; gap:1rem; }
.jat-logo { width:4rem; height:4rem; border-radius:.5rem; object-fit:cover; flex-shrink:0; background:#f3f4f6; }
.dark .jat-logo { background:#374151; }
.jat-logo-fallback {
    width:4rem; height:4rem; border-radius:.5rem; flex-shrink:0;
    display:flex; align-items:center; justify-content:center;
    background:linear-gradient(to bottom right,#2563eb,#06b6d4); color:#fff; font-weight:700; font-size:1.25rem;
}
.jat-body { flex:1; min-width:0; }
.jat-head { display:flex; align-items:flex-start; justify-content:space-between; gap:1rem; margin-bottom:.5rem; }
.jat-job { margin:0; font-size:1.125rem; font-weight:600; color:#111827; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
.dark .jat-job { color:#fff; }
.jat-co { margin:.25rem 0 0; color:#4b5563; }
.dark .jat-co { color:#9ca3af; }
.jat-badge {
    display:inline-flex; align-items:center; gap:.25rem; padding:.25rem .75rem;
    border-radius:9999px; font-size:.875rem; font-weight:500; white-space:nowrap; flex-shrink:0;
}
.jat-badge svg { width:1rem; height:1rem; }
.jat-meta { display:grid; grid-template-columns:1fr; gap:.75rem; margin-top:1rem; }
@media (min-width:768px){ .jat-meta{ grid-template-columns:repeat(2,minmax(0,1fr)); } }
.jat-meta-item { display:flex; align-items:center; gap:.5rem; font-size:.875rem; color:#4b5563; }
.dark .jat-meta-item { color:#9ca3af; }
.jat-meta-item svg { width:1rem; height:1rem; flex-shrink:0; }

.jat-callout { margin-top:.75rem; padding:.75rem; border-radius:.5rem; border:1px solid; }
.jat-callout-interview { background:#faf5ff; border-color:#e9d5ff; color:#581c87; }
.dark .jat-callout-interview { background:rgba(88,28,135,.2); border-color:#6b21a8; color:#d8b4fe; }
.jat-callout-offer { background:#f0fdf4; border-color:#bbf7d0; color:#14532d; }
.dark .jat-callout-offer { background:rgba(20,83,45,.2); border-color:#166534; color:#86efac; }
.jat-callout-inner { display:flex; align-items:center; gap:.5rem; font-size:.875rem; font-weight:500; }
.jat-callout-inner svg { width:1rem; height:1rem; flex-shrink:0; }

.jat-notes {
    margin-top:.75rem; padding:.75rem; border-radius:.5rem; background:#f9fafb;
    display:flex; align-items:flex-start; gap:.5rem;
}
.dark .jat-notes { background:rgba(55,65,81,.5); }
.jat-notes svg { width:1rem; height:1rem; color:#6b7280; margin-top:.125rem; flex-shrink:0; }
.jat-notes p { margin:0; font-size:.875rem; color:#374151; }
.dark .jat-notes p { color:#d1d5db; }

.jat-actions { display:flex; align-items:center; gap:.75rem; margin-top:1rem; flex-wrap:wrap; }
.jat-btn-primary {
    padding:.5rem 1rem; background:#2563eb; color:#fff; border:0; border-radius:.5rem;
    font-size:.875rem; cursor:pointer; transition:background .15s;
}
.jat-btn-primary:hover { background:#1d4ed8; }
.jat-btn-secondary {
    padding:.5rem 1rem; background:transparent; color:#374151; border:1px solid #d1d5db;
    border-radius:.5rem; font-size:.875rem; cursor:pointer; transition:background .15s;
}
.dark .jat-btn-secondary { color:#d1d5db; border-color:#4b5563; }
.jat-btn-secondary:hover { background:#f9fafb; }
.dark .jat-btn-secondary:hover { background:#374151; }
.jat-btn-danger {
    padding:.5rem; color:#dc2626; background:transparent; border:0; border-radius:.5rem; cursor:pointer;
}
.jat-btn-danger:hover { background:#fef2f2; }
.dark .jat-btn-danger:hover { background:rgba(127,29,29,.2); }
.jat-btn-danger svg { width:1rem; height:1rem; display:block; }

.jat-empty {
    text-align:center; padding:3rem 1.5rem; background:#fff; border-radius:.5rem;
    border:1px solid #e5e7eb;
}
.dark .jat-empty { background:#1f2937; border-color:#374151; }
.jat-empty svg { width:3rem; height:3rem; color:#9ca3af; margin:0 auto 0.75rem; }
.jat-empty p { margin:0; color:#4b5563; }
.dark .jat-empty p { color:#9ca3af; }

/* Modals */
.jat-modal { display:none; position:fixed; inset:0; z-index:50; align-items:center; justify-content:center; padding:1rem; background:rgba(0,0,0,.4); backdrop-filter:blur(2px); }
.jat-modal.is-open { display:flex; }
.jat-modal-panel { background:#fff; border-radius:.75rem; width:100%; max-width:56rem; max-height:90vh; display:flex; flex-direction:column; overflow:hidden; box-shadow:0 25px 50px -12px rgba(0,0,0,.25); }
.dark .jat-modal-panel { background:#1f2937; }
.jat-modal-sm { max-width:28rem; }
.jat-modal-head { padding:1rem 1.5rem; border-bottom:1px solid #e5e7eb; display:flex; align-items:center; justify-content:space-between; background:#f7f8f9; flex-shrink:0; }
.dark .jat-modal-head { background:#111827; border-color:#374151; }
.jat-modal-head h3 { margin:0; font-size:1.125rem; font-weight:600; color:#111827; }
.dark .jat-modal-head h3 { color:#fff; }
.jat-modal-close { border:0; background:transparent; padding:.5rem; border-radius:.5rem; cursor:pointer; color:#6b7280; }
.jat-modal-close:hover { background:#e5e7eb; }
.dark .jat-modal-close:hover { background:#374151; }
.jat-modal-body { padding:1.5rem; overflow-y:auto; flex:1; }
.jat-modal-foot { padding:1rem 1.5rem; border-top:1px solid #e5e7eb; display:flex; align-items:center; justify-content:space-between; gap:1rem; background:#f7f8f9; flex-shrink:0; }
.dark .jat-modal-foot { background:#111827; border-color:#374151; }
.jat-textarea {
    width:100%; border:1px solid #d1d5db; border-radius:.5rem; padding:.75rem 1rem;
    font-size:.875rem; resize:vertical; min-height:6rem; box-sizing:border-box;
    background:#fff; color:#111827;
}
.dark .jat-textarea { background:#111827; border-color:#4b5563; color:#f9fafb; }
.jat-textarea:focus { outline:none; box-shadow:0 0 0 2px rgba(37,99,235,.35); border-color:#2563eb; }
</style>

@include('partials.job-seeker-navbar')

<main class="jat-main">
    <div class="jat-wrap">
        <div class="jat-stack">
            {{-- Header + clickable status stats (Bolt lb) --}}
            <div class="jat-panel">
                <h2 class="jat-title">Job Applications Tracker</h2>
                <div class="jat-stats" id="jat-stats">
                    @foreach($statOrder as $label)
                        @php $meta = $statusMeta[$label]; @endphp
                        <button type="button" class="jat-stat" data-filter="{{ $label }}">
                            <div class="jat-stat-top">
                                @if($meta['icon'] === 'clock')
                                    <svg style="color:{{ $meta['color'] }};" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                @elseif($meta['icon'] === 'eye')
                                    <svg style="color:{{ $meta['color'] }};" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                @elseif($meta['icon'] === 'calendar')
                                    <svg style="color:{{ $meta['color'] }};" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                @elseif($meta['icon'] === 'check')
                                    <svg style="color:{{ $meta['color'] }};" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                @else
                                    <svg style="color:{{ $meta['color'] }};" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                @endif
                                <span class="jat-stat-label">{{ $label }}</span>
                            </div>
                            <p class="jat-stat-val" data-stat="{{ $label }}">{{ $stats[$label] ?? 0 }}</p>
                        </button>
                    @endforeach
                </div>
            </div>

            {{-- Tabs --}}
            <div class="jat-tabs">
                <button type="button" class="jat-tab is-active" data-filter="all" onclick="jatSetFilter('all')">All Applications (<span id="jat-total">{{ $totalCount }}</span>)</button>
                <button type="button" class="jat-tab" data-filter="Interview" onclick="jatSetFilter('Interview')">Interviews</button>
                <button type="button" class="jat-tab" data-filter="Offered" onclick="jatSetFilter('Offered')">Offers</button>
            </div>

            {{-- Cards --}}
            <div class="jat-list" id="jat-list">
                @forelse($applications as $application)
                    @php
                        $job = $application->jobAdvertisement;
                        $company = $job->company ?? null;
                        $label = JobApplicationController::boltStatusLabel((string) $application->status);
                        $meta = $statusMeta[$label];
                        $companyName = $company->name ?? 'Unknown Company';
                        $logoUrl = null;
                        if ($company && $company->logo) {
                            $logoUrl = $company->logo;
                            if (!str_starts_with($logoUrl, 'http://') && !str_starts_with($logoUrl, 'https://')) {
                                $logoUrl = asset('storage/' . $logoUrl);
                            }
                        }
                        $info = is_array($application->additional_info) ? $application->additional_info : [];
                        $salaryOffered = $info['salary_offered'] ?? null;
                        if (!$salaryOffered && $label === 'Offered' && $job && ($job->salary_min || $job->salary_max)) {
                            $min = $job->salary_min ? number_format((float) $job->salary_min) : null;
                            $max = $job->salary_max ? number_format((float) $job->salary_max) : null;
                            if ($min && $max) {
                                $salaryOffered = 'SCR ' . $min . ' – ' . $max;
                            } elseif ($min || $max) {
                                $salaryOffered = 'SCR ' . ($min ?: $max);
                            }
                        }
                        $initial = strtoupper(substr($companyName, 0, 1));
                    @endphp
                    <div class="jat-card application-card"
                         data-id="{{ $application->id }}"
                         data-status="{{ $label }}"
                         data-raw-status="{{ $application->status }}"
                         data-notes="{{ e($application->notes ?? '') }}">
                        <div class="jat-card-row">
                            @if($logoUrl)
                                <img src="{{ $logoUrl }}" alt="{{ $companyName }}" class="jat-logo" onerror="this.onerror=null;this.outerHTML='<div class=\'jat-logo-fallback\'>{{ $initial }}</div>';">
                            @else
                                <div class="jat-logo-fallback">{{ $initial }}</div>
                            @endif

                            <div class="jat-body">
                                <div class="jat-head">
                                    <div style="min-width:0;flex:1;">
                                        <h3 class="jat-job">{{ $job->title ?? 'Job' }}</h3>
                                        <p class="jat-co">{{ $companyName }}</p>
                                    </div>
                                    <span class="jat-badge" style="background:{{ $meta['bg'] }};color:{{ $meta['color'] }};">
                                        @if($meta['icon'] === 'clock')
                                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        @elseif($meta['icon'] === 'eye')
                                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        @elseif($meta['icon'] === 'calendar')
                                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        @elseif($meta['icon'] === 'check')
                                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        @else
                                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        @endif
                                        {{ $label }}
                                    </span>
                                </div>

                                <div class="jat-meta">
                                    <div class="jat-meta-item">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        <span>Applied: {{ $application->created_at->format('M j, Y') }}</span>
                                    </div>
                                    <div class="jat-meta-item">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        <span>Updated: {{ $application->updated_at->diffForHumans() }}</span>
                                    </div>
                                </div>

                                @if($application->interview_scheduled_at)
                                    <div class="jat-callout jat-callout-interview">
                                        <div class="jat-callout-inner">
                                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                            Interview Scheduled: {{ $application->interview_scheduled_at->format('M j, Y \a\t g:i A') }}
                                        </div>
                                    </div>
                                @endif

                                @if($salaryOffered)
                                    <div class="jat-callout jat-callout-offer">
                                        <div class="jat-callout-inner">
                                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            Salary Offered: {{ $salaryOffered }}
                                        </div>
                                    </div>
                                @endif

                                <div class="jat-notes application-notes" data-application-id="{{ $application->id }}" @if(!($application->notes)) style="display:none;" @endif>
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    <p class="jat-note-text">{{ $application->notes }}</p>
                                </div>

                                <div class="jat-actions">
                                    <button type="button" class="jat-btn-primary" onclick="jatOpenDetails({{ $application->id }})">View Details</button>
                                    <button type="button" class="jat-btn-secondary" onclick="jatOpenNote({{ $application->id }})">Add Note</button>
                                    @if($label !== 'Rejected')
                                        <button type="button" class="jat-btn-danger" title="Withdraw application" onclick="jatConfirmDelete({{ $application->id }})" aria-label="Withdraw">
                                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="jat-empty" id="jat-empty-all">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <p>You haven't applied to any jobs yet.</p>
                        <p style="margin-top:1rem;"><a href="{{ url('/jobs') }}" class="jat-btn-primary" style="display:inline-block;text-decoration:none;">Browse Jobs</a></p>
                    </div>
                @endforelse
            </div>

            <div class="jat-empty" id="jat-empty-filter" style="display:none;">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <p>No applications found with this filter.</p>
            </div>
        </div>
    </div>
</main>

{{-- Detail modal --}}
<div id="jat-detail-modal" class="jat-modal" role="dialog" aria-modal="true">
    <div class="jat-modal-panel">
        <div class="jat-modal-head">
            <h3>Application Details</h3>
            <button type="button" class="jat-modal-close" onclick="jatCloseDetails()" aria-label="Close">
                <svg style="width:1.25rem;height:1.25rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div id="jat-detail-body" class="jat-modal-body">
            <p style="text-align:center;color:#6b7280;">Loading…</p>
        </div>
        <div class="jat-modal-foot">
            <button type="button" class="jat-btn-secondary" onclick="jatCloseDetails()">Close</button>
            <div style="display:flex;gap:.75rem;flex-wrap:wrap;">
                <a id="jat-view-job" href="#" target="_blank" class="jat-btn-primary" style="text-decoration:none;">View Job Posting</a>
                <button type="button" id="jat-withdraw-btn" class="jat-btn-secondary" style="color:#dc2626;border-color:#fca5a5;" onclick="jatWithdrawFromModal()">Withdraw</button>
            </div>
        </div>
    </div>
</div>

{{-- Note modal --}}
<div id="jat-note-modal" class="jat-modal" role="dialog" aria-modal="true">
    <div class="jat-modal-panel jat-modal-sm">
        <div class="jat-modal-head">
            <h3>Add Note</h3>
            <button type="button" class="jat-modal-close" onclick="jatCloseNote()" aria-label="Close">
                <svg style="width:1.25rem;height:1.25rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="jat-modal-body">
            <textarea id="jat-note-text" class="jat-textarea" placeholder="Add your note here…"></textarea>
            <div style="display:flex;gap:.75rem;margin-top:1rem;">
                <button type="button" id="jat-save-note" class="jat-btn-primary" style="flex:1;" onclick="jatSaveNote()">Save Note</button>
                <button type="button" class="jat-btn-secondary" onclick="jatCloseNote()">Cancel</button>
            </div>
        </div>
    </div>
</div>

{{-- Delete confirm --}}
<div id="jat-delete-modal" class="jat-modal" role="dialog" aria-modal="true">
    <div class="jat-modal-panel jat-modal-sm">
        <div class="jat-modal-head">
            <h3>Withdraw application?</h3>
            <button type="button" class="jat-modal-close" onclick="jatCloseDelete()" aria-label="Close">
                <svg style="width:1.25rem;height:1.25rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="jat-modal-body">
            <p style="margin:0;color:#4b5563;font-size:.875rem;">This will remove the application from your tracker. This cannot be undone.</p>
            <div style="display:flex;gap:.75rem;margin-top:1.25rem;justify-content:flex-end;">
                <button type="button" class="jat-btn-secondary" onclick="jatCloseDelete()">Cancel</button>
                <button type="button" id="jat-delete-confirm" class="jat-btn-primary" style="background:#dc2626;" onclick="jatDeleteNow()">Withdraw</button>
            </div>
        </div>
    </div>
</div>

{{-- Decline interview --}}
<div id="jat-decline-modal" class="jat-modal" role="dialog" aria-modal="true">
    <div class="jat-modal-panel jat-modal-sm">
        <div class="jat-modal-head">
            <h3>Decline interview</h3>
            <button type="button" class="jat-modal-close" onclick="jatCloseDecline()" aria-label="Close">
                <svg style="width:1.25rem;height:1.25rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="jat-modal-body">
            <p style="margin:0 0 .75rem;font-size:.875rem;color:#4b5563;">Optionally share a short reason with the employer.</p>
            <textarea id="jat-decline-reason" class="jat-textarea" placeholder="Reason (optional)"></textarea>
            <div style="display:flex;gap:.75rem;margin-top:1rem;justify-content:flex-end;">
                <button type="button" class="jat-btn-secondary" onclick="jatCloseDecline()">Cancel</button>
                <button type="button" class="jat-btn-primary" onclick="jatSubmitDecline()">Submit</button>
            </div>
        </div>
    </div>
</div>

<script>
(function () {
    var currentFilter = 'all';
    var noteAppId = null;
    var deleteAppId = null;
    var detailAppId = null;
    var declineAppId = null;
    var csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';

    function toastOk(msg) {
        if (typeof window.showSuccessToast === 'function') window.showSuccessToast(msg);
        else alert(msg);
    }
    function toastErr(msg) {
        if (typeof window.showErrorToast === 'function') window.showErrorToast(msg);
        else alert(msg);
    }

    window.jatSetFilter = function (filter, allowToggle) {
        if (allowToggle && filter !== 'all' && currentFilter === filter) {
            filter = 'all';
        }
        currentFilter = filter;

        document.querySelectorAll('.jat-stat').forEach(function (el) {
            el.classList.toggle('is-active', filter !== 'all' && el.dataset.filter === filter);
        });
        document.querySelectorAll('.jat-tab').forEach(function (el) {
            var isAll = filter === 'all' && el.dataset.filter === 'all';
            el.classList.toggle('is-active', isAll || el.dataset.filter === filter);
        });

        var visible = 0;
        document.querySelectorAll('.application-card').forEach(function (card) {
            var show = filter === 'all' || card.dataset.status === filter;
            card.style.display = show ? '' : 'none';
            if (show) visible++;
        });

        var emptyFilter = document.getElementById('jat-empty-filter');
        var hasAny = document.querySelectorAll('.application-card').length > 0;
        if (emptyFilter) {
            emptyFilter.style.display = (hasAny && visible === 0) ? '' : 'none';
        }
    };

    // Stat cards toggle filter on / off (Bolt behavior)
    document.querySelectorAll('.jat-stat').forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            jatSetFilter(btn.dataset.filter, true);
        });
        btn.removeAttribute('onclick');
    });

    window.jatOpenDetails = function (id) {
        detailAppId = id;
        var modal = document.getElementById('jat-detail-modal');
        var body = document.getElementById('jat-detail-body');
        modal.classList.add('is-open');
        body.innerHTML = '<p style="text-align:center;color:#6b7280;">Loading…</p>';

        fetch('/job-seeker/applications/' + id, {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(function (r) { return r.json(); })
        .then(function (data) {
            if (!data.application) {
                body.innerHTML = '<p style="color:#dc2626;">Failed to load application details.</p>';
                return;
            }
            jatRenderDetails(data.application);
        })
        .catch(function () {
            body.innerHTML = '<p style="color:#dc2626;">An error occurred while loading the application.</p>';
        });
    };

    function esc(s) {
        return String(s == null ? '' : s)
            .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    }

    function jatRenderDetails(app) {
        var body = document.getElementById('jat-detail-body');
        var job = app.job_advertisement || {};
        var company = job.company || {};
        var statusMap = {
            applied: 'Applied', pending: 'Applied', reviewing: 'In Review', in_review: 'In Review',
            interview: 'Interview', shortlisted: 'Interview', interview_requested: 'Interview',
            offered: 'Offered', hired: 'Offered', rejected: 'Rejected'
        };
        var label = statusMap[app.status] || 'Applied';
        var applied = app.created_at ? new Date(app.created_at).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' }) : '';
        var hasInterview = !!app.interview_scheduled_at;
        var interviewDate = hasInterview ? new Date(app.interview_scheduled_at).toLocaleString('en-US', { year: 'numeric', month: 'short', day: 'numeric', hour: 'numeric', minute: '2-digit' }) : '';
        var iStatus = app.interview_status || 'pending';
        var info = app.additional_info || {};
        var salary = info.salary_offered || '';

        body.innerHTML =
            '<div style="display:flex;flex-direction:column;gap:1.25rem;">' +
            '<div style="border:1px solid #e5e7eb;border-radius:.75rem;padding:1.25rem;">' +
            '<div style="display:flex;justify-content:space-between;gap:1rem;align-items:flex-start;">' +
            '<div><h4 style="margin:0;font-size:1.25rem;font-weight:700;color:#111827;">' + esc(job.title || 'Job') + '</h4>' +
            '<p style="margin:.25rem 0 0;color:#6b7280;">' + esc(company.name || 'Company') + '</p></div>' +
            '<span style="padding:.25rem .75rem;border-radius:9999px;font-size:.75rem;font-weight:600;background:#eff6ff;color:#2563eb;">' + esc(label) + '</span>' +
            '</div>' +
            '<p style="margin:.75rem 0 0;font-size:.875rem;color:#4b5563;">Applied on ' + esc(applied) + '</p>' +
            '</div>' +
            (hasInterview ? (
                '<div style="background:#eef2ff;border:1px solid #c7d2fe;border-radius:.75rem;padding:1rem;">' +
                '<div style="font-weight:600;color:#3730a3;margin-bottom:.5rem;">Interview Scheduled — ' + esc(interviewDate) + '</div>' +
                (app.interview_location ? '<div style="font-size:.875rem;color:#312e81;">Location: ' + esc(app.interview_location) + '</div>' : '') +
                (app.interview_notes ? '<div style="font-size:.875rem;color:#312e81;margin-top:.25rem;">Notes: ' + esc(app.interview_notes) + '</div>' : '') +
                (iStatus === 'pending' ? (
                    '<div style="display:flex;gap:.5rem;margin-top:.75rem;">' +
                    '<button type="button" class="jat-btn-primary" onclick="jatRespondInterview(' + app.id + ',\'accepted\')">Accept Interview</button>' +
                    '<button type="button" class="jat-btn-secondary" style="color:#dc2626;border-color:#fca5a5;" onclick="jatOpenDecline(' + app.id + ')">Decline</button>' +
                    '</div>'
                ) : ('<div style="margin-top:.5rem;font-size:.875rem;font-weight:500;">You ' + esc(iStatus) + ' this interview.</div>')) +
                '</div>'
            ) : '') +
            (salary ? '<div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:.75rem;padding:1rem;color:#14532d;font-weight:500;font-size:.875rem;">Salary Offered: ' + esc(salary) + '</div>' : '') +
            (app.cover_letter ? (
                '<div><h5 style="margin:0 0 .5rem;font-size:1rem;font-weight:700;">Your Cover Letter</h5>' +
                '<div style="background:#f9fafb;border:1px solid #e5e7eb;border-radius:.5rem;padding:1rem;font-size:.875rem;white-space:pre-wrap;">' + esc(app.cover_letter) + '</div></div>'
            ) : '') +
            (app.notes ? (
                '<div class="application-note-detail"><h5 style="margin:0 0 .5rem;font-size:1rem;font-weight:700;">Your Note</h5>' +
                '<div style="background:#f9fafb;border:1px solid #e5e7eb;border-radius:.5rem;padding:1rem;font-size:.875rem;">' + esc(app.notes) + '</div></div>'
            ) : '') +
            (job.description ? (
                '<div><h5 style="margin:0 0 .5rem;font-size:1rem;font-weight:700;">Job Description</h5>' +
                '<div style="font-size:.875rem;color:#374151;white-space:pre-wrap;">' + esc(job.description) + '</div></div>'
            ) : '') +
            '</div>';

        var viewJob = document.getElementById('jat-view-job');
        if (viewJob) viewJob.href = job.id ? ('/jobs/' + job.id) : '#';

        var withdraw = document.getElementById('jat-withdraw-btn');
        if (withdraw) {
            withdraw.style.display = label === 'Rejected' ? 'none' : '';
            withdraw.onclick = function () { jatConfirmDelete(app.id); };
        }
    }

    window.jatCloseDetails = function () {
        document.getElementById('jat-detail-modal').classList.remove('is-open');
        detailAppId = null;
    };

    window.jatWithdrawFromModal = function () {
        if (detailAppId) jatConfirmDelete(detailAppId);
    };

    window.jatOpenNote = function (id) {
        noteAppId = id;
        var card = document.querySelector('.application-card[data-id="' + id + '"]');
        var existing = card ? (card.dataset.notes || '') : '';
        document.getElementById('jat-note-text').value = existing;
        document.getElementById('jat-note-modal').classList.add('is-open');
        document.getElementById('jat-note-text').focus();
    };

    window.jatCloseNote = function () {
        document.getElementById('jat-note-modal').classList.remove('is-open');
        noteAppId = null;
    };

    window.jatSaveNote = function () {
        if (!noteAppId) return;
        var text = document.getElementById('jat-note-text').value;
        var btn = document.getElementById('jat-save-note');
        btn.disabled = true;
        btn.textContent = 'Saving…';

        fetch('/job-seeker/applications/' + noteAppId + '/notes', {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrf,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ notes: text })
        })
        .then(function (r) { return r.json().then(function (d) { return { ok: r.ok, d: d }; }); })
        .then(function (res) {
            btn.disabled = false;
            btn.textContent = 'Save Note';
            if (!res.ok) {
                toastErr(res.d.message || 'Failed to save note');
                return;
            }
            toastOk('Note saved successfully');
            var id = noteAppId;
            var card = document.querySelector('.application-card[data-id="' + id + '"]');
            if (card) {
                card.dataset.notes = text;
                var box = card.querySelector('.application-notes');
                var p = card.querySelector('.jat-note-text');
                if (box && p) {
                    p.textContent = text;
                    box.style.display = text.trim() ? '' : 'none';
                }
            }
            jatCloseNote();
        })
        .catch(function () {
            btn.disabled = false;
            btn.textContent = 'Save Note';
            toastErr('An error occurred while saving the note.');
        });
    };

    window.jatConfirmDelete = function (id) {
        deleteAppId = id;
        document.getElementById('jat-delete-modal').classList.add('is-open');
    };

    window.jatCloseDelete = function () {
        document.getElementById('jat-delete-modal').classList.remove('is-open');
        deleteAppId = null;
    };

    window.jatDeleteNow = function () {
        if (!deleteAppId) return;
        var id = deleteAppId;
        var btn = document.getElementById('jat-delete-confirm');
        btn.disabled = true;
        btn.textContent = 'Withdrawing…';

        fetch('/job-seeker/applications/' + id, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrf,
                'Accept': 'application/json'
            }
        })
        .then(function (r) { return r.json().then(function (d) { return { ok: r.ok, d: d }; }); })
        .then(function (res) {
            btn.disabled = false;
            btn.textContent = 'Withdraw';
            if (!res.ok) {
                toastErr(res.d.message || 'Failed to withdraw application');
                return;
            }
            toastOk('Application withdrawn successfully');
            jatCloseDelete();
            jatCloseDetails();
            var card = document.querySelector('.application-card[data-id="' + id + '"]');
            if (card) {
                var status = card.dataset.status;
                card.remove();
                var statEl = document.querySelector('[data-stat="' + status + '"]');
                if (statEl) {
                    var n = Math.max(0, parseInt(statEl.textContent, 10) - 1);
                    statEl.textContent = String(n);
                }
                var total = document.getElementById('jat-total');
                if (total) total.textContent = String(Math.max(0, parseInt(total.textContent, 10) - 1));
                jatSetFilter(currentFilter);
            }
        })
        .catch(function () {
            btn.disabled = false;
            btn.textContent = 'Withdraw';
            toastErr('An error occurred while withdrawing the application.');
        });
    };

    window.jatRespondInterview = function (id, response) {
        fetch('/job-seeker/applications/' + id + '/interview-response', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrf,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ response: response, reason: '' })
        })
        .then(function (r) { return r.json(); })
        .then(function (data) {
            if (data.application) {
                toastOk(response === 'accepted' ? 'Interview accepted!' : 'Interview declined.');
                jatOpenDetails(id);
            } else {
                toastErr(data.message || 'Failed to respond');
            }
        })
        .catch(function () { toastErr('An error occurred.'); });
    };

    window.jatOpenDecline = function (id) {
        declineAppId = id;
        document.getElementById('jat-decline-reason').value = '';
        document.getElementById('jat-decline-modal').classList.add('is-open');
    };

    window.jatCloseDecline = function () {
        document.getElementById('jat-decline-modal').classList.remove('is-open');
        declineAppId = null;
    };

    window.jatSubmitDecline = function () {
        if (!declineAppId) return;
        var reason = document.getElementById('jat-decline-reason').value;
        var id = declineAppId;
        fetch('/job-seeker/applications/' + id + '/interview-response', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrf,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ response: 'declined', reason: reason || '' })
        })
        .then(function (r) { return r.json(); })
        .then(function (data) {
            if (data.application) {
                toastOk('Interview declined.');
                jatCloseDecline();
                jatOpenDetails(id);
            } else {
                toastErr(data.message || 'Failed to respond');
            }
        })
        .catch(function () { toastErr('An error occurred.'); });
    };

    // Close modals on backdrop click
    ['jat-detail-modal', 'jat-note-modal', 'jat-delete-modal', 'jat-decline-modal'].forEach(function (id) {
        var el = document.getElementById(id);
        if (!el) return;
        el.addEventListener('click', function (e) {
            if (e.target === el) el.classList.remove('is-open');
        });
    });
})();
</script>
@endsection
