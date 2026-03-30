@php
    $settings = \App\Models\CustomerIoSetting::current();
    $siteId = $settings?->getSiteId();
    $isEnabled = $settings?->is_enabled ?? false;
    $enablePageTracking = $settings?->enable_page_tracking ?? false;
@endphp

@if($isEnabled && $siteId)
{{-- Customer.io JS SDK --}}
<script>
    var _cio = _cio || [];
    (function() {
        var a,b,c;a=function(f){return function(){_cio.push([f].concat(Array.prototype.slice.call(arguments,0)))}};
        b=["load","identify","sidentify","track","page","on","off"];
        for(c=0;c<b.length;c++){_cio[b[c]]=a(b[c])};
        var t = document.createElement('script'),
            s = document.getElementsByTagName('script')[0];
        t.async = true;
        t.id    = 'cio-tracker';
        t.setAttribute('data-site-id', '{{ $siteId }}');
        t.setAttribute('data-use-array-params', 'true');
        t.setAttribute('data-auto-track-page', '{{ $enablePageTracking ? "true" : "false" }}');
        t.src = 'https://assets.customer.io/assets/track.js';
        s.parentNode.insertBefore(t, s);
    })();
</script>

<script>
(function() {
    'use strict';

    function getCookie(name) {
        var v = '; ' + document.cookie;
        var p = v.split('; ' + name + '=');
        if (p.length === 2) {
            var raw = p.pop().split(';').shift();
            try { return decodeURIComponent(raw); } catch(e) { return raw; }
        }
        return null;
    }

    function setCookie(name, value, days) {
        var expires = new Date(Date.now() + (days || 365) * 864e5).toUTCString();
        document.cookie = name + '=' + encodeURIComponent(value) + '; expires=' + expires + '; path=/; SameSite=Lax';
    }

    function getEmail() {
        return getCookie('pp_email') || getCookie('cio_email') || '';
    }

    function syncEmailToServer(email) {
        var csrfMeta = document.querySelector('meta[name="csrf-token"]');
        if (csrfMeta) {
            fetch('{{ route("subscriber.sync") }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfMeta.content, 'Accept': 'application/json' },
                body: JSON.stringify({ email: email, source: 'customerio_popup' }),
            }).catch(function() {});
        }
    }

    function identifyUser(email) {
        if (!email || email.indexOf('@') === -1) return;
        setCookie('pp_email', email);
        setCookie('cio_email', email);
        syncEmailToServer(email);
        _cio.identify({ id: email, email: email });
    }

    document.addEventListener('DOMContentLoaded', function() {
        var email = getEmail() || '{{ session("customer_email", "") }}' || '{{ auth()->user()?->email ?? "" }}';
        if (email) identifyUser(email);

        document.addEventListener('change', function(e) {
            if (e.target.type === 'email' && e.target.value) identifyUser(e.target.value.trim());
        });

        document.addEventListener('submit', function(e) {
            var inp = e.target.querySelector('input[type="email"]');
            if (inp && inp.value) identifyUser(inp.value.trim());
        });
    });

    window.PepMarketing = {
        identify: function(email, props) {
            identifyUser(email);
            if (props) _cio.identify(Object.assign({ id: email, email: email }, props));
        },
        track: function(eventName, props) {
            _cio.track(eventName, props || {});
        }
    };
})();
</script>
@endif
