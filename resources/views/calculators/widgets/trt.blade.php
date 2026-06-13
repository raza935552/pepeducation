{{-- TRT calculator widget — weekly target → per-injection volume by cadence --}}
<div x-data="trtCalc()" class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
    <div class="grid md:grid-cols-2">
        {{-- Inputs --}}
        <div class="p-6 sm:p-8 space-y-5">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Weekly testosterone target (mg)</label>
                <input type="number" min="0" step="5" x-model.number="weekly" class="w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500">
                <p class="text-xs text-gray-400 mt-1">Typical clinical range is often 100–200 mg/week (reference only).</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Ester concentration (mg/mL)</label>
                <select x-model.number="conc" class="w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500">
                    <option value="200">200 mg/mL (most common)</option>
                    <option value="250">250 mg/mL</option>
                    <option value="100">100 mg/mL</option>
                </select>
            </div>
            <div class="bg-red-50 border border-red-200 rounded-lg p-3">
                <p class="text-xs text-red-700">Testosterone is a Schedule III controlled substance. This is an arithmetic aid for education only — not a recommendation to use it. Hormone therapy requires a licensed physician and bloodwork.</p>
            </div>
        </div>

        {{-- Results --}}
        <div class="p-6 sm:p-8 bg-surface-50 border-t md:border-t-0 md:border-l border-gray-200">
            <p class="text-sm font-medium text-gray-500 mb-4">Per-injection by cadence</p>
            <div class="space-y-3">
                <template x-for="(row, i) in cadences" :key="i">
                    <div class="bg-white rounded-xl border border-gray-200 p-4">
                        <div class="flex items-center justify-between mb-1">
                            <span class="font-semibold text-gray-900" x-text="row.name"></span>
                            <span class="text-xs text-gray-400" x-text="row.freq"></span>
                        </div>
                        <div class="flex items-baseline gap-3">
                            <span class="text-2xl font-bold" style="color: {{ $config['accent'] }};" x-text="row.units + ' units'"></span>
                            <span class="text-sm text-gray-500"><span x-text="row.ml"></span> mL · <span x-text="row.mg"></span> mg</span>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>
</div>

<script>
    function trtCalc() {
        return {
            weekly: 140, conc: 200,
            row(name, freq, perWeek) {
                const mg = perWeek > 0 ? this.weekly / perWeek : 0;
                const ml = this.conc > 0 ? mg / this.conc : 0;
                return { name, freq, mg: mg.toFixed(1), ml: ml.toFixed(2), units: (ml * 100).toFixed(0) };
            },
            get cadences() {
                return [
                    this.row('Once weekly', '1×/week', 1),
                    this.row('Twice weekly', '2×/week', 2),
                    this.row('Every other day', '≈3.5×/week', 3.5),
                ];
            },
        };
    }
</script>
