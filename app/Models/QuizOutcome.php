<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuizOutcome extends Model
{
    protected $fillable = [
        'quiz_id',
        'name',
        'conditions',
        'redirect_url',
        'redirect_type',
        'result_title',
        'result_message',
        'result_image',
        'recommended_peptide_id',
        'product_link',
        'klaviyo_event',
        'klaviyo_list_id',
        'klaviyo_properties',
        'priority',
        'is_active',
    ];

    protected $casts = [
        'conditions' => 'array',
        'klaviyo_properties' => 'array',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class);
    }

    public function recommendedPeptide(): BelongsTo
    {
        return $this->belongsTo(Peptide::class, 'recommended_peptide_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForSegment($query, string $segment)
    {
        return $query->whereJsonContains('conditions->segment', strtolower($segment));
    }

    // Methods
    public function matchesSegment(string $segment): bool
    {
        $conditions = $this->conditions ?? [];

        if (($conditions['type'] ?? '') !== 'segment') {
            return false;
        }

        return strtolower($conditions['segment'] ?? '') === strtolower($segment);
    }

    public function matchesScore(int $score, string $scoreType = 'total'): bool
    {
        $conditions = $this->conditions ?? [];

        if (($conditions['type'] ?? '') !== 'score') {
            return false;
        }

        $minScore = $conditions['min_score'] ?? 0;
        $checkType = $conditions['score_type'] ?? 'total';

        return $checkType === $scoreType && $score >= $minScore;
    }

    public function matchesAnswer(array $answers): bool
    {
        $conditions = $this->conditions ?? [];

        if (($conditions['type'] ?? '') !== 'answer') {
            return false;
        }

        $targetProperty = $conditions['question'] ?? null;
        $targetValue = $conditions['value'] ?? null;

        if (!$targetProperty || !$targetValue) {
            return false;
        }

        foreach ($answers as $answer) {
            if (($answer['klaviyo_property'] ?? '') === $targetProperty &&
                ($answer['klaviyo_value'] ?? '') === $targetValue) {
                return true;
            }
        }

        return false;
    }
}
