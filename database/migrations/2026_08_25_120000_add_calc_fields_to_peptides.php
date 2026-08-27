<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Curated, structured inputs for the reconstitution / dosage calculator.
 *
 * Replaces render-time regex parsing of the free-text `typical_dose` column
 * (which misparsed ranges, IU units, and pulled numbers out of names like
 * "BPC-157"). These columns are backfilled once by `peptides:backfill-calc`
 * and are admin-editable, so every peptide pre-fills a human-checked value.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('peptides', function (Blueprint $table) {
            // Does this peptide get a reconstitution dosage page + calculator preset?
            // Explicit, so a route typo or a stack/IU compound can't sneak in.
            $table->boolean('calc_eligible')->default(false)->after('route');
            // Starting per-injection dose for the calculator preset.
            $table->decimal('calc_default_dose', 10, 3)->nullable()->after('calc_eligible');
            // 'mg' or 'mcg' (IU/mU compounds are marked ineligible instead).
            $table->string('calc_dose_unit', 8)->nullable()->after('calc_default_dose');
            // Amount of peptide in the vial the preset assumes (mg).
            $table->decimal('calc_vial_mg', 10, 3)->nullable()->after('calc_dose_unit');
            // Bacteriostatic water the preset assumes (mL).
            $table->decimal('calc_water_ml', 6, 2)->nullable()->after('calc_vial_mg');
            // Why it was excluded (stack / iu-dosed / non-injectable / no-dose), for admin clarity.
            $table->string('calc_note', 120)->nullable()->after('calc_water_ml');
        });
    }

    public function down(): void
    {
        Schema::table('peptides', function (Blueprint $table) {
            $table->dropColumn(['calc_eligible', 'calc_default_dose', 'calc_dose_unit', 'calc_vial_mg', 'calc_water_ml', 'calc_note']);
        });
    }
};
