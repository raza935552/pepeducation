<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerIoSetting extends Model
{
    protected $table = 'customerio_settings';

    protected $fillable = [
        'site_id', 'api_key', 'region', 'is_enabled',
        'track_quiz_started', 'track_quiz_completed', 'track_email_captured',
        'track_quiz_abandoned', 'track_lead_magnet_download', 'track_outbound_click',
        'track_stack_completed', 'track_subscribed', 'enable_page_tracking', 'meta',
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
        'track_quiz_started' => 'boolean',
        'track_quiz_completed' => 'boolean',
        'track_email_captured' => 'boolean',
        'track_quiz_abandoned' => 'boolean',
        'track_lead_magnet_download' => 'boolean',
        'track_outbound_click' => 'boolean',
        'track_stack_completed' => 'boolean',
        'track_subscribed' => 'boolean',
        'enable_page_tracking' => 'boolean',
        'meta' => 'array',
    ];

    public function setSiteIdAttribute($value): void
    {
        $this->attributes['site_id'] = $value ? encrypt($value) : null;
    }

    public function getSiteIdAttribute($value): ?string
    {
        if (!$value) return null;
        try { return decrypt($value); } catch (\Exception $e) { return $value; }
    }

    public function setApiKeyAttribute($value): void
    {
        $this->attributes['api_key'] = $value ? encrypt($value) : null;
    }

    public function getApiKeyAttribute($value): ?string
    {
        if (!$value) return null;
        try { return decrypt($value); } catch (\Exception $e) { return $value; }
    }

    public static function current(): ?self
    {
        return static::first();
    }

    public static function getOrCreate(): self
    {
        return static::firstOrCreate([], [
            'is_enabled' => false, 'region' => 'us',
            'track_quiz_started' => true, 'track_quiz_completed' => true,
            'track_email_captured' => true, 'track_quiz_abandoned' => true,
            'track_lead_magnet_download' => true, 'track_outbound_click' => true,
            'track_stack_completed' => true, 'track_subscribed' => true,
            'enable_page_tracking' => false,
        ]);
    }

    public function isConfigured(): bool
    {
        return !empty($this->site_id) && !empty($this->api_key);
    }

    public function getSiteId(): ?string
    {
        return $this->site_id ?: config('customerio.site_id');
    }

    public function getApiKey(): ?string
    {
        return $this->api_key ?: config('customerio.api_key');
    }

    public function getBaseUrl(): string
    {
        $region = $this->region ?: config('customerio.region', 'us');
        $urls = config('customerio.base_urls', [
            'us' => 'https://track.customer.io/api/v1/',
            'eu' => 'https://track-eu.customer.io/api/v1/',
        ]);
        return $urls[$region] ?? $urls['us'];
    }
}
