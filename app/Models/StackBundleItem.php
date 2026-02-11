<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StackBundleItem extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'stack_bundle_id', 'stack_product_id', 'quantity', 'order',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'order' => 'integer',
    ];

    public function bundle(): BelongsTo
    {
        return $this->belongsTo(StackBundle::class, 'stack_bundle_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(StackProduct::class, 'stack_product_id');
    }
}
