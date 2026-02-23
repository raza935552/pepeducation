<div>
    @if($this->activePopup)
        <div
            x-data="{ open: true }"
            x-show="open"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-50 flex items-center justify-center p-4"
            @keydown.escape.window="$wire.closePopup(); open = false"
        >
            <!-- Backdrop -->
            <div class="absolute inset-0 bg-black/50" @click="$wire.closePopup(); open = false"></div>

            <!-- Popup Content -->
            <div
                x-show="open"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                class="relative w-full max-w-md bg-white rounded-2xl shadow-2xl overflow-hidden"
                role="dialog" aria-modal="true" aria-labelledby="popup-title"
                style="{{ $this->activePopup->getInlineStyles() }}"
            >
                <!-- Close Button -->
                <button
                    @click="$wire.closePopup(); open = false"
                    aria-label="Close"
                    class="absolute top-3 right-3 z-10 p-1 rounded-full bg-black/10 hover:bg-black/20 transition-colors"
                >
                    <svg aria-hidden="true" class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>

                @if(!$submitted)
                    <!-- Popup Content -->
                    <div class="p-6 text-center">
                        @if($this->activePopup->design['image'] ?? null)
                            <img src="{{ Storage::url($this->activePopup->design['image']) }}" alt="{{ $this->activePopup->design['headline'] ?? $this->activePopup->name }}" class="w-full h-40 object-cover mb-4 -mt-6 -mx-6" style="width: calc(100% + 3rem);">
                        @endif

                        <h2 id="popup-title" class="text-2xl font-bold mb-2" style="color: {{ $this->activePopup->design['text_color'] ?? '#1f2937' }}">
                            {{ $this->activePopup->headline ?? $this->activePopup->name }}
                        </h2>

                        @if($this->activePopup->body)
                            <p class="text-gray-600 mb-6">{{ $this->activePopup->body }}</p>
                        @endif

                        @if($this->activePopup->type === 'lead_capture')
                            <form wire:submit="submitEmail" class="space-y-4">
                                <input
                                    type="email"
                                    wire:model="email"
                                    autocomplete="email"
                                    placeholder="{{ $this->activePopup->design['placeholder'] ?? 'Enter your email' }}"
                                    class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-brand-gold focus:ring-brand-gold disabled:opacity-50"
                                    required
                                    wire:loading.attr="disabled"
                                >
                                @error('email') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror

                                <button
                                    type="submit"
                                    wire:loading.attr="disabled"
                                    class="w-full py-3 px-6 rounded-lg font-semibold transition-colors disabled:opacity-50"
                                    style="background-color: {{ $this->activePopup->design['button_color'] ?? '#D4A35A' }}; color: {{ $this->activePopup->design['button_text_color'] ?? '#ffffff' }}"
                                >
                                    <span wire:loading.remove>{{ $this->activePopup->button_text ?? $this->activePopup->design['button_text'] ?? 'Subscribe' }}</span>
                                    <span wire:loading>Submitting...</span>
                                </button>
                            </form>

                            @if($this->activePopup->design['privacy_text'] ?? null)
                                <p class="text-xs text-gray-500 mt-4">{{ $this->activePopup->design['privacy_text'] }}</p>
                            @endif

                        @elseif($this->activePopup->type === 'cta')
                            @if($this->activePopup->design['cta_url'] ?? null)
                                <a
                                    href="{{ $this->activePopup->design['cta_url'] }}"
                                    class="inline-block py-3 px-8 rounded-lg font-semibold transition-colors"
                                    style="background-color: {{ $this->activePopup->design['button_color'] ?? '#D4A35A' }}; color: {{ $this->activePopup->design['button_text_color'] ?? '#ffffff' }}"
                                    wire:click="$dispatch('popup-cta-click', { popupId: {{ $this->activePopup->id }} })"
                                >
                                    {{ $this->activePopup->design['button_text'] ?? 'Learn More' }}
                                </a>
                            @endif

                        @elseif($this->activePopup->type === 'announcement')
                            @if($this->activePopup->design['body'] ?? null)
                                <div class="text-gray-700 mb-4">
                                    {!! nl2br(e($this->activePopup->design['body'])) !!}
                                </div>
                            @endif
                        @endif
                    </div>

                @else
                    <!-- Success State -->
                    <div class="p-8 text-center">
                        <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-green-100 flex items-center justify-center">
                            <svg aria-hidden="true" class="w-8 h-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold mb-2">{{ $this->activePopup->design['success_headline'] ?? 'Thank you!' }}</h3>
                        <p class="text-gray-600">{{ $this->activePopup->success_message ?? $this->activePopup->design['success_message'] ?? 'You\'ve been subscribed successfully.' }}</p>
                    </div>
                @endif
            </div>
        </div>
    @endif

    <!-- Popup Trigger Script -->
    @if($popups->count() > 0)
        @script
        <script>
            const popupConfigs = @json($popups->map(fn($p) => [
                'id' => $p->id,
                'triggers' => $p->triggers,
            ]));

            const triggeredPopups = new Set();

            popupConfigs.forEach(config => {
                const triggers = config.triggers || {};

                // Time-based trigger
                if (triggers.time_delay) {
                    setTimeout(() => {
                        if (!triggeredPopups.has(config.id)) {
                            triggeredPopups.add(config.id);
                            $wire.showPopup(config.id);
                        }
                    }, triggers.time_delay * 1000);
                }

                // Scroll-based trigger
                if (triggers.scroll_depth) {
                    const checkScroll = () => {
                        const scrolled = (window.scrollY / (document.body.scrollHeight - window.innerHeight)) * 100;
                        if (scrolled >= triggers.scroll_depth && !triggeredPopups.has(config.id)) {
                            triggeredPopups.add(config.id);
                            $wire.showPopup(config.id);
                            window.removeEventListener('scroll', checkScroll);
                        }
                    };
                    window.addEventListener('scroll', checkScroll);
                }

                // Exit intent trigger
                if (triggers.exit_intent) {
                    const exitHandler = (e) => {
                        if (e.clientY <= 0 && !triggeredPopups.has(config.id)) {
                            triggeredPopups.add(config.id);
                            $wire.showPopup(config.id);
                            document.removeEventListener('mouseout', exitHandler);
                        }
                    };
                    document.addEventListener('mouseout', exitHandler);
                }

                // Page view count trigger
                if (triggers.page_views) {
                    const viewCount = parseInt(sessionStorage.getItem('pp_page_views') || '0') + 1;
                    sessionStorage.setItem('pp_page_views', viewCount);
                    if (viewCount >= triggers.page_views && !triggeredPopups.has(config.id)) {
                        triggeredPopups.add(config.id);
                        setTimeout(() => $wire.showPopup(config.id), 1000);
                    }
                }
            });
        </script>
        @endscript
    @endif
</div>
