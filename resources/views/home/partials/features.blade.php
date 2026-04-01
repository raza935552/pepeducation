{{-- Feature Cards Section: What you can do here --}}
<section class="py-20 lg:py-28 bg-surface-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Section Header --}}
        <div class="text-center max-w-2xl mx-auto mb-14 lg:mb-20">
            <h2 class="text-3xl sm:text-4xl font-bold text-heading mb-4">
                Everything You Need In One Place
            </h2>
            <div class="w-12 h-1 bg-primary-500 rounded-full mx-auto mb-5"></div>
            <p class="text-lg text-body/70">
                Your complete peptide research toolkit
            </p>
        </div>

        {{-- 4-column grid --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 lg:gap-8">
            {{-- Card 1: Search & Discover --}}
            <a href="{{ route('peptides.index') }}"
               class="group relative bg-white rounded-2xl border border-surface-200 p-7 hover:shadow-xl hover:border-primary-200 transition-all duration-300 overflow-hidden">
                {{-- Hover gradient border effect --}}
                <div class="absolute inset-0 rounded-2xl bg-gradient-to-br from-primary-500/10 via-transparent to-secondary-500/5 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                <div class="relative">
                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-primary-100 to-primary-50 flex items-center justify-center mb-6 group-hover:shadow-md group-hover:shadow-primary-500/10 transition-all">
                        <svg aria-hidden="true" class="w-7 h-7 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-heading mb-2 group-hover:text-primary-600 transition-colors">
                        Search & Discover
                    </h3>
                    <p class="text-sm text-body/70 mb-5 leading-relaxed">
                        Browse our database of 70+ peptides with detailed research profiles and safety data.
                    </p>
                    <div class="flex flex-wrap gap-1.5 mb-4">
                        <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-primary-50 text-primary-600">BPC-157</span>
                        <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-primary-50 text-primary-600">Semaglutide</span>
                        <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-primary-50 text-primary-600">GHK-Cu</span>
                    </div>
                    {{-- Hover arrow --}}
                    <div class="flex items-center gap-1.5 text-sm font-semibold text-primary-500 opacity-0 group-hover:opacity-100 translate-x-0 group-hover:translate-x-1 transition-all duration-300">
                        Explore
                        <svg aria-hidden="true" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                        </svg>
                    </div>
                </div>
            </a>

            {{-- Card 2: Learn --}}
            <a href="{{ route('peptides.index') }}"
               class="group relative bg-white rounded-2xl border border-surface-200 p-7 hover:shadow-xl hover:border-primary-200 transition-all duration-300 overflow-hidden">
                <div class="absolute inset-0 rounded-2xl bg-gradient-to-br from-primary-500/10 via-transparent to-secondary-500/5 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                <div class="relative">
                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-primary-100 to-primary-50 flex items-center justify-center mb-6 group-hover:shadow-md group-hover:shadow-primary-500/10 transition-all">
                        <svg aria-hidden="true" class="w-7 h-7 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-heading mb-2 group-hover:text-primary-600 transition-colors">
                        Learn
                    </h3>
                    <p class="text-sm text-body/70 mb-5 leading-relaxed">
                        Research-backed guides and protocols to deepen your understanding of peptide science.
                    </p>
                    <div class="flex flex-wrap gap-1.5 mb-4">
                        <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-primary-50 text-primary-600">Protocols</span>
                        <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-primary-50 text-primary-600">Dosing</span>
                        <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-primary-50 text-primary-600">Research</span>
                    </div>
                    <div class="flex items-center gap-1.5 text-sm font-semibold text-primary-500 opacity-0 group-hover:opacity-100 translate-x-0 group-hover:translate-x-1 transition-all duration-300">
                        Explore
                        <svg aria-hidden="true" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                        </svg>
                    </div>
                </div>
            </a>

            {{-- Card 3: Tools --}}
            <a href="{{ route('calculator') }}"
               class="group relative bg-white rounded-2xl border border-surface-200 p-7 hover:shadow-xl hover:border-primary-200 transition-all duration-300 overflow-hidden">
                <div class="absolute inset-0 rounded-2xl bg-gradient-to-br from-primary-500/10 via-transparent to-secondary-500/5 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                <div class="relative">
                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-primary-100 to-primary-50 flex items-center justify-center mb-6 group-hover:shadow-md group-hover:shadow-primary-500/10 transition-all">
                        <svg aria-hidden="true" class="w-7 h-7 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-heading mb-2 group-hover:text-primary-600 transition-colors">
                        Tools
                    </h3>
                    <p class="text-sm text-body/70 mb-5 leading-relaxed">
                        Dosing calculators and reconstitution tools to plan your research protocols precisely.
                    </p>
                    <div class="flex flex-wrap gap-1.5 mb-4">
                        <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-primary-50 text-primary-600">Dosing Calculator</span>
                        <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-primary-50 text-primary-600">Stack Builder</span>
                    </div>
                    <div class="flex items-center gap-1.5 text-sm font-semibold text-primary-500 opacity-0 group-hover:opacity-100 translate-x-0 group-hover:translate-x-1 transition-all duration-300">
                        Explore
                        <svg aria-hidden="true" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                        </svg>
                    </div>
                </div>
            </a>

            {{-- Card 4: Quiz --}}
            <a href="{{ url('/quiz/find-your-peptide') }}"
               class="group relative bg-white rounded-2xl border border-surface-200 p-7 hover:shadow-xl hover:border-primary-200 transition-all duration-300 overflow-hidden">
                <div class="absolute inset-0 rounded-2xl bg-gradient-to-br from-primary-500/10 via-transparent to-secondary-500/5 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                <div class="relative">
                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-primary-100 to-primary-50 flex items-center justify-center mb-6 group-hover:shadow-md group-hover:shadow-primary-500/10 transition-all">
                        <svg aria-hidden="true" class="w-7 h-7 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-heading mb-2 group-hover:text-primary-600 transition-colors">
                        Quiz
                    </h3>
                    <p class="text-sm text-body/70 mb-5 leading-relaxed">
                        Find your perfect peptide match with our personalized recommendation quiz.
                    </p>
                    <div class="flex flex-wrap gap-1.5 mb-4">
                        <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-primary-50 text-primary-600">Personalized</span>
                        <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-primary-50 text-primary-600">60 Seconds</span>
                    </div>
                    <div class="flex items-center gap-1.5 text-sm font-semibold text-primary-500 opacity-0 group-hover:opacity-100 translate-x-0 group-hover:translate-x-1 transition-all duration-300">
                        Explore
                        <svg aria-hidden="true" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                        </svg>
                    </div>
                </div>
            </a>
        </div>
    </div>
</section>
