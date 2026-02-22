<?php $__env->startSection('content'); ?>
<style>
    /* Exact color matching from design */
    .search-header-bg {
        background: linear-gradient(135deg, #374151 0%, #1f2937 100%);
    }
    .job-card-selected {
        border-left: 4px solid #ec4899;
    }
    .job-card-selected .job-title {
        color: #ec4899;
    }
    .tag-pill {
        background-color: #fce7f3;
        color: #831843;
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
    .tab-all-jobs {
        background: linear-gradient(135deg, #374151 0%, #1f2937 100%);
    }
    .pink-button {
        background-color: #ec4899;
    }
    .pink-button:hover {
        background-color: #db2777;
    }
</style>

<div class="min-h-screen bg-gray-100">
    <!-- Top Search and Filter Bar - Exact Dark Blue Background -->
    <div class="py-6 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto search-header-bg text-white rounded-lg py-6 px-6">
            <!-- Preferred Work Location -->
            <div class="mb-4">
                <label class="block text-xs font-medium mb-1.5 text-white">Preferred work location <span class="text-white">Select location ▼</span></label>
    </div>

            <!-- First Filter Row -->
            <div class="grid grid-cols-1 md:grid-cols-12 gap-3 mb-3">
                <!-- Search Keyword -->
                <div class="col-span-12 md:col-span-3">
                    <div class="relative">
                        <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                <input 
                    type="text" 
                    id="keyword"
                    placeholder="Search keyword" 
                            class="w-full bg-white text-gray-900 border border-gray-300 rounded-lg pl-10 pr-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-pink-500"
                >
            </div>
                </div>

                <!-- All Job Categories -->
                <div class="col-span-12 md:col-span-3">
                    <select id="category_id" class="w-full bg-white text-gray-900 border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-pink-500">
                        <option value="">All job categories</option>
                </select>
            </div>

                <!-- All Seychelles Locations -->
                <div class="col-span-12 md:col-span-3">
                    <select id="location" class="w-full bg-white text-gray-900 border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-pink-500">
                        <option value="">All Seychelles locations</option>
                        <option value="Central Region">Central Region</option>
                        <option value="East Region">East Region</option>
                        <option value="West Region">West Region</option>
                        <option value="North Region">North Region</option>
                        <option value="South Region">South Region</option>
                </select>
            </div>

                <!-- Find Jobs Button -->
                <div class="col-span-12 md:col-span-3">
                    <button type="button" id="findJobsBtn" class="w-full pink-button text-white font-semibold py-2.5 px-6 rounded-lg transition shadow-md hover:shadow-lg">
                        Find jobs
                </button>
                </div>
            </div>

            <!-- Second Filter Row -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-5">
                <!-- No Minimum Salary -->
                <div>
                    <select id="salary_min" class="w-full bg-white text-gray-900 border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-pink-500">
                        <option value="">No minimum salary</option>
                        <option value="5000">SCR 5,000+</option>
                        <option value="10000">SCR 10,000+</option>
                        <option value="15000">SCR 15,000+</option>
                        <option value="20000">SCR 20,000+</option>
                        <option value="25000">SCR 25,000+</option>
                    </select>
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
                    <select id="job_tags" class="w-full bg-white text-gray-900 border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-pink-500">
                        <option value="">All job tags</option>
                    </select>
                </div>
            </div>

            <!-- Popular Searches -->
            <div>
                <p class="text-xs font-medium mb-2 text-white">Popular searches</p>
                <div class="flex flex-wrap gap-2">
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
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Section - Job Listings (2/3 width) -->
            <div class="lg:col-span-2">
                <!-- Tabs and Sort Controls -->
                <div class="bg-white rounded-lg shadow-sm p-4 mb-4 flex items-center justify-between border-b border-gray-200">
                    <div class="flex items-center gap-4">
                        <button class="tab-btn active tab-all-jobs px-5 py-2.5 font-semibold text-white rounded-t-lg" data-tab="all">All jobs</button>
                    </div>
                    <div class="flex items-center gap-4">
                        <select id="sortBy" class="border border-gray-300 rounded-lg px-3 py-1.5 text-sm text-gray-700 bg-white focus:outline-none focus:ring-2">
                            <option value="latest">Sort by: Latest jobs</option>
                            <option value="oldest">Sort by: Oldest jobs</option>
                            <option value="salary_high">Sort by: Highest salary</option>
                            <option value="salary_low">Sort by: Lowest salary</option>
                        </select>
                        <div class="flex border border-gray-300 rounded-lg overflow-hidden">
                            <button id="gridView" class="p-2 hover:bg-gray-100 transition border-r border-gray-300">
                                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                                </svg>
                            </button>
                            <button id="listView" class="p-2 bg-gray-100 hover:bg-gray-200 transition">
                                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Job Count and Pagination Info -->
                <div class="flex items-center justify-between mb-4 text-sm text-gray-600">
                    <span id="job-count">Showing 16 Jobs</span>
                    <span id="page-info">Page 1 of 4</span>
                </div>

                <!-- Jobs List Container -->
                <div id="jobs-container" class="space-y-4" data-view="list">
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

            <!-- Right Section - Job Detail View (1/3 width) -->
            <div class="lg:col-span-1">
                <div id="job-detail-container" class="bg-white rounded-lg shadow-sm sticky top-4 max-h-[calc(100vh-6rem)] overflow-y-auto">
                    <!-- Skeleton for job detail -->
                    <div id="job-detail-skeleton" class="animate-pulse">
                        <div class="h-48 bg-gray-200"></div>
                        <div class="p-6 space-y-4">
                            <div class="h-6 bg-gray-200 rounded w-3/4"></div>
                    <div class="h-4 bg-gray-200 rounded w-1/2"></div>
                            <div class="h-4 bg-gray-200 rounded w-full"></div>
                            <div class="h-10 bg-gray-200 rounded"></div>
                        </div>
                    </div>

                    <!-- Actual job detail (hidden initially) -->
                    <div id="job-detail-content" class="hidden">
                        <!-- Banner Image -->
                        <div class="relative h-32 overflow-hidden">
                            <img id="job-banner" src="/images/app-promo-banner.png" alt="Job Banner" class="w-full h-full object-cover">
                        </div>

                        <div class="p-6">
                            <!-- Header with Company Logo and Job Title -->
                            <div class="flex items-start gap-4 mb-4">
                                <div class="w-12 h-12 bg-gray-100 rounded-lg p-2 flex items-center justify-center flex-shrink-0">
                                    <img id="company-logo" src="" alt="Company Logo" class="w-full h-full object-contain">
                                </div>
                                <div class="flex-1">
                                    <h2 id="job-title" class="text-lg font-bold text-gray-900 mb-2"></h2>
                                    <div class="flex items-center gap-2 mb-1">
                                        <span id="company-name" class="text-sm text-gray-700 font-medium"></span>
                                        <svg class="w-4 h-4 flex-shrink-0" style="color: #3b82f6;" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                    <button id="follow-btn" class="text-xs font-semibold px-3 py-1 border border-gray-300 rounded-md hover:bg-gray-50 transition">Follow</button>
                                </div>
                            </div>

                        <!-- Rating -->
                        <div class="flex items-center gap-2 mb-4 pb-4 border-b border-gray-200">
                            <svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                            <span id="company-rating" class="text-sm font-bold text-gray-900">4.6</span>
                            <span id="company-reviews" class="text-xs text-gray-600">98 reviews</span>
                            <span class="text-xs text-gray-400">•</span>
                            <a href="#" class="text-xs text-blue-600 hover:underline">View all jobs</a>
                        </div>

                        <!-- Key Details -->
                        <div class="space-y-3 mb-4">
                            <div class="flex items-center gap-2 text-sm text-gray-700">
                                <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                </svg>
                                <span id="job-location"></span>
                            </div>
                            <div class="flex items-center gap-2 text-sm font-semibold" style="color: #ec4899;">
                                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span id="job-salary"></span>
                            </div>
                            <div class="flex items-center gap-2 text-xs text-gray-600">
                                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                </svg>
                                <span id="job-category"></span>
                            </div>
                            <div class="flex items-center gap-2 text-xs text-gray-600">
                                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                                </svg>
                                <span id="job-work-type"></span>
                            </div>
                            <div class="flex items-center gap-2 text-xs text-gray-600">
                                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span id="job-employment-type"></span>
                            </div>
                            <div class="flex items-center gap-2 text-xs text-gray-600">
                                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                <span id="job-experience"></span>
                            </div>
                            <div class="flex items-center gap-2 text-xs text-gray-500">
                                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                <span id="job-dates"></span>
                            </div>
                        </div>

                        <!-- Applicant Count -->
                        <div class="mb-4 pb-4 border-b border-gray-200">
                            <p id="applicant-count" class="text-xs text-pink-600 font-medium"></p>
                        </div>

                        <!-- Apply Button -->
                        <button id="apply-job-btn" class="w-full pink-button text-white font-semibold py-2.5 px-4 rounded-md transition shadow-sm mb-6">
                            Apply Job
                        </button>

                        <!-- Job Description -->
                        <div class="mb-6">
                            <h3 class="text-base font-bold text-gray-900 mb-3">Job Description</h3>
                            <div id="job-description" class="text-xs text-gray-700 leading-relaxed"></div>
                        </div>

                        <!-- Skills Section -->
                        <div id="skills-section" class="mb-6 hidden">
                            <h3 class="text-base font-bold text-gray-900 mb-3">Skills</h3>
                            <div id="job-skills" class="flex flex-wrap gap-2"></div>
                        </div>

                        <!-- About Company Section -->
                        <div id="about-company-section" class="mb-6">
                            <h3 class="text-base font-bold text-gray-900 mb-3">About Company</h3>
                            <div class="bg-gray-50 rounded-lg p-4 space-y-3">
                                <div class="grid grid-cols-2 gap-3 text-xs">
                                    <div>
                                        <p class="text-gray-500 mb-1">Company Name</p>
                                        <p id="about-company-name" class="font-semibold text-gray-900"></p>
                                    </div>
                                    <div>
                                        <p class="text-gray-500 mb-1">Company Type</p>
                                        <p id="about-company-type" class="font-semibold text-gray-900">Small-Medium Enterprise</p>
                                    </div>
                                    <div>
                                        <p class="text-gray-500 mb-1">Company Size</p>
                                        <p id="about-company-size" class="font-semibold text-gray-900">21-100</p>
                                    </div>
                                    <div>
                                        <p class="text-gray-500 mb-1">Job Vacancies</p>
                                        <p id="about-company-jobs" class="font-semibold text-gray-900">3</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Employer Questions -->
                        <div id="employer-questions-section" class="mb-6 hidden">
                            <h3 class="text-base font-bold text-gray-900 mb-3">Employer questions</h3>
                            <p class="text-xs text-gray-600 mb-2">Your application will include the following questions:</p>
                            <ul id="employer-questions-list" class="list-disc list-inside text-xs text-gray-700 space-y-1"></ul>
                        </div>
                        </div>
                    </div>
                </div>
                </div>
            </div>
        </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
    const API_BASE = '/api';
    let selectedJobId = null;
    let currentRequest = null;
    let currentView = 'list'; // 'list' or 'grid'

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
        const sortBy = params.get('sort') || document.getElementById('sortBy')?.value || 'latest';
        const page = params.get('page') || 1;
        
        // Build jobs URL with all parameters
        let jobsUrl = `${API_BASE}/jobs/search?per_page=16&page=${page}`;
        if (keyword) jobsUrl += `&keyword=${encodeURIComponent(keyword)}`;
        if (categoryId) jobsUrl += `&category_id=${categoryId}`;
        if (location) jobsUrl += `&location=${encodeURIComponent(location)}`;
        if (employmentType) jobsUrl += `&employment_type=${encodeURIComponent(employmentType)}`;
        if (salaryMin) jobsUrl += `&salary_min=${encodeURIComponent(salaryMin)}`;
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
        if (currentView === 'grid') {
            container.className = 'grid grid-cols-1 md:grid-cols-2 gap-4';
        } else {
            container.className = 'space-y-4';
        }
        
        if (data.data && data.data.length > 0) {
            if (currentView === 'grid') {
                // Grid view rendering
            container.innerHTML = data.data.map((job, index) => `
                    <div class="job-card-grid bg-white rounded-lg shadow-sm hover:shadow-md transition-all p-6 border border-gray-200 cursor-pointer" data-job-id="${job.id}">
                        <div class="flex items-start gap-4 mb-4">
                            <div class="w-12 h-12 rounded-full flex items-center justify-center flex-shrink-0" style="background-color: #e0e7ff;">
                                <svg class="w-6 h-6" style="color: #3b82f6;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h3 class="text-base font-bold text-gray-900 mb-1 truncate">${job.title}</h3>
                                <div class="flex items-center gap-1 mb-2">
                                    <span class="text-sm text-gray-700 font-medium truncate">${job.company?.name || 'Company'}</span>
                                    <svg class="w-4 h-4 flex-shrink-0" style="color: #3b82f6;" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>
                        
                        <p class="text-sm text-gray-600 line-clamp-2 mb-3">${job.description ? job.description.substring(0, 100) + '...' : 'Oversee and manage operations while driving digital solutions that support efficiency and business needs.'}</p>
                        
                        <div class="space-y-2 text-sm text-gray-600 mb-3">
                            ${job.location ? `<div class="flex items-center gap-2">
                                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                </svg>
                                <span class="truncate">${job.location}</span>
                            </div>` : ''}
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span>${job.employment_type || 'Full Time'}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                                </svg>
                                <span>${getWorkType(job)}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                </svg>
                                <span class="truncate">${job.category?.name || 'Uncategorized'}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                <span class="truncate">${getExperienceLevel(job)}</span>
                            </div>
                            ${job.application_deadline ? `<div class="flex items-center gap-2">
                                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                <span class="truncate text-xs">Expiring on ${formatDate(job.application_deadline)}</span>
                            </div>` : ''}
                        </div>
                        
                        <div class="flex items-center justify-between pt-3 border-t border-gray-100">
                            <span class="text-pink-600 font-bold text-sm">${formatSalary(job)}</span>
                            <span class="text-xs text-gray-500">/ month</span>
                        </div>
                    </div>
                `).join('');
            } else {
                // List view rendering (existing)
                container.innerHTML = data.data.map((job, index) => `
                    <div class="job-card bg-white rounded-lg shadow-sm hover:shadow-md transition-all p-6 border-2 ${selectedJobId == job.id ? 'job-card-selected' : 'border-gray-200'} cursor-pointer" data-job-id="${job.id}">
                        <div class="flex gap-4">
                            <!-- Document Icon -->
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 rounded-full flex items-center justify-center" style="background-color: #e0e7ff;">
                                    <svg class="w-6 h-6" style="color: #3b82f6;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                </div>
                            </div>
                            
                            <!-- Job Details -->
                            <div class="flex-1 space-y-2">
                                <div>
                                    <h3 class="job-title text-lg font-bold mb-1 ${selectedJobId == job.id ? '' : 'text-gray-900'}">${job.title}</h3>
                                    <div class="flex items-center gap-1">
                                        <span class="text-sm text-gray-700 font-medium">${job.company?.name || 'Company'}</span>
                                        <svg class="w-4 h-4" style="color: #3b82f6;" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                </div>
                                
                                <div class="flex flex-wrap gap-4 text-sm text-gray-600">
                                    ${job.location ? `<div class="flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    </svg>
                                    ${job.location}
                                    </div>` : ''}
                                    ${job.salary_min || job.salary_max ? `<div class="flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        ${formatSalary(job)}
                                    </div>` : ''}
                                </div>
                                
                                <div class="flex items-center gap-1 text-sm text-gray-600">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                    </svg>
                                    <span>${job.category?.name || 'Uncategorized'}</span>
                                </div>
                                
                                <div class="flex flex-wrap gap-3 text-sm text-gray-600">
                                    <div class="flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                                        </svg>
                                        <span>${getWorkType(job)}</span>
                                    </div>
                                    <div class="flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <span>${job.employment_type || 'Full Time'}</span>
                                    </div>
                                    <div class="flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                        <span>${getExperienceLevel(job)}</span>
                                    </div>
                                    ${job.application_deadline ? `<div class="flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                        <span>Expiring on ${formatDate(job.application_deadline)}</span>
                                    </div>` : ''}
                                </div>
                                
                                <!-- Tags (mock for now - can be extracted from description or added as field) -->
                                <div class="flex flex-wrap gap-2 mt-2">
                                    ${job.category?.name ? `<span class="tag-pill px-3 py-1 rounded-full text-xs font-medium">${job.category.name.split(' ')[0]}</span>` : ''}
                                    ${job.is_remote ? '<span class="tag-pill px-3 py-1 rounded-full text-xs font-medium">Remote</span>' : ''}
                                    ${job.employment_type ? `<span class="tag-pill px-3 py-1 rounded-full text-xs font-medium">${job.employment_type}</span>` : ''}
                                </div>
                            
                                <div class="flex items-center justify-between pt-3 border-t border-gray-100 mt-3">
                                    <span class="text-xs text-gray-500">Posted on ${formatDate(job.published_at || job.created_at)}</span>
                                    <button class="apply-now-btn pink-button text-white px-5 py-2 rounded-lg text-sm font-semibold transition shadow-sm" data-job-id="${job.id}">
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
                    navigateTo(`/jobs/${jobId}/apply`);
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
            document.getElementById('applicant-count').textContent = 
                applicantCount < 20 
                    ? `Under ${applicantCount} applicants so far. Your opportunity is still here!`
                    : `${applicantCount}+ applicants`;
            
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
            
            // Skills section
            const skillsData = ['IT Governance', 'Cybersecurity', 'Project Management', 'Leadership', 'Cloud Computing'];
            const skillsContainer = document.getElementById('job-skills');
            const skillsSection = document.getElementById('skills-section');
            if (skillsData && skillsData.length > 0) {
                skillsContainer.innerHTML = skillsData.map(skill => 
                    `<span class="tag-pill px-3 py-1.5 rounded-full text-xs font-medium">${skill}</span>`
                ).join('');
                skillsSection.classList.remove('hidden');
            }
            
            // About Company
            document.getElementById('about-company-name').textContent = job.company?.name || 'Company';
            
            // Apply button handler
            document.getElementById('apply-job-btn').onclick = () => {
                navigateTo(`/jobs/${jobId}/apply`);
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
        const employmentType = document.getElementById('employment_type').value;
        const remoteOption = document.getElementById('remote_option').value;
        const sortBy = document.getElementById('sortBy').value;
        
        if (keyword) params.append('keyword', keyword);
        if (categoryId) params.append('category_id', categoryId);
        if (location) params.append('location', location);
        if (salaryMin) params.append('salary_min', salaryMin);
        if (employmentType) params.append('employment_type', employmentType);
        if (remoteOption) params.append('remote_option', remoteOption);
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
        // Use event delegation for buttons to avoid duplicate listeners
        const findJobsBtn = document.getElementById('findJobsBtn');
        if (findJobsBtn && !findJobsBtn.dataset.listenerAttached) {
            findJobsBtn.dataset.listenerAttached = 'true';
            findJobsBtn.addEventListener('click', searchJobs);
        }
        
        // Grid/List view toggle
        const gridViewBtn = document.getElementById('gridView');
        const listViewBtn = document.getElementById('listView');
        
        if (gridViewBtn && !gridViewBtn.dataset.listenerAttached) {
            gridViewBtn.dataset.listenerAttached = 'true';
            gridViewBtn.addEventListener('click', function() {
                currentView = 'grid';
                gridViewBtn.classList.remove('hover:bg-gray-100');
                gridViewBtn.classList.add('bg-gray-100');
                if (listViewBtn) {
                    listViewBtn.classList.remove('bg-gray-100', 'hover:bg-gray-200');
                    listViewBtn.classList.add('hover:bg-gray-100');
                }
                loadPageData();
            });
        }
        
        if (listViewBtn && !listViewBtn.dataset.listenerAttached) {
            listViewBtn.dataset.listenerAttached = 'true';
            listViewBtn.addEventListener('click', function() {
                currentView = 'list';
                listViewBtn.classList.remove('hover:bg-gray-100');
                listViewBtn.classList.add('bg-gray-100', 'hover:bg-gray-200');
                if (gridViewBtn) {
                    gridViewBtn.classList.remove('bg-gray-100');
                    gridViewBtn.classList.add('hover:bg-gray-100');
                }
                loadPageData();
            });
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
        ['category_id', 'location', 'salary_min', 'employment_type', 'remote_option', 'sortBy'].forEach(id => {
            const element = document.getElementById(id);
            if (element && !element.dataset.listenerAttached) {
                element.dataset.listenerAttached = 'true';
                element.addEventListener('change', searchJobs);
            }
        });
        
        // Load URL parameters into form fields
        const urlParams = new URLSearchParams(window.location.search);
        ['keyword', 'category_id', 'location', 'salary_min', 'employment_type', 'remote_option', 'sortBy'].forEach(param => {
            const element = document.getElementById(param);
            if (element && urlParams.get(param)) {
                element.value = urlParams.get(param);
            }
        });
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
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\lysp\Downloads\Job Ad\resources\views/jobs/index.blade.php ENDPATH**/ ?>