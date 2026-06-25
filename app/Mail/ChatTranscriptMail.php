<?php

namespace App\Mail;

use App\Models\ChatConversation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ChatTranscriptMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public ChatConversation $conversation) {}

    public function build(): static
    {
        $messages = $this->conversation->messages()->orderBy('id')->get()->map(fn ($m) => [
            'who' => $m->sender === 'visitor' ? ($this->conversation->displayName() ?: 'You') : ($m->author_name ?: 'Professor Peptides'),
            'is_visitor' => $m->sender === 'visitor',
            'body' => $m->body,
            'time' => $m->created_at->format('M j, g:i A'),
        ]);

        return $this->subject('Your chat with Professor Peptides')
            ->view('emails.chat-transcript')
            ->with([
                'conversation' => $this->conversation,
                'messages' => $messages,
            ]);
    }
}
