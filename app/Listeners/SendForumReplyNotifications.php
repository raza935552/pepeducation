<?php

namespace App\Listeners;

use App\Events\ForumReplyPosted;
use App\Models\User;
use App\Notifications\NewForumReply;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;

class SendForumReplyNotifications implements ShouldQueue
{
    public function handle(ForumReplyPosted $event): void
    {
        $post = $event->post;
        $thread = $post->thread;

        if (! $thread) {
            return;
        }

        // Notify everyone subscribed to the thread, except:
        //  - the author of this reply
        //  - seed personas (no real inbox)
        //  - suspended users
        //  - users without a verified email
        $recipients = User::query()
            ->whereIn('id', $thread->subscriptions()->pluck('user_id'))
            ->where('id', '!=', $post->user_id)
            ->where('is_seed', false)
            ->where('is_suspended', false)
            ->whereNotNull('email_verified_at')
            ->get();

        if ($recipients->isEmpty()) {
            return;
        }

        Notification::send($recipients, new NewForumReply($post));
    }
}
