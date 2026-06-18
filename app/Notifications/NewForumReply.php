<?php

namespace App\Notifications;

use App\Models\ForumPost;
use App\Services\ForumContentSanitizer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewForumReply extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public ForumPost $post)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $thread = $this->post->thread;
        $excerpt = app(ForumContentSanitizer::class)->excerpt($this->post->body, 200);
        $url = route('community.threads.show', $thread) . '#reply-' . $this->post->id;

        return (new MailMessage)
            ->subject('New reply: ' . $thread->title)
            ->greeting('Hi ' . $notifiable->name . ',')
            ->line($this->post->user->name . ' replied to a discussion you\'re following:')
            ->line('"' . $thread->title . '"')
            ->line('— ' . $excerpt)
            ->action('View reply', $url)
            ->line('You\'re receiving this because you follow this discussion. You can unfollow it from the thread at any time.');
    }
}
