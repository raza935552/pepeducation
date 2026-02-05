<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $quiz->title }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <style>
        body { margin: 0; padding: 1rem; background: transparent; }
    </style>
</head>
<body>
    <livewire:quiz-player :quiz="$quiz" />
    @livewireScripts
</body>
</html>
