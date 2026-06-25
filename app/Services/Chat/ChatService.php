<?php

namespace App\Services\Chat;

use App\Models\ChatConversation;
use App\Models\ChatMessage;

/**
 * Shared chat helpers (persist a message + serialize). No broadcasting —
 * PP chat is polling-based (the widget and console poll for new messages).
 */
class ChatService
{
    public function post(ChatConversation $conv, string $sender, string $body, ?string $authorName = null, ?int $userId = null): ChatMessage
    {
        $msg = $conv->messages()->create([
            'sender' => $sender,
            'user_id' => $userId,
            'author_name' => $authorName,
            'body' => $body,
        ]);

        $current = (int) ($conv->unread_for_admin ?? 0);
        $conv->forceFill([
            'last_message_at' => now(),
            'unread_for_admin' => $sender === 'visitor' ? $current + 1 : $current,
        ])->save();

        return $msg;
    }

    public function row(ChatMessage $m): array
    {
        return [
            'id' => $m->id,
            'sender' => $m->sender,
            'author_name' => $m->author_name,
            'body' => $m->body,
            'time' => $m->created_at->format('g:i A'),
        ];
    }
}
