<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supporter extends Model
{
    protected $fillable = [
        'name',
        'logo',
        'website_url',
        'tier',
        'is_featured',
        'display_order',
        'status',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('display_order')->orderBy('name');
    }

    public function scopeByTier($query, $tier)
    {
        return $query->where('tier', $tier);
    }

    public function getTierColorAttribute(): string
    {
        return match ($this->tier) {
            'platinum' => 'bg-gray-100 text-gray-800',
            'gold' => 'bg-amber-100 text-amber-800',
            'silver' => 'bg-slate-100 text-slate-700',
            'bronze' => 'bg-orange-100 text-orange-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }
}
