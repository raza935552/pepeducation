{{-- Calculator email capture → /subscriber/sync (Customer.io + Biolinx Lead). Pass $source. --}}
@php $source = $source ?? 'calculator'; @endphp
<section class="py-10 bg-white">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div x-data="calcEmailCapture(@js($source))"
             class="rounded-2xl border border-primary-200 bg-gradient-to-br from-primary-50 to-white p-6 sm:p-8">
            <template x-if="!done">
                <div class="sm:flex sm:items-center sm:gap-8">
                    <div class="sm:flex-1">
                        <p class="text-xs font-semibold uppercase tracking-wider text-primary-600 mb-1.5">Free · No spam</p>
                        <h2 class="text-xl font-bold text-gray-900 mb-1">Peptide dosing &amp; reconstitution tips by email</h2>
                        <p class="text-sm text-gray-600">Drop your email for occasional research-backed dosing guides, protocol breakdowns and new-calculator updates.</p>
                    </div>
                    <form @submit.prevent="submit()" class="mt-4 sm:mt-0 sm:w-80 shrink-0">
                        <div class="flex flex-col gap-2">
                            <input type="email" x-model="email" required placeholder="you@email.com" autocomplete="email"
                                   class="w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500">
                            <button type="submit" :disabled="loading"
                                    class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg bg-primary-600 text-white font-semibold text-sm hover:bg-primary-700 disabled:opacity-60 transition-colors">
                                <span x-text="loading ? 'Sending…' : 'Send me dosing tips'"></span>
                            </button>
                        </div>
                        <p x-show="error" x-text="error" class="text-xs text-red-500 mt-1.5"></p>
                        <p class="text-[11px] text-gray-400 mt-1.5">Research use only · No purchase necessary · Unsubscribe anytime.</p>
                    </form>
                </div>
            </template>
            <template x-if="done">
                <div class="flex items-center gap-3 py-2">
                    <span class="w-10 h-10 rounded-full bg-green-100 text-green-600 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    </span>
                    <div>
                        <p class="font-semibold text-gray-900">You're in — check your inbox.</p>
                        <p class="text-sm text-gray-600">Dosing tips and protocol guides are on the way.</p>
                    </div>
                </div>
            </template>
        </div>
    </div>
</section>

<script>
    function calcEmailCapture(source) {
        return {
            email: '', loading: false, done: false, error: '',
            async submit() {
                if (!/^[^@\s]+@[^@\s]+\.[^@\s]+$/.test(this.email)) { this.error = 'Please enter a valid email.'; return; }
                this.loading = true; this.error = '';
                try {
                    const r = await fetch(@js(route('subscriber.sync')), {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                        body: JSON.stringify({ email: this.email, source }),
                    });
                    if (r.ok) { this.done = true; }
                    else { const d = await r.json().catch(() => ({})); this.error = d.message || 'Something went wrong — please try again.'; }
                } catch (e) { this.error = 'Network error — please try again.'; }
                this.loading = false;
            },
        };
    }
</script>
