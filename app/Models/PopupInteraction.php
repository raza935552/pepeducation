<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PopupInteraction extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'popup_id', 'session_id', 'subscriber_id',
        'interaction_type', 'dismiss_method', 'time_to_interaction', 'form_data',
        'created_at',
    ];

    protected $casts = [
        'form_data' => 'array',
        'created_at' => 'datetime',
    ];

    // Interaction Types
    public const TYPE_VIEW = 'view';
    public const TYPE_DISMISS = 'dismiss';
    public const TYPE_CONVERT = 'convert';

    // Dismiss Methods
    public const DISMISS_CLOSE_BUTTON = 'close_button';
    public const DISMISS_BACKDROP = 'backdrop';
    public const DISMISS_ESCAPE = 'escape';

    // Relationships
    public function popup(): BelongsTo
    {
        return $this->belongsTo(Popup::class);
    }

    public function session(): BelongsTo
    {
        return $this->belongsTo(UserSession::class, 'session_id', 'session_id');
    }

    public function subscriber(): BelongsTo
    {
        return $this->belongsTo(Subscriber::class);
    }

    // Scopes
    public function scopeViews($query)
    {
        return $query->where('interaction_type', self::TYPE_VIEW);
    }

    public function scopeConversions($query)
    {
        return $query->where('interaction_type', self::TYPE_CONVERT);
    }

    public function scopeDismissals($query)
    {
        return $query->where('interaction_type', self::TYPE_DISMISS);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }
}
