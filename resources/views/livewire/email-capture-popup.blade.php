<div x-data="{
        shown: @entangle('show'),
        hasShown: false,
        init() {
            // Check localStorage to not show again if dismissed
            if (localStorage.getItem('emailPopupDismissed')) return;

            // Show after 15 seconds delay
            setTimeout(() => {
                if (!this.hasShown && !localStorage.getItem('emailPopupDismissed')) {
                    this.shown = true;
                    this.hasShown = true;
                }
            }, 15000);

            // Show on exit intent (mouse leaving viewport)
            document.addEventListener('mouseleave', (e) => {
                if (e.clientY < 10 && !this.hasShown && !localStorage.getItem('emailPopupDismissed')) {
                    this.shown = true;
                    this.hasShown = true;
                }
            });
        },
        dismiss() {
            this.shown = false;
            localStorage.setItem('emailPopupDismissed', 'true');
        }
     }"
     x-show="shown"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     @keydown.escape.window="dismiss()"
     class="fixed inset-0 z-50 overflow-y-auto"
     style="display: none;">

    {{-- Backdrop --}}
    <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" @click="dismiss()"></div>

    {{-- Modal --}}
    <div class="fixed inset-0 flex items-center justify-center p-4">
        <div x-show="shown"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             @click.stop
             class="relative w-full max-w-md bg-white dark:bg-brown-800 rounded-2xl shadow-2xl overflow-hidden">

            {{-- Close Button --}}
            <button @click="dismiss()"
                    class="absolute top-4 right-4 p-2 rounded-full text-gray-400 hover:text-gray-600 dark:hover:text-cream-300 hover:bg-gray-100 dark:hover:bg-brown-700 transition-colors z-10">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>

            {{-- Header Gradient --}}
            <div class="h-2 bg-gradient-to-r from-gold-400 via-gold-500 to-caramel-500"></div>

            <div class="p-8">
                @if($success)
                    {{-- Success State --}}
                    <div class="text-center">
                        <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center">
                            <svg class="w-8 h-8 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-cream-100 mb-2">You're In!</h3>
                        <p class="text-gray-600 dark:text-cream-400 mb-6">Check your inbox for the latest peptide research updates.</p>
                        <button @click="dismiss(); localStorage.setItem('emailPopupDismissed', 'true');"
                                class="px-6 py-3 bg-gold-500 text-white font-medium rounded-xl hover:bg-gold-600 transition-colors">
                            Continue Browsing
                        </button>
                    </div>
                @else
                    {{-- Form State --}}
                    <div class="text-center mb-6">
                        <div class="w-14 h-14 mx-auto mb-4 rounded-2xl bg-gold-100 dark:bg-gold-900/30 flex items-center justify-center">
                            <svg class="w-7 h-7 text-gold-600 dark:text-gold-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-cream-100 mb-2">Stay Updated</h3>
                        <p class="text-gray-600 dark:text-cream-400">Get the latest peptide research, guides, and calculator updates delivered to your inbox.</p>
                    </div>

                    <form wire:submit="subscribe" class="space-y-4">
                        <div>
                            <input type="email"
                                   wire:model="email"
                                   placeholder="Enter your email"
                                   class="w-full px-4 py-3 rounded-xl border border-cream-300 dark:border-brown-600 bg-white dark:bg-brown-700 text-gray-900 dark:text-cream-100 placeholder-gray-400 dark:placeholder-cream-500 focus:outline-none focus:ring-2 focus:ring-gold-500 focus:border-transparent">
                            @error('email')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <button type="submit"
                                wire:loading.attr="disabled"
                                class="w-full px-6 py-3 bg-gold-500 text-white font-semibold rounded-xl hover:bg-gold-600 transition-colors disabled:opacity-50 flex items-center justify-center gap-2">
                            <span wire:loading.remove>Subscribe for Free</span>
                            <span wire:loading class="flex items-center gap-2">
                                <svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/>
                                </svg>
                                Subscribing...
                            </span>
                        </button>
                    </form>

                    <p class="text-xs text-gray-500 dark:text-cream-500 text-center mt-4">
                        No spam, unsubscribe anytime. We respect your privacy.
                    </p>
                @endif
            </div>
        </div>
    </div>
</div>
