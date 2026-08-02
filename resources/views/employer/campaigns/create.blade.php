@extends('layouts.employer')

@section('content')
{{-- Scoped CSS: match Bolt create-campaign states; avoid Tailwind purge --}}
<style>
.cc-page { background:#f9fafb; min-height:100vh; }
.cc-main { padding:2rem; margin-left:16rem; width:100%; flex:1; min-width:0; }
@media (max-width:768px){ .cc-main{ margin-left:0; padding:1rem; } }
.cc-header { display:flex; align-items:center; justify-content:space-between; gap:1rem; flex-wrap:wrap; margin-bottom:1.5rem; }
.cc-title { font-size:1.5rem; font-weight:700; color:#111827; margin:0; }
.cc-sub { color:#4b5563; margin:0.25rem 0 0; }
.cc-coins { display:inline-flex; align-items:center; gap:0.5rem; padding:0.5rem 1rem; color:#fff; border-radius:0.25rem; background:linear-gradient(to right,#f59e0b,#f97316); box-shadow:0 10px 15px -3px rgba(245,158,11,.35); }
.cc-grid { display:grid; grid-template-columns:2fr 1fr; gap:1.5rem; align-items:start; }
@media (max-width:1024px){ .cc-grid{ grid-template-columns:1fr; } }
.cc-panel { background:#fff; border:1px solid #e5e7eb; border-radius:0.25rem; box-shadow:0 1px 2px rgba(0,0,0,.04); }
.cc-panel-h { padding:1.5rem; border-bottom:1px solid #e5e7eb; display:flex; align-items:flex-start; justify-content:space-between; gap:1rem; }
.cc-panel-h h2 { font-size:1.125rem; font-weight:700; color:#111827; margin:0; }
.cc-panel-h p { font-size:0.875rem; color:#4b5563; margin:0.25rem 0 0; }
.cc-btn-blue { display:inline-flex; align-items:center; gap:0.5rem; padding:0.5rem 1rem; background:#2563eb; color:#fff; border:0; border-radius:0.25rem; font-weight:500; font-size:0.875rem; cursor:pointer; white-space:nowrap; }
.cc-btn-blue:hover { background:#1d4ed8; }
.cc-jobs { padding:1.5rem; display:flex; flex-direction:column; gap:1rem; }
.cc-job { border:2px solid #e5e7eb; border-radius:0.5rem; transition:all .15s; background:#fff; }
.cc-job.is-selected { border-color:#3b82f6; background:#eff6ff; }
.cc-job-inner { padding:1rem; }
.cc-job-row { display:flex; align-items:flex-start; gap:0.75rem; }
.cc-job-row input[type=checkbox] { margin-top:0.25rem; width:1.25rem; height:1.25rem; accent-color:#2563eb; cursor:pointer; flex-shrink:0; }
.cc-job-title { font-weight:700; color:#111827; margin:0; }
.cc-job-loc { font-size:0.875rem; color:#4b5563; margin:0.15rem 0 0; }
.cc-job-badge { display:none; padding:0.25rem 0.75rem; background:#2563eb; color:#fff; font-size:0.75rem; font-weight:700; border-radius:0.25rem; flex-shrink:0; }
.cc-job-badge.is-on { display:inline-flex; align-items:center; }
.cc-job-opts { display:none; margin-top:1rem; padding-top:1rem; border-top:1px solid #e5e7eb; }
.cc-job.is-selected .cc-job-opts { display:block; }
.cc-label { font-size:0.875rem; font-weight:600; color:#374151; margin:0 0 0.75rem; }
.cc-types { display:grid; grid-template-columns:repeat(3,minmax(0,1fr)); gap:0.75rem; }
@media (max-width:640px){ .cc-types{ grid-template-columns:1fr; } }
.cc-type { position:relative; padding:1rem; border-radius:0.5rem; border:2px solid #e5e7eb; background:#fff; text-align:left; cursor:pointer; transition:all .15s; }
.cc-type:hover { border-color:#60a5fa; }
.cc-type.is-active { border-color:#2563eb; background:#fff; box-shadow:0 4px 6px -1px rgba(0,0,0,.1); }
.cc-type-popular { position:absolute; top:-0.5rem; left:50%; transform:translateX(-50%); padding:0.125rem 0.5rem; font-size:0.65rem; font-weight:700; color:#fff; border-radius:0.25rem; background:linear-gradient(to right,#7c3aed,#a855f7); white-space:nowrap; }
.cc-type-icon { width:2.5rem; height:2.5rem; border-radius:0.5rem; display:flex; align-items:center; justify-content:center; margin-bottom:0.5rem; color:#fff; }
.cc-type-name { font-weight:700; font-size:0.875rem; color:#111827; margin:0 0 0.25rem; }
.cc-type-coins { display:flex; align-items:center; gap:0.25rem; font-size:1.125rem; font-weight:700; color:#111827; margin-bottom:0.15rem; }
.cc-type-scr { display:flex; align-items:center; gap:0.25rem; font-size:0.875rem; font-weight:600; color:#374151; }
.cc-durs { display:none; margin-top:1rem; }
.cc-durs.is-on { display:block; }
.cc-dur-row { display:flex; gap:0.5rem; }
.cc-dur { flex:1; padding:0.5rem 1rem; border-radius:0.25rem; border:2px solid #e5e7eb; background:#fff; color:#374151; font-size:0.875rem; font-weight:500; cursor:pointer; }
.cc-dur:hover { border-color:#60a5fa; }
.cc-dur.is-active { border-color:#2563eb; background:#eff6ff; color:#2563eb; font-weight:700; }
.cc-empty { text-align:center; padding:3rem 1rem; color:#6b7280; }
.cc-empty.is-hidden { display:none; }
.cc-summary { position:sticky; top:1.5rem; }
.cc-sum-body { padding:1.5rem; display:flex; flex-direction:column; gap:1rem; }
.cc-sum-muted { font-size:0.875rem; color:#4b5563; margin:0 0 0.5rem; }
.cc-sum-item { padding:0.75rem; background:#f9fafb; border-radius:0.25rem; }
.cc-sum-item strong { display:block; font-size:0.875rem; color:#111827; }
.cc-sum-item span { font-size:0.75rem; color:#4b5563; }
.cc-sum-sep { padding-top:1rem; border-top:1px solid #e5e7eb; }
.cc-sum-big { font-size:1.5rem; font-weight:700; color:#111827; margin:0; }
.cc-pay { display:grid; grid-template-columns:repeat(3,1fr); gap:0.5rem; margin-bottom:1rem; }
.cc-pay-btn { padding:0.75rem 1rem; border-radius:0.25rem; border:2px solid #e5e7eb; background:#fff; color:#374151; font-size:0.875rem; font-weight:700; cursor:pointer; }
.cc-pay-btn:hover { border-color:#60a5fa; }
.cc-pay-btn.is-active { border-color:#2563eb; background:#eff6ff; color:#2563eb; }
.cc-launch { width:100%; padding:0.75rem 1rem; border:0; border-radius:0.25rem; color:#fff; font-weight:500; cursor:pointer; background:linear-gradient(to right,#2563eb,#06b6d4); }
.cc-launch:hover { box-shadow:0 10px 15px -3px rgba(37,99,235,.3); }
.cc-launch:disabled { opacity:0.5; cursor:not-allowed; box-shadow:none; }
.cc-modal { position:fixed; inset:0; z-index:50; display:none; align-items:center; justify-content:center; padding:1rem; background:rgba(0,0,0,.5); }
.cc-modal.is-open { display:flex; }
.cc-modal-box { background:#fff; border-radius:0.5rem; width:100%; max-width:72rem; max-height:90vh; overflow:auto; box-shadow:0 25px 50px -12px rgba(0,0,0,.25); }
.cc-modal-h { position:sticky; top:0; background:#fff; border-bottom:1px solid #e5e7eb; padding:1.5rem; display:flex; justify-content:space-between; align-items:flex-start; gap:1rem; }
.cc-modal-close { width:2.5rem; height:2.5rem; border:0; border-radius:9999px; background:transparent; cursor:pointer; color:#4b5563; }
.cc-modal-close:hover { background:#f3f4f6; }
.cc-modal-grid { display:grid; grid-template-columns:repeat(3,minmax(0,1fr)); gap:1.5rem; padding:1.5rem; }
@media (max-width:768px){ .cc-modal-grid{ grid-template-columns:1fr; } }
.cc-modal-card { position:relative; padding:1.5rem; border:2px solid #e5e7eb; border-radius:0.5rem; }
.cc-modal-card:hover { border-color:#60a5fa; }
.cc-modal-select { width:100%; margin-top:1.25rem; padding:0.625rem; border:0; border-radius:0.5rem; color:#fff; font-weight:500; cursor:pointer; }
</style>

<div class="cc-page">
    @include('partials.employer-navbar')
    <div class="flex">
        @include('partials.employer-sidebar')
        <main class="cc-main">
            <div class="cc-header">
                <div>
                    <h1 class="cc-title">Create Job Campaign</h1>
                    <p class="cc-sub">Boost your job posting's visibility and reach more candidates</p>
                </div>
                <div class="cc-coins">
                    <svg style="width:1.25rem;height:1.25rem;" fill="currentColor" viewBox="0 0 20 20"><circle cx="10" cy="10" r="8"/></svg>
                    <span style="font-weight:700;">{{ number_format($coinBalance) }}</span>
                    <span style="font-size:0.875rem;opacity:0.9;">coins</span>
                </div>
            </div>

            @if(session('success'))
                <div style="margin-bottom:1rem;padding:1rem;border-radius:0.5rem;background:#ecfdf5;color:#065f46;border:1px solid #a7f3d0;">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div style="margin-bottom:1rem;padding:1rem;border-radius:0.5rem;background:#fef2f2;color:#991b1b;border:1px solid #fecaca;">{{ session('error') }}</div>
            @endif

            <div class="cc-grid">
                <div class="cc-panel">
                    <div class="cc-panel-h">
                        <div>
                            <h2>Select Jobs & Campaign Types</h2>
                            <p>Choose one or more job postings and assign campaign types to each</p>
                        </div>
                        <button type="button" id="btn-view-campaign-types" class="cc-btn-blue">
                            <svg style="width:1rem;height:1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            View Campaign Types
                        </button>
                    </div>

                    <div class="cc-jobs">
                        @forelse($jobs as $job)
                            @php $activeCampaign = $job->campaigns->first(); @endphp
                            <div class="cc-job campaign-job-card {{ $preselectedJobId == $job->id ? 'is-selected' : '' }}"
                                 data-job-id="{{ $job->id }}"
                                 data-job-title="{{ e($job->title) }}"
                                 data-job-location="{{ e($job->location ?? ($job->is_remote ? 'Remote' : '')) }}">
                                <div class="cc-job-inner">
                                    <div class="cc-job-row">
                                        <input type="checkbox" class="campaign-job-checkbox" data-job-id="{{ $job->id }}" {{ $preselectedJobId == $job->id ? 'checked' : '' }}>
                                        <div style="flex:1;min-width:0;">
                                            <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:0.5rem;">
                                                <div>
                                                    <h3 class="cc-job-title">{{ $job->title }}</h3>
                                                    <p class="cc-job-loc">{{ $job->location ?: ($job->is_remote ? 'Remote' : '—') }}</p>
                                                </div>
                                                <span class="cc-job-badge campaign-type-badge" data-job-id="{{ $job->id }}"></span>
                                                @if($activeCampaign)
                                                    <span style="padding:0.25rem 0.75rem;background:#2563eb;color:#fff;font-size:0.75rem;font-weight:700;border-radius:0.25rem;">{{ $activeCampaign->campaignType->name ?? 'Active' }}</span>
                                                @endif
                                            </div>

                                            <div class="cc-job-opts campaign-job-options" data-job-id="{{ $job->id }}">
                                                <p class="cc-label">Choose Campaign Type:</p>
                                                <div class="cc-types">
                                                    @foreach($campaignTypes as $type)
                                                        @php
                                                            $iconBg = match($type->slug) {
                                                                'growthhire' => 'linear-gradient(to bottom right,#2563eb,#06b6d4)',
                                                                'smarthire' => 'linear-gradient(to bottom right,#059669,#14b8a6)',
                                                                'powerhire' => 'linear-gradient(to bottom right,#7c3aed,#a855f7)',
                                                                default => '#6b7280'
                                                            };
                                                            $durOpts = $type->slug === 'growthhire' ? [7, 15] : [(int) $type->duration_days];
                                                            $iconSvg = match($type->slug) {
                                                                'growthhire' => '<svg style="width:1.25rem;height:1.25rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>',
                                                                'smarthire' => '<svg style="width:1.25rem;height:1.25rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>',
                                                                'powerhire' => '<svg style="width:1.25rem;height:1.25rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>',
                                                                default => ''
                                                            };
                                                        @endphp
                                                        <button type="button"
                                                            class="cc-type campaign-type-btn"
                                                            data-job-id="{{ $job->id }}"
                                                            data-type-id="{{ $type->id }}"
                                                            data-type-name="{{ e($type->name) }}"
                                                            data-type-slug="{{ $type->slug }}"
                                                            data-coins="{{ $type->coins_price }}"
                                                            data-scr="{{ $type->scr_price }}"
                                                            data-duration="{{ $type->duration_days }}"
                                                            data-duration-opts="{{ implode(',', $durOpts) }}">
                                                            @if($type->is_popular)<span class="cc-type-popular">POPULAR</span>@endif
                                                            <div class="cc-type-icon" style="background:{{ $iconBg }};">{!! $iconSvg !!}</div>
                                                            <p class="cc-type-name">{{ $type->name }}</p>
                                                            <div class="cc-type-coins">
                                                                <svg style="width:0.75rem;height:0.75rem;color:#f59e0b;" fill="currentColor" viewBox="0 0 20 20"><circle cx="10" cy="10" r="8"/></svg>
                                                                {{ $type->coins_price }}
                                                            </div>
                                                            <div class="cc-type-scr">
                                                                <svg style="width:0.75rem;height:0.75rem;color:#059669;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><rect x="2" y="6" width="20" height="12" rx="1.5" stroke-width="2"/><path stroke-width="2" d="M2 10h20"/></svg>
                                                                SCR {{ number_format($type->scr_price) }}
                                                            </div>
                                                        </button>
                                                    @endforeach
                                                </div>
                                                <div class="cc-durs campaign-duration-wrap" data-job-id="{{ $job->id }}">
                                                    <p class="cc-label" style="margin-top:0;">Campaign Duration:</p>
                                                    <div class="cc-dur-row campaign-duration-btns" data-job-id="{{ $job->id }}"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="cc-empty">
                                <p>No job postings yet.</p>
                                <a href="{{ route('employer.jobs.index') }}" style="color:#2563eb;">Create a job first</a>
                            </div>
                        @endforelse

                        @if($jobs->isNotEmpty())
                            <div id="campaign-empty-state" class="cc-empty">
                                <svg style="width:4rem;height:4rem;margin:0 auto 0.75rem;color:#9ca3af;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke-width="1.5"/><circle cx="12" cy="12" r="6" stroke-width="1.5"/><circle cx="12" cy="12" r="2" stroke-width="1.5"/></svg>
                                <p style="font-weight:500;">Select job postings above to create campaigns</p>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="cc-summary">
                    <div class="cc-panel">
                        <div class="cc-panel-h" style="display:block;">
                            <h2>Campaign Summary</h2>
                        </div>
                        <div class="cc-sum-body">
                            <div>
                                <p class="cc-sum-muted">Selected Campaigns</p>
                                <div id="summary-list"></div>
                                <p id="summary-empty-msg" style="font-size:0.875rem;color:#6b7280;margin:0;">No campaigns selected</p>
                            </div>

                            <div id="summary-details" style="display:none;">
                                <div class="cc-sum-sep">
                                    <p class="cc-sum-muted">Total Campaigns</p>
                                    <p id="summary-count" class="cc-sum-big">0</p>
                                </div>
                                <div class="cc-sum-sep">
                                    <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:0.5rem;">
                                        <span style="color:#4b5563;">Total Cost</span>
                                        <div style="text-align:right;">
                                            <div id="summary-cost" style="font-weight:600;color:#111827;">0 coins</div>
                                            <div id="summary-scr" style="font-size:0.875rem;color:#4b5563;"></div>
                                        </div>
                                    </div>
                                    <div id="summary-coin-balance" style="display:none;">
                                        <div style="display:flex;justify-content:space-between;margin-bottom:0.75rem;">
                                            <span style="color:#4b5563;">Your Balance</span>
                                            <span style="font-weight:600;color:#111827;">{{ number_format($coinBalance) }} coins</span>
                                        </div>
                                        <div style="display:flex;justify-content:space-between;font-size:1.125rem;font-weight:700;">
                                            <span style="color:#111827;">Balance After</span>
                                            <span id="summary-after">—</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="cc-sum-sep">
                                    <p class="cc-sum-muted" style="margin-bottom:0.75rem;">Payment Method</p>
                                    <div class="cc-pay">
                                        <button type="button" class="cc-pay-btn payment-tab is-active" data-method="coin">Coin</button>
                                        <button type="button" class="cc-pay-btn payment-tab" data-method="card">Card</button>
                                        <button type="button" class="cc-pay-btn payment-tab" data-method="lpo">LPO</button>
                                    </div>
                                    <button type="button" id="btn-launch-campaigns" class="cc-launch" disabled>Launch 0 Campaigns</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<div id="modal-campaign-types" class="cc-modal" role="dialog" aria-modal="true">
    <div class="cc-modal-box">
        <div class="cc-modal-h">
            <div>
                <h2 style="font-size:1.5rem;font-weight:700;color:#111827;margin:0;">Campaign Types</h2>
                <p style="font-size:0.875rem;color:#4b5563;margin:0.25rem 0 0;">Compare features and choose the best campaign for your needs</p>
            </div>
            <button type="button" id="modal-campaign-types-close" class="cc-modal-close" aria-label="Close">
                <svg style="width:1.5rem;height:1.5rem;margin:auto;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="cc-modal-grid">
            @foreach($campaignTypes as $type)
                @php
                    $iconBg = match($type->slug) {
                        'growthhire' => 'linear-gradient(to bottom right,#2563eb,#06b6d4)',
                        'smarthire' => 'linear-gradient(to bottom right,#059669,#14b8a6)',
                        'powerhire' => 'linear-gradient(to bottom right,#7c3aed,#a855f7)',
                        default => '#6b7280'
                    };
                    $btnBg = match($type->slug) { 'growthhire' => '#2563eb', 'smarthire' => '#059669', 'powerhire' => '#7c3aed', default => '#6b7280' };
                    $durLabel = $type->slug === 'growthhire' ? '7-15 days' : $type->duration_days . ' days';
                @endphp
                <div class="cc-modal-card">
                    @if($type->is_popular)
                        <span style="position:absolute;top:-0.75rem;left:50%;transform:translateX(-50%);padding:0.25rem 1rem;font-size:0.7rem;font-weight:700;color:#fff;border-radius:9999px;background:linear-gradient(to right,#7c3aed,#a855f7);box-shadow:0 4px 6px rgba(0,0,0,.15);">MOST POPULAR</span>
                    @endif
                    <div style="text-align:center;margin-bottom:1.5rem;">
                        <div style="width:5rem;height:5rem;margin:0 auto 1rem;border-radius:0.5rem;display:flex;align-items:center;justify-content:center;background:{{ $iconBg }};color:#fff;">
                            <svg style="width:2.5rem;height:2.5rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                        </div>
                        <h3 style="font-size:1.5rem;font-weight:700;color:#111827;margin:0 0 0.5rem;">{{ $type->name }}</h3>
                        <div style="display:flex;align-items:center;justify-content:center;gap:0.5rem;margin-bottom:0.35rem;">
                            <svg style="width:1.5rem;height:1.5rem;color:#f59e0b;" fill="currentColor" viewBox="0 0 20 20"><circle cx="10" cy="10" r="8"/></svg>
                            <span style="font-size:1.875rem;font-weight:700;color:#111827;">{{ $type->coins_price }}</span>
                        </div>
                        <div style="display:flex;align-items:center;justify-content:center;gap:0.5rem;margin-bottom:0.25rem;">
                            <span style="font-size:1.25rem;font-weight:600;color:#374151;">SCR {{ number_format($type->scr_price) }}</span>
                        </div>
                        <p style="font-size:0.875rem;color:#6b7280;margin:0;">{{ $durLabel }}</p>
                        <p style="font-size:0.75rem;color:#6b7280;margin:0.25rem 0 0;">Est. reach: {{ number_format($type->est_reach_min) }}-{{ number_format($type->est_reach_max) }} views</p>
                    </div>
                    <ul style="list-style:none;padding:0;margin:0;display:flex;flex-direction:column;gap:0.5rem;">
                        @foreach($type->features ?? [] as $f)
                            <li style="display:flex;gap:0.5rem;font-size:0.875rem;color:#374151;">
                                <svg style="width:1rem;height:1rem;color:#10b981;flex-shrink:0;margin-top:0.15rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                {{ $f }}
                            </li>
                        @endforeach
                    </ul>
                    <button type="button" class="cc-modal-select modal-select-type" style="background:{{ $btnBg }};" data-type-id="{{ $type->id }}" data-type-slug="{{ $type->slug }}">Select {{ $type->name }}</button>
                </div>
            @endforeach
        </div>
    </div>
</div>

<script>
(function() {
    var coinBalance = {{ (int) $coinBalance }};
    var preselectedJobId = {{ $preselectedJobId ? (int) $preselectedJobId : 'null' }};
    var state = { selections: {}, paymentMethod: 'coin' };

    function renderDurationButtons(jobId, opts, selectedDays) {
        var wrap = document.querySelector('.campaign-duration-wrap[data-job-id="' + jobId + '"]');
        var row = document.querySelector('.campaign-duration-btns[data-job-id="' + jobId + '"]');
        if (!wrap || !row) return;
        if (!opts || opts.length <= 1) {
            wrap.classList.remove('is-on');
            row.innerHTML = '';
            return;
        }
        wrap.classList.add('is-on');
        row.innerHTML = opts.map(function(d) {
            var active = Number(d) === Number(selectedDays) ? ' is-active' : '';
            return '<button type="button" class="cc-dur duration-btn' + active + '" data-job-id="' + jobId + '" data-days="' + d + '">' + d + ' days</button>';
        }).join('');
        row.querySelectorAll('.duration-btn').forEach(function(btn) {
            btn.addEventListener('click', function() {
                var jid = parseInt(this.dataset.jobId, 10);
                if (!state.selections[jid]) return;
                state.selections[jid].durationDays = parseInt(this.dataset.days, 10);
                row.querySelectorAll('.duration-btn').forEach(function(b) { b.classList.remove('is-active'); });
                this.classList.add('is-active');
                updateSummary();
            });
        });
    }

    function selectType(jobId, typeBtn) {
        var opts = String(typeBtn.dataset.durationOpts || typeBtn.dataset.duration || '7').split(',').map(function(n){ return parseInt(n, 10); }).filter(Boolean);
        var defaultDays = opts[0] || parseInt(typeBtn.dataset.duration, 10) || 7;
        state.selections[jobId] = {
            typeId: parseInt(typeBtn.dataset.typeId, 10),
            typeName: typeBtn.dataset.typeName,
            typeSlug: typeBtn.dataset.typeSlug,
            coins: parseInt(typeBtn.dataset.coins, 10),
            scr: parseInt(typeBtn.dataset.scr, 10),
            durationDays: defaultDays
        };
        document.querySelectorAll('.campaign-type-btn[data-job-id="' + jobId + '"]').forEach(function(b) { b.classList.remove('is-active'); });
        typeBtn.classList.add('is-active');
        var badge = document.querySelector('.campaign-type-badge[data-job-id="' + jobId + '"]');
        if (badge) {
            badge.textContent = typeBtn.dataset.typeName;
            badge.classList.add('is-on');
        }
        renderDurationButtons(jobId, opts, defaultDays);
        updateSummary();
    }

    function toggleJob(jobId, show) {
        var card = document.querySelector('.campaign-job-card[data-job-id="' + jobId + '"]');
        if (!card) return;
        if (show) {
            card.classList.add('is-selected');
            if (!state.selections[jobId] || !state.selections[jobId].typeId) {
                var growth = document.querySelector('.campaign-type-btn[data-job-id="' + jobId + '"][data-type-slug="growthhire"]')
                    || document.querySelector('.campaign-type-btn[data-job-id="' + jobId + '"]');
                if (growth) selectType(jobId, growth);
            }
        } else {
            card.classList.remove('is-selected');
            delete state.selections[jobId];
            document.querySelectorAll('.campaign-type-btn[data-job-id="' + jobId + '"]').forEach(function(b) { b.classList.remove('is-active'); });
            var badge = document.querySelector('.campaign-type-badge[data-job-id="' + jobId + '"]');
            if (badge) { badge.classList.remove('is-on'); badge.textContent = ''; }
            var wrap = document.querySelector('.campaign-duration-wrap[data-job-id="' + jobId + '"]');
            if (wrap) wrap.classList.remove('is-on');
        }
        updateSummary();
    }

    document.querySelectorAll('.campaign-job-checkbox').forEach(function(cb) {
        cb.addEventListener('change', function() {
            toggleJob(parseInt(this.dataset.jobId, 10), this.checked);
        });
    });

    document.querySelectorAll('.campaign-type-btn').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            var jobId = parseInt(this.dataset.jobId, 10);
            var cb = document.querySelector('.campaign-job-checkbox[data-job-id="' + jobId + '"]');
            if (cb && !cb.checked) {
                cb.checked = true;
                document.querySelector('.campaign-job-card[data-job-id="' + jobId + '"]').classList.add('is-selected');
            }
            selectType(jobId, this);
        });
    });

    document.querySelectorAll('.payment-tab').forEach(function(tab) {
        tab.addEventListener('click', function() {
            state.paymentMethod = this.dataset.method;
            document.querySelectorAll('.payment-tab').forEach(function(t) { t.classList.remove('is-active'); });
            this.classList.add('is-active');
            updateSummary();
        });
    });

    function updateSummary() {
        var list = document.getElementById('summary-list');
        var emptyMsg = document.getElementById('summary-empty-msg');
        var details = document.getElementById('summary-details');
        var emptyState = document.getElementById('campaign-empty-state');
        var btn = document.getElementById('btn-launch-campaigns');
        var coinBalBox = document.getElementById('summary-coin-balance');

        var entries = Object.keys(state.selections).map(function(k) {
            return [k, state.selections[k]];
        }).filter(function(pair) {
            return pair[1] && pair[1].typeId && pair[1].durationDays;
        });

        if (entries.length === 0) {
            list.innerHTML = '';
            emptyMsg.style.display = '';
            details.style.display = 'none';
            if (emptyState) emptyState.classList.remove('is-hidden');
            btn.disabled = true;
            btn.textContent = 'Launch 0 Campaigns';
            return;
        }

        emptyMsg.style.display = 'none';
        details.style.display = '';
        if (emptyState) emptyState.classList.add('is-hidden');

        var totalCoins = 0, totalScr = 0;
        list.innerHTML = entries.map(function(pair) {
            var jobId = pair[0], s = pair[1];
            var title = (document.querySelector('.campaign-job-card[data-job-id="' + jobId + '"]') || {}).dataset;
            title = (title && title.jobTitle) || 'Job';
            totalCoins += s.coins || 0;
            totalScr += s.scr || 0;
            return '<div class="cc-sum-item"><strong>' + title + '</strong><span>' + s.typeName + ' • ' + s.durationDays + ' days</span></div>';
        }).join('');

        document.getElementById('summary-count').textContent = entries.length;
        document.getElementById('summary-cost').textContent = totalCoins + ' coins';
        document.getElementById('summary-scr').textContent = 'SCR ' + totalScr;

        var canAfford = coinBalance >= totalCoins;
        var afterEl = document.getElementById('summary-after');
        if (state.paymentMethod === 'coin') {
            coinBalBox.style.display = '';
            var after = coinBalance - totalCoins;
            afterEl.textContent = after + ' coins';
            afterEl.style.color = after < 0 ? '#dc2626' : '#059669';
            btn.disabled = !canAfford;
            btn.textContent = canAfford
                ? ('Launch ' + entries.length + ' Campaign' + (entries.length !== 1 ? 's' : ''))
                : 'Insufficient Balance';
        } else {
            coinBalBox.style.display = 'none';
            btn.disabled = false;
            btn.textContent = state.paymentMethod === 'card' ? 'Proceed to Payment' : 'Submit LPO Request';
        }
    }

    function openModal() {
        document.getElementById('modal-campaign-types').classList.add('is-open');
    }
    function closeModal() {
        document.getElementById('modal-campaign-types').classList.remove('is-open');
    }
    document.getElementById('btn-view-campaign-types').addEventListener('click', openModal);
    document.getElementById('modal-campaign-types-close').addEventListener('click', closeModal);
    document.getElementById('modal-campaign-types').addEventListener('click', function(e) {
        if (e.target === this) closeModal();
    });
    document.querySelectorAll('.modal-select-type').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var slug = this.dataset.typeSlug;
            var checked = document.querySelector('.campaign-job-checkbox:checked');
            if (checked) {
                var jobId = parseInt(checked.dataset.jobId, 10);
                var typeBtn = document.querySelector('.campaign-type-btn[data-job-id="' + jobId + '"][data-type-slug="' + slug + '"]');
                if (typeBtn) selectType(jobId, typeBtn);
            }
            closeModal();
        });
    });

    document.getElementById('btn-launch-campaigns').addEventListener('click', function() {
        var entries = Object.keys(state.selections).map(function(k) {
            return [k, state.selections[k]];
        }).filter(function(pair) {
            return pair[1] && pair[1].typeId && pair[1].durationDays;
        });
        if (!entries.length) return;
        if (state.paymentMethod === 'coin') {
            var cost = entries.reduce(function(sum, p) { return sum + (p[1].coins || 0); }, 0);
            if (cost > coinBalance) return;
        }

        var btnEl = this;
        var orig = btnEl.textContent;
        btnEl.disabled = true;
        btnEl.textContent = 'Launching…';

        var formData = new FormData();
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);
        formData.append('payment_method', state.paymentMethod);
        entries.forEach(function(pair, i) {
            formData.append('campaigns[' + i + '][job_id]', pair[0]);
            formData.append('campaigns[' + i + '][campaign_type_id]', pair[1].typeId);
            formData.append('campaigns[' + i + '][duration_days]', pair[1].durationDays);
        });

        fetch('{{ route("employer.campaigns.store") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: formData
        })
        .then(function(r) { return r.json().then(function(d) { return { ok: r.ok, data: d }; }).catch(function() { return { ok: r.ok, data: {} }; }); })
        .then(function(res) {
            if (res.ok) {
                if (window.showSuccessToast) window.showSuccessToast(res.data.message || 'Campaigns launched!');
                setTimeout(function() { window.location.href = '{{ route("employer.campaigns.index") }}'; }, 500);
            } else {
                if (window.showErrorToast) window.showErrorToast((res.data && (res.data.message || res.data.error)) || 'Failed to launch');
                else alert((res.data && res.data.message) || 'Failed to launch');
                btnEl.disabled = false;
                btnEl.textContent = orig;
            }
        })
        .catch(function() {
            btnEl.disabled = false;
            btnEl.textContent = orig;
        });
    });

    if (preselectedJobId) {
        var cb = document.querySelector('.campaign-job-checkbox[data-job-id="' + preselectedJobId + '"]');
        if (cb) {
            cb.checked = true;
            toggleJob(preselectedJobId, true);
        }
    }
    updateSummary();
})();
</script>
@endsection
