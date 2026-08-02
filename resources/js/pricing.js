// Pricing page interactions (estimator + FAQ) — soft-nav safe
window.initializePricingPage = function () {
    const root = document.getElementById('pricing-page');
    if (!root) return;

    // Allow re-bind after soft navigation (new DOM nodes)
    root.querySelectorAll('[data-bound]').forEach((el) => delete el.dataset.bound);

    const plans = (() => {
        try {
            const el = document.getElementById('pricing-plans-data');
            return el ? JSON.parse(el.textContent || '[]') : [];
        } catch {
            return [];
        }
    })();

    if (!plans.length) return;

    const adsSlider = document.getElementById('pricing-ads-slider');
    const adsCountEl = document.getElementById('pricing-ads-count');
    const totalEl = document.getElementById('pricing-estimate-total');
    const unitEl = document.getElementById('pricing-estimate-unit');
    const detailEl = document.getElementById('pricing-estimate-detail');
    const alsoEl = document.getElementById('pricing-estimate-also');
    const currencyButtons = root.querySelectorAll('[data-pricing-currency]');
    const planButtons = root.querySelectorAll('[data-pricing-plan-index]');

    let selectedPlanIndex = Math.max(0, plans.findIndex((p) => p.popular));
    if (selectedPlanIndex < 0) selectedPlanIndex = Math.min(1, plans.length - 1);
    let currency = 'SCR';
    let adsCount = adsSlider ? Number(adsSlider.value) || 5 : 5;

    function planColor(plan) {
        return plan.color || 'blue';
    }

    function accentClass(color) {
        if (color === 'amber') return 'text-amber-500';
        if (color === 'rose') return 'text-rose-600';
        return 'text-blue-600';
    }

    function btnActiveClass(color) {
        if (color === 'amber') return 'bg-amber-500 text-white shadow-sm';
        if (color === 'rose') return 'bg-rose-600 text-white shadow-sm';
        return 'bg-blue-600 text-white shadow-sm';
    }

    function ringClass(color) {
        if (color === 'amber') return 'ring-2 ring-amber-400 border-transparent bg-gray-50 dark:bg-gray-700/60';
        if (color === 'rose') return 'ring-2 ring-rose-500 border-transparent bg-gray-50 dark:bg-gray-700/60';
        return 'ring-2 ring-blue-500 border-transparent bg-gray-50 dark:bg-gray-700/60';
    }

    function updateSliderTrack() {
        if (!adsSlider) return;
        const plan = plans[selectedPlanIndex];
        const color = planColor(plan);
        const fill = color === 'amber' ? '#f59e0b' : color === 'rose' ? '#e11d48' : '#2563eb';
        const track = document.documentElement.classList.contains('dark') ? '#374151' : '#e5e7eb';
        const pct = ((adsCount - 1) / 29) * 100;
        adsSlider.style.background = `linear-gradient(to right, ${fill} ${pct}%, ${track} ${pct}%)`;
    }

    function updatePlanButtons() {
        planButtons.forEach((btn) => {
            const idx = Number(btn.dataset.pricingPlanIndex);
            const plan = plans[idx];
            const base = 'relative flex flex-col items-center gap-2 rounded-xl px-4 py-4 border transition-all duration-150';
            if (idx === selectedPlanIndex) {
                btn.className = `${base} ${ringClass(planColor(plan))}`;
            } else {
                btn.className = `${base} border-gray-200 dark:border-gray-700 hover:border-gray-300 dark:hover:border-gray-600 bg-white dark:bg-gray-800`;
            }
        });
    }

    function updateCurrencyButtons() {
        const plan = plans[selectedPlanIndex];
        currencyButtons.forEach((btn) => {
            const cur = btn.dataset.pricingCurrency;
            const active = cur === currency;
            btn.className = `px-4 py-2 text-sm font-semibold transition-colors ${
                active
                    ? btnActiveClass(planColor(plan))
                    : 'bg-white dark:bg-gray-800 text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700'
            }`;
        });
    }

    function updateEstimate() {
        const plan = plans[selectedPlanIndex];
        if (!plan) return;
        const scrTotal = Number(plan.scr_price) * adsCount;
        const acTotal = Number(plan.coins_price) * adsCount;
        const color = planColor(plan);

        if (adsCountEl) {
            adsCountEl.textContent = String(adsCount);
            adsCountEl.className = `text-3xl font-bold tabular-nums ${accentClass(color)}`;
        }
        if (totalEl) {
            totalEl.textContent = (currency === 'SCR' ? scrTotal : acTotal).toLocaleString();
            totalEl.className = `text-4xl font-extrabold tabular-nums leading-none ${accentClass(color)}`;
        }
        if (unitEl) unitEl.textContent = currency;
        if (detailEl) {
            const unitPrice = currency === 'SCR'
                ? `${Number(plan.scr_price).toLocaleString()} SCR`
                : `${Number(plan.coins_price)} AC`;
            detailEl.textContent = `${adsCount} × ${unitPrice} per advert · ${plan.name} plan`;
        }
        if (alsoEl) {
            alsoEl.textContent = currency === 'SCR'
                ? `Also ${acTotal.toLocaleString()} AC`
                : `Also ${scrTotal.toLocaleString()} SCR`;
        }
        updateSliderTrack();
        updatePlanButtons();
        updateCurrencyButtons();
    }

    if (adsSlider && !adsSlider.dataset.bound) {
        adsSlider.dataset.bound = 'true';
        adsSlider.addEventListener('input', () => {
            adsCount = Number(adsSlider.value) || 1;
            updateEstimate();
        });
    }

    planButtons.forEach((btn) => {
        if (btn.dataset.bound) return;
        btn.dataset.bound = 'true';
        btn.addEventListener('click', () => {
            selectedPlanIndex = Number(btn.dataset.pricingPlanIndex);
            updateEstimate();
        });
    });

    currencyButtons.forEach((btn) => {
        if (btn.dataset.bound) return;
        btn.dataset.bound = 'true';
        btn.addEventListener('click', () => {
            currency = btn.dataset.pricingCurrency || 'SCR';
            updateEstimate();
        });
    });

    root.querySelectorAll('[data-faq-index]').forEach((btn) => {
        if (btn.dataset.bound) return;
        btn.dataset.bound = 'true';
        btn.addEventListener('click', () => {
            const item = btn.closest('[data-faq-item]');
            if (!item) return;
            const open = item.dataset.open === 'true';
            root.querySelectorAll('[data-faq-item]').forEach((el) => {
                el.dataset.open = 'false';
                el.classList.remove('border-blue-200', 'dark:border-blue-700', 'bg-blue-50/60', 'dark:bg-blue-900/10');
                el.classList.add('border-gray-200', 'dark:border-gray-700', 'bg-white', 'dark:bg-gray-800');
                const panel = el.querySelector('[data-faq-panel]');
                const icon = el.querySelector('[data-faq-icon]');
                if (panel) panel.classList.add('max-h-0');
                if (panel) panel.classList.remove('max-h-96');
                if (icon) icon.classList.remove('rotate-180', 'text-blue-600', 'dark:text-blue-400');
                if (icon) icon.classList.add('text-gray-400');
            });
            if (!open) {
                item.dataset.open = 'true';
                item.classList.add('border-blue-200', 'dark:border-blue-700', 'bg-blue-50/60', 'dark:bg-blue-900/10');
                item.classList.remove('border-gray-200', 'dark:border-gray-700', 'bg-white', 'dark:bg-gray-800');
                const panel = item.querySelector('[data-faq-panel]');
                const icon = item.querySelector('[data-faq-icon]');
                if (panel) {
                    panel.classList.remove('max-h-0');
                    panel.classList.add('max-h-96');
                }
                if (icon) {
                    icon.classList.add('rotate-180', 'text-blue-600', 'dark:text-blue-400');
                    icon.classList.remove('text-gray-400');
                }
            }
        });
    });

    updateEstimate();
};
