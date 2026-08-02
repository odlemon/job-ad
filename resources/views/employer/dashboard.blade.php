@extends('layouts.employer')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    @include('partials.employer-navbar')

    <div class="flex">
        @include('partials.employer-sidebar')

        <main class="flex-1 min-w-0 p-8 ml-64 w-full">
            <div class="w-full max-w-none space-y-6">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Dashboard Overview</h1>
                    <p class="text-gray-600 dark:text-gray-400 mt-1">Welcome back! Here's what's happening with your job postings</p>
                </div>

                <div id="dashboard-loading" class="space-y-6 animate-pulse">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        <div class="bg-white dark:bg-gray-800 rounded border border-gray-200 dark:border-gray-700 p-6 h-36"></div>
                        <div class="bg-white dark:bg-gray-800 rounded border border-gray-200 dark:border-gray-700 p-6 h-36"></div>
                        <div class="bg-white dark:bg-gray-800 rounded border border-gray-200 dark:border-gray-700 p-6 h-36"></div>
                        <div class="bg-white dark:bg-gray-800 rounded border border-gray-200 dark:border-gray-700 p-6 h-36"></div>
                    </div>
                </div>

                <div id="dashboard-content" class="hidden space-y-6">
                    {{-- Metric cards (Bolt: gradient icon, label then value) --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        <div class="bg-white dark:bg-gray-800 p-6 rounded border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md transition-shadow">
                            <div class="flex items-start justify-between mb-4">
                                <div class="p-3 rounded-lg shadow-lg" style="background: linear-gradient(to bottom right, #2563eb, #06b6d4);">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                </div>
                                <div id="active-jobs-trend" class="flex items-center gap-1 text-sm font-medium"></div>
                            </div>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Active Jobs</p>
                            <p class="text-3xl font-bold text-gray-900 dark:text-white" id="active-jobs-value">0</p>
                        </div>

                        <div class="bg-white dark:bg-gray-800 p-6 rounded border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md transition-shadow">
                            <div class="flex items-start justify-between mb-4">
                                <div class="p-3 rounded-lg shadow-lg" style="background: linear-gradient(to bottom right, #059669, #14b8a6);">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                                </div>
                                <div id="applications-trend" class="flex items-center gap-1 text-sm font-medium"></div>
                            </div>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Total Applications</p>
                            <p class="text-3xl font-bold text-gray-900 dark:text-white" id="applications-value">0</p>
                        </div>

                        <div class="bg-white dark:bg-gray-800 p-6 rounded border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md transition-shadow">
                            <div class="flex items-start justify-between mb-4">
                                <div class="p-3 rounded-lg shadow-lg" style="background: linear-gradient(to bottom right, #7c3aed, #a855f7);">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </div>
                                <div id="views-trend" class="flex items-center gap-1 text-sm font-medium"></div>
                            </div>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Total Views</p>
                            <p class="text-3xl font-bold text-gray-900 dark:text-white" id="views-value">0</p>
                        </div>

                        <div class="bg-white dark:bg-gray-800 p-6 rounded border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md transition-shadow">
                            <div class="flex items-start justify-between mb-4">
                                <div class="p-3 rounded-lg shadow-lg" style="background: linear-gradient(to bottom right, #ea580c, #f59e0b);">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                                </div>
                                <div id="conversion-trend" class="flex items-center gap-1 text-sm font-medium"></div>
                            </div>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Conversion Rate</p>
                            <p class="text-3xl font-bold text-gray-900 dark:text-white" id="conversion-value">0%</p>
                        </div>
                    </div>

                    {{-- Recent jobs + applicants --}}
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <div class="bg-white dark:bg-gray-800 rounded border border-gray-200 dark:border-gray-700 shadow-sm">
                            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                                <h2 class="text-lg font-bold text-gray-900 dark:text-white">Recent Job Postings</h2>
                            </div>
                            <div id="recent-jobs-list" class="divide-y divide-gray-200 dark:divide-gray-700"></div>
                        </div>

                        <div class="bg-white dark:bg-gray-800 rounded border border-gray-200 dark:border-gray-700 shadow-sm">
                            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                                <h2 class="text-lg font-bold text-gray-900 dark:text-white">Recent Applicants</h2>
                            </div>
                            <div id="recent-applicants-list" class="divide-y divide-gray-200 dark:divide-gray-700"></div>
                        </div>
                    </div>

                    {{-- Bottom summary strip --}}
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        <div class="p-6 rounded shadow-lg text-white" style="background: linear-gradient(to bottom right, #2563eb, #06b6d4);">
                            <div class="flex items-center justify-between mb-4">
                                <svg class="w-8 h-8 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span class="text-3xl font-bold" id="pending-reviews-value">0</span>
                            </div>
                            <h3 class="font-semibold mb-1">Pending Reviews</h3>
                            <p class="text-sm opacity-90">Applications waiting for review</p>
                        </div>

                        <div class="p-6 rounded shadow-lg text-white" style="background: linear-gradient(to bottom right, #059669, #14b8a6);">
                            <div class="flex items-center justify-between mb-4">
                                <svg class="w-8 h-8 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span class="text-3xl font-bold" id="shortlisted-value">0</span>
                            </div>
                            <h3 class="font-semibold mb-1">Shortlisted</h3>
                            <p class="text-sm opacity-90">Candidates in pipeline</p>
                        </div>

                        <div class="p-6 rounded shadow-lg text-white" style="background: linear-gradient(to bottom right, #7c3aed, #a855f7);">
                            <div class="flex items-center justify-between mb-4">
                                <svg class="w-8 h-8 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                                <span class="text-3xl font-bold" id="weekly-impressions-value">0</span>
                            </div>
                            <h3 class="font-semibold mb-1">This Week</h3>
                            <p class="text-sm opacity-90">Total job post impressions</p>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<script>
    const API_BASE = '/api';

    async function loadDashboard() {
        try {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
            const response = await fetch(`${API_BASE}/employer/dashboard`, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken || '',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                credentials: 'include',
            });

            if (!response.ok) {
                const errorData = await response.json().catch(() => ({}));
                console.error('Dashboard API error:', response.status, errorData);
                throw new Error(errorData.message || 'Failed to load dashboard');
            }

            const result = await response.json();
            const data = result.data;

            if (data.employer) {
                const nameEl = document.getElementById('company-name');
                const coinEl = document.getElementById('coin-balance');
                const initEl = document.getElementById('company-initials');
                if (nameEl) nameEl.textContent = data.employer.company_name || 'Company';
                if (coinEl) coinEl.textContent = Number(data.employer.coin_balance || 0).toLocaleString();
                const companyName = data.employer.company_name || 'Company';
                const initials = companyName.split(' ').map(n => n[0]).join('').substring(0, 2).toUpperCase();
                if (initEl) initEl.textContent = initials || 'C';
            }

            if (data.metrics) {
                document.getElementById('active-jobs-value').textContent = data.metrics.active_jobs.value;
                document.getElementById('active-jobs-trend').innerHTML = formatTrend(data.metrics.active_jobs.trend);

                document.getElementById('applications-value').textContent = formatNumber(data.metrics.total_applications.value);
                document.getElementById('applications-trend').innerHTML = formatTrend(data.metrics.total_applications.trend);

                document.getElementById('views-value').textContent = formatNumber(data.metrics.total_views.value);
                document.getElementById('views-trend').innerHTML = formatTrend(data.metrics.total_views.trend);

                document.getElementById('conversion-value').textContent = data.metrics.conversion_rate.value + '%';
                document.getElementById('conversion-trend').innerHTML = formatTrend(data.metrics.conversion_rate.trend);
            }

            const recentJobsList = document.getElementById('recent-jobs-list');
            if (data.recent_jobs && data.recent_jobs.length > 0) {
                recentJobsList.innerHTML = data.recent_jobs.map(job => {
                    const days = Math.floor(job.posted_days_ago || 0);
                    const status = (job.status === 'published' || job.status === 'active') ? 'active' : (job.status || 'paused');
                    const statusClass = status === 'active'
                        ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400'
                        : 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300';
                    return `
                    <div class="p-6 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                        <div class="flex items-start justify-between mb-3">
                            <div class="flex-1">
                                <h3 class="font-semibold text-gray-900 dark:text-white mb-1">${escapeHtml(job.title)}</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Posted ${days} ${days === 1 ? 'day' : 'days'} ago</p>
                            </div>
                            <span class="px-3 py-1 rounded-full text-xs font-medium ${statusClass}">${status}</span>
                        </div>
                        <div class="flex items-center gap-6 text-sm flex-wrap">
                            <div class="flex items-center gap-2 text-gray-600 dark:text-gray-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                <span>${job.applications_count} applications</span>
                            </div>
                            <div class="flex items-center gap-2 text-gray-600 dark:text-gray-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                <span>${formatNumber(job.views_count)} views</span>
                            </div>
                            ${job.today_activity > 0 ? `
                            <div class="flex items-center gap-1 text-emerald-600 dark:text-emerald-400 font-medium">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>
                                <span>+${job.today_activity} today</span>
                            </div>` : ''}
                        </div>
                    </div>`;
                }).join('');
            } else {
                recentJobsList.innerHTML = '<p class="text-gray-500 dark:text-gray-400 text-center py-8 px-6">No job postings yet</p>';
            }

            const recentApplicantsList = document.getElementById('recent-applicants-list');
            if (data.recent_applicants && data.recent_applicants.length > 0) {
                recentApplicantsList.innerHTML = data.recent_applicants.map(applicant => {
                    const status = (applicant.status || 'new').toLowerCase();
                    const statusClass = status === 'new'
                        ? 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400'
                        : (status === 'reviewed'
                            ? 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300'
                            : 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400');
                    const rating = Math.min(5, Math.max(1, Number(applicant.rating) || 4));
                    return `
                    <div class="p-6 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-full flex items-center justify-center text-white font-bold text-sm flex-shrink-0"
                                 style="background: linear-gradient(to bottom right, #2563eb, #06b6d4);">${escapeHtml(applicant.initials || '?')}</div>
                            <div class="flex-1 min-w-0">
                                <h3 class="font-semibold text-gray-900 dark:text-white truncate">${escapeHtml(applicant.name)}</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400 truncate">${escapeHtml(applicant.job_title || '')}</p>
                            </div>
                            <div class="flex items-center gap-2">
                                ${Array.from({ length: rating }).map(() => '<div class="w-1.5 h-1.5 bg-amber-500 rounded-full"></div>').join('')}
                            </div>
                        </div>
                        <div class="mt-3 flex items-center justify-between">
                            <span class="text-xs text-gray-500 dark:text-gray-400">${escapeHtml(applicant.time_ago || '')}</span>
                            <span class="px-2 py-1 rounded text-xs font-medium ${statusClass}">${escapeHtml(status)}</span>
                        </div>
                    </div>`;
                }).join('');
            } else {
                recentApplicantsList.innerHTML = '<p class="text-gray-500 dark:text-gray-400 text-center py-8 px-6">No applicants yet</p>';
            }

            if (data.summary) {
                document.getElementById('pending-reviews-value').textContent = data.summary.pending_reviews;
                document.getElementById('shortlisted-value').textContent = data.summary.shortlisted;
                document.getElementById('weekly-impressions-value').textContent = formatNumber(data.summary.weekly_impressions);
            }

            document.getElementById('dashboard-loading').classList.add('hidden');
            document.getElementById('dashboard-content').classList.remove('hidden');
        } catch (error) {
            console.error('Error loading dashboard:', error);
            document.getElementById('dashboard-loading').innerHTML = '<div class="text-center py-12 text-red-500"><p class="text-lg mb-2">Error loading dashboard</p><p class="text-sm">Please try again later</p></div>';
        }
    }

    function formatTrend(trend) {
        const isPositive = Number(trend) >= 0;
        const color = isPositive ? 'text-emerald-600' : 'text-red-600';
        const symbol = isPositive ? '+' : '';
        const arrow = isPositive
            ? `<svg class="w-4 h-4 ${color}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>`
            : `<svg class="w-4 h-4 ${color}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg>`;
        return `${arrow}<span class="${color}">${symbol}${Math.abs(Number(trend))}%</span>`;
    }

    function formatNumber(num) {
        const n = Number(num) || 0;
        if (n >= 1000) return (n / 1000).toFixed(1).replace(/\.0$/, '') + 'K';
        return n.toLocaleString();
    }

    function escapeHtml(str) {
        return String(str ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', loadDashboard);
    } else {
        loadDashboard();
    }

    if (typeof Livewire !== 'undefined') {
        document.addEventListener('livewire:navigated', function () {
            if (window.location.pathname === '/employer/dashboard') {
                setTimeout(loadDashboard, 100);
            }
        });
    }
</script>
@endsection
