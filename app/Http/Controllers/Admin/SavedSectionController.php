<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SavedSection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SavedSectionController extends Controller
{
    public function index(): JsonResponse
    {
        $sections = SavedSection::with('author:id,name')
            ->orderByDesc('created_at')
            ->get(['id', 'name', 'category', 'created_by', 'created_at']);

        return response()->json(['sections' => $sections]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'content' => 'required|array',
            'category' => 'nullable|string|max:50',
        ]);

        $section = SavedSection::create([
            'name' => $validated['name'],
            'content' => $validated['content'],
            'category' => $validated['category'] ?? 'custom',
            'created_by' => auth()->id(),
        ]);

        return response()->json(['success' => true, 'section' => $section]);
    }

    public function show(SavedSection $section): JsonResponse
    {
        return response()->json(['section' => $section]);
    }

    public function destroy(SavedSection $section): JsonResponse
    {
        $section->delete();

        return response()->json(['success' => true]);
    }
}
