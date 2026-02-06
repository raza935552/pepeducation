{{-- Newsletter CTA Section --}}
<section class="py-16 lg:py-20 bg-cream-100 relative overflow-hidden">
    {{-- Decorative elements --}}
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="absolute top-0 right-0 w-96 h-96 bg-gold-500/5 rounded-full blur-3xl"></div>
        <div class="absolute bottom-0 left-0 w-96 h-96 bg-caramel-500/5 rounded-full blur-3xl"></div>
    </div>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="max-w-2xl mx-auto text-center">
            {{-- Icon --}}
            <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-gold-500 to-caramel-500 flex items-center justify-center mx-auto mb-6 shadow-lg shadow-gold-500/20">
                <svg aria-hidden="true" class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
            </div>

            {{-- Heading --}}
            <h2 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-gray-900 mb-4">
                Get the Free Peptide Research Guide
            </h2>
            <p class="text-lg text-gray-600 mb-8">
                Subscribe to receive our comprehensive peptide research guide plus weekly updates on new research and protocols.
            </p>

            {{-- Newsletter Form (Livewire) --}}
            <div class="max-w-md mx-auto mb-6">
                <livewire:subscribe-form source="homepage_cta" />
            </div>

            {{-- Trust Text --}}
            <p class="text-sm text-gray-500 flex items-center justify-center gap-2">
                <svg aria-hidden="true" class="w-4 h-4 text-gold-500" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                </svg>
                No spam, ever. Unsubscribe anytime.
            </p>
        </div>
    </div>
</section>
