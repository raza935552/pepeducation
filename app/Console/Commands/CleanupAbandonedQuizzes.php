<?php

namespace App\Console\Commands;

use App\Models\QuizResponse;
use Illuminate\Console\Command;

class CleanupAbandonedQuizzes extends Command
{
    protected $signature = 'quiz:cleanup-abandoned {--hours=24 : Mark in-progress responses older than this many hours as abandoned}';
    protected $description = 'Mark stale in-progress quiz responses as abandoned';

    public function handle(): int
    {
        $hours = (int) $this->option('hours');
        $cutoff = now()->subHours($hours);

        $count = QuizResponse::where('status', 'in_progress')
            ->where('started_at', '<', $cutoff)
            ->update([
                'status' => 'abandoned',
                'updated_at' => now(),
            ]);

        $this->info("Marked {$count} stale quiz responses as abandoned (older than {$hours}h).");

        return self::SUCCESS;
    }
}
