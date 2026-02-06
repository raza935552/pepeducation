<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MediaController extends Controller
{
    public function index()
    {
        $disk = Storage::disk('public');

        // Ensure directory exists
        if (!$disk->exists('pages')) {
            return response()->json(['media' => []]);
        }

        $files = $disk->files('pages');

        $media = collect($files)
            ->filter(fn($f) => preg_match('/\.(jpg|jpeg|png|webp|gif|svg)$/i', $f))
            ->map(fn($f) => [
                'path' => $f,
                'url' => Storage::url($f),
                'name' => basename($f),
                'size' => $disk->size($f),
                'modified' => $disk->lastModified($f),
            ])
            ->sortByDesc('modified')
            ->values();

        return response()->json(['media' => $media]);
    }

    public function destroy(Request $request)
    {
        $request->validate(['path' => 'required|string|max:255']);

        $path = $request->input('path');

        // Security: only allow files in pages/ directory, no path traversal
        if (!str_starts_with($path, 'pages/') || str_contains($path, '..')) {
            return response()->json(['error' => 'Invalid path'], 403);
        }

        $disk = Storage::disk('public');

        if ($disk->exists($path)) {
            $disk->delete($path);
            return response()->json(['ok' => true]);
        }

        return response()->json(['error' => 'File not found'], 404);
    }
}
