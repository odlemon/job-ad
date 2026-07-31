// Import page functionality (must be loaded globally for wire:navigate)
import './job-detail.js';
import './profile.js';
import './applications.js';
import './notifications.js';
import './auth-modal.js';

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
            <div id="${toastId}-progress" class="h-full bg-white bg-opacity-30 transition-all ease-linear" style="width: 100%;"></div>
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

// Custom confirm dialog (replaces native confirm for our flows)
window.showConfirmDialog = function(message, options = {}) {
    const {
        title = 'Are you sure?',
        confirmText = 'Confirm',
        cancelText = 'Cancel',
    } = options || {};

    return new Promise((resolve) => {
        // Remove any existing dialog
        const existing = document.getElementById('confirm-dialog-overlay');
        if (existing) existing.remove();

        const overlay = document.createElement('div');
        overlay.id = 'confirm-dialog-overlay';
        overlay.className = 'fixed inset-0 z-50 flex items-center justify-center';
        overlay.style.cssText = 'background: rgba(0,0,0,0.4); backdrop-filter: blur(2px);';

        overlay.innerHTML = `
            <div class="bg-white rounded-xl shadow-xl max-w-sm w-full mx-4 p-6">
                <h3 class="text-base font-semibold text-gray-900 mb-2">${title}</h3>
                <p class="text-sm text-gray-700 mb-5">${message}</p>
                <div class="flex justify-end gap-3">
                    <button type="button" class="confirm-cancel px-4 py-2 text-sm font-medium text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50">
                        ${cancelText}
                    </button>
                    <button type="button" class="confirm-ok px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700">
                        ${confirmText}
                    </button>
                </div>
            </div>
        `;

        function cleanup(result) {
            overlay.remove();
            document.removeEventListener('keydown', handleKeydown);
            resolve(result);
        }

        function handleKeydown(e) {
            if (e.key === 'Escape') {
                cleanup(false);
            }
        }

        overlay.addEventListener('click', (e) => {
            if (e.target === overlay) {
                cleanup(false);
            }
        });

        const okBtn = overlay.querySelector('.confirm-ok');
        const cancelBtn = overlay.querySelector('.confirm-cancel');
        if (okBtn) okBtn.addEventListener('click', () => cleanup(true));
        if (cancelBtn) cancelBtn.addEventListener('click', () => cleanup(false));

        document.body.appendChild(overlay);
        document.addEventListener('keydown', handleKeydown);
    });
};

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
