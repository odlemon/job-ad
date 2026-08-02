@extends('layouts.job-seeker')

@section('content')
    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col">
        @include('partials.job-seeker-navbar')
        
        <!-- Main Content -->
        <main class="flex-1 p-8">
        <div class="max-w-4xl mx-auto">
            <!-- Back Button -->
            <a href="/job-seeker/applications" wire:navigate class="inline-flex items-center text-gray-600 dark:text-gray-400 hover:text-gray-900 mb-6">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                Back to Applications
            </a>

            @php
                $job = $application->jobAdvertisement;
                $company = $job->company ?? null;
                $statusConfig = [
                    'pending' => ['label' => 'Pending', 'color' => 'blue'],
                    'reviewing' => ['label' => 'In Review', 'color' => 'yellow'],
                    'shortlisted' => ['label' => 'Shortlisted', 'color' => 'purple'],
                    'hired' => ['label' => 'Hired', 'color' => 'green'],
                    'rejected' => ['label' => 'Rejected', 'color' => 'red'],
                ];
                $statusInfo = $statusConfig[$application->status] ?? $statusConfig['pending'];
                $statusColors = [
                    'blue' => 'bg-blue-100 text-blue-800',
                    'yellow' => 'bg-yellow-100 text-yellow-800',
                    'purple' => 'bg-purple-100 text-purple-800',
                    'green' => 'bg-green-100 text-green-800',
                    'red' => 'bg-red-100 text-red-800',
                ];
            @endphp

            <!-- Application Header -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm dark:shadow-none border border-gray-200 dark:border-gray-700 p-6 mb-6">
                <div class="flex items-start justify-between mb-4">
                    <div class="flex items-start space-x-4">
                        <!-- Company Logo -->
                        <div class="w-20 h-20 rounded-lg bg-gray-200 dark:bg-gray-700 flex items-center justify-center flex-shrink-0 overflow-hidden">
                            @if($company && $company->logo)
                                <img src="{{ asset('storage/' . $company->logo) }}" alt="{{ $company->name }}" class="w-full h-full object-cover">
                            @else
                                <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                            @endif
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-1">{{ $job->title }}</h1>
                            <p class="text-lg text-gray-600 dark:text-gray-400 mb-2">{{ $company->name ?? 'Unknown Company' }}</p>
                            <span class="inline-block px-3 py-1 rounded-full text-sm font-medium {{ $statusColors[$statusInfo['color']] }}">
                                {{ $statusInfo['label'] }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Applied Date</p>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $application->created_at->format('M d, Y') }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Last Updated</p>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $application->updated_at->diffForHumans() }}</p>
                    </div>
                    @if($application->reviewed_at)
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Reviewed At</p>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $application->reviewed_at->format('M d, Y') }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Interview Details (from employer request) -->
            @if($application->interview_scheduled_at)
            <div class="bg-indigo-50 border border-indigo-200 rounded-lg p-6 mb-6">
                <h2 class="text-lg font-semibold text-indigo-900 mb-3">Interview Details</h2>
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-3">
                    <div class="space-y-1 text-sm text-indigo-900">
                        <p>
                            <span class="font-medium">Date &amp; Time:</span>
                            {{ $application->interview_scheduled_at->format('M d, Y \\a\\t g:i A') }}
                        </p>
                        @if($application->interview_location)
                        <p>
                            <span class="font-medium">Location:</span> {{ $application->interview_location }}
                        </p>
                        @endif
                        @if($application->interview_notes)
                        <p class="mt-1">
                            <span class="font-medium">Notes:</span> {{ $application->interview_notes }}
                        </p>
                        @endif
                        @if($application->interview_status === 'declined' && $application->interview_response_reason)
                        <p class="mt-1 text-xs text-red-700">
                            <span class="font-medium">Your reason:</span> {{ $application->interview_response_reason }}
                        </p>
                        @endif
                    </div>
                    <div class="flex flex-col items-start md:items-end gap-2">
                        @php
                            $interviewStatus = $application->interview_status ?? 'pending';
                            $badgeClasses = [
                                'pending' => 'bg-yellow-100 text-yellow-800',
                                'accepted' => 'bg-emerald-100 text-emerald-800',
                                'declined' => 'bg-red-100 text-red-800',
                            ];
                            $badgeText = [
                                'pending' => 'Waiting for your response',
                                'accepted' => 'You accepted this interview',
                                'declined' => 'You declined this interview',
                            ];
                        @endphp
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $badgeClasses[$interviewStatus] ?? 'bg-indigo-100 text-indigo-800' }}">
                            {{ $badgeText[$interviewStatus] ?? 'Interview scheduled' }}
                        </span>
                        @if(!$application->interview_status || $application->interview_status === 'pending')
                        <div class="flex items-center gap-2 mt-1">
                            <button onclick="respondToInterview({{ $application->id }}, 'accepted')" class="px-4 py-1.5 text-xs font-semibold bg-gradient-to-r from-blue-500 to-cyan-400 text-white rounded-full hover:from-blue-600 hover:to-cyan-500 shadow-md transition">
                                Accept
                            </button>
                            <button onclick="openDeclineInterviewModal({{ $application->id }})" class="px-4 py-1.5 text-xs font-semibold border border-red-300 text-red-700 rounded-full hover:bg-red-50 dark:hover:bg-red-900/20 transition">
                                Decline
                            </button>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @elseif($application->status === 'offered' && isset($application->additional_info['salary']))
            <div class="bg-green-50 border border-green-200 rounded-lg p-6 mb-6">
                <h2 class="text-lg font-semibold text-green-900 mb-3">Offer Details</h2>
                <div class="space-y-2">
                    <p class="text-sm text-green-800">
                        <span class="font-medium">Salary Offered:</span> {{ $application->additional_info['salary'] }}
                    </p>
                    @if(isset($application->additional_info['start_date']))
                    <p class="text-sm text-green-800">
                        <span class="font-medium">Start Date:</span> {{ \Carbon\Carbon::parse($application->additional_info['start_date'])->format('M d, Y') }}
                    </p>
                    @endif
                    @if(isset($application->additional_info['offer_notes']))
                    <p class="text-sm text-green-800 mt-3">
                        <span class="font-medium">Notes:</span> {{ $application->additional_info['offer_notes'] }}
                    </p>
                    @endif
                </div>
            </div>
            @endif

            <!-- Decline Interview Modal -->
            <div id="decline-interview-modal" class="hidden fixed inset-0 z-40 flex items-center justify-center bg-black bg-opacity-40">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl max-w-md w-full mx-4 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Decline interview</h2>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">You can optionally share a short reason with the employer.</p>
                    <form id="decline-interview-form" onsubmit="submitDeclineInterview(event)">
                        <input type="hidden" id="decline-interview-application-id" value="{{ $application->id }}">
                        <textarea id="decline-interview-reason" rows="3" class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Reason for declining (optional)"></textarea>
                        <div class="mt-4 flex items-center justify-end gap-3">
                            <button type="button" onclick="closeDeclineInterviewModal()" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700">Cancel</button>
                            <button type="submit" id="decline-interview-submit-btn" class="px-4 py-2 text-sm font-medium bg-gradient-to-r from-blue-500 to-cyan-400 text-white rounded-lg hover:from-blue-600 hover:to-cyan-500 shadow-md flex items-center gap-2">
                                Submit
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Application Information -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm dark:shadow-none border border-gray-200 dark:border-gray-700 p-6 mb-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Application Information</h2>
                <div class="space-y-4">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Full Name</p>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $application->first_name }} {{ $application->last_name }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Email</p>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $application->email }}</p>
                    </div>
                    @if($application->phone)
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Phone</p>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $application->phone }}</p>
                    </div>
                    @endif
                    @if($application->cover_letter)
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Cover Letter</p>
                        <p class="text-sm text-gray-900 dark:text-white whitespace-pre-wrap">{{ $application->cover_letter }}</p>
                    </div>
                    @endif
                    @if($application->resume_path)
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Resume</p>
                        <a href="{{ asset('storage/' . $application->resume_path) }}" target="_blank" class="inline-flex items-center text-blue-600 hover:text-blue-800">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            View Resume
                        </a>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Job Details -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm dark:shadow-none border border-gray-200 dark:border-gray-700 p-6 mb-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Job Details</h2>
                <div class="space-y-4">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Job Title</p>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $job->title }}</p>
                    </div>
                    @if($job->description)
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Description</p>
                        <p class="text-sm text-gray-900 dark:text-white whitespace-pre-wrap">{{ $job->description }}</p>
                    </div>
                    @endif
                    @if($job->location)
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Location</p>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $job->location }}</p>
                    </div>
                    @endif
                    @if($job->employment_type)
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Employment Type</p>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ ucfirst(str_replace('_', ' ', $job->employment_type)) }}</p>
                    </div>
                    @endif
                    @if($job->salary_min || $job->salary_max)
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Salary</p>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">
                            @if($job->salary_min && $job->salary_max)
                                {{ $job->currency ?? 'SCR' }} {{ number_format($job->salary_min) }} - {{ number_format($job->salary_max) }}
                            @elseif($job->salary_min)
                                {{ $job->currency ?? 'SCR' }} {{ number_format($job->salary_min) }}+
                            @elseif($job->salary_max)
                                Up to {{ $job->currency ?? 'SCR' }} {{ number_format($job->salary_max) }}
                            @endif
                        </p>
                    </div>
                    @endif
                    <div class="pt-4">
                        <a href="{{ route('jobs.show', $job->id) }}" wire:navigate class="inline-flex items-center text-blue-600 hover:text-blue-800">
                            View Job Posting
                            <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Notes Section -->
            @if($application->notes)
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm dark:shadow-none border border-gray-200 dark:border-gray-700 p-6 mb-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Your Notes</h2>
                <p class="text-sm text-gray-900 dark:text-white whitespace-pre-wrap">{{ $application->notes }}</p>
            </div>
            @endif

            <!-- Actions -->
            <div class="flex items-center justify-between bg-white dark:bg-gray-800 rounded-lg shadow-sm dark:shadow-none border border-gray-200 dark:border-gray-700 p-6">
                <button onclick="deleteApplication({{ $application->id }})" class="px-4 py-2 text-red-600 bg-red-50 rounded-lg hover:bg-red-100 transition font-medium">
                    Delete Application
                </button>
                <div class="flex items-center space-x-3">
                    <button onclick="openNoteModal()" class="px-4 py-2 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 rounded-lg border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 transition font-medium">
                        {{ $application->notes ? 'Edit Note' : 'Add Note' }}
                    </button>
                    <a href="{{ route('jobs.show', $job->id) }}" wire:navigate class="px-4 py-2 bg-gradient-to-r from-blue-500 to-cyan-400 text-white rounded-lg hover:from-blue-600 hover:to-cyan-500 shadow-md transition font-medium">
                        View Job Posting
                    </a>
                </div>
            </div>
        </div>
    </main>
    </div>

<!-- Note Modal -->
<div id="noteModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white dark:bg-gray-800">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">{{ $application->notes ? 'Edit Note' : 'Add Note' }}</h3>
            <textarea id="noteText" rows="6" class="w-full border border-gray-300 dark:border-gray-600 rounded-lg p-3 focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Add a note about this application...">{{ $application->notes }}</textarea>
            <div class="flex justify-end space-x-3 mt-4">
                <button onclick="closeNoteModal()" class="px-4 py-2 text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-800 rounded-lg hover:bg-gray-200 transition">
                    Cancel
                </button>
                <button onclick="saveNote({{ $application->id }})" class="px-4 py-2 bg-gradient-to-r from-blue-500 to-cyan-400 text-white rounded-lg hover:from-blue-600 hover:to-cyan-500 shadow-md transition">
                    Save Note
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function openNoteModal() {
    document.getElementById('noteModal').classList.remove('hidden');
}

function closeNoteModal() {
    document.getElementById('noteModal').classList.add('hidden');
}

async function saveNote(applicationId) {
    const note = document.getElementById('noteText').value;
    
    try {
        const response = await fetch(`/job-seeker/applications/${applicationId}/notes`, {
            method: 'PUT',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ notes: note }),
        });
        
        if (response.ok) {
            window.location.reload();
        } else {
            alert('Failed to save note');
        }
    } catch (error) {
        console.error('Error saving note:', error);
        alert('An error occurred while saving the note');
    }
}

async function deleteApplication(applicationId) {
    const confirmed = await window.showConfirmDialog(
        'This application record will be permanently removed.',
        { title: 'Delete application?', confirmText: 'Delete', cancelText: 'Cancel' }
    );
    if (!confirmed) {
        return;
    }
    
    try {
        const response = await fetch(`/job-seeker/applications/${applicationId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json',
            },
        });
        
        if (response.ok) {
            window.location.href = '/job-seeker/applications';
        } else {
            alert('Failed to delete application');
        }
    } catch (error) {
        console.error('Error deleting application:', error);
        alert('An error occurred while deleting the application');
    }
}
</script>

@endsection

@push('scripts')
<script>
    function respondToInterview(applicationId, response) {
        if (response === 'accepted') {
            sendInterviewResponse(applicationId, 'accepted', '');
        }
    }

    function openDeclineInterviewModal(applicationId) {
        document.getElementById('decline-interview-application-id').value = applicationId;
        document.getElementById('decline-interview-modal').classList.remove('hidden');
    }

    function closeDeclineInterviewModal() {
        document.getElementById('decline-interview-modal').classList.add('hidden');
    }

    function submitDeclineInterview(e) {
        e.preventDefault();
        const applicationId = document.getElementById('decline-interview-application-id').value;
        const reason = document.getElementById('decline-interview-reason').value;
        sendInterviewResponse(applicationId, 'declined', reason);
    }

    function sendInterviewResponse(applicationId, response, reason) {
        const btn = response === 'declined'
            ? document.getElementById('decline-interview-submit-btn')
            : null;
        const original = btn ? btn.innerHTML : '';
        if (btn) {
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-sm inline-block mr-2 align-middle"></span><span>Sending...</span>';
        }

        fetch(`/job-seeker/applications/${applicationId}/interview-response`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                response: response,
                reason: reason || ''
            })
        })
        .then(r => r.json())
        .then(data => {
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = original;
            }
            if (data.application) {
                if (response === 'declined') {
                    closeDeclineInterviewModal();
                }
                if (typeof window.showSuccessToast === 'function') {
                    window.showSuccessToast('Interview response sent.');
                }
                // Reload page to reflect updated status
                window.location.reload();
            } else {
                if (typeof window.showErrorToast === 'function') {
                    window.showErrorToast(data.message || 'Failed to send response');
                }
            }
        })
        .catch(err => {
            console.error(err);
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = original;
            }
            if (typeof window.showErrorToast === 'function') {
                window.showErrorToast('An error occurred. Please try again.');
            }
        });
    }
</script>
@endpush
