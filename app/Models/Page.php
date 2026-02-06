<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use App\Services\HtmlSanitizer;

class Page extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'content',
        'html',
        'meta_title',
        'meta_description',
        'featured_image',
        'status',
        'template',
        'variant_of',
        'variant_weight',
        'created_by',
        'published_at',
    ];

    protected $casts = [
        'content' => 'array',
        'published_at' => 'datetime',
    ];

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'variant_of');
    }

    public function variants(): HasMany
    {
        return $this->hasMany(self::class, 'variant_of');
    }

    public function versions(): HasMany
    {
        return $this->hasMany(PageVersion::class)->orderByDesc('version');
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function isPublished(): bool
    {
        return $this->status === 'published';
    }

    public static function generateSlug(string $title): string
    {
        $slug = Str::slug($title);
        $original = $slug;
        $count = 1;

        while (static::where('slug', $slug)->exists()) {
            $slug = "{$original}-{$count}";
            $count++;
        }

        return $slug;
    }

    /**
     * Return HTML with dangerous tags stripped using HTMLPurifier
     */
    public function sanitizedHtml(): string
    {
        if (!$this->html) {
            return '';
        }

        return app(HtmlSanitizer::class)->sanitize($this->html);
    }
}
