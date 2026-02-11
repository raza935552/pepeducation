<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogCategory;
use Illuminate\Http\Request;

class BlogCategoryController extends Controller
{
    public function index()
    {
        $categories = BlogCategory::withCount('posts')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('admin.blog-categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:blog_categories,name',
            'slug' => 'nullable|string|max:100|unique:blog_categories,slug',
            'description' => 'nullable|string|max:500',
            'color' => 'nullable|string|max:7',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        if (empty($validated['sort_order'])) {
            $validated['sort_order'] = (BlogCategory::max('sort_order') ?? 0) + 1;
        }

        BlogCategory::create($validated);

        return back()->with('success', 'Blog category created successfully.');
    }

    public function update(Request $request, BlogCategory $blogCategory)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:blog_categories,name,' . $blogCategory->id,
            'slug' => 'nullable|string|max:100|unique:blog_categories,slug,' . $blogCategory->id,
            'description' => 'nullable|string|max:500',
            'color' => 'nullable|string|max:7',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $blogCategory->update($validated);

        return back()->with('success', 'Blog category updated successfully.');
    }

    public function destroy(BlogCategory $blogCategory)
    {
        if ($blogCategory->posts()->exists()) {
            return back()->with('error', 'Cannot delete category with associated blog posts.');
        }

        $blogCategory->delete();

        return back()->with('success', 'Blog category deleted successfully.');
    }
}
