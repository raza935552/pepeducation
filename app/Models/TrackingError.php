<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrackingError extends Model
{
    protected $fillable = [
        'session_id', 'user_id',
        'error_type', 'error_code', 'error_message', 'stack_trace',
        'page_url', 'page_title', 'component',
        'browser', 'os', 'device_type',
        'user_action', 'form_data',
        'occurrence_count', 'first_occurred_at', 'last_occurred_at',
        'status', 'resolution_notes',
    ];

    protected $casts = [
        'form_data' => 'array',
        'first_occurred_at' => 'datetime',
        'last_occurred_at' => 'datetime',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopeUnresolved($query)
    {
        return $query->whereIn('status', ['new', 'acknowledged']);
    }

    public function scopeOfType($query, string $type)
    {
        return $query->where('error_type', $type);
    }

    public function scopeOnPage($query, string $url)
    {
        return $query->where('page_url', $url);
    }

    public function scopeRecent($query)
    {
        return $query->where('last_occurred_at', '>=', now()->subDays(7));
    }

    // Error Types
    public const TYPE_JS_ERROR = 'js_error';
    public const TYPE_404 = '404';
    public const TYPE_FORM_ERROR = 'form_error';
    public const TYPE_API_ERROR = 'api_error';

    // Status
    public const STATUS_NEW = 'new';
    public const STATUS_ACKNOWLEDGED = 'acknowledged';
    public const STATUS_RESOLVED = 'resolved';
    public const STATUS_IGNORED = 'ignored';

    // Methods
    public function acknowledge(): void
    {
        $this->update(['status' => self::STATUS_ACKNOWLEDGED]);
    }

    public function resolve(string $notes = null): void
    {
        $this->update([
            'status' => self::STATUS_RESOLVED,
            'resolution_notes' => $notes,
        ]);
    }

    public function ignore(): void
    {
        $this->update(['status' => self::STATUS_IGNORED]);
    }

    public function incrementOccurrence(): void
    {
        $this->increment('occurrence_count');
        $this->update(['last_occurred_at' => now()]);
    }
}
