<form action="{{ $leadMagnet ? route('admin.lead-magnets.update', $leadMagnet) : route('admin.lead-magnets.store') }}"
    method="POST" enctype="multipart/form-data" class="space-y-6">
    @csrf
    @if($leadMagnet) @method('PUT') @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="card p-6">
                <h3 class="text-lg font-semibold mb-4">Basic Info</h3>
                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                            <input type="text" name="name" value="{{ old('name', $leadMagnet?->name) }}" required
                                class="w-full rounded-lg border-gray-300 focus:border-brand-gold focus:ring-brand-gold">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Slug</label>
                            <input type="text" name="slug" value="{{ old('slug', $leadMagnet?->slug) }}"
                                class="w-full rounded-lg border-gray-300 focus:border-brand-gold focus:ring-brand-gold">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                        <textarea name="description" rows="2" class="w-full rounded-lg border-gray-300 focus:border-brand-gold focus:ring-brand-gold">{{ old('description', $leadMagnet?->description) }}</textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">File {{ $leadMagnet ? '(leave empty to keep current)' : '' }}</label>
                        <input type="file" name="file" {{ !$leadMagnet ? 'required' : '' }}
                            class="w-full border border-gray-300 rounded-lg p-2">
                        @if($leadMagnet?->file_path)
                            <p class="text-sm text-gray-500 mt-1">Current: {{ $leadMagnet->file_name }} ({{ $leadMagnet->getFileSizeFormatted() }})</p>
                        @endif
                    </div>
                </div>
            </div>

            <div class="card p-6">
                <h3 class="text-lg font-semibold mb-4">Landing Page Content</h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Headline</label>
                        <input type="text" name="landing_headline" value="{{ old('landing_headline', $leadMagnet?->landing_headline) }}"
                            class="w-full rounded-lg border-gray-300 focus:border-brand-gold focus:ring-brand-gold">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                        <textarea name="landing_description" rows="3" class="w-full rounded-lg border-gray-300 focus:border-brand-gold focus:ring-brand-gold">{{ old('landing_description', $leadMagnet?->landing_description) }}</textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Download Button Text</label>
                        <input type="text" name="download_button_text" value="{{ old('download_button_text', $leadMagnet?->download_button_text ?? 'Download Now') }}"
                            class="w-full rounded-lg border-gray-300 focus:border-brand-gold focus:ring-brand-gold">
                    </div>
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="card p-6">
                <h3 class="text-lg font-semibold mb-4">Settings</h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Segment</label>
                        <select name="segment" class="w-full rounded-lg border-gray-300 focus:border-brand-gold focus:ring-brand-gold">
                            <option value="all" {{ ($leadMagnet?->segment ?? 'all') === 'all' ? 'selected' : '' }}>All</option>
                            <option value="tof" {{ $leadMagnet?->segment === 'tof' ? 'selected' : '' }}>TOF</option>
                            <option value="mof" {{ $leadMagnet?->segment === 'mof' ? 'selected' : '' }}>MOF</option>
                            <option value="bof" {{ $leadMagnet?->segment === 'bof' ? 'selected' : '' }}>BOF</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Delivery Method</label>
                        <select name="delivery_method" class="w-full rounded-lg border-gray-300 focus:border-brand-gold focus:ring-brand-gold">
                            <option value="email" {{ ($leadMagnet?->delivery_method ?? 'email') === 'email' ? 'selected' : '' }}>Email</option>
                            <option value="instant" {{ $leadMagnet?->delivery_method === 'instant' ? 'selected' : '' }}>Instant Download</option>
                        </select>
                    </div>
                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="is_active" value="1" {{ ($leadMagnet?->is_active ?? true) ? 'checked' : '' }}
                            class="rounded border-gray-300 text-brand-gold focus:ring-brand-gold">
                        <span>Active</span>
                    </label>
                </div>
            </div>

            <div class="card p-6">
                <h3 class="text-lg font-semibold mb-4">Klaviyo</h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Event Name</label>
                        <input type="text" name="klaviyo_event" value="{{ old('klaviyo_event', $leadMagnet?->klaviyo_event ?? 'Downloaded Lead Magnet') }}"
                            class="w-full rounded-lg border-gray-300 focus:border-brand-gold focus:ring-brand-gold">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Property Name</label>
                        <input type="text" name="klaviyo_property_name" value="{{ old('klaviyo_property_name', $leadMagnet?->klaviyo_property_name) }}"
                            placeholder="e.g. pp_downloaded_guide"
                            class="w-full rounded-lg border-gray-300 focus:border-brand-gold focus:ring-brand-gold">
                    </div>
                </div>
            </div>

            <button type="submit" class="w-full btn btn-primary">{{ $leadMagnet ? 'Update' : 'Create' }}</button>

            @if($leadMagnet)
                <form action="{{ route('admin.lead-magnets.destroy', $leadMagnet) }}" method="POST"
                    onsubmit="return confirm('Delete this lead magnet?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="w-full btn bg-red-500 text-white hover:bg-red-600">Delete</button>
                </form>
            @endif
        </div>
    </div>
</form>
