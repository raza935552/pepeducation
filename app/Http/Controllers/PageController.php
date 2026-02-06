<?php

namespace App\Http\Controllers;

use App\Models\Page;

class PageController extends Controller
{
    public function show(string $slug)
    {
        $page = Page::where('slug', $slug)
            ->published()
            ->firstOrFail();

        // A/B variant splitting: if page has published variants, randomly pick one
        $page = $this->pickVariant($page);

        // Use template-specific view if it exists
        $template = $page->template ?? 'default';
        $view = view()->exists("pages.{$template}") ? "pages.{$template}" : 'pages.show';

        return view($view, compact('page'));
    }

    private function pickVariant(Page $page): Page
    {
        // Only split if this is the original (not already a variant)
        if ($page->variant_of) {
            return $page;
        }

        $variants = Page::where('variant_of', $page->id)->published()->get();
        if ($variants->isEmpty()) {
            return $page;
        }

        // Weighted random selection
        $all = collect([$page, ...$variants]);
        $totalWeight = $all->sum('variant_weight');
        if ($totalWeight <= 0) {
            return $page;
        }

        $rand = random_int(1, $totalWeight);
        $cumulative = 0;

        foreach ($all as $candidate) {
            $cumulative += $candidate->variant_weight;
            if ($rand <= $cumulative) {
                return $candidate;
            }
        }

        return $page;
    }

    public function about()
    {
        return view('pages.about');
    }

    public function privacy()
    {
        return view('pages.privacy');
    }

    public function terms()
    {
        return view('pages.terms');
    }

    public function disclaimer()
    {
        return view('pages.disclaimer');
    }

    public function faq()
    {
        return view('pages.faq');
    }
}
