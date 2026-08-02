@extends('layouts.job-seeker')

@section('content')
<style>
.nf-main { flex:1; overflow-y:auto; }
.nf-wrap { max-width:80rem; margin:0 auto; padding:2rem 1rem; }
@media (min-width:640px){ .nf-wrap{ padding-left:1.5rem; padding-right:1.5rem; } }
@media (min-width:1024px){ .nf-wrap{ padding-left:2rem; padding-right:2rem; } }
.nf-stack { display:flex; flex-direction:column; gap:1.5rem; }
.nf-panel { background:#fff; border-radius:.75rem; padding:1.5rem; box-shadow:0 1px 2px rgba(0,0,0,.04); border:1px solid #e5e7eb; }
.dark .nf-panel { background:#1f2937; border-color:#374151; }
.nf-head { display:flex; align-items:center; justify-content:space-between; gap:1rem; flex-wrap:wrap; }
.nf-title { margin:0; font-size:1.5rem; font-weight:700; color:#111827; }
.dark .nf-title { color:#fff; }
.nf-sub { margin:.25rem 0 0; color:#4b5563; }
.dark .nf-sub { color:#9ca3af; }
.nf-markall {
    display:inline-flex; align-items:center; gap:.5rem; padding:.5rem 1rem; border:0; border-radius:.5rem;
    background:transparent; color:#2563eb; font-weight:500; cursor:pointer;
}
.dark .nf-markall { color:#22d3ee; }
.nf-markall:hover { background:#eff6ff; }
.dark .nf-markall:hover { background:rgba(30,58,138,.2); }
.nf-markall svg { width:1rem; height:1rem; }

.nf-list { display:flex; flex-direction:column; gap:.75rem; }
.nf-card {
    background:#fff; border-radius:.75rem; padding:1.25rem; box-shadow:0 1px 2px rgba(0,0,0,.04);
    border:1px solid #e5e7eb; transition:box-shadow .15s;
}
.dark .nf-card { background:#1f2937; border-color:#374151; }
.nf-card:hover { box-shadow:0 4px 6px -1px rgba(0,0,0,.08); }
.nf-card.is-unread { border-color:#bfdbfe; background:rgba(239,246,255,.45); }
.dark .nf-card.is-unread { border-color:#1e3a8a; background:rgba(30,58,138,.12); }
.nf-row { display:flex; align-items:flex-start; gap:1rem; }
.nf-icon { padding:.75rem; border-radius:.5rem; flex-shrink:0; display:flex; }
.nf-icon svg { width:1.25rem; height:1.25rem; }
.nf-body { flex:1; min-width:0; }
.nf-top { display:flex; align-items:flex-start; justify-content:space-between; gap:.75rem; margin-bottom:.25rem; }
.nf-card-title { margin:0; font-weight:600; color:#111827; }
.dark .nf-card-title { color:#fff; }
.nf-dot { width:.5rem; height:.5rem; border-radius:9999px; background:#2563eb; flex-shrink:0; margin-top:.375rem; }
.nf-msg { margin:0 0 .5rem; font-size:.875rem; color:#4b5563; }
.dark .nf-msg { color:#9ca3af; }
.nf-foot { display:flex; align-items:center; justify-content:space-between; gap:.75rem; flex-wrap:wrap; }
.nf-time { font-size:.75rem; color:#6b7280; }
.nf-actions { display:flex; align-items:center; gap:.75rem; }
.nf-link { font-size:.875rem; color:#2563eb; background:none; border:0; cursor:pointer; padding:0; text-decoration:none; }
.dark .nf-link { color:#22d3ee; }
.nf-link:hover { text-decoration:underline; }
.nf-muted { font-size:.875rem; color:#4b5563; background:none; border:0; cursor:pointer; padding:0; }
.dark .nf-muted { color:#9ca3af; }
.nf-muted:hover { color:#111827; }
.dark .nf-muted:hover { color:#fff; }

.nf-empty { text-align:center; padding:3rem 1.5rem; }
.nf-empty svg { width:3rem; height:3rem; color:#9ca3af; margin:0 auto .75rem; }
.nf-empty h3 { margin:0 0 .5rem; font-size:1.125rem; font-weight:600; color:#111827; }
.dark .nf-empty h3 { color:#fff; }
.nf-empty p { margin:0; color:#4b5563; }
.dark .nf-empty p { color:#9ca3af; }

.nf-skel { animation:nf-pulse 1.5s ease-in-out infinite; background:#e5e7eb; border-radius:.75rem; height:5.5rem; }
.dark .nf-skel { background:#374151; }
@keyframes nf-pulse { 0%,100%{opacity:1} 50%{opacity:.5} }
</style>

@include('partials.job-seeker-navbar')

<main class="nf-main">
    <div class="nf-wrap">
        <div class="nf-stack">
            <div class="nf-panel">
                <div class="nf-head">
                    <div>
                        <h2 class="nf-title">Notifications</h2>
                        <p class="nf-sub" id="notif-unread-text">You have <span id="notif-unread-num">0</span> unread notifications</p>
                    </div>
                    <button type="button" class="nf-markall" id="nf-mark-all" onclick="nfMarkAllRead()">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Mark all as read
                    </button>
                </div>
            </div>

            <div id="nf-list" class="nf-list"></div>
            <div id="nf-empty" class="nf-panel nf-empty" style="display:none;">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                <h3>No notifications yet</h3>
                <p>We'll notify you when something important happens</p>
            </div>
        </div>
    </div>
</main>

<script>
(function () {
    var csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';
    var items = [];

    function esc(s) {
        return String(s == null ? '' : s)
            .replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    }

    function timeAgo(dateString) {
        var date = new Date(dateString);
        var diff = Math.floor((Date.now() - date.getTime()) / 1000);
        if (isNaN(diff) || diff < 60) return 'Just now';
        if (diff < 3600) return Math.floor(diff / 60) + ' minutes ago';
        if (diff < 86400) return Math.floor(diff / 3600) + ' hours ago';
        if (diff < 604800) return Math.floor(diff / 86400) + ' days ago';
        return date.toLocaleDateString();
    }

    function styleFor(type) {
        var t = String(type || '').toLowerCase();
        if (t.indexOf('job') >= 0 || t.indexOf('alert') >= 0) {
            return { color: '#16a34a', bg: '#dcfce7', icon: 'bell' };
        }
        if (t.indexOf('interview') >= 0 || t.indexOf('message') >= 0 || t.indexOf('recruiter') >= 0) {
            return { color: '#9333ea', bg: '#f3e8ff', icon: 'chat' };
        }
        if (t.indexOf('expir') >= 0 || t.indexOf('reject') >= 0) {
            return { color: '#dc2626', bg: '#fee2e2', icon: 'clock' };
        }
        if (t.indexOf('company') >= 0 || t.indexOf('follow') >= 0) {
            return { color: '#ca8a04', bg: '#fef9c3', icon: 'building' };
        }
        return { color: '#2563eb', bg: '#dbeafe', icon: 'briefcase' };
    }

    function iconSvg(kind) {
        if (kind === 'bell') return '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>';
        if (kind === 'chat') return '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>';
        if (kind === 'clock') return '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>';
        if (kind === 'building') return '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>';
        return '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>';
    }

    function viewUrl(n) {
        var d = n.data || {};
        if (d.job_id) return '/jobs/' + d.job_id;
        if (d.application_id) return '/job-seeker/applications/' + d.application_id;
        if (d.company_slug) return '/companies/' + d.company_slug;
        if (d.company_id) return '/companies';
        return '/job-seeker/applications';
    }

    function setUnread(n) {
        var el = document.getElementById('notif-unread-num');
        var text = document.getElementById('notif-unread-text');
        if (el) el.textContent = String(n);
        if (text) text.textContent = 'You have ' + n + ' unread notification' + (n !== 1 ? 's' : '');
    }

    function showSkeleton() {
        var list = document.getElementById('nf-list');
        var empty = document.getElementById('nf-empty');
        empty.style.display = 'none';
        list.innerHTML = '';
        for (var i = 0; i < 4; i++) {
            var d = document.createElement('div');
            d.className = 'nf-skel';
            list.appendChild(d);
        }
    }

    function render() {
        var list = document.getElementById('nf-list');
        var empty = document.getElementById('nf-empty');
        list.innerHTML = '';

        if (!items.length) {
            empty.style.display = '';
            return;
        }
        empty.style.display = 'none';

        items.forEach(function (n) {
            var unread = !(n.is_read || n.read);
            var st = styleFor(n.type);
            var card = document.createElement('div');
            card.className = 'nf-card' + (unread ? ' is-unread' : '');
            card.dataset.id = String(n.id);
            card.innerHTML =
                '<div class="nf-row">' +
                    '<div class="nf-icon" style="background:' + st.bg + ';color:' + st.color + ';">' +
                        '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24">' + iconSvg(st.icon) + '</svg>' +
                    '</div>' +
                    '<div class="nf-body">' +
                        '<div class="nf-top">' +
                            '<h3 class="nf-card-title">' + esc(n.title) + '</h3>' +
                            (unread ? '<span class="nf-dot"></span>' : '') +
                        '</div>' +
                        '<p class="nf-msg">' + esc(n.message || n.body || '') + '</p>' +
                        '<div class="nf-foot">' +
                            '<span class="nf-time">' + esc(timeAgo(n.created_at)) + '</span>' +
                            '<div class="nf-actions">' +
                                '<a class="nf-link" href="' + esc(viewUrl(n)) + '">View</a>' +
                                (unread ? '<button type="button" class="nf-muted" data-action="read">Mark as read</button>' : '') +
                            '</div>' +
                        '</div>' +
                    '</div>' +
                '</div>';

            var btn = card.querySelector('[data-action="read"]');
            if (btn) {
                btn.addEventListener('click', function () { nfMarkRead(n.id); });
            }
            list.appendChild(card);
        });
    }

    async function load() {
        showSkeleton();
        try {
            var res = await fetch('/api/notifications?limit=50', {
                credentials: 'include',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrf
                }
            });
            if (res.status === 401 || res.status === 403) {
                window.location.href = '/login';
                return;
            }
            if (!res.ok) throw new Error('fail');
            var data = await res.json();
            items = data.notifications || data.data || [];
            setUnread(data.unread_count != null ? data.unread_count : items.filter(function (x) { return !(x.is_read || x.read); }).length);
            render();
        } catch (e) {
            document.getElementById('nf-list').innerHTML = '';
            var empty = document.getElementById('nf-empty');
            empty.style.display = '';
            empty.querySelector('h3').textContent = 'Could not load notifications';
            empty.querySelector('p').textContent = 'Please refresh and try again.';
        }
    }

    window.nfMarkRead = async function (id) {
        try {
            var res = await fetch('/api/notifications/' + id + '/read', {
                method: 'PUT',
                credentials: 'include',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrf
                }
            });
            if (!res.ok) return;
            var data = await res.json();
            items = items.map(function (n) {
                if (String(n.id) === String(id)) {
                    n.is_read = true;
                    n.read = true;
                }
                return n;
            });
            setUnread(data.unread_count != null ? data.unread_count : items.filter(function (x) { return !(x.is_read || x.read); }).length);
            if (window.JobHubNotifications) window.JobHubNotifications.refresh();
            render();
        } catch (e) {}
    };

    window.nfMarkAllRead = async function () {
        try {
            var res = await fetch('/api/notifications/mark-all-read', {
                method: 'PUT',
                credentials: 'include',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrf
                }
            });
            if (!res.ok) return;
            items = items.map(function (n) {
                n.is_read = true;
                n.read = true;
                return n;
            });
            setUnread(0);
            if (window.JobHubNotifications) window.JobHubNotifications.refresh();
            render();
        } catch (e) {}
    };

    window.addEventListener('jobhub:notification-new', function (e) {
        var payload = e.detail || {};
        if (payload.id) {
            items.unshift({
                id: payload.id,
                type: payload.type || 'alert',
                title: payload.title || 'Notification',
                message: payload.message || payload.body || '',
                body: payload.message || payload.body || '',
                is_read: false,
                read: false,
                data: payload.data || {},
                created_at: payload.created_at || new Date().toISOString()
            });
            render();
        } else {
            load();
        }
    });

    window.addEventListener('jobhub:notifications-all-read', function () {
        items = items.map(function (n) { n.is_read = true; n.read = true; return n; });
        setUnread(0);
        render();
    });

    window.addEventListener('jobhub:notifications-updated', function (e) {
        if (e.detail && typeof e.detail.unreadCount === 'number') {
            setUnread(e.detail.unreadCount);
        }
    });

    load();
})();
</script>
@endsection
