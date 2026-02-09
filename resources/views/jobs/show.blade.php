@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div id="job-detail" class="max-w-7xl mx-auto" data-auto-load="true">
        <!-- Skeleton Loading -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 animate-pulse">
            <div class="lg:col-span-2 space-y-6">
                <div class="h-5 bg-gray-200 rounded w-48"></div>
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <div class="h-8 bg-gray-200 rounded w-3/4 mb-3"></div>
                    <div class="h-5 bg-gray-200 rounded w-1/3 mb-4"></div>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                        <div class="h-16 bg-gray-200 rounded"></div>
                        <div class="h-16 bg-gray-200 rounded"></div>
                        <div class="h-16 bg-gray-200 rounded"></div>
                        <div class="h-16 bg-gray-200 rounded"></div>
                    </div>
                    <div class="flex gap-4">
                        <div class="h-12 bg-gray-200 rounded w-32"></div>
                        <div class="h-12 bg-gray-200 rounded w-32"></div>
                    </div>
                </div>
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <div class="h-6 bg-gray-200 rounded w-48 mb-4"></div>
                    <div class="space-y-2">
                        <div class="h-4 bg-gray-200 rounded w-full"></div>
                        <div class="h-4 bg-gray-200 rounded w-full"></div>
                        <div class="h-4 bg-gray-200 rounded w-3/4"></div>
                        <div class="h-4 bg-gray-200 rounded w-full"></div>
                        <div class="h-4 bg-gray-200 rounded w-5/6"></div>
                    </div>
                </div>
            </div>
            <div class="space-y-6">
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <div class="flex items-center gap-4 mb-4">
                        <div class="w-16 h-16 bg-gray-200 rounded-lg"></div>
                        <div>
                            <div class="h-5 bg-gray-200 rounded w-32 mb-2"></div>
                            <div class="h-4 bg-gray-200 rounded w-24"></div>
                        </div>
                    </div>
                    <div class="h-4 bg-gray-200 rounded w-full mb-2"></div>
                    <div class="h-4 bg-gray-200 rounded w-3/4 mb-4"></div>
                    <div class="h-10 bg-gray-200 rounded w-full"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Note: loadJobDetail and all helper functions are defined in app.js (job-detail.js)
    // The global handler in app.js will automatically load job details on navigation
    // This inline script is kept minimal for initial page loads only
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            // Let app.js handle it, but ensure it runs if needed
            if (window.location.pathname.match(/\/jobs\/\d+$/) && typeof window.loadJobDetail === 'function') {
                const element = document.getElementById('job-detail');
                if (element && element.innerHTML.includes('animate-pulse')) {
                    window.loadJobDetail();
                }
            }
        });
    } else {
        // Already loaded, check immediately
        if (window.location.pathname.match(/\/jobs\/\d+$/) && typeof window.loadJobDetail === 'function') {
            const element = document.getElementById('job-detail');
            if (element && element.innerHTML.includes('animate-pulse')) {
                window.loadJobDetail();
            }
        }
    }
</script>
@endsection
