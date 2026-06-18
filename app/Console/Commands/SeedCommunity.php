<?php

namespace App\Console\Commands;

use App\Models\ForumCategory;
use App\Models\ForumPost;
use App\Models\ForumReaction;
use App\Models\ForumThread;
use App\Models\User;
use App\Services\ForumContentSanitizer;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SeedCommunity extends Command
{
    protected $signature = 'community:seed {--fresh : Delete existing seed personas + their content first}';

    protected $description = 'Seed the community with personas and curated, compliant discussions';

    public function handle(ForumContentSanitizer $sanitizer): int
    {
        $data = require database_path('seeders/data/community_seed.php');

        if ($this->option('fresh')) {
            $this->warn('Removing existing seed data…');
            // Seed threads/posts cascade-delete with their authors.
            User::where('is_seed', true)->get()->each(function (User $u) {
                $u->forumThreads()->forceDelete();
                $u->forumPosts()->forceDelete();
                $u->forceDelete();
            });
        }

        // 1. Categories
        foreach ($data['categories'] as $c) {
            ForumCategory::updateOrCreate(['slug' => $c['slug']], $c + ['is_active' => true]);
        }
        $categories = ForumCategory::pluck('id', 'slug');

        // 2. Personas
        $personas = [];
        foreach ($data['personas'] as $p) {
            $personas[$p['slug']] = User::updateOrCreate(
                ['slug' => $p['slug']],
                [
                    'name' => $p['name'],
                    'email' => $p['slug'] . '@seed.professorpeptides.co',
                    'password' => Hash::make(Str::random(32)),
                    'role' => 'user',
                    'bio' => $p['bio'],
                    'is_seed' => true,
                    'email_verified_at' => now()->subDays($p['joined_days']),
                    'created_at' => now()->subDays($p['joined_days']),
                    'last_seen_at' => now()->subDays(rand(0, 5)),
                ]
            );
        }
        $this->info(count($personas) . ' personas ready.');

        // 3. Threads + replies
        $createdThreads = 0;
        $createdReplies = 0;

        foreach ($data['threads'] as $t) {
            if (ForumThread::where('title', $t['title'])->exists()) {
                continue; // idempotent
            }

            $author = $personas[$t['author']] ?? null;
            $categoryId = $categories[$t['category']] ?? null;
            if (! $author || ! $categoryId) {
                continue;
            }

            $createdAt = now()->subDays($t['created_days']);

            $thread = new ForumThread([
                'category_id' => $categoryId,
                'user_id' => $author->id,
                'title' => $t['title'],
                'slug' => ForumThread::generateUniqueSlug($t['title']),
                'body' => $sanitizer->sanitize($t['body']),
                'is_pinned' => $t['pinned'] ?? false,
                'status' => 'published',
                'views_count' => rand(40, 900),
            ]);
            $thread->created_at = $createdAt;
            $thread->updated_at = $createdAt;
            $thread->last_post_user_id = $author->id;
            $thread->last_activity_at = $createdAt;
            $thread->save();
            $createdThreads++;

            $lastActivity = $createdAt->copy();
            foreach ($t['replies'] as $r) {
                $rAuthor = $personas[$r['author']] ?? null;
                if (! $rAuthor) {
                    continue;
                }
                $replyAt = $createdAt->copy()->addHours($r['after_h']);

                $post = new ForumPost([
                    'thread_id' => $thread->id,
                    'user_id' => $rAuthor->id,
                    'body' => $sanitizer->sanitize($r['body']),
                    'status' => 'published',
                ]);
                $post->created_at = $replyAt;
                $post->updated_at = $replyAt;
                $post->save();
                $createdReplies++;

                $rAuthor->increment('forum_posts_count');
                $this->scatterLikes($post, $personas, rand(0, 5));

                if ($replyAt->greaterThan($lastActivity)) {
                    $lastActivity = $replyAt->copy();
                    $thread->last_post_user_id = $rAuthor->id;
                }
            }

            $thread->replies_count = count($t['replies']);
            $thread->last_activity_at = $lastActivity;
            $thread->save();

            $this->scatterLikes($thread, $personas, rand(1, 9));
        }

        // 4. Recompute cached counters on categories
        foreach (ForumCategory::all() as $cat) {
            $cat->update([
                'threads_count' => $cat->threads()->count(),
                'posts_count' => ForumPost::whereIn('thread_id', $cat->threads()->pluck('id'))->count(),
            ]);
        }

        $this->info("Seeded {$createdThreads} threads and {$createdReplies} replies.");

        return self::SUCCESS;
    }

    /**
     * Add N likes from distinct random personas to a thread or post.
     */
    private function scatterLikes($model, array $personas, int $n): void
    {
        if ($n <= 0) {
            return;
        }

        $likers = collect($personas)->random(min($n, count($personas)));
        $count = 0;
        foreach ($likers as $liker) {
            $exists = $model->reactions()->where('user_id', $liker->id)->exists();
            if ($exists) {
                continue;
            }
            $model->reactions()->create([
                'user_id' => $liker->id,
                'type' => 'like',
                'created_at' => $model->created_at,
                'updated_at' => $model->created_at,
            ]);
            $count++;
        }
        if ($count > 0) {
            $model->increment('likes_count', $count);
        }
    }
}
