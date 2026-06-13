<?php

namespace App\Http\Controllers;

use App\Models\Peptide;

class PeptideGoalController extends Controller
{
    /** Hub listing every "best peptides for {goal}" roundup. */
    public function index()
    {
        $goals = config('peptide_goals');

        return view('peptide-goals.index', compact('goals'));
    }

    /** A single ranked roundup page. */
    public function show(string $goal)
    {
        $config = config("peptide_goals.$goal");

        abort_unless($config, 404);

        // Resolve picks → live peptide data, preserving the curated ranking,
        // dropping any pick whose peptide is unpublished/removed.
        $picks = collect($config['picks'])->map(function ($p) {
            $pep = Peptide::where('slug', $p['slug'])->where('is_published', true)
                ->first(['id', 'name', 'slug', 'abbreviation', 'typical_dose', 'route', 'biolinx_url']);

            return $pep ? ['peptide' => $pep, 'why' => $p['why']] : null;
        })->filter()->values();

        // Other goals (for internal linking).
        $others = collect(config('peptide_goals'))
            ->reject(fn ($g) => $g['slug'] === $goal)
            ->values();

        return view('peptide-goals.show', compact('config', 'picks', 'others'));
    }
}
