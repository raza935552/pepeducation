<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OutboundClick extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'outbound_link_id', 'session_id', 'subscriber_id', 'user_id',
        'source_page', 'source_component', 'final_url', 'passed_data',
        // Attribution data sent to Fast Peptix
        'pp_session', 'pp_email_hash', 'pp_segment', 'pp_engagement_score',
        'pp_health_goal', 'pp_experience_level', 'pp_recommended_peptide',
        'pp_quiz_completed',
        // Device info
        'device_type', 'browser', 'os', 'country',
        // Conversion
        'converted', 'converted_at', 'conversion_value', 'order_id',
        'created_at',
    ];

    protected $casts = [
        'passed_data' => 'array',
        'pp_quiz_completed' => 'boolean',
        'converted' => 'boolean',
        'converted_at' => 'datetime',
        'conversion_value' => 'decimal:2',
        'created_at' => 'datetime',
    ];

    // Relationships
    public function outboundLink(): BelongsTo
    {
        return $this->belongsTo(OutboundLink::class);
    }

    public function session(): BelongsTo
    {
        return $this->belongsTo(UserSession::class, 'session_id', 'session_id');
    }

    public function subscriber(): BelongsTo
    {
        return $this->belongsTo(Subscriber::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopeConverted($query)
    {
        return $query->where('converted', true);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    public function scopeFromSegment($query, string $segment)
    {
        return $query->where('pp_segment', $segment);
    }

    // Methods
    public function markConverted(float $value = null): void
    {
        $this->update([
            'converted' => true,
            'converted_at' => now(),
            'conversion_value' => $value,
        ]);

        $this->outboundLink?->increment('conversions_count');
    }
}
