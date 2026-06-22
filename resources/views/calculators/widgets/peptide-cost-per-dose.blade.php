{{-- Peptide cost-per-dose widget --}}
<div x-data="costPerDoseCalc()" class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
    <div class="grid md:grid-cols-2">
        {{-- Inputs --}}
        <div class="p-6 sm:p-8 space-y-5">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Vial price</label>
                <input type="number" min="0" step="1" x-model.number="price" class="w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Peptide in vial (mg)</label>
                <input type="number" min="0" step="0.5" x-model.number="vialMg" class="w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Dose per administration</label>
                <div class="flex gap-2">
                    <input type="number" min="0" step="0.05" x-model.number="dose" class="w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500">
                    <select x-model="doseUnit" class="rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500">
                        <option value="mg">mg</option>
                        <option value="mcg">mcg</option>
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Doses per week</label>
                <input type="number" min="0" step="1" x-model.number="perWeek" class="w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500">
            </div>
        </div>

        {{-- Result --}}
        <div class="p-6 sm:p-8 bg-surface-50 border-t md:border-t-0 md:border-l border-gray-200 flex flex-col">
            <div class="flex items-center justify-between mb-4">
                <p class="text-sm font-medium text-gray-500">Cost breakdown</p>
                @include('calculators.partials._result-actions')
            </div>
            <div class="bg-white rounded-xl border border-gray-200 p-5 mb-4 text-center">
                <p class="text-xs uppercase tracking-wide text-gray-400 mb-1">Cost per dose</p>
                <p class="text-4xl font-bold" style="color: {{ $config['accent'] }};">$<span x-text="costPerDose"></span></p>
            </div>
            <dl class="text-sm space-y-2 mt-auto">
                <div class="flex justify-between"><dt class="text-gray-500">Doses per vial</dt><dd class="font-semibold text-gray-900" x-text="dosesPerVial"></dd></div>
                <div class="flex justify-between"><dt class="text-gray-500">Cost per week</dt><dd class="font-semibold text-gray-900">$<span x-text="costPerWeek"></span></dd></div>
                <div class="flex justify-between"><dt class="text-gray-500">Cost per month</dt><dd class="font-semibold text-gray-900">$<span x-text="costPerMonth"></span></dd></div>
                <div class="flex justify-between"><dt class="text-gray-500">A vial lasts</dt><dd class="font-semibold text-gray-900"><span x-text="weeksPerVial"></span> weeks</dd></div>
            </dl>
        </div>
    </div>
</div>

<script>
    function costPerDoseCalc() {
        const defaults = { price: 60, vialMg: 10, dose: 0.5, doseUnit: 'mg', perWeek: 7 };
        return {
            ...defaults, copied: false,
            reset() { Object.assign(this, defaults); },
            copy() { try { navigator.clipboard.writeText(`Cost per dose: $${this.costPerDose} — ${this.dosesPerVial} doses/vial`); } catch (e) {} this.copied = true; setTimeout(() => this.copied = false, 1500); },
            get doseMg() { return this.doseUnit === 'mcg' ? this.dose / 1000 : this.dose; },
            get dosesPerVialRaw() { return this.doseMg > 0 ? this.vialMg / this.doseMg : 0; },
            get dosesPerVial() { return this.dosesPerVialRaw > 0 ? Math.floor(this.dosesPerVialRaw) : 0; },
            get costPerDose() { return this.dosesPerVialRaw > 0 ? (this.price / this.dosesPerVialRaw).toFixed(2) : '0.00'; },
            get costPerWeek() { return (parseFloat(this.costPerDose) * (this.perWeek || 0)).toFixed(2); },
            get costPerMonth() { return (parseFloat(this.costPerWeek) * 4.345).toFixed(2); },
            get weeksPerVial() { return this.perWeek > 0 ? (this.dosesPerVialRaw / this.perWeek).toFixed(1) : '0'; },
        };
    }
</script>
