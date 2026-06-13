{{-- Explore — internal-linking hub to the high-value clusters --}}
@php
    $exploreCards = [
        ['href' => route('peptides.index'), 'emoji' => '📚', 'title' => 'Peptide Database', 'desc' => 'Research-backed guides on '.($stats['peptides'] ?? '68').'+ peptides — mechanisms, benefits, dosing and safety.'],
        ['href' => route('calculators.index'), 'emoji' => '💉', 'title' => 'Peptide Calculators', 'desc' => 'Reconstitution, dosage, BMI, TDEE, GLP-1 and more — instant, free research tools.'],
        ['href' => route('peptide-goals.index'), 'emoji' => '🎯', 'title' => 'Best Peptides by Goal', 'desc' => 'Ranked roundups for weight loss, muscle growth, healing, anti-aging, sleep and focus.'],
        ['href' => route('peptides.compare'), 'emoji' => '⚖️', 'title' => 'Compare Peptides', 'desc' => 'Side-by-side comparisons — semaglutide vs tirzepatide, BPC-157 vs TB-500 and more.'],
        ['href' => url('/peptide-tier-list'), 'emoji' => '🏆', 'title' => 'Peptide Tier List', 'desc' => 'The most-searched peptides of 2026 ranked by the strength of the evidence.'],
        ['href' => route('where-to-buy'), 'emoji' => '🛒', 'title' => 'Where to Buy', 'desc' => 'How to verify purity and COAs, and where to source research-grade peptides.'],
    ];
@endphp
<section class="py-14 lg:py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-2xl mx-auto mb-10">
            <h2 class="text-3xl sm:text-4xl font-bold text-heading mb-3">Explore Professor Peptides</h2>
            <p class="text-body/70">Free, research-backed tools and guides to help you understand peptides — from mechanisms and dosing to sourcing.</p>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach($exploreCards as $card)
                <a href="{{ $card['href'] }}" class="group bg-surface-50 rounded-2xl border border-surface-200 p-6 flex flex-col hover:shadow-lg hover:-translate-y-0.5 hover:border-primary-300 transition-all">
                    <span class="w-12 h-12 rounded-xl bg-white border border-surface-200 flex items-center justify-center text-2xl mb-4">{{ $card['emoji'] }}</span>
                    <h3 class="text-lg font-bold text-heading mb-1.5 group-hover:text-primary-600 transition-colors">{{ $card['title'] }}</h3>
                    <p class="text-sm text-body/70 flex-1">{{ $card['desc'] }}</p>
                    <span class="mt-4 inline-flex items-center gap-1.5 text-sm font-semibold text-primary-600 group-hover:gap-2.5 transition-all">
                        Explore
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </span>
                </a>
            @endforeach
        </div>
    </div>
</section>
