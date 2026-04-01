{{-- Go Deeper Section: Action-oriented CTAs --}}
<section class="py-20 lg:py-28 bg-surface-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Section Header --}}
        <div class="text-center mb-14 lg:mb-20">
            <h2 class="text-3xl sm:text-4xl font-bold text-heading uppercase tracking-wide mb-4">
                The Best Ways To Go Deeper
            </h2>
            <div class="w-12 h-1 bg-primary-500 rounded-full mx-auto"></div>
        </div>

        {{-- 3-column card grid --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 lg:gap-8">
            {{-- Card 1: Take the Quiz --}}
            <div class="group relative bg-white rounded-2xl border border-surface-200 p-7 lg:p-8 hover:shadow-xl transition-all duration-300 overflow-hidden flex flex-col">
                {{-- Hover gradient background --}}
                <div class="absolute inset-0 bg-gradient-to-br from-primary-50/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                <div class="relative flex flex-col flex-1">
                    {{-- Icon --}}
                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-primary-100 to-primary-50 flex items-center justify-center mb-5">
                        <svg aria-hidden="true" class="w-7 h-7 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                        </svg>
                    </div>
                    <span class="inline-flex self-start px-3 py-1 rounded-full text-xs font-semibold bg-primary-100 text-primary-700 mb-4">
                        Personalized
                    </span>
                    <h3 class="text-xl font-bold text-heading mb-3">
                        Find Your Ideal Peptide
                    </h3>
                    <p class="text-body/70 mb-8 leading-relaxed flex-1">
                        Answer a few questions and get matched with the peptides best suited to your research goals.
                    </p>
                    <a href="{{ url('/quiz/find-your-peptide') }}"
                       class="w-full inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl bg-primary-500 text-white font-semibold text-sm hover:bg-primary-600 transition-colors shadow-md shadow-primary-500/20">
                        Take the Quiz
                        <svg aria-hidden="true" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                        </svg>
                    </a>
                </div>
            </div>

            {{-- Card 2: Dosing Calculator --}}
            <div class="group relative bg-white rounded-2xl border border-surface-200 p-7 lg:p-8 hover:shadow-xl transition-all duration-300 overflow-hidden flex flex-col">
                <div class="absolute inset-0 bg-gradient-to-br from-green-50/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                <div class="relative flex flex-col flex-1">
                    {{-- Icon --}}
                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-green-100 to-green-50 flex items-center justify-center mb-5">
                        <svg aria-hidden="true" class="w-7 h-7 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <span class="inline-flex self-start px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700 mb-4">
                        Tools
                    </span>
                    <h3 class="text-xl font-bold text-heading mb-3">
                        Calculate & Plan
                    </h3>
                    <p class="text-body/70 mb-8 leading-relaxed flex-1">
                        Precise dosing calculations and reconstitution guides for your peptide research protocols.
                    </p>
                    <a href="{{ route('calculator') }}"
                       class="w-full inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl bg-primary-500 text-white font-semibold text-sm hover:bg-primary-600 transition-colors shadow-md shadow-primary-500/20">
                        Open Calculator
                        <svg aria-hidden="true" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                        </svg>
                    </a>
                </div>
            </div>

            {{-- Card 3: Stack Builder --}}
            <div class="group relative bg-white rounded-2xl border border-surface-200 p-7 lg:p-8 hover:shadow-xl transition-all duration-300 overflow-hidden flex flex-col">
                <div class="absolute inset-0 bg-gradient-to-br from-purple-50/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                <div class="relative flex flex-col flex-1">
                    {{-- Icon --}}
                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-purple-100 to-purple-50 flex items-center justify-center mb-5">
                        <svg aria-hidden="true" class="w-7 h-7 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                        </svg>
                    </div>
                    <span class="inline-flex self-start px-3 py-1 rounded-full text-xs font-semibold bg-purple-100 text-purple-700 mb-4">
                        Build
                    </span>
                    <h3 class="text-xl font-bold text-heading mb-3">
                        Build Your Stack
                    </h3>
                    <p class="text-body/70 mb-8 leading-relaxed flex-1">
                        Create your optimal peptide stack based on your research objectives and protocols.
                    </p>
                    <a href="{{ route('stack-builder') }}"
                       class="w-full inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl bg-primary-500 text-white font-semibold text-sm hover:bg-primary-600 transition-colors shadow-md shadow-primary-500/20">
                        Start Building
                        <svg aria-hidden="true" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
