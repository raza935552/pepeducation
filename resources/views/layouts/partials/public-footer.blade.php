<footer class="bg-brown-900 dark:bg-brown-950">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 lg:gap-12">
            {{-- Brand & Newsletter --}}
            <div class="lg:col-span-1">
                <a href="{{ url('/') }}" class="flex items-center gap-1 mb-4">
                    <span class="text-xl font-bold text-gold-400">Pep</span>
                    <span class="text-xl font-bold text-cream-100">Profesor</span>
                </a>
                <p class="text-sm text-cream-400 leading-relaxed mb-6">
                    AI-curated from research and clinical sources, reviewed by the community. Educational content only.
                </p>
                {{-- Mini Newsletter --}}
                <div class="mb-4">
                    <livewire:subscribe-form source="footer" />
                </div>
            </div>

            {{-- Quick Links --}}
            <div>
                <h3 class="text-sm font-semibold text-cream-100 uppercase tracking-wider mb-4">
                    Quick Links
                </h3>
                <ul class="space-y-3">
                    <li>
                        <a href="{{ route('peptides.index') }}" class="text-sm text-cream-400 hover:text-gold-400 transition-colors">
                            Browse Peptides
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('calculator') }}" class="text-sm text-cream-400 hover:text-gold-400 transition-colors">
                            Calculator
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('about') }}" class="text-sm text-cream-400 hover:text-gold-400 transition-colors">
                            About Us
                        </a>
                    </li>
                    <li>
                        <button onclick="Livewire.dispatch('openContactModal')" class="text-sm text-cream-400 hover:text-gold-400 transition-colors">
                            Contact Us
                        </button>
                    </li>
                </ul>
            </div>

            {{-- Categories --}}
            <div>
                <h3 class="text-sm font-semibold text-cream-100 uppercase tracking-wider mb-4">
                    Categories
                </h3>
                <ul class="space-y-3">
                    <li>
                        <a href="{{ route('peptides.index', ['category' => 'wound-healing']) }}" class="text-sm text-cream-400 hover:text-gold-400 transition-colors">
                            Wound Healing
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('peptides.index', ['category' => 'weight-loss']) }}" class="text-sm text-cream-400 hover:text-gold-400 transition-colors">
                            Weight Loss
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('peptides.index', ['category' => 'anti-aging']) }}" class="text-sm text-cream-400 hover:text-gold-400 transition-colors">
                            Anti-Aging
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('peptides.index', ['category' => 'cognitive-enhancement']) }}" class="text-sm text-cream-400 hover:text-gold-400 transition-colors">
                            Cognitive
                        </a>
                    </li>
                </ul>
            </div>

            {{-- Legal --}}
            <div>
                <h3 class="text-sm font-semibold text-cream-100 uppercase tracking-wider mb-4">
                    Legal
                </h3>
                <ul class="space-y-3">
                    <li>
                        <a href="{{ route('privacy') }}" class="text-sm text-cream-400 hover:text-gold-400 transition-colors">
                            Privacy Policy
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('terms') }}" class="text-sm text-cream-400 hover:text-gold-400 transition-colors">
                            Terms of Service
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('disclaimer') }}" class="text-sm text-cream-400 hover:text-gold-400 transition-colors">
                            Disclaimer
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('faq') }}" class="text-sm text-cream-400 hover:text-gold-400 transition-colors">
                            FAQ
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        {{-- Supporters Section --}}
        @php $supporters = \App\Models\Supporter::active()->ordered()->get(); @endphp
        @if($supporters->isNotEmpty())
            <div class="mt-12 pt-8 border-t border-brown-700">
                <div class="text-center mb-6">
                    <h3 class="text-sm font-semibold text-cream-100 uppercase tracking-wider mb-2">Our Supporters</h3>
                    <p class="text-xs text-cream-500">Thank you to our generous supporters</p>
                </div>
                <div class="flex flex-wrap items-center justify-center gap-6 lg:gap-8">
                    @foreach($supporters as $supporter)
                        @if($supporter->website_url)
                            <a href="{{ $supporter->website_url }}" target="_blank" rel="noopener noreferrer"
                               class="group flex items-center gap-2 px-4 py-2 rounded-lg bg-brown-800/50 hover:bg-brown-800 transition-colors"
                               title="{{ $supporter->name }}">
                        @else
                            <div class="flex items-center gap-2 px-4 py-2 rounded-lg bg-brown-800/50">
                        @endif
                            @if($supporter->logo)
                                <img src="{{ Storage::url($supporter->logo) }}" alt="{{ $supporter->name }}"
                                     class="h-8 w-auto object-contain {{ $supporter->website_url ? 'group-hover:opacity-80 transition-opacity' : '' }}">
                            @else
                                <span class="text-sm font-medium text-cream-300 {{ $supporter->website_url ? 'group-hover:text-gold-400 transition-colors' : '' }}">
                                    {{ $supporter->name }}
                                </span>
                            @endif
                            @if($supporter->is_featured)
                                <svg class="w-4 h-4 text-gold-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                            @endif
                        @if($supporter->website_url)
                            </a>
                        @else
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Bottom Bar --}}
        <div class="mt-12 pt-8 border-t border-brown-700">
            <div class="flex flex-col md:flex-row items-center justify-between gap-4">
                <p class="text-sm text-cream-500">
                    &copy; {{ date('Y') }} PepProfesor. For educational and research purposes only.
                </p>
                {{-- Social Links --}}
                <div class="flex items-center gap-3">
                    <a href="#" class="p-2 rounded-lg text-cream-500 hover:text-gold-400 hover:bg-brown-800 transition-colors">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"/>
                        </svg>
                    </a>
                    <a href="#" class="p-2 rounded-lg text-cream-500 hover:text-gold-400 hover:bg-brown-800 transition-colors">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z"/>
                        </svg>
                    </a>
                    <a href="#" class="p-2 rounded-lg text-cream-500 hover:text-gold-400 hover:bg-brown-800 transition-colors">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </div>
</footer>
