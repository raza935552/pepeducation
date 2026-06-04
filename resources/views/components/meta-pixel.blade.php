@php
    // Shared Meta pixel with Biolinx (same dataset) so the education → store funnel
    // is one audience. Seasons on PP traffic, sets _fbp/_fbc, and forwards the
    // ad-click identity to every Biolinx link.
    // Managed from Admin → Settings → Tracking Pixels (tracking.meta_pixel_id).
    // Presence of an ID = enabled; clear the field in admin to turn it off.
    $metaPixelId = \App\Models\Setting::getValue('tracking', 'meta_pixel_id', null);
@endphp

@if(!empty($metaPixelId))
<!-- Meta Pixel (shared with Biolinx) -->
<script>
    !function(f,b,e,v,n,t,s)
    {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
    n.callMethod.apply(n,arguments):n.queue.push(arguments)};
    if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
    n.queue=[];t=b.createElement(e);t.async=!0;
    t.src=v;s=b.getElementsByTagName(e)[0];
    s.parentNode.insertBefore(t,s)}(window,document,'script',
    'https://connect.facebook.net/en_US/fbevents.js');

    fbq('init', '{{ $metaPixelId }}');
    fbq('track', 'PageView');

    // Cross-domain forward: append the ad-click identity (fbclid from URL + the
    // _fbp/_fbc cookies this pixel just set) to every link pointing at Biolinx, so
    // the Purchase CAPI on Biolinx can match back to the original ad click. The
    // server-side /go redirect also forwards these (+ email); this covers direct
    // biolinxlabs.com links on the landers.
    (function () {
        function cookie(n){var m=document.cookie.match('(^|;)\\s*'+n+'\\s*=\\s*([^;]+)');return m?m.pop():'';}
        var fbclid=(new URLSearchParams(location.search)).get('fbclid')||'';
        function decorate(){
            var fbp=cookie('_fbp'), fbc=cookie('_fbc');
            document.querySelectorAll('a[href*="biolinxlabs.com"]').forEach(function(a){
                try{
                    var u=new URL(a.href, location.origin);
                    if(fbclid && !u.searchParams.get('fbclid')) u.searchParams.set('fbclid', fbclid);
                    if(fbp && !u.searchParams.get('fbp')) u.searchParams.set('fbp', fbp);
                    if(fbc && !u.searchParams.get('fbc')) u.searchParams.set('fbc', fbc);
                    a.href=u.toString();
                }catch(e){}
            });
        }
        if(document.readyState!=='loading') decorate();
        else document.addEventListener('DOMContentLoaded', decorate);
    })();
</script>
<noscript><img height="1" width="1" style="display:none" alt=""
    src="https://www.facebook.com/tr?id={{ $metaPixelId }}&ev=PageView&noscript=1"/></noscript>
<!-- End Meta Pixel -->
@endif
