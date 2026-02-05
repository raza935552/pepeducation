<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AbTest extends Model
{
    protected $fillable = [
        'name', 'description', 'test_type', 'target_element', 'target_url',
        'variants', 'traffic_split', 'goal_type', 'goal_element', 'goal_url',
        'status', 'starts_at', 'ends_at',
    ];

    protected $casts = [
        'variants' => 'array',
        'traffic_split' => 'array',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
    ];

    // Relationships
    public function assignments(): HasMany
    {
        return $this->hasMany(AbTestAssignment::class);
    }

    public function events(): HasMany
    {
        return $this->hasMany(UserEvent::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active')
            ->where(function ($q) {
                $q->whereNull('starts_at')->orWhere('starts_at', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('ends_at')->orWhere('ends_at', '>=', now());
            });
    }

    public function scopeForUrl($query, string $url)
    {
        return $query->where('target_url', $url)
            ->orWhereNull('target_url');
    }

    // Methods
    public function assignVariant(string $sessionId): string
    {
        $existing = $this->assignments()->where('session_id', $sessionId)->first();
        if ($existing) {
            return $existing->variant;
        }

        $variant = $this->selectRandomVariant();

        $this->assignments()->create([
            'session_id' => $sessionId,
            'variant' => $variant,
        ]);

        return $variant;
    }

    protected function selectRandomVariant(): string
    {
        $rand = mt_rand(1, 100);
        $cumulative = 0;

        foreach ($this->traffic_split as $variant => $percentage) {
            $cumulative += $percentage;
            if ($rand <= $cumulative) {
                return $variant;
            }
        }

        return array_key_first($this->traffic_split);
    }

    public function getStats(): array
    {
        $stats = [];
        foreach (array_keys($this->variants) as $variant) {
            $assignments = $this->assignments()->where('variant', $variant);
            $stats[$variant] = [
                'participants' => $assignments->count(),
                'conversions' => $assignments->where('converted', true)->count(),
                'conversion_rate' => $assignments->count() > 0
                    ? round($assignments->where('converted', true)->count() / $assignments->count() * 100, 2)
                    : 0,
            ];
        }
        return $stats;
    }
}
