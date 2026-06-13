{{-- Reset + Copy buttons for a calculator result header. Requires reset(), copy(), copied in the Alpine scope. --}}
<div class="flex gap-1.5">
    <button type="button" @click="reset()" class="text-xs inline-flex items-center gap-1 px-2 py-1 rounded-md border border-gray-200 text-gray-400 hover:bg-white transition-colors">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>Reset
    </button>
    <button type="button" @click="copy()" class="text-xs inline-flex items-center gap-1 px-2 py-1 rounded-md border border-gray-200 text-gray-500 hover:bg-white transition-colors">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg><span x-text="copied ? 'Copied!' : 'Copy'"></span>
    </button>
</div>
