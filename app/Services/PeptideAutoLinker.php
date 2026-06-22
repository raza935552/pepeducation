<?php

namespace App\Services;

use App\Models\Peptide;
use Illuminate\Support\Facades\Cache;

/**
 * Auto internal-linking: turns the first mention of each peptide name in body
 * content into a link to that peptide's page. Spreads topical authority across
 * the site (blog posts + peptide overviews) with no manual upkeep.
 *
 * Safe by design:
 *   - never links text already inside an <a> or a heading (h1–h6)
 *   - links each peptide at most once per body, first occurrence only
 *   - matches longest names first (so "BPC-157" wins over a partial)
 *   - word-boundary, case-insensitive; caps total links to avoid over-optimization
 *   - excludes the current peptide (no self-links)
 */
class PeptideAutoLinker
{
    /** Max auto-links injected per body — keeps it natural, not spammy. */
    protected int $maxLinks = 10;

    /**
     * @param  string    $html        Body HTML (already sanitized) or escaped text.
     * @param  int|null  $excludeId   Peptide id to skip (the current page).
     */
    public function link(string $html, ?int $excludeId = null): string
    {
        if (trim($html) === '') {
            return $html;
        }

        $map = $this->nameMap(); // name => ['slug'=>, 'id'=>], longest-first
        if (empty($map)) {
            return $html;
        }

        $linkedSlugs = [];
        $count = 0;
        $inAnchor = 0;
        $inHeading = 0;

        // Tokenize into tags vs text so we only ever touch text outside <a>/<h*>.
        $tokens = preg_split('/(<[^>]+>)/', $html, -1, PREG_SPLIT_DELIM_CAPTURE);

        foreach ($tokens as &$tok) {
            if ($tok === '' || $tok === null) {
                continue;
            }
            if ($tok[0] === '<') {
                if (preg_match('/^<a\b/i', $tok))           $inAnchor++;
                elseif (preg_match('/^<\/a\s*>/i', $tok))   $inAnchor = max(0, $inAnchor - 1);
                elseif (preg_match('/^<h[1-6]\b/i', $tok))  $inHeading++;
                elseif (preg_match('/^<\/h[1-6]\s*>/i', $tok)) $inHeading = max(0, $inHeading - 1);
                continue;
            }
            if ($inAnchor || $inHeading || $count >= $this->maxLinks) {
                continue;
            }

            foreach ($map as $name => $info) {
                if ($count >= $this->maxLinks) {
                    break;
                }
                if ($info['id'] === $excludeId || isset($linkedSlugs[$info['slug']])) {
                    continue;
                }
                $pattern = '/\b(' . preg_quote($name, '/') . ')\b/i';
                if (preg_match($pattern, $tok)) {
                    $url = route('peptides.show', $info['slug']);
                    $replacement = '<a href="' . $url . '" class="text-primary-600 hover:text-primary-700 underline decoration-primary-300 underline-offset-2">$1</a>';
                    $tok = preg_replace($pattern, $replacement, $tok, 1); // first occurrence only
                    $linkedSlugs[$info['slug']] = true;
                    $count++;
                }
            }
        }
        unset($tok);

        return implode('', $tokens);
    }

    /**
     * Published peptide names + abbreviations → slug/id, ordered longest-first.
     * Cached 1h; short/ambiguous tokens (<4 chars) skipped to avoid false hits.
     */
    protected function nameMap(): array
    {
        return Cache::remember('peptide_autolink_map', 3600, function () {
            $map = [];
            Peptide::where('is_published', true)
                ->get(['id', 'name', 'abbreviation', 'slug'])
                ->each(function ($p) use (&$map) {
                    foreach ([$p->name, $p->abbreviation] as $term) {
                        $term = trim((string) $term);
                        if (mb_strlen($term) >= 4) {
                            // Keep the first id/slug seen for a given term (case-insensitive key).
                            $key = $term;
                            if (! isset($map[$key])) {
                                $map[$key] = ['slug' => $p->slug, 'id' => $p->id];
                            }
                        }
                    }
                });

            // Longest names first so specific matches win over substrings.
            uksort($map, fn ($a, $b) => mb_strlen($b) <=> mb_strlen($a));

            return $map;
        });
    }
}
