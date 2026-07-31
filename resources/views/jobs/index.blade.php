@extends('layouts.app')

@section('content')
<style>
    .search-header-bg {
        background: linear-gradient(135deg, #374151 0%, #1f2937 100%);
    }
    .job-card-selected {
        border-left: 4px solid #3b82f6;
        border-top-color: #e5e7eb;
        border-right-color: #e5e7eb;
        border-bottom-color: #e5e7eb;
        background-color: #f0f7ff;
    }
    .job-card-selected .job-title {
        color: #ec4899 !important;
    }
    .tag-pill {
        background-color: #fce7f3;
        color: #be185d;
        border: 1px solid #fbcfe8;
    }
    .popular-search-btn {
        background-color: rgba(255, 255, 255, 0.15);
        backdrop-filter: blur(10px);
        color: #ffffff;
    }
    .popular-search-btn:hover {
        background-color: rgba(255, 255, 255, 0.25);
        color: #ffffff;
    }
    .rainbow-text {
        background: linear-gradient(90deg, #f472b6, #a78bfa, #60a5fa, #34d399, #fbbf24, #f472b6);
        background-size: 200% auto;
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        animation: rainbow-shift 3s linear infinite;
    }
    @keyframes rainbow-shift {
        0% { background-position: 0% center; }
        100% { background-position: 200% center; }
    }
    .tab-all-jobs {
        background: #1f2937;
    }
    .pink-button {
        background-color: #ec4899;
    }
    .pink-button:hover {
        background-color: #db2777;
    }
    #job-detail-container {
        border: 1px solid #e5e7eb;
    }
    #job-detail-container::-webkit-scrollbar {
        width: 4px;
    }
    #job-detail-container::-webkit-scrollbar-thumb {
        background-color: #d1d5db;
        border-radius: 4px;
    }
</style>

<div class="min-h-screen bg-gray-100">
    <!-- Top Search and Filter Bar -->
    <div class="py-6 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto search-header-bg text-white rounded-xl py-6 px-6">
            <!-- Preferred Work Location -->
            <div class="mb-4">
                <label class="block text-xs font-medium mb-1.5">
                    <span class="rainbow-text">Preferred work location</span>
                    <span class="text-white ml-1">Select location ▼</span>
                </label>
            </div>

            <!-- Single Filter Row -->
            <div style="display: flex; flex-wrap: nowrap; gap: 0.5rem; margin-bottom: 0.75rem;">
                <!-- Search Keyword -->
                <div style="flex: 1 1 0%; min-width: 0;">
                    <div class="relative">
                        <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        <input 
                            type="text" 
                            id="keyword"
                            placeholder="Search keyword" 
                            class="w-full bg-white text-gray-900 border border-gray-300 rounded-lg pl-9 pr-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-pink-500"
                        >
                    </div>
                </div>

                <!-- All Job Categories -->
                <div style="flex: 1 1 0%; min-width: 0;">
                    <select id="category_id" class="w-full bg-white text-gray-900 border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-pink-500">
                        <option value="">All job categories</option>
                    </select>
                </div>

                <!-- All Seychelles Locations -->
                <div style="flex: 1 1 0%; min-width: 0;">
                    <div class="relative">
                        <input type="hidden" id="location" value="">
                        <button
                            type="button"
                            id="locationDropdownButton"
                            class="w-full bg-white text-gray-900 border border-gray-300 rounded-lg px-3 py-2.5 text-sm flex items-center justify-between focus:outline-none focus:ring-2 focus:ring-pink-500"
                        >
                            <span id="locationDropdownLabel" class="truncate">All Seychelles locations</span>
                            <svg class="w-4 h-4 text-gray-500 flex-shrink-0 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div id="locationDropdownPanel" class="hidden absolute z-50 mt-2 w-full bg-white border border-gray-300 rounded-lg shadow-lg">
                            <div class="p-2 border-b border-gray-100">
                                <div class="relative">
                                    <svg class="absolute left-2.5 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                    <input
                                        type="text"
                                        id="locationSearchInput"
                                        placeholder="Search"
                                        class="w-full border border-gray-300 rounded-md pl-8 pr-3 py-1.5 text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-pink-500"
                                    >
                                </div>
                            </div>
                            <div id="locationOptionsContainer" class="max-h-56 overflow-y-auto py-1"></div>
                        </div>
                    </div>
                </div>

                <!-- Find Jobs Button -->
                <div style="flex: 0 0 auto;">
                    <button type="button" id="findJobsBtn" style="white-space: nowrap;" class="pink-button text-white font-semibold py-2.5 px-6 rounded-lg transition shadow-md hover:shadow-lg">
                        Find jobs
                    </button>
                </div>
            </div>

            <!-- Show More Options Toggle -->
            <div class="mb-4">
                <button type="button" id="toggleMoreFilters" class="flex items-center gap-1.5 text-xs text-gray-300 hover:text-white transition cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path>
                    </svg>
                    <span id="toggleMoreFiltersLabel">Show more options</span>
                </button>
            </div>

            <!-- Extra Filters (hidden by default) -->
            <div id="moreFiltersPanel" style="display: none;" class="mb-5">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                    <!-- No Minimum Salary -->
                    <div>
                        <div class="relative">
                            <input type="hidden" id="salary_min" value="">
                            <input type="hidden" id="salary_period" value="month">
                            <button
                                type="button"
                                id="salaryDropdownButton"
                                class="w-full bg-white text-gray-900 border border-gray-300 rounded-lg px-4 py-2.5 text-sm flex items-center justify-between focus:outline-none focus:ring-2 focus:ring-pink-500"
                            >
                                <span id="salaryDropdownLabel">No minimum salary</span>
                                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                            <div id="salaryDropdownPanel" class="hidden absolute z-50 mt-2 w-full bg-white border border-gray-300 rounded-lg shadow-lg p-3">
                                <p class="text-sm font-medium text-gray-900 mb-2">Enter minimum amount (SCR)</p>
                                <input
                                    type="number"
                                    id="salaryMinInput"
                                    min="0"
                                    step="1"
                                    placeholder="0"
                                    class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-pink-500 mb-3"
                                >
                                <div class="flex items-center gap-4 mb-3">
                                    <label class="inline-flex items-center gap-1.5 text-sm text-gray-700 cursor-pointer">
                                        <input type="radio" name="salary_period_choice" value="month" class="text-pink-500 focus:ring-pink-500" checked>
                                        <span>per month</span>
                                    </label>
                                    <label class="inline-flex items-center gap-1.5 text-sm text-gray-700 cursor-pointer">
                                        <input type="radio" name="salary_period_choice" value="hour" class="text-pink-500 focus:ring-pink-500">
                                        <span>per hour</span>
                                    </label>
                                </div>
                                <button type="button" id="salaryConfirmButton" class="w-full pink-button text-white font-semibold py-2 rounded-md transition">
                                    Confirm
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- All Job Types -->
                    <div>
                        <select id="employment_type" class="w-full bg-white text-gray-900 border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-pink-500">
                            <option value="">All job types</option>
                            <option value="Full Time">Full Time</option>
                            <option value="Part Time">Part Time</option>
                            <option value="Contract">Contract</option>
                            <option value="Freelance">Freelance</option>
                        </select>
                    </div>

                    <!-- All Remote Options -->
                    <div>
                        <select id="remote_option" class="w-full bg-white text-gray-900 border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-pink-500">
                            <option value="">All Remote options</option>
                            <option value="remote">Remote</option>
                            <option value="hybrid">Hybrid</option>
                            <option value="office">Office</option>
                        </select>
                    </div>

                    <!-- All Job Tags -->
                    <div>
                        <div class="relative">
                            <input type="hidden" id="job_tags" value="">
                            <button
                                type="button"
                                id="jobTagsDropdownButton"
                                class="w-full bg-white text-gray-900 border border-gray-300 rounded-lg px-4 py-2.5 text-sm flex items-center justify-between focus:outline-none focus:ring-2 focus:ring-pink-500"
                            >
                                <span id="jobTagsDropdownLabel">All job tags</span>
                                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                            <div id="jobTagsDropdownPanel" class="hidden absolute z-50 mt-2 w-full bg-white border border-gray-300 rounded-lg shadow-lg">
                                <div id="jobTagsOptionsContainer" class="max-h-56 overflow-y-auto py-2"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Popular Searches -->
            <div>
                <div class="flex flex-wrap items-center gap-2">
                    <span class="text-xs font-medium text-white mr-1">Popular searches</span>
                    <button type="button" class="popular-search-btn popular-search px-4 py-1.5 rounded-full text-xs font-medium transition" data-keyword="Admin">Admin</button>
                    <button type="button" class="popular-search-btn popular-search px-4 py-1.5 rounded-full text-xs font-medium transition" data-keyword="Operator">Operator</button>
                    <button type="button" class="popular-search-btn popular-search px-4 py-1.5 rounded-full text-xs font-medium transition" data-keyword="Hotel Operations Manager">Hotel Operations Manager</button>
                    <button type="button" class="popular-search-btn popular-search px-4 py-1.5 rounded-full text-xs font-medium transition" data-keyword="Cleaner">Cleaner</button>
                    <button type="button" class="popular-search-btn popular-search px-4 py-1.5 rounded-full text-xs font-medium transition" data-keyword="Technician">Technician</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Area -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            <!-- Left Section - Job Listings (narrower) -->
            <div id="jobs-list-column" class="lg:col-span-4">
                <!-- Tabs and Sort Controls -->
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center gap-3">
                        <button class="tab-btn active tab-all-jobs px-4 py-2 font-semibold text-white text-sm rounded-full" data-tab="all">All jobs</button>
                    </div>
                    <div class="flex items-center gap-2">
                        <select id="sortBy" class="border border-gray-200 rounded-lg px-2.5 py-1.5 text-xs text-gray-600 bg-white focus:outline-none">
                            <option value="latest">Sort by: Latest jobs</option>
                            <option value="oldest">Sort by: Oldest jobs</option>
                            <option value="salary_high">Sort by: Highest salary</option>
                            <option value="salary_low">Sort by: Lowest salary</option>
                        </select>
                        <button id="toggleViewBtn" class="p-1.5 border border-gray-200 rounded-lg hover:bg-gray-100 transition" title="Toggle view">
                            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h8m-8 6h16"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Job Count and Pagination Info -->
                <div class="flex items-center justify-between mb-3 text-xs text-gray-500">
                    <span id="job-count">Showing 16 Jobs</span>
                    <span id="page-info">Page 1 of 4</span>
                </div>

                <!-- Jobs List Container -->
                <div id="jobs-container" class="space-y-3" data-view="list">
                    <!-- Skeleton loaders will be inserted here -->
                </div>
                
                <!-- Skeleton Template -->
                <template id="skeleton-template">
                    <div class="skeleton-loader space-y-4">
                        <!-- Skeleton Card 1 -->
                        <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200 animate-pulse">
                            <div class="flex gap-4">
                                <div class="w-12 h-12 bg-gray-200 rounded-full flex-shrink-0"></div>
                                <div class="flex-1 space-y-3">
                                    <div class="h-5 bg-gray-200 rounded w-2/3"></div>
                                    <div class="h-4 bg-gray-200 rounded w-1/3"></div>
                                    <div class="flex gap-4">
                                        <div class="h-4 bg-gray-200 rounded w-24"></div>
                                        <div class="h-4 bg-gray-200 rounded w-32"></div>
                                    </div>
                                    <div class="h-4 bg-gray-200 rounded w-20"></div>
                                    <div class="flex gap-2">
                                        <div class="h-4 bg-gray-200 rounded w-16"></div>
                                        <div class="h-4 bg-gray-200 rounded w-20"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Skeleton Card 2 -->
                        <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200 animate-pulse">
                            <div class="flex gap-4">
                                <div class="w-12 h-12 bg-gray-200 rounded-full flex-shrink-0"></div>
                                <div class="flex-1 space-y-3">
                                    <div class="h-5 bg-gray-200 rounded w-3/4"></div>
                                    <div class="h-4 bg-gray-200 rounded w-1/4"></div>
                                    <div class="flex gap-4">
                                        <div class="h-4 bg-gray-200 rounded w-28"></div>
                                        <div class="h-4 bg-gray-200 rounded w-36"></div>
                                    </div>
                                    <div class="h-4 bg-gray-200 rounded w-24"></div>
                                    <div class="flex gap-2">
                                        <div class="h-4 bg-gray-200 rounded w-20"></div>
                                        <div class="h-4 bg-gray-200 rounded w-16"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Skeleton Card 3 -->
                        <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200 animate-pulse">
                            <div class="flex gap-4">
                                <div class="w-12 h-12 bg-gray-200 rounded-full flex-shrink-0"></div>
                                <div class="flex-1 space-y-3">
                                    <div class="h-5 bg-gray-200 rounded w-1/2"></div>
                                    <div class="h-4 bg-gray-200 rounded w-2/5"></div>
                                    <div class="flex gap-4">
                                        <div class="h-4 bg-gray-200 rounded w-20"></div>
                                        <div class="h-4 bg-gray-200 rounded w-28"></div>
                                    </div>
                                    <div class="h-4 bg-gray-200 rounded w-28"></div>
                                    <div class="flex gap-2">
                                        <div class="h-4 bg-gray-200 rounded w-24"></div>
                                        <div class="h-4 bg-gray-200 rounded w-20"></div>
                                    </div>
                </div>
            </div>
        </div>
                        <!-- Skeleton Card 4 -->
                        <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200 animate-pulse">
                            <div class="flex gap-4">
                                <div class="w-12 h-12 bg-gray-200 rounded-full flex-shrink-0"></div>
                                <div class="flex-1 space-y-3">
                                    <div class="h-5 bg-gray-200 rounded w-3/5"></div>
                                    <div class="h-4 bg-gray-200 rounded w-1/3"></div>
                                    <div class="flex gap-4">
                        <div class="h-4 bg-gray-200 rounded w-24"></div>
                        <div class="h-4 bg-gray-200 rounded w-32"></div>
                                    </div>
                                    <div class="h-4 bg-gray-200 rounded w-20"></div>
                                    <div class="flex gap-2">
                                        <div class="h-4 bg-gray-200 rounded w-16"></div>
                                        <div class="h-4 bg-gray-200 rounded w-24"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>

                <!-- Pagination -->
                <div id="pagination" class="mt-6"></div>
            </div>

            <!-- Right Section - Job Detail View (wider) -->
            <div id="job-detail-column" class="lg:col-span-8">
                <div id="job-detail-container" class="bg-white rounded-lg shadow-sm sticky top-4 max-h-[calc(100vh-2rem)] overflow-y-auto">
                    <!-- Skeleton for job detail -->
                    <div id="job-detail-skeleton" class="animate-pulse">
                        <div class="h-44 bg-gray-200 rounded-t-lg"></div>
                        <div class="px-6 pt-10 pb-6 space-y-4">
                            <div class="h-6 bg-gray-200 rounded w-3/4"></div>
                            <div class="h-4 bg-gray-200 rounded w-1/2"></div>
                            <div class="h-8 bg-gray-200 rounded w-20"></div>
                            <div class="h-px bg-gray-100 my-2"></div>
                            <div class="space-y-3">
                                <div class="h-4 bg-gray-200 rounded w-2/3"></div>
                                <div class="h-4 bg-gray-200 rounded w-1/2"></div>
                                <div class="h-4 bg-gray-200 rounded w-3/5"></div>
                    <div class="h-4 bg-gray-200 rounded w-1/2"></div>
                                <div class="h-4 bg-gray-200 rounded w-2/3"></div>
                            </div>
                            <div class="h-10 bg-gray-200 rounded w-32 ml-auto"></div>
                        </div>
                    </div>

                    <!-- Actual job detail (hidden initially) -->
                    <div id="job-detail-content" class="hidden">
                        <!-- Banner Image -->
                        <div class="h-44 overflow-hidden rounded-t-lg">
                            <img id="job-banner" src="/images/app-promo-banner.png" alt="Job Banner" class="w-full h-full object-cover">
                        </div>

                        <div class="px-6 pb-6">
                            <!-- Logo row + action icons -->
                            <div class="flex items-center justify-between mt-4 mb-4">
                                <div class="w-14 h-14 bg-white rounded-xl shadow-md p-1.5 flex items-center justify-center border border-gray-100">
                                    <img id="company-logo" src="" alt="Company Logo" class="w-full h-full object-contain rounded-lg">
                                </div>
                                <div class="flex items-center gap-2">
                                    <button class="w-8 h-8 border border-gray-200 rounded-md flex items-center justify-center hover:bg-gray-50 transition">
                                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path></svg>
                                    </button>
                                    <button class="w-8 h-8 border border-gray-200 rounded-md flex items-center justify-center hover:bg-gray-50 transition">
                                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"></path></svg>
                                    </button>
                                    <button class="w-8 h-8 border border-gray-200 rounded-md flex items-center justify-center hover:bg-gray-50 transition">
                                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                                    </button>
                                    <button class="w-8 h-8 border border-gray-200 rounded-md flex items-center justify-center hover:bg-gray-50 transition">
                                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9"></path></svg>
                                    </button>
                                </div>
                            </div>

                            <!-- Job Title -->
                            <h2 id="job-title" class="text-xl font-bold text-gray-900 mb-1"></h2>
                            <!-- Company Name & Verified -->
                            <div class="flex items-center gap-2 mb-2">
                                <span id="company-name" class="text-sm text-gray-700 font-medium"></span>
                                <svg class="w-4 h-4 flex-shrink-0" style="color: #3b82f6;" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <!-- Follow button -->
                            <button id="follow-btn" class="text-xs font-semibold px-4 py-1.5 rounded-md transition mb-3" style="background-color: #3b82f6; color: white;">Follow</button>

                            <!-- Rating row -->
                            <div class="flex items-center gap-2 mb-5 pb-4 border-b border-gray-100">
                                <svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                                <span id="company-rating" class="text-sm font-bold text-gray-900">4.6</span>
                                <span id="company-reviews" class="text-xs text-gray-500">98 reviews</span>
                                <span class="text-xs text-gray-300 mx-1">•</span>
                                <a href="#" class="text-xs text-blue-600 hover:underline font-medium">View all jobs</a>
                            </div>

                            <!-- Key Details -->
                            <div class="space-y-3.5 mb-5">
                                <div class="flex items-center gap-3 text-sm text-gray-700">
                                    <svg class="w-5 h-5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    </svg>
                                    <span id="job-location"></span>
                                </div>
                                <div class="flex items-center gap-3 text-sm font-semibold" style="color: #ec4899;">
                                    <svg class="w-5 h-5 flex-shrink-0" style="color: #ec4899;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                                    <span id="job-salary"></span>
                                </div>
                                <div class="flex items-center gap-3 text-sm text-gray-600">
                                    <svg class="w-5 h-5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                    </svg>
                                    <span id="job-category"></span>
                                </div>
                                <div class="flex items-center gap-3 text-sm text-gray-600">
                                    <svg class="w-5 h-5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                    </svg>
                                    <span id="job-work-type"></span>
                                </div>
                                <div class="flex items-center gap-3 text-sm text-gray-600">
                                    <svg class="w-5 h-5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                    </svg>
                                    <span id="job-employment-type"></span>
                                </div>
                                <div class="flex items-center gap-3 text-sm text-gray-600">
                                    <svg class="w-5 h-5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                    </svg>
                                    <span id="job-experience"></span>
                                </div>
                                <div class="flex items-center gap-3 text-sm text-gray-500">
                                    <svg class="w-5 h-5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span id="job-dates"></span>
                                </div>
                            </div>

                            <!-- Applicant Count + Apply Button row -->
                            <div class="flex items-center justify-between mb-6 pb-5 border-b border-gray-100">
                                <p id="applicant-count" class="text-sm text-pink-600 font-medium flex items-center gap-1.5">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                </p>
                                <button id="apply-job-btn" class="pink-button text-white font-semibold py-2.5 px-8 rounded-lg transition shadow-sm text-sm">
                                    Apply Job
                                </button>
                            </div>

                            <!-- Job Description -->
                            <div class="mb-6">
                                <h3 class="text-lg font-bold text-gray-900 mb-3">Job Description</h3>
                                <div id="job-description" class="text-sm text-gray-700 leading-relaxed"></div>
                            </div>

                            <!-- Skills Section -->
                            <div id="skills-section" class="mb-6 hidden">
                                <h3 class="text-lg font-bold text-gray-900 mb-3">Skills</h3>
                                <div id="job-skills" class="flex flex-wrap gap-2"></div>
                            </div>

                            <!-- About Company Section -->
                            <div id="about-company-section" class="mb-6">
                                <div class="flex items-center justify-between mb-3">
                                    <h3 class="text-lg font-bold text-gray-900">About Company</h3>
                                    <a id="go-to-employer-link" href="#" class="text-sm text-blue-600 hover:underline font-medium flex items-center gap-1">
                                        Go to Employer
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                    </a>
                                </div>
                                <div class="border border-gray-200 rounded-lg p-5 space-y-4">
                                    <div class="grid grid-cols-3 gap-4 text-sm">
                                        <div>
                                            <p class="text-gray-500 mb-1">Company Name</p>
                                            <p id="about-company-name" class="font-semibold text-gray-900"></p>
                                        </div>
                                        <div>
                                            <p class="text-gray-500 mb-1">Company Type</p>
                                            <p id="about-company-type" class="font-semibold text-gray-900"></p>
                                        </div>
                                        <div>
                                            <p class="text-gray-500 mb-1">Company Size</p>
                                            <p id="about-company-size" class="font-semibold text-gray-900"></p>
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-3 gap-4 text-sm">
                                        <div>
                                            <p class="text-gray-500 mb-1">Job Vacancies</p>
                                            <p id="about-company-jobs" class="font-semibold text-gray-900">0</p>
                                        </div>
                                        <div class="col-span-2">
                                            <p class="text-gray-500 mb-1">Company Social Media</p>
                                            <div id="about-company-socials" class="flex items-center gap-2 mt-1"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Employer Questions -->
                            <div id="employer-questions-section" class="mb-6 hidden">
                                <h3 class="text-lg font-bold text-gray-900 mb-3">Employer questions</h3>
                                <p class="text-sm text-gray-600 mb-2">Your application will include the following questions:</p>
                                <ul id="employer-questions-list" class="text-sm text-gray-700 space-y-2"></ul>
                            </div>

                            <!-- Featured Jobs -->
                            <div id="featured-jobs-section" class="mb-6 hidden">
                                <h3 class="text-lg font-bold text-gray-900 mb-4">Featured jobs</h3>
                                <div id="featured-jobs-container" class="grid grid-cols-1 sm:grid-cols-2 gap-4"></div>
                            </div>

                            <!-- Stay Cautious -->
                            <div class="mb-6 bg-blue-50 border border-blue-200 rounded-lg p-5">
                                <h4 class="text-base font-bold text-gray-900 mb-2">Stay cautious</h4>
                                <p class="text-sm text-gray-600 mb-4">Protect yourself: never share your bank or credit card details when applying for jobs. Report any suspicious job postings immediately</p>
                                <div class="flex items-center gap-4">
                                    <button id="report-job-btn" class="px-4 py-2 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700 transition flex items-center gap-1.5">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9"></path></svg>
                                        Report Job
                                    </button>
                                    <a href="#" class="text-sm text-gray-600 hover:text-gray-900 font-medium">Learn More</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                </div>
            </div>
        </div>
</div>

@push('scripts')
<script>
    const API_BASE = '/api';

    // Toggle more filters panel
    document.getElementById('toggleMoreFilters')?.addEventListener('click', function() {
        const panel = document.getElementById('moreFiltersPanel');
        const label = document.getElementById('toggleMoreFiltersLabel');
        if (panel.style.display === 'none') {
            panel.style.display = 'block';
            label.textContent = 'Hide options';
        } else {
            panel.style.display = 'none';
            label.textContent = 'Show more options';
        }
    });

    let selectedJobId = null;
    let currentRequest = null;
    let currentJobData = null;
    let currentView = 'list'; // 'list' or 'grid'
    let lastJobsData = null; // store last fetched data for view toggling
    const LOCATION_OPTIONS = [
        'All Seychelles locations',
        'Central Region',
        'East Region',
        'West Region',
        'North Region',
        'South Region',
        'Anse Boileau',
        'Anse Royale',
        'Anse-aux-Pins',
        'Au Cap',
        'Baie Lazare',
        'Beau Vallon',
        'Bel Air',
        'English River',
        'Grand Anse Mahe',
        'Plaisance',
        'Port Glaud',
        'Takamaka',
        'Victoria',
        'Mahe',
        'Praslin',
        'La Digue'
    ];
    const JOB_TAG_OPTIONS = [
        'Work Experience',
        'Fresh Graduate',
        'Seychellois Only',
        'Open to Everyone'
    ];

    // Navigate helper
    function navigateTo(url) {
        if (typeof Livewire !== 'undefined' && Livewire.navigate) {
            Livewire.navigate(url);
        } else {
            window.location.href = url;
        }
    }

    // Fast fetch without loading overlay
    async function fetchFast(url) {
        const response = await fetch(url, { headers: { 'Accept': 'application/json' } });
        return response.json();
    }
    
    // Show skeleton loading
    function showSkeleton() {
        const container = document.getElementById('jobs-container');
        const template = document.getElementById('skeleton-template');
        if (container && template) {
            const skeleton = template.content.cloneNode(true);
            container.innerHTML = '';
            container.appendChild(skeleton);
        }
    }

    // Hide skeleton loading
    function hideSkeleton() {
        const skeletons = document.querySelectorAll('.skeleton-loader');
        skeletons.forEach(skeleton => skeleton.remove());
    }

    // Switch page layout based on selected view
    function applyLayoutForView() {
        const listColumn = document.getElementById('jobs-list-column');
        const detailColumn = document.getElementById('job-detail-column');
        if (!listColumn || !detailColumn) return;

        if (currentView === 'grid') {
            listColumn.classList.remove('lg:col-span-4');
            listColumn.classList.add('lg:col-span-12');
            detailColumn.classList.add('hidden');
        } else {
            listColumn.classList.remove('lg:col-span-12');
            listColumn.classList.add('lg:col-span-4');
            detailColumn.classList.remove('hidden');
            detailColumn.classList.add('lg:col-span-8');
        }
    }

    function closeFilterDropdownPanels(exceptId = null) {
        ['locationDropdownPanel', 'jobTagsDropdownPanel', 'salaryDropdownPanel'].forEach((id) => {
            if (id === exceptId) return;
            const panel = document.getElementById(id);
            if (panel) {
                panel.classList.add('hidden');
                panel.style.display = 'none';
            }
        });
    }

    function getSelectedLocations() {
        const hidden = document.getElementById('location');
        if (!hidden || !hidden.value) return [];
        return hidden.value.split(',').map((v) => v.trim()).filter(Boolean);
    }

    function setLocationValue(value) {
        const hidden = document.getElementById('location');
        const label = document.getElementById('locationDropdownLabel');
        const normalized = Array.isArray(value)
            ? value
            : (value === 'All Seychelles locations' ? [] : (value ? [value] : []));
        if (hidden) hidden.value = normalized.join(',');
        if (label) {
            if (normalized.length === 0) label.textContent = 'All Seychelles locations';
            else if (normalized.length === 1) label.textContent = normalized[0];
            else label.textContent = `${normalized.length} locations selected`;
        }
    }

    function renderLocationOptions(query = '') {
        const container = document.getElementById('locationOptionsContainer');
        const hidden = document.getElementById('location');
        if (!container || !hidden) return;

        const q = query.trim().toLowerCase();
        const selected = getSelectedLocations();
        const filtered = LOCATION_OPTIONS.filter((opt) => opt.toLowerCase().includes(q));

        container.innerHTML = filtered.map((opt) => {
            const checked = opt === 'All Seychelles locations'
                ? selected.length === 0
                : selected.includes(opt);
            return `
                <button
                    type="button"
                    class="w-full px-3 py-2 text-left hover:bg-gray-50 flex items-center gap-2 text-sm text-gray-700"
                    data-location-option="${opt}"
                >
                    <span class="inline-flex w-4 h-4 rounded border ${checked ? 'bg-blue-600 border-blue-600' : 'border-gray-300'} items-center justify-center flex-shrink-0">
                        ${checked ? '<svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>' : ''}
                    </span>
                    <span>${opt}</span>
                </button>
            `;
        }).join('');
    }

    function initializeLocationDropdown() {
        const button = document.getElementById('locationDropdownButton');
        const panel = document.getElementById('locationDropdownPanel');
        const input = document.getElementById('locationSearchInput');
        const hidden = document.getElementById('location');
        const optionsContainer = document.getElementById('locationOptionsContainer');
        if (!button || !panel || !input || !hidden || !optionsContainer) return;

        panel.onclick = function(e) { e.stopPropagation(); };
        panel.style.display = 'none';
        const isOpen = () => panel.style.display !== 'none';
        const closePanel = () => {
            panel.classList.add('hidden');
            panel.style.display = 'none';
        };

        const openPanel = () => {
            closeFilterDropdownPanels('locationDropdownPanel');
            panel.classList.remove('hidden');
            panel.style.display = 'block';
            renderLocationOptions(input.value);
            setTimeout(() => input.focus(), 0);
        };

        button.onclick = function() {
            if (!isOpen()) openPanel();
            else closePanel();
        };

        input.onclick = function(e) { e.stopPropagation(); };
        input.oninput = function() { renderLocationOptions(input.value); };

        optionsContainer.onclick = function(e) {
            e.stopPropagation();
            e.preventDefault();
            const optionBtn = e.target.closest('[data-location-option]');
            if (!optionBtn) return;
            const selectedOption = optionBtn.getAttribute('data-location-option') || '';
            const selected = getSelectedLocations();
            let next;
            if (selectedOption === 'All Seychelles locations') {
                next = [];
            } else if (selected.includes(selectedOption)) {
                next = selected.filter((s) => s !== selectedOption);
            } else {
                next = [...selected, selectedOption];
            }
            setLocationValue(next);
            renderLocationOptions(input.value);
        };

        if (!document.body.dataset.locationDropdownListenerAttached) {
            document.body.dataset.locationDropdownListenerAttached = 'true';
            document.addEventListener('click', function(e) {
                if (!panel.contains(e.target) && !button.contains(e.target)) {
                    closePanel();
                }
            });
        }

        if (!panel.classList.contains('hidden')) {
            renderLocationOptions(input.value);
        } else {
            renderLocationOptions('');
        }
    }

    function getSelectedJobTags() {
        const hidden = document.getElementById('job_tags');
        if (!hidden || !hidden.value) return [];
        return hidden.value.split(',').map((v) => v.trim()).filter(Boolean);
    }

    function setJobTagsValue(tags) {
        const hidden = document.getElementById('job_tags');
        const label = document.getElementById('jobTagsDropdownLabel');
        const normalized = Array.isArray(tags) ? tags : [];
        if (hidden) hidden.value = normalized.join(',');
        if (label) {
            if (normalized.length === 0) label.textContent = 'All job tags';
            else if (normalized.length === 1) label.textContent = normalized[0];
            else label.textContent = `${normalized.length} tags selected`;
        }
    }

    function renderJobTagOptions() {
        const container = document.getElementById('jobTagsOptionsContainer');
        if (!container) return;
        const selected = getSelectedJobTags();

        container.innerHTML = JOB_TAG_OPTIONS.map((tag) => {
            const checked = selected.includes(tag);
            return `
                <button
                    type="button"
                    class="w-full px-3 py-2 text-left hover:bg-gray-50 flex items-center gap-2 text-sm text-gray-700"
                    data-job-tag-option="${tag}"
                >
                    <span class="inline-flex w-4 h-4 rounded border ${checked ? 'bg-blue-600 border-blue-600' : 'border-gray-300'} items-center justify-center flex-shrink-0">
                        ${checked ? '<svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>' : ''}
                    </span>
                    <span>${tag}</span>
                </button>
            `;
        }).join('');
    }

    function initializeJobTagsDropdown() {
        const button = document.getElementById('jobTagsDropdownButton');
        const panel = document.getElementById('jobTagsDropdownPanel');
        const optionsContainer = document.getElementById('jobTagsOptionsContainer');
        if (!button || !panel || !optionsContainer) return;

        panel.onclick = function(e) { e.stopPropagation(); };
        panel.style.display = 'none';
        const isOpen = () => panel.style.display !== 'none';
        const closePanel = () => {
            panel.classList.add('hidden');
            panel.style.display = 'none';
        };
        const openPanel = () => {
            closeFilterDropdownPanels('jobTagsDropdownPanel');
            panel.classList.remove('hidden');
            panel.style.display = 'block';
            renderJobTagOptions();
        };

        button.onclick = function() {
            if (!isOpen()) openPanel();
            else closePanel();
        };

        optionsContainer.onclick = function(e) {
            e.stopPropagation();
            e.preventDefault();
            const optionBtn = e.target.closest('[data-job-tag-option]');
            if (!optionBtn) return;
            const tag = optionBtn.getAttribute('data-job-tag-option');
            const selected = getSelectedJobTags();
            const next = selected.includes(tag)
                ? selected.filter((t) => t !== tag)
                : [...selected, tag];
            setJobTagsValue(next);
            renderJobTagOptions();
        };

        if (!document.body.dataset.jobTagsDropdownListenerAttached) {
            document.body.dataset.jobTagsDropdownListenerAttached = 'true';
            document.addEventListener('click', function(e) {
                if (!panel.contains(e.target) && !button.contains(e.target)) {
                    closePanel();
                }
            });
        }

        renderJobTagOptions();
    }

    function setSalaryMinValue(value) {
        const hidden = document.getElementById('salary_min');
        const label = document.getElementById('salaryDropdownLabel');
        const input = document.getElementById('salaryMinInput');
        const numeric = (value || '').toString().trim();

        if (hidden) hidden.value = numeric;
        if (input) input.value = numeric;

        if (label) {
            if (!numeric) label.textContent = 'No minimum salary';
            else label.textContent = `SCR ${Number(numeric).toLocaleString()}`;
        }
    }

    function initializeSalaryDropdown() {
        const button = document.getElementById('salaryDropdownButton');
        const panel = document.getElementById('salaryDropdownPanel');
        const input = document.getElementById('salaryMinInput');
        const confirmBtn = document.getElementById('salaryConfirmButton');
        const periodHidden = document.getElementById('salary_period');
        if (!button || !panel || !input || !confirmBtn || !periodHidden) return;

        panel.onclick = function(e) { e.stopPropagation(); };
        panel.style.display = 'none';
        const isOpen = () => panel.style.display !== 'none';
        const closePanel = () => {
            panel.classList.add('hidden');
            panel.style.display = 'none';
        };
        const openPanel = () => {
            closeFilterDropdownPanels('salaryDropdownPanel');
            panel.classList.remove('hidden');
            panel.style.display = 'block';
            setTimeout(() => input.focus(), 0);
        };

        button.onclick = function() {
            if (!isOpen()) openPanel();
            else closePanel();
        };

        if (!confirmBtn.dataset.listenerAttached) {
            confirmBtn.dataset.listenerAttached = 'true';
            confirmBtn.addEventListener('click', function() {
                const raw = input.value.trim();
                const numeric = raw === '' ? '' : Math.max(0, parseInt(raw, 10) || 0).toString();
                setSalaryMinValue(numeric);

                const selectedRadio = document.querySelector('input[name="salary_period_choice"]:checked');
                periodHidden.value = selectedRadio ? selectedRadio.value : 'month';

                closePanel();
                searchJobs();
            });
        }

        if (!document.body.dataset.salaryDropdownListenerAttached) {
            document.body.dataset.salaryDropdownListenerAttached = 'true';
            document.addEventListener('click', function(e) {
                if (!panel.contains(e.target) && !button.contains(e.target)) {
                    closePanel();
                }
            });
        }
    }
    
    // Load all data in parallel
    async function loadPageData() {
        const requestId = Date.now();
        currentRequest = requestId;
        
        // Show skeleton loading
        showSkeleton();
        
        const params = new URLSearchParams(window.location.search);
        const keyword = params.get('keyword') || document.getElementById('keyword')?.value || '';
        const categoryId = params.get('category_id') || document.getElementById('category_id')?.value || '';
        const location = params.get('location') || document.getElementById('location')?.value || '';
        const employmentType = params.get('employment_type') || document.getElementById('employment_type')?.value || '';
        const salaryMin = params.get('salary_min') || document.getElementById('salary_min')?.value || '';
        const remoteOption = params.get('remote_option') || document.getElementById('remote_option')?.value || '';
        const jobTags = params.get('experience_tags') || document.getElementById('job_tags')?.value || '';
        const sortBy = params.get('sort') || document.getElementById('sortBy')?.value || 'latest';
        const page = params.get('page') || 1;
        
        // Build jobs URL with all parameters
        let jobsUrl = `${API_BASE}/jobs/search?per_page=16&page=${page}`;
        if (keyword) jobsUrl += `&keyword=${encodeURIComponent(keyword)}`;
        if (categoryId) jobsUrl += `&category_id=${categoryId}`;
        if (location) jobsUrl += `&location=${encodeURIComponent(location)}`;
        if (employmentType) jobsUrl += `&employment_type=${encodeURIComponent(employmentType)}`;
        if (salaryMin) jobsUrl += `&salary_min=${encodeURIComponent(salaryMin)}`;
        if (jobTags) jobsUrl += `&experience_tags=${encodeURIComponent(jobTags)}`;
        if (sortBy) jobsUrl += `&sort=${sortBy}`;
        if (remoteOption) {
            if (remoteOption === 'remote') jobsUrl += `&is_remote=1`;
            else if (remoteOption === 'hybrid') jobsUrl += `&employment_type=Hybrid`;
            else if (remoteOption === 'office') jobsUrl += `&is_remote=0`;
        }

        try {
            const [categories, jobs] = await Promise.all([
                fetchFast(`${API_BASE}/categories`),
                fetchFast(jobsUrl)
            ]);
            
            if (currentRequest !== requestId) return;
            
            renderCategories(categories, categoryId);
            lastJobsData = jobs;
            renderJobs(jobs);
            
            // Select first job if none selected
            if (!selectedJobId && jobs.data && jobs.data.length > 0) {
                selectJob(jobs.data[0].id);
            }
        } catch (error) {
            if (currentRequest === requestId) {
                console.error('Error loading data:', error);
                hideSkeleton();
                const container = document.getElementById('jobs-container');
                if (container) {
                    container.innerHTML = '<div class="text-center py-12 text-red-500"><p class="text-lg mb-2">Error loading jobs</p><p class="text-sm">Please try again later</p></div>';
                }
            }
        } finally {
            if (currentRequest === requestId) {
                currentRequest = null;
            }
        }
    }

    // Render categories dropdown
    function renderCategories(data, selectedId) {
        const select = document.getElementById('category_id');
        if (select && data.data) {
            select.innerHTML = '<option value="">All job categories</option>' +
                data.data.map(cat => `<option value="${cat.id}" ${selectedId == cat.id ? 'selected' : ''}>${cat.name}</option>`).join('');
        }
    }

    // Format date
    function formatDate(dateString) {
        if (!dateString) return '';
        const date = new Date(dateString);
        return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
    }

    // Format salary
    function formatSalary(job) {
        if (job.hide_salary) {
            return 'Negotiable';
        }
        if (job.salary_min && job.salary_max) {
            return `${job.currency || 'SCR'} ${parseInt(job.salary_min).toLocaleString()} - ${job.currency || 'SCR'} ${parseInt(job.salary_max).toLocaleString()} per month`;
        } else if (job.salary_min) {
            return `${job.currency || 'SCR'} ${parseInt(job.salary_min).toLocaleString()} per month`;
        } else if (job.salary_max) {
            return `Up to ${job.currency || 'SCR'} ${parseInt(job.salary_max).toLocaleString()} per month`;
        }
        return 'Salary not specified';
    }

    // Get work type display
    function getWorkType(job) {
        let types = [];
        if (job.is_remote) {
            types.push('Remote');
        } else if (job.employment_type && job.employment_type.toLowerCase().includes('hybrid')) {
            types.push('Hybrid');
        } else {
            types.push('Office');
        }
        return types.join(', ') || 'Not specified';
    }

    // Get experience level display
    function getExperienceLevel(job) {
        if (job.experience_level) {
            return `Work Experience, ${job.experience_level}`;
        }
        return 'Work Experience, Open to Everyone';
    }

    // Render jobs list
    function renderJobs(data) {
        const container = document.getElementById('jobs-container');
        
        // Hide skeleton loading
        hideSkeleton();
        
        // Update job count and page info
        if (data.data) {
            document.getElementById('job-count').textContent = `Showing ${data.data.length} Jobs`;
            document.getElementById('page-info').textContent = `Page ${data.current_page || 1} of ${data.last_page || 1}`;
        }
        
        // Apply correct classes based on view mode
        applyLayoutForView();
        if (currentView === 'grid') {
            container.className = 'bg-white border border-gray-200 rounded-md overflow-hidden';
        } else {
            container.className = 'space-y-3';
        }
        
        if (data.data && data.data.length > 0) {
            if (currentView === 'grid') {
                // Grid view - exact flat list design
                container.innerHTML = data.data.map((job, index) => `
                    <div class="job-card-grid ${selectedJobId == job.id ? 'bg-[#EAF2FF]' : 'bg-white'} ${index < data.data.length - 1 ? 'border-b border-gray-200' : ''} px-5 py-5 cursor-pointer" data-job-id="${job.id}">
                        <div class="flex items-start gap-4">
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 rounded-md flex items-center justify-center" style="background-color: #dbeafe;">
                                    <svg class="w-6 h-6" style="color: #2563eb;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-start justify-between gap-6">
                                    <div class="min-w-0 flex-1">
                                        <h3 class="job-title text-xl md:text-2xl leading-tight font-bold mb-1" style="color: #ec4899;">${job.title}</h3>
                                        <p class="text-sm text-gray-700 mb-3 line-clamp-1">${job.description ? job.description.substring(0, 140) + '...' : 'No description provided.'}</p>
                                        <div class="flex flex-wrap gap-2 mb-3">
                                            ${job.location ? `<span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full border border-gray-300 text-xs text-gray-700 bg-white">
                                                <svg class="w-3 h-3 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path></svg>
                                                ${job.location}
                                            </span>` : ''}
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full border border-gray-300 text-xs text-gray-700 bg-white">
                                                <svg class="w-3 h-3 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                ${job.employment_type || 'Full Time'}
                                            </span>
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full border border-gray-300 text-xs text-gray-700 bg-white">
                                                <svg class="w-3 h-3 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                                                ${getWorkType(job)}
                                            </span>
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full border border-gray-300 text-xs text-gray-700 bg-white">
                                                <svg class="w-3 h-3 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                                ${job.category?.name || 'Uncategorized'}
                                            </span>
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full border border-gray-300 text-xs text-gray-700 bg-white">
                                                <svg class="w-3 h-3 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                                ${getExperienceLevel(job)}
                                            </span>
                                        </div>
                                        <div class="flex items-center gap-1 text-sm font-semibold text-gray-800 mb-1">
                                            <span>${job.company?.name || 'Company'}</span>
                                            <svg class="w-3.5 h-3.5 flex-shrink-0" style="color: #2563eb;" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                        </div>
                                        <div class="space-y-1 text-xs text-gray-500">
                                            <div class="flex items-center gap-1.5">
                                                <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                Posted on ${formatDate(job.published_at || job.created_at)}
                                            </div>
                                            ${job.application_deadline ? `<div class="flex items-center gap-1.5">
                                                <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                Expiring on ${formatDate(job.application_deadline)}
                                            </div>` : ''}
                                        </div>
                                    </div>
                                    <div class="flex-shrink-0 text-right self-center">
                                        <p class="font-bold text-2xl md:text-3xl leading-tight" style="color: #ec4899;">${formatSalary(job)}</p>
                                        <p class="text-xs text-gray-500">/ month</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `).join('');
            } else {
                // List view rendering
                container.innerHTML = data.data.map((job, index) => `
                    <div class="job-card bg-white rounded-lg shadow-sm hover:shadow-md transition-all p-4 border-2 ${selectedJobId == job.id ? 'job-card-selected' : 'border-gray-100'} cursor-pointer" data-job-id="${job.id}">
                        <div class="flex gap-3">
                            <div class="flex-shrink-0">
                                <div class="w-10 h-10 rounded-lg flex items-center justify-center" style="background-color: #e0e7ff;">
                                    <svg class="w-5 h-5" style="color: #3b82f6;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="flex-1 min-w-0 space-y-1.5">
                                <div>
                                    <h3 class="job-title text-sm font-bold mb-0.5 ${selectedJobId == job.id ? '' : 'text-gray-900'}" style="${selectedJobId == job.id ? 'color:#ec4899' : ''}">${job.title}</h3>
                                    <div class="flex items-center gap-1">
                                        <span class="text-xs text-gray-600 font-medium">${job.company?.name || 'Company'}</span>
                                        <svg class="w-3.5 h-3.5 flex-shrink-0" style="color: #3b82f6;" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="space-y-1 text-xs text-gray-500">
                                    ${job.location ? `<div class="flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path></svg>
                                        <span class="truncate">${job.location}</span>
                                    </div>` : ''}
                                    ${!job.hide_salary && (job.salary_min || job.salary_max) ? `<div class="flex items-center gap-1.5" style="color: #ec4899;">
                                        <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                        <span class="font-medium">${formatSalary(job)}</span>
                                    </div>` : ''}
                                    <div class="flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                                        <span>${job.category?.name || 'Uncategorized'}</span>
                                    </div>
                                    <div class="flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                                        <span>${getWorkType(job)}</span>
                                    </div>
                                    <div class="flex flex-wrap gap-x-3 gap-y-1">
                                        <div class="flex items-center gap-1.5">
                                            <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            <span>${job.employment_type || 'full_time'}</span>
                                        </div>
                                        <div class="flex items-center gap-1.5">
                                            <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                            <span>${getExperienceLevel(job)}</span>
                                        </div>
                                    </div>
                                    ${job.application_deadline ? `<div class="flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                        <span>Expiring on ${formatDate(job.application_deadline)}</span>
                                    </div>` : ''}
                                </div>
                                <div class="flex flex-wrap gap-1.5 mt-1.5">
                                    ${job.category?.name ? `<span class="tag-pill px-2.5 py-0.5 rounded-full text-xs font-medium">${job.category.name.split(' ')[0]}</span>` : ''}
                                    ${job.is_remote ? '<span class="tag-pill px-2.5 py-0.5 rounded-full text-xs font-medium">Remote</span>' : ''}
                                    ${job.employment_type ? `<span class="tag-pill px-2.5 py-0.5 rounded-full text-xs font-medium">${job.employment_type}</span>` : ''}
                                </div>
                                <div class="flex items-center justify-between pt-2.5 border-t border-gray-50 mt-2">
                                    <span class="text-xs text-gray-400">Posted on ${formatDate(job.published_at || job.created_at)}</span>
                                    <button class="apply-now-btn pink-button text-white px-4 py-1.5 rounded-md text-xs font-semibold transition shadow-sm" data-job-id="${job.id}">
                                        Apply now
                                    </button>
                                </div>
                        </div>
                    </div>
                </div>
            `).join('');
            }
            
            // Add click handlers
            document.querySelectorAll('.job-card, .job-card-grid').forEach(card => {
                card.addEventListener('click', function(e) {
                    if (!e.target.closest('.apply-now-btn')) {
                        const jobId = this.dataset.jobId;
                        selectJob(jobId);
                    }
                });
            });

            // Add apply button handlers
            document.querySelectorAll('.apply-now-btn').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    const jobId = this.dataset.jobId;
                    handleApply(jobId);
                });
            });
            
            // Render pagination if available
            if (data.last_page > 1) {
                renderPagination(data);
            } else {
                document.getElementById('pagination').innerHTML = '';
            }
        } else {
            container.innerHTML = '<div class="text-center py-12 text-gray-500"><p class="text-lg mb-2">No jobs found</p><p class="text-sm">Try adjusting your search criteria</p></div>';
            document.getElementById('pagination').innerHTML = '';
        }
    }

    // Handle Apply button click - delegates to the shared global function
    function handleApply(jobId) {
        window.handleJobApply(jobId, currentJobData);
    }

    // Select and load job detail
    async function selectJob(jobId) {
        selectedJobId = jobId;
        
        // Update card selection for both list and grid views
        document.querySelectorAll('.job-card, .job-card-grid').forEach(card => {
            const titleElement = card.querySelector('.job-title');
            if (card.dataset.jobId == jobId) {
                if (card.classList.contains('job-card')) {
                    card.classList.add('job-card-selected');
                    card.classList.remove('border-gray-200');
                }
                if (titleElement) {
                    titleElement.style.color = '#ec4899';
                }
            } else {
                if (card.classList.contains('job-card')) {
                    card.classList.remove('job-card-selected');
                    card.classList.add('border-gray-200');
                }
                if (titleElement) {
                    titleElement.style.color = '#111827';
                }
            }
        });

        try {
            const response = await fetch(`${API_BASE}/jobs/${jobId}`, {
                headers: { 'Accept': 'application/json' }
            });
            
            if (!response.ok) throw new Error('Failed to load job');
            
            const result = await response.json();
            const job = result.data || result;
            
            // Hide skeleton, show content
            document.getElementById('job-detail-skeleton').classList.add('hidden');
            document.getElementById('job-detail-content').classList.remove('hidden');

            // Dynamic reviews from company
            const reviewStats = result.review_stats;
            if (reviewStats && reviewStats.review_count > 0) {
                document.getElementById('company-rating').textContent = reviewStats.avg_rating;
                document.getElementById('company-reviews').textContent = `${reviewStats.review_count} reviews`;
            } else {
                document.getElementById('company-rating').textContent = '-';
                document.getElementById('company-reviews').textContent = 'No reviews yet';
            }
            
            // Link "View all jobs" to company page
            const viewAllLink = document.querySelector('#job-detail-content a[href="#"]');
            if (viewAllLink && job.company?.slug) {
                viewAllLink.href = `/companies/${job.company.slug}`;
            }
            
            // Populate job detail
            document.getElementById('job-title').textContent = job.title;
            document.getElementById('company-name').textContent = job.company?.name || 'Company';
            document.getElementById('job-location').textContent = job.location || 'Location not specified';
            document.getElementById('job-salary').textContent = formatSalary(job);
            document.getElementById('job-category').textContent = job.category?.name || 'Uncategorized';
            document.getElementById('job-work-type').textContent = getWorkType(job);
            document.getElementById('job-employment-type').textContent = job.employment_type || 'Full Time';
            document.getElementById('job-experience').textContent = getExperienceLevel(job);
            
            const postedDate = formatDate(job.published_at || job.created_at);
            const expiryDate = job.application_deadline ? formatDate(job.application_deadline) : null;
            document.getElementById('job-dates').textContent = `Posted ${postedDate}${expiryDate ? ` - Expiring ${expiryDate}` : ''}`;
            
            // Applicant count
            const applicantCount = job.applications_count || Math.floor(Math.random() * 20);
            const applicantEl = document.getElementById('applicant-count');
            const applicantText = applicantCount < 20 
                ? `Under ${applicantCount} applicants so far. Your opportunity is still here!`
                : `${applicantCount}+ applicants`;
            applicantEl.innerHTML = `<svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path></svg> ${applicantText}`;
            
            // Job description
            const descElement = document.getElementById('job-description');
            if (job.description) {
                descElement.innerHTML = job.description.replace(/\n/g, '<br>');
            } else {
                descElement.innerHTML = '<p>No description available.</p>';
            }
            
            // Company logo (placeholder)
            const logoElement = document.getElementById('company-logo');
            if (job.company?.logo) {
                logoElement.src = job.company.logo;
                logoElement.alt = job.company.name;
            } else {
                logoElement.src = 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iODAiIGhlaWdodD0iODAiIHZpZXdCb3g9IjAgMCA4MCA4MCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHJlY3Qgd2lkdGg9IjgwIiBoZWlnaHQ9IjgwIiBmaWxsPSIjRjNGNEY2Ii8+CjxwYXRoIGQ9Ik00MCA0MEM0NS41MjI4IDQwIDUwIDM2LjQxODMgNTAgMzJDNTAgMjcuNTgxNyA0NS41MjI4IDI0IDQwIDI0QzM0LjQ3NzIgMjQgMzAgMjcuNTgxNyAzMCAzMkMzMCAzNi40MTgzIDM0LjQ3NzIgNDAgNDAgNDBaIiBmaWxsPSIjOTk5Ii8+CjxwYXRoIGQ9Ik00MCA1MEM1MC44MzY2IDUwIDU4IDQ3LjMxMzcgNTggNDRWNDJINThWNDRDNTggNDYuNjg2MyA1MC44MzY2IDUwIDQwIDUwQzI5LjE2MzQgNTAgMjIgNDYuNjg2MyAyMiA0NFY0NkgyMlY0NEMyMiA0Ny4zMTM3IDI5LjE2MzQgNTAgNDAgNTBaIiBmaWxsPSIjOTk5Ii8+Cjwvc3ZnPgo=';
            }
            
            // Skills section - use requirements or hardcoded fallback
            let skillsData = [];
            if (job.requirements) {
                const parsed = typeof job.requirements === 'string' ? job.requirements.split(',').map(s => s.trim()).filter(Boolean) : [];
                if (parsed.length > 0 && parsed[0].length < 50) skillsData = parsed;
            }
            if (skillsData.length === 0) {
                skillsData = ['IT Governance', 'Cybersecurity', 'Project Management', 'Leadership', 'Cloud Computing'];
            }
            const skillsContainer = document.getElementById('job-skills');
            const skillsSection = document.getElementById('skills-section');
            skillsContainer.innerHTML = skillsData.map(skill => 
                `<span class="tag-pill px-3 py-1.5 rounded-md text-xs font-medium">${skill}</span>`
            ).join('');
            skillsSection.classList.remove('hidden');
            
            // About Company
            document.getElementById('about-company-name').textContent = job.company?.name || 'Company';
            document.getElementById('about-company-type').textContent = job.company?.industry || 'Small-Medium Enterprise';
            document.getElementById('about-company-size').textContent = job.company?.size || '21-100';
            
            document.getElementById('about-company-jobs').textContent = result.total_company_jobs || '0';
            
            // Go to Employer link
            const goToEmployerLink = document.getElementById('go-to-employer-link');
            if (goToEmployerLink && job.company?.slug) {
                goToEmployerLink.href = `/companies/${job.company.slug}`;
            }
            
            // Company Social Media icons
            const socialsContainer = document.getElementById('about-company-socials');
            if (socialsContainer) {
                let socialsHTML = '';
                const co = job.company || {};
                if (co.facebook) {
                    socialsHTML += `<a href="${co.facebook}" target="_blank" class="w-8 h-8 rounded-full flex items-center justify-center" style="background:#ec4899;"><svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg></a>`;
                }
                if (co.linkedin) {
                    socialsHTML += `<a href="${co.linkedin}" target="_blank" class="w-8 h-8 rounded-full flex items-center justify-center" style="background:#0077b5;"><svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg></a>`;
                }
                if (co.twitter) {
                    socialsHTML += `<a href="${co.twitter}" target="_blank" class="w-8 h-8 rounded-full flex items-center justify-center" style="background:#1da1f2;"><svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/></svg></a>`;
                }
                if (co.website) {
                    socialsHTML += `<a href="${co.website}" target="_blank" class="w-8 h-8 rounded-full flex items-center justify-center" style="background:#ec4899;"><svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path></svg></a>`;
                }
                if (!socialsHTML) {
                    socialsHTML = '<span class="text-xs text-gray-400">No social links</span>';
                }
                socialsContainer.innerHTML = socialsHTML;
            }
            
            // Employer Questions
            const questionsSection = document.getElementById('employer-questions-section');
            const questionsList = document.getElementById('employer-questions-list');
            const appQuestions = job.application_questions;
            if (appQuestions && Array.isArray(appQuestions) && appQuestions.length > 0) {
                questionsList.innerHTML = appQuestions.map(q => {
                    const qText = typeof q === 'string' ? q : (q.question || q.text || JSON.stringify(q));
                    return `<li class="flex items-start gap-2">
                        <span class="w-1.5 h-1.5 rounded-full mt-1.5 flex-shrink-0" style="background:#ec4899;"></span>
                        <span>${qText}</span>
                    </li>`;
                }).join('');
                questionsSection.classList.remove('hidden');
            } else {
                const defaultQuestions = [
                    "What's your expected monthly basic salary?",
                    "Which of the following statements best describes your right to work in Seychelles?",
                    "Which of the following types of qualifications do you have?",
                    "How many years' experience do you have in a similar role?"
                ];
                questionsList.innerHTML = defaultQuestions.map(q => 
                    `<li class="flex items-start gap-2">
                        <span class="w-1.5 h-1.5 rounded-full mt-1.5 flex-shrink-0" style="background:#ec4899;"></span>
                        <span>${q}</span>
                    </li>`
                ).join('');
                questionsSection.classList.remove('hidden');
            }
            
            // Featured Jobs (similar jobs)
            const featuredSection = document.getElementById('featured-jobs-section');
            const featuredContainer = document.getElementById('featured-jobs-container');
            const similarJobs = result.similar_jobs;
            if (similarJobs && similarJobs.length > 0) {
                featuredContainer.innerHTML = similarJobs.slice(0, 2).map(sj => {
                    const sjLogo = sj.company?.logo 
                        ? `<img src="${sj.company.logo}" alt="" class="w-full h-full object-cover rounded">`
                        : `<div class="w-full h-full bg-gray-200 rounded flex items-center justify-center"><svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg></div>`;
                    const sjDate = sj.published_at ? formatDate(sj.published_at) : '';
                    return `<div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition cursor-pointer" onclick="selectJob(${sj.id})">
                        <div class="flex items-start gap-3">
                            <div class="w-12 h-12 flex-shrink-0 rounded overflow-hidden bg-gray-100">${sjLogo}</div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-start justify-between">
                                    <h4 class="text-sm font-bold text-gray-900 line-clamp-2">${sj.title || 'Job Title'}</h4>
                                    <svg class="w-4 h-4 text-yellow-400 flex-shrink-0 ml-1" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                                </div>
                                <p class="text-xs text-gray-500 mt-0.5">${sj.company?.name || ''}</p>
                                <p class="text-xs text-gray-400 mt-0.5">${sj.location || ''}</p>
                            </div>
                        </div>
                        <p class="text-xs text-gray-400 mt-3">${sjDate}</p>
                    </div>`;
                }).join('');
                featuredSection.classList.remove('hidden');
            } else {
                featuredSection.classList.add('hidden');
            }
            
            // Store current job data for the apply modal
            currentJobData = job;

            // Apply button handler
            document.getElementById('apply-job-btn').onclick = () => {
                handleApply(jobId);
            };
            
        } catch (error) {
            console.error('Error loading job detail:', error);
        }
    }

    // Render pagination
    function renderPagination(data) {
        const container = document.getElementById('pagination');
        if (!container) return;
        
        const pages = [];
        const currentPage = data.current_page || 1;
        const lastPage = data.last_page || 1;
        
        // Previous button
        if (currentPage > 1) {
            pages.push(`<button onclick="goToPage(${currentPage - 1})" class="px-4 py-2 bg-white text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 transition">Previous</button>`);
        }
        
        // Page numbers
        for (let i = 1; i <= lastPage; i++) {
            if (i === 1 || i === lastPage || (i >= currentPage - 2 && i <= currentPage + 2)) {
                const isActive = i === currentPage;
                pages.push(`<button onclick="goToPage(${i})" class="px-4 py-2 ${isActive ? 'text-white' : 'bg-white text-gray-700 hover:bg-gray-50'} border border-gray-300 rounded-lg transition" ${isActive ? 'style="background: linear-gradient(135deg, #374151 0%, #1f2937 100%);"' : ''}>${i}</button>`);
            } else if (i === currentPage - 3 || i === currentPage + 3) {
                pages.push(`<span class="px-2 text-gray-500">...</span>`);
            }
        }
        
        // Next button
        if (currentPage < lastPage) {
            pages.push(`<button onclick="goToPage(${currentPage + 1})" class="px-4 py-2 bg-white text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 transition">Next</button>`);
        }
        
        container.innerHTML = `
            <div class="flex justify-center items-center gap-2">
                ${pages.join('')}
            </div>
        `;
    }

    // Go to page
    function goToPage(page) {
        const params = new URLSearchParams(window.location.search);
        params.set('page', page);
        navigateTo(`/jobs?${params.toString()}`);
    }

    // Search jobs
    async function searchJobs() {
        const params = new URLSearchParams();
        
        const keyword = document.getElementById('keyword').value;
        const categoryId = document.getElementById('category_id').value;
        const location = document.getElementById('location').value;
        const salaryMin = document.getElementById('salary_min').value;
        const salaryPeriod = document.getElementById('salary_period')?.value || 'month';
        const employmentType = document.getElementById('employment_type').value;
        const remoteOption = document.getElementById('remote_option').value;
        const jobTags = document.getElementById('job_tags').value;
        const sortBy = document.getElementById('sortBy').value;
        
        if (keyword) params.append('keyword', keyword);
        if (categoryId) params.append('category_id', categoryId);
        if (location) params.append('location', location);
        if (salaryMin) params.append('salary_min', salaryMin);
        if (salaryMin && salaryPeriod) params.append('salary_period', salaryPeriod);
        if (employmentType) params.append('employment_type', employmentType);
        if (remoteOption) params.append('remote_option', remoteOption);
        if (jobTags) params.append('experience_tags', jobTags);
        if (sortBy) params.append('sort', sortBy);
        
        params.set('page', '1');
        
        const newUrl = `/jobs?${params.toString()}`;
        if (typeof window.history !== 'undefined' && window.history.pushState) {
            window.history.pushState({}, '', newUrl);
        }
        
        await loadPageData();
    }

    // Initialize event listeners (use event delegation to avoid duplicate listeners)
    function initializeEventListeners() {
        closeFilterDropdownPanels();
        initializeLocationDropdown();
        initializeJobTagsDropdown();
        initializeSalaryDropdown();

        // Use event delegation for buttons to avoid duplicate listeners
        const findJobsBtn = document.getElementById('findJobsBtn');
        if (findJobsBtn && !findJobsBtn.dataset.listenerAttached) {
            findJobsBtn.dataset.listenerAttached = 'true';
            findJobsBtn.addEventListener('click', searchJobs);
        }
        
        // Single toggle view button
        const toggleViewBtn = document.getElementById('toggleViewBtn');
        if (toggleViewBtn) {
            toggleViewBtn.onclick = function() {
                currentView = currentView === 'list' ? 'grid' : 'list';
                toggleViewBtn.classList.toggle('bg-gray-100', currentView === 'grid');
                applyLayoutForView();
                if (lastJobsData) {
                    renderJobs(lastJobsData);
                } else {
                    loadPageData();
                }
            };
        }
        
        // Popular search buttons (use event delegation)
        document.querySelectorAll('.popular-search').forEach(btn => {
            if (!btn.dataset.listenerAttached) {
                btn.dataset.listenerAttached = 'true';
                btn.addEventListener('click', function() {
        const keywordInput = document.getElementById('keyword');
                    if (keywordInput) {
                        keywordInput.value = this.dataset.keyword || '';
                        searchJobs();
                    }
                });
            }
        });
        
        // Filter change listeners
        ['category_id', 'employment_type', 'remote_option', 'sortBy'].forEach(id => {
            const element = document.getElementById(id);
            if (element && !element.dataset.listenerAttached) {
                element.dataset.listenerAttached = 'true';
                element.addEventListener('change', searchJobs);
            }
        });
        
        // Load URL parameters into form fields
        const urlParams = new URLSearchParams(window.location.search);
        ['keyword', 'category_id', 'employment_type', 'remote_option', 'sortBy'].forEach(param => {
            const element = document.getElementById(param);
            if (element && urlParams.get(param)) {
                element.value = urlParams.get(param);
            }
        });

        setSalaryMinValue(urlParams.get('salary_min') || '');
        const salaryPeriod = urlParams.get('salary_period') || 'month';
        const periodRadio = document.querySelector(`input[name="salary_period_choice"][value="${salaryPeriod}"]`);
        if (periodRadio) {
            periodRadio.checked = true;
            const periodHidden = document.getElementById('salary_period');
            if (periodHidden) periodHidden.value = salaryPeriod;
        }

        const urlLocations = (urlParams.get('location') || '')
            .split(',')
            .map((v) => v.trim())
            .filter(Boolean);
        setLocationValue(urlLocations);
        const urlTags = (urlParams.get('experience_tags') || '')
            .split(',')
            .map((v) => v.trim())
            .filter(Boolean);
        setJobTagsValue(urlTags);
        const locationSearchInput = document.getElementById('locationSearchInput');
        if (locationSearchInput) {
            locationSearchInput.value = '';
        }
        renderLocationOptions('');
        renderJobTagOptions();
    }

    // Initialize function that runs on both initial load and navigation
    function initialize() {
        initializeEventListeners();
        loadPageData();
    }

    // Initialize on page load
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initialize);
    } else {
        initialize();
    }

    // Re-initialize on Livewire navigation
    if (typeof Livewire !== 'undefined') {
    document.addEventListener('livewire:navigated', function() {
        currentRequest = null;
            // Clear listener flags so they can be re-attached
            document.querySelectorAll('[data-listener-attached]').forEach(el => {
                delete el.dataset.listenerAttached;
            });
            setTimeout(initialize, 100);
        });
    }
    
    // Handle browser back/forward
    window.addEventListener('popstate', function() {
        currentRequest = null;
        // Clear listener flags
        document.querySelectorAll('[data-listener-attached]').forEach(el => {
            delete el.dataset.listenerAttached;
        });
        setTimeout(initialize, 100);
    });
</script>
@endpush
@endsection
