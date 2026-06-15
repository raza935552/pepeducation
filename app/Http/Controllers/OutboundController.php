<?php

namespace App\Http\Controllers;

use App\Models\OutboundLink;
use App\Services\Tracking\TrackingManager;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;

class OutboundController extends Controller
{
    public function track(Request $request, string $slug): RedirectResponse
    {
        $link = OutboundLink::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        $tracking = new TrackingManager($request);
        $trackingData = $tracking->getCrossDomainData();

        // Add subscriber email if available (session link or cookie fallback)
        $session = $tracking->getSession();
        if ($session->subscriber) {
            $trackingData['email'] = $session->subscriber->email;
        } elseif ($email = $request->cookie('pp_email')) {
            $trackingData['email'] = $email;
        }

        // Allow per-product destination override (must match outbound link domain)
        $destinationOverride = null;
        if ($dest = $request->query('dest')) {
            $linkDomain = parse_url($link->destination_url, PHP_URL_HOST);
            $destDomain = parse_url($dest, PHP_URL_HOST);
            if ($linkDomain && $destDomain && $destDomain === $linkDomain) {
                $destinationOverride = $dest;
            }
        }

        // If the click did NOT come through a tagged ad lander (no pp_lander in
        // session), attribute it to the PP content page it was clicked from — the
        // tier-list, a peptide guide, the home page, etc. Otherwise Biolinx records
        // these store-bridge clicks as "(none)". Marked "page:" so the team can tell
        // content-page CTAs apart from tracked ad landers.
        if (empty($trackingData['pp_lander']) && ($ref = $this->landerFromReferer($request))) {
            $trackingData['pp_lander'] = $ref;
        }

        $finalUrl = $link->buildFinalUrl($trackingData, $destinationOverride);

        // Use TrackingManager for proper recording + Customer.io sync
        $tracking->recordOutboundClick($link, $finalUrl, $trackingData);

        return redirect()->away($finalUrl);
    }

    /**
     * Derive a "which PP page bridged this click" label from the same-site referer,
     * for /go clicks that didn't originate on a tagged ad lander. Returns e.g.
     * "page:peptide-tier-list", "page:bpc-157", "page:home" — or null when the
     * referer is missing or off-site (we never forward an external referer).
     */
    private function landerFromReferer(Request $request): ?string
    {
        $referer = (string) $request->header('referer');
        if ($referer === '') {
            return null;
        }

        $parts = parse_url($referer);
        $host = $parts['host'] ?? '';
        $norm = fn ($h) => preg_replace('/^www\./', '', strtolower((string) $h));
        if ($host === '' || $norm($host) !== $norm($request->getHost())) {
            return null; // off-site referer — don't attribute it as a PP page
        }

        $path = trim((string) ($parts['path'] ?? ''), '/');
        if ($path === '' || $path === 'go/'.config('biolinx.go_slug', 'biolinxlabs')) {
            return 'page:home';
        }

        $slug = (string) Str::of($path)->afterLast('/')->lower()->slug();

        return $slug === '' ? 'page:home' : 'page:'.Str::limit($slug, 60, '');
    }
}
