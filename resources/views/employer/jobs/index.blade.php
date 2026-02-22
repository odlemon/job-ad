@extends('layouts.employer')

@section('content')
<div class="min-h-screen bg-gray-50">
    @include('partials.employer-navbar')

    <div class="flex">
        @include('partials.employer-sidebar')

        <!-- Main Content -->
        <main class="flex-1 p-8 ml-64">
            <div class="max-w-7xl mx-auto">
                <!-- Header -->
                <div class="flex items-start justify-between mb-6">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Job Listings</h1>
                        <p class="text-sm text-gray-500 mt-1">Manage and track all your job postings</p>
                    </div>
                    <button type="button" onclick="openCreateJobModal(event)" class="px-5 py-2.5 bg-gradient-to-r from-blue-600 to-cyan-500 text-white rounded-lg hover:from-blue-700 hover:to-cyan-600 transition font-semibold text-sm flex items-center space-x-2 shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        <span>Post New Job</span>
                    </button>
                </div>

                <!-- Search and Filter Bar -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6">
                    <form method="GET" action="{{ route('employer.jobs.index') }}" class="flex items-center space-x-3">
                        <!-- Search Input -->
                        <div class="flex-1 relative">
                            <svg class="absolute left-3.5 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            <input 
                                type="text" 
                                name="search" 
                                value="{{ $search }}" 
                                placeholder="Search jobs..." 
                                class="w-full pl-11 pr-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-gray-50"
                            >
                        </div>

                        <!-- Status Filter -->
                        <div class="relative">
                            <select 
                                name="status" 
                                onchange="this.form.submit()" 
                                class="appearance-none bg-white border border-gray-200 rounded-lg px-4 py-2.5 pr-9 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent cursor-pointer font-medium text-gray-700"
                            >
                                <option value="all" {{ $currentStatus === 'all' ? 'selected' : '' }}>All Status</option>
                                <option value="active" {{ $currentStatus === 'active' ? 'selected' : '' }}>Active</option>
                                <option value="paused" {{ $currentStatus === 'paused' ? 'selected' : '' }}>Paused</option>
                                <option value="draft" {{ $currentStatus === 'draft' ? 'selected' : '' }}>Draft</option>
                                <option value="closed" {{ $currentStatus === 'closed' ? 'selected' : '' }}>Closed</option>
                            </select>
                            <svg class="absolute right-2.5 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </div>

                        <!-- Filter Button -->
                        <button type="button" class="px-4 py-2.5 border border-gray-200 rounded-lg hover:bg-gray-50 transition flex items-center space-x-2 text-sm font-medium text-gray-700">
                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                            </svg>
                            <span>Filter</span>
                        </button>
                    </form>
                </div>

                <!-- Jobs Table -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden" style="overflow-y: visible;">
                    <div class="overflow-x-auto" style="overflow-y: visible;">
                        <table class="min-w-full">
                            <thead>
                                <tr class="border-b border-gray-200">
                                    <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">JOB TITLE</th>
                                    <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">LOCATION</th>
                                    <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">TYPE</th>
                                    <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">STATUS</th>
                                    <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">APPLICATIONS</th>
                                    <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">VIEWS</th>
                                    <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">ACTIONS</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($jobs as $job)
                                    <tr class="border-b border-gray-100 hover:bg-gray-50/50 transition group" data-job-id="{{ $job->id }}" style="border-left: 3px solid #f59e0b;">
                                        <!-- JOB TITLE -->
                                        <td class="px-6 py-4">
                                            <div class="flex items-center">
                                                <!-- Blue square icon -->
                                                <div class="w-9 h-9 bg-blue-100 rounded-lg flex items-center justify-center mr-3 flex-shrink-0">
                                                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                                    </svg>
                                                </div>
                                                <div>
                                                    <div class="flex items-center space-x-2">
                                                        <span class="text-sm font-semibold text-gray-900">{{ $job->title }}</span>
                                                        @if($job->status === 'published' && $job->created_at->diffInDays(now()) < 7)
                                                            <span class="px-2 py-0.5 text-[10px] font-bold bg-orange-500 text-white rounded-md uppercase tracking-wide">Promoted</span>
                                                        @endif
                                                    </div>
                                                    @if($job->salary_min || $job->salary_max)
                                                        <div class="text-xs text-gray-400 mt-0.5">
                                                            @php
                                                                $min = is_numeric($job->salary_min) ? ($job->salary_min >= 1000 ? '$' . number_format($job->salary_min / 1000, 0) . 'k' : '$' . $job->salary_min) : '$' . $job->salary_min;
                                                                $max = is_numeric($job->salary_max) ? ($job->salary_max >= 1000 ? '$' . number_format($job->salary_max / 1000, 0) . 'k' : '$' . $job->salary_max) : '$' . $job->salary_max;
                                                            @endphp
                                                            {{ $min }} - {{ $max }}
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>

                                        <!-- LOCATION -->
                                        <td class="px-6 py-4">
                                            <span class="text-sm text-gray-600">
                                                @if($job->is_remote)
                                                    Remote
                                                @else
                                                    {{ $job->location ?? 'Not specified' }}
                                                @endif
                                            </span>
                                        </td>

                                        <!-- TYPE -->
                                        <td class="px-6 py-4">
                                            <span class="text-sm text-gray-600">
                                                {{ ucfirst(str_replace('_', '-', $job->employment_type ?? 'Full-time')) }}
                                            </span>
                                        </td>

                                        <!-- STATUS -->
                                        <td class="px-6 py-4">
                                            @php
                                                $statusMap = [
                                                    'published' => ['label' => 'active', 'class' => 'bg-emerald-50 text-emerald-600 border border-emerald-200'],
                                                    'draft' => ['label' => 'draft', 'class' => 'bg-gray-50 text-gray-500 border border-gray-200'],
                                                    'closed' => ['label' => 'closed', 'class' => 'bg-red-50 text-red-600 border border-red-200'],
                                                    'archived' => ['label' => 'archived', 'class' => 'bg-gray-50 text-gray-500 border border-gray-200'],
                                                ];
                                                $statusInfo = $statusMap[$job->status] ?? ['label' => $job->status, 'class' => 'bg-gray-50 text-gray-500 border border-gray-200'];
                                                if ($job->status === 'draft' && $job->published_at) {
                                                    $statusInfo = ['label' => 'paused', 'class' => 'bg-amber-50 text-amber-600 border border-amber-200'];
                                                }
                                            @endphp
                                            <span class="inline-flex items-center px-2.5 py-1 text-xs font-medium rounded-md {{ $statusInfo['class'] }}">
                                                {{ $statusInfo['label'] }}
                                            </span>
                                        </td>

                                        <!-- APPLICATIONS -->
                                        <td class="px-6 py-4">
                                            <div class="flex items-center text-sm text-gray-600">
                                                <svg class="w-4 h-4 mr-1.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                                </svg>
                                                <span class="font-medium">{{ $job->applications_count ?? $job->applications()->count() }}</span>
                                            </div>
                                        </td>

                                        <!-- VIEWS -->
                                        <td class="px-6 py-4">
                                            <div class="flex items-center text-sm text-gray-600">
                                                <svg class="w-4 h-4 mr-1.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                </svg>
                                                <span class="font-medium">{{ number_format($job->views_count ?? 0) }}</span>
                                            </div>
                                        </td>

                                        <!-- ACTIONS -->
                                        <td class="px-6 py-4" style="position: relative; overflow: visible;">
                                            <div class="flex items-center space-x-1">
                                                <!-- Edit -->
                                                <button onclick="openEditJobModal({{ $job->id }})" class="p-2 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition" title="Edit">
                                                    <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                    </svg>
                                                </button>

                                                <!-- Pause/Play -->
                                                @if($job->status === 'published')
                                                    <button onclick="toggleJobStatus({{ $job->id }})" class="p-2 text-gray-400 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition" title="Pause">
                                                        <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                        </svg>
                                                    </button>
                                                @else
                                                    <button onclick="toggleJobStatus({{ $job->id }})" class="p-2 text-gray-400 hover:text-green-600 hover:bg-green-50 rounded-lg transition" title="Activate">
                                                        <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path>
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                        </svg>
                                                    </button>
                                                @endif

                                                <!-- Analytics/Chart -->
                                                <button onclick="openShowJobModal({{ $job->id }})" class="p-2 text-gray-400 hover:text-purple-600 hover:bg-purple-50 rounded-lg transition" title="Analytics">
                                                    <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                                                    </svg>
                                                </button>

                                                <!-- More Options -->
                                                <div class="relative inline-block" style="z-index: 9999;">
                                                    <button onclick="toggleMoreMenu({{ $job->id }})" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition" title="More options">
                                                        <svg class="w-[18px] h-[18px]" fill="currentColor" viewBox="0 0 24 24">
                                                            <circle cx="12" cy="5" r="1.5"></circle>
                                                            <circle cx="12" cy="12" r="1.5"></circle>
                                                            <circle cx="12" cy="19" r="1.5"></circle>
                                                        </svg>
                                                    </button>
                                                    <div id="more-menu-{{ $job->id }}" class="hidden absolute right-0 top-full mt-1 w-48 bg-white rounded-lg shadow-2xl border border-gray-200 overflow-hidden" style="z-index: 99999; min-width: 12rem; position: absolute;">
                                                        <button onclick="openShowJobModal({{ $job->id }})" class="w-full text-left block px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition whitespace-nowrap">View Details</button>
                                                        <button onclick="deleteJob({{ $job->id }})" class="w-full text-left block px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 transition whitespace-nowrap">Delete</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-6 py-16 text-center">
                                            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                                </svg>
                                            </div>
                                            <h3 class="text-lg font-semibold text-gray-900 mb-1">No job postings found</h3>
                                            <p class="text-sm text-gray-500 mb-6">Get started by creating your first job posting.</p>
                                            <button type="button" onclick="openCreateJobModal(event)" class="inline-flex items-center px-5 py-2.5 bg-gradient-to-r from-blue-600 to-cyan-500 text-white rounded-lg hover:from-blue-700 hover:to-cyan-600 transition font-medium text-sm">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                                </svg>
                                                Post New Job
                                            </button>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<script>
async function toggleJobStatus(jobId) {
    try {
        const response = await fetch(`/employer/jobs/${jobId}/toggle-status`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json',
            },
        });
        
        if (response.ok) {
            const data = await response.json();
            // Update the status badge in the table
            updateJobStatusInTable(jobId, data.status);
        } else {
            const data = await response.json();
            alert(data.message || 'Failed to update job status');
        }
    } catch (error) {
        console.error('Error toggling job status:', error);
        alert('An error occurred while updating the job status');
    }
}

// Function to update job status in table
function updateJobStatusInTable(jobId, newStatus) {
    const row = document.querySelector(`tr[data-job-id="${jobId}"]`);
    if (!row) return;
    
    const statusCell = row.querySelector('td:nth-child(4)'); // STATUS column
    const pausePlayBtn = row.querySelector('button[onclick*="toggleJobStatus"]');
    
    if (!statusCell) return;
    
    // Update status badge
    let statusLabel = 'draft';
    let statusClass = 'bg-gray-50 text-gray-500 border border-gray-200';
    
    if (newStatus === 'published') {
        statusLabel = 'active';
        statusClass = 'bg-emerald-50 text-emerald-600 border border-emerald-200';
    } else if (newStatus === 'draft') {
        // Check if it was previously published (paused)
        const currentLabel = statusCell.textContent.trim();
        if (currentLabel === 'active') {
            statusLabel = 'paused';
            statusClass = 'bg-amber-50 text-amber-600 border border-amber-200';
        } else {
            statusLabel = 'draft';
            statusClass = 'bg-gray-50 text-gray-500 border border-gray-200';
        }
    }
    
    statusCell.innerHTML = `<span class="inline-flex items-center px-2.5 py-1 text-xs font-medium rounded-md ${statusClass}">${statusLabel}</span>`;
    
    // Update pause/play button
    if (pausePlayBtn) {
        if (newStatus === 'published') {
            pausePlayBtn.innerHTML = `
                <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            `;
            pausePlayBtn.title = 'Pause';
            pausePlayBtn.className = 'p-2 text-gray-400 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition';
        } else {
            pausePlayBtn.innerHTML = `
                <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            `;
            pausePlayBtn.title = 'Activate';
            pausePlayBtn.className = 'p-2 text-gray-400 hover:text-green-600 hover:bg-green-50 rounded-lg transition';
        }
    }
}

function toggleMoreMenu(jobId) {
    const menu = document.getElementById(`more-menu-${jobId}`);
    if (menu) {
        menu.classList.toggle('hidden');
    }
    
    // Close other menus
    document.querySelectorAll('[id^="more-menu-"]').forEach(m => {
        if (m.id !== `more-menu-${jobId}`) {
            m.classList.add('hidden');
        }
    });
}

// Close menus when clicking outside
document.addEventListener('click', function(e) {
    if (!e.target.closest('[onclick*="toggleMoreMenu"]') && !e.target.closest('[id^="more-menu-"]')) {
        document.querySelectorAll('[id^="more-menu-"]').forEach(menu => {
            menu.classList.add('hidden');
        });
    }
});

async function deleteJob(jobId) {
    if (!confirm('Are you sure you want to delete this job posting?')) {
        return;
    }
    
    try {
        const response = await fetch(`/employer/jobs/${jobId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json',
            },
        });
        
        if (response.ok) {
            // Remove row from table
            const row = document.querySelector(`tr[data-job-id="${jobId}"]`);
            if (row) {
                row.remove();
            } else {
                window.location.reload();
            }
        } else {
            alert('Failed to delete job posting');
        }
    } catch (error) {
        console.error('Error deleting job:', error);
        alert('An error occurred while deleting the job posting');
    }
}

// Function to add new job to table dynamically
function addJobToTable(job) {
    const tbody = document.querySelector('tbody');
    if (!tbody) return;
    
    // Remove empty state row if exists
    const emptyRow = tbody.querySelector('tr td[colspan="7"]');
    if (emptyRow) {
        emptyRow.closest('tr').remove();
    }
    
    // Format salary
    let salaryDisplay = '';
    if (job.salary_min || job.salary_max) {
        const min = job.salary_min ? (job.salary_min >= 1000 ? '$' + Math.round(job.salary_min / 1000) + 'k' : '$' + job.salary_min.toLocaleString()) : '';
        const max = job.salary_max ? (job.salary_max >= 1000 ? '$' + Math.round(job.salary_max / 1000) + 'k' : '$' + job.salary_max.toLocaleString()) : '';
        if (min && max) {
            salaryDisplay = `<div class="text-xs text-gray-400 mt-0.5">${min} - ${max}</div>`;
        } else if (min) {
            salaryDisplay = `<div class="text-xs text-gray-400 mt-0.5">${min}+</div>`;
        } else if (max) {
            salaryDisplay = `<div class="text-xs text-gray-400 mt-0.5">Up to ${max}</div>`;
        }
    }
    
    // Determine status
    let statusLabel = 'draft';
    let statusClass = 'bg-gray-50 text-gray-500 border border-gray-200';
    if (job.status === 'published') {
        statusLabel = 'active';
        statusClass = 'bg-emerald-50 text-emerald-600 border border-emerald-200';
    } else if (job.status === 'draft' && job.published_at) {
        statusLabel = 'paused';
        statusClass = 'bg-amber-50 text-amber-600 border border-amber-200';
    } else if (job.status === 'closed') {
        statusLabel = 'closed';
        statusClass = 'bg-red-50 text-red-600 border border-red-200';
    }
    
    // Check if promoted (published within last 7 days)
    const promotedBadge = (job.status === 'published' && new Date(job.created_at).getTime() > Date.now() - 7 * 24 * 60 * 60 * 1000) 
        ? '<span class="px-2 py-0.5 text-[10px] font-bold bg-orange-500 text-white rounded-md uppercase tracking-wide">Promoted</span>' 
        : '';
    
    // Format employment type
    const employmentType = job.employment_type ? job.employment_type.replace('_', '-').replace(/\b\w/g, l => l.toUpperCase()) : 'Full-time';
    
    // Location
    const location = job.is_remote ? 'Remote' : (job.location || 'Not specified');
    
    // Create new row
    const newRow = document.createElement('tr');
    newRow.setAttribute('data-job-id', job.id);
    newRow.className = 'border-b border-gray-100 hover:bg-gray-50/50 transition group';
    newRow.style.borderLeft = '3px solid #f59e0b';
    newRow.innerHTML = `
        <!-- JOB TITLE -->
        <td class="px-6 py-4">
            <div class="flex items-center">
                <div class="w-9 h-9 bg-blue-100 rounded-lg flex items-center justify-center mr-3 flex-shrink-0">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <div>
                    <div class="flex items-center space-x-2">
                        <span class="text-sm font-semibold text-gray-900">${job.title}</span>
                        ${promotedBadge}
                    </div>
                    ${salaryDisplay}
                </div>
            </div>
        </td>

        <!-- LOCATION -->
        <td class="px-6 py-4">
            <span class="text-sm text-gray-600">${location}</span>
        </td>

        <!-- TYPE -->
        <td class="px-6 py-4">
            <span class="text-sm text-gray-600">${employmentType}</span>
        </td>

        <!-- STATUS -->
        <td class="px-6 py-4">
            <span class="inline-flex items-center px-2.5 py-1 text-xs font-medium rounded-md ${statusClass}">
                ${statusLabel}
            </span>
        </td>

        <!-- APPLICATIONS -->
        <td class="px-6 py-4">
            <div class="flex items-center text-sm text-gray-600">
                <svg class="w-4 h-4 mr-1.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
                <span class="font-medium">${job.applications_count || 0}</span>
            </div>
        </td>

        <!-- VIEWS -->
        <td class="px-6 py-4">
            <div class="flex items-center text-sm text-gray-600">
                <svg class="w-4 h-4 mr-1.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                </svg>
                <span class="font-medium">${(job.views_count || 0).toLocaleString()}</span>
            </div>
        </td>

        <!-- ACTIONS -->
        <td class="px-6 py-4" style="position: relative; overflow: visible;">
            <div class="flex items-center space-x-1">
                <!-- Edit -->
                <button onclick="openEditJobModal(${job.id})" class="p-2 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition" title="Edit">
                    <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                </button>

                <!-- Pause/Play -->
                ${job.status === 'published' ? `
                    <button onclick="toggleJobStatus(${job.id})" class="p-2 text-gray-400 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition" title="Pause">
                        <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </button>
                ` : `
                    <button onclick="toggleJobStatus(${job.id})" class="p-2 text-gray-400 hover:text-green-600 hover:bg-green-50 rounded-lg transition" title="Activate">
                        <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </button>
                `}

                <!-- Analytics/Chart -->
                <button onclick="openShowJobModal(${job.id})" class="p-2 text-gray-400 hover:text-purple-600 hover:bg-purple-50 rounded-lg transition" title="Analytics">
                    <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                    </svg>
                </button>

                <!-- More Options -->
                <div class="relative inline-block" style="z-index: 9999;">
                    <button onclick="toggleMoreMenu(${job.id})" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition" title="More options">
                        <svg class="w-[18px] h-[18px]" fill="currentColor" viewBox="0 0 24 24">
                            <circle cx="12" cy="5" r="1.5"></circle>
                            <circle cx="12" cy="12" r="1.5"></circle>
                            <circle cx="12" cy="19" r="1.5"></circle>
                        </svg>
                    </button>
                    <div id="more-menu-${job.id}" class="hidden absolute right-0 top-full mt-1 w-48 bg-white rounded-lg shadow-2xl border border-gray-200 overflow-hidden" style="z-index: 99999; min-width: 12rem; position: absolute;">
                        <button onclick="openShowJobModal(${job.id})" class="w-full text-left block px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition whitespace-nowrap">View Details</button>
                        <button onclick="deleteJob(${job.id})" class="w-full text-left block px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 transition whitespace-nowrap">Delete</button>
                    </div>
                </div>
            </div>
        </td>
    `;
    
    // Insert at the top of the table
    tbody.insertBefore(newRow, tbody.firstChild);
}

// Simple toast notification function
function showSimpleToast(message, type = 'success') {
    const toast = document.createElement('div');
    const bgColor = type === 'success' ? 'bg-green-500' : 'bg-red-500';
    toast.className = `fixed top-4 right-4 ${bgColor} text-white px-6 py-3 rounded-lg shadow-lg z-50 flex items-center space-x-2`;
    toast.innerHTML = `
        <span>${message}</span>
        <button onclick="this.parentElement.remove()" class="ml-2 text-white hover:text-gray-200">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    `;
    document.body.appendChild(toast);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        if (toast.parentElement) {
            toast.remove();
        }
    }, 5000);
}

// Create Job Modal Functions
function openCreateJobModal(e) {
    if (e) {
        e.preventDefault();
        e.stopPropagation();
    }
    document.getElementById('create-job-modal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
    return false;
}

function closeCreateJobModal() {
    document.getElementById('create-job-modal').classList.add('hidden');
    document.body.style.overflow = 'auto';
    document.getElementById('create-job-form').reset();
    // Clear errors
    document.querySelectorAll('[id^="error-"]').forEach(el => {
        el.classList.add('hidden');
        el.textContent = '';
    });
}

// Handle form submission
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('create-job-form');
    if (form) {
        form.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const submitBtn = document.getElementById('create-job-submit-btn');
            const loadingIcon = document.getElementById('create-job-loading');
            const formData = new FormData(form);
            
            // Salary values are already in actual amounts, no conversion needed
            
            // Show loading
            submitBtn.disabled = true;
            loadingIcon.classList.remove('hidden');
            
            // Clear previous errors
            document.querySelectorAll('[id^="error-"]').forEach(el => {
                el.classList.add('hidden');
                el.textContent = '';
            });
            
            try {
                const response = await fetch('{{ route("employer.jobs.store") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: formData
                });
                
                let data;
                try {
                    const responseText = await response.text();
                    console.log('Raw response:', responseText);
                    data = JSON.parse(responseText);
                    console.log('Parsed response data:', data);
                } catch (jsonError) {
                    console.error('Failed to parse JSON response:', jsonError);
                    console.error('Response status:', response.status);
                    console.error('Response headers:', response.headers);
                    throw new Error('Invalid response from server');
                }
                
                if (response.ok) {
                    // Extract job from response (could be data.job or just data)
                    const job = data.job || data;
                    
                    if (!job || !job.id) {
                        console.error('Invalid job data received:', data);
                        alert('Job was created but could not be displayed. Please refresh the page.');
                        closeCreateJobModal();
                        // Reload page to show the new job
                        setTimeout(() => window.location.reload(), 1000);
                        return;
                    }
                    
                    // Ensure job has all required fields for display
                    if (!job.company) {
                        job.company = { name: 'Company' };
                    }
                    if (!job.category) {
                        job.category = null;
                    }
                    if (job.applications_count === undefined) {
                        job.applications_count = 0;
                    }
                    if (job.views_count === undefined) {
                        job.views_count = 0;
                    }
                    if (!job.created_at) {
                        job.created_at = new Date().toISOString();
                    }
                    
                    // Success - close modal first
                    closeCreateJobModal();
                    
                    // Show success message
                    const successMessage = data.message || 'Job posting created successfully!';
                    if (typeof window.showSuccessToast === 'function') {
                        window.showSuccessToast(successMessage);
                    } else {
                        // Create a simple toast notification
                        showSimpleToast(successMessage, 'success');
                    }
                    
                    // Add new job to table dynamically
                    try {
                        addJobToTable(job);
                    } catch (error) {
                        console.error('Error adding job to table:', error);
                        // If adding to table fails, reload the page
                        setTimeout(() => window.location.reload(), 1500);
                    }
                } else {
                    // Show validation errors
                    if (data.errors) {
                        Object.keys(data.errors).forEach(field => {
                            const errorEl = document.getElementById(`error-${field}`);
                            if (errorEl) {
                                errorEl.textContent = data.errors[field][0];
                                errorEl.classList.remove('hidden');
                            }
                        });
                    } else {
                        const errorMessage = data.message || 'Failed to create job posting';
                        if (typeof window.showErrorToast === 'function') {
                            window.showErrorToast(errorMessage);
                        } else {
                            alert(errorMessage);
                        }
                    }
                }
            } catch (error) {
                console.error('Error creating job:', error);
                const errorMessage = error.message || 'An error occurred while creating the job posting';
                if (typeof window.showErrorToast === 'function') {
                    window.showErrorToast(errorMessage);
                } else {
                    alert(errorMessage);
                }
            } finally {
                submitBtn.disabled = false;
                loadingIcon.classList.add('hidden');
            }
        });
    }
    
    // Close modal on outside click
    const modal = document.getElementById('create-job-modal');
    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === this) {
                closeCreateJobModal();
            }
        });
    }
});
</script>

<!-- Create Job Modal -->
<div id="create-job-modal" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-xl max-w-4xl w-full max-h-[90vh] overflow-y-auto">
        <div class="p-6 border-b border-gray-200 sticky top-0 bg-white z-10">
            <div class="flex items-center justify-between">
                <h3 class="text-2xl font-bold text-gray-900">Post New Job</h3>
                <button onclick="closeCreateJobModal()" class="text-gray-400 hover:text-gray-600 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>
        
        <form id="create-job-form" class="p-6">
            @csrf
            
            <!-- Basic Information -->
            <div class="mb-8">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Basic Information</h2>
                
                <div class="space-y-6">
                    <div>
                        <label for="modal_title" class="block text-sm font-medium text-gray-700 mb-2">Job Title *</label>
                        <input type="text" id="modal_title" name="title" required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="e.g., Senior Software Engineer">
                        <p id="error-title" class="mt-1 text-sm text-red-600 hidden"></p>
                    </div>

                    <div>
                        <label for="modal_category_id" class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                        <select id="modal_category_id" name="category_id" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">Select a category</option>
                            @foreach($categories ?? [] as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="modal_description" class="block text-sm font-medium text-gray-700 mb-2">Job Description *</label>
                        <textarea id="modal_description" name="description" required rows="6" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Describe the role, responsibilities, and what you're looking for..."></textarea>
                        <p id="error-description" class="mt-1 text-sm text-red-600 hidden"></p>
                    </div>

                    <div>
                        <label for="modal_requirements" class="block text-sm font-medium text-gray-700 mb-2">Requirements</label>
                        <textarea id="modal_requirements" name="requirements" rows="4" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="List the required skills, qualifications, and experience..."></textarea>
                    </div>

                    <div>
                        <label for="modal_benefits" class="block text-sm font-medium text-gray-700 mb-2">Benefits</label>
                        <textarea id="modal_benefits" name="benefits" rows="3" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="List the benefits and perks..."></textarea>
                    </div>
                </div>
            </div>

            <!-- Job Details -->
            <div class="mb-8">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Job Details</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="modal_employment_type" class="block text-sm font-medium text-gray-700 mb-2">Employment Type</label>
                        <select id="modal_employment_type" name="employment_type" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">Select type</option>
                            <option value="full_time">Full Time</option>
                            <option value="part_time">Part Time</option>
                            <option value="contract">Contract</option>
                            <option value="freelance">Freelance</option>
                            <option value="internship">Internship</option>
                        </select>
                    </div>

                    <div>
                        <label for="modal_experience_level" class="block text-sm font-medium text-gray-700 mb-2">Experience Level</label>
                        <select id="modal_experience_level" name="experience_level" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">Select level</option>
                            <option value="entry">Entry Level</option>
                            <option value="mid">Mid Level</option>
                            <option value="senior">Senior Level</option>
                            <option value="executive">Executive</option>
                        </select>
                    </div>

                    <div>
                        <label for="modal_location" class="block text-sm font-medium text-gray-700 mb-2">Location</label>
                        <input type="text" id="modal_location" name="location" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="e.g., New York, NY">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Remote Work</label>
                        <div class="flex items-center">
                            <input type="checkbox" id="modal_is_remote" name="is_remote" value="1" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                            <label for="modal_is_remote" class="ml-2 text-sm text-gray-700">This is a remote position</label>
                        </div>
                    </div>

                    <div>
                        <label for="modal_salary_min" class="block text-sm font-medium text-gray-700 mb-2">Salary Min</label>
                        <input type="number" id="modal_salary_min" name="salary_min" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="e.g., 3000">
                    </div>

                    <div>
                        <label for="modal_salary_max" class="block text-sm font-medium text-gray-700 mb-2">Salary Max</label>
                        <input type="number" id="modal_salary_max" name="salary_max" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="e.g., 5000">
                    </div>

                    <div>
                        <label for="modal_currency" class="block text-sm font-medium text-gray-700 mb-2">Currency</label>
                        <select id="modal_currency" name="currency" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="SCR">SCR</option>
                            <option value="USD">USD</option>
                            <option value="EUR">EUR</option>
                        </select>
                    </div>

                    <div>
                        <label for="modal_application_deadline" class="block text-sm font-medium text-gray-700 mb-2">Application Deadline</label>
                        <input type="date" id="modal_application_deadline" name="application_deadline" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                </div>
            </div>

            <!-- Publishing Options -->
            <div class="mb-8">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Publishing Options</h2>
                
                <div>
                    <label for="modal_status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select id="modal_status" name="status" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="draft">Save as Draft</option>
                        <option value="published">Publish Immediately</option>
                    </select>
                    <p class="mt-2 text-sm text-gray-500">Draft jobs are saved but not visible to job seekers. Published jobs are immediately visible.</p>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200">
                <button type="button" onclick="closeCreateJobModal()" class="px-6 py-2 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition font-medium">
                    Cancel
                </button>
                <button type="submit" id="create-job-submit-btn" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium flex items-center space-x-2">
                    <span>Create Job Posting</span>
                    <svg id="create-job-loading" class="w-5 h-5 animate-spin hidden" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Job Modal -->
<div id="edit-job-modal" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-xl max-w-4xl w-full max-h-[90vh] overflow-y-auto">
        <div class="p-6 border-b border-gray-200 sticky top-0 bg-white z-10">
            <div class="flex items-center justify-between">
                <h3 class="text-2xl font-bold text-gray-900">Edit Job Posting</h3>
                <button onclick="closeEditJobModal()" class="text-gray-400 hover:text-gray-600 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>
        
        <form id="edit-job-form" class="p-6">
            @csrf
            @method('PUT')
            <input type="hidden" id="edit-job-id" name="job_id">
            
            <!-- Basic Information -->
            <div class="mb-8">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Basic Information</h2>
                
                <div class="space-y-6">
                    <div>
                        <label for="edit_title" class="block text-sm font-medium text-gray-700 mb-2">Job Title *</label>
                        <input type="text" id="edit_title" name="title" required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="e.g., Senior Software Engineer">
                        <p id="error-edit-title" class="mt-1 text-sm text-red-600 hidden"></p>
                    </div>

                    <div>
                        <label for="edit_category_id" class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                        <select id="edit_category_id" name="category_id" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">Select a category</option>
                            @foreach($categories ?? [] as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="edit_description" class="block text-sm font-medium text-gray-700 mb-2">Job Description *</label>
                        <textarea id="edit_description" name="description" required rows="6" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Describe the role, responsibilities, and what you're looking for..."></textarea>
                        <p id="error-edit-description" class="mt-1 text-sm text-red-600 hidden"></p>
                    </div>

                    <div>
                        <label for="edit_requirements" class="block text-sm font-medium text-gray-700 mb-2">Requirements</label>
                        <textarea id="edit_requirements" name="requirements" rows="4" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="List the required skills, qualifications, and experience..."></textarea>
                    </div>

                    <div>
                        <label for="edit_benefits" class="block text-sm font-medium text-gray-700 mb-2">Benefits</label>
                        <textarea id="edit_benefits" name="benefits" rows="3" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="List the benefits and perks..."></textarea>
                    </div>
                </div>
            </div>

            <!-- Job Details -->
            <div class="mb-8">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Job Details</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="edit_employment_type" class="block text-sm font-medium text-gray-700 mb-2">Employment Type</label>
                        <select id="edit_employment_type" name="employment_type" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">Select type</option>
                            <option value="full_time">Full Time</option>
                            <option value="part_time">Part Time</option>
                            <option value="contract">Contract</option>
                            <option value="freelance">Freelance</option>
                            <option value="internship">Internship</option>
                        </select>
                    </div>

                    <div>
                        <label for="edit_experience_level" class="block text-sm font-medium text-gray-700 mb-2">Experience Level</label>
                        <select id="edit_experience_level" name="experience_level" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">Select level</option>
                            <option value="entry">Entry Level</option>
                            <option value="mid">Mid Level</option>
                            <option value="senior">Senior Level</option>
                            <option value="executive">Executive</option>
                        </select>
                    </div>

                    <div>
                        <label for="edit_location" class="block text-sm font-medium text-gray-700 mb-2">Location</label>
                        <input type="text" id="edit_location" name="location" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="e.g., New York, NY">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Remote Work</label>
                        <div class="flex items-center">
                            <input type="checkbox" id="edit_is_remote" name="is_remote" value="1" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                            <label for="edit_is_remote" class="ml-2 text-sm text-gray-700">This is a remote position</label>
                        </div>
                    </div>

                    <div>
                        <label for="edit_salary_min" class="block text-sm font-medium text-gray-700 mb-2">Salary Min</label>
                        <input type="number" id="edit_salary_min" name="salary_min" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="e.g., 3000">
                    </div>

                    <div>
                        <label for="edit_salary_max" class="block text-sm font-medium text-gray-700 mb-2">Salary Max</label>
                        <input type="number" id="edit_salary_max" name="salary_max" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="e.g., 5000">
                    </div>

                    <div>
                        <label for="edit_currency" class="block text-sm font-medium text-gray-700 mb-2">Currency</label>
                        <select id="edit_currency" name="currency" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="SCR">SCR</option>
                            <option value="USD">USD</option>
                            <option value="EUR">EUR</option>
                        </select>
                    </div>

                    <div>
                        <label for="edit_application_deadline" class="block text-sm font-medium text-gray-700 mb-2">Application Deadline</label>
                        <input type="date" id="edit_application_deadline" name="application_deadline" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                </div>
            </div>

            <!-- Publishing Options -->
            <div class="mb-8">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Publishing Options</h2>
                
                <div>
                    <label for="edit_status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select id="edit_status" name="status" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="draft">Draft</option>
                        <option value="published">Published</option>
                        <option value="closed">Closed</option>
                        <option value="archived">Archived</option>
                    </select>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200">
                <button type="button" onclick="closeEditJobModal()" class="px-6 py-2 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition font-medium">
                    Cancel
                </button>
                <button type="submit" id="edit-job-submit-btn" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium flex items-center space-x-2">
                    <span>Update Job Posting</span>
                    <svg id="edit-job-loading" class="w-5 h-5 animate-spin hidden" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Show Job Modal -->
<div id="show-job-modal" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-xl max-w-4xl w-full max-h-[90vh] overflow-y-auto">
        <div class="p-6 border-b border-gray-200 sticky top-0 bg-white z-10">
            <div class="flex items-center justify-between">
                <h3 id="show-job-title" class="text-2xl font-bold text-gray-900"></h3>
                <button onclick="closeShowJobModal()" class="text-gray-400 hover:text-gray-600 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>
        
        <div id="show-job-content" class="p-6">
            <!-- Content will be loaded dynamically -->
            <div class="flex items-center justify-center py-12">
                <svg class="w-8 h-8 animate-spin text-gray-400" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </div>
        </div>
    </div>
</div>

<script>
// Edit Job Modal Functions
async function openEditJobModal(jobId) {
    const modal = document.getElementById('edit-job-modal');
    const form = document.getElementById('edit-job-form');
    
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
    
    // Show loading state
    form.style.opacity = '0.5';
    form.style.pointerEvents = 'none';
    
    try {
        const response = await fetch(`/employer/jobs/${jobId}/edit`, {
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
        });
        
        const data = await response.json();
        
        if (response.ok) {
            const job = data.job;
            
            // Populate form fields
            document.getElementById('edit-job-id').value = job.id;
            document.getElementById('edit_title').value = job.title || '';
            document.getElementById('edit_category_id').value = job.category_id || '';
            document.getElementById('edit_description').value = job.description || '';
            document.getElementById('edit_requirements').value = job.requirements || '';
            document.getElementById('edit_benefits').value = job.benefits || '';
            document.getElementById('edit_employment_type').value = job.employment_type || '';
            document.getElementById('edit_experience_level').value = job.experience_level || '';
            document.getElementById('edit_location').value = job.location || '';
            document.getElementById('edit_is_remote').checked = job.is_remote || false;
            document.getElementById('edit_salary_min').value = job.salary_min || '';
            document.getElementById('edit_salary_max').value = job.salary_max || '';
            document.getElementById('edit_currency').value = job.currency || 'SCR';
            document.getElementById('edit_application_deadline').value = job.application_deadline ? job.application_deadline.split('T')[0] : '';
            document.getElementById('edit_status').value = job.status || 'draft';
            
            // Clear errors
            document.querySelectorAll('[id^="error-edit-"]').forEach(el => {
                el.classList.add('hidden');
                el.textContent = '';
            });
        } else {
            alert('Failed to load job data');
            closeEditJobModal();
        }
    } catch (error) {
        console.error('Error loading job:', error);
        alert('An error occurred while loading the job');
        closeEditJobModal();
    } finally {
        form.style.opacity = '1';
        form.style.pointerEvents = 'auto';
    }
}

function closeEditJobModal() {
    document.getElementById('edit-job-modal').classList.add('hidden');
    document.body.style.overflow = 'auto';
    document.getElementById('edit-job-form').reset();
    document.querySelectorAll('[id^="error-edit-"]').forEach(el => {
        el.classList.add('hidden');
        el.textContent = '';
    });
}

// Show Job Modal Functions
async function openShowJobModal(jobId) {
    const modal = document.getElementById('show-job-modal');
    const content = document.getElementById('show-job-content');
    const title = document.getElementById('show-job-title');
    
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
    
    // Show loading
    content.innerHTML = `
        <div class="flex items-center justify-center py-12">
            <svg class="w-8 h-8 animate-spin text-gray-400" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        </div>
    `;
    title.textContent = 'Loading...';
    
    try {
        const response = await fetch(`/employer/jobs/${jobId}`, {
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
        });
        
        const data = await response.json();
        
        if (response.ok) {
            const job = data.job;
            title.textContent = job.title;
            
            // Format salary
            let salaryDisplay = '';
            if (job.salary_min || job.salary_max) {
                const min = job.salary_min ? (job.salary_min >= 1000 ? Math.round(job.salary_min / 1000) + 'k' : job.salary_min.toLocaleString()) : '';
                const max = job.salary_max ? (job.salary_max >= 1000 ? Math.round(job.salary_max / 1000) + 'k' : job.salary_max.toLocaleString()) : '';
                if (min && max) {
                    salaryDisplay = `${job.currency || 'SCR'} ${min} - ${max}`;
                } else if (min) {
                    salaryDisplay = `${job.currency || 'SCR'} ${min}+`;
                } else if (max) {
                    salaryDisplay = `Up to ${job.currency || 'SCR'} ${max}`;
                }
            }
            
            // Determine status badge
            let statusClass = 'bg-gray-100 text-gray-800';
            if (job.status === 'published') statusClass = 'bg-green-100 text-green-800';
            else if (job.status === 'draft') statusClass = 'bg-yellow-100 text-yellow-800';
            else if (job.status === 'closed') statusClass = 'bg-red-100 text-red-800';
            
            content.innerHTML = `
                <div class="mb-6">
                    <div class="flex items-start justify-between mb-4">
                        <div>
                            <p class="text-gray-600">${job.company?.name || 'Unknown Company'}</p>
                        </div>
                        <span class="px-3 py-1 rounded-full text-sm font-medium ${statusClass}">
                            ${job.status.charAt(0).toUpperCase() + job.status.slice(1)}
                        </span>
                    </div>
                </div>

                <div class="bg-gray-50 rounded-lg p-6 mb-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Job Details</h2>
                    <div class="grid grid-cols-2 gap-4 mb-6">
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Location</p>
                            <p class="text-sm font-medium text-gray-900">${job.is_remote ? 'Remote' : (job.location || 'Not specified')}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Employment Type</p>
                            <p class="text-sm font-medium text-gray-900">${job.employment_type ? job.employment_type.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase()) : 'Not specified'}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Experience Level</p>
                            <p class="text-sm font-medium text-gray-900">${job.experience_level ? job.experience_level.charAt(0).toUpperCase() + job.experience_level.slice(1) : 'Not specified'}</p>
                        </div>
                        ${salaryDisplay ? `
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Salary</p>
                            <p class="text-sm font-medium text-gray-900">${salaryDisplay}</p>
                        </div>
                        ` : ''}
                        ${job.application_deadline ? `
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Application Deadline</p>
                            <p class="text-sm font-medium text-gray-900">${new Date(job.application_deadline).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })}</p>
                        </div>
                        ` : ''}
                    </div>

                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Description</h3>
                        <p class="text-gray-700 whitespace-pre-wrap">${job.description || 'No description provided'}</p>
                    </div>

                    ${job.requirements ? `
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Requirements</h3>
                        <p class="text-gray-700 whitespace-pre-wrap">${job.requirements}</p>
                    </div>
                    ` : ''}

                    ${job.benefits ? `
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Benefits</h3>
                        <p class="text-gray-700 whitespace-pre-wrap">${job.benefits}</p>
                    </div>
                    ` : ''}
                </div>

                <div class="grid grid-cols-3 gap-4 mb-6">
                    <div class="bg-gray-50 rounded-lg p-6">
                        <div class="text-3xl font-bold text-gray-900 mb-1">${(job.views_count || 0).toLocaleString()}</div>
                        <div class="text-sm text-gray-500">Total Views</div>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-6">
                        <div class="text-3xl font-bold text-gray-900 mb-1">${job.applications_count || 0}</div>
                        <div class="text-sm text-gray-500">Applications</div>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-6">
                        <div class="text-sm font-medium text-gray-900 mb-1">Posted</div>
                        <div class="text-sm text-gray-500">${new Date(job.created_at).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })}</div>
                    </div>
                </div>

                <div class="flex items-center justify-end space-x-3 pt-6 border-t border-gray-200">
                    <button onclick="openEditJobModal(${job.id}); closeShowJobModal();" class="px-4 py-2 bg-white text-gray-700 rounded-lg border border-gray-300 hover:bg-gray-50 transition font-medium">
                        Edit Job
                    </button>
                    <button onclick="deleteJob(${job.id}); closeShowJobModal();" class="px-4 py-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition font-medium">
                        Delete
                    </button>
                </div>
            `;
        } else {
            content.innerHTML = '<p class="text-red-600">Failed to load job details</p>';
        }
    } catch (error) {
        console.error('Error loading job:', error);
        content.innerHTML = '<p class="text-red-600">An error occurred while loading the job</p>';
    }
}

function closeShowJobModal() {
    document.getElementById('show-job-modal').classList.add('hidden');
    document.body.style.overflow = 'auto';
}

// Handle edit form submission
document.addEventListener('DOMContentLoaded', function() {
    const editForm = document.getElementById('edit-job-form');
    if (editForm) {
        editForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const jobId = document.getElementById('edit-job-id').value;
            const submitBtn = document.getElementById('edit-job-submit-btn');
            const loadingIcon = document.getElementById('edit-job-loading');
            const formData = new FormData(editForm);
            
            // Salary values are already in actual amounts, no conversion needed
            
            // Show loading
            submitBtn.disabled = true;
            loadingIcon.classList.remove('hidden');
            
            // Clear previous errors
            document.querySelectorAll('[id^="error-edit-"]').forEach(el => {
                el.classList.add('hidden');
                el.textContent = '';
            });
            
            try {
                const response = await fetch(`/employer/jobs/${jobId}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: formData
                });
                
                const data = await response.json();
                
                if (response.ok) {
                    // Success - close modal and update table row
                    closeEditJobModal();
                    
                    if (typeof window.showSuccessToast === 'function') {
                        window.showSuccessToast('Job posting updated successfully!');
                    } else {
                        alert('Job posting updated successfully!');
                    }
                    
                    // Update the table row
                    updateJobRowInTable(data.job);
                } else {
                    // Show validation errors
                    if (data.errors) {
                        Object.keys(data.errors).forEach(field => {
                            const errorEl = document.getElementById(`error-edit-${field}`);
                            if (errorEl) {
                                errorEl.textContent = data.errors[field][0];
                                errorEl.classList.remove('hidden');
                            }
                        });
                    } else {
                        alert(data.message || 'Failed to update job posting');
                    }
                }
            } catch (error) {
                console.error('Error updating job:', error);
                alert('An error occurred while updating the job posting');
            } finally {
                submitBtn.disabled = false;
                loadingIcon.classList.add('hidden');
            }
        });
    }
    
    // Close modals on outside click
    ['edit-job-modal', 'show-job-modal'].forEach(modalId => {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.addEventListener('click', function(e) {
                if (e.target === this) {
                    if (modalId === 'edit-job-modal') {
                        closeEditJobModal();
                    } else {
                        closeShowJobModal();
                    }
                }
            });
        }
    });
});

// Function to update job row in table
function updateJobRowInTable(job) {
    const row = document.querySelector(`tr[data-job-id="${job.id}"]`);
    if (!row) return;
    
    // Update title
    const titleCell = row.querySelector('td:first-child');
    if (titleCell) {
        const titleText = titleCell.querySelector('.text-sm.font-medium');
        if (titleText) {
            titleText.textContent = job.title;
        }
    }
    
    // Update location
    const locationCell = row.querySelector('td:nth-child(2)');
    if (locationCell) {
        locationCell.textContent = job.is_remote ? 'Remote' : (job.location || 'Not specified');
    }
    
    // Update employment type
    const typeCell = row.querySelector('td:nth-child(3)');
    if (typeCell) {
        typeCell.textContent = job.employment_type ? job.employment_type.replace('_', '-').replace(/\b\w/g, l => l.toUpperCase()) : 'Full-time';
    }
    
    // Update status
    updateJobStatusInTable(job.id, job.status);
}
</script>
@endsection
