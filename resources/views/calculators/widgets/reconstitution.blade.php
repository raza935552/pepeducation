{{-- Reconstitution calculator widget. Optional $seed = ['mg'=>,'water'=>,'dose'=>,'doseUnit'=>] pre-fills it. --}}
@php $seed = $seed ?? []; $accent = $accent ?? ($config['accent'] ?? '#2563EB'); @endphp
<div x-data="reconCalc(@js($seed))" class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
    <div class="grid md:grid-cols-2">
        {{-- Inputs --}}
        <div class="p-6 sm:p-8 space-y-5">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Peptide in vial (mg)</label>
                <input type="number" min="0" step="0.5" x-model.number="mg"
                       class="w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Bacteriostatic water (mL)</label>
                <input type="number" min="0" step="0.5" x-model.number="water"
                       class="w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Desired dose</label>
                <div class="flex gap-2">
                    <input type="number" min="0" step="any" x-model.number="dose"
                           class="flex-1 rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500">
                    <select x-model="doseUnit" class="rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500">
                        <option value="mcg">mcg</option>
                        <option value="mg">mg</option>
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Syringe size</label>
                <select x-model.number="syringe" class="w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500">
                    <option value="100">U-100 (1 mL)</option>
                    <option value="50">U-100 (0.5 mL)</option>
                    <option value="30">U-100 (0.3 mL)</option>
                </select>
            </div>
        </div>

        {{-- Results --}}
        <div class="p-6 sm:p-8 bg-surface-50 border-t md:border-t-0 md:border-l border-gray-200 flex flex-col">
            <p class="text-sm font-medium text-gray-500 mb-4">Your result</p>

            <div class="bg-white rounded-xl border border-gray-200 p-5 mb-4 text-center">
                <p class="text-xs uppercase tracking-wide text-gray-400 mb-1">Draw this many units</p>
                <p class="text-4xl font-bold" style="color: {{ $accent }};" x-text="units"></p>
                <p class="text-sm text-gray-500 mt-1"><span x-text="drawMl"></span> mL on a U-100 syringe</p>
            </div>

            {{-- Syringe fill visual --}}
            <div class="mb-4">
                <div class="h-4 rounded-full bg-gray-200 overflow-hidden">
                    <div class="h-full rounded-full transition-all duration-300"
                         style="background-color: {{ $accent }};"
                         :style="`width: ${fillPct}%`"></div>
                </div>
                <div class="flex justify-between text-[11px] text-gray-400 mt-1">
                    <span>0</span><span x-text="syringe + ' units'"></span>
                </div>
            </div>

            <dl class="text-sm space-y-2 mt-auto">
                <div class="flex justify-between"><dt class="text-gray-500">Concentration</dt><dd class="font-semibold text-gray-900"><span x-text="concentration"></span> mcg/mL</dd></div>
                <div class="flex justify-between"><dt class="text-gray-500">Per unit</dt><dd class="font-semibold text-gray-900"><span x-text="mcgPerUnit"></span> mcg</dd></div>
                <div class="flex justify-between"><dt class="text-gray-500">Doses per vial</dt><dd class="font-semibold text-gray-900" x-text="dosesPerVial"></dd></div>
            </dl>
        </div>
    </div>
</div>

<script>
    function reconCalc(seed = {}) {
        return {
            mg: seed.mg ?? 5, water: seed.water ?? 2, dose: seed.dose ?? 250, doseUnit: seed.doseUnit ?? 'mcg', syringe: 100,
            get doseMcg() { return (this.doseUnit === 'mg' ? this.dose * 1000 : this.dose) || 0; },
            get concRaw() { return this.water > 0 ? (this.mg * 1000) / this.water : 0; },
            get concentration() { return this.concRaw ? Math.round(this.concRaw).toLocaleString() : '0'; },
            get mcgPerUnit() { return (this.concRaw / 100).toFixed(1); },
            get unitsRaw() { return this.concRaw > 0 ? this.doseMcg / (this.concRaw / 100) : 0; },
            get units() { return this.unitsRaw ? (Math.round(this.unitsRaw * 10) / 10) : 0; },
            get drawMl() { return (this.unitsRaw / 100).toFixed(3); },
            get fillPct() { return Math.min(100, this.syringe > 0 ? (this.unitsRaw / this.syringe) * 100 : 0); },
            get dosesPerVial() { return this.doseMcg > 0 ? Math.floor((this.mg * 1000) / this.doseMcg) : 0; },
        };
    }
</script>
