<?php

namespace App\Console\Commands;

use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\BlogTag;
use App\Models\Peptide;
use App\Services\Seo\SeoGeneratorService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * Auto-generate NEW, non-duplicate blog posts. Unlike blog:generate (which works
 * off the static content plan), this brainstorms FRESH topics each run against the
 * current set of titles — so monthly runs never repeat. Drafts by default (review
 * before publish); covers are generated automatically. Reuses the proven
 * prompt/parse/create logic from GenerateBlogPostsCommand.
 */
class GenerateBlogPostsAutoCommand extends GenerateBlogPostsCommand
{
    protected $signature = 'blog:auto-generate
                            {--count=3 : how many NEW posts to generate}
                            {--publish : publish immediately (default: create as draft)}
                            {--dry-run : brainstorm + list topics only, write nothing}';

    protected $description = 'Brainstorm + write NEW non-duplicate blog posts (high-intent, compliance-safe). Drafts by default. Built for a monthly schedule.';

    public function handle(): int
    {
        $provider = (new SeoGeneratorService())->getProvider();
        if (!$provider) {
            $this->error('Claude API key not configured (Admin → Settings → SEO).');
            return self::FAILURE;
        }

        $count = max(1, (int) $this->option('count'));

        $topics = $this->brainstormTopics($provider, $count);
        if (empty($topics)) {
            $this->error('Brainstorm returned no usable topics.');
            return self::FAILURE;
        }

        $this->info('Brainstormed '.count($topics).' candidate topics:');
        foreach ($topics as $t) {
            $this->line('  • '.$t['title'].'  ['.$t['category'].']');
        }

        if ($this->option('dry-run')) {
            return self::SUCCESS;
        }

        $created = 0; $failed = 0; $skipped = 0;
        foreach ($topics as $t) {
            // Dedup guard: never recreate an existing title/slug.
            if (BlogPost::where('title', $t['title'])->exists()
                || BlogPost::where('slug', Str::slug($t['title']))->exists()) {
                $this->line('  SKIP (exists): '.$t['title']);
                $skipped++;
                continue;
            }

            $this->info('  Writing: '.$t['title']);
            $raw = $provider->generate($this->buildPrompt($t), 4000);
            $parsed = $raw ? $this->parseResponse($raw, $t) : null;
            if (!$parsed) {
                $this->error('    FAILED: AI generation/parse returned nothing');
                $failed++;
                continue;
            }

            $post = $this->createPost($parsed, $t, Carbon::now());
            $created++;
            $this->info('    Created '.$post->status.': '.$post->slug.' ('.$post->reading_time.' min)');

            sleep(2); // be nice to the API
        }

        if ($created > 0) {
            $this->newLine();
            $this->info('Generating covers for the new posts...');
            $this->call('blog:generate-covers');
        }

        $this->newLine();
        $this->info("Done. Created: {$created}, Failed: {$failed}, Skipped: {$skipped}");
        return self::SUCCESS;
    }

    /**
     * Ask Claude for {count} brand-new topic specs (plan format) that don't
     * duplicate any existing title. Sanitizes category + peptide slugs to valid values.
     */
    protected function brainstormTopics($provider, int $count): array
    {
        $existing = BlogPost::orderByDesc('created_at')->pluck('title')->implode("\n- ");
        $cats = BlogCategory::pluck('name')->implode(' | ');
        $peptides = Peptide::orderBy('name')->get(['name', 'slug'])
            ->map(fn ($p) => $p->name.':'.$p->slug)->implode(', ');

        $prompt = <<<PROMPT
You are the content strategist for Professor Peptides (professorpeptides.co), a peptide-research EDUCATION site that funnels readers toward researching peptides.

Propose exactly {$count} NEW blog post topics with HIGH commercial intent (a specific peptide someone may research/buy, "best X for Y" rankings, decision/comparison guides). Each must be genuinely DISTINCT from every existing post below AND from each other.

EXISTING POSTS — never duplicate the angle of any of these:
- {$existing}

CATEGORIES (choose exactly one name per topic): {$cats}

VALID PEPTIDE SLUGS (use ONLY these in "peptides"): {$peptides}

Guidance: prefer popular peptides that do NOT yet have a dedicated post, plus high-intent "best/comparison/decision" angles. Keep everything research/education framed (no medical advice, no cure claims).

Return ONLY a JSON array of exactly {$count} objects, nothing else:
[{"title":"...","keyword":"primary search keyword","category":"<one category name>","tags":["3-6 lowercase tags"],"peptides":["2-5 valid slugs"],"intent":"primer|comparison|ranking|guide"}]
PROMPT;

        $raw = $provider->generate($prompt, 2000);
        if (!$raw) {
            return [];
        }

        $start = strpos($raw, '[');
        $end = strrpos($raw, ']');
        if ($start === false || $end === false) {
            return [];
        }
        $arr = json_decode(substr($raw, $start, $end - $start + 1), true);
        if (!is_array($arr)) {
            return [];
        }

        $validCats = BlogCategory::pluck('name')->all();
        $validSlugs = Peptide::pluck('slug')->all();
        $out = [];
        foreach ($arr as $t) {
            if (empty($t['title']) || !is_string($t['title'])) {
                continue;
            }
            if (empty($t['category']) || !in_array($t['category'], $validCats, true)) {
                $t['category'] = 'Specific Use Cases';
            }
            $t['tags'] = array_values(array_filter(array_map('strval', (array) ($t['tags'] ?? []))));
            $t['peptides'] = array_values(array_intersect((array) ($t['peptides'] ?? []), $validSlugs));
            $t['keyword'] = (string) ($t['keyword'] ?? $t['title']);
            $t['intent'] = (string) ($t['intent'] ?? 'guide');
            $out[] = $t;
        }
        return $out;
    }

    /**
     * Same as the parent, but defaults to DRAFT (publish only with --publish) and
     * attributes to the blog author persona (Dr. James Carter, user 5).
     */
    protected function createPost(array $parsed, array $plan, Carbon $publishDate): BlogPost
    {
        $category = BlogCategory::where('name', $plan['category'] ?? 'Specific Use Cases')->first();

        $tagIds = [];
        foreach (($plan['tags'] ?? []) as $tagName) {
            $tagIds[] = BlogTag::firstOrCreate(['slug' => Str::slug($tagName)], ['name' => $tagName])->id;
        }

        $peptideIds = [];
        foreach (($plan['peptides'] ?? []) as $slug) {
            if ($pep = Peptide::where('slug', $slug)->first()) {
                $peptideIds[] = $pep->id;
            }
        }

        $html = $this->addInternalLinks($parsed['html'], $plan['peptides'] ?? []);
        $status = $this->option('publish') ? 'published' : 'draft';

        $post = BlogPost::create([
            'title' => $plan['title'],
            'slug' => BlogPost::generateSlug($plan['title']),
            'excerpt' => $parsed['excerpt'],
            'html' => $html,
            'meta_title' => $parsed['meta_title'],
            'meta_description' => $parsed['meta_description'],
            'status' => $status,
            'is_featured' => false,
            'reading_time' => BlogPost::estimateReadingTime($html),
            'created_by' => 5,
            'published_at' => $status === 'published' ? $publishDate : null,
        ]);

        if ($category) {
            $post->categories()->sync([$category->id]);
        }
        if (!empty($tagIds)) {
            $post->tags()->sync($tagIds);
        }
        if (!empty($peptideIds)) {
            $post->peptides()->sync($peptideIds);
        }

        return $post;
    }
}
