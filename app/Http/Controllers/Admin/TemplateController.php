<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PageTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TemplateController extends Controller
{
    public function index()
    {
        $templates = PageTemplate::with('creator')
            ->active()
            ->latest()
            ->get()
            ->groupBy('category');

        return response()->json([
            'templates' => $templates,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'content' => 'required|string',
            'category' => 'required|in:advertorial,listicle,landing,custom',
            'thumbnail' => 'nullable|string', // base64 image
        ]);

        $content = json_decode($validated['content'], true);

        // Process thumbnail if provided
        $thumbnailPath = null;
        if (!empty($validated['thumbnail'])) {
            $thumbnailPath = $this->saveThumbnail($validated['thumbnail']);
        }

        $template = PageTemplate::create([
            'name' => $validated['name'],
            'slug' => PageTemplate::generateSlug($validated['name']),
            'description' => $validated['description'],
            'thumbnail' => $thumbnailPath,
            'content' => $content,
            'category' => $validated['category'],
            'is_system' => false,
            'is_active' => true,
            'created_by' => auth()->id(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Template saved successfully.',
            'template' => $template,
        ]);
    }

    /**
     * Save base64 thumbnail to storage.
     */
    private function saveThumbnail(string $base64): ?string
    {
        // Extract base64 data (handle data URL format)
        if (preg_match('/^data:image\/(\w+);base64,/', $base64, $matches)) {
            $extension = $matches[1];
            $base64 = substr($base64, strpos($base64, ',') + 1);
        } else {
            $extension = 'png';
        }

        // Only allow safe image extensions
        $allowedExtensions = ['png', 'jpg', 'jpeg', 'webp'];
        if (!in_array(strtolower($extension), $allowedExtensions)) {
            return null;
        }

        $imageData = base64_decode($base64);
        if ($imageData === false) {
            return null;
        }

        // Enforce max size: 2MB
        if (strlen($imageData) > 2 * 1024 * 1024) {
            return null;
        }

        // Verify it's actually an image
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->buffer($imageData);
        if (!str_starts_with($mimeType, 'image/')) {
            return null;
        }

        $filename = 'templates/' . Str::uuid() . '.' . $extension;
        Storage::disk('public')->put($filename, $imageData);

        return Storage::url($filename);
    }

    public function show(PageTemplate $template)
    {
        return response()->json([
            'template' => $template,
        ]);
    }

    public function destroy(PageTemplate $template)
    {
        if ($template->is_system) {
            return response()->json([
                'success' => false,
                'message' => 'System templates cannot be deleted.',
            ], 403);
        }

        $template->delete();

        return response()->json([
            'success' => true,
            'message' => 'Template deleted successfully.',
        ]);
    }
}
