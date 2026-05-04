<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <span>BioLinx URL Manager</span>
            <a href="{{ $homeUrl }}" target="_blank" rel="noopener" class="text-sm text-blue-600 hover:underline">Visit {{ $brand }} -></a>
        </div>
    </x-slot>

    <div class="card p-6 mb-6 border-l-4 border-cyan-400" x-data="biolinxBulk()">
        <h3 class="text-lg font-semibold mb-1">Per-peptide BioLinx product URLs</h3>
        <p class="text-sm text-gray-500 mb-4">
            Pick one peptide at a time, paste the matching <span class="font-mono text-xs">biolinxlabs.com/products/...</span> URL, and click Save. Empty rows fall back to the config default (if mapped) or the {{ $brand }} home page.
        </p>

        <div class="flex flex-wrap items-center gap-3 mb-4">
            <input type="text" x-model="filter" placeholder="Filter peptides..."
                class="flex-1 max-w-sm rounded-lg border-gray-300 focus:border-cyan-500 focus:ring-cyan-500 text-sm">
            <label class="flex items-center gap-2 text-sm text-gray-600">
                <input type="checkbox" x-model="showOnlyMissing" class="rounded border-gray-300 text-cyan-500 focus:ring-cyan-500">
                Only show rows without a custom URL
            </label>
            <span class="ml-auto text-xs text-gray-500">
                <span class="text-green-600 font-semibold" x-text="customCount"></span> custom &middot;
                <span class="text-blue-600 font-semibold" x-text="defaultCount"></span> using default &middot;
                <span class="text-gray-500 font-semibold" x-text="homeCount"></span> -> home
            </span>
        </div>

        <div class="border rounded-lg overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 w-44">Peptide</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500">BioLinx URL</th>
                        <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 w-32">Status</th>
                        <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 w-24">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($peptides as $peptide)
                        @php
                            $defaultUrl = $defaultMap[$peptide->slug] ?? null;
                        @endphp
                        <tr x-data="biolinxRow({
                                peptideId: {{ $peptide->id }},
                                slug: @js($peptide->slug),
                                name: @js($peptide->name),
                                originalUrl: @js($peptide->biolinx_url ?? ''),
                                defaultUrl: @js($defaultUrl ?? ''),
                                isPublished: {{ $peptide->is_published ? 'true' : 'false' }},
                            })"
                            x-show="visible(filter, showOnlyMissing)"
                            x-init="$watch('url', () => dirty = url !== originalUrl)"
                            class="hover:bg-gray-50">
                            <td class="px-3 py-2 align-top">
                                <div class="font-medium text-gray-900 text-sm">{{ $peptide->name }}</div>
                                <div class="text-[11px] text-gray-400 font-mono">{{ $peptide->slug }}</div>
                                @if(!$peptide->is_published)
                                    <span class="inline-block mt-1 text-[10px] uppercase tracking-wider px-1.5 py-0.5 rounded bg-gray-200 text-gray-600">Draft</span>
                                @endif
                            </td>
                            <td class="px-3 py-2 align-top">
                                <input type="url"
                                    x-model="url"
                                    @keydown.enter.prevent="save()"
                                    :placeholder="defaultUrl || 'https://biolinxlabs.com/products/...'"
                                    class="w-full rounded-lg border-gray-300 focus:border-cyan-500 focus:ring-cyan-500 font-mono text-xs">
                                <template x-if="defaultUrl && !url">
                                    <p class="mt-1 text-[11px] text-blue-600">Default: <a :href="defaultUrl" target="_blank" rel="noopener" class="underline" x-text="defaultUrl"></a></p>
                                </template>
                            </td>
                            <td class="px-3 py-2 text-center align-top">
                                <span x-show="url" class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-green-100 text-green-700 text-[11px] font-medium">Custom</span>
                                <span x-show="!url && defaultUrl" class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-blue-100 text-blue-700 text-[11px] font-medium">Default</span>
                                <span x-show="!url && !defaultUrl" class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-gray-100 text-gray-600 text-[11px] font-medium">Home</span>
                            </td>
                            <td class="px-3 py-2 text-right align-top">
                                <button type="button"
                                    @click="save()"
                                    :disabled="!dirty || saving"
                                    :class="dirty && !saving ? 'bg-cyan-600 text-white hover:bg-cyan-700' : 'bg-gray-100 text-gray-400 cursor-not-allowed'"
                                    class="px-3 py-1.5 rounded-md text-xs font-medium transition">
                                    <span x-show="!saving && !saved">Save</span>
                                    <span x-show="saving">...</span>
                                    <span x-show="saved" class="text-white">Saved</span>
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    @push('scripts')
    <script>
    function biolinxBulk() {
        return {
            filter: '',
            showOnlyMissing: false,
            customCount: 0, defaultCount: 0, homeCount: 0,
            init() { this.recount(); },
            recount() {
                const rows = document.querySelectorAll('[data-biolinx-row]');
                let custom = 0, def = 0, home = 0;
                rows.forEach(r => {
                    const url = r.getAttribute('data-url') || '';
                    const dflt = r.getAttribute('data-default') || '';
                    if (url) custom++; else if (dflt) def++; else home++;
                });
                this.customCount = custom; this.defaultCount = def; this.homeCount = home;
            },
        };
    }

    function biolinxRow({ peptideId, slug, name, originalUrl, defaultUrl, isPublished }) {
        return {
            peptideId, slug, name, originalUrl, defaultUrl, isPublished,
            url: originalUrl,
            saving: false, saved: false, dirty: false, error: '',
            visible(filter, onlyMissing) {
                const f = (filter || '').toLowerCase();
                if (f && !this.name.toLowerCase().includes(f) && !this.slug.toLowerCase().includes(f)) return false;
                if (onlyMissing && this.url) return false;
                return true;
            },
            async save() {
                if (!this.dirty || this.saving) return;
                this.saving = true; this.saved = false; this.error = '';
                try {
                    const res = await fetch(`/admin/biolinx-urls/${this.peptideId}`, {
                        method: 'PUT',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({ biolinx_url: this.url || null }),
                    });
                    const data = await res.json();
                    if (data.success) {
                        this.originalUrl = this.url;
                        this.dirty = false;
                        this.saved = true;
                        setTimeout(() => this.saved = false, 1500);
                    } else {
                        this.error = data.message || 'Save failed';
                    }
                } catch (e) {
                    this.error = 'Network error: ' + e.message;
                }
                this.saving = false;
            },
        };
    }
    </script>
    @endpush
</x-admin-layout>
