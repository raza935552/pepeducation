<?php

namespace App\Support;

use App\Models\Peptide;

/**
 * Per-peptide dosage calculator pages (/calculators/{slug}-dosage).
 *
 * Only injectable peptides get a page — a reconstitution / syringe-unit tool
 * makes no sense for topical, oral or nasal compounds. The seed pre-fills the
 * reconstitution widget with a sensible starting dose parsed from the peptide's
 * typical_dose string.
 */
class PeptideDosage
{
    public const SUFFIX = '-dosage';

    /** Published, injectable peptides — the set that gets a dosage page. */
    public static function eligible()
    {
        return Peptide::where('is_published', true)
            ->where('route', 'like', '%inject%')
            ->orderBy('name')
            ->get(['id', 'slug', 'name', 'abbreviation', 'typical_dose', 'dose_frequency', 'route', 'half_life', 'molecular_weight']);
    }

    /** Resolve a "{slug}-dosage" URL segment to its peptide, or null. */
    public static function resolve(string $segment): ?Peptide
    {
        if (!str_ends_with($segment, self::SUFFIX)) {
            return null;
        }

        $slug = substr($segment, 0, -strlen(self::SUFFIX));

        return Peptide::where('slug', $slug)
            ->where('is_published', true)
            ->where('route', 'like', '%inject%')
            ->first();
    }

    /** Starting values for the reconstitution widget, parsed from typical_dose. */
    public static function seed(Peptide $peptide): array
    {
        $raw = (string) ($peptide->typical_dose ?? '');
        $unit = stripos($raw, 'mg') !== false ? 'mg' : 'mcg';

        preg_match('/([0-9]*\.?[0-9]+)/', $raw, $m);
        $dose = isset($m[1]) ? (float) $m[1] : ($unit === 'mg' ? 1 : 250);

        // GLP-1s ship in larger vials; everything else defaults to a common 5 mg.
        $vialMg = stripos($peptide->name, 'glp') !== false
            || in_array($peptide->slug, ['semaglutide', 'tirzepatide', 'retatrutide', 'cagrilintide'], true)
            ? 10 : 5;

        return [
            'mg'       => $vialMg,
            'water'    => 2,
            'dose'     => $dose,
            'doseUnit' => $unit,
        ];
    }
}
