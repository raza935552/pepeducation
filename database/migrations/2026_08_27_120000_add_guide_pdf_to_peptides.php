<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Per-peptide education guide PDF (the 10-page reconstitution/dosing guide).
 * Managed admin-side and attached to the peptide record so future products can
 * auto-generate and attach their own guide.
 */
return new class extends Migration {
    public function up(): void {
        Schema::table('peptides', function (Blueprint $t) {
            $t->string('guide_pdf')->nullable()->after('biolinx_url');   // path under public/, e.g. guides/bpc-157.pdf
            $t->timestamp('guide_updated_at')->nullable()->after('guide_pdf');
        });
    }
    public function down(): void {
        Schema::table('peptides', function (Blueprint $t) { $t->dropColumn(['guide_pdf', 'guide_updated_at']); });
    }
};
