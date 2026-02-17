<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ResultsBank extends Model
{
    protected $table = 'results_bank';

    protected $fillable = [
        'health_goal',
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
    ];

    protected $casts = [
        'star_rating' => 'decimal:1',
        'benefits' => 'array',
        'is_active' => 'boolean',
    ];

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
     * Get the human-readable health goal label.
     */
    public function getGoalLabelAttribute(): string
    {
        return self::HEALTH_GOALS[$this->health_goal] ?? ucfirst(str_replace('_', ' ', $this->health_goal));
    }

    /**
     * Get the human-readable experience level label.
     */
    public function getExperienceLabelAttribute(): string
    {
        return self::EXPERIENCE_LEVELS[$this->experience_level] ?? ucfirst($this->experience_level);
    }
}
