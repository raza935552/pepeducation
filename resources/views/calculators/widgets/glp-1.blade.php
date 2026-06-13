{{-- GLP-1 calculator widget — titration schedule + projected timeline --}}
<div x-data="glp1Calc()" class="space-y-6">
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 sm:p-8">
        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Compound</label>
                <select x-model="compound" class="w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500">
                    <option value="semaglutide">Semaglutide</option>
                    <option value="tirzepatide">Tirzepatide</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Units</label>
                <div class="inline-flex rounded-lg border border-gray-300 p-1 w-full">
                    <button type="button" @click="units='metric'" :class="units==='metric' ? 'bg-primary-500 text-white' : 'text-gray-600'" class="flex-1 px-2 py-1.5 rounded-md text-sm font-medium">Metric</button>
                    <button type="button" @click="units='imperial'" :class="units==='imperial' ? 'bg-primary-500 text-white' : 'text-gray-600'" class="flex-1 px-2 py-1.5 rounded-md text-sm font-medium">Imperial</button>
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5"><span x-text="units==='metric' ? 'Height (cm)' : 'Height (in)'"></span></label>
                <input type="number" min="0" x-model.number="height" class="w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5"><span x-text="units==='metric' ? 'Weight (kg)' : 'Weight (lb)'"></span></label>
                <input type="number" min="0" step="0.1" x-model.number="weight" class="w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500">
            </div>
        </div>

        <div class="mt-6 grid grid-cols-2 sm:grid-cols-4 gap-3">
            <div class="bg-surface-50 rounded-xl p-4 text-center">
                <p class="text-xs uppercase tracking-wide text-gray-400">Start BMI</p>
                <p class="text-2xl font-bold text-gray-900" x-text="startBmi"></p>
            </div>
            <div class="bg-surface-50 rounded-xl p-4 text-center">
                <p class="text-xs uppercase tracking-wide text-gray-400">Projected BMI (68w)</p>
                <p class="text-2xl font-bold" style="color: {{ $config['accent'] }};" x-text="endBmi"></p>
            </div>
            <div class="bg-surface-50 rounded-xl p-4 text-center">
                <p class="text-xs uppercase tracking-wide text-gray-400">Maintenance dose</p>
                <p class="text-2xl font-bold text-gray-900" x-text="maintenanceDose"></p>
            </div>
            <div class="bg-surface-50 rounded-xl p-4 text-center">
                <p class="text-xs uppercase tracking-wide text-gray-400">Est. total loss</p>
                <p class="text-2xl font-bold" style="color: {{ $config['accent'] }};" x-text="totalLoss"></p>
            </div>
        </div>
    </div>

    {{-- Titration schedule --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        <h3 class="font-bold text-gray-900 px-6 pt-5">Titration schedule</h3>
        <p class="text-sm text-gray-500 px-6 pb-3">Standard step-up ladder for the selected compound.</p>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-surface-50 text-gray-500"><tr><th class="text-left font-medium px-6 py-2">Weeks</th><th class="text-left font-medium px-6 py-2">Weekly dose</th></tr></thead>
                <tbody>
                    <template x-for="(s, i) in schedule" :key="i">
                        <tr class="border-t border-gray-100"><td class="px-6 py-2.5 text-gray-700" x-text="s.weeks"></td><td class="px-6 py-2.5 font-semibold text-gray-900" x-text="s.dose"></td></tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>

    {{-- Projected timeline --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        <h3 class="font-bold text-gray-900 px-6 pt-5">Projected weight-loss timeline</h3>
        <p class="text-sm text-gray-500 px-6 pb-3">Illustrative trajectory using average published response rates. Individual results vary widely.</p>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-surface-50 text-gray-500"><tr><th class="text-left font-medium px-6 py-2">Milestone</th><th class="text-left font-medium px-6 py-2">Est. loss</th><th class="text-left font-medium px-6 py-2">Weight</th><th class="text-left font-medium px-6 py-2">BMI</th></tr></thead>
                <tbody>
                    <template x-for="(m, i) in timeline" :key="i">
                        <tr class="border-t border-gray-100">
                            <td class="px-6 py-2.5 text-gray-700" x-text="m.label"></td>
                            <td class="px-6 py-2.5 text-gray-500" x-text="m.pct"></td>
                            <td class="px-6 py-2.5 font-semibold text-gray-900" x-text="m.weight"></td>
                            <td class="px-6 py-2.5 text-gray-700" x-text="m.bmi"></td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    function glp1Calc() {
        return {
            compound: 'semaglutide', units: 'metric', height: 175, weight: 90,
            ladders: {
                semaglutide: [
                    { weeks: 'Weeks 1–4', dose: '0.25 mg' }, { weeks: 'Weeks 5–8', dose: '0.5 mg' },
                    { weeks: 'Weeks 9–12', dose: '1.0 mg' }, { weeks: 'Weeks 13–16', dose: '1.7 mg' },
                    { weeks: 'Week 17+', dose: '2.4 mg (maintenance)' },
                ],
                tirzepatide: [
                    { weeks: 'Weeks 1–4', dose: '2.5 mg' }, { weeks: 'Weeks 5–8', dose: '5 mg' },
                    { weeks: 'Weeks 9–12', dose: '7.5 mg' }, { weeks: 'Weeks 13–16', dose: '10 mg' },
                    { weeks: 'Weeks 17–20', dose: '12.5 mg' }, { weeks: 'Week 21+', dose: '15 mg (maintenance)' },
                ],
            },
            // Average cumulative % weight loss at each milestone (study averages, illustrative)
            curves: {
                semaglutide: [ ['Week 4', 0.02], ['Week 12', 0.06], ['Week 24', 0.10], ['Week 52', 0.135], ['Week 68', 0.15] ],
                tirzepatide: [ ['Week 4', 0.03], ['Week 12', 0.075], ['Week 24', 0.15], ['Week 52', 0.19], ['Week 68', 0.21] ],
            },
            get heightM() { return this.units === 'metric' ? (this.height || 0) / 100 : (this.height || 0) * 0.0254; },
            get weightKg() { return this.units === 'metric' ? (this.weight || 0) : (this.weight || 0) * 0.453592; },
            bmiOf(kg) { return this.heightM > 0 ? (kg / (this.heightM * this.heightM)) : 0; },
            get startBmi() { return this.bmiOf(this.weightKg).toFixed(1); },
            get schedule() { return this.ladders[this.compound]; },
            get maintenanceDose() { const l = this.schedule; return l[l.length - 1].dose.split(' ')[0] + ' mg'; },
            get curve() { return this.curves[this.compound]; },
            get totalLoss() { return Math.round(this.curve[this.curve.length - 1][1] * 100) + '%'; },
            get endBmi() { const end = this.weightKg * (1 - this.curve[this.curve.length - 1][1]); return this.bmiOf(end).toFixed(1); },
            fmtWeight(kg) { return this.units === 'metric' ? kg.toFixed(1) + ' kg' : (kg / 0.453592).toFixed(0) + ' lb'; },
            get timeline() {
                return this.curve.map(([label, pct]) => {
                    const w = this.weightKg * (1 - pct);
                    return { label, pct: '−' + Math.round(pct * 100) + '%', weight: this.fmtWeight(w), bmi: this.bmiOf(w).toFixed(1) };
                });
            },
        };
    }
</script>
