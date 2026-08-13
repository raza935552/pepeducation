<?php

namespace App\Http\Controllers\Webhooks;

use App\Http\Controllers\Controller;
use App\Models\DevRequest;
use App\Services\Telegram\DevBotSender;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/** PP Telegram intake for the dev-request pipeline (mirror of Biolinx). */
class TelegramIntakeController extends Controller
{
    public function handle(Request $request)
    {
        $secret = (string) config('services.telegram_intake.secret');
        $provided = (string) ($request->header('X-Telegram-Bot-Api-Secret-Token') ?: $request->query('key', ''));
        if ($secret === '' || ! hash_equals($secret, $provided)) {
            return response()->json(['ok' => false], 403);
        }

        $msg = $request->input('message') ?? $request->input('edited_message');
        $text = trim((string) ($msg['text'] ?? ''));
        $chatId = (string) ($msg['chat']['id'] ?? '');
        $messageId = (string) ($msg['message_id'] ?? '');
        if ($text === '' || $chatId === '' || $messageId === '') {
            return response()->json(['ok' => true]);
        }

        // Only explicit requests: leading "!" / "/do" or an @mention of the bot.
        $botUser = trim((string) config('services.telegram_intake.bot_username', 'ppsystemai_bot'));
        $t = ltrim($text);
        $isRequest = false;
        if (str_starts_with($t, '!')) {
            $text = ltrim(substr($t, 1)); $isRequest = true;
        } elseif (preg_match('/^\/do\b/i', $t)) {
            $text = trim(preg_replace('/^\/do\b/i', '', $t)); $isRequest = true;
        } elseif ($botUser !== '' && stripos($t, '@' . $botUser) !== false) {
            $text = trim(str_ireplace('@' . $botUser, '', $t)); $isRequest = true;
        }
        if (! $isRequest || $text === '') {
            return response()->json(['ok' => true]);
        }

        // Lock to the dev group; discovery mode until it's set.
        $allowed = trim((string) \App\Models\Setting::get('devpipeline.group_chat_id', ''));
        if ($allowed === '') {
            Log::info('PP intake discovery: unlocked chat', ['chat_id' => $chatId, 'title' => $msg['chat']['title'] ?? null]);
        } elseif ($chatId !== $allowed) {
            return response()->json(['ok' => true]);
        }

        try {
            $req = DevRequest::firstOrCreate(
                ['external_id' => $chatId . ':' . $messageId],
                [
                    'source' => 'telegram', 'chat_id' => $chatId,
                    'from_name' => trim(($msg['from']['first_name'] ?? '') . ' ' . ($msg['from']['last_name'] ?? '')) ?: null,
                    'from_username' => $msg['from']['username'] ?? null,
                    'message' => $text, 'risk' => DevRequest::guessRisk($text), 'status' => 'pending',
                ]
            );
            if ($req->wasRecentlyCreated) {
                $risk = $req->risk === 'danger' ? '🔴 sensitive — extra checks' : '🟢 safe';
                DevBotSender::send($chatId, "📥 <b>Request logged</b> (#{$req->id}, {$risk}). Queued for the agent.");
            }
        } catch (\Throwable $e) {
            Log::warning('PP intake failed', ['error' => $e->getMessage()]);
        }

        return response()->json(['ok' => true]);
    }
}
