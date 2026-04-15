<?php

namespace App\Console\Commands;

use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\BlogTag;
use App\Models\Peptide;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class SeedBlogPostsCommand extends Command
{
    protected $signature = 'blog:seed
                            {--draft : Create as draft instead of published}
                            {--fresh : Delete all existing posts first}';

    protected $description = 'Seed all pre-written blog posts into the database';

    public function handle(): int
    {
        if ($this->option('fresh')) {
            if ($this->confirm('This will delete ALL existing blog posts. Continue?')) {
                BlogPost::query()->delete();
                $this->warn('All blog posts deleted.');
            } else {
                return self::FAILURE;
            }
        }

        $postsDir = database_path('data/blog-posts');
        if (!is_dir($postsDir)) {
            $this->error("No blog posts directory found at: {$postsDir}");
            return self::FAILURE;
        }

        $files = glob($postsDir . '/*.php');
        sort($files);

        // Create author accounts for natural-looking blog
        $authorIds = $this->ensureAuthors();

        $this->info("Found " . count($files) . " blog post files.");
        $created = 0;
        $skipped = 0;

        // Stagger publish dates going back in time
        $publishDate = Carbon::now()->subDays(count($files) * 3);

        foreach ($files as $file) {
            $post = require $file;

            if (!is_array($post) || empty($post['title']) || empty($post['html'])) {
                $this->warn("  SKIP: Invalid format in " . basename($file));
                $skipped++;
                continue;
            }

            $slug = $post['slug'] ?? Str::slug($post['title']);

            if (BlogPost::where('slug', $slug)->exists()) {
                $this->line("  SKIP: {$post['title']} (already exists)");
                $skipped++;
                $publishDate->addDays(rand(2, 4));
                continue;
            }

            // Find category
            $category = BlogCategory::where('name', $post['category'] ?? '')->first();

            // Create/find tags
            $tagIds = [];
            foreach ($post['tags'] ?? [] as $tagName) {
                $tag = BlogTag::firstOrCreate(
                    ['slug' => Str::slug($tagName)],
                    ['name' => $tagName]
                );
                $tagIds[] = $tag->id;
            }

            // Find peptide IDs
            $peptideIds = [];
            foreach ($post['peptides'] ?? [] as $pepSlug) {
                $peptide = Peptide::where('slug', $pepSlug)->first();
                if ($peptide) $peptideIds[] = $peptide->id;
            }

            $status = $this->option('draft') ? 'draft' : 'published';
            $html = $this->addInternalLinks($post['html'], $post['peptides'] ?? []);

            $blogPost = BlogPost::create([
                'title' => $post['title'],
                'slug' => $slug,
                'excerpt' => $post['excerpt'] ?? Str::limit(strip_tags($post['html']), 180),
                'html' => $html,
                'meta_title' => $post['meta_title'] ?? Str::limit($post['title'], 58),
                'meta_description' => $post['meta_description'] ?? '',
                'status' => $status,
                'is_featured' => $post['is_featured'] ?? false,
                'reading_time' => BlogPost::estimateReadingTime($html),
                'created_by' => $authorIds[array_rand($authorIds)],
                'published_at' => $status === 'published' ? $publishDate->copy() : null,
            ]);

            if ($category) $blogPost->categories()->sync([$category->id]);
            if (!empty($tagIds)) $blogPost->tags()->sync($tagIds);
            if (!empty($peptideIds)) $blogPost->peptides()->sync($peptideIds);

            $created++;
            $this->info("  Created: {$blogPost->title} ({$blogPost->reading_time} min read)");
            $publishDate->addDays(rand(2, 4));
        }

        $this->info("Done. Created: {$created}, Skipped: {$skipped}");
        return self::SUCCESS;
    }

    private function ensureAuthors(): array
    {
        $authors = [
            ['name' => 'Dr. Sarah Mitchell', 'email' => 'sarah.mitchell@professorpeptides.co'],
            ['name' => 'Dr. James Carter', 'email' => 'james.carter@professorpeptides.co'],
            ['name' => 'Elena Rodriguez, MSc', 'email' => 'elena.rodriguez@professorpeptides.co'],
            ['name' => 'Michael Torres', 'email' => 'michael.torres@professorpeptides.co'],
            ['name' => 'Dr. Priya Sharma', 'email' => 'priya.sharma@professorpeptides.co'],
        ];

        $ids = [];
        foreach ($authors as $author) {
            $user = User::firstOrCreate(
                ['email' => $author['email']],
                ['name' => $author['name'], 'password' => bcrypt(Str::random(32))]
            );
            $ids[] = $user->id;
        }

        $this->info("  Authors ready: " . implode(', ', array_column($authors, 'name')));
        return $ids;
    }

    private function addInternalLinks(string $html, array $peptideSlugs): string
    {
        foreach ($peptideSlugs as $slug) {
            $peptide = Peptide::where('slug', $slug)->first();
            if (!$peptide) continue;

            $name = preg_quote($peptide->name, '/');
            $url = route('peptides.show', $peptide->slug);

            // Only link if not already inside an <a> tag
            if (!str_contains($html, 'href="' . $url . '"')) {
                $html = preg_replace(
                    '/(?<!["\'>\/])(' . $name . ')(?![^<]*<\/a>)/i',
                    '<a href="' . $url . '">$1</a>',
                    $html,
                    1
                );
            }
        }

        return $html;
    }
}
