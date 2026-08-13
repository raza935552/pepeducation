<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** Telegram change-request captured for the PP Claude Code processor loop. */
class DevRequest extends Model
{
    protected $fillable = [
        'source', 'external_id', 'chat_id', 'from_name', 'from_username',
        'message', 'risk', 'status', 'result', 'commit_sha', 'processed_at',
    ];

    protected $casts = ['processed_at' => 'datetime'];

    /** Sensitive areas the processor treats as high-risk (PP has no checkout). */
    public const PROTECTED_PATHS = ['database/migrations/', '.env', 'app/Http/Middleware/'];

    public function scopePending($q)
    {
        return $q->where('status', 'pending');
    }

    public static function guessRisk(string $message): string
    {
        $m = strtolower($message);
        foreach (['migration', '.env', 'redirect', 'middleware', 'delete', 'drop'] as $w) {
            if (str_contains($m, $w)) {
                return 'danger';
            }
        }
        return 'safe';
    }
}
