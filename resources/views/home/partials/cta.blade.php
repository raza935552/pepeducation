{{-- Newsletter CTA Section --}}
<section class="relative bg-dark-900 py-20 lg:py-28 overflow-hidden">
    {{-- Dramatic gradient background --}}
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="absolute inset-0 bg-gradient-to-br from-dark-950 via-dark-900 to-dark-800"></div>
        <div class="absolute top-0 right-0 w-[500px] h-[500px] bg-primary-500/10 rounded-full blur-3xl"></div>
        <div class="absolute bottom-0 left-0 w-[400px] h-[400px] bg-secondary-500/8 rounded-full blur-3xl"></div>
        {{-- Decorative grid pattern --}}
        <div class="absolute inset-0 opacity-[0.03]" style="background-image: linear-gradient(rgb(var(--primary-400)) 1px, transparent 1px), linear-gradient(90deg, rgb(var(--primary-400)) 1px, transparent 1px); background-size: 48px 48px;"></div>
        {{-- Floating dots --}}
        <div class="absolute top-20 left-[15%] w-2 h-2 rounded-full bg-primary-500/20"></div>
        <div class="absolute top-32 right-[20%] w-3 h-3 rounded-full bg-primary-500/15"></div>
        <div class="absolute bottom-24 left-[25%] w-2.5 h-2.5 rounded-full bg-secondary-500/15"></div>
        <div class="absolute bottom-16 right-[10%] w-2 h-2 rounded-full bg-primary-500/20"></div>
        <div class="absolute top-1/2 left-[8%] w-1.5 h-1.5 rounded-full bg-primary-400/25"></div>
        <div class="absolute top-1/3 right-[30%] w-2 h-2 rounded-full bg-secondary-400/15"></div>
    </div>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="max-w-2xl mx-auto text-center">
            {{-- Icon --}}
            <div class="w-18 h-18 rounded-2xl bg-gradient-to-br from-primary-500 to-primary-700 flex items-center justify-center mx-auto mb-8 shadow-xl shadow-primary-500/25" style="width: 4.5rem; height: 4.5rem;">
                <svg aria-hidden="true" class="w-9 h-9 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
            </div>

            {{-- Separator line --}}
            <div class="w-12 h-1 bg-primary-500/40 rounded-full mx-auto mb-8"></div>

            {{-- Heading --}}
            <h2 class="text-3xl sm:text-4xl lg:text-5xl font-bold text-white mb-5 leading-tight">
                Get the Free Peptide Research Guide
            </h2>
            <p class="text-lg sm:text-xl text-dark-300 mb-10 leading-relaxed">
                Subscribe to receive our comprehensive peptide research guide plus weekly updates on new research and protocols.
            </p>

            {{-- Newsletter Form (Livewire) --}}
            <div class="max-w-md mx-auto mb-6">
                <livewire:subscribe-form source="homepage_cta" />
            </div>

            {{-- Trust Text --}}
            <p class="text-sm text-dark-400 flex items-center justify-center gap-2">
                <svg aria-hidden="true" class="w-4 h-4 text-primary-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                </svg>
                No spam, ever. Unsubscribe anytime.
            </p>
        </div>
    </div>
</section>
