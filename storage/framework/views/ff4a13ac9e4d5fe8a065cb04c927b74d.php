

<?php $__env->startSection('content'); ?>
<div class="min-h-screen bg-gray-50 flex">
    <!-- Left Sidebar -->
    <aside class="w-64 bg-white min-h-screen sticky top-0 border-r border-gray-200 flex flex-col">
        <!-- Dark gray strip at top -->
        <div class="h-1 bg-gray-800"></div>
        
        <!-- Branding Section -->
        <div class="px-6 py-6 border-b border-gray-200">
            <div class="flex items-center space-x-3">
                <!-- Icon with solid background -->
                <div class="w-10 h-10 rounded-lg bg-blue-500 flex items-center justify-center flex-shrink-0">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <div>
                    <div class="text-lg font-bold text-blue-600">JobHub</div>
                    <div class="text-xs text-gray-500">Job Seeker Portal</div>
                </div>
            </div>
        </div>

        <!-- Navigation Links -->
        <nav class="flex-1 px-4 py-4 space-y-1">
            <!-- Dashboard Overview (Active) -->
            <a href="/dashboard" wire:navigate class="flex items-center space-x-3 px-4 py-3 relative group">
                <div class="absolute left-0 top-0 bottom-0 w-1 bg-blue-600 rounded-r-full"></div>
                <div class="absolute left-0 top-0 bottom-0 right-0 bg-blue-600 rounded-r-lg -z-10"></div>
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                </svg>
                <span class="text-white font-medium">Dashboard Overview</span>
            </a>

            <!-- My Profile -->
            <a href="/job-seeker/profile" wire:navigate class="flex items-center space-x-3 px-4 py-3 text-gray-700 hover:bg-gray-50 rounded-lg transition">
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
                <span>My Profile</span>
            </a>

            <!-- Job Applications -->
            <a href="/job-seeker/applications" wire:navigate class="flex items-center space-x-3 px-4 py-3 text-gray-700 hover:bg-gray-50 rounded-lg transition">
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                </svg>
                <span>Job Applications</span>
            </a>

            <!-- Job Discovery -->
            <a href="/jobs" wire:navigate class="flex items-center space-x-3 px-4 py-3 text-gray-700 hover:bg-gray-50 rounded-lg transition">
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                <span>Job Discovery</span>
            </a>

            <!-- Companies -->
            <a href="#" class="flex items-center space-x-3 px-4 py-3 text-gray-700 hover:bg-gray-50 rounded-lg transition">
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
                <span>Companies</span>
            </a>

            <!-- Notifications -->
            <a href="#" class="flex items-center space-x-3 px-4 py-3 text-gray-700 hover:bg-gray-50 rounded-lg transition">
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                </svg>
                <span>Notifications</span>
            </a>

            <!-- Career Tools -->
            <a href="#" class="flex items-center space-x-3 px-4 py-3 text-gray-700 hover:bg-gray-50 rounded-lg transition">
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
                <span>Career Tools</span>
            </a>

            <!-- Support -->
            <a href="#" class="flex items-center space-x-3 px-4 py-3 text-gray-700 hover:bg-gray-50 rounded-lg transition">
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span>Support</span>
            </a>

            <!-- Settings -->
            <a href="#" class="flex items-center space-x-3 px-4 py-3 text-gray-700 hover:bg-gray-50 rounded-lg transition">
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
                <span>Settings</span>
            </a>
        </nav>
    </aside>

    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col">
        <!-- Top Header Bar -->
        <header class="bg-white border-b border-gray-200 sticky top-0 z-40">
            <div class="px-6 py-4">
                <div class="flex items-center justify-between">
                    <!-- Search Bar -->
                    <div class="flex-1 max-w-2xl">
                        <div class="relative">
                            <input 
                                type="text" 
                                placeholder="Search jobs, companies..." 
                                class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            >
                            <svg class="absolute left-3 top-2.5 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                    </div>

                    <!-- Right Side Icons -->
                    <div class="flex items-center space-x-4 ml-6">
                        <!-- Dark Mode Toggle -->
                        <button class="p-2 text-gray-600 hover:text-gray-900 transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
                            </svg>
                        </button>

                        <!-- Notifications -->
                        <button class="p-2 text-gray-600 hover:text-gray-900 transition relative">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                            </svg>
                            <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
                        </button>

                        <!-- User Avatar -->
                        <div class="relative" id="user-menu-container">
                            <button id="user-menu-button" class="flex items-center">
                                <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center">
                                    <span id="user-initials" class="text-white text-sm font-semibold">U</span>
                                </div>
                            </button>
                            
                            <!-- Dropdown Menu -->
                            <div id="user-menu-dropdown" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 py-1 z-50">
                                <a href="/dashboard" wire:navigate class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Dashboard</a>
                                <a href="/job-seeker/profile" wire:navigate class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">My Profile</a>
                                <div class="border-t border-gray-200 my-1"></div>
                                <a href="<?php echo e(route('logout')); ?>" class="block px-4 py-2 text-sm text-red-600 hover:bg-red-50">Logout</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="flex-1 p-8">
            <div class="max-w-7xl mx-auto">
                <!-- Dashboard Header -->
                <div class="mb-8">
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">Dashboard Overview</h1>
                    <p class="text-gray-600">Welcome back! Here's your job search overview.</p>
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
                    <!-- Row 1: Application Statistics -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        <!-- Total Applications -->
                        <div class="bg-white rounded-xl shadow-sm p-6">
                            <div class="flex items-center justify-between mb-4">
                                <div class="p-3 bg-blue-100 rounded-lg">
                                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="text-3xl font-bold text-gray-900 mb-1" id="total-applications-value">0</div>
                            <div class="text-sm text-gray-500">Total Applications</div>
                            <div class="text-xs text-green-600 mt-2" id="total-applications-change">+0 this week</div>
                        </div>

                        <!-- In Review -->
                        <div class="bg-white rounded-xl shadow-sm p-6">
                            <div class="flex items-center justify-between mb-4">
                                <div class="p-3 bg-yellow-100 rounded-lg">
                                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="text-3xl font-bold text-gray-900 mb-1" id="in-review-value">0</div>
                            <div class="text-sm text-gray-500">In Review</div>
                            <div class="text-xs text-gray-600 mt-2" id="in-review-detail">0 interviews scheduled</div>
                        </div>

                        <!-- Offers Received -->
                        <div class="bg-white rounded-xl shadow-sm p-6">
                            <div class="flex items-center justify-between mb-4">
                                <div class="p-3 bg-green-100 rounded-lg">
                                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="text-3xl font-bold text-gray-900 mb-1" id="offers-value">0</div>
                            <div class="text-sm text-gray-500">Offers Received</div>
                            <div class="text-xs text-green-600 mt-2" id="offers-change">+0 this week</div>
                        </div>

                        <!-- Rejected -->
                        <div class="bg-white rounded-xl shadow-sm p-6">
                            <div class="flex items-center justify-between mb-4">
                                <div class="p-3 bg-red-100 rounded-lg">
                                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="text-3xl font-bold text-gray-900 mb-1" id="rejected-value">0</div>
                            <div class="text-sm text-gray-500">Rejected</div>
                            <div class="text-xs text-gray-600 mt-2">Keep applying!</div>
                        </div>
                    </div>

                    <!-- Row 2: Recent Activity and Achievements -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- Recent Activity -->
                        <div class="bg-white rounded-xl shadow-sm p-6">
                            <div class="flex justify-between items-center mb-4">
                                <h2 class="text-xl font-bold text-gray-900">Recent Activity</h2>
                                <a href="/job-seeker/applications" wire:navigate class="text-sm text-blue-600 hover:text-blue-700 font-medium">View All</a>
                            </div>
                            <div id="recent-activity-list" class="space-y-4">
                                <!-- Activities will be loaded here -->
                            </div>
                        </div>

                        <!-- Achievements -->
                        <div class="bg-white rounded-xl shadow-sm p-6">
                            <div class="flex items-center space-x-2 mb-4">
                                <svg class="w-6 h-6 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                </svg>
                                <h2 class="text-xl font-bold text-gray-900">Achievements</h2>
                            </div>
                            <div id="achievements-list" class="space-y-4">
                                <!-- Achievements will be loaded here -->
                            </div>
                        </div>
                    </div>

                    <!-- Row 3: Profile Completeness and Skill Match -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- Profile Completeness -->
                        <div class="bg-white rounded-xl shadow-sm p-6">
                            <h2 class="text-xl font-bold text-gray-900 mb-4">Profile Completeness</h2>
                            <div class="mb-6">
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-sm font-medium text-gray-700">Overall Progress</span>
                                    <span class="text-sm font-bold text-blue-600" id="profile-completeness-percent">0%</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-3">
                                    <div id="profile-completeness-bar" class="bg-gradient-to-r from-blue-400 to-blue-600 h-3 rounded-full transition-all duration-500" style="width: 0%"></div>
                                </div>
                            </div>
                            <div id="profile-completeness-items" class="space-y-3">
                                <!-- Items will be loaded here -->
                            </div>
                        </div>

                        <!-- Skill Match Analytics -->
                        <div class="bg-white rounded-xl shadow-sm p-6">
                            <h2 class="text-xl font-bold text-gray-900 mb-4">Skill Match Analytics</h2>
                            <div class="mb-4">
                                <div class="text-3xl font-bold text-gray-900 mb-1" id="average-match">0%</div>
                                <div class="text-sm text-gray-500">Average Match</div>
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
</div>

<?php $__env->startPush('scripts'); ?>
<script>
    const API_BASE = '/api';

    // User menu dropdown toggle
    (function() {
        const userMenuButton = document.getElementById('user-menu-button');
        const userMenuDropdown = document.getElementById('user-menu-dropdown');
        
        if (userMenuButton && userMenuDropdown) {
            userMenuButton.addEventListener('click', function(e) {
                e.stopPropagation();
                userMenuDropdown.classList.toggle('hidden');
            });
            
            document.addEventListener('click', function(e) {
                if (!userMenuButton.contains(e.target) && !userMenuDropdown.contains(e.target)) {
                    userMenuDropdown.classList.add('hidden');
                }
            });
        }
    })();

    async function loadDashboard() {
        try {
            // Load user profile
            const userResponse = await fetch(`${API_BASE}/auth/me`, {
                credentials: 'include',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                }
            });
            
            if (userResponse.ok) {
                const userData = await userResponse.json();
                if (userData.user) {
                    const user = userData.user;
                    const initials = (user.name || 'U').charAt(0).toUpperCase();
                    document.getElementById('user-initials').textContent = initials;
                }
            } else if (userResponse.status === 401 || userResponse.status === 403) {
                // Redirect to login if unauthorized
                window.location.href = '/login';
                return;
            }

            // Load applications
            let applications = [];
            let total = 0;
            
            try {
                const appsResponse = await fetch(`${API_BASE}/job-seeker/applications`, {
                    credentials: 'include',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                    }
                });

                if (appsResponse.ok) {
                    const appsData = await appsResponse.json();
                    applications = appsData.data || [];
                    total = appsData.total || 0;
                } else if (appsResponse.status === 404) {
                    // Job seeker profile not found - this is okay for new users
                    applications = [];
                    total = 0;
                } else if (appsResponse.status === 401 || appsResponse.status === 403) {
                    window.location.href = '/login';
                    return;
                } else {
                    console.warn('Applications API returned status:', appsResponse.status);
                    applications = [];
                    total = 0;
                }
            } catch (fetchError) {
                console.error('Error fetching applications:', fetchError);
                applications = [];
                total = 0;
            }
                
            // Calculate statistics
            const inReview = applications.filter(app => ['pending', 'reviewing', 'shortlisted'].includes(app.status)).length;
            const offers = applications.filter(app => app.status === 'hired' || app.status === 'accepted').length;
            const rejected = applications.filter(app => app.status === 'rejected').length;
            
            // Update stat cards
            document.getElementById('total-applications-value').textContent = total;
            document.getElementById('in-review-value').textContent = inReview;
            document.getElementById('offers-value').textContent = offers;
            document.getElementById('rejected-value').textContent = rejected;

            // Update recent activity
            const recentActivityList = document.getElementById('recent-activity-list');
            if (applications.length > 0) {
                recentActivityList.innerHTML = applications.slice(0, 4).map(app => {
                    const timeAgo = getTimeAgo(new Date(app.created_at));
                    const statusColor = getStatusDotColor(app.status);
                    return `
                        <div class="flex items-start space-x-3">
                            <div class="w-2 h-2 ${statusColor} rounded-full mt-2"></div>
                            <div class="flex-1">
                                <p class="text-sm text-gray-900">
                                    ${app.status === 'shortlisted' ? 'Interview scheduled' : 'Applied to'} ${app.job_advertisement?.title || 'Job'} (${app.job_advertisement?.company?.name || 'Company'})
                                </p>
                                <p class="text-xs text-gray-500 mt-1">${timeAgo}</p>
                            </div>
                        </div>
                    `;
                }).join('');
            } else {
                recentActivityList.innerHTML = '<p class="text-gray-500 text-center py-4">No recent activity</p>';
            }

            // Load achievements
            const achievementsList = document.getElementById('achievements-list');
            const achievements = [
                { icon: 'target', title: 'Profile Complete', description: 'Completed your profile 100%', achieved: true },
                { icon: 'rocket', title: 'First Application', description: 'Submitted your first job application', achieved: total > 0 },
                { icon: 'star', title: 'Active Seeker', description: 'Applied to 10+ jobs', achieved: total >= 10 },
            ];

            achievementsList.innerHTML = achievements.map(achievement => {
                const iconSvg = getAchievementIcon(achievement.icon);
                const achievedClass = achievement.achieved ? 'text-green-600' : 'text-gray-400';
                return `
                    <div class="flex items-start space-x-3">
                        <div class="${achievedClass}">${iconSvg}</div>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-900">${achievement.title}</p>
                            <p class="text-xs text-gray-500">${achievement.description}</p>
                        </div>
                        ${achievement.achieved ? '<svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>' : ''}
                    </div>
                `;
            }).join('');

            // Load profile completeness
            let profile = {};
            try {
                const profileResponse = await fetch(`${API_BASE}/job-seeker/profile`, {
                    credentials: 'include',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                    }
                });

                if (profileResponse.ok) {
                    const profileData = await profileResponse.json();
                    profile = profileData.data || {};
                } else if (profileResponse.status === 404) {
                    // Profile not found - use empty profile
                    profile = {};
                } else {
                    console.warn('Profile API returned status:', profileResponse.status);
                    profile = {};
                }
            } catch (fetchError) {
                console.error('Error fetching profile:', fetchError);
                profile = {};
            }
            
            const items = [
                { label: 'Basic Information', complete: !!(profile.first_name && profile.last_name) },
                { label: 'Resume Uploaded', complete: !!profile.cv_file_path },
                { label: 'Add Certifications', complete: false }, // Placeholder
            ];

            const completedCount = items.filter(item => item.complete).length;
            const completenessPercent = Math.round((completedCount / items.length) * 100);

            document.getElementById('profile-completeness-percent').textContent = completenessPercent + '%';
            document.getElementById('profile-completeness-bar').style.width = completenessPercent + '%';

            const completenessItems = document.getElementById('profile-completeness-items');
            completenessItems.innerHTML = items.map(item => `
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-700">${item.label}</span>
                    ${item.complete 
                        ? '<svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>'
                        : '<span class="text-xs text-orange-600">Pending</span>'
                    }
                </div>
            `).join('');

            // Skill Match Analytics (placeholder data)
            const skills = [
                { name: 'JavaScript', match: 92 },
                { name: 'React', match: 85 },
                { name: 'Node.js', match: 68 },
            ];
            const averageMatch = Math.round(skills.reduce((sum, skill) => sum + skill.match, 0) / skills.length);

            document.getElementById('average-match').textContent = averageMatch + '%';

            const skillMatchesList = document.getElementById('skill-matches-list');
            skillMatchesList.innerHTML = skills.map(skill => {
                const colorClass = skill.match >= 80 ? 'bg-green-500' : skill.match >= 60 ? 'bg-blue-500' : 'bg-yellow-500';
                return `
                    <div>
                        <div class="flex justify-between items-center mb-1">
                            <span class="text-sm font-medium text-gray-700">${skill.name}</span>
                            <span class="text-sm font-bold text-gray-900">${skill.match}%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="${colorClass} h-2 rounded-full transition-all duration-500" style="width: ${skill.match}%"></div>
                        </div>
                    </div>
                `;
            }).join('');

            // Hide loading, show content
            document.getElementById('dashboard-loading').classList.add('hidden');
            document.getElementById('dashboard-content').classList.remove('hidden');

        } catch (error) {
            console.error('Error loading dashboard:', error);
            // Show error but still display the dashboard with default values
            document.getElementById('dashboard-loading').classList.add('hidden');
            document.getElementById('dashboard-content').classList.remove('hidden');
            
            // Show error toast if available
            if (typeof window.showToast === 'function') {
                window.showToast('Some data could not be loaded. Please refresh the page.', 'error');
            }
        }
    }

    function getTimeAgo(date) {
        const seconds = Math.floor((new Date() - date) / 1000);
        if (seconds < 60) return 'just now';
        const minutes = Math.floor(seconds / 60);
        if (minutes < 60) return minutes + ' minute' + (minutes > 1 ? 's' : '') + ' ago';
        const hours = Math.floor(minutes / 60);
        if (hours < 24) return hours + ' hour' + (hours > 1 ? 's' : '') + ' ago';
        const days = Math.floor(hours / 24);
        return days + ' day' + (days > 1 ? 's' : '') + ' ago';
    }

    function getStatusDotColor(status) {
        const colors = {
            'pending': 'bg-blue-500',
            'reviewing': 'bg-blue-500',
            'shortlisted': 'bg-green-500',
            'hired': 'bg-green-500',
            'rejected': 'bg-red-500',
        };
        return colors[status] || 'bg-gray-500';
    }

    function getAchievementIcon(type) {
        const icons = {
            target: '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>',
            rocket: '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>',
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
            if (window.location.pathname === '/dashboard') {
                setTimeout(loadDashboard, 100);
            }
        });
    }
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.job-seeker', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\lysp\Downloads\Job Ad\resources\views/job-seeker/dashboard.blade.php ENDPATH**/ ?>