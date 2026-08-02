/* Jobs search page — loaded globally for Livewire wire:navigate */
const API_BASE = '/api';

    function bindToggleMoreFilters() {
        const btn = document.getElementById('toggleMoreFilters');
        if (!btn || btn.dataset.listenerAttached === 'true') return;
        btn.dataset.listenerAttached = 'true';
        btn.addEventListener('click', function() {
            const panel = document.getElementById('moreFiltersPanel');
            const label = document.getElementById('toggleMoreFiltersLabel');
            if (!panel || !label) return;
            if (panel.style.display === 'none') {
                panel.style.display = 'block';
                label.textContent = 'Hide options';
            } else {
                panel.style.display = 'none';
                label.textContent = 'Show more options';
            }
        });
    }

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
        const data = await response.json().catch(() => ({}));
        if (!response.ok) {
            throw new Error(data.message || `Request failed (${response.status})`);
        }
        return data;
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
                    class="w-full px-3 py-2 text-left hover:bg-gray-50 dark:hover:bg-gray-700 flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300"
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
                const p = document.getElementById('locationDropdownPanel');
                const b = document.getElementById('locationDropdownButton');
                if (p && b && !p.contains(e.target) && !b.contains(e.target)) {
                    p.classList.add('hidden');
                    p.style.display = 'none';
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
                    class="w-full px-3 py-2 text-left hover:bg-gray-50 dark:hover:bg-gray-700 flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300"
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
                const p = document.getElementById('jobTagsDropdownPanel');
                const b = document.getElementById('jobTagsDropdownButton');
                if (p && b && !p.contains(e.target) && !b.contains(e.target)) {
                    p.classList.add('hidden');
                    p.style.display = 'none';
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
                const p = document.getElementById('salaryDropdownPanel');
                const b = document.getElementById('salaryDropdownButton');
                if (p && b && !p.contains(e.target) && !b.contains(e.target)) {
                    p.classList.add('hidden');
                    p.style.display = 'none';
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
        
        // Unfiltered list → cached published endpoint; otherwise search
        const hasFilters = !!(keyword || categoryId || location || employmentType || salaryMin || jobTags || remoteOption
            || (sortBy && sortBy !== 'latest' && sortBy !== 'newest'));
        let jobsUrl = hasFilters
            ? `${API_BASE}/jobs/search?per_page=16&page=${page}`
            : `${API_BASE}/jobs/published?per_page=16&page=${page}`;
        if (hasFilters) {
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
            container.className = 'bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-md overflow-hidden';
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
                                        <p class="text-sm text-gray-700 dark:text-gray-300 mb-3 line-clamp-1">${job.description ? job.description.substring(0, 140) + '...' : 'No description provided.'}</p>
                                        <div class="flex flex-wrap gap-2 mb-3">
                                            ${job.location ? `<span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full border border-gray-300 dark:border-gray-600 text-xs text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800">
                                                <svg class="w-3 h-3 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path></svg>
                                                ${job.location}
                                            </span>` : ''}
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full border border-gray-300 dark:border-gray-600 text-xs text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800">
                                                <svg class="w-3 h-3 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                ${job.employment_type || 'Full Time'}
                                            </span>
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full border border-gray-300 dark:border-gray-600 text-xs text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800">
                                                <svg class="w-3 h-3 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                                                ${getWorkType(job)}
                                            </span>
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full border border-gray-300 dark:border-gray-600 text-xs text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800">
                                                <svg class="w-3 h-3 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                                ${job.category?.name || 'Uncategorized'}
                                            </span>
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full border border-gray-300 dark:border-gray-600 text-xs text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800">
                                                <svg class="w-3 h-3 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                                ${getExperienceLevel(job)}
                                            </span>
                                        </div>
                                        <div class="flex items-center gap-1 text-sm font-semibold text-gray-800 dark:text-gray-100 mb-1">
                                            <span>${job.company?.name || 'Company'}</span>
                                            <svg class="w-3.5 h-3.5 flex-shrink-0" style="color: #2563eb;" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                        </div>
                                        <div class="space-y-1 text-xs text-gray-500 dark:text-gray-400">
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
                                        <p class="text-xs text-gray-500 dark:text-gray-400">/ month</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `).join('');
            } else {
                // List view rendering
                container.innerHTML = data.data.map((job, index) => `
                    <div class="job-card bg-white dark:bg-gray-800 rounded-lg shadow-sm dark:shadow-none hover:shadow-md transition-all p-4 border-2 ${selectedJobId == job.id ? 'job-card-selected' : 'border-gray-100'} cursor-pointer" data-job-id="${job.id}">
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
                                        <span class="text-xs text-gray-600 dark:text-gray-400 font-medium">${job.company?.name || 'Company'}</span>
                                        <svg class="w-3.5 h-3.5 flex-shrink-0" style="color: #3b82f6;" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="space-y-1 text-xs text-gray-500 dark:text-gray-400">
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
                                    <button class="apply-now-btn pink-button text-white px-4 py-1.5 rounded-md text-xs font-semibold transition shadow-sm dark:shadow-none" data-job-id="${job.id}">
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
            container.innerHTML = '<div class="text-center py-12 text-gray-500 dark:text-gray-400"><p class="text-lg mb-2">No jobs found</p><p class="text-sm">Try adjusting your search criteria</p></div>';
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
                        : `<div class="w-full h-full bg-gray-200 dark:bg-gray-700 rounded flex items-center justify-center"><svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg></div>`;
                    const sjDate = sj.published_at ? formatDate(sj.published_at) : '';
                    return `<div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 hover:shadow-md transition cursor-pointer" onclick="selectJob(${sj.id})">
                        <div class="flex items-start gap-3">
                            <div class="w-12 h-12 flex-shrink-0 rounded overflow-hidden bg-gray-100 dark:bg-gray-800">${sjLogo}</div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-start justify-between">
                                    <h4 class="text-sm font-bold text-gray-900 dark:text-white line-clamp-2">${sj.title || 'Job Title'}</h4>
                                    <svg class="w-4 h-4 text-yellow-400 flex-shrink-0 ml-1" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                                </div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">${sj.company?.name || ''}</p>
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
            pages.push(`<button onclick="goToPage(${currentPage - 1})" class="px-4 py-2 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition">Previous</button>`);
        }
        
        // Page numbers
        for (let i = 1; i <= lastPage; i++) {
            if (i === 1 || i === lastPage || (i >= currentPage - 2 && i <= currentPage + 2)) {
                const isActive = i === currentPage;
                pages.push(`<button onclick="goToPage(${i})" class="px-4 py-2 ${isActive ? 'text-white' : 'bg-white text-gray-700 dark:text-gray-300 hover:bg-gray-50'} border border-gray-300 dark:border-gray-600 rounded-lg transition" ${isActive ? 'style="background: linear-gradient(135deg, #374151 0%, #1f2937 100%);"' : ''}>${i}</button>`);
            } else if (i === currentPage - 3 || i === currentPage + 3) {
                pages.push(`<span class="px-2 text-gray-500 dark:text-gray-400">...</span>`);
            }
        }
        
        // Next button
        if (currentPage < lastPage) {
            pages.push(`<button onclick="goToPage(${currentPage + 1})" class="px-4 py-2 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition">Next</button>`);
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

    
    function initialize() {
        if (!document.getElementById('jobs-container')) return;
        bindToggleMoreFilters();
        initializeEventListeners();
        loadPageData();
    }

    window.initializeJobsPage = function () {
        currentRequest = null;
        document.querySelectorAll('[data-listener-attached]').forEach((el) => {
            delete el.dataset.listenerAttached;
        });
        initialize();
    };
