<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WebhookLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'source', 'event_type', 'payload', 'headers',
        'status', 'response', 'error_message',
        'processed_at', 'created_at',
    ];

    protected $casts = [
        'payload' => 'array',
        'headers' => 'array',
        'processed_at' => 'datetime',
        'created_at' => 'datetime',
    ];

    // Sources
    public const SOURCE_KLAVIYO = 'klaviyo';
    public const SOURCE_FASTPEPTIX = 'fastpeptix';

    // Status
    public const STATUS_PENDING = 'pending';
    public const STATUS_PROCESSED = 'processed';
    public const STATUS_FAILED = 'failed';

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeFailed($query)
    {
        return $query->where('status', self::STATUS_FAILED);
    }

    public function scopeFromSource($query, string $source)
    {
        return $query->where('source', $source);
    }

    public function scopeRecent($query)
    {
        return $query->where('created_at', '>=', now()->subDays(7));
    }

    // Methods
    public function markProcessed(array $response = null): void
    {
        $this->update([
            'status' => self::STATUS_PROCESSED,
            'response' => $response,
            'processed_at' => now(),
        ]);
    }

    public function markFailed(string $error): void
    {
        $this->update([
            'status' => self::STATUS_FAILED,
            'error_message' => $error,
            'processed_at' => now(),
        ]);
    }
}
