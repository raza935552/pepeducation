@props(['title' => null, 'description' => null, 'image' => null, 'canonical' => null, 'hideChrome' => false, 'noPixel' => false])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
@include('partials.gtm-head')
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ? "$title - " : '' }}{{ config('app.name') }}</title>
    <meta name="description" content="{{ $description ?? 'Professor Peptides is a free educational resource for peptide research, protocols, dosing, benefits, and safety information.' }}">

    {{-- Favicons --}}
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="48x48" href="/favicon-48x48.png">
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="manifest" href="/site.webmanifest">
    <meta name="theme-color" content="#2563EB">

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

    {{-- Performance hints --}}
    <link rel="preconnect" href="https://fonts.bunny.net" crossorigin>
    <link rel="dns-prefetch" href="https://www.googletagmanager.com">
    <link rel="dns-prefetch" href="https://cdn.customer.io">
    <link rel="dns-prefetch" href="https://api.indexnow.org">
    <link rel="preload" as="style" href="https://fonts.bunny.net/css?family=inter:400,500,600,700">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    @if(\App\Models\Setting::getValue('tracking', 'cookie_consent_enabled', false))
        <script>window.__ppConsentRequired = true;</script>
    @endif

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <x-theme-variables />

    {{-- Google tag: GA4 analytics + Google Ads (cross-domain linker for gclid).
         Google Ads is dormant until tracking.google_ads_id is set in admin. --}}
    @php
        $ga4Id = \App\Models\Setting::getValue('tracking', 'ga4_measurement_id');
        $googleAdsId = trim((string) \App\Models\Setting::getValue('tracking', 'google_ads_id'));
        $gtagLoadId = $ga4Id ?: ($googleAdsId ?: null);
    @endphp
    @if($gtagLoadId)
    <script async src="https://www.googletagmanager.com/gtag/js?id={{ $gtagLoadId }}"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        @if($ga4Id)
        gtag('config', '{{ $ga4Id }}');
        @endif
        @if($googleAdsId)
        // Google Ads — linker on so gclid auto-decorates links to the store (cross-domain).
        gtag('config', '{{ $googleAdsId }}', { 'linker': { 'domains': ['biolinxlabs.com'] } });
        @endif
    </script>
    @endif

    {{-- Forward Google Ads click ids to any direct Biolinx link on the lander (the
         /go redirect and gtag linker also cover this; this catches plain links). --}}
    <script>
    (function(){
        var q=new URLSearchParams(location.search);
        var ids={gclid:q.get('gclid'),gbraid:q.get('gbraid'),wbraid:q.get('wbraid')};
        if(!ids.gclid&&!ids.gbraid&&!ids.wbraid) return;
        function decorate(){
            document.querySelectorAll('a[href*="biolinxlabs.com"]').forEach(function(a){
                try{var u=new URL(a.href, location.origin);
                    Object.keys(ids).forEach(function(k){ if(ids[k]&&!u.searchParams.get(k)) u.searchParams.set(k, ids[k]); });
                    a.href=u.toString();
                }catch(e){}
            });
        }
        if(document.readyState!=='loading') decorate();
        else document.addEventListener('DOMContentLoaded', decorate);
    })();
    </script>

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

    {{-- Meta Pixel (shared with Biolinx) — seasons the pixel + sets _fbp/_fbc.
         Suppressed on pure-info pages (blog, legal) via :no-pixel="true" to keep
         the education brand's Meta footprint to conversion-intent pages only. --}}
    @unless($noPixel)
        <x-meta-pixel />
    @endunless

    @livewireStyles

    {{-- Brand entity schema (WebSite + Organization) on every page — strengthens
         the canonical "Professor Peptides" signal sitewide (vs the .org). --}}
    @include('partials.schema-website')

    @stack('head')

    {{-- Buy CTA click tracking helper (fire-and-forget) --}}
    <script>
        window.ppTrackBuyClick = function(linkEl, marketingPayload, serverPayload) {
            try {
                if (window.PepMarketing && marketingPayload) {
                    PepMarketing.track('Clicked Buy CTA', marketingPayload);
                }
                if (navigator.sendBeacon) {
                    var fd = new FormData();
                    Object.keys(serverPayload || {}).forEach(function(k){ fd.append(k, serverPayload[k]); });
                    fd.append('source_url', window.location.href);
                    navigator.sendBeacon('/track/buy-click', fd);
                } else {
                    fetch('/track/buy-click', {
                        method: 'POST',
                        keepalive: true,
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify(Object.assign({}, serverPayload || {}, { source_url: window.location.href })),
                    }).catch(function(){});
                }
            } catch (e) {}
        };
    </script>
</head>
<body class="min-h-screen bg-surface-50 text-gray-900">
@include('partials.gtm-body')
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

    <x-chat-widget />
</body>
</html>
