@props(['title' => null, 'description' => null, 'image' => null, 'canonical' => null])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ? "$title - " : '' }}{{ config('app.name') }}</title>
    @if($description)
        <meta name="description" content="{{ $description }}">
    @endif

    {{-- Canonical URL --}}
    <link rel="canonical" href="{{ $canonical ?? url()->current() }}">

    {{-- Open Graph --}}
    <meta property="og:type" content="website">
    <meta property="og:title" content="{{ $title ?? config('app.name') }}">
    <meta property="og:site_name" content="{{ config('app.name') }}">
    @if($description)
        <meta property="og:description" content="{{ $description }}">
    @endif
    <meta property="og:image" content="{{ $image ?? asset('images/og-default.jpg') }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:url" content="{{ $canonical ?? url()->current() }}">

    {{-- Twitter Card --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $title ?? config('app.name') }}">
    @if($description)
        <meta name="twitter:description" content="{{ $description }}">
    @endif
    <meta name="twitter:image" content="{{ $image ?? asset('images/og-default.jpg') }}">

    <link rel="preconnect" href="https://fonts.bunny.net" crossorigin>
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700" rel="stylesheet" />

    @if(\App\Models\Setting::getValue('tracking', 'cookie_consent_enabled', false))
        <script>window.__ppConsentRequired = true;</script>
    @endif
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @livewireStyles
    @stack('head')
</head>
<body class="min-h-screen bg-cream-50 text-gray-900">
    <a href="#main-content" class="sr-only focus:not-sr-only focus:absolute focus:top-4 focus:left-4 focus:z-50 focus:px-4 focus:py-2 focus:bg-brand-gold focus:text-white focus:rounded-lg focus:outline-none">
        Skip to main content
    </a>

    @include('layouts.partials.public-header')

    <main id="main-content" role="main">
        {{ $slot }}
    </main>

    @include('layouts.partials.public-footer')

    {{-- Search Modal --}}
    @livewire('search-modal')

    {{-- Contact Modal --}}
    @livewire('contact-modal')

    {{-- Peptide Request Modal --}}
    @livewire('peptide-request-modal')

    {{-- Edit Suggestion Modal --}}
    @livewire('edit-suggestion-modal')

    {{-- Dynamic Popup Manager --}}
    @livewire('popup-manager')

    @livewireScripts
    @stack('scripts')

    {{-- Sync Klaviyo popup email submissions to our Subscriber system --}}
    <script>
        (function() {
            // Identify user from pp_email cookie (enables abandonment flows)
            var ppEmail = (function(n) {
                var v = document.cookie.match('(^|;)\\s*' + n + '\\s*=\\s*([^;]+)');
                return v ? decodeURIComponent(v.pop()) : null;
            })('pp_email');
            if (ppEmail && window.klaviyo) {
                klaviyo.identify({ $email: ppEmail });
            }

            var synced = {};
            function syncKlaviyoEmail(email) {
                if (!email || synced[email]) return;
                synced[email] = true;
                fetch('{{ route("subscriber.sync") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                    },
                    body: JSON.stringify({ email: email, source: 'klaviyo_popup' })
                }).catch(function() {});
            }

            // Listen for Klaviyo form submissions
            function initKlaviyoListener() {
                if (window.klaviyo && typeof window.klaviyo.isIdentified === 'function') {
                    window.klaviyo.isIdentified().then(function(identified) {
                        if (identified) {
                            window.klaviyo.getEmail().then(function(email) {
                                if (email) syncKlaviyoEmail(email);
                            });
                        }
                    });
                }
            }

            // Poll for Klaviyo identification changes (catches popup submissions)
            var checkInterval = setInterval(function() {
                if (!window.klaviyo || typeof window.klaviyo.isIdentified !== 'function') return;
                window.klaviyo.isIdentified().then(function(identified) {
                    if (identified) {
                        window.klaviyo.getEmail().then(function(email) {
                            if (email) {
                                syncKlaviyoEmail(email);
                                clearInterval(checkInterval);
                            }
                        });
                    }
                });
            }, 2000);

            // Stop polling after 5 minutes
            setTimeout(function() { clearInterval(checkInterval); }, 300000);

            // Also check on page load
            if (document.readyState === 'complete') {
                initKlaviyoListener();
            } else {
                window.addEventListener('load', initKlaviyoListener);
            }
        })();
    </script>

    @if(\App\Models\Setting::getValue('tracking', 'cookie_consent_enabled', false))
        @include('partials.cookie-consent')
    @endif
</body>
</html>
