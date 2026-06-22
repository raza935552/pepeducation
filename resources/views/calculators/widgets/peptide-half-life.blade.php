{{-- Peptide half-life / clearance widget --}}
<div x-data="halfLifeCalc()" class="space-y-6">
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="grid md:grid-cols-2">
            {{-- Inputs --}}
            <div class="p-6 sm:p-8 space-y-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Half-life (hours)</label>
                    <input type="number" min="0" step="0.1" x-model.number="halfLife" class="w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Starting dose <span class="text-gray-400">(optional)</span></label>
                    <div class="flex gap-2">
                        <input type="number" min="0" step="0.1" x-model.number="dose" class="w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500">
                        <select x-model="doseUnit" class="rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500">
                            <option value="mg">mg</option>
                            <option value="mcg">mcg</option>
                        </select>
                    </div>
                </div>
            </div>
            {{-- Result --}}
            <div class="p-6 sm:p-8 bg-surface-50 border-t md:border-t-0 md:border-l border-gray-200 flex flex-col">
                <div class="flex items-center justify-between mb-4">
                    <p class="text-sm font-medium text-gray-500">Practically cleared after</p>
                    @include('calculators.partials._result-actions')
                </div>
                <div class="bg-white rounded-xl border border-gray-200 p-5 mb-4 text-center">
                    <p class="text-xs uppercase tracking-wide text-gray-400 mb-1">≈97% gone (5 half-lives)</p>
                    <p class="text-4xl font-bold" style="color: {{ $config['accent'] }};" x-text="clearText"></p>
                </div>
                <p class="text-xs text-gray-400 mt-auto">After five half-lives ~96.9% of the compound has cleared — the common rule of thumb for an effective washout.</p>
            </div>
        </div>
    </div>

    {{-- Decay table --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        <h3 class="font-bold text-gray-900 px-6 pt-5">Amount remaining over time</h3>
        <p class="text-sm text-gray-500 px-6 pb-3">Each half-life removes half of what is left.</p>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-surface-50 text-gray-500"><tr><th class="text-left font-medium px-6 py-2">Half-lives</th><th class="text-left font-medium px-6 py-2">Time elapsed</th><th class="text-left font-medium px-6 py-2">% remaining</th><th class="text-left font-medium px-6 py-2">Amount left</th></tr></thead>
                <tbody>
                    <template x-for="row in rows" :key="row.n">
                        <tr class="border-t border-gray-100">
                            <td class="px-6 py-2.5 font-semibold text-gray-900" x-text="row.n"></td>
                            <td class="px-6 py-2.5 text-gray-500" x-text="row.time"></td>
                            <td class="px-6 py-2.5 text-gray-500" x-text="row.pct + '%'"></td>
                            <td class="px-6 py-2.5 font-semibold" style="color: {{ $config['accent'] }};" x-text="row.amount"></td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    function halfLifeCalc() {
        const defaults = { halfLife: 4, dose: null, doseUnit: 'mg' };
        return {
            ...defaults, copied: false,
            reset() { Object.assign(this, defaults); },
            copy() { try { navigator.clipboard.writeText(`Half-life ${this.halfLife}h → ~97% cleared after ${this.clearText}`); } catch (e) {} this.copied = true; setTimeout(() => this.copied = false, 1500); },
            fmtTime(h) {
                if (!h || h <= 0) return '0 h';
                if (h < 48) return (Math.round(h * 10) / 10) + ' h';
                return (Math.round((h / 24) * 10) / 10) + ' days';
            },
            get clearText() { return this.fmtTime((this.halfLife || 0) * 5); },
            get rows() {
                const out = [];
                for (let n = 1; n <= 5; n++) {
                    const pct = 100 / Math.pow(2, n);
                    out.push({
                        n,
                        time: this.fmtTime((this.halfLife || 0) * n),
                        pct: pct < 1 ? pct.toFixed(2) : pct.toFixed(pct % 1 === 0 ? 0 : 2),
                        amount: this.dose > 0 ? (this.dose * pct / 100).toPrecision(3) + ' ' + this.doseUnit : '—',
                    });
                }
                return out;
            },
        };
    }
</script>
