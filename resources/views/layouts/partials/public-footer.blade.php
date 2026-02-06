<footer class="bg-brown-900">
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
        @php $supporters = Cache::remember('footer_supporters', 900, fn() => \App\Models\Supporter::active()->ordered()->get()); @endphp
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
                                <svg aria-hidden="true" class="w-4 h-4 text-gold-400" fill="currentColor" viewBox="0 0 20 20">
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
                {{-- Social links placeholder removed - add real URLs when accounts are created --}}
            </div>
        </div>
    </div>
</footer>
