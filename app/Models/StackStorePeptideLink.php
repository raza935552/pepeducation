<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StackStorePeptideLink extends Model
{
    protected $fillable = [
        'stack_store_id',
        'peptide_name',
        'url',
        'outbound_link_id',
        'price',
        'is_in_stock',
        'order',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_in_stock' => 'boolean',
    ];

    public function store(): BelongsTo
    {
        return $this->belongsTo(StackStore::class, 'stack_store_id');
    }

    public function outboundLink(): BelongsTo
    {
        return $this->belongsTo(OutboundLink::class);
    }
}
