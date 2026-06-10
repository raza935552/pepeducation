<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * A Biolinx order attributed to a PP lander/campaign (mirrored via the
 * pp:push-conversions bridge). Powers the revenue + conversion-rate columns
 * in the Ad Analytics dashboard. Idempotent on biolinx_order_id.
 */
class LanderConversion extends Model
{
    protected $fillable = [
        'biolinx_order_id', 'pp_lander',
        'utm_source', 'utm_medium', 'utm_campaign', 'utm_content', 'utm_term',
        'fbclid', 'revenue', 'currency', 'order_type', 'status', 'ordered_at',
    ];

    protected $casts = [
        'revenue' => 'decimal:2',
        'ordered_at' => 'datetime',
    ];
}
