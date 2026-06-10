<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <span>Edit lander — {{ $lander->name }}</span>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.landers.preview', $lander) }}" target="_blank" class="text-sm text-gray-500 hover:text-gray-700">Preview ↗</a>
                <a href="{{ route('admin.landers.index') }}" class="text-sm text-gray-500 hover:text-gray-700">← All landers</a>
            </div>
        </div>
    </x-slot>

    @php
        // Small helpers so the form prefills from saved content (old() wins on validation errors).
        $v  = fn ($path, $fallback = '') => old('content.' . $path, $lander->c($path, $fallback));
        $lbl = 'block text-xs font-semibold text-gray-600 mb-1';
        $inp = 'w-full rounded-lg border-gray-300 text-sm';
        $ta  = 'w-full rounded-lg border-gray-300 text-sm';
    @endphp

    @if(session('success'))
        <div class="mb-4 rounded-lg bg-green-50 border border-green-200 text-green-700 px-4 py-3 text-sm">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="mb-4 rounded-lg bg-red-50 border border-red-200 text-red-700 px-4 py-3 text-sm">Please check the fields below.</div>
    @endif

    <form method="POST" action="{{ route('admin.landers.update', $lander) }}" class="space-y-6 max-w-4xl pb-16">
        @csrf @method('PUT')

        {{-- Page settings --}}
        <div class="card p-5">
            <h3 class="text-sm font-semibold text-gray-900 mb-4">Page settings</h3>
            <div class="grid sm:grid-cols-2 gap-4">
                <div><label class="{{ $lbl }}">Admin name</label><input name="name" value="{{ old('name', $lander->name) }}" class="{{ $inp }}"></div>
                <div class="flex items-end gap-5">
                    <label class="inline-flex items-center gap-2 text-sm"><input type="hidden" name="is_active" value="0"><input type="checkbox" name="is_active" value="1" {{ $lander->is_active ? 'checked' : '' }} class="rounded"> Active (live)</label>
                    <label class="inline-flex items-center gap-2 text-sm"><input type="hidden" name="noindex" value="0"><input type="checkbox" name="noindex" value="1" {{ $lander->noindex ? 'checked' : '' }} class="rounded"> noindex</label>
                </div>
                <div><label class="{{ $lbl }}">SEO title</label><input name="content[meta][title]" value="{{ $v('meta.title') }}" class="{{ $inp }}"></div>
                <div><label class="{{ $lbl }}">SEO description</label><input name="content[meta][description]" value="{{ $v('meta.description') }}" class="{{ $inp }}"></div>
            </div>
        </div>

        {{-- Tracking (UTM on the CTA outbound link) --}}
        <div class="card p-5">
            <h3 class="text-sm font-semibold text-gray-900 mb-1">Tracking (UTM)</h3>
            <p class="text-xs text-gray-400 mb-4">Applied to every Biolinx link on this lander (via <code>/go/{{ $lander->outbound_slug }}</code>).</p>
            <div class="grid sm:grid-cols-4 gap-4">
                <div><label class="{{ $lbl }}">utm_source</label><input name="utm_source" value="{{ old('utm_source', $outbound->utm_source ?? '') }}" class="{{ $inp }}"></div>
                <div><label class="{{ $lbl }}">utm_medium</label><input name="utm_medium" value="{{ old('utm_medium', $outbound->utm_medium ?? '') }}" class="{{ $inp }}"></div>
                <div><label class="{{ $lbl }}">utm_campaign</label><input name="utm_campaign" value="{{ old('utm_campaign', $outbound->utm_campaign ?? '') }}" class="{{ $inp }}"></div>
                <div><label class="{{ $lbl }}">utm_content</label><input name="utm_content" value="{{ old('utm_content', $outbound->utm_content ?? '') }}" class="{{ $inp }}"></div>
            </div>
        </div>

        {{-- Template-specific content fields (one partial per render template). --}}
        @includeIf('admin.landers._fields-' . $lander->template, ['v' => $v, 'lbl' => $lbl, 'inp' => $inp, 'ta' => $ta])

        <div class="sticky bottom-0 bg-white border-t border-gray-200 py-3 flex justify-end">
            <button type="submit" class="btn btn-primary">Save lander</button>
        </div>
    </form>
</x-admin-layout>
