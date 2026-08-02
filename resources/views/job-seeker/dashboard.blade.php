@extends('layouts.job-seeker')

@section('content')
<style>
.jsd-main { flex:1; overflow-y:auto; }
.jsd-wrap { max-width:80rem; margin:0 auto; padding:2rem 1rem; }
@media (min-width:640px){ .jsd-wrap{ padding-left:1.5rem; padding-right:1.5rem; } }
@media (min-width:1024px){ .jsd-wrap{ padding-left:2rem; padding-right:2rem; } }
.jsd-stack { display:flex; flex-direction:column; gap:1.5rem; }
.jsd-kpis { display:grid; grid-template-columns:1fr; gap:1.5rem; }
@media (min-width:768px){ .jsd-kpis{ grid-template-columns:repeat(2,minmax(0,1fr)); } }
@media (min-width:1024px){ .jsd-kpis{ grid-template-columns:repeat(4,minmax(0,1fr)); } }
.jsd-card { background:#fff; border-radius:.75rem; padding:1.5rem; box-shadow:0 1px 2px rgba(0,0,0,.04); border:1px solid #e5e7eb; transition:box-shadow .15s; }
.dark .jsd-card { background:#1f2937; border-color:#374151; }
.jsd-card:hover { box-shadow:0 10px 15px -3px rgba(0,0,0,.08); }
.jsd-kpi-row { display:flex; align-items:flex-start; justify-content:space-between; gap:1rem; }
.jsd-kpi-label { font-size:.875rem; font-weight:500; color:#4b5563; margin:0; }
.dark .jsd-kpi-label { color:#9ca3af; }
.jsd-kpi-val { font-size:1.875rem; font-weight:700; color:#111827; margin:.5rem 0 0; line-height:1.2; }
.dark .jsd-kpi-val { color:#fff; }
.jsd-kpi-sub { font-size:.75rem; color:#6b7280; margin:.5rem 0 0; }
.dark .jsd-kpi-sub { color:#9ca3af; }
.jsd-icon { padding:.75rem; border-radius:.5rem; color:#fff; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
.jsd-icon svg { width:1.5rem; height:1.5rem; }
.jsd-row-3 { display:grid; grid-template-columns:1fr; gap:1.5rem; }
@media (min-width:1024px){ .jsd-row-3{ grid-template-columns:2fr 1fr; } }
.jsd-row-2 { display:grid; grid-template-columns:1fr; gap:1.5rem; }
@media (min-width:1024px){ .jsd-row-2{ grid-template-columns:repeat(2,minmax(0,1fr)); } }
.jsd-h { font-size:1.125rem; font-weight:600; color:#111827; margin:0; }
.dark .jsd-h { color:#fff; }
.jsd-link { font-size:.875rem; color:#2563eb; text-decoration:none; }
.jsd-link:hover { text-decoration:underline; }
.dark .jsd-link { color:#22d3ee; }
.jsd-act { display:flex; align-items:flex-start; gap:.75rem; padding:.75rem; border-radius:.5rem; transition:background .15s; }
.jsd-act:hover { background:#f9fafb; }
.dark .jsd-act:hover { background:rgba(55,65,81,.5); }
.jsd-dot { width:.5rem; height:.5rem; border-radius:9999px; margin-top:.5rem; flex-shrink:0; }
.jsd-ach { display:flex; gap:.75rem; align-items:flex-start; padding:.75rem; border-radius:.5rem; border:1px solid #dbeafe; background:linear-gradient(to right,#eff6ff,#ecfeff); }
.dark .jsd-ach { border-color:#4b5563; background:linear-gradient(to right,rgba(55,65,81,.5),rgba(55,65,81,.3)); }
.jsd-bar-track { width:100%; height:.75rem; background:#e5e7eb; border-radius:9999px; overflow:hidden; }
.dark .jsd-bar-track { background:#374151; }
.jsd-bar-fill { height:100%; border-radius:9999px; background:linear-gradient(to right,#2563eb,#06b6d4); transition:width .4s; }
.jsd-skill-track { width:100%; height:.5rem; background:#e5e7eb; border-radius:9999px; overflow:hidden; }
.dark .jsd-skill-track { background:#374151; }
.jsd-skill-fill { height:100%; border-radius:9999px; transition:width .4s; }
.jsd-skel { animation:jsd-pulse 1.5s ease-in-out infinite; background:#e5e7eb; border-radius:.5rem; }
.dark .jsd-skel { background:#374151; }
@keyframes jsd-pulse { 0%,100%{opacity:1} 50%{opacity:.5} }
.jsd-empty { text-align:center; padding:1.5rem; color:#6b7280; font-size:.875rem; }
</style>

@include('partials.job-seeker-navbar')

<main class="jsd-main">
    <div class="jsd-wrap">
        <div id="dashboard-loading" class="jsd-stack">
            <div class="jsd-kpis">
                @for($i=0;$i<4;$i++)
                    <div class="jsd-card"><div class="jsd-skel" style="height:5rem;"></div></div>
                @endfor
            </div>
            <div class="jsd-row-3">
                <div class="jsd-card"><div class="jsd-skel" style="height:12rem;"></div></div>
                <div class="jsd-card"><div class="jsd-skel" style="height:12rem;"></div></div>
            </div>
            <div class="jsd-row-2">
                <div class="jsd-card"><div class="jsd-skel" style="height:10rem;"></div></div>
                <div class="jsd-card"><div class="jsd-skel" style="height:10rem;"></div></div>
            </div>
        </div>

        <div id="dashboard-content" class="jsd-stack" style="display:none;">
            {{-- KPI cards — Bolt Hc --}}
            <div class="jsd-kpis">
                <div class="jsd-card">
                    <div class="jsd-kpi-row">
                        <div style="flex:1;min-width:0;">
                            <p class="jsd-kpi-label">Total Applications</p>
                            <p class="jsd-kpi-val" id="total-applications-value">0</p>
                            <p class="jsd-kpi-sub" id="total-applications-change">+0 this week</p>
                        </div>
                        <div class="jsd-icon" style="background:linear-gradient(to bottom right,#2563eb,#3b82f6);">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        </div>
                    </div>
                </div>
                <div class="jsd-card">
                    <div class="jsd-kpi-row">
                        <div style="flex:1;min-width:0;">
                            <p class="jsd-kpi-label">In Review</p>
                            <p class="jsd-kpi-val" id="in-review-value">0</p>
                            <p class="jsd-kpi-sub" id="in-review-detail">0 interviews scheduled</p>
                        </div>
                        <div class="jsd-icon" style="background:linear-gradient(to bottom right,#ca8a04,#eab308);">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                    </div>
                </div>
                <div class="jsd-card">
                    <div class="jsd-kpi-row">
                        <div style="flex:1;min-width:0;">
                            <p class="jsd-kpi-label">Offers Received</p>
                            <p class="jsd-kpi-val" id="offers-value">0</p>
                            <p class="jsd-kpi-sub" id="offers-change">+0 this week</p>
                        </div>
                        <div class="jsd-icon" style="background:linear-gradient(to bottom right,#16a34a,#22c55e);">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                    </div>
                </div>
                <div class="jsd-card">
                    <div class="jsd-kpi-row">
                        <div style="flex:1;min-width:0;">
                            <p class="jsd-kpi-label">Rejected</p>
                            <p class="jsd-kpi-val" id="rejected-value">0</p>
                            <p class="jsd-kpi-sub">Keep applying!</p>
                        </div>
                        <div class="jsd-icon" style="background:linear-gradient(to bottom right,#dc2626,#ef4444);">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Recent Activity (2/3) + Achievements (1/3) --}}
            <div class="jsd-row-3">
                <div class="jsd-card">
                    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1rem;">
                        <h3 class="jsd-h">Recent Activity</h3>
                        <a href="{{ route('job-seeker.applications') }}" class="jsd-link">View All</a>
                    </div>
                    <div id="recent-activity-list" style="display:flex;flex-direction:column;gap:1rem;"></div>
                </div>
                <div class="jsd-card">
                    <div style="display:flex;align-items:center;gap:.5rem;margin-bottom:1rem;">
                        <svg style="width:1.25rem;height:1.25rem;color:#eab308;" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                        <h3 class="jsd-h">Achievements</h3>
                    </div>
                    <div id="achievements-list" style="display:flex;flex-direction:column;gap:.75rem;"></div>
                </div>
            </div>

            {{-- Profile Completeness + Skill Match --}}
            <div class="jsd-row-2">
                <div class="jsd-card">
                    <h3 class="jsd-h" style="margin-bottom:1rem;">Profile Completeness</h3>
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:.5rem;">
                        <span style="font-size:.875rem;font-weight:500;color:#374151;">Overall Progress</span>
                        <span id="profile-completeness-percent" style="font-size:.875rem;font-weight:700;color:#111827;">0%</span>
                    </div>
                    <div class="jsd-bar-track" style="margin-bottom:1rem;">
                        <div id="profile-completeness-bar" class="jsd-bar-fill" style="width:0%;"></div>
                    </div>
                    <div id="profile-completeness-items" style="display:flex;flex-direction:column;gap:.5rem;padding-top:.5rem;"></div>
                </div>
                <div class="jsd-card">
                    <h3 class="jsd-h" style="margin-bottom:1rem;">Skill Match Analytics</h3>
                    <div style="margin-bottom:1.25rem;">
                        <p id="average-match" style="font-size:1.5rem;font-weight:700;color:#111827;margin:0;">0%</p>
                        <p style="font-size:.875rem;color:#6b7280;margin:.25rem 0 0;">Average Match</p>
                    </div>
                    <div id="skill-matches-list" style="display:flex;flex-direction:column;gap:1rem;"></div>
                </div>
            </div>
        </div>
    </div>
</main>

@push('scripts')
<script>
(function() {
    var API_BASE = '/api';

    function esc(s) {
        return String(s == null ? '' : s)
            .replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;')
            .replace(/"/g,'&quot;');
    }

    function getTimeAgo(date) {
        var seconds = Math.floor((new Date() - date) / 1000);
        if (seconds < 60) return 'just now';
        var minutes = Math.floor(seconds / 60);
        if (minutes < 60) return minutes === 1 ? '1 minute ago' : minutes + ' minutes ago';
        var hours = Math.floor(minutes / 60);
        if (hours < 24) return hours === 1 ? '1 hour ago' : hours + ' hours ago';
        var days = Math.floor(hours / 24);
        return days === 1 ? '1 day ago' : days + ' days ago';
    }

    function activityAction(activity) {
        var status = activity.status || '';
        var title = activity.job_title || '';
        if (status === 'shortlisted' || status === 'interview') return title ? ('Interview scheduled') : 'Interview scheduled';
        if (status === 'reviewing' || status === 'in_review') return 'Application viewed';
        if (status === 'hired' || status === 'offered' || status === 'accepted') return 'Offer received';
        if (status === 'rejected') return 'Application rejected';
        return title ? ('Applied to ' + title) : 'Applied to a job';
    }

    function achievementEmoji(icon, achieved) {
        if (!achieved) return '🔒';
        if (icon === 'target') return '🎯';
        if (icon === 'rocket') return '🎉';
        if (icon === 'star') return '⭐';
        return '🏆';
    }

    function skillBarColor(pct) {
        if (pct >= 85) return '#22c55e';
        if (pct >= 70) return '#3b82f6';
        return '#eab308';
    }

    async function loadDashboard() {
        var loading = document.getElementById('dashboard-loading');
        var content = document.getElementById('dashboard-content');
        loading.style.display = '';
        content.style.display = 'none';

        try {
            var response = await fetch(API_BASE + '/job-seeker/dashboard', {
                credentials: 'include',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                }
            });

            if (response.status === 401 || response.status === 403) {
                window.location.href = '/';
                return;
            }
            if (!response.ok) throw new Error('Failed to load dashboard');

            var data = await response.json();
            var stats = data.stats || {};

            document.getElementById('total-applications-value').textContent = stats.total_applications || 0;
            document.getElementById('total-applications-change').textContent = '+' + (stats.this_week_applications || 0) + ' this week';
            document.getElementById('in-review-value').textContent = stats.in_review || 0;
            var interviews = stats.interview_scheduled || 0;
            document.getElementById('in-review-detail').textContent = interviews + ' interview' + (interviews !== 1 ? 's' : '') + ' scheduled';
            document.getElementById('offers-value').textContent = stats.offers || 0;
            document.getElementById('offers-change').textContent = '+' + (stats.this_week_offers || 0) + ' this week';
            document.getElementById('rejected-value').textContent = stats.rejected || 0;

            var recentActivityList = document.getElementById('recent-activity-list');
            var recentActivity = data.recent_activity || [];
            if (recentActivity.length > 0) {
                recentActivityList.innerHTML = recentActivity.map(function(activity) {
                    var timeAgo = getTimeAgo(new Date(activity.created_at));
                    var success = ['shortlisted','interview','hired','offered','accepted'].indexOf(activity.status) >= 0;
                    var dot = success ? '#22c55e' : '#3b82f6';
                    return '<div class="jsd-act">' +
                        '<span class="jsd-dot" style="background:' + dot + ';"></span>' +
                        '<div style="flex:1;min-width:0;">' +
                            '<p style="font-size:.875rem;font-weight:500;color:#111827;margin:0;">' + esc(activityAction(activity)) + '</p>' +
                            '<p style="font-size:.875rem;color:#4b5563;margin:.15rem 0 0;">' + esc(activity.company_name || 'Company') + '</p>' +
                        '</div>' +
                        '<span style="font-size:.75rem;color:#6b7280;white-space:nowrap;">' + esc(timeAgo) + '</span>' +
                    '</div>';
                }).join('');
            } else {
                recentActivityList.innerHTML = '<p class="jsd-empty">No recent activity</p>';
            }

            var achievementsList = document.getElementById('achievements-list');
            var achievements = data.achievements || [];
            if (achievements.length > 0) {
                achievementsList.innerHTML = achievements.map(function(a) {
                    var emoji = achievementEmoji(a.icon, a.achieved !== false);
                    var opacity = a.achieved === false ? 'opacity:.55;' : '';
                    return '<div class="jsd-ach" style="' + opacity + '">' +
                        '<span style="font-size:1.5rem;line-height:1;">' + emoji + '</span>' +
                        '<div>' +
                            '<p style="font-size:.875rem;font-weight:500;color:#111827;margin:0;">' + esc(a.title || a.name) + '</p>' +
                            '<p style="font-size:.75rem;color:#6b7280;margin:.15rem 0 0;">' + esc(a.description || a.desc || '') + '</p>' +
                        '</div>' +
                    '</div>';
                }).join('');
            } else {
                achievementsList.innerHTML = '<p class="jsd-empty">Complete your profile to unlock achievements</p>';
            }

            var profileCompleteness = data.profile_completeness || { percentage: 0, items: [] };
            document.getElementById('profile-completeness-percent').textContent = (profileCompleteness.percentage || 0) + '%';
            document.getElementById('profile-completeness-bar').style.width = (profileCompleteness.percentage || 0) + '%';
            document.getElementById('profile-completeness-items').innerHTML = (profileCompleteness.items || []).map(function(item) {
                var complete = !!item.complete;
                return '<div style="display:flex;align-items:center;justify-content:space-between;font-size:.875rem;">' +
                    '<span style="color:' + (complete ? '#16a34a' : '#374151') + ';">' + (complete ? '✓ ' : '• ') + esc(item.label) + '</span>' +
                    '<span style="font-weight:500;color:' + (complete ? '#16a34a' : '#eab308') + ';">' + esc(item.status || (complete ? 'Complete' : 'Pending')) + '</span>' +
                '</div>';
            }).join('') || '<p class="jsd-empty">No profile checklist yet</p>';

            var skillMatchAnalytics = data.skill_match_analytics || { average_match: 0, skills: [] };
            document.getElementById('average-match').textContent = (skillMatchAnalytics.average_match || 0) + '%';
            var skillMatchesList = document.getElementById('skill-matches-list');
            if (skillMatchAnalytics.skills && skillMatchAnalytics.skills.length > 0) {
                skillMatchesList.innerHTML = skillMatchAnalytics.skills.map(function(skill) {
                    var pct = Number(skill.match) || 0;
                    return '<div>' +
                        '<div style="display:flex;justify-content:space-between;margin-bottom:.35rem;font-size:.75rem;">' +
                            '<span style="color:#374151;">' + esc(skill.name) + '</span>' +
                            '<span style="font-weight:600;color:#111827;">' + pct + '%</span>' +
                        '</div>' +
                        '<div class="jsd-skill-track"><div class="jsd-skill-fill" style="width:' + pct + '%;background:' + skillBarColor(pct) + ';"></div></div>' +
                    '</div>';
                }).join('');
            } else {
                skillMatchesList.innerHTML = '<p class="jsd-empty">Add skills to your profile to see match analytics</p>';
            }

            loading.style.display = 'none';
            content.style.display = '';
        } catch (err) {
            console.error('Error loading dashboard:', err);
            loading.style.display = 'none';
            content.style.display = '';
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', loadDashboard);
    } else {
        loadDashboard();
    }

    document.addEventListener('livewire:navigated', function() {
        var path = window.location.pathname;
        if (path === '/dashboard' || path === '/job-seeker' || path === '/job-seeker/dashboard') {
            setTimeout(loadDashboard, 100);
        }
    });
})();
</script>
@endpush
@endsection
