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

        // Never treat a bot's own messages as requests (avoids reply loops).
        if (! empty($msg['from']['is_bot'])) {
            return response()->json(['ok' => true]);
        }

        // Resolve the locked dev group first — behaviour differs inside vs. outside it.
        $allowed = trim((string) \App\Models\Setting::getValue('devpipeline', 'group_chat_id', ''));
        $inDevGroup = ($allowed !== '' && $chatId === $allowed);

        // Strip an optional trigger ("!" / "/do" / @mention); remember if one was present.
        $botUser = trim((string) config('services.telegram_intake.bot_username', 'ppsystemai_bot'));
        $t = ltrim($text);
        $hadTrigger = false;
        if (str_starts_with($t, '!')) {
            $text = ltrim(substr($t, 1)); $hadTrigger = true;
        } elseif (preg_match('/^\/do\b/i', $t)) {
            $text = trim(preg_replace('/^\/do\b/i', '', $t)); $hadTrigger = true;
        } elseif ($botUser !== '' && stripos($t, '@' . $botUser) !== false) {
            $text = trim(str_ireplace('@' . $botUser, '', $t)); $hadTrigger = true;
        }

        // Inside the dedicated dev group every human message is a request (no trigger
        // needed); we only skip slash commands other than "/do". Outside it, a trigger
        // is required, and while unlocked we log each chat id for discovery.
        if ($inDevGroup) {
            if (! $hadTrigger && str_starts_with($t, '/')) {
                return response()->json(['ok' => true]);
            }
        } else {
            if ($allowed === '') {
                Log::warning('PP intake discovery: unlocked chat', ['chat_id' => $chatId, 'title' => $msg['chat']['title'] ?? null]);
            }
            if (! $hadTrigger) {
                return response()->json(['ok' => true]);
            }
        }

        if ($text === '') {
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
