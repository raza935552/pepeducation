<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OutboundLink extends Model
{
    protected $fillable = [
        'name', 'slug', 'destination_url', 'peptide_id',
        'utm_source', 'utm_medium', 'utm_campaign', 'utm_content',
        'append_segment', 'append_session', 'append_email', 'append_quiz_data',
        'track_klaviyo', 'click_count', 'is_active',
    ];

    protected $casts = [
        'append_segment' => 'boolean',
        'append_session' => 'boolean',
        'append_email' => 'boolean',
        'append_quiz_data' => 'boolean',
        'track_klaviyo' => 'boolean',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function clicks(): HasMany
    {
        return $this->hasMany(OutboundClick::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Methods
    public function getTrackingUrl(): string
    {
        return route('outbound.track', $this->slug);
    }

    public function buildFinalUrl(array $trackingData = []): string
    {
        $url = $this->destination_url;
        $params = [];

        // UTM params
        if ($this->utm_source) $params['utm_source'] = $this->utm_source;
        if ($this->utm_medium) $params['utm_medium'] = $this->utm_medium;
        if ($this->utm_campaign) $params['utm_campaign'] = $this->utm_campaign;
        if ($this->utm_content) $params['utm_content'] = $this->utm_content;

        // Pass through all pp_* params from getCrossDomainData()
        if ($this->append_session && isset($trackingData['pp_session'])) {
            $params['pp_session'] = $trackingData['pp_session'];
        }
        if ($this->append_segment && isset($trackingData['pp_segment'])) {
            $params['pp_segment'] = $trackingData['pp_segment'];
        }
        if ($this->append_email && isset($trackingData['email'])) {
            $params['pp_email_hash'] = hash('sha256', $trackingData['email']);
        } elseif (isset($trackingData['pp_email_hash'])) {
            $params['pp_email_hash'] = $trackingData['pp_email_hash'];
        }

        // Quiz data fields
        if ($this->append_quiz_data) {
            foreach (['pp_health_goal', 'pp_experience_level', 'pp_recommended_peptide', 'pp_quiz_completed'] as $key) {
                if (isset($trackingData[$key])) {
                    $params[$key] = $trackingData[$key];
                }
            }
        }

        // Engagement score
        if (isset($trackingData['pp_engagement_score'])) {
            $params['pp_engagement_score'] = $trackingData['pp_engagement_score'];
        }

        if (empty($params)) {
            return $url;
        }

        // Parse URL properly to preserve fragments
        $parsed = parse_url($url);
        $fragment = isset($parsed['fragment']) ? '#' . $parsed['fragment'] : '';
        $baseUrl = str_replace($fragment, '', $url);
        $separator = str_contains($baseUrl, '?') ? '&' : '?';

        return $baseUrl . $separator . http_build_query($params) . $fragment;
    }
}
