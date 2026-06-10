<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * One row per lander page-load (recorded after the response, never blocks the
 * render). `is_ad` = the load carried a Meta click id or ad UTM, i.e. paid ad
 * traffic. Powers the admin Ad Analytics dashboard (visits + CTR per lander /
 * campaign / ad). CTA click-throughs live in `outbound_clicks`.
 */
class LanderVisit extends Model
{
    public $timestamps = false; // only created_at, written explicitly

    protected $fillable = [
        'lander_slug', 'session_id', 'is_ad', 'fbclid',
        'utm_source', 'utm_medium', 'utm_campaign', 'utm_content', 'utm_term',
        'referer', 'ip', 'user_agent', 'created_at',
    ];

    protected $casts = [
        'is_ad' => 'boolean',
        'created_at' => 'datetime',
    ];
}
