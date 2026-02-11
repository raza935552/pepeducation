<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class StackBundle extends Model
{
    protected $fillable = [
        'name', 'slug', 'description', 'image', 'stack_goal_id',
        'bundle_price', 'external_url', 'outbound_link_id',
        'is_professor_pick', 'is_active', 'order',
    ];

    protected $casts = [
        'bundle_price' => 'decimal:2',
        'is_professor_pick' => 'boolean',
        'is_active' => 'boolean',
        'order' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($bundle) {
            if (empty($bundle->slug)) {
                $bundle->slug = Str::slug($bundle->name);
            }
        });
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function goal(): BelongsTo
    {
        return $this->belongsTo(StackGoal::class, 'stack_goal_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(StackBundleItem::class)->orderBy('order');
    }

    public function outboundLink(): BelongsTo
    {
        return $this->belongsTo(OutboundLink::class);
    }

    public function stores(): BelongsToMany
    {
        return $this->belongsToMany(StackStore::class, 'stack_store_bundle')
            ->withPivot('price', 'url', 'outbound_link_id', 'is_in_stock', 'is_recommended')
            ->withTimestamps()
            ->orderBy('stack_stores.order');
    }

    // Accessors
    public function getRegularTotalAttribute(): string
    {
        $total = $this->items->sum(function ($item) {
            return ($item->product->current_price ?? 0) * $item->quantity;
        });
        return number_format($total, 2, '.', '');
    }

    public function getSavingsAmountAttribute(): string
    {
        $regularTotal = (float) $this->regular_total;
        $savings = $regularTotal - (float) $this->bundle_price;
        return number_format(max(0, $savings), 2, '.', '');
    }

    public function getSavingsPercentageAttribute(): int
    {
        $regularTotal = (float) $this->regular_total;
        if ($regularTotal <= 0) return 0;
        return (int) round((($regularTotal - (float) $this->bundle_price) / $regularTotal) * 100);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeProfessorPicks($query)
    {
        return $query->where('is_professor_pick', true);
    }

    public function scopeForGoal($query, $goalId)
    {
        return $query->where('stack_goal_id', $goalId);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }
}
