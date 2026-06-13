<x-public-layout
    title="Best Peptides by Goal — Ranked Roundups"
    description="Ranked, research-backed roundups of the best peptides for weight loss, muscle growth, healing, anti-aging, sleep, cognition and libido.">

    <section class="bg-surface-100 py-14">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <p class="text-sm font-semibold uppercase tracking-wider text-primary-600 mb-3">Research roundups</p>
            <h1 class="text-3xl md:text-5xl font-bold text-gray-900 mb-4">Best Peptides by <span class="text-primary-500">Goal</span></h1>
            <p class="text-lg text-gray-600 max-w-2xl mx-auto">Ranked by the strength of the published research — not hype. Pick a goal to see the top compounds, how they work, and the honest caveats.</p>
        </div>
    </section>

    <section class="py-12 bg-cream-50">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($goals as $g)
                    <a href="{{ route('peptide-goals.show', $g['slug']) }}"
                       class="group bg-white rounded-2xl border border-gray-200 p-6 flex flex-col hover:shadow-lg hover:-translate-y-0.5 transition-all">
                        <span class="w-12 h-12 rounded-xl flex items-center justify-center text-2xl mb-4" style="background-color: {{ $g['accent'] }}1a;">{{ $g['emoji'] }}</span>
                        <h2 class="text-lg font-bold text-gray-900 leading-tight mb-1">{{ $g['h1'] }}</h2>
                        <p class="text-sm text-gray-600 flex-1">{{ \Illuminate\Support\Str::limit($g['intro'], 110) }}</p>
                        <span class="mt-4 inline-flex items-center gap-1.5 text-sm font-semibold group-hover:gap-2.5 transition-all" style="color: {{ $g['accent'] }};">
                            See the ranking
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </span>
                    </a>
                @endforeach
            </div>
        </div>
    </section>
</x-public-layout>
