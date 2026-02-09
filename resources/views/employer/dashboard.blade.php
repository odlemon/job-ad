@extends('layouts.employer')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Top Bar -->
    <div class="bg-white border-b border-gray-200 sticky top-0 z-40">
        <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <!-- Left: Logo and Portal Name -->
                <div class="flex items-center space-x-4">
                    <a href="/" wire:navigate class="text-2xl font-bold text-gray-900">JobHub</a>
                    <span class="text-gray-500">Employer Portal</span>
                </div>

                <!-- Center: Search -->
                <div class="flex-1 max-w-2xl mx-8">
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

                <!-- Right: Coins, Notifications, Dark Mode, User -->
                <div class="flex items-center space-x-4">
                    <!-- Coins -->
                    <div class="flex items-center space-x-2 bg-yellow-50 px-4 py-2 rounded-lg">
                        <svg class="w-5 h-5 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z"></path>
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.613 1.242.919 2.12 1.082v1.88a1 1 0 102 0v-1.88c.878-.163 1.558-.47 2.12-1.082a1 1 0 10-1.51-1.31c-.163.187-.452.377-.843.504v-1.941a4.535 4.535 0 001.676-.662C13.398 9.765 14 8.99 14 8c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 5.092V4.151z" clip-rule="evenodd"></path>
                        </svg>
                        <span id="coin-balance" class="font-semibold text-gray-900">0</span>
                        <span class="text-gray-600">coins</span>
                    </div>

                    <!-- Notifications -->
                    <button class="p-2 text-gray-600 hover:text-gray-900 transition relative">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                        </svg>
                        <span class="absolute top-0 right-0 w-2 h-2 bg-red-500 rounded-full"></span>
                    </button>

                    <!-- Dark Mode Toggle -->
                    <button class="p-2 text-gray-600 hover:text-gray-900 transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
                        </svg>
                    </button>

                    <!-- User Info with Dropdown -->
                    <div class="relative" id="user-menu-container">
                        <button id="user-menu-button" class="flex items-center space-x-3 hover:opacity-80 transition cursor-pointer">
                            <div class="text-right">
                                <div id="company-name" class="font-semibold text-gray-900">Company</div>
                                <div class="text-sm text-gray-500">Admin</div>
                            </div>
                            <div class="w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center">
                                <span id="company-initials" class="text-white font-semibold">C</span>
                            </div>
                        </button>
                        
                        <!-- Dropdown Menu -->
                        <div id="user-menu-dropdown" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 py-1 z-50">
                            <a href="/employer/dashboard" wire:navigate class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Dashboard</a>
                            <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Settings</a>
                            <div class="border-t border-gray-200 my-1"></div>
                            <a href="{{ route('logout') }}" class="block px-4 py-2 text-sm text-red-600 hover:bg-red-50">Logout</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="flex">
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
                        <div class="text-xs text-gray-500">Employer Portal</div>
                    </div>
                </div>
            </div>

            <!-- Navigation Links -->
            <nav class="flex-1 px-4 py-4 space-y-1">
                <!-- Dashboard (Active) -->
                <a href="/employer/dashboard" wire:navigate class="flex items-center space-x-3 px-4 py-3 relative group">
                    <div class="absolute left-0 top-0 bottom-0 w-1 bg-blue-600 rounded-r-full"></div>
                    <div class="absolute left-0 top-0 bottom-0 right-0 bg-blue-600 rounded-r-lg -z-10"></div>
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                    </svg>
                    <span class="text-white font-medium">Dashboard</span>
                </a>

                <!-- Company Profile -->
                <a href="#" class="flex items-center space-x-3 px-4 py-3 text-gray-700 hover:bg-gray-50 rounded-lg transition">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                    <span>Company Profile</span>
                </a>

                <!-- Job Listings -->
                <a href="#" class="flex items-center space-x-3 px-4 py-3 text-gray-700 hover:bg-gray-50 rounded-lg transition">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                    <span>Job Listings</span>
                </a>

                <!-- Applicants -->
                <a href="#" class="flex items-center space-x-3 px-4 py-3 text-gray-700 hover:bg-gray-50 rounded-lg transition">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                    <span>Applicants</span>
                </a>

                <!-- Analytics -->
                <a href="#" class="flex items-center space-x-3 px-4 py-3 text-gray-700 hover:bg-gray-50 rounded-lg transition">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    <span>Analytics</span>
                </a>

                <!-- Job Campaigns -->
                <a href="#" class="flex items-center space-x-3 px-4 py-3 text-gray-700 hover:bg-gray-50 rounded-lg transition">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"></path>
                    </svg>
                    <span>Job Campaigns</span>
                </a>

                <!-- Create Campaign -->
                <a href="#" class="flex items-center space-x-3 px-4 py-3 text-gray-700 hover:bg-gray-50 rounded-lg transition">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                    </svg>
                    <span>Create Campaign</span>
                </a>

                <!-- Coins & Billing -->
                <a href="#" class="flex items-center space-x-3 px-4 py-3 text-gray-700 hover:bg-gray-50 rounded-lg transition">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span>Coins & Billing</span>
                </a>

                <!-- Invoices -->
                <a href="#" class="flex items-center space-x-3 px-4 py-3 text-gray-700 hover:bg-gray-50 rounded-lg transition">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <span>Invoices</span>
                </a>

                <!-- Team Management -->
                <a href="#" class="flex items-center space-x-3 px-4 py-3 text-gray-700 hover:bg-gray-50 rounded-lg transition">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    <span>Team Management</span>
                </a>
            </nav>

            <!-- Bottom Section (Settings & Exit) -->
            <div class="px-4 py-4 border-t border-gray-200 space-y-1 mt-auto">
                <!-- Settings -->
                <a href="#" class="flex items-center space-x-3 px-4 py-3 text-gray-700 hover:bg-gray-50 rounded-lg transition">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    <span>Settings</span>
                </a>

                <!-- Exit Portal -->
                <a href="{{ route('logout') }}" class="flex items-center space-x-3 px-4 py-3 text-red-600 hover:bg-red-50 rounded-lg transition">
                    <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                    </svg>
                    <span>Exit Portal</span>
                </a>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 p-8">
            <div class="max-w-7xl mx-auto">
                <!-- Dashboard Header -->
                <div class="mb-8">
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">Dashboard Overview</h1>
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
                            <h2 class="text-xl font-bold text-gray-900 mb-4">Recent Job Postings</h2>
                            <div id="recent-jobs-list" class="space-y-4">
                                <!-- Jobs will be loaded here -->
                            </div>
                        </div>

                        <!-- Recent Applicants -->
                        <div class="bg-white rounded-xl shadow-sm p-6">
                            <h2 class="text-xl font-bold text-gray-900 mb-4">Recent Applicants</h2>
                            <div id="recent-applicants-list" class="space-y-4">
                                <!-- Applicants will be loaded here -->
                            </div>
                        </div>
                    </div>

                    <!-- Summary Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Pending Reviews -->
                        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 text-white">
                            <div class="flex items-center justify-between mb-4">
                                <div class="p-3 bg-white bg-opacity-20 rounded-lg">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="text-4xl font-bold mb-2" id="pending-reviews-value">0</div>
                            <div class="text-blue-100">Applications waiting for review</div>
                        </div>

                        <!-- Shortlisted -->
                        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-lg p-6 text-white">
                            <div class="flex items-center justify-between mb-4">
                                <div class="p-3 bg-white bg-opacity-20 rounded-lg">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="text-4xl font-bold mb-2" id="shortlisted-value">0</div>
                            <div class="text-green-100">Candidates in pipeline</div>
                        </div>

                        <!-- This Week -->
                        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl shadow-lg p-6 text-white">
                            <div class="flex items-center justify-between mb-4">
                                <div class="p-3 bg-white bg-opacity-20 rounded-lg">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="text-4xl font-bold mb-2" id="weekly-impressions-value">0</div>
                            <div class="text-purple-100">Total job post impressions</div>
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
                
                document.getElementById('applications-value').textContent = data.metrics.total_applications.value.toLocaleString();
                document.getElementById('applications-trend').innerHTML = formatTrend(data.metrics.total_applications.trend);
                
                document.getElementById('views-value').textContent = data.metrics.total_views.value;
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
                            <h3 class="font-semibold text-gray-900">${job.title}</h3>
                            <span class="px-3 py-1 rounded-full text-xs font-medium ${
                                job.status === 'published' || job.status === 'active' 
                                    ? 'bg-green-100 text-green-700' 
                                    : 'bg-gray-100 text-gray-700'
                            }">${job.status}</span>
                        </div>
                        <div class="text-sm text-gray-600 space-y-1">
                            <div>Posted ${job.posted_days_ago} ${job.posted_days_ago === 1 ? 'day' : 'days'} ago</div>
                            <div class="flex items-center space-x-4">
                                <span>${job.applications_count} applications</span>
                                <span>${job.views_count.toLocaleString()} views</span>
                            </div>
                            ${job.today_activity > 0 ? `<div class="text-green-600 font-medium">+${job.today_activity} today</div>` : ''}
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
                    <div class="flex items-center space-x-4 pb-4 border-b border-gray-200 last:border-0 last:pb-0">
                        <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center flex-shrink-0">
                            <span class="text-white font-semibold">${applicant.initials}</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between mb-1">
                                <h4 class="font-semibold text-gray-900 truncate">${applicant.name}</h4>
                                <span class="px-2 py-1 rounded-full text-xs font-medium ${
                                    applicant.status === 'new' ? 'bg-blue-100 text-blue-700' :
                                    applicant.status === 'shortlisted' ? 'bg-green-100 text-green-700' :
                                    'bg-gray-100 text-gray-700'
                                }">${applicant.status}</span>
                            </div>
                            <p class="text-sm text-gray-600 truncate">${applicant.job_title}</p>
                            <p class="text-xs text-gray-500">${applicant.time_ago}</p>
                        </div>
                        <div class="flex space-x-1">
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
                document.getElementById('weekly-impressions-value').textContent = data.summary.weekly_impressions;
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
        return `<span class="${color} font-medium">${symbol}${Math.abs(trend)}%</span>`;
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

    // User menu dropdown toggle
    (function() {
        const userMenuButton = document.getElementById('user-menu-button');
        const userMenuDropdown = document.getElementById('user-menu-dropdown');
        
        if (userMenuButton && userMenuDropdown) {
            userMenuButton.addEventListener('click', function(e) {
                e.stopPropagation();
                userMenuDropdown.classList.toggle('hidden');
            });
            
            // Close dropdown when clicking outside
            document.addEventListener('click', function(e) {
                if (!userMenuButton.contains(e.target) && !userMenuDropdown.contains(e.target)) {
                    userMenuDropdown.classList.add('hidden');
                }
            });
        }
    })();
</script>
@endsection
