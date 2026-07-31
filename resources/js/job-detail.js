// Job Detail Page Functionality
// This file contains all functions needed for the job detail page
// It's loaded globally so it works with wire:navigate

const API_BASE = '/api';

// Get job ID from URL (works with wire:navigate)
function getJobIdFromUrl() {
    const path = window.location.pathname;
    const match = path.match(/\/jobs\/(\d+)/);
    return match ? parseInt(match[1]) : null;
}

// Make loadJobDetail globally accessible - this is the main function
// Add request deduplication to prevent multiple simultaneous calls
let loadingJobId = null;
let loadingPromise = null;

window.loadJobDetail = async function() {
    const jobId = getJobIdFromUrl();
    if (!jobId) {
        const element = document.getElementById('job-detail');
        if (element) {
            element.innerHTML = '<div class="text-center py-12 text-red-500">Invalid job ID</div>';
        }
        return;
    }
    
    // Prevent duplicate requests for the same job
    if (loadingJobId === jobId && loadingPromise) {
        return loadingPromise;
    }
    
    loadingJobId = jobId;
    loadingPromise = (async () => {
        try {
            const response = await fetch(`${API_BASE}/jobs/${jobId}`);
            const data = await response.json();
        
        if (data.data) {
            const job = data.data;
            const similarJobs = data.similar_jobs || [];
            const otherCompanyJobs = data.other_company_jobs || [];
            
            // Check if job is saved and company is followed (if logged in)
            // Prefer flags already on the job payload — avoid extra round-trips
            applySavedStateFromJob(job);
            applyApplicationStateFromJob(job);
            const companyId = job.company?.id || job.employer_id || job.employer?.id;
            if (job.is_following || job.employer?.is_following) {
                applyFollowedState(true);
            } else if (companyId) {
                checkCompanyFollowed(companyId);
            }
            
            // Format dates
            const postedDate = job.published_at ? new Date(job.published_at).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }) : '';
            const deadlineDate = '';
            const isExpiringSoon = false;
            
            // Format salary
            const salaryRange = job.hide_salary
                ? 'Negotiable'
                : job.salary_min && job.salary_max
                    ? `${job.currency || 'SCR'} ${parseInt(job.salary_min).toLocaleString()} - ${parseInt(job.salary_max).toLocaleString()} per month`
                    : job.salary_min
                        ? `${job.currency || 'SCR'} ${parseInt(job.salary_min).toLocaleString()} per month`
                        : job.salary_max
                            ? `${job.currency || 'SCR'} ${parseInt(job.salary_max).toLocaleString()} per month`
                            : 'Not specified';
            
            // Work environment
            const workEnv = job.is_remote ? 'Remote' : 'Office';
            
            const jobDetailElement = document.getElementById('job-detail');
            if (jobDetailElement) {
                jobDetailElement.removeAttribute('data-auto-load');
            }
            document.getElementById('job-detail').innerHTML = `
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Left Column - Main Content -->
                    <div class="lg:col-span-2 space-y-6">
                        <!-- Back Navigation -->
                        <a href="/jobs" wire:navigate class="inline-flex items-center text-blue-600 hover:text-blue-700 font-medium mb-4">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                            </svg>
                            Go back to search results
                        </a>
                        
                        <!-- Job Header -->
                        <div class="bg-white rounded-xl shadow-sm p-6">
                            <h1 class="text-3xl font-bold text-gray-900 mb-3">${job.title}</h1>
                            <div class="flex items-center space-x-2 mb-4">
                                <span class="text-lg font-semibold text-gray-700">${job.company?.name || 'Company'}</span>
                                ${job.company?.is_verified ? `
                                <svg class="w-5 h-5 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                                ` : ''}
                            </div>
                            
                            <!-- Job Metadata -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                ${job.location ? `
                                <div class="flex items-center text-gray-600">
                                    <svg class="w-5 h-5 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    ${job.location}
                                </div>
                                ` : ''}
                                ${job.hide_salary || job.salary_min || job.salary_max ? `
                                <div class="flex items-center text-gray-600">
                                    <svg class="w-5 h-5 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    ${salaryRange}
                                </div>
                                ` : ''}
                                ${job.category ? `
                                <div class="flex items-center text-gray-600">
                                    <svg class="w-5 h-5 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                    </svg>
                                    ${job.category.name}
                                </div>
                                ` : ''}
                                <div class="flex items-center text-gray-600">
                                    <svg class="w-5 h-5 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                    </svg>
                                    ${workEnv}
                                </div>
                                ${job.employment_type ? `
                                <div class="flex items-center text-gray-600">
                                    <svg class="w-5 h-5 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                    </svg>
                                    ${job.employment_type.charAt(0).toUpperCase() + job.employment_type.slice(1).replace('_', ' ')}
                                </div>
                                ` : ''}
                                ${postedDate ? `
                                <div class="flex items-center text-gray-600">
                                    <svg class="w-5 h-5 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Posted ${postedDate}${deadlineDate ? ` • Expiring ${deadlineDate}` : ''}
                                </div>
                                ` : ''}
                            </div>
                            
                            ${isExpiringSoon && deadlineDate ? `
                            <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-4">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm text-yellow-700">
                                            This job posting is expiring soon! Apply before ${deadlineDate}
                                        </p>
                                    </div>
                                </div>
                            </div>
                            ` : ''}
                            
                            <!-- Action Icons -->
                            <div class="flex items-center space-x-4 mb-6">
                                <button onclick="shareJob(${job.id})" class="p-2 text-gray-400 hover:text-gray-600 transition" title="Share">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342c.400 0 .816-.04 1.249-.11 1.844-.428 3.305-1.812 3.305-3.517 0-1.003-.18-1.947-.493-2.827-.112-.34-.012-.709.098-1.036.198-.598.234-1.235.098-1.827-.272-1.185-.901-2.095-1.66-2.527-.298-.16-.637-.22-.969-.14-.857.187-1.628.673-2.338 1.33-1.12 1.02-1.855 2.47-2.028 4.005-.054.55-.08 1.09-.08 1.645 0 .41.03.809.085 1.204.178.814.46 1.511.81 2.082.748 1.222 1.98 2.07 3.19 2.07z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                </button>
                                <button id="save-job-btn" onclick="saveJob(${job.id})" class="p-2 text-gray-400 hover:text-pink-500 transition" title="Save">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                    </svg>
                                </button>
                                <button onclick="bookmarkJob(${job.id})" class="p-2 text-gray-400 hover:text-gray-600 transition" title="Bookmark">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"></path>
                                    </svg>
                                </button>
                            </div>
                            
                            <!-- Job Image Placeholder -->
                            <div class="w-full h-64 bg-gradient-to-r from-blue-100 to-pink-100 rounded-xl mb-6 flex items-center justify-center">
                                <svg class="w-24 h-24 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            
                            <!-- Job Description -->
                            <div class="mb-6">
                                <h2 class="text-xl font-bold text-gray-900 mb-3">Job Description</h2>
                                <p class="text-gray-700 leading-relaxed">${job.description || 'No description provided.'}</p>
                            </div>
                            
                            ${job.requirements ? `
                            <div class="mb-6">
                                <h2 class="text-xl font-bold text-gray-900 mb-3">Key Responsibilities</h2>
                                <div class="text-gray-700 leading-relaxed whitespace-pre-wrap">${job.requirements}</div>
                            </div>
                            ` : ''}
                            
                            ${job.benefits ? `
                            <div class="mb-6">
                                <h2 class="text-xl font-bold text-gray-900 mb-3">Highlights</h2>
                                <div class="text-gray-700 leading-relaxed whitespace-pre-wrap">${job.benefits}</div>
                            </div>
                            ` : ''}
                            
                            <!-- Skills Section -->
                            <div class="mb-6">
                                <h2 class="text-2xl font-bold text-gray-900 mb-4">Skills</h2>
                                <div class="flex flex-wrap gap-2">
                                    ${job.experience_level ? `
                                    <span class="px-4 py-2 bg-pink-100 text-pink-700 rounded-full text-sm font-medium">${job.experience_level.charAt(0).toUpperCase() + job.experience_level.slice(1)} Level</span>
                                    ` : ''}
                                    <span class="px-4 py-2 bg-pink-100 text-pink-700 rounded-full text-sm font-medium">Open to Everyone</span>
                                    ${job.category ? `
                                    <span class="px-4 py-2 bg-pink-100 text-pink-700 rounded-full text-sm font-medium">${job.category.name}</span>
                                    ` : ''}
                                </div>
                            </div>
                            
                            <!-- Employer Questions -->
                            <div class="mb-6">
                                <h2 class="text-2xl font-bold text-gray-900 mb-3">Employer questions</h2>
                                <p class="text-gray-600 mb-3">Your application will include the following questions:</p>
                                <ul class="list-disc list-inside space-y-2 text-gray-700">
                                    <li>What's your expected monthly basic salary?</li>
                                    <li>Which of the following statements best describes your right to work in Seychelles?</li>
                                    <li>Which of the following types of qualifications do you have?</li>
                                    <li>How many years' experience do you have in this role?</li>
                                </ul>
                            </div>
                            
                            <!-- Stay Cautious -->
                            <div class="mb-6">
                                <h2 class="text-2xl font-bold text-gray-900 mb-3">Stay cautious</h2>
                                <p class="text-gray-700 mb-4">Protect yourself: never share your bank or credit card details when applying for jobs. Report any suspicious job postings immediately.</p>
                                <div class="flex items-center space-x-4">
                                    <button onclick="reportJob(${job.id})" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9"></path>
                                        </svg>
                                        Report Job
                                    </button>
                                    <a href="#" class="text-blue-600 hover:text-blue-700 font-medium">Learn More</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Right Column - Sidebar -->
                    <div class="space-y-6">
                        <!-- Company Card -->
                        ${job.company ? `
                        <div class="bg-white rounded-xl shadow-sm p-6">
                            <div class="flex items-center space-x-4 mb-4">
                                <div class="w-16 h-16 bg-gradient-to-r from-blue-100 to-pink-100 rounded-full flex items-center justify-center">
                                    ${job.company.logo ? `
                                    <img src="${job.company.logo}" alt="${job.company.name}" class="w-16 h-16 rounded-full object-cover">
                                    ` : `
                                    <span class="text-2xl font-bold text-gray-600">${job.company.name.charAt(0)}</span>
                                    `}
                                </div>
                                <div>
                                    <h3 class="font-bold text-gray-900">${job.company.name}</h3>
                                    <p class="text-sm text-gray-500">BRN/UEN: ${job.company.id || 'N/A'}</p>
                                    <div class="flex items-center mt-1">
                                        <span class="text-yellow-400">★</span>
                                        <span class="text-sm text-gray-600 ml-1">4.2 (45 reviews)</span>
                                    </div>
                                </div>
                            </div>
                            <div class="space-y-2">
                                <button onclick="viewEmployerProfile(${job.company.id})" class="w-full px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition flex items-center justify-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                    View employer profile
                                </button>
                                <button id="follow-company-btn" onclick="toggleFollowCompany(${job.company.id})" class="w-full px-4 py-2 border border-blue-300 text-blue-700 rounded-lg hover:bg-blue-50 transition flex items-center justify-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                                    </svg>
                                    Follow Company
                                </button>
                            </div>
                        </div>
                        ` : ''}
                        
                        <!-- Apply Section -->
                        <div class="bg-white rounded-xl shadow-sm p-6">
                            <h3 class="text-lg font-bold text-gray-900 mb-4">Interested in this job?</h3>
                            <div id="apply-button-container">
                                <button id="apply-btn-${job.id}" onclick="handleApply(${job.id})" class="w-full bg-gradient-to-r from-pink-500 to-pink-600 text-white px-6 py-4 rounded-xl font-semibold hover:from-pink-600 hover:to-pink-700 transition transform hover:scale-[1.02] active:scale-[0.98] flex items-center justify-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                    </svg>
                                    Apply for this job
                                </button>
                            </div>
                        </div>
                        
                        <!-- Other Jobs from Company -->
                        ${otherCompanyJobs.length > 0 ? `
                        <div class="bg-white rounded-xl shadow-sm p-6">
                            <h3 class="text-lg font-bold text-gray-900 mb-4">See other available jobs?</h3>
                            <button onclick="navigateTo('/jobs?company=' + (job.company?.id || ''))" class="w-full bg-gradient-to-r from-pink-500 to-pink-600 text-white px-6 py-3 rounded-xl font-semibold hover:from-pink-600 hover:to-pink-700 transition">
                                View all jobs
                            </button>
                        </div>
                        ` : ''}
                        
                        <!-- Similar Jobs -->
                        ${similarJobs.length > 0 ? `
                        <div class="bg-white rounded-xl shadow-sm p-6">
                            <h3 class="text-lg font-bold text-gray-900 mb-4">Similar jobs you might like</h3>
                            <div class="space-y-4">
                                ${similarJobs.slice(0, 2).map(similarJob => `
                                <a href="/jobs/${similarJob.id}" wire:navigate class="block p-4 border border-gray-200 rounded-lg hover:border-blue-300 hover:shadow-md transition">
                                    <div class="flex items-start space-x-3">
                                        <div class="w-12 h-12 bg-gradient-to-r from-blue-100 to-pink-100 rounded-full flex items-center justify-center flex-shrink-0">
                                            ${similarJob.company?.logo ? `
                                            <img src="${similarJob.company.logo}" alt="${similarJob.company.name}" class="w-12 h-12 rounded-full object-cover">
                                            ` : `
                                            <span class="text-lg font-bold text-gray-600">${similarJob.company?.name?.charAt(0) || 'C'}</span>
                                            `}
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <h4 class="font-semibold text-gray-900 truncate">${similarJob.title}</h4>
                                            <p class="text-sm text-gray-600 truncate">${similarJob.company?.name || 'Company'}</p>
                                            <p class="text-sm text-gray-500">@ ${similarJob.location || 'Location not specified'}</p>
                                        </div>
                                    </div>
                                </a>
                                `).join('')}
                            </div>
                            <a href="/jobs?category=${job.category?.id || ''}" wire:navigate class="block mt-4 text-blue-600 hover:text-blue-700 font-medium text-sm">
                                View more similar jobs →
                            </a>
                        </div>
                        ` : ''}
                    </div>
                </div>
            `;
        } else {
            const element = document.getElementById('job-detail');
            if (element) {
                element.innerHTML = '<div class="text-center py-12 text-red-500"><p class="text-lg mb-2">Job not found</p><p class="text-sm">The job you\'re looking for doesn\'t exist</p></div>';
            }
        }
        } catch (error) {
            console.error('Error loading job:', error);
            const element = document.getElementById('job-detail');
            if (element) {
                element.innerHTML = '<div class="text-center py-12 text-red-500"><p class="text-lg mb-2">Error loading job</p><p class="text-sm">Please try again later</p></div>';
            }
        } finally {
            // Clear loading state after a short delay to allow for rapid navigation
            setTimeout(() => {
                if (loadingJobId === jobId) {
                    loadingJobId = null;
                    loadingPromise = null;
                }
            }, 100);
        }
    })();
    
    return loadingPromise;
};

// Helper functions
function applySavedStateFromJob(job) {
    if (!job?.is_saved) return;
    const btn = document.getElementById('save-job-btn');
    if (btn) {
        btn.classList.remove('text-gray-400');
        btn.classList.add('text-pink-500');
    }
}

function applyApplicationStateFromJob(job) {
    const hasApplied = !!(job?.application_status || job?.has_applied);
    if (!hasApplied) return;
    const applyBtn = document.getElementById(`apply-btn-${job.id}`);
    if (!applyBtn) return;
    applyBtn.disabled = true;
    applyBtn.classList.remove('bg-gradient-to-r', 'from-pink-500', 'to-pink-600', 'hover:from-pink-600', 'hover:to-pink-700');
    applyBtn.classList.add('bg-gray-400', 'cursor-not-allowed');
    applyBtn.innerHTML = `
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
        </svg>
        Already Applied
    `;
    applyBtn.onclick = null;
}

function applyFollowedState(isFollowed) {
    const btn = document.getElementById('follow-company-btn');
    if (!btn || !isFollowed) return;
    btn.textContent = 'Following';
    btn.classList.remove('border-blue-300', 'text-blue-700');
    btn.classList.add('bg-blue-600', 'text-white', 'border-blue-600');
}

async function checkJobSaved(jobId) {
    try {
        const response = await fetch(`${API_BASE}/job-seeker/saved-jobs/check/${jobId}`);
        if (response.ok) {
            const data = await response.json();
            if (data.is_saved) applySavedStateFromJob({ is_saved: true });
        }
    } catch (error) {
        // Not logged in or error - ignore
    }
}

async function checkCompanyFollowed(companyId) {
    try {
        const response = await fetch(`${API_BASE}/job-seeker/followed-companies/check/${companyId}`);
        if (response.ok) {
            const data = await response.json();
            applyFollowedState(!!data.is_followed);
        }
    } catch (error) {
        // Not logged in or error - ignore
    }
}

async function checkApplicationStatus(jobId) {
    try {
        const response = await fetch(`${API_BASE}/job-seeker/applications/check/${jobId}`, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        
        if (response.ok) {
            const data = await response.json();
            if (data.has_applied) applyApplicationStateFromJob({ id: jobId, has_applied: true });
        }
    } catch (error) {
        // Not logged in or error - ignore, button will work normally
    }
}

window.handleApply = function(jobId) {
    if (typeof window.handleJobApply === 'function') {
        window.handleJobApply(jobId);
    } else if (!window.IS_AUTHENTICATED) {
        if (typeof window.openAuthModal === 'function') {
            window.openAuthModal('login');
        } else {
            window.location.href = '/';
        }
    } else {
        if (typeof window.navigateTo === 'function') {
            window.navigateTo(`/jobs/${jobId}/apply`);
        } else {
            window.location.href = `/jobs/${jobId}/apply`;
        }
    }
};

window.shareJob = function(jobId) {
    if (navigator.share) {
        navigator.share({
            title: 'Check out this job',
            url: window.location.href
        });
    } else {
        navigator.clipboard.writeText(window.location.href);
        showSuccessToast('Job link copied to clipboard!');
    }
};

window.bookmarkJob = function(jobId) {
    window.saveJob(jobId);
};

window.reportJob = function(jobId) {
    if (confirm('Are you sure you want to report this job posting?')) {
        showInfoToast('Thank you for reporting. We will review this job posting.');
    }
};

window.viewEmployerProfile = function(companyId) {
    if (typeof window.navigateTo === 'function') {
        window.navigateTo(`/companies/${companyId}`);
    } else {
        window.location.href = `/companies/${companyId}`;
    }
};

window.saveJob = async function(jobId) {
    try {
        const response = await fetch(`${API_BASE}/job-seeker/saved-jobs`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ job_id: jobId })
        });
        
        if (response.status === 401) {
            if (typeof window.openAuthModal === 'function') window.openAuthModal('login');
            return;
        }
        
        const data = await response.json();
        const btn = document.getElementById('save-job-btn');
        
        if (response.ok) {
            if (btn) {
                btn.classList.remove('text-gray-400');
                btn.classList.add('text-pink-500');
            }
        } else {
            showErrorToast(data.message || 'Failed to save job');
        }
    } catch (error) {
        console.error('Error saving job:', error);
        if (typeof window.openAuthModal === 'function') window.openAuthModal('login');
    }
};

window.toggleFollowCompany = async function(companyId) {
    try {
        const currentJobId = getJobIdFromUrl();
        const checkResponse = await fetch(`${API_BASE}/job-seeker/followed-companies/check/${companyId}`);
        if (checkResponse.status === 401) {
            if (typeof window.openAuthModal === 'function') window.openAuthModal('login');
            return;
        }
        
        const checkData = await checkResponse.json();
        const btn = document.getElementById('follow-company-btn');
        
        if (checkData.is_followed) {
            const response = await fetch(`${API_BASE}/job-seeker/followed-companies/${companyId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });
            
            if (response.ok) {
                if (btn) {
                    btn.textContent = 'Follow Company';
                    btn.classList.remove('bg-blue-600', 'text-white', 'border-blue-600');
                    btn.classList.add('border-blue-300', 'text-blue-700');
                }
            }
        } else {
            const response = await fetch(`${API_BASE}/job-seeker/followed-companies`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ company_id: companyId })
            });
            
            if (response.ok) {
                if (btn) {
                    btn.textContent = 'Following';
                    btn.classList.remove('border-blue-300', 'text-blue-700');
                    btn.classList.add('bg-blue-600', 'text-white', 'border-blue-600');
                }
            } else {
                const data = await response.json();
                showErrorToast(data.message || 'Failed to follow company');
            }
        }
    } catch (error) {
        console.error('Error toggling follow:', error);
        if (typeof window.openAuthModal === 'function') window.openAuthModal('login');
    }
};
