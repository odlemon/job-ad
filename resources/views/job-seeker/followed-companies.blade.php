@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Followed Companies</h1>
        <p class="text-gray-600">Companies you're following</p>
    </div>

    <div id="followed-companies-container" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div class="text-center py-12 text-gray-500 col-span-full">Loading followed companies...</div>
    </div>
</div>

@push('scripts')
<script>
    const API_BASE = '/api';
    
    function loadFollowedCompanies() {
        fetch(`${API_BASE}/job-seeker/followed-companies`)
            .then(response => response.json())
            .then(data => {
                const container = document.getElementById('followed-companies-container');
                if (data.data && data.data.length > 0) {
                    container.innerHTML = data.data.map(followed => {
                        const company = followed.company;
                        return `
                            <div class="bg-white rounded-lg shadow-sm hover:shadow-md transition p-6 border border-gray-100">
                                <div class="flex items-start justify-between mb-4">
                                    <div class="w-16 h-16 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                        ${company.logo ? 
                                            `<img src="${company.logo}" alt="${company.name}" class="w-full h-full object-cover rounded-lg">` :
                                            `<span class="text-2xl font-bold text-blue-600">${company.name.charAt(0).toUpperCase()}</span>`
                                        }
                                    </div>
                                    <button onclick="unfollowCompany(${company.id})" class="text-red-600 hover:text-red-700 text-sm font-medium">
                                        Unfollow
                                    </button>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">
                                    <a href="/companies/${company.slug || company.id}" class="hover:text-blue-600">${company.name}</a>
                                </h3>
                                ${company.description ? `<p class="text-sm text-gray-600 mb-4 line-clamp-2">${company.description.substring(0, 100)}...</p>` : ''}
                                <div class="flex items-center justify-between text-sm text-gray-600">
                                    <span>${company.job_advertisements_count || 0} Jobs</span>
                                    <a href="/jobs?company_id=${company.id}" class="text-blue-600 hover:text-blue-700 font-medium">
                                        View Jobs →
                                    </a>
                                </div>
                            </div>
                        `;
                    }).join('');
                } else {
                    container.innerHTML = '<div class="text-center py-12 text-gray-500 col-span-full">No followed companies yet. <a href="/jobs" wire:navigate class="text-blue-600 hover:underline">Browse companies</a> to follow them!</div>';
                }
            })
            .catch(error => {
                console.error('Error loading followed companies:', error);
                document.getElementById('followed-companies-container').innerHTML = '<div class="text-center py-12 text-red-500 col-span-full">Error loading followed companies. Please try again later.</div>';
            });
    }
    
    function unfollowCompany(companyId) {
        if (!confirm('Unfollow this company?')) return;
        
        fetch(`${API_BASE}/job-seeker/followed-companies/${companyId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (response.ok) {
                loadFollowedCompanies();
            } else {
                showErrorToast(data.message || 'Failed to unfollow company');
            }
        })
        .catch(error => {
            console.error('Error unfollowing company:', error);
            showErrorToast('An error occurred. Please try again.');
        });
    }
    
    document.addEventListener('DOMContentLoaded', loadFollowedCompanies);
</script>
@endpush
@endsection
