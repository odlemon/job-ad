@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">My Applications</h1>
        <p class="text-gray-600">Track your job applications</p>
    </div>

    <div id="applications-container" class="space-y-4">
        <div class="text-center py-12 text-gray-500">Loading applications...</div>
    </div>
</div>

@push('scripts')
<script>
    const API_BASE = '/api';
    
    function loadApplications() {
        fetch(`${API_BASE}/job-seeker/applications`)
            .then(response => response.json())
            .then(data => {
                const container = document.getElementById('applications-container');
                if (data.data && data.data.length > 0) {
                    container.innerHTML = data.data.map(app => `
                        <div class="bg-white rounded-lg shadow-sm hover:shadow-md transition p-6 border border-gray-100">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <h3 class="text-xl font-semibold text-gray-900 mb-2">
                                        <a href="/jobs/${app.job_advertisement_id}" class="hover:text-blue-600">
                                            ${app.job_advertisement?.title || 'Job Title'}
                                        </a>
                                    </h3>
                                    <div class="flex items-center text-sm text-gray-600 mb-2">
                                        <span class="font-medium">${app.job_advertisement?.company?.name || 'Company'}</span>
                                    </div>
                                    <div class="flex flex-wrap gap-4 text-sm text-gray-600 mb-4">
                                        ${app.job_advertisement?.location ? `<span class="flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            </svg>
                                            ${app.job_advertisement.location}
                                        </span>` : ''}
                                        <span class="flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                            Applied ${new Date(app.created_at).toLocaleDateString()}
                                        </span>
                                    </div>
                                    ${app.cover_letter ? `<p class="text-gray-600 text-sm line-clamp-2">${app.cover_letter.substring(0, 200)}...</p>` : ''}
                                </div>
                                <div class="ml-4 flex flex-col items-end space-y-2">
                                    <span class="px-3 py-1 text-xs rounded ${getStatusColor(app.status)}">${app.status}</span>
                                    ${app.status === 'pending' ? `
                                        <button onclick="withdrawApplication(${app.id})" class="text-red-600 hover:text-red-700 text-sm font-medium">
                                            Withdraw
                                        </button>
                                    ` : ''}
                                </div>
                            </div>
                        </div>
                    `).join('');
                } else {
                    container.innerHTML = '<div class="text-center py-12 text-gray-500">No applications yet. <a href="/jobs" wire:navigate class="text-blue-600 hover:underline">Start applying!</a></div>';
                }
            })
            .catch(error => {
                console.error('Error loading applications:', error);
                document.getElementById('applications-container').innerHTML = '<div class="text-center py-12 text-red-500">Error loading applications. Please try again later.</div>';
            });
    }
    
    function getStatusColor(status) {
        const colors = {
            'pending': 'bg-yellow-100 text-yellow-800',
            'reviewing': 'bg-blue-100 text-blue-800',
            'shortlisted': 'bg-green-100 text-green-800',
            'rejected': 'bg-red-100 text-red-800',
            'hired': 'bg-purple-100 text-purple-800'
        };
        return colors[status] || 'bg-gray-100 text-gray-800';
    }
    
    function withdrawApplication(id) {
        if (!confirm('Are you sure you want to withdraw this application?')) return;
        
        fetch(`${API_BASE}/job-seeker/applications/${id}/withdraw`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (response.ok) {
                loadApplications();
            } else {
                showErrorToast(data.message || 'Failed to withdraw application');
            }
        })
        .catch(error => {
            console.error('Error withdrawing application:', error);
            showErrorToast('An error occurred. Please try again.');
        });
    }
    
    document.addEventListener('DOMContentLoaded', loadApplications);
</script>
@endpush
@endsection
