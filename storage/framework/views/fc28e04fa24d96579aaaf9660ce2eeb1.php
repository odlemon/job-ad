<?php $__env->startSection('content'); ?>
<div class="min-h-screen bg-gray-50">
    <?php echo $__env->make('partials.employer-navbar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <div class="flex">
        <?php echo $__env->make('partials.employer-sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        <!-- Main Content -->
        <main class="flex-1 p-8 ml-64">
            <div class="max-w-7xl mx-auto">
                <!-- Dashboard Header -->
                <div class="mb-8">
                    <h1 class="text-2xl font-bold text-gray-900 mb-2">Dashboard Overview</h1>
                    <p class="text-gray-600">Welcome back! Here's what's happening with your job postings.</p>
                </div>

                <!-- Loading State -->
                <div id="dashboard-loading" class="space-y-6 animate-pulse">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        <div class="bg-white rounded-xl shadow-sm p-6 h-32"></div>
                        <div class="bg-white rounded-xl shadow-sm p-6 h-32"></div>
                        <div class="bg-white rounded-xl shadow-sm p-6 h-32"></div>
                        <div class="bg-white rounded-xl shadow-sm p-6 h-32"></div>
                    </div>
                </div>

                <!-- Dashboard Content -->
                <div id="dashboard-content" class="hidden space-y-6">
                    <!-- Key Metrics Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        <!-- Active Jobs -->
                        <div class="bg-white rounded-xl shadow-sm p-6">
                            <div class="flex items-center justify-between mb-4">
                                <div class="p-3 bg-blue-100 rounded-lg">
                                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                                <div id="active-jobs-trend" class="flex items-center text-sm">
                                    <svg class="w-4 h-4 text-green-600 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path>
                                    </svg>
                                    <span class="text-green-600 font-medium">+0%</span>
                                </div>
                            </div>
                            <div class="text-3xl font-bold text-gray-900 mb-1" id="active-jobs-value">0</div>
                            <div class="text-sm text-gray-500">Active Jobs</div>
                        </div>

                        <!-- Total Applications -->
                        <div class="bg-white rounded-xl shadow-sm p-6">
                            <div class="flex items-center justify-between mb-4">
                                <div class="p-3 bg-green-100 rounded-lg">
                                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                    </svg>
                                </div>
                                <div id="applications-trend" class="flex items-center text-sm">
                                    <svg class="w-4 h-4 text-green-600 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path>
                                    </svg>
                                    <span class="text-green-600 font-medium">+0%</span>
                                </div>
                            </div>
                            <div class="text-3xl font-bold text-gray-900 mb-1" id="applications-value">0</div>
                            <div class="text-sm text-gray-500">Total Applications</div>
                        </div>

                        <!-- Total Views -->
                        <div class="bg-white rounded-xl shadow-sm p-6">
                            <div class="flex items-center justify-between mb-4">
                                <div class="p-3 bg-purple-100 rounded-lg">
                                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </div>
                                <div id="views-trend" class="flex items-center text-sm">
                                    <svg class="w-4 h-4 text-green-600 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path>
                                    </svg>
                                    <span class="text-green-600 font-medium">+0%</span>
                                </div>
                            </div>
                            <div class="text-3xl font-bold text-gray-900 mb-1" id="views-value">0</div>
                            <div class="text-sm text-gray-500">Total Views</div>
                        </div>

                        <!-- Conversion Rate -->
                        <div class="bg-white rounded-xl shadow-sm p-6">
                            <div class="flex items-center justify-between mb-4">
                                <div class="p-3 bg-orange-100 rounded-lg">
                                    <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                    </svg>
                                </div>
                                <div id="conversion-trend" class="flex items-center text-sm">
                                    <svg class="w-4 h-4 text-red-600 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                                    </svg>
                                    <span class="text-red-600 font-medium">-0%</span>
                                </div>
                            </div>
                            <div class="text-3xl font-bold text-gray-900 mb-1" id="conversion-value">0%</div>
                            <div class="text-sm text-gray-500">Conversion Rate</div>
                        </div>
                    </div>

                    <!-- Recent Jobs and Applicants -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- Recent Job Postings -->
                        <div class="bg-white rounded-xl shadow-sm p-6">
                            <h2 class="text-lg font-bold text-gray-900 mb-4">Recent Job Postings</h2>
                            <div id="recent-jobs-list" class="space-y-4">
                                <!-- Jobs will be loaded here -->
                            </div>
                        </div>

                        <!-- Recent Applicants -->
                        <div class="bg-white rounded-xl shadow-sm p-6">
                            <h2 class="text-lg font-bold text-gray-900 mb-4">Recent Applicants</h2>
                            <div id="recent-applicants-list" class="space-y-4">
                                <!-- Applicants will be loaded here -->
                            </div>
                        </div>
                    </div>

                    <!-- Summary Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Pending Reviews -->
                        <div class="bg-gradient-to-r from-blue-500 to-blue-400 rounded-xl shadow-lg p-6 text-white">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <div class="mb-4">
                                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </div>
                                    <h3 class="text-lg font-bold mb-1">Pending Reviews</h3>
                                    <p class="text-sm text-white text-opacity-90">Applications waiting for review</p>
                                </div>
                                <div class="text-4xl font-bold" id="pending-reviews-value">0</div>
                            </div>
                        </div>

                        <!-- Shortlisted -->
                        <div class="bg-gradient-to-r from-green-500 to-green-400 rounded-xl shadow-lg p-6 text-white">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <div class="mb-4">
                                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </div>
                                    <h3 class="text-lg font-bold mb-1">Shortlisted</h3>
                                    <p class="text-sm text-white text-opacity-90">Candidates in pipeline</p>
                                </div>
                                <div class="text-4xl font-bold" id="shortlisted-value">0</div>
                            </div>
                        </div>

                        <!-- This Week -->
                        <div class="bg-gradient-to-r from-purple-500 to-purple-400 rounded-xl shadow-lg p-6 text-white">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <div class="mb-4">
                                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                                        </svg>
                                    </div>
                                    <h3 class="text-lg font-bold mb-1">This Week</h3>
                                    <p class="text-sm text-white text-opacity-90">Total job post impressions</p>
                                </div>
                                <div class="text-4xl font-bold" id="weekly-impressions-value">0</div>
                            </div>
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

            // Update company info
            if (data.employer) {
                document.getElementById('company-name').textContent = data.employer.company_name || 'Company';
                document.getElementById('coin-balance').textContent = data.employer.coin_balance || 0;
                
                // Set company initials
                const companyName = data.employer.company_name || 'Company';
                const initials = companyName.split(' ').map(n => n[0]).join('').substring(0, 2).toUpperCase();
                document.getElementById('company-initials').textContent = initials || 'C';
            }

            // Update metrics
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

            // Render recent jobs
            const recentJobsList = document.getElementById('recent-jobs-list');
            if (data.recent_jobs && data.recent_jobs.length > 0) {
                recentJobsList.innerHTML = data.recent_jobs.map(job => `
                    <div class="border-b border-gray-200 pb-4 last:border-0 last:pb-0">
                        <div class="flex items-start justify-between mb-2">
                            <h3 class="font-semibold text-gray-900 text-base">${job.title}</h3>
                            <span class="px-2.5 py-1 rounded-md text-xs font-medium ml-3 ${
                                job.status === 'published' || job.status === 'active' 
                                    ? 'bg-green-100 text-green-700' 
                                    : 'bg-gray-100 text-gray-700'
                            }">${job.status === 'published' ? 'active' : job.status}</span>
                        </div>
                        <div class="text-sm text-gray-600 space-y-1.5">
                            <div>Posted ${Math.floor(job.posted_days_ago)} ${Math.floor(job.posted_days_ago) === 1 ? 'day' : 'days'} ago</div>
                            <div class="flex items-center gap-4">
                                <div class="flex items-center gap-1.5">
                                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                    </svg>
                                    <span>${job.applications_count} applications</span>
                                </div>
                                <div class="flex items-center gap-1.5">
                                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                    <span>${formatNumber(job.views_count)} views</span>
                                </div>
                            </div>
                            ${job.today_activity > 0 ? `
                                <div class="flex items-center gap-1.5 text-green-600 font-medium">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path>
                                    </svg>
                                    <span>+${job.today_activity} today</span>
                                </div>
                            ` : ''}
                        </div>
                    </div>
                `).join('');
            } else {
                recentJobsList.innerHTML = '<p class="text-gray-500 text-center py-8">No job postings yet</p>';
            }

            // Render recent applicants
            const recentApplicantsList = document.getElementById('recent-applicants-list');
            if (data.recent_applicants && data.recent_applicants.length > 0) {
                recentApplicantsList.innerHTML = data.recent_applicants.map(applicant => `
                    <div class="flex items-center gap-4 pb-4 border-b border-gray-200 last:border-0 last:pb-0">
                        <div class="w-12 h-12 bg-blue-600 rounded-full flex items-center justify-center flex-shrink-0">
                            <span class="text-white font-semibold text-sm">${applicant.initials}</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between mb-1">
                                <h4 class="font-semibold text-gray-900 text-sm truncate">${applicant.name}</h4>
                                <span class="px-2.5 py-1 rounded-md text-xs font-medium ml-3 ${
                                    applicant.status === 'new' ? 'bg-blue-100 text-blue-700' :
                                    applicant.status === 'shortlisted' ? 'bg-green-100 text-green-700' :
                                    applicant.status === 'reviewed' ? 'bg-amber-100 text-amber-700' :
                                    'bg-gray-100 text-gray-700'
                                }">${applicant.status}</span>
                            </div>
                            <p class="text-sm text-gray-600 truncate mb-0.5">${applicant.job_title}</p>
                            <p class="text-xs text-gray-500">Applied: ${applicant.time_ago}</p>
                        </div>
                        <div class="flex gap-1 flex-shrink-0">
                            ${Array(5).fill(0).map(() => '<div class="w-1.5 h-1.5 bg-orange-400 rounded-full"></div>').join('')}
                        </div>
                    </div>
                `).join('');
            } else {
                recentApplicantsList.innerHTML = '<p class="text-gray-500 text-center py-8">No applicants yet</p>';
            }

            // Update summary cards
            if (data.summary) {
                document.getElementById('pending-reviews-value').textContent = data.summary.pending_reviews;
                document.getElementById('shortlisted-value').textContent = data.summary.shortlisted;
                document.getElementById('weekly-impressions-value').textContent = formatNumber(data.summary.weekly_impressions);
            }

            // Hide loading, show content
            document.getElementById('dashboard-loading').classList.add('hidden');
            document.getElementById('dashboard-content').classList.remove('hidden');

        } catch (error) {
            console.error('Error loading dashboard:', error);
            document.getElementById('dashboard-loading').innerHTML = '<div class="text-center py-12 text-red-500"><p class="text-lg mb-2">Error loading dashboard</p><p class="text-sm">Please try again later</p></div>';
        }
    }

    function formatTrend(trend) {
        const isPositive = trend >= 0;
        const color = isPositive ? 'text-green-600' : 'text-red-600';
        const symbol = isPositive ? '+' : '';
        const arrowIcon = isPositive 
            ? '<svg class="w-4 h-4 ' + color + ' mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path></svg>'
            : '<svg class="w-4 h-4 ' + color + ' mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path></svg>';
        return arrowIcon + `<span class="${color} font-medium">${symbol}${Math.abs(trend)}%</span>`;
    }

    function formatNumber(num) {
        if (num >= 1000) {
            return (num / 1000).toFixed(1) + 'K';
        }
        return num.toLocaleString();
    }

    // Load dashboard on page load
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', loadDashboard);
    } else {
        loadDashboard();
    }

    // Reload on navigation
    if (typeof Livewire !== 'undefined') {
        document.addEventListener('livewire:navigated', function() {
            if (window.location.pathname === '/employer/dashboard') {
                setTimeout(loadDashboard, 100);
            }
        });
    }

</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.employer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\lysp\Downloads\Job Ad\resources\views/employer/dashboard.blade.php ENDPATH**/ ?>