<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LeadMagnet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LeadMagnetController extends Controller
{
    public function index()
    {
        $leadMagnets = LeadMagnet::withCount('downloads')
            ->latest()
            ->paginate(15);

        return view('admin.lead-magnets.index', compact('leadMagnets'));
    }

    public function create()
    {
        return view('admin.lead-magnets.create');
    }

    public function store(Request $request)
    {
        $validated = $this->validateLeadMagnet($request);

        $filePath = null;
        $fileName = null;
        $fileSize = null;

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filePath = $file->store('lead-magnets', 'public');
            $fileName = $file->getClientOriginalName();
            $fileSize = $file->getSize();
        }

        $leadMagnet = LeadMagnet::create([
            'name' => $validated['name'],
            'slug' => $validated['slug'] ?: \Str::slug($validated['name']),
            'description' => $validated['description'],
            'file_path' => $filePath,
            'file_name' => $fileName ?? $validated['name'],
            'file_type' => $validated['file_type'] ?? 'pdf',
            'file_size' => $fileSize,
            'thumbnail' => $validated['thumbnail'],
            'segment' => $validated['segment'] ?? 'all',
            'delivery_method' => $validated['delivery_method'] ?? 'email',
            'download_button_text' => $validated['download_button_text'] ?? 'Download Now',
            'landing_headline' => $validated['landing_headline'],
            'landing_description' => $validated['landing_description'],
            'landing_benefits' => $validated['landing_benefits'] ?? [],
            'klaviyo_flow_id' => $validated['klaviyo_flow_id'],
            'klaviyo_event' => $validated['klaviyo_event'] ?? 'Downloaded Lead Magnet',
            'klaviyo_property_name' => $validated['klaviyo_property_name'],
            'is_active' => $validated['is_active'] ?? true,
        ]);

        return redirect()->route('admin.lead-magnets.edit', $leadMagnet)->with('success', 'Lead magnet created.');
    }

    public function edit(LeadMagnet $leadMagnet)
    {
        return view('admin.lead-magnets.edit', compact('leadMagnet'));
    }

    public function update(Request $request, LeadMagnet $leadMagnet)
    {
        $validated = $this->validateLeadMagnet($request, $leadMagnet->id);

        $data = [
            'name' => $validated['name'],
            'slug' => $validated['slug'] ?: \Str::slug($validated['name']),
            'description' => $validated['description'],
            'file_type' => $validated['file_type'],
            'thumbnail' => $validated['thumbnail'],
            'segment' => $validated['segment'],
            'delivery_method' => $validated['delivery_method'],
            'download_button_text' => $validated['download_button_text'],
            'landing_headline' => $validated['landing_headline'],
            'landing_description' => $validated['landing_description'],
            'landing_benefits' => $validated['landing_benefits'] ?? [],
            'klaviyo_flow_id' => $validated['klaviyo_flow_id'],
            'klaviyo_event' => $validated['klaviyo_event'],
            'klaviyo_property_name' => $validated['klaviyo_property_name'],
            'is_active' => $validated['is_active'] ?? false,
        ];

        if ($request->hasFile('file')) {
            // Delete old file
            if ($leadMagnet->file_path) {
                Storage::disk('public')->delete($leadMagnet->file_path);
            }

            $file = $request->file('file');
            $data['file_path'] = $file->store('lead-magnets', 'public');
            $data['file_name'] = $file->getClientOriginalName();
            $data['file_size'] = $file->getSize();
        }

        $leadMagnet->update($data);

        return back()->with('success', 'Lead magnet updated.');
    }

    public function destroy(LeadMagnet $leadMagnet)
    {
        if ($leadMagnet->file_path) {
            Storage::disk('public')->delete($leadMagnet->file_path);
        }
        $leadMagnet->delete();

        return redirect()->route('admin.lead-magnets.index')->with('success', 'Lead magnet deleted.');
    }

    public function duplicate(LeadMagnet $leadMagnet)
    {
        $newLeadMagnet = $leadMagnet->replicate();
        $newLeadMagnet->name = $leadMagnet->name . ' (Copy)';
        $newLeadMagnet->slug = \Str::slug($newLeadMagnet->name);
        $newLeadMagnet->views_count = 0;
        $newLeadMagnet->downloads_count = 0;
        $newLeadMagnet->save();

        return redirect()->route('admin.lead-magnets.edit', $newLeadMagnet)->with('success', 'Lead magnet duplicated.');
    }

    protected function validateLeadMagnet(Request $request, ?int $id = null): array
    {
        return $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:lead_magnets,slug' . ($id ? ",$id" : ''),
            'description' => 'nullable|string',
            'file' => $id ? 'nullable|file|max:51200' : 'required|file|max:51200',
            'file_type' => 'nullable|in:pdf,video,zip',
            'thumbnail' => 'nullable|string',
            'segment' => 'nullable|in:tof,mof,bof,all',
            'delivery_method' => 'nullable|in:instant,email',
            'download_button_text' => 'nullable|string|max:100',
            'landing_headline' => 'nullable|string|max:255',
            'landing_description' => 'nullable|string',
            'landing_benefits' => 'nullable|array',
            'klaviyo_flow_id' => 'nullable|string',
            'klaviyo_event' => 'nullable|string|max:255',
            'klaviyo_property_name' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ]);
    }
}
