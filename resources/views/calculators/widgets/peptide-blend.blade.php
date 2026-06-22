{{-- Peptide blend (two peptides, one vial) reconstitution widget --}}
<div x-data="blendCalc()" class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
    <div class="grid md:grid-cols-2">
        {{-- Inputs --}}
        <div class="p-6 sm:p-8 space-y-5">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Peptide A in vial (mg)</label>
                <input type="number" min="0" step="0.5" x-model.number="mgA" class="w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Peptide B in vial (mg)</label>
                <input type="number" min="0" step="0.5" x-model.number="mgB" class="w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Shared bacteriostatic water (mL)</label>
                <input type="number" min="0" step="0.5" x-model.number="water" class="w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Target dose of Peptide A</label>
                <div class="flex gap-2">
                    <input type="number" min="0" step="0.05" x-model.number="doseA" class="w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500">
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
                <p class="text-sm font-medium text-gray-500">One draw delivers both</p>
                @include('calculators.partials._result-actions')
            </div>
            <div class="bg-white rounded-xl border border-gray-200 p-5 mb-4 text-center">
                <p class="text-xs uppercase tracking-wide text-gray-400 mb-1">Units on a U-100 syringe</p>
                <p class="text-4xl font-bold" style="color: {{ $config['accent'] }};" x-text="units"></p>
                <p class="text-sm text-gray-500 mt-1"><span x-text="mlDraw"></span> mL</p>
            </div>
            <dl class="text-sm space-y-2 mt-auto">
                <div class="flex justify-between"><dt class="text-gray-500">Peptide A per draw</dt><dd class="font-semibold text-gray-900"><span x-text="deliveredA"></span></dd></div>
                <div class="flex justify-between"><dt class="text-gray-500">Peptide B per draw</dt><dd class="font-semibold" style="color: {{ $config['accent'] }};"><span x-text="deliveredB"></span></dd></div>
                <div class="flex justify-between"><dt class="text-gray-500">Concentration A / B</dt><dd class="font-semibold text-gray-900"><span x-text="concA"></span> / <span x-text="concB"></span> mg/mL</dd></div>
            </dl>
        </div>
    </div>
</div>

<script>
    function blendCalc() {
        const defaults = { mgA: 10, mgB: 10, water: 2, doseA: 0.25, doseUnit: 'mg' };
        return {
            ...defaults, copied: false,
            reset() { Object.assign(this, defaults); },
            copy() { try { navigator.clipboard.writeText(`${this.units} units (${this.mlDraw} mL) → A ${this.deliveredA}, B ${this.deliveredB}`); } catch (e) {} this.copied = true; setTimeout(() => this.copied = false, 1500); },
            get doseAmg() { return this.doseUnit === 'mcg' ? this.doseA / 1000 : this.doseA; },
            get concAraw() { return this.water > 0 ? this.mgA / this.water : 0; },
            get concBraw() { return this.water > 0 ? this.mgB / this.water : 0; },
            get concA() { return this.concAraw.toFixed(2); },
            get concB() { return this.concBraw.toFixed(2); },
            get mlDrawRaw() { return this.concAraw > 0 ? this.doseAmg / this.concAraw : 0; },
            get mlDraw() { return this.mlDrawRaw.toFixed(3); },
            get units() { return Math.round(this.mlDrawRaw * 100 * 10) / 10; },
            fmtDose(mg) {
                if (mg <= 0) return '0';
                return mg < 1 ? (Math.round(mg * 1000)) + ' mcg' : (Math.round(mg * 1000) / 1000) + ' mg';
            },
            get deliveredA() { return this.fmtDose(this.mlDrawRaw * this.concAraw); },
            get deliveredB() { return this.fmtDose(this.mlDrawRaw * this.concBraw); },
        };
    }
</script>
