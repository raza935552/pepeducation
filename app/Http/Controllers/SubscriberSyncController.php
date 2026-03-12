<?php

namespace App\Http\Controllers;

use App\Services\SubscriberService;
use Illuminate\Http\Request;

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
        if (!empty($sessionIds)) {
            \App\Models\QuizResponse::whereIn('session_id', $sessionIds)
                ->where('status', 'in_progress')
                ->whereNull('subscriber_id')
                ->update([
                    'subscriber_id' => $subscriber->id,
                    'email' => $subscriber->email,
                ]);
        }

        return response()->json(['ok' => true]);
    }
}
