<?php

namespace App\Http\Controllers;

use App\Models\Peptide;

class CalculatorController extends Controller
{
    public function index()
    {
        $peptides = Peptide::where('is_published', true)
            ->orderBy('name')
            ->select('id', 'name', 'abbreviation', 'typical_dose', 'dose_frequency', 'route')
            ->get();

        return view('calculator.index', compact('peptides'));
    }
}
