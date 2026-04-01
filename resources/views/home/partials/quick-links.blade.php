{{-- Quick Links Section --}}
<section class="py-12 lg:py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center">
            <p class="text-sm font-semibold text-body/50 uppercase tracking-wider mb-6">Quick Links</p>
            <div class="flex flex-wrap items-center justify-center gap-3">
                <a href="{{ route('calculator') }}"
                   class="px-5 py-2.5 rounded-full text-sm font-medium border border-surface-300 text-body/70 hover:bg-primary-500 hover:text-white hover:border-primary-500 transition-all duration-200">
                    Peptide Calculator
                </a>
                <a href="{{ route('stack-builder') }}"
                   class="px-5 py-2.5 rounded-full text-sm font-medium border border-surface-300 text-body/70 hover:bg-primary-500 hover:text-white hover:border-primary-500 transition-all duration-200">
                    Stack Builder
                </a>
                <a href="{{ route('peptides.index') }}"
                   class="px-5 py-2.5 rounded-full text-sm font-medium border border-surface-300 text-body/70 hover:bg-primary-500 hover:text-white hover:border-primary-500 transition-all duration-200">
                    Browse Database
                </a>
                <a href="{{ url('/quiz/find-your-peptide') }}"
                   class="px-5 py-2.5 rounded-full text-sm font-medium border border-surface-300 text-body/70 hover:bg-primary-500 hover:text-white hover:border-primary-500 transition-all duration-200">
                    Take the Quiz
                </a>
                <a href="{{ route('blog.index') }}"
                   class="px-5 py-2.5 rounded-full text-sm font-medium border border-surface-300 text-body/70 hover:bg-primary-500 hover:text-white hover:border-primary-500 transition-all duration-200">
                    Blog
                </a>
                <a href="{{ route('faq') }}"
                   class="px-5 py-2.5 rounded-full text-sm font-medium border border-surface-300 text-body/70 hover:bg-primary-500 hover:text-white hover:border-primary-500 transition-all duration-200">
                    FAQ
                </a>
            </div>
        </div>
    </div>
</section>
