<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OutboundLink;
use Illuminate\Http\Request;

class OutboundLinkController extends Controller
{
    public function index()
    {
        $links = OutboundLink::withCount('clicks')
            ->latest()
            ->paginate(15);

        return view('admin.outbound-links.index', compact('links'));
    }

    public function create()
    {
        return view('admin.outbound-links.create');
    }

    public function store(Request $request)
    {
        $validated = $this->validateLink($request);

        $link = OutboundLink::create([
            'name' => $validated['name'],
            'slug' => $validated['slug'] ?: \Str::slug($validated['name']),
            'destination_url' => $validated['destination_url'],
            'utm_source' => $validated['utm_source'] ?? 'professorpeptides',
            'utm_medium' => $validated['utm_medium'] ?? 'referral',
            'utm_campaign' => $validated['utm_campaign'],
            'utm_content' => $validated['utm_content'],
            'append_segment' => $validated['append_segment'] ?? true,
            'append_session' => $validated['append_session'] ?? true,
            'append_email' => $validated['append_email'] ?? true,
            'append_quiz_data' => $validated['append_quiz_data'] ?? true,
            'is_active' => $validated['is_active'] ?? true,
        ]);

        return redirect()->route('admin.outbound-links.edit', $link)->with('success', 'Outbound link created.');
    }

    public function edit(OutboundLink $outboundLink)
    {
        $outboundLink->loadCount('clicks');
        $recentClicks = $outboundLink->clicks()->latest()->limit(10)->get();

        return view('admin.outbound-links.edit', compact('outboundLink', 'recentClicks'));
    }

    public function update(Request $request, OutboundLink $outboundLink)
    {
        $validated = $this->validateLink($request, $outboundLink->id);

        $outboundLink->update([
            'name' => $validated['name'],
            'slug' => $validated['slug'] ?: \Str::slug($validated['name']),
            'destination_url' => $validated['destination_url'],
            'utm_source' => $validated['utm_source'],
            'utm_medium' => $validated['utm_medium'],
            'utm_campaign' => $validated['utm_campaign'],
            'utm_content' => $validated['utm_content'],
            'append_segment' => $validated['append_segment'] ?? false,
            'append_session' => $validated['append_session'] ?? false,
            'append_email' => $validated['append_email'] ?? false,
            'append_quiz_data' => $validated['append_quiz_data'] ?? false,
            'is_active' => $validated['is_active'] ?? false,
        ]);

        return back()->with('success', 'Outbound link updated.');
    }

    public function destroy(OutboundLink $outboundLink)
    {
        $outboundLink->delete();
        return redirect()->route('admin.outbound-links.index')->with('success', 'Outbound link deleted.');
    }

    protected function validateLink(Request $request, ?int $id = null): array
    {
        return $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:outbound_links,slug' . ($id ? ",$id" : ''),
            'destination_url' => 'required|url',
            'utm_source' => 'nullable|string|max:100',
            'utm_medium' => 'nullable|string|max:100',
            'utm_campaign' => 'nullable|string|max:100',
            'utm_content' => 'nullable|string|max:100',
            'append_segment' => 'boolean',
            'append_session' => 'boolean',
            'append_email' => 'boolean',
            'append_quiz_data' => 'boolean',
            'is_active' => 'boolean',
        ]);
    }
}
