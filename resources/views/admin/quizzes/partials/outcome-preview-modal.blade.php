{{-- Phone-frame preview modal for outcome result cards --}}
<div x-data="{
        open: false,
        name: '',
        result_title: '',
        result_message: '',
        result_image: '',
        redirect_url: '',
        redirect_type: '',
    }"
    x-on:open-outcome-preview.window="
        name = $event.detail.name || '';
        result_title = $event.detail.result_title || '';
        result_message = $event.detail.result_message || '';
        result_image = $event.detail.result_image || '';
        redirect_url = $event.detail.redirect_url || '';
        redirect_type = $event.detail.redirect_type || '';
        open = true;
    "
    x-on:keydown.escape.window="open = false"
    x-show="open"
    x-cloak
    class="fixed inset-0 z-50 flex items-center justify-center"
>
    {{-- Backdrop --}}
    <div class="absolute inset-0 bg-black/50" @click="open = false"></div>

    {{-- Modal content --}}
    <div class="relative z-10 flex flex-col items-center">
        {{-- Preview badge --}}
        <span class="mb-2 px-3 py-1 text-xs font-semibold rounded-full bg-teal-100 text-teal-700 shadow-sm">Preview</span>

        {{-- Phone frame --}}
        <div class="bg-white rounded-[2rem] border-4 border-gray-800 shadow-2xl w-[320px] overflow-hidden">
            {{-- Notch bar --}}
            <div class="bg-gray-800 h-7 flex items-center justify-center">
                <div class="w-20 h-4 bg-gray-900 rounded-b-xl"></div>
            </div>

            {{-- Status bar --}}
            <div class="bg-white px-5 pt-2 pb-1 flex items-center justify-between text-[10px] text-gray-400">
                <span>9:41</span>
                <div class="flex items-center gap-1">
                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zm6-4a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zm6-3a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"/></svg>
                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M17.778 8.222c-4.296-4.296-11.26-4.296-15.556 0A1 1 0 01.808 6.808c5.076-5.076 13.308-5.076 18.384 0a1 1 0 01-1.414 1.414zM14.95 11.05a7 7 0 00-9.9 0 1 1 0 01-1.414-1.414 9 9 0 0112.728 0 1 1 0 01-1.414 1.414zM12.12 13.88a3 3 0 00-4.242 0 1 1 0 01-1.415-1.415 5 5 0 017.072 0 1 1 0 01-1.415 1.415zM9 16a1 1 0 011-1h.01a1 1 0 110 2H10a1 1 0 01-1-1z" clip-rule="evenodd"/></svg>
                    <svg class="w-4 h-3" fill="currentColor" viewBox="0 0 24 12"><rect x="0" y="1" width="20" height="10" rx="2" stroke="currentColor" fill="none" stroke-width="1"/><rect x="21" y="4" width="2" height="4" rx="0.5"/><rect x="2" y="3" width="14" height="6" rx="1" fill="currentColor"/></svg>
                </div>
            </div>

            {{-- Result card content --}}
            <div class="px-6 py-8 text-center min-h-[340px] flex flex-col items-center justify-center">
                <template x-if="result_image">
                    <img :src="'/storage/' + result_image" :alt="result_title || name" class="w-24 h-24 rounded-full object-cover mx-auto mb-5 shadow-sm">
                </template>

                <h2 class="text-2xl font-bold text-gray-900 mb-3" x-text="result_title || name || 'Untitled Outcome'"></h2>

                <p class="text-sm text-gray-600 mb-6 leading-relaxed" x-show="result_message" x-text="result_message"></p>

                <template x-if="redirect_url">
                    <span class="inline-block px-6 py-2.5 rounded-lg text-sm font-medium text-white bg-brand-gold shadow-sm cursor-default"
                        x-text="redirect_type === 'product' ? 'View Product' : 'Continue'"></span>
                </template>
            </div>

            {{-- Home indicator --}}
            <div class="bg-white pb-3 pt-1 flex justify-center">
                <div class="w-28 h-1 bg-gray-300 rounded-full"></div>
            </div>
        </div>

        {{-- Close button --}}
        <button @click="open = false" class="mt-3 px-4 py-1.5 text-sm text-white/80 hover:text-white transition-colors">
            Close <span class="text-xs opacity-60 ml-1">ESC</span>
        </button>
    </div>
</div>
