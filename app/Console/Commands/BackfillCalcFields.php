<?php

namespace App\Console\Commands;

use App\Models\Peptide;
use App\Services\Calculator\DoseParser;
use Illuminate\Console\Command;

/**
 * Populate the curated calc_* columns on every peptide from its typical_dose.
 * Idempotent. Run with --force to overwrite values an admin may have edited;
 * by default it only fills rows whose calc fields are still untouched (never
 * eligible AND never explicitly excluded).
 */
class BackfillCalcFields extends Command
{
    protected $signature = 'peptides:backfill-calc {--force : Overwrite existing curated values} {--dry : Show what would change without saving}';
    protected $description = 'Backfill structured reconstitution-calculator inputs from typical_dose';

    public function handle(DoseParser $parser): int
    {
        $rows = Peptide::orderBy('name')->get();
        $eligible = 0; $excluded = 0; $skipped = 0;
        $table = [];

        foreach ($rows as $p) {
            $touched = $p->calc_eligible || $p->calc_note !== null || $p->calc_default_dose !== null;
            if ($touched && !$this->option('force')) { $skipped++; continue; }

            $r = $parser->parse($p);
            $table[] = [$p->name, $p->route, $p->typical_dose, $r['eligible'] ? 'YES' : 'no', $r['eligible'] ? ($r['dose'].' '.$r['unit'].' / '.$r['vial_mg'].'mg vial') : $r['note']];

            if (!$this->option('dry')) {
                $p->forceFill([
                    'calc_eligible'     => $r['eligible'],
                    'calc_default_dose' => $r['dose'],
                    'calc_dose_unit'    => $r['unit'],
                    'calc_vial_mg'      => $r['vial_mg'],
                    'calc_water_ml'     => $r['water_ml'],
                    'calc_note'         => $r['note'],
                ])->save();
            }
            $r['eligible'] ? $eligible++ : $excluded++;
        }

        $this->table(['Peptide', 'Route', 'typical_dose', 'Eligible', 'Preset / reason'], $table);
        $this->info(($this->option('dry') ? '[dry] ' : '') . "Eligible: $eligible  Excluded: $excluded  Skipped(existing): $skipped");
        return self::SUCCESS;
    }
}
