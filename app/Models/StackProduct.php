<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class StackProduct extends Model
{
    protected $fillable = [
        'name', 'slug', 'subtitle', 'description', 'image',
        'price', 'sale_price', 'dosing_info', 'key_benefits',
        'external_url', 'outbound_link_id', 'related_peptide_id',
        'is_featured', 'is_active', 'order',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'key_benefits' => 'array',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
        'order' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($product) {
            if (empty($product->slug)) {
                $product->slug = Str::slug($product->name);
            }
        });
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function goals(): BelongsToMany
    {
        return $this->belongsToMany(StackGoal::class, 'goal_stack_product')
            ->withPivot('order');
    }

    public function relatedPeptide(): BelongsTo
    {
        return $this->belongsTo(Peptide::class, 'related_peptide_id');
    }

    public function outboundLink(): BelongsTo
    {
        return $this->belongsTo(OutboundLink::class);
    }

    public function stores(): BelongsToMany
    {
        return $this->belongsToMany(StackStore::class, 'stack_store_product')
            ->withPivot('price', 'url', 'outbound_link_id', 'is_in_stock', 'is_recommended')
            ->withTimestamps()
            ->orderBy('stack_stores.order');
    }

    // Accessors
    public function getCurrentPriceAttribute(): string
    {
        return $this->sale_price ?? $this->price;
    }

    public function getSavingsAttribute(): string
    {
        if (!$this->sale_price) return '0.00';
        return number_format($this->price - $this->sale_price, 2, '.', '');
    }

    public function getHasSaleAttribute(): bool
    {
        return $this->sale_price !== null && $this->sale_price < $this->price;
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeForGoal($query, string $slug)
    {
        return $query->whereHas('goals', fn ($q) => $q->where('slug', $slug));
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }
}
