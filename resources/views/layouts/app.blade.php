<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'JobHub - Find Your Next Career Opportunity' }}</title>

    @include('partials.theme-head')

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    
    <style>
        /* Page transition loading indicator */
        [wire\:navigate] {
            cursor: pointer;
        }
        
        /* Loading bar at top of page */
        .nprogress-custom-parent {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            z-index: 9999;
        }
        
        /* Smooth page transitions */
        [wire\:loading] {
            opacity: 0.6;
            pointer-events: none;
            transition: opacity 0.2s ease;
        }
    </style>
</head>
<body class="font-sans antialiased bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 transition-colors duration-200">
    <!-- Loading Indicator for SPA Navigation -->
    <div id="page-loading" class="fixed top-0 left-0 right-0 h-1 bg-gradient-to-r from-pink-500 via-blue-500 to-pink-500 z-[9999] hidden" style="animation: loading-bar 1s ease-in-out infinite;">
    </div>
    <style>
        @keyframes loading-bar {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(100%); }
        }
    </style>

    <!-- Header -->
    <header class="bg-white dark:bg-gray-800 shadow-sm dark:shadow-none sticky top-0 z-50 transition-colors duration-200">
        <!-- Top Navigation -->
        <nav class="border-b border-gray-200 dark:border-gray-700">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-16">
                    <!-- Logo -->
                    <div class="flex items-center">
                        <a href="{{ route('landing') }}" wire:navigate class="flex items-center space-x-2">
                            <svg class="w-8 h-8 text-blue-600 dark:text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                            <span class="text-2xl font-bold text-gray-900 dark:text-white">JobHub</span>
                        </a>
                    </div>

                    <!-- Main Navigation -->
                    <div class="hidden md:flex items-center space-x-6">
                        <a href="{{ route('jobs.index') }}" wire:navigate class="text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-cyan-400 transition">Search Jobs</a>
                        <a href="{{ route('tenders.index') }}" wire:navigate class="text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-cyan-400 transition">Tenders</a>
                        <a href="{{ route('categories.index') }}" wire:navigate.hover class="text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-cyan-400 transition">Job Categories</a>
                        <a href="{{ route('companies.index') }}" wire:navigate class="text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-cyan-400 transition">Companies</a>
                        <a href="{{ route('pricing.index') }}" wire:navigate class="text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-cyan-400 transition">Pricing</a>
                    </div>

                    <!-- Right Side Actions -->
                    <div class="flex items-center space-x-4">
                        @include('partials.theme-toggle')
                        <!-- Login/Register Button -->
                        @if(auth()->check())
                            <a href="{{ route('dashboard') }}" wire:navigate class="bg-gradient-to-r from-blue-500 to-cyan-400 text-white px-4 py-2 rounded-lg hover:from-blue-600 hover:to-cyan-500 shadow-md transition">
                                Dashboard
                            </a>
                        @else
                            <button onclick="window.openAuthModal('login')" class="bg-gradient-to-r from-blue-500 to-cyan-400 text-white px-4 py-2 rounded-lg hover:from-blue-600 hover:to-cyan-500 shadow-md transition">
                                Login/Register
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </nav>
    </header>

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    @livewireScripts
    
    <!-- SPA Navigation Helper Script -->
    <script>
        window.IS_AUTHENTICATED = @json(auth()->check());
        window.AUTH_USER = @json(auth()->check() ? auth()->user() : null);

        // Show loading indicator during navigation
        document.addEventListener('livewire:navigate', () => {
            document.getElementById('page-loading').classList.remove('hidden');
        });
        
        document.addEventListener('livewire:navigated', () => {
            document.getElementById('page-loading').classList.add('hidden');
        });
        
        // Helper function for programmatic SPA navigation
        window.navigateTo = function(url) {
            if (typeof Livewire !== 'undefined' && Livewire.navigate) {
                Livewire.navigate(url);
            } else {
                window.location.href = url;
            }
        };
        
        // Fallback for auth modal - will be overridden by auth-modal.js if loaded
        window.openAuthModal = window.openAuthModal || function(tab = 'login') {
            const modal = document.getElementById('authModal');
            if (!modal) {
                console.error('Auth modal not found. Make sure the modal HTML is included.');
                return;
            }
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.style.overflow = 'hidden';
            
            // Basic tab switching
            const loginTab = document.getElementById('loginTab');
            const signUpTab = document.getElementById('signUpTab');
            const loginForm = document.getElementById('loginForm');
            const registerForm = document.getElementById('registerForm');
            const userTypeSelection = document.getElementById('userTypeSelection');
            
            if (tab === 'login') {
                if (loginTab) {
                    loginTab.classList.add('bg-blue-600', 'text-white');
                    loginTab.classList.remove('bg-gray-100', 'text-gray-700');
                }
                if (signUpTab) {
                    signUpTab.classList.remove('bg-blue-600', 'text-white');
                    signUpTab.classList.add('bg-gray-100', 'text-gray-700');
                }
                if (loginForm) loginForm.classList.remove('hidden');
                if (registerForm) registerForm.classList.add('hidden');
                if (userTypeSelection) userTypeSelection.classList.add('hidden');
            } else {
                if (loginTab) {
                    loginTab.classList.remove('bg-blue-600', 'text-white');
                    loginTab.classList.add('bg-gray-100', 'text-gray-700');
                }
                if (signUpTab) {
                    signUpTab.classList.add('bg-blue-600', 'text-white');
                    signUpTab.classList.remove('bg-gray-100', 'text-gray-700');
                }
                if (loginForm) loginForm.classList.add('hidden');
                if (registerForm) registerForm.classList.remove('hidden');
                if (userTypeSelection) userTypeSelection.classList.remove('hidden');
            }
        };
    </script>
    
    @stack('scripts')

    <!-- Auth Modal -->
    @include('partials.auth-modal')

    <!-- Apply Modal -->
    @include('partials.apply-modal')

    <!-- Footer -->
    <footer class="bg-gray-900 text-white mt-20 hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div>
                    <a href="{{ route('landing') }}" wire:navigate>
                        <h3 class="text-lg font-semibold mb-4">JobHub</h3>
                    </a>
                    <p class="text-gray-400 text-sm">Connecting talent with opportunity.</p>
                </div>
                <div>
                    <h4 class="font-semibold mb-4">For Job Seekers</h4>
                    <ul class="space-y-2 text-sm text-gray-400">
                        <li><a href="{{ route('jobs.index') }}" wire:navigate class="hover:text-white transition">Browse Jobs</a></li>
                        <li><a href="{{ route('register') }}" wire:navigate class="hover:text-white transition">Create Profile</a></li>
                        <li><a href="#" class="hover:text-white transition">Career Resources</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-semibold mb-4">For Employers</h4>
                    <ul class="space-y-2 text-sm text-gray-400">
                        <li><a href="#" class="hover:text-white transition">Post a Job</a></li>
                        <li><a href="#" class="hover:text-white transition">Browse Candidates</a></li>
                        <li><a href="{{ route('pricing.index') }}" wire:navigate class="hover:text-white transition">Pricing</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-semibold mb-4">Support</h4>
                    <ul class="space-y-2 text-sm text-gray-400">
                        <li><a href="#" class="hover:text-white transition">Help Center</a></li>
                        <li><a href="#" class="hover:text-white transition">Contact Us</a></li>
                        <li><a href="#" class="hover:text-white transition">FAQ</a></li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-800 mt-8 pt-8 text-center text-sm text-gray-400">
                <p>&copy; {{ date('Y') }} JobHub. All rights reserved.</p>
            </div>
        </div>
    </footer>
</body>
</html>
