<div x-data="{ show: !document.cookie.includes('pp_consent=1') }"
     x-show="show"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="translate-y-full"
     x-transition:enter-end="translate-y-0"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="translate-y-0"
     x-transition:leave-end="translate-y-full"
     class="fixed bottom-0 inset-x-0 z-50 p-4"
     style="display: none;">
    <div class="max-w-4xl mx-auto bg-white rounded-xl shadow-lg border border-gray-200 p-4 sm:p-6 flex flex-col sm:flex-row items-start sm:items-center gap-4">
        <div class="flex-1 text-sm text-gray-600">
            We use cookies to improve your experience and analyze site usage.
            <a href="/privacy-policy" class="underline text-brand-gold hover:text-brand-gold-dark">Learn more</a>
        </div>
        <div class="flex gap-3 shrink-0">
            <button @click="show = false"
                class="px-4 py-2 text-sm text-gray-500 hover:text-gray-700">
                Decline
            </button>
            <button @click="window.PPTracker?.setConsent(true); show = false"
                class="px-5 py-2 bg-brand-gold text-white text-sm font-medium rounded-lg hover:bg-brand-gold-dark">
                Accept
            </button>
        </div>
    </div>
</div>
