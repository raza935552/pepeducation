<?php

namespace App\Http\Controllers;

use App\Models\OutboundLink;
use App\Services\Tracking\TrackingManager;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

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

        $finalUrl = $link->buildFinalUrl($trackingData, $destinationOverride);

        // Use TrackingManager for proper recording + Klaviyo sync
        $tracking->recordOutboundClick($link, $finalUrl, $trackingData);

        return redirect()->away($finalUrl);
    }
}
