{{-- Homepage FAQ — targets head informational queries + emits FAQPage schema --}}
@php
    $homeFaqs = [
        ['q' => 'What are peptides?', 'a' => 'Peptides are short chains of amino acids — the building blocks of proteins. In research they are studied as signalling molecules that can influence processes like tissue repair, metabolism, growth-hormone release and appetite. Professor Peptides catalogues '.($stats['peptides'] ?? '68').'+ of them with evidence-based guides.'],
        ['q' => 'Are peptides legal?', 'a' => 'Most research peptides are sold legally for laboratory and research use, but are not approved for human consumption. Legality and approval status vary by compound and jurisdiction — always check the specific peptide and your local law. Nothing on this site is medical advice.'],
        ['q' => 'How do you reconstitute and dose peptides?', 'a' => 'Lyophilized (freeze-dried) peptides are reconstituted with bacteriostatic water, then drawn on an insulin syringe. Our free reconstitution and per-peptide dosage calculators convert your vial size and target dose into the exact units to draw.'],
        ['q' => 'Which peptides are best for weight loss, muscle or recovery?', 'a' => 'It depends on the goal — GLP-1 peptides (semaglutide, tirzepatide) dominate weight-loss research, growth-hormone secretagogues support muscle research, and BPC-157/TB-500 lead recovery research. See our "Best Peptides by Goal" roundups for ranked, research-backed lists.'],
        ['q' => 'Is Professor Peptides free?', 'a' => 'Yes. All guides, calculators, comparisons and tools are free. We are an educational resource — everything is for research and informational purposes only, not medical advice.'],
    ];
@endphp

@push('head')
<script type="application/ld+json">
{!! json_encode(['@context' => 'https://schema.org', '@type' => 'FAQPage', 'mainEntity' => collect($homeFaqs)->map(fn ($f) => ['@type' => 'Question', 'name' => $f['q'], 'acceptedAnswer' => ['@type' => 'Answer', 'text' => $f['a']]])->values()->all()], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
</script>
@endpush

<section class="py-14 lg:py-20 bg-surface-50 border-t border-surface-200">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-3xl sm:text-4xl font-bold text-heading text-center mb-8">Peptides — Frequently Asked Questions</h2>
        <div class="space-y-3" x-data="{ open: 0 }">
            @foreach($homeFaqs as $i => $faq)
                <div class="bg-white rounded-xl border border-surface-200 overflow-hidden">
                    <button type="button" @click="open === {{ $i }} ? open = null : open = {{ $i }}" class="w-full flex items-center justify-between gap-4 text-left px-5 py-4 font-semibold text-heading">
                        <span>{{ $faq['q'] }}</span>
                        <svg class="w-5 h-5 text-gray-400 shrink-0 transition-transform" :class="open === {{ $i }} && 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="open === {{ $i }}" x-transition.opacity class="px-5 pb-4 -mt-1 text-body/70 leading-relaxed">{{ $faq['a'] }}</div>
                </div>
            @endforeach
        </div>
    </div>
</section>
