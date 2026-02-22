<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Job Seeker Dashboard - JobHub' }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="font-sans antialiased bg-gray-50" data-user-type="job_seeker">
    <div class="min-h-screen bg-gray-50">
        @include('partials.job-seeker-sidebar')
        
        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col ml-64">
            @yield('content')
        </div>
    </div>

    @livewireScripts
    @stack('scripts')
</body>
</html>
