<?php

namespace App\Http\Controllers;

use App\Models\Peptide;
use Illuminate\Support\Facades\Redirect;

class CalculatorController extends Controller
{
    /**
     * Calculators hub — grid of every tool.
     */
    public function index()
    {
        $calculators = config('calculators');

        return view('calculators.index', compact('calculators'));
    }

    /**
     * Single calculator page — widget + guide + FAQ + JSON-LD.
     */
    public function show(string $calculator)
    {
        $config = config("calculators.$calculator");

        abort_unless($config, 404);

        // Related peptide guides (for the in-page bridge). Only published ones.
        $related = collect($config['related'] ?? [])
            ->map(fn ($slug) => Peptide::where('slug', $slug)->where('is_published', true)
                ->first(['name', 'slug', 'abbreviation']))
            ->filter()
            ->values();

        // Peptide list (used by reconstitution + protocol widgets for presets).
        $peptides = Peptide::where('is_published', true)
            ->orderBy('name')
            ->get(['id', 'name', 'abbreviation', 'typical_dose', 'dose_frequency', 'route']);

        return view('calculators.show', compact('config', 'related', 'peptides'));
    }

    /**
     * Legacy /calculator → 301 to the reconstitution tool (preserves link equity).
     */
    public function legacyRedirect()
    {
        return Redirect::route('calculators.show', 'reconstitution', 301);
    }
}
