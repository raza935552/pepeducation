<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lander extends Model
{
    protected $fillable = [
        'slug', 'name', 'template', 'outbound_slug', 'is_active', 'noindex', 'content',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'noindex' => 'boolean',
        'content' => 'array',
    ];

    /** Public URL of the lander. */
    public function getUrlAttribute(): string
    {
        return url('/lp/' . $this->slug);
    }

    /**
     * Safe accessor into the content tree by dot path, with a default.
     * e.g. $lander->c('hero.headline', '')
     */
    public function c(string $path, $default = '')
    {
        return data_get($this->content ?? [], $path, $default);
    }
}
