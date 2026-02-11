<?php

namespace App\Http\Controllers;

use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\BlogTag;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    public function index(Request $request)
    {
        $query = BlogPost::published()->with(['author', 'categories']);

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('excerpt', 'like', "%{$search}%");
            });
        }

        if ($categorySlug = $request->input('category')) {
            $query->whereHas('categories', fn($q) => $q->where('slug', $categorySlug));
        }

        $posts = $query->latest('published_at')->paginate(12)->withQueryString();

        $featuredPosts = BlogPost::published()->featured()
            ->with(['author', 'categories'])
            ->latest('published_at')
            ->take(3)
            ->get();

        $categories = BlogCategory::ordered()->withCount([
            'posts' => fn($q) => $q->published(),
        ])->get();

        return view('blog.index', compact('posts', 'featuredPosts', 'categories'));
    }

    public function show(string $slug)
    {
        $post = BlogPost::where('slug', $slug)
            ->published()
            ->with(['author', 'categories', 'tags', 'peptides'])
            ->firstOrFail();

        $post->increment('views_count');

        $relatedPosts = BlogPost::published()
            ->where('id', '!=', $post->id)
            ->whereHas('categories', function ($q) use ($post) {
                $q->whereIn('blog_categories.id', $post->categories->pluck('id'));
            })
            ->with(['author', 'categories'])
            ->latest('published_at')
            ->take(3)
            ->get();

        $categories = BlogCategory::ordered()->withCount([
            'posts' => fn($q) => $q->published(),
        ])->get();

        $popularPosts = BlogPost::published()
            ->where('id', '!=', $post->id)
            ->orderByDesc('views_count')
            ->take(5)
            ->get();

        return view('blog.show', compact('post', 'relatedPosts', 'categories', 'popularPosts'));
    }

    public function category(BlogCategory $category)
    {
        $posts = BlogPost::published()
            ->whereHas('categories', fn($q) => $q->where('blog_categories.id', $category->id))
            ->with(['author', 'categories'])
            ->latest('published_at')
            ->paginate(12);

        $categories = BlogCategory::ordered()->withCount([
            'posts' => fn($q) => $q->published(),
        ])->get();

        return view('blog.category', compact('category', 'posts', 'categories'));
    }

    public function tag(BlogTag $tag)
    {
        $posts = BlogPost::published()
            ->whereHas('tags', fn($q) => $q->where('blog_tags.id', $tag->id))
            ->with(['author', 'categories'])
            ->latest('published_at')
            ->paginate(12);

        $categories = BlogCategory::ordered()->withCount([
            'posts' => fn($q) => $q->published(),
        ])->get();

        return view('blog.tag', compact('tag', 'posts', 'categories'));
    }
}
