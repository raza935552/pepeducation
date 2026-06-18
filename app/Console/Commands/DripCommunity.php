<?php

namespace App\Console\Commands;

use App\Models\ForumPost;
use App\Models\ForumThread;
use App\Models\Setting;
use App\Models\User;
use App\Services\ForumContentSanitizer;
use Illuminate\Console\Command;

class DripCommunity extends Command
{
    protected $signature = 'community:drip {--count=1 : How many seed replies to post this run} {--force : Ignore the drip_enabled setting}';

    protected $description = 'Post a small number of seed replies to keep the community looking active (gated by a setting)';

    /** Generic, broadly-applicable, COMPLIANT filler lines (discussion, never endorsement). */
    private const LINES = [
        'Following this — useful thread.',
        'Good point. Worth adding that a lot of this is still preclinical, so I hold it loosely.',
        'This matches what I\'ve read. The mechanism angle is the interesting part for me.',
        'Appreciate the measured take. So much of what\'s online overstates things.',
        'Saving this one. The reconstitution calculator on the main site has saved me from a couple of silly mistakes.',
        'Has anyone else found the Peptides 101 guide a decent baseline for this?',
        'Agreed — "preclinical signal" and "proven in humans" are very different claims.',
        'Solid summary. The signaling-vs-building-block distinction keeps coming up for a reason.',
        'Coming back to this after re-reading the linked article — makes more sense now.',
        'The honesty about uncertainty in this thread is why I keep coming back here.',
        'Late to this but wanted to say it\'s one of the clearer explanations I\'ve seen.',
        'Bookmarking. The "follow the study type" advice is underrated.',
        'This lines up with the blog write-up on the topic. Worth a read for anyone newer.',
        'Good reminder that tolerability is individual — what the trials show isn\'t a personal guarantee.',
        'The storage/handling point deserves its own thread honestly. Easy to overlook.',
        'Helpful framing. I\'d been conflating a couple of these mechanisms.',
        'Thanks for keeping it grounded — no hype, just the research.',
        'Came here from the calculators page and this thread answered my question.',
        'Adding a +1 for label-everything-when-you-reconstitute. Future-you forgets.',
        'Really clear. The key/lock analogy for receptors finally stuck for me here.',
        'Worth re-stating for newcomers: this is educational discussion, not medical advice.',
        'The protein + resistance-training point is the one I wish I\'d understood earlier.',
        'Good thread. The "what isn\'t known yet" parts are the most useful to me.',
        'Re-reading this with fresh eyes — the mechanism distinction is doing all the work.',
        'Appreciate everyone keeping sources and study types front and centre.',
        'This is the kind of careful discussion that\'s hard to find elsewhere. Thanks all.',
    ];

    public function handle(ForumContentSanitizer $sanitizer): int
    {
        if (! $this->option('force') && ! Setting::getValue('community', 'drip_enabled', false)) {
            return self::SUCCESS; // silently off
        }

        $personas = User::where('is_seed', true)->pluck('id', 'id');
        if ($personas->isEmpty()) {
            $this->warn('No seed personas — run community:seed first.');
            return self::SUCCESS;
        }

        $posted = 0;
        $count = max(1, (int) $this->option('count'));

        for ($i = 0; $i < $count; $i++) {
            $thread = ForumThread::published()
                ->where('is_locked', false)
                ->where('created_at', '>=', now()->subDays(90))
                ->inRandomOrder()
                ->first();

            if (! $thread) {
                break;
            }

            // Pick a seed persona who isn't the last poster (avoid double-posting).
            $authorId = $personas->reject(fn ($id) => $id === $thread->last_post_user_id)->random();
            $line = self::LINES[array_rand(self::LINES)];

            $post = ForumPost::create([
                'thread_id' => $thread->id,
                'user_id' => $authorId,
                'body' => $sanitizer->sanitize($line),
                'status' => 'published',
            ]);

            $thread->increment('replies_count');
            $thread->update(['last_activity_at' => now(), 'last_post_user_id' => $authorId]);
            $thread->category()->increment('posts_count');
            User::whereKey($authorId)->increment('forum_posts_count');

            $posted++;
        }

        $this->info("Drip posted {$posted} seed repl" . ($posted === 1 ? 'y' : 'ies') . '.');

        return self::SUCCESS;
    }
}
