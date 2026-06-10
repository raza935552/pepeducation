@props([
    'base' => '',
    'defaultCampaign' => '',
])

{{-- Meta-ad destination URL builder. The utm_* entered here are the REAL ad params
     that flow Ad → Lander → Biolinx (forwarded verbatim by the CTA hand-off). --}}
<div
    x-data="landerAdUrl({
        base: @js($base),
        source: 'facebook',
        medium: 'paid_social',
        campaign: @js($defaultCampaign),
        content: '',
        term: '',
    })"
    class="rounded-xl border border-gray-200 bg-gray-50 p-4 max-w-xl"
>
    <div class="grid grid-cols-2 gap-3">
        <label class="block">
            <span class="block text-[11px] font-semibold text-gray-500 uppercase mb-1">utm_source</span>
            <input type="text" x-model="source" placeholder="facebook" class="w-full rounded-lg border-gray-300 text-sm py-1.5">
        </label>
        <label class="block">
            <span class="block text-[11px] font-semibold text-gray-500 uppercase mb-1">utm_medium</span>
            <input type="text" x-model="medium" placeholder="paid_social" class="w-full rounded-lg border-gray-300 text-sm py-1.5">
        </label>
        <label class="block">
            <span class="block text-[11px] font-semibold text-gray-500 uppercase mb-1">utm_campaign <span class="text-gray-400 normal-case font-normal">(campaign name)</span></span>
            <input type="text" x-model="campaign" placeholder="summer_research" class="w-full rounded-lg border-gray-300 text-sm py-1.5">
        </label>
        <label class="block">
            <span class="block text-[11px] font-semibold text-gray-500 uppercase mb-1">utm_content <span class="text-gray-400 normal-case font-normal">(ad / creative)</span></span>
            <input type="text" x-model="content" placeholder="ad_variant_a" class="w-full rounded-lg border-gray-300 text-sm py-1.5">
        </label>
        <label class="block col-span-2">
            <span class="block text-[11px] font-semibold text-gray-500 uppercase mb-1">utm_term <span class="text-gray-400 normal-case font-normal">(optional — audience / keyword)</span></span>
            <input type="text" x-model="term" placeholder="" class="w-full rounded-lg border-gray-300 text-sm py-1.5">
        </label>
    </div>

    <div class="mt-3">
        <span class="block text-[11px] font-semibold text-gray-500 uppercase mb-1">Ad destination URL</span>
        <div class="flex items-stretch gap-2">
            <input type="text" :value="url" readonly @focus="$event.target.select()"
                   class="flex-1 rounded-lg border-gray-300 bg-white text-xs font-mono py-2 text-gray-700">
            <button type="button" @click="copy()"
                    class="shrink-0 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4"
                    x-text="copied ? 'Copied ✓' : 'Copy'"></button>
        </div>
        <p class="mt-1.5 text-[11px] text-gray-400">Paste this as your Meta ad's website URL. These utm_* values are what Biolinx records for this lander.</p>
    </div>
</div>

@once
@push('scripts')
<script>
function landerAdUrl(init) {
    return {
        base: init.base,
        source: init.source || '',
        medium: init.medium || '',
        campaign: init.campaign || '',
        content: init.content || '',
        term: init.term || '',
        copied: false,
        get url() {
            const params = new URLSearchParams();
            const add = (k, v) => { v = (v || '').trim(); if (v) params.set(k, v); };
            add('utm_source', this.source);
            add('utm_medium', this.medium);
            add('utm_campaign', this.campaign);
            add('utm_content', this.content);
            add('utm_term', this.term);
            const qs = params.toString();
            return qs ? this.base + '?' + qs : this.base;
        },
        copy() {
            const done = () => { this.copied = true; setTimeout(() => this.copied = false, 1600); };
            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(this.url).then(done).catch(done);
            } else {
                const t = document.createElement('textarea');
                t.value = this.url; document.body.appendChild(t); t.select();
                try { document.execCommand('copy'); } catch (e) {}
                document.body.removeChild(t); done();
            }
        },
    };
}
</script>
@endpush
@endonce
