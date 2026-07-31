@extends('layouts.app')

@section('content')
    <!-- Hero Section -->
    <section class="bg-gradient-to-br from-blue-50 to-indigo-100 py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h1 class="text-4xl md:text-5xl font-bold text-gray-900 mb-2">
                    Discover Your Next
                </h1>
                <h2 class="text-4xl md:text-6xl font-bold text-blue-600 mb-4">
                    Career Opportunity
                </h2>
                <p class="text-base md:text-lg text-gray-600 max-w-2xl mx-auto">
                    Connect with thousands of companies hiring for roles across all industries and experience levels.
                </p>
            </div>

            <!-- Search Bar -->
            <style>
                .landing-rainbow-text {
                    background: linear-gradient(90deg, #f472b6, #a78bfa, #60a5fa, #34d399, #fbbf24, #f472b6);
                    background-size: 200% auto;
                    -webkit-background-clip: text;
                    -webkit-text-fill-color: transparent;
                    background-clip: text;
                    animation: landing-rainbow 3s linear infinite;
                }
                @keyframes landing-rainbow {
                    0% { background-position: 0% center; }
                    100% { background-position: 200% center; }
                }
            </style>
            <div class="max-w-4xl mx-auto bg-gray-800 rounded-xl shadow-lg p-6">
                <form id="heroSearchForm" onsubmit="handleHeroSearch(event)">
                    <!-- Preferred Work Location Label -->
                    <div class="mb-4">
                        <span class="text-sm font-medium text-white">Preferred work location</span>
                    </div>

                    <!-- Single Filter Row -->
                    <div style="display: flex; gap: 0.5rem; margin-bottom: 0.75rem; align-items: stretch;">
                        <!-- Keyword Search -->
                        <div style="flex: 1 1 0%; min-width: 0;">
                            <div class="relative" style="height: 100%;">
                                <svg class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 transform -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                                <input 
                                    type="text" 
                                    id="keyword"
                                    name="keyword"
                                    placeholder="Search keyword" 
                                    class="w-full bg-white border border-gray-300 rounded-lg pl-9 pr-3 py-2.5 text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-pink-500"
                                    style="height: 100%;"
                                >
                            </div>
                        </div>

                        <!-- Category Dropdown -->
                        <div style="flex: 1 1 0%; min-width: 0;">
                            <select id="category_id" name="category_id" class="w-full bg-white border border-gray-300 rounded-lg px-3 py-2.5 text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-pink-500" style="height: 100%;">
                                <option value="">All job categories</option>
                            </select>
                        </div>

                        <!-- Location Dropdown (searchable multi-select) -->
                        <div style="flex: 1 1 0%; min-width: 0; position: relative;">
                            <input type="hidden" id="location" name="location" value="">
                            <button
                                type="button"
                                id="heroLocationBtn"
                                class="w-full bg-white border border-gray-300 rounded-lg px-3 py-2.5 text-sm text-gray-900 flex items-center justify-between focus:outline-none focus:ring-2 focus:ring-pink-500"
                                style="height: 100%;"
                            >
                                <span id="heroLocationLabel" class="truncate">All Seychelles locations</span>
                                <svg class="w-4 h-4 text-gray-400 flex-shrink-0 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </button>
                            <div id="heroLocationPanel" class="hidden absolute z-50 mt-1 w-full bg-white border border-gray-300 rounded-lg shadow-lg" style="min-width: 220px;">
                                <div class="p-2 border-b border-gray-100">
                                    <div class="relative">
                                        <svg class="w-4 h-4 text-gray-400 absolute left-2.5 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                        <input type="text" id="heroLocationSearch" placeholder="Search" class="w-full border border-gray-300 rounded-md pl-8 pr-3 py-1.5 text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-pink-500">
                                    </div>
                                </div>
                                <div id="heroLocationOptions" class="max-h-56 overflow-y-auto py-1"></div>
                            </div>
                        </div>

                        <!-- Find Jobs Button -->
                        <div style="flex: 0 0 auto;">
                            <button type="submit" class="bg-pink-500 text-white px-6 py-2.5 rounded-lg font-semibold hover:bg-pink-600 transition" style="white-space: nowrap; height: 100%;">
                                Find jobs
                            </button>
                        </div>
                    </div>

                    <!-- Show More Options Toggle -->
                    <div class="mb-3">
                        <button type="button" id="toggleAdvancedOptions" class="flex items-center text-xs text-gray-300 cursor-pointer hover:text-white transition">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path>
                            </svg>
                            <span id="toggleText">Show more options</span>
                        </button>
                    </div>

                    <!-- Advanced Options (Hidden by default) -->
                    <div id="advancedOptions" class="hidden mb-4 p-4 bg-gray-700 rounded-lg">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="text-sm text-white mb-2 block">Employment Type</label>
                                <select id="employment_type" name="employment_type" class="w-full bg-white text-gray-900 border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="">All types</option>
                                    <option value="full_time">Full Time</option>
                                    <option value="part_time">Part Time</option>
                                    <option value="contract">Contract</option>
                                    <option value="temporary">Temporary</option>
                                </select>
                            </div>
                            <div>
                                <label class="text-sm text-white mb-2 block">Experience Level</label>
                                <select id="experience_level" name="experience_level" class="w-full bg-white text-gray-900 border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="">All levels</option>
                                    <option value="entry">Entry Level</option>
                                    <option value="mid">Mid Level</option>
                                    <option value="senior">Senior Level</option>
                                    <option value="executive">Executive</option>
                                </select>
                            </div>
                            <div>
                                <label class="text-sm text-white mb-2 block">Salary Range (Min)</label>
                                <input type="number" id="salary_min" name="salary_min" placeholder="e.g., 3000" class="w-full bg-white text-gray-900 border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="text-sm text-white mb-2 block">Salary Range (Max)</label>
                                <input type="number" id="salary_max" name="salary_max" placeholder="e.g., 5000" class="w-full bg-white text-gray-900 border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                        </div>
                    </div>

                    <!-- Popular Searches -->
                    <div>
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="text-xs font-medium text-white mr-1">Popular searches</span>
                            <button type="button" onclick="setPopularSearch('Admin')" class="px-4 py-1.5 bg-transparent border border-white/30 text-white rounded-full text-xs hover:bg-white/20 transition">Admin</button>
                            <button type="button" onclick="setPopularSearch('Operator')" class="px-4 py-1.5 bg-transparent border border-white/30 text-white rounded-full text-xs hover:bg-white/20 transition">Operator</button>
                            <button type="button" onclick="setPopularSearch('Hotel Operations Manager')" class="px-4 py-1.5 bg-transparent border border-white/30 text-white rounded-full text-xs hover:bg-white/20 transition">Hotel Operations Manager</button>
                            <button type="button" onclick="setPopularSearch('Cleaner')" class="px-4 py-1.5 bg-transparent border border-white/30 text-white rounded-full text-xs hover:bg-white/20 transition">Cleaner</button>
                            <button type="button" onclick="setPopularSearch('Technician')" class="px-4 py-1.5 bg-transparent border border-white/30 text-white rounded-full text-xs hover:bg-white/20 transition">Technician</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <!-- Popular Categories Section -->
    <section class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center mb-8">
                <h2 class="text-3xl font-bold text-gray-900">Popular categories</h2>
                <a href="{{ route('jobs.index') }}" wire:navigate class="text-pink-600 hover:text-pink-700 font-semibold text-sm">View all</a>
            </div>

            <div id="popular-categories" class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-4">
                <!-- Skeleton loaders -->
                <div class="bg-white rounded-lg p-5 border border-gray-200 hover:shadow-md transition animate-pulse">
                    <div class="w-12 h-12 bg-gray-200 rounded-lg mb-3"></div>
                    <div class="h-4 bg-gray-200 rounded w-full mb-1.5"></div>
                    <div class="h-3 bg-gray-200 rounded w-2/3"></div>
                </div>
                <div class="bg-white rounded-lg p-5 border border-gray-200 hover:shadow-md transition animate-pulse">
                    <div class="w-12 h-12 bg-gray-200 rounded-lg mb-3"></div>
                    <div class="h-4 bg-gray-200 rounded w-full mb-1.5"></div>
                    <div class="h-3 bg-gray-200 rounded w-2/3"></div>
                </div>
                <div class="bg-white rounded-lg p-5 border border-gray-200 hover:shadow-md transition animate-pulse hidden md:block">
                    <div class="w-12 h-12 bg-gray-200 rounded-lg mb-3"></div>
                    <div class="h-4 bg-gray-200 rounded w-full mb-1.5"></div>
                    <div class="h-3 bg-gray-200 rounded w-2/3"></div>
                </div>
                <div class="bg-white rounded-lg p-5 border border-gray-200 hover:shadow-md transition animate-pulse hidden lg:block">
                    <div class="w-12 h-12 bg-gray-200 rounded-lg mb-3"></div>
                    <div class="h-4 bg-gray-200 rounded w-full mb-1.5"></div>
                    <div class="h-3 bg-gray-200 rounded w-2/3"></div>
                </div>
                <div class="bg-white rounded-lg p-5 border border-gray-200 hover:shadow-md transition animate-pulse hidden lg:block">
                    <div class="w-12 h-12 bg-gray-200 rounded-lg mb-3"></div>
                    <div class="h-4 bg-gray-200 rounded w-full mb-1.5"></div>
                    <div class="h-3 bg-gray-200 rounded w-2/3"></div>
                </div>
                <div class="bg-white rounded-lg p-5 border border-gray-200 hover:shadow-md transition animate-pulse hidden lg:block">
                    <div class="w-12 h-12 bg-gray-200 rounded-lg mb-3"></div>
                    <div class="h-4 bg-gray-200 rounded w-full mb-1.5"></div>
                    <div class="h-3 bg-gray-200 rounded w-2/3"></div>
                </div>
            </div>
        </div>
    </section>

    <!-- Find Your Next Employer Section -->
    <section class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-8">
                <h2 class="text-3xl font-bold text-gray-900 mb-3">Find your next employer</h2>
                <p class="text-gray-600 max-w-2xl text-sm">
                    Explore company profiles to find the right workplace for you. Learn about jobs, reviews, company culture, perks and benefits.
                </p>
            </div>

            <!-- Company Carousel -->
            <div class="relative">
                <!-- Left Arrow -->
                <button id="carousel-prev" class="absolute left-0 top-1/2 -translate-y-1/2 -translate-x-6 z-10 bg-white rounded-full w-10 h-10 shadow-md hover:shadow-lg transition hidden lg:flex items-center justify-center border border-gray-300">
                    <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </button>
                
                <!-- Carousel Container -->
                <div class="overflow-hidden px-12 lg:px-0">
                    <div id="company-carousel" class="flex space-x-4 transition-transform duration-500 ease-in-out scroll-smooth">
                        <!-- Skeleton loaders -->
                        <div class="flex-shrink-0 w-56 bg-white rounded-lg p-5 border border-gray-200 shadow-sm animate-pulse">
                            <div class="w-14 h-14 bg-gray-200 rounded-lg mb-3"></div>
                            <div class="h-5 bg-gray-200 rounded w-3/4 mb-2"></div>
                            <div class="h-3.5 bg-gray-200 rounded w-1/2"></div>
                        </div>
                        <div class="flex-shrink-0 w-56 bg-white rounded-lg p-5 border border-gray-200 shadow-sm animate-pulse">
                            <div class="w-14 h-14 bg-gray-200 rounded-lg mb-3"></div>
                            <div class="h-5 bg-gray-200 rounded w-3/4 mb-2"></div>
                            <div class="h-3.5 bg-gray-200 rounded w-1/2"></div>
                        </div>
                        <div class="flex-shrink-0 w-56 bg-white rounded-lg p-5 border border-gray-200 shadow-sm animate-pulse">
                            <div class="w-14 h-14 bg-gray-200 rounded-lg mb-3"></div>
                            <div class="h-5 bg-gray-200 rounded w-3/4 mb-2"></div>
                            <div class="h-3.5 bg-gray-200 rounded w-1/2"></div>
                        </div>
                        <div class="flex-shrink-0 w-56 bg-white rounded-lg p-5 border border-gray-200 shadow-sm animate-pulse">
                            <div class="w-14 h-14 bg-gray-200 rounded-lg mb-3"></div>
                            <div class="h-5 bg-gray-200 rounded w-3/4 mb-2"></div>
                            <div class="h-3.5 bg-gray-200 rounded w-1/2"></div>
                        </div>
                        <div class="flex-shrink-0 w-56 bg-white rounded-lg p-5 border border-gray-200 shadow-sm animate-pulse">
                            <div class="w-14 h-14 bg-gray-200 rounded-lg mb-3"></div>
                            <div class="h-5 bg-gray-200 rounded w-3/4 mb-2"></div>
                            <div class="h-3.5 bg-gray-200 rounded w-1/2"></div>
                        </div>
                    </div>
                </div>
                
                <!-- Right Arrow -->
                <button id="carousel-next" class="absolute right-0 top-1/2 -translate-y-1/2 translate-x-6 z-10 bg-white rounded-full w-10 h-10 shadow-md hover:shadow-lg transition hidden lg:flex items-center justify-center border border-gray-300">
                    <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </button>
                
                <!-- Pagination Dots -->
                <div id="carousel-dots" class="flex justify-center gap-1.5 mt-5">
                    <button class="w-2 h-2 bg-gray-900 rounded-full"></button>
                    <button class="w-2 h-2 bg-gray-300 rounded-full"></button>
                    <button class="w-2 h-2 bg-gray-300 rounded-full"></button>
                </div>
                
                <!-- See More Button -->
                <div class="text-center mt-5">
                    <button onclick="navigateToCompanies()" class="border-2 border-gray-900 text-gray-900 px-8 py-2.5 rounded-lg font-semibold hover:bg-gray-900 hover:text-white transition text-sm">
                        See more →
                    </button>
                </div>
            </div>
        </div>
    </section>

    <!-- Popular Jobs Section -->
    <section class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center mb-8">
                <h2 class="text-3xl font-bold text-gray-900">Popular jobs</h2>
                <a href="{{ route('jobs.index') }}" wire:navigate class="text-pink-600 hover:text-pink-700 font-semibold text-sm">View all</a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4" id="popular-jobs">
                <!-- Skeleton loaders -->
                <div class="bg-white rounded-lg border border-gray-200 p-5 shadow-sm animate-pulse">
                    <div class="w-12 h-12 bg-blue-100 rounded-lg mb-4"></div>
                    <div class="h-5 bg-gray-200 rounded w-full mb-3"></div>
                    <div class="h-4 bg-gray-200 rounded w-2/3 mb-2"></div>
                    <div class="h-3 bg-gray-200 rounded w-1/2 mb-2"></div>
                    <div class="h-3 bg-gray-200 rounded w-1/3 mb-2"></div>
                    <div class="flex gap-2 mb-3">
                        <div class="h-6 bg-gray-200 rounded w-20"></div>
                        <div class="h-6 bg-gray-200 rounded w-20"></div>
                    </div>
                    <div class="h-3 bg-gray-200 rounded w-1/2 mb-4"></div>
                    <div class="h-10 bg-gray-200 rounded w-full"></div>
                </div>
                <div class="bg-white rounded-lg border border-gray-200 p-5 shadow-sm animate-pulse">
                    <div class="w-12 h-12 bg-blue-100 rounded-lg mb-4"></div>
                    <div class="h-5 bg-gray-200 rounded w-full mb-3"></div>
                    <div class="h-4 bg-gray-200 rounded w-2/3 mb-2"></div>
                    <div class="h-3 bg-gray-200 rounded w-1/2 mb-2"></div>
                    <div class="h-3 bg-gray-200 rounded w-1/3 mb-2"></div>
                    <div class="flex gap-2 mb-3">
                        <div class="h-6 bg-gray-200 rounded w-20"></div>
                    </div>
                    <div class="h-3 bg-gray-200 rounded w-1/2 mb-4"></div>
                    <div class="h-10 bg-gray-200 rounded w-full"></div>
                </div>
                <div class="bg-white rounded-lg border border-gray-200 p-5 shadow-sm animate-pulse hidden md:block">
                    <div class="w-12 h-12 bg-blue-100 rounded-lg mb-4"></div>
                    <div class="h-5 bg-gray-200 rounded w-full mb-3"></div>
                    <div class="h-4 bg-gray-200 rounded w-2/3 mb-2"></div>
                    <div class="h-3 bg-gray-200 rounded w-1/2 mb-2"></div>
                    <div class="h-3 bg-gray-200 rounded w-1/3 mb-2"></div>
                    <div class="flex gap-2 mb-3">
                        <div class="h-6 bg-gray-200 rounded w-20"></div>
                    </div>
                    <div class="h-3 bg-gray-200 rounded w-1/2 mb-4"></div>
                    <div class="h-10 bg-gray-200 rounded w-full"></div>
                </div>
                <div class="bg-white rounded-lg border border-gray-200 p-5 shadow-sm animate-pulse hidden lg:block">
                    <div class="w-12 h-12 bg-blue-100 rounded-lg mb-4"></div>
                    <div class="h-5 bg-gray-200 rounded w-full mb-3"></div>
                    <div class="h-4 bg-gray-200 rounded w-2/3 mb-2"></div>
                    <div class="h-3 bg-gray-200 rounded w-1/2 mb-2"></div>
                    <div class="h-3 bg-gray-200 rounded w-1/3 mb-2"></div>
                    <div class="flex gap-2 mb-3">
                        <div class="h-6 bg-gray-200 rounded w-20"></div>
                        <div class="h-6 bg-gray-200 rounded w-20"></div>
                    </div>
                    <div class="h-3 bg-gray-200 rounded w-1/2 mb-4"></div>
                    <div class="h-10 bg-gray-200 rounded w-full"></div>
                </div>
            </div>
            
            <!-- See More Button -->
            <div class="text-center mt-8">
                <button onclick="navigateToJobs()" class="border-2 border-gray-900 text-gray-900 px-8 py-2.5 rounded-lg font-semibold hover:bg-gray-900 hover:text-white transition text-sm">
                    See more →
                </button>
            </div>
        </div>
    </section>

    <!-- App Download Banner -->
    <section class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-2xl overflow-hidden shadow-xl">
                <div class="grid grid-cols-1 lg:grid-cols-2">
                    <!-- Left Side - Blue Gradient Background with Content -->
                    <div class="bg-gradient-to-br from-blue-500 to-blue-700 text-white p-8 lg:p-12">
                        <div class="flex items-center mb-4">
                            <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                            </svg>
                            <span class="text-sm font-semibold uppercase tracking-wide">DOWNLOAD OUR APP</span>
                        </div>
                        <h2 class="text-4xl font-bold mb-4">Job searching made easier on the move</h2>
                        <p class="text-blue-100 mb-8 text-lg">
                            Access thousands of jobs, get instant notifications, and apply with one tap. Your next career opportunity is just a download away.
                        </p>
                        
                        <!-- Download Buttons -->
                        <div class="flex flex-col sm:flex-row gap-4 mb-8">
                            <button class="bg-white text-gray-900 px-6 py-3 rounded-lg font-semibold hover:bg-gray-100 transition flex items-center justify-center">
                                <svg class="w-6 h-6 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M18.71 19.5c-.83 1.24-1.71 2.45-3.05 2.47-1.34.03-1.77-.79-3.29-.79-1.53 0-2 .77-3.27.82-1.31.05-2.3-1.32-3.14-2.53C4.25 17 2.94 12.45 4.7 9.39c.87-1.52 2.43-2.48 4.12-2.51 1.28-.02 2.5.87 3.29.87.78 0 2.26-1.07 3.81-.91.65.03 2.47.26 3.64 1.98-.09.06-2.17 1.28-2.15 3.81.03 3.02 2.65 4.03 2.68 4.04-.03.07-.42 1.44-1.38 2.83M13 3.5c.73-.83 1.94-1.46 2.94-1.5.13 1.17-.34 2.35-1.04 3.19-.69.85-1.83 1.51-2.95 1.42-.15-1.15.41-2.35 1.05-3.11z"/>
                                </svg>
                                Download on the App Store
                            </button>
                            <button class="bg-white text-gray-900 px-6 py-3 rounded-lg font-semibold hover:bg-gray-100 transition flex items-center justify-center">
                                <svg class="w-6 h-6 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M3,20.5V3.5C3,2.91 3.34,2.39 3.84,2.05L13.69,12L3.84,21.95C3.34,21.6 3,21.09 3,20.5M16.81,15.12L6.05,21.34L14.54,12.85L16.81,15.12M20.16,10.81C20.5,11.08 20.75,11.5 20.75,12C20.75,12.5 20.53,12.9 20.18,13.18L17.89,14.5L15.39,12L17.89,9.5L20.16,10.81M6.05,2.66L16.81,8.88L14.54,11.15L6.05,2.66Z"/>
                                </svg>
                                Get it on Google Play
                            </button>
                        </div>

                        <!-- QR Code -->
                        <div class="flex items-center space-x-4 mb-6">
                            <div class="bg-white p-3 rounded-lg shadow-md">
                                <img src="https://api.qrserver.com/v1/create-qr-code/?size=128x128&data=https://jobhub.app/download&ecc=M&margin=1" alt="QR Code - Scan to download app" class="w-32 h-32">
                            </div>
                            <div>
                                <p class="text-sm font-semibold mb-2">Scan to download</p>
                            </div>
                        </div>

                        <!-- Features -->
                        <div class="flex flex-col sm:flex-row gap-4">
                            <div class="flex items-center text-sm text-blue-100">
                                <svg class="w-4 h-4 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                                Free to use
                            </div>
                            <div class="flex items-center text-sm text-blue-100">
                                <svg class="w-4 h-4 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                                Instant alerts
                            </div>
                        </div>
                    </div>

                    <!-- Right Side - Image -->
                    <div class="hidden lg:block">
                        <img src="{{ asset('images/app-promo-banner.png') }}" alt="Job searching made easier on the move" class="w-full h-full object-cover">
                    </div>
                </div>
            </div>
        </div>
    </section>

    @push('scripts')
    <script>
        const API_BASE = '/api';
        
        // Toggle advanced options
        document.addEventListener('DOMContentLoaded', function() {
            const toggleBtn = document.getElementById('toggleAdvancedOptions');
            const advancedOptions = document.getElementById('advancedOptions');
            const toggleText = document.getElementById('toggleText');
            
            if (toggleBtn && advancedOptions) {
                toggleBtn.addEventListener('click', function() {
                    const isHidden = advancedOptions.classList.contains('hidden');
                    if (isHidden) {
                        advancedOptions.classList.remove('hidden');
                        toggleText.textContent = 'Hide options';
                    } else {
                        advancedOptions.classList.add('hidden');
                        toggleText.textContent = 'Show more options';
                    }
                });
            }
            
            // Load categories
            loadCategories();
        });
        
        // Load categories for dropdown
        async function loadCategories() {
            try {
                const response = await fetch(`${API_BASE}/categories`);
                const data = await response.json();
                const select = document.getElementById('category_id');
                
                if (select && data.data) {
                    data.data.forEach(category => {
                        const option = document.createElement('option');
                        option.value = category.id;
                        option.textContent = category.name;
                        select.appendChild(option);
                    });
                }
            } catch (error) {
                console.error('Error loading categories:', error);
            }
        }
        
        // Set popular search
        function setPopularSearch(keyword) {
            const keywordInput = document.getElementById('keyword');
            if (keywordInput) {
                keywordInput.value = keyword;
                // Optionally trigger search
                handleHeroSearch(null, keyword);
            }
        }
        
        // Handle hero search form submission
        function handleHeroSearch(event, searchKeyword = null) {
            if (event) {
                event.preventDefault();
            }
            
            const form = document.getElementById('heroSearchForm');
            const formData = new FormData(form);
            const params = new URLSearchParams();
            
            // Add keyword (from parameter or form)
            const keyword = searchKeyword || formData.get('keyword') || '';
            if (keyword) {
                params.append('keyword', keyword);
            }
            
            // Add other search parameters
            const categoryId = formData.get('category_id');
            if (categoryId) {
                params.append('category_id', categoryId);
            }
            
            const location = formData.get('location') || formData.get('preferred_location');
            if (location) {
                params.append('location', location);
            }
            
            const employmentType = formData.get('employment_type');
            if (employmentType) {
                params.append('employment_type', employmentType);
            }
            
            const experienceLevel = formData.get('experience_level');
            if (experienceLevel) {
                params.append('experience_level', experienceLevel);
            }
            
            const salaryMin = formData.get('salary_min');
            if (salaryMin) {
                params.append('salary_min', salaryMin);
            }
            
            const salaryMax = formData.get('salary_max');
            if (salaryMax) {
                params.append('salary_max', salaryMax);
            }
            
            // Navigate to jobs page with search parameters
            const searchUrl = `/jobs?${params.toString()}`;
            if (typeof navigateTo === 'function') {
                navigateTo(searchUrl);
            } else {
                window.location.href = searchUrl;
            }
        }
        
        // Make function globally available
        window.handleHeroSearch = handleHeroSearch;
        window.setPopularSearch = setPopularSearch;

        // ======= Location Searchable Multi-Select Dropdown =======
        const HERO_LOCATIONS = [
            'All Seychelles locations',
            'Central Region', 'East Region', 'West Region', 'North Region', 'South Region',
            'Anse Boileau', 'Anse Royale', 'Anse-aux-Pins', 'Au Cap', 'Baie Lazare',
            'Beau Vallon', 'Bel Air', 'English River', 'Grand Anse Mahe', 'Plaisance',
            'Port Glaud', 'Takamaka', 'Victoria', 'Mahe', 'Praslin', 'La Digue'
        ];

        function getHeroSelectedLocations() {
            const h = document.getElementById('location');
            if (!h || !h.value) return [];
            return h.value.split(',').map(v => v.trim()).filter(Boolean);
        }

        function setHeroLocationValue(arr) {
            const h = document.getElementById('location');
            const label = document.getElementById('heroLocationLabel');
            const normalized = Array.isArray(arr) ? arr : [];
            if (h) h.value = normalized.join(',');
            if (label) {
                if (normalized.length === 0) label.textContent = 'All Seychelles locations';
                else if (normalized.length === 1) label.textContent = normalized[0];
                else label.textContent = normalized.length + ' locations selected';
            }
        }

        function renderHeroLocationOptions(query) {
            const container = document.getElementById('heroLocationOptions');
            if (!container) return;
            const q = (query || '').trim().toLowerCase();
            const selected = getHeroSelectedLocations();
            const filtered = HERO_LOCATIONS.filter(opt => opt.toLowerCase().includes(q));
            container.innerHTML = filtered.map(opt => {
                const checked = opt === 'All Seychelles locations' ? selected.length === 0 : selected.includes(opt);
                return '<button type="button" class="w-full px-3 py-2 text-left hover:bg-gray-50 flex items-center gap-2 text-sm text-gray-700" data-hero-loc="' + opt + '">'
                    + '<span class="inline-flex w-4 h-4 rounded border ' + (checked ? 'bg-blue-600 border-blue-600' : 'border-gray-300') + ' items-center justify-center flex-shrink-0">'
                    + (checked ? '<svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>' : '')
                    + '</span><span>' + opt + '</span></button>';
            }).join('');
        }

        function initHeroLocationDropdown() {
            const btn = document.getElementById('heroLocationBtn');
            const panel = document.getElementById('heroLocationPanel');
            const searchInput = document.getElementById('heroLocationSearch');
            const optsCont = document.getElementById('heroLocationOptions');
            if (!btn || !panel || !searchInput || !optsCont) return;

            panel.onclick = function(e) { e.stopPropagation(); };
            panel.style.display = 'none';

            var isOpen = function() { return panel.style.display !== 'none'; };
            var closePanel = function() { panel.classList.add('hidden'); panel.style.display = 'none'; };
            var openPanel = function() {
                panel.classList.remove('hidden');
                panel.style.display = 'block';
                renderHeroLocationOptions(searchInput.value);
                setTimeout(function() { searchInput.focus(); }, 0);
            };

            btn.onclick = function() { isOpen() ? closePanel() : openPanel(); };
            searchInput.onclick = function(e) { e.stopPropagation(); };
            searchInput.oninput = function() { renderHeroLocationOptions(searchInput.value); };

            optsCont.onclick = function(e) {
                e.stopPropagation();
                e.preventDefault();
                var optBtn = e.target.closest('[data-hero-loc]');
                if (!optBtn) return;
                var val = optBtn.getAttribute('data-hero-loc') || '';
                var selected = getHeroSelectedLocations();
                var next;
                if (val === 'All Seychelles locations') { next = []; }
                else if (selected.includes(val)) { next = selected.filter(function(s) { return s !== val; }); }
                else { next = selected.concat([val]); }
                setHeroLocationValue(next);
                renderHeroLocationOptions(searchInput.value);
            };

            document.addEventListener('click', function(e) {
                if (!panel.contains(e.target) && !btn.contains(e.target)) closePanel();
            });

            renderHeroLocationOptions('');
        }

        initHeroLocationDropdown();
        
        // Parallel fetch wrapper for faster loading
        async function fetchFast(url) {
            const response = await fetch(url, {
                headers: { 'Accept': 'application/json' }
            });
            return response.json();
        }

        // Render popular categories
        // Category icons mapping
        const categoryIcons = {
            'Sales / Retail / Marketing': `<svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>`,
            'Hospitality / F&B': `<svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path></svg>`,
            'Customer Service / Receptionists': `<svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0L5.343 6.343z"></path></svg>`,
            'Administrative / Clerical': `<svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>`,
            'Warehousing & Logistics': `<svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>`,
            'Drivers / Riders / Delivery': `<svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>`
        };
        
        function getCategoryIcon(categoryName) {
            for (const [key, icon] of Object.entries(categoryIcons)) {
                if (categoryName.includes(key.split(' / ')[0]) || categoryName.includes(key.split(' & ')[0])) {
                    return icon;
                }
            }
            // Default icon
            return `<svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>`;
        }

        function renderPopularCategories(data) {
            const container = document.getElementById('popular-categories');
            if (container && data?.data?.length > 0) {
                container.innerHTML = data.data.slice(0, 6).map((cat, index) => `
                    <div class="bg-white rounded-lg p-6 border border-gray-100 shadow-sm hover:shadow-md transition-all cursor-pointer fade-in" style="animation-delay: ${index * 50}ms" onclick="searchByCategory(${cat.id})">
                        <div class="w-12 h-12 bg-gray-50 rounded-lg flex items-center justify-center mb-4">
                            ${getCategoryIcon(cat.name)}
                        </div>
                        <h3 class="font-semibold text-gray-900 mb-1 text-sm">${cat.name}</h3>
                        <p class="text-xs text-gray-600">${cat.job_advertisements_count || 0} available jobs</p>
                    </div>
                `).join('');
            }
        }

        // Company carousel state
        let currentCarouselPage = 0;
        const companiesPerPage = 5;
        
        // Render featured companies
        function renderFeaturedCompanies(data) {
            const carousel = document.getElementById('company-carousel');
            if (carousel && data?.data?.length > 0) {
                // Store all companies globally for carousel navigation
                window.allCompanies = data.data;
                
                // Render first page
                updateCarousel(0);
                
                // Setup carousel navigation
                setupCarouselNavigation(data.data.length);
            }
        }
        
        function updateCarousel(page) {
            const carousel = document.getElementById('company-carousel');
            if (!carousel || !window.allCompanies) return;
            
            currentCarouselPage = page;
            const startIndex = page * companiesPerPage;
            const endIndex = startIndex + companiesPerPage;
            const companiesToShow = window.allCompanies.slice(startIndex, endIndex);
            
            // Add smooth transition
            carousel.style.transition = 'opacity 0.3s ease-in-out';
            carousel.style.opacity = '0';
            
            setTimeout(() => {
                carousel.innerHTML = companiesToShow.map(company => `
                    <div class="flex-shrink-0 w-56 bg-white rounded-lg p-5 border border-gray-200 shadow-sm hover:shadow-md transition cursor-pointer fade-in" onclick="navigateToCompany('${company.slug || company.id}')">
                        <div class="w-14 h-14 bg-blue-50 rounded-lg flex items-center justify-center mb-3 border border-blue-100">
                            ${company.logo ? 
                                `<img src="${company.logo}" alt="${company.name}" class="w-full h-full object-cover rounded-lg">` :
                                `<svg class="w-7 h-7 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>`
                            }
                        </div>
                        <h3 class="font-semibold text-gray-900 mb-2 text-base">${company.name || 'Company'}</h3>
                        <p class="text-sm text-gray-600">${company.job_advertisements_count || 0} Available Jobs</p>
                    </div>
                `).join('');
                
                carousel.style.opacity = '1';
            }, 150);
            
            // Update pagination dots
            updateCarouselDots();
            
            // Update arrow visibility
            const totalPages = Math.ceil(window.allCompanies.length / companiesPerPage);
            const prevBtn = document.getElementById('carousel-prev');
            const nextBtn = document.getElementById('carousel-next');
            
            if (prevBtn) {
                prevBtn.style.display = page === 0 ? 'none' : 'flex';
            }
            if (nextBtn) {
                nextBtn.style.display = page >= totalPages - 1 ? 'none' : 'flex';
            }
        }
        
        function setupCarouselNavigation(totalCompanies) {
            const totalPages = Math.ceil(totalCompanies / companiesPerPage);
            
            const prevBtn = document.getElementById('carousel-prev');
            const nextBtn = document.getElementById('carousel-next');
            
            if (prevBtn) {
                prevBtn.onclick = () => {
                    if (currentCarouselPage > 0) {
                        updateCarousel(currentCarouselPage - 1);
                    }
                };
            }
            
            if (nextBtn) {
                nextBtn.onclick = () => {
                    if (currentCarouselPage < totalPages - 1) {
                        updateCarousel(currentCarouselPage + 1);
                    }
                };
            }
        }
        
        function updateCarouselDots() {
            const dotsContainer = document.getElementById('carousel-dots');
            if (!dotsContainer || !window.allCompanies) return;
            
            const totalPages = Math.ceil(window.allCompanies.length / companiesPerPage);
            dotsContainer.innerHTML = Array.from({ length: totalPages }, (_, i) => `
                <button onclick="updateCarousel(${i})" class="w-2 h-2 rounded-full transition ${i === currentCarouselPage ? 'bg-gray-900' : 'bg-gray-300'}" aria-label="Go to page ${i + 1}"></button>
            `).join('');
        }
        
        function navigateToCompany(companyIdOrSlug) {
            if (typeof navigateTo === 'function') {
                navigateTo(`/companies/${companyIdOrSlug}`);
            } else {
                window.location.href = `/companies/${companyIdOrSlug}`;
            }
        }
        
        function navigateToCompanies() {
            if (typeof navigateTo === 'function') {
                navigateTo('/companies');
            } else {
                window.location.href = '/companies';
            }
        }
        
        function searchByCategory(categoryId) {
            if (typeof navigateTo === 'function') {
                navigateTo(`/jobs?category_id=${categoryId}`);
            } else {
                window.location.href = `/jobs?category_id=${categoryId}`;
            }
        }
        
        // Make functions globally available
        window.updateCarousel = updateCarousel;
        window.navigateToCompany = navigateToCompany;
        window.navigateToCompanies = navigateToCompanies;
        window.searchByCategory = searchByCategory;

        // Render categories dropdown
        function renderCategoriesDropdown(data) {
            const selects = document.querySelectorAll('select');
            const categorySelect = Array.from(selects).find(s => s.innerHTML.includes('All job categories'));
            if (categorySelect && data?.data) {
                const currentValue = categorySelect.value;
                categorySelect.innerHTML = '<option value="">All job categories</option>' +
                    data.data.map(cat => `<option value="${cat.id}">${cat.name}</option>`).join('');
                if (currentValue) categorySelect.value = currentValue;
            }
        }

        // Format date helper
        function formatDate(dateString) {
            if (!dateString) return '';
            const date = new Date(dateString);
            const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            const day = date.getDate().toString().padStart(2, '0');
            const month = months[date.getMonth()];
            const year = date.getFullYear();
            return `Posted on ${day} ${month} ${year}`;
        }
        
        // Format salary helper
        function formatSalary(job) {
            if (!job.salary_min && !job.salary_max) return '';
            const currency = job.currency || 'SCR';
            if (job.salary_min && job.salary_max && job.salary_min !== job.salary_max) {
                return `${currency}${job.salary_min} - ${currency}${job.salary_max}`;
            }
            return `${currency}${job.salary_min || job.salary_max} per month`;
        }
        
        // Format employment types
        function formatEmploymentTypes(employmentType) {
            if (!employmentType) return '';
            const types = employmentType.split(',').map(t => t.trim()).filter(t => t);
            return types.map(type => `
                <span class="px-2.5 py-1 bg-gray-100 text-gray-700 text-xs rounded font-medium">${type}</span>
            `).join('');
        }
        
        // Render popular jobs
        function renderPopularJobs(data) {
            const container = document.getElementById('popular-jobs');
            if (!container) return;
            
            if (data?.data?.length > 0) {
                container.innerHTML = data.data.slice(0, 8).map((job, index) => {
                    const postedDate = formatDate(job.created_at || job.published_at);
                    const salary = formatSalary(job);
                    const employmentTypes = formatEmploymentTypes(job.employment_type);
                    const companyVerified = job.company?.verified_at ? '<svg class="w-4 h-4 text-blue-600 ml-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>' : '';
                    
                    return `
                        <div onclick="handleJobClick(${job.id})" class="bg-white rounded-lg border border-gray-200 p-5 shadow-sm hover:shadow-lg hover:border-pink-300 transition-all fade-in cursor-pointer group" style="animation-delay: ${index * 50}ms">
                            <!-- Icon -->
                            <div class="w-12 h-12 bg-blue-100 group-hover:bg-pink-100 rounded-lg flex items-center justify-center mb-4 transition-colors">
                                <svg class="w-6 h-6 text-blue-600 group-hover:text-pink-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                            
                            <!-- Job Title -->
                            <h3 class="font-semibold text-gray-900 group-hover:text-pink-600 mb-2 text-sm leading-tight line-clamp-2 transition-colors">${job.title || 'Job Title'}</h3>
                            
                            <!-- Company Name with Verified Badge -->
                            <div class="flex items-center text-sm text-gray-700 mb-3">
                                <span class="font-medium">${job.company?.name || 'Company'}</span>
                                ${companyVerified}
                            </div>
                            
                            <!-- Location -->
                            ${job.location ? `
                                <div class="flex items-center text-xs text-gray-600 mb-2">
                                    <svg class="w-3.5 h-3.5 mr-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    </svg>
                                    <span class="truncate">${job.location}</span>
                                </div>
                            ` : ''}
                            
                            <!-- Salary -->
                            ${salary ? `
                                <div class="flex items-center text-xs text-gray-600 mb-3">
                                    <svg class="w-3.5 h-3.5 mr-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span>${salary}</span>
                                </div>
                            ` : ''}
                            
                            <!-- Employment Types -->
                            ${employmentTypes ? `
                                <div class="flex flex-wrap gap-2 mb-3">
                                    ${employmentTypes}
                                </div>
                            ` : ''}
                            
                            <!-- Posted Date -->
                            ${postedDate ? `
                                <div class="text-xs text-gray-500 mb-4">
                                    ${postedDate}
                                </div>
                            ` : ''}
                            
                            <!-- Apply Button -->
                            <button onclick="event.stopPropagation(); handleApply(${job.id})" class="w-full bg-pink-500 text-white py-2.5 rounded-lg font-semibold hover:bg-pink-600 transition text-sm">
                                Apply now
                            </button>
                        </div>
                    `;
                }).join('');
            } else {
                container.innerHTML = '<div class="col-span-4 text-center py-12 text-gray-500"><p class="text-lg mb-2">No jobs available</p><p class="text-sm">Check back later for new opportunities</p></div>';
            }
        }
        
        function navigateToJobs() {
            if (typeof navigateTo === 'function') {
                navigateTo('/jobs');
            } else {
                window.location.href = '/jobs';
            }
        }
        
        function handleApply(jobId) {
            if (typeof navigateTo === 'function') {
                navigateTo(`/jobs/${jobId}/apply`);
            } else {
                window.location.href = `/jobs/${jobId}/apply`;
            }
        }
        
        function handleJobClick(jobId) {
            if (typeof navigateTo === 'function') {
                navigateTo(`/jobs/${jobId}`);
            } else {
                window.location.href = `/jobs/${jobId}`;
            }
        }
        
        // Make functions globally available
        window.navigateToJobs = navigateToJobs;
        window.handleApply = handleApply;
        window.handleJobClick = handleJobClick;
        window.handleJobClick = handleJobClick;

        // Load all data in parallel
        async function loadAllData() {
            try {
                const [categories, popularCategories, companies, jobs] = await Promise.all([
                    fetchFast(`${API_BASE}/categories`),
                    fetchFast(`${API_BASE}/categories/popular`),
                    fetchFast(`${API_BASE}/companies/featured`),
                    fetchFast(`${API_BASE}/jobs/published?per_page=8`)
                ]);
                
                renderCategoriesDropdown(categories);
                renderPopularCategories(popularCategories);
                renderFeaturedCompanies(companies);
                renderPopularJobs(jobs);
            } catch (error) {
                console.error('Error loading data:', error);
            }
        }

        // Handle search form submission
        async function handleSearch(event) {
            if (event) event.preventDefault();
            
            const keyword = document.querySelector('input[placeholder="Search keyword"]')?.value || '';
            const categoryId = document.querySelector('select:has(option[value="All job categories"])')?.value || '';
            const locationVal = document.getElementById('location')?.value || '';
            
            const params = new URLSearchParams();
            if (keyword) params.append('keyword', keyword);
            if (categoryId) params.append('category_id', categoryId);
            if (locationVal) params.append('location', locationVal);
            
            const searchBtn = event?.target?.closest('button') || Array.from(document.querySelectorAll('button')).find(btn => btn.textContent.includes('Find jobs'));
            if (searchBtn) {
                searchBtn.classList.add('btn-loading');
                searchBtn.disabled = true;
            }
            
            // Small delay for smooth UX
            await new Promise(resolve => setTimeout(resolve, 300));
            navigateTo(`/jobs?${params.toString()}`);
        }

        // Navigate using Livewire for SPA-like experience
        function navigateTo(url) {
            if (typeof Livewire !== 'undefined' && Livewire.navigate) {
                Livewire.navigate(url);
            } else {
                window.location.href = url;
            }
        }

        // Search by category
        function searchByCategory(categoryId) {
            navigateTo(`/jobs?category_id=${categoryId}`);
        }

        // Handle apply button
        function handleApply(jobId) {
            navigateTo(`/jobs/${jobId}`);
        }

        // Initialize on page load - load all data in parallel
        document.addEventListener('DOMContentLoaded', loadAllData);
        
        // Also reload on Livewire navigation
        document.addEventListener('livewire:navigated', loadAllData);
    </script>
    @endpush
@endsection
