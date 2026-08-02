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

<div class="min-h-screen bg-gray-100 dark:bg-gray-800">
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
                            class="w-full bg-white dark:bg-gray-800 text-gray-900 dark:text-white border border-gray-300 dark:border-gray-600 rounded-lg pl-9 pr-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-pink-500"
                        >
                    </div>
                </div>

                <!-- All Job Categories -->
                <div style="flex: 1 1 0%; min-width: 0;">
                    <select id="category_id" class="w-full bg-white dark:bg-gray-800 text-gray-900 dark:text-white border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-pink-500">
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
                            class="w-full bg-white dark:bg-gray-800 text-gray-900 dark:text-white border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2.5 text-sm flex items-center justify-between focus:outline-none focus:ring-2 focus:ring-pink-500"
                        >
                            <span id="locationDropdownLabel" class="truncate">All Seychelles locations</span>
                            <svg class="w-4 h-4 text-gray-500 dark:text-gray-400 flex-shrink-0 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div id="locationDropdownPanel" class="hidden absolute z-50 mt-2 w-full bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg shadow-lg">
                            <div class="p-2 border-b border-gray-100 dark:border-gray-700">
                                <div class="relative">
                                    <svg class="absolute left-2.5 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                    <input
                                        type="text"
                                        id="locationSearchInput"
                                        placeholder="Search"
                                        class="w-full border border-gray-300 dark:border-gray-600 rounded-md pl-8 pr-3 py-1.5 text-sm text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-pink-500"
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
                                class="w-full bg-white dark:bg-gray-800 text-gray-900 dark:text-white border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-2.5 text-sm flex items-center justify-between focus:outline-none focus:ring-2 focus:ring-pink-500"
                            >
                                <span id="salaryDropdownLabel">No minimum salary</span>
                                <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                            <div id="salaryDropdownPanel" class="hidden absolute z-50 mt-2 w-full bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg shadow-lg p-3">
                                <p class="text-sm font-medium text-gray-900 dark:text-white mb-2">Enter minimum amount (SCR)</p>
                                <input
                                    type="number"
                                    id="salaryMinInput"
                                    min="0"
                                    step="1"
                                    placeholder="0"
                                    class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 text-sm text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-pink-500 mb-3"
                                >
                                <div class="flex items-center gap-4 mb-3">
                                    <label class="inline-flex items-center gap-1.5 text-sm text-gray-700 dark:text-gray-300 cursor-pointer">
                                        <input type="radio" name="salary_period_choice" value="month" class="text-pink-500 focus:ring-pink-500" checked>
                                        <span>per month</span>
                                    </label>
                                    <label class="inline-flex items-center gap-1.5 text-sm text-gray-700 dark:text-gray-300 cursor-pointer">
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
                        <select id="employment_type" class="w-full bg-white dark:bg-gray-800 text-gray-900 dark:text-white border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-pink-500">
                            <option value="">All job types</option>
                            <option value="Full Time">Full Time</option>
                            <option value="Part Time">Part Time</option>
                            <option value="Contract">Contract</option>
                            <option value="Freelance">Freelance</option>
                        </select>
                    </div>

                    <!-- All Remote Options -->
                    <div>
                        <select id="remote_option" class="w-full bg-white dark:bg-gray-800 text-gray-900 dark:text-white border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-pink-500">
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
                                class="w-full bg-white dark:bg-gray-800 text-gray-900 dark:text-white border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-2.5 text-sm flex items-center justify-between focus:outline-none focus:ring-2 focus:ring-pink-500"
                            >
                                <span id="jobTagsDropdownLabel">All job tags</span>
                                <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                            <div id="jobTagsDropdownPanel" class="hidden absolute z-50 mt-2 w-full bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg shadow-lg">
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
                        <select id="sortBy" class="border border-gray-200 dark:border-gray-700 rounded-lg px-2.5 py-1.5 text-xs text-gray-600 dark:text-gray-400 bg-white dark:bg-gray-800 focus:outline-none">
                            <option value="latest">Sort by: Latest jobs</option>
                            <option value="oldest">Sort by: Oldest jobs</option>
                            <option value="salary_high">Sort by: Highest salary</option>
                            <option value="salary_low">Sort by: Lowest salary</option>
                        </select>
                        <button id="toggleViewBtn" class="p-1.5 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition" title="Toggle view">
                            <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h8m-8 6h16"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Job Count and Pagination Info -->
                <div class="flex items-center justify-between mb-3 text-xs text-gray-500 dark:text-gray-400">
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
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm dark:shadow-none p-6 border border-gray-200 dark:border-gray-700 animate-pulse">
                            <div class="flex gap-4">
                                <div class="w-12 h-12 bg-gray-200 dark:bg-gray-700 rounded-full flex-shrink-0"></div>
                                <div class="flex-1 space-y-3">
                                    <div class="h-5 bg-gray-200 dark:bg-gray-700 rounded w-2/3"></div>
                                    <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-1/3"></div>
                                    <div class="flex gap-4">
                                        <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-24"></div>
                                        <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-32"></div>
                                    </div>
                                    <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-20"></div>
                                    <div class="flex gap-2">
                                        <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-16"></div>
                                        <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-20"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Skeleton Card 2 -->
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm dark:shadow-none p-6 border border-gray-200 dark:border-gray-700 animate-pulse">
                            <div class="flex gap-4">
                                <div class="w-12 h-12 bg-gray-200 dark:bg-gray-700 rounded-full flex-shrink-0"></div>
                                <div class="flex-1 space-y-3">
                                    <div class="h-5 bg-gray-200 dark:bg-gray-700 rounded w-3/4"></div>
                                    <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-1/4"></div>
                                    <div class="flex gap-4">
                                        <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-28"></div>
                                        <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-36"></div>
                                    </div>
                                    <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-24"></div>
                                    <div class="flex gap-2">
                                        <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-20"></div>
                                        <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-16"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Skeleton Card 3 -->
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm dark:shadow-none p-6 border border-gray-200 dark:border-gray-700 animate-pulse">
                            <div class="flex gap-4">
                                <div class="w-12 h-12 bg-gray-200 dark:bg-gray-700 rounded-full flex-shrink-0"></div>
                                <div class="flex-1 space-y-3">
                                    <div class="h-5 bg-gray-200 dark:bg-gray-700 rounded w-1/2"></div>
                                    <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-2/5"></div>
                                    <div class="flex gap-4">
                                        <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-20"></div>
                                        <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-28"></div>
                                    </div>
                                    <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-28"></div>
                                    <div class="flex gap-2">
                                        <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-24"></div>
                                        <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-20"></div>
                                    </div>
                </div>
            </div>
        </div>
                        <!-- Skeleton Card 4 -->
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm dark:shadow-none p-6 border border-gray-200 dark:border-gray-700 animate-pulse">
                            <div class="flex gap-4">
                                <div class="w-12 h-12 bg-gray-200 dark:bg-gray-700 rounded-full flex-shrink-0"></div>
                                <div class="flex-1 space-y-3">
                                    <div class="h-5 bg-gray-200 dark:bg-gray-700 rounded w-3/5"></div>
                                    <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-1/3"></div>
                                    <div class="flex gap-4">
                        <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-24"></div>
                        <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-32"></div>
                                    </div>
                                    <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-20"></div>
                                    <div class="flex gap-2">
                                        <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-16"></div>
                                        <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-24"></div>
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
                <div id="job-detail-container" class="bg-white dark:bg-gray-800 rounded-lg shadow-sm dark:shadow-none sticky top-4 max-h-[calc(100vh-2rem)] overflow-y-auto">
                    <!-- Skeleton for job detail -->
                    <div id="job-detail-skeleton" class="animate-pulse">
                        <div class="h-44 bg-gray-200 dark:bg-gray-700 rounded-t-lg"></div>
                        <div class="px-6 pt-10 pb-6 space-y-4">
                            <div class="h-6 bg-gray-200 dark:bg-gray-700 rounded w-3/4"></div>
                            <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-1/2"></div>
                            <div class="h-8 bg-gray-200 dark:bg-gray-700 rounded w-20"></div>
                            <div class="h-px bg-gray-100 dark:bg-gray-800 my-2"></div>
                            <div class="space-y-3">
                                <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-2/3"></div>
                                <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-1/2"></div>
                                <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-3/5"></div>
                    <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-1/2"></div>
                                <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-2/3"></div>
                            </div>
                            <div class="h-10 bg-gray-200 dark:bg-gray-700 rounded w-32 ml-auto"></div>
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
                                <div class="w-14 h-14 bg-white dark:bg-gray-800 rounded-xl shadow-md p-1.5 flex items-center justify-center border border-gray-100 dark:border-gray-700">
                                    <img id="company-logo" src="" alt="Company Logo" class="w-full h-full object-contain rounded-lg">
                                </div>
                                <div class="flex items-center gap-2">
                                    <button class="w-8 h-8 border border-gray-200 dark:border-gray-700 rounded-md flex items-center justify-center hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                                        <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path></svg>
                                    </button>
                                    <button class="w-8 h-8 border border-gray-200 dark:border-gray-700 rounded-md flex items-center justify-center hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                                        <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"></path></svg>
                                    </button>
                                    <button class="w-8 h-8 border border-gray-200 dark:border-gray-700 rounded-md flex items-center justify-center hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                                        <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                                    </button>
                                    <button class="w-8 h-8 border border-gray-200 dark:border-gray-700 rounded-md flex items-center justify-center hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                                        <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9"></path></svg>
                                    </button>
                                </div>
                            </div>

                            <!-- Job Title -->
                            <h2 id="job-title" class="text-xl font-bold text-gray-900 dark:text-white mb-1"></h2>
                            <!-- Company Name & Verified -->
                            <div class="flex items-center gap-2 mb-2">
                                <span id="company-name" class="text-sm text-gray-700 dark:text-gray-300 font-medium"></span>
                                <svg class="w-4 h-4 flex-shrink-0" style="color: #3b82f6;" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <!-- Follow button -->
                            <button id="follow-btn" class="text-xs font-semibold px-4 py-1.5 rounded-md transition mb-3" style="background-color: #3b82f6; color: white;">Follow</button>

                            <!-- Rating row -->
                            <div class="flex items-center gap-2 mb-5 pb-4 border-b border-gray-100 dark:border-gray-700">
                                <svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                                <span id="company-rating" class="text-sm font-bold text-gray-900 dark:text-white">4.6</span>
                                <span id="company-reviews" class="text-xs text-gray-500 dark:text-gray-400">98 reviews</span>
                                <span class="text-xs text-gray-300 mx-1">•</span>
                                <a href="#" class="text-xs text-blue-600 hover:underline font-medium">View all jobs</a>
                            </div>

                            <!-- Key Details -->
                            <div class="space-y-3.5 mb-5">
                                <div class="flex items-center gap-3 text-sm text-gray-700 dark:text-gray-300">
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
                                <div class="flex items-center gap-3 text-sm text-gray-600 dark:text-gray-400">
                                    <svg class="w-5 h-5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                    </svg>
                                    <span id="job-category"></span>
                                </div>
                                <div class="flex items-center gap-3 text-sm text-gray-600 dark:text-gray-400">
                                    <svg class="w-5 h-5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                    </svg>
                                    <span id="job-work-type"></span>
                                </div>
                                <div class="flex items-center gap-3 text-sm text-gray-600 dark:text-gray-400">
                                    <svg class="w-5 h-5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                    </svg>
                                    <span id="job-employment-type"></span>
                                </div>
                                <div class="flex items-center gap-3 text-sm text-gray-600 dark:text-gray-400">
                                    <svg class="w-5 h-5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                    </svg>
                                    <span id="job-experience"></span>
                                </div>
                                <div class="flex items-center gap-3 text-sm text-gray-500 dark:text-gray-400">
                                    <svg class="w-5 h-5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span id="job-dates"></span>
                                </div>
                            </div>

                            <!-- Applicant Count + Apply Button row -->
                            <div class="flex items-center justify-between mb-6 pb-5 border-b border-gray-100 dark:border-gray-700">
                                <p id="applicant-count" class="text-sm text-pink-600 font-medium flex items-center gap-1.5">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                </p>
                                <button id="apply-job-btn" class="pink-button text-white font-semibold py-2.5 px-8 rounded-lg transition shadow-sm dark:shadow-none text-sm">
                                    Apply Job
                                </button>
                            </div>

                            <!-- Job Description -->
                            <div class="mb-6">
                                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-3">Job Description</h3>
                                <div id="job-description" class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed"></div>
                            </div>

                            <!-- Skills Section -->
                            <div id="skills-section" class="mb-6 hidden">
                                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-3">Skills</h3>
                                <div id="job-skills" class="flex flex-wrap gap-2"></div>
                            </div>

                            <!-- About Company Section -->
                            <div id="about-company-section" class="mb-6">
                                <div class="flex items-center justify-between mb-3">
                                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">About Company</h3>
                                    <a id="go-to-employer-link" href="#" class="text-sm text-blue-600 hover:underline font-medium flex items-center gap-1">
                                        Go to Employer
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                    </a>
                                </div>
                                <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-5 space-y-4">
                                    <div class="grid grid-cols-3 gap-4 text-sm">
                                        <div>
                                            <p class="text-gray-500 dark:text-gray-400 mb-1">Company Name</p>
                                            <p id="about-company-name" class="font-semibold text-gray-900 dark:text-white"></p>
                                        </div>
                                        <div>
                                            <p class="text-gray-500 dark:text-gray-400 mb-1">Company Type</p>
                                            <p id="about-company-type" class="font-semibold text-gray-900 dark:text-white"></p>
                                        </div>
                                        <div>
                                            <p class="text-gray-500 dark:text-gray-400 mb-1">Company Size</p>
                                            <p id="about-company-size" class="font-semibold text-gray-900 dark:text-white"></p>
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-3 gap-4 text-sm">
                                        <div>
                                            <p class="text-gray-500 dark:text-gray-400 mb-1">Job Vacancies</p>
                                            <p id="about-company-jobs" class="font-semibold text-gray-900 dark:text-white">0</p>
                                        </div>
                                        <div class="col-span-2">
                                            <p class="text-gray-500 dark:text-gray-400 mb-1">Company Social Media</p>
                                            <div id="about-company-socials" class="flex items-center gap-2 mt-1"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Employer Questions -->
                            <div id="employer-questions-section" class="mb-6 hidden">
                                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-3">Employer questions</h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">Your application will include the following questions:</p>
                                <ul id="employer-questions-list" class="text-sm text-gray-700 dark:text-gray-300 space-y-2"></ul>
                            </div>

                            <!-- Featured Jobs -->
                            <div id="featured-jobs-section" class="mb-6 hidden">
                                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Featured jobs</h3>
                                <div id="featured-jobs-container" class="grid grid-cols-1 sm:grid-cols-2 gap-4"></div>
                            </div>

                            <!-- Stay Cautious -->
                            <div class="mb-6 bg-blue-50 border border-blue-200 rounded-lg p-5">
                                <h4 class="text-base font-bold text-gray-900 dark:text-white mb-2">Stay cautious</h4>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">Protect yourself: never share your bank or credit card details when applying for jobs. Report any suspicious job postings immediately</p>
                                <div class="flex items-center gap-4">
                                    <button id="report-job-btn" class="px-4 py-2 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700 transition flex items-center gap-1.5">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9"></path></svg>
                                        Report Job
                                    </button>
                                    <a href="#" class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 font-medium">Learn More</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                </div>
            </div>
        </div>
</div>


@endsection
