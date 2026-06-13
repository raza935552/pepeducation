<?php

namespace App\Http\Controllers;

use App\Models\Peptide;
use App\Services\BioLinxService;

class WhereToBuyController extends Controller
{
    /** Hub — every peptide, split by whether Biolinx carries it. */
    public function index()
    {
        $peptides = Peptide::published()->orderBy('name')->get(['name', 'slug', 'abbreviation', 'route', 'biolinx_url']);

        return view('public.where-to-buy', compact('peptides'));
    }

    /** Per-peptide buying guide (only for peptides Biolinx actually carries). */
    public function show(string $peptide)
    {
        $pep = Peptide::published()->where('slug', $peptide)
            ->first(['id', 'name', 'slug', 'abbreviation', 'type', 'route', 'storage', 'research_status', 'molecular_weight', 'half_life', 'biolinx_url']);

        abort_unless($pep && BioLinxService::hasProductForPeptide($pep), 404);

        return view('where-to-buy.show', ['peptide' => $pep]);
    }

    /** Peptides eligible for a buying-guide page (carried by Biolinx). */
    public static function eligible()
    {
        return Peptide::published()->orderBy('name')
            ->get(['id', 'name', 'slug', 'abbreviation', 'route', 'biolinx_url'])
            ->filter(fn ($p) => BioLinxService::hasProductForPeptide($p))
            ->values();
    }
}
