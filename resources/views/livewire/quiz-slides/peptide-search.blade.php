{{-- Peptide Search Slide --}}
<div class="card p-8" wire:key="slide-peptide-search-{{ $currentStep }}"
    x-data="{
        search: '',
        selectedPeptide: null,
        peptides: @js($this->peptideSearchData),

        // Levenshtein distance for fuzzy matching
        levenshtein(a, b) {
            const m = a.length, n = b.length;
            const d = Array.from({length: m + 1}, (_, i) => [i]);
            for (let j = 1; j <= n; j++) d[0][j] = j;
            for (let i = 1; i <= m; i++) {
                for (let j = 1; j <= n; j++) {
                    d[i][j] = a[i-1] === b[j-1]
                        ? d[i-1][j-1]
                        : 1 + Math.min(d[i-1][j], d[i][j-1], d[i-1][j-1]);
                }
            }
            return d[m][n];
        },

        get filteredPeptides() {
            const names = Object.keys(this.peptides);
            if (!this.search.trim()) return names;

            const q = this.search.toLowerCase().trim();

            // 1. Exact substring match (fast path)
            const exact = names.filter(name => name.toLowerCase().includes(q));
            if (exact.length > 0) return exact;

            // 2. Fuzzy match: Levenshtein distance on full name or starts-with segments
            const threshold = Math.max(2, Math.floor(q.length * 0.3));
            const fuzzy = names
                .map(name => {
                    const nl = name.toLowerCase();
                    // Check distance on full name
                    let dist = this.levenshtein(q, nl);
                    // Also check if query is a fuzzy prefix (compare to same-length substring)
                    if (q.length < nl.length) {
                        const prefixDist = this.levenshtein(q, nl.substring(0, q.length));
                        dist = Math.min(dist, prefixDist);
                    }
                    return { name, dist };
                })
                .filter(r => r.dist <= threshold)
                .sort((a, b) => a.dist - b.dist)
                .map(r => r.name);

            return fuzzy;
        },

        get hasResults() {
            return this.filteredPeptides.length > 0;
        },

        selectPeptide(name) {
            this.selectedPeptide = name;
            $wire.selectPeptide(name);
        }
    }">

    {{-- Header --}}
    <div class="text-center mb-6">
        <div class="w-14 h-14 bg-teal-50 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-7 h-7 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
        </div>
        @if($this->currentSlide['content_title'] ?? null)
            <h2 class="text-2xl font-bold text-gray-900 mb-2">{{ $this->currentSlide['content_title'] }}</h2>
        @else
            <h2 class="text-2xl font-bold text-gray-900 mb-2">Find Your Peptide</h2>
        @endif
        @if($this->currentSlide['content_body'] ?? null)
            <p class="text-gray-600">{{ $this->currentSlide['content_body'] }}</p>
        @else
            <p class="text-gray-600">Search for any peptide to compare prices across trusted vendors.</p>
        @endif
    </div>

    {{-- Search Input --}}
    <div class="relative mb-6 max-w-md mx-auto">
        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
        </div>
        <input type="text" x-model="search"
            placeholder="Type a peptide name (e.g. BPC-157, Semaglutide)..."
            class="w-full pl-10 pr-4 py-3 rounded-xl border-gray-300 focus:border-teal-500 focus:ring-teal-500 text-sm">
        <button x-show="search" @click="search = ''; selectedPeptide = null" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>

    {{-- Results --}}
    <div class="space-y-3 max-w-lg mx-auto">
        <template x-for="peptideName in filteredPeptides" :key="peptideName">
            <div class="border rounded-xl overflow-hidden transition-all"
                :class="selectedPeptide === peptideName ? 'border-teal-500 ring-2 ring-teal-200 bg-teal-50/30' : 'border-gray-200'">
                {{-- Peptide Row (click to select & advance) --}}
                <button @click="selectPeptide(peptideName)"
                    class="w-full flex items-center justify-between px-4 py-3.5 transition-colors bg-gray-50 hover:bg-teal-50 group">
                    <div class="flex items-center gap-3">
                        <span class="w-8 h-8 rounded-lg flex items-center justify-center transition-colors"
                            :class="peptides[peptideName]?.has_deal ? 'bg-teal-100 group-hover:bg-teal-500' : 'bg-gray-100 group-hover:bg-gray-500'">
                            <svg class="w-4 h-4 group-hover:text-white" :class="peptides[peptideName]?.has_deal ? 'text-teal-600' : 'text-gray-500'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                            </svg>
                        </span>
                        <span class="font-semibold text-gray-900" x-text="peptideName"></span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span x-show="peptides[peptideName]?.has_deal" class="text-xs font-medium text-green-600 bg-green-50 px-2 py-0.5 rounded-full">Best Deal</span>
                        <svg class="w-4 h-4 text-gray-400 group-hover:text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </div>
                </button>
            </div>
        </template>

        {{-- No results + Email Capture --}}
        <div x-show="search && !hasResults" class="text-center py-6">
            <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <p class="text-gray-700 font-medium mb-1">We don't have "<span x-text="search"></span>" yet.</p>
            <p class="text-gray-500 text-sm mb-4">Drop your email and we'll send the best price from a vendor we trust.</p>

            <form wire:submit="submitEmail" class="max-w-sm mx-auto space-y-3">
                <input type="email" wire:model="email"
                    placeholder="Enter your email"
                    autocomplete="email"
                    class="w-full rounded-xl border-gray-300 focus:border-teal-500 focus:ring-teal-500 text-sm py-3 text-center">
                @error('email') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                <button type="submit" wire:loading.attr="disabled"
                    class="w-full btn btn-primary py-3 disabled:opacity-50">
                    <span wire:loading.remove>Notify Me</span>
                    <span wire:loading>Submitting...</span>
                </button>
            </form>
        </div>

        {{-- Empty state --}}
        <div x-show="!search && Object.keys(peptides).length === 0" class="text-center py-8">
            <p class="text-gray-400">No peptide links available yet.</p>
        </div>
    </div>

    {{-- Optional CTA --}}
    @if(!empty($this->currentSlide['cta_text']))
        <div class="text-center mt-8">
            @if(!empty($this->currentSlide['cta_url']))
                <a href="{{ $this->currentSlide['cta_url'] }}" target="_blank" rel="noopener noreferrer"
                    class="btn btn-primary inline-block text-lg px-8 py-3">
                    {{ $this->currentSlide['cta_text'] }}
                </a>
            @else
                <button wire:click="advanceSlide" wire:loading.attr="disabled" wire:loading.class="opacity-50 pointer-events-none" class="btn btn-primary text-lg px-8 py-3">
                    {{ $this->currentSlide['cta_text'] }}
                </button>
            @endif
        </div>
    @endif

    {{-- Navigation --}}
    <div class="flex items-center justify-between mt-6">
        @if((($quiz->settings ?? [])['allow_back'] ?? true) && $currentStep > 0)
            <button wire:click="previousStep" wire:loading.attr="disabled" wire:loading.class="opacity-50 pointer-events-none" class="btn bg-gray-200 text-gray-700 hover:bg-gray-300">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Back
            </button>
        @else
            <div></div>
        @endif

        <button wire:click="advanceSlide" wire:loading.attr="disabled" wire:loading.class="opacity-50 pointer-events-none" class="text-sm text-gray-400 hover:text-gray-600 underline">
            Skip
        </button>
    </div>

    <p class="text-center text-xs text-gray-400 mt-3" x-show="search && hasResults">Click a peptide to select it and continue</p>
</div>
