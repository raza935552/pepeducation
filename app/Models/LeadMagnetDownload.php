<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeadMagnetDownload extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'lead_magnet_id', 'session_id', 'subscriber_id', 'user_id',
        'source_page', 'source_popup', 'utm_source', 'utm_campaign',
        'delivery_method', 'email_sent', 'downloaded', 'downloaded_at',
        'synced_to_klaviyo', 'created_at',
    ];

    protected $casts = [
        'email_sent' => 'boolean',
        'downloaded' => 'boolean',
        'downloaded_at' => 'datetime',
        'synced_to_klaviyo' => 'boolean',
        'created_at' => 'datetime',
    ];

    // Relationships
    public function leadMagnet(): BelongsTo
    {
        return $this->belongsTo(LeadMagnet::class);
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

    // Scopes
    public function scopeDownloaded($query)
    {
        return $query->where('downloaded', true);
    }

    public function scopeNeedsSyncToKlaviyo($query)
    {
        return $query->where('synced_to_klaviyo', false);
    }

    public function scopeFromPopup($query, string $popupSlug)
    {
        return $query->where('source_popup', $popupSlug);
    }

    // Methods
    public function markDownloaded(): void
    {
        $this->update([
            'downloaded' => true,
            'downloaded_at' => now(),
        ]);

        $this->leadMagnet?->increment('downloads_count');
    }

    public function markEmailSent(): void
    {
        $this->update(['email_sent' => true]);
    }
}
