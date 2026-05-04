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

    {{-- Webmaster verification --}}
    @php
        $googleVerify = \App\Models\Setting::getValue('seo', 'google_verification', '');
        $bingVerify   = \App\Models\Setting::getValue('seo', 'bing_verification', '');
        $yandexVerify = \App\Models\Setting::getValue('seo', 'yandex_verification', '');
    @endphp
    @if($googleVerify)
        <meta name="google-site-verification" content="{{ $googleVerify }}">
    @endif
    @if($bingVerify)
        <meta name="msvalidate.01" content="{{ $bingVerify }}">
    @endif
    @if($yandexVerify)
        <meta name="yandex-verification" content="{{ $yandexVerify }}">
    @endif

    @php
        $resolvedShareImage = $image;
        if (!$resolvedShareImage) {
            $configuredOg = \App\Models\Setting::getValue('seo', 'og_image', null)
                ?: \App\Models\Setting::getValue('branding', 'logo_url', null);
            if (!empty($configuredOg)) {
                $resolvedShareImage = \Illuminate\Support\Str::startsWith($configuredOg, ['http://','https://'])
                    ? $configuredOg
                    : url($configuredOg);
            }
        }
    @endphp

    {{-- Open Graph --}}
    <meta property="og:type" content="website">
    <meta property="og:title" content="{{ $title ?? config('app.name') }}">
    <meta property="og:site_name" content="{{ config('app.name') }}">
    <meta property="og:description" content="{{ $description ?? 'Professor Peptides is a free educational resource for peptide research, protocols, dosing, benefits, and safety information.' }}">
    @if($resolvedShareImage)
        <meta property="og:image" content="{{ $resolvedShareImage }}">
        <meta property="og:image:width" content="1200">
        <meta property="og:image:height" content="630">
    @endif
    <meta property="og:url" content="{{ $canonical ?? url()->current() }}">

    {{-- Twitter Card --}}
    <meta name="twitter:card" content="{{ $resolvedShareImage ? 'summary_large_image' : 'summary' }}">
    <meta name="twitter:title" content="{{ $title ?? config('app.name') }}">
    <meta name="twitter:description" content="{{ $description ?? 'Professor Peptides is a free educational resource for peptide research, protocols, dosing, benefits, and safety information.' }}">
    @if($resolvedShareImage)
        <meta name="twitter:image" content="{{ $resolvedShareImage }}">
    @endif

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

    {{-- Yandex Metrica --}}
    @php $yandexMetricaId = \App\Models\Setting::getValue('tracking', 'yandex_metrica_id'); @endphp
    @if($yandexMetricaId)
    <script type="text/javascript">
        (function(m,e,t,r,i,k,a){m[i]=m[i]||function(){(m[i].a=m[i].a||[]).push(arguments)};
        m[i].l=1*new Date();
        for (var j = 0; j < document.scripts.length; j++) {if (document.scripts[j].src === r) { return; }}
        k=e.createElement(t),a=e.getElementsByTagName(t)[0],k.async=1,k.src=r,a.parentNode.insertBefore(k,a)})
        (window, document, "script", "https://mc.yandex.ru/metrika/tag.js", "ym");

        ym({{ (int) $yandexMetricaId }}, "init", {
            clickmap:true,
            trackLinks:true,
            accurateTrackBounce:true,
            webvisor:true
        });
    </script>
    <noscript><div><img src="https://mc.yandex.ru/watch/{{ (int) $yandexMetricaId }}" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
    @endif

    {{-- Customer.io CDP (cioanalytics) --}}
    <x-cio-cdp />

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
