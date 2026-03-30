<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ResultsBank extends Model
{
    protected $table = 'results_bank';

    protected $fillable = [
        'health_goal',
        'health_goal_label',
        'experience_level',
        'peptide_name',
        'peptide_slug',
        'stack_product_id',
        'star_rating',
        'rating_label',
        'testimonial',
        'testimonial_author',
        'description',
        'benefits',
        'is_active',
        'display_fields',
        'accordion_items',
    ];

    protected $casts = [
        'star_rating' => 'decimal:1',
        'benefits' => 'array',
        'is_active' => 'boolean',
        'display_fields' => 'array',
        'accordion_items' => 'array',
    ];

    public function shouldDisplay(string $field): bool
    {
        $fields = $this->display_fields;
        if (empty($fields)) {
            return true; // Show everything by default
        }
        // If the field isn't explicitly set in display_fields, show it by default
        if (!array_key_exists($field, $fields)) {
            return true;
        }
        return !empty($fields[$field]);
    }

    // Health goal constants (match option values from quiz)
    public const GOAL_FAT_LOSS = 'fat_loss';
    public const GOAL_MUSCLE_GROWTH = 'muscle_growth';
    public const GOAL_ANTI_AGING = 'anti_aging';
    public const GOAL_INJURY_RECOVERY = 'injury_recovery';
    public const GOAL_COGNITIVE = 'cognitive';
    public const GOAL_SLEEP = 'sleep';
    public const GOAL_IMMUNE = 'immune';
    public const GOAL_SEXUAL_HEALTH = 'sexual_health';
    public const GOAL_GUT_HEALTH = 'gut_health';
    public const GOAL_GENERAL_WELLNESS = 'general_wellness';

    public const HEALTH_GOALS = [
        self::GOAL_FAT_LOSS => 'Fat Loss & Metabolism',
        self::GOAL_MUSCLE_GROWTH => 'Muscle Growth & Recovery',
        self::GOAL_ANTI_AGING => 'Anti-Aging & Longevity',
        self::GOAL_INJURY_RECOVERY => 'Injury Recovery & Healing',
        self::GOAL_COGNITIVE => 'Cognitive Enhancement',
        self::GOAL_SLEEP => 'Sleep Optimization',
        self::GOAL_IMMUNE => 'Immune Support',
        self::GOAL_SEXUAL_HEALTH => 'Sexual Health & Vitality',
        self::GOAL_GUT_HEALTH => 'Gut Health',
        self::GOAL_GENERAL_WELLNESS => 'General Wellness',
    ];

    public const EXPERIENCE_BEGINNER = 'beginner';
    public const EXPERIENCE_ADVANCED = 'advanced';

    public const EXPERIENCE_LEVELS = [
        self::EXPERIENCE_BEGINNER => 'Beginner',
        self::EXPERIENCE_ADVANCED => 'Advanced',
    ];

    public function stackProduct(): BelongsTo
    {
        return $this->belongsTo(StackProduct::class);
    }

    /**
     * Resolve the best peptide recommendation for given quiz answers.
     */
    public static function resolve(string $healthGoal, string $experienceLevel): ?self
    {
        return static::where('health_goal', $healthGoal)
            ->where('experience_level', $experienceLevel)
            ->where('is_active', true)
            ->with('stackProduct.stores')
            ->first();
    }

    /**
     * Resolve a recommendation excluding a specific peptide (Path 3 same-category).
     * Falls back to any experience level if the exact match is the excluded peptide.
     */
    public static function resolveExcluding(string $healthGoal, string $experienceLevel, string $excludeSlug): ?self
    {
        // Try exact match first, excluding the unwanted peptide
        $entry = static::where('health_goal', $healthGoal)
            ->where('experience_level', $experienceLevel)
            ->where('peptide_slug', '!=', $excludeSlug)
            ->where('is_active', true)
            ->with('stackProduct.stores')
            ->first();

        if ($entry) return $entry;

        // Fall back to any experience level in the same category
        return static::where('health_goal', $healthGoal)
            ->where('peptide_slug', '!=', $excludeSlug)
            ->where('is_active', true)
            ->with('stackProduct.stores')
            ->orderBy('star_rating', 'desc')
            ->orderBy('id')
            ->first();
    }

    /**
     * Get all health goals: hardcoded constants + any custom goals from the database.
     */
    public static function allHealthGoals(): array
    {
        $custom = static::select('health_goal', 'health_goal_label')
            ->distinct('health_goal')
            ->pluck('health_goal_label', 'health_goal')
            ->filter(fn ($label, $key) => $key) // remove null keys
            ->mapWithKeys(fn ($label, $key) => [
                $key => self::HEALTH_GOALS[$key] ?? ($label ?: ucfirst(str_replace('_', ' ', $key))),
            ])
            ->toArray();

        return array_merge(self::HEALTH_GOALS, $custom);
    }

    /**
     * Get the human-readable health goal label.
     */
    public function getGoalLabelAttribute(): string
    {
        return self::HEALTH_GOALS[$this->health_goal]
            ?? $this->health_goal_label
            ?? ucfirst(str_replace('_', ' ', $this->health_goal));
    }

    /**
     * Get the human-readable experience level label.
     */
    public function getExperienceLabelAttribute(): string
    {
        return self::EXPERIENCE_LEVELS[$this->experience_level] ?? ucfirst($this->experience_level);
    }
}
