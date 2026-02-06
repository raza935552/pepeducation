<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SavedSection extends Model
{
    protected $fillable = [
        'name', 'content', 'category', 'created_by',
    ];

    protected $casts = [
        'content' => 'array',
    ];

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
