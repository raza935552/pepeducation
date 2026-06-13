{{-- GLP-1 dose converter widget — weekly mg → units + ladder table --}}
<div x-data="glpUnitsCalc()" class="space-y-6">
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="grid md:grid-cols-2">
            {{-- Inputs --}}
            <div class="p-6 sm:p-8 space-y-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Compound</label>
                    <select x-model="compound" class="w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500">
                        <option value="semaglutide">Semaglutide</option>
                        <option value="tirzepatide">Tirzepatide</option>
                        <option value="custom">Custom</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Peptide in vial (mg)</label>
                    <input type="number" min="0" step="0.5" x-model.number="mg" class="w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Bacteriostatic water (mL)</label>
                    <input type="number" min="0" step="0.5" x-model.number="water" class="w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Weekly dose (mg)</label>
                    <input type="number" min="0" step="0.05" x-model.number="dose" class="w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500">
                </div>
            </div>

            {{-- Result --}}
            <div class="p-6 sm:p-8 bg-surface-50 border-t md:border-t-0 md:border-l border-gray-200 flex flex-col">
                <p class="text-sm font-medium text-gray-500 mb-4">Draw for your dose</p>
                <div class="bg-white rounded-xl border border-gray-200 p-5 mb-4 text-center">
                    <p class="text-xs uppercase tracking-wide text-gray-400 mb-1">Units on a U-100 syringe</p>
                    <p class="text-4xl font-bold" style="color: {{ $config['accent'] }};" x-text="units(dose)"></p>
                    <p class="text-sm text-gray-500 mt-1"><span x-text="ml(dose)"></span> mL</p>
                </div>
                <dl class="text-sm space-y-2 mt-auto">
                    <div class="flex justify-between"><dt class="text-gray-500">Concentration</dt><dd class="font-semibold text-gray-900"><span x-text="concMgMl"></span> mg/mL</dd></div>
                    <div class="flex justify-between"><dt class="text-gray-500">Weekly doses per vial</dt><dd class="font-semibold text-gray-900" x-text="dosesPerVial"></dd></div>
                </dl>
            </div>
        </div>
    </div>

    {{-- Dose ladder reference --}}
    <div x-show="ladder.length" class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        <h3 class="font-bold text-gray-900 px-6 pt-5">Dose ladder — units for your reconstitution</h3>
        <p class="text-sm text-gray-500 px-6 pb-3">Every standard titration step converted for your vial.</p>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-surface-50 text-gray-500"><tr><th class="text-left font-medium px-6 py-2">Weekly dose</th><th class="text-left font-medium px-6 py-2">mL</th><th class="text-left font-medium px-6 py-2">Units</th></tr></thead>
                <tbody>
                    <template x-for="(d, i) in ladder" :key="i">
                        <tr class="border-t border-gray-100"><td class="px-6 py-2.5 font-semibold text-gray-900" x-text="d + ' mg'"></td><td class="px-6 py-2.5 text-gray-500" x-text="ml(d)"></td><td class="px-6 py-2.5 font-semibold" style="color: {{ $config['accent'] }};" x-text="units(d)"></td></tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    function glpUnitsCalc() {
        return {
            compound: 'semaglutide', mg: 5, water: 2, dose: 0.25,
            ladders: { semaglutide: [0.25, 0.5, 1.0, 1.7, 2.4], tirzepatide: [2.5, 5, 7.5, 10, 12.5, 15], custom: [] },
            get ladder() { return this.ladders[this.compound] || []; },
            get concMgMlRaw() { return this.water > 0 ? this.mg / this.water : 0; },
            get concMgMl() { return this.concMgMlRaw ? this.concMgMlRaw.toFixed(2) : '0'; },
            ml(d) { return this.concMgMlRaw > 0 ? (d / this.concMgMlRaw).toFixed(3) : '0'; },
            units(d) { return this.concMgMlRaw > 0 ? (Math.round((d / this.concMgMlRaw) * 100 * 10) / 10) : 0; },
            get dosesPerVial() { return this.dose > 0 ? Math.floor(this.mg / this.dose) : 0; },
        };
    }
</script>
