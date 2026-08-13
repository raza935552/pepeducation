<?php

namespace App\Console\Commands;

use App\Models\DevRequest;
use App\Services\Telegram\DevBotSender;
use Illuminate\Console\Command;

class DevRequestReply extends Command
{
    protected $signature = 'devrequests:reply {id} {text?} {--status=} {--commit=}';
    protected $description = 'Post a Telegram update for a dev request and/or set its status.';

    public function handle(): int
    {
        $req = DevRequest::find((int) $this->argument('id'));
        if (! $req) { $this->error('not found'); return self::FAILURE; }
        $text = (string) $this->argument('text');
        if ($text !== '') { DevBotSender::send($req->chat_id ?: '', "#{$req->id}: {$text}"); }
        $update = [];
        if ($status = $this->option('status')) {
            $update['status'] = $status;
            if (in_array($status, ['done', 'skipped', 'failed', 'blocked'], true)) { $update['processed_at'] = now(); }
        }
        if ($commit = $this->option('commit')) { $update['commit_sha'] = $commit; }
        if ($text !== '') { $update['result'] = $text; }
        if ($update) { $req->update($update); }
        $this->info("ok (#{$req->id})");
        return self::SUCCESS;
    }
}
