@extends('layouts.employer')

@section('content')
<style>
.tm-page{background:#f9fafb;min-height:100vh}
.tm-main{padding:2rem;margin-left:16rem;width:100%;flex:1;min-width:0}
@media(max-width:768px){.tm-main{margin-left:0;padding:1rem}}
.tm-stack{display:flex;flex-direction:column;gap:1.5rem;width:100%}
.tm-header{display:flex;align-items:center;justify-content:space-between;gap:1rem;flex-wrap:wrap}
.tm-title{font-size:1.5rem;font-weight:700;color:#111827;margin:0}
.tm-sub{color:#4b5563;margin:.25rem 0 0}
.tm-btn-primary{display:inline-flex;align-items:center;gap:.5rem;padding:.5rem 1rem;border:0;border-radius:.25rem;color:#fff;font-weight:500;cursor:pointer;background:linear-gradient(to right,#2563eb,#06b6d4)}
.tm-btn-primary:hover{box-shadow:0 10px 15px -3px rgba(37,99,235,.35)}
.tm-btn-primary:disabled{opacity:.5;cursor:not-allowed;box-shadow:none}
.tm-stats{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:1rem}
@media(max-width:900px){.tm-stats{grid-template-columns:repeat(2,minmax(0,1fr))}}
.tm-stat{background:#fff;padding:1rem;border-radius:.25rem;border:1px solid #e5e7eb;box-shadow:0 1px 2px rgba(0,0,0,.04)}
.tm-stat-label{font-size:.875rem;color:#4b5563;margin:0 0 .25rem}
.tm-stat-val{font-size:1.5rem;font-weight:700;margin:0}
.tm-grid{display:grid;grid-template-columns:2fr 1fr;gap:1.5rem;align-items:start}
@media(max-width:1024px){.tm-grid{grid-template-columns:1fr}}
.tm-card{background:#fff;border-radius:.25rem;border:1px solid #e5e7eb;box-shadow:0 1px 2px rgba(0,0,0,.04);overflow:hidden}
.tm-card-h{padding:1.5rem;border-bottom:1px solid #e5e7eb}
.tm-card-h h2{font-size:1.125rem;font-weight:700;color:#111827;margin:0}
.tm-list{padding:1rem;display:flex;flex-direction:column;gap:.75rem}
.tm-member{position:relative;border-radius:.5rem;border:1px solid rgba(229,231,235,.9);background:linear-gradient(to bottom right,#f9fafb,#f3f4f6);padding:1.25rem;transition:all .2s;overflow:hidden}
.tm-member:hover{box-shadow:0 4px 6px -1px rgba(0,0,0,.1);border-color:#93c5fd}
.tm-member-top{display:flex;align-items:flex-start;justify-content:space-between;gap:1rem}
.tm-identity{display:flex;gap:1rem;align-items:flex-start;min-width:0}
.tm-avatar{position:relative;width:3.5rem;height:3.5rem;border-radius:.75rem;display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;font-size:1.125rem;background:linear-gradient(to bottom right,#2563eb,#06b6d4);box-shadow:0 10px 15px -3px rgba(37,99,235,.3);flex-shrink:0}
.tm-crown{position:absolute;top:-.25rem;right:-.25rem;width:1.25rem;height:1.25rem;border-radius:9999px;background:linear-gradient(to bottom right,#fbbf24,#d97706);display:flex;align-items:center;justify-content:center}
.tm-name{font-size:1.125rem;font-weight:700;color:#111827;margin:0}
.tm-email{font-size:.875rem;color:#4b5563;margin:.15rem 0 0}
.tm-badge{display:inline-block;padding:.125rem .625rem;border-radius:.375rem;font-size:.75rem;font-weight:600;margin-left:.5rem;vertical-align:middle}
.tm-badge-admin{background:#fee2e2;color:#b91c1c}
.tm-badge-manager{background:#ede9fe;color:#6d28d9}
.tm-badge-recruiter{background:#dbeafe;color:#1d4ed8}
.tm-badge-viewer{background:#f3f4f6;color:#374151}
.tm-badge-pending{background:#fef3c7;color:#b45309}
.tm-meta{display:flex;flex-wrap:wrap;gap:1.5rem;margin-top:1rem;font-size:.875rem;color:#4b5563}
.tm-meta-item{display:flex;align-items:center;gap:.4rem}
.tm-dot{width:.375rem;height:.375rem;border-radius:9999px}
.tm-actions{display:flex;flex-wrap:wrap;gap:.5rem;margin-top:1rem;align-items:center}
.tm-btn{display:inline-flex;align-items:center;gap:.35rem;padding:.5rem .75rem;border-radius:.5rem;border:1px solid #e5e7eb;background:#fff;color:#374151;font-size:.875rem;font-weight:500;cursor:pointer}
.tm-btn:hover{background:#f9fafb;border-color:#d1d5db}
.tm-btn-danger{background:#fef2f2;border-color:#fecaca;color:#dc2626;margin-left:auto}
.tm-btn-danger:hover{background:#fee2e2}
.tm-roles{padding:1rem;display:flex;flex-direction:column;gap:1rem}
.tm-role-block{padding:1rem;border:1px solid #e5e7eb;border-radius:.5rem;background:#f9fafb}
.tm-role-title{display:flex;align-items:center;gap:.5rem;font-weight:700;color:#111827;margin:0 0 .75rem;text-transform:capitalize}
.tm-perm{display:flex;align-items:center;gap:.5rem;font-size:.875rem;color:#374151;margin:.35rem 0}
.tm-banner{padding:1.5rem;border-radius:.25rem;color:#fff;background:linear-gradient(to right,#2563eb,#06b6d4);box-shadow:0 10px 15px -3px rgba(37,99,235,.3);display:flex;align-items:center;justify-content:space-between;gap:1rem;flex-wrap:wrap}
.tm-banner a,.tm-banner-btn{padding:.75rem 1.5rem;background:#fff;color:#2563eb;border:0;border-radius:.25rem;font-weight:500;text-decoration:none;cursor:pointer;white-space:nowrap}
.tm-modal{position:fixed;inset:0;z-index:50;display:none;align-items:center;justify-content:center;padding:1rem;background:rgba(0,0,0,.5)}
.tm-modal.is-open{display:flex}
.tm-modal-box{background:#fff;border-radius:.5rem;width:100%;max-width:28rem;max-height:90vh;overflow:auto;box-shadow:0 25px 50px -12px rgba(0,0,0,.25)}
.tm-modal-h{padding:1.25rem 1.5rem;border-bottom:1px solid #e5e7eb;display:flex;justify-content:space-between;align-items:center}
.tm-modal-b{padding:1.5rem}
.tm-field{margin-bottom:1rem}
.tm-field label{display:block;font-size:.875rem;font-weight:500;color:#374151;margin-bottom:.35rem}
.tm-field input,.tm-field select,.tm-field textarea{width:100%;padding:.5rem .75rem;border:1px solid #e5e7eb;border-radius:.25rem;font-size:.875rem;box-sizing:border-box}
.tm-modal-actions{display:flex;justify-content:flex-end;gap:.5rem;margin-top:1rem}
.tm-empty{padding:2rem;text-align:center;color:#6b7280}
</style>

<div class="tm-page">
    @include('partials.employer-navbar')
    <div class="flex">
        @include('partials.employer-sidebar')
        <main class="tm-main">
            <div class="tm-stack">
                <div class="tm-header">
                    <div>
                        <h1 class="tm-title">Team Management</h1>
                        <p class="tm-sub">Manage your team members and their permissions</p>
                    </div>
                    @if($canManageTeam && count($assignableRoles))
                        <button type="button" id="btn-invite" class="tm-btn-primary">
                            <svg style="width:1.25rem;height:1.25rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            Invite Member
                        </button>
                    @endif
                </div>

                @if(session('success'))
                    <div style="padding:1rem;border-radius:.5rem;background:#ecfdf5;color:#065f46;border:1px solid #a7f3d0;">{{ session('success') }}</div>
                @endif
                @if(session('error'))
                    <div style="padding:1rem;border-radius:.5rem;background:#fef2f2;color:#991b1b;border:1px solid #fecaca;">{{ session('error') }}</div>
                @endif

                <div class="tm-stats">
                    <div class="tm-stat">
                        <p class="tm-stat-label">Total Members</p>
                        <p class="tm-stat-val" style="color:#111827;" id="stat-total">{{ $stats['total'] }}</p>
                    </div>
                    <div class="tm-stat">
                        <p class="tm-stat-label">Active Members</p>
                        <p class="tm-stat-val" style="color:#059669;" id="stat-active">{{ $stats['active'] }}</p>
                    </div>
                    <div class="tm-stat">
                        <p class="tm-stat-label">Pending Invites</p>
                        <p class="tm-stat-val" style="color:#d97706;" id="stat-pending">{{ $stats['pending'] }}</p>
                    </div>
                    <div class="tm-stat">
                        <p class="tm-stat-label">Jobs Posted</p>
                        <p class="tm-stat-val" style="color:#2563eb;" id="stat-jobs">{{ $stats['jobs'] }}</p>
                    </div>
                </div>

                <div class="tm-grid">
                    <div class="tm-card">
                        <div class="tm-card-h"><h2>Team Members</h2></div>
                        <div class="tm-list" id="members-list">
                            @forelse($members as $m)
                                @php
                                    $badge = \App\Support\TeamPermissions::BADGE_CLASSES[$m->role] ?? 'tm-badge-viewer';
                                    $canEdit = $canManageTeam && in_array($m->role === 'admin' ? 'admin' : $m->role, $assignableRoles, true)
                                        || ($actor->role === 'admin');
                                    if ($actor->role === 'manager' && in_array($m->role, ['admin','manager'], true)) {
                                        $canEdit = false;
                                    }
                                    $canRemove = $canManageTeam && $m->role !== 'admin' && $m->user_id !== $actor->user_id
                                        && !($actor->role === 'manager' && in_array($m->role, ['admin','manager'], true));
                                @endphp
                                <div class="tm-member" data-id="{{ $m->id }}" data-role="{{ $m->role }}" data-name="{{ e($m->name) }}" data-email="{{ e($m->email) }}">
                                    <div class="tm-member-top">
                                        <div class="tm-identity">
                                            <div class="tm-avatar">
                                                {{ $m->initials() }}
                                                @if($m->role === 'admin')
                                                    <span class="tm-crown" title="Admin">
                                                        <svg style="width:.75rem;height:.75rem;color:#fff;" fill="currentColor" viewBox="0 0 20 20"><path d="M2 6l4 3 4-5 4 5 4-3v9H2V6z"/></svg>
                                                    </span>
                                                @endif
                                            </div>
                                            <div>
                                                <p class="tm-name">
                                                    {{ $m->name }}
                                                    <span class="tm-badge {{ $badge }}">{{ $m->role }}</span>
                                                    @if($m->status === 'pending')
                                                        <span class="tm-badge tm-badge-pending">pending</span>
                                                    @elseif($m->status === 'inactive')
                                                        <span class="tm-badge tm-badge-viewer">inactive</span>
                                                    @endif
                                                </p>
                                                <p class="tm-email">{{ $m->email }}</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tm-meta">
                                        <div class="tm-meta-item"><span class="tm-dot" style="background:#2563eb;"></span> Joined <strong style="color:#111827;">{{ optional($m->joined_at ?? $m->created_at)->format('Y-m-d') }}</strong></div>
                                        <div class="tm-meta-item"><span class="tm-dot" style="background:#059669;"></span> Last Active <strong style="color:#111827;">{{ $m->lastActiveLabel() }}</strong></div>
                                        <div class="tm-meta-item"><span class="tm-dot" style="background:#7c3aed;"></span> Jobs Posted <strong style="color:#111827;">{{ $m->jobs_posted }}</strong></div>
                                    </div>
                                    <div class="tm-actions">
                                        @if($canEdit)
                                            <button type="button" class="tm-btn btn-edit-role" data-id="{{ $m->id }}" data-role="{{ $m->role }}" data-name="{{ e($m->name) }}">
                                                <svg style="width:1rem;height:1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                                Edit Role
                                            </button>
                                        @endif
                                        <button type="button" class="tm-btn btn-message" data-id="{{ $m->id }}" data-name="{{ e($m->name) }}" data-email="{{ e($m->email) }}">
                                            <svg style="width:1rem;height:1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                            Send Message
                                        </button>
                                        @if($canRemove)
                                            <button type="button" class="tm-btn tm-btn-danger btn-remove" data-id="{{ $m->id }}" data-name="{{ e($m->name) }}">
                                                <svg style="width:1rem;height:1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                Remove
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            @empty
                                <div class="tm-empty">No team members yet. Invite someone to get started.</div>
                            @endforelse
                        </div>
                    </div>

                    <div class="tm-card">
                        <div class="tm-card-h"><h2>Roles & Permissions</h2></div>
                        <div class="tm-roles">
                            @foreach($rolePermissions as $role => $perms)
                                <div class="tm-role-block">
                                    <p class="tm-role-title">
                                        <svg style="width:1.25rem;height:1.25rem;color:#2563eb;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                        {{ $role }}
                                    </p>
                                    @foreach($perms as $perm)
                                        <div class="tm-perm">
                                            <svg style="width:1rem;height:1rem;color:#059669;flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                            {{ $perm }}
                                        </div>
                                    @endforeach
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="tm-banner">
                    <div>
                        <h2 style="font-size:1.25rem;font-weight:700;margin:0 0 .35rem;">Need to scale your hiring?</h2>
                        <p style="color:#dbeafe;margin:0;">Upgrade to add unlimited team members and advanced permissions</p>
                    </div>
                    <a href="{{ route('pricing.index') }}" class="tm-banner-btn">Upgrade Plan</a>
                </div>
            </div>
        </main>
    </div>
</div>

{{-- Invite --}}
<div id="modal-invite" class="tm-modal">
    <div class="tm-modal-box">
        <div class="tm-modal-h">
            <h3 style="margin:0;font-weight:700;">Invite Member</h3>
            <button type="button" class="tm-btn modal-close" data-close="modal-invite">✕</button>
        </div>
        <form id="form-invite" class="tm-modal-b">
            <div class="tm-field"><label>Name</label><input type="text" name="name" required></div>
            <div class="tm-field"><label>Email</label><input type="email" name="email" required></div>
            <div class="tm-field">
                <label>Role</label>
                <select name="role" required>
                    @foreach($assignableRoles as $r)
                        <option value="{{ $r }}">{{ ucfirst($r) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="tm-modal-actions">
                <button type="button" class="tm-btn modal-close" data-close="modal-invite">Cancel</button>
                <button type="submit" class="tm-btn-primary">Send Invite</button>
            </div>
        </form>
    </div>
</div>

{{-- Edit role --}}
<div id="modal-role" class="tm-modal">
    <div class="tm-modal-box">
        <div class="tm-modal-h">
            <h3 style="margin:0;font-weight:700;">Edit Role</h3>
            <button type="button" class="tm-btn modal-close" data-close="modal-role">✕</button>
        </div>
        <form id="form-role" class="tm-modal-b">
            <input type="hidden" name="id" id="role-member-id">
            <p style="margin:0 0 1rem;color:#4b5563;">Member: <strong id="role-member-name"></strong></p>
            <div class="tm-field">
                <label>Role</label>
                <select name="role" id="role-select" required>
                    @foreach($assignableRoles as $r)
                        <option value="{{ $r }}">{{ ucfirst($r) }}</option>
                    @endforeach
                    @if($actor->role === 'admin')
                        {{-- admin can assign any role including ones already listed --}}
                    @endif
                </select>
            </div>
            <div class="tm-modal-actions">
                <button type="button" class="tm-btn modal-close" data-close="modal-role">Cancel</button>
                <button type="submit" class="tm-btn-primary">Update Role</button>
            </div>
        </form>
    </div>
</div>

{{-- Message --}}
<div id="modal-message" class="tm-modal">
    <div class="tm-modal-box">
        <div class="tm-modal-h">
            <h3 style="margin:0;font-weight:700;">Send Message</h3>
            <button type="button" class="tm-btn modal-close" data-close="modal-message">✕</button>
        </div>
        <form id="form-message" class="tm-modal-b">
            <input type="hidden" name="id" id="msg-member-id">
            <p style="margin:0 0 1rem;color:#4b5563;">To: <strong id="msg-member-name"></strong></p>
            <div class="tm-field"><label>Subject</label><input type="text" name="subject" required value="Message from {{ $companyName }}"></div>
            <div class="tm-field"><label>Message</label><textarea name="body" rows="5" required></textarea></div>
            <div class="tm-modal-actions">
                <button type="button" class="tm-btn modal-close" data-close="modal-message">Cancel</button>
                <button type="submit" class="tm-btn-primary">Send</button>
            </div>
        </form>
    </div>
</div>

{{-- Remove --}}
<div id="modal-remove" class="tm-modal">
    <div class="tm-modal-box">
        <div class="tm-modal-h">
            <h3 style="margin:0;font-weight:700;">Remove Member</h3>
            <button type="button" class="tm-btn modal-close" data-close="modal-remove">✕</button>
        </div>
        <div class="tm-modal-b">
            <p>Remove <strong id="remove-member-name"></strong> from the team? This cannot be undone.</p>
            <input type="hidden" id="remove-member-id">
            <div class="tm-modal-actions">
                <button type="button" class="tm-btn modal-close" data-close="modal-remove">Cancel</button>
                <button type="button" id="btn-confirm-remove" class="tm-btn tm-btn-danger" style="margin-left:0;">Remove</button>
            </div>
        </div>
    </div>
</div>

<script>
(function() {
    var csrf = document.querySelector('meta[name="csrf-token"]').content;
    var routes = {
        invite: @json(route('employer.team.invite')),
        role: @json(url('/employer/team')),
        message: @json(url('/employer/team')),
    };

    function openModal(id){ document.getElementById(id).classList.add('is-open'); }
    function closeModal(id){ document.getElementById(id).classList.remove('is-open'); }
    function toastOk(m){ if(window.showSuccessToast) window.showSuccessToast(m); else alert(m); }
    function toastErr(m){ if(window.showErrorToast) window.showErrorToast(m); else alert(m); }

    document.querySelectorAll('.modal-close').forEach(function(b){
        b.addEventListener('click', function(){ closeModal(this.dataset.close); });
    });
    document.querySelectorAll('.tm-modal').forEach(function(m){
        m.addEventListener('click', function(e){ if(e.target===m) m.classList.remove('is-open'); });
    });

    var inviteBtn = document.getElementById('btn-invite');
    if (inviteBtn) inviteBtn.addEventListener('click', function(){ openModal('modal-invite'); });

    document.querySelectorAll('.btn-edit-role').forEach(function(btn){
        btn.addEventListener('click', function(){
            document.getElementById('role-member-id').value = this.dataset.id;
            document.getElementById('role-member-name').textContent = this.dataset.name;
            document.getElementById('role-select').value = this.dataset.role;
            openModal('modal-role');
        });
    });

    document.querySelectorAll('.btn-message').forEach(function(btn){
        btn.addEventListener('click', function(){
            document.getElementById('msg-member-id').value = this.dataset.id;
            document.getElementById('msg-member-name').textContent = this.dataset.name + ' (' + this.dataset.email + ')';
            openModal('modal-message');
        });
    });

    document.querySelectorAll('.btn-remove').forEach(function(btn){
        btn.addEventListener('click', function(){
            document.getElementById('remove-member-id').value = this.dataset.id;
            document.getElementById('remove-member-name').textContent = this.dataset.name;
            openModal('modal-remove');
        });
    });

    function postForm(url, formData) {
        return fetch(url, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            body: formData
        }).then(function(r){ return r.json().then(function(d){ return { ok:r.ok, status:r.status, data:d }; }); });
    }

    var formInvite = document.getElementById('form-invite');
    if (formInvite) formInvite.addEventListener('submit', function(e){
        e.preventDefault();
        var fd = new FormData(this);
        fd.append('_token', csrf);
        postForm(routes.invite, fd).then(function(res){
            if(res.ok){ toastOk(res.data.message); closeModal('modal-invite'); setTimeout(function(){ location.reload(); }, 400); }
            else toastErr((res.data && res.data.message) || 'Invite failed');
        });
    });

    document.getElementById('form-role').addEventListener('submit', function(e){
        e.preventDefault();
        var id = document.getElementById('role-member-id').value;
        var fd = new FormData();
        fd.append('_token', csrf);
        fd.append('_method', 'PUT');
        fd.append('role', document.getElementById('role-select').value);
        postForm(routes.role + '/' + id + '/role', fd).then(function(res){
            if(res.ok){ toastOk(res.data.message); closeModal('modal-role'); setTimeout(function(){ location.reload(); }, 400); }
            else toastErr((res.data && res.data.message) || 'Update failed');
        });
    });

    document.getElementById('form-message').addEventListener('submit', function(e){
        e.preventDefault();
        var form = this;
        var id = document.getElementById('msg-member-id').value;
        var fd = new FormData(form);
        fd.append('_token', csrf);
        postForm(routes.message + '/' + id + '/message', fd).then(function(res){
            if(res.ok){ toastOk(res.data.message); closeModal('modal-message'); form.reset(); }
            else toastErr((res.data && res.data.message) || 'Send failed');
        });
    });

    document.getElementById('btn-confirm-remove').addEventListener('click', function(){
        var id = document.getElementById('remove-member-id').value;
        var fd = new FormData();
        fd.append('_token', csrf);
        fd.append('_method', 'DELETE');
        postForm(routes.role + '/' + id, fd).then(function(res){
            if(res.ok){ toastOk(res.data.message); closeModal('modal-remove'); setTimeout(function(){ location.reload(); }, 400); }
            else toastErr((res.data && res.data.message) || 'Remove failed');
        });
    });
})();
</script>
@endsection
