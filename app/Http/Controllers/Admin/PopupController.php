<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Popup;
use App\Models\LeadMagnet;
use Illuminate\Http\Request;

class PopupController extends Controller
{
    public function index()
    {
        $popups = Popup::withCount(['interactions as views' => fn($q) => $q->where('interaction_type', 'view')])
            ->latest()
            ->paginate(15);

        return view('admin.popups.index', compact('popups'));
    }

    public function create()
    {
        $leadMagnets = LeadMagnet::active()->get();
        return view('admin.popups.create', compact('leadMagnets'));
    }

    public function store(Request $request)
    {
        $validated = $this->validatePopup($request);

        $popup = Popup::create([
            'name' => $validated['name'],
            'slug' => $validated['slug'] ?: \Str::slug($validated['name']),
            'headline' => $validated['headline'],
            'body' => $validated['body'],
            'image' => $validated['image'],
            'button_text' => $validated['button_text'] ?? 'Subscribe',
            'success_message' => $validated['success_message'] ?? 'Thanks for subscribing!',
            'success_redirect_url' => $validated['success_redirect_url'],
            'form_fields' => $validated['form_fields'] ?? $this->defaultFormFields(),
            'triggers' => $validated['triggers'] ?? $this->defaultTriggers(),
            'targeting' => $validated['targeting'] ?? [],
            'display_rules' => $validated['display_rules'] ?? $this->defaultDisplayRules(),
            'design' => $validated['design'] ?? $this->defaultDesign(),
            'klaviyo_list_id' => $validated['klaviyo_list_id'],
            'lead_magnet_id' => $validated['lead_magnet_id'],
            'is_active' => $validated['is_active'] ?? false,
        ]);

        return redirect()->route('admin.popups.edit', $popup)->with('success', 'Popup created.');
    }

    public function edit(Popup $popup)
    {
        $leadMagnets = LeadMagnet::active()->get();
        return view('admin.popups.edit', compact('popup', 'leadMagnets'));
    }

    public function update(Request $request, Popup $popup)
    {
        $validated = $this->validatePopup($request, $popup->id);

        $popup->update([
            'name' => $validated['name'],
            'slug' => $validated['slug'] ?: \Str::slug($validated['name']),
            'headline' => $validated['headline'],
            'body' => $validated['body'],
            'image' => $validated['image'],
            'button_text' => $validated['button_text'],
            'success_message' => $validated['success_message'],
            'success_redirect_url' => $validated['success_redirect_url'],
            'form_fields' => $validated['form_fields'],
            'triggers' => $validated['triggers'],
            'targeting' => $validated['targeting'],
            'display_rules' => $validated['display_rules'],
            'design' => $validated['design'],
            'klaviyo_list_id' => $validated['klaviyo_list_id'],
            'lead_magnet_id' => $validated['lead_magnet_id'],
            'is_active' => $validated['is_active'] ?? false,
            'starts_at' => $validated['starts_at'],
            'ends_at' => $validated['ends_at'],
        ]);

        return back()->with('success', 'Popup updated.');
    }

    public function destroy(Popup $popup)
    {
        $popup->delete();
        return redirect()->route('admin.popups.index')->with('success', 'Popup deleted.');
    }

    public function duplicate(Popup $popup)
    {
        $newPopup = $popup->replicate();
        $newPopup->name = $popup->name . ' (Copy)';
        $newPopup->slug = \Str::slug($newPopup->name);
        $newPopup->is_active = false;
        $newPopup->views_count = 0;
        $newPopup->conversions_count = 0;
        $newPopup->dismissals_count = 0;
        $newPopup->save();

        return redirect()->route('admin.popups.edit', $newPopup)->with('success', 'Popup duplicated.');
    }

    protected function validatePopup(Request $request, ?int $id = null): array
    {
        return $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:popups,slug' . ($id ? ",$id" : ''),
            'headline' => 'required|string|max:255',
            'body' => 'nullable|string',
            'image' => 'nullable|string',
            'button_text' => 'nullable|string|max:100',
            'success_message' => 'nullable|string|max:255',
            'success_redirect_url' => 'nullable|url',
            'form_fields' => 'nullable|array',
            'triggers' => 'nullable|array',
            'targeting' => 'nullable|array',
            'display_rules' => 'nullable|array',
            'design' => 'nullable|array',
            'klaviyo_list_id' => 'nullable|string',
            'lead_magnet_id' => 'nullable|exists:lead_magnets,id',
            'is_active' => 'boolean',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date|after:starts_at',
        ]);
    }

    protected function defaultFormFields(): array
    {
        return [['name' => 'email', 'type' => 'email', 'required' => true, 'placeholder' => 'Enter your email']];
    }

    protected function defaultTriggers(): array
    {
        return ['time_delay' => 15, 'exit_intent' => true];
    }

    protected function defaultDisplayRules(): array
    {
        return ['show_once_per_hours' => 24, 'max_shows_total' => 3, 'hide_if_subscribed' => true];
    }

    protected function defaultDesign(): array
    {
        return ['position' => 'center', 'size' => 'medium', 'animation' => 'fade', 'overlay' => true];
    }
}
