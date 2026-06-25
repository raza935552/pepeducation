<?php

namespace App\Services\Chat;

use Illuminate\Support\Facades\Cache;

/**
 * Tracks which admin agents currently have the Live Chat console open
 * (heartbeat in cache). Drives live vs offline mode for new chats.
 */
class ChatPresence
{
    private const KEY = 'livechat.online_agents';
    private const TTL = 90; // seconds

    public static function heartbeat(int $userId): void
    {
        $agents = Cache::get(self::KEY, []);
        $agents[$userId] = now()->timestamp;
        Cache::put(self::KEY, $agents, self::TTL);
    }

    public static function onlineCount(): int
    {
        $cutoff = now()->timestamp - self::TTL;
        $agents = array_filter(Cache::get(self::KEY, []), fn ($ts) => $ts >= $cutoff);
        return count($agents);
    }

    public static function anyOnline(): bool
    {
        return self::onlineCount() > 0;
    }
}
