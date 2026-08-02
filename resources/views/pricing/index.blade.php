@extends('layouts.app')

@section('title', 'Pricing')

@section('content')
@php
    $colorStyles = [
        'blue' => [
            'badge' => 'bg-blue-50 dark:bg-blue-900/30',
            'badgeText' => 'text-blue-700 dark:text-blue-300',
            'acBadge' => 'bg-blue-100 dark:bg-blue-800/40',
            'acText' => 'text-blue-800 dark:text-blue-200',
            'iconBg' => 'bg-blue-100 dark:bg-blue-900/40',
            'iconColor' => 'text-blue-600 dark:text-blue-400',
            'popularRing' => '',
        ],
        'amber' => [
            'badge' => 'bg-blue-50 dark:bg-blue-900/30',
            'badgeText' => 'text-blue-700 dark:text-blue-300',
            'acBadge' => 'bg-blue-100 dark:bg-blue-800/40',
            'acText' => 'text-blue-800 dark:text-blue-200',
            'iconBg' => 'bg-amber-100 dark:bg-amber-900/40',
            'iconColor' => 'text-amber-600 dark:text-amber-400',
            'popularRing' => 'ring-2 ring-blue-500',
        ],
        'rose' => [
            'badge' => 'bg-blue-50 dark:bg-blue-900/30',
            'badgeText' => 'text-blue-700 dark:text-blue-300',
            'acBadge' => 'bg-blue-100 dark:bg-blue-800/40',
            'acText' => 'text-blue-800 dark:text-blue-200',
            'iconBg' => 'bg-rose-100 dark:bg-rose-900/40',
            'iconColor' => 'text-rose-600 dark:text-rose-400',
            'popularRing' => '',
        ],
    ];
    $planCols = $plans->take(3)->values();
@endphp

<div id="pricing-page" class="min-h-screen bg-gray-50 dark:bg-gray-900 transition-colors duration-200">
    <script type="application/json" id="pricing-plans-data">@json($plansJson)</script>

    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        {{-- Header --}}
        <div class="text-center mb-12">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-semibold tracking-widest uppercase bg-blue-100 dark:bg-blue-900/40 text-blue-700 dark:text-blue-300 rounded-full mb-4">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Plans &amp; Pricing
            </span>
            <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-4">Find the right plan for your hiring needs</h1>
            <p class="text-lg text-gray-500 dark:text-gray-400 max-w-2xl mx-auto">
                From growing businesses to enterprise-level hiring, we have a plan to help you find the best talent efficiently.
            </p>
        </div>

        {{-- Plan cards (Bolt: prices only, no CTA) --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
            @foreach($planCols as $plan)
                @php $style = $colorStyles[$plan['color']] ?? $colorStyles['blue']; @endphp
                <div class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden {{ $plan['popular'] ? $style['popularRing'] : '' }}">
                    @if($plan['popular'])
                        <div class="absolute top-0 inset-x-0 flex justify-center">
                            <span class="bg-blue-500 text-white text-xs font-bold px-4 py-1 rounded-b-lg tracking-wide uppercase">Most Popular</span>
                        </div>
                    @endif
                    <div class="px-6 {{ $plan['popular'] ? 'pt-10' : 'pt-8' }} pb-6 border-b border-gray-100 dark:border-gray-700">
                        <div class="flex items-center gap-3 mb-3">
                            <span class="inline-flex items-center justify-center w-10 h-10 rounded-xl {{ $style['iconBg'] }}">
                                @if(($plan['color'] ?? '') === 'amber')
                                    <svg class="w-5 h-5 {{ $style['iconColor'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                @elseif(($plan['color'] ?? '') === 'rose')
                                    <svg class="w-5 h-5 {{ $style['iconColor'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
                                @else
                                    <svg class="w-5 h-5 {{ $style['iconColor'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                                @endif
                            </span>
                            <h2 class="text-xl font-bold text-gray-900 dark:text-white">{{ $plan['name'] }}</h2>
                        </div>
                        <p class="text-sm leading-relaxed text-gray-500 dark:text-gray-400">{{ $plan['description'] }}</p>
                    </div>
                    <div class="px-6 py-5 space-y-3">
                        <div class="flex items-center justify-between rounded-lg px-4 py-2.5 {{ $style['badge'] }}">
                            <span class="text-sm font-medium {{ $style['badgeText'] }}">SCR Price</span>
                            <span class="text-lg font-bold {{ $style['badgeText'] }}">{{ number_format($plan['scr_price']) }} SCR</span>
                        </div>
                        <div class="flex items-center justify-center">
                            <span class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-widest">or</span>
                        </div>
                        <div class="flex items-center justify-between rounded-lg px-4 py-2.5 {{ $style['acBadge'] }}">
                            <span class="text-sm font-medium {{ $style['acText'] }}">AdCredit Cost</span>
                            <span class="text-lg font-bold {{ $style['acText'] }}">{{ number_format($plan['coins_price']) }} AC</span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Trust logos --}}
        <div class="mt-14 mb-2">
            <p class="text-center text-sm font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-8">Trusted by businesses to hire top talent</p>
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 px-6 py-5">
                <div class="flex items-center justify-around flex-wrap gap-6">
                    <div class="flex items-center gap-2 text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                        <svg class="w-7 h-7" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="16" cy="16" r="13" stroke="currentColor" stroke-width="2"/>
                            <polygon points="16,8 20,13 24,13 21,17 22,22 16,19 10,22 11,17 8,13 12,13" stroke="currentColor" stroke-width="1.5" fill="none"/>
                        </svg>
                        <span class="text-sm font-semibold tracking-wide">Iconic</span>
                    </div>
                    <div class="flex items-center gap-2 text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                        <svg class="w-7 h-7" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M6 26 L6 10 L12 6 L12 22 Z" stroke="currentColor" stroke-width="1.8" fill="none"/>
                            <path d="M14 26 L14 14 L20 10 L20 22 Z" stroke="currentColor" stroke-width="1.8" fill="none"/>
                        </svg>
                        <span class="text-sm font-semibold tracking-wide">Logique</span>
                    </div>
                    <div class="flex items-center gap-2 text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                        <svg class="w-7 h-7" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M16 6 L10 13 L16 11 L22 13 Z" stroke="currentColor" stroke-width="1.8" fill="none"/>
                            <path d="M10 13 L10 22 M22 13 L22 22 M16 11 L16 26" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                        </svg>
                        <span class="text-sm font-bold tracking-widest uppercase">Prelude</span>
                    </div>
                    <div class="flex items-center gap-2 text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                        <svg class="w-7 h-7" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="16" cy="16" r="10" stroke="currentColor" stroke-width="1.8"/>
                            <circle cx="16" cy="16" r="6" stroke="currentColor" stroke-width="1.5"/>
                            <circle cx="16" cy="16" r="2.5" stroke="currentColor" stroke-width="1.5"/>
                        </svg>
                        <span class="text-sm font-semibold tracking-wide">Signet</span>
                    </div>
                    <div class="flex items-center gap-2 text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                        <svg class="w-7 h-7" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <rect x="6" y="10" width="3" height="12" rx="1" stroke="currentColor" stroke-width="1.5"/>
                            <rect x="11" y="7" width="3" height="15" rx="1" stroke="currentColor" stroke-width="1.5"/>
                            <rect x="16" y="12" width="3" height="10" rx="1" stroke="currentColor" stroke-width="1.5"/>
                            <rect x="21" y="9" width="3" height="13" rx="1" stroke="currentColor" stroke-width="1.5"/>
                        </svg>
                        <span class="text-sm font-semibold tracking-wide">Emblem</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Comparison --}}
        @if($planCols->count() >= 1)
        <div class="mt-16 mb-8 text-center">
            <span class="inline-flex items-center gap-1.5 bg-blue-100 dark:bg-blue-900/40 rounded-full px-3 py-1 text-xs font-semibold text-blue-700 dark:text-blue-300 mb-4 uppercase tracking-widest">
                <svg class="w-3.5 h-3.5" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <rect x="1" y="1" width="6" height="6" rx="1" stroke="currentColor" stroke-width="1.5"/>
                    <rect x="9" y="1" width="6" height="6" rx="1" stroke="currentColor" stroke-width="1.5"/>
                    <rect x="1" y="9" width="6" height="6" rx="1" stroke="currentColor" stroke-width="1.5"/>
                    <rect x="9" y="9" width="6" height="6" rx="1" stroke="currentColor" stroke-width="1.5"/>
                </svg>
                Comparison
            </span>
            <h2 class="text-4xl font-bold text-gray-900 dark:text-white mb-3">Compare plans</h2>
            <p class="text-gray-500 dark:text-gray-400 max-w-md mx-auto text-base leading-relaxed">
                See which plan fits your team's goals — from essential job postings to full-featured hiring solutions.
            </p>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full border-collapse min-w-[640px]">
                    <thead>
                        <tr class="border-b border-gray-100 dark:border-gray-700">
                            <th class="text-left px-6 py-5 text-sm font-bold text-gray-800 dark:text-gray-100 w-1/2 sm:w-2/5 border-r border-gray-100 dark:border-gray-700">Feature</th>
                            @foreach($planCols as $plan)
                                @php $style = $colorStyles[$plan['color']] ?? $colorStyles['blue']; @endphp
                                <th class="px-4 py-5 text-center w-[20%] border-r border-gray-100 dark:border-gray-700 last:border-r-0">
                                    <div class="flex flex-col items-center gap-1.5">
                                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg {{ $style['iconBg'] }}">
                                            @if(($plan['color'] ?? '') === 'amber')
                                                <svg class="w-4 h-4 {{ $style['iconColor'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                            @elseif(($plan['color'] ?? '') === 'rose')
                                                <svg class="w-4 h-4 {{ $style['iconColor'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
                                            @else
                                                <svg class="w-4 h-4 {{ $style['iconColor'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                                            @endif
                                        </span>
                                        <span class="text-sm font-bold text-gray-800 dark:text-gray-100">{{ $plan['name'] }}</span>
                                    </div>
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($featureMatrix as $rowIndex => $row)
                            <tr class="border-b border-gray-50 dark:border-gray-700/50 transition-colors duration-150 {{ $rowIndex % 2 === 0 ? 'bg-gray-50/50 dark:bg-gray-800/50' : 'bg-white dark:bg-gray-800' }}">
                                <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300 leading-snug border-r border-gray-100 dark:border-gray-700">
                                    <span class="inline-flex items-center gap-0.5">
                                        {{ $row['label'] }}
                                        <span class="relative group ml-1.5 inline-flex align-middle">
                                            <svg class="w-3.5 h-3.5 text-gray-400 dark:text-gray-500 hover:text-gray-500 cursor-help transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            <span class="pointer-events-none absolute left-1/2 -translate-x-1/2 bottom-full mb-2 w-56 px-3 py-2 rounded-lg text-xs leading-relaxed text-white bg-gray-800 dark:bg-gray-900 shadow-xl border border-gray-700 opacity-0 group-hover:opacity-100 transition-opacity z-20">
                                                {{ $row['tooltip'] }}
                                            </span>
                                        </span>
                                    </span>
                                </td>
                                @foreach($planCols as $i => $plan)
                                    @php $val = $row['values'][$i] ?? null; @endphp
                                    <td class="px-4 py-4 text-center border-r border-gray-100 dark:border-gray-700 last:border-r-0">
                                        @if($val === null)
                                            <div class="flex justify-center">
                                                <svg class="w-4 h-4 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/></svg>
                                            </div>
                                        @elseif($val === true)
                                            <div class="flex justify-center">
                                                <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-green-100 dark:bg-green-900/40">
                                                    <svg class="w-3.5 h-3.5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                                </span>
                                            </div>
                                        @else
                                            <div class="flex justify-center">
                                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $val }}</span>
                                            </div>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        {{-- Cost estimator --}}
        <div class="mt-16">
            <div class="mb-8 text-center">
                <span class="inline-flex items-center gap-1.5 bg-blue-100 dark:bg-blue-900/40 rounded-full px-3 py-1 text-xs font-semibold text-blue-700 dark:text-blue-300 mb-4 uppercase tracking-widest">
                    <svg class="w-3.5 h-3.5" viewBox="0 0 16 16" fill="none"><path d="M8 2v12M2 8h12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                    Cost Estimator
                </span>
                <h2 class="text-4xl font-bold text-gray-900 dark:text-white mb-3">Calculate your Job Ad Cost</h2>
                <p class="text-gray-500 dark:text-gray-400 max-w-md mx-auto text-base leading-relaxed">
                    Drag the slider to set how many job adverts you need, then pick a plan to see the total cost.
                </p>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="px-8 py-8 space-y-8">
                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-sm font-semibold text-gray-700 dark:text-gray-200">Number of job adverts</span>
                            <span id="pricing-ads-count" class="text-3xl font-bold tabular-nums text-blue-600">5</span>
                        </div>
                        <input id="pricing-ads-slider" type="range" min="1" max="30" value="5"
                               class="w-full h-2 rounded-full appearance-none cursor-pointer">
                        <div class="flex justify-between text-xs text-gray-400 dark:text-gray-500 mt-1.5">
                            <span>1 advert</span>
                            <span>30 adverts</span>
                        </div>
                    </div>

                    <div>
                        <p class="text-sm font-semibold text-gray-700 dark:text-gray-200 mb-3">Select a plan</p>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            @foreach($planCols as $index => $plan)
                                @php $style = $colorStyles[$plan['color']] ?? $colorStyles['blue']; @endphp
                                <button type="button" data-pricing-plan-index="{{ $index }}"
                                        class="relative flex flex-col items-center gap-2 rounded-xl px-4 py-4 border border-gray-200 dark:border-gray-700 hover:border-gray-300 dark:hover:border-gray-600 bg-white dark:bg-gray-800 transition-all duration-150">
                                    <span class="inline-flex items-center justify-center w-9 h-9 rounded-lg {{ $style['iconBg'] }}">
                                        @if(($plan['color'] ?? '') === 'amber')
                                            <svg class="w-[18px] h-[18px] {{ $style['iconColor'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                        @elseif(($plan['color'] ?? '') === 'rose')
                                            <svg class="w-[18px] h-[18px] {{ $style['iconColor'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
                                        @else
                                            <svg class="w-[18px] h-[18px] {{ $style['iconColor'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                                        @endif
                                    </span>
                                    <span class="text-sm font-semibold text-gray-800 dark:text-gray-100">{{ $plan['name'] }}</span>
                                    <span class="text-xs text-gray-400 dark:text-gray-500">{{ number_format($plan['scr_price']) }} SCR / ad</span>
                                </button>
                            @endforeach
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-4 p-5 rounded-xl bg-gray-50 dark:bg-gray-700/40 border border-gray-100 dark:border-gray-700">
                        <div class="flex-1">
                            <p class="text-xs text-gray-400 dark:text-gray-500 uppercase tracking-widest font-semibold mb-1">Estimated total</p>
                            <div class="flex items-end gap-2">
                                <span id="pricing-estimate-total" class="text-4xl font-extrabold tabular-nums leading-none text-blue-600">0</span>
                                <span id="pricing-estimate-unit" class="text-lg font-semibold text-gray-400 dark:text-gray-500 mb-0.5">SCR</span>
                            </div>
                            <p id="pricing-estimate-detail" class="text-xs text-gray-400 dark:text-gray-500 mt-1.5"></p>
                        </div>
                        <div class="flex flex-col gap-2 sm:items-end">
                            <p class="text-xs text-gray-400 dark:text-gray-500 font-semibold uppercase tracking-widest">Currency</p>
                            <div class="flex rounded-lg border border-gray-200 dark:border-gray-600 overflow-hidden">
                                <button type="button" data-pricing-currency="SCR" class="px-4 py-2 text-sm font-semibold bg-blue-600 text-white shadow-sm">SCR</button>
                                <button type="button" data-pricing-currency="AC" class="px-4 py-2 text-sm font-semibold bg-white dark:bg-gray-800 text-gray-500 dark:text-gray-400">AC</button>
                            </div>
                            <p id="pricing-estimate-also" class="text-xs text-gray-400 dark:text-gray-500"></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- FAQ --}}
        <div class="mt-16">
            <div class="mb-8 text-center">
                <span class="inline-flex items-center gap-1.5 bg-blue-100 dark:bg-blue-900/40 rounded-full px-3 py-1 text-xs font-semibold text-blue-700 dark:text-blue-300 mb-4 w-fit uppercase tracking-widest">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    FAQ
                </span>
                <h2 class="text-4xl font-bold text-gray-900 dark:text-white mb-3">Frequently asked questions</h2>
                <p class="text-gray-500 dark:text-gray-400 max-w-md mx-auto text-base leading-relaxed">
                    These are the most commonly asked questions about our job ad plans and pricing.
                </p>
            </div>
            <div class="space-y-2">
                @foreach($faqs as $index => $faq)
                    <div data-faq-item class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 transition-colors duration-200 overflow-hidden" data-open="false">
                        <button type="button" data-faq-index="{{ $index }}" class="w-full flex items-center justify-between gap-4 px-5 py-4 text-left">
                            <span class="text-sm font-semibold leading-snug text-gray-700 dark:text-gray-200">{{ $faq['q'] }}</span>
                            <svg data-faq-icon class="w-4 h-4 flex-shrink-0 text-gray-400 dark:text-gray-500 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <div data-faq-panel class="overflow-hidden transition-all duration-200 ease-in-out max-h-0">
                            <p class="px-5 pb-5 text-sm text-gray-500 dark:text-gray-400 leading-relaxed">{{ $faq['a'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <p class="text-center text-sm text-gray-400 dark:text-gray-500 mt-8">
            All plans include a single job advert posting. Prices shown in SCR (Seychellois Rupee) and AC (AdCredits).<br>
            Need a custom plan?
            <a href="{{ $ctaUrl }}"
               @if(!auth()->check()) onclick="event.preventDefault(); window.openAuthModal && window.openAuthModal('login');" @endif
               class="text-blue-600 dark:text-blue-400 hover:underline font-medium">Contact our sales team</a>.
        </p>
    </div>
</div>
@endsection
