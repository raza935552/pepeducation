{{-- Featured CTA Section (Caramel Background) --}}
<section class="relative bg-caramel-500 overflow-hidden">
    {{-- Decorative elements --}}
    <div class="absolute inset-0 opacity-10">
        <div class="absolute top-0 left-0 w-96 h-96 bg-white rounded-full blur-3xl -translate-x-1/2 -translate-y-1/2"></div>
        <div class="absolute bottom-0 right-0 w-96 h-96 bg-white rounded-full blur-3xl translate-x-1/2 translate-y-1/2"></div>
    </div>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 lg:py-20">
        <div class="grid lg:grid-cols-2 gap-12 items-center">
            {{-- Left: Content --}}
            <div>
                <span class="inline-block px-4 py-1.5 rounded-full bg-white/20 text-white text-sm font-medium mb-6">
                    Research Platform
                </span>
                <h2 class="text-3xl sm:text-4xl lg:text-5xl font-bold text-white leading-tight mb-6">
                    Access comprehensive peptide research
                </h2>
                <p class="text-lg text-white/90 mb-8 leading-relaxed">
                    From dosing protocols to mechanism of action, find everything you need to make informed decisions about peptide research.
                </p>
                <div class="flex flex-col sm:flex-row gap-4">
                    <a href="{{ route('peptides.index') }}"
                       class="inline-flex items-center justify-center px-8 py-4 bg-white text-caramel-600 font-semibold rounded-full hover:bg-cream-100 transition-all duration-300 shadow-lg hover:shadow-xl">
                        Get Started
                        <svg aria-hidden="true" class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                        </svg>
                    </a>
                    <button type="button"
                            @click="$dispatch('open-search')"
                            class="inline-flex items-center justify-center px-8 py-4 bg-transparent border-2 border-white text-white font-semibold rounded-full hover:bg-white/10 transition-all duration-300">
                        Learn More
                    </button>
                </div>
            </div>

            {{-- Right: Stats Cards --}}
            <div class="grid grid-cols-2 gap-4">
                <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-6 border border-white/20">
                    <div class="text-4xl font-bold text-white mb-2">70+</div>
                    <div class="text-white/80">Research Peptides</div>
                </div>
                <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-6 border border-white/20">
                    <div class="text-4xl font-bold text-white mb-2">500+</div>
                    <div class="text-white/80">Research Studies</div>
                </div>
                <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-6 border border-white/20">
                    <div class="text-4xl font-bold text-white mb-2">20+</div>
                    <div class="text-white/80">Categories</div>
                </div>
                <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-6 border border-white/20">
                    <div class="text-4xl font-bold text-white mb-2">Free</div>
                    <div class="text-white/80">Always & Forever</div>
                </div>
            </div>
        </div>
    </div>
</section>
