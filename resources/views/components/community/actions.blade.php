@props(['type', 'id', 'likes' => 0, 'liked' => false])

<div x-data="communityActions({ type: '{{ $type }}', id: {{ (int) $id }}, liked: {{ $liked ? 'true' : 'false' }}, count: {{ (int) $likes }} })"
     class="flex items-center gap-4 text-sm">

    {{-- Like --}}
    <button type="button" @click="toggleLike()" :disabled="busy"
            class="inline-flex items-center gap-1.5 transition"
            :class="liked ? 'text-rose-600 font-semibold' : 'text-gray-500 hover:text-rose-600'">
        <svg class="h-4 w-4" :fill="liked ? 'currentColor' : 'none'" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
        </svg>
        <span x-text="count"></span>
    </button>

    {{-- Report --}}
    <div class="relative" x-data="{ open: false }" @click.outside="open = false">
        <button type="button" @click="open = !open" class="text-gray-400 hover:text-gray-600">Report</button>
        <div x-show="open" x-cloak x-transition
             class="absolute right-0 z-10 mt-2 w-56 rounded-lg border border-gray-200 bg-white p-3 shadow-lg">
            <template x-if="!reported">
                <div>
                    <p class="text-xs font-medium text-gray-700 mb-2">Why are you reporting this?</p>
                    <select x-model="reason" class="w-full text-sm rounded border-gray-300 mb-2">
                        <option value="spam">Spam or advertising</option>
                        <option value="harassment">Harassment or abuse</option>
                        <option value="medical_claims">Unsafe / medical claims</option>
                        <option value="off_topic">Off-topic</option>
                        <option value="other">Other</option>
                    </select>
                    <textarea x-model="details" rows="2" maxlength="500" placeholder="Add detail (optional)"
                              class="w-full text-sm rounded border-gray-300 mb-2"></textarea>
                    <button type="button" @click="submitReport(); open = false"
                            class="w-full rounded bg-gray-900 text-white text-sm py-1.5 hover:bg-gray-700">Submit report</button>
                </div>
            </template>
            <p x-show="reported" class="text-xs text-emerald-700">Thanks — our moderators will review this.</p>
        </div>
    </div>
</div>

@once
    @push('head')
        <style>[x-cloak]{display:none!important}</style>
    @endpush
    @push('scripts')
        <script>
            function communityActions(init) {
                return {
                    type: init.type, id: init.id,
                    liked: init.liked, count: init.count,
                    busy: false, reason: 'spam', details: '', reported: false,
                    csrf: document.querySelector('meta[name="csrf-token"]').content,
                    async toggleLike() {
                        if (this.busy) return; this.busy = true;
                        try {
                            const r = await fetch('{{ route('community.react') }}', {
                                method: 'POST',
                                headers: {'Content-Type':'application/json','X-CSRF-TOKEN':this.csrf,'Accept':'application/json'},
                                body: JSON.stringify({ type: this.type, id: this.id })
                            });
                            if (r.ok) { const d = await r.json(); this.liked = d.liked; this.count = d.count; }
                        } finally { this.busy = false; }
                    },
                    async submitReport() {
                        try {
                            const r = await fetch('{{ route('community.report') }}', {
                                method: 'POST',
                                headers: {'Content-Type':'application/json','X-CSRF-TOKEN':this.csrf,'Accept':'application/json'},
                                body: JSON.stringify({ type: this.type, id: this.id, reason: this.reason, details: this.details })
                            });
                            if (r.ok) this.reported = true;
                        } catch (e) {}
                    }
                }
            }
        </script>
    @endpush
@endonce
