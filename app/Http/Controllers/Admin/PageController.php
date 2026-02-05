<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PageController extends Controller
{
    public function index()
    {
        $pages = Page::with('author')
            ->latest()
            ->paginate(15);

        return view('admin.pages.index', compact('pages'));
    }

    public function create()
    {
        return view('admin.pages.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:pages,slug',
            'content' => 'nullable|string',
            'html' => 'nullable|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'status' => 'required|in:draft,published',
            'template' => 'nullable|string|max:50',
        ]);

        $slug = $validated['slug'] ?: Page::generateSlug($validated['title']);
        $content = $validated['content'] ? json_decode($validated['content'], true) : null;

        $page = Page::create([
            'title' => $validated['title'],
            'slug' => $slug,
            'content' => $content,
            'html' => $validated['html'] ?? null,
            'meta_title' => $validated['meta_title'],
            'meta_description' => $validated['meta_description'],
            'status' => $validated['status'],
            'template' => $validated['template'] ?? 'default',
            'created_by' => auth()->id(),
            'published_at' => $validated['status'] === 'published' ? now() : null,
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Page created successfully.',
                'page' => $page,
            ]);
        }

        return redirect()
            ->route('admin.pages.index')
            ->with('success', 'Page created successfully.');
    }

    public function edit(Page $page)
    {
        return view('admin.pages.edit', compact('page'));
    }

    public function update(Request $request, Page $page)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:pages,slug,' . $page->id,
            'content' => 'nullable|string',
            'html' => 'nullable|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'status' => 'required|in:draft,published',
            'template' => 'nullable|string|max:50',
        ]);

        $slug = $validated['slug'] ?: Page::generateSlug($validated['title']);
        $content = $validated['content'] ? json_decode($validated['content'], true) : null;

        $wasPublished = $page->isPublished();
        $isNowPublished = $validated['status'] === 'published';

        $page->update([
            'title' => $validated['title'],
            'slug' => $slug,
            'content' => $content,
            'html' => $validated['html'] ?? null,
            'meta_title' => $validated['meta_title'],
            'meta_description' => $validated['meta_description'],
            'status' => $validated['status'],
            'template' => $validated['template'] ?? 'default',
            'published_at' => !$wasPublished && $isNowPublished ? now() : $page->published_at,
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Page updated successfully.',
                'page' => $page,
            ]);
        }

        return redirect()
            ->route('admin.pages.index')
            ->with('success', 'Page updated successfully.');
    }

    public function destroy(Page $page)
    {
        $page->delete();

        return redirect()
            ->route('admin.pages.index')
            ->with('success', 'Page deleted successfully.');
    }

    public function duplicate(Page $page)
    {
        $newPage = $page->replicate(['published_at']);
        $newPage->title = $page->title . ' (Copy)';
        $newPage->slug = Page::generateSlug($newPage->title);
        $newPage->status = 'draft';
        $newPage->created_by = auth()->id();
        $newPage->save();

        return redirect()
            ->route('admin.pages.edit', $newPage)
            ->with('success', 'Page duplicated successfully.');
    }

    public function uploadImage(Request $request)
    {
        $request->validate([
            'image' => 'required|image|max:5120',
        ]);

        $path = $request->file('image')->store('pages', 'public');
        $url = Storage::url($path);

        // GrapesJS asset manager format
        return response()->json([
            'data' => [$url],
        ]);
    }
}
