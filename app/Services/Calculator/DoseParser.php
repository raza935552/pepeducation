<?php

namespace App\Services\Calculator;

use App\Models\Peptide;

/**
 * Turns a peptide's free-text `typical_dose` into curated, structured calculator
 * inputs. Used ONCE by `peptides:backfill-calc` to populate the calc_* columns;
 * the calculator then reads those columns, never this parser, so a good result is
 * reviewable and editable in admin instead of re-guessed on every page load.
 *
 * It fixes the failures of the old inline regex:
 *  - never pulls a number out of a compound name ("BPC-157" no longer seeds 157)
 *  - uses the MIDPOINT of a range ("250-500 mcg" -> 375), not the low end
 *  - detects IU / mU / stacks / non-injectables and marks them ineligible
 *    (the tool is mg/mcg-based; IU conversion is compound-specific)
 */
class DoseParser
{
    /** @return array{eligible:bool, dose:?float, unit:?string, vial_mg:?float, water_ml:?float, note:?string} */
    public function parse(Peptide $p): array
    {
        $raw = trim((string) $p->typical_dose);
        $name = (string) $p->name;
        $route = strtolower((string) $p->route);
        $type = strtolower((string) $p->type);

        $ineligible = fn (string $note) => [
            'eligible' => false, 'dose' => null, 'unit' => null,
            'vial_mg' => null, 'water_ml' => null, 'note' => $note,
        ];

        // 1) Only injectables get a reconstitution page.
        if (stripos($route, 'inject') === false && stripos($route, 'subcut') === false && stripos($route, 'sub-q') === false && stripos($route, 'sub q') === false && stripos($route, 'im') !== 0) {
            if (stripos($route, 'inject') === false && stripos($route, 'subcut') === false) {
                return $ineligible('non-injectable route');
            }
        }

        // 2) Multi-peptide stacks / protocols cannot be reconstituted as one vial.
        if (preg_match('/\b(stack|protocol|blend)\b/i', $name) || preg_match('/\b(stack|protocol)\b/i', $type)) {
            return $ineligible('multi-peptide stack/protocol');
        }
        // A dose string listing more than one compound (e.g. "BPC-157: ... + TB-500: ...").
        if (substr_count($raw, ':') >= 1 && preg_match('/\+|\band\b/i', $raw)) {
            return $ineligible('dose lists multiple compounds');
        }

        // 2b) Weight-based dosing (mg/kg, mcg/kg, per 100g, "body weight"): not a
        //     fixed vial dose. The units-on-a-syringe preset can't represent it.
        if (preg_match('#(?:mcg|mg|μg|ug|iu)\s*/\s*kg#i', $raw) || preg_match('#/\s*\d*\s*g\b#i', $raw) || stripos($raw, 'body weight') !== false || stripos($raw, '/kg') !== false) {
            return $ineligible('weight-based dosing');
        }

        // 2c) Oil-based / not reconstituted (testosterone / TRT ship in oil).
        if (preg_match('/\b(trt|testosterone)\b/i', $name)) {
            return $ineligible('oil-based, not reconstituted');
        }

        // 2d) Pre-mixed blends dosed only in mL (no mg/mcg strength given).
        if (preg_match('/\bml\b/i', $raw) && !preg_match('/\bm?cg\b|\bmg\b|μg/i', $raw)) {
            return $ineligible('pre-mixed / dosed in mL');
        }

        // 3) IU / mU / unit-dosed compounds: not mg/mcg convertible.
        if (preg_match('/\b(iu|mu|units?)\b/i', $raw) && !preg_match('/\bm?cg\b|\bmg\b/i', $raw)) {
            return $ineligible('IU/unit-dosed (not mg/mcg)');
        }
        // Mixed "0.15-0.3 mg/day (0.5-1 IU)" — has mg AND IU: prefer mg but flag.
        $hasIu = (bool) preg_match('/\b(iu|mu)\b/i', $raw);

        // 4) Strip anything that looks like a compound code ("BPC-157", "AOD-9604",
        //    "SS-31", "PE-22-28") so its digits are never read as a dose.
        $clean = preg_replace('/\b[A-Za-z]{1,6}-?\d{1,4}(?:-\d{1,4})?\b/', ' ', $raw);
        // Also strip standalone "TB-500", "CJC-1295" style already covered above.

        // 5) Unit: mcg wins when present (also μg / microgram); otherwise mg if
        //    "mg" appears anywhere (handles digit-adjacent "0.25mg" and "2-3mg",
        //    which a word-boundary test would miss). "mcg" has no "mg" substring.
        $hasMcg = (bool) preg_match('/mcg|μg|microg/i', $clean);
        $hasMg  = (bool) preg_match('/mg/i', $clean);
        $unit = $hasMcg ? 'mcg' : ($hasMg ? 'mg' : 'mcg');

        // 6) Pull all numbers from the CLEANED string; use the midpoint of the
        //    first one or two (a range) as the starting dose.
        preg_match_all('/\d*\.?\d+/', $clean, $nums);
        $vals = array_map('floatval', $nums[0] ?? []);
        $vals = array_values(array_filter($vals, fn ($v) => $v > 0));

        if (!$vals) {
            return $ineligible('no numeric dose found');
        }

        // Range detection: "250-500", "2-3 mg". If the raw shows a dash/"to"
        // between the first two numbers, average them; else take the first.
        $dose = $vals[0];
        if (count($vals) >= 2 && preg_match('/\d\s*(?:-|–|—|to)\s*\d/i', $clean)) {
            $dose = round(($vals[0] + $vals[1]) / 2, 3);
        }

        // 7) Large-volume compounds (a single dose of tens of mg, e.g. glutathione,
        //    NAD+, L-carnitine): these are given in mL, not drawn as units off a
        //    shared small vial, so the reconstitution-units model does not fit.
        $doseMg = $unit === 'mg' ? $dose : $dose / 1000;
        if ($doseMg >= 20) {
            return $ineligible('large-volume dosing (mL, not units)');
        }

        // 8) Vial size the preset assumes. GLP-1 class ships larger; a peptide whose
        //    single dose alone exceeds a 5 mg vial (MOTS-c, SS-31) needs a 10 mg vial
        //    so the preset shows more than one dose per vial.
        $vialMg = $this->defaultVialMg($p);
        if ($doseMg > $vialMg * 0.5) {
            $vialMg = max($vialMg, 10.0);
        }

        $note = $hasIu ? 'mg preferred; also IU-labelled' : null;

        return [
            'eligible' => true,
            'dose'     => $dose,
            'unit'     => $unit,
            'vial_mg'  => $vialMg,
            'water_ml' => 2.0,
            'note'     => $note,
        ];
    }

    public function defaultVialMg(Peptide $p): float
    {
        $isGlp = stripos((string) $p->name, 'glp') !== false
            || in_array($p->slug, ['semaglutide', 'tirzepatide', 'retatrutide', 'cagrilintide', 'survodutide', 'mazdutide'], true);
        return $isGlp ? 10.0 : 5.0;
    }
}
