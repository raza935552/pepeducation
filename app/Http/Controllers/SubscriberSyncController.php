<?php

namespace App\Http\Controllers;

use App\Services\SubscriberService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SubscriberSyncController extends Controller
{
    /**
     * Sync an email from popup into our Subscriber system.
     * Also links the subscriber to any active quiz response in the current session.
     */
    public function sync(Request $request, SubscriberService $service)
    {
        $request->validate([
            'email' => 'required|email:rfc',
            'phone' => 'nullable|string|max:32',
            'source' => 'nullable|string|max:100',
            // Browser Lead's event_id — forwarded to Biolinx so its CAPI Lead dedups.
            'lead_event_id' => 'nullable|string|max:120',
        ]);

        // Advertorial / lander lead pages (source "lp-*") capture the lead no matter what —
        // we want every lead from paid campaigns. The disposable filter still protects the
        // popups/giveaway lists from throwaway addresses.
        $syncSource = (string) $request->input('source', '');
        $alwaysCapture = str_starts_with($syncSource, 'lp-');
        if (! $alwaysCapture && \App\Support\DisposableEmail::isDisposable($request->email)) {
            return response()->json(['success' => false, 'message' => 'Please use a permanent email address.'], 422);
        }

        $subscriber = $service->subscribe($request->email, [
            'source' => $request->input('source', 'popup'),
            'phone' => $request->input('phone'),
            'segment' => $request->cookie('pp_segment') ?? 'tof',
            'lead_event_id' => $request->input('lead_event_id'),
        ]);

        $service->setEmailCookie($request->email);

        // Link subscriber to the current session (enables cross-domain email passing)
        $sessionId = $request->cookie('pp_session_id');
        if ($sessionId) {
            \App\Models\UserSession::where('session_id', $sessionId)
                ->whereNull('subscriber_id')
                ->update(['subscriber_id' => $subscriber->id]);
        }

        // Link subscriber to any active quiz response in this session
        $sessionIds = array_filter([
            $sessionId,
            session()->getId(),
        ]);

        $linked = 0;
        if (!empty($sessionIds)) {
            $linked = \App\Models\QuizResponse::whereIn('session_id', $sessionIds)
                ->where('status', 'in_progress')
                ->whereNull('subscriber_id')
                ->update([
                    'subscriber_id' => $subscriber->id,
                    'email' => $subscriber->email,
                ]);
        }

        Log::info('Subscriber sync completed', [
            'subscriber_id' => $subscriber->id,
            'email' => $subscriber->email,
            'session_id' => $sessionId,
            'quiz_responses_linked' => $linked,
        ]);

        // Lander guide auto-delivery: if the lead came from a lander mapped to a
        // guide, email them that guide PDF (the PT-141 popup promises "the guide,
        // sent to your inbox"). Sent AFTER the response so capture stays instant,
        // and a mail failure can never break the capture.
        $guideMap = [
            'lp-pt141' => ['pt-141', 'PT-141'],
            'lp-glow' => ['glow', 'GLOW'],
            'lp-reta' => ['retatrutide', 'Retatrutide'],
        ];
        $source = (string) $request->input('source', '');
        $email = $subscriber->email;
        foreach ($guideMap as $prefix => [$guideKey, $guideName]) {
            if (str_starts_with($source, $prefix)) {
                dispatch(function () use ($email, $guideKey, $guideName) {
                    try {
                        \Illuminate\Support\Facades\Mail::to($email)
                            ->send(new \App\Mail\LanderGuideMail($guideKey, $guideName));
                        Log::info('Lander guide emailed', ['email' => $email, 'guide' => $guideKey]);
                    } catch (\Throwable $e) {
                        Log::warning('Lander guide email failed for ' . $email . ': ' . $e->getMessage());
                    }
                })->afterResponse();
                break;
            }
        }

        return response()->json(['ok' => true]);
    }
}
