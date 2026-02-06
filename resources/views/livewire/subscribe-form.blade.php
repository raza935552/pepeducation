<div>
    @if($success)
        <div class="flex items-center gap-2 px-4 py-2.5 rounded-lg bg-emerald-900/50 border border-emerald-700 text-emerald-400 text-sm">
            <svg aria-hidden="true" class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            {{ $message }}
        </div>
    @else
        <form wire:submit="subscribe" class="flex gap-2">
            <input type="email"
                   wire:model="email"
                   placeholder="Your email"
                   autocomplete="email"
                   class="flex-1 px-4 py-2.5 rounded-lg bg-brown-800 border border-brown-700 text-cream-100 placeholder-cream-500 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500 focus:border-transparent">
            <button type="submit"
                    wire:loading.attr="disabled"
                    class="px-4 py-2.5 bg-gold-500 text-white font-medium text-sm rounded-lg hover:bg-gold-600 transition-colors disabled:opacity-50 flex items-center gap-2">
                <span wire:loading.remove>Subscribe</span>
                <span wire:loading class="flex items-center gap-2">
                    <svg aria-hidden="true" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/>
                    </svg>
                </span>
            </button>
        </form>
        @error('email')
            <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
        @enderror
    @endif
</div>
