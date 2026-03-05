<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class StackStore extends Model
{
    public const CATEGORY_RESEARCH_GRADE = 'research_grade';
    public const CATEGORY_TELEHEALTH = 'telehealth';
    public const CATEGORY_AFFORDABLE = 'affordable';

    public const CATEGORIES = [
        self::CATEGORY_RESEARCH_GRADE => 'Research Grade',
        self::CATEGORY_TELEHEALTH => 'Telehealth',
        self::CATEGORY_AFFORDABLE => 'Affordable',
    ];

    protected $fillable = [
        'name', 'slug', 'logo', 'website_url', 'description',
        'category', 'is_active', 'is_recommended', 'order',
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

    public function peptideLinks(): HasMany
    {
        return $this->hasMany(StackStorePeptideLink::class)->orderBy('order');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }

    public function scopeCategory($query, string $category)
    {
        return $query->where('category', $category);
    }
}
