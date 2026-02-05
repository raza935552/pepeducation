<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserPreference extends Model
{
    protected $fillable = [
        'user_id',
        'notify_edit_status',
        'notify_marketing',
        'notify_weekly_digest',
        'data_usage_opt_in',
    ];

    protected function casts(): array
    {
        return [
            'notify_edit_status' => 'boolean',
            'notify_marketing' => 'boolean',
            'notify_weekly_digest' => 'boolean',
            'data_usage_opt_in' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
