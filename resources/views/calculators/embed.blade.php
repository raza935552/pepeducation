<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex,follow">
    <title>{{ $config['name'] }} — Professor Peptides</title>
    <link rel="canonical" href="{{ route('calculators.show', $config['slug']) }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <x-theme-variables />
    <style>body { margin: 0; padding: 1rem; background: #fff; }</style>
</head>
<body class="bg-white text-gray-900">
    <div class="max-w-2xl mx-auto">
        @includeIf('calculators.widgets.'.$config['slug'])

        <p class="text-center text-xs text-gray-400 mt-3">
            <a href="{{ route('calculators.show', $config['slug']) }}?utm_source=embed&utm_medium=iframe&utm_campaign={{ $config['slug'] }}"
               target="_blank" rel="noopener" class="hover:text-gray-600 font-medium">
                {{ $config['name'] }} by Professor Peptides ↗
            </a>
        </p>
    </div>

    @livewireScripts

    {{-- Auto-resize: tell the parent page our real height so the iframe fits with no scrollbars. --}}
    <script>
        (function () {
            var slug = @json($config['slug']);
            function postHeight() {
                try { parent.postMessage({ ppCalc: slug, height: document.body.scrollHeight }, '*'); } catch (e) {}
            }
            window.addEventListener('load', postHeight);
            if (window.ResizeObserver) { new ResizeObserver(postHeight).observe(document.body); }
            setInterval(postHeight, 1000);
        })();
    </script>
</body>
</html>
