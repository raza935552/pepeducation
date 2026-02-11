<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class StackStore extends Model
{
    protected $fillable = [
        'name', 'slug', 'logo', 'website_url', 'description',
        'is_active', 'is_recommended', 'order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_recommended' => 'boolean',
        'order' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($store) {
            if (empty($store->slug)) {
                $store->slug = Str::slug($store->name);
            }
        });
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(StackProduct::class, 'stack_store_product')
            ->withPivot('price', 'url', 'outbound_link_id', 'is_in_stock', 'is_recommended')
            ->withTimestamps();
    }

    public function bundles(): BelongsToMany
    {
        return $this->belongsToMany(StackBundle::class, 'stack_store_bundle')
            ->withPivot('price', 'url', 'outbound_link_id', 'is_in_stock', 'is_recommended')
            ->withTimestamps();
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }
}
