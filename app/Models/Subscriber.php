<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subscriber extends Model
{
    protected $fillable = [
        'email', 'name', 'phone', 'source', 'status',
        'subscribed_at', 'unsubscribed_at', 'ip_address', 'user_agent',
        // Segmentation
        'segment', 'quiz_completed', 'quiz_completed_at',
        // Klaviyo
        'klaviyo_id', 'klaviyo_synced_at', 'klaviyo_properties', 'needs_klaviyo_sync',
        // First Touch Attribution
        'first_session_id', 'first_utm_source', 'first_utm_medium',
        'first_utm_campaign', 'first_utm_content', 'first_referrer', 'first_landing_page',
        // Engagement
        'total_sessions', 'total_page_views', 'engagement_score',
        'engagement_tier', 'last_activity_at',
        // Downloads & Interest
        'lead_magnets_downloaded', 'peptides_viewed', 'primary_interest',
        'clicked_to_shop', 'shop_clicks', 'first_shop_click_at',
        // Conversion
        'is_customer', 'first_purchase_at', 'lifetime_value',
        // Device/Location
        'device_type', 'country', 'region', 'city',
    ];

    protected $casts = [
        'subscribed_at' => 'datetime',
        'unsubscribed_at' => 'datetime',
        'quiz_completed' => 'boolean',
        'quiz_completed_at' => 'datetime',
        'klaviyo_synced_at' => 'datetime',
        'klaviyo_properties' => 'array',
        'needs_klaviyo_sync' => 'boolean',
        'last_activity_at' => 'datetime',
        'lead_magnets_downloaded' => 'array',
        'peptides_viewed' => 'array',
        'clicked_to_shop' => 'boolean',
        'first_shop_click_at' => 'datetime',
        'is_customer' => 'boolean',
        'first_purchase_at' => 'datetime',
        'lifetime_value' => 'decimal:2',
    ];

    // Relationships
    public function sessions(): HasMany
    {
        return $this->hasMany(UserSession::class);
    }

    public function quizResponses(): HasMany
    {
        return $this->hasMany(QuizResponse::class);
    }

    public function popupInteractions(): HasMany
    {
        return $this->hasMany(PopupInteraction::class);
    }

    public function leadMagnetDownloads(): HasMany
    {
        return $this->hasMany(LeadMagnetDownload::class);
    }

    public function outboundClicks(): HasMany
    {
        return $this->hasMany(OutboundClick::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeSegment($query, string $segment)
    {
        return $query->where('segment', $segment);
    }

    public function scopeFromSource($query, string $source)
    {
        return $query->where('source', $source);
    }

    public function scopeHotLeads($query)
    {
        return $query->where('engagement_tier', 'hot');
    }

    public function scopeWarmLeads($query)
    {
        return $query->where('engagement_tier', 'warm');
    }

    public function scopeNeedsSyncToKlaviyo($query)
    {
        return $query->where('needs_klaviyo_sync', true);
    }

    // Methods
    public function unsubscribe(): void
    {
        $this->update([
            'status' => 'unsubscribed',
            'unsubscribed_at' => now(),
        ]);
    }

    public function updateEngagementTier(): void
    {
        $hotThreshold = (int) Setting::getValue('scoring', 'tier_hot_threshold', 40);
        $warmThreshold = (int) Setting::getValue('scoring', 'tier_warm_threshold', 15);

        $tier = match (true) {
            $this->engagement_score >= $hotThreshold => 'hot',
            $this->engagement_score >= $warmThreshold => 'warm',
            default => 'cold',
        };

        $this->update(['engagement_tier' => $tier]);
    }

    public function addEngagementPoints(int $points): void
    {
        $this->increment('engagement_score', $points);
        $this->updateEngagementTier();
    }

    /**
     * Batch update engagement tiers for all subscribers (for scheduled task)
     */
    public static function recalculateAllTiers(): int
    {
        $hot = (int) Setting::getValue('scoring', 'tier_hot_threshold', 40);
        $warm = (int) Setting::getValue('scoring', 'tier_warm_threshold', 15);

        $updated = 0;
        $updated += self::where('engagement_score', '>=', $hot)->where('engagement_tier', '!=', 'hot')->update(['engagement_tier' => 'hot']);
        $updated += self::where('engagement_score', '>=', $warm)->where('engagement_score', '<', $hot)->where('engagement_tier', '!=', 'warm')->update(['engagement_tier' => 'warm']);
        $updated += self::where('engagement_score', '<', $warm)->where('engagement_tier', '!=', 'cold')->update(['engagement_tier' => 'cold']);

        return $updated;
    }
}
