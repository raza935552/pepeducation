<?php

namespace App\Services\Telegram;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/** Minimal Telegram sender for the PP dev-request pipeline bot. */
class DevBotSender
{
    /**
     * Send an HTML message. Returns the sent Telegram message_id on success
     * (truthy), or null on failure — the pipeline stores that id so a user REPLY
     * to a bot question threads back to the originating request.
     */
    public static function send(string $chatId, string $text): ?int
    {
        $token = (string) config('services.telegram_intake.bot_token');
        if ($token === '' || $chatId === '') {
            return null;
        }
        try {
            $resp = Http::timeout(10)->asForm()->post("https://api.telegram.org/bot{$token}/sendMessage", [
                'chat_id' => $chatId, 'text' => $text,
                'parse_mode' => 'HTML', 'disable_web_page_preview' => true,
            ]);
            $id = $resp->json('result.message_id');
            return $id ? (int) $id : null;
        } catch (\Throwable $e) {
            Log::warning('PP DevBotSender error', ['error' => $e->getMessage()]);
            return null;
        }
    }
}
