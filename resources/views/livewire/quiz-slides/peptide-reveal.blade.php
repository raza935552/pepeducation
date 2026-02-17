{{-- Peptide Reveal Slide --}}
<div class="card p-8" wire:key="slide-peptide-reveal-{{ $currentStep }}">

    @php $result = $this->resultsBankEntry; @endphp

    @if($result)
        {{-- Reveal Header --}}
        <div class="text-center mb-8">
            <div class="w-16 h-16 bg-brand-gold/10 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg aria-hidden="true" class="w-8 h-8 text-brand-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 3.104v5.714a2.25 2.25 0 01-.659 1.591L5 14.5M9.75 3.104c-.251.023-.501.05-.75.082m.75-.082a24.301 24.301 0 014.5 0m0 0v5.714c0 .597.237 1.17.659 1.591L19.8 15.3M14.25 3.104c.251.023.501.05.75.082M19.8 15.3l-1.57.393A9.065 9.065 0 0112 15a9.065 9.065 0 00-6.23.693L5 14.5m14.8.8l1.402 1.402c1.232 1.232.65 3.318-1.067 3.611A48.29 48.29 0 0112 21c-2.773 0-5.491-.235-8.135-.687-1.718-.293-2.3-2.379-1.067-3.61L5 14.5"/>
                </svg>
            </div>
            <p class="text-sm text-brand-gold font-semibold uppercase tracking-wide mb-2">Your Personalized Recommendation</p>
            <h2 class="text-3xl font-bold text-gray-900">{{ $result->peptide_name }}</h2>
        </div>

        {{-- Star Rating --}}
        <div class="flex items-center justify-center gap-2 mb-6">
            <div class="flex items-center">
                @for($i = 1; $i <= 5; $i++)
                    @if($i <= floor($result->star_rating))
                        <svg class="w-6 h-6 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                    @elseif($i - 0.5 <= $result->star_rating)
                        {{-- Half star --}}
                        <svg class="w-6 h-6" viewBox="0 0 20 20">
                            <defs>
                                <linearGradient id="half-{{ $i }}">
                                    <stop offset="50%" stop-color="#FBBF24"/>
                                    <stop offset="50%" stop-color="#D1D5DB"/>
                                </linearGradient>
                            </defs>
                            <path fill="url(#half-{{ $i }})" d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                    @else
                        <svg class="w-6 h-6 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                    @endif
                @endfor
            </div>
            <span class="text-lg font-bold text-gray-900">{{ $result->star_rating }}</span>
            <span class="text-sm text-gray-500">&mdash; {{ $result->rating_label }}</span>
        </div>

        {{-- Description --}}
        @if($result->description)
            <p class="text-gray-600 text-center max-w-lg mx-auto mb-6">{{ $result->description }}</p>
        @endif

        {{-- Benefits --}}
        @if(!empty($result->benefits))
            <div class="bg-green-50 rounded-lg p-6 mb-6 max-w-md mx-auto">
                <h3 class="text-sm font-semibold text-green-800 uppercase tracking-wide mb-3">Key Benefits</h3>
                <ul class="space-y-2">
                    @foreach($result->benefits as $benefit)
                        <li class="flex items-start gap-2">
                            <svg class="w-5 h-5 text-green-500 mt-0.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-green-900 text-sm">{{ $benefit }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Testimonial --}}
        @if($result->testimonial)
            <div class="bg-gray-50 rounded-lg p-6 mb-8 max-w-lg mx-auto">
                <svg class="w-8 h-8 text-gray-300 mb-3" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10H14.017zM0 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151C7.546 6.068 5.983 8.789 5.983 11h4v10H0z"/>
                </svg>
                <p class="text-gray-700 italic mb-3">{{ $result->testimonial }}</p>
                @if($result->testimonial_author)
                    <p class="text-sm text-gray-500 font-medium">&mdash; {{ $result->testimonial_author }}</p>
                @endif
            </div>
        @endif

    @else
        {{-- Fallback if no match found --}}
        <div class="text-center py-8">
            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg aria-hidden="true" class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 3.104v5.714a2.25 2.25 0 01-.659 1.591L5 14.5M9.75 3.104c-.251.023-.501.05-.75.082m.75-.082a24.301 24.301 0 014.5 0m0 0v5.714c0 .597.237 1.17.659 1.591L19.8 15.3M14.25 3.104c.251.023.501.05.75.082"/>
                </svg>
            </div>
            <h2 class="text-2xl font-bold mb-4">Your Peptide Recommendation</h2>
            <p class="text-gray-500 mb-6">We're preparing your personalized recommendation.</p>
        </div>
    @endif

    {{-- CTA: Use linked peptide page if available, otherwise fall back to slug lookup --}}
    @php
        $ctaUrl = $this->currentSlide['cta_url'] ?? null;
        $ctaText = $this->currentSlide['cta_text'] ?? null;

        // If there's a linked StackProduct with a related peptide page, use that
        $stackProduct = $this->stackProduct;
        if ($stackProduct && $stackProduct->relatedPeptide) {
            $ctaUrl = $ctaUrl ?: route('peptides.show', $stackProduct->relatedPeptide->slug);
            $ctaText = $ctaText ?: 'Learn More About ' . ($result->peptide_name ?? 'This Peptide');
        } elseif ($result && $result->peptide_slug) {
            // Fallback: try to link to the peptide page by slug from ResultsBank
            $ctaUrl = $ctaUrl ?: '/peptides/' . $result->peptide_slug;
            $ctaText = $ctaText ?: 'Learn More About ' . $result->peptide_name;
        }
    @endphp
    @if($ctaText)
        <div class="text-center mb-4">
            <a href="{{ $ctaUrl }}"
               class="btn btn-primary inline-block text-lg px-8 py-3">
                {{ $ctaText }}
            </a>
        </div>
    @endif

    {{-- Navigation --}}
    <div class="flex items-center justify-between mt-6">
        @if((($quiz->settings ?? [])['allow_back'] ?? true) && $currentStep > 0)
            <button wire:click="previousStep" class="btn bg-gray-200 text-gray-700 hover:bg-gray-300">
                <svg aria-hidden="true" class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Back
            </button>
        @else
            <div></div>
        @endif

        <button wire:click="advanceSlide" class="btn btn-primary">
            Continue
            <svg aria-hidden="true" class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
        </button>
    </div>
</div>
