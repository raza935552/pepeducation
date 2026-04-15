<?php

namespace App\Services\Seo;

use App\Models\BlogPost;
use App\Models\Peptide;
use App\Models\Setting;
use App\Services\Seo\Providers\ClaudeProvider;
use Illuminate\Support\Facades\Crypt;

class SeoGeneratorService
{
    public function getProvider(): ?ClaudeProvider
    {
        $key = self::decryptKey(Setting::getValue('seo', 'claude_api_key'));
        $model = Setting::getValue('seo', 'claude_model', 'claude-sonnet-4-20250514');
        return $key ? new ClaudeProvider($key, $model) : null;
    }

    public function getProviderWithKey(string $apiKey): ClaudeProvider
    {
        $model = Setting::getValue('seo', 'claude_model', 'claude-sonnet-4-20250514');
        return new ClaudeProvider($apiKey, $model);
    }

    public function isConfigured(): bool
    {
        return $this->getProvider() !== null;
    }

    // ──────────────────────────────────────────────
    // Peptide SEO Meta
    // ──────────────────────────────────────────────

    public function generateForPeptide(Peptide $peptide): ?array
    {
        $provider = $this->getProvider();
        if (!$provider) return null;

        $prompt = $this->buildPeptideMetaPrompt($peptide);
        $text = $provider->generate($prompt, 300);

        return $text ? $this->parseJsonResponse($text) : null;
    }

    protected function buildPeptideMetaPrompt(Peptide $peptide): string
    {
        $name = $peptide->name;
        $abbr = $peptide->abbreviation && $peptide->abbreviation !== $name ? $peptide->abbreviation : '';
        $categories = $peptide->categories->pluck('name')->take(3)->implode(', ');
        $overview = mb_substr(strip_tags($peptide->overview ?? ''), 0, 400);
        $benefits = is_array($peptide->key_benefits) ? implode('; ', $peptide->key_benefits) : '';
        $benefits = mb_substr($benefits, 0, 300);
        $mechanism = mb_substr(strip_tags($peptide->mechanism_of_action ?? ''), 0, 200);

        return <<<PROMPT
You are a senior SEO strategist for "Professor Peptides" (professorpeptides.co), a FREE peptide education platform. Your goal: write meta title and description that DOMINATE search results for this peptide.

CONTEXT:
- This is an EDUCATION site, not a store. We don't sell anything.
- We teach people about peptide research, protocols, benefits, safety, and dosing.
- Our audience: people researching peptides for the first time, biohackers, health-conscious adults, and people considering peptide therapy.
- Competitors: examine.com, peptidesciences.com, reddit peptide threads, health blogs.

PEPTIDE DATA:
Name: {$name}
Abbreviation: {$abbr}
Categories: {$categories}
Overview: {$overview}
Key Benefits: {$benefits}
Mechanism: {$mechanism}
URL: professorpeptides.co/peptides/{$peptide->slug}

KEYWORD STRATEGY (think through these, don't output):
- Primary: "{$name}" and "{$name} peptide"
- Intent keywords: "benefits", "dosing", "side effects", "protocol", "guide", "what is", "how to use"
- Long-tail: "{$name} benefits and side effects", "{$name} dosing protocol", "{$name} guide"
- People Also Ask: "What does {$name} do?", "Is {$name} safe?", "How to take {$name}"

RULES:
- meta_title: MAX 58 chars. Front-load the peptide name. Include a high-intent keyword (benefits, guide, dosing). Make it click-worthy without being clickbait. Don't end with "| Professor Peptides" — Google adds site name automatically.
- meta_description: MAX 155 chars. Start with a hook — what makes this peptide interesting or unique. Include 2-3 search terms naturally. End with a soft CTA ("Learn more", "Explore protocols", "See our guide"). Must read like a human wrote it, not AI.
- Write like an expert educator, NOT a marketer. Authoritative but accessible.
- NO emojis, NO hype words (amazing, incredible, revolutionary), NO ALL CAPS.
- NEVER use em-dashes (the long dash character). Use colons, periods, or hyphens instead.
- Output ONLY valid JSON: {"meta_title":"...","meta_description":"..."}
PROMPT;
    }

    // ──────────────────────────────────────────────
    // Peptide Content Rewrite
    // ──────────────────────────────────────────────

    public function rewritePeptideOverview(Peptide $peptide): ?string
    {
        $provider = $this->getProvider();
        if (!$provider) return null;

        $prompt = $this->buildOverviewRewritePrompt($peptide);
        return $provider->generate($prompt, 1500);
    }

    protected function buildOverviewRewritePrompt(Peptide $peptide): string
    {
        $name = $peptide->name;
        $overview = strip_tags($peptide->overview ?? '');
        $benefits = is_array($peptide->key_benefits) ? implode('; ', $peptide->key_benefits) : '';
        $mechanism = strip_tags($peptide->mechanism_of_action ?? '');
        $categories = $peptide->categories->pluck('name')->implode(', ');

        return <<<PROMPT
You are a peptide researcher and science writer for Professor Peptides, a trusted education platform. Rewrite the overview section for {$name} to be more comprehensive, engaging, and SEO-optimized.

CURRENT OVERVIEW:
{$overview}

ADDITIONAL CONTEXT:
- Categories: {$categories}
- Key Benefits: {$benefits}
- Mechanism: {$mechanism}

WRITING GUIDELINES:
1. Write 2-3 paragraphs (200-350 words total).
2. First paragraph: Hook the reader — what is this peptide and why should they care? Define it clearly for someone who's never heard of it.
3. Second paragraph: How it works (mechanism) and key benefits. Be specific with data when available (percentages, study results).
4. Third paragraph: Who uses it and what to expect. Practical, not theoretical.
5. Use natural language — it should read like an expert explaining to a friend, not a textbook or Wikipedia.
6. Naturally include these search phrases where they fit: "{$name} benefits", "{$name} side effects", "how {$name} works".
7. NO medical advice language ("you should take", "consult your doctor"). Frame as educational: "research suggests", "studies indicate", "users report".
8. NO fluff, filler sentences, or AI-sounding phrases ("In the realm of", "It's worth noting", "In conclusion").
8b. NEVER use em-dashes. Use commas, periods, or colons instead.
9. Include specific numbers, study references, or comparisons where the data supports them.
10. Write in a voice that's confident and knowledgeable — like a professor explaining to a smart student.

Output ONLY the rewritten overview text. No headings, no markdown, no explanation. Plain text paragraphs only.
PROMPT;
    }

    // ──────────────────────────────────────────────
    // Blog Post Generation
    // ──────────────────────────────────────────────

    public function generateBlogOutline(string $topic, ?string $targetKeyword = null): ?string
    {
        $provider = $this->getProvider();
        if (!$provider) return null;

        $keyword = $targetKeyword ?? $topic;

        $prompt = <<<PROMPT
You are a content strategist for Professor Peptides (professorpeptides.co), a peptide education platform. Create a detailed blog post outline that will rank for "{$keyword}".

TOPIC: {$topic}
TARGET KEYWORD: {$keyword}

Create an outline with:
1. A compelling title (under 65 chars, includes the target keyword)
2. A meta description (under 155 chars)
3. An introduction hook (2-3 sentences that make the reader want to continue)
4. 5-8 H2 sections with:
   - Section heading (include semantic keywords)
   - 2-3 bullet points of what to cover
   - Any data points or studies to reference
5. A conclusion section
6. 3-5 internal link suggestions (link to specific peptide pages on the site)

GUIDELINES:
- Write for humans first, search engines second
- Target "People Also Ask" questions as H2s where natural
- Include comparison angles (e.g., "X vs Y") where relevant
- Make it comprehensive enough to be THE definitive resource on this topic
- Aim for 1500-2500 word article when fully written
- Voice: authoritative educator, not salesy blogger

Output as structured text with clear headings. No JSON needed.
PROMPT;

        return $provider->generate($prompt, 1500);
    }

    // ──────────────────────────────────────────────
    // Helpers
    // ──────────────────────────────────────────────

    public function parseJsonResponse(string $text): ?array
    {
        $text = trim($text);

        if (preg_match('/\{[^}]+\}/', $text, $matches)) {
            $data = json_decode($matches[0], true);
            if ($data && isset($data['meta_title'], $data['meta_description'])) {
                return [
                    'meta_title' => self::smartTruncate($data['meta_title'], 60),
                    'meta_description' => self::smartTruncate($data['meta_description'], 155),
                ];
            }
        }

        return null;
    }

    public static function smartTruncate(string $text, int $max): string
    {
        $text = trim($text);
        if (mb_strlen($text) <= $max) {
            return $text;
        }

        $cut = mb_substr($text, 0, $max);
        $lastPeriod = mb_strrpos($cut, '.');
        $lastBang = mb_strrpos($cut, '!');
        $sentenceEnd = max($lastPeriod ?: 0, $lastBang ?: 0);

        if ($sentenceEnd > $max * 0.6) {
            return mb_substr($text, 0, $sentenceEnd + 1);
        }

        $lastSpace = mb_strrpos($cut, ' ');
        if ($lastSpace > $max * 0.6) {
            return rtrim(mb_substr($text, 0, $lastSpace));
        }

        return $cut;
    }

    public static function encryptKey(?string $key): ?string
    {
        return empty($key) ? null : Crypt::encryptString($key);
    }

    public static function decryptKey(?string $encrypted): ?string
    {
        if (empty($encrypted)) return null;

        try {
            return Crypt::decryptString($encrypted);
        } catch (\Exception) {
            return $encrypted;
        }
    }

    public static function maskKey(?string $encrypted): string
    {
        if (empty($encrypted)) return '';

        $key = self::decryptKey($encrypted);
        if (empty($key) || strlen($key) < 8) return '';

        return substr($key, 0, 4) . str_repeat('*', 8) . substr($key, -4);
    }
}
