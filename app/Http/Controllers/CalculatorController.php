<?php

namespace App\Http\Controllers;

use App\Models\Peptide;
use App\Support\PeptideDosage;
use Illuminate\Support\Facades\Redirect;

class CalculatorController extends Controller
{
    /**
     * Calculators hub — grid of every tool + per-peptide dosage index.
     */
    public function index()
    {
        $calculators = config('calculators');
        $dosagePeptides = PeptideDosage::eligible();

        return view('calculators.index', compact('calculators', 'dosagePeptides'));
    }

    /**
     * Single calculator page — a config tool, or a per-peptide "{slug}-dosage" page.
     */
    public function show(string $calculator)
    {
        $config = config("calculators.$calculator");

        if ($config) {
            // Related peptide guides (for the in-page bridge). Only published ones.
            $related = collect($config['related'] ?? [])
                ->map(fn ($slug) => Peptide::where('slug', $slug)->where('is_published', true)
                    ->first(['name', 'slug', 'abbreviation']))
                ->filter()
                ->values();

            // Peptide list (used by reconstitution + protocol widgets for presets).
            $peptides = Peptide::where('is_published', true)
                ->orderBy('name')
                ->get(['id', 'name', 'slug', 'abbreviation', 'typical_dose', 'dose_frequency', 'route', 'biolinx_url']);

            return view('calculators.show', compact('config', 'related', 'peptides'));
        }

        // Per-peptide dosage page (e.g. /calculators/bpc-157-dosage)
        if ($peptide = PeptideDosage::resolve($calculator)) {
            return view('calculators.peptide-dosage', [
                'peptide' => $peptide,
                'seed'    => PeptideDosage::seed($peptide),
            ]);
        }

        abort(404);
    }

    /**
     * Chrome-less embeddable version of a calculator (for iframes on other sites).
     */
    public function embed(string $calculator)
    {
        $config = config("calculators.$calculator");

        abort_unless($config, 404);

        $peptides = Peptide::where('is_published', true)
            ->orderBy('name')
            ->get(['id', 'name', 'slug', 'abbreviation', 'typical_dose', 'dose_frequency', 'route', 'biolinx_url']);

        return view('calculators.embed', compact('config', 'peptides'));
    }

    /**
     * Legacy /calculator → 301 to the reconstitution tool (preserves link equity).
     */
    public function legacyRedirect()
    {
        return Redirect::route('calculators.show', 'reconstitution', 301);
    }
}
