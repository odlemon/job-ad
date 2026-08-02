{{-- Job seeker sidebar — Bolt /job-seeker (component ab) --}}
<style>
.js-side-overlay { display:none; position:fixed; inset:0; background:rgba(0,0,0,.5); z-index:40; }
.js-side-overlay.is-open { display:block; }
@media (min-width:1024px){ .js-side-overlay,.js-side-overlay.is-open { display:none !important; } }

.js-side {
    width:16rem;
    background:#fff;
    border-right:1px solid #e5e7eb;
    position:fixed;
    inset:0 auto 0 0;
    z-index:50;
    display:flex;
    flex-direction:column;
    transform:translateX(-100%);
    transition:transform .3s, background .2s, border-color .2s;
}
.js-side.is-open { transform:translateX(0); }
@media (min-width:1024px){
    .js-side { transform:none; }
}
.dark .js-side { background:#1f2937; border-color:#374151; }

.js-side-mobile-head {
    display:flex;
    align-items:center;
    justify-content:space-between;
    padding:1rem;
    border-bottom:1px solid #e5e7eb;
    flex-shrink:0;
}
.dark .js-side-mobile-head { border-color:#374151; }
@media (min-width:1024px){ .js-side-mobile-head { display:none; } }
.js-side-mobile-head h2 { margin:0; font-size:1.125rem; font-weight:600; color:#111827; }
.dark .js-side-mobile-head h2 { color:#fff; }
.js-side-close {
    padding:.5rem; border:0; border-radius:.375rem; background:transparent; cursor:pointer; color:#4b5563;
}
.dark .js-side-close { color:#9ca3af; }
.js-side-close:hover { background:#f3f4f6; }
.dark .js-side-close:hover { background:#374151; }
.js-side-close svg { width:1.25rem; height:1.25rem; display:block; }

.js-side-nav { flex:1; overflow-y:auto; padding:1rem; min-height:0; }
.js-side-list { display:flex; flex-direction:column; gap:.25rem; }

.js-side-link {
    width:100%;
    display:flex;
    align-items:center;
    gap:.75rem;
    padding:.75rem 1rem;
    border-radius:.375rem;
    font-weight:500;
    font-size:1rem;
    line-height:1.5;
    text-decoration:none;
    color:#374151;
    transition:all .2s;
    box-sizing:border-box;
    border:0;
    background:transparent;
    cursor:pointer;
    text-align:left;
}
.dark .js-side-link { color:#d1d5db; }
.js-side-link:hover { background:#f3f4f6; }
.dark .js-side-link:hover { background:#374151; }
.js-side-link svg { width:1.25rem; height:1.25rem; flex-shrink:0; }
.js-side-link.is-active {
    color:#fff;
    background:linear-gradient(to right,#2563eb,#06b6d4);
    box-shadow:0 10px 15px -3px rgba(0,0,0,.1), 0 4px 6px -4px rgba(0,0,0,.1);
}
.js-side-link.is-active:hover {
    color:#fff;
    background:linear-gradient(to right,#2563eb,#06b6d4);
}

.js-side-foot {
    padding:1rem;
    border-top:1px solid #e5e7eb;
    flex-shrink:0;
}
.dark .js-side-foot { border-color:#374151; }
.js-side-user { display:flex; align-items:center; gap:.75rem; }
.js-side-avatar {
    width:2.5rem; height:2.5rem; border-radius:9999px;
    display:flex; align-items:center; justify-content:center;
    color:#fff; font-size:.875rem; font-weight:600; flex-shrink:0;
    background:linear-gradient(to bottom right,#2563eb,#06b6d4);
}
.js-side-user-name {
    margin:0; font-size:.875rem; font-weight:500; color:#111827;
    white-space:nowrap; overflow:hidden; text-overflow:ellipsis;
}
.dark .js-side-user-name { color:#fff; }
.js-side-user-email {
    margin:0; font-size:.75rem; color:#6b7280;
    white-space:nowrap; overflow:hidden; text-overflow:ellipsis;
}
.dark .js-side-user-email { color:#9ca3af; }
</style>

@php
    $currentRoute = request()->route() ? request()->route()->getName() : '';
    $currentPath = request()->path();
    $isDashboard = $currentRoute === 'dashboard' || $currentRoute === 'job-seeker.dashboard' || in_array($currentPath, ['dashboard', 'job-seeker', 'job-seeker/dashboard'], true);
    $isProfile = str_contains((string) $currentRoute, 'job-seeker.profile') || str_contains($currentPath, 'job-seeker/profile');
    $isApplications = str_contains((string) $currentRoute, 'application') || str_contains($currentPath, 'job-seeker/applications');
    $isDiscovery = str_contains((string) $currentRoute, 'job-seeker.discovery')
        || str_contains($currentPath, 'job-seeker/discovery')
        || str_contains((string) $currentRoute, 'saved')
        || str_contains($currentPath, 'job-seeker/saved-jobs');
    $isCompanies = str_contains((string) $currentRoute, 'job-seeker.followed-companies')
        || str_contains($currentPath, 'job-seeker/followed-companies');
    $isNotifications = str_contains((string) $currentRoute, 'notification') || str_contains($currentPath, 'notifications');
    $isCareerTools = str_contains((string) $currentRoute, 'job-seeker.career-tools')
        || str_contains($currentPath, 'job-seeker/career-tools');
    $isSupport = str_contains((string) $currentRoute, 'job-seeker.support')
        || str_contains($currentPath, 'job-seeker/support');
    $isSaved = false;
    $isSettings = str_contains((string) $currentRoute, 'job-seeker.settings')
        || str_contains($currentPath, 'job-seeker/settings');

    $user = auth()->user();
    $userInitials = strtoupper(substr($user->name ?? 'JD', 0, 2));
    if (str_contains($user->name ?? '', ' ')) {
        $parts = preg_split('/\s+/', trim($user->name));
        $userInitials = strtoupper(substr($parts[0], 0, 1) . substr($parts[1] ?? '', 0, 1));
    }
    $userName = $user->name ?? 'Job Seeker';
    $userEmail = $user->email ?? '';
@endphp

<div id="js-side-overlay" class="js-side-overlay" onclick="window.closeJobSeekerSidebar && window.closeJobSeekerSidebar()"></div>

<aside id="job-seeker-sidebar" class="js-side" aria-label="Job seeker navigation">
    <div class="js-side-mobile-head">
        <img src="{{ asset('scoop.png') }}" alt="Scoop" style="height:1.75rem;width:auto;object-fit:contain;">
        <button type="button" class="js-side-close" onclick="window.closeJobSeekerSidebar && window.closeJobSeekerSidebar()" aria-label="Close menu">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    </div>

    <nav class="js-side-nav">
        <div class="js-side-list">
            <a href="{{ route('dashboard') }}" class="js-side-link {{ $isDashboard ? 'is-active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                <span>Dashboard Overview</span>
            </a>
            <a href="{{ route('job-seeker.profile') }}" class="js-side-link {{ $isProfile ? 'is-active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                <span>My Profile</span>
            </a>
            <a href="{{ route('job-seeker.applications') }}" class="js-side-link {{ $isApplications ? 'is-active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                <span>Job Applications</span>
            </a>
            <a href="{{ route('job-seeker.discovery') }}" class="js-side-link {{ $isDiscovery || $isSaved ? 'is-active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <span>Job Discovery</span>
            </a>
            <a href="{{ route('job-seeker.followed-companies') }}" class="js-side-link {{ $isCompanies ? 'is-active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                <span>Companies</span>
            </a>
            <a href="{{ route('job-seeker.notifications') }}" class="js-side-link {{ $isNotifications ? 'is-active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                <span>Notifications</span>
            </a>
            <a href="{{ route('job-seeker.career-tools') }}" class="js-side-link {{ $isCareerTools ? 'is-active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 01-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 01-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                <span>Career Tools</span>
            </a>
            <a href="{{ route('job-seeker.support') }}" class="js-side-link {{ $isSupport ? 'is-active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>Support</span>
            </a>
            <a href="{{ route('job-seeker.settings') }}" class="js-side-link {{ $isSettings ? 'is-active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/></svg>
                <span>Settings</span>
            </a>
        </div>
    </nav>

    <div class="js-side-foot">
        <div class="js-side-user">
            <div class="js-side-avatar">{{ $userInitials }}</div>
            <div style="min-width:0;flex:1;">
                <p class="js-side-user-name">{{ $userName }}</p>
                <p class="js-side-user-email">{{ $userEmail }}</p>
            </div>
        </div>
    </div>
</aside>

<script>
(function () {
    function openSidebar() {
        var side = document.getElementById('job-seeker-sidebar');
        var overlay = document.getElementById('js-side-overlay');
        if (side) side.classList.add('is-open');
        if (overlay) overlay.classList.add('is-open');
        document.body.style.overflow = 'hidden';
    }
    function closeSidebar() {
        var side = document.getElementById('job-seeker-sidebar');
        var overlay = document.getElementById('js-side-overlay');
        if (side) side.classList.remove('is-open');
        if (overlay) overlay.classList.remove('is-open');
        document.body.style.overflow = '';
    }
    window.openJobSeekerSidebar = openSidebar;
    window.closeJobSeekerSidebar = closeSidebar;
    window.addEventListener('resize', function () {
        if (window.innerWidth >= 1024) closeSidebar();
    });
})();
</script>
