@extends('layouts.employer')

@section('content')
<div class="min-h-screen bg-white dark:bg-gray-800">
    @include('partials.employer-navbar')

    <div class="flex">
        @include('partials.employer-sidebar')

        <main class="flex-1 p-6 ml-64 w-0 min-w-0">
            <div class="w-full">
                {{-- Back + Header (tight, no extra space) --}}
                <a href="{{ route('employer.campaigns.index') }}" wire:navigate class="inline-flex items-center mb-3 text-[#2563eb] hover:text-[#1d4ed8] font-medium text-sm">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                    Back
                </a>
                <h1 class="text-2xl font-bold" style="color: #1A202C;">Job Applicants</h1>
                <p class="text-sm mt-1" style="color: #6B7280;">Review and manage applicants for this position</p>

                {{-- Job post details card (light blue tint) --}}
                @php
                    $primaryCampaign = $job->campaigns->first();
                    $companyName = $job->company->name ?? 'Employer';
                @endphp
                <div class="mt-4 p-5 rounded-md border border-gray-200 dark:border-gray-700 bg-[#F0F5FA]">
                    <div class="flex flex-wrap items-start justify-between gap-4">
                        <div>
                            <h2 class="text-lg font-bold" style="color: #1A202C;">{{ $job->title }}</h2>
                            <div class="flex flex-wrap items-center gap-2 mt-1">
                                <span class="flex items-center gap-1 text-sm" style="color: #6B7280;">
                                    <svg class="w-4 h-4" style="color: #6B7280;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    {{ $job->location ?: ($job->is_remote ? 'Remote' : 'Multiple locations') }}
                                </span>
                                @if($primaryCampaign)
                                    <span class="inline-flex items-center px-2.5 py-0.5 text-xs font-medium text-white rounded-md" style="background-color: #0EA5E9;">Featured</span>
                                @endif
                            </div>
                            <p class="text-sm mt-2" style="color: #6B7280;">Posted on: {{ $job->created_at->format('d M Y') }}, by {{ $companyName }}</p>
                            @if($primaryCampaign && $primaryCampaign->ends_at)
                                <p class="text-sm" style="color: #6B7280;">Expiring on: {{ $primaryCampaign->ends_at->format('d M Y') }}</p>
                            @endif
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="bg-white dark:bg-gray-800 rounded-md border border-gray-200 dark:border-gray-700 px-4 py-2.5 shadow-sm dark:shadow-none">
                                <div class="flex items-center gap-1.5" style="color: #374151;">
                                    <svg class="w-4 h-4" style="color: #6B7280;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    <span class="font-semibold">{{ $viewsCount }}</span> <span class="text-sm" style="color: #6B7280;">Views</span>
                                </div>
                            </div>
                            <div class="bg-white dark:bg-gray-800 rounded-md border border-gray-200 dark:border-gray-700 px-4 py-2.5 shadow-sm dark:shadow-none">
                                <div class="flex items-center gap-1.5" style="color: #374151;">
                                    <svg class="w-4 h-4" style="color: #6B7280;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                                    <span class="font-semibold">{{ $applications->count() }}</span> <span class="text-sm" style="color: #6B7280;">Applications</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Summary cards: larger, explicit colors --}}
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-4">
                    <div class="bg-white dark:bg-gray-800 rounded-md border border-gray-200 dark:border-gray-700 p-6 shadow-sm dark:shadow-none">
                        <div class="text-sm mb-1" style="color: #6B7280;">Total Applicants</div>
                        <div class="text-3xl font-bold" style="color: #1A202C;">{{ $stats['total'] }}</div>
                    </div>
                    <div class="bg-white dark:bg-gray-800 rounded-md border border-gray-200 dark:border-gray-700 p-6 shadow-sm dark:shadow-none">
                        <div class="text-sm mb-1" style="color: #6B7280;">Shortlisted</div>
                        <div class="text-3xl font-bold" style="color: #1A202C;">{{ $stats['shortlisted'] }}</div>
                    </div>
                    <div class="bg-white dark:bg-gray-800 rounded-md border border-gray-200 dark:border-gray-700 p-6 shadow-sm dark:shadow-none">
                        <div class="text-sm mb-1" style="color: #6B7280;">Selected</div>
                        <div class="text-3xl font-bold" style="color: #059669;">{{ $stats['selected'] }}</div>
                    </div>
                    <div class="bg-white dark:bg-gray-800 rounded-md border border-gray-200 dark:border-gray-700 p-6 shadow-sm dark:shadow-none">
                        <div class="text-sm mb-1" style="color: #6B7280;">Rejected</div>
                        <div class="text-3xl font-bold" style="color: #DC2626;">{{ $stats['rejected'] }}</div>
                    </div>
                </div>

                {{-- Applicants list --}}
                <div class="mt-5">
                    <h3 class="text-lg font-bold mb-4" style="color: #1A202C;">Applicants</h3>
                    <div class="space-y-4">
                        @forelse($applications as $application)
                            @php
                                $initials = strtoupper(substr($application->first_name ?? '', 0, 1) . substr($application->last_name ?? '', 0, 1));
                                $statusColors = [
                                    'pending' => 'bg-[#3B82F6] text-white border-0',
                                    'reviewing' => 'bg-[#9333EA] text-white border-0',
                                    'shortlisted' => 'bg-[#F97316] text-white border-0',
                                    'interview_requested' => 'bg-[#9333EA] text-white border-0',
                                    'rejected' => 'bg-[#DC2626] text-white border-0',
                                    'hired' => 'bg-[#059669] text-white border-0',
                                ];
                                $statusLabels = [
                                    'pending' => 'NEW',
                                    'reviewing' => 'INTERVIEW',
                                    'shortlisted' => 'SHORTLISTED',
                                    'interview_requested' => 'INTERVIEW REQ',
                                    'rejected' => 'REJECTED',
                                    'hired' => 'SELECTED',
                                ];
                                $statusColor = $statusColors[$application->status] ?? 'bg-gray-500 text-white border-0';
                                $statusLabel = $statusLabels[$application->status] ?? strtoupper($application->status ?? '');
                                $experience = 'N/A';
                                if ($application->jobSeeker && $application->jobSeeker->relationLoaded('experiences') && $application->jobSeeker->experiences->isNotEmpty()) {
                                    $totalYears = 0;
                                    foreach ($application->jobSeeker->experiences as $exp) {
                                        if ($exp && isset($exp->start_date) && $exp->start_date) {
                                            try {
                                                $start = new DateTime($exp->start_date);
                                                $end = (isset($exp->end_date) && $exp->end_date) ? new DateTime($exp->end_date) : new DateTime();
                                                $totalYears += $start->diff($end)->y;
                                            } catch (\Exception $e) {}
                                        }
                                    }
                                    $experience = $totalYears > 0 ? $totalYears . ' years' : 'N/A';
                                }
                                $education = 'N/A';
                                if ($application->jobSeeker && $application->jobSeeker->relationLoaded('educations') && $application->jobSeeker->educations->isNotEmpty()) {
                                    $ed = $application->jobSeeker->educations->first();
                                    $education = ($ed->degree ?? '') . ($ed->field_of_study ? ' ' . $ed->field_of_study : '') . ($ed->institution ? ', ' . $ed->institution : '');
                                    if (trim($education) === '') $education = 'N/A';
                                }
                                $profilePhoto = $application->jobSeeker?->profile_photo ?? null;
                                $photoUrl = null;
                                if ($profilePhoto) {
                                    $photoUrl = str_starts_with($profilePhoto, 'http') ? $profilePhoto : asset('storage/' . $profilePhoto);
                                }
                                $avatarGradient = ['from-blue-500 to-blue-600', 'from-purple-500 to-purple-600', 'from-rose-500 to-rose-600', 'from-amber-500 to-amber-600'][abs(crc32($application->first_name . $application->last_name)) % 4];
                                $showUrl = route('employer.applications.show', $application->id) . '?from=job_applicants&job_id=' . $job->id;
                            @endphp
                            <div class="bg-white dark:bg-gray-800 rounded-md border border-gray-200 dark:border-gray-700 shadow-sm dark:shadow-none p-5 flex flex-col md:flex-row md:items-center gap-4">
                                <div class="flex flex-1 flex-wrap items-start gap-4 min-w-0">
                                    <div class="relative flex-shrink-0">
                                        @if($photoUrl)
                                            <img src="{{ $photoUrl }}" alt="" class="w-14 h-14 rounded-full object-cover" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                        @endif
                                        <div class="w-14 h-14 rounded-full bg-gradient-to-br {{ $avatarGradient }} flex items-center justify-center text-white font-bold text-lg {{ $photoUrl ? 'hidden' : '' }}" @if($photoUrl) style="display: none;" @endif>{{ $initials ?: '?' }}</div>
                                        @if(isset($application->match_score))
                                            <span class="absolute -top-1 -right-1 w-6 h-6 rounded-full text-white text-xs font-bold flex items-center justify-center" style="background-color: #3B82F6;">{{ $application->match_score }}</span>
                                        @endif
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <div class="flex flex-wrap items-center gap-2 mb-1">
                                            <h4 class="text-base font-bold" style="color: #1A202C;">{{ $application->first_name }} {{ $application->last_name }}</h4>
                                            <span class="px-2.5 py-0.5 text-xs font-semibold rounded-md {{ $statusColor }}">{{ $statusLabel }}</span>
                                        </div>
                                        <div class="space-y-1 text-sm" style="color: #6B7280;">
                                            <p class="flex items-center gap-1.5"><svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg> {{ $application->email }}</p>
                                            @if($application->phone)<p class="flex items-center gap-1.5"><svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg> {{ $application->phone }}</p>@endif
                                            <p class="flex items-center gap-1.5"><svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg> Applied {{ $application->created_at->format('Y-m-d') }}</p>
                                            <p class="flex items-center gap-1.5"><svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg> Experience: {{ $experience }}</p>
                                            <p class="flex items-center gap-1.5"><svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/></svg> Education: {{ $education }}</p>
                                            @if($application->jobSeeker && ($application->jobSeeker->city || $application->jobSeeker->country))
                                                <p class="flex items-center gap-1.5"><svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg> Location: {{ trim(($application->jobSeeker->city ?? '') . ', ' . ($application->jobSeeker->country ?? ''), ' ,') ?: 'N/A' }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="flex flex-wrap items-center gap-2 flex-shrink-0">
                                    <a href="{{ $showUrl }}" wire:navigate class="inline-flex items-center gap-2 px-4 py-2 text-white rounded-md font-medium text-sm transition shadow-sm dark:shadow-none" style="background-color: #2563eb;">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        View Profile
                                    </a>
                                    <button type="button" onclick="jobApplicantUpdateStatus({{ $application->id }}, 'shortlisted', this)" class="inline-flex items-center gap-1.5 px-3 py-2 border rounded-md hover:bg-gray-50 dark:hover:bg-gray-700 text-sm font-medium border-gray-300 dark:border-gray-600" style="color: #374151;">Shortlist</button>
                                    <button type="button" onclick="jobApplicantUpdateStatus({{ $application->id }}, 'rejected', this)" class="inline-flex items-center gap-1.5 px-3 py-2 border rounded-md hover:bg-gray-50 dark:hover:bg-gray-700 text-sm font-medium border-gray-300 dark:border-gray-600" style="color: #374151;">Reject</button>
                                    <button type="button" onclick="jobApplicantUpdateStatus({{ $application->id }}, 'hired', this)" class="inline-flex items-center gap-1.5 px-3 py-2 border rounded-md hover:bg-gray-50 dark:hover:bg-gray-700 text-sm font-medium border-gray-300 dark:border-gray-600" style="color: #374151;">Select</button>
                                    <a href="{{ route('employer.applications.index', ['job_id' => $job->id]) }}" class="inline-flex items-center gap-1.5 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md hover:bg-gray-50 dark:hover:bg-gray-700 text-sm font-medium" style="color: #374151;">Pool</a>
                                    <a href="{{ route('employer.applications.export', ['ids' => $application->id]) }}" class="p-2 border border-gray-300 dark:border-gray-600 rounded-md hover:bg-gray-50 dark:hover:bg-gray-700" style="color: #6B7280;" title="Download">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                    </a>
                                </div>
                            </div>
                        @empty
                            <div class="bg-white dark:bg-gray-800 rounded-md border border-gray-200 dark:border-gray-700 p-12 text-center">
                                <p style="color: #6B7280;">No applicants yet for this job.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>
<script>
function jobApplicantUpdateStatus(applicationId, status, btn) {
    var orig = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<span class="inline-block w-4 h-4 border-2 border-gray-400 border-t-blue-600 rounded-full animate-spin"></span>';
    fetch('/employer/applications/' + applicationId + '/status', {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({ status: status })
    })
    .then(function(r) { return r.json(); })
    .then(function() { window.location.reload(); })
    .catch(function() { btn.disabled = false; btn.innerHTML = orig; });
}
</script>
@endsection
