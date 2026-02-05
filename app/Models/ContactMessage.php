<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContactMessage extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'email',
        'subject',
        'message',
        'status',
        'assigned_to',
        'admin_notes',
        'resolved_at',
    ];

    protected $casts = [
        'resolved_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function getStatusBadgeAttribute(): array
    {
        return match ($this->status) {
            'new' => ['label' => 'New', 'color' => 'blue'],
            'in_progress' => ['label' => 'In Progress', 'color' => 'yellow'],
            'resolved' => ['label' => 'Resolved', 'color' => 'green'],
            default => ['label' => 'Unknown', 'color' => 'gray'],
        };
    }

    public static function subjectOptions(): array
    {
        return [
            'general' => 'General Question',
            'bug' => 'Bug Report',
            'feature' => 'Feature Request',
            'correction' => 'Content Correction',
            'partnership' => 'Partnership Inquiry',
            'other' => 'Other',
        ];
    }
}
