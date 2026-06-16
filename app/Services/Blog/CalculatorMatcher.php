<?php

namespace App\Services\Blog;

use App\Models\BlogPost;

/**
 * Maps a blog post to the calculators (config/calculators.php) it should link to.
 *
 * Two outputs:
 *  - forPost()       → ranked calculators for the on-page "Related Calculators" block
 *                      (uses keyword hits + a topical fallback per blog category, so a
 *                      relevant post is never left without a tool).
 *  - inlineTargets() → only calculators whose trigger phrase actually appears in the
 *                      post body, with that phrase — used to inject ONE natural
 *                      in-content anchor each.
 */
class CalculatorMatcher
{
    /**
     * Trigger phrases per calculator slug (lowercase, matched as substrings).
     * Order matters for inline anchors: the first phrase found is the anchor text.
     */
    private const KEYWORDS = [
        'glp-1'          => ['glp-1', 'glp1', 'semaglutide', 'tirzepatide', 'retatrutide', 'ozempic', 'wegovy', 'mounjaro', 'zepbound', 'titration schedule'],
        'weight-loss'    => ['weight loss', 'weight-loss', 'lose weight', 'fat loss', 'caloric deficit'],
        'glp-1-units'    => ['mg to units', 'how many units', 'dose converter', 'units on the syringe', 'units to draw'],
        'reconstitution' => ['reconstitut', 'bacteriostatic', 'bac water', 'how to mix', 'mixing your', 'syringe units', 'insulin syringe', 'mcg/ml'],
        'bmi'            => ['body mass index', 'bmi'],
        'trt'            => ['testosterone replacement', 'trt', 'low testosterone'],
        'melanotan'      => ['melanotan', 'mt-2', 'mt-ii', 'tanning peptide'],
        'fitness'        => ['hypertrophy', 'lean muscle', 'muscle gain', 'workout routine', 'athletic performance'],
        'tdee'           => ['tdee', 'maintenance calories', 'total daily energy', 'daily calorie'],
        'body-fat'       => ['body fat percentage', 'body-fat', 'bodyfat'],
        'macro'          => ['macronutrient', 'macro split', 'protein intake', 'macros'],
        'protocol'       => ['dosing protocol', 'dosing schedule', 'cycle length', 'how to dose'],
    ];

    /**
     * Topical fallback by blog-category slug → ordered calculator slugs. Ensures a
     * matched post always surfaces relevant tools even with no explicit keyword.
     */
    private const CATEGORY_DEFAULTS = [
        'weight-loss-peptides'        => ['glp-1', 'weight-loss', 'glp-1-units', 'bmi'],
        'healing-recovery'            => ['reconstitution', 'protocol'],
        'anti-aging-longevity'        => ['reconstitution', 'protocol'],
        'growth-hormone-performance'  => ['fitness', 'reconstitution', 'tdee'],
        'cognitive-nootropics'        => ['reconstitution', 'protocol'],
        'practical-guides'            => ['reconstitution', 'protocol', 'glp-1-units'],
        'regulation-safety'           => ['reconstitution'],
        'specific-use-cases'          => ['protocol', 'reconstitution'],
    ];

    /**
     * Ranked calculators for the on-page block. Each item:
     * [slug, name, emoji, tagline, url, keyword] (keyword null when from category fallback).
     *
     * @return array<int, array<string, mixed>>
     */
    public function forPost(BlogPost $post, int $limit = 3): array
    {
        $tools = config('calculators', []);
        $scores = [];   // slug => score
        $hits   = [];   // slug => matched phrase

        $title   = mb_strtolower((string) $post->title);
        $excerpt = mb_strtolower((string) $post->excerpt);
        $body    = mb_strtolower(strip_tags((string) $post->html));

        foreach (self::KEYWORDS as $slug => $phrases) {
            if (!isset($tools[$slug])) {
                continue;
            }
            foreach ($phrases as $phrase) {
                $s = 0;
                if (str_contains($title, $phrase))   { $s += 6; }
                if (str_contains($excerpt, $phrase))  { $s += 3; }
                if (str_contains($body, $phrase))     { $s += 2; $hits[$slug] = $hits[$slug] ?? $phrase; }
                if ($s > 0) {
                    $scores[$slug] = ($scores[$slug] ?? 0) + $s;
                }
            }
        }

        // Topical fallback by category (weighted below any real keyword match).
        foreach ($post->categories as $cat) {
            $defaults = self::CATEGORY_DEFAULTS[$cat->slug] ?? [];
            foreach ($defaults as $i => $slug) {
                if (isset($tools[$slug])) {
                    $scores[$slug] = ($scores[$slug] ?? 0) + max(1, 4 - $i);
                }
            }
        }

        arsort($scores);
        $out = [];
        foreach (array_slice(array_keys($scores), 0, $limit) as $slug) {
            $cfg = $tools[$slug];
            $out[] = [
                'slug'    => $slug,
                'name'    => $cfg['name'] ?? $slug,
                'emoji'   => $cfg['emoji'] ?? '🧮',
                'tagline' => $cfg['tagline'] ?? '',
                'url'     => route('calculators.show', $slug),
                'keyword' => $hits[$slug] ?? null,
            ];
        }

        return $out;
    }

    /**
     * Calculators whose trigger phrase actually appears in the post body — for inline
     * anchor injection. Returns [slug, phrase, url, name], top $limit by relevance.
     *
     * @return array<int, array<string, mixed>>
     */
    public function inlineTargets(BlogPost $post, int $limit = 2): array
    {
        return collect($this->forPost($post, 6))
            ->filter(fn ($c) => !empty($c['keyword']))
            ->take($limit)
            ->values()
            ->all();
    }
}
