<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Peptide;

class HomeController extends Controller
{
    public function index()
    {
        $featuredPeptides = Peptide::with('categories')
            ->orderBy('name')
            ->take(6)
            ->get();

        $categories = Category::withCount('peptides')
            ->having('peptides_count', '>', 0)
            ->orderBy('peptides_count', 'desc')
            ->take(8)
            ->get();

        $stats = [
            'peptides' => Peptide::count(),
            'categories' => Category::count(),
        ];

        return view('home', compact('featuredPeptides', 'categories', 'stats'));
    }
}
