<!-- Left Sidebar -->
<aside class="w-64 bg-white fixed left-0 top-0 h-screen overflow-y-auto z-30 border-r border-gray-200 flex flex-col">
    <!-- Navigation Links -->
    <nav class="flex-1 px-4 py-6 space-y-2">
        @php
            $currentRoute = request()->route()->getName() ?? request()->path();
            $isDashboard = $currentRoute === 'dashboard' || request()->path() === '/dashboard';
            $isProfile = str_contains($currentRoute, 'profile') || str_contains(request()->path(), 'profile');
            $isApplications = str_contains($currentRoute, 'application') || str_contains(request()->path(), 'application');
            $isJobs = str_contains($currentRoute, 'jobs') && !str_contains($currentRoute, 'application');
            $isCompanies = str_contains($currentRoute, 'company') || str_contains($currentRoute, 'followed');
            $isNotifications = str_contains($currentRoute, 'notification');
            $isCareerTools = str_contains($currentRoute, 'career') || str_contains($currentRoute, 'tool');
            $isSupport = str_contains($currentRoute, 'support');
            $isSettings = str_contains($currentRoute, 'setting');
            
            $user = auth()->user();
            $userInitials = strtoupper(substr($user->first_name ?? '', 0, 1) . substr($user->last_name ?? '', 0, 1));
            if (empty($userInitials)) {
                $userInitials = strtoupper(substr($user->name ?? 'JD', 0, 2));
            }
            $userName = ($user->first_name ?? '') . ' ' . ($user->last_name ?? '');
            if (trim($userName) === '') {
                $userName = $user->name ?? 'John Doe';
            }
            $userEmail = $user->email ?? 'john.doe@email.com';
        @endphp

        <!-- Dashboard Overview - Active with gradient and shadow -->
        <a href="/dashboard" wire:navigate class="flex items-center space-x-3 px-4 py-3 rounded-lg transition {{ $isDashboard ? 'bg-gradient-to-r from-blue-500 to-cyan-400 text-white shadow-md' : 'text-gray-700 hover:bg-gray-50' }}">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
            </svg>
            <span class="font-medium">Dashboard Overview</span>
        </a>

        <!-- My Profile -->
        <a href="/job-seeker/profile" wire:navigate class="flex items-center space-x-3 px-4 py-3 rounded-lg transition {{ $isProfile ? 'bg-gradient-to-r from-blue-500 to-cyan-400 text-white shadow-md' : 'text-gray-700 hover:bg-gray-50' }}">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
            </svg>
            <span class="{{ $isProfile ? 'font-medium' : '' }}">My Profile</span>
        </a>

        <!-- Job Applications -->
        <a href="/job-seeker/applications" wire:navigate class="flex items-center space-x-3 px-4 py-3 rounded-lg transition {{ $isApplications ? 'bg-gradient-to-r from-blue-500 to-cyan-400 text-white shadow-md' : 'text-gray-700 hover:bg-gray-50' }}">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
            </svg>
            <span class="{{ $isApplications ? 'font-medium' : '' }}">Job Applications</span>
        </a>

        <!-- Job Discovery -->
        <a href="/jobs" wire:navigate class="flex items-center space-x-3 px-4 py-3 rounded-lg transition {{ $isJobs ? 'bg-gradient-to-r from-blue-500 to-cyan-400 text-white shadow-md' : 'text-gray-700 hover:bg-gray-50' }}">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
            <span class="{{ $isJobs ? 'font-medium' : '' }}">Job Discovery</span>
        </a>

        <!-- Companies -->
        <a href="/job-seeker/followed-companies" wire:navigate class="flex items-center space-x-3 px-4 py-3 rounded-lg transition {{ $isCompanies ? 'bg-gradient-to-r from-blue-500 to-cyan-400 text-white shadow-md' : 'text-gray-700 hover:bg-gray-50' }}">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
            </svg>
            <span class="{{ $isCompanies ? 'font-medium' : '' }}">Companies</span>
        </a>

        <!-- Notifications -->
        <a href="/job-seeker/notifications" wire:navigate class="flex items-center space-x-3 px-4 py-3 rounded-lg transition {{ $isNotifications ? 'bg-gradient-to-r from-blue-500 to-cyan-400 text-white shadow-md' : 'text-gray-700 hover:bg-gray-50' }}">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
            </svg>
            <span class="{{ $isNotifications ? 'font-medium' : '' }}">Notifications</span>
        </a>

        <!-- Career Tools -->
        <a href="#" class="flex items-center space-x-3 px-4 py-3 rounded-lg transition {{ $isCareerTools ? 'bg-gradient-to-r from-blue-500 to-cyan-400 text-white shadow-md' : 'text-gray-700 hover:bg-gray-50' }}">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
            </svg>
            <span class="{{ $isCareerTools ? 'font-medium' : '' }}">Career Tools</span>
        </a>

        <!-- Support -->
        <a href="#" class="flex items-center space-x-3 px-4 py-3 rounded-lg transition {{ $isSupport ? 'bg-gradient-to-r from-blue-500 to-cyan-400 text-white shadow-md' : 'text-gray-700 hover:bg-gray-50' }}">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span class="{{ $isSupport ? 'font-medium' : '' }}">Support</span>
        </a>

        <!-- Settings -->
        <a href="#" class="flex items-center space-x-3 px-4 py-3 rounded-lg transition {{ $isSettings ? 'bg-gradient-to-r from-blue-500 to-cyan-400 text-white shadow-md' : 'text-gray-700 hover:bg-gray-50' }}">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
            </svg>
            <span class="{{ $isSettings ? 'font-medium' : '' }}">Settings</span>
        </a>
    </nav>

    <!-- User Profile Section at Bottom -->
    <div class="px-4 py-4 border-t border-gray-200">
        <div class="flex items-center space-x-3">
            <div class="w-10 h-10 rounded-full bg-blue-600 flex items-center justify-center flex-shrink-0">
                <span class="text-sm font-bold text-white">{{ $userInitials }}</span>
            </div>
            <div class="flex-1 min-w-0">
                <div class="text-sm font-semibold text-gray-900 truncate">{{ $userName }}</div>
                <div class="text-xs text-gray-500 truncate">{{ $userEmail }}</div>
            </div>
        </div>
    </div>
</aside>
