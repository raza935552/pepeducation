{{-- Lean body mass (Boer) widget --}}
<div x-data="lbmCalc()" class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
    <div class="grid md:grid-cols-2">
        <div class="p-6 sm:p-8 space-y-5">
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Units</label>
                    <select x-model="unit" class="w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500">
                        <option value="metric">Metric</option>
                        <option value="imperial">Imperial</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Sex</label>
                    <select x-model="sex" class="w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500">
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                    </select>
                </div>
            </div>
            <template x-if="unit === 'metric'">
                <div class="grid grid-cols-2 gap-3">
                    <div><label class="block text-sm font-medium text-gray-700 mb-1.5">Height (cm)</label><input type="number" min="0" step="1" x-model.number="cm" class="w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500"></div>
                    <div><label class="block text-sm font-medium text-gray-700 mb-1.5">Weight (kg)</label><input type="number" min="0" step="0.5" x-model.number="kg" class="w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500"></div>
                </div>
            </template>
            <template x-if="unit === 'imperial'">
                <div class="grid grid-cols-3 gap-3">
                    <div><label class="block text-sm font-medium text-gray-700 mb-1.5">Ft</label><input type="number" min="0" step="1" x-model.number="ft" class="w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500"></div>
                    <div><label class="block text-sm font-medium text-gray-700 mb-1.5">In</label><input type="number" min="0" step="1" x-model.number="inch" class="w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500"></div>
                    <div><label class="block text-sm font-medium text-gray-700 mb-1.5">Lb</label><input type="number" min="0" step="1" x-model.number="lb" class="w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500"></div>
                </div>
            </template>
        </div>
        <div class="p-6 sm:p-8 bg-surface-50 border-t md:border-t-0 md:border-l border-gray-200 flex flex-col">
            <div class="flex items-center justify-between mb-4">
                <p class="text-sm font-medium text-gray-500">Body composition</p>
                @include('calculators.partials._result-actions')
            </div>
            <div class="bg-white rounded-xl border border-gray-200 p-5 mb-4 text-center">
                <p class="text-xs uppercase tracking-wide text-gray-400 mb-1">Lean body mass</p>
                <p class="text-4xl font-bold" style="color: {{ $config['accent'] }};"><span x-text="lbm"></span> <span class="text-lg" x-text="wUnit"></span></p>
            </div>
            <dl class="text-sm space-y-2 mt-auto">
                <div class="flex justify-between"><dt class="text-gray-500">Fat mass (approx.)</dt><dd class="font-semibold text-gray-900"><span x-text="fatMass"></span> <span x-text="wUnit"></span></dd></div>
                <div class="flex justify-between"><dt class="text-gray-500">LBM as % of weight</dt><dd class="font-semibold text-gray-900"><span x-text="lbmPct"></span>%</dd></div>
            </dl>
        </div>
    </div>
</div>

<script>
    function lbmCalc() {
        const defaults = { unit: 'metric', sex: 'male', cm: 178, kg: 80, ft: 5, inch: 10, lb: 176 };
        return {
            ...defaults, copied: false,
            reset() { Object.assign(this, defaults); },
            copy() { try { navigator.clipboard.writeText(`LBM ~${this.lbm} ${this.wUnit} (Boer)`); } catch (e) {} this.copied = true; setTimeout(() => this.copied = false, 1500); },
            get wUnit() { return this.unit === 'metric' ? 'kg' : 'lb'; },
            get kgVal() { return this.unit === 'metric' ? this.kg : this.lb * 0.453592; },
            get cmVal() { return this.unit === 'metric' ? this.cm : (this.ft * 12 + this.inch) * 2.54; },
            get lbmKg() {
                const v = this.sex === 'male'
                    ? 0.407 * this.kgVal + 0.267 * this.cmVal - 19.2
                    : 0.252 * this.kgVal + 0.473 * this.cmVal - 48.3;
                return Math.max(0, Math.min(v, this.kgVal));
            },
            get lbm() { return Math.round((this.unit === 'metric' ? this.lbmKg : this.lbmKg / 0.453592) * 10) / 10; },
            get fatMass() { const fk = this.kgVal - this.lbmKg; return Math.round((this.unit === 'metric' ? fk : fk / 0.453592) * 10) / 10; },
            get lbmPct() { return this.kgVal > 0 ? Math.round(this.lbmKg / this.kgVal * 100) : 0; },
        };
    }
</script>
