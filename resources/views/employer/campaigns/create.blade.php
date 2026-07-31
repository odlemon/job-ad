@extends('layouts.employer')

@section('content')
{{-- Original design: #f8f8f8 page bg, white cards #e0e0e0 border, 8px radius, #007bff button, orange pill coins --}}
<div class="min-h-screen" style="background-color: #f8f8f8;">
    @include('partials.employer-navbar')

    <div class="flex">
        @include('partials.employer-sidebar')

        <main class="flex-1 p-8 ml-64">
            {{-- Page header --}}
            <div class="flex items-start justify-between mb-8">
                <div>
                    <h1 class="font-bold" style="font-size: 26px; font-weight: 700; color: #1A202C;">Create Job Campaign</h1>
                    <p class="mt-1" style="font-size: 15px; color: #6B7280;">Boost your job posting's visibility and reach more candidates</p>
                </div>
                <div class="flex items-center gap-2 text-white px-5 py-2.5 shadow-sm" style="background-color: #ff8c00; border-radius: 9999px;">
                    <svg class="w-5 h-5 opacity-90" fill="currentColor" viewBox="0 0 20 20"><circle cx="10" cy="10" r="8"/></svg>
                    <span class="font-bold" style="font-size: 15px;">{{ number_format($coinBalance) }}</span>
                    <span class="opacity-90" style="font-size: 13px;">coins</span>
                </div>
            </div>

            @if(session('success'))
                <div class="mb-4 p-4 rounded-lg bg-green-50 text-green-800 border border-green-200" style="border-radius: 8px;">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="mb-4 p-4 rounded-lg bg-red-50 text-red-800 border border-red-200" style="border-radius: 8px;">{{ session('error') }}</div>
            @endif

            <div class="flex gap-6 items-start">
                {{-- LEFT: Select Jobs & Campaign Types (75%) --}}
                <div class="min-w-0 flex-shrink-0 bg-white p-6 shadow-sm" style="flex: 0 0 75%; border: 1px solid #e0e0e0; border-radius: 8px;">
                    <div class="flex items-start justify-between gap-4 mb-6">
                        <div>
                            <h2 class="font-bold" style="font-size: 18px; font-weight: 600; color: #1A202C;">Select Jobs & Campaign Types</h2>
                            <p class="mt-1" style="font-size: 14px; color: #6B7280;">Choose one or more job postings and assign campaign types to each</p>
                        </div>
                        <button type="button" id="btn-view-campaign-types" class="flex-shrink-0 px-4 py-2.5 text-white text-sm font-medium flex items-center gap-2 transition hover:opacity-90" style="background-color: #007bff; border-radius: 6px;">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            View Campaign Types
                        </button>
                    </div>

                    {{-- Job list: each job = separate card with gap --}}
                    <div class="space-y-5">
                        @forelse($jobs as $job)
                            @php
                                $activeCampaign = $job->campaigns->first();
                            @endphp
                            <div class="campaign-job-card p-4 transition-all" style="border: 1px solid #e5e7eb; border-radius: 8px; background-color: #ffffff;" data-job-id="{{ $job->id }}" data-job-title="{{ e($job->title) }}" data-job-location="{{ e($job->location ?? ($job->is_remote ? 'Remote' : '')) }}">
                                <label class="flex items-start gap-3 cursor-pointer">
                                    <input type="checkbox" class="campaign-job-checkbox mt-0.5 w-4 h-4 rounded flex-shrink-0" style="accent-color: #3B82F6;" data-job-id="{{ $job->id }}" {{ $preselectedJobId == $job->id ? 'checked' : '' }}>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center justify-between gap-2">
                                            <span class="font-medium" style="font-size: 16px; color: #1A202C;">{{ $job->title }}</span>
                                            <span class="campaign-type-badge hidden flex-shrink-0 text-xs font-medium px-2.5 py-1 rounded" style="background-color: #3B82F6; color: #FFFFFF;" data-job-id="{{ $job->id }}"></span>
                                            @if($activeCampaign)
                                                <span class="flex-shrink-0 text-xs font-medium px-2.5 py-1 rounded" style="background-color: #3B82F6; color: #FFFFFF;">{{ $activeCampaign->campaignType->name ?? 'Active' }}</span>
                                            @endif
                                        </div>
                                        <p class="mt-0.5" style="font-size: 14px; color: #6B7280;">{{ $job->location ?: ($job->is_remote ? 'Remote' : '—') }}</p>
                                    </div>
                                </label>

                                {{-- Expanded: campaign type + duration (hidden by default) --}}
                                <div class="campaign-job-options hidden mt-5 ml-7 pt-4" style="border-top: 1px solid #e5e7eb;" data-job-id="{{ $job->id }}">
                                    <p class="font-medium mb-3" style="font-size: 13px; color: #374151;">Choose Campaign Type:</p>
                                    <div class="grid grid-cols-3 gap-4 mb-5">
                                        @foreach($campaignTypes as $type)
                                            @php
                                                $iconStyle = match($type->slug) {
                                                    'growthhire' => 'background: linear-gradient(135deg, #3B82F6 0%, #60A5FA 100%)',
                                                    'smarthire' => 'background-color: #10B981',
                                                    'powerhire' => 'background-color: #8B5CF6',
                                                    default => 'background-color: #6b7280'
                                                };
                                                $iconSvg = match($type->slug) {
                                                    'growthhire' => '<svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>',
                                                    'smarthire' => '<svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 18l6-6 4 4 8-12"/></svg>',
                                                    'powerhire' => '<svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>',
                                                    default => ''
                                                };
                                            @endphp
                                            <button type="button" class="campaign-type-btn relative p-4 text-left transition border rounded-lg hover:shadow-sm" style="border: 1px solid #e5e7eb; border-radius: 8px; background-color: #F7F9FF;" data-job-id="{{ $job->id }}" data-type-id="{{ $type->id }}" data-type-name="{{ e($type->name) }}" data-type-slug="{{ $type->slug }}" data-coins="{{ $type->coins_price }}" data-scr="{{ $type->scr_price }}" data-duration="{{ $type->duration_days }}">
                                                @if($type->is_popular)<span class="absolute -top-2 left-1/2 -translate-x-1/2 text-[10px] font-bold uppercase px-2 py-0.5 rounded" style="background-color: #9333EA; color: #FFFFFF;">POPULAR</span>@endif
                                                <div class="w-9 h-9 flex items-center justify-center mb-2 rounded-lg text-white flex-shrink-0" style="{{ $iconStyle }};">{!! $iconSvg !!}</div>
                                                <span class="font-semibold block" style="font-size: 15px; color: #1A202C;">{{ $type->name }}</span>
                                                <div class="flex items-center gap-1.5 mt-1">
                                                    <svg class="w-5 h-5 flex-shrink-0" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="9" fill="#F59E0B"/><circle cx="12" cy="12" r="6" fill="none" stroke="#D97706" stroke-width="1.2" opacity="0.8"/></svg>
                                                    <span class="font-bold" style="color: #1A202C; font-size: 14px;">{{ $type->coins_price }}</span>
                                                </div>
                                                <div class="flex items-center gap-1.5 mt-0.5">
                                                    <svg class="w-5 h-5 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="#059669" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="6" width="20" height="12" rx="1.5"/><path d="M2 10h20M2 14h20"/></svg>
                                                    <span class="text-sm font-medium" style="color: #1A202C;">SCR {{ number_format($type->scr_price) }}</span>
                                                </div>
                                            </button>
                                        @endforeach
                                    </div>
                                    <p class="font-medium mb-2" style="font-size: 13px; color: #374151;">Campaign Duration:</p>
                                    <div class="flex gap-2">
                                        @foreach([7, 15] as $d)
                                            <button type="button" class="duration-btn flex-1 py-2.5 rounded text-sm font-medium border transition" style="border-radius: 6px; border: 1px solid #e5e7eb; background: #fff; color: #374151;" data-job-id="{{ $job->id }}" data-days="{{ $d }}">{{ $d }} days</button>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-12 text-gray-500" style="font-size: 15px;">
                                <p>No job postings yet.</p>
                                <a href="{{ route('employer.jobs.index') }}" class="text-blue-600 hover:underline mt-1 inline-block" style="color: #007bff;">Create a job first</a>
                            </div>
                        @endforelse
                    </div>

                    @if($jobs->isNotEmpty())
                        <div id="campaign-empty-state" class="text-center py-10">
                            <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke-width="1.5"/><circle cx="12" cy="12" r="6" stroke-width="1.5"/><circle cx="12" cy="12" r="2" stroke-width="1.5"/></svg>
                            <p class="text-gray-400" style="font-size: 14px;">Select job postings above to create campaigns</p>
                        </div>
                    @endif
                </div>

                {{-- RIGHT: Campaign Summary (25%) --}}
                <div class="flex-shrink-0 min-w-0 bg-white p-6 shadow-sm" style="flex: 0 0 25%; border: 1px solid #e0e0e0; border-radius: 8px;">
                    <h2 class="font-bold pb-4 mb-0" style="font-size: 18px; font-weight: 600; color: #1A202C;">Campaign Summary</h2>
                    <div class="mb-4" style="border-bottom: 1px solid #e5e7eb;"></div>

                    <div id="summary-selected" class="hidden">
                        <p class="font-normal mb-2" style="font-size: 14px; color: #374151;">Selected Campaigns</p>
                        <ul id="summary-list" class="space-y-3 mb-5 list-none pl-0" style="font-size: 14px;"></ul>
                        <div class="pt-4 space-y-3" style="border-top: 1px solid #e5e7eb;">
                            <div>
                                <p class="mb-0.5" style="font-size: 13px; color: #6B7280;">Total Campaigns</p>
                                <p id="summary-count" class="font-bold mt-0" style="font-size: 1.5rem; color: #1A202C;">0</p>
                            </div>
                            <div class="pt-3" style="border-top: 1px solid #e5e7eb;">
                                <p class="mb-1" style="font-size: 13px; color: #6B7280;">Total Cost</p>
                                <p id="summary-cost" class="font-semibold mb-0.5" style="font-size: 14px; color: #1A202C;">0 coins</p>
                                <p id="summary-scr" class="mb-0" style="font-size: 13px; color: #10B981;"></p>
                            </div>
                            <div class="flex justify-between pt-2" style="font-size: 14px;">
                                <span style="color: #6B7280;">Your Balance</span>
                                <span class="font-semibold" style="color: #1A202C;">{{ number_format($coinBalance) }} coins</span>
                            </div>
                            <div class="flex justify-between pt-3" style="border-top: 1px solid #e5e7eb;">
                                <span class="font-semibold" style="font-size: 14px; color: #1A202C;">Balance After</span>
                                <span id="summary-after" class="font-bold" style="color: #10B981;">—</span>
                            </div>
                        </div>
                    </div>
                    <div id="summary-empty">
                        <p class="font-normal mb-1" style="font-size: 14px; color: #374151;">Selected Campaigns</p>
                        <p class="font-normal m-0" style="font-size: 14px; color: #6B7280;">No campaigns selected</p>
                    </div>

                    <div id="summary-actions" class="hidden mt-6">
                        <div>
                            <p class="mb-2" style="font-size: 14px; color: #6B7280;">Payment Method</p>
                            <div class="flex rounded-md overflow-hidden" style="border: 1px solid #e5e7eb;">
                                <button type="button" class="payment-tab flex-1 py-2.5 text-sm font-medium transition border-r" style="background-color: #eff6ff; border-color: #3b82f6; color: #1e40af;" data-method="coin">Coin</button>
                                <button type="button" class="payment-tab flex-1 py-2.5 text-sm font-medium text-gray-700 transition bg-white hover:bg-gray-50 border-r" style="border-color: #e5e7eb;" data-method="card">Card</button>
                                <button type="button" class="payment-tab flex-1 py-2.5 text-sm font-medium text-gray-700 transition bg-white hover:bg-gray-50" style="border-color: #e5e7eb;" data-method="lpo">LPO</button>
                            </div>
                        </div>
                        <button type="button" id="btn-launch-campaigns" disabled class="mt-5 w-full py-3 text-white font-semibold text-sm transition disabled:opacity-50 disabled:cursor-not-allowed rounded-lg bg-gradient-to-r from-blue-600 to-cyan-500 hover:from-blue-700 hover:to-cyan-600">
                            Launch <span id="launch-count">0</span> <span id="launch-label">Campaign(s)</span>
                        </button>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

{{-- Campaign Types Modal (same design language) --}}
<div id="modal-campaign-types" class="fixed inset-0 z-50 hidden items-center justify-center p-4" style="background: rgba(0,0,0,0.35); backdrop-filter: blur(4px);">
    <div class="bg-white w-full max-w-4xl max-h-[90vh] overflow-hidden flex flex-col shadow-xl" style="border-radius: 8px; border: 1px solid #e5e7eb;">
        <div class="p-6 flex-shrink-0 flex items-start justify-between" style="border-bottom: 1px solid #e5e7eb;">
            <div>
                <h3 class="font-bold" style="font-size: 20px; color: #1A202C;">Campaign Types</h3>
                <p class="mt-0.5" style="font-size: 14px; color: #6B7280;">Compare features and choose the best campaign for your needs</p>
            </div>
            <button type="button" id="modal-campaign-types-close" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded transition" style="border-radius: 6px;">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="p-6 overflow-y-auto flex-1">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach($campaignTypes as $type)
                    @php
                        $modalIconStyle = match($type->slug) {
                            'growthhire' => 'background: linear-gradient(135deg, #3B82F6 0%, #60A5FA 100%)',
                            'smarthire' => 'background-color: #10B981',
                            'powerhire' => 'background-color: #8B5CF6',
                            default => 'background-color: #6b7280'
                        };
                        $btnBg = match($type->slug) { 'growthhire' => '#3B82F6', 'smarthire' => '#10B981', 'powerhire' => '#8B5CF6', default => '#6b7280' };
                        $iconSvg = match($type->slug) {
                            'growthhire' => '<svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>',
                            'smarthire' => '<svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 18l6-6 4 4 8-12"/></svg>',
                            'powerhire' => '<svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>',
                            default => ''
                        };
                        $durationLabel = $type->slug === 'growthhire' ? '7-15 days' : $type->duration_days . ' days';
                    @endphp
                    <div class="p-6 flex flex-col relative bg-white rounded-xl shadow-md" style="border-radius: 0.75rem; border: 1px solid #e5e7eb;">
                        @if($type->is_popular)
                            <span class="absolute -top-2.5 left-1/2 -translate-x-1/2 text-xs font-bold px-3 py-1 rounded" style="background-color: #9333EA; color: #FFFFFF;">MOST POPULAR</span>
                        @endif
                        <div class="flex justify-center mb-4 mt-1">
                            <div class="w-14 h-14 flex items-center justify-center text-white rounded-xl flex-shrink-0" style="{{ $modalIconStyle }}; border-radius: 0.75rem;">{!! $iconSvg !!}</div>
                        </div>
                        <h4 class="font-bold text-center" style="font-size: 1.125rem; color: #1A202C;">{{ $type->name }}</h4>
                        <div class="mt-3 flex items-center justify-center gap-4 flex-wrap">
                            <span class="inline-flex items-center gap-1.5 font-bold" style="font-size: 1rem; color: #1A202C;">
                                <svg class="w-5 h-5 flex-shrink-0" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="9" fill="#F59E0B"/><circle cx="12" cy="12" r="6" fill="none" stroke="#D97706" stroke-width="1.2" opacity="0.8"/></svg>
                                {{ $type->coins_price }}
                            </span>
                            <span class="inline-flex items-center gap-1.5" style="font-size: 0.875rem; color: #1A202C;">
                                <svg class="w-5 h-5 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="#059669" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="6" width="20" height="12" rx="1.5"/><path d="M2 10h20M2 14h20"/></svg>
                                SCR {{ number_format($type->scr_price) }}
                            </span>
                        </div>
                        <p class="text-center mt-2" style="font-size: 0.8125rem; color: #6B7280;">{{ $durationLabel }}</p>
                        <p class="text-center" style="font-size: 0.75rem; color: #6B7280;">Est. reach: {{ number_format($type->est_reach_min) }}-{{ number_format($type->est_reach_max) }} views</p>
                        <ul class="mt-4 space-y-2 flex-1">
                            @foreach($type->features ?? [] as $f)
                                <li class="flex items-start gap-2" style="font-size: 0.875rem; color: #374151;">
                                    <svg class="w-4 h-4 mt-0.5 flex-shrink-0" style="color: #10B981;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    {{ $f }}
                                </li>
                            @endforeach
                        </ul>
                        <button type="button" class="modal-select-type mt-5 w-full py-2.5 text-white font-medium text-sm transition rounded-lg hover:opacity-90" style="background-color: {{ $btnBg }};" data-type-id="{{ $type->id }}" data-type-name="{{ e($type->name) }}">Select {{ $type->name }}</button>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<script>
(function() {
    const coinBalance = {{ (int) $coinBalance }};
    const preselectedJobId = {{ $preselectedJobId ? (int) $preselectedJobId : 'null' }};
    const state = { selections: {}, paymentMethod: 'coin' };
    const typeStyles = { growthhire: { border: '#3B82F6', bg: '#ffffff' }, smarthire: { border: '#3B82F6', bg: '#ffffff' }, powerhire: { border: '#3B82F6', bg: '#ffffff' } };

    function toggleJobOptions(jobId, show) {
        const card = document.querySelector(`.campaign-job-card[data-job-id="${jobId}"]`);
        const opts = document.querySelector(`.campaign-job-options[data-job-id="${jobId}"]`);
        const badge = document.querySelector(`.campaign-type-badge[data-job-id="${jobId}"]`);
        if (!card || !opts) return;
        if (show) {
            opts.classList.remove('hidden');
            card.style.border = '2px solid #3B82F6';
            card.style.backgroundColor = '#F7F9FF';
            // Default to GrowthHire + 7 days when no type chosen yet
            setTimeout(function() {
                if (!state.selections[jobId] || !state.selections[jobId].typeId) {
                    const growthBtn = document.querySelector(`.campaign-type-btn[data-job-id="${jobId}"][data-type-slug="growthhire"]`);
                    if (growthBtn) growthBtn.click();
                }
            }, 0);
        } else {
            opts.classList.add('hidden');
            card.style.border = '1px solid #e5e7eb';
            card.style.backgroundColor = '#ffffff';
            if (badge) { badge.classList.add('hidden'); badge.textContent = ''; badge.style.background = ''; badge.style.color = ''; }
            delete state.selections[jobId];
            document.querySelectorAll(`.campaign-type-btn[data-job-id="${jobId}"]`).forEach(b => { b.style.border = '1px solid #e5e7eb'; b.style.backgroundColor = '#F7F9FF'; });
            document.querySelectorAll(`.duration-btn[data-job-id="${jobId}"]`).forEach(b => { b.style.background = '#fff'; b.style.borderColor = '#e5e7eb'; b.style.color = '#374151'; });
        }
        updateSummary();
    }

    document.querySelectorAll('.campaign-job-checkbox').forEach(cb => {
        cb.addEventListener('change', function() { toggleJobOptions(parseInt(this.dataset.jobId, 10), this.checked); });
    });

    document.querySelectorAll('.campaign-type-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const jobId = parseInt(this.dataset.jobId, 10);
            const slug = this.dataset.typeSlug;
            if (!state.selections[jobId]) state.selections[jobId] = {};
            state.selections[jobId].typeId = parseInt(this.dataset.typeId, 10);
            state.selections[jobId].typeName = this.dataset.typeName;
            state.selections[jobId].typeSlug = slug;
            state.selections[jobId].coins = parseInt(this.dataset.coins, 10);
            state.selections[jobId].scr = parseInt(this.dataset.scr, 10);
            state.selections[jobId].duration = parseInt(this.dataset.duration, 10);
            if (!state.selections[jobId].durationDays) {
                state.selections[jobId].durationDays = 7;
                const first = document.querySelector(`.duration-btn[data-job-id="${jobId}"][data-days="7"]`);
                if (first) { first.style.background = '#3B82F6'; first.style.color = '#fff'; first.style.borderColor = '#3B82F6'; }
            }
            const s = typeStyles[slug] || typeStyles.growthhire;
            document.querySelectorAll(`.campaign-type-btn[data-job-id="${jobId}"]`).forEach(b => { b.style.border = '1px solid #e5e7eb'; b.style.backgroundColor = '#F7F9FF'; });
            this.style.border = `2px solid ${s.border}`;
            this.style.backgroundColor = s.bg;
            const badgeEl = document.querySelector(`.campaign-type-badge[data-job-id="${jobId}"]`);
            if (badgeEl) {
                badgeEl.textContent = this.dataset.typeName;
                badgeEl.classList.remove('hidden');
                badgeEl.style.background = '#3B82F6';
                badgeEl.style.color = '#FFFFFF';
                badgeEl.style.borderRadius = '6px';
            }
            updateSummary();
        });
    });

    document.querySelectorAll('.duration-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const jobId = parseInt(this.dataset.jobId, 10);
            if (!state.selections[jobId]) state.selections[jobId] = {};
            state.selections[jobId].durationDays = parseInt(this.dataset.days, 10);
            document.querySelectorAll(`.duration-btn[data-job-id="${jobId}"]`).forEach(b => { b.style.background = '#fff'; b.style.color = '#374151'; b.style.borderColor = '#e5e7eb'; });
            this.style.background = '#3B82F6';
            this.style.color = '#fff';
            this.style.borderColor = '#3B82F6';
            updateSummary();
        });
    });

    document.querySelectorAll('.payment-tab').forEach(tab => {
        tab.addEventListener('click', function() {
            state.paymentMethod = this.dataset.method;
            document.querySelectorAll('.payment-tab').forEach(t => {
                t.style.background = '#fff';
                t.style.color = '#374151';
                t.style.borderColor = '#e5e7eb';
            });
            this.style.background = '#eff6ff';
            this.style.borderColor = '#3b82f6';
            this.style.color = '#1e40af';
            updateSummary();
        });
    });

    function updateSummary() {
        const list = document.getElementById('summary-list');
        const summarySelected = document.getElementById('summary-selected');
        const summaryEmpty = document.getElementById('summary-empty');
        const summaryCount = document.getElementById('summary-count');
        const summaryCost = document.getElementById('summary-cost');
        const summaryAfter = document.getElementById('summary-after');
        const launchCount = document.getElementById('launch-count');
        const btnLaunch = document.getElementById('btn-launch-campaigns');
        const emptyState = document.getElementById('campaign-empty-state');

        const entries = Object.entries(state.selections).filter(([_, v]) => v.typeId && v.durationDays);
        const summaryActions = document.getElementById('summary-actions');
        if (entries.length === 0) {
            summarySelected.classList.add('hidden');
            summaryEmpty.classList.remove('hidden');
            if (summaryActions) summaryActions.classList.add('hidden');
            btnLaunch.disabled = true;
            launchCount.textContent = '0';
            const launchLabel = document.getElementById('launch-label');
            if (launchLabel) launchLabel.textContent = 'Campaign(s)';
            if (emptyState) emptyState.classList.remove('hidden');
            return;
        }
        if (summaryActions) summaryActions.classList.remove('hidden');
        let totalCoins = 0, totalScr = 0;
        const items = entries.map(([jobId, s], idx) => {
            const title = document.querySelector(`.campaign-job-card[data-job-id="${jobId}"]`)?.dataset?.jobTitle || 'Job';
            totalCoins += s.coins || 0;
            totalScr += s.scr || 0;
            const dur = s.durationDays || 7;
            const border = idx < entries.length - 1 ? 'border-bottom: 1px solid #e5e7eb;' : '';
            return `<li class="pb-3" style="${border}"><p class="font-semibold m-0" style="font-size: 15px; color: #1A202C;">${title}</p><p class="mt-0.5 m-0" style="font-size: 13px; color: #6B7280;">${s.typeName} • ${dur} days</p></li>`;
        });
        list.innerHTML = items.join('');
        summarySelected.classList.remove('hidden');
        summaryEmpty.classList.add('hidden');
        summaryCount.textContent = entries.length;
        summaryCost.textContent = totalCoins + ' coins';
        const summaryScrEl = document.getElementById('summary-scr');
        if (summaryScrEl) summaryScrEl.textContent = 'SCR ' + totalScr;
        const after = coinBalance - totalCoins;
        summaryAfter.textContent = after + ' coins';
        summaryAfter.style.color = after < 0 ? '#dc2626' : '#10B981';
        launchCount.textContent = entries.length;
        const launchLabel = document.getElementById('launch-label');
        if (launchLabel) launchLabel.textContent = entries.length === 1 ? 'Campaign' : 'Campaign(s)';
        btnLaunch.disabled = false;
        if (emptyState) emptyState.classList.add('hidden');
    }

    document.getElementById('btn-view-campaign-types').addEventListener('click', function() {
        document.getElementById('modal-campaign-types').classList.remove('hidden');
        document.getElementById('modal-campaign-types').classList.add('flex');
    });
    document.getElementById('modal-campaign-types-close').addEventListener('click', function() {
        document.getElementById('modal-campaign-types').classList.add('hidden');
        document.getElementById('modal-campaign-types').classList.remove('flex');
    });
    document.getElementById('modal-campaign-types').addEventListener('click', function(e) {
        if (e.target === this) { this.classList.add('hidden'); this.classList.remove('flex'); }
    });
    document.querySelectorAll('.modal-select-type').forEach(btn => {
        btn.addEventListener('click', function() {
            document.getElementById('modal-campaign-types').classList.add('hidden');
            document.getElementById('modal-campaign-types').classList.remove('flex');
        });
    });

    document.getElementById('btn-launch-campaigns').addEventListener('click', function() {
        const entries = Object.entries(state.selections).filter(([_, v]) => v.typeId && v.durationDays);
        if (entries.length === 0) return;
        const btnEl = this;
        const orig = btnEl.innerHTML;
        btnEl.disabled = true;
        btnEl.innerHTML = '<span class="inline-block w-5 h-5 border-2 border-white border-t-transparent rounded-full animate-spin"></span>';
        const formData = new FormData();
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);
        formData.append('payment_method', state.paymentMethod);
        entries.forEach(([jobId, s], i) => {
            formData.append(`campaigns[${i}][job_id]`, jobId);
            formData.append(`campaigns[${i}][campaign_type_id]`, s.typeId);
            formData.append(`campaigns[${i}][duration_days]`, s.durationDays || 7);
        });
        fetch('{{ route("employer.campaigns.store") }}', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' },
            body: formData
        }).then(r => r.json().then(d => ({ ok: r.ok, data: d })).catch(() => ({ ok: r.ok, data: {} })))
          .then(({ ok, data }) => {
            if (ok) {
                if (typeof window.showSuccessToast === 'function') window.showSuccessToast(data.message || 'Campaigns launched!');
                setTimeout(() => { window.location.href = '{{ route("employer.campaigns.index") }}'; }, 600);
            } else {
                if (typeof window.showErrorToast === 'function') window.showErrorToast(data.message || data.error || 'Failed');
                btnEl.disabled = false;
                btnEl.innerHTML = orig;
            }
          }).catch(() => { btnEl.disabled = false; btnEl.innerHTML = orig; });
    });

    if (preselectedJobId) {
        const cb = document.querySelector(`.campaign-job-checkbox[data-job-id="${preselectedJobId}"]`);
        if (cb && !cb.checked) { cb.checked = true; toggleJobOptions(preselectedJobId, true); }
    }
})();
</script>
@endsection
