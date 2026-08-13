<?php

namespace App\Console\Commands;

use App\Models\DevRequest;
use Illuminate\Console\Command;

class DevRequestCount extends Command
{
    protected $signature = 'devrequests:count';
    protected $description = 'Print the number of pending dev requests.';

    public function handle(): int
    {
        $this->line((string) DevRequest::pending()->count());
        return self::SUCCESS;
    }
}
