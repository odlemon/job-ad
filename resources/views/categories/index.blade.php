@extends('layouts.app')

@section('title', 'Job Categories')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 transition-colors duration-200">
    {{-- Hero --}}
    <div class="relative bg-gradient-to-r from-blue-600 to-cyan-500 overflow-hidden">
        <div class="absolute inset-0 opacity-10" style="background-image: linear-gradient(rgba(255,255,255,.15) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,.15) 1px, transparent 1px); background-size: 24px 24px;"></div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 relative">
            <div class="text-center">
                <h1 class="text-4xl md:text-5xl font-bold text-white mb-4">Browse Our Job Categories</h1>
                <p class="text-lg md:text-xl text-white/90 max-w-2xl mx-auto">
                    Discover opportunities across {{ count($categories) }} diverse categories with {{ number_format($totalJobs) }} available positions
                </p>
            </div>
        </div>
    </div>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        @if(empty($categories))
            <div class="text-center py-16 text-gray-500 dark:text-gray-400">
                <p class="text-lg">No categories available yet.</p>
                <a href="{{ route('jobs.index') }}" wire:navigate class="inline-flex mt-6 items-center gap-2 px-8 py-4 bg-gradient-to-r from-blue-600 to-cyan-500 text-white font-semibold rounded-md hover:shadow-lg transition-all">
                    View All Jobs
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                </a>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($categories as $index => $category)
                    @php
                        $tone = $palette[$index % count($palette)];
                        $count = (int) $category['jobs_count'];
                        $href = route('jobs.index', ['category_id' => $category['id']]);
                        $icon = $category['icon'] ?? null;
                    @endphp
                    <a href="{{ $href }}" wire:navigate.hover
                       class="group bg-white dark:bg-gray-800 rounded-md shadow-sm dark:shadow-none hover:shadow-lg transition-all duration-200 p-4 text-left border border-gray-200 dark:border-gray-700 hover:border-blue-400 dark:hover:border-cyan-500 block">
                        <div class="flex items-start justify-between mb-3">
                            <div class="{{ $tone['bg'] }} {{ $tone['color'] }} p-2.5 rounded-md">
                                @if(!empty($icon) && str_starts_with($icon, '<'))
                                    {!! $icon !!}
                                @elseif(!empty($icon))
                                    <span class="text-xl leading-none">{{ $icon }}</span>
                                @else
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                    </svg>
                                @endif
                            </div>
                            <svg class="w-4 h-4 text-gray-400 group-hover:text-blue-600 dark:group-hover:text-cyan-400 transform group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </div>
                        <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-1">{{ $category['name'] }}</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            {{ $count }} {{ $count === 1 ? 'job' : 'jobs' }} available
                        </p>
                    </a>
                @endforeach
            </div>

            <div class="mt-12 text-center">
                <a href="{{ route('jobs.index') }}" wire:navigate
                   class="inline-flex items-center gap-2 px-8 py-4 bg-gradient-to-r from-blue-600 to-cyan-500 text-white font-semibold rounded-md hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-200">
                    View All Jobs
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                </a>
            </div>
        @endif
    </main>
</div>
@endsection
