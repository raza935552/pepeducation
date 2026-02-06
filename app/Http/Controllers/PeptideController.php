<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Peptide;
use Illuminate\Http\Request;

class PeptideController extends Controller
{
    public function index(Request $request)
    {
        $query = Peptide::with('categories')->published();

        // Search
        if ($rawSearch = $request->get('search')) {
            $search = str_replace(['%', '_'], ['\\%', '\\_'], $rawSearch);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('full_name', 'like', "%{$search}%")
                  ->orWhere('overview', 'like', "%{$search}%");
            });
        }

        // Filter by category
        if ($category = $request->get('category')) {
            $query->whereHas('categories', fn($q) => $q->where('slug', $category));
        }

        // Filter by research status
        if ($research = $request->get('research')) {
            $query->where('research_status', $research);
        }

        $peptides = $query->orderBy('name')->paginate(12);
        $categories = Category::withCount(['peptides' => fn($q) => $q->published()])
            ->having('peptides_count', '>', 0)
            ->orderBy('name')
            ->get();

        return view('peptides.index', compact('peptides', 'categories'));
    }

    public function show(Peptide $peptide)
    {
        if (!$peptide->is_published) {
            abort(404);
        }

        $peptide->load('categories');

        $relatedPeptides = Peptide::published()
            ->with('categories')
            ->whereHas('categories', fn($q) => $q->whereIn('categories.id', $peptide->categories->pluck('id')))
            ->where('peptides.id', '!=', $peptide->id)
            ->limit(4)
            ->get();

        return view('peptides.show', compact('peptide', 'relatedPeptides'));
    }
}
