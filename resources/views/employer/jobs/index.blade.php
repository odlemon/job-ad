@extends('layouts.employer')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    @include('partials.employer-navbar')

    <div class="flex">
        @include('partials.employer-sidebar')

        <main class="flex-1 min-w-0 p-8 ml-64 w-full">
            <div class="w-full max-w-none space-y-6">
                <div class="flex items-center justify-between gap-4 flex-wrap">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Job Listings</h1>
                        <p class="text-gray-600 dark:text-gray-400 mt-1">Manage and track all your job postings</p>
                    </div>
                    <button type="button" onclick="openCreateJobModal(event)"
                            class="flex items-center gap-2 px-4 py-2 text-white rounded font-medium hover:shadow-lg transition-all"
                            style="background: linear-gradient(to right, #2563eb, #06b6d4);">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Post New Job
                    </button>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded border border-gray-200 dark:border-gray-700 shadow-sm">
                    <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                        <div class="flex flex-col sm:flex-row gap-4">
                            <div class="flex-1 relative">
                                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                <input type="text" id="jobs-search" value="{{ $search ?? '' }}" placeholder="Search jobs..."
                                       class="w-full pl-10 pr-4 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded text-sm text-gray-900 dark:text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div class="flex gap-2">
                                <select id="jobs-status" class="px-4 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded text-sm text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="all">All Status</option>
                                    <option value="published">Active</option>
                                    <option value="paused">Paused</option>
                                    <option value="draft">Draft</option>
                                    <option value="closed">Closed</option>
                                </select>
                                <button type="button" class="flex items-center gap-2 px-4 py-2 border border-gray-200 dark:border-gray-600 rounded text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                                    Filter
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50 dark:bg-gray-700/50 border-b border-gray-200 dark:border-gray-700">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Job Title</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Location</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Type</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Applications</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Views</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse($jobs as $job)
                                    @php
                                        $hasActiveCampaign = $job->relationLoaded('campaigns') && $job->campaigns->where('status', 'active')->isNotEmpty();
                                        $rowStatus = $job->status === 'draft' && $job->published_at ? 'paused' : $job->status;
                                        if ($rowStatus === 'published') {
                                            $filterStatus = 'published';
                                            $statusLabel = 'active';
                                            $statusClass = 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400';
                                        } elseif ($rowStatus === 'paused') {
                                            $filterStatus = 'paused';
                                            $statusLabel = 'paused';
                                            $statusClass = 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400';
                                        } else {
                                            $filterStatus = $rowStatus;
                                            $statusLabel = $rowStatus;
                                            $statusClass = 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300';
                                        }
                                        if ($job->hide_salary) {
                                            $salaryLabel = 'Negotiable';
                                        } elseif ($job->salary_min || $job->salary_max) {
                                            $min = is_numeric($job->salary_min) ? ($job->salary_min >= 1000 ? '$' . number_format($job->salary_min / 1000, 0) . 'k' : '$' . $job->salary_min) : '';
                                            $max = is_numeric($job->salary_max) ? ($job->salary_max >= 1000 ? '$' . number_format($job->salary_max / 1000, 0) . 'k' : '$' . $job->salary_max) : '';
                                            $salaryLabel = trim($min . ($min && $max ? ' - ' : '') . $max) ?: '—';
                                        } else {
                                            $salaryLabel = '—';
                                        }
                                    @endphp
                                    <tr class="job-row hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors"
                                        data-job-id="{{ $job->id }}"
                                        data-job-title="{{ strtolower(e($job->title)) }}"
                                        data-job-location="{{ strtolower(e($job->location ?? ($job->is_remote ? 'remote' : ''))) }}"
                                        data-status="{{ $filterStatus }}">
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-3">
                                                <div class="w-10 h-10 rounded flex items-center justify-center flex-shrink-0" style="background: linear-gradient(to bottom right, #2563eb, #06b6d4);">
                                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                                </div>
                                                <div>
                                                    <div class="flex items-center gap-2 flex-wrap">
                                                        <p class="font-semibold text-gray-900 dark:text-white">{{ $job->title }}</p>
                                                        @if($hasActiveCampaign)
                                                            <span class="px-2 py-0.5 text-white text-xs font-medium rounded" style="background: linear-gradient(to right, #f59e0b, #f97316);">Promoted</span>
                                                        @endif
                                                    </div>
                                                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $salaryLabel }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">@if($job->is_remote) Remote @else {{ $job->location ?? 'Not specified' }} @endif</td>
                                        <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">{{ ucfirst(str_replace('_', '-', $job->employment_type ?? 'Full-time')) }}</td>
                                        <td class="px-6 py-4"><span class="px-3 py-1 rounded-full text-xs font-medium {{ $statusClass }}">{{ $statusLabel }}</span></td>
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-2 text-sm text-gray-900 dark:text-white font-medium">
                                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                                {{ $job->applications_count ?? $job->applications()->count() }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-2 text-sm text-gray-900 dark:text-white font-medium">
                                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                {{ number_format($job->views_count ?? 0) }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-2">
                                                <button type="button" onclick="openEditJobModal({{ $job->id }})" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-600 rounded transition-colors" title="Edit Job">
                                                    <svg class="w-4 h-4 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                                </button>
                                                <a href="{{ route('employer.jobs.stats', $job->id) }}" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-600 rounded transition-colors inline-flex" title="View Statistics">
                                                    <svg class="w-4 h-4 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                                                </a>
                                                <button type="button" onclick="deleteJob({{ $job->id }})" class="p-2 hover:bg-red-50 dark:hover:bg-red-900/20 rounded transition-colors" title="Delete">
                                                    <svg class="w-4 h-4 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr id="jobs-empty-state">
                                        <td colspan="7" class="px-6 py-16 text-center">
                                            <div class="w-16 h-16 bg-gray-100 dark:bg-gray-800 rounded-full flex items-center justify-center mx-auto mb-4">
                                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                            </div>
                                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-1">No job postings found</h3>
                                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">Get started by creating your first job posting.</p>
                                            <button type="button" onclick="openCreateJobModal(event)" class="inline-flex items-center px-5 py-2.5 text-white rounded font-medium text-sm" style="background: linear-gradient(to right, #2563eb, #06b6d4);">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                                Post New Job
                                            </button>
                                        </td>
                                    </tr>
                                @endforelse
                                @if($jobs->isNotEmpty())
                                    <tr id="jobs-no-results" class="hidden">
                                        <td colspan="7" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">No jobs match your filters.</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>
<script>
(function() {
    function applyJobsFilters() {
        var search = (document.getElementById('jobs-search') && document.getElementById('jobs-search').value || '').trim().toLowerCase();
        var status = (document.getElementById('jobs-status') && document.getElementById('jobs-status').value) || 'all';
        var rows = document.querySelectorAll('tbody .job-row');
        var noResults = document.getElementById('jobs-no-results');
        var visible = 0;
        rows.forEach(function(row) {
            var title = (row.getAttribute('data-job-title') || '').toLowerCase();
            var location = (row.getAttribute('data-job-location') || '').toLowerCase();
            var rowStatus = row.getAttribute('data-status') || '';
            var matchSearch = !search || title.indexOf(search) !== -1 || location.indexOf(search) !== -1;
            var matchStatus = status === 'all' || rowStatus === status;
            var show = matchSearch && matchStatus;
            row.style.display = show ? '' : 'none';
            if (show) visible++;
        });
        if (noResults) {
            noResults.classList.toggle('hidden', visible > 0);
        }
    }
    function setupJobsFilters() {
        var searchEl = document.getElementById('jobs-search');
        var statusEl = document.getElementById('jobs-status');
        if (!searchEl && !statusEl) return;
        var debounce = null;
        if (searchEl) {
            searchEl.addEventListener('input', function() {
                clearTimeout(debounce);
                debounce = setTimeout(applyJobsFilters, 150);
            });
        }
        if (statusEl) {
            statusEl.addEventListener('change', applyJobsFilters);
        }
        applyJobsFilters();
    }
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', setupJobsFilters);
    } else {
        setupJobsFilters();
    }
})();

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
    let statusClass = 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300';
    
    if (newStatus === 'published') {
        statusLabel = 'active';
        statusClass = 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400';
    } else if (newStatus === 'draft') {
        // Check if it was previously published (paused)
        const currentLabel = statusCell.textContent.trim();
        if (currentLabel === 'active') {
            statusLabel = 'paused';
            statusClass = 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400';
        } else {
            statusLabel = 'draft';
            statusClass = 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300';
        }
    }
    
    statusCell.innerHTML = `<span class="px-3 py-1 rounded-full text-xs font-medium ${statusClass}">${statusLabel}</span>`;
    
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

async function deleteJob(jobId) {
    const confirmed = await window.showConfirmDialog(
        'This job posting will be permanently deleted.',
        { title: 'Delete job posting?', confirmText: 'Delete', cancelText: 'Cancel' }
    );
    if (!confirmed) {
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
            const row = document.querySelector(`tr[data-job-id="${jobId}"]`);
            if (row) {
                row.remove();
                if (!document.querySelector('tbody .job-row')) {
                    window.location.reload();
                }
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
    let salaryDisplay = '<p class="text-sm text-gray-500 dark:text-gray-400">—</p>';
    if (job.hide_salary) {
        salaryDisplay = '<p class="text-sm text-gray-500 dark:text-gray-400">Negotiable</p>';
    } else if (job.salary_min || job.salary_max) {
        const min = job.salary_min ? (job.salary_min >= 1000 ? '$' + Math.round(job.salary_min / 1000) + 'k' : '$' + job.salary_min.toLocaleString()) : '';
        const max = job.salary_max ? (job.salary_max >= 1000 ? '$' + Math.round(job.salary_max / 1000) + 'k' : '$' + job.salary_max.toLocaleString()) : '';
        if (min && max) {
            salaryDisplay = `<p class="text-sm text-gray-500 dark:text-gray-400">${min} - ${max}</p>`;
        } else if (min) {
            salaryDisplay = `<p class="text-sm text-gray-500 dark:text-gray-400">${min}+</p>`;
        } else if (max) {
            salaryDisplay = `<p class="text-sm text-gray-500 dark:text-gray-400">Up to ${max}</p>`;
        }
    }
    
    // Determine status
    let statusLabel = 'draft';
    let statusClass = 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300';
    if (job.status === 'published') {
        statusLabel = 'active';
        statusClass = 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400';
    } else if (job.status === 'draft' && job.published_at) {
        statusLabel = 'paused';
        statusClass = 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400';
    } else if (job.status === 'closed') {
        statusLabel = 'closed';
        statusClass = 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300';
    }
    
    const hasPromoted = Array.isArray(job.campaigns) && job.campaigns.some(function(c) { return c && c.status === 'active'; });
    const promotedBadge = hasPromoted
        ? '<span class="px-2 py-0.5 text-white text-xs font-medium rounded" style="background: linear-gradient(to right, #f59e0b, #f97316);">Promoted</span>'
        : '';
    
    // Format employment type
    const employmentType = job.employment_type ? job.employment_type.replace('_', '-').replace(/\b\w/g, l => l.toUpperCase()) : 'Full-time';
    
    // Location
    const location = job.is_remote ? 'Remote' : (job.location || 'Not specified');
    const rowStatus = (job.status === 'draft' && job.published_at) ? 'paused' : (job.status || 'draft');
    const titleLower = (job.title || '').toLowerCase();
    const locationLower = (job.is_remote ? 'remote' : (job.location || '')).toLowerCase();
    
    // Create new row
    const newRow = document.createElement('tr');
    newRow.setAttribute('data-job-id', job.id);
    newRow.setAttribute('data-job-title', titleLower);
    newRow.setAttribute('data-job-location', locationLower);
    newRow.setAttribute('data-status', rowStatus);
    newRow.className = 'job-row hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors';
    newRow.innerHTML = `
        <td class="px-6 py-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded flex items-center justify-center flex-shrink-0" style="background: linear-gradient(to bottom right, #2563eb, #06b6d4);">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                </div>
                <div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <p class="font-semibold text-gray-900 dark:text-white">${job.title}</p>
                        ${promotedBadge}
                    </div>
                    ${salaryDisplay}
                </div>
            </div>
        </td>
        <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">${location}</td>
        <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">${employmentType}</td>
        <td class="px-6 py-4"><span class="px-3 py-1 rounded-full text-xs font-medium ${statusClass}">${statusLabel}</span></td>
        <td class="px-6 py-4">
            <div class="flex items-center gap-2 text-sm text-gray-900 dark:text-white font-medium">
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                ${job.applications_count || 0}
            </div>
        </td>
        <td class="px-6 py-4">
            <div class="flex items-center gap-2 text-sm text-gray-900 dark:text-white font-medium">
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                ${(job.views_count || 0).toLocaleString()}
            </div>
        </td>
        <td class="px-6 py-4">
            <div class="flex items-center gap-2">
                <button type="button" onclick="openEditJobModal(${job.id})" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-600 rounded transition-colors" title="Edit Job">
                    <svg class="w-4 h-4 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                </button>
                <a href="/employer/jobs/${job.id}/stats" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-600 rounded transition-colors inline-flex" title="View Statistics">
                    <svg class="w-4 h-4 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                </a>
                <button type="button" onclick="deleteJob(${job.id})" class="p-2 hover:bg-red-50 dark:hover:bg-red-900/20 rounded transition-colors" title="Delete">
                    <svg class="w-4 h-4 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                </button>
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

// Create Job Modal - 3-step wizard
let cjmStep = 1;
let cjmSkills = [];

function openCreateJobModal(e) {
    if (e) { e.preventDefault(); e.stopPropagation(); }
    cjmStep = 1;
    cjmSkills = [];
    document.getElementById('create-job-modal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
    cjmRenderStep();
    return false;
}

function closeCreateJobModal() {
    document.getElementById('create-job-modal').classList.add('hidden');
    document.body.style.overflow = 'auto';
    document.getElementById('create-job-form').reset();
    cjmSkills = [];
    const sl = document.getElementById('cjm-skills-list');
    if (sl) sl.innerHTML = '';
    document.querySelectorAll('[id^="error-"]').forEach(el => { el.classList.add('hidden'); el.textContent = ''; });
}

function cjmRenderStep() {
    document.getElementById('cjm-step-1').classList.toggle('hidden', cjmStep !== 1);
    document.getElementById('cjm-step-2').classList.toggle('hidden', cjmStep !== 2);
    document.getElementById('cjm-step-3').classList.toggle('hidden', cjmStep !== 3);
    document.getElementById('cjm-step-label').textContent = `Step ${cjmStep} of 3`;

    // Indicators
    for (let i = 1; i <= 3; i++) {
        const ind = document.getElementById('cjm-ind-' + i);
        if (i <= cjmStep) {
            ind.className = 'w-9 h-9 rounded-full bg-blue-600 text-white flex items-center justify-center text-sm font-bold';
        } else {
            ind.className = 'w-9 h-9 rounded-full bg-white dark:bg-gray-800 border-2 border-gray-300 dark:border-gray-600 text-gray-400 flex items-center justify-center text-sm font-bold';
        }
    }
    for (let i = 1; i <= 2; i++) {
        const line = document.getElementById('cjm-line-' + i);
        line.className = i < cjmStep ? 'w-16 h-0.5 bg-blue-600' : 'w-16 h-0.5 bg-gray-300';
    }

    // Buttons
    const prev = document.getElementById('cjm-btn-prev');
    const cancel = document.getElementById('cjm-btn-cancel');
    const next = document.getElementById('cjm-btn-next');
    const submit = document.getElementById('cjm-btn-submit');
    prev.classList.toggle('hidden', cjmStep === 1);
    cancel.classList.toggle('hidden', cjmStep !== 1);
    next.classList.toggle('hidden', cjmStep === 3);
    submit.classList.toggle('hidden', cjmStep !== 3);

    if (cjmStep === 3) cjmBuildReview();
}

function cjmNextStep() {
    if (cjmStep === 1) {
        const title = document.getElementById('modal_title').value.trim();
        if (!title) { document.getElementById('modal_title').focus(); return; }
    }
    if (cjmStep === 2) {
        const desc = document.getElementById('modal_description').value.trim();
        if (!desc) { document.getElementById('modal_description').focus(); return; }
    }
    if (cjmStep < 3) { cjmStep++; cjmRenderStep(); }
}

function cjmPrevStep() {
    if (cjmStep > 1) { cjmStep--; cjmRenderStep(); }
}

function cjmBuildReview() {
    const v = k => { const e = document.getElementById(k); return e ? (e.options ? (e.selectedOptions[0]?.text || e.value) : e.value) : ''; };
    const rows = [
        ['Position', v('modal_title') || 'Not specified'],
        ['Category', v('modal_category_id') || 'Not specified'],
        ['Location', `${v('modal_island') || ''} ${v('modal_district') ? '- ' + v('modal_district') : ''}`.trim() || 'Not specified'],
        ['Type', `${v('modal_employment_type')} - ${v('modal_work_environment')}`],
        ['Education', v('modal_education_level') || 'Not specified'],
        ['Salary', document.getElementById('modal_hide_salary')?.checked ? 'Negotiable' : `${parseInt(document.getElementById('modal_salary_min')?.value || 0).toLocaleString()} SCR - ${parseInt(document.getElementById('modal_salary_max')?.value || 100000).toLocaleString()} SCR`],
    ];
    document.getElementById('cjm-review-content').innerHTML = rows.map(([l, r]) =>
        `<div class="flex items-center justify-between py-1.5"><span class="text-gray-500 dark:text-gray-400">${l}:</span><span class="font-medium text-gray-900 dark:text-white text-right">${r}</span></div>`
    ).join('');
}

// Skills tag input
document.addEventListener('DOMContentLoaded', function() {
    const skillInput = document.getElementById('cjm-skill-input');
    if (skillInput) {
        skillInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
            e.preventDefault();
                const val = this.value.trim();
                if (val && !cjmSkills.includes(val)) {
                    cjmSkills.push(val);
                    cjmRenderSkills();
                }
                this.value = '';
            }
            if (e.key === 'Backspace' && !this.value && cjmSkills.length) {
                cjmSkills.pop();
                cjmRenderSkills();
            }
        });
    }
});

function cjmRenderSkills() {
    const list = document.getElementById('cjm-skills-list');
    const hidden = document.getElementById('modal_requirements');
    list.innerHTML = cjmSkills.map((s, i) =>
        `<span class="inline-flex items-center gap-1 px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm font-medium">${s}<button type="button" onclick="cjmRemoveSkill(${i})" class="text-blue-500 hover:text-blue-700 ml-0.5">&times;</button></span>`
    ).join('');
    hidden.value = cjmSkills.join(', ');
}

function cjmRemoveSkill(i) { cjmSkills.splice(i, 1); cjmRenderSkills(); }

// Toggle hide salary
function cjmToggleHideSalary(checked) {
    const wrapper = document.getElementById('cjm-salary-slider-wrapper');
    if (checked) {
        wrapper.style.opacity = '0.3';
        wrapper.style.pointerEvents = 'none';
    } else {
        wrapper.style.opacity = '1';
        wrapper.style.pointerEvents = 'auto';
    }
}

// Dual-range salary slider
document.addEventListener('DOMContentLoaded', function() {
    const rangeMin = document.getElementById('cjm-range-min');
    const rangeMax = document.getElementById('cjm-range-max');
    if (!rangeMin || !rangeMax) return;

    function updateSalaryRange() {
        let minVal = parseInt(rangeMin.value);
        let maxVal = parseInt(rangeMax.value);
        if (minVal > maxVal) { [minVal, maxVal] = [maxVal, minVal]; rangeMin.value = minVal; rangeMax.value = maxVal; }
        document.getElementById('modal_salary_min').value = minVal;
        document.getElementById('modal_salary_max').value = maxVal;
        document.getElementById('cjm-sal-min').textContent = minVal.toLocaleString() + ' SCR';
        document.getElementById('cjm-sal-max').textContent = maxVal.toLocaleString() + ' SCR';
        const pctMin = (minVal / 100000) * 100;
        const pctMax = ((100000 - maxVal) / 100000) * 100;
        document.getElementById('cjm-range-fill').style.left = pctMin + '%';
        document.getElementById('cjm-range-fill').style.right = pctMax + '%';
    }
    rangeMin.addEventListener('input', updateSalaryRange);
    rangeMax.addEventListener('input', updateSalaryRange);
});

async function cjmSubmit() {
    const form = document.getElementById('create-job-form');
    const submitBtn = document.getElementById('cjm-btn-submit');
            const loadingIcon = document.getElementById('create-job-loading');
            const formData = new FormData(form);
            
    // Set location from island + district
    const island = formData.get('island') || '';
    const district = formData.get('district') || '';
    if (island || district) formData.set('location', [island, district].filter(Boolean).join(', '));
            
    // Set is_remote from work_environment
    const we = formData.get('work_environment');
    if (we === 'remote') formData.set('is_remote', '1');

            submitBtn.disabled = true;
            loadingIcon.classList.remove('hidden');
    document.querySelectorAll('[id^="error-"]').forEach(el => { el.classList.add('hidden'); el.textContent = ''; });
            
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
            data = JSON.parse(responseText);
        } catch (jsonError) {
            throw new Error('Invalid response from server');
        }
                
                if (response.ok) {
            const job = data.job || data;
            if (!job || !job.id) {
                    closeCreateJobModal();
                setTimeout(() => window.location.reload(), 1000);
                return;
            }
            closeCreateJobModal();
                    if (typeof window.showSuccessToast === 'function') {
                window.showSuccessToast(data.message || 'Job created! Redirecting to create campaignâ€¦');
            }
            if (data.redirect) {
                window.location.href = data.redirect;
                return;
            }
            if (!job.company) job.company = { name: 'Company' };
            if (!job.category) job.category = null;
            if (job.applications_count === undefined) job.applications_count = 0;
            if (job.views_count === undefined) job.views_count = 0;
            if (!job.created_at) job.created_at = new Date().toISOString();
            try { addJobToTable(job); } catch (e) { setTimeout(() => window.location.reload(), 1500); }
                } else {
                    if (data.errors) {
                // Go back to the step with errors
                if (data.errors.title || data.errors.category_id || data.errors.island || data.errors.district) { cjmStep = 1; cjmRenderStep(); }
                else if (data.errors.description) { cjmStep = 2; cjmRenderStep(); }
                        Object.keys(data.errors).forEach(field => {
                            const errorEl = document.getElementById(`error-${field}`);
                    if (errorEl) { errorEl.textContent = data.errors[field][0]; errorEl.classList.remove('hidden'); }
                        });
                    } else {
                if (typeof window.showErrorToast === 'function') window.showErrorToast(data.message || 'Failed to create job posting');
                    }
                }
            } catch (error) {
        if (typeof window.showErrorToast === 'function') window.showErrorToast(error.message || 'An error occurred');
            } finally {
                submitBtn.disabled = false;
                loadingIcon.classList.add('hidden');
            }
    }
    
    // Close modal on outside click
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('create-job-modal');
    if (modal) {
        modal.addEventListener('click', function(e) { if (e.target === this) closeCreateJobModal(); });
    }
});
</script>

<!-- Create Job Modal (3-step wizard) -->
<div id="create-job-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4" style="background:rgba(0,0,0,0.4);backdrop-filter:blur(2px);">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl w-full max-w-5xl max-h-[90vh] flex flex-col overflow-hidden">
        <!-- Header -->
        <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-700 flex-shrink-0" style="background:linear-gradient(180deg,#f0f4ff 0%,#fff 100%);">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">Create Job Posting</h3>
                    <p id="cjm-step-label" class="text-sm text-blue-600 mt-0.5">Step 1 of 3</p>
                </div>
                <button onclick="closeCreateJobModal()" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        </div>
        
        <!-- Body (scrollable) -->
        <div class="flex-1 overflow-y-auto p-6 bg-white dark:bg-gray-800">
            <form id="create-job-form">
            @csrf
            
                <!-- Step Indicator -->
                <div class="flex items-center justify-center mb-8">
                    <div id="cjm-ind-1" class="w-9 h-9 rounded-full bg-blue-600 text-white flex items-center justify-center text-sm font-bold">1</div>
                    <div id="cjm-line-1" class="w-16 h-0.5 bg-gray-300 dark:bg-gray-600"></div>
                    <div id="cjm-ind-2" class="w-9 h-9 rounded-full bg-white dark:bg-gray-800 border-2 border-gray-300 dark:border-gray-600 text-gray-400 flex items-center justify-center text-sm font-bold">2</div>
                    <div id="cjm-line-2" class="w-16 h-0.5 bg-gray-300 dark:bg-gray-600"></div>
                    <div id="cjm-ind-3" class="w-9 h-9 rounded-full bg-white dark:bg-gray-800 border-2 border-gray-300 dark:border-gray-600 text-gray-400 flex items-center justify-center text-sm font-bold">3</div>
                </div>

                <!-- STEP 1: Basic Information -->
                <div id="cjm-step-1">
                    <h4 class="text-lg font-bold text-gray-900 dark:text-white mb-5">Basic Information</h4>
                    <div class="space-y-5">
                    <div>
                            <label class="flex items-center gap-1.5 text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                                <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21"/></svg>
                                Job Title *
                            </label>
                            <input type="text" id="modal_title" name="title" required class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="e.g., Senior Software Engineer">
                        <p id="error-title" class="mt-1 text-sm text-red-600 hidden"></p>
                    </div>
                    <div>
                            <label class="flex items-center gap-1.5 text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                                <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 005.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 009.568 3z"/><path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6z"/></svg>
                                Category *
                            </label>
                            <select id="modal_category_id" name="category_id" class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">Select a category</option>
                            @foreach($categories ?? [] as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                        <div class="grid grid-cols-2 gap-4">
                    <div>
                                <label class="flex items-center gap-1.5 text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                                    <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/></svg>
                                    Island *
                                </label>
                                <select id="modal_island" name="island" class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="">Select an island</option>
                                    <option value="Mahe">MahÃ©</option>
                                    <option value="Praslin">Praslin</option>
                                    <option value="La Digue">La Digue</option>
                                    <option value="Silhouette">Silhouette</option>
                                    <option value="Other">Other</option>
                                </select>
                    </div>
                    <div>
                                <label class="flex items-center gap-1.5 text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                                    <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/></svg>
                                    District *
                                </label>
                                <select id="modal_district" name="district" class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="">Select a district</option>
                                    <option value="Victoria">Victoria</option>
                                    <option value="Anse Royale">Anse Royale</option>
                                    <option value="Beau Vallon">Beau Vallon</option>
                                    <option value="Glacis">Glacis</option>
                                    <option value="Grand Anse Mahe">Grand Anse MahÃ©</option>
                                    <option value="Grand Anse Praslin">Grand Anse Praslin</option>
                                    <option value="Baie Lazare">Baie Lazare</option>
                                    <option value="Takamaka">Takamaka</option>
                                    <option value="Port Glaud">Port Glaud</option>
                                    <option value="Other">Other</option>
                                </select>
                    </div>
                    </div>
                        <div class="grid grid-cols-3 gap-4">
                    <div>
                                <label class="flex items-center gap-1.5 text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                                    <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    Job Type *
                                </label>
                                <select id="modal_employment_type" name="employment_type" class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="full_time">Full Time</option>
                            <option value="part_time">Part Time</option>
                            <option value="contract">Contract</option>
                                    <option value="temporary">Temporary</option>
                        </select>
                    </div>
                    <div>
                                <label class="flex items-center gap-1.5 text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                                    <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9.004 9.004 0 008.716-6.747M12 21a9.004 9.004 0 01-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 017.843 4.582M12 3a8.997 8.997 0 00-7.843 4.582m15.686 0A11.953 11.953 0 0112 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0121 12c0 .778-.099 1.533-.284 2.253m0 0A17.919 17.919 0 0112 16.5c-3.162 0-6.133-.815-8.716-2.247m0 0A9.015 9.015 0 013 12c0-1.605.42-3.113 1.157-4.418"/></svg>
                                    Work Environment *
                                </label>
                                <select id="modal_work_environment" name="work_environment" class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="on_site">On-site</option>
                                    <option value="remote">Remote</option>
                                    <option value="hybrid">Hybrid</option>
                        </select>
                    </div>
                    <div>
                                <label class="flex items-center gap-1.5 text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                                    <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.342M6.75 15a.75.75 0 100-1.5.75.75 0 000 1.5zm0 0v-3.675A55.378 55.378 0 0112 8.443m-7.007 11.55A5.981 5.981 0 006.75 15.75v-1.5"/></svg>
                                    Education Level
                                </label>
                                <select id="modal_education_level" name="education_level" class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="">Not specified</option>
                                    <option value="high_school">High School</option>
                                    <option value="diploma">Diploma</option>
                                    <option value="bachelors">Bachelor's Degree</option>
                                    <option value="masters">Master's Degree</option>
                                    <option value="phd">PhD</option>
                                </select>
                    </div>
                        </div>
                    <div>
                            <label class="flex items-center gap-1.5 text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                                <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Salary Range (Optional)
                            </label>
                            <div id="cjm-salary-slider-wrapper">
                                <div class="relative h-6 mt-2">
                                    <div class="absolute top-1/2 left-0 right-0 h-1 bg-gray-200 dark:bg-gray-700 rounded-full -translate-y-1/2"></div>
                                    <div id="cjm-range-fill" class="absolute top-1/2 h-1 bg-blue-500 rounded-full -translate-y-1/2" style="left:0%;right:0%"></div>
                                    <input type="range" id="cjm-range-min" min="0" max="100000" step="1000" value="0" class="absolute top-0 left-0 w-full h-6 appearance-none bg-transparent pointer-events-none [&::-webkit-slider-thumb]:pointer-events-auto [&::-webkit-slider-thumb]:appearance-none [&::-webkit-slider-thumb]:w-4 [&::-webkit-slider-thumb]:h-4 [&::-webkit-slider-thumb]:bg-blue-600 [&::-webkit-slider-thumb]:rounded-full [&::-webkit-slider-thumb]:shadow [&::-webkit-slider-thumb]:cursor-pointer [&::-moz-range-thumb]:pointer-events-auto [&::-moz-range-thumb]:w-4 [&::-moz-range-thumb]:h-4 [&::-moz-range-thumb]:bg-blue-600 [&::-moz-range-thumb]:rounded-full [&::-moz-range-thumb]:border-0 [&::-moz-range-thumb]:cursor-pointer">
                                    <input type="range" id="cjm-range-max" min="0" max="100000" step="1000" value="100000" class="absolute top-0 left-0 w-full h-6 appearance-none bg-transparent pointer-events-none [&::-webkit-slider-thumb]:pointer-events-auto [&::-webkit-slider-thumb]:appearance-none [&::-webkit-slider-thumb]:w-4 [&::-webkit-slider-thumb]:h-4 [&::-webkit-slider-thumb]:bg-blue-600 [&::-webkit-slider-thumb]:rounded-full [&::-webkit-slider-thumb]:shadow [&::-webkit-slider-thumb]:cursor-pointer [&::-moz-range-thumb]:pointer-events-auto [&::-moz-range-thumb]:w-4 [&::-moz-range-thumb]:h-4 [&::-moz-range-thumb]:bg-blue-600 [&::-moz-range-thumb]:rounded-full [&::-moz-range-thumb]:border-0 [&::-moz-range-thumb]:cursor-pointer">
                        </div>
                                <div class="flex items-center justify-between text-xs text-gray-500 dark:text-gray-400 mt-1">
                                    <span>0 SCR</span>
                                    <span>100k SCR</span>
                    </div>
                                <div class="flex items-center justify-center gap-2 mt-3">
                                    <span class="px-3 py-1 bg-emerald-50 text-emerald-700 rounded-md text-sm font-medium border border-emerald-200" id="cjm-sal-min">0 SCR</span>
                                    <span class="text-gray-400">-</span>
                                    <span class="px-3 py-1 bg-emerald-50 text-emerald-700 rounded-md text-sm font-medium border border-emerald-200" id="cjm-sal-max">100,000 SCR</span>
                    </div>
                            </div>
                            <div class="flex items-center mt-3">
                                <input type="checkbox" id="modal_hide_salary" name="hide_salary" value="1" class="w-4 h-4 text-blue-600 border-gray-300 dark:border-gray-600 rounded focus:ring-blue-500" onchange="cjmToggleHideSalary(this.checked)">
                                <label for="modal_hide_salary" class="ml-2 text-sm text-gray-600 dark:text-gray-400">Don't show salary (display as "Negotiable")</label>
                            </div>
                            <input type="hidden" id="modal_salary_min" name="salary_min" value="0">
                            <input type="hidden" id="modal_salary_max" name="salary_max" value="100000">
                        </div>
                    </div>
                    </div>

                <!-- STEP 2: Job Details -->
                <div id="cjm-step-2" class="hidden">
                    <h4 class="text-lg font-bold text-gray-900 dark:text-white mb-5">Job Details</h4>
                    <div class="space-y-5">
                    <div>
                            <label class="flex items-center gap-1.5 text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                                <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                                Job Description *
                            </label>
                            <div id="cjm-desc-toolbar" class="flex items-center gap-1 border border-gray-300 dark:border-gray-600 border-b-0 rounded-t-lg px-3 py-2 bg-gray-50 dark:bg-gray-900">
                                <button type="button" onclick="document.execCommand('bold')" class="p-1.5 rounded hover:bg-gray-200 text-gray-600 dark:text-gray-400 font-bold text-sm">B</button>
                                <button type="button" onclick="document.execCommand('italic')" class="p-1.5 rounded hover:bg-gray-200 text-gray-600 dark:text-gray-400 italic text-sm">I</button>
                                <button type="button" onclick="document.execCommand('underline')" class="p-1.5 rounded hover:bg-gray-200 text-gray-600 dark:text-gray-400 underline text-sm">U</button>
                                <div class="w-px h-5 bg-gray-300 dark:bg-gray-600 mx-1"></div>
                                <button type="button" onclick="document.execCommand('justifyLeft')" class="p-1.5 rounded hover:bg-gray-200"><svg class="w-4 h-4 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 6h18M3 12h12M3 18h18"/></svg></button>
                                <button type="button" onclick="document.execCommand('justifyCenter')" class="p-1.5 rounded hover:bg-gray-200"><svg class="w-4 h-4 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 6h18M6 12h12M3 18h18"/></svg></button>
                                <button type="button" onclick="document.execCommand('justifyRight')" class="p-1.5 rounded hover:bg-gray-200"><svg class="w-4 h-4 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 6h18M9 12h12M3 18h18"/></svg></button>
                                <div class="w-px h-5 bg-gray-300 dark:bg-gray-600 mx-1"></div>
                                <button type="button" onclick="document.execCommand('insertUnorderedList')" class="p-1.5 rounded hover:bg-gray-200"><svg class="w-4 h-4 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01"/></svg></button>
                                <button type="button" onclick="document.execCommand('insertOrderedList')" class="p-1.5 rounded hover:bg-gray-200"><svg class="w-4 h-4 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M10 6h11M10 12h11M10 18h11M3 6l2 0M3 12l2 0M3 18l2 0"/></svg></button>
                    </div>
                            <textarea id="modal_description" name="description" required rows="8" class="w-full border border-gray-300 dark:border-gray-600 rounded-b-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Provide a detailed description of the role, responsibilities, and what makes this position great..."></textarea>
                            <p id="error-description" class="mt-1 text-sm text-red-600 hidden"></p>
                        </div>
                    <div>
                            <label class="flex items-center gap-1.5 text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                                <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 005.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 009.568 3z"/><path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6z"/></svg>
                                Required Skills
                            </label>
                            <div class="border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2.5 focus-within:ring-2 focus-within:ring-blue-500 focus-within:border-transparent bg-white dark:bg-gray-800">
                                <div id="cjm-skills-list" class="flex flex-wrap gap-2 mb-0"></div>
                                <input type="text" id="cjm-skill-input" class="w-full border-0 outline-none p-0 text-sm mt-1 focus:ring-0" placeholder="Type a skill and press Enter...">
                            </div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Press Enter to add a skill, Backspace to remove the last one</p>
                            <input type="hidden" id="modal_requirements" name="requirements" value="">
                    </div>
                </div>
            </div>

                <!-- STEP 3: Review & Publish -->
                <div id="cjm-step-3" class="hidden">
                    <h4 class="text-lg font-bold text-gray-900 dark:text-white mb-5">Review & Publish</h4>
                    <div id="cjm-review-summary" class="border border-gray-200 dark:border-gray-700 rounded-lg p-5 bg-white dark:bg-gray-800">
                        <div class="flex items-center gap-2 mb-4">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z"/></svg>
                            <span class="font-semibold text-gray-900 dark:text-white">Job Summary</span>
                </div>
                        <div id="cjm-review-content" class="space-y-2 text-sm"></div>
                    </div>
                    <input type="hidden" name="status" value="published">
                </div>
            </form>
            </div>

        <!-- Footer -->
        <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 flex items-center justify-between flex-shrink-0 bg-white dark:bg-gray-800">
            <button type="button" id="cjm-btn-prev" onclick="cjmPrevStep()" class="px-5 py-2.5 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition font-medium text-sm hidden">
                Previous
            </button>
            <button type="button" id="cjm-btn-cancel" onclick="closeCreateJobModal()" class="px-5 py-2.5 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition font-medium text-sm">
                    Cancel
                </button>
            <div class="flex-1"></div>
            <button type="button" id="cjm-btn-next" onclick="cjmNextStep()" class="px-6 py-2.5 bg-gradient-to-r from-blue-500 to-cyan-400 text-white rounded-lg hover:from-blue-600 hover:to-cyan-500 shadow-md transition font-medium text-sm">
                Next Step
            </button>
            <button type="button" id="cjm-btn-submit" onclick="cjmSubmit()" class="hidden px-6 py-2.5 bg-gradient-to-r from-blue-500 to-cyan-400 text-white rounded-lg hover:from-blue-600 hover:to-cyan-500 shadow-md transition font-medium text-sm flex items-center gap-2">
                <span>Create Campaign</span>
                <svg id="create-job-loading" class="w-5 h-5 animate-spin hidden" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                </button>
            </div>
    </div>
</div>

<!-- Edit Job Modal (3-step wizard) -->
<div id="edit-job-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4" style="background:rgba(0,0,0,0.4);backdrop-filter:blur(2px);">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl w-full max-w-5xl max-h-[90vh] flex flex-col overflow-hidden">
        <!-- Header -->
        <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-700 flex-shrink-0" style="background:linear-gradient(180deg,#f0f4ff 0%,#fff 100%);">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">Edit Job Posting</h3>
                    <p id="ejm-step-label" class="text-sm text-blue-600 mt-0.5">Step 1 of 3</p>
                </div>
                <button onclick="closeEditJobModal()" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        </div>
        
        <!-- Body -->
        <div class="flex-1 overflow-y-auto p-6 bg-white dark:bg-gray-800">
            <form id="edit-job-form">
            @csrf
            @method('PUT')
            <input type="hidden" id="edit-job-id" name="job_id">
            
                <!-- Step Indicator -->
                <div class="flex items-center justify-center mb-8">
                    <div id="ejm-ind-1" class="w-9 h-9 rounded-full bg-blue-600 text-white flex items-center justify-center text-sm font-bold">1</div>
                    <div id="ejm-line-1" class="w-16 h-0.5 bg-gray-300 dark:bg-gray-600"></div>
                    <div id="ejm-ind-2" class="w-9 h-9 rounded-full bg-white dark:bg-gray-800 border-2 border-gray-300 dark:border-gray-600 text-gray-400 flex items-center justify-center text-sm font-bold">2</div>
                    <div id="ejm-line-2" class="w-16 h-0.5 bg-gray-300 dark:bg-gray-600"></div>
                    <div id="ejm-ind-3" class="w-9 h-9 rounded-full bg-white dark:bg-gray-800 border-2 border-gray-300 dark:border-gray-600 text-gray-400 flex items-center justify-center text-sm font-bold">3</div>
                </div>

                <!-- STEP 1: Basic Information -->
                <div id="ejm-step-1">
                    <h4 class="text-lg font-bold text-gray-900 dark:text-white mb-5">Basic Information</h4>
                    <div class="space-y-5">
                    <div>
                            <label class="flex items-center gap-1.5 text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                                <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21"/></svg>
                                Job Title *
                            </label>
                            <input type="text" id="edit_title" name="title" required class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="e.g., Senior Software Engineer">
                        <p id="error-edit-title" class="mt-1 text-sm text-red-600 hidden"></p>
                    </div>
                    <div>
                            <label class="flex items-center gap-1.5 text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                                <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 005.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 009.568 3z"/><path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6z"/></svg>
                                Category *
                            </label>
                            <select id="edit_category_id" name="category_id" class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">Select a category</option>
                            @foreach($categories ?? [] as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                        <div class="grid grid-cols-2 gap-4">
                    <div>
                                <label class="flex items-center gap-1.5 text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                                    <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/></svg>
                                    Island *
                                </label>
                                <select id="edit_island" name="island" class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="">Select an island</option>
                                    <option value="Mahe">MahÃ©</option>
                                    <option value="Praslin">Praslin</option>
                                    <option value="La Digue">La Digue</option>
                                    <option value="Silhouette">Silhouette</option>
                                    <option value="Other">Other</option>
                                </select>
                    </div>
                    <div>
                                <label class="flex items-center gap-1.5 text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                                    <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/></svg>
                                    District *
                                </label>
                                <select id="edit_district" name="district" class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="">Select a district</option>
                                    <option value="Victoria">Victoria</option>
                                    <option value="Anse Royale">Anse Royale</option>
                                    <option value="Beau Vallon">Beau Vallon</option>
                                    <option value="Glacis">Glacis</option>
                                    <option value="Grand Anse Mahe">Grand Anse MahÃ©</option>
                                    <option value="Grand Anse Praslin">Grand Anse Praslin</option>
                                    <option value="Baie Lazare">Baie Lazare</option>
                                    <option value="Takamaka">Takamaka</option>
                                    <option value="Port Glaud">Port Glaud</option>
                                    <option value="Other">Other</option>
                                </select>
                    </div>
                    </div>
                        <div class="grid grid-cols-3 gap-4">
                    <div>
                                <label class="flex items-center gap-1.5 text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                                    <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    Job Type *
                                </label>
                                <select id="edit_employment_type" name="employment_type" class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="full_time">Full Time</option>
                            <option value="part_time">Part Time</option>
                            <option value="contract">Contract</option>
                                    <option value="temporary">Temporary</option>
                        </select>
                    </div>
                    <div>
                                <label class="flex items-center gap-1.5 text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                                    <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9.004 9.004 0 008.716-6.747M12 21a9.004 9.004 0 01-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 017.843 4.582M12 3a8.997 8.997 0 00-7.843 4.582m15.686 0A11.953 11.953 0 0112 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0121 12c0 .778-.099 1.533-.284 2.253m0 0A17.919 17.919 0 0112 16.5c-3.162 0-6.133-.815-8.716-2.247m0 0A9.015 9.015 0 013 12c0-1.605.42-3.113 1.157-4.418"/></svg>
                                    Work Environment *
                                </label>
                                <select id="edit_work_environment" name="work_environment" class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="on_site">On-site</option>
                                    <option value="remote">Remote</option>
                                    <option value="hybrid">Hybrid</option>
                        </select>
                    </div>
                    <div>
                                <label class="flex items-center gap-1.5 text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                                    <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.342M6.75 15a.75.75 0 100-1.5.75.75 0 000 1.5zm0 0v-3.675A55.378 55.378 0 0112 8.443m-7.007 11.55A5.981 5.981 0 006.75 15.75v-1.5"/></svg>
                                    Education Level
                                </label>
                                <select id="edit_education_level" name="education_level" class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="">Not specified</option>
                                    <option value="high_school">High School</option>
                                    <option value="diploma">Diploma</option>
                                    <option value="bachelors">Bachelor's Degree</option>
                                    <option value="masters">Master's Degree</option>
                                    <option value="phd">PhD</option>
                                </select>
                    </div>
                        </div>
                    <div>
                            <label class="flex items-center gap-1.5 text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                                <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Salary Range (Optional)
                            </label>
                            <div id="ejm-salary-slider-wrapper">
                                <div class="relative h-6 mt-2">
                                    <div class="absolute top-1/2 left-0 right-0 h-1 bg-gray-200 dark:bg-gray-700 rounded-full -translate-y-1/2"></div>
                                    <div id="ejm-range-fill" class="absolute top-1/2 h-1 bg-blue-500 rounded-full -translate-y-1/2" style="left:0%;right:0%"></div>
                                    <input type="range" id="ejm-range-min" min="0" max="100000" step="1000" value="0" class="absolute top-0 left-0 w-full h-6 appearance-none bg-transparent pointer-events-none [&::-webkit-slider-thumb]:pointer-events-auto [&::-webkit-slider-thumb]:appearance-none [&::-webkit-slider-thumb]:w-4 [&::-webkit-slider-thumb]:h-4 [&::-webkit-slider-thumb]:bg-blue-600 [&::-webkit-slider-thumb]:rounded-full [&::-webkit-slider-thumb]:shadow [&::-webkit-slider-thumb]:cursor-pointer [&::-moz-range-thumb]:pointer-events-auto [&::-moz-range-thumb]:w-4 [&::-moz-range-thumb]:h-4 [&::-moz-range-thumb]:bg-blue-600 [&::-moz-range-thumb]:rounded-full [&::-moz-range-thumb]:border-0 [&::-moz-range-thumb]:cursor-pointer">
                                    <input type="range" id="ejm-range-max" min="0" max="100000" step="1000" value="100000" class="absolute top-0 left-0 w-full h-6 appearance-none bg-transparent pointer-events-none [&::-webkit-slider-thumb]:pointer-events-auto [&::-webkit-slider-thumb]:appearance-none [&::-webkit-slider-thumb]:w-4 [&::-webkit-slider-thumb]:h-4 [&::-webkit-slider-thumb]:bg-blue-600 [&::-webkit-slider-thumb]:rounded-full [&::-webkit-slider-thumb]:shadow [&::-webkit-slider-thumb]:cursor-pointer [&::-moz-range-thumb]:pointer-events-auto [&::-moz-range-thumb]:w-4 [&::-moz-range-thumb]:h-4 [&::-moz-range-thumb]:bg-blue-600 [&::-moz-range-thumb]:rounded-full [&::-moz-range-thumb]:border-0 [&::-moz-range-thumb]:cursor-pointer">
                        </div>
                                <div class="flex items-center justify-between text-xs text-gray-500 dark:text-gray-400 mt-1">
                                    <span>0 SCR</span>
                                    <span>100k SCR</span>
                    </div>
                                <div class="flex items-center justify-center gap-2 mt-3">
                                    <span class="px-3 py-1 bg-emerald-50 text-emerald-700 rounded-md text-sm font-medium border border-emerald-200" id="ejm-sal-min">0 SCR</span>
                                    <span class="text-gray-400">-</span>
                                    <span class="px-3 py-1 bg-emerald-50 text-emerald-700 rounded-md text-sm font-medium border border-emerald-200" id="ejm-sal-max">100,000 SCR</span>
                                </div>
                            </div>
                            <div class="flex items-center mt-3">
                                <input type="checkbox" id="edit_hide_salary" name="hide_salary" value="1" class="w-4 h-4 text-blue-600 border-gray-300 dark:border-gray-600 rounded focus:ring-blue-500" onchange="ejmToggleHideSalary(this.checked)">
                                <label for="edit_hide_salary" class="ml-2 text-sm text-gray-600 dark:text-gray-400">Don't show salary (display as "Negotiable")</label>
                            </div>
                            <input type="hidden" id="edit_salary_min" name="salary_min" value="0">
                            <input type="hidden" id="edit_salary_max" name="salary_max" value="100000">
                        </div>
                    </div>
                    </div>

                <!-- STEP 2: Job Details -->
                <div id="ejm-step-2" class="hidden">
                    <h4 class="text-lg font-bold text-gray-900 dark:text-white mb-5">Job Details</h4>
                    <div class="space-y-5">
                    <div>
                            <label class="flex items-center gap-1.5 text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                                <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                                Job Description *
                            </label>
                            <textarea id="edit_description" name="description" required rows="8" class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Provide a detailed description of the role, responsibilities, and what makes this position great..."></textarea>
                            <p id="error-edit-description" class="mt-1 text-sm text-red-600 hidden"></p>
                    </div>
                    <div>
                            <label class="flex items-center gap-1.5 text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                                <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 005.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 009.568 3z"/><path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6z"/></svg>
                                Required Skills
                            </label>
                            <div class="border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2.5 focus-within:ring-2 focus-within:ring-blue-500 focus-within:border-transparent bg-white dark:bg-gray-800">
                                <div id="ejm-skills-list" class="flex flex-wrap gap-2 mb-0"></div>
                                <input type="text" id="ejm-skill-input" class="w-full border-0 outline-none p-0 text-sm mt-1 focus:ring-0" placeholder="Type a skill and press Enter...">
                    </div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Press Enter to add a skill, Backspace to remove the last one</p>
                            <input type="hidden" id="edit_requirements" name="requirements" value="">
                        </div>
                    <div>
                            <label class="flex items-center gap-1.5 text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                                <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 11.25v8.25a1.5 1.5 0 01-1.5 1.5H5.25a1.5 1.5 0 01-1.5-1.5v-8.25M12 4.875A2.625 2.625 0 109.375 7.5H12m0-2.625V7.5m0-2.625A2.625 2.625 0 1114.625 7.5H12m0 0V21m-8.625-9.75h18c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125h-18c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z"/></svg>
                                Benefits
                            </label>
                            <textarea id="edit_benefits" name="benefits" rows="4" class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="List the benefits and perks..."></textarea>
                    </div>
                </div>
            </div>

                <!-- STEP 3: Review & Publish -->
                <div id="ejm-step-3" class="hidden">
                    <h4 class="text-lg font-bold text-gray-900 dark:text-white mb-5">Review & Publish</h4>
                    <div id="ejm-review-summary" class="border border-gray-200 dark:border-gray-700 rounded-lg p-5 bg-white dark:bg-gray-800 mb-5">
                        <div class="flex items-center gap-2 mb-4">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z"/></svg>
                            <span class="font-semibold text-gray-900 dark:text-white">Job Summary</span>
                        </div>
                        <div id="ejm-review-content" class="space-y-2 text-sm"></div>
                    </div>
                <div>
                        <label class="flex items-center gap-1.5 text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Status</label>
                        <select id="edit_status" name="status" class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="draft">Draft</option>
                        <option value="published">Published</option>
                        <option value="closed">Closed</option>
                        <option value="archived">Archived</option>
                    </select>
                </div>
                </div>
            </form>
            </div>

        <!-- Footer -->
        <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 flex items-center justify-between flex-shrink-0 bg-white dark:bg-gray-800">
            <button type="button" id="ejm-btn-prev" onclick="ejmPrevStep()" class="px-5 py-2.5 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition font-medium text-sm hidden">
                Previous
            </button>
            <button type="button" id="ejm-btn-cancel" onclick="closeEditJobModal()" class="px-5 py-2.5 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition font-medium text-sm">
                    Cancel
                </button>
            <div class="flex-1"></div>
            <button type="button" id="ejm-btn-next" onclick="ejmNextStep()" class="px-6 py-2.5 bg-gradient-to-r from-blue-500 to-cyan-400 text-white rounded-lg hover:from-blue-600 hover:to-cyan-500 shadow-md transition font-medium text-sm">
                Next Step
            </button>
            <button type="button" id="ejm-btn-submit" onclick="ejmSubmit()" class="hidden px-6 py-2.5 bg-gradient-to-r from-blue-500 to-cyan-400 text-white rounded-lg hover:from-blue-600 hover:to-cyan-500 shadow-md transition font-medium text-sm flex items-center gap-2">
                <span>Update Job</span>
                <svg id="edit-job-loading" class="w-5 h-5 animate-spin hidden" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                </button>
            </div>
    </div>
</div>

<!-- Show Job Modal -->
<div id="show-job-modal" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl max-w-4xl w-full max-h-[90vh] overflow-y-auto">
        <div class="p-6 border-b border-gray-200 dark:border-gray-700 sticky top-0 bg-white dark:bg-gray-800 z-10">
            <div class="flex items-center justify-between">
                <h3 id="show-job-title" class="text-2xl font-bold text-gray-900 dark:text-white"></h3>
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
// Edit Job Modal - 3-step wizard
let ejmStep = 1;
let ejmSkills = [];

async function openEditJobModal(jobId) {
    const modal = document.getElementById('edit-job-modal');
    const form = document.getElementById('edit-job-form');
    ejmStep = 1;
    ejmSkills = [];
    
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
    ejmRenderStep();
    
    form.style.opacity = '0.5';
    form.style.pointerEvents = 'none';
    
    try {
        const response = await fetch(`/employer/jobs/${jobId}/edit`, {
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
        });
        const data = await response.json();
        if (response.ok) {
            const job = data.job;
            document.getElementById('edit-job-id').value = job.id;
            document.getElementById('edit_title').value = job.title || '';
            document.getElementById('edit_category_id').value = job.category_id || '';
            document.getElementById('edit_description').value = job.description || '';
            document.getElementById('edit_benefits').value = job.benefits || '';
            document.getElementById('edit_employment_type').value = job.employment_type || '';
            document.getElementById('edit_island').value = job.island || '';
            document.getElementById('edit_district').value = job.district || '';
            document.getElementById('edit_work_environment').value = job.work_environment || '';
            document.getElementById('edit_education_level').value = job.education_level || '';
            document.getElementById('edit_status').value = job.status || 'draft';
            
            // Salary slider
            const salMin = parseInt(job.salary_min) || 0;
            const salMax = parseInt(job.salary_max) || 100000;
            document.getElementById('ejm-range-min').value = salMin;
            document.getElementById('ejm-range-max').value = salMax;
            document.getElementById('edit_salary_min').value = salMin;
            document.getElementById('edit_salary_max').value = salMax;
            document.getElementById('ejm-sal-min').textContent = salMin.toLocaleString() + ' SCR';
            document.getElementById('ejm-sal-max').textContent = salMax.toLocaleString() + ' SCR';
            const pctMin = (salMin / 100000) * 100;
            const pctMax = ((100000 - salMax) / 100000) * 100;
            document.getElementById('ejm-range-fill').style.left = pctMin + '%';
            document.getElementById('ejm-range-fill').style.right = pctMax + '%';

            // Hide salary checkbox
            const hideSal = job.hide_salary || false;
            document.getElementById('edit_hide_salary').checked = hideSal;
            ejmToggleHideSalary(hideSal);

            // Skills from requirements
            if (job.requirements) {
                ejmSkills = job.requirements.split(',').map(s => s.trim()).filter(Boolean);
        } else {
                ejmSkills = [];
            }
            ejmRenderSkills();
            document.getElementById('edit_requirements').value = job.requirements || '';

            document.querySelectorAll('[id^="error-edit-"]').forEach(el => { el.classList.add('hidden'); el.textContent = ''; });
        } else {
            if (typeof window.showErrorToast === 'function') window.showErrorToast('Failed to load job data');
            closeEditJobModal();
        }
    } catch (error) {
        console.error('Error loading job:', error);
        if (typeof window.showErrorToast === 'function') window.showErrorToast('An error occurred while loading the job');
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
    ejmSkills = [];
    const sl = document.getElementById('ejm-skills-list');
    if (sl) sl.innerHTML = '';
    document.querySelectorAll('[id^="error-edit-"]').forEach(el => { el.classList.add('hidden'); el.textContent = ''; });
}

function ejmRenderStep() {
    document.getElementById('ejm-step-1').classList.toggle('hidden', ejmStep !== 1);
    document.getElementById('ejm-step-2').classList.toggle('hidden', ejmStep !== 2);
    document.getElementById('ejm-step-3').classList.toggle('hidden', ejmStep !== 3);
    document.getElementById('ejm-step-label').textContent = `Step ${ejmStep} of 3`;
    for (let i = 1; i <= 3; i++) {
        const ind = document.getElementById('ejm-ind-' + i);
        ind.className = i <= ejmStep
            ? 'w-9 h-9 rounded-full bg-blue-600 text-white flex items-center justify-center text-sm font-bold'
            : 'w-9 h-9 rounded-full bg-white border-2 border-gray-300 text-gray-400 flex items-center justify-center text-sm font-bold';
    }
    for (let i = 1; i <= 2; i++) {
        document.getElementById('ejm-line-' + i).className = i < ejmStep ? 'w-16 h-0.5 bg-blue-600' : 'w-16 h-0.5 bg-gray-300';
    }
    document.getElementById('ejm-btn-prev').classList.toggle('hidden', ejmStep === 1);
    document.getElementById('ejm-btn-cancel').classList.toggle('hidden', ejmStep !== 1);
    document.getElementById('ejm-btn-next').classList.toggle('hidden', ejmStep === 3);
    document.getElementById('ejm-btn-submit').classList.toggle('hidden', ejmStep !== 3);
    if (ejmStep === 3) ejmBuildReview();
}

function ejmNextStep() {
    if (ejmStep === 1) { if (!document.getElementById('edit_title').value.trim()) { document.getElementById('edit_title').focus(); return; } }
    if (ejmStep === 2) { if (!document.getElementById('edit_description').value.trim()) { document.getElementById('edit_description').focus(); return; } }
    if (ejmStep < 3) { ejmStep++; ejmRenderStep(); }
}
function ejmPrevStep() { if (ejmStep > 1) { ejmStep--; ejmRenderStep(); } }

function ejmBuildReview() {
    const v = k => { const e = document.getElementById(k); return e ? (e.options ? (e.selectedOptions[0]?.text || e.value) : e.value) : ''; };
    const rows = [
        ['Position', v('edit_title') || 'Not specified'],
        ['Category', v('edit_category_id') || 'Not specified'],
        ['Location', `${v('edit_island') || ''} ${v('edit_district') ? '- ' + v('edit_district') : ''}`.trim() || 'Not specified'],
        ['Type', `${v('edit_employment_type')} - ${v('edit_work_environment')}`],
        ['Education', v('edit_education_level') || 'Not specified'],
        ['Salary', document.getElementById('edit_hide_salary')?.checked ? 'Negotiable' : `${parseInt(document.getElementById('edit_salary_min')?.value || 0).toLocaleString()} SCR - ${parseInt(document.getElementById('edit_salary_max')?.value || 100000).toLocaleString()} SCR`],
        ['Status', v('edit_status')],
    ];
    document.getElementById('ejm-review-content').innerHTML = rows.map(([l, r]) =>
        `<div class="flex items-center justify-between py-1.5"><span class="text-gray-500 dark:text-gray-400">${l}:</span><span class="font-medium text-gray-900 dark:text-white text-right">${r}</span></div>`
    ).join('');
}

function ejmToggleHideSalary(checked) {
    const wrapper = document.getElementById('ejm-salary-slider-wrapper');
    if (wrapper) { wrapper.style.opacity = checked ? '0.3' : '1'; wrapper.style.pointerEvents = checked ? 'none' : 'auto'; }
}

function ejmRenderSkills() {
    const list = document.getElementById('ejm-skills-list');
    const hidden = document.getElementById('edit_requirements');
    list.innerHTML = ejmSkills.map((s, i) =>
        `<span class="inline-flex items-center gap-1 px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm font-medium">${s}<button type="button" onclick="ejmRemoveSkill(${i})" class="text-blue-500 hover:text-blue-700 ml-0.5">&times;</button></span>`
    ).join('');
    hidden.value = ejmSkills.join(', ');
}
function ejmRemoveSkill(i) { ejmSkills.splice(i, 1); ejmRenderSkills(); }

// Edit skills tag input
document.addEventListener('DOMContentLoaded', function() {
    const skillInput = document.getElementById('ejm-skill-input');
    if (skillInput) {
        skillInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') { e.preventDefault(); const val = this.value.trim(); if (val && !ejmSkills.includes(val)) { ejmSkills.push(val); ejmRenderSkills(); } this.value = ''; }
            if (e.key === 'Backspace' && !this.value && ejmSkills.length) { ejmSkills.pop(); ejmRenderSkills(); }
        });
    }
    // Edit salary dual range
    const erjMin = document.getElementById('ejm-range-min');
    const erjMax = document.getElementById('ejm-range-max');
    if (erjMin && erjMax) {
        function updateEditSalaryRange() {
            let minVal = parseInt(erjMin.value), maxVal = parseInt(erjMax.value);
            if (minVal > maxVal) { [minVal, maxVal] = [maxVal, minVal]; erjMin.value = minVal; erjMax.value = maxVal; }
            document.getElementById('edit_salary_min').value = minVal;
            document.getElementById('edit_salary_max').value = maxVal;
            document.getElementById('ejm-sal-min').textContent = minVal.toLocaleString() + ' SCR';
            document.getElementById('ejm-sal-max').textContent = maxVal.toLocaleString() + ' SCR';
            document.getElementById('ejm-range-fill').style.left = (minVal / 100000 * 100) + '%';
            document.getElementById('ejm-range-fill').style.right = ((100000 - maxVal) / 100000 * 100) + '%';
        }
        erjMin.addEventListener('input', updateEditSalaryRange);
        erjMax.addEventListener('input', updateEditSalaryRange);
    }
});

async function ejmSubmit() {
    const form = document.getElementById('edit-job-form');
    const jobId = document.getElementById('edit-job-id').value;
    const submitBtn = document.getElementById('ejm-btn-submit');
    const loadingIcon = document.getElementById('edit-job-loading');
    const formData = new FormData(form);

    const island = formData.get('island') || '';
    const district = formData.get('district') || '';
    if (island || district) formData.set('location', [island, district].filter(Boolean).join(', '));
    const we = formData.get('work_environment');
    if (we === 'remote') formData.set('is_remote', '1');

    submitBtn.disabled = true;
    loadingIcon.classList.remove('hidden');
    document.querySelectorAll('[id^="error-edit-"]').forEach(el => { el.classList.add('hidden'); el.textContent = ''; });

    try {
        const response = await fetch(`/employer/jobs/${jobId}`, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' },
            body: formData
        });
        const data = await response.json();
        if (response.ok) {
            closeEditJobModal();
            if (typeof window.showSuccessToast === 'function') window.showSuccessToast('Job posting updated successfully!');
            updateJobRowInTable(data.job);
        } else {
            if (data.errors) {
                if (data.errors.title || data.errors.category_id || data.errors.island || data.errors.district) { ejmStep = 1; ejmRenderStep(); }
                else if (data.errors.description) { ejmStep = 2; ejmRenderStep(); }
                Object.keys(data.errors).forEach(field => {
                    const errorEl = document.getElementById(`error-edit-${field}`);
                    if (errorEl) { errorEl.textContent = data.errors[field][0]; errorEl.classList.remove('hidden'); }
                });
            } else {
                if (typeof window.showErrorToast === 'function') window.showErrorToast(data.message || 'Failed to update job posting');
            }
        }
    } catch (error) {
        if (typeof window.showErrorToast === 'function') window.showErrorToast('An error occurred while updating the job posting');
    } finally {
        submitBtn.disabled = false;
        loadingIcon.classList.add('hidden');
    }
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
                            <p class="text-gray-600 dark:text-gray-400">${job.company?.name || 'Unknown Company'}</p>
                        </div>
                        <span class="px-3 py-1 rounded-full text-sm font-medium ${statusClass}">
                            ${job.status.charAt(0).toUpperCase() + job.status.slice(1)}
                        </span>
                    </div>
                </div>

                <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-6 mb-6">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Job Details</h2>
                    <div class="grid grid-cols-2 gap-4 mb-6">
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Location</p>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">${job.is_remote ? 'Remote' : (job.location || 'Not specified')}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Employment Type</p>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">${job.employment_type ? job.employment_type.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase()) : 'Not specified'}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Experience Level</p>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">${job.experience_level ? job.experience_level.charAt(0).toUpperCase() + job.experience_level.slice(1) : 'Not specified'}</p>
                        </div>
                        ${salaryDisplay ? `
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Salary</p>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">${salaryDisplay}</p>
                        </div>
                        ` : ''}
                        
                    </div>

                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Description</h3>
                        <p class="text-gray-700 dark:text-gray-300 whitespace-pre-wrap">${job.description || 'No description provided'}</p>
                    </div>

                    ${job.requirements ? `
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Requirements</h3>
                        <p class="text-gray-700 dark:text-gray-300 whitespace-pre-wrap">${job.requirements}</p>
                    </div>
                    ` : ''}

                    ${job.benefits ? `
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Benefits</h3>
                        <p class="text-gray-700 dark:text-gray-300 whitespace-pre-wrap">${job.benefits}</p>
                    </div>
                    ` : ''}
                </div>

                <div class="grid grid-cols-3 gap-4 mb-6">
                    <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-6">
                        <div class="text-3xl font-bold text-gray-900 dark:text-white mb-1">${(job.views_count || 0).toLocaleString()}</div>
                        <div class="text-sm text-gray-500 dark:text-gray-400">Total Views</div>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-6">
                        <div class="text-3xl font-bold text-gray-900 dark:text-white mb-1">${job.applications_count || 0}</div>
                        <div class="text-sm text-gray-500 dark:text-gray-400">Applications</div>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-6">
                        <div class="text-sm font-medium text-gray-900 dark:text-white mb-1">Posted</div>
                        <div class="text-sm text-gray-500 dark:text-gray-400">${new Date(job.created_at).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })}</div>
                    </div>
                </div>

                <div class="flex items-center justify-end space-x-3 pt-6 border-t border-gray-200 dark:border-gray-700">
                    <button onclick="openEditJobModal(${job.id}); closeShowJobModal();" class="px-4 py-2 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 rounded-lg border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 transition font-medium">
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
    
    // Close modals on outside click
document.addEventListener('DOMContentLoaded', function() {
    ['edit-job-modal', 'show-job-modal'].forEach(modalId => {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.addEventListener('click', function(e) {
                if (e.target === this) {
                    if (modalId === 'edit-job-modal') closeEditJobModal();
                    else closeShowJobModal();
                }
            });
        }
    });
});

// Function to update job row in table (real-time after edit)
function updateJobRowInTable(job) {
    const row = document.querySelector('tr[data-job-id="' + job.id + '"]');
    if (!row) return;
    
    var hasPromoted = Array.isArray(job.campaigns) && job.campaigns.some(function(c) { return c && c.status === 'active'; });
    var salaryHtml = '<p class="text-sm text-gray-500 dark:text-gray-400">—</p>';
    if (job.hide_salary) {
        salaryHtml = '<p class="text-sm text-gray-500 dark:text-gray-400">Negotiable</p>';
    } else if (job.salary_min || job.salary_max) {
        var min = job.salary_min != null ? (job.salary_min >= 1000 ? '$' + Math.round(job.salary_min / 1000) + 'k' : '$' + job.salary_min) : '';
        var max = job.salary_max != null ? (job.salary_max >= 1000 ? '$' + Math.round(job.salary_max / 1000) + 'k' : '$' + job.salary_max) : '';
        if (min && max) salaryHtml = '<p class="text-sm text-gray-500 dark:text-gray-400">' + min + ' - ' + max + '</p>';
        else if (min) salaryHtml = '<p class="text-sm text-gray-500 dark:text-gray-400">' + min + '+</p>';
        else if (max) salaryHtml = '<p class="text-sm text-gray-500 dark:text-gray-400">Up to ' + max + '</p>';
    }
    var promotedBadge = hasPromoted ? '<span class="px-2 py-0.5 text-white text-xs font-medium rounded" style="background: linear-gradient(to right, #f59e0b, #f97316);">Promoted</span>' : '';
    var esc = function(s) { return (s || '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;'); };

    var titleCell = row.querySelector('td:first-child');
    if (titleCell) {
        titleCell.innerHTML = '<div class="flex items-center gap-3">' +
            '<div class="w-10 h-10 rounded flex items-center justify-center flex-shrink-0" style="background: linear-gradient(to bottom right, #2563eb, #06b6d4);">' +
            '<svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg></div>' +
            '<div><div class="flex items-center gap-2 flex-wrap"><p class="font-semibold text-gray-900 dark:text-white">' + esc(job.title) + '</p>' + promotedBadge + '</div>' + salaryHtml + '</div></div>';
    }

    var locationCell = row.querySelector('td:nth-child(2)');
    if (locationCell) {
        var loc = job.is_remote ? 'Remote' : (job.location || 'Not specified');
        locationCell.className = 'px-6 py-4 text-sm text-gray-600 dark:text-gray-400';
        locationCell.textContent = loc;
    }
    
    var typeCell = row.querySelector('td:nth-child(3)');
    if (typeCell) {
        var typeStr = job.employment_type ? job.employment_type.replace(/_/g, '-').replace(/\b\w/g, function(l) { return l.toUpperCase(); }) : 'Full-time';
        typeCell.className = 'px-6 py-4 text-sm text-gray-600 dark:text-gray-400';
        typeCell.textContent = typeStr;
    }

    row.setAttribute('data-job-title', (job.title || '').toLowerCase());
    row.setAttribute('data-job-location', (job.is_remote ? 'remote' : (job.location || '')).toLowerCase());
    var rowStatus = (job.status === 'draft' && job.published_at) ? 'paused' : (job.status || 'draft');
    row.setAttribute('data-status', rowStatus);

    var appsCell = row.querySelector('td:nth-child(5)');
    if (appsCell && job.applications_count != null) {
        var appsVal = appsCell.querySelector('.font-medium') || appsCell;
        if (appsCell.querySelector('.font-medium')) {
            // keep icon + number layout; replace trailing text nodes awkwardly — rewrite cell
            appsCell.innerHTML = '<div class="flex items-center gap-2 text-sm text-gray-900 dark:text-white font-medium"><svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>' + job.applications_count + '</div>';
        }
    }
    var viewsCell = row.querySelector('td:nth-child(6)');
    if (viewsCell && job.views_count != null) {
        viewsCell.innerHTML = '<div class="flex items-center gap-2 text-sm text-gray-900 dark:text-white font-medium"><svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>' + Number(job.views_count).toLocaleString() + '</div>';
    }

    updateJobStatusInTable(job.id, job.status);
}
</script>
@endsection
