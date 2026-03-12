<?php

namespace App\Http\Controllers;

use App\Services\SubscriberService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SubscriberSyncController extends Controller
{
    /**
     * Sync an email from Klaviyo popup into our Subscriber system.
     * Also links the subscriber to any active quiz response in the current session.
     */
    public function sync(Request $request, SubscriberService $service)
    {
        $request->validate([
            'email' => 'required|email:rfc',
            'source' => 'nullable|string|max:100',
        ]);

        $subscriber = $service->subscribe($request->email, [
            'source' => $request->input('source', 'klaviyo_popup'),
            'segment' => $request->cookie('pp_segment') ?? 'tof',
        ]);

        $service->setEmailCookie($request->email);

        // Link subscriber to any active quiz response in this session
        $sessionIds = array_filter([
            $request->cookie('pp_session_id'),
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
            'session_ids_checked' => $sessionIds,
            'quiz_responses_linked' => $linked,
        ]);

        return response()->json(['ok' => true]);
    }
}
