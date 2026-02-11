<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\BlogTag;
use App\Models\Peptide;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BlogPostController extends Controller
{
    public function index(Request $request)
    {
        $query = BlogPost::with(['author', 'categories']);

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('excerpt', 'like', "%{$search}%");
            });
        }

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        if ($categoryId = $request->get('category')) {
            $query->whereHas('categories', fn ($q) => $q->where('blog_categories.id', $categoryId));
        }

        $posts = $query->latest()->paginate(15)->withQueryString();
        $categories = BlogCategory::ordered()->get();

        return view('admin.blog-posts.index', compact('posts', 'categories'));
    }

    public function create()
    {
        $categories = BlogCategory::ordered()->get();
        $tags = BlogTag::orderBy('name')->get();
        $peptides = Peptide::published()->orderBy('name')->get();

        return view('admin.blog-posts.create', compact('categories', 'tags', 'peptides'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:blog_posts,slug',
            'content' => 'nullable|string|max:2000000',
            'html' => 'nullable|string|max:2000000',
            'excerpt' => 'nullable|string|max:1000',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'status' => 'required|in:draft,published,scheduled',
            'featured_image' => 'nullable|string|max:500',
            'is_featured' => 'nullable',
            'published_at' => 'nullable|date',
        ]);

        $slug = $validated['slug'] ?: BlogPost::generateSlug($validated['title']);
        $content = $validated['content'] ? json_decode($validated['content'], true) : null;
        $html = $validated['html'] ?? null;

        $post = BlogPost::create([
            'title' => $validated['title'],
            'slug' => $slug,
            'excerpt' => $validated['excerpt'] ?? null,
            'content' => $content,
            'html' => $html,
            'meta_title' => $validated['meta_title'] ?? null,
            'meta_description' => $validated['meta_description'] ?? null,
            'featured_image' => $validated['featured_image'] ?? null,
            'status' => $validated['status'],
            'is_featured' => $request->boolean('is_featured'),
            'reading_time' => BlogPost::estimateReadingTime($html),
            'created_by' => auth()->id(),
            'published_at' => $this->resolvePublishedAt($validated),
        ]);

        $post->categories()->sync($request->get('categories', []));
        $this->syncTagsWithNew($post, $request->get('tags', []), $request->get('new_tags', ''));
        $post->peptides()->sync($request->get('peptides', []));

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Blog post created successfully.',
                'post' => $post,
            ]);
        }

        return redirect()
            ->route('admin.blog-posts.index')
            ->with('success', 'Blog post created successfully.');
    }

    public function edit(BlogPost $blogPost)
    {
        $blogPost->load(['categories', 'tags', 'peptides']);
        $categories = BlogCategory::ordered()->get();
        $tags = BlogTag::orderBy('name')->get();
        $peptides = Peptide::published()->orderBy('name')->get();

        return view('admin.blog-posts.edit', compact('blogPost', 'categories', 'tags', 'peptides'));
    }

    public function update(Request $request, BlogPost $blogPost)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:blog_posts,slug,' . $blogPost->id,
            'content' => 'nullable|string|max:2000000',
            'html' => 'nullable|string|max:2000000',
            'excerpt' => 'nullable|string|max:1000',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'status' => 'required|in:draft,published,scheduled',
            'featured_image' => 'nullable|string|max:500',
            'is_featured' => 'nullable',
            'published_at' => 'nullable|date',
        ]);

        $slug = $validated['slug'] ?: BlogPost::generateSlug($validated['title'], $blogPost->id);
        $content = $validated['content'] ? json_decode($validated['content'], true) : null;
        $html = $validated['html'] ?? null;

        if ($blogPost->content || $blogPost->html) {
            BlogPostVersionController::createVersion($blogPost);
        }

        $wasPublished = $blogPost->isPublished();
        $isNowPublished = $validated['status'] === 'published';

        $blogPost->update([
            'title' => $validated['title'],
            'slug' => $slug,
            'excerpt' => $validated['excerpt'] ?? null,
            'content' => $content,
            'html' => $html,
            'meta_title' => $validated['meta_title'] ?? null,
            'meta_description' => $validated['meta_description'] ?? null,
            'featured_image' => $validated['featured_image'] ?? null,
            'status' => $validated['status'],
            'is_featured' => $request->boolean('is_featured'),
            'reading_time' => BlogPost::estimateReadingTime($html),
            'published_at' => $this->resolvePublishedAt($validated, $blogPost, $wasPublished, $isNowPublished),
        ]);

        $blogPost->categories()->sync($request->get('categories', []));
        $this->syncTagsWithNew($blogPost, $request->get('tags', []), $request->get('new_tags', ''));
        $blogPost->peptides()->sync($request->get('peptides', []));

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Blog post updated successfully.',
                'post' => $blogPost,
            ]);
        }

        return redirect()
            ->route('admin.blog-posts.index')
            ->with('success', 'Blog post updated successfully.');
    }

    public function destroy(BlogPost $blogPost)
    {
        $blogPost->delete();

        return redirect()
            ->route('admin.blog-posts.index')
            ->with('success', 'Blog post deleted successfully.');
    }

    public function duplicate(BlogPost $blogPost)
    {
        $newPost = $blogPost->replicate(['published_at', 'views_count']);
        $newPost->title = $blogPost->title . ' (Copy)';
        $newPost->slug = BlogPost::generateSlug($newPost->title);
        $newPost->status = 'draft';
        $newPost->is_featured = false;
        $newPost->views_count = 0;
        $newPost->created_by = auth()->id();
        $newPost->save();

        $newPost->categories()->sync($blogPost->categories->pluck('id'));
        $newPost->tags()->sync($blogPost->tags->pluck('id'));
        $newPost->peptides()->sync($blogPost->peptides->pluck('id'));

        return redirect()
            ->route('admin.blog-posts.edit', $newPost)
            ->with('success', 'Blog post duplicated successfully.');
    }

    public function toggleFeatured(BlogPost $blogPost)
    {
        $blogPost->update(['is_featured' => !$blogPost->is_featured]);

        return back()->with('success', $blogPost->is_featured ? 'Post marked as featured.' : 'Post removed from featured.');
    }

    public function uploadImage(Request $request)
    {
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

        $name = Str::random(40) . '.' . $file->getClientOriginalExtension();
        $dir = storage_path('app/public/blog');

        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $file->move($dir, $name);
        $url = Storage::disk('public')->url('blog/' . $name);

        return response()->json([
            'data' => [$url],
        ]);
    }

    private function syncTagsWithNew(BlogPost $post, array $tagIds, ?string $newTags): void
    {
        $allTagIds = array_filter($tagIds);

        if ($newTags) {
            $names = array_filter(array_map('trim', explode(',', $newTags)));
            foreach ($names as $name) {
                $tag = BlogTag::firstOrCreate(
                    ['slug' => Str::slug($name)],
                    ['name' => $name]
                );
                $allTagIds[] = $tag->id;
            }
        }

        $post->tags()->sync(array_unique($allTagIds));
    }

    private function resolvePublishedAt(array $validated, ?BlogPost $existing = null, bool $wasPublished = false, bool $isNowPublished = false): mixed
    {
        if ($validated['status'] === 'scheduled' && !empty($validated['published_at'])) {
            return $validated['published_at'];
        }

        if ($validated['status'] === 'published') {
            if ($existing && $wasPublished) {
                return $existing->published_at;
            }
            return now();
        }

        return $existing->published_at ?? null;
    }
}
