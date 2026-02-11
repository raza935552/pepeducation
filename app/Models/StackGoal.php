<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class StackGoal extends Model
{
    protected $fillable = [
        'name', 'slug', 'description', 'icon', 'image', 'color', 'order', 'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'order' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($goal) {
            if (empty($goal->slug)) {
                $goal->slug = Str::slug($goal->name);
            }
        });
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(StackProduct::class, 'goal_stack_product')
            ->withPivot('order')
            ->orderByPivot('order');
    }

    public function bundles(): HasMany
    {
        return $this->hasMany(StackBundle::class);
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
