@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Saved Jobs</h1>
        <p class="text-gray-600 dark:text-gray-400">Jobs you've saved for later</p>
    </div>

    <div id="saved-jobs-container" class="space-y-4">
        <div class="text-center py-12">
            <div class="spinner mx-auto mb-4"></div>
            <p class="text-gray-500 dark:text-gray-400">Loading saved jobs...</p>
        </div>
    </div>
</div>

@push('scripts')
<script>
    const API_BASE = '/api';
    
    async function loadSavedJobs() {
        const container = document.getElementById('saved-jobs-container');
        try {
            const { data } = await fetchWithLoading(`${API_BASE}/job-seeker/saved-jobs`, {}, container);
            
            if (data.data && data.data.length > 0) {
                container.innerHTML = data.data.map((saved, index) => {
                        const job = saved.job;
                        return `
                            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm dark:shadow-none hover:shadow-md transition-smooth p-6 border border-gray-100 dark:border-gray-700 fade-in" style="animation-delay: ${index * 0.05}s">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">
                                            <a href="/jobs/${job.id}" wire:navigate class="hover:text-blue-600">${job.title}</a>
                                        </h3>
                                        <div class="flex items-center text-sm text-gray-600 dark:text-gray-400 mb-2">
                                            <span class="font-medium">${job.company?.name || 'Company'}</span>
                                        </div>
                                        <div class="flex flex-wrap gap-4 text-sm text-gray-600 dark:text-gray-400 mb-4">
                                            ${job.location ? `<span class="flex items-center">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                </svg>
                                                ${job.location}
                                            </span>` : ''}
                                            ${job.salary_min || job.salary_max ? `<span class="flex items-center">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                ${job.salary_min && job.salary_max ? `${job.salary_min} - ${job.salary_max}` : job.salary_min || job.salary_max} ${job.currency || 'SCR'}
                                            </span>` : ''}
                                        </div>
                                        <p class="text-gray-600 dark:text-gray-400 text-sm line-clamp-2">${job.description?.substring(0, 200) || ''}...</p>
                                    </div>
                                    <div class="ml-4 flex flex-col space-y-2">
                                        <button onclick="handleApply(${job.id})" class="bg-pink-500 text-white px-6 py-2 rounded-lg font-semibold hover:bg-pink-600 transition whitespace-nowrap">
                                            Apply now
                                        </button>
                                        <button onclick="unsaveJob(${job.id})" class="border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 px-6 py-2 rounded-lg font-semibold hover:bg-gray-50 dark:hover:bg-gray-700 transition whitespace-nowrap">
                                            Remove
                                        </button>
                                    </div>
                                </div>
                            </div>
                        `;
                    }).join('');
                } else {
                    container.innerHTML = '<div class="text-center py-12 text-gray-500 dark:text-gray-400"><p class="text-lg mb-2">No saved jobs yet</p><p class="text-sm"><a href="/jobs" wire:navigate class="text-blue-600 hover:underline">Browse jobs</a> to save them!</p></div>';
                }
            } catch (error) {
                console.error('Error loading saved jobs:', error);
                container.innerHTML = '<div class="text-center py-12 text-red-500"><p class="text-lg mb-2">Error loading saved jobs</p><p class="text-sm">Please try again later</p></div>';
            }
    }
    
    function handleApply(jobId) {
        navigateTo(`/jobs/${jobId}/apply`);
    }
    
    async function unsaveJob(jobId) {
        const confirmed = await window.showConfirmDialog(
            'This job will be removed from your saved list.',
            { title: 'Remove saved job?', confirmText: 'Remove', cancelText: 'Cancel' }
        );
        if (!confirmed) return;
        
        fetch(`${API_BASE}/job-seeker/saved-jobs/${jobId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (response.ok) {
                loadSavedJobs();
            } else {
                showErrorToast(data.message || 'Failed to remove job');
            }
        })
        .catch(error => {
            console.error('Error unsaving job:', error);
            showErrorToast('An error occurred. Please try again.');
        });
    }
    
    document.addEventListener('DOMContentLoaded', loadSavedJobs);
</script>
@endpush
@endsection
