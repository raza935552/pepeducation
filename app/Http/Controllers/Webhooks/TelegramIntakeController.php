<?php

namespace App\Http\Controllers\Webhooks;

use App\Http\Controllers\Controller;
use App\Models\DevMessage;
use App\Models\DevRequest;
use App\Services\Telegram\DevBotSender;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

/**
 * PP Telegram intake for the dev-request pipeline (mirror of Biolinx).
 *
 * Every message is stored in the dev_messages transcript (conversation memory);
 * file attachments (text/code + images, NOT video) are downloaded via getFile; a
 * user REPLY to one of the bot's questions threads back to the original request.
 * Verified with TELEGRAM_INTAKE_SECRET; only the locked group is accepted.
 */
class TelegramIntakeController extends Controller
{
    /** Downloadable attachment types: text/code + images. Video/audio are rejected. */
    private const ALLOWED_EXT = [
        'html', 'htm', 'txt', 'md', 'css', 'js', 'json', 'csv', 'svg', 'xml', 'php',
        'jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp', 'ico', 'avif',
    ];

    public function handle(Request $request)
    {
        $secret = (string) config('services.telegram_intake.secret');
        $provided = (string) ($request->header('X-Telegram-Bot-Api-Secret-Token') ?: $request->query('key', ''));
        if ($secret === '' || ! hash_equals($secret, $provided)) {
            return response()->json(['ok' => false], 403);
        }

        $msg = $request->input('message') ?? $request->input('edited_message');
        if (! is_array($msg)) {
            return response()->json(['ok' => true]);
        }

        $text = trim((string) ($msg['text'] ?? $msg['caption'] ?? ''));
        $chatId = (string) ($msg['chat']['id'] ?? '');
        $messageId = (string) ($msg['message_id'] ?? '');
        $replyTo = isset($msg['reply_to_message']['message_id']) ? (int) $msg['reply_to_message']['message_id'] : null;

        [$fileId, $fileName] = $this->extractAttachment($msg);

        if ($chatId === '' || $messageId === '' || ($text === '' && $fileId === null)) {
            return response()->json(['ok' => true]);
        }
        if (! empty($msg['from']['is_bot'])) {
            return response()->json(['ok' => true]);
        }

        $allowed = trim((string) \App\Models\Setting::getValue('devpipeline', 'group_chat_id', ''));
        $inDevGroup = ($allowed !== '' && $chatId === $allowed);

        // Trigger stripping: "!", "/do", "@botusername", or a natural "ai" mention
        // ("@ai", "hey ai", "ai:", "ai,"). A bare "ai " + word is NOT stripped.
        $botUser = trim((string) config('services.telegram_intake.bot_username', 'ppsystemai_bot'));
        $aiPattern = '/^(?:@ai\b|(?:hey|ok|yo)\s+ai\b|ai\s*[:,>_-]+)\s*/i';
        $t = ltrim($text);
        $hadTrigger = false;
        if (str_starts_with($t, '!')) {
            $text = ltrim(substr($t, 1)); $hadTrigger = true;
        } elseif (preg_match('/^\/do\b/i', $t)) {
            $text = trim(preg_replace('/^\/do\b/i', '', $t)); $hadTrigger = true;
        } elseif ($botUser !== '' && stripos($t, '@' . $botUser) !== false) {
            $text = trim(str_ireplace('@' . $botUser, '', $t)); $hadTrigger = true;
        } elseif (preg_match($aiPattern, $t)) {
            $text = trim(preg_replace($aiPattern, '', $t)); $hadTrigger = true;
        }

        if ($inDevGroup) {
            $skipAsCommand = (! $hadTrigger && str_starts_with($t, '/'));
        } else {
            if ($allowed === '') {
                Log::warning('PP intake discovery: unlocked chat', ['chat_id' => $chatId, 'title' => $msg['chat']['title'] ?? null]);
            }
            $skipAsCommand = ! $hadTrigger;
        }

        // Download the attachment (if any).
        $localPath = null;
        $attachNote = null;
        if ($fileId !== null) {
            [$localPath, $attachNote] = $this->storeAttachment($fileId, $fileName, $chatId, $messageId);
            if ($attachNote !== null) {
                $text = trim($text . "\n\n" . $attachNote);
            }
        }

        $fromName = trim(($msg['from']['first_name'] ?? '') . ' ' . ($msg['from']['last_name'] ?? '')) ?: null;
        $fromUser = $msg['from']['username'] ?? null;

        // Conversation memory: record EVERY message.
        try {
            $transcript = DevMessage::create([
                'chat_id' => $chatId,
                'message_id' => (int) $messageId,
                'from_name' => $fromName,
                'from_username' => $fromUser,
                'is_bot' => false,
                'reply_to_message_id' => $replyTo,
                'text' => $text !== '' ? $text : null,
                'file_name' => $localPath ? $fileName : null,
                'file_local_path' => $localPath,
                'file_mime' => $localPath ? ($msg['document']['mime_type'] ?? null) : null,
            ]);
        } catch (\Throwable $e) {
            $transcript = null;
            Log::warning('PP intake transcript failed', ['error' => $e->getMessage()]);
        }

        // Reply-threading: an answer to one of the bot's questions.
        if ($replyTo !== null) {
            $parent = DevRequest::where('chat_id', $chatId)
                ->where('last_bot_message_id', $replyTo)
                ->latest('id')->first();
            if ($parent) {
                $answer = trim("[reply from " . ($fromUser ? '@' . $fromUser : ($fromName ?: 'user')) . "]: " . $text);
                $parent->message = trim($parent->message . "\n\n" . $answer);
                $parent->status = 'pending';
                $parent->save();
                if ($transcript) { $transcript->update(['dev_request_id' => $parent->id]); }
                DevBotSender::send($chatId, "#{$parent->id}: 👍 Got your reply, resuming.");
                return response()->json(['ok' => true]);
            }
        }

        if ($skipAsCommand || ($text === '' && $localPath === null)) {
            return response()->json(['ok' => true]);
        }

        try {
            $req = DevRequest::firstOrCreate(
                ['external_id' => $chatId . ':' . $messageId],
                [
                    'source' => 'telegram', 'chat_id' => $chatId,
                    'from_name' => $fromName, 'from_username' => $fromUser,
                    'message' => $text, 'risk' => DevRequest::guessRisk($text),
                    'status' => 'pending', 'attachment_path' => $localPath,
                ]
            );
            if ($transcript && $req->wasRecentlyCreated) {
                $transcript->update(['dev_request_id' => $req->id]);
            }
            if ($req->wasRecentlyCreated) {
                $risk = $req->risk === 'danger' ? '🔴 sensitive — extra checks' : '🟢 safe';
                $extra = $localPath ? ' 📎 file attached' : '';
                DevBotSender::send($chatId, "📥 <b>Request logged</b> (#{$req->id}, {$risk}{$extra}). Queued for the agent.");
            }
        } catch (\Throwable $e) {
            Log::warning('PP intake failed', ['error' => $e->getMessage()]);
        }

        return response()->json(['ok' => true]);
    }

    /** Pull a document or the largest photo out of the update. [file_id, file_name] or [null, null]. */
    private function extractAttachment(array $msg): array
    {
        if (is_array($msg['document'] ?? null) && ! empty($msg['document']['file_id'])) {
            $name = trim((string) ($msg['document']['file_name'] ?? '')) ?: ('file_' . ($msg['message_id'] ?? '0') . '.txt');
            return [(string) $msg['document']['file_id'], $name];
        }
        if (is_array($msg['photo'] ?? null) && count($msg['photo'])) {
            $largest = end($msg['photo']);
            if (! empty($largest['file_id'])) {
                return [(string) $largest['file_id'], 'photo_' . ($msg['message_id'] ?? '0') . '.jpg'];
            }
        }
        return [null, null];
    }

    /** Download a Telegram file (text or image) to storage/app/devrequests. Returns [absPath|null, note|null]. */
    private function storeAttachment(string $fileId, ?string $name, string $chatId, string $messageId): array
    {
        $name = trim((string) $name) ?: 'file.txt';
        $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
        if (! in_array($ext, self::ALLOWED_EXT, true)) {
            return [null, "[Attachment '{$name}' was NOT saved — only text/code and image files are supported (no video/audio)]"];
        }

        $token = (string) config('services.telegram_intake.bot_token');
        if ($token === '') {
            return [null, null];
        }

        try {
            $info = Http::timeout(15)->get("https://api.telegram.org/bot{$token}/getFile", ['file_id' => $fileId])->json();
            $filePath = (string) ($info['result']['file_path'] ?? '');
            $size = (int) ($info['result']['file_size'] ?? 0);
            if ($filePath === '') {
                return [null, "[Attachment '{$name}' could not be fetched from Telegram]"];
            }
            if ($size > 15 * 1024 * 1024) {
                return [null, "[Attachment '{$name}' was NOT saved — files must be under 15 MB]"];
            }
            $content = Http::timeout(45)->get("https://api.telegram.org/file/bot{$token}/{$filePath}")->body();
            $safeName = preg_replace('/[^A-Za-z0-9._-]/', '_', $name);
            $rel = 'devrequests/' . preg_replace('/[^0-9-]/', '', $chatId) . '_' . $messageId . '_' . $safeName;
            Storage::disk('local')->put($rel, $content);
            $abs = Storage::disk('local')->path($rel);

            return [$abs, "[Attached file '{$name}' saved to: {$abs} — read/use this file for the request]"];
        } catch (\Throwable $e) {
            Log::warning('PP intake attachment fetch failed', ['error' => $e->getMessage()]);
            return [null, "[Attachment '{$name}' could not be downloaded]"];
        }
    }
}
