<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UserSession extends Model
{
    protected $fillable = [
        'session_id', 'user_id', 'subscriber_id',
        // Timing
        'started_at', 'ended_at', 'duration_seconds',
        // Entry/Exit
        'entry_url', 'exit_url', 'referrer', 'referrer_domain',
        // UTM
        'utm_source', 'utm_medium', 'utm_campaign', 'utm_content', 'utm_term',
        // Device
        'ip_address', 'user_agent', 'device_type', 'browser', 'browser_version',
        'os', 'os_version', 'is_mobile', 'is_bot',
        // Location
        'country', 'country_name', 'region', 'city', 'latitude', 'longitude',
        // Engagement
        'pages_viewed', 'events_count', 'avg_scroll_depth', 'engagement_score',
        // Segmentation
        'segment', 'is_returning', 'session_number',
        // Conversion
        'converted', 'conversion_type', 'converted_at',
        // Status
        'is_bounced', 'synced_to_klaviyo',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
        'converted_at' => 'datetime',
        'is_returning' => 'boolean',
        'is_mobile' => 'boolean',
        'is_bot' => 'boolean',
        'is_bounced' => 'boolean',
        'converted' => 'boolean',
        'synced_to_klaviyo' => 'boolean',
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function subscriber(): BelongsTo
    {
        return $this->belongsTo(Subscriber::class);
    }

    public function events(): HasMany
    {
        return $this->hasMany(UserEvent::class, 'session_id', 'session_id');
    }

    public function abTestAssignments(): HasMany
    {
        return $this->hasMany(AbTestAssignment::class, 'session_id', 'session_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->whereNull('ended_at');
    }

    public function scopeConverted($query)
    {
        return $query->where('converted', true);
    }

    public function scopeSegment($query, string $segment)
    {
        return $query->where('segment', $segment);
    }

    public function scopeFromSource($query, string $source)
    {
        return $query->where('utm_source', $source);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('started_at', today());
    }

    // Methods
    public function endSession(): void
    {
        $this->update([
            'ended_at' => now(),
            'duration_seconds' => now()->diffInSeconds($this->started_at),
        ]);
    }

    public function addEngagementPoints(int $points): void
    {
        $this->increment('engagement_score', $points);
    }
}
