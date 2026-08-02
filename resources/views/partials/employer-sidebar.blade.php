{{-- Employer sidebar — Bolt design (no Settings link) --}}
<style>
.emp-side { width:16rem; background:#fff; border-right:1px solid #e5e7eb; height:100vh; position:fixed; left:0; top:0; z-index:30; display:flex; flex-direction:column; transition:background .2s,border-color .2s; }
.dark .emp-side { background:#1f2937; border-color:#374151; }
.emp-side-brand { padding:1.25rem 1.25rem 1rem; border-bottom:1px solid #e5e7eb; flex-shrink:0; }
.dark .emp-side-brand { border-color:#374151; }
.emp-side-brand-link { display:flex; flex-direction:column; align-items:flex-start; gap:.35rem; text-decoration:none; }
.emp-side-brand-img { height:2.35rem; width:auto; max-width:100%; object-fit:contain; display:block; }
.emp-side-sub { font-size:.75rem; color:#6b7280; margin:0; }
.dark .emp-side-sub { color:#9ca3af; }
.emp-side-nav { flex:1; padding:1rem; overflow-y:auto; min-height:0; }
.emp-side-nav-list { display:flex; flex-direction:column; gap:.25rem; }
.emp-side-link { width:100%; display:flex; align-items:center; gap:.75rem; padding:.75rem 1rem; border-radius:.25rem; font-size:.875rem; font-weight:500; text-decoration:none; transition:all .15s; color:#374151; box-sizing:border-box; border:0; background:transparent; cursor:pointer; text-align:left; }
.dark .emp-side-link { color:#d1d5db; }
.emp-side-link:hover { background:#f3f4f6; }
.dark .emp-side-link:hover { background:#374151; }
.emp-side-link svg { width:1.25rem; height:1.25rem; flex-shrink:0; }
.emp-side-link.is-active { color:#fff; background:linear-gradient(to right,#2563eb,#06b6d4); box-shadow:0 10px 15px -3px rgba(37,99,235,.3); }
.emp-side-link.is-active:hover { background:linear-gradient(to right,#2563eb,#06b6d4); color:#fff; }
.emp-side-foot { padding:1rem; border-top:1px solid #e5e7eb; flex-shrink:0; }
.dark .emp-side-foot { border-color:#374151; }
.emp-side-exit { width:100%; display:flex; align-items:center; gap:.75rem; padding:.75rem 1rem; border-radius:.25rem; font-size:.875rem; font-weight:500; text-decoration:none; color:#dc2626; transition:all .15s; box-sizing:border-box; }
.dark .emp-side-exit { color:#f87171; }
.emp-side-exit:hover { background:#fef2f2; }
.dark .emp-side-exit:hover { background:rgba(127,29,29,.2); }
.emp-side-exit svg { width:1.25rem; height:1.25rem; flex-shrink:0; }
@media (max-width:768px) {
    .emp-side { transform:translateX(-100%); }
    .emp-side.is-open { transform:translateX(0); }
}
</style>

@php
    $currentRoute = request()->route() ? request()->route()->getName() : '';
    $currentPath = request()->path();
    $isJobApplicantsPage = $currentRoute === 'employer.jobs.applicants' || preg_match('#^employer/jobs/\d+/applicants$#', $currentPath);
    $isDashboard = str_contains((string) $currentRoute, 'employer.dashboard') || str_contains($currentPath, 'employer/dashboard');
    $isCompanyProfile = str_contains((string) $currentRoute, 'employer.company-profile') || str_contains($currentPath, 'employer/company-profile');
    $isJobs = !$isJobApplicantsPage && (str_contains((string) $currentRoute, 'employer.jobs') || preg_match('#^employer/jobs#', $currentPath));
    $isApplications = $isJobApplicantsPage || str_contains((string) $currentRoute, 'employer.applications') || str_contains($currentPath, 'employer/applications');
    $isAnalytics = str_contains((string) $currentRoute, 'employer.analytics') || str_contains($currentPath, 'employer/analytics');
    $isCampaignsList = $currentRoute === 'employer.campaigns.index';
    $isCampaignsCreate = $currentRoute === 'employer.campaigns.create';
    $isCoins = str_contains((string) $currentRoute, 'employer.coins') || str_contains($currentPath, 'employer/coins');
    $isInvoices = str_contains((string) $currentRoute, 'employer.invoices') || str_contains($currentPath, 'employer/invoices');
    $isTeam = str_contains((string) $currentRoute, 'employer.team') || str_contains($currentPath, 'employer/team');
@endphp

<aside id="employer-sidebar" class="emp-side">
    <div class="emp-side-brand">
        <a href="/" class="emp-side-brand-link">
            <img src="{{ asset('scoop.png') }}" alt="Scoop" class="emp-side-brand-img">
            <p class="emp-side-sub">Employer Portal</p>
        </a>
    </div>

    <nav class="emp-side-nav">
        <div class="emp-side-nav-list">
            <a href="{{ url('/employer/dashboard') }}" class="emp-side-link {{ $isDashboard ? 'is-active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                <span>Dashboard</span>
            </a>

            <a href="{{ route('employer.company-profile') }}" class="emp-side-link {{ $isCompanyProfile ? 'is-active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                <span>Company Profile</span>
            </a>

            <a href="{{ route('employer.jobs.index') }}" class="emp-side-link {{ $isJobs ? 'is-active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                <span>Job Listings</span>
            </a>

            <a href="{{ route('employer.applications.index') }}" class="emp-side-link {{ $isApplications ? 'is-active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                <span>Applicants</span>
            </a>

            <a href="{{ route('employer.analytics.index') }}" class="emp-side-link {{ $isAnalytics ? 'is-active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                <span>Analytics</span>
            </a>

            <a href="{{ route('employer.campaigns.index') }}" class="emp-side-link {{ $isCampaignsList ? 'is-active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/></svg>
                <span>Job Campaigns</span>
            </a>

            <a href="{{ route('employer.campaigns.create') }}" class="emp-side-link {{ $isCampaignsCreate ? 'is-active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                <span>Create Campaign</span>
            </a>

            <a href="{{ route('employer.coins.index') }}" class="emp-side-link {{ $isCoins ? 'is-active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>Coins & Billing</span>
            </a>

            <a href="{{ route('employer.invoices.index') }}" class="emp-side-link {{ $isInvoices ? 'is-active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <span>Invoices</span>
            </a>

            <a href="{{ route('employer.team.index') }}" class="emp-side-link {{ $isTeam ? 'is-active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                <span>Team Management</span>
            </a>
        </div>
    </nav>

    <div class="emp-side-foot">
        <a href="{{ url('/') }}" class="emp-side-exit">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
            <span>Exit Portal</span>
        </a>
    </div>
</aside>
