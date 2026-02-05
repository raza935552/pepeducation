<form action="{{ $popup ? route('admin.popups.update', $popup) : route('admin.popups.store') }}" method="POST" class="space-y-6">
    @csrf
    @if($popup) @method('PUT') @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <!-- Content -->
            <div class="card p-6">
                <h3 class="text-lg font-semibold mb-4">Popup Content</h3>
                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Name (internal)</label>
                            <input type="text" name="name" value="{{ old('name', $popup?->name) }}" required
                                class="w-full rounded-lg border-gray-300 focus:border-brand-gold focus:ring-brand-gold">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Slug</label>
                            <input type="text" name="slug" value="{{ old('slug', $popup?->slug) }}"
                                class="w-full rounded-lg border-gray-300 focus:border-brand-gold focus:ring-brand-gold">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Headline</label>
                        <input type="text" name="headline" value="{{ old('headline', $popup?->headline) }}" required
                            class="w-full rounded-lg border-gray-300 focus:border-brand-gold focus:ring-brand-gold">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Body Text</label>
                        <textarea name="body" rows="3" class="w-full rounded-lg border-gray-300 focus:border-brand-gold focus:ring-brand-gold">{{ old('body', $popup?->body) }}</textarea>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Button Text</label>
                            <input type="text" name="button_text" value="{{ old('button_text', $popup?->button_text ?? 'Subscribe') }}"
                                class="w-full rounded-lg border-gray-300 focus:border-brand-gold focus:ring-brand-gold">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Success Message</label>
                            <input type="text" name="success_message" value="{{ old('success_message', $popup?->success_message ?? 'Thanks for subscribing!') }}"
                                class="w-full rounded-lg border-gray-300 focus:border-brand-gold focus:ring-brand-gold">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Triggers -->
            <div class="card p-6">
                <h3 class="text-lg font-semibold mb-4">Triggers</h3>
                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Time Delay (seconds)</label>
                        <input type="number" name="triggers[time_delay]" value="{{ old('triggers.time_delay', $popup?->triggers['time_delay'] ?? 15) }}"
                            class="w-full rounded-lg border-gray-300 focus:border-brand-gold focus:ring-brand-gold">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Scroll Depth (%)</label>
                        <input type="number" name="triggers[scroll_depth]" value="{{ old('triggers.scroll_depth', $popup?->triggers['scroll_depth'] ?? '') }}"
                            class="w-full rounded-lg border-gray-300 focus:border-brand-gold focus:ring-brand-gold">
                    </div>
                    <div class="flex items-end">
                        <label class="flex items-center gap-2">
                            <input type="checkbox" name="triggers[exit_intent]" value="1" {{ ($popup?->triggers['exit_intent'] ?? false) ? 'checked' : '' }}
                                class="rounded border-gray-300 text-brand-gold focus:ring-brand-gold">
                            <span class="text-sm">Exit Intent</span>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <div class="card p-6">
                <h3 class="text-lg font-semibold mb-4">Status</h3>
                <label class="flex items-center gap-2">
                    <input type="checkbox" name="is_active" value="1" {{ ($popup?->is_active ?? false) ? 'checked' : '' }}
                        class="rounded border-gray-300 text-brand-gold focus:ring-brand-gold">
                    <span>Active</span>
                </label>
            </div>

            <div class="card p-6">
                <h3 class="text-lg font-semibold mb-4">Lead Magnet</h3>
                <select name="lead_magnet_id" class="w-full rounded-lg border-gray-300 focus:border-brand-gold focus:ring-brand-gold">
                    <option value="">None</option>
                    @foreach($leadMagnets as $lm)
                        <option value="{{ $lm->id }}" {{ ($popup?->lead_magnet_id ?? '') == $lm->id ? 'selected' : '' }}>
                            {{ $lm->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="card p-6">
                <h3 class="text-lg font-semibold mb-4">Klaviyo</h3>
                <input type="text" name="klaviyo_list_id" value="{{ old('klaviyo_list_id', $popup?->klaviyo_list_id) }}"
                    placeholder="List ID" class="w-full rounded-lg border-gray-300 focus:border-brand-gold focus:ring-brand-gold">
            </div>

            <button type="submit" class="w-full btn btn-primary">{{ $popup ? 'Update Popup' : 'Create Popup' }}</button>

            @if($popup)
                <form action="{{ route('admin.popups.destroy', $popup) }}" method="POST"
                    onsubmit="return confirm('Delete this popup?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="w-full btn bg-red-500 text-white hover:bg-red-600">Delete</button>
                </form>
            @endif
        </div>
    </div>
</form>
