<!-- Left Sidebar -->
<aside class="w-64 bg-white h-screen fixed left-0 top-0 border-r border-gray-200 flex flex-col overflow-y-auto z-30">
    <!-- Top Branding Section -->
    <div class="px-6 py-6 border-b border-gray-200">
        <div class="flex items-center space-x-3">
            <!-- Logo: Square blue icon with 3 briefcase shapes -->
            <div class="w-10 h-10 rounded-lg bg-blue-600 flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <!-- Three briefcase shapes arranged horizontally -->
                    <path d="M3 7h4v8H3V7z" fill="currentColor"/>
                    <path d="M10 7h4v8h-4V7z" fill="currentColor"/>
                    <path d="M17 7h4v8h-4V7z" fill="currentColor"/>
                </svg>
            </div>
            <div>
                <div class="text-lg font-bold text-blue-600">JobHub</div>
                <div class="text-xs text-gray-500">Employer Portal</div>
            </div>
        </div>
    </div>

    <!-- Navigation Links -->
    <nav class="flex-1 px-4 py-4 space-y-1">
        @php
            $currentRoute = request()->route() ? request()->route()->getName() : request()->path();
            $currentPath = request()->path();
            $isJobApplicantsPage = $currentRoute === 'employer.jobs.applicants' || preg_match('#^employer/jobs/\d+/applicants$#', $currentPath);
            $isDashboard = str_contains($currentRoute, 'employer.dashboard') || str_contains($currentPath, '/employer/dashboard');
            $isJobs = !$isJobApplicantsPage && (str_contains($currentRoute, 'employer.jobs') || str_contains($currentPath, '/employer/jobs'));
            $isApplications = str_contains($currentRoute, 'employer.applications') || str_contains($currentPath, '/employer/applications');
            $isCompanyProfile = str_contains($currentRoute, 'employer.company-profile') || str_contains($currentPath, '/employer/company-profile');
            $isCampaignsList = $currentRoute === 'employer.campaigns.index' || $isJobApplicantsPage;
            $isCampaignsCreate = $currentRoute === 'employer.campaigns.create';
            $isTenders = str_contains($currentRoute, 'employer.tenders') || str_contains($currentPath, '/employer/tenders');
        @endphp

        <!-- Dashboard (Active with gradient) -->
        <a href="/employer/dashboard" wire:navigate class="flex items-center space-x-3 px-4 py-3 rounded-lg transition {{ $isDashboard ? 'bg-gradient-to-r from-blue-500 to-cyan-400 text-white shadow-md' : 'text-gray-700 hover:bg-gray-50' }}">
            <svg class="w-5 h-5 {{ $isDashboard ? 'text-white' : 'text-gray-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
            </svg>
            <span class="{{ $isDashboard ? 'font-semibold text-white' : 'font-medium' }}">Dashboard</span>
        </a>

        <!-- Company Profile -->
        <a href="{{ route('employer.company-profile') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg transition {{ $isCompanyProfile ? 'bg-gradient-to-r from-blue-500 to-cyan-400 text-white shadow-md' : 'text-gray-700 hover:bg-gray-50' }}">
            <svg class="w-5 h-5 {{ $isCompanyProfile ? 'text-white' : 'text-gray-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
            </svg>
            <span class="{{ $isCompanyProfile ? 'font-semibold text-white' : 'font-medium' }}">Company Profile</span>
        </a>

        <!-- Job Listings -->
        <a href="{{ route('employer.jobs.index') }}" wire:navigate class="flex items-center space-x-3 px-4 py-3 rounded-lg transition {{ $isJobs ? 'bg-gradient-to-r from-blue-500 to-cyan-400 text-white shadow-md' : 'text-gray-700 hover:bg-gray-50' }}">
            <svg class="w-5 h-5 {{ $isJobs ? 'text-white' : 'text-gray-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
            </svg>
            <span class="{{ $isJobs ? 'font-semibold text-white' : 'font-medium' }}">Job Listings</span>
        </a>

        <!-- Applicants -->
        <a href="{{ route('employer.applications.index') }}" wire:navigate class="flex items-center space-x-3 px-4 py-3 rounded-lg transition {{ $isApplications ? 'bg-gradient-to-r from-blue-500 to-cyan-400 text-white shadow-md' : 'text-gray-700 hover:bg-gray-50' }}">
            <svg class="w-5 h-5 {{ $isApplications ? 'text-white' : 'text-gray-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
            </svg>
            <span class="{{ $isApplications ? 'font-semibold text-white' : 'font-medium' }}">Applicants</span>
        </a>

        <!-- Analytics -->
        <a href="#" class="flex items-center space-x-3 px-4 py-3 text-gray-700 hover:bg-gray-50 rounded-lg transition">
            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
            </svg>
            <span class="font-medium">Analytics</span>
        </a>

        <!-- Tenders -->
        <a href="{{ route('employer.tenders.index') }}" wire:navigate class="flex items-center space-x-3 px-4 py-3 rounded-lg transition {{ $isTenders ? 'bg-gradient-to-r from-blue-500 to-cyan-400 text-white shadow-md' : 'text-gray-700 hover:bg-gray-50' }}">
            <svg class="w-5 h-5 {{ $isTenders ? 'text-white' : 'text-gray-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <span class="{{ $isTenders ? 'font-semibold text-white' : 'font-medium' }}">Tenders</span>
        </a>

        <!-- Job Campaigns (list) -->
        <a href="{{ route('employer.campaigns.index') }}" wire:navigate class="flex items-center space-x-3 px-4 py-3 rounded-lg transition {{ $isCampaignsList ? 'bg-gradient-to-r from-blue-500 to-cyan-400 text-white shadow-md' : 'text-gray-700 hover:bg-gray-50' }}">
            <svg class="w-5 h-5 {{ $isCampaignsList ? 'text-white' : 'text-gray-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"></path>
            </svg>
            <span class="{{ $isCampaignsList ? 'font-semibold text-white' : 'font-medium' }}">Job Campaigns</span>
        </a>

        <!-- Create Campaign -->
        <a href="{{ route('employer.campaigns.create') }}" wire:navigate class="flex items-center space-x-3 px-4 py-3 rounded-lg transition {{ $isCampaignsCreate ? 'bg-gradient-to-r from-blue-500 to-cyan-400 text-white shadow-md' : 'text-gray-700 hover:bg-gray-50' }}">
            <svg class="w-5 h-5 {{ $isCampaignsCreate ? 'text-white' : 'text-gray-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
            </svg>
            <span class="{{ $isCampaignsCreate ? 'font-semibold text-white' : 'font-medium' }}">Create Campaign</span>
        </a>

        <!-- Coins & Billing -->
        <a href="#" class="flex items-center space-x-3 px-4 py-3 text-gray-700 hover:bg-gray-50 rounded-lg transition">
            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v12m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <span class="font-medium">Coins & Billing</span>
        </a>

        <!-- Invoices -->
        <a href="#" class="flex items-center space-x-3 px-4 py-3 text-gray-700 hover:bg-gray-50 rounded-lg transition">
            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <span class="font-medium">Invoices</span>
        </a>

        <!-- Team Management -->
        <a href="#" class="flex items-center space-x-3 px-4 py-3 text-gray-700 hover:bg-gray-50 rounded-lg transition">
            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
            </svg>
            <span class="font-medium">Team Management</span>
        </a>
    </nav>

</aside>
