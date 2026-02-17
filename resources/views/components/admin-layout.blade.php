@props(['title' => 'Admin'])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title }} - {{ config('app.name') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="h-full bg-gray-100" x-data="{ sidebarOpen: false }">
    <div class="min-h-full">
        @include('layouts.partials.admin-sidebar')

        <div class="lg:pl-64">
            @include('layouts.partials.admin-header')

            <main class="py-6">
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    @if(isset($header))
                        <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                            <h1 class="text-2xl font-bold text-gray-900">{{ $header }}</h1>
                            @if(isset($headerAction))
                                <div>{{ $headerAction }}</div>
                            @endif
                        </div>
                    @endif

                    @if(session('success'))
                        <div class="mb-6 rounded-lg bg-green-50 p-4 text-green-700">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="mb-6 rounded-lg bg-red-50 p-4 text-red-700">
                            {{ session('error') }}
                        </div>
                    @endif

                    {{ $slot }}
                </div>
            </main>
        </div>
    </div>

    @livewireScripts
    @stack('scripts')
</body>
</html>
