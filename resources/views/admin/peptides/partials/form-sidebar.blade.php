@php
    $categories = \App\Models\Category::orderBy('name')->get();
    $selectedCategories = old('categories', $peptide?->categories->pluck('id')->toArray() ?? []);
@endphp

<!-- Publish Status -->
<div class="card">
    <h3 class="text-lg font-semibold text-gray-900 mb-4">Status</h3>

    <label class="flex items-center gap-3 cursor-pointer">
        <input type="hidden" name="is_published" value="0">
        <input type="checkbox" name="is_published" value="1"
               {{ old('is_published', $peptide?->is_published) ? 'checked' : '' }}
               class="w-5 h-5 rounded border-gray-300 text-primary-600 focus:ring-primary-500">
        <span class="text-gray-700">Published</span>
    </label>
    <p class="mt-2 text-sm text-gray-500">
        Unpublished peptides will not appear on the public site.
    </p>

    <div class="mt-4 pt-4 border-t border-gray-100">
        <label class="block text-sm font-medium text-gray-700 mb-1">Popularity / best-seller rank</label>
        <input type="number" name="popularity" min="0" step="1"
               value="{{ old('popularity', $peptide?->popularity ?? 0) }}"
               class="input w-full" placeholder="0">
        <p class="mt-1 text-xs text-gray-500">
            Higher = shown first on the homepage &amp; /peptides (best-sellers lead). 0 = alphabetical tail.
        </p>
    </div>
</div>

<!-- BioLinx Product URL -->
<div class="card">
    <div class="flex items-center justify-between mb-3">
        <h3 class="text-lg font-semibold text-gray-900">BioLinx Product URL</h3>
        @php $defaultMap = config('biolinx.product_map', []); @endphp
        @if($peptide && isset($defaultMap[$peptide->slug]))
            <span class="text-[10px] uppercase tracking-wider px-2 py-0.5 rounded-full bg-blue-100 text-blue-700">Default mapped</span>
        @endif
    </div>
    <input type="url" name="biolinx_url"
           value="{{ old('biolinx_url', $peptide?->biolinx_url) }}"
           placeholder="{{ ($peptide && isset($defaultMap[$peptide->slug])) ? $defaultMap[$peptide->slug] : 'https://biolinxlabs.com/products/...' }}"
           class="input w-full text-sm">
    <p class="mt-2 text-xs text-gray-500">
        Direct product URL on BioLinx Labs. When set, the "Available At" buttons across the site link here. Leave blank to use the default mapped URL (if any) or fall back to the BioLinx home page.
    </p>
    @if($peptide?->biolinx_url)
        <a href="{{ $peptide->biolinx_url }}" target="_blank" rel="noopener" class="mt-2 inline-flex items-center gap-1 text-xs text-blue-600 hover:underline">
            Open current URL
            <svg aria-hidden="true" class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
        </a>
    @endif
</div>

<!-- Research Status -->
<div class="card">
    <h3 class="text-lg font-semibold text-gray-900 mb-4">Research Status</h3>

    <select name="research_status" class="input w-full">
        @foreach(['extensive' => 'Extensively Studied', 'well' => 'Well Researched', 'emerging' => 'Emerging Research', 'limited' => 'Limited Research'] as $value => $label)
            <option value="{{ $value }}" {{ old('research_status', $peptide?->research_status ?? 'limited') === $value ? 'selected' : '' }}>
                {{ $label }}
            </option>
        @endforeach
    </select>
</div>

<!-- Categories -->
<div class="card">
    <h3 class="text-lg font-semibold text-gray-900 mb-4">Categories</h3>

    <div class="space-y-2 max-h-64 overflow-y-auto">
        @foreach($categories as $category)
            <label class="flex items-center gap-3 cursor-pointer p-2 rounded hover:bg-gray-50 transition-colors">
                <input type="checkbox" name="categories[]" value="{{ $category->id }}"
                       {{ in_array($category->id, $selectedCategories) ? 'checked' : '' }}
                       class="w-4 h-4 rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                <span class="inline-flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full" style="background-color: {{ $category->color }}"></span>
                    <span class="text-gray-700">{{ $category->name }}</span>
                </span>
            </label>
        @endforeach
    </div>
</div>

<!-- SEO -->
<div class="card" x-data="seoFields()">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-semibold text-gray-900">SEO</h3>
        @if($peptide?->id)
        <button type="button" @click="generateSeo()" :disabled="generating"
            class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium rounded-lg transition"
            :class="generating ? 'bg-gray-100 text-gray-400' : 'bg-purple-600 text-white hover:bg-purple-700'">
            <svg x-show="!generating" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
            <svg x-show="generating" class="animate-spin w-3.5 h-3.5" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
            <span x-text="generating ? 'Generating...' : 'Generate with AI'"></span>
        </button>
        @endif
    </div>

    <p x-show="seoMessage" x-cloak class="mb-3 text-xs p-2 rounded-lg"
        :class="seoSuccess ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-700'" x-text="seoMessage"></p>

    <div class="space-y-4">
        <div>
            <div class="flex justify-between mb-1">
                <label class="block text-sm font-medium text-gray-700">Meta Title</label>
                <span class="text-xs" :class="titleLen > 60 ? 'text-red-500' : (titleLen > 50 ? 'text-yellow-500' : 'text-gray-400')" x-text="titleLen + '/60'"></span>
            </div>
            <input type="text" name="meta_title" x-model="title" x-ref="metaTitle"
                   value="{{ old('meta_title', $peptide?->meta_title) }}"
                   class="input w-full" placeholder="SEO title...">
        </div>

        <div>
            <div class="flex justify-between mb-1">
                <label class="block text-sm font-medium text-gray-700">Meta Description</label>
                <span class="text-xs" :class="descLen > 155 ? 'text-red-500' : (descLen > 140 ? 'text-yellow-500' : 'text-gray-400')" x-text="descLen + '/155'"></span>
            </div>
            <textarea name="meta_description" rows="3" class="input w-full" x-model="desc" x-ref="metaDesc"
                      placeholder="SEO description...">{{ old('meta_description', $peptide?->meta_description) }}</textarea>
        </div>

        {{-- Google Preview --}}
        <div class="p-3 bg-gray-50 rounded-lg border border-gray-200">
            <p class="text-xs text-gray-400 mb-1">Google Preview</p>
            <p class="text-blue-700 text-sm font-medium truncate" x-text="title || '{{ $peptide?->name ?? 'Peptide Name' }}'"></p>
            <p class="text-green-700 text-xs">professorpeptides.co/peptides/{{ $peptide?->slug ?? '...' }}</p>
            <p class="text-gray-600 text-xs mt-0.5 line-clamp-2" x-text="desc || 'Enter a meta description...'"></p>
        </div>
    </div>
</div>

@push('scripts')
<script>
function seoFields() {
    return {
        title: '{{ addslashes(old('meta_title', $peptide?->meta_title ?? '')) }}',
        desc: '{{ addslashes(old('meta_description', $peptide?->meta_description ?? '')) }}',
        generating: false,
        seoMessage: '',
        seoSuccess: false,
        get titleLen() { return this.title.length; },
        get descLen() { return this.desc.length; },
        async generateSeo() {
            this.generating = true;
            this.seoMessage = '';
            try {
                const res = await fetch('{{ route("admin.settings.seo.generate-one") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ peptide_id: {{ $peptide?->id ?? 0 }} }),
                });
                const data = await res.json();
                if (data.success) {
                    this.title = data.meta_title;
                    this.desc = data.meta_description;
                    this.seoMessage = 'SEO generated and saved!';
                    this.seoSuccess = true;
                } else {
                    this.seoMessage = data.error || 'Generation failed.';
                    this.seoSuccess = false;
                }
            } catch (e) {
                this.seoMessage = 'Network error.';
                this.seoSuccess = false;
            }
            this.generating = false;
            setTimeout(() => this.seoMessage = '', 4000);
        }
    };
}
</script>
@endpush

<!-- Peptide guide (PDF) -->
<div class="card">
    <h3 class="text-lg font-semibold text-gray-900 mb-2">Peptide guide (PDF)</h3>
    <p class="text-xs text-gray-500 mb-3">The 10-page reconstitution + dosing guide attached to this peptide.</p>
    @if($peptide?->guide_pdf && file_exists(storage_path('app/'.$peptide->guide_pdf)))
        <div class="flex items-center gap-2 mb-3 p-2.5 rounded-lg bg-green-50 border border-green-200">
            <svg class="w-5 h-5 text-green-600 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 4H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V18a2 2 0 01-2 2z"/></svg>
            <div class="min-w-0 flex-1">
                <a href="{{ route('admin.peptides.guide', $peptide) }}" target="_blank" class="text-sm font-medium text-green-800 hover:underline">View guide PDF</a>
                <p class="text-[11px] text-green-700">Private · updated {{ optional($peptide->guide_updated_at)->diffForHumans() ?? 'recently' }} · {{ round(filesize(storage_path('app/'.$peptide->guide_pdf))/1024) }} KB</p>
            </div>
        </div>
    @else
        <p class="text-xs text-gray-400 mb-3">No guide attached yet.</p>
    @endif
    <label class="block text-sm font-medium text-gray-700 mb-1">Replace / upload PDF</label>
    <input type="file" name="guide_pdf_file" accept="application/pdf" class="block w-full text-sm text-gray-600 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100">
    <p class="mt-1 text-[11px] text-gray-400">PDF only, max 20 MB. Regenerate the whole set with <code>node guides/build.js</code>.</p>
</div>

