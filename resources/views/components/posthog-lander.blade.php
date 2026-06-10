@php
    // PostHog for the paid-ad LANDERS ONLY (not the rest of Professor Peptides).
    // Configured in Admin → Settings → "PostHog (Landers)". Presence of a key +
    // the enabled toggle turns it on. Included only in the lander templates, so
    // every current AND future lander gets it automatically — and nowhere else.
    $phEnabled = \App\Models\Setting::getValue('integrations', 'posthog_enabled', false);
    $phKey     = \App\Models\Setting::getValue('integrations', 'posthog_key');
    $phHost    = \App\Models\Setting::getValue('integrations', 'posthog_host') ?: 'https://us.i.posthog.com';
@endphp

@if($phEnabled && !empty($phKey))
{{-- PostHog (landers) --}}
<script>
!function(t,e){var o,n,p,r;e.__SV||(window.posthog=e,e._i=[],e.init=function(i,s,a){function g(t,e){var o=e.split(".");2==o.length&&(t=t[o[0]],e=o[1]);t[e]=function(){t.push([e].concat(Array.prototype.slice.call(arguments,0)))}}(p=t.createElement("script")).type="text/javascript",p.crossOrigin="anonymous",p.async=!0,p.src=s.api_host.replace(".i.posthog.com","-assets.i.posthog.com")+"/static/array.js",(r=t.getElementsByTagName("script")[0]).parentNode.insertBefore(p,r);var u=e;for(void 0!==a?u=e[a]=[]:a="posthog",u.people=u.people||[],u.toString=function(t){var e="posthog";return"posthog"!==a&&(e+="."+a),t||(e+=" (stub)"),e},u.people.toString=function(){return u.toString(1)+".people (stub)"},o="init Ee Ts Cs js capture Ne calculateEventProperties Ks register register_once register_for_session unregister unregister_for_session js getFeatureFlag getFeatureFlagPayload isFeatureEnabled reloadFeatureFlags updateEarlyAccessFeatureEnrollment getEarlyAccessFeatures on onFeatureFlags onSurveysLoaded onSessionId getSurveys getActiveMatchingSurveys renderSurvey canRenderSurvey canRenderSurveyAsync identify setPersonProperties group resetGroups setPersonPropertiesForFlags resetPersonPropertiesForFlags setGroupPropertiesForFlags resetGroupPropertiesForFlags reset get_distinct_id getGroups get_session_id get_session_replay_url alias set_config startSessionRecording stopSessionRecording sessionRecordingStarted captureException loadToolbar get_property getSessionProperty Ds Fs createPersonProfile Is opt_in_capturing opt_out_capturing has_opted_in_capturing has_opted_out_capturing clear_opt_in_out_capturing Ss debug Ms getPageViewId captureTraceFeedback captureTraceMetric".split(" "),n=0;n<o.length;n++)g(u,o[n]);e._i.push([i,s,a])},e.__SV=1)}(document,window.posthog||[]);
posthog.init('{{ $phKey }}', {
    api_host: '{{ $phHost }}',
    person_profiles: 'identified_only',
    autocapture: true,
    capture_pageview: true,
    capture_pageleave: true,
    disable_session_recording: false,
    enable_heatmaps: true,
});
// Tag every lander event with which lander + the forwarded ad params, so PostHog
// can break performance down by lander/campaign just like the admin dashboards.
try {
    posthog.register({
        pp_lander: @json(isset($lander) ? $lander->slug : (request()->segment(2) ?? null)),
        utm_source: @json(request()->query('utm_source')),
        utm_campaign: @json(request()->query('utm_campaign')),
        utm_content: @json(request()->query('utm_content')),
    });
} catch (e) {}
</script>
@endif
