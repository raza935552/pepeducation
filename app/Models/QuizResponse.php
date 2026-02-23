<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuizResponse extends Model
{
    protected $fillable = [
        'quiz_id',
        'session_id',
        'user_id',
        'subscriber_id',
        'answers',
        'navigation_history',
        'score_tof',
        'score_mof',
        'score_bof',
        'total_score',
        'segment',
        'outcome_id',
        'outcome_name',
        'recommended_peptide_id',
        'klaviyo_properties',
        'tags',
        'email',
        'phone',
        'started_at',
        'completed_at',
        'duration_seconds',
        'questions_answered',
        'status',
        'synced_to_klaviyo',
        'utm_source',
        'utm_medium',
        'utm_campaign',
    ];

    protected $casts = [
        'answers' => 'array',
        'navigation_history' => 'array',
        'klaviyo_properties' => 'array',
        'tags' => 'array',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'synced_to_klaviyo' => 'boolean',
    ];

    // Relationships
    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class);
    }

    public function session(): BelongsTo
    {
        return $this->belongsTo(UserSession::class, 'session_id', 'session_id');
    }

    public function subscriber(): BelongsTo
    {
        return $this->belongsTo(Subscriber::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function outcome(): BelongsTo
    {
        return $this->belongsTo(QuizOutcome::class, 'outcome_id');
    }

    public function recommendedPeptide(): BelongsTo
    {
        return $this->belongsTo(Peptide::class, 'recommended_peptide_id');
    }

    // Scopes
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    public function scopeSegment($query, string $segment)
    {
        return $query->where('segment', strtolower($segment));
    }

    public function scopeNeedsSyncToKlaviyo($query)
    {
        return $query->completed()->where('synced_to_klaviyo', false);
    }

    // Methods
    public function markCompleted(): void
    {
        $this->update([
            'status' => 'completed',
            'completed_at' => now(),
            'duration_seconds' => now()->diffInSeconds($this->started_at),
        ]);
    }

    public function getSegmentLabel(): string
    {
        return match ($this->segment) {
            'tof' => 'Explorer',
            'mof' => 'Researcher',
            'bof' => 'Ready to Start',
            default => 'Unknown',
        };
    }

    public function buildKlaviyoProperties(): array
    {
        $properties = [];

        foreach ($this->answers as $answer) {
            $klaviyoProp = $answer['klaviyo_property'] ?? null;
            $klaviyoVal = $answer['klaviyo_value'] ?? null;

            if ($klaviyoProp && $klaviyoVal) {
                $properties[$klaviyoProp] = $klaviyoVal;
            }
        }

        $properties['pp_quiz_completed'] = true;
        $properties['pp_quiz_name'] = $this->quiz?->name;
        $properties['pp_segment'] = $this->segment;

        if (!empty($this->tags)) {
            $properties['pp_tags'] = $this->tags;
        }

        return $properties;
    }
}
