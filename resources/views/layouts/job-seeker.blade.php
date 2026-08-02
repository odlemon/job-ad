<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Job Seeker Dashboard - Scoop' }}</title>
    <link rel="icon" type="image/png" href="{{ asset('scoop.png') }}">

    @include('partials.theme-head')

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="font-sans antialiased bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 transition-colors duration-200" data-user-type="job_seeker" data-user-id="{{ auth()->id() }}">
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900 flex">
        @include('partials.job-seeker-sidebar')

        <div class="flex-1 flex flex-col min-w-0 js-seeker-main">
            <style>
                .js-seeker-main { margin-left:0; }
                @media (min-width:1024px){ .js-seeker-main { margin-left:16rem; } }
            </style>
            @yield('content')
        </div>
    </div>

    @livewireScripts
    @stack('scripts')
</body>
</html>
