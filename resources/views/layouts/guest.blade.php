<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'PepProfesor') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 dark:text-cream-100 antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-cream-100 dark:bg-brown-900">
            <div>
                <a href="/" class="flex items-center gap-1">
                    <span class="text-3xl font-bold text-gold-500 dark:text-gold-400">Pep</span>
                    <span class="text-3xl font-bold text-gray-900 dark:text-cream-100">Profesor</span>
                </a>
            </div>

            <div class="w-full sm:max-w-md mt-6 px-6 py-8 bg-white dark:bg-brown-800 shadow-xl overflow-hidden rounded-2xl border border-cream-200 dark:border-brown-700">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
