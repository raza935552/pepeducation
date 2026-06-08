<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * Captures the Meta click identifiers from an ad landing (?fbclid=…&fbp=…&fbc=…)
 * or the _fbp/_fbc cookies the pixel sets, into the session — so the cross-domain
 * hand-off (OutboundController → buildFinalUrl) can forward them to Biolinx, where
 * the Conversions API matches the purchase back to the original ad click.
 */
class CaptureMetaClickIds
{
    public function handle(Request $request, Closure $next)
    {
        if ($v = $request->query('fbp')) {
            session(['meta_fbp' => substr((string) $v, 0, 255)]);
        } elseif (!session('meta_fbp') && ($c = $request->cookie('_fbp'))) {
            session(['meta_fbp' => substr((string) $c, 0, 255)]);
        }

        if ($v = $request->query('fbc')) {
            session(['meta_fbc' => substr((string) $v, 0, 255)]);
        } elseif (!session('meta_fbc') && ($c = $request->cookie('_fbc'))) {
            session(['meta_fbc' => substr((string) $c, 0, 255)]);
        }

        if ($v = $request->query('fbclid')) {
            session(['meta_fbclid' => substr((string) $v, 0, 400)]);
        }

        // Capture the ad UTMs from the landing URL into the session (same durable
        // mechanism as fbclid) so the cross-domain hand-off forwards the REAL ad
        // campaign — Ad → Lander → Biolinx. Latest ad click wins.
        foreach (['utm_source', 'utm_medium', 'utm_campaign', 'utm_content', 'utm_term'] as $k) {
            if ($v = $request->query($k)) {
                session(['ad_' . $k => substr((string) $v, 0, 200)]);
            }
        }

        return $next($request);
    }
}
