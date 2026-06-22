{{-- Weekly weight-loss projection widget --}}
<div x-data="lossProjection()" class="space-y-6">
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="grid md:grid-cols-2">
            <div class="p-6 sm:p-8 space-y-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Starting weight</label>
                    <div class="flex gap-2">
                        <input type="number" min="0" step="0.5" x-model.number="start" class="w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500">
                        <select x-model="unit" class="rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500">
                            <option value="lb">lb</option>
                            <option value="kg">kg</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Rate mode</label>
                    <select x-model="mode" class="w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500">
                        <option value="percent">% of body weight / week</option>
                        <option value="fixed">Fixed amount / week</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5"><span x-text="mode === 'percent' ? 'Weekly rate (%)' : 'Weekly loss (' + unit + ')'"></span></label>
                    <input type="number" min="0" step="0.1" x-model.number="rate" class="w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Weeks to project</label>
                    <input type="number" min="1" max="52" step="1" x-model.number="weeks" class="w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500">
                </div>
            </div>
            <div class="p-6 sm:p-8 bg-surface-50 border-t md:border-t-0 md:border-l border-gray-200 flex flex-col">
                <div class="flex items-center justify-between mb-4">
                    <p class="text-sm font-medium text-gray-500">Projected result</p>
                    @include('calculators.partials._result-actions')
                </div>
                <div class="bg-white rounded-xl border border-gray-200 p-5 text-center">
                    <p class="text-xs uppercase tracking-wide text-gray-400 mb-1">After <span x-text="weeks"></span> weeks</p>
                    <p class="text-4xl font-bold" style="color: {{ $config['accent'] }};"><span x-text="endWeight"></span> <span class="text-lg" x-text="unit"></span></p>
                    <p class="text-sm text-gray-500 mt-1">−<span x-text="totalLost"></span> <span x-text="unit"></span> total</p>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        <h3 class="font-bold text-gray-900 px-6 pt-5">Week-by-week forecast</h3>
        <p class="text-sm text-gray-500 px-6 pb-3" x-text="mode === 'percent' ? 'Percent mode tapers as you get lighter.' : 'Fixed amount removed each week.'"></p>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-surface-50 text-gray-500"><tr><th class="text-left font-medium px-6 py-2">Week</th><th class="text-left font-medium px-6 py-2">Date</th><th class="text-left font-medium px-6 py-2">Projected weight</th></tr></thead>
                <tbody>
                    <template x-for="row in rows" :key="row.week">
                        <tr class="border-t border-gray-100">
                            <td class="px-6 py-2.5 font-semibold text-gray-900" x-text="row.week"></td>
                            <td class="px-6 py-2.5 text-gray-500" x-text="row.date"></td>
                            <td class="px-6 py-2.5 font-semibold" style="color: {{ $config['accent'] }};"><span x-text="row.weight"></span> <span x-text="unit"></span></td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    function lossProjection() {
        const defaults = { unit: 'lb', start: 200, mode: 'percent', rate: 1, weeks: 12 };
        return {
            ...defaults, copied: false,
            reset() { Object.assign(this, defaults); },
            copy() { try { navigator.clipboard.writeText(`After ${this.weeks} weeks: ${this.endWeight} ${this.unit} (−${this.totalLost})`); } catch (e) {} this.copied = true; setTimeout(() => this.copied = false, 1500); },
            fmtDate(w) { const d = new Date(); d.setDate(d.getDate() + w * 7); return d.toLocaleDateString(undefined, { month: 'short', day: 'numeric' }); },
            get rows() {
                const out = [];
                let w = this.start || 0;
                const n = Math.min(Math.max(this.weeks || 1, 1), 52);
                for (let i = 1; i <= n; i++) {
                    const loss = this.mode === 'percent' ? w * (this.rate / 100) : (this.rate || 0);
                    w = Math.max(0, w - loss);
                    out.push({ week: i, date: this.fmtDate(i), weight: Math.round(w * 10) / 10 });
                }
                return out;
            },
            get endWeight() { const r = this.rows; return r.length ? r[r.length - 1].weight : this.start; },
            get totalLost() { return Math.round(((this.start || 0) - this.endWeight) * 10) / 10; },
        };
    }
</script>
