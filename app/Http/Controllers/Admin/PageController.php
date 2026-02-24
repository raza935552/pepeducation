<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Http\Controllers\Admin\PageVersionController;
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
            'content' => 'nullable|string|max:2000000',
            'html' => 'nullable|string|max:2000000',
            'css' => 'nullable|string|max:500000',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'status' => 'required|in:draft,published',
            'template' => 'nullable|string|max:50',
            'featured_image' => 'nullable|string|max:500',
        ]);

        $slug = $validated['slug'] ?: Page::generateSlug($validated['title']);
        $content = $validated['content'] ? json_decode($validated['content'], true) : null;

        $page = Page::create([
            'title' => $validated['title'],
            'slug' => $slug,
            'content' => $content,
            'html' => $validated['html'] ?? null,
            'css' => $validated['css'] ?? null,
            'meta_title' => $validated['meta_title'],
            'meta_description' => $validated['meta_description'],
            'featured_image' => $validated['featured_image'] ?? null,
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
            'content' => 'nullable|string|max:2000000',
            'html' => 'nullable|string|max:2000000',
            'css' => 'nullable|string|max:500000',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'status' => 'required|in:draft,published',
            'template' => 'nullable|string|max:50',
            'featured_image' => 'nullable|string|max:500',
        ]);

        $slug = $validated['slug'] ?: Page::generateSlug($validated['title']);
        $content = $validated['content'] ? json_decode($validated['content'], true) : null;

        // Auto-create version before updating
        if ($page->content || $page->html) {
            PageVersionController::createVersion($page);
        }

        $wasPublished = $page->isPublished();
        $isNowPublished = $validated['status'] === 'published';

        $page->update([
            'title' => $validated['title'],
            'slug' => $slug,
            'content' => $content,
            'html' => $validated['html'] ?? null,
            'css' => $validated['css'] ?? null,
            'meta_title' => $validated['meta_title'],
            'meta_description' => $validated['meta_description'],
            'featured_image' => $validated['featured_image'] ?? null,
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

    public function createVariant(Page $page)
    {
        $variant = $page->replicate(['published_at']);
        $variant->title = $page->title . ' (Variant)';
        $variant->slug = Page::generateSlug($variant->title);
        $variant->status = 'draft';
        $variant->variant_of = $page->id;
        $variant->variant_weight = 50;
        $variant->created_by = auth()->id();
        $variant->save();

        return redirect()
            ->route('admin.pages.edit', $variant)
            ->with('success', 'A/B variant created. Edit and publish to start testing.');
    }

    public function uploadImage(Request $request)
    {
        // GrapesJS sends file as array (image[]) when multiUpload is true
        $file = $request->file('image');
        if (is_array($file)) {
            $file = $file[0] ?? null;
        }

        if (!$file || !$file->isValid()) {
            return response()->json([
                'errors' => ['image' => ['Please upload a valid image file.']],
            ], 422);
        }

        $allowed = ['jpeg', 'jpg', 'png', 'webp', 'gif'];
        $ext = strtolower($file->getClientOriginalExtension());
        if (!in_array($ext, $allowed) || !in_array($file->getMimeType(), ['image/jpeg', 'image/png', 'image/webp', 'image/gif'])) {
            return response()->json([
                'errors' => ['image' => ['File must be JPG, PNG, WebP, or GIF.']],
            ], 422);
        }

        if ($file->getSize() > 5 * 1024 * 1024) {
            return response()->json([
                'errors' => ['image' => ['Image must be under 5MB.']],
            ], 422);
        }
        $name = \Illuminate\Support\Str::random(40) . '.' . $file->getClientOriginalExtension();

        // Use move instead of store() to avoid getRealPath() issues on Windows
        $disk = Storage::disk('public');
        $dir = storage_path('app/public/pages');

        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $file->move($dir, $name);
        $url = $disk->url('pages/' . $name);

        return response()->json([
            'data' => [$url],
        ]);
    }
}
