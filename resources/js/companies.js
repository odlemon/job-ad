function csrfToken() {
    return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
}

function starRow(rating) {
    let html = '';
    for (let i = 1; i <= 5; i++) {
        const on = i <= rating ? 'text-yellow-400 fill-current' : 'text-gray-300 fill-current';
        html += `<svg class="w-3.5 h-3.5 ${on}" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>`;
    }
    return html;
}

function companyCardHtml(c) {
    const initial = (c.name || '?').charAt(0).toUpperCase();
    const logo = c.logo_url
        ? `<img src="${c.logo_url}" alt="${c.name}" class="w-full h-full object-cover" loading="lazy">`
        : `<span class="text-4xl font-bold text-blue-600 dark:text-blue-400">${initial}</span>`;
    const jobs = c.jobs_count || 0;
    return `
        <a href="${c.url}" wire:navigate
           class="company-card bg-white dark:bg-gray-800 rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 p-6 flex flex-col items-center justify-center cursor-pointer group border border-gray-100 dark:border-gray-700 hover:border-blue-300 dark:hover:border-blue-600 hover:-translate-y-1">
            <div class="w-20 h-20 bg-gradient-to-br from-blue-50 to-slate-100 dark:from-gray-700 dark:to-gray-600 rounded-xl flex items-center justify-center mb-4 group-hover:scale-105 transition-transform duration-300 shadow-sm overflow-hidden">${logo}</div>
            <h3 class="text-base font-bold text-gray-900 dark:text-white text-center mb-2 line-clamp-2 min-h-[3rem] flex items-center justify-center">${c.name}</h3>
            <p class="text-sm text-gray-600 dark:text-gray-400 text-center font-medium">${jobs} ${jobs === 1 ? 'job' : 'jobs'}</p>
        </a>`;
}

window.initializeCompaniesPage = function () {
    const root = document.getElementById('companies-page');
    if (!root || root.dataset.bound === 'true') return;
    root.dataset.bound = 'true';

    const apiUrl = root.dataset.apiUrl;
    const grid = document.getElementById('companies-grid');
    const countEl = document.getElementById('company-count');
    const emptyEl = document.getElementById('companies-empty');
    const paginationEl = document.getElementById('companies-pagination');
    const searchInput = document.getElementById('company-search');
    const industryMenu = document.getElementById('industry-menu');
    const industryBtn = document.getElementById('industry-filter-btn');
    const industryBadge = document.getElementById('industry-count-badge');
    const industryClear = document.getElementById('industry-clear');
    const jobsMenu = document.getElementById('jobs-menu');
    const jobsBtn = document.getElementById('jobs-filter-btn');
    const clearBtn = document.getElementById('clear-filters-btn');

    let selectedIndustries = (root.dataset.initialIndustry || '')
        .split(',')
        .map((s) => s.trim())
        .filter(Boolean);
    let jobsValue = root.dataset.initialJobs || 'all';
    let page = Number(paginationEl?.dataset.page || 1);
    let lastPage = Number(paginationEl?.dataset.lastPage || 1);
    let debounceTimer = null;
    let loading = false;

    function hasActiveFilters() {
        return !!(searchInput?.value || '').trim() || selectedIndustries.length > 0 || jobsValue !== 'all';
    }

    function paintFilterChrome() {
        if (industryBadge) {
            industryBadge.textContent = String(selectedIndustries.length || '');
            industryBadge.classList.toggle('hidden', selectedIndustries.length === 0);
        }
        industryClear?.classList.toggle('hidden', selectedIndustries.length === 0);
        clearBtn?.classList.toggle('hidden', !hasActiveFilters());
        document.querySelectorAll('.jobs-option').forEach((btn) => {
            const active = btn.dataset.jobsValue === jobsValue;
            btn.className =
                'jobs-option w-full text-left px-3 py-2 rounded text-sm hover:bg-gray-50 transition-colors ' +
                (active ? 'bg-blue-50 text-blue-600 font-medium' : 'text-gray-700');
        });
        document.querySelectorAll('.industry-checkbox').forEach((cb) => {
            cb.checked = selectedIndustries.includes(cb.value);
        });
    }

    function params() {
        const q = new URLSearchParams();
        const search = (searchInput?.value || '').trim();
        if (search) q.set('search', search);
        if (selectedIndustries.length) q.set('industry', selectedIndustries.join(','));
        if (jobsValue && jobsValue !== 'all') q.set('jobs', jobsValue);
        q.set('page', String(page));
        q.set('per_page', '24');
        return q;
    }

    function syncUrl() {
        const url = new URL(window.location.href);
        ['search', 'industry', 'jobs', 'sort', 'page'].forEach((k) => url.searchParams.delete(k));
        const search = (searchInput?.value || '').trim();
        if (search) url.searchParams.set('search', search);
        if (selectedIndustries.length) url.searchParams.set('industry', selectedIndustries.join(','));
        if (jobsValue && jobsValue !== 'all') url.searchParams.set('jobs', jobsValue);
        if (page > 1) url.searchParams.set('page', String(page));
        window.history.replaceState({}, '', url);
    }

    function renderPagination() {
        if (!paginationEl) return;
        if (lastPage <= 1) {
            paginationEl.innerHTML = '';
            return;
        }
        let html = `<button type="button" data-page-action="prev" class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-sm disabled:opacity-50" ${page <= 1 ? 'disabled' : ''}>Previous</button>`;
        for (let i = 1; i <= lastPage; i++) {
            html += `<button type="button" data-page="${i}" class="w-10 h-10 rounded-md text-sm font-medium ${
                page === i
                    ? 'bg-blue-600 text-white'
                    : 'border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300'
            }">${i}</button>`;
        }
        html += `<button type="button" data-page-action="next" class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-sm disabled:opacity-50" ${page >= lastPage ? 'disabled' : ''}>Next</button>`;
        paginationEl.innerHTML = html;
        paginationEl.querySelectorAll('[data-page]').forEach((btn) => {
            btn.addEventListener('click', () => {
                page = Number(btn.dataset.page);
                fetchList();
            });
        });
        paginationEl.querySelector('[data-page-action="prev"]')?.addEventListener('click', () => {
            page = Math.max(1, page - 1);
            fetchList();
        });
        paginationEl.querySelector('[data-page-action="next"]')?.addEventListener('click', () => {
            page = Math.min(lastPage, page + 1);
            fetchList();
        });
    }

    async function fetchList() {
        if (!apiUrl || !grid || loading) return;
        loading = true;
        paintFilterChrome();
        try {
            const res = await fetch(`${apiUrl}?${params().toString()}`, {
                headers: { Accept: 'application/json' },
            });
            const json = await res.json();
            const items = json.data || [];
            const meta = json.meta || {};
            lastPage = meta.last_page || 1;
            page = meta.current_page || page;

            grid.innerHTML = items.map(companyCardHtml).join('');
            if (countEl) {
                countEl.innerHTML = `Showing <span class="font-semibold text-gray-900 dark:text-white">${meta.total ?? items.length}</span> companies`;
            }
            emptyEl?.classList.toggle('hidden', items.length > 0);
            renderPagination();
            syncUrl();
        } catch (e) {
            console.error('Companies fetch failed', e);
        } finally {
            loading = false;
        }
    }

    industryBtn?.addEventListener('click', (e) => {
        e.stopPropagation();
        industryMenu?.classList.toggle('hidden');
        jobsMenu?.classList.add('hidden');
    });
    jobsBtn?.addEventListener('click', (e) => {
        e.stopPropagation();
        jobsMenu?.classList.toggle('hidden');
        industryMenu?.classList.add('hidden');
    });

    document.addEventListener('mousedown', (e) => {
        if (!e.target.closest('.filter-dropdown')) {
            industryMenu?.classList.add('hidden');
            jobsMenu?.classList.add('hidden');
        }
    });

    document.querySelectorAll('.industry-checkbox').forEach((cb) => {
        cb.addEventListener('change', () => {
            selectedIndustries = Array.from(document.querySelectorAll('.industry-checkbox:checked')).map(
                (el) => el.value
            );
            page = 1;
            fetchList();
        });
    });

    industryClear?.addEventListener('click', (e) => {
        e.preventDefault();
        e.stopPropagation();
        selectedIndustries = [];
        page = 1;
        fetchList();
    });

    document.querySelectorAll('.jobs-option').forEach((btn) => {
        btn.addEventListener('click', () => {
            jobsValue = btn.dataset.jobsValue || 'all';
            jobsMenu?.classList.add('hidden');
            page = 1;
            fetchList();
        });
    });

    clearBtn?.addEventListener('click', () => {
        if (searchInput) searchInput.value = '';
        selectedIndustries = [];
        jobsValue = 'all';
        page = 1;
        fetchList();
    });

    searchInput?.addEventListener('input', () => {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => {
            page = 1;
            fetchList();
        }, 300);
    });

    paintFilterChrome();
    renderPagination();
};

window.initializeCompanyDetailPage = function () {
    const root = document.getElementById('company-detail-page');
    if (!root) return;

    function requireAuth(redirectBack = true) {
        if (window.IS_AUTHENTICATED) return true;
        if (redirectBack && typeof sessionStorage !== 'undefined') {
            sessionStorage.setItem('loginRedirect', window.location.href);
        }
        if (typeof window.openAuthModal === 'function') {
            window.openAuthModal('login');
        }
        return false;
    }

    function toastSuccess(msg) {
        if (typeof window.showSuccessToast === 'function') window.showSuccessToast(msg);
        else alert(msg);
    }
    function toastError(msg) {
        if (typeof window.showErrorToast === 'function') window.showErrorToast(msg);
        else alert(msg);
    }
    function toastInfo(msg) {
        if (typeof window.showInfoToast === 'function') window.showInfoToast(msg);
        else if (typeof window.showSuccessToast === 'function') window.showSuccessToast(msg);
        else alert(msg);
    }

    const TAB_ACTIVE =
        'bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400';
    const TAB_INACTIVE =
        'text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-cyan-400 hover:bg-gray-50 dark:hover:bg-gray-700';

    function activateTab(key) {
        root.querySelectorAll('.company-tab').forEach((btn) => {
            const active = btn.dataset.companyTab === key;
            btn.className =
                'company-tab px-6 py-3 font-medium text-sm rounded-lg transition-colors whitespace-nowrap ' +
                (active ? TAB_ACTIVE : TAB_INACTIVE);
        });
        root.querySelectorAll('.company-tab-panel').forEach((panel) => {
            panel.classList.toggle('hidden', panel.id !== `tab-${key}`);
        });
    }

    root.querySelectorAll('.company-tab').forEach((btn) => {
        if (btn.dataset.bound === 'true') return;
        btn.dataset.bound = 'true';
        btn.addEventListener('click', () => activateTab(btn.dataset.companyTab));
    });
    activateTab('profile');

    // Follow
    const followBtn = document.getElementById('follow-btn');
    const followLabel = document.getElementById('follow-btn-label');
    const followHeart = document.getElementById('follow-heart');
    const followersCountEl = document.getElementById('followers-count');
    let following = root.dataset.isFollowing === '1';

    function paintFollow() {
        if (!followBtn) return;
        followBtn.className =
            'px-6 py-2.5 font-medium rounded-lg border-2 transition-all duration-200 flex items-center gap-2 ' +
            (following
                ? 'bg-blue-50 dark:bg-blue-900/20 border-blue-600 text-blue-600 dark:text-blue-400'
                : 'bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:border-blue-600 dark:hover:border-cyan-400');
        if (followLabel) followLabel.textContent = following ? 'Following' : 'Follow';
        if (followHeart) followHeart.classList.toggle('fill-current', following);
    }

    async function toggleFollow() {
        if (!requireAuth()) return;

        if (root.dataset.isSeeker !== '1') {
            // Authenticated but not a job seeker (e.g. employer)
            toastInfo('Follow is available for job seeker accounts.');
            return;
        }

        const url = following ? root.dataset.unfollowUrl : root.dataset.followUrl;
        const method = following ? 'DELETE' : 'POST';
        try {
            const res = await fetch(url, {
                method,
                headers: {
                    Accept: 'application/json',
                    'X-CSRF-TOKEN': csrfToken(),
                    'X-Requested-With': 'XMLHttpRequest',
                },
            });
            if (res.status === 401) {
                requireAuth();
                return;
            }
            const json = await res.json().catch(() => ({}));
            if (res.status === 403) {
                toastError(json.message || 'Only job seekers can follow companies.');
                return;
            }
            if (!res.ok) {
                toastError(json.message || 'Unable to update follow status');
                return;
            }
            following = !!json.is_following;
            root.dataset.isFollowing = following ? '1' : '0';
            paintFollow();
            if (followersCountEl && json.followers_count != null) {
                followersCountEl.textContent = Number(json.followers_count).toLocaleString();
            }
            toastSuccess(following ? 'You are now following this company' : 'Unfollowed');
        } catch (e) {
            console.error(e);
            toastError('Unable to update follow status');
        }
    }

    followBtn?.addEventListener('click', () => {
        toggleFollow();
    });

    // Send CV → auth, then open Jobs tab (Apply on a role)
    document.getElementById('send-cv-btn')?.addEventListener('click', () => {
        if (!requireAuth()) return;

        const jobsList = document.getElementById('company-jobs-list');
        const hasJobs = jobsList && jobsList.querySelector('.company-job-card, a');
        if (hasJobs) {
            activateTab('jobs');
            document.getElementById('tab-jobs')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
            toastInfo('Choose a position below to apply with your CV.');
            return;
        }
        const email = root.dataset.companyEmail;
        const name = root.dataset.companyName || 'Company';
        if (email) {
            window.location.href = `mailto:${email}?subject=${encodeURIComponent('CV application – ' + name)}`;
        } else {
            toastInfo('This company has no open roles or contact email yet.');
        }
    });

    // Share (no auth required)
    document.getElementById('share-btn')?.addEventListener('click', async () => {
        const shareData = { title: root.dataset.companyName, url: window.location.href };
        try {
            if (navigator.share) {
                await navigator.share(shareData);
            } else {
                await navigator.clipboard.writeText(window.location.href);
                toastSuccess('Company link copied to clipboard');
            }
        } catch (_) {
            /* cancelled */
        }
    });

    // Jobs: alert, filters, list/grid, load more
    let jobsPage = 1;
    let jobsLastPage = Number(root.dataset.jobsLastPage || 1);
    let jobsAlertOn = following;
    const jobsList = document.getElementById('company-jobs-list');
    const jobsLoadMore = document.getElementById('jobs-load-more');
    const jobsAlertBtn = document.getElementById('jobs-alert-btn');
    const typeFilter = document.getElementById('company-jobs-type-filter');
    const eduFilter = document.getElementById('company-jobs-education-filter');
    const sortFilter = document.getElementById('company-jobs-sort');
    const viewListBtn = document.getElementById('company-jobs-view-list');
    const viewGridBtn = document.getElementById('company-jobs-view-grid');
    const jobsVisibleCount = document.getElementById('jobs-visible-count');

    function paintAlertBtn() {
        if (!jobsAlertBtn) return;
        jobsAlertBtn.textContent = jobsAlertOn ? 'Alert Active' : 'Enable Alerts';
        jobsAlertBtn.className =
            'px-4 py-2 rounded-lg font-medium transition-colors ' +
            (jobsAlertOn
                ? 'bg-green-600 text-white hover:bg-green-700'
                : 'bg-blue-600 text-white hover:bg-blue-700');
    }
    paintAlertBtn();

    jobsAlertBtn?.addEventListener('click', async () => {
        if (!requireAuth()) return;
        if (root.dataset.isSeeker !== '1') {
            toastInfo('Job alerts are available for job seeker accounts.');
            return;
        }
        // Alerts = follow company so seeker gets updates
        if (!following) {
            await toggleFollow();
        }
        jobsAlertOn = following;
        paintAlertBtn();
        if (jobsAlertOn) {
            toastSuccess('Job alerts enabled for this company');
        }
    });

    function setJobsView(mode) {
        if (!jobsList) return;
        jobsList.dataset.view = mode;
        if (mode === 'card') {
            jobsList.className = 'grid grid-cols-1 lg:grid-cols-2 gap-4';
            viewGridBtn?.classList.add('bg-blue-600', 'text-white');
            viewGridBtn?.classList.remove('bg-gray-200', 'dark:bg-gray-700', 'text-gray-600', 'dark:text-gray-400');
            viewListBtn?.classList.remove('bg-blue-600', 'text-white');
            viewListBtn?.classList.add('bg-gray-200', 'dark:bg-gray-700', 'text-gray-600', 'dark:text-gray-400');
        } else {
            jobsList.className = 'space-y-4';
            viewListBtn?.classList.add('bg-blue-600', 'text-white');
            viewListBtn?.classList.remove('bg-gray-200', 'dark:bg-gray-700', 'text-gray-600', 'dark:text-gray-400');
            viewGridBtn?.classList.remove('bg-blue-600', 'text-white');
            viewGridBtn?.classList.add('bg-gray-200', 'dark:bg-gray-700', 'text-gray-600', 'dark:text-gray-400');
        }
    }
    viewListBtn?.addEventListener('click', () => setJobsView('list'));
    viewGridBtn?.addEventListener('click', () => setJobsView('card'));

    function filterJobsDom() {
        if (!jobsList) return;
        const type = (typeFilter?.value || '').toLowerCase();
        const edu = (eduFilter?.value || '').toLowerCase();
        const sort = sortFilter?.value || 'default';
        const cards = Array.from(jobsList.querySelectorAll('.company-job-card'));
        let visible = 0;
        cards.forEach((card) => {
            const okType = !type || (card.dataset.jobType || '').includes(type);
            const okEdu = !edu || (card.dataset.education || '').includes(edu);
            const show = okType && okEdu;
            card.style.display = show ? '' : 'none';
            if (show) visible += 1;
        });
        if (sort === 'a-z' || sort === 'z-a') {
            cards
                .sort((a, b) => {
                    const cmp = (a.dataset.title || '').localeCompare(b.dataset.title || '');
                    return sort === 'a-z' ? cmp : -cmp;
                })
                .forEach((c) => jobsList.appendChild(c));
        }
        if (jobsVisibleCount) jobsVisibleCount.textContent = String(visible);
    }
    [typeFilter, eduFilter, sortFilter].forEach((el) => el?.addEventListener('change', filterJobsDom));

    // Intercept Apply Now: auth gate then go to job (or apply flow)
    jobsList?.addEventListener('click', (e) => {
        const applyLink = e.target.closest('a[data-apply-job], a.company-job-apply');
        if (!applyLink) return;
        if (!window.IS_AUTHENTICATED) {
            e.preventDefault();
            if (typeof sessionStorage !== 'undefined') {
                sessionStorage.setItem('loginRedirect', applyLink.href);
            }
            if (typeof window.openAuthModal === 'function') window.openAuthModal('login');
        }
    });

    function jobCard(j) {
        const chips = [j.location, j.job_type, j.category]
            .filter(Boolean)
            .map(
                (t) =>
                    `<span class="px-3 py-1 bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 text-gray-700 dark:text-gray-300 text-xs rounded-full">${t}</span>`
            )
            .join('');
        return `
            <div class="company-job-card bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 hover:shadow-lg transition-shadow overflow-hidden"
                 data-title="${(j.title || '').toLowerCase()}"
                 data-job-type="${(j.job_type || '').toLowerCase()}"
                 data-education="">
                <div class="p-5">
                    <a href="${j.url}" wire:navigate class="text-lg font-semibold text-pink-600 dark:text-pink-400 hover:text-pink-700">${j.title}</a>
                    <div class="flex flex-wrap gap-2 mt-3 mb-3">${chips}</div>
                </div>
                <a href="${j.url}" data-apply-job wire:navigate class="company-job-apply block w-full bg-blue-600 hover:bg-blue-700 text-center text-sm font-semibold text-white py-2.5">Apply Now</a>
            </div>`;
    }

    jobsLoadMore?.addEventListener('click', async () => {
        if (jobsPage >= jobsLastPage) return;
        jobsPage += 1;
        try {
            const res = await fetch(`${root.dataset.jobsApi}?page=${jobsPage}&per_page=10`, {
                headers: { Accept: 'application/json' },
            });
            const json = await res.json();
            const items = json.data || [];
            jobsLastPage = json.meta?.last_page || jobsLastPage;
            document.getElementById('jobs-empty')?.remove();
            items.forEach((j) => {
                jobsList.insertAdjacentHTML('beforeend', jobCard(j));
            });
            filterJobsDom();
            if (jobsPage >= jobsLastPage) jobsLoadMore.classList.add('hidden');
        } catch (e) {
            console.error(e);
        }
    });

    // Reviews load more / sort
    let reviewsPage = 1;
    let reviewsLastPage = Number(root.dataset.reviewsLastPage || 1);
    const reviewsList = document.getElementById('company-reviews-list');
    const reviewsLoadMore = document.getElementById('reviews-load-more');
    const reviewsSort = document.getElementById('reviews-sort');

    function reviewCard(r) {
        const meta = [r.role, r.location, r.employment_status].filter(Boolean).join(' · ');
        return `
            <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 bg-gray-50 dark:bg-gray-700/50">
                <div class="flex items-center gap-2 mb-2">
                    <div class="flex">${starRow(r.rating)}</div>
                    <span class="text-xs text-gray-500 dark:text-gray-400">${r.created_at || ''}</span>
                </div>
                ${meta ? `<p class="text-xs text-gray-500 dark:text-gray-400 mb-2">${meta}</p>` : ''}
                ${r.good_things ? `<p class="text-sm text-gray-700 dark:text-gray-300 mb-1"><span class="font-semibold">The good things:</span> ${r.good_things}</p>` : ''}
                ${r.challenges ? `<p class="text-sm text-gray-700 dark:text-gray-300"><span class="font-semibold">The challenges:</span> ${r.challenges}</p>` : ''}
            </div>`;
    }

    async function fetchReviews(reset) {
        if (!root.dataset.reviewsApi || !reviewsList) return;
        if (reset) {
            reviewsPage = 1;
            reviewsList.innerHTML = '';
        }
        const sort = reviewsSort?.value || 'newest';
        const res = await fetch(
            `${root.dataset.reviewsApi}?page=${reviewsPage}&per_page=10&sort=${encodeURIComponent(sort)}`,
            { headers: { Accept: 'application/json' } }
        );
        const json = await res.json();
        const items = json.data || [];
        reviewsLastPage = json.meta?.last_page || 1;
        document.getElementById('reviews-empty')?.remove();
        if (items.length === 0 && reviewsPage === 1) {
            reviewsList.innerHTML =
                '<p class="text-gray-600 dark:text-gray-400" id="reviews-empty">No reviews yet.</p>';
        } else {
            items.forEach((r) => reviewsList.insertAdjacentHTML('beforeend', reviewCard(r)));
        }
        reviewsLoadMore?.classList.toggle('hidden', reviewsPage >= reviewsLastPage);
    }

    reviewsLoadMore?.addEventListener('click', () => {
        if (reviewsPage >= reviewsLastPage) return;
        reviewsPage += 1;
        fetchReviews(false);
    });

    reviewsSort?.addEventListener('change', () => fetchReviews(true));

    // Review modal
    const modal = document.getElementById('add-review-modal');
    const openReview = document.getElementById('open-review-modal');
    const closeReview = document.getElementById('add-review-modal-close');
    const backdrop = document.getElementById('add-review-modal-backdrop');
    const form = document.getElementById('add-review-form');
    const ratingInput = document.getElementById('review-rating-input');

    function openModal() {
        modal?.classList.remove('hidden');
    }
    function closeModal() {
        modal?.classList.add('hidden');
    }
    openReview?.addEventListener('click', () => {
        if (!requireAuth()) return;
        openModal();
    });
    document.getElementById('guest-review-btn')?.addEventListener('click', () => {
        requireAuth();
    });
    closeReview?.addEventListener('click', closeModal);
    backdrop?.addEventListener('click', closeModal);

    document.querySelectorAll('.review-star').forEach((btn) => {
        btn.addEventListener('click', () => {
            const val = Number(btn.dataset.rating);
            if (ratingInput) ratingInput.value = String(val);
            document.querySelectorAll('.review-star').forEach((s) => {
                s.classList.toggle('text-yellow-400', Number(s.dataset.rating) <= val);
                s.classList.toggle('text-gray-200', Number(s.dataset.rating) > val);
            });
        });
    });

    form?.addEventListener('submit', async (e) => {
        e.preventDefault();
        if (!requireAuth()) return;
        if (!ratingInput?.value) {
            toastError('Please select a rating.');
            return;
        }
        const fd = new FormData(form);
        try {
            const res = await fetch(form.dataset.action, {
                method: 'POST',
                headers: {
                    Accept: 'application/json',
                    'X-CSRF-TOKEN': csrfToken(),
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: fd,
            });
            if (res.status === 401) {
                requireAuth();
                return;
            }
            const json = await res.json();
            if (!res.ok) {
                toastError(json.message || 'Could not submit review');
                return;
            }
            closeModal();
            toastSuccess(json.message || 'Review submitted');
            fetchReviews(true);
            openReview?.classList.add('hidden');
        } catch (err) {
            console.error(err);
            toastError('Could not submit review');
        }
    });
};
