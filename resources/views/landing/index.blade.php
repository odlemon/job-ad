@extends('layouts.app')

@section('content')
    <!-- Hero Section -->
    <section class="bg-gradient-to-br from-blue-50 to-indigo-100 py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h1 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4">
                    Discover Your Next <span class="text-blue-600">Career Opportunity</span>
                </h1>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                    Connect with thousands of companies hiring for roles across all industries and experience levels.
                </p>
            </div>

            <!-- Search Bar -->
            <div class="max-w-4xl mx-auto bg-white rounded-xl shadow-lg p-6">
                <div class="mb-4">
                    <label class="text-sm text-gray-600">Preferred work location</label>
                    <select class="w-full mt-1 border border-gray-300 rounded-lg px-4 py-2 text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option>Select location</option>
                        <option>Victoria, Mahe</option>
                        <option>Beau Vallon, Mahe</option>
                        <option>Anse Royale, Mahe</option>
                    </select>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
                    <!-- Keyword Search -->
                    <div class="md:col-span-2">
                        <div class="relative">
                            <input 
                                type="text" 
                                placeholder="Search keyword" 
                                class="w-full border border-gray-300 rounded-lg px-4 py-3 pl-10 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            >
                            <svg class="w-5 h-5 text-gray-400 absolute left-3 top-1/2 transform -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                    </div>

                    <!-- Category Dropdown -->
                    <div>
                        <select class="w-full border border-gray-300 rounded-lg px-4 py-3 text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option>All job categories</option>
                            <option>Sales / Retail / Marketing</option>
                            <option>Hospitality / F&B</option>
                            <option>Customer Service</option>
                            <option>Administrative</option>
                        </select>
                    </div>

                    <!-- Location Dropdown -->
                    <div>
                        <select class="w-full border border-gray-300 rounded-lg px-4 py-3 text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option>All Seychelles locations</option>
                            <option>Victoria, Mahe</option>
                            <option>Beau Vallon, Mahe</option>
                            <option>Anse Royale, Mahe</option>
                        </select>
                    </div>
                </div>

                <div class="flex items-center justify-between">
                    <div class="flex items-center text-sm text-gray-600 cursor-pointer hover:text-blue-600">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        Show more options
                    </div>
                    <button type="button" onclick="handleSearch(event)" class="bg-pink-500 text-white px-8 py-3 rounded-lg font-semibold hover:bg-pink-600 transition">
                        Find jobs
                    </button>
                </div>

                <!-- Popular Searches -->
                <div class="mt-4 pt-4 border-t border-gray-200">
                    <p class="text-sm text-gray-600 mb-2">Popular searches:</p>
                    <div class="flex flex-wrap gap-2">
                        <button class="px-4 py-1 bg-gray-100 text-gray-700 rounded-full text-sm hover:bg-gray-200 transition">Admin</button>
                        <button class="px-4 py-1 bg-gray-100 text-gray-700 rounded-full text-sm hover:bg-gray-200 transition">Operator</button>
                        <button class="px-4 py-1 bg-gray-100 text-gray-700 rounded-full text-sm hover:bg-gray-200 transition">Hotel Operations Manager</button>
                        <button class="px-4 py-1 bg-gray-100 text-gray-700 rounded-full text-sm hover:bg-gray-200 transition">Cleaner</button>
                        <button class="px-4 py-1 bg-gray-100 text-gray-700 rounded-full text-sm hover:bg-gray-200 transition">Technician</button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Popular Categories Section -->
    <section class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center mb-8">
                <h2 class="text-3xl font-bold text-gray-900">Popular categories</h2>
                <a href="{{ route('jobs.index') }}" wire:navigate class="text-red-600 hover:text-red-700 font-semibold">View all</a>
            </div>

            <div id="popular-categories" class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-6">
                <!-- Skeleton loaders -->
                <div class="bg-gray-50 rounded-lg p-6 animate-pulse">
                    <div class="w-12 h-12 bg-gray-200 rounded-lg mb-4"></div>
                    <div class="h-4 bg-gray-200 rounded w-3/4 mb-2"></div>
                    <div class="h-3 bg-gray-200 rounded w-1/2"></div>
                </div>
                <div class="bg-gray-50 rounded-lg p-6 animate-pulse">
                    <div class="w-12 h-12 bg-gray-200 rounded-lg mb-4"></div>
                    <div class="h-4 bg-gray-200 rounded w-3/4 mb-2"></div>
                    <div class="h-3 bg-gray-200 rounded w-1/2"></div>
                </div>
                <div class="bg-gray-50 rounded-lg p-6 animate-pulse hidden md:block">
                    <div class="w-12 h-12 bg-gray-200 rounded-lg mb-4"></div>
                    <div class="h-4 bg-gray-200 rounded w-3/4 mb-2"></div>
                    <div class="h-3 bg-gray-200 rounded w-1/2"></div>
                </div>
                <div class="bg-gray-50 rounded-lg p-6 animate-pulse hidden lg:block">
                    <div class="w-12 h-12 bg-gray-200 rounded-lg mb-4"></div>
                    <div class="h-4 bg-gray-200 rounded w-3/4 mb-2"></div>
                    <div class="h-3 bg-gray-200 rounded w-1/2"></div>
                </div>
                <div class="bg-gray-50 rounded-lg p-6 animate-pulse hidden lg:block">
                    <div class="w-12 h-12 bg-gray-200 rounded-lg mb-4"></div>
                    <div class="h-4 bg-gray-200 rounded w-3/4 mb-2"></div>
                    <div class="h-3 bg-gray-200 rounded w-1/2"></div>
                </div>
                <div class="bg-gray-50 rounded-lg p-6 animate-pulse hidden lg:block">
                    <div class="w-12 h-12 bg-gray-200 rounded-lg mb-4"></div>
                    <div class="h-4 bg-gray-200 rounded w-3/4 mb-2"></div>
                    <div class="h-3 bg-gray-200 rounded w-1/2"></div>
                </div>
            </div>
        </div>
    </section>

    <!-- Find Your Next Employer Section -->
    <section class="py-16 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-8">
                <h2 class="text-3xl font-bold text-gray-900 mb-3">Find your next employer</h2>
                <p class="text-gray-600 max-w-2xl">
                    Explore company profiles to find the right workplace for you. Learn about jobs, reviews, company culture, perks and benefits.
                </p>
            </div>

            <!-- Company Carousel -->
            <div class="relative">
                <div id="company-carousel" class="flex space-x-6 overflow-x-auto pb-4 scrollbar-hide">
                    <!-- Skeleton loaders -->
                    <div class="flex-shrink-0 w-64 bg-white rounded-lg p-6 shadow-sm animate-pulse">
                        <div class="w-16 h-16 bg-gray-200 rounded-lg mb-4"></div>
                        <div class="h-4 bg-gray-200 rounded w-3/4 mb-2"></div>
                        <div class="h-3 bg-gray-200 rounded w-1/2"></div>
                    </div>
                    <div class="flex-shrink-0 w-64 bg-white rounded-lg p-6 shadow-sm animate-pulse">
                        <div class="w-16 h-16 bg-gray-200 rounded-lg mb-4"></div>
                        <div class="h-4 bg-gray-200 rounded w-3/4 mb-2"></div>
                        <div class="h-3 bg-gray-200 rounded w-1/2"></div>
                    </div>
                    <div class="flex-shrink-0 w-64 bg-white rounded-lg p-6 shadow-sm animate-pulse">
                        <div class="w-16 h-16 bg-gray-200 rounded-lg mb-4"></div>
                        <div class="h-4 bg-gray-200 rounded w-3/4 mb-2"></div>
                        <div class="h-3 bg-gray-200 rounded w-1/2"></div>
                    </div>
                    <div class="flex-shrink-0 w-64 bg-white rounded-lg p-6 shadow-sm animate-pulse">
                        <div class="w-16 h-16 bg-gray-200 rounded-lg mb-4"></div>
                        <div class="h-4 bg-gray-200 rounded w-3/4 mb-2"></div>
                        <div class="h-3 bg-gray-200 rounded w-1/2"></div>
                    </div>
                </div>
                
                <div class="text-center mt-6">
                    <button class="border-2 border-gray-900 text-gray-900 px-6 py-2 rounded-lg font-semibold hover:bg-gray-900 hover:text-white transition">
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
                <a href="{{ route('jobs.index') }}" wire:navigate class="text-red-600 hover:text-red-700 font-semibold">View all</a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6" id="popular-jobs">
                <!-- Skeleton loaders -->
                <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-100 animate-pulse">
                    <div class="w-12 h-12 bg-gray-200 rounded-lg mb-4"></div>
                    <div class="h-5 bg-gray-200 rounded w-3/4 mb-3"></div>
                    <div class="h-4 bg-gray-200 rounded w-1/2 mb-2"></div>
                    <div class="h-3 bg-gray-200 rounded w-2/3 mb-2"></div>
                    <div class="h-3 bg-gray-200 rounded w-1/3 mb-4"></div>
                    <div class="h-10 bg-gray-200 rounded w-full"></div>
                </div>
                <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-100 animate-pulse">
                    <div class="w-12 h-12 bg-gray-200 rounded-lg mb-4"></div>
                    <div class="h-5 bg-gray-200 rounded w-3/4 mb-3"></div>
                    <div class="h-4 bg-gray-200 rounded w-1/2 mb-2"></div>
                    <div class="h-3 bg-gray-200 rounded w-2/3 mb-2"></div>
                    <div class="h-3 bg-gray-200 rounded w-1/3 mb-4"></div>
                    <div class="h-10 bg-gray-200 rounded w-full"></div>
                </div>
                <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-100 animate-pulse hidden md:block">
                    <div class="w-12 h-12 bg-gray-200 rounded-lg mb-4"></div>
                    <div class="h-5 bg-gray-200 rounded w-3/4 mb-3"></div>
                    <div class="h-4 bg-gray-200 rounded w-1/2 mb-2"></div>
                    <div class="h-3 bg-gray-200 rounded w-2/3 mb-2"></div>
                    <div class="h-3 bg-gray-200 rounded w-1/3 mb-4"></div>
                    <div class="h-10 bg-gray-200 rounded w-full"></div>
                </div>
                <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-100 animate-pulse hidden lg:block">
                    <div class="w-12 h-12 bg-gray-200 rounded-lg mb-4"></div>
                    <div class="h-5 bg-gray-200 rounded w-3/4 mb-3"></div>
                    <div class="h-4 bg-gray-200 rounded w-1/2 mb-2"></div>
                    <div class="h-3 bg-gray-200 rounded w-2/3 mb-2"></div>
                    <div class="h-3 bg-gray-200 rounded w-1/3 mb-4"></div>
                    <div class="h-10 bg-gray-200 rounded w-full"></div>
                </div>
            </div>
        </div>
    </section>

    <!-- App Download Banner -->
    <section class="py-16 bg-gradient-to-r from-blue-600 to-blue-800 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <!-- Left Side - Content -->
                <div>
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
                    <div class="flex items-center space-x-4">
                        <div class="bg-white p-4 rounded-lg">
                            <div class="w-32 h-32 bg-gray-200 flex items-center justify-center">
                                <span class="text-gray-400 text-xs">QR Code</span>
                            </div>
                        </div>
                        <div>
                            <p class="text-sm font-semibold mb-2">Scan to download</p>
                            <div class="space-y-2">
                                <div class="flex items-center text-sm">
                                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                    Free to use
                                </div>
                                <div class="flex items-center text-sm">
                                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                    Instant alerts
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Side - Image Placeholder -->
                <div class="hidden lg:block">
                    <div class="bg-white/10 rounded-lg p-8 backdrop-blur-sm">
                        <div class="aspect-square bg-white/20 rounded-lg flex items-center justify-center">
                            <svg class="w-32 h-32 text-white/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @push('scripts')
    <script>
        const API_BASE = '/api';
        
        // Parallel fetch wrapper for faster loading
        async function fetchFast(url) {
            const response = await fetch(url, {
                headers: { 'Accept': 'application/json' }
            });
            return response.json();
        }

        // Render popular categories
        function renderPopularCategories(data) {
            const container = document.getElementById('popular-categories');
            if (container && data?.data?.length > 0) {
                container.innerHTML = data.data.slice(0, 6).map((cat, index) => `
                    <div class="bg-gray-50 rounded-lg p-6 hover:shadow-lg transition-all cursor-pointer fade-in" style="animation-delay: ${index * 50}ms" onclick="searchByCategory(${cat.id})">
                        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mb-4">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                        </div>
                        <h3 class="font-semibold text-gray-900 mb-1">${cat.name}</h3>
                        <p class="text-sm text-gray-600">${cat.job_advertisements_count || 0} jobs</p>
                    </div>
                `).join('');
            }
        }

        // Render featured companies
        function renderFeaturedCompanies(data) {
            const carousel = document.getElementById('company-carousel');
            if (carousel && data?.data?.length > 0) {
                carousel.innerHTML = data.data.slice(0, 5).map(company => `
                    <div class="flex-shrink-0 w-64 bg-white rounded-lg p-6 shadow-sm hover:shadow-md transition cursor-pointer fade-in" onclick="Livewire.navigate('/companies/${company.slug || company.id}')">
                        <div class="w-16 h-16 bg-blue-100 rounded-lg flex items-center justify-center mb-4">
                            ${company.logo ? 
                                `<img src="${company.logo}" alt="${company.name}" class="w-full h-full object-cover rounded-lg">` :
                                `<span class="text-2xl font-bold text-blue-600">${company.name.charAt(0).toUpperCase()}</span>`
                            }
                        </div>
                        <h3 class="font-semibold text-gray-900 mb-2">${company.name}</h3>
                        <p class="text-sm text-gray-600">${company.job_advertisements_count || 0} Jobs</p>
                    </div>
                `).join('');
            }
        }

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

        // Render popular jobs
        function renderPopularJobs(data) {
            const container = document.getElementById('popular-jobs');
            if (!container) return;
            
            if (data?.data?.length > 0) {
                container.innerHTML = data.data.slice(0, 8).map((job, index) => `
                    <div class="bg-white rounded-lg shadow-sm hover:shadow-md transition-all p-6 border border-gray-100 fade-in" style="animation-delay: ${index * 50}ms">
                        <div class="flex items-start justify-between mb-4">
                            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <span class="text-blue-600 font-bold">${job.company?.name?.charAt(0) || 'C'}</span>
                            </div>
                        </div>
                        <h3 class="font-semibold text-gray-900 mb-2 line-clamp-2">${job.title}</h3>
                        <div class="flex items-center text-sm text-gray-600 mb-2">
                            <span class="font-medium">${job.company?.name || 'Company'}</span>
                        </div>
                        <div class="space-y-1 mb-4 text-sm text-gray-600">
                            ${job.location ? `<div class="flex items-center"><svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path></svg>${job.location}</div>` : ''}
                            ${job.salary_min || job.salary_max ? `<div class="flex items-center"><svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>${job.salary_min && job.salary_max ? `${job.salary_min} - ${job.salary_max}` : job.salary_min || job.salary_max} ${job.currency || 'SCR'}</div>` : ''}
                        </div>
                        ${job.employment_type ? `<span class="px-2 py-1 bg-blue-50 text-blue-700 text-xs rounded mb-3 inline-block">${job.employment_type}</span>` : ''}
                        <button onclick="handleApply(${job.id})" class="w-full bg-pink-500 text-white py-2 rounded-lg font-semibold hover:bg-pink-600 transition mt-2">
                            Apply now
                        </button>
                    </div>
                `).join('');
            } else {
                container.innerHTML = '<div class="col-span-4 text-center py-12 text-gray-500"><p class="text-lg mb-2">No jobs available</p><p class="text-sm">Check back later for new opportunities</p></div>';
            }
        }

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
            const location = document.querySelector('select:has(option[value="All Seychelles locations"])')?.value || '';
            
            const params = new URLSearchParams();
            if (keyword) params.append('keyword', keyword);
            if (categoryId) params.append('category_id', categoryId);
            if (location && location !== 'All Seychelles locations') params.append('location', location);
            
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
