@extends('layouts.job-seeker')

@section('content')
    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col">
        @include('partials.job-seeker-navbar')
        
        <!-- Main Content -->
        <main class="flex-1 p-8 bg-gray-50">
            <div class="max-w-7xl mx-auto">
                <!-- Header -->
                <div class="mb-8">
                    <h1 class="text-2xl font-bold text-gray-900">Job Applications Tracker</h1>
                </div>

                <!-- Stats Cards -->
                <div class="grid grid-cols-5 gap-4 mb-6">
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 text-center">
                        <div class="flex items-center justify-center gap-2 mb-2">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <span class="text-sm font-medium text-gray-700">Applied</span>
                        </div>
                        <div class="text-3xl font-bold text-gray-900">{{ $stats['pending'] ?? $applications->total() }}</div>
                    </div>
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 text-center">
                        <div class="flex items-center justify-center gap-2 mb-2">
                            <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <span class="text-sm font-medium text-gray-700">In Review</span>
                        </div>
                        <div class="text-3xl font-bold text-gray-900">{{ $stats['reviewing'] ?? 0 }}</div>
                    </div>
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 text-center">
                        <div class="flex items-center justify-center gap-2 mb-2">
                            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            <span class="text-sm font-medium text-gray-700">Interview</span>
                        </div>
                        <div class="text-3xl font-bold text-gray-900">{{ $stats['interview'] ?? 0 }}</div>
                    </div>
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 text-center">
                        <div class="flex items-center justify-center gap-2 mb-2">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <span class="text-sm font-medium text-gray-700">Offered</span>
                        </div>
                        <div class="text-3xl font-bold text-gray-900">{{ $stats['offered'] ?? $stats['hired'] ?? 0 }}</div>
                    </div>
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 text-center">
                        <div class="flex items-center justify-center gap-2 mb-2">
                            <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <span class="text-sm font-medium text-gray-700">Rejected</span>
                        </div>
                        <div class="text-3xl font-bold text-gray-900">{{ $stats['rejected'] ?? 0 }}</div>
                    </div>
                </div>

                <!-- Tabs -->
                <div class="mb-6">
                    <div class="flex gap-2">
                        <button id="tab-all" class="status-tab-btn px-6 py-2.5 bg-gradient-to-r from-blue-500 to-cyan-400 text-white rounded-lg font-medium text-sm shadow-md transition" data-filter="all">
                            All Applications ({{ $applications->total() }})
                        </button>
                        <button id="tab-interviews" class="status-tab-btn px-6 py-2.5 bg-white text-gray-700 rounded-lg font-medium text-sm border border-gray-200 hover:bg-gray-50 transition" data-filter="interview">
                            Interviews
                        </button>
                        <button id="tab-offers" class="status-tab-btn px-6 py-2.5 bg-white text-gray-700 rounded-lg font-medium text-sm border border-gray-200 hover:bg-gray-50 transition" data-filter="offered">
                            Offers
                        </button>
                    </div>
                </div>

                <!-- Applications List -->
                <div class="space-y-4">
                            @forelse($applications as $application)
                                @php
                                    $job = $application->jobAdvertisement;
                                    $company = $job->company ?? null;
                            $statusConfig = [
                                'pending' => ['label' => 'Applied', 'color' => 'bg-blue-100 text-blue-700', 'filter' => 'applied'],
                                'reviewing' => ['label' => 'In Review', 'color' => 'bg-yellow-100 text-yellow-800', 'filter' => 'reviewing'],
                                'shortlisted' => ['label' => 'Shortlisted', 'color' => 'bg-purple-100 text-purple-700', 'filter' => 'interview'],
                                'interview_requested' => ['label' => 'Interview', 'color' => 'bg-indigo-100 text-indigo-700', 'filter' => 'interview'],
                                'rejected' => ['label' => 'Rejected', 'color' => 'bg-red-100 text-red-700', 'filter' => 'rejected'],
                                'hired' => ['label' => 'Offered', 'color' => 'bg-green-100 text-green-700', 'filter' => 'offered'],
                            ];
                            $status = $statusConfig[$application->status] ?? $statusConfig['pending'];
                            $companyName = $company->name ?? 'Unknown Company';
                            $logoUrl = null;
                            if ($company && $company->logo) {
                                $logoUrl = $company->logo;
                                if (!str_starts_with($logoUrl, 'http://') && !str_starts_with($logoUrl, 'https://')) {
                                    $logoUrl = asset('storage/' . $logoUrl);
                                }
                            }
                                @endphp
                        
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 hover:shadow-md transition application-card" data-status="{{ $application->status }}" data-filter="{{ $status['filter'] }}" data-search="{{ strtolower($job->title . ' ' . $companyName) }}">
                            <div class="flex items-start gap-4">
                                <!-- Company Logo -->
                                <div class="flex-shrink-0">
                                    @if($logoUrl)
                                        <img src="{{ $logoUrl }}" alt="{{ $companyName }}" class="w-14 h-14 rounded-lg object-cover" onerror="this.onerror=null; this.parentElement.innerHTML='<div class=\'w-14 h-14 rounded-lg bg-gray-100 flex items-center justify-center\'><span class=\'text-lg font-semibold text-gray-600\'>{{ substr($companyName, 0, 1) }}</span></div>';">
                                            @else
                                        <div class="w-14 h-14 rounded-lg bg-gray-100 flex items-center justify-center">
                                            <span class="text-lg font-semibold text-gray-600">{{ substr($companyName, 0, 1) }}</span>
                                                </div>
                                            @endif
                                </div>

                                <!-- Application Details -->
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-start justify-between mb-3">
                                        <div class="flex-1 min-w-0">
                                            <h3 class="text-base font-bold text-gray-900 mb-1">{{ $job->title }}</h3>
                                            <p class="text-sm text-gray-600">{{ $companyName }}</p>
                                        </div>
                                        <span class="ml-4 px-3 py-1 rounded-full text-xs font-medium whitespace-nowrap {{ $status['color'] }}">
                                            <svg class="w-3.5 h-3.5 inline-block mr-1 -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                @if(in_array($application->status, ['shortlisted', 'interview_requested']))
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                @elseif($application->status === 'reviewing')
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                @elseif($application->status === 'hired')
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                @elseif($application->status === 'rejected')
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                @else
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                @endif
                                            </svg>
                                            {{ $status['label'] }}
                                        </span>
                                    </div>

                                    <div class="flex items-center gap-8 text-sm text-gray-600 mb-3">
                                        <div class="flex items-center gap-2">
                                            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                            <span class="text-gray-700">Applied: {{ $application->created_at->format('M j, Y') }}</span>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            <span class="text-gray-700">Updated: {{ $application->updated_at->diffForHumans() }}</span>
                                        </div>
                                    </div>

                                    @if($application->interview_scheduled_at)
                                        <div class="bg-purple-50 border border-purple-200 rounded-md px-3 py-2.5 mb-3">
                                            <div class="flex items-center gap-2">
                                                <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                                <span class="text-sm text-purple-900">Interview Scheduled: {{ \Carbon\Carbon::parse($application->interview_scheduled_at)->format('M j, Y \a\t g:i A') }}</span>
                                            </div>
                                        </div>
                                    @endif

                                    @if($application->notes)
                                        <div class="flex items-start gap-2 text-sm text-gray-700 mb-3 application-notes" data-application-id="{{ $application->id }}">
                                            <svg class="w-4 h-4 mt-0.5 flex-shrink-0 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                            <span class="text-gray-700">{{ $application->notes }}</span>
                                        </div>
                                    @endif

                                    <div class="flex items-center gap-3">
                                        <button type="button" onclick="openApplicationModal({{ $application->id }})" class="px-5 py-2 bg-gradient-to-r from-blue-500 to-cyan-400 text-white rounded-md text-sm font-medium hover:from-blue-600 hover:to-cyan-500 shadow-md transition">View Details</button>
                                        <button type="button" onclick="openAddNoteModal({{ $application->id }})" class="px-5 py-2 bg-white border border-gray-300 text-gray-700 rounded-md text-sm font-medium hover:bg-gray-50 transition">Add Note</button>
                                        <button type="button" onclick="confirmDelete({{ $application->id }})" class="p-2 text-red-600 hover:bg-red-50 rounded-md transition">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                            @empty
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-12 text-center">
                            <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                        <h3 class="text-lg font-medium text-gray-900 mb-2">No applications found</h3>
                            <p class="text-gray-500 mb-6">You haven't applied to any jobs yet.</p>
                            <a href="/jobs" wire:navigate class="inline-flex items-center px-6 py-2.5 bg-gradient-to-r from-blue-500 to-cyan-400 text-white rounded-lg hover:from-blue-600 hover:to-cyan-500 shadow-md transition font-medium">Browse Jobs</a>
                        </div>
                            @endforelse
                </div>

                <!-- Pagination -->
                @if($applications->hasPages())
                    <div class="mt-8">
                        {{ $applications->links() }}
                    </div>
                @endif
            </div>
        </main>
    </div>

<!-- Application Detail Modal (matches employer Applicant Profile design) -->
<div id="applicationModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4" style="background: rgba(0,0,0,0.4); backdrop-filter: blur(2px);">
    <div class="relative bg-white rounded-xl shadow-2xl w-full max-w-6xl max-h-[90vh] flex flex-col overflow-hidden">
        <!-- Modal Header -->
        <div class="px-6 py-4 flex justify-between items-center flex-shrink-0 border-b border-gray-200" style="background-color: #F7F8F9;">
            <h3 class="text-xl font-semibold text-gray-900">Application Details</h3>
            <div class="flex items-center space-x-3">
                <button type="button" onclick="closeApplicationModal()" class="p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition" title="Close">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Modal Body -->
        <div id="applicationModalContent" class="p-6 overflow-y-auto flex-1 bg-white">
            <div class="text-center py-8">
                <div class="spinner mx-auto mb-4"></div>
                <p class="text-gray-500">Loading application details...</p>
            </div>
        </div>

        <!-- Modal Footer -->
        <div id="applicationModalFooter" class="border-t border-gray-200 px-6 py-4 flex items-center justify-between flex-shrink-0" style="display: none; background-color: #F7F8F9;">
            <button type="button" onclick="closeApplicationModal()" class="px-5 py-2.5 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition font-medium text-sm">
                Close
            </button>
            <div class="flex items-center space-x-3">
                <a id="jsAppModalViewJob" href="#" target="_blank" class="inline-flex items-center px-5 py-2.5 bg-gray-700 text-white rounded-lg hover:bg-gray-600 transition font-medium text-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"/></svg>
                    View Job Posting
                </a>
                <button type="button" id="jsAppModalWithdraw" class="px-5 py-2.5 border border-red-300 text-red-700 rounded-lg hover:bg-red-50 transition font-medium text-sm">
                    Withdraw
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Decline Interview Modal -->
<div id="jsDeclineInterviewModal" class="hidden fixed inset-0 z-[60] flex items-center justify-center" style="background: rgba(0,0,0,0.4); backdrop-filter: blur(2px);">
    <div class="bg-white rounded-xl shadow-xl max-w-md w-full mx-4 p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-2">Decline interview</h2>
        <p class="text-sm text-gray-600 mb-4">You can optionally share a short reason with the employer.</p>
        <form id="jsDeclineInterviewForm" onsubmit="submitDeclineInterview(event)">
            <input type="hidden" id="jsDeclineAppId" value="">
            <textarea id="jsDeclineReason" rows="3" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Reason for declining (optional)"></textarea>
            <div class="mt-4 flex items-center justify-end gap-3">
                <button type="button" onclick="closeDeclineModal()" class="px-4 py-2 text-sm font-medium text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50">Cancel</button>
                <button type="submit" id="jsDeclineSubmitBtn" class="px-4 py-2 text-sm font-medium bg-gradient-to-r from-blue-500 to-cyan-400 text-white rounded-lg hover:from-blue-600 hover:to-cyan-500 shadow-md flex items-center gap-2">
                    Submit
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Add Note Modal -->
<div id="addNoteModal" class="hidden fixed inset-0 bg-transparent h-full w-full z-50 flex items-center justify-center p-4">
    <div class="relative bg-white rounded-lg shadow-xl w-full max-w-md">
        <!-- Modal Header -->
        <div class="bg-white border-b border-gray-200 px-6 py-4 flex justify-between items-center rounded-t-lg">
            <h3 class="text-lg font-bold text-gray-900">Add Note</h3>
            <button onclick="closeAddNoteModal()" class="text-gray-400 hover:text-gray-600 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <!-- Modal Content -->
        <div class="p-6">
            <textarea id="noteText" rows="4" placeholder="Add your note here..." 
                class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
            <div class="flex gap-3 mt-4">
                <button id="saveNoteBtn" onclick="saveNote()" class="flex-1 px-6 py-2.5 bg-gradient-to-r from-blue-500 to-cyan-400 text-white rounded-lg hover:from-blue-600 hover:to-cyan-500 shadow-md transition font-medium flex items-center justify-center gap-2">
                    <span id="saveNoteText">Save Note</span>
                    <div id="saveNoteSpinner" class="hidden">
                        <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </div>
                </button>
                <button onclick="closeAddNoteModal()" class="px-6 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition font-medium">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>

<script>
let currentApplicationId = null;

// Tab filtering
document.querySelectorAll('.status-tab-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        setActiveTab(this);
        filterApplications(this.dataset.filter);
    });
});

function setActiveTab(activeBtn) {
    document.querySelectorAll('.status-tab-btn').forEach(btn => {
        btn.classList.remove('bg-gradient-to-r', 'from-blue-500', 'to-cyan-400', 'text-white', 'shadow-md');
        btn.classList.add('bg-white', 'text-gray-700', 'border', 'border-gray-200', 'hover:bg-gray-50');
    });
    activeBtn.classList.remove('bg-white', 'text-gray-700', 'border', 'border-gray-200', 'hover:bg-gray-50');
    activeBtn.classList.add('bg-gradient-to-r', 'from-blue-500', 'to-cyan-400', 'text-white', 'shadow-md');
}

function filterApplications(filter) {
    const cards = document.querySelectorAll('.application-card');
    cards.forEach(card => {
        if (filter === 'all' || card.dataset.filter === filter) {
            card.style.display = '';
        } else {
            card.style.display = 'none';
        }
    });
}

    function openApplicationModal(applicationId) {
        const modal = document.getElementById('applicationModal');
        const content = document.getElementById('applicationModalContent');
        
        modal.classList.remove('hidden');
        
        // Show loading state
        content.innerHTML = `
            <div class="text-center py-8">
                <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                <p class="text-gray-500 mt-4">Loading application details...</p>
            </div>
        `;
        
        // Fetch application details
        fetch(`/job-seeker/applications/${applicationId}`, {
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
        const content = document.getElementById('applicationModalContent');
        
    const statusConfig = {
        'pending': { label: 'Pending', color: 'bg-blue-100 text-blue-700' },
        'reviewing': { label: 'In Review', color: 'bg-yellow-100 text-yellow-800' },
        'shortlisted': { label: 'Shortlisted', color: 'bg-purple-100 text-purple-700' },
        'interview_requested': { label: 'Interview', color: 'bg-indigo-100 text-indigo-700' },
        'rejected': { label: 'Rejected', color: 'bg-red-100 text-red-700' },
        'hired': { label: 'Hired', color: 'bg-green-100 text-green-700' },
    };
    const status = statusConfig[application.status] || statusConfig['pending'];

    const job = application.job_advertisement || {};
    const company = job.company || {};
    let companyLogo = null;
    if (company.logo) {
        companyLogo = company.logo.startsWith('http') ? company.logo : `{{ asset('storage/') }}/${company.logo}`;
    }
    const companyInitial = (company.name || 'C').charAt(0).toUpperCase();
    const appliedDate = new Date(application.created_at).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' });

    // Interview section
    const hasInterview = !!application.interview_scheduled_at;
    const interviewStatus = application.interview_status || 'pending';
    const interviewDate = hasInterview ? new Date(application.interview_scheduled_at).toLocaleString('en-US', { year: 'numeric', month: 'short', day: 'numeric', hour: 'numeric', minute: '2-digit' }) : '';
    const interviewBadgeClasses = {
            'pending': 'bg-yellow-100 text-yellow-800',
        'accepted': 'bg-emerald-100 text-emerald-800',
        'declined': 'bg-red-100 text-red-800',
    };
    const interviewBadgeText = {
        'pending': 'Waiting for your response',
        'accepted': 'You accepted',
        'declined': 'You declined',
    };

        content.innerHTML = `
        <div class="space-y-6 text-gray-800">
            <!-- Summary Card (employer Applicant Profile style) -->
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6">
                <div class="flex items-start gap-5">
                    <div class="flex-shrink-0">
                        ${companyLogo
                            ? `<img src="${companyLogo}" alt="${company.name}" class="w-[72px] h-[72px] rounded-xl object-cover border border-gray-200" onerror="this.onerror=null;this.outerHTML='<div style=\\'width:72px;height:72px\\' class=\\'rounded-xl flex items-center justify-center bg-gray-100 border border-gray-200 text-gray-400 font-bold text-2xl\\'>${companyInitial}</div>';">`
                            : `<div style="width:72px;height:72px" class="rounded-xl flex items-center justify-center bg-gray-100 border border-gray-200 text-gray-400 font-bold text-2xl">${companyInitial}</div>`
                        }
                                </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <h4 class="text-[22px] font-bold text-gray-900 leading-tight">${job.title || 'Job Title'}</h4>
                                <p class="text-sm text-gray-500 mt-1">${company.name || 'Company'}</p>
                            </div>
                            <span class="px-3 py-1 rounded-full text-xs font-semibold ${status.color} whitespace-nowrap">${status.label}</span>
                        </div>
                        <div class="grid grid-cols-2 gap-x-12 gap-y-2.5 mt-4 text-sm text-gray-700">
                            ${job.location ? `<div class="flex items-center gap-2.5"><svg class="w-[18px] h-[18px] text-gray-500 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/></svg>${job.location}</div>` : ''}
                            ${job.employment_type ? `<div class="flex items-center gap-2.5"><svg class="w-[18px] h-[18px] text-gray-500 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 14.15v4.25c0 1.094-.787 2.036-1.872 2.18-2.087.277-4.216.42-6.378.42s-4.291-.143-6.378-.42c-1.085-.144-1.872-1.086-1.872-2.18v-4.25m16.5 0a2.18 2.18 0 00.75-1.661V8.706c0-1.081-.768-2.015-1.837-2.175a48.114 48.114 0 00-3.413-.387m4.5 8.006c-.194.165-.42.295-.673.38A23.978 23.978 0 0112 15.75c-2.648 0-5.195-.429-7.577-1.22a2.016 2.016 0 01-.673-.38m0 0A2.18 2.18 0 013 12.489V8.706c0-1.081.768-2.015 1.837-2.175a48.111 48.111 0 013.413-.387m7.5 0V5.25A2.25 2.25 0 0013.5 3h-3a2.25 2.25 0 00-2.25 2.25v.894m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>${job.employment_type}</div>` : ''}
                            ${(job.salary_min || job.salary_max) ? `<div class="flex items-center gap-2.5"><svg class="w-[18px] h-[18px] text-gray-500 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>SCR ${(job.salary_min || 0).toLocaleString()} – ${(job.salary_max || 0).toLocaleString()}</div>` : ''}
                            ${job.category ? `<div class="flex items-center gap-2.5"><svg class="w-[18px] h-[18px] text-gray-500 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 005.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 009.568 3z"/><path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6z"/></svg>${job.category.name}</div>` : ''}
                        </div>
                    </div>
                </div>
                        </div>
                        
            <!-- Applied On -->
            <div class="flex flex-wrap items-center gap-4 bg-blue-50 rounded-xl px-4 py-3 border border-blue-100">
                <div class="flex items-center gap-2 text-gray-700">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/></svg>
                    <span class="text-sm font-medium">Applied on ${appliedDate}</span>
                            </div>
                        </div>
                        
            ${hasInterview ? `
            <!-- Interview Details -->
            <div class="bg-indigo-50 border border-indigo-200 rounded-xl px-5 py-4">
                <div class="flex items-center justify-between gap-3 mb-3">
                    <div class="flex items-center gap-2 text-indigo-800">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        <span class="text-sm font-semibold">Interview Scheduled</span>
                    </div>
                    <span class="px-3 py-1 rounded-full text-xs font-semibold ${interviewBadgeClasses[interviewStatus] || 'bg-indigo-100 text-indigo-800'}">${interviewBadgeText[interviewStatus] || 'Scheduled'}</span>
                </div>
                <div class="text-sm text-indigo-900 space-y-1">
                    <div><span class="font-medium">Date &amp; Time:</span> ${interviewDate}</div>
                    ${application.interview_location ? `<div><span class="font-medium">Location:</span> ${application.interview_location}</div>` : ''}
                    ${application.interview_notes ? `<div><span class="font-medium">Notes:</span> ${application.interview_notes}</div>` : ''}
                    ${interviewStatus === 'declined' && application.interview_response_reason ? `<div class="mt-1 text-xs text-red-800"><span class="font-medium">Your reason:</span> ${application.interview_response_reason}</div>` : ''}
                        </div>
                ${interviewStatus === 'pending' ? `
                <div class="flex items-center gap-2 mt-4">
                    <button onclick="jsRespondInterview(${application.id}, 'accepted')" class="px-5 py-2 text-sm font-semibold bg-gradient-to-r from-blue-500 to-cyan-400 text-white rounded-lg hover:from-blue-600 hover:to-cyan-500 shadow-md transition">Accept Interview</button>
                    <button onclick="jsOpenDeclineModal(${application.id})" class="px-5 py-2 text-sm font-semibold border border-red-300 text-red-700 rounded-lg hover:bg-red-50 transition">Decline</button>
                </div>
                ` : ''}
            </div>
            ` : ''}

                <!-- Job Description -->
                ${job.description ? `
                    <div class="border-b border-gray-200 pb-6">
                <h5 class="flex items-center gap-2 text-lg font-bold text-gray-900 mb-3">
                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                    Job Description
                </h5>
                <p class="text-sm text-gray-700 leading-relaxed whitespace-pre-wrap">${job.description}</p>
                    </div>
                ` : ''}

                <!-- Cover Letter -->
                ${application.cover_letter ? `
                    <div class="border-b border-gray-200 pb-6">
                <h5 class="flex items-center gap-2 text-lg font-bold text-gray-900 mb-3">
                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/></svg>
                    Your Cover Letter
                </h5>
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 text-sm text-gray-700 leading-relaxed whitespace-pre-wrap">${application.cover_letter}</div>
                    </div>
                ` : ''}

            <!-- Your Note -->
            ${application.notes ? `
            <div class="application-note-detail border-b border-gray-200 pb-6">
                <h5 class="flex items-center gap-2 text-lg font-bold text-gray-900 mb-3">
                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/></svg>
                    Your Note
                </h5>
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 text-sm text-gray-700 leading-relaxed">${application.notes}</div>
                    </div>
                ` : ''}
            </div>
        `;

    // Wire footer
    const footer = document.getElementById('applicationModalFooter');
    if (footer) footer.style.display = 'flex';
    const viewJobLink = document.getElementById('jsAppModalViewJob');
    if (viewJobLink && job.id) viewJobLink.href = '/jobs/' + job.id;
    const withdrawBtn = document.getElementById('jsAppModalWithdraw');
    if (withdrawBtn) {
        withdrawBtn.onclick = function() { withdrawApplication(application.id, application.status); };
    }
    }

    function closeApplicationModal() {
        document.getElementById('applicationModal').classList.add('hidden');
    const footer = document.getElementById('applicationModalFooter');
    if (footer) footer.style.display = 'none';
}

// Interview response functions
function jsRespondInterview(applicationId, response) {
    sendJsInterviewResponse(applicationId, response, '');
}

function jsOpenDeclineModal(applicationId) {
    document.getElementById('jsDeclineAppId').value = applicationId;
    document.getElementById('jsDeclineReason').value = '';
    document.getElementById('jsDeclineInterviewModal').classList.remove('hidden');
}

function closeDeclineModal() {
    document.getElementById('jsDeclineInterviewModal').classList.add('hidden');
}

function submitDeclineInterview(e) {
    e.preventDefault();
    const appId = document.getElementById('jsDeclineAppId').value;
    const reason = document.getElementById('jsDeclineReason').value;
    sendJsInterviewResponse(appId, 'declined', reason);
}

function sendJsInterviewResponse(applicationId, response, reason) {
    const btn = response === 'declined' ? document.getElementById('jsDeclineSubmitBtn') : null;
    const orig = btn ? btn.innerHTML : '';
    if (btn) { btn.disabled = true; btn.innerHTML = '<span class="spinner-sm inline-block mr-2 align-middle"></span><span>Sending...</span>'; }

    fetch(`/job-seeker/applications/${applicationId}/interview-response`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        },
        body: JSON.stringify({ response: response, reason: reason || '' })
    })
    .then(r => r.json())
    .then(data => {
        if (btn) { btn.disabled = false; btn.innerHTML = orig; }
        if (data.application) {
            closeDeclineModal();
            if (typeof window.showSuccessToast === 'function') window.showSuccessToast(response === 'accepted' ? 'Interview accepted!' : 'Interview declined.');
            // Re-fetch and refresh modal
            openApplicationModal(applicationId);
        } else {
            if (typeof window.showErrorToast === 'function') window.showErrorToast(data.message || 'Failed to respond');
        }
    })
    .catch(err => {
        if (btn) { btn.disabled = false; btn.innerHTML = orig; }
        console.error(err);
        if (typeof window.showErrorToast === 'function') window.showErrorToast('An error occurred.');
    });
}

function openAddNoteModal(applicationId) {
    currentApplicationId = applicationId;
    
    // Find existing note in the card
    const cards = document.querySelectorAll('.application-card');
    let existingNote = '';
    
    cards.forEach(card => {
        const viewBtn = card.querySelector(`button[onclick*="openApplicationModal(${applicationId})"]`);
        if (viewBtn) {
            const notesSection = card.querySelector(`.application-notes[data-application-id="${applicationId}"]`);
            if (notesSection) {
                const noteSpan = notesSection.querySelector('span');
                if (noteSpan) {
                    existingNote = noteSpan.textContent.trim();
                }
            }
        }
    });
    
    document.getElementById('addNoteModal').classList.remove('hidden');
    document.getElementById('noteText').value = existingNote;
    document.getElementById('noteText').focus();
}

function closeAddNoteModal() {
    document.getElementById('addNoteModal').classList.add('hidden');
    currentApplicationId = null;
    // Reset button state
    const saveBtn = document.getElementById('saveNoteBtn');
    const saveText = document.getElementById('saveNoteText');
    const saveSpinner = document.getElementById('saveNoteSpinner');
    if (saveBtn) {
        saveBtn.disabled = false;
        saveText.classList.remove('hidden');
        saveSpinner.classList.add('hidden');
    }
}

function saveNote() {
    const noteText = document.getElementById('noteText').value;
    const saveBtn = document.getElementById('saveNoteBtn');
    const saveText = document.getElementById('saveNoteText');
    const saveSpinner = document.getElementById('saveNoteSpinner');
    
    if (!noteText.trim()) {
        if (typeof window.showWarningToast === 'function') {
            window.showWarningToast('Please enter a note');
        } else {
            alert('Please enter a note');
        }
        return;
    }
    
    // Show loading state
    saveBtn.disabled = true;
    saveText.classList.add('hidden');
    saveSpinner.classList.remove('hidden');
    
    fetch(`/job-seeker/applications/${currentApplicationId}/note`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        },
        body: JSON.stringify({ note: noteText })
    })
    .then(response => response.json())
    .then(data => {
        if (data.message) {
            if (typeof window.showSuccessToast === 'function') {
                window.showSuccessToast('Note saved successfully');
            }
            closeAddNoteModal();
            // Update the note in real time without page reload
            updateNoteInCard(currentApplicationId, noteText);
            // Update note in detail modal if it's open
            updateNoteInDetailModal(currentApplicationId, noteText);
        } else {
            if (typeof window.showErrorToast === 'function') {
                window.showErrorToast('Failed to save note');
            } else {
                alert('Failed to save note');
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        if (typeof window.showErrorToast === 'function') {
            window.showErrorToast('An error occurred while saving the note.');
        } else {
            alert('An error occurred while saving the note.');
        }
    })
    .finally(() => {
        // Hide loading state
        saveBtn.disabled = false;
        saveText.classList.remove('hidden');
        saveSpinner.classList.add('hidden');
    });
}

function updateNoteInDetailModal(applicationId, noteText) {
    const modal = document.getElementById('applicationModal');
    if (modal && !modal.classList.contains('hidden')) {
        const modalContent = document.getElementById('applicationModalContent');
        if (modalContent) {
            let notesSection = modalContent.querySelector('.application-note-detail');
            if (!notesSection) {
                const spaceDiv = modalContent.querySelector('.space-y-6');
                if (spaceDiv) {
                    notesSection = document.createElement('div');
                    notesSection.className = 'application-note-detail border-b border-gray-200 pb-6';
                    notesSection.innerHTML = `
                        <h5 class="flex items-center gap-2 text-lg font-bold text-gray-900 mb-3">
                            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/></svg>
                            Your Note
                        </h5>
                        <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 text-sm text-gray-700 leading-relaxed">${noteText}</div>
                    `;
                    spaceDiv.appendChild(notesSection);
                }
            } else {
                const noteDiv = notesSection.querySelector('.bg-gray-50');
                if (noteDiv) noteDiv.textContent = noteText;
            }
        }
    }
}

function updateNoteInCard(applicationId, noteText) {
    // Find the card for this application by looking for the button with the application ID
    const cards = document.querySelectorAll('.application-card');
    let targetCard = null;
    
    cards.forEach(card => {
        const viewBtn = card.querySelector(`button[onclick*="openApplicationModal(${applicationId})"]`);
        const deleteBtn = card.querySelector(`button[onclick*="confirmDelete(${applicationId})"]`);
        if (viewBtn || deleteBtn) {
            targetCard = card;
        }
    });
    
    if (targetCard) {
        updateNoteInCardElement(targetCard, noteText, applicationId);
    }
}

function updateNoteInCardElement(card, noteText, applicationId) {
    // Find or create the notes section
    let notesSection = card.querySelector(`.application-notes[data-application-id="${applicationId}"]`);
    
    if (!notesSection) {
        // Find the interview scheduled section or dates section to insert after
        let insertAfter = card.querySelector('.bg-purple-50');
        if (!insertAfter) {
            insertAfter = card.querySelector('.flex.items-center.gap-8');
        }
        
        if (insertAfter && insertAfter.parentElement) {
            notesSection = document.createElement('div');
            notesSection.className = 'flex items-start gap-2 text-sm text-gray-700 mb-3 application-notes';
            notesSection.setAttribute('data-application-id', applicationId);
            notesSection.innerHTML = `
                <svg class="w-4 h-4 mt-0.5 flex-shrink-0 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <span class="text-gray-700">${noteText}</span>
            `;
            insertAfter.parentElement.insertBefore(notesSection, insertAfter.nextSibling);
        }
    } else {
        // Update existing notes
        const noteTextSpan = notesSection.querySelector('span');
        if (noteTextSpan) {
            noteTextSpan.textContent = noteText;
        }
    }
}

function confirmDelete(applicationId) {
    // Show confirmation toast
    if (typeof window.showWarningToast === 'function') {
        window.showWarningToast('Deleting application...', 2000);
    }
    
    // Small delay to show the toast, then proceed with deletion
    setTimeout(() => {
        // First fetch the application to check its status
        fetch(`/job-seeker/applications/${applicationId}`, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.application) {
                withdrawApplication(applicationId, data.application.status);
            } else {
                if (typeof window.showErrorToast === 'function') {
                    window.showErrorToast('Failed to load application details');
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            if (typeof window.showErrorToast === 'function') {
                window.showErrorToast('An error occurred while checking application status.');
            }
        });
    }, 500);
}

function withdrawApplication(applicationId, currentStatus) {
    // Check if status has been changed by employer
    // Only allow withdrawal if status is 'pending' or 'reviewing'
    // If status is 'shortlisted', 'hired', or 'rejected', employer has already acted
    const withdrawableStatuses = ['pending', 'reviewing', 'interview_requested'];
    
    if (!withdrawableStatuses.includes(currentStatus)) {
        if (typeof window.showErrorToast === 'function') {
            window.showErrorToast('Cannot withdraw application. The employer has already reviewed your application.');
        } else {
            alert('Cannot withdraw application. The employer has already reviewed your application.');
        }
        return;
    }
    
    fetch(`/job-seeker/applications/${applicationId}`, {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.message) {
            if (typeof window.showSuccessToast === 'function') {
                window.showSuccessToast('Application withdrawn successfully');
            }
            closeApplicationModal();
            // Remove the card in real time without page reload
            removeApplicationCard(applicationId);
        } else {
            if (typeof window.showErrorToast === 'function') {
                window.showErrorToast(data.error || 'Failed to withdraw application');
            } else {
                alert(data.error || 'Failed to withdraw application');
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        if (typeof window.showErrorToast === 'function') {
            window.showErrorToast('An error occurred while withdrawing the application.');
        } else {
            alert('An error occurred while withdrawing the application.');
        }
    });
}

function removeApplicationCard(applicationId) {
    // Find the card for this application
    const cards = document.querySelectorAll('.application-card');
    cards.forEach(card => {
        const viewBtn = card.querySelector(`button[onclick*="openApplicationModal(${applicationId})"]`);
        const deleteBtn = card.querySelector(`button[onclick*="confirmDelete(${applicationId})"]`);
        if (viewBtn || deleteBtn) {
            // Animate out
            card.style.transition = 'opacity 0.3s, transform 0.3s';
            card.style.opacity = '0';
            card.style.transform = 'translateX(-20px)';
            setTimeout(() => {
                card.remove();
                // Update stats if needed
                updateStatsAfterDelete();
            }, 300);
        }
    });
}

function updateStatsAfterDelete() {
    // Recalculate and update the stats cards
    const cards = document.querySelectorAll('.application-card:not([style*="display: none"])');
    const stats = {
        applied: 0,
        reviewing: 0,
        interview: 0,
        offered: 0,
        rejected: 0
    };
    
    cards.forEach(card => {
        const filter = card.dataset.filter;
        if (filter && stats.hasOwnProperty(filter)) {
            stats[filter]++;
        }
    });
    
    // Update the stat numbers (if elements exist)
    const statElements = {
        applied: document.querySelector('.application-card')?.closest('.grid')?.querySelector('.text-3xl')?.parentElement,
        // We can add more specific selectors if needed
    };
}
</script>

@endsection
