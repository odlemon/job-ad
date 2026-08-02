@extends('layouts.job-seeker')

@section('content')
<style>
.st-main{flex:1;overflow-y:auto}
.st-wrap{max-width:80rem;margin:0 auto;padding:2rem 1rem}
@media(min-width:640px){.st-wrap{padding-left:1.5rem;padding-right:1.5rem}}
@media(min-width:1024px){.st-wrap{padding-left:2rem;padding-right:2rem}}
.st-stack{display:flex;flex-direction:column;gap:1.5rem}
.st-panel{background:#fff;border-radius:.75rem;padding:1.5rem;border:1px solid #e5e7eb;box-shadow:0 1px 2px rgba(0,0,0,.04)}
.dark .st-panel{background:#1f2937;border-color:#374151}
.st-title{margin:0 0 .5rem;font-size:1.5rem;font-weight:700;color:#111827}
.dark .st-title{color:#fff}
.st-sub{margin:0;color:#4b5563}
.dark .st-sub{color:#9ca3af}
.st-sec-head{display:flex;align-items:center;gap:.5rem;margin-bottom:1rem}
.st-sec-head svg{width:1.25rem;height:1.25rem;color:#2563eb;flex-shrink:0}
.dark .st-sec-head svg{color:#22d3ee}
.st-sec-head h3{margin:0;font-size:1.125rem;font-weight:600;color:#111827}
.dark .st-sec-head h3{color:#fff}
.st-rows{display:flex;flex-direction:column;gap:0}
.st-row{display:flex;align-items:center;justify-content:space-between;gap:1rem;padding:.75rem;border-radius:.375rem;cursor:pointer}
.st-row:hover{background:#f9fafb}
.dark .st-row:hover{background:rgba(55,65,81,.5)}
.st-row-text{min-width:0}
.st-row-text strong{display:block;font-size:.95rem;font-weight:500;color:#111827}
.dark .st-row-text strong{color:#fff}
.st-row-text span{display:block;font-size:.8rem;color:#6b7280;margin-top:.15rem}
.dark .st-row-text span{color:#9ca3af}
.st-switch{position:relative;width:2.5rem;height:1.25rem;flex-shrink:0}
.st-switch input{opacity:0;width:0;height:0;position:absolute}
.st-switch i{position:absolute;inset:0;background:#d1d5db;border-radius:9999px;transition:background .2s;cursor:pointer}
.dark .st-switch i{background:#4b5563}
.st-switch i:before{content:"";position:absolute;height:1rem;width:1rem;left:.125rem;top:.125rem;background:#fff;border-radius:9999px;transition:transform .2s;box-shadow:0 1px 2px rgba(0,0,0,.15)}
.st-switch input:checked + i{background:#2563eb}
.dark .st-switch input:checked + i{background:#06b6d4}
.st-switch input:checked + i:before{transform:translateX(1.25rem)}
.st-switch input:disabled + i{opacity:.5;cursor:wait}
.st-action{width:100%;display:flex;align-items:center;justify-content:space-between;gap:1rem;padding:1rem;border-radius:.5rem;border:1px solid #e5e7eb;background:transparent;cursor:pointer;text-align:left;margin-bottom:.75rem}
.dark .st-action{border-color:#374151}
.st-action:hover{background:#f9fafb}
.dark .st-action:hover{background:rgba(55,65,81,.5)}
.st-action:last-child{margin-bottom:0}
.st-action-left{display:flex;align-items:center;gap:.75rem;min-width:0}
.st-action-left svg{width:1.25rem;height:1.25rem;color:#6b7280;flex-shrink:0}
.dark .st-action-left svg{color:#9ca3af}
.st-action-left strong{display:block;font-size:.95rem;font-weight:500;color:#111827}
.dark .st-action-left strong{color:#fff}
.st-action-left span{display:block;font-size:.8rem;color:#6b7280;margin-top:.15rem}
.dark .st-action-left span{color:#9ca3af}
.st-action-trail{font-size:.875rem;font-weight:500;flex-shrink:0}
.st-trail-blue{color:#2563eb}
.dark .st-trail-blue{color:#22d3ee}
.st-trail-yellow{color:#ca8a04}
.dark .st-trail-yellow{color:#facc15}
.st-trail-green{color:#16a34a}
.st-upgrade{border-radius:.75rem;padding:1.5rem;color:#fff;background:linear-gradient(to bottom right,#2563eb,#06b6d4)}
.st-upgrade-head{display:flex;align-items:center;gap:.5rem;margin-bottom:.5rem}
.st-upgrade-head svg{width:1.5rem;height:1.5rem}
.st-upgrade h3{margin:0;font-size:1.25rem;font-weight:700}
.st-upgrade p{margin:0 0 1rem;opacity:.9}
.st-upgrade a{display:inline-block;padding:.5rem 1.5rem;border-radius:.375rem;background:#fff;color:#2563eb;font-weight:500;text-decoration:none}
.st-upgrade a:hover{background:#f3f4f6}
.st-modal{display:none;position:fixed;inset:0;z-index:60;align-items:center;justify-content:center;padding:1rem;background:rgba(0,0,0,.45)}
.st-modal.is-open{display:flex}
.st-modal-panel{background:#fff;border-radius:.75rem;width:100%;max-width:28rem;box-shadow:0 25px 50px -12px rgba(0,0,0,.3)}
.dark .st-modal-panel{background:#1f2937}
.st-modal-head{display:flex;align-items:center;justify-content:space-between;padding:1rem 1.25rem;border-bottom:1px solid #e5e7eb}
.dark .st-modal-head{border-color:#374151}
.st-modal-head h3{margin:0;font-size:1.05rem;font-weight:600;color:#111827}
.dark .st-modal-head h3{color:#fff}
.st-modal-body{padding:1.25rem}
.st-close{border:0;background:transparent;cursor:pointer;color:#6b7280;padding:.35rem;border-radius:.375rem}
.st-close:hover{background:#f3f4f6}
.dark .st-close:hover{background:#374151}
.st-field{margin-bottom:1rem}
.st-field label{display:block;font-size:.875rem;font-weight:500;margin-bottom:.4rem;color:#374151}
.dark .st-field label{color:#d1d5db}
.st-field input{width:100%;padding:.65rem .75rem;border:1px solid #d1d5db;border-radius:.5rem;background:#fff;color:#111827;font-size:.875rem;box-sizing:border-box}
.dark .st-field input{background:#111827;border-color:#4b5563;color:#fff}
.st-field input:focus{outline:none;border-color:#2563eb;box-shadow:0 0 0 2px rgba(37,99,235,.25)}
.st-btn{width:100%;padding:.65rem 1rem;border:0;border-radius:.5rem;color:#fff;font-weight:500;cursor:pointer;background:linear-gradient(to right,#2563eb,#06b6d4)}
.st-btn:disabled{opacity:.6;cursor:wait}
.st-hint{font-size:.8rem;color:#6b7280;margin:0 0 1rem}
.dark .st-hint{color:#9ca3af}
</style>

@include('partials.job-seeker-navbar')

<main class="st-main">
    <div class="st-wrap">
        <div class="st-stack">
            <div class="st-panel">
                <h2 class="st-title">Account Settings</h2>
                <p class="st-sub">Manage your account preferences and security settings</p>
            </div>

            <div class="st-panel">
                <div class="st-sec-head">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                    <h3>Notification Preferences</h3>
                </div>
                <div class="st-rows" id="st-notif-rows"></div>
            </div>

            <div class="st-panel">
                <div class="st-sec-head">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    <h3>Security</h3>
                </div>
                <button type="button" class="st-action" id="st-change-password">
                    <div class="st-action-left">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        <div>
                            <strong>Change Password</strong>
                            <span id="st-password-label">Loading…</span>
                        </div>
                    </div>
                    <span class="st-action-trail st-trail-blue">Update</span>
                </button>
                <button type="button" class="st-action" id="st-2fa">
                    <div class="st-action-left">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        <div>
                            <strong>Two-Factor Authentication</strong>
                            <span>Add an extra layer of security</span>
                        </div>
                    </div>
                    <span class="st-action-trail st-trail-yellow" id="st-2fa-label">Enable</span>
                </button>
            </div>

            <div class="st-panel">
                <div class="st-sec-head">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    <h3>Privacy</h3>
                </div>
                <div class="st-rows" id="st-privacy-rows"></div>
            </div>

            <div class="st-upgrade">
                <div class="st-upgrade-head">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
                    <h3>Upgrade to Premium</h3>
                </div>
                <p>Unlock advanced features like priority applications, AI resume optimization, and more</p>
                <a id="st-pricing-link" href="{{ route('pricing.index') }}">View Plans</a>
            </div>
        </div>
    </div>
</main>

<div id="st-modal" class="st-modal" role="dialog" aria-modal="true">
    <div class="st-modal-panel">
        <div class="st-modal-head">
            <h3 id="st-modal-title">Change Password</h3>
            <button type="button" class="st-close" onclick="stCloseModal()" aria-label="Close">
                <svg style="width:1.25rem;height:1.25rem" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="st-modal-body" id="st-modal-body"></div>
    </div>
</div>

<script>
(function () {
    var csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';
    var state = {
        notifications: {},
        privacy: {},
        security: {},
        pricing_url: @json(route('pricing.index'))
    };

    var notifDefs = [
        { key: 'email_notifications', title: 'Email Notifications', desc: 'Receive email updates about your applications' },
        { key: 'job_alerts', title: 'Job Alerts', desc: 'Get notified about new job matches' },
        { key: 'application_updates', title: 'Application Updates', desc: 'Alerts for application status changes' },
        { key: 'marketing_emails', title: 'Marketing Emails', desc: 'Career tips and platform updates' }
    ];
    var privacyDefs = [
        { key: 'public_profile', title: 'Public Profile', desc: 'Make your profile visible to employers' },
        { key: 'show_activity_status', title: 'Show Activity Status', desc: 'Let employers see when you\'re active' },
        { key: 'allow_contact_by_recruiters', title: 'Allow Contact by Recruiters', desc: 'Let recruiters message you directly' }
    ];

    function headers(json) {
        var h = { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': csrf };
        if (json) h['Content-Type'] = 'application/json';
        return h;
    }
    function esc(s) {
        return String(s == null ? '' : s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    }
    function toastOk(m){ if (window.showSuccessToast) window.showSuccessToast(m); else alert(m); }
    function toastErr(m){ if (window.showErrorToast) window.showErrorToast(m); else alert(m); }

    function switchHtml(key, checked, group) {
        return '<label class="st-row"><div class="st-row-text"><strong></strong><span></span></div>' +
            '<span class="st-switch"><input type="checkbox" data-group="' + group + '" data-key="' + key + '"' + (checked ? ' checked' : '') + '><i></i></span></label>';
    }

    function renderToggles(containerId, defs, values, group) {
        var box = document.getElementById(containerId);
        box.innerHTML = defs.map(function (d) {
            var el = document.createElement('div');
            el.innerHTML = switchHtml(d.key, !!values[d.key], group);
            var label = el.firstChild;
            label.querySelector('strong').textContent = d.title;
            label.querySelector('span').textContent = d.desc;
            return label.outerHTML;
        }).join('');
        box.querySelectorAll('input[type=checkbox]').forEach(function (input) {
            input.addEventListener('change', function () { onToggle(input); });
        });
    }

    function renderSecurity() {
        document.getElementById('st-password-label').textContent = state.security.password_changed_label || 'Not changed recently';
        var on = !!state.security.two_factor_enabled;
        var trail = document.getElementById('st-2fa-label');
        trail.textContent = on ? 'Enabled' : 'Enable';
        trail.className = 'st-action-trail ' + (on ? 'st-trail-green' : 'st-trail-yellow');
    }

    async function onToggle(input) {
        var group = input.getAttribute('data-group');
        var key = input.getAttribute('data-key');
        var value = input.checked;
        var prev = !value;
        input.disabled = true;
        var url = group === 'notifications'
            ? '/job-seeker/settings/notifications'
            : '/job-seeker/settings/privacy';
        var body = {};
        body[key] = value;
        try {
            var res = await fetch(url, {
                method: 'PUT', credentials: 'include', headers: headers(true),
                body: JSON.stringify(body)
            });
            var data = await res.json();
            if (!res.ok) throw new Error(data.message || 'Save failed');
            if (group === 'notifications' && data.notifications) state.notifications = data.notifications;
            if (group === 'privacy' && data.privacy) state.privacy = data.privacy;
            toastOk('Saved');
        } catch (e) {
            input.checked = prev;
            toastErr(e.message || 'Could not save');
        } finally {
            input.disabled = false;
        }
    }

    async function loadBootstrap() {
        var res = await fetch('/job-seeker/settings/bootstrap', { credentials: 'include', headers: headers(false) });
        if (res.status === 401 || res.status === 403) { window.location.href = '/login'; return; }
        if (!res.ok) throw new Error('Failed to load settings');
        var data = await res.json();
        state.notifications = data.notifications || {};
        state.privacy = data.privacy || {};
        state.security = data.security || {};
        if (data.pricing_url) {
            state.pricing_url = data.pricing_url;
            document.getElementById('st-pricing-link').href = data.pricing_url;
        }
        renderToggles('st-notif-rows', notifDefs, state.notifications, 'notifications');
        renderToggles('st-privacy-rows', privacyDefs, state.privacy, 'privacy');
        renderSecurity();
    }

    document.getElementById('st-change-password').addEventListener('click', function () {
        openModal('Change Password',
            '<p class="st-hint">Choose a strong password with at least 6 characters.</p>' +
            '<div class="st-field"><label>Current password</label><input type="password" id="st-cur-pw" autocomplete="current-password"></div>' +
            '<div class="st-field"><label>New password</label><input type="password" id="st-new-pw" autocomplete="new-password"></div>' +
            '<div class="st-field"><label>Confirm new password</label><input type="password" id="st-conf-pw" autocomplete="new-password"></div>' +
            '<button type="button" class="st-btn" id="st-pw-save">Update Password</button>'
        );
        document.getElementById('st-pw-save').onclick = async function () {
            var btn = this; btn.disabled = true; btn.textContent = 'Updating…';
            try {
                var res = await fetch('/job-seeker/settings/password', {
                    method: 'PUT', credentials: 'include', headers: headers(true),
                    body: JSON.stringify({
                        current_password: document.getElementById('st-cur-pw').value,
                        password: document.getElementById('st-new-pw').value,
                        password_confirmation: document.getElementById('st-conf-pw').value
                    })
                });
                var data = await res.json();
                if (!res.ok) {
                    var msg = data.message || Object.entries(data.errors || {}).flat().join(' ') || 'Failed';
                    throw new Error(msg);
                }
                if (data.security) {
                    state.security.password_changed_at = data.security.password_changed_at;
                    state.security.password_changed_label = data.security.password_changed_label;
                    renderSecurity();
                }
                stCloseModal();
                toastOk(data.message || 'Password updated');
            } catch (e) {
                toastErr(e.message || 'Could not update password');
            } finally {
                btn.disabled = false; btn.textContent = 'Update Password';
            }
        };
    });

    document.getElementById('st-2fa').addEventListener('click', async function () {
        var enabling = !state.security.two_factor_enabled;
        var confirmMsg = enabling
            ? 'Enable two-factor authentication for your account?'
            : 'Disable two-factor authentication?';
        var ok = await window.showConfirmDialog(confirmMsg, {
            title: enabling ? 'Enable 2FA?' : 'Disable 2FA?',
            confirmText: enabling ? 'Enable' : 'Disable',
            cancelText: 'Cancel',
            danger: !enabling
        });
        if (!ok) return;
        try {
            var res = await fetch('/job-seeker/settings/two-factor', {
                method: 'PUT', credentials: 'include', headers: headers(true),
                body: JSON.stringify({ enabled: enabling })
            });
            var data = await res.json();
            if (!res.ok) throw new Error(data.message || 'Failed');
            state.security.two_factor_enabled = !!(data.security && data.security.two_factor_enabled);
            renderSecurity();
            toastOk(data.message || 'Updated');
        } catch (e) {
            toastErr(e.message || 'Could not update 2FA');
        }
    });

    function openModal(title, html) {
        document.getElementById('st-modal-title').textContent = title;
        document.getElementById('st-modal-body').innerHTML = html;
        document.getElementById('st-modal').classList.add('is-open');
    }
    window.stCloseModal = function () {
        document.getElementById('st-modal').classList.remove('is-open');
    };
    document.getElementById('st-modal').addEventListener('click', function (e) {
        if (e.target === this) stCloseModal();
    });

    loadBootstrap().catch(function () {
        toastErr('Could not load settings');
        renderToggles('st-notif-rows', notifDefs, {}, 'notifications');
        renderToggles('st-privacy-rows', privacyDefs, {}, 'privacy');
        renderSecurity();
    });
})();
</script>
@endsection
