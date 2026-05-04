<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use App\Models\Peptide;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class InsightsController extends Controller
{
    public function index()
    {
        // Search analytics
        $totalSearches = DB::table('search_logs')->count();
        $searchesLast30 = DB::table('search_logs')->where('created_at', '>=', now()->subDays(30))->count();

        $topSearches = DB::table('search_logs')
            ->select('query', DB::raw('COUNT(*) as searches'), DB::raw('AVG(result_count) as avg_results'))
            ->groupBy('query')
            ->orderByDesc('searches')
            ->limit(25)
            ->get();

        $zeroResultSearches = DB::table('search_logs')
            ->where('result_count', 0)
            ->select('query', DB::raw('COUNT(*) as searches'))
            ->groupBy('query')
            ->orderByDesc('searches')
            ->limit(25)
            ->get();

        // Author analytics
        $authorStats = User::where('is_public_author', true)
            ->withCount(['authoredPosts as published_count' => fn ($q) => $q->where('status', 'published')])
            ->withSum(['authoredPosts as total_views' => fn ($q) => $q->where('status', 'published')], 'views_count')
            ->orderByDesc('total_views')
            ->get(['id', 'name', 'slug', 'credentials']);

        // Top blog posts by views
        $topPosts = BlogPost::published()
            ->orderByDesc('views_count')
            ->limit(15)
            ->get(['id', 'title', 'slug', 'views_count', 'created_by']);

        // Top peptide categories by published count
        $peptidesByCat = DB::table('categories')
            ->leftJoin('category_peptide', 'categories.id', '=', 'category_peptide.category_id')
            ->leftJoin('peptides', function ($j) {
                $j->on('peptides.id', '=', 'category_peptide.peptide_id')->where('peptides.is_published', 1);
            })
            ->select('categories.name', DB::raw('COUNT(DISTINCT peptides.id) as count'))
            ->groupBy('categories.id', 'categories.name')
            ->orderByDesc('count')
            ->limit(10)
            ->get();

        return view('admin.insights.index', compact(
            'totalSearches', 'searchesLast30', 'topSearches', 'zeroResultSearches',
            'authorStats', 'topPosts', 'peptidesByCat'
        ));
    }
}
