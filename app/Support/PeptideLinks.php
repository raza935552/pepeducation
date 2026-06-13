<?php

namespace App\Support;

use App\Models\Peptide;
use App\Services\BioLinxService;

/**
 * Internal-linking helper for peptide guide pages — surfaces the tools and
 * roundups that reference a given peptide (dosage calculator, "best for" goals,
 * comparisons, where-to-buy), completing the site's internal-link mesh.
 */
class PeptideLinks
{
    /** "Best peptides for {goal}" roundups whose ranking includes this peptide. */
    public static function goalsFor(string $slug): array
    {
        return collect(config('peptide_goals', []))
            ->filter(fn ($g) => collect($g['picks'] ?? [])->pluck('slug')->contains($slug))
            ->map(fn ($g) => ['slug' => $g['slug'], 'h1' => $g['h1'], 'emoji' => $g['emoji']])
            ->values()->all();
    }

    /** Curated "X vs Y" comparison pages that include this peptide (with the other peptide's name). */
    public static function comparisonsFor(Peptide $peptide): array
    {
        $pairs = collect(config('peptide_comparisons', []))
            ->keys()
            ->map(fn ($k) => explode('__', $k))
            ->filter(fn ($p) => in_array($peptide->slug, $p, true))
            ->values();

        if ($pairs->isEmpty()) {
            return [];
        }

        // Resolve the "other" peptide's display name in one query.
        $otherSlugs = $pairs->map(fn ($p) => $p[0] === $peptide->slug ? $p[1] : $p[0])->unique();
        $names = Peptide::whereIn('slug', $otherSlugs)->pluck('name', 'slug');

        return $pairs->map(function ($p) use ($peptide, $names) {
            $other = $p[0] === $peptide->slug ? $p[1] : $p[0];
            if (!isset($names[$other])) {
                return null;
            }
            return [
                'url' => route('peptides.compare.pair', ['slugA' => $peptide->slug, 'slugB' => $other]),
                'label' => $peptide->name.' vs '.$names[$other],
            ];
        })->filter()->values()->all();
    }

    /** Dosage calculator URL (injectable peptides only). */
    public static function dosageUrl(Peptide $peptide): ?string
    {
        return stripos($peptide->route ?? '', 'inject') !== false
            ? route('calculators.show', $peptide->slug.'-dosage')
            : null;
    }

    /** Where-to-buy buying guide URL (only peptides Biolinx carries). */
    public static function whereToBuyUrl(Peptide $peptide): ?string
    {
        return BioLinxService::hasProductForPeptide($peptide)
            ? route('where-to-buy.show', $peptide->slug)
            : null;
    }

    /** True if there is anything to show. */
    public static function hasAny(Peptide $peptide): bool
    {
        return self::dosageUrl($peptide)
            || self::whereToBuyUrl($peptide)
            || !empty(self::goalsFor($peptide->slug))
            || !empty(self::comparisonsFor($peptide));
    }
}
