@php
    $user = Auth::user();
    $employer = $user->employer ?? null;
    $company = null;
    if ($employer && $employer->company_id) {
        $company = \App\Models\Company::find($employer->company_id);
    }
    $companyName = $company->name ?? $employer->company_name ?? 'Company';
    $companyInitials = strtoupper(substr($companyName, 0, 2));
    if (strlen($companyName) > 0 && strpos($companyName, ' ') !== false) {
        $words = explode(' ', $companyName);
        $companyInitials = strtoupper(substr($words[0], 0, 1) . (isset($words[1]) ? substr($words[1], 0, 1) : ''));
    }
@endphp

<!-- Top Bar -->
<div class="bg-white border-b border-gray-200 sticky top-0 z-40 ml-64">
    <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            <!-- Left: Search -->
            <div class="flex-1 max-w-2xl">
                <div class="relative">
                    <input 
                        type="text" 
                        placeholder="Search jobs, applicants, campaigns..." 
                        class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    >
                    <svg class="absolute left-3 top-2.5 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
            </div>

            <!-- Right: Coins, Dark Mode, Notifications, User -->
            <div class="flex items-center space-x-4 ml-6">
                <!-- Coins - Orange rectangular badge -->
                <div class="flex items-center space-x-1.5 bg-orange-500 px-3 py-1.5 rounded-md">
                    <span id="coin-balance" class="font-semibold text-white text-sm">{{ number_format($employer->coin_balance ?? 0) }}</span>
                    <span class="text-white text-xs">coins</span>
                    <svg class="w-3 h-3 text-white ml-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </div>

                <!-- Dark Mode Toggle -->
                <button class="p-2 text-gray-600 hover:text-gray-900 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
                    </svg>
                </button>

                <!-- Notifications -->
                <div class="relative" id="notification-container">
                    <button id="notification-button" type="button" class="p-2 text-gray-600 hover:text-gray-900 transition relative" onclick="event.stopPropagation();">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                        </svg>
                        <span id="notification-badge" class="hidden absolute top-0 right-0 w-5 h-5 bg-red-500 text-white text-xs rounded-full flex items-center justify-center font-semibold">0</span>
                    </button>
                    
                    <!-- Notification Dropdown -->
                    <div id="notification-dropdown" class="hidden absolute right-0 mt-2 w-96 bg-white rounded-lg shadow-xl border border-gray-200 z-[100] max-h-96 overflow-hidden flex flex-col">
                        <div class="px-4 py-3 border-b border-gray-200 flex items-center justify-between bg-white">
                            <h3 class="font-semibold text-gray-900">Notifications</h3>
                            <button id="mark-all-read-btn" type="button" class="text-sm text-blue-600 hover:text-blue-800" onclick="event.stopPropagation();">Mark all read</button>
                        </div>
                        <div id="notification-list" class="overflow-y-auto flex-1 bg-white">
                            <div class="p-4 text-center text-gray-500">
                                <div class="inline-block animate-spin rounded-full h-6 w-6 border-b-2 border-blue-600 mb-2"></div>
                                <p class="text-sm">Loading notifications...</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- User Info with Dropdown -->
                <div class="relative" id="user-menu-container">
                    <button id="user-menu-button" class="flex items-center space-x-3 hover:opacity-80 transition cursor-pointer">
                        <div class="text-right">
                            <div id="company-name" class="font-semibold text-gray-900">{{ $companyName }}</div>
                            <div class="text-sm text-gray-500">Admin</div>
                        </div>
                        <div class="w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center">
                            <span id="company-initials" class="text-white font-semibold">{{ $companyInitials }}</span>
                        </div>
                    </button>
                    
                    <!-- Dropdown Menu -->
                    <div id="user-menu-dropdown" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 py-1 z-50">
                        <a href="{{ route('employer.dashboard') }}" wire:navigate class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Dashboard</a>
                        <a href="{{ route('employer.company-profile') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Company Profile</a>
                        <a href="{{ route('employer.tenders.index') }}" wire:navigate class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Tenders</a>
                        <div class="border-t border-gray-200 my-1"></div>
                        <form method="POST" action="{{ route('logout') }}" id="logout-form">
                            @csrf
                            <button type="submit" class="w-full text-left block px-4 py-2 text-sm text-red-600 hover:bg-red-50">Logout</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// User menu dropdown toggle - make sure it works globally
(function() {
    function initUserMenu() {
        const userMenuButton = document.getElementById('user-menu-button');
        const userMenuDropdown = document.getElementById('user-menu-dropdown');
        
        if (userMenuButton && userMenuDropdown) {
            // Remove existing listeners to prevent duplicates
            const newButton = userMenuButton.cloneNode(true);
            userMenuButton.parentNode.replaceChild(newButton, userMenuButton);
            
            const newDropdown = userMenuDropdown.cloneNode(true);
            userMenuDropdown.parentNode.replaceChild(newDropdown, userMenuDropdown);
            
            // Get fresh references
            const button = document.getElementById('user-menu-button');
            const dropdown = document.getElementById('user-menu-dropdown');
            
            button.addEventListener('click', function(e) {
                e.stopPropagation();
                dropdown.classList.toggle('hidden');
            });
            
            // Close dropdown when clicking outside
            document.addEventListener('click', function(e) {
                const container = document.getElementById('user-menu-container');
                if (container && !container.contains(e.target)) {
                    dropdown.classList.add('hidden');
                }
            });
        }
    }
    
    // Initialize on page load
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initUserMenu);
    } else {
        initUserMenu();
    }
    
    // Re-initialize on Livewire navigation
    if (typeof Livewire !== 'undefined') {
        document.addEventListener('livewire:navigated', function() {
            setTimeout(initUserMenu, 100);
        });
    }
})();
</script>
