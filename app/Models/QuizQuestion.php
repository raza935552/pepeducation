<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuizQuestion extends Model
{
    protected $fillable = [
        'quiz_id', 'question_text', 'question_type', 'order',
        'options', 'klaviyo_property', 'is_required', 'settings',
    ];

    protected $casts = [
        'options' => 'array',
        'settings' => 'array',
        'is_required' => 'boolean',
    ];

    // Question Types (must match database enum values)
    public const TYPE_SINGLE = 'single';
    public const TYPE_MULTIPLE = 'multiple';
    public const TYPE_TEXT = 'text';
    public const TYPE_EMAIL = 'email';
    public const TYPE_SCALE = 'scale';

    // Relationships
    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class);
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
