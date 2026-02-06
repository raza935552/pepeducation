<?php

namespace App\Http\Controllers;

use App\Models\LeadMagnet;
use App\Models\LeadMagnetDownload;
use App\Models\Subscriber;
use App\Services\Tracking\TrackingManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LeadMagnetController extends Controller
{
    /**
     * Show lead magnet landing page
     */
    public function landing(string $slug)
    {
        $leadMagnet = LeadMagnet::where('slug', $slug)
            ->active()
            ->firstOrFail();

        $leadMagnet->increment('views_count');

        return view('lead-magnets.landing', compact('leadMagnet'));
    }

    /**
     * Handle lead magnet download with tracking
     */
    public function download(Request $request, string $slug)
    {
        // Rate limit: max 10 downloads per hour per IP+session
        $sessionId = $request->cookie('pp_session_id') ?? session()->getId();
        $key = 'lead_download_' . request()->ip() . '_' . substr(md5($sessionId), 0, 8);
        if (cache()->get($key, 0) >= 10) {
            abort(429, 'Too many download requests. Please try again later.');
        }
        cache()->put($key, cache()->get($key, 0) + 1, 3600);

        $leadMagnet = LeadMagnet::where('slug', $slug)
            ->active()
            ->firstOrFail();

        $tracking = new TrackingManager($request);
        $session = $tracking->getSession();

        // Get subscriber from session or email parameter
        $subscriber = $session->subscriber;
        if (!$subscriber && $request->has('email')) {
            $subscriber = Subscriber::where('email', $request->email)->first();
        }

        // Create download record
        $download = LeadMagnetDownload::create([
            'lead_magnet_id' => $leadMagnet->id,
            'session_id' => $session->session_id,
            'subscriber_id' => $subscriber?->id,
            'user_id' => auth()->id(),
            'source_page' => $request->header('referer'),
            'source_popup' => $request->get('popup'),
            'utm_source' => $session->utm_source,
            'utm_campaign' => $session->utm_campaign,
            'delivery_method' => $leadMagnet->delivery_method,
            'downloaded' => true,
            'downloaded_at' => now(),
            'created_at' => now(),
        ]);

        // Increment lead magnet download count
        $leadMagnet->increment('downloads_count');

        // Track the download event
        $tracking->trackLeadMagnetDownload($download);

        // Update subscriber engagement if linked
        if ($subscriber) {
            $downloadedMagnets = $subscriber->lead_magnets_downloaded ?? [];
            if (!in_array($leadMagnet->slug, $downloadedMagnets)) {
                $downloadedMagnets[] = $leadMagnet->slug;
                $subscriber->update([
                    'lead_magnets_downloaded' => $downloadedMagnets,
                    'engagement_score' => $subscriber->engagement_score + 10,
                ]);
            }
        }

        // Handle delivery method
        if ($leadMagnet->delivery_method === LeadMagnet::DELIVERY_INSTANT) {
            return $this->serveFile($leadMagnet);
        }

        // Email delivery - show confirmation
        return redirect()->route('lead-magnet.landing', $slug)
            ->with('success', 'Check your email! Your download is on its way.');
    }

    /**
     * Serve the file for instant download
     */
    protected function serveFile(LeadMagnet $leadMagnet)
    {
        if (!$leadMagnet->file_path || !Storage::disk('public')->exists($leadMagnet->file_path)) {
            abort(404, 'File not found');
        }

        $extension = $leadMagnet->file_type
            ?? pathinfo($leadMagnet->file_path, PATHINFO_EXTENSION)
            ?: 'pdf';

        return Storage::disk('public')->download(
            $leadMagnet->file_path,
            $leadMagnet->file_name ?? $leadMagnet->slug . '.' . $extension
        );
    }
}
