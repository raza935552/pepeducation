<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * First-touch visitor entry — the exact link each visitor arrived on, + referrer
 * + ad source. One row per session (first request). Populated by LogVisitorEntry.
 */
class VisitorEntry extends Model
{
    public $timestamps = false; // only created_at, written explicitly

    protected $fillable = [
        'session_id', 'landing_url', 'path', 'referrer', 'referrer_domain',
        'is_ad', 'utm_source', 'utm_medium', 'utm_campaign', 'utm_content', 'utm_term',
        'fbclid', 'device', 'ip', 'user_agent', 'created_at',
    ];

    protected $casts = [
        'is_ad' => 'boolean',
        'created_at' => 'datetime',
    ];
}
