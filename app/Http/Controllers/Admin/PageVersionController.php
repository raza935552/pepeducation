<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Models\PageVersion;
use Illuminate\Http\JsonResponse;

class PageVersionController extends Controller
{
    public function index(Page $page): JsonResponse
    {
        $versions = $page->versions()
            ->with('author:id,name')
            ->select('id', 'page_id', 'version', 'title', 'created_by', 'created_at')
            ->limit(50)
            ->get();

        return response()->json(['versions' => $versions]);
    }

    public function restore(Page $page, PageVersion $version): JsonResponse
    {
        abort_unless($version->page_id === $page->id, 404);

        // Save current state as a new version before restoring
        self::createVersion($page);

        $page->update([
            'title' => $version->title,
            'content' => $version->content,
            'html' => $version->html,
        ]);

        return response()->json(['success' => true, 'message' => "Restored to v{$version->version}"]);
    }

    public static function createVersion(Page $page): PageVersion
    {
        $lastVersion = PageVersion::where('page_id', $page->id)->max('version') ?? 0;

        return PageVersion::create([
            'page_id' => $page->id,
            'version' => $lastVersion + 1,
            'title' => $page->title,
            'content' => $page->getRawOriginal('content'),
            'html' => $page->html,
            'created_by' => auth()->id(),
            'created_at' => now(),
        ]);
    }
}
