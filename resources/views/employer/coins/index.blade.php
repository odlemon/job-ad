@extends('layouts.employer')

@section('content')
@php
    $defaultPkg = $packages->firstWhere('coins', $defaultAmount) ?? $packages->first();
@endphp
<style>
.cb-page { background:#f9fafb; min-height:100vh; }
.cb-main { padding:2rem; margin-left:16rem; width:100%; flex:1; min-width:0; }
@media (max-width:768px){ .cb-main{ margin-left:0; padding:1rem; } }
.cb-stack { display:flex; flex-direction:column; gap:1.5rem; width:100%; }
.cb-title { font-size:1.5rem; font-weight:700; color:#111827; margin:0; }
.cb-sub { color:#4b5563; margin:0.25rem 0 0; }
.cb-stats { display:grid; grid-template-columns:repeat(3,minmax(0,1fr)); gap:1.5rem; }
@media (max-width:768px){ .cb-stats{ grid-template-columns:1fr; } }
.cb-stat { background:#fff; padding:1.5rem; border-radius:0.25rem; border:1px solid #e5e7eb; box-shadow:0 1px 2px rgba(0,0,0,.04); }
.cb-stat-top { display:flex; align-items:center; justify-content:space-between; margin-bottom:1rem; }
.cb-stat-label { font-size:0.875rem; color:#4b5563; margin:0; text-align:right; }
.cb-stat-val { font-size:2.25rem; font-weight:700; color:#111827; margin:0; line-height:1.2; text-align:right; }
.cb-stat-foot { font-size:0.875rem; color:#4b5563; margin:0; }
.cb-panel { background:#fff; border-radius:0.5rem; border:2px solid #e5e7eb; box-shadow:0 10px 15px -3px rgba(0,0,0,.08); overflow:hidden; }
.cb-panel-h { padding:1.5rem; color:#fff; background:linear-gradient(to right,#2563eb,#06b6d4); }
.cb-panel-h h2 { font-size:1.5rem; font-weight:700; margin:0; }
.cb-panel-h p { color:#dbeafe; margin:0.25rem 0 0; }
.cb-panel-body { padding:2rem; }
.cb-purchase { display:flex; gap:1.5rem; align-items:flex-start; }
@media (max-width:1024px){ .cb-purchase{ flex-direction:column; } }
.cb-left { flex:1; min-width:0; display:flex; flex-direction:column; gap:1.5rem; }
.cb-right { width:400px; flex-shrink:0; }
@media (max-width:1024px){ .cb-right{ width:100%; } }
.cb-rate { border:2px solid #fde68a; border-radius:0.5rem; padding:1rem; background:linear-gradient(to bottom right,#fffbeb,#fff7ed); display:flex; align-items:center; justify-content:space-between; gap:1rem; flex-wrap:wrap; }
.cb-rate h3 { font-size:1rem; font-weight:700; color:#111827; margin:0 0 0.25rem; }
.cb-rate p { font-size:0.875rem; color:#4b5563; margin:0; }
.cb-btn-grad { display:inline-flex; align-items:center; gap:0.5rem; padding:0.5rem 1rem; color:#fff; border:0; border-radius:0.25rem; font-weight:500; cursor:pointer; white-space:nowrap; background:linear-gradient(to right,#2563eb,#06b6d4); }
.cb-btn-grad:hover { box-shadow:0 10px 15px -3px rgba(37,99,235,.35); }
.cb-amounts { display:grid; grid-template-columns:repeat(3,minmax(0,1fr)); gap:1rem; }
@media (max-width:640px){ .cb-amounts{ grid-template-columns:repeat(2,minmax(0,1fr)); } }
.cb-amt { position:relative; padding:1.5rem; border-radius:0.75rem; border:2px solid #e5e7eb; background:#fff; cursor:pointer; transition:all .15s; text-align:center; }
.cb-amt:hover { border-color:#d1d5db; transform:scale(1.05); }
.cb-amt.is-selected { border-color:#3b82f6; box-shadow:0 20px 25px -5px rgba(0,0,0,.1); transform:scale(1.05); }
.cb-disc { position:absolute; top:-0.5rem; right:-0.5rem; padding:0.25rem 0.75rem; font-size:0.75rem; font-weight:700; color:#fff; border-radius:9999px; background:linear-gradient(to right,#10b981,#22c55e); box-shadow:0 10px 15px -3px rgba(16,185,129,.35); }
.cb-check { position:absolute; top:-0.75rem; left:-0.75rem; width:3rem; height:3rem; border-radius:9999px; background:#fff; display:none; align-items:center; justify-content:center; box-shadow:0 20px 25px -5px rgba(0,0,0,.15); border:4px solid #3b82f6; }
.cb-amt.is-selected .cb-check { display:flex; }
.cb-amt-row { display:flex; align-items:center; justify-content:center; gap:0.5rem; margin-bottom:0.75rem; }
.cb-amt-label { font-size:2.25rem; font-weight:700; color:#111827; }
.cb-amt-price { font-size:1.5rem; font-weight:700; color:#111827; margin:0 0 0.15rem; }
.cb-amt-strike { font-size:0.875rem; color:#6b7280; text-decoration:line-through; margin:0; }
.cb-tip { border:1px solid #bfdbfe; border-radius:0.5rem; padding:1rem; background:linear-gradient(to right,#eff6ff,#ecfeff); display:flex; gap:0.75rem; align-items:flex-start; }
.cb-tip-icon { width:2.5rem; height:2.5rem; border-radius:9999px; background:#2563eb; color:#fff; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
.cb-summary { position:sticky; top:1.5rem; border:2px solid #e2e8f0; border-radius:0.5rem; padding:1.5rem; background:linear-gradient(to bottom right,#f8fafc,#f9fafb); }
.cb-summary h3 { font-size:1.125rem; font-weight:700; color:#111827; margin:0 0 1.5rem; }
.cb-sum-row { display:flex; align-items:center; justify-content:space-between; padding-bottom:0.75rem; margin-bottom:1rem; border-bottom:1px solid #e5e7eb; }
.cb-sum-row span.label { font-size:0.875rem; font-weight:500; color:#4b5563; }
.cb-discount { display:none; margin-bottom:1rem; border:2px solid #10b981; border-radius:0.5rem; padding:1rem; background:linear-gradient(to right,#d1fae5,#dcfce7); }
.cb-discount.is-on { display:flex; align-items:center; gap:0.75rem; }
.cb-total-box { background:#fff; border:2px solid #d1d5db; border-radius:0.5rem; padding:1.5rem; margin-bottom:1.5rem; }
.cb-total-val { font-size:3rem; font-weight:700; color:#111827; margin:0.5rem 0; line-height:1; }
.cb-buy { width:100%; padding:1rem; border:0; border-radius:0.5rem; color:#fff; font-weight:700; font-size:1.125rem; cursor:pointer; background:linear-gradient(to right,#2563eb,#06b6d4); margin-bottom:1rem; display:flex; align-items:center; justify-content:center; gap:0.75rem; }
.cb-buy:hover { box-shadow:0 25px 50px -12px rgba(37,99,235,.35); transform:scale(1.02); }
.cb-buy:disabled { opacity:0.6; cursor:not-allowed; transform:none; box-shadow:none; }
.cb-refer { padding:2rem; border-radius:0.25rem; color:#fff; box-shadow:0 10px 15px -3px rgba(37,99,235,.3); background:linear-gradient(to right,#2563eb,#06b6d4); display:flex; align-items:center; justify-content:space-between; gap:1.5rem; }
.cb-refer h2 { font-size:1.5rem; font-weight:700; margin:0 0 0.5rem; }
.cb-refer p { color:#dbeafe; margin:0 0 1rem; }
.cb-refer-btn { padding:0.75rem 1.5rem; background:#fff; color:#2563eb; border:0; border-radius:0.25rem; font-weight:500; cursor:pointer; }
.cb-refer-btn:hover { background:#eff6ff; }
.cb-modal { position:fixed; inset:0; z-index:50; display:none; align-items:center; justify-content:center; padding:1rem; background:rgba(0,0,0,.5); }
.cb-modal.is-open { display:flex; }
.cb-modal-box { background:#fff; border-radius:0.5rem; width:100%; max-width:72rem; max-height:90vh; overflow:auto; box-shadow:0 25px 50px -12px rgba(0,0,0,.25); }
.cb-modal-h { position:sticky; top:0; background:#fff; border-bottom:1px solid #e5e7eb; padding:1.5rem; display:flex; justify-content:space-between; align-items:flex-start; gap:1rem; }
.cb-modal-grid { display:grid; grid-template-columns:repeat(3,minmax(0,1fr)); gap:1.5rem; padding:1.5rem; }
@media (max-width:768px){ .cb-modal-grid{ grid-template-columns:1fr; } }
.cb-modal-card { position:relative; padding:1.5rem; border:2px solid #e5e7eb; border-radius:0.5rem; }
</style>

<div class="cb-page">
    @include('partials.employer-navbar')
    <div class="flex">
        @include('partials.employer-sidebar')
        <main class="cb-main">
            <div class="cb-stack">
                <div>
                    <h1 class="cb-title">Coins & Billing</h1>
                    <p class="cb-sub">Manage your coin balance and purchase packages</p>
                </div>

                @if(session('success'))
                    <div style="padding:1rem;border-radius:0.5rem;background:#ecfdf5;color:#065f46;border:1px solid #a7f3d0;">{{ session('success') }}</div>
                @endif
                @if(session('error'))
                    <div style="padding:1rem;border-radius:0.5rem;background:#fef2f2;color:#991b1b;border:1px solid #fecaca;">{{ session('error') }}</div>
                @endif

                <div class="cb-stats">
                    <div class="cb-stat">
                        <div class="cb-stat-top">
                            <svg style="width:2.5rem;height:2.5rem;color:#f59e0b;" fill="currentColor" viewBox="0 0 20 20"><circle cx="10" cy="10" r="8"/></svg>
                            <div>
                                <p class="cb-stat-label">Current Balance</p>
                                <p class="cb-stat-val" id="stat-balance">{{ number_format($coinBalance) }}</p>
                            </div>
                        </div>
                        <p class="cb-stat-foot">coins available</p>
                    </div>
                    <div class="cb-stat">
                        <div class="cb-stat-top">
                            <svg style="width:2.5rem;height:2.5rem;color:#3b82f6;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                            <div>
                                <p class="cb-stat-label">This Month</p>
                                <p class="cb-stat-val">{{ number_format($thisMonthSpent) }}</p>
                            </div>
                        </div>
                        <p class="cb-stat-foot">coins spent</p>
                    </div>
                    <div class="cb-stat">
                        <div class="cb-stat-top">
                            <svg style="width:2.5rem;height:2.5rem;color:#10b981;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"/></svg>
                            <div>
                                <p class="cb-stat-label">Rewards Earned</p>
                                <p class="cb-stat-val" id="stat-rewards">{{ number_format($rewardsEarned) }}</p>
                            </div>
                        </div>
                        <p class="cb-stat-foot">bonus coins</p>
                    </div>
                </div>

                <div class="cb-panel">
                    <div class="cb-panel-h">
                        <div style="display:flex;align-items:center;gap:0.75rem;margin-bottom:0.5rem;">
                            <svg style="width:2rem;height:2rem;" fill="currentColor" viewBox="0 0 20 20"><circle cx="10" cy="10" r="8"/></svg>
                            <h2>Purchase Coins</h2>
                        </div>
                        <p>Buy coins to power your job campaigns. 1 coin = {{ $scrPerCoin }} SCR</p>
                    </div>
                    <div class="cb-panel-body">
                        <div class="cb-purchase">
                            <div class="cb-left">
                                <div class="cb-rate">
                                    <div>
                                        <h3>Standard Rate</h3>
                                        <p>{{ $scrPerCoin }} SCR = 1 Coin</p>
                                    </div>
                                    <button type="button" id="btn-view-campaign-types" class="cb-btn-grad">
                                        <svg style="width:1rem;height:1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        View Campaign Types
                                    </button>
                                </div>

                                <div>
                                    <h3 style="font-size:1.25rem;font-weight:700;color:#111827;margin:0 0 1rem;">Select Coin Amount</h3>
                                    <div class="cb-amounts">
                                        @foreach($packages as $pkg)
                                            <button type="button"
                                                class="cb-amt coin-amount-btn {{ $pkg['coins'] === $defaultAmount ? 'is-selected' : '' }}"
                                                data-coins="{{ $pkg['coins'] }}"
                                                data-base="{{ $pkg['base_scr'] }}"
                                                data-discount="{{ $pkg['discount_percent'] }}"
                                                data-discount-amount="{{ $pkg['discount_amount'] }}"
                                                data-final="{{ $pkg['final_scr'] }}">
                                                @if($pkg['discount_percent'] > 0)
                                                    <span class="cb-disc">-{{ $pkg['discount_percent'] }}%</span>
                                                @endif
                                                <span class="cb-check">
                                                    <svg style="width:1.5rem;height:1.5rem;color:#2563eb;stroke-width:3;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                                </span>
                                                <div class="cb-amt-row">
                                                    <svg style="width:2.5rem;height:2.5rem;color:#f59e0b;" fill="currentColor" viewBox="0 0 20 20"><circle cx="10" cy="10" r="8"/></svg>
                                                    <span class="cb-amt-label">{{ $pkg['label'] }}</span>
                                                </div>
                                                <p class="cb-amt-price">{{ number_format($pkg['final_scr']) }} SCR</p>
                                                @if($pkg['discount_percent'] > 0)
                                                    <p class="cb-amt-strike">{{ number_format($pkg['base_scr']) }} SCR</p>
                                                @endif
                                            </button>
                                        @endforeach
                                    </div>
                                </div>

                                <div class="cb-tip">
                                    <div class="cb-tip-icon">
                                        <svg style="width:1.25rem;height:1.25rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                    </div>
                                    <div>
                                        <p style="font-weight:600;color:#111827;margin:0 0 0.25rem;">Instant Delivery</p>
                                        <p style="font-size:0.875rem;color:#4b5563;margin:0;">Coins are credited to your account immediately after payment. Use them to launch campaigns and reach top talent faster.</p>
                                    </div>
                                </div>
                            </div>

                            <div class="cb-right">
                                <div class="cb-summary">
                                    <h3>Purchase Summary</h3>
                                    <div class="cb-sum-row">
                                        <span class="label">Coins</span>
                                        <div style="display:flex;align-items:center;gap:0.5rem;">
                                            <svg style="width:1.25rem;height:1.25rem;color:#f59e0b;" fill="currentColor" viewBox="0 0 20 20"><circle cx="10" cy="10" r="8"/></svg>
                                            <span id="sum-coins" style="font-size:1.5rem;font-weight:700;color:#111827;">{{ number_format($defaultAmount) }}</span>
                                        </div>
                                    </div>
                                    <div class="cb-sum-row">
                                        <span class="label">Rate</span>
                                        <span style="font-size:1.125rem;font-weight:600;color:#374151;">{{ $scrPerCoin }} SCR/coin</span>
                                    </div>
                                    <div class="cb-sum-row">
                                        <span class="label">Base Price</span>
                                        <span id="sum-base" style="font-size:1.125rem;font-weight:600;color:#374151;">{{ number_format($defaultAmount * $scrPerCoin) }} SCR</span>
                                    </div>
                                    <div id="sum-discount" class="cb-discount {{ ($defaultPkg['discount_percent'] ?? 0) > 0 ? 'is-on' : '' }}">
                                        <div style="width:2.5rem;height:2.5rem;border-radius:9999px;background:#10b981;color:#fff;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                            <svg style="width:1.25rem;height:1.25rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        </div>
                                        <div>
                                            <p id="sum-discount-label" style="font-size:0.875rem;font-weight:500;color:#047857;margin:0;">{{ $defaultPkg['discount_percent'] ?? 0 }}% Bulk Discount</p>
                                            <p id="sum-discount-amount" style="font-size:1.125rem;font-weight:700;color:#059669;margin:0;">-{{ number_format($defaultPkg['discount_amount'] ?? 0) }} SCR</p>
                                        </div>
                                    </div>
                                    <div class="cb-total-box">
                                        <span style="font-size:0.875rem;font-weight:500;color:#4b5563;">Total to Pay</span>
                                        <p id="sum-total" class="cb-total-val">{{ number_format($defaultPkg['final_scr'] ?? 0) }}</p>
                                        <p style="font-size:0.875rem;color:#6b7280;margin:0;">SCR</p>
                                    </div>
                                    <button type="button" id="btn-complete-purchase" class="cb-buy">
                                        <svg style="width:1.5rem;height:1.5rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                                        <span>Complete Purchase</span>
                                    </button>
                                    <p style="text-align:center;font-size:0.75rem;color:#6b7280;margin:0;">Secure payment • Instant coin delivery</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="cb-refer">
                    <div style="flex:1;">
                        <h2>Refer & Earn Coins!</h2>
                        <p>Invite other companies and earn 50 bonus coins for each successful referral</p>
                        <button type="button" id="btn-referral-link" class="cb-refer-btn">Get Referral Link</button>
                    </div>
                    <svg style="width:8rem;height:8rem;opacity:0.2;flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"/></svg>
                </div>
            </div>
        </main>
    </div>
</div>

<div id="modal-campaign-types" class="cb-modal" role="dialog" aria-modal="true">
    <div class="cb-modal-box">
        <div class="cb-modal-h">
            <div>
                <h2 style="font-size:1.5rem;font-weight:700;color:#111827;margin:0;">Campaign Types</h2>
                <p style="font-size:0.875rem;color:#4b5563;margin:0.25rem 0 0;">Compare features and choose the best campaign for your needs</p>
            </div>
            <button type="button" id="modal-campaign-types-close" style="width:2.5rem;height:2.5rem;border:0;border-radius:9999px;background:transparent;cursor:pointer;color:#4b5563;" aria-label="Close">
                <svg style="width:1.5rem;height:1.5rem;margin:auto;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="cb-modal-grid">
            @foreach($campaignTypes as $type)
                @php
                    $btnBg = match($type->slug) { 'growthhire' => '#2563eb', 'smarthire' => '#059669', 'powerhire' => '#7c3aed', default => '#6b7280' };
                    $iconBg = match($type->slug) {
                        'growthhire' => 'linear-gradient(to bottom right,#2563eb,#06b6d4)',
                        'smarthire' => 'linear-gradient(to bottom right,#059669,#14b8a6)',
                        'powerhire' => 'linear-gradient(to bottom right,#7c3aed,#a855f7)',
                        default => '#6b7280'
                    };
                    $durLabel = $type->slug === 'growthhire' ? '7-15 days' : $type->duration_days . ' days';
                @endphp
                <div class="cb-modal-card">
                    @if($type->is_popular)
                        <span style="position:absolute;top:-0.75rem;left:50%;transform:translateX(-50%);padding:0.25rem 1rem;font-size:0.7rem;font-weight:700;color:#fff;border-radius:9999px;background:linear-gradient(to right,#7c3aed,#a855f7);">MOST POPULAR</span>
                    @endif
                    <div style="text-align:center;margin-bottom:1.5rem;">
                        <div style="width:5rem;height:5rem;margin:0 auto 1rem;border-radius:0.5rem;display:flex;align-items:center;justify-content:center;background:{{ $iconBg }};color:#fff;">
                            <svg style="width:2.5rem;height:2.5rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                        </div>
                        <h3 style="font-size:1.5rem;font-weight:700;color:#111827;margin:0 0 0.5rem;">{{ $type->name }}</h3>
                        <div style="display:flex;align-items:center;justify-content:center;gap:0.5rem;margin-bottom:0.35rem;">
                            <svg style="width:1.5rem;height:1.5rem;color:#f59e0b;" fill="currentColor" viewBox="0 0 20 20"><circle cx="10" cy="10" r="8"/></svg>
                            <span style="font-size:1.875rem;font-weight:700;">{{ $type->coins_price }}</span>
                        </div>
                        <p style="font-size:1.125rem;font-weight:600;color:#374151;margin:0;">SCR {{ number_format($type->scr_price) }}</p>
                        <p style="font-size:0.875rem;color:#6b7280;margin:0.25rem 0 0;">{{ $durLabel }}</p>
                    </div>
                    <ul style="list-style:none;padding:0;margin:0 0 1.25rem;display:flex;flex-direction:column;gap:0.5rem;">
                        @foreach($type->features ?? [] as $f)
                            <li style="display:flex;gap:0.5rem;font-size:0.875rem;color:#374151;">
                                <svg style="width:1rem;height:1rem;color:#10b981;flex-shrink:0;margin-top:0.15rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                {{ $f }}
                            </li>
                        @endforeach
                    </ul>
                    <a href="{{ route('employer.campaigns.create') }}" style="display:block;width:100%;padding:0.625rem;border:0;border-radius:0.5rem;color:#fff;font-weight:500;text-align:center;text-decoration:none;background:{{ $btnBg }};">Select {{ $type->name }}</a>
                </div>
            @endforeach
        </div>
    </div>
</div>

<script>
(function() {
    var selected = {{ (int) $defaultAmount }};
    var referralUrl = @json($referralUrl);
    var purchaseUrl = @json(route('employer.coins.purchase'));

    function fmt(n) {
        return Number(n).toLocaleString('en-US');
    }

    function updateSummary(btn) {
        selected = parseInt(btn.dataset.coins, 10);
        var base = parseInt(btn.dataset.base, 10);
        var disc = parseInt(btn.dataset.discount, 10);
        var discAmt = parseInt(btn.dataset.discountAmount, 10);
        var final = parseInt(btn.dataset.final, 10);

        document.getElementById('sum-coins').textContent = fmt(selected);
        document.getElementById('sum-base').textContent = fmt(base) + ' SCR';
        document.getElementById('sum-total').textContent = fmt(final);

        var discBox = document.getElementById('sum-discount');
        if (disc > 0) {
            discBox.classList.add('is-on');
            document.getElementById('sum-discount-label').textContent = disc + '% Bulk Discount';
            document.getElementById('sum-discount-amount').textContent = '-' + fmt(discAmt) + ' SCR';
        } else {
            discBox.classList.remove('is-on');
        }
    }

    document.querySelectorAll('.coin-amount-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.coin-amount-btn').forEach(function(b) { b.classList.remove('is-selected'); });
            this.classList.add('is-selected');
            updateSummary(this);
        });
    });

    var modal = document.getElementById('modal-campaign-types');
    document.getElementById('btn-view-campaign-types').addEventListener('click', function() {
        modal.classList.add('is-open');
    });
    document.getElementById('modal-campaign-types-close').addEventListener('click', function() {
        modal.classList.remove('is-open');
    });
    modal.addEventListener('click', function(e) {
        if (e.target === modal) modal.classList.remove('is-open');
    });

    document.getElementById('btn-referral-link').addEventListener('click', function() {
        var btn = this;
        function done() {
            if (window.showSuccessToast) window.showSuccessToast('Referral link copied!');
            else alert('Referral link copied:\n' + referralUrl);
            btn.textContent = 'Link Copied!';
            setTimeout(function() { btn.textContent = 'Get Referral Link'; }, 2000);
        }
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(referralUrl).then(done).catch(function() {
                window.prompt('Copy your referral link:', referralUrl);
                done();
            });
        } else {
            window.prompt('Copy your referral link:', referralUrl);
            done();
        }
    });

    document.getElementById('btn-complete-purchase').addEventListener('click', function() {
        var btn = this;
        var orig = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<span>Processing…</span>';

        var fd = new FormData();
        fd.append('_token', document.querySelector('meta[name="csrf-token"]').content);
        fd.append('coins', String(selected));

        fetch(purchaseUrl, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: fd
        })
        .then(function(r) { return r.json().then(function(d) { return { ok: r.ok, data: d }; }).catch(function() { return { ok: r.ok, data: {} }; }); })
        .then(function(res) {
            if (res.ok) {
                if (window.showSuccessToast) window.showSuccessToast(res.data.message || 'Purchase complete!');
                var bal = document.getElementById('stat-balance');
                if (bal && typeof res.data.new_balance !== 'undefined') {
                    bal.textContent = fmt(res.data.new_balance);
                }
                setTimeout(function() { window.location.reload(); }, 600);
            } else {
                var msg = (res.data && (res.data.message || (res.data.errors && Object.values(res.data.errors)[0][0]))) || 'Purchase failed';
                if (window.showErrorToast) window.showErrorToast(msg);
                else alert(msg);
                btn.disabled = false;
                btn.innerHTML = orig;
            }
        })
        .catch(function() {
            btn.disabled = false;
            btn.innerHTML = orig;
        });
    });
})();
</script>
@endsection
