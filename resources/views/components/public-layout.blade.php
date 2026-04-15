@props(['title' => null, 'description' => null, 'image' => null, 'canonical' => null, 'hideChrome' => false])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ? "$title - " : '' }}{{ config('app.name') }}</title>
    <meta name="description" content="{{ $description ?? 'Professor Peptides is a free educational resource for peptide research, protocols, dosing, benefits, and safety information.' }}">

    {{-- Canonical URL --}}
    <link rel="canonical" href="{{ $canonical ?? url()->current() }}">

    {{-- Open Graph --}}
    <meta property="og:type" content="website">
    <meta property="og:title" content="{{ $title ?? config('app.name') }}">
    <meta property="og:site_name" content="{{ config('app.name') }}">
    <meta property="og:description" content="{{ $description ?? 'Professor Peptides is a free educational resource for peptide research, protocols, dosing, benefits, and safety information.' }}">
    <meta property="og:image" content="{{ $image ?? asset('images/og-default.jpg') }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:url" content="{{ $canonical ?? url()->current() }}">

    {{-- Twitter Card --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $title ?? config('app.name') }}">
    <meta name="twitter:description" content="{{ $description ?? 'Professor Peptides is a free educational resource for peptide research, protocols, dosing, benefits, and safety information.' }}">
    <meta name="twitter:image" content="{{ $image ?? asset('images/og-default.jpg') }}">

    <link rel="preconnect" href="https://fonts.bunny.net" crossorigin>
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700" rel="stylesheet" />

    @if(\App\Models\Setting::getValue('tracking', 'cookie_consent_enabled', false))
        <script>window.__ppConsentRequired = true;</script>
    @endif

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <x-theme-variables />

    {{-- Google Analytics 4 --}}
    @php $ga4Id = \App\Models\Setting::getValue('tracking', 'ga4_measurement_id'); @endphp
    @if($ga4Id)
    <script async src="https://www.googletagmanager.com/gtag/js?id={{ $ga4Id }}"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', '{{ $ga4Id }}');
    </script>
    @endif

    @livewireStyles
    @stack('head')
</head>
<body class="min-h-screen bg-surface-50 text-gray-900">
    <a href="#main-content" class="sr-only focus:not-sr-only focus:absolute focus:top-4 focus:left-4 focus:z-50 focus:px-4 focus:py-2 focus:bg-primary focus:text-white focus:rounded-lg focus:outline-none">
        Skip to main content
    </a>

    @unless($hideChrome)
        @include('layouts.partials.public-header')
    @endunless

    <main id="main-content" role="main">
        {{ $slot }}
    </main>

    @unless($hideChrome)
        @include('layouts.partials.public-footer')
    @endunless

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

    {{-- Customer.io tracking --}}
    @include('components.customerio-tracking')

    @if(\App\Models\Setting::getValue('tracking', 'cookie_consent_enabled', false))
        @include('partials.cookie-consent')
    @endif
</body>
</html>
