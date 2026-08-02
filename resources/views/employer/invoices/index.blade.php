@extends('layouts.employer')

@section('content')
<style>
.inv-page { background:#f9fafb; min-height:100vh; }
.inv-main { padding:2rem; margin-left:16rem; width:100%; flex:1; min-width:0; }
@media (max-width:768px){ .inv-main{ margin-left:0; padding:1rem; } }
.inv-stack { display:flex; flex-direction:column; gap:1.5rem; width:100%; }
.inv-header { display:flex; align-items:center; justify-content:space-between; gap:1rem; flex-wrap:wrap; }
.inv-title { font-size:1.5rem; font-weight:700; color:#111827; margin:0; }
.inv-sub { color:#4b5563; margin:0.25rem 0 0; }
.inv-btn-outline { display:inline-flex; align-items:center; gap:0.5rem; padding:0.5rem 1rem; border:1px solid #e5e7eb; border-radius:0.25rem; background:#fff; color:#374151; font-weight:500; font-size:0.875rem; cursor:pointer; text-decoration:none; }
.inv-btn-outline:hover { background:#f9fafb; }
.inv-stats { display:grid; grid-template-columns:repeat(3,minmax(0,1fr)); gap:1.5rem; }
@media (max-width:768px){ .inv-stats{ grid-template-columns:1fr; } }
.inv-stat { background:#fff; padding:1.5rem; border-radius:0.25rem; border:1px solid #e5e7eb; box-shadow:0 1px 2px rgba(0,0,0,.04); }
.inv-stat-top { display:flex; align-items:center; justify-content:space-between; margin-bottom:1rem; }
.inv-stat-icon { width:3rem; height:3rem; border-radius:0.25rem; display:flex; align-items:center; justify-content:center; color:#fff; }
.inv-stat-val { font-size:1.875rem; font-weight:700; color:#111827; }
.inv-stat-label { font-size:0.875rem; color:#4b5563; margin:0; }
.inv-card { background:#fff; border-radius:0.25rem; border:1px solid #e5e7eb; box-shadow:0 1px 2px rgba(0,0,0,.04); overflow:hidden; }
.inv-toolbar { padding:1rem; border-bottom:1px solid #e5e7eb; display:flex; flex-wrap:wrap; gap:1rem; }
.inv-search { position:relative; flex:1; min-width:200px; }
.inv-search svg { position:absolute; left:0.75rem; top:50%; transform:translateY(-50%); width:1.25rem; height:1.25rem; color:#9ca3af; }
.inv-search input, .inv-select { width:100%; padding:0.5rem 1rem 0.5rem 2.5rem; background:#f9fafb; border:1px solid #e5e7eb; border-radius:0.25rem; font-size:0.875rem; color:#111827; box-sizing:border-box; }
.inv-select { padding-left:1rem; width:auto; min-width:10rem; }
.inv-search input:focus, .inv-select:focus { outline:none; box-shadow:0 0 0 2px #3b82f6; }
.inv-table { width:100%; border-collapse:collapse; }
.inv-table thead { background:#f9fafb; border-bottom:1px solid #e5e7eb; }
.inv-table th { padding:0.75rem 1.5rem; text-align:left; font-size:0.75rem; font-weight:600; color:#4b5563; text-transform:uppercase; letter-spacing:0.05em; }
.inv-table td { padding:1rem 1.5rem; vertical-align:middle; }
.inv-table tbody tr { border-bottom:1px solid #e5e7eb; transition:background .15s; }
.inv-table tbody tr:hover { background:#f9fafb; }
.inv-id { display:flex; align-items:center; gap:0.75rem; }
.inv-id-icon { width:2.5rem; height:2.5rem; border-radius:0.25rem; display:flex; align-items:center; justify-content:center; color:#fff; background:linear-gradient(to bottom right,#2563eb,#06b6d4); flex-shrink:0; }
.inv-badge { display:inline-flex; align-items:center; gap:0.25rem; padding:0.25rem 0.75rem; border-radius:9999px; font-size:0.75rem; font-weight:500; width:fit-content; }
.inv-badge-paid { background:#d1fae5; color:#047857; }
.inv-badge-pending { background:#fef3c7; color:#b45309; }
.inv-badge-failed { background:#fee2e2; color:#b91c1c; }
.inv-actions { display:flex; align-items:center; gap:0.5rem; }
.inv-icon-btn { padding:0.5rem; border:0; background:transparent; border-radius:0.25rem; cursor:pointer; color:#4b5563; }
.inv-icon-btn:hover { background:#f3f4f6; }
.inv-empty { padding:3rem 1.5rem; text-align:center; color:#6b7280; }
.inv-pay-row { display:flex; align-items:center; justify-content:space-between; gap:1rem; padding:1rem; background:#f9fafb; border-radius:0.25rem; flex-wrap:wrap; }
.inv-modal { position:fixed; inset:0; z-index:50; display:none; align-items:center; justify-content:center; padding:1rem; background:rgba(0,0,0,.5); }
.inv-modal.is-open { display:flex; }
.inv-modal-box { background:#fff; border-radius:0.5rem; width:100%; max-width:32rem; max-height:90vh; overflow:auto; box-shadow:0 25px 50px -12px rgba(0,0,0,.25); }
.inv-modal-h { padding:1.25rem 1.5rem; border-bottom:1px solid #e5e7eb; display:flex; justify-content:space-between; align-items:center; }
.inv-modal-b { padding:1.5rem; }
.inv-field { margin-bottom:1rem; }
.inv-field label { display:block; font-size:0.875rem; font-weight:500; color:#374151; margin-bottom:0.35rem; }
.inv-field input, .inv-field select { width:100%; padding:0.5rem 0.75rem; border:1px solid #e5e7eb; border-radius:0.25rem; font-size:0.875rem; box-sizing:border-box; }
.inv-modal-actions { display:flex; justify-content:flex-end; gap:0.5rem; margin-top:1rem; }
.inv-btn-primary { padding:0.5rem 1rem; border:0; border-radius:0.25rem; color:#fff; font-weight:500; cursor:pointer; background:linear-gradient(to right,#2563eb,#06b6d4); }
</style>

<div class="inv-page">
    @include('partials.employer-navbar')
    <div class="flex">
        @include('partials.employer-sidebar')
        <main class="inv-main">
            <div class="inv-stack">
                <div class="inv-header">
                    <div>
                        <h1 class="inv-title">Invoices & Billing History</h1>
                        <p class="inv-sub">View and download all your invoices</p>
                    </div>
                    <a href="{{ route('employer.invoices.export', request()->only(['q','range'])) }}" class="inv-btn-outline" id="btn-export-all">
                        <svg style="width:1rem;height:1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        Export All
                    </a>
                </div>

                @if(session('success'))
                    <div style="padding:1rem;border-radius:0.5rem;background:#ecfdf5;color:#065f46;border:1px solid #a7f3d0;">{{ session('success') }}</div>
                @endif
                @if(session('error'))
                    <div style="padding:1rem;border-radius:0.5rem;background:#fef2f2;color:#991b1b;border:1px solid #fecaca;">{{ session('error') }}</div>
                @endif

                <div class="inv-stats">
                    <div class="inv-stat">
                        <div class="inv-stat-top">
                            <div class="inv-stat-icon" style="background:linear-gradient(to bottom right,#2563eb,#06b6d4);">
                                <svg style="width:1.5rem;height:1.5rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            </div>
                            <span class="inv-stat-val">{{ number_format($totalInvoices) }}</span>
                        </div>
                        <p class="inv-stat-label">Total Invoices</p>
                    </div>
                    <div class="inv-stat">
                        <div class="inv-stat-top">
                            <div class="inv-stat-icon" style="background:linear-gradient(to bottom right,#059669,#14b8a6);">
                                <svg style="width:1.5rem;height:1.5rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                            </div>
                            <span class="inv-stat-val">{{ $currency }} {{ number_format($totalSpent, 2) }}</span>
                        </div>
                        <p class="inv-stat-label">Total Spent</p>
                    </div>
                    <div class="inv-stat">
                        <div class="inv-stat-top">
                            <div class="inv-stat-icon" style="background:linear-gradient(to bottom right,#7c3aed,#a855f7);">
                                <svg style="width:1.5rem;height:1.5rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            </div>
                            <span class="inv-stat-val">{{ $currency }} {{ number_format($lastPaymentTotal, 2) }}</span>
                        </div>
                        <p class="inv-stat-label">Last Payment</p>
                    </div>
                </div>

                <div class="inv-card">
                    <form method="GET" action="{{ route('employer.invoices.index') }}" class="inv-toolbar" id="invoice-filter-form">
                        <div class="inv-search">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            <input type="text" name="q" value="{{ $filters['q'] }}" placeholder="Search invoices..." id="invoice-search">
                        </div>
                        <div style="display:flex;gap:0.5rem;flex-wrap:wrap;">
                            <select name="range" class="inv-select" id="invoice-range">
                                <option value="all" {{ $filters['range'] === 'all' ? 'selected' : '' }}>All Time</option>
                                <option value="30" {{ $filters['range'] === '30' ? 'selected' : '' }}>Last 30 days</option>
                                <option value="90" {{ $filters['range'] === '90' ? 'selected' : '' }}>Last 90 days</option>
                                <option value="year" {{ $filters['range'] === 'year' ? 'selected' : '' }}>This Year</option>
                            </select>
                            <button type="submit" class="inv-btn-outline">
                                <svg style="width:1rem;height:1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                                Filter
                            </button>
                        </div>
                    </form>

                    <div style="overflow-x:auto;">
                        <table class="inv-table">
                            <thead>
                                <tr>
                                    <th>Invoice ID</th>
                                    <th>Date</th>
                                    <th>Description</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($invoices as $inv)
                                    <tr data-invoice-id="{{ $inv['id'] }}">
                                        <td>
                                            <div class="inv-id">
                                                <div class="inv-id-icon">
                                                    <svg style="width:1.25rem;height:1.25rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                                </div>
                                                <span style="font-weight:600;color:#111827;">{{ $inv['invoice_id'] }}</span>
                                            </div>
                                        </td>
                                        <td style="font-size:0.875rem;color:#4b5563;">{{ $inv['date'] }}</td>
                                        <td>
                                            <p style="font-weight:500;color:#111827;margin:0;">{{ $inv['description'] }}</p>
                                            <p style="font-size:0.875rem;color:#6b7280;margin:0;">{{ $inv['payment_method'] }}</p>
                                        </td>
                                        <td>
                                            <p style="font-weight:600;color:#111827;margin:0;">{{ $inv['currency'] }} {{ number_format($inv['total'], 2) }}</p>
                                            <p style="font-size:0.875rem;color:#6b7280;margin:0;">Tax: {{ $inv['currency'] }} {{ number_format($inv['tax'], 2) }}</p>
                                        </td>
                                        <td>
                                            @php
                                                $badgeClass = match($inv['status']) {
                                                    'paid' => 'inv-badge-paid',
                                                    'pending' => 'inv-badge-pending',
                                                    'failed' => 'inv-badge-failed',
                                                    default => 'inv-badge-paid',
                                                };
                                            @endphp
                                            <span class="inv-badge {{ $badgeClass }}">
                                                @if($inv['status'] === 'paid')
                                                    <svg style="width:0.75rem;height:0.75rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4"/></svg>
                                                @endif
                                                {{ $inv['status'] }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="inv-actions">
                                                <button type="button" class="inv-icon-btn btn-view-invoice" data-id="{{ $inv['id'] }}" title="View" aria-label="View invoice">
                                                    <svg style="width:1rem;height:1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                </button>
                                                <a href="{{ route('employer.invoices.download', $inv['id']) }}" class="inv-icon-btn" title="Download" aria-label="Download invoice" style="display:inline-flex;">
                                                    <svg style="width:1rem;height:1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="inv-empty">No invoices found for this filter.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="inv-card">
                    <div style="padding:1.5rem;border-bottom:1px solid #e5e7eb;">
                        <h2 style="font-size:1.125rem;font-weight:700;color:#111827;margin:0;">Payment Method</h2>
                    </div>
                    <div style="padding:1.5rem;">
                        @if($hasCard)
                            <div class="inv-pay-row">
                                <div style="display:flex;align-items:center;gap:1rem;">
                                    <div class="inv-stat-icon" style="background:linear-gradient(to bottom right,#2563eb,#06b6d4);">
                                        <svg style="width:1.5rem;height:1.5rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                                    </div>
                                    <div>
                                        <p id="card-label" style="font-weight:500;color:#111827;margin:0;">{{ $cardBrand }} ending in {{ $cardLast4 }}</p>
                                        <p id="card-exp" style="font-size:0.875rem;color:#4b5563;margin:0;">{{ $cardExp ? 'Expires '.$cardExp : 'No expiry on file' }}</p>
                                    </div>
                                </div>
                                <button type="button" id="btn-update-card" class="inv-btn-outline">Update</button>
                            </div>
                        @else
                            <div class="inv-pay-row">
                                <div>
                                    <p id="card-label" style="font-weight:500;color:#111827;margin:0;">No payment method on file</p>
                                    <p id="card-exp" style="font-size:0.875rem;color:#4b5563;margin:0;">Add a card to use for purchases</p>
                                </div>
                                <button type="button" id="btn-update-card" class="inv-btn-outline">Add</button>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<div id="modal-invoice-view" class="inv-modal" role="dialog" aria-modal="true">
    <div class="inv-modal-box" style="max-width:36rem;">
        <div class="inv-modal-h">
            <h3 style="font-size:1.125rem;font-weight:700;margin:0;">Invoice Details</h3>
            <button type="button" class="inv-icon-btn modal-close" data-close="modal-invoice-view" aria-label="Close">
                <svg style="width:1.25rem;height:1.25rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="inv-modal-b" id="invoice-view-body">
            <p style="color:#6b7280;">Loading…</p>
        </div>
    </div>
</div>

<div id="modal-update-card" class="inv-modal" role="dialog" aria-modal="true">
    <div class="inv-modal-box">
        <div class="inv-modal-h">
            <h3 style="font-size:1.125rem;font-weight:700;margin:0;">Update Payment Method</h3>
            <button type="button" class="inv-icon-btn modal-close" data-close="modal-update-card" aria-label="Close">
                <svg style="width:1.25rem;height:1.25rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form id="form-update-card" class="inv-modal-b">
            <div class="inv-field">
                <label for="billing_card_brand">Card Brand</label>
                <select id="billing_card_brand" name="billing_card_brand" required>
                    @foreach(['Visa','Mastercard','Amex','Other'] as $brand)
                        <option value="{{ $brand }}" {{ ($cardBrand ?? '') === $brand ? 'selected' : '' }}>{{ $brand }}</option>
                    @endforeach
                </select>
            </div>
            <div class="inv-field">
                <label for="billing_card_last4">Last 4 Digits</label>
                <input type="text" id="billing_card_last4" name="billing_card_last4" maxlength="4" pattern="\d{4}" value="{{ $cardLast4 ?? '' }}" required>
            </div>
            <div class="inv-field">
                <label for="billing_card_exp">Expiry (MM/YYYY)</label>
                <input type="text" id="billing_card_exp" name="billing_card_exp" placeholder="12/2025" value="{{ $cardExp ?? '' }}" required>
            </div>
            <div class="inv-modal-actions">
                <button type="button" class="inv-btn-outline modal-close" data-close="modal-update-card">Cancel</button>
                <button type="submit" class="inv-btn-primary">Save</button>
            </div>
        </form>
    </div>
</div>

<script>
(function() {
    var showUrl = @json(url('/employer/invoices'));
    var updateUrl = @json(route('employer.invoices.payment-method'));
    var downloadBase = @json(url('/employer/invoices'));

    function openModal(id) { document.getElementById(id).classList.add('is-open'); }
    function closeModal(id) { document.getElementById(id).classList.remove('is-open'); }

    document.querySelectorAll('.modal-close').forEach(function(btn) {
        btn.addEventListener('click', function() { closeModal(this.dataset.close); });
    });
    document.querySelectorAll('.inv-modal').forEach(function(modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === modal) modal.classList.remove('is-open');
        });
    });

    document.getElementById('btn-update-card').addEventListener('click', function() {
        openModal('modal-update-card');
    });

    document.querySelectorAll('.btn-view-invoice').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var id = this.dataset.id;
            var body = document.getElementById('invoice-view-body');
            body.innerHTML = '<p style="color:#6b7280;">Loading…</p>';
            openModal('modal-invoice-view');
            fetch(showUrl + '/' + id, {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(function(r) { return r.json(); })
            .then(function(data) {
                var inv = data.invoice;
                body.innerHTML =
                    '<div style="display:flex;flex-direction:column;gap:0.75rem;">' +
                    '<div style="display:flex;justify-content:space-between;"><span style="color:#6b7280;">Invoice ID</span><strong>' + inv.invoice_id + '</strong></div>' +
                    '<div style="display:flex;justify-content:space-between;"><span style="color:#6b7280;">Date</span><span>' + inv.date + '</span></div>' +
                    '<div style="display:flex;justify-content:space-between;"><span style="color:#6b7280;">Description</span><span>' + inv.description + '</span></div>' +
                    '<div style="display:flex;justify-content:space-between;"><span style="color:#6b7280;">Payment Method</span><span>' + inv.payment_method + '</span></div>' +
                    '<div style="display:flex;justify-content:space-between;"><span style="color:#6b7280;">Subtotal</span><span>' + inv.currency + ' ' + Number(inv.amount).toFixed(2) + '</span></div>' +
                    '<div style="display:flex;justify-content:space-between;"><span style="color:#6b7280;">Tax</span><span>' + inv.currency + ' ' + Number(inv.tax).toFixed(2) + '</span></div>' +
                    '<div style="display:flex;justify-content:space-between;border-top:1px solid #e5e7eb;padding-top:0.75rem;"><span style="font-weight:600;">Total</span><strong>' + inv.currency + ' ' + Number(inv.total).toFixed(2) + '</strong></div>' +
                    '<div style="display:flex;justify-content:space-between;"><span style="color:#6b7280;">Status</span><span>' + inv.status + '</span></div>' +
                    '<a href="' + downloadBase + '/' + inv.id + '/download" class="inv-btn-primary" style="text-align:center;text-decoration:none;margin-top:0.5rem;">Download PDF</a>' +
                    '</div>';
            })
            .catch(function() {
                body.innerHTML = '<p style="color:#b91c1c;">Failed to load invoice.</p>';
            });
        });
    });

    document.getElementById('form-update-card').addEventListener('submit', function(e) {
        e.preventDefault();
        var fd = new FormData(this);
        fd.append('_token', document.querySelector('meta[name="csrf-token"]').content);
        fetch(updateUrl, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: fd
        })
        .then(function(r) { return r.json().then(function(d) { return { ok: r.ok, data: d }; }); })
        .then(function(res) {
            if (res.ok) {
                var c = res.data.card;
                document.getElementById('card-label').textContent = c.brand + ' ending in ' + c.last4;
                document.getElementById('card-exp').textContent = 'Expires ' + c.exp;
                var btn = document.getElementById('btn-update-card');
                if (btn) btn.textContent = 'Update';
                closeModal('modal-update-card');
                if (window.showSuccessToast) window.showSuccessToast(res.data.message || 'Payment method updated.');
                else alert(res.data.message || 'Updated');
                setTimeout(function() { window.location.reload(); }, 400);
            } else {
                var msg = (res.data && res.data.message) || 'Update failed';
                if (res.data && res.data.errors) {
                    msg = Object.values(res.data.errors)[0][0];
                }
                if (window.showErrorToast) window.showErrorToast(msg);
                else alert(msg);
            }
        });
    });
})();
</script>
@endsection
