@php
    use App\Support\PeptideLinks;
    $dosageUrl = PeptideLinks::dosageUrl($peptide);
    $buyUrl = PeptideLinks::whereToBuyUrl($peptide);
    $goals = PeptideLinks::goalsFor($peptide->slug);
    $comparisons = PeptideLinks::comparisonsFor($peptide);
@endphp

@if($dosageUrl || $buyUrl || $goals || $comparisons)
    <section class="mt-12">
        <div class="bg-surface-50 rounded-2xl border border-surface-200 p-6 sm:p-8">
            <h2 class="text-xl font-bold text-gray-900 mb-1">Tools &amp; guides for {{ $peptide->name }}</h2>
            <p class="text-sm text-gray-500 mb-6">Everything else on the site that relates to {{ $peptide->name }}.</p>

            {{-- Primary tool cards --}}
            @if($dosageUrl || $buyUrl)
                <div class="grid sm:grid-cols-2 gap-3 mb-6">
                    @if($dosageUrl)
                        <a href="{{ $dosageUrl }}" class="flex items-center gap-3 rounded-xl border border-gray-200 bg-white p-4 hover:border-primary-300 hover:shadow-sm transition-all">
                            <span class="w-10 h-10 rounded-lg bg-primary-50 flex items-center justify-center text-xl shrink-0">💉</span>
                            <span class="min-w-0">
                                <span class="block text-sm font-semibold text-gray-900">{{ $peptide->name }} Dosage Calculator</span>
                                <span class="block text-xs text-gray-500">Reconstitution &amp; syringe units</span>
                            </span>
                        </a>
                    @endif
                    @if($buyUrl)
                        <a href="{{ $buyUrl }}" class="flex items-center gap-3 rounded-xl border border-gray-200 bg-white p-4 hover:border-primary-300 hover:shadow-sm transition-all">
                            <span class="w-10 h-10 rounded-lg bg-primary-50 flex items-center justify-center text-xl shrink-0">🛒</span>
                            <span class="min-w-0">
                                <span class="block text-sm font-semibold text-gray-900">Where to Buy {{ $peptide->name }}</span>
                                <span class="block text-xs text-gray-500">Buying guide &amp; vetted source</span>
                            </span>
                        </a>
                    @endif
                </div>
            @endif

            {{-- Goals this peptide ranks in --}}
            @if($goals)
                <div class="mb-5">
                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-400 mb-2">Ranked among the best peptides for</p>
                    <div class="flex flex-wrap gap-2">
                        @foreach($goals as $g)
                            <a href="{{ route('peptide-goals.show', $g['slug']) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-white border border-gray-200 text-sm text-gray-700 hover:border-primary-300 hover:text-primary-600 transition-colors">
                                <span>{{ $g['emoji'] }}</span> {{ $g['h1'] }}
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Comparisons --}}
            @if($comparisons)
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-400 mb-2">Compare {{ $peptide->name }}</p>
                    <div class="flex flex-wrap gap-2">
                        @foreach($comparisons as $c)
                            <a href="{{ $c['url'] }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-white border border-gray-200 text-sm text-gray-700 hover:border-primary-300 hover:text-primary-600 transition-colors">
                                <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                                {{ $c['label'] }}
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </section>
@endif
