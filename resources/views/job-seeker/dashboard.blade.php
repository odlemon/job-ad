@extends('layouts.job-seeker')

@section('content')
<style>
    .stat-card-blue { background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); }
    .stat-card-yellow { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); }
    .stat-card-green { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }
    .stat-card-red { background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); }
</style>

<!-- Main Content Area -->
<div class="flex-1 flex flex-col">
    @include('partials.job-seeker-navbar')

    <!-- Main Content -->
    <main class="flex-1 p-6 bg-gray-50">
        <div class="max-w-7xl mx-auto">
            <!-- Dashboard Header -->
            <div class="mb-6">
                <h1 class="text-2xl font-bold text-gray-900">Dashboard Overview</h1>
            </div>

            <!-- Loading Skeleton -->
            <div id="dashboard-loading">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                    <div class="bg-white rounded-lg shadow-sm p-6 animate-pulse">
                        <div class="h-12 w-12 bg-gray-200 rounded-lg mb-4"></div>
                        <div class="h-8 bg-gray-200 rounded mb-2 w-16"></div>
                        <div class="h-4 bg-gray-200 rounded mb-2 w-32"></div>
                        <div class="h-3 bg-gray-200 rounded w-24"></div>
                    </div>
                    <div class="bg-white rounded-lg shadow-sm p-6 animate-pulse">
                        <div class="h-12 w-12 bg-gray-200 rounded-lg mb-4"></div>
                        <div class="h-8 bg-gray-200 rounded mb-2 w-16"></div>
                        <div class="h-4 bg-gray-200 rounded mb-2 w-32"></div>
                        <div class="h-3 bg-gray-200 rounded w-24"></div>
                    </div>
                    <div class="bg-white rounded-lg shadow-sm p-6 animate-pulse">
                        <div class="h-12 w-12 bg-gray-200 rounded-lg mb-4"></div>
                        <div class="h-8 bg-gray-200 rounded mb-2 w-16"></div>
                        <div class="h-4 bg-gray-200 rounded mb-2 w-32"></div>
                        <div class="h-3 bg-gray-200 rounded w-24"></div>
                    </div>
                    <div class="bg-white rounded-lg shadow-sm p-6 animate-pulse">
                        <div class="h-12 w-12 bg-gray-200 rounded-lg mb-4"></div>
                        <div class="h-8 bg-gray-200 rounded mb-2 w-16"></div>
                        <div class="h-4 bg-gray-200 rounded mb-2 w-32"></div>
                        <div class="h-3 bg-gray-200 rounded w-24"></div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                    <div class="bg-white rounded-lg shadow-sm p-6 animate-pulse">
                        <div class="h-6 bg-gray-200 rounded mb-4 w-32"></div>
                        <div class="space-y-3">
                            <div class="h-12 bg-gray-200 rounded"></div>
                            <div class="h-12 bg-gray-200 rounded"></div>
                            <div class="h-12 bg-gray-200 rounded"></div>
                        </div>
                    </div>
                    <div class="bg-white rounded-lg shadow-sm p-6 animate-pulse">
                        <div class="h-6 bg-gray-200 rounded mb-4 w-32"></div>
                        <div class="space-y-3">
                            <div class="h-16 bg-gray-200 rounded"></div>
                            <div class="h-16 bg-gray-200 rounded"></div>
                            <div class="h-16 bg-gray-200 rounded"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Dashboard Content -->
            <div id="dashboard-content" class="hidden">
                <!-- Stats Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                    <!-- Total Applications -->
                    <div class="bg-white rounded-lg shadow-sm p-5 border border-gray-100">
                        <div class="flex items-start justify-between mb-3">
                            <div>
                                <p class="text-gray-600 text-sm mb-2">Total Applications</p>
                                <h3 class="text-3xl font-bold text-gray-900" id="total-applications-value">0</h3>
                                <p class="text-blue-600 text-xs font-medium mt-2" id="total-applications-change">+0 this week</p>
                            </div>
                            <div class="p-3 stat-card-blue rounded-lg">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- In Review -->
                    <div class="bg-white rounded-lg shadow-sm p-5 border border-gray-100">
                        <div class="flex items-start justify-between mb-3">
                            <div>
                                <p class="text-gray-600 text-sm mb-2">In Review</p>
                                <h3 class="text-3xl font-bold text-gray-900" id="in-review-value">0</h3>
                                <p class="text-blue-600 text-xs font-medium mt-2" id="in-review-detail">0 interviews scheduled</p>
                            </div>
                            <div class="p-3 stat-card-yellow rounded-lg">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Offers Received -->
                    <div class="bg-white rounded-lg shadow-sm p-5 border border-gray-100">
                        <div class="flex items-start justify-between mb-3">
                            <div>
                                <p class="text-gray-600 text-sm mb-2">Offers Received</p>
                                <h3 class="text-3xl font-bold text-gray-900" id="offers-value">0</h3>
                                <p class="text-green-600 text-xs font-medium mt-2" id="offers-change">+0 this week</p>
                            </div>
                            <div class="p-3 stat-card-green rounded-lg">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Rejected -->
                    <div class="bg-white rounded-lg shadow-sm p-5 border border-gray-100">
                        <div class="flex items-start justify-between mb-3">
                            <div>
                                <p class="text-gray-600 text-sm mb-2">Rejected</p>
                                <h3 class="text-3xl font-bold text-gray-900" id="rejected-value">0</h3>
                                <p class="text-gray-600 text-xs mt-2">Keep applying!</p>
                            </div>
                            <div class="p-3 stat-card-red rounded-lg">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Row 2: Recent Activity and Achievements -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                    <!-- Recent Activity -->
                    <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-100">
                        <div class="flex justify-between items-center mb-5">
                            <h2 class="text-lg font-bold text-gray-900">Recent Activity</h2>
                            <a href="/job-seeker/applications" wire:navigate class="text-sm text-blue-600 hover:text-blue-700 font-medium">View All</a>
                        </div>
                        <div id="recent-activity-list" class="space-y-4">
                            <!-- Activities will be loaded here -->
                        </div>
                    </div>

                    <!-- Achievements -->
                    <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-100">
                        <div class="flex items-center gap-2 mb-5">
                            <svg class="w-5 h-5 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                            </svg>
                            <h2 class="text-lg font-bold text-gray-900">Achievements</h2>
                        </div>
                        <div id="achievements-list" class="space-y-3">
                            <!-- Achievements will be loaded here -->
                        </div>
                    </div>
                </div>

                <!-- Row 3: Profile Completeness and Skill Match -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Profile Completeness -->
                    <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-100">
                        <h2 class="text-lg font-bold text-gray-900 mb-5">Profile Completeness</h2>
                        <div class="mb-6">
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-sm font-medium text-gray-700">Overall Progress</span>
                                <span class="text-sm font-bold text-gray-900" id="profile-completeness-percent">0%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-3">
                                <div id="profile-completeness-bar" class="bg-gradient-to-r from-blue-400 to-cyan-500 h-3 rounded-full transition-all duration-500" style="width: 0%"></div>
                            </div>
                        </div>
                        <div id="profile-completeness-items" class="space-y-3">
                            <!-- Items will be loaded here -->
                        </div>
                    </div>

                    <!-- Skill Match Analytics -->
                    <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-100">
                        <h2 class="text-lg font-bold text-gray-900 mb-5">Skill Match Analytics</h2>
                        <div class="mb-5">
                            <div class="flex items-baseline gap-2 mb-1">
                                <h3 class="text-4xl font-bold text-gray-900" id="average-match">0%</h3>
                            </div>
                            <p class="text-sm text-gray-500">Average Match</p>
                        </div>
                        <div id="skill-matches-list" class="space-y-4">
                            <!-- Skill matches will be loaded here -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

@push('scripts')
<script>
    const API_BASE = '/api';

    async function loadDashboard() {
        try {
            // Show loading
            document.getElementById('dashboard-loading').classList.remove('hidden');
            document.getElementById('dashboard-content').classList.add('hidden');

            // Load dashboard data from API
            const response = await fetch(`${API_BASE}/job-seeker/dashboard`, {
                credentials: 'include',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                }
            });

            if (response.status === 401 || response.status === 403) {
                window.location.href = '/login';
                return;
            }

            if (!response.ok) {
                throw new Error('Failed to load dashboard data');
            }

            const data = await response.json();

            // Update stat cards
            const stats = data.stats || {};
            document.getElementById('total-applications-value').textContent = stats.total_applications || 0;
            document.getElementById('total-applications-change').textContent = `+${stats.this_week_applications || 0} this week`;
            document.getElementById('in-review-value').textContent = stats.in_review || 0;
            document.getElementById('in-review-detail').textContent = `${stats.interview_scheduled || 0} interview${(stats.interview_scheduled || 0) !== 1 ? 's' : ''} scheduled`;
            document.getElementById('offers-value').textContent = stats.offers || 0;
            document.getElementById('offers-change').textContent = `+${stats.this_week_offers || 0} this week`;
            document.getElementById('rejected-value').textContent = stats.rejected || 0;

            // Update recent activity
            const recentActivityList = document.getElementById('recent-activity-list');
            const recentActivity = data.recent_activity || [];
            if (recentActivity.length > 0) {
                recentActivityList.innerHTML = recentActivity.map(activity => {
                    const timeAgo = getTimeAgo(new Date(activity.created_at));
                    const statusColor = getStatusDotColor(activity.status);
                    const statusText = activity.status === 'shortlisted' ? 'Interview scheduled' : 
                                     activity.status === 'reviewing' ? 'Application viewed' : 
                                     activity.status === 'rejected' ? 'Application rejected' :
                                     'Applied to';
                    return `
                        <div class="flex items-start gap-3">
                            <div class="w-2 h-2 ${statusColor} rounded-full mt-2 flex-shrink-0"></div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900">
                                    ${statusText}
                                </p>
                                <p class="text-xs text-gray-600 truncate">${activity.company_name || 'Company'}</p>
                            </div>
                            <span class="text-xs text-red-500 flex-shrink-0">${timeAgo}</span>
                        </div>
                    `;
                }).join('');
            } else {
                recentActivityList.innerHTML = '<p class="text-gray-500 text-center py-6 text-sm">No recent activity</p>';
            }

            // Update achievements
            const achievementsList = document.getElementById('achievements-list');
            const achievements = data.achievements || [];
            achievementsList.innerHTML = achievements.map(achievement => {
                const iconSvg = getAchievementIcon(achievement.icon);
                // Match the design: pink background for achieved, gray for not achieved
                const iconBgClass = achievement.achieved ? 'bg-pink-100' : 'bg-gray-100';
                const iconTextClass = achievement.achieved ? 'text-pink-600' : 'text-gray-400';
                const cardBgClass = achievement.achieved ? 'bg-pink-50' : 'bg-white';
                const iconShape = achievement.icon === 'target' ? 'rounded-full' : 'rounded-lg';
                return `
                    <div class="flex items-center gap-3 p-3 rounded-lg ${cardBgClass}">
                        <div class="p-2 ${iconBgClass} ${iconShape} flex-shrink-0">
                            <div class="${iconTextClass}">
                                ${iconSvg}
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-gray-900">${achievement.title}</p>
                            <p class="text-xs text-gray-600">${achievement.description}</p>
                        </div>
                    </div>
                `;
            }).join('');

            // Update profile completeness
            const profileCompleteness = data.profile_completeness || { percentage: 0, items: [] };
            document.getElementById('profile-completeness-percent').textContent = profileCompleteness.percentage + '%';
            document.getElementById('profile-completeness-bar').style.width = profileCompleteness.percentage + '%';

            const completenessItems = document.getElementById('profile-completeness-items');
            completenessItems.innerHTML = profileCompleteness.items.map(item => `
                <div class="flex items-center justify-between py-2">
                    <span class="text-sm ${item.complete ? 'text-green-600 font-medium' : 'text-gray-700'}">${item.label}</span>
                    <span class="text-xs font-medium ${item.complete ? 'text-green-600' : 'text-orange-600'}">${item.status}</span>
                </div>
            `).join('');

            // Update skill match analytics
            const skillMatchAnalytics = data.skill_match_analytics || { average_match: 0, skills: [] };
            document.getElementById('average-match').textContent = skillMatchAnalytics.average_match + '%';

            const skillMatchesList = document.getElementById('skill-matches-list');
            if (skillMatchAnalytics.skills && skillMatchAnalytics.skills.length > 0) {
                skillMatchesList.innerHTML = skillMatchAnalytics.skills.map(skill => {
                    return `
                        <div>
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-sm text-gray-700">${skill.name}</span>
                                <span class="text-sm font-bold text-gray-900">${skill.match}%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2.5">
                                <div class="${skill.color} h-2.5 rounded-full transition-all duration-500" style="width: ${skill.match}%"></div>
                            </div>
                        </div>
                    `;
                }).join('');
            } else {
                skillMatchesList.innerHTML = '<p class="text-gray-500 text-sm text-center py-4">Add skills to your profile to see match analytics</p>';
            }

            // Hide loading, show content
            document.getElementById('dashboard-loading').classList.add('hidden');
            document.getElementById('dashboard-content').classList.remove('hidden');

        } catch (error) {
            console.error('Error loading dashboard:', error);
            document.getElementById('dashboard-loading').classList.add('hidden');
            document.getElementById('dashboard-content').classList.remove('hidden');
        }
    }

    function getTimeAgo(date) {
        const seconds = Math.floor((new Date() - date) / 1000);
        if (seconds < 60) return 'just now';
        const minutes = Math.floor(seconds / 60);
        if (minutes < 60) return minutes === 1 ? '1 minute ago' : `${minutes} minutes ago`;
        const hours = Math.floor(minutes / 60);
        if (hours < 24) return hours === 1 ? '1 hour ago' : `${hours} hours ago`;
        const days = Math.floor(hours / 24);
        return days === 1 ? '1 day ago' : `${days} days ago`;
    }

    function getStatusDotColor(status) {
        const colors = {
            'pending': 'bg-blue-500',
            'reviewing': 'bg-blue-500',
            'shortlisted': 'bg-green-500',
            'hired': 'bg-green-500',
            'accepted': 'bg-green-500',
            'rejected': 'bg-red-500',
        };
        return colors[status] || 'bg-gray-500';
    }

    function getAchievementIcon(type) {
        const icons = {
            target: '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>',
            rocket: '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z"></path></svg>',
            star: '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>',
        };
        return icons[type] || icons.target;
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
            if (window.location.pathname === '/dashboard' || window.location.pathname === '/job-seeker/dashboard') {
                setTimeout(loadDashboard, 100);
            }
        });
    }
</script>
@endpush
@endsection
