<?php

namespace App\Console\Commands;

use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\BlogTag;
use App\Models\Peptide;
use App\Services\Seo\SeoGeneratorService;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class GenerateBlogPostsCommand extends Command
{
    protected $signature = 'blog:generate
                            {--limit=0 : Number of posts to generate (0 = all)}
                            {--offset=0 : Skip this many posts}
                            {--dry-run : Preview without creating}
                            {--draft : Create as draft instead of published}';

    protected $description = 'Generate blog posts from the content plan using Claude AI';

    public function handle(): int
    {
        $generator = new SeoGeneratorService();
        $provider = $generator->getProvider();

        if (!$provider) {
            $this->error('Claude API key not configured. Set it at /admin/settings/seo');
            return self::FAILURE;
        }

        $plan = require database_path('data/blog-plan.php');
        $offset = (int) $this->option('offset');
        $limit = (int) $this->option('limit');

        if ($offset > 0) {
            $plan = array_slice($plan, $offset);
        }
        if ($limit > 0) {
            $plan = array_slice($plan, 0, $limit);
        }

        $this->info("Generating " . count($plan) . " blog posts...");
        $created = 0;
        $failed = 0;

        // Stagger publish dates (2-3 posts per week, going back in time for natural look)
        $publishDate = Carbon::now()->subDays(count($plan) * 3);

        foreach ($plan as $i => $post) {
            // Skip if already exists
            if (BlogPost::where('slug', $post['slug'])->exists()) {
                $this->line("  SKIP: {$post['title']} (already exists)");
                $publishDate->addDays(rand(2, 4));
                continue;
            }

            $this->info("  [{$created}/{$i}] Generating: {$post['title']}");

            if ($this->option('dry-run')) {
                $this->comment("    Would create: {$post['slug']}");
                $publishDate->addDays(rand(2, 4));
                continue;
            }

            // Build the prompt
            $prompt = $this->buildPrompt($post);
            $raw = $provider->generate($prompt, 4000);

            if (!$raw) {
                $this->error("    FAILED: AI generation returned null");
                $failed++;
                continue;
            }

            // Parse the response
            $parsed = $this->parseResponse($raw, $post);
            if (!$parsed) {
                $this->error("    FAILED: Could not parse AI response");
                $failed++;
                continue;
            }

            // Create the blog post
            $blogPost = $this->createPost($parsed, $post, $publishDate->copy());
            $created++;

            $this->info("    Created: {$blogPost->title} ({$blogPost->reading_time} min read)");

            // Advance publish date
            $publishDate->addDays(rand(2, 4));

            // Rate limit - be nice to the API
            if ($i < count($plan) - 1) {
                sleep(2);
            }
        }

        $this->info("Done. Created: {$created}, Failed: {$failed}, Skipped: " . (count($plan) - $created - $failed));
        return self::SUCCESS;
    }

    private function buildPrompt(array $post): string
    {
        $title = $post['title'];
        $keyword = $post['keyword'];
        $intent = $post['intent'];
        $tags = implode(', ', $post['tags']);

        // Get related peptide data for accuracy
        $peptideContext = $this->getPeptideContext($post['peptides']);

        return <<<PROMPT
Write a blog post for Professor Peptides (professorpeptides.co), a free peptide education website.

ARTICLE DETAILS:
Title: {$title}
Target keyword: {$keyword}
Related topics: {$tags}
Search intent: {$intent}

PEPTIDE DATA FOR ACCURACY (use this data, do not fabricate numbers):
{$peptideContext}

WRITING INSTRUCTIONS:

Voice and tone:
- Write like a knowledgeable friend who happens to be a biochemist. Not a professor lecturing, not a marketer selling.
- Vary sentence length. Mix short punchy statements with longer explanatory ones.
- Use "you" naturally but not in every sentence.
- Start some paragraphs with the subject, others with context. Avoid formulaic patterns.
- No corporate speak. No "In this comprehensive guide." No "Let's dive in." No "In the ever-evolving world of."

Structure:
- Open with a hook that makes someone want to keep reading. A surprising fact, a common misconception, or a real scenario.
- Use H2 headings that are useful and specific (not generic like "Benefits" - instead "What BPC-157 Actually Does in the Body").
- 5-7 H2 sections. Each section should be 150-250 words.
- Include specific numbers from studies where available (dosages in mcg/mg, percentages, study sizes).
- End with a practical takeaway, not a generic conclusion.

SEO requirements:
- Use the target keyword "{$keyword}" naturally 3-5 times throughout.
- Include related semantic terms from the tags.
- Write 1200-1800 words total.

Accuracy requirements:
- Only cite real studies and real data. If you're unsure about a specific number, say "research suggests" rather than making up a percentage.
- Clearly distinguish between FDA-approved peptides and experimental/research compounds.
- Never give medical advice. Use "research indicates", "studies show", "users report" framing.
- If something is not FDA approved, say so clearly.

Things to NEVER do:
- Never use em-dashes. Use commas, periods, or colons instead.
- Never use "In the realm of", "It's worth noting", "In conclusion", "Let's dive in", "comprehensive guide", "game-changer", "revolutionizing".
- Never use emoji.
- Never start 3+ paragraphs the same way.
- Never write filler sentences that don't add information.
- Never fabricate study results or specific statistics.

OUTPUT FORMAT:
Return the content as a JSON object with these fields:
{
  "meta_title": "SEO title under 58 chars",
  "meta_description": "SEO description under 155 chars",
  "excerpt": "2-3 sentence summary for blog listing cards, under 200 chars",
  "html": "Full article HTML using h2, p, ul/li, ol/li, strong, em tags. No h1 (title is separate). No inline styles. No divs."
}

Output ONLY the JSON. No explanation before or after.
PROMPT;
    }

    private function getPeptideContext(array $slugs): string
    {
        if (empty($slugs)) {
            return "No specific peptides. This is a general educational article.";
        }

        $context = [];
        foreach ($slugs as $slug) {
            $peptide = Peptide::where('slug', $slug)->first();
            if (!$peptide) continue;

            $benefits = is_array($peptide->key_benefits) ? implode('; ', $peptide->key_benefits) : '';
            $mechanism = strip_tags($peptide->mechanism_of_action ?? '');
            $overview = Str::limit(strip_tags($peptide->overview ?? ''), 300, '');
            $warnings = is_array($peptide->safety_warnings) ? implode('; ', array_slice($peptide->safety_warnings, 0, 3)) : '';
            $protocols = '';
            if (is_array($peptide->protocols)) {
                foreach (array_slice($peptide->protocols, 0, 3) as $p) {
                    $protocols .= ($p['goal'] ?? '') . ': ' . ($p['dose'] ?? '') . ' ' . ($p['frequency'] ?? '') . '; ';
                }
            }

            $context[] = "--- {$peptide->name} ---\nOverview: {$overview}\nBenefits: {$benefits}\nMechanism: {$mechanism}\nProtocols: {$protocols}\nWarnings: {$warnings}";
        }

        return implode("\n\n", $context) ?: "No specific peptide data available.";
    }

    private function parseResponse(string $raw, array $post): ?array
    {
        $raw = trim($raw);

        // Try to extract JSON from the response
        // Handle potential markdown code blocks
        if (preg_match('/```(?:json)?\s*(\{.*\})\s*```/s', $raw, $m)) {
            $raw = $m[1];
        }

        // Find the JSON object
        $start = strpos($raw, '{');
        $end = strrpos($raw, '}');
        if ($start === false || $end === false) {
            return null;
        }

        $json = substr($raw, $start, $end - $start + 1);
        $data = json_decode($json, true);

        if (!$data || !isset($data['html'])) {
            return null;
        }

        return [
            'meta_title' => SeoGeneratorService::smartTruncate($data['meta_title'] ?? $post['title'], 58),
            'meta_description' => SeoGeneratorService::smartTruncate($data['meta_description'] ?? '', 155),
            'excerpt' => Str::limit($data['excerpt'] ?? '', 200),
            'html' => $data['html'],
        ];
    }

    private function createPost(array $parsed, array $plan, Carbon $publishDate): BlogPost
    {
        // Find category
        $category = BlogCategory::where('name', $plan['category'])->first();

        // Create or find tags
        $tagIds = [];
        foreach ($plan['tags'] as $tagName) {
            $tag = BlogTag::firstOrCreate(
                ['slug' => Str::slug($tagName)],
                ['name' => $tagName]
            );
            $tagIds[] = $tag->id;
        }

        // Find peptide IDs
        $peptideIds = [];
        foreach ($plan['peptides'] as $slug) {
            $peptide = Peptide::where('slug', $slug)->first();
            if ($peptide) $peptideIds[] = $peptide->id;
        }

        // Add internal links to peptide pages in the HTML
        $html = $this->addInternalLinks($parsed['html'], $plan['peptides']);

        $status = $this->option('draft') ? 'draft' : 'published';

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
            'created_by' => 1,
            'published_at' => $status === 'published' ? $publishDate : null,
        ]);

        // Attach relationships
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

    private function addInternalLinks(string $html, array $peptideSlugs): string
    {
        // Add links to peptide pages where peptide names are mentioned
        foreach ($peptideSlugs as $slug) {
            $peptide = Peptide::where('slug', $slug)->first();
            if (!$peptide) continue;

            $name = preg_quote($peptide->name, '/');
            $url = route('peptides.show', $peptide->slug);

            // Only link the first occurrence, and not inside existing links or headings
            $html = preg_replace(
                '/(?<!<a[^>]*>)(?<!["\'>\/])(' . $name . ')(?![^<]*<\/a>)(?![^<]*<\/h[1-6]>)/i',
                '<a href="' . $url . '">$1</a>',
                $html,
                1
            );
        }

        return $html;
    }
}
