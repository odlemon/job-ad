@extends('layouts.job-seeker')

@section('content')
<style>
.su-main{flex:1;overflow-y:auto}
.su-wrap{max-width:80rem;margin:0 auto;padding:2rem 1rem}
@media(min-width:640px){.su-wrap{padding-left:1.5rem;padding-right:1.5rem}}
@media(min-width:1024px){.su-wrap{padding-left:2rem;padding-right:2rem}}
.su-stack{display:flex;flex-direction:column;gap:1.5rem}
.su-panel{background:#fff;border-radius:.75rem;padding:1.5rem;border:1px solid #e5e7eb;box-shadow:0 1px 2px rgba(0,0,0,.04)}
.dark .su-panel{background:#1f2937;border-color:#374151}
.su-title{margin:0 0 .5rem;font-size:1.5rem;font-weight:700;color:#111827}
.dark .su-title{color:#fff}
.su-sub{margin:0;color:#4b5563}
.dark .su-sub{color:#9ca3af}
.su-grid{display:grid;grid-template-columns:1fr;gap:1.5rem}
@media(min-width:768px){.su-grid{grid-template-columns:repeat(2,minmax(0,1fr))}}
.su-h3{margin:0 0 1rem;font-size:1.125rem;font-weight:600;color:#111827}
.dark .su-h3{color:#fff}
.su-field{margin-bottom:1rem}
.su-field label{display:block;font-size:.875rem;font-weight:500;margin-bottom:.4rem;color:#374151}
.dark .su-field label{color:#d1d5db}
.su-field input,.su-field select,.su-field textarea{width:100%;padding:.65rem .75rem;border:1px solid #d1d5db;border-radius:.5rem;background:#fff;color:#111827;font-size:.875rem;box-sizing:border-box}
.dark .su-field input,.dark .su-field select,.dark .su-field textarea{background:#111827;border-color:#4b5563;color:#fff}
.su-field input:focus,.su-field select:focus,.su-field textarea:focus{outline:none;border-color:#2563eb;box-shadow:0 0 0 2px rgba(37,99,235,.25)}
.su-btn{width:100%;padding:.65rem 1rem;border:0;border-radius:.5rem;color:#fff;font-weight:500;cursor:pointer}
.su-btn:disabled{opacity:.6;cursor:wait}
.su-btn-blue{background:linear-gradient(to right,#2563eb,#06b6d4)}
.su-btn-blue:hover{box-shadow:0 10px 15px -3px rgba(37,99,235,.3)}
.su-btn-red{background:linear-gradient(to right,#dc2626,#f97316)}
.su-btn-red:hover{box-shadow:0 10px 15px -3px rgba(220,38,38,.3)}
.su-btn-secondary{padding:.5rem 1rem;border:1px solid #d1d5db;border-radius:.5rem;background:transparent;cursor:pointer;color:#374151;font-size:.875rem}
.dark .su-btn-secondary{border-color:#4b5563;color:#d1d5db}
.su-ticket{display:flex;align-items:flex-start;justify-content:space-between;gap:1rem;padding:1rem;border:1px solid #e5e7eb;border-radius:.75rem;margin-bottom:.75rem}
.dark .su-ticket{border-color:#374151}
.su-ticket:hover{background:#f9fafb}
.dark .su-ticket:hover{background:rgba(55,65,81,.35)}
.su-pill{display:inline-flex;align-items:center;padding:.15rem .55rem;border-radius:9999px;font-size:.7rem;font-weight:600;text-transform:capitalize}
.su-pill-open{background:#dbeafe;color:#1d4ed8}
.su-pill-in_progress{background:#fef3c7;color:#b45309}
.su-pill-resolved,.su-pill-closed{background:#dcfce7;color:#15803d}
.su-pill-low{background:#f3f4f6;color:#4b5563}
.su-pill-medium{background:#e0e7ff;color:#4338ca}
.su-pill-high{background:#fee2e2;color:#b91c1c}
.dark .su-pill-open{background:rgba(30,58,138,.35);color:#93c5fd}
.dark .su-pill-in_progress{background:rgba(120,53,15,.35);color:#fcd34d}
.dark .su-pill-resolved,.dark .su-pill-closed{background:rgba(20,83,45,.35);color:#86efac}
.dark .su-pill-low{background:#374151;color:#d1d5db}
.dark .su-pill-medium{background:rgba(49,46,129,.4);color:#c7d2fe}
.dark .su-pill-high{background:rgba(127,29,29,.4);color:#fca5a5}
.su-faq details{border:1px solid #e5e7eb;border-radius:.5rem;padding:.85rem 1rem;margin-bottom:.5rem}
.dark .su-faq details{border-color:#374151}
.su-faq summary{cursor:pointer;font-weight:500;color:#111827;list-style:none}
.dark .su-faq summary{color:#fff}
.su-faq summary::-webkit-details-marker{display:none}
.su-faq details[open] summary{margin-bottom:.5rem}
.su-faq p{margin:0;font-size:.875rem;color:#4b5563;line-height:1.5}
.dark .su-faq p{color:#9ca3af}
.su-cta-grid{display:grid;grid-template-columns:1fr;gap:1rem}
@media(min-width:640px){.su-cta-grid{grid-template-columns:repeat(2,minmax(0,1fr))}}
.su-cta{border-radius:.75rem;padding:1.25rem 1.5rem;color:#fff}
.su-cta h4{margin:0 0 .35rem;font-size:1.05rem;font-weight:700}
.su-cta p{margin:0 0 1rem;opacity:.9;font-size:.875rem}
.su-cta button,.su-cta a{display:inline-block;padding:.5rem 1rem;border:0;border-radius:.375rem;background:#fff;font-weight:500;font-size:.875rem;text-decoration:none;cursor:pointer}
.su-cta-chat{background:linear-gradient(to bottom right,#2563eb,#06b6d4)}
.su-cta-chat button{color:#2563eb}
.su-cta-email{background:linear-gradient(to bottom right,#9333ea,#ec4899)}
.su-cta-email a{color:#9333ea}
.su-empty{text-align:center;color:#6b7280;padding:1.25rem;font-size:.875rem}
.su-modal{display:none;position:fixed;inset:0;z-index:60;align-items:center;justify-content:center;padding:1rem;background:rgba(0,0,0,.45)}
.su-modal.is-open{display:flex}
.su-modal-panel{background:#fff;border-radius:.75rem;width:100%;max-width:36rem;max-height:90vh;overflow:auto;box-shadow:0 25px 50px -12px rgba(0,0,0,.3)}
.dark .su-modal-panel{background:#1f2937}
.su-modal-head{display:flex;align-items:center;justify-content:space-between;padding:1rem 1.25rem;border-bottom:1px solid #e5e7eb}
.dark .su-modal-head{border-color:#374151}
.su-modal-head h3{margin:0;font-size:1.05rem;font-weight:600}
.su-modal-body{padding:1.25rem}
.su-close{border:0;background:transparent;cursor:pointer;color:#6b7280;padding:.35rem;border-radius:.375rem}
.su-close:hover{background:#f3f4f6}
.dark .su-close:hover{background:#374151}
.su-link{background:none;border:0;color:#2563eb;cursor:pointer;font-size:.875rem;padding:0}
.dark .su-link{color:#22d3ee}
.su-link:hover{text-decoration:underline}
</style>

@include('partials.job-seeker-navbar')

<main class="su-main">
    <div class="su-wrap">
        <div class="su-stack">
            <div class="su-panel">
                <h2 class="su-title">Support & Feedback</h2>
                <p class="su-sub">Get help or report an issue — our team is notified in real time</p>
            </div>

            <div class="su-grid">
                <div class="su-panel">
                    <h3 class="su-h3">Create Support Ticket</h3>
                    <form id="su-ticket-form" novalidate>
                        <div class="su-field">
                            <label for="su-subject">Subject</label>
                            <input id="su-subject" name="subject" required maxlength="160" placeholder="Brief summary of your issue">
                        </div>
                        <div class="su-field">
                            <label for="su-priority">Priority</label>
                            <select id="su-priority" name="priority">
                                <option value="low">Low</option>
                                <option value="medium" selected>Medium</option>
                                <option value="high">High</option>
                            </select>
                        </div>
                        <div class="su-field">
                            <label for="su-message">Message</label>
                            <textarea id="su-message" name="message" rows="5" required maxlength="5000" placeholder="Describe your issue in detail…"></textarea>
                        </div>
                        <button type="submit" class="su-btn su-btn-blue" id="su-ticket-btn">Submit Ticket</button>
                    </form>
                </div>

                <div class="su-panel">
                    <h3 class="su-h3">Report Suspicious Job</h3>
                    <form id="su-report-form" novalidate>
                        <div class="su-field">
                            <label for="su-job-ref">Job ID / URL</label>
                            <input id="su-job-ref" name="job_reference" required placeholder="e.g. 42 or https://…/jobs/42">
                        </div>
                        <div class="su-field">
                            <label for="su-reason">Reason</label>
                            <select id="su-reason" name="category" required>
                                <option value="scam">Scam</option>
                                <option value="inappropriate">Inappropriate</option>
                                <option value="duplicate">Duplicate</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div class="su-field">
                            <label for="su-details">Details</label>
                            <textarea id="su-details" name="details" rows="5" maxlength="5000" placeholder="Tell us what looked wrong…"></textarea>
                        </div>
                        <button type="submit" class="su-btn su-btn-red" id="su-report-btn">Submit Report</button>
                    </form>
                </div>
            </div>

            <div class="su-panel">
                <div style="display:flex;align-items:center;justify-content:space-between;gap:1rem;margin-bottom:1rem;flex-wrap:wrap">
                    <h3 class="su-h3" style="margin:0">My Support Tickets</h3>
                    <button type="button" class="su-link" id="su-refresh">Refresh</button>
                </div>
                <div id="su-tickets"></div>
            </div>

            <div class="su-panel">
                <h3 class="su-h3">FAQ</h3>
                <div class="su-faq" id="su-faqs"></div>
            </div>

            <div class="su-cta-grid">
                <div class="su-cta su-cta-chat">
                    <h4>Live Chat</h4>
                    <p>Need a quick reply? Start a high-priority chat request with our support team.</p>
                    <button type="button" id="su-live-chat">Start Chat</button>
                </div>
                <div class="su-cta su-cta-email">
                    <h4>Email Support</h4>
                    <p>Prefer email? Reach us directly and we will respond as soon as possible.</p>
                    <a id="su-email-link" href="mailto:support@scoop.app">support@scoop.app</a>
                </div>
            </div>
        </div>
    </div>
</main>

<div id="su-modal" class="su-modal" role="dialog" aria-modal="true">
    <div class="su-modal-panel">
        <div class="su-modal-head">
            <h3 id="su-modal-title">Ticket</h3>
            <button type="button" class="su-close" onclick="suCloseModal()" aria-label="Close">
                <svg style="width:1.25rem;height:1.25rem" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="su-modal-body" id="su-modal-body"></div>
    </div>
</div>

<script>
(function () {
    var csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';
    var state = { tickets: [], faqs: [], supportEmail: 'support@scoop.app' };

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

    function pill(kind, value) {
        return '<span class="su-pill su-pill-' + esc(value) + '">' + esc(String(value).replace('_', ' ')) + '</span>';
    }

    function renderTickets() {
        var box = document.getElementById('su-tickets');
        if (!state.tickets.length) {
            box.innerHTML = '<div class="su-empty">No tickets yet. Submit one above and we will notify the admin team instantly.</div>';
            return;
        }
        box.innerHTML = state.tickets.map(function (t) {
            return '<div class="su-ticket"><div style="min-width:0"><div style="display:flex;gap:.4rem;flex-wrap:wrap;margin-bottom:.4rem">' +
                pill('status', t.status) + pill('priority', t.priority) +
                '</div><strong style="color:#111827">' + esc(t.subject) + '</strong>' +
                '<div style="font-size:.8rem;color:#6b7280;margin-top:.25rem">#' + t.id + ' · ' + esc(t.date || t.time_ago || '') + '</div></div>' +
                '<button type="button" class="su-btn-secondary" data-view="' + t.id + '">View</button></div>';
        }).join('');
        box.querySelectorAll('[data-view]').forEach(function (btn) {
            btn.addEventListener('click', function () { suViewTicket(btn.getAttribute('data-view')); });
        });
    }

    function renderFaqs() {
        document.getElementById('su-faqs').innerHTML = (state.faqs || []).map(function (f) {
            return '<details><summary>' + esc(f.q) + '</summary><p>' + esc(f.a) + '</p></details>';
        }).join('');
    }

    async function loadBootstrap() {
        var res = await fetch('/job-seeker/support/bootstrap', { credentials: 'include', headers: headers(false) });
        if (res.status === 401 || res.status === 403) { window.location.href = '/login'; return; }
        if (!res.ok) throw new Error('Failed to load support data');
        var data = await res.json();
        state.tickets = data.tickets || [];
        state.faqs = data.faqs || [];
        state.supportEmail = data.support_email || 'support@scoop.app';
        document.getElementById('su-email-link').href = 'mailto:' + state.supportEmail;
        document.getElementById('su-email-link').textContent = state.supportEmail;
        renderTickets();
        renderFaqs();
    }

    document.getElementById('su-ticket-form').addEventListener('submit', async function (e) {
        e.preventDefault();
        var btn = document.getElementById('su-ticket-btn');
        btn.disabled = true; btn.textContent = 'Submitting…';
        try {
            var res = await fetch('/job-seeker/support/tickets', {
                method: 'POST', credentials: 'include', headers: headers(true),
                body: JSON.stringify({
                    subject: document.getElementById('su-subject').value.trim(),
                    priority: document.getElementById('su-priority').value,
                    message: document.getElementById('su-message').value.trim(),
                    channel: 'ticket'
                })
            });
            var data = await res.json();
            if (!res.ok) throw new Error(data.message || Object.entries(data.errors || {}).flat().join(' ') || 'Failed');
            state.tickets.unshift(data.ticket);
            renderTickets();
            e.target.reset();
            document.getElementById('su-priority').value = 'medium';
            toastOk('Ticket submitted — admins have been notified');
        } catch (err) {
            toastErr(err.message || 'Could not submit ticket');
        } finally {
            btn.disabled = false; btn.textContent = 'Submit Ticket';
        }
    });

    document.getElementById('su-report-form').addEventListener('submit', async function (e) {
        e.preventDefault();
        var btn = document.getElementById('su-report-btn');
        btn.disabled = true; btn.textContent = 'Submitting…';
        try {
            var res = await fetch('/job-seeker/support/report-job', {
                method: 'POST', credentials: 'include', headers: headers(true),
                body: JSON.stringify({
                    job_reference: document.getElementById('su-job-ref').value.trim(),
                    category: document.getElementById('su-reason').value,
                    details: document.getElementById('su-details').value.trim()
                })
            });
            var data = await res.json();
            if (!res.ok) throw new Error(data.message || Object.entries(data.errors || {}).flat().join(' ') || 'Failed');
            e.target.reset();
            toastOk(data.message || 'Report submitted');
        } catch (err) {
            toastErr(err.message || 'Could not submit report');
        } finally {
            btn.disabled = false; btn.textContent = 'Submit Report';
        }
    });

    document.getElementById('su-refresh').addEventListener('click', function () {
        loadBootstrap().then(function () { toastOk('Tickets refreshed'); }).catch(function () { toastErr('Refresh failed'); });
    });

    document.getElementById('su-live-chat').addEventListener('click', function () {
        openModal('Live Chat Request',
            '<p style="margin:0 0 1rem;font-size:.875rem;color:#4b5563">Send a high-priority request and an admin will pick it up from their inbox.</p>' +
            '<div class="su-field"><label>What do you need help with?</label><textarea id="su-chat-msg" rows="4" placeholder="Type your message…"></textarea></div>' +
            '<button type="button" class="su-btn su-btn-blue" id="su-chat-send">Send Chat Request</button>'
        );
        document.getElementById('su-chat-send').onclick = async function () {
            var msg = document.getElementById('su-chat-msg').value.trim();
            if (!msg) { toastErr('Please enter a message'); return; }
            var btn = this; btn.disabled = true;
            try {
                var res = await fetch('/job-seeker/support/tickets', {
                    method: 'POST', credentials: 'include', headers: headers(true),
                    body: JSON.stringify({
                        subject: 'Live chat request',
                        message: msg,
                        priority: 'high',
                        channel: 'live_chat'
                    })
                });
                var data = await res.json();
                if (!res.ok) throw new Error(data.message || 'Failed');
                state.tickets.unshift(data.ticket);
                renderTickets();
                suCloseModal();
                toastOk('Chat request sent — admins notified');
            } catch (err) {
                toastErr(err.message || 'Could not start chat');
            } finally {
                btn.disabled = false;
            }
        };
    });

    window.suViewTicket = function (id) {
        var t = state.tickets.find(function (x) { return String(x.id) === String(id); });
        if (!t) return;
        openModal('Ticket #' + t.id,
            '<div style="display:flex;gap:.4rem;flex-wrap:wrap;margin-bottom:.75rem">' + pill('status', t.status) + pill('priority', t.priority) + '</div>' +
            '<h4 style="margin:0 0 .5rem">' + esc(t.subject) + '</h4>' +
            '<p style="margin:0 0 1rem;font-size:.875rem;color:#4b5563;white-space:pre-wrap">' + esc(t.message) + '</p>' +
            (t.admin_response
                ? '<div style="padding:1rem;border-radius:.5rem;background:#eff6ff;border:1px solid #bfdbfe"><strong style="display:block;margin-bottom:.35rem">Admin response</strong><p style="margin:0;font-size:.875rem;white-space:pre-wrap">' + esc(t.admin_response) + '</p></div>'
                : '<p style="margin:0;font-size:.875rem;color:#6b7280">No admin response yet. You will get a notification when the team replies.</p>') +
            '<p style="margin:1rem 0 0;font-size:.75rem;color:#9ca3af">' + esc(t.date || '') + (t.channel ? ' · via ' + esc(t.channel.replace('_', ' ')) : '') + '</p>'
        );
    };

    function openModal(title, html) {
        document.getElementById('su-modal-title').textContent = title;
        document.getElementById('su-modal-body').innerHTML = html;
        document.getElementById('su-modal').classList.add('is-open');
    }
    window.suCloseModal = function () {
        document.getElementById('su-modal').classList.remove('is-open');
    };
    document.getElementById('su-modal').addEventListener('click', function (e) {
        if (e.target === this) suCloseModal();
    });

    loadBootstrap().catch(function () {
        toastErr('Could not load support page');
        renderTickets();
        renderFaqs();
    });
})();
</script>
@endsection
