<?php

namespace App\Console\Commands;

use App\Models\DevMessage;
use App\Models\DevRequest;
use App\Services\Telegram\DevBotSender;
use Illuminate\Console\Command;

/**
 * Post a Telegram update for a dev request and/or set its status.
 *   ...reply 12 "✅ Done. Deployed." --status=done --commit=abc1234
 *   ...reply 12 "Which page?" --status=awaiting        (user replies to thread back)
 *   ...reply 12 "✅ Done (covers #13,#14)." --status=done --merge=13,14
 * Status: pending|processing|awaiting(_reply)|done|skipped|failed|blocked|merged.
 */
class DevRequestReply extends Command
{
    protected $signature = 'devrequests:reply {id} {text?} {--status=} {--commit=} {--merge=}';
    protected $description = 'Post a Telegram update for a dev request and/or set its status.';

    public function handle(): int
    {
        $req = DevRequest::find((int) $this->argument('id'));
        if (! $req) { $this->error('not found'); return self::FAILURE; }

        $status = $this->option('status');
        if ($status === 'awaiting') { $status = 'awaiting_reply'; }

        $text = (string) $this->argument('text');
        $botMessageId = null;
        if ($text !== '') {
            $botMessageId = DevBotSender::send($req->chat_id ?: '', "#{$req->id}: {$text}");
            try {
                DevMessage::create([
                    'chat_id' => $req->chat_id, 'message_id' => $botMessageId ?: 0,
                    'dev_request_id' => $req->id,
                    'from_username' => config('services.telegram_intake.bot_username'),
                    'is_bot' => true, 'text' => $text,
                ]);
            } catch (\Throwable $e) { /* transcript best-effort */ }
        }

        $update = [];
        if ($status) {
            $update['status'] = $status;
            if (in_array($status, ['done', 'skipped', 'failed', 'blocked', 'merged'], true)) { $update['processed_at'] = now(); }
        }
        if ($commit = $this->option('commit')) { $update['commit_sha'] = $commit; }
        if ($text !== '') { $update['result'] = $text; }
        if ($botMessageId && $status === 'awaiting_reply') { $update['last_bot_message_id'] = $botMessageId; }
        if ($update) { $req->update($update); }

        if ($merge = $this->option('merge')) {
            $ids = array_filter(array_map('intval', explode(',', $merge)));
            if ($ids) {
                DevRequest::whereIn('id', $ids)->where('id', '!=', $req->id)
                    ->update(['status' => 'merged', 'processed_at' => now(), 'result' => "merged into #{$req->id}"]);
                $this->info('merged: ' . implode(',', $ids));
            }
        }

        $this->info("ok (#{$req->id}" . ($status ? " → {$status}" : '') . ')');
        return self::SUCCESS;
    }
}
