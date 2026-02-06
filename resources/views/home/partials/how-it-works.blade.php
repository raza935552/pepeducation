{{-- How It Works Section --}}
<section class="py-16 lg:py-20 bg-cream-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Section Header --}}
        <div class="text-center max-w-2xl mx-auto mb-12">
            <h2 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-4">
                How It Works
            </h2>
            <p class="text-gray-600">
                Start your peptide research journey in three simple steps
            </p>
        </div>

        {{-- Steps Grid --}}
        <div class="grid md:grid-cols-3 gap-8">
            {{-- Step 1: Search & Discover --}}
            <div class="relative text-center group">
                {{-- Step Number --}}
                <div class="absolute -top-4 left-1/2 -translate-x-1/2 w-8 h-8 rounded-full bg-gold-500 text-white font-bold text-sm flex items-center justify-center">
                    1
                </div>
                <div class="bg-white rounded-2xl p-8 pt-10 border border-cream-200 h-full group-hover:shadow-xl group-hover:-translate-y-1 transition-all duration-300">
                    <div class="w-16 h-16 rounded-xl bg-gradient-to-br from-gold-400/20 to-gold-500/20 flex items-center justify-center mx-auto mb-6">
                        <svg aria-hidden="true" class="w-8 h-8 text-gold-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-3">
                        Search & Discover
                    </h3>
                    <p class="text-gray-600 mb-6">
                        Browse our database of 70+ peptides or use instant search to find exactly what you need.
                    </p>
                    <button type="button"
                            @click="$dispatch('open-search')"
                            class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full bg-cream-200 text-gray-700 font-medium text-sm hover:bg-cream-300 transition-colors">
                        <svg aria-hidden="true" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        Try Search
                    </button>
                </div>
            </div>

            {{-- Step 2: Review Research --}}
            <div class="relative text-center group">
                {{-- Step Number --}}
                <div class="absolute -top-4 left-1/2 -translate-x-1/2 w-8 h-8 rounded-full bg-gold-500 text-white font-bold text-sm flex items-center justify-center">
                    2
                </div>
                <div class="bg-white rounded-2xl p-8 pt-10 border border-cream-200 h-full group-hover:shadow-xl group-hover:-translate-y-1 transition-all duration-300">
                    <div class="w-16 h-16 rounded-xl bg-gradient-to-br from-gold-400/20 to-gold-500/20 flex items-center justify-center mx-auto mb-6">
                        <svg aria-hidden="true" class="w-8 h-8 text-gold-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-3">
                        Review Research
                    </h3>
                    <p class="text-gray-600 mb-6">
                        Access detailed research data, mechanisms of action, benefits, and safety information.
                    </p>
                    <a href="{{ route('peptides.index') }}"
                       class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full bg-cream-200 text-gray-700 font-medium text-sm hover:bg-cream-300 transition-colors">
                        <svg aria-hidden="true" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                        </svg>
                        Browse Database
                    </a>
                </div>
            </div>

            {{-- Step 3: Calculate & Plan --}}
            <div class="relative text-center group">
                {{-- Step Number --}}
                <div class="absolute -top-4 left-1/2 -translate-x-1/2 w-8 h-8 rounded-full bg-gold-500 text-white font-bold text-sm flex items-center justify-center">
                    3
                </div>
                <div class="bg-white rounded-2xl p-8 pt-10 border border-cream-200 h-full group-hover:shadow-xl group-hover:-translate-y-1 transition-all duration-300">
                    <div class="w-16 h-16 rounded-xl bg-gradient-to-br from-gold-400/20 to-gold-500/20 flex items-center justify-center mx-auto mb-6">
                        <svg aria-hidden="true" class="w-8 h-8 text-gold-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-3">
                        Calculate & Plan
                    </h3>
                    <p class="text-gray-600 mb-6">
                        Use our dosing calculator and reconstitution tools to plan your research protocols.
                    </p>
                    <a href="{{ route('calculator') }}"
                       class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full bg-cream-200 text-gray-700 font-medium text-sm hover:bg-cream-300 transition-colors">
                        <svg aria-hidden="true" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                        </svg>
                        Calculator
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
