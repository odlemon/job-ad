@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Job Listings</h1>
        <p class="text-gray-600">Find your perfect job opportunity</p>
    </div>

    <!-- Search Filters -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-8">
        <form id="searchForm" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="md:col-span-2">
                <input 
                    type="text" 
                    name="keyword"
                    id="keyword"
                    placeholder="Search keyword" 
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    value="{{ request('keyword') }}"
                >
            </div>
            <div>
                <select name="category_id" id="category_id" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">All categories</option>
                </select>
            </div>
            <div>
                <select name="location" id="location" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">All locations</option>
                    <option value="Victoria, Mahe" {{ request('location') == 'Victoria, Mahe' ? 'selected' : '' }}>Victoria, Mahe</option>
                    <option value="Beau Vallon, Mahe" {{ request('location') == 'Beau Vallon, Mahe' ? 'selected' : '' }}>Beau Vallon, Mahe</option>
                    <option value="Anse Royale, Mahe" {{ request('location') == 'Anse Royale, Mahe' ? 'selected' : '' }}>Anse Royale, Mahe</option>
                </select>
            </div>
            <div class="md:col-span-4">
                <button type="submit" id="searchBtn" class="bg-pink-500 text-white px-6 py-2 rounded-lg font-semibold hover:bg-pink-600 transition inline-flex items-center">
                    <span id="searchBtnText">Search Jobs</span>
                </button>
            </div>
        </form>
    </div>

    <!-- Jobs List -->
    <div id="jobs-container" class="space-y-4">
        <!-- Skeleton loaders -->
        <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-100 animate-pulse">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <div class="h-6 bg-gray-200 rounded w-2/3 mb-3"></div>
                    <div class="h-4 bg-gray-200 rounded w-1/3 mb-3"></div>
                    <div class="flex gap-4 mb-3">
                        <div class="h-4 bg-gray-200 rounded w-24"></div>
                        <div class="h-4 bg-gray-200 rounded w-32"></div>
                    </div>
                    <div class="h-4 bg-gray-200 rounded w-full mb-2"></div>
                    <div class="h-4 bg-gray-200 rounded w-3/4"></div>
                </div>
                <div class="ml-4">
                    <div class="h-10 bg-gray-200 rounded w-24"></div>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-100 animate-pulse">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <div class="h-6 bg-gray-200 rounded w-1/2 mb-3"></div>
                    <div class="h-4 bg-gray-200 rounded w-1/4 mb-3"></div>
                    <div class="flex gap-4 mb-3">
                        <div class="h-4 bg-gray-200 rounded w-24"></div>
                        <div class="h-4 bg-gray-200 rounded w-32"></div>
                    </div>
                    <div class="h-4 bg-gray-200 rounded w-full mb-2"></div>
                    <div class="h-4 bg-gray-200 rounded w-2/3"></div>
                </div>
                <div class="ml-4">
                    <div class="h-10 bg-gray-200 rounded w-24"></div>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-100 animate-pulse">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <div class="h-6 bg-gray-200 rounded w-3/5 mb-3"></div>
                    <div class="h-4 bg-gray-200 rounded w-1/3 mb-3"></div>
                    <div class="flex gap-4 mb-3">
                        <div class="h-4 bg-gray-200 rounded w-24"></div>
                        <div class="h-4 bg-gray-200 rounded w-32"></div>
                    </div>
                    <div class="h-4 bg-gray-200 rounded w-full mb-2"></div>
                    <div class="h-4 bg-gray-200 rounded w-1/2"></div>
                </div>
                <div class="ml-4">
                    <div class="h-10 bg-gray-200 rounded w-24"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Pagination -->
    <div id="pagination" class="mt-8"></div>
</div>

@push('scripts')
<script>
    const API_BASE = '/api';
    
    // Fast fetch without loading overlay
    async function fetchFast(url) {
        const response = await fetch(url, { headers: { 'Accept': 'application/json' } });
        return response.json();
    }

    // Navigate helper
    function navigateTo(url) {
        if (typeof Livewire !== 'undefined' && Livewire.navigate) {
            Livewire.navigate(url);
        } else {
            window.location.href = url;
        }
    }
    
    // Load all data in parallel with request deduplication
    let currentRequest = null;
    async function loadPageData() {
        // Return existing request if one is in progress
        if (currentRequest) {
            return currentRequest;
        }
        
        currentRequest = (async () => {
            const params = new URLSearchParams(window.location.search);
            const keyword = params.get('keyword') || '';
            const categoryId = params.get('category_id') || '';
            const location = params.get('location') || '';
            
            let jobsUrl = `${API_BASE}/jobs/search?per_page=15`;
            if (keyword) jobsUrl += `&keyword=${encodeURIComponent(keyword)}`;
            if (categoryId) jobsUrl += `&category_id=${categoryId}`;
            if (location) jobsUrl += `&location=${encodeURIComponent(location)}`;

            try {
                const [categories, jobs] = await Promise.all([
                    fetchFast(`${API_BASE}/categories`),
                    fetchFast(jobsUrl)
                ]);
                
                renderCategories(categories, categoryId);
                renderJobs(jobs);
            } catch (error) {
                console.error('Error loading data:', error);
                const container = document.getElementById('jobs-container');
                if (container) {
                    container.innerHTML = '<div class="text-center py-12 text-red-500"><p class="text-lg mb-2">Error loading jobs</p><p class="text-sm">Please try again later</p></div>';
                }
            } finally {
                // Clear request after a short delay to allow rapid navigation
                setTimeout(() => {
                    currentRequest = null;
                }, 100);
            }
        })();
        
        return currentRequest;
    }

    // Render categories dropdown
    function renderCategories(data, selectedId) {
        const select = document.getElementById('category_id');
        if (select && data.data) {
            select.innerHTML = '<option value="">All categories</option>' +
                data.data.map(cat => `<option value="${cat.id}" ${selectedId == cat.id ? 'selected' : ''}>${cat.name}</option>`).join('');
        }
    }

    // Render jobs list
    function renderJobs(data) {
        const container = document.getElementById('jobs-container');
        
        if (data.data && data.data.length > 0) {
            container.innerHTML = data.data.map((job, index) => `
                <div class="bg-white rounded-lg shadow-sm hover:shadow-md transition-all p-6 border border-gray-100 fade-in" style="animation-delay: ${index * 30}ms">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <h3 class="text-xl font-semibold text-gray-900 mb-2">
                                <a href="/jobs/${job.id}" wire:navigate class="hover:text-blue-600">${job.title}</a>
                            </h3>
                            <div class="flex items-center text-sm text-gray-600 mb-2">
                                <span class="font-medium">${job.company?.name || 'Company'}</span>
                            </div>
                            <div class="flex flex-wrap gap-4 text-sm text-gray-600 mb-4">
                                ${job.location ? `<span class="flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    </svg>
                                    ${job.location}
                                </span>` : ''}
                                ${job.salary_min || job.salary_max ? `<span class="flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    ${job.salary_min && job.salary_max ? `${job.salary_min} - ${job.salary_max}` : job.salary_min || job.salary_max} ${job.currency || 'SCR'}
                                </span>` : ''}
                                ${job.employment_type ? `<span class="px-2 py-1 bg-blue-50 text-blue-700 text-xs rounded">${job.employment_type}</span>` : ''}
                            </div>
                            <p class="text-gray-600 text-sm line-clamp-2">${job.description?.substring(0, 200) || ''}...</p>
                        </div>
                        <div class="ml-4">
                            <a href="/jobs/${job.id}" wire:navigate class="bg-pink-500 text-white px-6 py-2 rounded-lg font-semibold hover:bg-pink-600 transition whitespace-nowrap inline-block">
                                View Job
                            </a>
                        </div>
                    </div>
                </div>
            `).join('');
            
            // Render pagination if available
            if (data.last_page > 1) {
                renderPagination(data);
            }
        } else {
            container.innerHTML = '<div class="text-center py-12 text-gray-500"><p class="text-lg mb-2">No jobs found</p><p class="text-sm">Try adjusting your search criteria</p></div>';
        }
    }

    // Render pagination
    function renderPagination(data) {
        const container = document.getElementById('pagination');
        if (!container) return;
        
        const pages = [];
        for (let i = 1; i <= data.last_page; i++) {
            const isActive = i === data.current_page;
            pages.push(`<button onclick="goToPage(${i})" class="px-4 py-2 ${isActive ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50'} border rounded-lg transition">${i}</button>`);
        }
        
        container.innerHTML = `<div class="flex justify-center gap-2">${pages.join('')}</div>`;
    }

    // Go to page
    function goToPage(page) {
        const params = new URLSearchParams(window.location.search);
        params.set('page', page);
        navigateTo(`/jobs?${params.toString()}`);
    }

    // Initialize
    document.addEventListener('DOMContentLoaded', function() {
        loadPageData();
        
        document.getElementById('searchForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            const submitBtn = document.getElementById('searchBtn');
            const btnText = document.getElementById('searchBtnText');
            submitBtn.classList.add('btn-loading');
            submitBtn.disabled = true;
            
            const formData = new FormData(this);
            const params = new URLSearchParams();
            for (const [key, value] of formData.entries()) {
                if (value) params.append(key, value);
            }
            
            await new Promise(resolve => setTimeout(resolve, 200));
            navigateTo(`/jobs?${params.toString()}`);
        });
    });

    // Reload on Livewire navigation with deduplication
    let isLoading = false;
    document.addEventListener('livewire:navigated', function() {
        if (!isLoading) {
            isLoading = true;
            loadPageData().finally(() => {
                isLoading = false;
            });
        }
    });
</script>
@endpush
@endsection
