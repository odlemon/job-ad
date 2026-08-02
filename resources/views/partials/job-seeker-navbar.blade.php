@php
    $user = Auth::user();
    $userName = $user->name ?? 'User';
    $userInitials = strtoupper(substr($userName, 0, 1));
    if (str_contains($userName, ' ')) {
        $words = preg_split('/\s+/', trim($userName));
        $userInitials = strtoupper(substr($words[0], 0, 1) . substr($words[1] ?? '', 0, 1));
    }
@endphp

<style>
.js-top { position:sticky; top:0; z-index:30; background:#fff; border-bottom:1px solid #e5e7eb; }
.dark .js-top { background:#1f2937; border-color:#374151; }
.js-top-inner { display:flex; align-items:center; justify-content:space-between; gap:1rem; padding:.75rem 1rem; }
.js-top-left { display:flex; align-items:center; gap:1rem; min-width:0; }
.js-top-menu {
    display:inline-flex; align-items:center; justify-content:center;
    padding:.5rem; border:0; border-radius:.375rem; background:transparent; cursor:pointer; color:#374151;
}
.dark .js-top-menu { color:#d1d5db; }
.js-top-menu:hover { background:#f3f4f6; }
.dark .js-top-menu:hover { background:#374151; }
.js-top-menu svg { width:1.5rem; height:1.5rem; display:block; }
@media (min-width:1024px){ .js-top-menu { display:none; } }
.js-top-search { display:none; align-items:center; gap:.5rem; background:#f3f4f6; border-radius:.375rem; padding:.5rem 1rem; width:20rem; max-width:100%; }
.dark .js-top-search { background:#374151; }
@media (min-width:768px){ .js-top-search{ display:flex; } }
.js-top-search input { flex:1; border:0; background:transparent; outline:none; font-size:.875rem; color:#111827; min-width:0; }
.dark .js-top-search input { color:#f9fafb; }
.js-top-right { display:flex; align-items:center; gap:.75rem; margin-left:auto; flex-shrink:0; }
.js-top-icon { padding:.5rem; border-radius:.375rem; border:0; background:transparent; cursor:pointer; color:#374151; position:relative; display:inline-flex; align-items:center; justify-content:center; text-decoration:none; }
.dark .js-top-icon { color:#d1d5db; }
.js-top-icon:hover { background:#f3f4f6; }
.dark .js-top-icon:hover { background:#374151; }
.js-top-icon svg { width:1.25rem; height:1.25rem; }
.js-top-badge { position:absolute; top:.25rem; right:.25rem; min-width:1rem; height:1rem; padding:0 .2rem; background:#ef4444; color:#fff; font-size:.65rem; font-weight:700; border-radius:9999px; display:none; align-items:center; justify-content:center; line-height:1; }
.js-top-badge:not(.hidden) { display:flex; }
.js-top-avatar { width:2.5rem; height:2.5rem; border-radius:9999px; display:flex; align-items:center; justify-content:center; color:#fff; font-size:.875rem; font-weight:700; background:linear-gradient(to bottom right,#2563eb,#06b6d4); border:0; cursor:pointer; }
</style>

<header class="js-top">
    <div class="js-top-inner">
        <div class="js-top-left">
            <button type="button" class="js-top-menu" onclick="window.openJobSeekerSidebar && window.openJobSeekerSidebar()" aria-label="Open menu">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>
            <form action="{{ route('job-seeker.discovery') }}" method="GET" class="js-top-search">
                <svg style="width:1.25rem;height:1.25rem;color:#9ca3af;flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input type="text" name="q" placeholder="Search jobs, companies..." value="{{ request('q') }}">
            </form>
        </div>

        <div class="js-top-right">
            @include('partials.theme-toggle')

            <a href="{{ route('job-seeker.notifications') }}" class="js-top-icon" aria-label="Notifications">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                <span id="notification-badge" class="js-top-badge">0</span>
            </a>

            <div class="relative" id="user-menu-container">
                <button type="button" id="user-menu-button" class="js-top-avatar" aria-label="Account menu">{{ $userInitials }}</button>
                <div id="user-menu-dropdown" class="hidden absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 py-1 z-50">
                    <a href="{{ route('dashboard') }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">Dashboard</a>
                    <a href="{{ route('job-seeker.profile') }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">My Profile</a>
                    <div class="border-t border-gray-200 dark:border-gray-700 my-1"></div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full text-left block px-4 py-2 text-sm text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20">Logout</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>

<script>
(function() {
    function initUserMenu() {
        var button = document.getElementById('user-menu-button');
        var dropdown = document.getElementById('user-menu-dropdown');
        var container = document.getElementById('user-menu-container');
        if (!button || !dropdown || !container) return;
        button.onclick = function(e) {
            e.stopPropagation();
            dropdown.classList.toggle('hidden');
        };
        document.addEventListener('click', function(e) {
            if (!container.contains(e.target)) dropdown.classList.add('hidden');
        });
    }
    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', initUserMenu);
    else initUserMenu();
})();
</script>
