<?php

namespace App\Console\Commands;

use App\Models\DevMessage;
use App\Models\DevRequest;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Claim the oldest pending dev_request (JSON), or print NONE. Carries conversation
 * memory: the recent group transcript (messages + uploaded files), other pending
 * requests in the chat (merge candidates), and the request's attachment_path.
 */
class DevRequestNext extends Command
{
    protected $signature = 'devrequests:next';
    protected $description = 'Claim the next pending dev request (JSON, with transcript), or print NONE.';

    public function handle(): int
    {
        $req = DB::transaction(function () {
            $r = DevRequest::where('status', 'pending')->orderBy('id')->lockForUpdate()->first();
            if ($r) { $r->update(['status' => 'processing']); }
            return $r;
        });
        if (! $req) { $this->line('NONE'); return self::SUCCESS; }

        $transcript = DevMessage::where('chat_id', $req->chat_id)
            ->orderBy('id', 'desc')->limit(30)->get()->reverse()->values()
            ->map(function (DevMessage $m) {
                $who = $m->is_bot ? 'bot' : ('@' . ($m->from_username ?: ($m->from_name ?: 'user')));
                $row = ['who' => $who, 'text' => $m->text];
                if ($m->file_local_path) { $row['file'] = $m->file_local_path; $row['file_name'] = $m->file_name; }
                if ($m->reply_to_message_id) { $row['reply_to_message_id'] = $m->reply_to_message_id; }
                return $row;
            });

        $related = DevRequest::where('chat_id', $req->chat_id)
            ->where('status', 'pending')->where('id', '!=', $req->id)
            ->orderBy('id')->limit(10)->get(['id', 'message'])
            ->map(fn ($r) => ['id' => $r->id, 'message' => Str::limit((string) $r->message, 200)]);

        $this->line(json_encode([
            'id' => $req->id, 'risk' => $req->risk,
            'from' => $req->from_name ?: $req->from_username, 'message' => $req->message,
            'attachment_path' => $req->attachment_path,
            'transcript' => $transcript,
            'related_pending' => $related,
            'protected_paths' => DevRequest::PROTECTED_PATHS,
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
        return self::SUCCESS;
    }
}
