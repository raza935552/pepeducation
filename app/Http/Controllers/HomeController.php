<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Peptide;
use Illuminate\Support\Facades\Cache;

class HomeController extends Controller
{
    public function index()
    {
        $featuredPeptides = Cache::remember('home_featured', 1800, function () {
            return Peptide::with('categories')
                ->where('is_published', true)
                ->orderBy('name')
                ->take(6)
                ->get();
        });

        $categories = Cache::remember('home_categories', 1800, function () {
            return Category::withCount(['peptides' => fn ($q) => $q->where('is_published', true)])
                ->having('peptides_count', '>', 0)
                ->orderBy('peptides_count', 'desc')
                ->take(8)
                ->get();
        });

        $stats = Cache::remember('home_stats', 1800, function () {
            return [
                'peptides' => Peptide::where('is_published', true)->count(),
                'categories' => Category::count(),
            ];
        });

        return view('home', compact('featuredPeptides', 'categories', 'stats'));
    }
}
