<?php

namespace App\Console\Commands;

use App\Models\DevRequest;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DevRequestNext extends Command
{
    protected $signature = 'devrequests:next';
    protected $description = 'Claim the next pending dev request (JSON), or print NONE.';

    public function handle(): int
    {
        $req = DB::transaction(function () {
            $r = DevRequest::where('status', 'pending')->orderBy('id')->lockForUpdate()->first();
            if ($r) { $r->update(['status' => 'processing']); }
            return $r;
        });
        if (! $req) { $this->line('NONE'); return self::SUCCESS; }
        $this->line(json_encode([
            'id' => $req->id, 'risk' => $req->risk,
            'from' => $req->from_name ?: $req->from_username, 'message' => $req->message,
            'protected_paths' => DevRequest::PROTECTED_PATHS,
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
        return self::SUCCESS;
    }
}
