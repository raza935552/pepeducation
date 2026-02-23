<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuizQuestion extends Model
{
    // Slide Types
    public const SLIDE_QUESTION = 'question';
    public const SLIDE_QUESTION_TEXT = 'question_text';
    public const SLIDE_INTERMISSION = 'intermission';
    public const SLIDE_LOADING = 'loading';
    public const SLIDE_EMAIL_CAPTURE = 'email_capture';
    public const SLIDE_PEPTIDE_REVEAL = 'peptide_reveal';
    public const SLIDE_VENDOR_REVEAL = 'vendor_reveal';
    public const SLIDE_BRIDGE = 'bridge';

    public const SLIDE_TYPES = [
        self::SLIDE_QUESTION,
        self::SLIDE_QUESTION_TEXT,
        self::SLIDE_INTERMISSION,
        self::SLIDE_LOADING,
        self::SLIDE_EMAIL_CAPTURE,
        self::SLIDE_PEPTIDE_REVEAL,
        self::SLIDE_VENDOR_REVEAL,
        self::SLIDE_BRIDGE,
    ];

    // Predefined marketing/Klaviyo segmentation tags for answer options
    public const OPTION_TAGS = [
        // Health Goal Interest
        'interested_weight_loss',
        'interested_muscle_growth',
        'interested_anti_aging',
        'interested_cognitive',
        'interested_sleep',
        'interested_injury_recovery',
        'interested_general_wellness',
        'interested_immune',
        'interested_sexual_health',
        'interested_gut_health',
        // Experience Level
        'beginner',
        'intermediate',
        'advanced',
        // Purchase Intent
        'ready_to_buy',
        'researching',
        'just_curious',
        // Funnel Signals
        'high_intent',
        'needs_education',
        'price_sensitive',
        'wants_to_stack',
        // Barrier / Hesitation
        'barrier_education',
        'barrier_sourcing',
        'barrier_safety',
        'barrier_needles',
        'hesitation_too_many_choices',
        'hesitation_vendor_trust',
        'hesitation_hype_vs_real',
        // Vendor Preference
        'prefers_telehealth',
        'prefers_research_grade',
        'prefers_affordable',
        'values_lab_reports',
        'values_reviews',
        // Stacking Behavior
        'wants_to_add',
        'wants_to_upgrade',
        'wants_fresh_start',
        'just_browsing',
        // Product Interest
        'interested_bpc157',
        'interested_tirzepatide',
        'interested_ghk_cu',
        'interested_semaglutide',
        'interested_pt141',
        'interested_epithalon',
        'interested_tb500',
        'interested_semax',
        'interested_dsip',
        'interested_thymosin_alpha1',
        'interested_cjc1295_ipamorelin',
        // Demographics
        'male',
        'female',
        'age_18_29',
        'age_30_39',
        'age_40_49',
        'age_50_59',
        'age_60_plus',
    ];

    // Slide types that collect user input (need answer storage)
    public const INPUT_SLIDE_TYPES = [
        self::SLIDE_QUESTION,
        self::SLIDE_QUESTION_TEXT,
        self::SLIDE_EMAIL_CAPTURE,
    ];

    // Question Types (must match database values)
    public const TYPE_SINGLE = 'single_choice';
    public const TYPE_MULTIPLE = 'multiple_choice';
    public const TYPE_TEXT = 'text';
    public const TYPE_EMAIL = 'email';
    public const TYPE_SCALE = 'scale';

    protected $fillable = [
        'quiz_id', 'slide_type', 'question_text', 'question_type', 'order',
        'options', 'klaviyo_property', 'is_required', 'settings',
        'show_conditions',
        'content_title', 'content_body', 'content_source',
        'auto_advance_seconds', 'cta_text', 'cta_url',
        'dynamic_content_key', 'dynamic_content_map',
    ];

    protected $casts = [
        'options' => 'array',
        'settings' => 'array',
        'show_conditions' => 'array',
        'is_required' => 'boolean',
        'dynamic_content_map' => 'array',
        'auto_advance_seconds' => 'integer',
    ];

    // Relationships
    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class);
    }

    // Slide type helpers
    public function isQuestion(): bool
    {
        return in_array($this->slide_type, [self::SLIDE_QUESTION, self::SLIDE_QUESTION_TEXT]);
    }

    public function isInputSlide(): bool
    {
        return in_array($this->slide_type, self::INPUT_SLIDE_TYPES);
    }

    public function isAutoAdvance(): bool
    {
        return $this->slide_type === self::SLIDE_LOADING && $this->auto_advance_seconds > 0;
    }

    // Methods
    public function getOptionScores(string $selectedValue): array
    {
        if (!$this->options) return ['tof' => 0, 'mof' => 0, 'bof' => 0];

        foreach ($this->options as $option) {
            if (($option['value'] ?? '') === $selectedValue) {
                return [
                    'tof' => $option['score_tof'] ?? 0,
                    'mof' => $option['score_mof'] ?? 0,
                    'bof' => $option['score_bof'] ?? 0,
                ];
            }
        }

        return ['tof' => 0, 'mof' => 0, 'bof' => 0];
    }

    public function getKlaviyoValue(string $selectedValue): ?string
    {
        if (!$this->options) return $selectedValue;

        foreach ($this->options as $option) {
            if (($option['value'] ?? '') === $selectedValue) {
                return $option['klaviyo_value'] ?? $selectedValue;
            }
        }

        return $selectedValue;
    }

    public function getOptionTags(string $selectedValue): array
    {
        if (!$this->options) return [];

        foreach ($this->options as $option) {
            if (($option['value'] ?? '') === $selectedValue) {
                return $option['tags'] ?? [];
            }
        }

        return [];
    }

    public static function getSlideTypeLabel(string $type): string
    {
        return match ($type) {
            self::SLIDE_QUESTION => 'Question (Choice)',
            self::SLIDE_QUESTION_TEXT => 'Question (Text Input)',
            self::SLIDE_INTERMISSION => 'Intermission',
            self::SLIDE_LOADING => 'Loading Screen',
            self::SLIDE_EMAIL_CAPTURE => 'Email Capture',
            self::SLIDE_PEPTIDE_REVEAL => 'Peptide Reveal',
            self::SLIDE_VENDOR_REVEAL => 'Vendor Reveal',
            self::SLIDE_BRIDGE => 'Bridge (CTA)',
            default => ucfirst($type),
        };
    }

    /*
     * Options structure:
     * [
     *   {
     *     "value": "weight_loss",
     *     "label": "Weight Loss",
     *     "klaviyo_value": "Weight Loss",
     *     "score_tof": 3,
     *     "score_mof": 1,
     *     "score_bof": 0,
     *     "tags": ["interested_weight_loss"]
     *   }
     * ]
     */
}
