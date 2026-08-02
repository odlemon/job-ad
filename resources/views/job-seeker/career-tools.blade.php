@extends('layouts.job-seeker')

@section('content')
<style>
.ct-main{flex:1;overflow-y:auto}
.ct-wrap{max-width:80rem;margin:0 auto;padding:2rem 1rem}
@media(min-width:640px){.ct-wrap{padding-left:1.5rem;padding-right:1.5rem}}
@media(min-width:1024px){.ct-wrap{padding-left:2rem;padding-right:2rem}}
.ct-stack{display:flex;flex-direction:column;gap:1.5rem}
.ct-panel{background:#fff;border-radius:.75rem;padding:1.5rem;border:1px solid #e5e7eb;box-shadow:0 1px 2px rgba(0,0,0,.04)}
.dark .ct-panel{background:#1f2937;border-color:#374151}
.ct-title{margin:0 0 .5rem;font-size:1.5rem;font-weight:700;color:#111827}
.dark .ct-title{color:#fff}
.ct-sub{margin:0;color:#4b5563}
.dark .ct-sub{color:#9ca3af}
.ct-grid{display:grid;grid-template-columns:1fr;gap:1.5rem}
@media(min-width:768px){.ct-grid{grid-template-columns:repeat(2,minmax(0,1fr))}}
@media(min-width:1024px){.ct-grid{grid-template-columns:repeat(3,minmax(0,1fr))}}
.ct-card{background:#fff;border:1px solid #e5e7eb;border-radius:.75rem;padding:1.5rem;box-shadow:0 1px 2px rgba(0,0,0,.04);transition:box-shadow .15s}
.dark .ct-card{background:#1f2937;border-color:#374151}
.ct-card:hover{box-shadow:0 10px 15px -3px rgba(0,0,0,.08)}
.ct-icon{width:3rem;height:3rem;border-radius:.5rem;display:flex;align-items:center;justify-content:center;margin-bottom:1rem;transition:transform .15s}
.ct-card:hover .ct-icon{transform:scale(1.1)}
.ct-icon svg{width:1.5rem;height:1.5rem;color:#fff}
.ct-card h3{margin:0 0 .5rem;font-size:1.125rem;font-weight:600;color:#111827}
.dark .ct-card h3{color:#fff}
.ct-card p{margin:0 0 1rem;font-size:.875rem;color:#4b5563;min-height:2.5rem}
.dark .ct-card p{color:#9ca3af}
.ct-btn{width:100%;padding:.5rem 1rem;border:0;border-radius:.5rem;color:#fff;font-weight:500;cursor:pointer;background:linear-gradient(to right,#2563eb,#06b6d4)}
.ct-btn:hover{box-shadow:0 10px 15px -3px rgba(37,99,235,.3)}
.ct-btn:disabled{opacity:.6;cursor:wait;box-shadow:none}
.ct-btn-secondary{padding:.5rem 1rem;border:1px solid #d1d5db;border-radius:.5rem;background:transparent;cursor:pointer;color:#374151;font-size:.875rem}
.dark .ct-btn-secondary{border-color:#4b5563;color:#d1d5db}
.ct-btn-secondary:hover{background:#f9fafb}
.dark .ct-btn-secondary:hover{background:#374151}
.ct-link{background:none;border:0;color:#2563eb;cursor:pointer;font-size:.875rem;padding:0;display:inline-flex;align-items:center;gap:.25rem}
.dark .ct-link{color:#22d3ee}
.ct-link:hover{text-decoration:underline}
.ct-head{display:flex;align-items:center;justify-content:space-between;gap:1rem;margin-bottom:1rem;flex-wrap:wrap}
.ct-h3{margin:0;font-size:1.125rem;font-weight:600;color:#111827}
.dark .ct-h3{color:#fff}
.ct-doc{display:flex;align-items:center;justify-content:space-between;gap:1rem;padding:1rem;border:1px solid #e5e7eb;border-radius:.5rem}
.dark .ct-doc{border-color:#374151}
.ct-doc:hover{background:#f9fafb}
.dark .ct-doc:hover{background:rgba(55,65,81,.4)}
.ct-doc-left{display:flex;align-items:center;gap:.75rem;min-width:0}
.ct-doc-ico{padding:.5rem;border-radius:.5rem;background:#fee2e2;color:#dc2626}
.dark .ct-doc-ico{background:rgba(127,29,29,.3);color:#fca5a5}
.ct-doc-ico svg{width:1.25rem;height:1.25rem;display:block}
.ct-boost{border-radius:.75rem;padding:1.5rem;color:#fff;background:linear-gradient(to bottom right,#2563eb,#06b6d4)}
.ct-boost h3{margin:0 0 .5rem;font-size:1.25rem;font-weight:700}
.ct-boost p{margin:0 0 1rem;opacity:.9}
.ct-boost-btn{padding:.5rem 1.5rem;border:0;border-radius:.375rem;background:#fff;color:#2563eb;font-weight:500;cursor:pointer}
.ct-boost-btn:hover{background:#f3f4f6}

.ct-modal{display:none;position:fixed;inset:0;z-index:60;align-items:center;justify-content:center;padding:1rem;background:rgba(0,0,0,.45)}
.ct-modal.is-open{display:flex}
.ct-modal-panel{background:#fff;border-radius:.75rem;width:100%;max-width:56rem;max-height:92vh;overflow:auto;box-shadow:0 25px 50px -12px rgba(0,0,0,.3)}
.dark .ct-modal-panel{background:#1f2937}
.ct-modal-sm{max-width:36rem}
.ct-modal-head{display:flex;align-items:center;justify-content:space-between;gap:1rem;padding:1rem 1.5rem;border-bottom:1px solid #e5e7eb;position:sticky;top:0;background:inherit;z-index:1}
.dark .ct-modal-head{border-color:#374151}
.ct-modal-head h3{margin:0;font-size:1.125rem;font-weight:600;color:#111827}
.dark .ct-modal-head h3{color:#fff}
.ct-modal-body{padding:1.5rem}
.ct-close{border:0;background:transparent;cursor:pointer;color:#6b7280;padding:.35rem;border-radius:.375rem}
.ct-close:hover{background:#f3f4f6}
.dark .ct-close:hover{background:#374151}
.ct-field{margin-bottom:1rem}
.ct-field label{display:block;font-size:.875rem;font-weight:500;margin-bottom:.4rem;color:#374151}
.dark .ct-field label{color:#d1d5db}
.ct-field input,.ct-field select,.ct-field textarea{width:100%;padding:.6rem .75rem;border:1px solid #d1d5db;border-radius:.5rem;background:#fff;color:#111827;font-size:.875rem;box-sizing:border-box}
.dark .ct-field input,.dark .ct-field select,.dark .ct-field textarea{background:#111827;border-color:#4b5563;color:#fff}
.ct-field input:focus,.ct-field select:focus,.ct-field textarea:focus{outline:none;box-shadow:0 0 0 2px rgba(37,99,235,.3);border-color:#2563eb}
.ct-actions{display:flex;gap:.75rem;flex-wrap:wrap;justify-content:flex-end;margin-top:1rem}
.ct-preview{border:1px solid #e5e7eb;border-radius:.5rem;padding:1rem;background:#f9fafb;max-height:24rem;overflow:auto}
.dark .ct-preview{background:#111827;border-color:#374151}
.ct-q{border:1px solid #e5e7eb;border-radius:.75rem;padding:1rem;margin-bottom:.75rem}
.dark .ct-q{border-color:#374151}
.ct-q.is-active{border-color:#2563eb;background:#eff6ff}
.dark .ct-q.is-active{background:rgba(30,58,138,.2);border-color:#22d3ee}
.ct-choice{display:flex;align-items:center;gap:.5rem;padding:.4rem 0;cursor:pointer;font-size:.875rem;color:#374151}
.dark .ct-choice{color:#d1d5db}
.ct-path{border:1px solid #e5e7eb;border-radius:.75rem;padding:1rem;margin-bottom:.75rem}
.dark .ct-path{border-color:#374151}
.ct-bar{height:.5rem;background:#e5e7eb;border-radius:9999px;overflow:hidden;margin-top:.5rem}
.dark .ct-bar{background:#374151}
.ct-bar>i{display:block;height:100%;background:linear-gradient(to right,#2563eb,#06b6d4)}
.ct-salary{display:grid;grid-template-columns:repeat(3,1fr);gap:.75rem;margin-top:1rem}
.ct-salary div{text-align:center;padding:1rem;border-radius:.5rem;background:#f3f4f6}
.dark .ct-salary div{background:#111827}
.ct-salary strong{display:block;font-size:1.25rem;color:#111827}
.dark .ct-salary strong{color:#fff}
.ct-empty{text-align:center;color:#6b7280;padding:1.5rem;font-size:.875rem}
</style>

@include('partials.job-seeker-navbar')

<main class="ct-main">
    <div class="ct-wrap">
        <div class="ct-stack" id="ct-hub">
            <div class="ct-panel">
                <h2 class="ct-title">Career Tools & Resources</h2>
                <p class="ct-sub">Professional tools to help you succeed in your job search</p>
            </div>

            <div class="ct-grid" id="ct-tools-grid"></div>

            <div class="ct-panel">
                <div class="ct-head">
                    <h3 class="ct-h3">Recent Documents</h3>
                    <button type="button" class="ct-link" onclick="ctOpenTool('resume')">
                        <svg style="width:1rem;height:1rem" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        New Document
                    </button>
                </div>
                <div id="ct-docs" class="ct-stack" style="gap:.75rem"></div>
            </div>

            <div class="ct-boost">
                <h3>Boost Your Skills</h3>
                <p>Access free courses and resources to improve your professional skills</p>
                <button type="button" class="ct-boost-btn" onclick="ctOpenCourses()">Browse Courses</button>
            </div>
        </div>
    </div>
</main>

{{-- Shared tool modal shell --}}
<div id="ct-modal" class="ct-modal" role="dialog" aria-modal="true">
    <div class="ct-modal-panel" id="ct-modal-panel">
        <div class="ct-modal-head">
            <h3 id="ct-modal-title">Tool</h3>
            <button type="button" class="ct-close" onclick="ctCloseModal()" aria-label="Close">
                <svg style="width:1.25rem;height:1.25rem" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="ct-modal-body" id="ct-modal-body"></div>
    </div>
</div>

<script>
(function () {
    var csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';
    var state = { profile: null, documents: [], courses: [], roles: [], assessmentTopics: [] };
    var interviewIdx = 0;
    var interviewQs = [];
    var assessment = null;

    var tools = [
        { id: 'resume', title: 'Resume Builder', description: 'Create professional resumes with AI-powered templates', action: 'Build Resume', color: 'linear-gradient(to bottom right,#2563eb,#3b82f6)', icon: 'doc' },
        { id: 'cover', title: 'Cover Letter Builder', description: 'Generate compelling cover letters tailored to each job', action: 'Create Letter', color: 'linear-gradient(to bottom right,#9333ea,#a855f7)', icon: 'chat' },
        { id: 'interview', title: 'Interview Prep', description: 'Practice with common interview questions and tips', action: 'Start Practice', color: 'linear-gradient(to bottom right,#16a34a,#22c55e)', icon: 'mic' },
        { id: 'salary', title: 'Salary Calculator', description: 'Estimate your market value based on skills and experience', action: 'Calculate', color: 'linear-gradient(to bottom right,#ca8a04,#eab308)', icon: 'cash' },
        { id: 'assessment', title: 'Skill Assessments', description: 'Take tests to validate your skills and get certificates', action: 'Take Assessment', color: 'linear-gradient(to bottom right,#dc2626,#ef4444)', icon: 'check' },
        { id: 'paths', title: 'Career Path', description: 'Explore career paths and growth opportunities', action: 'Explore Paths', color: 'linear-gradient(to bottom right,#0891b2,#06b6d4)', icon: 'trend' }
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

    function iconSvg(kind) {
        var paths = {
            doc: 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
            chat: 'M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z',
            mic: 'M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z',
            cash: 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
            check: 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
            trend: 'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6'
        };
        return '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="' + paths[kind] + '"/></svg>';
    }

    function renderTools() {
        document.getElementById('ct-tools-grid').innerHTML = tools.map(function (t) {
            return '<div class="ct-card"><div class="ct-icon" style="background:' + t.color + '">' + iconSvg(t.icon) + '</div><h3>' + esc(t.title) + '</h3><p>' + esc(t.description) + '</p><button type="button" class="ct-btn" data-tool="' + t.id + '">' + esc(t.action) + '</button></div>';
        }).join('');
        document.querySelectorAll('[data-tool]').forEach(function (btn) {
            btn.addEventListener('click', function () { ctOpenTool(btn.getAttribute('data-tool')); });
        });
    }

    function renderDocs() {
        var box = document.getElementById('ct-docs');
        if (!state.documents.length) {
            box.innerHTML = '<div class="ct-empty">No documents yet. Build a resume or cover letter to get started.</div>';
            return;
        }
        box.innerHTML = state.documents.map(function (d) {
            return '<div class="ct-doc"><div class="ct-doc-left"><div class="ct-doc-ico">' + iconSvg('doc') + '</div><div style="min-width:0"><p style="margin:0;font-weight:500;color:#111827" class="dark-text">' + esc(d.name) + '</p><p style="margin:0;font-size:.875rem;color:#6b7280">' + esc(d.date) + ' · ' + esc(d.size) + '</p></div></div><div style="display:flex;gap:.35rem">' +
                (d.download_url ? '<a class="ct-btn-secondary" style="text-decoration:none" href="' + esc(d.download_url) + '" target="_blank" rel="noopener">Download</a>' : '') +
                (d.can_delete ? '<button type="button" class="ct-btn-secondary" data-del="' + d.id + '">Delete</button>' : '') +
                '</div></div>';
        }).join('');
        box.querySelectorAll('[data-del]').forEach(function (btn) {
            btn.addEventListener('click', function () { ctDeleteDoc(btn.getAttribute('data-del')); });
        });
    }

    async function loadBootstrap() {
        var res = await fetch('/job-seeker/career-tools/bootstrap', { credentials: 'include', headers: headers(false) });
        if (res.status === 401 || res.status === 403) { window.location.href = '/login'; return; }
        if (!res.ok) throw new Error('bootstrap failed');
        var data = await res.json();
        state.profile = data.profile || {};
        state.documents = data.documents || [];
        state.courses = data.courses || [];
        state.roles = data.roles || [];
        state.assessmentTopics = data.assessment_topics || [];
        renderDocs();
    }

    function openModal(title, bodyHtml, wide) {
        document.getElementById('ct-modal-title').textContent = title;
        document.getElementById('ct-modal-body').innerHTML = bodyHtml;
        document.getElementById('ct-modal-panel').classList.toggle('ct-modal-sm', !wide);
        document.getElementById('ct-modal').classList.add('is-open');
    }
    window.ctCloseModal = function () {
        document.getElementById('ct-modal').classList.remove('is-open');
    };
    document.getElementById('ct-modal').addEventListener('click', function (e) {
        if (e.target === this) ctCloseModal();
    });

    window.ctOpenTool = function (id) {
        var url = new URL(window.location.href);
        url.searchParams.set('tool', id);
        history.replaceState({}, '', url.pathname + '?' + url.searchParams.toString());
        if (id === 'resume') return openResume();
        if (id === 'cover') return openCover();
        if (id === 'interview') return openInterview();
        if (id === 'salary') return openSalary();
        if (id === 'assessment') return openAssessment();
        if (id === 'paths') return openPaths();
    };

    function openResume() {
        var p = state.profile || {};
        openModal('Resume Builder',
            '<div class="ct-field"><label>Template</label><select id="ct-resume-template"><option value="modern">Modern</option><option value="classic">Classic</option><option value="compact">Compact</option></select></div>' +
            '<div class="ct-field"><label>Headline</label><input id="ct-resume-headline" value="' + esc(p.headline || '') + '" placeholder="e.g. Full-Stack Developer"></div>' +
            '<div class="ct-field"><label>Professional summary</label><textarea id="ct-resume-summary" rows="4">' + esc(p.bio || '') + '</textarea></div>' +
            '<div class="ct-actions"><button type="button" class="ct-btn-secondary" onclick="ctCloseModal()">Cancel</button><button type="button" class="ct-btn" style="width:auto" id="ct-resume-go">Generate & Save</button></div>' +
            '<div id="ct-resume-out" style="margin-top:1rem;display:none"><div class="ct-head"><strong>Preview</strong><a id="ct-resume-dl" class="ct-link" href="#" target="_blank">Download PDF</a></div><div class="ct-preview" id="ct-resume-preview"></div></div>',
            true
        );
        document.getElementById('ct-resume-go').onclick = async function () {
            var btn = this; btn.disabled = true; btn.textContent = 'Generating…';
            try {
                var res = await fetch('/job-seeker/career-tools/resume', {
                    method: 'POST', credentials: 'include', headers: headers(true),
                    body: JSON.stringify({
                        template: document.getElementById('ct-resume-template').value,
                        headline: document.getElementById('ct-resume-headline').value,
                        summary: document.getElementById('ct-resume-summary').value,
                        save: true
                    })
                });
                var data = await res.json();
                if (!res.ok) throw new Error(data.message || 'Failed');
                document.getElementById('ct-resume-out').style.display = '';
                document.getElementById('ct-resume-preview').innerHTML = data.html;
                if (data.document?.download_url) document.getElementById('ct-resume-dl').href = data.document.download_url;
                if (data.document) { state.documents.unshift(data.document); renderDocs(); }
                toastOk('Resume generated and saved');
            } catch (e) { toastErr(e.message || 'Could not generate resume'); }
            finally { btn.disabled = false; btn.textContent = 'Generate & Save'; }
        };
    }

    function openCover() {
        openModal('Cover Letter Builder',
            '<div class="ct-field"><label>Job title</label><input id="ct-cover-title" placeholder="e.g. Software Developer"></div>' +
            '<div class="ct-field"><label>Company</label><input id="ct-cover-company" placeholder="Company name"></div>' +
            '<div class="ct-field"><label>Tone</label><select id="ct-cover-tone"><option value="professional">Professional</option><option value="enthusiastic">Enthusiastic</option><option value="concise">Concise</option></select></div>' +
            '<div class="ct-field"><label>Extra highlights (optional)</label><textarea id="ct-cover-highlights" rows="3" placeholder="Projects, metrics, or achievements to emphasize"></textarea></div>' +
            '<div class="ct-actions"><button type="button" class="ct-btn-secondary" onclick="ctCloseModal()">Cancel</button><button type="button" class="ct-btn" style="width:auto" id="ct-cover-go">Create Letter</button></div>' +
            '<div id="ct-cover-out" style="margin-top:1rem;display:none"><div class="ct-head"><strong>Preview</strong><a id="ct-cover-dl" class="ct-link" href="#" target="_blank">Download PDF</a></div><div class="ct-preview" id="ct-cover-preview"></div></div>',
            true
        );
        document.getElementById('ct-cover-go').onclick = async function () {
            var btn = this; btn.disabled = true; btn.textContent = 'Writing…';
            try {
                var res = await fetch('/job-seeker/career-tools/cover-letter', {
                    method: 'POST', credentials: 'include', headers: headers(true),
                    body: JSON.stringify({
                        job_title: document.getElementById('ct-cover-title').value,
                        company: document.getElementById('ct-cover-company').value,
                        tone: document.getElementById('ct-cover-tone').value,
                        highlights: document.getElementById('ct-cover-highlights').value,
                        save: true
                    })
                });
                var data = await res.json();
                if (!res.ok) throw new Error(data.message || Object.entries(data.errors || {}).flat().join(' ') || 'Failed');
                document.getElementById('ct-cover-out').style.display = '';
                document.getElementById('ct-cover-preview').innerHTML = data.html;
                if (data.document?.download_url) document.getElementById('ct-cover-dl').href = data.document.download_url;
                if (data.document) { state.documents.unshift(data.document); renderDocs(); }
                toastOk('Cover letter ready');
            } catch (e) { toastErr(e.message || 'Could not create letter'); }
            finally { btn.disabled = false; btn.textContent = 'Create Letter'; }
        };
    }

    function renderInterviewCard() {
        var q = interviewQs[interviewIdx];
        if (!q) return '';
        return '<div class="ct-q is-active"><div style="font-size:.75rem;color:#6b7280;margin-bottom:.35rem">Question ' + (interviewIdx + 1) + ' of ' + interviewQs.length + '</div><h4 style="margin:0 0 .75rem;color:#111827">' + esc(q.q) + '</h4><p style="margin:0;font-size:.875rem;color:#2563eb"><strong>Tip:</strong> ' + esc(q.tip) + '</p></div>' +
            '<div class="ct-actions" style="justify-content:space-between"><button type="button" class="ct-btn-secondary" id="ct-int-prev"' + (interviewIdx === 0 ? ' disabled' : '') + '>Previous</button><button type="button" class="ct-btn" style="width:auto" id="ct-int-next">' + (interviewIdx >= interviewQs.length - 1 ? 'Finish' : 'Next') + '</button></div>';
    }

    function openInterview() {
        openModal('Interview Prep',
            '<div class="ct-field"><label>Focus area</label><select id="ct-int-role"><option value="general">General</option><option value="software">Software / Tech</option><option value="hospitality">Hospitality</option></select></div>' +
            '<div class="ct-actions" style="justify-content:flex-start"><button type="button" class="ct-btn" style="width:auto" id="ct-int-start">Start Practice</button></div>' +
            '<div id="ct-int-stage" style="margin-top:1rem"></div>',
            true
        );
        document.getElementById('ct-int-start').onclick = async function () {
            var role = document.getElementById('ct-int-role').value;
            var res = await fetch('/job-seeker/career-tools/interview?role=' + encodeURIComponent(role), { credentials: 'include', headers: headers(false) });
            var data = await res.json();
            interviewQs = data.questions || [];
            interviewIdx = 0;
            var tips = (data.tips || []).map(function (t) { return '<li style="margin-bottom:.35rem">' + esc(t) + '</li>'; }).join('');
            document.getElementById('ct-int-stage').innerHTML = '<div class="ct-panel" style="padding:1rem;margin-bottom:1rem"><strong>Quick tips</strong><ul style="margin:.5rem 0 0;padding-left:1.1rem;color:#4b5563;font-size:.875rem">' + tips + '</ul></div><div id="ct-int-card"></div>';
            bindInterviewNav();
        };
    }
    function bindInterviewNav() {
        var card = document.getElementById('ct-int-card');
        if (!card) return;
        card.innerHTML = renderInterviewCard();
        var prev = document.getElementById('ct-int-prev');
        var next = document.getElementById('ct-int-next');
        if (prev) prev.onclick = function () { interviewIdx = Math.max(0, interviewIdx - 1); bindInterviewNav(); };
        if (next) next.onclick = function () {
            if (interviewIdx >= interviewQs.length - 1) { toastOk('Great practice session — you are interview ready!'); ctCloseModal(); return; }
            interviewIdx++; bindInterviewNav();
        };
    }

    function openSalary() {
        var roleOpts = (state.roles || []).map(function (r) { return '<option value="' + esc(r) + '">' + esc(r) + '</option>'; }).join('');
        var p = state.profile || {};
        openModal('Salary Calculator',
            '<div class="ct-field"><label>Role</label><select id="ct-sal-role">' + roleOpts + '</select></div>' +
            '<div class="ct-field"><label>Experience</label><select id="ct-sal-exp"><option value="entry">Entry</option><option value="mid" selected>Mid</option><option value="senior">Senior</option><option value="lead">Lead</option></select></div>' +
            '<div class="ct-field"><label>Location</label><input id="ct-sal-loc" value="' + esc(p.location || 'Victoria, Mahé') + '"></div>' +
            '<div class="ct-field"><label>Education</label><input id="ct-sal-edu" placeholder="e.g. Bachelor"></div>' +
            '<div class="ct-actions"><button type="button" class="ct-btn" style="width:auto" id="ct-sal-go">Calculate</button></div>' +
            '<div id="ct-sal-out" style="margin-top:1rem"></div>',
            false
        );
        document.getElementById('ct-sal-go').onclick = async function () {
            var btn = this; btn.disabled = true;
            try {
                var res = await fetch('/job-seeker/career-tools/salary', {
                    method: 'POST', credentials: 'include', headers: headers(true),
                    body: JSON.stringify({
                        role: document.getElementById('ct-sal-role').value,
                        experience: document.getElementById('ct-sal-exp').value,
                        location: document.getElementById('ct-sal-loc').value,
                        education: document.getElementById('ct-sal-edu').value
                    })
                });
                var data = await res.json();
                if (!res.ok) throw new Error(data.message || 'Failed');
                var r = data.range;
                document.getElementById('ct-sal-out').innerHTML =
                    '<div class="ct-salary"><div><span style="font-size:.75rem;color:#6b7280">Low</span><strong>SCR ' + Number(r.low).toLocaleString() + '</strong></div>' +
                    '<div><span style="font-size:.75rem;color:#6b7280">Market mid</span><strong>SCR ' + Number(r.mid).toLocaleString() + '</strong></div>' +
                    '<div><span style="font-size:.75rem;color:#6b7280">High</span><strong>SCR ' + Number(r.high).toLocaleString() + '</strong></div></div>' +
                    '<p style="margin:1rem 0 0;font-size:.875rem;color:#4b5563">' + esc(data.advice) + '</p>' +
                    (data.live_market_average ? '<p style="margin:.5rem 0 0;font-size:.875rem;color:#2563eb">Live posted jobs average nearby: SCR ' + Number(data.live_market_average).toLocaleString() + '</p>' : '') +
                    (data.your_expectation?.min || data.your_expectation?.max ? '<p style="margin:.5rem 0 0;font-size:.875rem;color:#6b7280">Your profile expectation: SCR ' + (data.your_expectation.min || '—') + ' – ' + (data.your_expectation.max || '—') + '</p>' : '');
            } catch (e) { toastErr(e.message || 'Calculation failed'); }
            finally { btn.disabled = false; }
        };
    }

    function openAssessment() {
        var opts = (state.assessmentTopics || []).map(function (t) {
            return '<option value="' + esc(t.id) + '">' + esc(t.label) + '</option>';
        }).join('');
        openModal('Skill Assessments',
            '<div class="ct-field"><label>Assessment</label><select id="ct-as-topic">' + opts + '</select></div>' +
            '<div class="ct-actions" style="justify-content:flex-start"><button type="button" class="ct-btn" style="width:auto" id="ct-as-start">Begin</button></div>' +
            '<div id="ct-as-stage" style="margin-top:1rem"></div>',
            true
        );
        document.getElementById('ct-as-start').onclick = async function () {
            var topic = document.getElementById('ct-as-topic').value;
            var res = await fetch('/job-seeker/career-tools/assessments/' + encodeURIComponent(topic), { credentials: 'include', headers: headers(false) });
            var data = await res.json();
            if (!res.ok) { toastErr(data.message || 'Failed'); return; }
            assessment = data;
            var html = '<p style="font-size:.875rem;color:#6b7280;margin:0 0 1rem">Pass mark: ' + data.pass_score + '%. Answer all questions, then submit.</p>';
            (data.questions || []).forEach(function (q, i) {
                html += '<div class="ct-q"><div style="font-weight:600;margin-bottom:.5rem">' + (i + 1) + '. ' + esc(q.prompt) + '</div>';
                (q.choices || []).forEach(function (c, ci) {
                    html += '<label class="ct-choice"><input type="radio" name="as-' + esc(q.id) + '" value="' + ci + '"> <span>' + esc(c) + '</span></label>';
                });
                html += '</div>';
            });
            html += '<div class="ct-actions"><button type="button" class="ct-btn" style="width:auto" id="ct-as-submit">Submit Assessment</button></div><div id="ct-as-result"></div>';
            document.getElementById('ct-as-stage').innerHTML = html;
            document.getElementById('ct-as-submit').onclick = submitAssessment;
        };
    }

    async function submitAssessment() {
        if (!assessment) return;
        var answers = {};
        (assessment.questions || []).forEach(function (q) {
            var el = document.querySelector('input[name="as-' + q.id + '"]:checked');
            if (el) answers[q.id] = Number(el.value);
        });
        if (Object.keys(answers).length < (assessment.questions || []).length) {
            toastErr('Please answer every question');
            return;
        }
        var res = await fetch('/job-seeker/career-tools/assessments/' + encodeURIComponent(assessment.topic) + '/submit', {
            method: 'POST', credentials: 'include', headers: headers(true),
            body: JSON.stringify({ answers: answers })
        });
        var data = await res.json();
        if (!res.ok) { toastErr(data.message || 'Submit failed'); return; }
        var review = (data.review || []).map(function (r) {
            return '<div class="ct-q" style="border-color:' + (r.correct ? '#86efac' : '#fca5a5') + '"><strong>' + esc(r.prompt) + '</strong><div style="font-size:.875rem;margin-top:.35rem">' +
                (r.correct ? '✓ Correct' : '✗ Your answer: ' + esc(r.your_choice || '—') + ' · Correct: ' + esc(r.correct_choice)) +
                '</div><div style="font-size:.8rem;color:#6b7280;margin-top:.25rem">' + esc(r.explain) + '</div></div>';
        }).join('');
        document.getElementById('ct-as-result').innerHTML =
            '<div class="ct-panel" style="margin-top:1rem"><h4 style="margin:0 0 .5rem">Score: ' + data.score + '% (' + data.correct + '/' + data.total + ')</h4><p style="margin:0 0 1rem;color:#4b5563">' + esc(data.message) + '</p>' +
            (data.document ? '<a class="ct-link" href="' + esc(data.document.download_url) + '" target="_blank">Download certificate PDF</a>' : '') +
            '</div>' + review;
        if (data.document) { state.documents.unshift(data.document); renderDocs(); }
        toastOk(data.passed ? 'Assessment passed!' : 'Assessment complete');
    }

    async function openPaths() {
        openModal('Career Path Explorer', '<div class="ct-empty">Loading paths…</div>', true);
        var res = await fetch('/job-seeker/career-tools/paths', { credentials: 'include', headers: headers(false) });
        var data = await res.json();
        if (!res.ok) { toastErr('Could not load paths'); return; }
        var html = '<p style="margin:0 0 1rem;font-size:.875rem;color:#6b7280">Matched against your profile skills' +
            ((data.your_skills || []).length ? ': ' + data.your_skills.map(esc).join(', ') : '') + '.</p>';
        (data.paths || []).forEach(function (p) {
            html += '<div class="ct-path"><div style="display:flex;justify-content:space-between;gap:1rem;align-items:flex-start"><div><h4 style="margin:0 0 .35rem">' + esc(p.title) + '</h4><p style="margin:0;font-size:.875rem;color:#4b5563">' + esc(p.summary) + '</p></div><strong style="color:#2563eb">' + p.match_percent + '%</strong></div>' +
                '<div class="ct-bar"><i style="width:' + p.match_percent + '%"></i></div>' +
                '<div style="margin-top:.75rem;font-size:.8rem;color:#6b7280">Skills: ' + (p.skills || []).map(esc).join(', ') + '</div>' +
                '<ul style="margin:.5rem 0 0;padding-left:1.1rem;font-size:.875rem;color:#374151">' + (p.next_steps || []).map(function (s) { return '<li>' + esc(s) + '</li>'; }).join('') + '</ul></div>';
        });
        if ((data.related_jobs || []).length) {
            html += '<h4 style="margin:1.25rem 0 .5rem">Related live jobs</h4>';
            data.related_jobs.forEach(function (j) {
                html += '<div class="ct-doc"><div><strong>' + esc(j.title) + '</strong><div style="font-size:.8rem;color:#6b7280">' + esc(j.company) + (j.location ? ' · ' + esc(j.location) : '') + '</div></div><a class="ct-link" href="' + esc(j.url) + '">View</a></div>';
            });
        }
        document.getElementById('ct-modal-body').innerHTML = html;
    }

    window.ctOpenCourses = function () {
        var list = state.courses || [];
        var html = list.length ? list.map(function (c) {
            return '<div class="ct-path"><h4 style="margin:0 0 .35rem">' + esc(c.title) + '</h4><p style="margin:0;font-size:.875rem;color:#4b5563">' + esc(c.description || 'Professional learning resource') + '</p>' +
                '<div style="margin-top:.5rem;font-size:.8rem;color:#6b7280">' + esc(c.provider || 'JobHub Learning') + (c.level ? ' · ' + esc(c.level) : '') + (c.duration ? ' · ' + esc(c.duration) : '') + '</div>' +
                (c.url ? '<div style="margin-top:.5rem"><a class="ct-link" href="' + esc(c.url) + '" target="_blank" rel="noopener">Open course</a></div>' : '') +
                '</div>';
        }).join('') : '<div class="ct-empty">No courses published yet. Check back soon — assessments and career paths still help you grow today.</div>';
        openModal('Browse Courses', html, true);
    };

    window.ctDeleteDoc = async function (id) {
        var ok = await window.showConfirmDialog('This document will be permanently deleted.', {
            title: 'Delete document?',
            confirmText: 'Delete',
            cancelText: 'Cancel'
        });
        if (!ok) return;
        var res = await fetch('/job-seeker/career-tools/documents/' + id, { method: 'DELETE', credentials: 'include', headers: headers(false) });
        if (!res.ok) { toastErr('Could not delete'); return; }
        state.documents = state.documents.filter(function (d) { return String(d.id) !== String(id); });
        renderDocs();
        toastOk('Document deleted');
    };

    renderTools();
    loadBootstrap().then(function () {
        var initial = @json($initialTool);
        if (initial) ctOpenTool(initial);
    }).catch(function () {
        toastErr('Could not load career tools data');
        renderDocs();
    });
})();
</script>
@endsection
