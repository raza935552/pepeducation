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
        $link = OutboundLink::where('slug', $slug)->where('is_active', true)->firstOrFail();

        $tracking = new TrackingManager($request);
        $trackingData = $tracking->getCrossDomainData();

        // Add subscriber email if available
        $session = $tracking->getSession();
        if ($session->subscriber) {
            $trackingData['email'] = $session->subscriber->email;
        }

        // Build final URL with all tracking params
        $finalUrl = $link->buildFinalUrl($trackingData);

        // Record the click (also syncs to Klaviyo internally)
        $tracking->recordOutboundClick($link, $finalUrl, $trackingData);

        return redirect()->away($finalUrl);
    }
}
