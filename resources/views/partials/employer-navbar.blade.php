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
<div class="h-16 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 sticky top-0 z-40 ml-64 transition-colors duration-200">
    <div class="flex items-center justify-between h-16 px-6 w-full">
            <!-- Left: Search -->
            <div class="flex items-center gap-4 flex-1 max-w-xl">
                <div class="relative flex-1">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    <input
                        type="text"
                        placeholder="Search jobs, applicants, campaigns..."
                        class="w-full pl-10 pr-4 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded text-sm text-gray-900 dark:text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    >
                </div>
            </div>

            <!-- Right: Coins, Dark Mode, Notifications, User -->
            <div class="flex items-center gap-4">
                <!-- Coins -->
                <div class="flex items-center gap-2 px-4 py-2 text-white rounded shadow-lg" style="background: linear-gradient(to right, #f59e0b, #f97316);">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M10 18a8 8 0 100-16 8 8 0 000 16zM7 9a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3z"/></svg>
                    <span id="coin-balance" class="font-bold">{{ number_format($employer->coin_balance ?? 0) }}</span>
                    <span class="text-sm opacity-90">coins</span>
                </div>

                @include('partials.theme-toggle')

                <!-- Notifications -->
                <div class="relative" id="notification-container">
                    <button id="notification-button" type="button" class="relative p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded transition-colors" onclick="event.stopPropagation();">
                        <svg class="w-5 h-5 text-gray-700 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                        </svg>
                        <span id="notification-badge" class="hidden absolute -top-0.5 -right-0.5 min-w-4 h-4 px-1 bg-red-500 text-white text-[10px] font-bold rounded-full items-center justify-center" style="display:none;"></span>
                    </button>
                    
                    <!-- Notification Dropdown -->
                    <div id="notification-dropdown" class="hidden absolute right-0 mt-2 w-96 bg-white dark:bg-gray-800 rounded-lg shadow-xl border border-gray-200 dark:border-gray-700 z-[100] max-h-96 overflow-hidden flex flex-col">
                        <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between bg-white dark:bg-gray-800">
                            <h3 class="font-semibold text-gray-900 dark:text-white">Notifications</h3>
                            <button id="mark-all-read-btn" type="button" class="text-sm text-blue-600 dark:text-blue-400 hover:text-blue-800" onclick="event.stopPropagation();">Mark all read</button>
                        </div>
                        <div id="notification-list" class="overflow-y-auto flex-1 bg-white dark:bg-gray-800">
                            <div class="p-4 text-center text-gray-500 dark:text-gray-400">
                                <div class="inline-block animate-spin rounded-full h-6 w-6 border-b-2 border-blue-600 mb-2"></div>
                                <p class="text-sm">Loading notifications...</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- User Info with Dropdown -->
                <div class="relative flex items-center gap-3 pl-4 border-l border-gray-200 dark:border-gray-700" id="user-menu-container">
                    <button id="user-menu-button" class="flex items-center gap-3 hover:opacity-80 transition cursor-pointer">
                        <div class="text-right">
                            <p id="company-name" class="text-sm font-medium text-gray-900 dark:text-white">{{ $companyName }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Admin</p>
                        </div>
                        <div class="w-10 h-10 rounded-full flex items-center justify-center text-white font-bold" style="background: linear-gradient(to bottom right, #2563eb, #06b6d4);">
                            <span id="company-initials">{{ $companyInitials }}</span>
                        </div>
                    </button>
                    
                    <!-- Dropdown Menu -->
                    <div id="user-menu-dropdown" class="hidden absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 py-1 z-50">
                        <a href="{{ route('employer.dashboard') }}" wire:navigate class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">Dashboard</a>
                        <a href="{{ route('employer.company-profile') }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">Company Profile</a>
                        <a href="{{ route('employer.tenders.index') }}" wire:navigate class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">Tenders</a>
                        <div class="border-t border-gray-200 dark:border-gray-700 my-1"></div>
                        <form method="POST" action="{{ route('logout') }}" id="logout-form">
                            @csrf
                            <button type="submit" class="w-full text-left block px-4 py-2 text-sm text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20">Logout</button>
                        </form>
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
