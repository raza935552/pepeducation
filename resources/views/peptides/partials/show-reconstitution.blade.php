<div class="card" x-data="{ calcOpen: false }">
    <h2 class="section-heading-lg">
        <span class="section-icon bg-gradient-to-br from-blue-400 to-blue-600 shadow-blue-500/30">
            <svg aria-hidden="true" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
            </svg>
        </span>
        Reconstitution
    </h2>

    <div class="mb-4">
        <button @click="calcOpen = !calcOpen" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-blue-100 text-blue-700 text-sm font-medium hover:bg-blue-200 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
            </svg>
            <span x-text="calcOpen ? 'Hide Calculator' : 'Reconstitution Calculator'"></span>
            <svg class="w-3 h-3 transition-transform" :class="calcOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </button>
    </div>

    {{-- Inline calculator --}}
    <div x-show="calcOpen" x-collapse x-cloak class="mb-6">
        <div class="rounded-xl border border-blue-200 bg-blue-50 overflow-hidden">
            <div class="p-4">
                @livewire('peptide-calculator')
            </div>
        </div>
    </div>

    @if($peptide->reconstitution_steps && count($peptide->reconstitution_steps))
        <ol class="space-y-3">
            @foreach($peptide->reconstitution_steps as $index => $step)
                <li class="flex items-start gap-4 p-4 rounded-xl bg-blue-50 border border-blue-200">
                    <span class="shrink-0 flex items-center justify-center w-7 h-7 rounded-full bg-gradient-to-br from-blue-400 to-blue-500 text-white text-sm font-bold">
                        {{ $index + 1 }}
                    </span>
                    <span class="text-gray-700 pt-0.5">{{ $step }}</span>
                </li>
            @endforeach
        </ol>
    @endif
</div>
