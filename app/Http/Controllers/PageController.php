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

        // Use template-specific view if it exists
        $template = $page->template ?? 'default';
        $view = view()->exists("pages.{$template}") ? "pages.{$template}" : 'pages.show';

        return view($view, compact('page'));
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
