<form action="{{ $link ? route('admin.outbound-links.update', $link) : route('admin.outbound-links.store') }}" method="POST" class="space-y-6">
    @csrf
    @if($link) @method('PUT') @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="card p-6">
                <h3 class="text-lg font-semibold mb-4">Link Details</h3>
                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                            <input type="text" name="name" value="{{ old('name', $link?->name) }}" required
                                class="w-full rounded-lg border-gray-300 focus:border-brand-gold focus:ring-brand-gold">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Slug</label>
                            <input type="text" name="slug" value="{{ old('slug', $link?->slug) }}"
                                class="w-full rounded-lg border-gray-300 focus:border-brand-gold focus:ring-brand-gold">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Destination URL</label>
                        <input type="url" name="destination_url" value="{{ old('destination_url', $link?->destination_url) }}" required
                            placeholder="https://fastpeptix.com/product/..."
                            class="w-full rounded-lg border-gray-300 focus:border-brand-gold focus:ring-brand-gold">
                    </div>
                                    </div>
            </div>

            <div class="card p-6">
                <h3 class="text-lg font-semibold mb-4">UTM Parameters</h3>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">UTM Source</label>
                        <input type="text" name="utm_source" value="{{ old('utm_source', $link?->utm_source ?? 'professorpeptides') }}"
                            class="w-full rounded-lg border-gray-300 focus:border-brand-gold focus:ring-brand-gold">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">UTM Medium</label>
                        <input type="text" name="utm_medium" value="{{ old('utm_medium', $link?->utm_medium ?? 'referral') }}"
                            class="w-full rounded-lg border-gray-300 focus:border-brand-gold focus:ring-brand-gold">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">UTM Campaign</label>
                        <input type="text" name="utm_campaign" value="{{ old('utm_campaign', $link?->utm_campaign) }}"
                            class="w-full rounded-lg border-gray-300 focus:border-brand-gold focus:ring-brand-gold">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">UTM Content</label>
                        <input type="text" name="utm_content" value="{{ old('utm_content', $link?->utm_content) }}"
                            class="w-full rounded-lg border-gray-300 focus:border-brand-gold focus:ring-brand-gold">
                    </div>
                </div>
            </div>

            <div class="card p-6">
                <h3 class="text-lg font-semibold mb-4">Cross-Domain Tracking Data</h3>
                <p class="text-sm text-gray-500 mb-4">Select what data to pass to Fast Peptix for attribution.</p>
                <div class="grid grid-cols-2 gap-4">
                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="append_session" value="1" {{ ($link?->append_session ?? true) ? 'checked' : '' }}
                            class="rounded border-gray-300 text-brand-gold focus:ring-brand-gold">
                        <span class="text-sm">Session ID (pp_session)</span>
                    </label>
                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="append_segment" value="1" {{ ($link?->append_segment ?? true) ? 'checked' : '' }}
                            class="rounded border-gray-300 text-brand-gold focus:ring-brand-gold">
                        <span class="text-sm">Segment (pp_segment)</span>
                    </label>
                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="append_email" value="1" {{ ($link?->append_email ?? true) ? 'checked' : '' }}
                            class="rounded border-gray-300 text-brand-gold focus:ring-brand-gold">
                        <span class="text-sm">Email Hash (pp_email_hash)</span>
                    </label>
                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="append_quiz_data" value="1" {{ ($link?->append_quiz_data ?? true) ? 'checked' : '' }}
                            class="rounded border-gray-300 text-brand-gold focus:ring-brand-gold">
                        <span class="text-sm">Quiz Data (health_goal, etc.)</span>
                    </label>
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="card p-6">
                <h3 class="text-lg font-semibold mb-4">Status</h3>
                <label class="flex items-center gap-2">
                    <input type="checkbox" name="is_active" value="1" {{ ($link?->is_active ?? true) ? 'checked' : '' }}
                        class="rounded border-gray-300 text-brand-gold focus:ring-brand-gold">
                    <span>Active</span>
                </label>
            </div>

            @if($link)
                <div class="card p-6">
                    <h3 class="text-lg font-semibold mb-4">Tracking URL</h3>
                    <div class="bg-gray-50 rounded-lg p-3 text-sm font-mono break-all">{{ $link->getTrackingUrl() }}</div>
                    <button type="button" onclick="navigator.clipboard.writeText('{{ $link->getTrackingUrl() }}')"
                        class="mt-2 text-sm text-brand-gold hover:underline">Copy URL</button>
                </div>

                <div class="card p-6">
                    <h3 class="text-lg font-semibold mb-4">Stats</h3>
                    <dl class="space-y-2">
                        <div class="flex justify-between">
                            <dt class="text-gray-600">Total Clicks</dt>
                            <dd class="font-medium">{{ number_format($link->click_count) }}</dd>
                        </div>
                    </dl>
                </div>
            @endif

            <button type="submit" class="w-full btn btn-primary">{{ $link ? 'Update' : 'Create' }}</button>

            @if($link)
                <form action="{{ route('admin.outbound-links.destroy', $link) }}" method="POST"
                    onsubmit="return confirm('Delete this link?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="w-full btn bg-red-500 text-white hover:bg-red-600">Delete</button>
                </form>
            @endif
        </div>
    </div>
</form>
