<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Contribution extends Model
{
    protected $fillable = [
        'user_id',
        'peptide_id',
        'section',
        'original_content',
        'new_content',
        'edit_reason',
        'status',
        'reviewer_notes',
        'reviewed_by',
        'reviewed_at',
        'published_at',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
        'published_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function peptide(): BelongsTo
    {
        return $this->belongsTo(Peptide::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function getStatusBadgeAttribute(): array
    {
        return match ($this->status) {
            'pending' => ['label' => 'Pending', 'color' => 'yellow'],
            'under_review' => ['label' => 'Under Review', 'color' => 'blue'],
            'approved' => ['label' => 'Approved', 'color' => 'green'],
            'rejected' => ['label' => 'Rejected', 'color' => 'red'],
            default => ['label' => 'Unknown', 'color' => 'gray'],
        };
    }

    public function getSectionLabelAttribute(): string
    {
        return match ($this->section) {
            'overview' => 'Overview',
            'benefits' => 'Key Benefits',
            'timeline' => 'What to Expect',
            'warnings' => 'Safety Warnings',
            'mechanism' => 'Mechanism of Action',
            'quick_stats' => 'Quick Reference',
            'molecular' => 'Molecular Info',
            default => ucfirst($this->section),
        };
    }
}
