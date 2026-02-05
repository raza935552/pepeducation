<section class="relative bg-cream-100 dark:bg-brown-900 overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid lg:grid-cols-2 gap-12 lg:gap-8 items-center py-16 lg:py-24">
            {{-- Left: Text Content --}}
            <div class="max-w-xl">
                {{-- Rotating category text --}}
                <div class="mb-6" x-data="{
                    categories: ['Wound Healing', 'Weight Loss', 'Anti-Aging', 'Cognitive Enhancement', 'Muscle Growth'],
                    current: 0,
                    init() {
                        setInterval(() => {
                            this.current = (this.current + 1) % this.categories.length;
                        }, 3000);
                    }
                }">
                    <span class="text-lg sm:text-xl font-medium text-gold-500 dark:text-gold-400"
                          x-text="categories[current]"
                          x-transition:enter="transition ease-out duration-300"
                          x-transition:enter-start="opacity-0 transform translate-y-2"
                          x-transition:enter-end="opacity-100 transform translate-y-0">
                        Wound Healing
                    </span>
                </div>

                {{-- Main Heading --}}
                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-bold text-gray-900 dark:text-cream-100 leading-[1.1] mb-6">
                    Peptide research,
                    <span class="block text-gold-500 dark:text-gold-400 italic">personalized to you</span>
                </h1>

                {{-- Subheading --}}
                <p class="text-lg text-gray-600 dark:text-cream-300 mb-8 leading-relaxed">
                    Comprehensive research data on 70+ peptides. Dosing calculators.
                    Evidence-based protocols. All free, forever.
                </p>

                {{-- CTA Buttons --}}
                <div class="flex flex-col sm:flex-row gap-4">
                    <a href="{{ route('peptides.index') }}"
                       class="inline-flex items-center justify-center px-8 py-4 bg-gray-900 dark:bg-cream-100 text-white dark:text-brown-900 font-semibold rounded-full hover:bg-gray-800 dark:hover:bg-cream-200 transition-all duration-300 shadow-lg hover:shadow-xl hover:-translate-y-0.5">
                        Get Started
                        <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                        </svg>
                    </a>
                    <button type="button"
                            @click="$dispatch('open-search')"
                            class="inline-flex items-center justify-center px-8 py-4 bg-cream-200 dark:bg-brown-800 text-gray-900 dark:text-cream-100 font-semibold rounded-full hover:bg-cream-300 dark:hover:bg-brown-700 transition-all duration-300">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        Search Peptides
                    </button>
                </div>

                {{-- Trust indicators --}}
                <div class="mt-10 pt-8 border-t border-cream-300 dark:border-brown-700">
                    <div class="flex flex-wrap items-center gap-6 text-sm text-gray-500 dark:text-cream-400">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-gold-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span>Evidence-based</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-gold-500" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/>
                            </svg>
                            <span>Community-driven</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-gold-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                            </svg>
                            <span>100% Free</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right: Category Grid --}}
            <div class="relative">
                @include('home.partials.hero-categories')
            </div>
        </div>
    </div>
</section>
