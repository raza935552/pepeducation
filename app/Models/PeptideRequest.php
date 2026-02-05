<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PeptideRequest extends Model
{
    protected $fillable = [
        'user_id',
        'peptide_name',
        'source_links',
        'pdf_path',
        'notes',
        'status',
        'rejection_reason',
        'published_peptide_id',
        'processed_by',
        'processed_at',
    ];

    protected $casts = [
        'source_links' => 'array',
        'processed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function publishedPeptide(): BelongsTo
    {
        return $this->belongsTo(Peptide::class, 'published_peptide_id');
    }

    public function processor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    public function getStatusBadgeAttribute(): array
    {
        return match ($this->status) {
            'pending' => ['label' => 'Pending', 'color' => 'yellow'],
            'in_progress' => ['label' => 'In Progress', 'color' => 'blue'],
            'published' => ['label' => 'Published', 'color' => 'green'],
            'rejected' => ['label' => 'Rejected', 'color' => 'red'],
            default => ['label' => 'Unknown', 'color' => 'gray'],
        };
    }
}
