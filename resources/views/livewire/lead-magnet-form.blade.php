<div>
    @if($submitted)
        <div class="text-center py-6">
            <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg aria-hidden="true" class="w-8 h-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>

            @if($downloadUrl)
                <h3 class="text-xl font-bold text-gray-900 mb-2">Your download is ready!</h3>
                <p class="text-gray-600 mb-4">Click the button below to download your free guide.</p>
                <a href="{{ $downloadUrl }}"
                   class="inline-flex items-center gap-2 bg-gradient-to-r from-gold-500 to-gold-600 text-white font-bold px-6 py-3 rounded-full hover:from-gold-600 hover:to-gold-700 transition-all shadow-lg"
                   download>
                    <svg aria-hidden="true" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                    </svg>
                    Download Now
                </a>
            @else
                <h3 class="text-xl font-bold text-gray-900 mb-2">Check your email!</h3>
                <p class="text-gray-600">We've sent your download link to <strong>{{ $email }}</strong></p>
                <p class="text-sm text-gray-500 mt-2">Make sure to check your spam folder if you don't see it.</p>
            @endif
        </div>
    @else
        <form wire:submit="submit" class="space-y-4">
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                    Email Address <span class="text-red-500">*</span>
                </label>
                <input type="email"
                       id="email"
                       wire:model="email"
                       required
                       autocomplete="email"
                       placeholder="Enter your email"
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gold-500 focus:border-gold-500 transition-colors @error('email') border-red-500 @enderror">
                @error('email')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                    Name <span class="text-gray-400">(optional)</span>
                </label>
                <input type="text"
                       id="name"
                       wire:model="name"
                       autocomplete="name"
                       placeholder="Your name"
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gold-500 focus:border-gold-500 transition-colors">
            </div>

            <button type="submit"
                    wire:loading.attr="disabled"
                    wire:loading.class="opacity-75 cursor-wait"
                    class="w-full bg-gradient-to-r from-gold-500 to-gold-600 text-white font-bold px-6 py-4 rounded-full hover:from-gold-600 hover:to-gold-700 transition-all shadow-lg flex items-center justify-center gap-2">
                <span wire:loading.remove>
                    <svg aria-hidden="true" class="w-5 h-5 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                    </svg>
                    {{ $leadMagnet->download_button_text ?? 'Get Free Access' }}
                </span>
                <span wire:loading>
                    <svg aria-hidden="true" class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Processing...
                </span>
            </button>

            <p class="text-xs text-gray-500 text-center">
                By downloading, you agree to receive emails from PepProfesor. Unsubscribe anytime.
            </p>
        </form>
    @endif
</div>
