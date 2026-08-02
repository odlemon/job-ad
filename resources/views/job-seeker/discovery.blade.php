@extends('layouts.job-seeker')

@section('content')
<style>
.jd-main { flex:1; overflow-y:auto; }
.jd-wrap { max-width:80rem; margin:0 auto; padding:2rem 1rem; }
@media (min-width:640px){ .jd-wrap{ padding-left:1.5rem; padding-right:1.5rem; } }
@media (min-width:1024px){ .jd-wrap{ padding-left:2rem; padding-right:2rem; } }
.jd-stack { display:flex; flex-direction:column; gap:1.5rem; }
.jd-panel { background:#fff; border-radius:.75rem; padding:1.5rem; box-shadow:0 1px 2px rgba(0,0,0,.04); border:1px solid #e5e7eb; }
.dark .jd-panel { background:#1f2937; border-color:#374151; }
.jd-title { margin:0 0 1rem; font-size:1.5rem; font-weight:700; color:#111827; }
.dark .jd-title { color:#fff; }

.jd-search-row { display:flex; flex-direction:column; gap:.75rem; }
@media (min-width:768px){ .jd-search-row{ flex-direction:row; } }
.jd-field {
    display:flex; align-items:center; gap:.5rem; background:#f3f4f6; border-radius:.5rem;
    padding:.75rem 1rem; flex:1; min-width:0;
}
.dark .jd-field { background:#374151; }
.jd-field-loc { flex:0 0 auto; width:100%; }
@media (min-width:768px){ .jd-field-loc{ width:10rem; } }
.jd-field svg { width:1.25rem; height:1.25rem; color:#9ca3af; flex-shrink:0; }
.jd-field input {
    flex:1; border:0; background:transparent; outline:none; min-width:0;
    font-size:.875rem; color:#111827;
}
.dark .jd-field input { color:#fff; }
.jd-field input::placeholder { color:#9ca3af; }
.jd-search-btn {
    padding:.75rem 1.5rem; border:0; border-radius:.5rem; color:#fff; font-weight:500; cursor:pointer;
    background:linear-gradient(to right,#2563eb,#06b6d4); transition:box-shadow .15s; white-space:nowrap;
}
.jd-search-btn:hover { box-shadow:0 10px 15px -3px rgba(37,99,235,.3); }
.jd-search-btn:disabled { opacity:.7; cursor:wait; }

.jd-tools { display:flex; flex-wrap:wrap; align-items:center; gap:.75rem; margin-top:1rem; }
.jd-tool {
    display:inline-flex; align-items:center; gap:.5rem; padding:.5rem 1rem;
    border:1px solid #d1d5db; border-radius:.5rem; background:transparent; cursor:pointer;
    font-size:.875rem; color:#374151; transition:background .15s;
}
.dark .jd-tool { border-color:#4b5563; color:#d1d5db; }
.jd-tool:hover { background:#f9fafb; }
.dark .jd-tool:hover { background:#374151; }
.jd-tool svg { width:1rem; height:1rem; }
.jd-tool.is-on { border-color:#2563eb; color:#2563eb; background:#eff6ff; }
.dark .jd-tool.is-on { border-color:#22d3ee; color:#22d3ee; background:rgba(30,58,138,.2); }

.jd-adv {
    display:none; margin-top:1rem; padding:1rem; border-radius:.5rem;
    background:#f9fafb;
}
.dark .jd-adv { background:rgba(55,65,81,.5); }
.jd-adv.is-open { display:block; }
.jd-adv-grid { display:grid; grid-template-columns:1fr; gap:1rem; }
@media (min-width:768px){ .jd-adv-grid{ grid-template-columns:repeat(3,minmax(0,1fr)); } }
.jd-adv label { display:block; font-size:.875rem; font-weight:500; color:#374151; margin-bottom:.5rem; }
.dark .jd-adv label { color:#d1d5db; }
.jd-adv select {
    width:100%; padding:.5rem .75rem; border:1px solid #d1d5db; border-radius:.5rem;
    background:#fff; color:#111827; font-size:.875rem;
}
.dark .jd-adv select { background:#1f2937; border-color:#4b5563; color:#fff; }

.jd-tabs { display:flex; gap:.5rem; border-bottom:1px solid #e5e7eb; }
.dark .jd-tabs { border-color:#374151; }
.jd-tab {
    padding:.5rem 1rem; border:0; border-bottom:2px solid transparent; background:transparent;
    font-weight:500; cursor:pointer; color:#4b5563; margin-bottom:-1px;
}
.dark .jd-tab { color:#9ca3af; }
.jd-tab:hover { color:#111827; }
.dark .jd-tab:hover { color:#fff; }
.jd-tab.is-active { border-bottom-color:#2563eb; color:#2563eb; }
.dark .jd-tab.is-active { border-bottom-color:#22d3ee; color:#22d3ee; }

.jd-grid { display:grid; grid-template-columns:1fr; gap:1rem; }
@media (min-width:1024px){ .jd-grid{ grid-template-columns:repeat(2,minmax(0,1fr)); } }

.jd-card {
    background:#fff; border-radius:.75rem; padding:1.5rem; border:1px solid #e5e7eb;
    box-shadow:0 1px 2px rgba(0,0,0,.04); transition:box-shadow .15s;
}
.dark .jd-card { background:#1f2937; border-color:#374151; }
.jd-card:hover { box-shadow:0 4px 6px -1px rgba(0,0,0,.08); }
.jd-card-top { display:flex; align-items:flex-start; justify-content:space-between; gap:.75rem; margin-bottom:1rem; }
.jd-card-id { display:flex; align-items:flex-start; gap:.75rem; flex:1; min-width:0; }
.jd-logo { width:3rem; height:3rem; border-radius:.5rem; object-fit:cover; flex-shrink:0; background:#f3f4f6; }
.dark .jd-logo { background:#374151; }
.jd-logo-fb {
    width:3rem; height:3rem; border-radius:.5rem; flex-shrink:0; display:flex; align-items:center; justify-content:center;
    background:linear-gradient(to bottom right,#2563eb,#06b6d4); color:#fff; font-weight:700;
}
.jd-job-title { margin:0; font-size:1rem; font-weight:600; color:#111827; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
.dark .jd-job-title { color:#fff; }
.jd-card:hover .jd-job-title { color:#2563eb; }
.dark .jd-card:hover .jd-job-title { color:#22d3ee; }
.jd-co { margin:.25rem 0 0; font-size:.875rem; color:#4b5563; }
.dark .jd-co { color:#9ca3af; }
.jd-icon-btns { display:flex; gap:.5rem; flex-shrink:0; }
.jd-icon-btn {
    padding:.5rem; border:0; background:transparent; border-radius:.5rem; cursor:pointer; color:#9ca3af;
}
.jd-icon-btn:hover { background:#f3f4f6; }
.dark .jd-icon-btn:hover { background:#374151; }
.jd-icon-btn.is-saved { color:#ef4444; }
.jd-icon-btn svg { width:1.25rem; height:1.25rem; display:block; }

.jd-meta { display:flex; flex-direction:column; gap:.5rem; margin-bottom:1rem; }
.jd-meta-row { display:flex; align-items:center; gap:.5rem; font-size:.875rem; color:#4b5563; }
.dark .jd-meta-row { color:#9ca3af; }
.jd-meta-row svg { width:1rem; height:1rem; flex-shrink:0; }

.jd-tags { display:flex; flex-wrap:wrap; gap:.5rem; margin-bottom:1rem; }
.jd-tag { padding:.25rem .5rem; border-radius:9999px; font-size:.75rem; background:#dbeafe; color:#1d4ed8; }
.dark .jd-tag { background:rgba(30,58,138,.3); color:#60a5fa; }
.jd-tag-more { padding:.25rem .5rem; border-radius:9999px; font-size:.75rem; background:#f3f4f6; color:#4b5563; }
.dark .jd-tag-more { background:#374151; color:#9ca3af; }

.jd-foot {
    display:flex; align-items:center; justify-content:space-between; gap:.75rem;
    padding-top:1rem; border-top:1px solid #e5e7eb;
}
.dark .jd-foot { border-color:#374151; }
.jd-posted { font-size:.75rem; color:#6b7280; }
.jd-apply {
    padding:.5rem 1rem; border:0; border-radius:.5rem; color:#fff; font-size:.875rem; font-weight:500; cursor:pointer;
    background:linear-gradient(to right,#2563eb,#06b6d4); text-decoration:none; display:inline-block;
}
.jd-apply:hover { box-shadow:0 10px 15px -3px rgba(37,99,235,.3); }
.jd-apply.is-applied { background:#9ca3af; cursor:default; box-shadow:none; }

.jd-expire {
    margin-top:.75rem; padding:.5rem; border-radius:.5rem;
    background:#fef2f2; border:1px solid #fecaca; color:#b91c1c; font-size:.75rem;
}
.dark .jd-expire { background:rgba(127,29,29,.2); border-color:#991b1b; color:#fca5a5; }

.jd-empty { text-align:center; padding:3rem 1rem; color:#6b7280; }
.dark .jd-empty { color:#9ca3af; }
.jd-skel { animation:jd-pulse 1.5s ease-in-out infinite; background:#e5e7eb; border-radius:.75rem; height:14rem; }
.dark .jd-skel { background:#374151; }
@keyframes jd-pulse { 0%,100%{opacity:1} 50%{opacity:.5} }

.jd-modal { display:none; position:fixed; inset:0; z-index:50; align-items:center; justify-content:center; padding:1rem; background:rgba(0,0,0,.4); }
.jd-modal.is-open { display:flex; }
.jd-modal-panel { background:#fff; border-radius:.75rem; width:100%; max-width:28rem; padding:1.5rem; box-shadow:0 25px 50px -12px rgba(0,0,0,.25); }
.dark .jd-modal-panel { background:#1f2937; }
.jd-modal-panel h3 { margin:0 0 .5rem; font-size:1.125rem; font-weight:600; color:#111827; }
.dark .jd-modal-panel h3 { color:#fff; }
.jd-modal-panel p { margin:0 0 1rem; font-size:.875rem; color:#4b5563; }
.dark .jd-modal-panel p { color:#9ca3af; }
</style>

@include('partials.job-seeker-navbar')

<main class="jd-main">
    <div class="jd-wrap">
        <div class="jd-stack">
            <div class="jd-panel">
                <h2 class="jd-title">Job Discovery</h2>

                <div class="jd-search-row">
                    <div class="jd-field">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        <input type="text" id="jd-q" placeholder="Job title, keywords, or company" value="{{ $initialQuery }}">
                    </div>
                    <div class="jd-field jd-field-loc">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        <input type="text" id="jd-loc" placeholder="Location" value="{{ $initialLocation }}">
                    </div>
                    <button type="button" class="jd-search-btn" id="jd-search-btn" onclick="jdSearch()">Search</button>
                </div>

                <div class="jd-tools">
                    <button type="button" class="jd-tool" id="jd-adv-toggle" onclick="jdToggleAdv()">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                        <span>Advanced Filters</span>
                    </button>
                    <button type="button" class="jd-tool" id="jd-alert-btn" onclick="jdCreateAlert()">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                        <span id="jd-alert-label">Create Alert</span>
                    </button>
                </div>

                <div class="jd-adv" id="jd-adv">
                    <div class="jd-adv-grid">
                        <div>
                            <label for="jd-type">Job Type</label>
                            <select id="jd-type">
                                <option value="">All Types</option>
                                <option value="full-time">Full Time</option>
                                <option value="part-time">Part Time</option>
                                <option value="contract">Contract</option>
                                <option value="internship">Internship</option>
                            </select>
                        </div>
                        <div>
                            <label for="jd-exp">Experience Level</label>
                            <select id="jd-exp">
                                <option value="">All Levels</option>
                                <option value="entry">Entry Level</option>
                                <option value="mid">Mid Level</option>
                                <option value="senior">Senior Level</option>
                            </select>
                        </div>
                        <div>
                            <label for="jd-salary">Salary Range</label>
                            <select id="jd-salary">
                                <option value="">Any Salary</option>
                                <option value="10000">SCR 10,000+</option>
                                <option value="15000">SCR 15,000+</option>
                                <option value="20000">SCR 20,000+</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="jd-tabs">
                <button type="button" class="jd-tab {{ $initialTab === 'discover' ? 'is-active' : '' }}" data-tab="discover" onclick="jdSetTab('discover')">Recommended (<span id="jd-rec-count">0</span>)</button>
                <button type="button" class="jd-tab {{ $initialTab === 'saved' ? 'is-active' : '' }}" data-tab="saved" onclick="jdSetTab('saved')">Saved Jobs (<span id="jd-saved-count">0</span>)</button>
            </div>

            <div id="jd-grid" class="jd-grid"></div>
            <div id="jd-empty" class="jd-empty" style="display:none;"></div>
        </div>
    </div>
</main>

<div id="jd-alert-modal" class="jd-modal" role="dialog">
    <div class="jd-modal-panel">
        <h3>Job alert</h3>
        <p id="jd-alert-msg">We’ll notify you when new jobs match your preferences.</p>
        <div style="display:flex;gap:.75rem;justify-content:flex-end;">
            <button type="button" class="jd-tool" onclick="jdCloseAlertModal()">Close</button>
            <button type="button" class="jd-search-btn" id="jd-alert-confirm" onclick="jdConfirmAlert()">Enable alerts</button>
        </div>
    </div>
</div>

<script>
(function () {
    var API = '/api';
    var csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';
    var tab = @json($initialTab);
    var recommended = [];
    var saved = [];
    var alertsOn = false;
    var loading = false;

    function headers(json) {
        var h = {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': csrf
        };
        if (json) h['Content-Type'] = 'application/json';
        return h;
    }

    function toastOk(m) { if (window.showSuccessToast) window.showSuccessToast(m); else alert(m); }
    function toastErr(m) { if (window.showErrorToast) window.showErrorToast(m); else alert(m); }

    function esc(s) {
        return String(s == null ? '' : s)
            .replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    }

    function logoUrl(job) {
        var u = job.employer?.logo_url || job.company?.logo || job.company?.logo_url || '';
        if (!u) return '';
        if (u.indexOf('http') === 0) return u;
        return '/storage/' + String(u).replace(/^\/?storage\//, '');
    }

    function salaryText(job) {
        if (job.hide_salary) return 'Salary not disclosed';
        var cur = job.salary_currency || job.currency || 'SCR';
        var min = job.salary_min, max = job.salary_max;
        if (min && max) return cur + ' ' + Number(min).toLocaleString() + ' – ' + Number(max).toLocaleString();
        if (min) return cur + ' ' + Number(min).toLocaleString() + '+';
        if (max) return 'Up to ' + cur + ' ' + Number(max).toLocaleString();
        return 'Salary negotiable';
    }

    function postedText(job) {
        if (!job.created_at) return 'Recently';
        var d = new Date(job.created_at);
        if (isNaN(d.getTime())) return 'Recently';
        var diff = (Date.now() - d.getTime()) / 1000;
        if (diff < 3600) return Math.max(1, Math.floor(diff / 60)) + ' min ago';
        if (diff < 86400) return Math.floor(diff / 3600) + ' hours ago';
        if (diff < 604800) return Math.floor(diff / 86400) + ' days ago';
        return d.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
    }

    function tags(job) {
        var list = [];
        if (job.category?.name) list.push(job.category.name);
        if (job.job_type?.name) list.push(job.job_type.name);
        if (job.experience_level) list.push(job.experience_level);
        if (job.work_environment) list.push(job.work_environment);
        if (Array.isArray(job.skill_tags)) list = list.concat(job.skill_tags);
        // unique
        var seen = {};
        return list.filter(function (t) {
            var k = String(t).toLowerCase();
            if (!t || seen[k]) return false;
            seen[k] = true;
            return true;
        });
    }

    function isExpiring(job) {
        if (!job.expiry_date) return false;
        var d = new Date(job.expiry_date);
        if (isNaN(d.getTime())) return false;
        var days = (d.getTime() - Date.now()) / 86400000;
        return days >= 0 && days <= 7;
    }

    function expiryLabel(job) {
        if (!job.expiry_date) return '';
        var d = new Date(job.expiry_date);
        return d.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
    }

    function showSkeleton() {
        var grid = document.getElementById('jd-grid');
        var empty = document.getElementById('jd-empty');
        empty.style.display = 'none';
        grid.innerHTML = '';
        for (var i = 0; i < 4; i++) {
            var d = document.createElement('div');
            d.className = 'jd-skel';
            grid.appendChild(d);
        }
    }

    function render() {
        var list = tab === 'saved' ? saved : recommended;
        document.getElementById('jd-rec-count').textContent = String(recommended.length);
        document.getElementById('jd-saved-count').textContent = String(saved.length);

        var grid = document.getElementById('jd-grid');
        var empty = document.getElementById('jd-empty');
        grid.innerHTML = '';

        if (!list.length) {
            empty.style.display = '';
            empty.textContent = tab === 'saved'
                ? 'No saved jobs yet. Heart a job from Recommended to save it here.'
                : 'No jobs found. Try adjusting your search or filters.';
            return;
        }
        empty.style.display = 'none';

        list.forEach(function (job) {
            var logo = logoUrl(job);
            var initial = (job.employer_name || job.company?.name || 'J').charAt(0).toUpperCase();
            var t = tags(job);
            var shown = t.slice(0, 3);
            var more = t.length - 3;
            var savedOn = !!job.is_saved || tab === 'saved';
            var applied = !!job.has_applied;

            var card = document.createElement('div');
            card.className = 'jd-card';
            card.dataset.id = String(job.id);
            card.innerHTML =
                '<div class="jd-card-top">' +
                    '<div class="jd-card-id">' +
                        (logo
                            ? '<img class="jd-logo" src="' + esc(logo) + '" alt="" onerror="this.outerHTML=\'<div class=\\\'jd-logo-fb\\\'>' + esc(initial) + '</div>\'">'
                            : '<div class="jd-logo-fb">' + esc(initial) + '</div>') +
                        '<div style="min-width:0;flex:1;">' +
                            '<h3 class="jd-job-title">' + esc(job.title || 'Job') + '</h3>' +
                            '<p class="jd-co">' + esc(job.employer_name || job.company?.name || 'Company') + '</p>' +
                        '</div>' +
                    '</div>' +
                    '<div class="jd-icon-btns">' +
                        '<button type="button" class="jd-icon-btn' + (savedOn ? ' is-saved' : '') + '" data-action="save" title="' + (savedOn ? 'Unsave' : 'Save') + '" aria-label="Save job">' +
                            '<svg fill="' + (savedOn ? 'currentColor' : 'none') + '" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>' +
                        '</button>' +
                        '<button type="button" class="jd-icon-btn" data-action="share" title="Copy link" aria-label="Share">' +
                            '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/></svg>' +
                        '</button>' +
                    '</div>' +
                '</div>' +
                '<div class="jd-meta">' +
                    (job.location ? '<div class="jd-meta-row"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg><span>' + esc(job.location) + '</span></div>' : '') +
                    '<div class="jd-meta-row"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg><span>' + esc(job.job_type?.name || 'Full Time') + '</span></div>' +
                    '<div class="jd-meta-row"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg><span>' + esc(salaryText(job)) + '</span></div>' +
                '</div>' +
                (shown.length ? (
                    '<div class="jd-tags">' +
                    shown.map(function (x) { return '<span class="jd-tag">' + esc(x) + '</span>'; }).join('') +
                    (more > 0 ? '<span class="jd-tag-more">+' + more + '</span>' : '') +
                    '</div>'
                ) : '') +
                '<div class="jd-foot">' +
                    '<span class="jd-posted">Posted: ' + esc(postedText(job)) + '</span>' +
                    (applied
                        ? '<span class="jd-apply is-applied">Applied</span>'
                        : '<a class="jd-apply" href="/jobs/' + job.id + '/apply">Apply Now</a>') +
                '</div>' +
                (isExpiring(job) ? '<div class="jd-expire">Expiring soon: ' + esc(expiryLabel(job)) + '</div>' : '');

            card.querySelector('[data-action="save"]').addEventListener('click', function () {
                jdToggleSave(job);
            });
            card.querySelector('[data-action="share"]').addEventListener('click', function () {
                jdShare(job);
            });
            grid.appendChild(card);
        });
    }

    async function loadRecommended() {
        var q = document.getElementById('jd-q').value.trim();
        var loc = document.getElementById('jd-loc').value.trim();
        var type = document.getElementById('jd-type').value;
        var exp = document.getElementById('jd-exp').value;
        var salary = document.getElementById('jd-salary').value;

        var params = new URLSearchParams();
        params.set('per_page', '24');
        if (q) params.set('keyword', q);
        if (loc) params.set('location', loc);
        if (type) {
            var typeVariants = [type];
            if (type.indexOf('-') >= 0) typeVariants.push(type.replace(/-/g, '_'));
            if (type.indexOf('_') >= 0) typeVariants.push(type.replace(/_/g, '-'));
            params.set('job_type', typeVariants.join(','));
        }
        if (exp) params.set('experience_tags', exp);
        if (salary) params.set('salary_min', salary);

        var hasFilters = !!(q || loc || type || exp || salary);
        var url = hasFilters
            ? (API + '/jobs/search?' + params.toString())
            : (API + '/job-seeker/recommended-jobs');

        // Fallback: if recommended empty without filters, use published search
        var res = await fetch(url, { credentials: 'include', headers: headers(false) });
        if (res.status === 401 || res.status === 403) {
            window.location.href = '/login';
            return;
        }
        if (!res.ok) throw new Error('Failed to load jobs');
        var data = await res.json();
        recommended = data.data || [];

        if (!hasFilters && recommended.length === 0) {
            var res2 = await fetch(API + '/jobs/search?per_page=24&sort=newest', { credentials: 'include', headers: headers(false) });
            if (res2.ok) {
                var d2 = await res2.json();
                recommended = d2.data || [];
            }
        }
    }

    async function loadSaved() {
        var res = await fetch(API + '/job-seeker/saved-jobs?per_page=50', { credentials: 'include', headers: headers(false) });
        if (res.status === 401 || res.status === 403) {
            window.location.href = '/login';
            return;
        }
        if (!res.ok) throw new Error('Failed to load saved jobs');
        var data = await res.json();
        var rows = data.data || data;
        if (!Array.isArray(rows)) rows = [];
        saved = rows.map(function (row) {
            // paginator: { job: {...} } or already presented job
            if (row.job) {
                var j = row.job;
                // enrich if nested company
                return {
                    id: j.id,
                    title: j.title,
                    employer_name: j.company?.name || j.employer_name,
                    location: j.location,
                    salary_min: j.salary_min,
                    salary_max: j.salary_max,
                    salary_currency: j.currency || j.salary_currency || 'SCR',
                    job_type: { name: j.employment_type || 'Full Time' },
                    category: j.category,
                    created_at: j.created_at || row.created_at,
                    is_saved: true,
                    has_applied: false,
                    employer: {
                        logo_url: j.company?.logo || j.company?.logo_url
                    },
                    company: j.company,
                    experience_level: j.experience_level,
                    work_environment: j.work_environment,
                    expiry_date: j.application_deadline || j.expires_at || null
                };
            }
            row.is_saved = true;
            return row;
        });
    }

    async function loadAlertState() {
        try {
            var res = await fetch(API + '/job-seeker/settings', { credentials: 'include', headers: headers(false) });
            if (!res.ok) return;
            var data = await res.json();
            var s = data.data || data;
            alertsOn = !!(s.job_alerts);
            updateAlertUi();
        } catch (e) {}
    }

    function updateAlertUi() {
        var btn = document.getElementById('jd-alert-btn');
        var label = document.getElementById('jd-alert-label');
        if (alertsOn) {
            btn.classList.add('is-on');
            label.textContent = 'Alerts On';
        } else {
            btn.classList.remove('is-on');
            label.textContent = 'Create Alert';
        }
    }

    async function refresh(showSkel) {
        if (loading) return;
        loading = true;
        var btn = document.getElementById('jd-search-btn');
        btn.disabled = true;
        if (showSkel !== false) showSkeleton();
        try {
            await Promise.all([loadRecommended(), loadSaved()]);
            render();
        } catch (e) {
            console.error(e);
            document.getElementById('jd-grid').innerHTML = '';
            var empty = document.getElementById('jd-empty');
            empty.style.display = '';
            empty.textContent = 'Could not load jobs. Please try again.';
        } finally {
            loading = false;
            btn.disabled = false;
        }
    }

    window.jdSearch = function () {
        tab = 'discover';
        document.querySelectorAll('.jd-tab').forEach(function (el) {
            el.classList.toggle('is-active', el.dataset.tab === 'discover');
        });
        refresh(true);
    };

    window.jdSetTab = function (next) {
        tab = next;
        document.querySelectorAll('.jd-tab').forEach(function (el) {
            el.classList.toggle('is-active', el.dataset.tab === next);
        });
        var url = new URL(window.location.href);
        if (next === 'saved') url.searchParams.set('tab', 'saved');
        else url.searchParams.delete('tab');
        history.replaceState({}, '', url.pathname + (url.search || ''));
        render();
    };

    window.jdToggleAdv = function () {
        var panel = document.getElementById('jd-adv');
        var btn = document.getElementById('jd-adv-toggle');
        var open = !panel.classList.contains('is-open');
        panel.classList.toggle('is-open', open);
        btn.classList.toggle('is-on', open);
    };

    window.jdToggleSave = async function (job) {
        var currentlySaved = !!job.is_saved || saved.some(function (s) { return s.id === job.id; });
        try {
            var res;
            if (currentlySaved) {
                res = await fetch(API + '/job-seeker/saved-jobs/' + job.id, {
                    method: 'DELETE', credentials: 'include', headers: headers(false)
                });
            } else {
                res = await fetch(API + '/job-seeker/saved-jobs', {
                    method: 'POST', credentials: 'include', headers: headers(true),
                    body: JSON.stringify({ job_id: job.id })
                });
            }
            if (!res.ok) {
                var err = await res.json().catch(function () { return {}; });
                toastErr(err.message || 'Could not update saved job');
                return;
            }
            job.is_saved = !currentlySaved;
            // sync lists
            recommended.forEach(function (j) {
                if (j.id === job.id) j.is_saved = job.is_saved;
            });
            if (job.is_saved) {
                if (!saved.some(function (s) { return s.id === job.id; })) {
                    saved.unshift(Object.assign({}, job, { is_saved: true }));
                }
                toastOk('Job saved');
            } else {
                saved = saved.filter(function (s) { return s.id !== job.id; });
                toastOk('Job removed from saved');
            }
            render();
        } catch (e) {
            toastErr('Could not update saved job');
        }
    };

    window.jdShare = async function (job) {
        var url = window.location.origin + '/jobs/' + job.id;
        try {
            if (navigator.clipboard && navigator.clipboard.writeText) {
                await navigator.clipboard.writeText(url);
                toastOk('Job link copied');
            } else {
                prompt('Copy job link:', url);
            }
        } catch (e) {
            prompt('Copy job link:', url);
        }
    };

    window.jdCreateAlert = function () {
        var modal = document.getElementById('jd-alert-modal');
        var msg = document.getElementById('jd-alert-msg');
        var confirm = document.getElementById('jd-alert-confirm');
        if (alertsOn) {
            msg.textContent = 'Job alerts are currently enabled. Disable them?';
            confirm.textContent = 'Disable alerts';
        } else {
            msg.textContent = 'Get notified when new jobs match your search preferences.';
            confirm.textContent = 'Enable alerts';
        }
        modal.classList.add('is-open');
    };

    window.jdCloseAlertModal = function () {
        document.getElementById('jd-alert-modal').classList.remove('is-open');
    };

    window.jdConfirmAlert = async function () {
        var next = !alertsOn;
        try {
            var res = await fetch(API + '/job-seeker/settings', {
                method: 'PUT',
                credentials: 'include',
                headers: headers(true),
                body: JSON.stringify({ job_alerts: next })
            });
            if (!res.ok) {
                toastErr('Could not update alerts');
                return;
            }
            alertsOn = next;
            updateAlertUi();
            jdCloseAlertModal();
            toastOk(next ? 'Job alerts enabled' : 'Job alerts disabled');
        } catch (e) {
            toastErr('Could not update alerts');
        }
    };

    document.getElementById('jd-alert-modal').addEventListener('click', function (e) {
        if (e.target === this) jdCloseAlertModal();
    });

    ['jd-q', 'jd-loc'].forEach(function (id) {
        document.getElementById(id).addEventListener('keydown', function (e) {
            if (e.key === 'Enter') jdSearch();
        });
    });
    ['jd-type', 'jd-exp', 'jd-salary'].forEach(function (id) {
        document.getElementById(id).addEventListener('change', function () {
            if (tab === 'discover') jdSearch();
        });
    });

    loadAlertState();
    refresh(true);
})();
</script>
@endsection
