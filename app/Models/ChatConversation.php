<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class ChatConversation extends Model
{
    protected $fillable = [
        'token', 'visitor_id', 'name', 'email', 'status', 'mode',
        'human_requested', 'human_requested_at',
        'rating', 'rated_at', 'transcript_sent_at',
        'assigned_to', 'unread_for_admin', 'last_message_at',
        'ip_address', 'user_agent', 'landing_page',
    ];

    protected $casts = [
        'last_message_at' => 'datetime',
        'human_requested' => 'boolean',
        'human_requested_at' => 'datetime',
        'rated_at' => 'datetime',
        'transcript_sent_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (ChatConversation $c) {
            if (empty($c->token)) {
                $c->token = Str::random(40);
            }
        });
    }

    public function messages(): HasMany
    {
        return $this->hasMany(ChatMessage::class, 'conversation_id');
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function displayName(): string
    {
        return $this->name ?: (\Illuminate\Support\Str::before((string) $this->email, '@') ?: 'Visitor');
    }
}
