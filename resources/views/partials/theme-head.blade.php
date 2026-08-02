{{-- FOUC-safe theme bootstrap — works without Vite; persists across all layouts --}}
<script>
(function () {
    var STORAGE_KEY = 'jobhub-theme';

    function getStored() {
        try { return localStorage.getItem(STORAGE_KEY); } catch (e) { return null; }
    }

    function resolveTheme() {
        var stored = getStored();
        if (stored === 'dark' || stored === 'light') return stored;
        if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) return 'dark';
        return 'light';
    }

    function updateToggleIcons(isDark) {
        document.querySelectorAll('[data-theme-toggle]').forEach(function (btn) {
            var moon = btn.querySelector('[data-theme-icon="moon"]');
            var sun = btn.querySelector('[data-theme-icon="sun"]');
            if (moon) {
                moon.classList.toggle('hidden', isDark);
                moon.style.display = isDark ? 'none' : '';
            }
            if (sun) {
                sun.classList.toggle('hidden', !isDark);
                sun.style.display = isDark ? '' : 'none';
            }
            btn.setAttribute('aria-label', isDark ? 'Switch to light mode' : 'Switch to dark mode');
            btn.setAttribute('title', isDark ? 'Light mode' : 'Dark mode');
            btn.setAttribute('aria-pressed', isDark ? 'true' : 'false');
        });
    }

    function applyTheme(theme) {
        var isDark = theme === 'dark';
        var root = document.documentElement;
        root.classList.toggle('dark', isDark);
        if (document.body) {
            document.body.classList.toggle('dark', isDark);
        }
        root.style.colorScheme = isDark ? 'dark' : 'light';
        updateToggleIcons(isDark);
        try {
            window.dispatchEvent(new CustomEvent('jobhub:theme-changed', { detail: { theme: theme, isDark: isDark } }));
        } catch (e) {}
    }

    function setTheme(theme) {
        try { localStorage.setItem(STORAGE_KEY, theme); } catch (e) {}
        applyTheme(theme);
    }

    var toggling = false;
    function toggleTheme() {
        if (toggling) return document.documentElement.classList.contains('dark') ? 'dark' : 'light';
        toggling = true;
        var next = document.documentElement.classList.contains('dark') ? 'light' : 'dark';
        setTheme(next);
        setTimeout(function () { toggling = false; }, 0);
        return next;
    }

    // Apply before paint
    applyTheme(resolveTheme());

    window.__jobhubTheme = {
        apply: applyTheme,
        set: setTheme,
        toggle: toggleTheme,
        resolve: resolveTheme,
        syncIcons: function () {
            updateToggleIcons(document.documentElement.classList.contains('dark'));
        }
    };

    // Capture-phase delegation so toggles work on every page / soft-nav re-render
    document.addEventListener('click', function (e) {
        var btn = e.target && e.target.closest ? e.target.closest('[data-theme-toggle]') : null;
        if (!btn) return;
        e.preventDefault();
        e.stopPropagation();
        toggleTheme();
    }, true);

    function onReady(fn) {
        if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', fn);
        else fn();
    }

    onReady(function () {
        applyTheme(resolveTheme());
    });

    document.addEventListener('livewire:navigated', function () {
        applyTheme(resolveTheme());
    });
})();
</script>
<link rel="preconnect" href="https://fonts.bunny.net" crossorigin>
<link rel="preload" as="style" href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" onload="this.onload=null;this.rel='stylesheet'">
<noscript><link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" /></noscript>
<style>
/* Ensure theme toggle icons swap reliably even if utility CSS loads late */
[data-theme-toggle] [data-theme-icon] { width: 1.25rem; height: 1.25rem; display: block; }
html.dark [data-theme-toggle] [data-theme-icon="moon"],
body.dark [data-theme-toggle] [data-theme-icon="moon"] { display: none !important; }
html:not(.dark) [data-theme-toggle] [data-theme-icon="sun"],
body:not(.dark) [data-theme-toggle] [data-theme-icon="sun"] { display: none !important; }
html.dark [data-theme-toggle] [data-theme-icon="sun"],
body.dark [data-theme-toggle] [data-theme-icon="sun"] { display: block !important; }
html:not(.dark) [data-theme-toggle] [data-theme-icon="moon"],
body:not(.dark) [data-theme-toggle] [data-theme-icon="moon"] { display: block !important; }
</style>
