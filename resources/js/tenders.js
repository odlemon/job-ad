// Public tenders list — soft-nav safe (Bolt search-tenders UI)
const PER_PAGE = 6;

function daysFromNow(isoDate) {
    if (!isoDate || isoDate === '9999-12-31') return 9999;
    const target = new Date(isoDate + 'T00:00:00');
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    return Math.round((target - today) / 86400000);
}

window.initializeTendersPage = function () {
    const root = document.getElementById('tenders-page');
    if (!root) return;

    const grid = document.getElementById('tenders-grid');
    const countEl = document.getElementById('tender-count');
    const emptyEl = document.getElementById('tenders-empty');
    const paginationEl = document.getElementById('tenders-pagination');
    if (!grid) return;

    const cards = Array.from(grid.querySelectorAll('.tender-card'));
    const searchInput = document.getElementById('tender-search');
    const searchBtn = document.getElementById('tender-search-btn');
    const filterCategory = document.getElementById('filter-category');
    const filterSector = document.getElementById('filter-sector');
    const filterType = document.getElementById('filter-type');
    const filterBudget = document.getElementById('filter-budget');
    const filterDeadline = document.getElementById('filter-deadline');
    const filterLocation = document.getElementById('filter-location');
    const filterSort = document.getElementById('filter-sort');

    let page = 1;

    function q() {
        return (searchInput?.value || '').toLowerCase().trim();
    }

    function matches(card) {
        const query = q();
        if (query) {
            const hay = [
                card.dataset.title || '',
                card.dataset.ref || '',
                (card.dataset.category || '').toLowerCase(),
            ].join(' ');
            if (!hay.includes(query)) return false;
        }

        if (filterCategory?.value && card.dataset.category !== filterCategory.value) return false;
        if (filterSector?.value && card.dataset.sector !== filterSector.value) return false;
        if (filterType?.value && card.dataset.type !== filterType.value) return false;
        if (filterBudget?.value && card.dataset.budgetBand !== filterBudget.value) return false;
        if (filterLocation?.value && !(card.dataset.location || '').includes(filterLocation.value)) return false;

        if (filterDeadline?.value) {
            const days = daysFromNow(card.dataset.deadlineSort);
            if (filterDeadline.value === '7days' && !(days >= 0 && days <= 7)) return false;
            if (filterDeadline.value === '30days' && !(days >= 0 && days <= 30)) return false;
            if (filterDeadline.value === '3months' && !(days >= 0 && days <= 92)) return false;
        }

        return true;
    }

    function sortCards(list) {
        const sortVal = filterSort?.value || 'newest';
        return [...list].sort((a, b) => {
            if (sortVal === 'closing') {
                return (a.dataset.deadlineSort || '').localeCompare(b.dataset.deadlineSort || '');
            }
            if (sortVal === 'highest') {
                return Number(b.dataset.budgetMax || 0) - Number(a.dataset.budgetMax || 0);
            }
            if (sortVal === 'az') {
                return (a.dataset.title || '').localeCompare(b.dataset.title || '');
            }
            return (b.dataset.published || b.dataset.created || '').localeCompare(
                a.dataset.published || a.dataset.created || ''
            );
        });
    }

    function renderPagination(totalPages) {
        if (!paginationEl) return;
        if (totalPages <= 1) {
            paginationEl.classList.add('hidden');
            paginationEl.innerHTML = '';
            return;
        }

        paginationEl.classList.remove('hidden');
        let html = `
            <button type="button" data-page-action="prev"
                class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                ${page === 1 ? 'disabled' : ''}>Previous</button>
        `;
        for (let i = 1; i <= totalPages; i++) {
            html += `
                <button type="button" data-page="${i}"
                    class="w-10 h-10 rounded-md font-medium transition-colors ${
                        page === i
                            ? 'bg-gradient-to-r from-blue-600 to-teal-500 text-white'
                            : 'border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700'
                    }">${i}</button>
            `;
        }
        html += `
            <button type="button" data-page-action="next"
                class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                ${page === totalPages ? 'disabled' : ''}>Next</button>
        `;
        paginationEl.innerHTML = html;

        paginationEl.querySelectorAll('[data-page]').forEach((btn) => {
            btn.addEventListener('click', () => {
                page = Number(btn.dataset.page);
                run();
            });
        });
        paginationEl.querySelector('[data-page-action="prev"]')?.addEventListener('click', () => {
            page = Math.max(1, page - 1);
            run();
        });
        paginationEl.querySelector('[data-page-action="next"]')?.addEventListener('click', () => {
            page = Math.min(totalPages, page + 1);
            run();
        });
    }

    function run() {
        const matched = sortCards(cards.filter(matches));
        const total = matched.length;
        const totalPages = Math.max(1, Math.ceil(total / PER_PAGE));
        if (page > totalPages) page = totalPages;

        const start = (page - 1) * PER_PAGE;
        const end = start + PER_PAGE;
        const pageItems = matched.slice(start, end);

        cards.forEach((c) => {
            c.style.display = 'none';
        });
        pageItems.forEach((c) => {
            grid.appendChild(c);
            c.style.display = '';
        });

        if (countEl) {
            if (total === 0) {
                countEl.textContent = 'Showing 0 of 0 tenders';
            } else {
                countEl.textContent = `Showing ${start + 1}-${Math.min(end, total)} of ${total} tenders`;
            }
        }

        if (emptyEl) {
            emptyEl.classList.toggle('hidden', total > 0);
        }

        renderPagination(totalPages);
    }

    const bindOnce = (el, event, fn) => {
        if (!el || el.dataset.tendersBound === 'true') return;
        el.dataset.tendersBound = 'true';
        el.addEventListener(event, fn);
    };

    bindOnce(searchInput, 'input', () => {
        page = 1;
        run();
    });
    bindOnce(searchInput, 'keydown', (e) => {
        if (e.key === 'Enter') {
            page = 1;
            run();
        }
    });
    bindOnce(searchBtn, 'click', () => {
        page = 1;
        run();
    });

    [filterCategory, filterSector, filterType, filterBudget, filterDeadline, filterLocation, filterSort].forEach((el) => {
        bindOnce(el, 'change', () => {
            page = 1;
            run();
        });
    });

    run();
};

const TAB_ACTIVE =
    'text-blue-600 dark:text-cyan-400 border-b-2 border-blue-600 dark:border-cyan-400 bg-blue-50/50 dark:bg-blue-900/10';
const TAB_INACTIVE =
    'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white border-b-2 border-transparent';

window.initializeTenderDetailPage = function () {
    const root = document.getElementById('tender-detail-page');
    if (!root) return;

    // Soft-nav can remount the page; reset bind flags on this root's controls
    if (root.dataset.detailInit === 'true' && root.querySelector('.tender-tab[data-bound="true"]')) {
        // Still re-apply active tab styles in case DOM was restored
    }
    root.dataset.detailInit = 'true';

    let urls = [];
    try {
        urls = JSON.parse(root.dataset.attachmentUrls || '[]');
    } catch (_) {
        urls = [];
    }

    const tabs = Array.from(root.querySelectorAll('.tender-tab'));
    const panels = Array.from(root.querySelectorAll('.tender-panel'));

    function activateTab(key) {
        tabs.forEach((btn) => {
            const active = btn.getAttribute('data-tab') === key;
            btn.className = `tender-tab px-6 py-4 font-medium transition-colors whitespace-nowrap ${
                active ? TAB_ACTIVE : TAB_INACTIVE
            }`;
            if (active) btn.setAttribute('aria-selected', 'true');
            else btn.removeAttribute('aria-selected');
        });
        panels.forEach((panel) => {
            panel.classList.toggle('hidden', panel.id !== `tender-panel-${key}`);
        });
    }

    tabs.forEach((btn) => {
        if (btn.dataset.bound === 'true') return;
        btn.dataset.bound = 'true';
        btn.addEventListener('click', () => activateTab(btn.getAttribute('data-tab')));
    });

    activateTab('overview');

    function downloadAll() {
        if (!urls.length) {
            alert('No documents available for download yet.');
            return;
        }
        urls.forEach((url) => {
            const a = document.createElement('a');
            a.href = url;
            a.target = '_blank';
            a.rel = 'noopener';
            a.download = '';
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
        });
    }

    root.querySelectorAll('[data-download-all]').forEach((btn) => {
        if (btn.dataset.bound === 'true') return;
        btn.dataset.bound = 'true';
        btn.addEventListener('click', downloadAll);
    });

    const modal = document.getElementById('tender-clarify-modal');
    const messageEl = document.getElementById('clarify-message');

    function openModal() {
        if (!modal) return;
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        messageEl?.focus();
    }

    function closeModal() {
        if (!modal) return;
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    root.querySelectorAll('[data-clarify-open]').forEach((btn) => {
        if (btn.dataset.bound === 'true') return;
        btn.dataset.bound = 'true';
        btn.addEventListener('click', openModal);
    });

    root.querySelectorAll('[data-clarify-close]').forEach((btn) => {
        if (btn.dataset.bound === 'true') return;
        btn.dataset.bound = 'true';
        btn.addEventListener('click', closeModal);
    });

    const submitBtn = root.querySelector('[data-clarify-submit]');
    if (submitBtn && submitBtn.dataset.bound !== 'true') {
        submitBtn.dataset.bound = 'true';
        submitBtn.addEventListener('click', () => {
            const msg = (messageEl?.value || '').trim();
            if (!msg) {
                messageEl?.focus();
                return;
            }
            const ref = root.dataset.tenderRef || root.dataset.tenderTitle || 'Tender';
            const subject = encodeURIComponent(`Clarification request: ${ref}`);
            const body = encodeURIComponent(msg);
            window.location.href = `mailto:?subject=${subject}&body=${body}`;
            closeModal();
        });
    }
};
