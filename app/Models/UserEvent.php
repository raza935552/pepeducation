<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserEvent extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'session_id', 'user_id', 'subscriber_id',
        'event_type', 'event_category', 'event_data',
        'page_url', 'page_title', 'page_type',
        'element_type', 'element_id', 'element_class', 'element_text',
        'element_x', 'element_y',
        'sequence', 'time_since_session_start', 'time_since_last_event',
        'scroll_depth', 'time_on_page', 'engagement_points',
        'synced_to_klaviyo', 'synced_at', 'created_at',
    ];

    protected $casts = [
        'event_data' => 'array',
        'synced_to_klaviyo' => 'boolean',
        'created_at' => 'datetime',
    ];

    // Relationships
    public function session(): BelongsTo
    {
        return $this->belongsTo(UserSession::class, 'session_id', 'session_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function subscriber(): BelongsTo
    {
        return $this->belongsTo(Subscriber::class);
    }

    public function abTest(): BelongsTo
    {
        return $this->belongsTo(AbTest::class);
    }

    // Scopes
    public function scopeOfType($query, string $type)
    {
        return $query->where('event_type', $type);
    }

    public function scopeCategory($query, string $category)
    {
        return $query->where('event_category', $category);
    }

    public function scopeOnPage($query, string $url)
    {
        return $query->where('page_url', $url);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    public function scopeNeedsSyncToKlaviyo($query)
    {
        return $query->where('synced_to_klaviyo', false);
    }

    public function scopeOlderThan($query, int $days)
    {
        return $query->where('created_at', '<', now()->subDays($days));
    }

    // Event Types
    public const TYPE_PAGE_VIEW = 'page_view';
    public const TYPE_CLICK = 'click';
    public const TYPE_SCROLL = 'scroll';
    public const TYPE_FORM_FOCUS = 'form_focus';
    public const TYPE_FORM_SUBMIT = 'form_submit';
    public const TYPE_QUIZ_START = 'quiz_start';
    public const TYPE_QUIZ_ANSWER = 'quiz_answer';
    public const TYPE_QUIZ_COMPLETE = 'quiz_complete';
    public const TYPE_POPUP_VIEW = 'popup_view';
    public const TYPE_POPUP_CONVERT = 'popup_convert';
    public const TYPE_LEAD_MAGNET = 'lead_magnet_download';
    public const TYPE_CTA_CLICK = 'cta_click';
    public const TYPE_OUTBOUND_CLICK = 'outbound_click';
    public const TYPE_RAGE_CLICK = 'rage_click';
}
