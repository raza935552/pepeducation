<?php

namespace App\Http\Controllers\Community;

use App\Http\Controllers\Controller;
use App\Models\ForumThread;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    public function toggle(Request $request, ForumThread $thread)
    {
        $user = $request->user();
        abort_unless($user->canParticipateInCommunity(), 403);

        $existing = $user->forumSubscriptions()->where('thread_id', $thread->id)->first();

        if ($existing) {
            $existing->delete();
            $subscribed = false;
        } else {
            $user->forumSubscriptions()->create(['thread_id' => $thread->id]);
            $subscribed = true;
        }

        if ($request->expectsJson()) {
            return response()->json(['subscribed' => $subscribed]);
        }

        return back()->with('status', $subscribed ? 'You are now following this discussion.' : 'You unfollowed this discussion.');
    }
}
