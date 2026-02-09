<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'JobHub - Find Your Next Career Opportunity' }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

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
<body class="font-sans antialiased bg-white">
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
    <header class="bg-white shadow-sm sticky top-0 z-50">
        <!-- Top Navigation -->
        <nav class="border-b border-gray-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-16">
                    <!-- Logo -->
                    <div class="flex items-center">
                        <a href="{{ route('landing') }}" wire:navigate class="flex items-center space-x-2">
                            <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                            <span class="text-2xl font-bold text-gray-900">JobHub</span>
                        </a>
                    </div>

                    <!-- Main Navigation -->
                    <div class="hidden md:flex items-center space-x-6">
                        <a href="{{ route('jobs.index') }}" wire:navigate class="text-gray-700 hover:text-blue-600 transition">Search Jobs</a>
                        <a href="#" class="text-gray-700 hover:text-blue-600 transition">Search Tenders</a>
                        <a href="#" class="text-gray-700 hover:text-blue-600 transition">Job Categories</a>
                        <a href="#" class="text-gray-700 hover:text-blue-600 transition">Companies</a>
                    </div>

                    <!-- Right Side Actions -->
                    <div class="flex items-center space-x-4">
                        <!-- Dark Mode Toggle -->
                        <button type="button" class="p-2 text-gray-600 hover:text-gray-900 transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
                            </svg>
                        </button>
                        <!-- Login/Signup Button -->
                        @if(auth()->check())
                            <a href="{{ route('dashboard') }}" wire:navigate class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                                Dashboard
                            </a>
                        @else
                            <a href="{{ route('login') }}" wire:navigate class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                                Login/Signup
                            </a>
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
    </script>
    
    @stack('scripts')

    <!-- Footer -->
    <footer class="bg-gray-900 text-white mt-20">
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
                        <li><a href="#" class="hover:text-white transition">Pricing</a></li>
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
