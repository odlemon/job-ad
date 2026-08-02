// Import page functionality (must be loaded globally for wire:navigate)
import './theme.js';
import './job-detail.js';
import './job-search.js';
import './tenders.js';
import './companies.js';
import './profile.js';
import './applications.js';
import './notifications.js';
import './auth-modal.js';
import './pricing.js';

// Global loading utilities
window.showLoading = function(element) {
    if (typeof element === 'string') {
        element = document.querySelector(element);
    }
    if (element) {
        element.classList.add('opacity-50', 'pointer-events-none');
        const spinner = document.createElement('div');
        spinner.className = 'spinner mx-auto';
        spinner.id = 'temp-spinner';
        element.appendChild(spinner);
    }
};

window.hideLoading = function(element) {
    if (typeof element === 'string') {
        element = document.querySelector(element);
    }
    if (element) {
        element.classList.remove('opacity-50', 'pointer-events-none');
        const spinner = element.querySelector('#temp-spinner');
        if (spinner) spinner.remove();
    }
};

// Global fetch with loading
window.fetchWithLoading = async function(url, options = {}, loadingElement = null) {
    if (loadingElement) {
        showLoading(loadingElement);
    }
    
    try {
        const response = await fetch(url, {
            ...options,
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                ...options.headers
            }
        });
        
        const data = await response.json();
        return { response, data };
    } catch (error) {
        console.error('Fetch error:', error);
        throw error;
    } finally {
        if (loadingElement) {
            hideLoading(loadingElement);
        }
    }
};

// Toast notifications - Enhanced reusable component
window.showToast = function(message, type = 'success', duration = 4000) {
    // Create toast container if it doesn't exist
    let toastContainer = document.getElementById('toast-container');
    if (!toastContainer) {
        toastContainer = document.createElement('div');
        toastContainer.id = 'toast-container';
        toastContainer.className = 'fixed top-4 right-4 z-50 space-y-3 pointer-events-none';
        document.body.appendChild(toastContainer);
    }

    // Create toast element
    const toast = document.createElement('div');
    const toastId = 'toast-' + Date.now() + '-' + Math.random().toString(36).substr(2, 9);
    toast.id = toastId;
    
    // Type configurations
    const typeConfig = {
        success: {
            bg: 'bg-green-500',
            icon: `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>`,
            border: 'border-green-600'
        },
        error: {
            bg: 'bg-red-500',
            icon: `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>`,
            border: 'border-red-600'
        },
        warning: {
            bg: 'bg-yellow-500',
            icon: `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
            </svg>`,
            border: 'border-yellow-600'
        },
        info: {
            bg: 'bg-blue-500',
            icon: `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>`,
            border: 'border-blue-600'
        }
    };

    const config = typeConfig[type] || typeConfig.success;
    
    toast.className = `${config.bg} ${config.border} border-l-4 text-white px-6 py-4 rounded-lg shadow-xl pointer-events-auto flex items-start space-x-3 min-w-[300px] max-w-md transform transition-all duration-300 ease-out translate-x-full opacity-0`;
    
    toast.innerHTML = `
        <div class="flex-shrink-0 mt-0.5">
            ${config.icon}
        </div>
        <div class="flex-1">
            <p class="text-sm font-medium">${message}</p>
        </div>
        <button onclick="document.getElementById('${toastId}').remove()" class="flex-shrink-0 text-white hover:text-gray-200 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
        <div class="absolute bottom-0 left-0 right-0 h-1 bg-black bg-opacity-20 rounded-b-lg overflow-hidden">
            <div id="${toastId}-progress" class="h-full bg-white dark:bg-gray-800 bg-opacity-30 transition-all ease-linear" style="width: 100%;"></div>
        </div>
    `;
    
    // Add to container
    toastContainer.appendChild(toast);
    
    // Animate in
    setTimeout(() => {
        toast.classList.remove('translate-x-full', 'opacity-0');
        toast.classList.add('translate-x-0', 'opacity-100');
    }, 10);
    
    // Progress bar animation
    const progressBar = toast.querySelector(`#${toastId}-progress`);
    if (progressBar) {
        setTimeout(() => {
            progressBar.style.width = '0%';
        }, 50);
    }
    
    // Auto dismiss with pause on hover
    let dismissTimeout;
    let startTime = Date.now();
    let remainingTime = duration;
    
    function startDismissTimer() {
        clearTimeout(dismissTimeout);
        dismissTimeout = setTimeout(() => {
            dismissToast(toastId);
        }, remainingTime);
        
        if (progressBar) {
            const progressPercent = (remainingTime / duration) * 100;
            progressBar.style.transition = `all ${remainingTime}ms linear`;
            progressBar.style.width = '0%';
        }
    }
    
    // Pause on hover
    toast.addEventListener('mouseenter', () => {
        const elapsed = Date.now() - startTime;
        remainingTime = remainingTime - elapsed;
        clearTimeout(dismissTimeout);
        if (progressBar) {
            progressBar.style.transition = 'none';
            const currentPercent = (remainingTime / duration) * 100;
            progressBar.style.width = currentPercent + '%';
        }
    });
    
    toast.addEventListener('mouseleave', () => {
        startTime = Date.now();
        startDismissTimer();
    });
    
    // Start the timer
    startDismissTimer();
    
    return toastId;
};

// Helper function to dismiss toast
function dismissToast(toastId) {
    const toast = document.getElementById(toastId);
    if (toast) {
        toast.classList.add('translate-x-full', 'opacity-0');
        setTimeout(() => {
            toast.remove();
            // Remove container if empty
            const container = document.getElementById('toast-container');
            if (container && container.children.length === 0) {
                container.remove();
            }
        }, 300);
    }
}

// Convenience functions
window.showSuccessToast = function(message, duration) {
    return window.showToast(message, 'success', duration);
};

window.showErrorToast = function(message, duration) {
    return window.showToast(message, 'error', duration);
};

window.showWarningToast = function(message, duration) {
    return window.showToast(message, 'warning', duration);
};

window.showInfoToast = function(message, duration) {
    return window.showToast(message, 'info', duration);
};

// Custom confirm dialog (replaces native browser confirm across the platform)
window.showConfirmDialog = function(message, options = {}) {
    const opts = options || {};
    const title = opts.title || 'Are you sure?';
    const confirmText = opts.confirmText || 'Confirm';
    const cancelText = opts.cancelText || 'Cancel';
    const dangerHint = `${title} ${confirmText} ${message}`;
    const danger = opts.danger === true || (
        opts.danger !== false && /delete|remove|withdraw|disable|permanent|cannot be undone/i.test(dangerHint)
    );

    const esc = (s) => String(s ?? '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;');

    return new Promise((resolve) => {
        const existing = document.getElementById('confirm-dialog-overlay');
        if (existing) existing.remove();

        const overlay = document.createElement('div');
        overlay.id = 'confirm-dialog-overlay';
        overlay.setAttribute('role', 'dialog');
        overlay.setAttribute('aria-modal', 'true');
        overlay.setAttribute('aria-labelledby', 'confirm-dialog-title');
        overlay.style.cssText = 'position:fixed;inset:0;z-index:9999;display:flex;align-items:center;justify-content:center;padding:1rem;background:rgba(15,23,42,.55);backdrop-filter:blur(4px);-webkit-backdrop-filter:blur(4px);opacity:0;transition:opacity .15s ease;';

        const isDark = document.documentElement.classList.contains('dark') || document.body.classList.contains('dark');
        const panelBg = isDark ? '#1f2937' : '#ffffff';
        const titleColor = isDark ? '#f9fafb' : '#111827';
        const textColor = isDark ? '#d1d5db' : '#4b5563';
        const borderColor = isDark ? '#374151' : '#e5e7eb';
        const cancelBg = isDark ? '#111827' : '#ffffff';
        const cancelHover = isDark ? '#374151' : '#f9fafb';
        const cancelTextColor = isDark ? '#e5e7eb' : '#374151';
        const iconBg = danger ? (isDark ? 'rgba(127,29,29,.35)' : '#fee2e2') : (isDark ? 'rgba(30,58,138,.35)' : '#dbeafe');
        const iconColor = danger ? (isDark ? '#fca5a5' : '#dc2626') : (isDark ? '#93c5fd' : '#2563eb');
        const okBg = danger
            ? 'linear-gradient(to right,#dc2626,#f97316)'
            : 'linear-gradient(to right,#2563eb,#06b6d4)';

        const iconSvg = danger
            ? '<svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>'
            : '<svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>';

        overlay.innerHTML = `
            <div class="confirm-dialog-panel" style="background:${panelBg};border:1px solid ${borderColor};border-radius:1rem;box-shadow:0 25px 50px -12px rgba(0,0,0,.35);width:100%;max-width:26rem;padding:1.35rem 1.35rem 1.2rem;transform:translateY(8px) scale(.98);opacity:0;transition:transform .18s ease,opacity .18s ease;">
                <div style="display:flex;gap:.9rem;align-items:flex-start;">
                    <div style="flex-shrink:0;width:2.75rem;height:2.75rem;border-radius:.85rem;display:flex;align-items:center;justify-content:center;background:${iconBg};color:${iconColor};">${iconSvg}</div>
                    <div style="min-width:0;flex:1;">
                        <h3 id="confirm-dialog-title" style="margin:0 0 .4rem;font-size:1.05rem;font-weight:700;color:${titleColor};line-height:1.3;">${esc(title)}</h3>
                        <p style="margin:0;font-size:.9rem;color:${textColor};line-height:1.5;">${esc(message)}</p>
                    </div>
                </div>
                <div style="display:flex;justify-content:flex-end;gap:.65rem;margin-top:1.25rem;">
                    <button type="button" class="confirm-cancel" style="padding:.55rem 1rem;border-radius:.65rem;border:1px solid ${borderColor};background:${cancelBg};color:${cancelTextColor};font-size:.875rem;font-weight:500;cursor:pointer;">${esc(cancelText)}</button>
                    <button type="button" class="confirm-ok" style="padding:.55rem 1rem;border-radius:.65rem;border:0;background:${okBg};color:#fff;font-size:.875rem;font-weight:600;cursor:pointer;box-shadow:0 8px 16px -8px rgba(37,99,235,.55);">${esc(confirmText)}</button>
                </div>
            </div>
        `;

        function cleanup(result) {
            const panel = overlay.querySelector('.confirm-dialog-panel');
            overlay.style.opacity = '0';
            if (panel) {
                panel.style.opacity = '0';
                panel.style.transform = 'translateY(8px) scale(.98)';
            }
            setTimeout(() => {
                overlay.remove();
                document.removeEventListener('keydown', handleKeydown);
                resolve(result);
            }, 140);
        }

        function handleKeydown(e) {
            if (e.key === 'Escape') cleanup(false);
            if (e.key === 'Enter') cleanup(true);
        }

        overlay.addEventListener('click', (e) => {
            if (e.target === overlay) cleanup(false);
        });

        const okBtn = overlay.querySelector('.confirm-ok');
        const cancelBtn = overlay.querySelector('.confirm-cancel');
        if (okBtn) {
            okBtn.addEventListener('click', () => cleanup(true));
            okBtn.addEventListener('mouseenter', () => { okBtn.style.filter = 'brightness(1.05)'; });
            okBtn.addEventListener('mouseleave', () => { okBtn.style.filter = ''; });
        }
        if (cancelBtn) {
            cancelBtn.addEventListener('click', () => cleanup(false));
            cancelBtn.addEventListener('mouseenter', () => { cancelBtn.style.background = cancelHover; });
            cancelBtn.addEventListener('mouseleave', () => { cancelBtn.style.background = cancelBg; });
        }

        document.body.appendChild(overlay);
        document.addEventListener('keydown', handleKeydown);
        requestAnimationFrame(() => {
            overlay.style.opacity = '1';
            const panel = overlay.querySelector('.confirm-dialog-panel');
            if (panel) {
                panel.style.opacity = '1';
                panel.style.transform = 'translateY(0) scale(1)';
            }
            (okBtn || cancelBtn)?.focus();
        });
    });
};

// Friendly aliases used across Blade/JS
window.appConfirm = window.showConfirmDialog;
window.confirmAction = window.showConfirmDialog;

// Loading overlay
window.showLoadingOverlay = function() {
    const overlay = document.createElement('div');
    overlay.id = 'global-loading-overlay';
    overlay.className = 'loading-overlay';
    overlay.innerHTML = '<div class="spinner-lg"></div>';
    document.body.appendChild(overlay);
};

window.hideLoadingOverlay = function() {
    const overlay = document.getElementById('global-loading-overlay');
    if (overlay) {
        overlay.style.opacity = '0';
        overlay.style.transition = 'opacity 0.3s';
        setTimeout(() => overlay.remove(), 300);
    }
};

// Livewire navigation wrapper
window.navigateTo = function(url) {
    if (typeof Livewire !== 'undefined' && Livewire.navigate) {
        Livewire.navigate(url);
    } else {
        window.location.href = url;
    }
};

// Global handler for job detail pages (works with wire:navigate)
// Optimized version with minimal retry logic and no MutationObserver overhead
(function() {
    let isHandling = false;
    
    function checkAndLoadJobDetail() {
        // Prevent duplicate execution
        if (isHandling) return;
        
        if (!window.location.pathname.match(/\/jobs\/\d+$/)) {
            return;
        }
        
        const jobDetailElement = document.getElementById('job-detail');
        if (!jobDetailElement) {
            return;
        }
        
        const needsLoading = jobDetailElement.getAttribute('data-auto-load') === 'true';
        const hasRealContent = jobDetailElement.innerHTML.includes('Job Description') ||
                            jobDetailElement.innerHTML.includes('Apply for this job');
        const isSkeleton = jobDetailElement.innerHTML.includes('animate-pulse');
        
        // If function is available, use it
        if (typeof window.loadJobDetail === 'function') {
            if (isSkeleton || !hasRealContent || needsLoading) {
                isHandling = true;
                jobDetailElement.removeAttribute('data-auto-load');
                window.loadJobDetail().finally(() => {
                    isHandling = false;
                });
                return true;
            }
        }
        return false;
    }
    
    // Check on initial load
    function init() {
        if (checkAndLoadJobDetail()) {
            return;
        }
        // If function not ready, retry once after a short delay
        if (typeof window.loadJobDetail !== 'function') {
            setTimeout(() => {
                if (typeof window.loadJobDetail === 'function') {
                    checkAndLoadJobDetail();
                }
            }, 100);
        }
    }
    
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
    
    // Listen for Livewire navigation - simplified
    if (typeof Livewire !== 'undefined') {
        document.addEventListener('livewire:navigated', function() {
            // Reset handling flag on navigation
            isHandling = false;
            // Try immediately, then once more if needed
            if (!checkAndLoadJobDetail()) {
                setTimeout(() => {
                    if (typeof window.loadJobDetail === 'function') {
                        checkAndLoadJobDetail();
                    }
                }, 100);
            }
        });
    }
    
    // Handle popstate (browser back/forward)
    window.addEventListener('popstate', function() {
        isHandling = false;
        setTimeout(checkAndLoadJobDetail, 50);
    });
})();

// Profile page handler is in profile.js (imported above)

// Jobs search page — soft-nav friendly (inline Blade scripts do not re-run on wire:navigate)
(function () {
    let isHandling = false;

    function isJobsIndexPath() {
        const path = window.location.pathname.replace(/\/+$/, '') || '/';
        return path === '/jobs';
    }

    function checkAndLoadJobsPage() {
        if (isHandling) return false;
        if (!isJobsIndexPath()) return false;

        const container = document.getElementById('jobs-container');
        if (!container) return false;
        if (typeof window.initializeJobsPage !== 'function') return false;

        isHandling = true;
        try {
            window.initializeJobsPage();
        } finally {
            isHandling = false;
        }
        return true;
    }

    function init() {
        if (checkAndLoadJobsPage()) return;
        if (typeof window.initializeJobsPage !== 'function') {
            setTimeout(() => {
                if (typeof window.initializeJobsPage === 'function') {
                    checkAndLoadJobsPage();
                }
            }, 100);
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

    document.addEventListener('livewire:navigated', function () {
        isHandling = false;
        if (!checkAndLoadJobsPage()) {
            setTimeout(() => {
                if (typeof window.initializeJobsPage === 'function') {
                    checkAndLoadJobsPage();
                }
            }, 100);
        }
    });

    window.addEventListener('popstate', function () {
        isHandling = false;
        setTimeout(checkAndLoadJobsPage, 50);
    });
})();

// Tenders list + detail (SSR, soft-nav safe)
(function () {
    function path() {
        return window.location.pathname.replace(/\/+$/, '') || '/';
    }

    function checkAndLoadTendersPage() {
        if (path() !== '/tenders') return false;
        if (!document.getElementById('tenders-page')) return false;
        if (typeof window.initializeTendersPage !== 'function') return false;
        window.initializeTendersPage();
        return true;
    }

    function checkAndLoadTenderDetailPage() {
        if (!/^\/tenders\/[^/]+$/.test(path())) return false;
        if (!document.getElementById('tender-detail-page')) return false;
        if (typeof window.initializeTenderDetailPage !== 'function') return false;
        window.initializeTenderDetailPage();
        return true;
    }

    function init() {
        if (checkAndLoadTendersPage() || checkAndLoadTenderDetailPage()) return;
        setTimeout(() => {
            checkAndLoadTendersPage();
            checkAndLoadTenderDetailPage();
        }, 100);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

    document.addEventListener('livewire:navigated', function () {
        setTimeout(() => {
            checkAndLoadTendersPage();
            checkAndLoadTenderDetailPage();
        }, 50);
    });

    window.addEventListener('popstate', function () {
        setTimeout(() => {
            checkAndLoadTendersPage();
            checkAndLoadTenderDetailPage();
        }, 50);
    });
})();

// Companies list + detail (SSR + lean public APIs)
(function () {
    function path() {
        return window.location.pathname.replace(/\/+$/, '') || '/';
    }

    function checkAndLoadCompaniesPage() {
        if (path() !== '/companies') return false;
        if (!document.getElementById('companies-page')) return false;
        if (typeof window.initializeCompaniesPage !== 'function') return false;
        window.initializeCompaniesPage();
        return true;
    }

    function checkAndLoadCompanyDetailPage() {
        if (!/^\/companies\/[^/]+$/.test(path())) return false;
        if (!document.getElementById('company-detail-page')) return false;
        if (typeof window.initializeCompanyDetailPage !== 'function') return false;
        window.initializeCompanyDetailPage();
        return true;
    }

    function init() {
        if (checkAndLoadCompaniesPage() || checkAndLoadCompanyDetailPage()) return;
        setTimeout(() => {
            checkAndLoadCompaniesPage();
            checkAndLoadCompanyDetailPage();
        }, 100);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

    document.addEventListener('livewire:navigated', function () {
        setTimeout(() => {
            checkAndLoadCompaniesPage();
            checkAndLoadCompanyDetailPage();
        }, 50);
    });

    window.addEventListener('popstate', function () {
        setTimeout(() => {
            checkAndLoadCompaniesPage();
            checkAndLoadCompanyDetailPage();
        }, 50);
    });
})();

// Pricing page — estimator/FAQ (SSR HTML, JS for interactions)
(function () {
    function isPricingPath() {
        const path = window.location.pathname.replace(/\/+$/, '') || '/';
        return path === '/pricing';
    }

    function checkAndLoadPricingPage() {
        if (!isPricingPath()) return false;
        if (!document.getElementById('pricing-page')) return false;
        if (typeof window.initializePricingPage !== 'function') return false;
        window.initializePricingPage();
        return true;
    }

    function init() {
        if (checkAndLoadPricingPage()) return;
        setTimeout(checkAndLoadPricingPage, 100);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

    document.addEventListener('livewire:navigated', function () {
        setTimeout(checkAndLoadPricingPage, 50);
    });

    window.addEventListener('popstate', function () {
        setTimeout(checkAndLoadPricingPage, 50);
    });
})();
