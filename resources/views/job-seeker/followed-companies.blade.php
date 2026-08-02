@extends('layouts.job-seeker')

@section('content')
<style>
.ce-main { flex:1; overflow-y:auto; }
.ce-wrap { max-width:80rem; margin:0 auto; padding:2rem 1rem; }
@media (min-width:640px){ .ce-wrap{ padding-left:1.5rem; padding-right:1.5rem; } }
@media (min-width:1024px){ .ce-wrap{ padding-left:2rem; padding-right:2rem; } }
.ce-stack { display:flex; flex-direction:column; gap:1.5rem; }
.ce-panel { background:#fff; border-radius:.75rem; padding:1.5rem; box-shadow:0 1px 2px rgba(0,0,0,.04); border:1px solid #e5e7eb; }
.dark .ce-panel { background:#1f2937; border-color:#374151; }
.ce-title { margin:0 0 .5rem; font-size:1.5rem; font-weight:700; color:#111827; }
.dark .ce-title { color:#fff; }
.ce-sub { margin:0; color:#4b5563; }
.dark .ce-sub { color:#9ca3af; }

.ce-stats { display:grid; grid-template-columns:1fr; gap:1.5rem; }
@media (min-width:768px){ .ce-stats{ grid-template-columns:repeat(3,minmax(0,1fr)); } }
.ce-stat { display:flex; align-items:center; gap:.75rem; }
.ce-stat-icon { padding:.75rem; border-radius:.5rem; display:flex; }
.ce-stat-icon svg { width:1.5rem; height:1.5rem; }
.ce-stat-label { margin:0; font-size:.875rem; color:#4b5563; }
.dark .ce-stat-label { color:#9ca3af; }
.ce-stat-val { margin:0; font-size:1.5rem; font-weight:700; color:#111827; }
.dark .ce-stat-val { color:#fff; }

.ce-head { display:flex; align-items:center; justify-content:space-between; gap:1rem; margin-bottom:1.5rem; flex-wrap:wrap; }
.ce-h3 { margin:0; font-size:1.125rem; font-weight:600; color:#111827; }
.dark .ce-h3 { color:#fff; }
.ce-link { font-size:.875rem; color:#2563eb; text-decoration:none; background:none; border:0; cursor:pointer; padding:0; }
.ce-link:hover { text-decoration:underline; }
.dark .ce-link { color:#22d3ee; }

.ce-grid { display:grid; grid-template-columns:1fr; gap:1rem; }
@media (min-width:768px){ .ce-grid{ grid-template-columns:repeat(2,minmax(0,1fr)); } }
@media (min-width:1024px){ .ce-grid{ grid-template-columns:repeat(3,minmax(0,1fr)); } }

.ce-card {
    padding:1rem; border-radius:.75rem; border:1px solid #e5e7eb; transition:box-shadow .15s;
    background:#fff;
}
.dark .ce-card { border-color:#374151; background:#1f2937; }
.ce-card:hover { box-shadow:0 10px 15px -3px rgba(0,0,0,.08); }
.ce-card.is-selected { border-color:#2563eb; box-shadow:0 0 0 1px #2563eb; }
.dark .ce-card.is-selected { border-color:#22d3ee; box-shadow:0 0 0 1px #22d3ee; }
.ce-card-top { display:flex; align-items:flex-start; justify-content:space-between; margin-bottom:.75rem; }
.ce-logo {
    width:3.5rem; height:3.5rem; border-radius:.5rem; object-fit:cover;
    background:linear-gradient(to bottom right,#2563eb,#06b6d4);
}
.ce-logo-fb {
    width:3.5rem; height:3.5rem; border-radius:.5rem;
    display:flex; align-items:center; justify-content:center;
    background:linear-gradient(to bottom right,#2563eb,#06b6d4);
    color:#fff; font-size:1.5rem; font-weight:700;
}
.ce-bell {
    padding:.5rem; border:0; background:transparent; border-radius:.5rem; cursor:pointer; color:#2563eb;
}
.dark .ce-bell { color:#22d3ee; }
.ce-bell:hover { background:#f3f4f6; }
.dark .ce-bell:hover { background:#374151; }
.ce-bell svg { width:1rem; height:1rem; display:block; }
.ce-name { margin:0 0 .25rem; font-size:1rem; font-weight:600; color:#111827; }
.dark .ce-name { color:#fff; }
.ce-card:hover .ce-name { color:#2563eb; }
.dark .ce-card:hover .ce-name { color:#22d3ee; }
.ce-ind { margin:0 0 .75rem; font-size:.875rem; color:#4b5563; }
.dark .ce-ind { color:#9ca3af; }
.ce-meta { display:flex; align-items:center; gap:1rem; margin-bottom:.75rem; font-size:.875rem; color:#4b5563; }
.dark .ce-meta { color:#9ca3af; }
.ce-meta-item { display:flex; align-items:center; gap:.25rem; }
.ce-meta-item svg { width:1rem; height:1rem; }
.ce-foot {
    display:flex; align-items:center; justify-content:space-between; gap:.75rem;
    padding-top:.75rem; border-top:1px solid #e5e7eb;
}
.dark .ce-foot { border-color:#374151; }
.ce-jobs { font-size:.875rem; font-weight:500; color:#2563eb; }
.dark .ce-jobs { color:#22d3ee; }
.ce-view {
    font-size:.875rem; color:#4b5563; background:none; border:0; cursor:pointer; text-decoration:none;
}
.dark .ce-view { color:#9ca3af; }
.ce-view:hover { color:#111827; }
.dark .ce-view:hover { color:#fff; }
.ce-check { display:flex; align-items:center; gap:.5rem; margin-top:.75rem; font-size:.75rem; color:#6b7280; }
.ce-check input { accent-color:#2563eb; }

.ce-btn {
    padding:.5rem 1rem; border:0; border-radius:.375rem; color:#fff; font-weight:500; cursor:pointer;
    background:linear-gradient(to right,#2563eb,#06b6d4);
}
.ce-btn:hover { box-shadow:0 10px 15px -3px rgba(37,99,235,.3); }
.ce-btn:disabled { opacity:.5; cursor:not-allowed; box-shadow:none; }

.ce-empty { text-align:center; padding:2.5rem 1rem; color:#6b7280; }
.dark .ce-empty { color:#9ca3af; }
.ce-skel { animation:ce-pulse 1.5s ease-in-out infinite; background:#e5e7eb; border-radius:.75rem; height:11rem; }
.dark .ce-skel { background:#374151; }
@keyframes ce-pulse { 0%,100%{opacity:1} 50%{opacity:.5} }

.ce-modal { display:none; position:fixed; inset:0; z-index:50; align-items:center; justify-content:center; padding:1rem; background:rgba(0,0,0,.4); }
.ce-modal.is-open { display:flex; }
.ce-modal-panel { background:#fff; border-radius:.75rem; width:100%; max-width:48rem; max-height:90vh; overflow:auto; padding:1.5rem; box-shadow:0 25px 50px -12px rgba(0,0,0,.25); }
.dark .ce-modal-panel { background:#1f2937; }
.ce-modal-sm { max-width:28rem; }
.ce-modal-panel h3 { margin:0 0 .75rem; font-size:1.125rem; font-weight:600; color:#111827; }
.dark .ce-modal-panel h3 { color:#fff; }
.ce-compare-table { width:100%; border-collapse:collapse; font-size:.875rem; }
.ce-compare-table th, .ce-compare-table td { border-bottom:1px solid #e5e7eb; padding:.75rem; text-align:left; vertical-align:top; }
.dark .ce-compare-table th, .dark .ce-compare-table td { border-color:#374151; color:#d1d5db; }
.ce-compare-table th { color:#111827; font-weight:600; }
.dark .ce-compare-table th { color:#fff; }
</style>

@include('partials.job-seeker-navbar')

<main class="ce-main">
    <div class="ce-wrap">
        <div class="ce-stack">
            <div class="ce-panel">
                <h2 class="ce-title">Company Engagement</h2>
                <p class="ce-sub">Follow companies to get updates about new job openings and company news</p>
            </div>

            <div class="ce-stats">
                <div class="ce-panel">
                    <div class="ce-stat">
                        <div class="ce-stat-icon" style="background:#dbeafe;color:#2563eb;">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        </div>
                        <div>
                            <p class="ce-stat-label">Following</p>
                            <p class="ce-stat-val" id="ce-following">0</p>
                        </div>
                    </div>
                </div>
                <div class="ce-panel">
                    <div class="ce-stat">
                        <div class="ce-stat-icon" style="background:#dcfce7;color:#16a34a;">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                        </div>
                        <div>
                            <p class="ce-stat-label">New Openings</p>
                            <p class="ce-stat-val" id="ce-openings">0</p>
                        </div>
                    </div>
                </div>
                <div class="ce-panel">
                    <div class="ce-stat">
                        <div class="ce-stat-icon" style="background:#f3e8ff;color:#9333ea;">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        </div>
                        <div>
                            <p class="ce-stat-label">Profile Views</p>
                            <p class="ce-stat-val" id="ce-views">0</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="ce-panel">
                <div class="ce-head">
                    <h3 class="ce-h3">Followed Companies</h3>
                    <a href="{{ url('/companies') }}" class="ce-link">Browse All Companies</a>
                </div>
                <div id="ce-grid" class="ce-grid"></div>
                <div id="ce-empty" class="ce-empty" style="display:none;"></div>
            </div>

            <div class="ce-panel">
                <h3 class="ce-h3" style="margin-bottom:1rem;">Compare Companies</h3>
                <p class="ce-sub" style="margin-bottom:1rem;">Select companies to compare their culture, benefits, and salary ranges</p>
                <button type="button" class="ce-btn" id="ce-compare-btn" onclick="ceStartCompare()" disabled>Start Comparison</button>
            </div>
        </div>
    </div>
</main>

<div id="ce-unfollow-modal" class="ce-modal" role="dialog">
    <div class="ce-modal-panel ce-modal-sm">
        <h3>Unfollow company?</h3>
        <p class="ce-sub" id="ce-unfollow-msg" style="margin-bottom:1.25rem;">You will stop receiving updates from this company.</p>
        <div style="display:flex;gap:.75rem;justify-content:flex-end;">
            <button type="button" class="ce-link" onclick="ceCloseUnfollow()">Cancel</button>
            <button type="button" class="ce-btn" style="background:#dc2626;" id="ce-unfollow-confirm" onclick="ceConfirmUnfollow()">Unfollow</button>
        </div>
    </div>
</div>

<div id="ce-compare-modal" class="ce-modal" role="dialog">
    <div class="ce-modal-panel">
        <div style="display:flex;justify-content:space-between;align-items:center;gap:1rem;margin-bottom:1rem;">
            <h3 style="margin:0;">Company comparison</h3>
            <button type="button" class="ce-link" onclick="ceCloseCompare()">Close</button>
        </div>
        <div id="ce-compare-body"></div>
    </div>
</div>

<script>
(function () {
    var API = '/api';
    var csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';
    var companies = [];
    var selected = {};
    var unfollowId = null;

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

    function updateCompareBtn() {
        var n = Object.keys(selected).filter(function (k) { return selected[k]; }).length;
        var btn = document.getElementById('ce-compare-btn');
        btn.disabled = n < 2;
        btn.textContent = n < 2 ? 'Start Comparison' : ('Start Comparison (' + n + ')');
    }

    function showSkeleton() {
        var grid = document.getElementById('ce-grid');
        var empty = document.getElementById('ce-empty');
        empty.style.display = 'none';
        grid.innerHTML = '';
        for (var i = 0; i < 3; i++) {
            var d = document.createElement('div');
            d.className = 'ce-skel';
            grid.appendChild(d);
        }
    }

    function render() {
        var grid = document.getElementById('ce-grid');
        var empty = document.getElementById('ce-empty');
        grid.innerHTML = '';

        if (!companies.length) {
            empty.style.display = '';
            empty.innerHTML = 'You are not following any companies yet.<br><a class="ce-link" href="/companies">Browse companies</a> to follow them.';
            updateCompareBtn();
            return;
        }
        empty.style.display = 'none';

        companies.forEach(function (c) {
            var initial = (c.name || 'C').charAt(0).toUpperCase();
            var rating = c.rating != null ? c.rating : 'N/A';
            var size = c.size || 'N/A';
            var isSel = !!selected[c.company_id];

            var card = document.createElement('div');
            card.className = 'ce-card' + (isSel ? ' is-selected' : '');
            card.dataset.id = String(c.company_id);
            card.innerHTML =
                '<div class="ce-card-top">' +
                    (c.logo
                        ? '<img class="ce-logo" src="' + esc(c.logo) + '" alt="" onerror="this.outerHTML=\'<div class=\\\'ce-logo-fb\\\'>' + esc(initial) + '</div>\'">'
                        : '<div class="ce-logo-fb">' + esc(initial) + '</div>') +
                    '<button type="button" class="ce-bell" data-action="unfollow" title="Unfollow" aria-label="Unfollow">' +
                        '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>' +
                    '</button>' +
                '</div>' +
                '<h4 class="ce-name">' + esc(c.name) + '</h4>' +
                '<p class="ce-ind">' + esc(c.industry || 'Industry not set') + '</p>' +
                '<div class="ce-meta">' +
                    '<div class="ce-meta-item"><svg style="color:#eab308;" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg><span>' + esc(rating) + '</span></div>' +
                    '<div class="ce-meta-item"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg><span>' + esc(size) + '</span></div>' +
                '</div>' +
                '<div class="ce-foot">' +
                    '<span class="ce-jobs">' + esc(c.jobs_count) + ' open positions</span>' +
                    '<a class="ce-view" href="' + esc(c.url) + '">View</a>' +
                '</div>' +
                '<label class="ce-check"><input type="checkbox" data-action="select"' + (isSel ? ' checked' : '') + '> Compare</label>';

            card.querySelector('[data-action="unfollow"]').addEventListener('click', function () {
                ceAskUnfollow(c);
            });
            card.querySelector('[data-action="select"]').addEventListener('change', function (e) {
                if (e.target.checked) selected[c.company_id] = true;
                else delete selected[c.company_id];
                card.classList.toggle('is-selected', !!selected[c.company_id]);
                updateCompareBtn();
            });
            grid.appendChild(card);
        });
        updateCompareBtn();
    }

    async function load() {
        showSkeleton();
        try {
            var res = await fetch(API + '/job-seeker/followed-companies?per_page=50', {
                credentials: 'include',
                headers: headers(false)
            });
            if (res.status === 401 || res.status === 403) {
                window.location.href = '/login';
                return;
            }
            if (!res.ok) throw new Error('Failed');
            var data = await res.json();
            companies = data.data || [];
            var stats = data.stats || {};
            document.getElementById('ce-following').textContent = String(stats.following != null ? stats.following : companies.length);
            document.getElementById('ce-openings').textContent = String(stats.new_openings != null ? stats.new_openings : 0);
            document.getElementById('ce-views').textContent = String(stats.profile_views != null ? stats.profile_views : 0);
            // drop selections for removed companies
            Object.keys(selected).forEach(function (id) {
                if (!companies.some(function (c) { return String(c.company_id) === String(id); })) {
                    delete selected[id];
                }
            });
            render();
        } catch (e) {
            document.getElementById('ce-grid').innerHTML = '';
            var empty = document.getElementById('ce-empty');
            empty.style.display = '';
            empty.textContent = 'Could not load followed companies. Please try again.';
        }
    }

    window.ceAskUnfollow = function (c) {
        unfollowId = c.company_id;
        document.getElementById('ce-unfollow-msg').textContent = 'Stop following ' + (c.name || 'this company') + '? You can follow them again later.';
        document.getElementById('ce-unfollow-modal').classList.add('is-open');
    };

    window.ceCloseUnfollow = function () {
        document.getElementById('ce-unfollow-modal').classList.remove('is-open');
        unfollowId = null;
    };

    window.ceConfirmUnfollow = async function () {
        if (!unfollowId) return;
        var id = unfollowId;
        var btn = document.getElementById('ce-unfollow-confirm');
        btn.disabled = true;
        try {
            var res = await fetch(API + '/job-seeker/followed-companies/' + id, {
                method: 'DELETE',
                credentials: 'include',
                headers: headers(false)
            });
            var data = await res.json().catch(function () { return {}; });
            if (!res.ok) {
                toastErr(data.message || 'Failed to unfollow');
                return;
            }
            toastOk('Company unfollowed');
            delete selected[id];
            ceCloseUnfollow();
            await load();
        } catch (e) {
            toastErr('An error occurred while unfollowing.');
        } finally {
            btn.disabled = false;
        }
    };

    window.ceStartCompare = function () {
        var ids = Object.keys(selected).filter(function (k) { return selected[k]; }).map(Number);
        var list = companies.filter(function (c) { return ids.indexOf(c.company_id) >= 0; });
        if (list.length < 2) {
            toastErr('Select at least 2 companies to compare');
            return;
        }

        var fields = [
            { key: 'industry', label: 'Industry' },
            { key: 'size', label: 'Company size' },
            { key: 'location', label: 'Location' },
            { key: 'rating', label: 'Rating' },
            { key: 'jobs_count', label: 'Open positions' },
            { key: 'website', label: 'Website' }
        ];

        var html = '<table class="ce-compare-table"><thead><tr><th></th>';
        list.forEach(function (c) {
            html += '<th>' + esc(c.name) + '</th>';
        });
        html += '</tr></thead><tbody>';
        fields.forEach(function (f) {
            html += '<tr><th>' + esc(f.label) + '</th>';
            list.forEach(function (c) {
                var v = c[f.key];
                if (v == null || v === '') v = '—';
                html += '<td>' + esc(v) + '</td>';
            });
            html += '</tr>';
        });
        html += '</tbody></table>';

        document.getElementById('ce-compare-body').innerHTML = html;
        document.getElementById('ce-compare-modal').classList.add('is-open');
    };

    window.ceCloseCompare = function () {
        document.getElementById('ce-compare-modal').classList.remove('is-open');
    };

    ['ce-unfollow-modal', 'ce-compare-modal'].forEach(function (id) {
        document.getElementById(id).addEventListener('click', function (e) {
            if (e.target === this) this.classList.remove('is-open');
        });
    });

    load();
})();
</script>
@endsection
