@php
    $cdpWriteKey = config('services.customerio.cdp_write_key');
@endphp

@if($cdpWriteKey)
{{-- Customer.io CDP (cioanalytics) — loaded from Connections > JavaScript source --}}
<script>
  !function(){var i="cioanalytics", analytics=(window[i]=window[i]||[]);if(!analytics.initialize)if(analytics.invoked)window.console&&console.error&&console.error("Snippet included twice.");else{analytics.invoked=!0;analytics.methods=["trackSubmit","trackClick","trackLink","trackForm","pageview","identify","reset","group","track","ready","alias","debug","page","once","off","on","addSourceMiddleware","addIntegrationMiddleware","setAnonymousId","addDestinationMiddleware"];analytics.factory=function(e){return function(){var t=Array.prototype.slice.call(arguments);t.unshift(e);analytics.push(t);return analytics}};for(var e=0;e<analytics.methods.length;e++){var key=analytics.methods[e];analytics[key]=analytics.factory(key)}analytics.load=function(key,e){var t=document.createElement("script");t.type="text/javascript";t.async=!0;t.setAttribute('data-global-customerio-analytics-key', i);t.src="https://cdp.customer.io/v1/analytics-js/snippet/" + key + "/analytics.min.js";var n=document.getElementsByTagName("script")[0];n.parentNode.insertBefore(t,n);analytics._writeKey=key;analytics._loadOptions=e};analytics.SNIPPET_VERSION="4.15.3";
    analytics.load("{{ $cdpWriteKey }}");
    analytics.page();
  }}();
</script>

{{-- PepTracking helper — auto-identify, cookie persistence, public API --}}
<script>
(function() {
    var cio = window.cioanalytics;

    function getCookie(name) {
        var v = '; ' + document.cookie;
        var p = v.split('; ' + name + '=');
        if (p.length === 2) {
            try { return decodeURIComponent(p.pop().split(';').shift()); } catch(e) { return null; }
        }
        return null;
    }

    function setCookie(name, value) {
        document.cookie = name + '=' + encodeURIComponent(value) + '; expires=' + new Date(Date.now() + 365*864e5).toUTCString() + '; path=/; SameSite=Lax';
    }

    function identifyEmail(email) {
        if (!email || email.indexOf('@') === -1) return;
        setCookie('cio_email', email);
        cio.identify(email, { email: email });
    }

    document.addEventListener('DOMContentLoaded', function() {
        var email = getCookie('cio_email') || getCookie('pp_email') || '{{ auth()->user()?->email ?? "" }}';
        if (email) identifyEmail(email);

        document.addEventListener('change', function(e) {
            if (e.target.type === 'email' && e.target.value) identifyEmail(e.target.value.trim());
        });

        document.addEventListener('submit', function(e) {
            var inp = e.target.querySelector && e.target.querySelector('input[type="email"]');
            if (inp && inp.value) identifyEmail(inp.value.trim());
        });
    });

    window.PepTracking = {
        identify: function(email, traits) {
            if (!email) return;
            setCookie('cio_email', email);
            cio.identify(email, Object.assign({ email: email }, traits || {}));
        },
        track: function(eventName, properties) {
            cio.track(eventName, properties || {});
        },
        page: function(name, properties) {
            cio.page(name, properties || {});
        },
        reset: function() {
            cio.reset();
            document.cookie = 'cio_email=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;';
        }
    };
})();
</script>
@endif
