<?php

namespace App\Http\Controllers;

use App\Models\Peptide;

class PeptideComparisonController extends Controller
{
    public function index(?string $slugA = null, ?string $slugB = null)
    {
        $allPeptides = Peptide::published()
            ->orderBy('name')
            ->select('id', 'name', 'slug', 'abbreviation')
            ->get();

        $peptideA = $slugA ? Peptide::published()->where('slug', $slugA)->with('categories')->first() : null;
        $peptideB = $slugB ? Peptide::published()->where('slug', $slugB)->with('categories')->first() : null;

        return view('peptides.compare', compact('allPeptides', 'peptideA', 'peptideB'));
    }
}
