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

    /** Published peptides flagged calc_eligible — the set that gets a dosage page. */
    public static function eligible()
    {
        return Peptide::where('is_published', true)
            ->where('calc_eligible', true)
            ->orderBy('name')
            ->get(['id', 'slug', 'name', 'abbreviation', 'typical_dose', 'dose_frequency', 'route', 'half_life', 'molecular_weight', 'calc_default_dose', 'calc_dose_unit', 'calc_vial_mg', 'calc_water_ml']);
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
            ->where('calc_eligible', true)
            ->first();
    }

    /**
     * Starting values for the reconstitution widget, from the curated calc_*
     * columns (backfilled by peptides:backfill-calc, admin-editable). Falls back
     * to safe defaults only if a column is somehow blank.
     */
    public static function seed(Peptide $peptide): array
    {
        return [
            'mg'       => (float) ($peptide->calc_vial_mg ?: (new \App\Services\Calculator\DoseParser)->defaultVialMg($peptide)),
            'water'    => (float) ($peptide->calc_water_ml ?: 2),
            'dose'     => (float) ($peptide->calc_default_dose ?: ($peptide->calc_dose_unit === 'mg' ? 1 : 250)),
            'doseUnit' => $peptide->calc_dose_unit ?: 'mcg',
        ];
    }
}
