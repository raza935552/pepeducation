<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Page Builder' }} - PepProfesor Admin</title>
    @vite(['resources/css/app.css', 'resources/js/grapesjs-editor.js'])
    <link rel="stylesheet" href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap">
    @stack('styles')
</head>
<body class="bg-gray-900 text-gray-100 overflow-hidden">
    <div id="page-builder" class="h-screen flex flex-col">
        {{ $slot }}
    </div>
    @stack('scripts')
</body>
</html>
