<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BugReport extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'description',
        'page_url',
        'priority',
        'status',
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

    public function getStatusBadgeAttribute(): array
    {
        return match ($this->status) {
            'reported' => ['label' => 'Reported', 'color' => 'blue'],
            'in_progress' => ['label' => 'In Progress', 'color' => 'yellow'],
            'fixed' => ['label' => 'Fixed', 'color' => 'green'],
            'closed' => ['label' => 'Closed', 'color' => 'gray'],
            default => ['label' => 'Unknown', 'color' => 'gray'],
        };
    }

    public function getPriorityBadgeAttribute(): array
    {
        return match ($this->priority) {
            'low' => ['label' => 'Low', 'color' => 'gray'],
            'medium' => ['label' => 'Medium', 'color' => 'blue'],
            'high' => ['label' => 'High', 'color' => 'orange'],
            'critical' => ['label' => 'Critical', 'color' => 'red'],
            default => ['label' => 'Unknown', 'color' => 'gray'],
        };
    }
}
