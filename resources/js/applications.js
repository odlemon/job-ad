(function() {
    'use strict';

    const API_BASE = '/api';
    let applications = [];
    let currentPage = 1;
    let currentStatusFilter = '';
    let currentSort = 'newest';

    // ========== Helper Functions ==========
    function getCsrfToken() {
        const token = document.querySelector('meta[name="csrf-token"]');
        return token ? token.getAttribute('content') : '';
    }

    function showToast(message, type = 'info') {
        if (typeof window.showToast === 'function') {
            window.showToast(message, type);
        } else {
            alert(message);
        }
    }

    function showSuccessToast(message) {
        showToast(message, 'success');
    }

    function showErrorToast(message) {
        showToast(message, 'error');
    }

    // ========== Status Helpers ==========
    function getStatusBadge(status) {
        const badges = {
            'pending': { text: 'Pending', class: 'bg-yellow-100 text-yellow-800 border-yellow-200' },
            'reviewing': { text: 'Under Review', class: 'bg-blue-100 text-blue-800 border-blue-200' },
            'shortlisted': { text: 'Shortlisted', class: 'bg-green-100 text-green-800 border-green-200' },
            'rejected': { text: 'Rejected', class: 'bg-red-100 text-red-800 border-red-200' },
            'hired': { text: 'Hired', class: 'bg-purple-100 text-purple-800 border-purple-200' }
        };
        const badge = badges[status] || { text: status, class: 'bg-gray-100 text-gray-800 border-gray-200' };
        return `<span class="px-3 py-1 text-xs font-semibold rounded-full border ${badge.class}">${badge.text}</span>`;
    }

    function getStatusIcon(status) {
        const icons = {
            'pending': `<svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>`,
            'reviewing': `<svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
            </svg>`,
            'shortlisted': `<svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>`,
            'rejected': `<svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>`,
            'hired': `<svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>`
        };
        return icons[status] || '';
    }

    // ========== Load Applications ==========
    async function loadApplications(page = 1) {
        const container = document.getElementById('applications-container');
        const skeleton = document.getElementById('applications-skeleton');
        
        if (!container) return;

        // Show skeleton
        if (skeleton) skeleton.classList.remove('hidden');
        container.innerHTML = '';
        container.appendChild(skeleton);

        try {
            const statusFilter = document.getElementById('status-filter')?.value || '';
            const sortFilter = document.getElementById('sort-filter')?.value || 'newest';
            currentStatusFilter = statusFilter;
            currentSort = sortFilter;
            currentPage = page;

            let url = `${API_BASE}/job-seeker/applications?per_page=10&page=${page}`;
            if (statusFilter) {
                url += `&status=${statusFilter}`;
            }

            const response = await fetch(url, {
                credentials: 'include',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': getCsrfToken()
                }
            });

            if (response.status === 401 || response.status === 403) {
                window.location.href = '/';
                return;
            }

            if (!response.ok) {
                throw new Error('Failed to load applications');
            }

            const result = await response.json();
            
            // Hide skeleton
            if (skeleton) skeleton.classList.add('hidden');

            // Laravel paginator returns: { data: [...], meta: {...}, links: {...} }
            const apps = result.data || [];
            
            if (apps.length > 0) {
                applications = apps;
                
                // Apply sorting
                let sortedApplications = [...applications];
                if (sortFilter === 'oldest') {
                    sortedApplications.sort((a, b) => new Date(a.created_at) - new Date(b.created_at));
                } else if (sortFilter === 'status') {
                    const statusOrder = { 'pending': 1, 'reviewing': 2, 'shortlisted': 3, 'rejected': 4, 'hired': 5 };
                    sortedApplications.sort((a, b) => (statusOrder[a.status] || 99) - (statusOrder[b.status] || 99));
                } else {
                    sortedApplications.sort((a, b) => new Date(b.created_at) - new Date(a.created_at));
                }

                renderApplications(sortedApplications);
                updateStats(apps);
                renderPagination(result);
            } else {
                container.innerHTML = `
                    <div class="bg-white rounded-xl shadow-sm p-12 text-center">
                        <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">No applications found</h3>
                        <p class="text-gray-600 mb-6">${statusFilter ? 'Try adjusting your filters or' : ''} Start applying to jobs to see them here.</p>
                        <a href="/jobs" wire:navigate class="inline-flex items-center px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            Browse Jobs
                        </a>
                    </div>
                `;
                updateStats([]);
            }
        } catch (error) {
            console.error('Error loading applications:', error);
            if (skeleton) skeleton.classList.add('hidden');
            container.innerHTML = `
                <div class="bg-white rounded-xl shadow-sm p-12 text-center">
                    <svg class="w-16 h-16 text-red-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Error loading applications</h3>
                    <p class="text-gray-600 mb-6">Please try again later.</p>
                    <button onclick="loadApplications()" class="inline-flex items-center px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium">
                        Retry
                    </button>
                </div>
            `;
        }
    }

    // ========== Render Applications ==========
    function renderApplications(apps) {
        const container = document.getElementById('applications-container');
        if (!container) return;

        if (apps.length === 0) {
            container.innerHTML = `
                <div class="bg-white rounded-xl shadow-sm p-12 text-center">
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">No applications found</h3>
                    <p class="text-gray-600">Try adjusting your filters.</p>
                </div>
            `;
            return;
        }

        container.innerHTML = apps.map(app => {
            const job = app.job_advertisement || {};
            const company = job.company || {};
            const appliedDate = new Date(app.created_at).toLocaleDateString('en-US', { 
                month: 'short', 
                day: 'numeric', 
                year: 'numeric' 
            });
            const reviewedDate = app.reviewed_at ? new Date(app.reviewed_at).toLocaleDateString('en-US', { 
                month: 'short', 
                day: 'numeric', 
                year: 'numeric' 
            }) : null;

            return `
                <div class="bg-white rounded-xl shadow-sm hover:shadow-md transition p-6 border border-gray-100">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start gap-4 mb-4">
                                <div class="flex-shrink-0">
                                    ${company.logo ? `
                                        <img src="${company.logo}" alt="${company.name}" class="w-16 h-16 rounded-lg object-cover border border-gray-200">
                                    ` : `
                                        <div class="w-16 h-16 rounded-lg bg-gray-100 flex items-center justify-center border border-gray-200">
                                            <span class="text-2xl font-bold text-gray-400">${(company.name || 'C')[0].toUpperCase()}</span>
                                        </div>
                                    `}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h3 class="text-xl font-bold text-gray-900 mb-1 truncate">
                                        <a href="/jobs/${job.id}" wire:navigate class="hover:text-blue-600 transition">
                                            ${job.title || 'Job Title'}
                                        </a>
                                    </h3>
                                    <p class="text-lg text-gray-700 mb-2">${company.name || 'Company'}</p>
                                    <div class="flex flex-wrap items-center gap-4 text-sm text-gray-600">
                                        ${job.location ? `
                                            <span class="flex items-center">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                </svg>
                                                ${job.location}
                                            </span>
                                        ` : ''}
                                        ${job.job_type ? `
                                            <span class="flex items-center">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                ${job.job_type}
                                            </span>
                                        ` : ''}
                                        ${job.salary_range ? `
                                            <span class="flex items-center">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                ${job.salary_range}
                                            </span>
                                        ` : ''}
                                    </div>
                                </div>
                            </div>
                            
                            <div class="border-t border-gray-200 pt-4 mt-4">
                                <div class="flex flex-wrap items-center gap-4 text-sm text-gray-600 mb-3">
                                    <span class="flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                        Applied: ${appliedDate}
                                    </span>
                                    ${reviewedDate ? `
                                        <span class="flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            Reviewed: ${reviewedDate}
                                        </span>
                                    ` : ''}
                                </div>
                                ${app.cover_letter ? `
                                    <p class="text-sm text-gray-600 line-clamp-2">${app.cover_letter.substring(0, 150)}${app.cover_letter.length > 150 ? '...' : ''}</p>
                                ` : ''}
                            </div>
                        </div>
                        <div class="flex flex-col items-end gap-3 flex-shrink-0">
                            ${getStatusBadge(app.status)}
                            ${app.status === 'pending' ? `
                                <button onclick="withdrawApplication(${app.id})" class="text-red-600 hover:text-red-700 text-sm font-medium px-3 py-1 rounded hover:bg-red-50 transition">
                                    Withdraw
                                </button>
                            ` : ''}
                            <a href="/jobs/${job.id}" wire:navigate class="text-blue-600 hover:text-blue-700 text-sm font-medium px-3 py-1 rounded hover:bg-blue-50 transition">
                                View Job
                            </a>
                        </div>
                    </div>
                </div>
            `;
        }).join('');
    }

    // ========== Update Stats ==========
    function updateStats(apps) {
        const total = apps.length;
        const pending = apps.filter(a => a.status === 'pending').length;
        const shortlisted = apps.filter(a => a.status === 'shortlisted').length;
        const rejected = apps.filter(a => a.status === 'rejected').length;

        const totalEl = document.getElementById('stat-total');
        const pendingEl = document.getElementById('stat-pending');
        const shortlistedEl = document.getElementById('stat-shortlisted');
        const rejectedEl = document.getElementById('stat-rejected');

        if (totalEl) totalEl.textContent = total;
        if (pendingEl) pendingEl.textContent = pending;
        if (shortlistedEl) shortlistedEl.textContent = shortlisted;
        if (rejectedEl) rejectedEl.textContent = rejected;
    }

    // ========== Render Pagination ==========
    function renderPagination(data) {
        const container = document.getElementById('pagination-container');
        if (!container || !data.meta) return;

        const { current_page, last_page, per_page, total } = data.meta;
        if (last_page <= 1) {
            container.innerHTML = '';
            return;
        }

        let paginationHTML = '<div class="flex items-center space-x-2">';
        
        // Previous button
        if (current_page > 1) {
            paginationHTML += `
                <button onclick="loadApplications(${current_page - 1})" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                    Previous
                </button>
            `;
        }

        // Page numbers
        for (let i = 1; i <= last_page; i++) {
            if (i === 1 || i === last_page || (i >= current_page - 2 && i <= current_page + 2)) {
                paginationHTML += `
                    <button onclick="loadApplications(${i})" 
                        class="px-4 py-2 border rounded-lg transition ${
                            i === current_page 
                                ? 'bg-blue-600 text-white border-blue-600' 
                                : 'border-gray-300 text-gray-700 hover:bg-gray-50'
                        }">
                        ${i}
                    </button>
                `;
            } else if (i === current_page - 3 || i === current_page + 3) {
                paginationHTML += `<span class="px-2 text-gray-500">...</span>`;
            }
        }

        // Next button
        if (current_page < last_page) {
            paginationHTML += `
                <button onclick="loadApplications(${current_page + 1})" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                    Next
                </button>
            `;
        }

        paginationHTML += '</div>';
        container.innerHTML = paginationHTML;
    }

    // ========== Withdraw Application ==========
    async function withdrawApplication(id) {
        if (!confirm('Are you sure you want to withdraw this application? This action cannot be undone.')) {
            return;
        }

        try {
            const response = await fetch(`${API_BASE}/job-seeker/applications/${id}/withdraw`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': getCsrfToken(),
                    'Accept': 'application/json'
                },
                credentials: 'include'
            });

            const data = await response.json();

            if (response.ok) {
                showSuccessToast('Application withdrawn successfully');
                loadApplications(currentPage);
            } else {
                showErrorToast(data.message || 'Failed to withdraw application');
            }
        } catch (error) {
            console.error('Error withdrawing application:', error);
            showErrorToast('An error occurred. Please try again.');
        }
    }

    // ========== Event Listeners ==========
    function setupEventListeners() {
        const statusFilter = document.getElementById('status-filter');
        const sortFilter = document.getElementById('sort-filter');

        if (statusFilter) {
            statusFilter.addEventListener('change', () => loadApplications(1));
        }

        if (sortFilter) {
            sortFilter.addEventListener('change', () => {
                // Re-render with current applications
                renderApplications(applications);
            });
        }
    }

    // ========== Initialization ==========
    function initApplications() {
        if (window.location.pathname !== '/job-seeker/applications') return;

        console.log('[applications.js] Initializing applications page...');
        setupEventListeners();
        loadApplications();
    }

    // Run on initial page load
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initApplications);
    } else {
        initApplications();
    }

    // Run on Livewire SPA navigation
    document.addEventListener('livewire:navigated', function() {
        setTimeout(initApplications, 50);
    });

    // Expose functions globally
    window.loadApplications = loadApplications;
    window.withdrawApplication = withdrawApplication;
})();
