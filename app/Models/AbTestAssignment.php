<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AbTestAssignment extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'ab_test_id', 'session_id', 'user_id', 'subscriber_id',
        'variant', 'converted', 'converted_at', 'conversion_value', 'created_at',
    ];

    protected $casts = [
        'converted' => 'boolean',
        'converted_at' => 'datetime',
        'conversion_value' => 'decimal:2',
        'created_at' => 'datetime',
    ];

    // Relationships
    public function abTest(): BelongsTo
    {
        return $this->belongsTo(AbTest::class);
    }

    public function session(): BelongsTo
    {
        return $this->belongsTo(UserSession::class, 'session_id', 'session_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function subscriber(): BelongsTo
    {
        return $this->belongsTo(Subscriber::class);
    }

    // Scopes
    public function scopeConverted($query)
    {
        return $query->where('converted', true);
    }

    public function scopeVariant($query, string $variant)
    {
        return $query->where('variant', $variant);
    }

    // Methods
    public function markConverted(float $value = null): void
    {
        $this->update([
            'converted' => true,
            'converted_at' => now(),
            'conversion_value' => $value,
        ]);
    }
}
