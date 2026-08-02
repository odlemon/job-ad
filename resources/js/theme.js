// Theme sync helper — primary toggle logic lives in partials/theme-head.blade.php
// so dark/light works even if this Vite bundle fails to load.
(function () {
    function sync() {
        if (window.__jobhubTheme && typeof window.__jobhubTheme.apply === 'function') {
            window.__jobhubTheme.apply(window.__jobhubTheme.resolve());
            if (typeof window.__jobhubTheme.syncIcons === 'function') {
                window.__jobhubTheme.syncIcons();
            }
            return;
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', sync);
    } else {
        sync();
    }

    document.addEventListener('livewire:navigated', sync);
    window.addEventListener('pageshow', sync);
})();
