<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Quiz extends Model
{
    protected $fillable = [
        'name', 'slug', 'type', 'description',
        'title', 'intro_text', 'intro_image',
        'completion_title', 'completion_text',
        'settings', 'design',
        'klaviyo_list_id', 'klaviyo_start_event', 'klaviyo_complete_event',
        'outcome_page_id', 'redirect_url',
        'starts_count', 'completions_count',
        'is_active',
    ];

    protected $casts = [
        'settings' => 'array',
        'design' => 'array',
        'is_active' => 'boolean',
    ];

    // Quiz Types
    public const TYPE_SEGMENTATION = 'segmentation';
    public const TYPE_PRODUCT = 'product';
    public const TYPE_CUSTOM = 'custom';

    // Relationships
    public function questions(): HasMany
    {
        return $this->hasMany(QuizQuestion::class)->orderBy('order');
    }

    public function outcomes(): HasMany
    {
        return $this->hasMany(QuizOutcome::class);
    }

    public function responses(): HasMany
    {
        return $this->hasMany(QuizResponse::class);
    }

    public function outcomePage(): BelongsTo
    {
        return $this->belongsTo(Page::class, 'outcome_page_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    // Settings Accessors
    public function requiresEmail(): bool
    {
        return $this->settings['require_email'] ?? true;
    }

    public function showProgressBar(): bool
    {
        return $this->settings['show_progress_bar'] ?? true;
    }

    public function allowBack(): bool
    {
        return $this->settings['allow_back'] ?? true;
    }

    // Methods
    public function getCompletionRate(): float
    {
        if ($this->starts_count === 0) return 0;
        return round($this->completions_count / $this->starts_count * 100, 2);
    }

    public function determineOutcome(array $scores): ?QuizOutcome
    {
        return $this->outcomes()
            ->where('min_score', '<=', $scores['total'] ?? 0)
            ->orderByDesc('min_score')
            ->first();
    }

    public function determineSegment(array $scores): string
    {
        $tof = $scores['tof'] ?? 0;
        $mof = $scores['mof'] ?? 0;
        $bof = $scores['bof'] ?? 0;

        if ($bof >= $mof && $bof >= $tof) return 'bof';
        if ($mof >= $tof) return 'mof';
        return 'tof';
    }
}
