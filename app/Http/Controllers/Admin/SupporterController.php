<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Supporter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SupporterController extends Controller
{
    public function index(Request $request)
    {
        $query = Supporter::query();

        if ($request->filled('search')) {
            $search = str_replace(['%', '_'], ['\\%', '\\_'], $request->search);
            $query->where('name', 'like', '%' . $search . '%');
        }

        if ($request->filled('tier')) {
            $query->where('tier', $request->tier);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $supporters = $query->ordered()->paginate(20)->withQueryString();

        return view('admin.supporters.index', compact('supporters'));
    }

    public function create()
    {
        return view('admin.supporters.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'logo' => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:2048',
            'website_url' => 'nullable|url|max:255',
            'tier' => 'required|in:platinum,gold,silver,bronze',
            'is_featured' => 'boolean',
            'display_order' => 'integer|min:0',
            'status' => 'required|in:active,inactive',
        ]);

        if ($request->hasFile('logo')) {
            $validated['logo'] = $request->file('logo')->store('supporters', 'public');
        }

        $validated['is_featured'] = $request->boolean('is_featured');

        Supporter::create($validated);

        return redirect()->route('admin.supporters.index')
            ->with('success', 'Supporter created successfully.');
    }

    public function edit(Supporter $supporter)
    {
        return view('admin.supporters.edit', compact('supporter'));
    }

    public function update(Request $request, Supporter $supporter)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'logo' => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:2048',
            'website_url' => 'nullable|url|max:255',
            'tier' => 'required|in:platinum,gold,silver,bronze',
            'is_featured' => 'boolean',
            'display_order' => 'integer|min:0',
            'status' => 'required|in:active,inactive',
        ]);

        if ($request->hasFile('logo')) {
            if ($supporter->logo) {
                Storage::disk('public')->delete($supporter->logo);
            }
            $validated['logo'] = $request->file('logo')->store('supporters', 'public');
        }

        $validated['is_featured'] = $request->boolean('is_featured');

        $supporter->update($validated);

        return redirect()->route('admin.supporters.index')
            ->with('success', 'Supporter updated successfully.');
    }

    public function destroy(Supporter $supporter)
    {
        if ($supporter->logo) {
            Storage::disk('public')->delete($supporter->logo);
        }

        $supporter->delete();

        return redirect()->route('admin.supporters.index')
            ->with('success', 'Supporter deleted successfully.');
    }
}
