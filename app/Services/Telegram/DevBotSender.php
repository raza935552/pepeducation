<?php

namespace App\Services\Telegram;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/** Minimal Telegram sender for the PP dev-request pipeline bot. */
class DevBotSender
{
    public static function send(string $chatId, string $text): bool
    {
        $token = (string) config('services.telegram_intake.bot_token');
        if ($token === '' || $chatId === '') {
            return false;
        }
        try {
            Http::timeout(10)->asForm()->post("https://api.telegram.org/bot{$token}/sendMessage", [
                'chat_id' => $chatId, 'text' => $text,
                'parse_mode' => 'HTML', 'disable_web_page_preview' => true,
            ]);
            return true;
        } catch (\Throwable $e) {
            Log::warning('PP DevBotSender error', ['error' => $e->getMessage()]);
            return false;
        }
    }
}
