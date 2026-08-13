<?php

namespace App\Http\Controllers\Webhooks;

use App\Http\Controllers\Controller;
use App\Models\DevRequest;
use App\Services\Telegram\DevBotSender;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

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
        // Documents (file attachments) carry their text in 'caption', not 'text'.
        $text = trim((string) ($msg['text'] ?? $msg['caption'] ?? ''));
        $doc = is_array($msg['document'] ?? null) ? $msg['document'] : null;
        $chatId = (string) ($msg['chat']['id'] ?? '');
        $messageId = (string) ($msg['message_id'] ?? '');
        if ($chatId === '' || $messageId === '' || ($text === '' && $doc === null)) {
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

        if ($text === '' && $doc === null) {
            return response()->json(['ok' => true]);
        }

        // Save a text-type attachment to disk so the processor can read it later.
        if ($doc !== null) {
            $note = $this->storeAttachment($doc, $chatId, $messageId);
            if ($note === null && $text === '') {
                return response()->json(['ok' => true]);
            }
            if ($note !== null) {
                $text = trim($text . "\n\n" . $note);
            }
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

    /**
     * Download a Telegram document and save it under storage/app/devrequests.
     * Returns a note (with the saved path) to append to the request message,
     * or null when the update should be ignored entirely.
     */
    private function storeAttachment(array $doc, string $chatId, string $messageId): ?string
    {
        $name = trim((string) ($doc['file_name'] ?? '')) ?: 'file.txt';
        $size = (int) ($doc['file_size'] ?? 0);
        $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
        $allowed = ['html', 'htm', 'txt', 'md', 'css', 'js', 'json', 'csv', 'svg', 'xml', 'php'];
        if (! in_array($ext, $allowed, true)) {
            return "[Attachment '{$name}' was NOT saved - only text files (.html, .txt, .css, .js, ...) are supported]";
        }
        if ($size <= 0 || $size > 5 * 1024 * 1024) {
            return "[Attachment '{$name}' was NOT saved - files must be under 5 MB]";
        }

        $token = (string) config('services.telegram_intake.bot_token');
        if ($token === '') {
            return null;
        }

        try {
            $info = Http::timeout(15)
                ->get("https://api.telegram.org/bot{$token}/getFile", ['file_id' => (string) ($doc['file_id'] ?? '')])
                ->json();
            $filePath = (string) ($info['result']['file_path'] ?? '');
            if ($filePath === '') {
                return "[Attachment '{$name}' could not be fetched from Telegram]";
            }
            $content = Http::timeout(30)->get("https://api.telegram.org/file/bot{$token}/{$filePath}")->body();

            $safeName = preg_replace('/[^A-Za-z0-9._-]/', '_', $name);
            $rel = 'devrequests/' . preg_replace('/[^0-9-]/', '', $chatId) . '_' . $messageId . '_' . $safeName;
            Storage::disk('local')->put($rel, $content);

            return "[Attached file '{$name}' saved to: " . Storage::disk('local')->path($rel) . ']';
        } catch (\Throwable $e) {
            Log::warning('PP intake attachment fetch failed', ['error' => $e->getMessage()]);

            return "[Attachment '{$name}' could not be downloaded]";
        }
    }
}
