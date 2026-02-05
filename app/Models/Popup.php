<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Popup extends Model
{
    protected $fillable = [
        'name', 'slug', 'type',
        'headline', 'body', 'image', 'button_text',
        'success_message', 'success_redirect_url',
        'form_fields', 'triggers', 'targeting', 'display_rules', 'design',
        'klaviyo_list_id', 'klaviyo_event', 'lead_magnet_id',
        'views_count', 'conversions_count', 'dismissals_count', 'impressions_count',
        'is_active', 'starts_at', 'ends_at',
    ];

    protected $casts = [
        'form_fields' => 'array',
        'triggers' => 'array',
        'targeting' => 'array',
        'display_rules' => 'array',
        'design' => 'array',
        'is_active' => 'boolean',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
    ];

    // Relationships
    public function leadMagnet(): BelongsTo
    {
        return $this->belongsTo(LeadMagnet::class);
    }

    public function interactions(): HasMany
    {
        return $this->hasMany(PopupInteraction::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where(fn($q) => $q->whereNull('starts_at')->orWhere('starts_at', '<=', now()))
            ->where(fn($q) => $q->whereNull('ends_at')->orWhere('ends_at', '>=', now()));
    }

    public function scopeForSegment($query, ?string $segment)
    {
        return $query->where(function ($q) use ($segment) {
            $q->whereNull('targeting')
                ->orWhereJsonContains('targeting->segments', $segment)
                ->orWhereJsonLength('targeting->segments', 0);
        });
    }

    // Methods
    public function getConversionRate(): float
    {
        if ($this->views_count === 0) return 0;
        return round($this->conversions_count / $this->views_count * 100, 2);
    }

    public function shouldShowForSession(string $sessionId, array $context = []): bool
    {
        // Check targeting
        if (!$this->matchesTargeting($context)) return false;

        // Check display rules
        if (!$this->matchesDisplayRules($sessionId)) return false;

        return true;
    }

    protected function matchesTargeting(array $context): bool
    {
        $targeting = $this->targeting ?? [];

        // Check segment
        if (!empty($targeting['segments']) && isset($context['segment'])) {
            if (!in_array($context['segment'], $targeting['segments'])) return false;
        }

        // Check device
        if (!empty($targeting['devices']) && isset($context['device_type'])) {
            if (!in_array($context['device_type'], $targeting['devices'])) return false;
        }

        // Check page
        if (!empty($targeting['exclude_pages']) && isset($context['page_url'])) {
            foreach ($targeting['exclude_pages'] as $pattern) {
                if (str_contains($context['page_url'], $pattern)) return false;
            }
        }

        return true;
    }

    protected function matchesDisplayRules(string $sessionId): bool
    {
        $rules = $this->display_rules ?? [];

        // Hide if subscribed
        if ($rules['hide_if_subscribed'] ?? false) {
            $session = UserSession::where('session_id', $sessionId)->first();
            if ($session?->subscriber_id) return false;
        }

        return true;
    }

    public function getInlineStyles(): string
    {
        $design = $this->design ?? [];
        $styles = [];

        if (!empty($design['background_color'])) {
            $styles[] = "background-color: {$design['background_color']}";
        }

        if (!empty($design['border_radius'])) {
            $styles[] = "border-radius: {$design['border_radius']}px";
        }

        return implode('; ', $styles);
    }
}
