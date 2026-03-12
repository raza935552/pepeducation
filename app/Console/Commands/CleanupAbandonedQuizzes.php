<?php

namespace App\Console\Commands;

use App\Models\QuizResponse;
use App\Services\Klaviyo\KlaviyoService;
use Illuminate\Console\Command;

class CleanupAbandonedQuizzes extends Command
{
    protected $signature = 'quiz:cleanup-abandoned {--hours=24 : Mark in-progress responses older than this many hours as abandoned}';
    protected $description = 'Mark stale in-progress quiz responses as abandoned and fire Klaviyo events';

    public function handle(KlaviyoService $klaviyo): int
    {
        $hours = (int) $this->option('hours');
        $cutoff = now()->subHours($hours);

        $responses = QuizResponse::where('status', 'in_progress')
            ->where('started_at', '<', $cutoff)
            ->with('subscriber', 'quiz')
            ->get();

        $marked = 0;
        $tracked = 0;

        foreach ($responses as $response) {
            $response->update([
                'status' => 'abandoned',
                'updated_at' => now(),
            ]);
            $marked++;

            // Fire Klaviyo "Quiz Abandoned" event if subscriber exists
            if ($response->subscriber && $klaviyo->isEnabled()) {
                try {
                    if ($klaviyo->trackQuizAbandoned($response->subscriber, $response)) {
                        $tracked++;
                    }
                } catch (\Exception $e) {
                    $this->warn("Klaviyo abandon event failed for response #{$response->id}: {$e->getMessage()}");
                }
            }
        }

        $this->info("Marked {$marked} stale quiz responses as abandoned (older than {$hours}h). Tracked {$tracked} to Klaviyo.");

        return self::SUCCESS;
    }
}
