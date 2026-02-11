<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use App\Models\BlogPostVersion;
use Illuminate\Http\JsonResponse;

class BlogPostVersionController extends Controller
{
    public function index(BlogPost $blogPost): JsonResponse
    {
        $versions = $blogPost->versions()
            ->with('author:id,name')
            ->select('id', 'blog_post_id', 'version', 'title', 'created_by', 'created_at')
            ->limit(50)
            ->get();

        return response()->json(['versions' => $versions]);
    }

    public function restore(BlogPost $blogPost, BlogPostVersion $version): JsonResponse
    {
        abort_unless($version->blog_post_id === $blogPost->id, 404);

        self::createVersion($blogPost);

        $blogPost->update([
            'title' => $version->title,
            'content' => $version->content,
            'html' => $version->html,
        ]);

        return response()->json(['success' => true, 'message' => "Restored to v{$version->version}"]);
    }

    public static function createVersion(BlogPost $post): BlogPostVersion
    {
        $lastVersion = BlogPostVersion::where('blog_post_id', $post->id)->max('version') ?? 0;

        return BlogPostVersion::create([
            'blog_post_id' => $post->id,
            'version' => $lastVersion + 1,
            'title' => $post->title,
            'content' => $post->getRawOriginal('content'),
            'html' => $post->html,
            'created_by' => auth()->id(),
            'created_at' => now(),
        ]);
    }
}
