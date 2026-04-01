{{-- Go Deeper Section: Action-oriented CTAs --}}
<section class="py-16 lg:py-24 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Section Header --}}
        <div class="text-center mb-12 lg:mb-16">
            <h2 class="text-3xl sm:text-4xl font-bold text-heading uppercase tracking-wide">
                The Best Ways To Go Deeper
            </h2>
        </div>

        {{-- 3-column card grid --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 lg:gap-8">
            {{-- Card 1: Take the Quiz --}}
            <div class="group relative bg-white rounded-xl border border-surface-200 p-6 lg:p-8 border-l-4 border-l-primary-500 hover:shadow-lg hover:border-surface-300 transition-all duration-300">
                <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold bg-primary-50 text-primary-600 mb-4">
                    Personalized
                </span>
                <h3 class="text-xl font-bold text-heading mb-3">
                    Find Your Ideal Peptide
                </h3>
                <p class="text-body/70 mb-6 leading-relaxed">
                    Answer a few questions and get matched with the peptides best suited to your research goals.
                </p>
                <a href="{{ url('/quiz/find-your-peptide') }}"
                   class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full bg-primary-500 text-white font-semibold text-sm hover:bg-primary-600 transition-colors">
                    Take the Quiz
                    <svg aria-hidden="true" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                    </svg>
                </a>
            </div>

            {{-- Card 2: Dosing Calculator --}}
            <div class="group relative bg-white rounded-xl border border-surface-200 p-6 lg:p-8 border-l-4 border-l-primary-500 hover:shadow-lg hover:border-surface-300 transition-all duration-300">
                <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold bg-primary-50 text-primary-600 mb-4">
                    Tools
                </span>
                <h3 class="text-xl font-bold text-heading mb-3">
                    Calculate & Plan
                </h3>
                <p class="text-body/70 mb-6 leading-relaxed">
                    Precise dosing calculations and reconstitution guides for your peptide research protocols.
                </p>
                <a href="{{ route('calculator') }}"
                   class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full bg-primary-500 text-white font-semibold text-sm hover:bg-primary-600 transition-colors">
                    Open Calculator
                    <svg aria-hidden="true" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                    </svg>
                </a>
            </div>

            {{-- Card 3: Stack Builder --}}
            <div class="group relative bg-white rounded-xl border border-surface-200 p-6 lg:p-8 border-l-4 border-l-primary-500 hover:shadow-lg hover:border-surface-300 transition-all duration-300">
                <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold bg-primary-50 text-primary-600 mb-4">
                    Build
                </span>
                <h3 class="text-xl font-bold text-heading mb-3">
                    Build Your Stack
                </h3>
                <p class="text-body/70 mb-6 leading-relaxed">
                    Create your optimal peptide stack based on your research objectives and protocols.
                </p>
                <a href="{{ route('stack-builder') }}"
                   class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full bg-primary-500 text-white font-semibold text-sm hover:bg-primary-600 transition-colors">
                    Start Building
                    <svg aria-hidden="true" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                    </svg>
                </a>
            </div>
        </div>
    </div>
</section>
