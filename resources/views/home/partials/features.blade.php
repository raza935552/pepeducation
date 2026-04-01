{{-- Feature Cards Section: What you can do here --}}
<section class="py-16 lg:py-24 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Section Header --}}
        <div class="text-center max-w-2xl mx-auto mb-12 lg:mb-16">
            <h2 class="text-3xl sm:text-4xl font-bold text-heading mb-4">
                Everything You Need In One Place
            </h2>
            <p class="text-lg text-body/70">
                Your complete peptide research toolkit
            </p>
        </div>

        {{-- 4-column grid --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            {{-- Card 1: Search & Discover --}}
            <a href="{{ route('peptides.index') }}"
               class="group relative bg-white rounded-xl border border-surface-200 p-6 hover:shadow-lg hover:border-primary-300 transition-all duration-300">
                <div class="w-12 h-12 rounded-xl bg-primary-50 flex items-center justify-center mb-5 group-hover:bg-primary-100 transition-colors">
                    <svg aria-hidden="true" class="w-6 h-6 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-heading mb-2 group-hover:text-primary-600 transition-colors">
                    Search & Discover
                </h3>
                <p class="text-sm text-body/70 mb-4 leading-relaxed">
                    Browse our database of 70+ peptides with detailed research profiles and safety data.
                </p>
                <div class="flex flex-wrap gap-1.5">
                    <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-surface-100 text-body/60">BPC-157</span>
                    <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-surface-100 text-body/60">Semaglutide</span>
                    <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-surface-100 text-body/60">GHK-Cu</span>
                </div>
            </a>

            {{-- Card 2: Learn --}}
            <a href="{{ route('peptides.index') }}"
               class="group relative bg-white rounded-xl border border-surface-200 p-6 hover:shadow-lg hover:border-primary-300 transition-all duration-300">
                <div class="w-12 h-12 rounded-xl bg-primary-50 flex items-center justify-center mb-5 group-hover:bg-primary-100 transition-colors">
                    <svg aria-hidden="true" class="w-6 h-6 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-heading mb-2 group-hover:text-primary-600 transition-colors">
                    Learn
                </h3>
                <p class="text-sm text-body/70 mb-4 leading-relaxed">
                    Research-backed guides and protocols to deepen your understanding of peptide science.
                </p>
                <div class="flex flex-wrap gap-1.5">
                    <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-surface-100 text-body/60">Protocols</span>
                    <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-surface-100 text-body/60">Dosing</span>
                    <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-surface-100 text-body/60">Research</span>
                </div>
            </a>

            {{-- Card 3: Tools --}}
            <a href="{{ route('calculator') }}"
               class="group relative bg-white rounded-xl border border-surface-200 p-6 hover:shadow-lg hover:border-primary-300 transition-all duration-300">
                <div class="w-12 h-12 rounded-xl bg-primary-50 flex items-center justify-center mb-5 group-hover:bg-primary-100 transition-colors">
                    <svg aria-hidden="true" class="w-6 h-6 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-heading mb-2 group-hover:text-primary-600 transition-colors">
                    Tools
                </h3>
                <p class="text-sm text-body/70 mb-4 leading-relaxed">
                    Dosing calculators and reconstitution tools to plan your research protocols precisely.
                </p>
                <div class="flex flex-wrap gap-1.5">
                    <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-surface-100 text-body/60">Dosing Calculator</span>
                    <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-surface-100 text-body/60">Stack Builder</span>
                </div>
            </a>

            {{-- Card 4: Quiz --}}
            <a href="{{ url('/quiz/find-your-peptide') }}"
               class="group relative bg-white rounded-xl border border-surface-200 p-6 hover:shadow-lg hover:border-primary-300 transition-all duration-300">
                <div class="w-12 h-12 rounded-xl bg-primary-50 flex items-center justify-center mb-5 group-hover:bg-primary-100 transition-colors">
                    <svg aria-hidden="true" class="w-6 h-6 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-heading mb-2 group-hover:text-primary-600 transition-colors">
                    Quiz
                </h3>
                <p class="text-sm text-body/70 mb-4 leading-relaxed">
                    Find your perfect peptide match with our personalized recommendation quiz.
                </p>
                <div class="flex flex-wrap gap-1.5">
                    <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-surface-100 text-body/60">Personalized</span>
                    <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-surface-100 text-body/60">60 Seconds</span>
                </div>
            </a>
        </div>
    </div>
</section>
