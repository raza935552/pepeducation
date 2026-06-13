{{-- Melanotan II calculator widget — reconstitution + illustrative schedule --}}
<div x-data="mtCalc()" class="space-y-6">
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="grid md:grid-cols-2">
            {{-- Inputs --}}
            <div class="p-6 sm:p-8 space-y-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">MT-II in vial (mg)</label>
                    <input type="number" min="0" step="0.5" x-model.number="mg" class="w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Bacteriostatic water (mL)</label>
                    <input type="number" min="0" step="0.5" x-model.number="water" class="w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Target micro-dose (mcg)</label>
                    <input type="number" min="0" step="10" x-model.number="dose" class="w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500">
                </div>
            </div>
            {{-- Results --}}
            <div class="p-6 sm:p-8 bg-surface-50 border-t md:border-t-0 md:border-l border-gray-200 flex flex-col">
                <div class="flex items-center justify-between mb-4">
                    <p class="text-sm font-medium text-gray-500">Your result</p>
                    @include('calculators.partials._result-actions')
                </div>
                <div class="bg-white rounded-xl border border-gray-200 p-5 mb-4 text-center">
                    <p class="text-xs uppercase tracking-wide text-gray-400 mb-1">Draw this many units</p>
                    <p class="text-4xl font-bold" style="color: {{ $config['accent'] }};" x-text="units"></p>
                    <p class="text-sm text-gray-500 mt-1"><span x-text="drawMl"></span> mL on a U-100 syringe</p>
                </div>
                <dl class="text-sm space-y-2 mt-auto">
                    <div class="flex justify-between"><dt class="text-gray-500">Concentration</dt><dd class="font-semibold text-gray-900"><span x-text="concentration"></span> mcg/mL</dd></div>
                    <div class="flex justify-between"><dt class="text-gray-500">Per unit</dt><dd class="font-semibold text-gray-900"><span x-text="mcgPerUnit"></span> mcg</dd></div>
                    <div class="flex justify-between"><dt class="text-gray-500">Doses per vial</dt><dd class="font-semibold text-gray-900" x-text="dosesPerVial"></dd></div>
                </dl>
            </div>
        </div>
    </div>

    {{-- Illustrative schedule --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        <h3 class="font-bold text-gray-900 px-6 pt-5">Illustrative schedule pattern</h3>
        <p class="text-sm text-gray-500 px-6 pb-3">A common loading-then-maintenance pattern, scaled to your target dose. Reference only.</p>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-surface-50 text-gray-500"><tr><th class="text-left font-medium px-6 py-2">Phase</th><th class="text-left font-medium px-6 py-2">Per dose</th><th class="text-left font-medium px-6 py-2">Frequency</th><th class="text-left font-medium px-6 py-2">Units</th></tr></thead>
                <tbody>
                    <template x-for="(p, i) in phases" :key="i">
                        <tr class="border-t border-gray-100"><td class="px-6 py-2.5 font-semibold text-gray-900" x-text="p.phase"></td><td class="px-6 py-2.5 text-gray-700" x-text="p.dose"></td><td class="px-6 py-2.5 text-gray-500" x-text="p.freq"></td><td class="px-6 py-2.5 text-gray-700" x-text="p.units"></td></tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    function mtCalc() {
        const defaults = { mg: 10, water: 2, dose: 250 };
        return {
            ...defaults, copied: false,
            reset() { Object.assign(this, defaults); },
            copy() { try { navigator.clipboard.writeText(`Draw ${this.units} units (${this.drawMl} mL) — ${this.dose} mcg MT-II`); } catch (e) {} this.copied = true; setTimeout(() => this.copied = false, 1500); },
            get concRaw() { return this.water > 0 ? (this.mg * 1000) / this.water : 0; },
            get concentration() { return this.concRaw ? Math.round(this.concRaw).toLocaleString() : '0'; },
            get mcgPerUnit() { return (this.concRaw / 100).toFixed(1); },
            unitsFor(mcg) { return this.concRaw > 0 ? mcg / (this.concRaw / 100) : 0; },
            get unitsRaw() { return this.unitsFor(this.dose); },
            get units() { return this.unitsRaw ? (Math.round(this.unitsRaw * 10) / 10) : 0; },
            get drawMl() { return (this.unitsRaw / 100).toFixed(3); },
            get dosesPerVial() { return this.dose > 0 ? Math.floor((this.mg * 1000) / this.dose) : 0; },
            get phases() {
                const half = Math.round(this.dose / 2);
                return [
                    { phase: 'Loading (week 1)', dose: half + ' mcg', freq: 'daily', units: this.unitsFor(half).toFixed(1) },
                    { phase: 'Loading (week 2)', dose: this.dose + ' mcg', freq: 'daily', units: this.unitsFor(this.dose).toFixed(1) },
                    { phase: 'Maintenance', dose: this.dose + ' mcg', freq: '2–3×/week', units: this.unitsFor(this.dose).toFixed(1) },
                ];
            },
        };
    }
</script>
