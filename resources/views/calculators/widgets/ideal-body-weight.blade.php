{{-- Ideal body weight (Devine) + healthy BMI range widget --}}
<div x-data="ibwCalc()" class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
    <div class="grid md:grid-cols-2">
        <div class="p-6 sm:p-8 space-y-5">
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Units</label>
                    <select x-model="unit" class="w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500">
                        <option value="imperial">Imperial</option>
                        <option value="metric">Metric</option>
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
            <template x-if="unit === 'imperial'">
                <div class="grid grid-cols-2 gap-3">
                    <div><label class="block text-sm font-medium text-gray-700 mb-1.5">Height (ft)</label><input type="number" min="0" step="1" x-model.number="ft" class="w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500"></div>
                    <div><label class="block text-sm font-medium text-gray-700 mb-1.5">(in)</label><input type="number" min="0" step="1" x-model.number="inch" class="w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500"></div>
                </div>
            </template>
            <template x-if="unit === 'metric'">
                <div><label class="block text-sm font-medium text-gray-700 mb-1.5">Height (cm)</label><input type="number" min="0" step="1" x-model.number="cm" class="w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500"></div>
            </template>
        </div>
        <div class="p-6 sm:p-8 bg-surface-50 border-t md:border-t-0 md:border-l border-gray-200 flex flex-col">
            <div class="flex items-center justify-between mb-4">
                <p class="text-sm font-medium text-gray-500">Reference weight</p>
                @include('calculators.partials._result-actions')
            </div>
            <div class="bg-white rounded-xl border border-gray-200 p-5 mb-4 text-center">
                <p class="text-xs uppercase tracking-wide text-gray-400 mb-1">Ideal (Devine)</p>
                <p class="text-4xl font-bold" style="color: {{ $config['accent'] }};"><span x-text="ibw"></span> <span class="text-lg" x-text="wUnit"></span></p>
            </div>
            <dl class="text-sm mt-auto">
                <div class="flex justify-between"><dt class="text-gray-500">Healthy range (BMI 18.5–24.9)</dt><dd class="font-semibold text-gray-900"><span x-text="rangeLow"></span>–<span x-text="rangeHigh"></span> <span x-text="wUnit"></span></dd></div>
            </dl>
        </div>
    </div>
</div>

<script>
    function ibwCalc() {
        const defaults = { unit: 'imperial', sex: 'male', ft: 5, inch: 10, cm: 178 };
        return {
            ...defaults, copied: false,
            reset() { Object.assign(this, defaults); },
            copy() { try { navigator.clipboard.writeText(`Ideal weight ~${this.ibw} ${this.wUnit} (healthy ${this.rangeLow}–${this.rangeHigh})`); } catch (e) {} this.copied = true; setTimeout(() => this.copied = false, 1500); },
            get wUnit() { return this.unit === 'metric' ? 'kg' : 'lb'; },
            get cmVal() { return this.unit === 'metric' ? this.cm : (this.ft * 12 + this.inch) * 2.54; },
            get inchesOver5ft() { const totalIn = this.cmVal / 2.54; return Math.max(0, totalIn - 60); },
            get ibwKg() { const base = this.sex === 'male' ? 50 : 45.5; return Math.max(0, base + 2.3 * this.inchesOver5ft); },
            get ibw() { return Math.round(this.unit === 'metric' ? this.ibwKg : this.ibwKg / 0.453592); },
            get mVal() { return this.cmVal / 100; },
            get rangeLow() { const kg = 18.5 * this.mVal * this.mVal; return Math.round(this.unit === 'metric' ? kg : kg / 0.453592); },
            get rangeHigh() { const kg = 24.9 * this.mVal * this.mVal; return Math.round(this.unit === 'metric' ? kg : kg / 0.453592); },
        };
    }
</script>
