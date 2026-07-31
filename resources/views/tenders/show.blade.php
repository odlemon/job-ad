@extends('layouts.app')

@section('content')
@php
    $categoryName = $tender->category ? $tender->category->name : ($tender->sector ?? 'General');
    $budgetRange = ($tender->budget_min && $tender->budget_max)
        ? '$' . number_format((float)$tender->budget_min) . ' - $' . number_format((float)$tender->budget_max)
        : ($tender->amount ? '$' . number_format((float)$tender->amount) : 'N/A');
    $requirements = is_array($tender->requirements) ? $tender->requirements : [];
    $requiredDocs = is_array($tender->required_documents) ? $tender->required_documents : [];
    $eligibility = is_array($tender->eligibility_criteria) ? $tender->eligibility_criteria : [];
    $attachments = is_array($tender->attachments) ? $tender->attachments : [];
    $mediaBaseUrl = app(\App\Services\RemoteUploadService::class)->getMediaBaseUrl();
@endphp

<section class="bg-gray-50 min-h-screen pb-16">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 pt-8">

        <!-- Back link -->
        <a href="{{ route('tenders.index') }}" class="inline-flex items-center gap-1.5 text-sm font-medium text-blue-600 hover:text-blue-800 transition mb-6">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Back to all tenders
        </a>

        <!-- Header card -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 mb-6">
            <div class="flex flex-wrap gap-2 mb-3">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold" style="background-color: #E8F5E9; color: #2E7D32;">{{ $categoryName }}</span>
                @if($tender->tender_type)
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold" style="background-color: #F3E5F5; color: #7B1FA2;">{{ $tender->tender_type }}</span>
                @endif
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold" style="background-color: #E3F2FD; color: #1565C0;">Active</span>
            </div>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-900 mb-2">{{ $tender->title }}</h1>
            <div class="flex items-center gap-2 text-sm text-gray-500">
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                Reference: {{ $tender->reference_number ?? 'N/A' }}
            </div>
        </div>

        <!-- Tabs + Content -->
        <div class="flex flex-col lg:flex-row gap-6">
            <!-- Left: tabs and content -->
            <div class="flex-1 min-w-0">
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                    <div class="border-b border-gray-200 px-6">
                        <nav class="flex gap-6 -mb-px" aria-label="Tabs">
                            <button type="button" class="tender-tab py-3.5 text-sm font-medium border-b-2 transition whitespace-nowrap" data-tab="overview">Overview</button>
                            <button type="button" class="tender-tab py-3.5 text-sm font-medium border-b-2 transition whitespace-nowrap" data-tab="submission">Submission Details</button>
                            <button type="button" class="tender-tab py-3.5 text-sm font-medium border-b-2 transition whitespace-nowrap" data-tab="attachments">Attachments</button>
                            <button type="button" class="tender-tab py-3.5 text-sm font-medium border-b-2 transition whitespace-nowrap" data-tab="dates">Important Dates</button>
                        </nav>
                    </div>

                    <!-- Overview -->
                    <div id="tender-panel-overview" class="tender-panel p-6">
                        <h2 class="text-lg font-bold text-gray-900 mb-3">Tender Summary</h2>
                        <p class="text-sm text-gray-700 leading-relaxed mb-6">{{ $tender->summary ?? $tender->description }}</p>

                        @if($tender->scope_of_work)
                        <h2 class="text-lg font-bold text-gray-900 mb-3">Scope of Work</h2>
                        <p class="text-sm text-gray-700 leading-relaxed mb-6">{{ $tender->scope_of_work }}</p>
                        @endif

                        @if(count($requirements) > 0)
                        <h2 class="text-lg font-bold text-gray-900 mb-3">Requirements</h2>
                        <ul class="space-y-2">
                            @foreach($requirements as $req)
                            <li class="flex items-center gap-2.5 text-sm text-gray-700">
                                <svg class="w-5 h-5 flex-shrink-0" viewBox="0 0 20 20" fill="none">
                                    <circle cx="10" cy="10" r="9" stroke="#22C55E" stroke-width="1.5"/>
                                    <path d="M6 10.5l2.5 2.5L14 7.5" stroke="#22C55E" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                                {{ $req }}
                            </li>
                            @endforeach
                        </ul>
                        @endif
                    </div>

                    <!-- Submission Details -->
                    <div id="tender-panel-submission" class="tender-panel p-6 hidden">
                        @if($tender->submission_method)
                        <h2 class="text-lg font-bold text-gray-900 mb-3">Submission Method</h2>
                        <p class="text-sm text-blue-600 mb-6">{{ $tender->submission_method }}</p>
                        @endif

                        @if(count($requiredDocs) > 0)
                        <h2 class="text-lg font-bold text-gray-900 mb-3">Required Documents</h2>
                        <ul class="space-y-2.5 mb-6">
                            @foreach($requiredDocs as $doc)
                            <li class="flex items-center gap-2.5 text-sm text-gray-700">
                                <svg class="w-5 h-5 flex-shrink-0 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                {{ $doc }}
                            </li>
                            @endforeach
                        </ul>
                        @endif

                        @if(count($eligibility) > 0)
                        <h2 class="text-lg font-bold text-gray-900 mb-3">Eligibility Criteria</h2>
                        <ul class="space-y-2.5">
                            @foreach($eligibility as $el)
                            <li class="flex items-center gap-2.5 text-sm text-gray-700">
                                <svg class="w-5 h-5 flex-shrink-0" viewBox="0 0 20 20" fill="none">
                                    <circle cx="10" cy="10" r="9" stroke="#22C55E" stroke-width="1.5"/>
                                    <path d="M6 10.5l2.5 2.5L14 7.5" stroke="#22C55E" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                                {{ $el }}
                            </li>
                            @endforeach
                        </ul>
                        @endif
                    </div>

                    <!-- Attachments -->
                    <div id="tender-panel-attachments" class="tender-panel p-6 hidden">
                        <div class="flex items-center justify-between mb-5">
                            <h2 class="text-lg font-bold text-gray-900">Downloadable Documents</h2>
                            @if(count($attachments) > 0)
                            <button type="button" onclick="downloadAllAttachments()" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-white text-sm font-medium transition" style="background: linear-gradient(to right, #007bff, #00e0b7);">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                Download All
                            </button>
                            @endif
                        </div>
                        @if(count($attachments) > 0)
                        <div class="space-y-3">
                            @foreach($attachments as $att)
                            @php
                                $attName = is_array($att) ? ($att['name'] ?? 'Document') : $att;
                                $attType = is_array($att) ? ($att['type'] ?? 'File') : 'File';
                                $attSize = is_array($att) ? ($att['size'] ?? '') : '';
                                $attUrl = is_array($att) ? ($att['url'] ?? null) : null;
                                if ($attUrl && !str_starts_with($attUrl, 'http')) {
                                    $attUrl = rtrim($mediaBaseUrl, '/') . '/' . ltrim($attUrl, '/');
                                }
                            @endphp
                            <div class="flex items-center justify-between p-4 rounded-lg border border-gray-200 bg-white hover:bg-gray-50 transition">
                                <div class="flex items-center gap-3 min-w-0">
                                    @if($attType === 'PDF')
                                    <div class="w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0" style="background-color: #FEE2E2;">
                                        <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    </div>
                                    @else
                                    <div class="w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0" style="background-color: #E8F5E9;">
                                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    </div>
                                    @endif
                                    <div class="min-w-0">
                                        <p class="text-sm font-medium text-gray-900 truncate">{{ $attName }}</p>
                                        <p class="text-xs text-gray-500">{{ $attType }}@if($attSize) &bull; {{ $attSize }}@endif</p>
                                    </div>
                                </div>
                                @if($attUrl)
                                <a href="{{ $attUrl }}" target="_blank" download class="p-2 text-blue-500 hover:text-blue-700 transition flex-shrink-0">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                </a>
                                @else
                                <span class="p-2 text-gray-300 flex-shrink-0 cursor-not-allowed" title="File not yet uploaded">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                </span>
                                @endif
                            </div>
                            @endforeach
                        </div>
                        @else
                        <p class="text-sm text-gray-500">No attachments available for this tender.</p>
                        @endif
                    </div>

                    <!-- Important Dates -->
                    <div id="tender-panel-dates" class="tender-panel p-6 hidden">
                        <h2 class="text-lg font-bold text-gray-900 mb-5">Key Dates & Timeline</h2>
                        <div class="space-y-3">
                            @if($tender->published_date)
                            <div class="flex items-center gap-4 p-4 rounded-lg border border-blue-100" style="background-color: #EFF6FF;">
                                <div class="w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0" style="background-color: #DBEAFE;">
                                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-blue-800">Published Date</p>
                                    <p class="text-sm text-blue-700">{{ $tender->published_date->format('l, F j, Y') }}</p>
                                </div>
                            </div>
                            @endif
                            @if($tender->clarification_deadline)
                            <div class="flex items-center gap-4 p-4 rounded-lg border border-amber-100" style="background-color: #FFFBEB;">
                                <div class="w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0" style="background-color: #FEF3C7;">
                                    <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-amber-800">Clarification Deadline</p>
                                    <p class="text-sm text-amber-700">{{ $tender->clarification_deadline->format('l, F j, Y') }}</p>
                                </div>
                            </div>
                            @endif
                            @if($tender->submission_deadline)
                            <div class="flex items-center gap-4 p-4 rounded-lg border border-red-100" style="background-color: #FEF2F2;">
                                <div class="w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0" style="background-color: #FEE2E2;">
                                    <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-red-700">Submission Deadline</p>
                                    <p class="text-sm text-red-600">{{ $tender->submission_deadline->format('l, F j, Y') }}</p>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right sidebar -->
            <div class="lg:w-80 shrink-0">
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 sticky top-24">
                    <h3 class="text-base font-bold text-gray-900 mb-5">Tender Information</h3>

                    <div class="space-y-5">
                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0" style="background-color: #E8F5E9;">
                                <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">Budget Range</p>
                                <p class="text-sm font-semibold text-gray-900">{{ $budgetRange }}</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0" style="background-color: #E3F2FD;">
                                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">Sector</p>
                                <p class="text-sm font-semibold text-gray-900">{{ $tender->sector ?? 'N/A' }}</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0" style="background-color: #F3E5F5;">
                                <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm0 0h2"/></svg>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">Procuring Entity</p>
                                <p class="text-sm font-semibold text-gray-900">{{ $tender->procuring_entity ?? $tender->entity_name ?? 'N/A' }}</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0" style="background-color: #E0F7FA;">
                                <svg class="w-4 h-4 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">Country/Region</p>
                                <p class="text-sm font-semibold text-gray-900">{{ $tender->country_region ?? 'N/A' }}</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0" style="background-color: #FFEBEE;">
                                <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">Location</p>
                                <p class="text-sm font-semibold text-gray-900">{{ $tender->location ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 space-y-3">
                        <button type="button" onclick="downloadAllAttachments()" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg text-white text-sm font-medium transition" style="background: linear-gradient(to right, #007bff, #00e0b7);">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                            Download All Documents
                        </button>
                        <button type="button" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg border border-gray-300 bg-white text-gray-700 text-sm font-medium hover:bg-gray-50 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Request Clarification
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@php
    $attachmentUrls = [];
    foreach ($attachments as $att) {
        $url = is_array($att) ? ($att['url'] ?? null) : null;
        if ($url && !str_starts_with($url, 'http')) {
            $url = rtrim($mediaBaseUrl, '/') . '/' . ltrim($url, '/');
        }
        if ($url) {
            $attachmentUrls[] = $url;
        }
    }
@endphp

@push('scripts')
<script>
var tenderAttachmentUrls = @json($attachmentUrls);

function downloadAllAttachments() {
    if (!tenderAttachmentUrls.length) {
        alert('No documents available for download yet.');
        return;
    }
    tenderAttachmentUrls.forEach(function (url) {
        var a = document.createElement('a');
        a.href = url;
        a.target = '_blank';
        a.download = '';
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
    });
}

document.addEventListener('DOMContentLoaded', function () {
    var tabs = document.querySelectorAll('.tender-tab');
    var panels = document.querySelectorAll('.tender-panel');

    function activateTab(key) {
        tabs.forEach(function (btn) {
            var isActive = btn.getAttribute('data-tab') === key;
            btn.classList.remove('border-blue-600', 'text-blue-600', 'border-transparent', 'text-gray-500');
            btn.classList.add(isActive ? 'border-blue-600' : 'border-transparent', isActive ? 'text-blue-600' : 'text-gray-500');
        });
        panels.forEach(function (panel) {
            panel.classList.toggle('hidden', panel.id !== 'tender-panel-' + key);
        });
    }

    tabs.forEach(function (btn) {
        btn.addEventListener('click', function () { activateTab(btn.getAttribute('data-tab')); });
    });

    activateTab('overview');
});
</script>
@endpush
@endsection
