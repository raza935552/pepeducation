{{-- Quick Links Section --}}
<section class="py-14 lg:py-18 bg-surface-50 border-t border-surface-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center">
            <p class="text-sm font-semibold text-body/50 uppercase tracking-wider mb-8">Quick Links</p>
            <div class="flex flex-wrap items-center justify-center gap-3 sm:gap-4">
                <a href="{{ route('calculator') }}"
                   class="inline-flex items-center gap-2 px-6 py-3 rounded-full text-sm font-medium border border-surface-300 text-body/70 hover:bg-primary-500 hover:text-white hover:border-primary-500 hover:shadow-md hover:shadow-primary-500/20 transition-all duration-200">
                    <svg aria-hidden="true" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                    </svg>
                    Peptide Calculator
                </a>
                <a href="{{ route('stack-builder') }}"
                   class="inline-flex items-center gap-2 px-6 py-3 rounded-full text-sm font-medium border border-surface-300 text-body/70 hover:bg-primary-500 hover:text-white hover:border-primary-500 hover:shadow-md hover:shadow-primary-500/20 transition-all duration-200">
                    <svg aria-hidden="true" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                    </svg>
                    Stack Builder
                </a>
                <a href="{{ route('peptides.index') }}"
                   class="inline-flex items-center gap-2 px-6 py-3 rounded-full text-sm font-medium border border-surface-300 text-body/70 hover:bg-primary-500 hover:text-white hover:border-primary-500 hover:shadow-md hover:shadow-primary-500/20 transition-all duration-200">
                    <svg aria-hidden="true" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    Browse Database
                </a>
                <a href="{{ url('/quiz/find-your-peptide') }}"
                   class="inline-flex items-center gap-2 px-6 py-3 rounded-full text-sm font-medium border border-surface-300 text-body/70 hover:bg-primary-500 hover:text-white hover:border-primary-500 hover:shadow-md hover:shadow-primary-500/20 transition-all duration-200">
                    <svg aria-hidden="true" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                    </svg>
                    Take the Quiz
                </a>
                <a href="{{ route('blog.index') }}"
                   class="inline-flex items-center gap-2 px-6 py-3 rounded-full text-sm font-medium border border-surface-300 text-body/70 hover:bg-primary-500 hover:text-white hover:border-primary-500 hover:shadow-md hover:shadow-primary-500/20 transition-all duration-200">
                    <svg aria-hidden="true" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                    </svg>
                    Blog
                </a>
                <a href="{{ route('faq') }}"
                   class="inline-flex items-center gap-2 px-6 py-3 rounded-full text-sm font-medium border border-surface-300 text-body/70 hover:bg-primary-500 hover:text-white hover:border-primary-500 hover:shadow-md hover:shadow-primary-500/20 transition-all duration-200">
                    <svg aria-hidden="true" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    FAQ
                </a>
            </div>
        </div>
    </div>
</section>
