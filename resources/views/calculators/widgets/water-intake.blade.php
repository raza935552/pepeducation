{{-- Water intake widget --}}
<div x-data="waterCalc()" class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
    <div class="grid md:grid-cols-2">
        <div class="p-6 sm:p-8 space-y-5">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Body weight</label>
                <div class="flex gap-2">
                    <input type="number" min="0" step="0.5" x-model.number="weight" class="w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500">
                    <select x-model="unit" class="rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500">
                        <option value="lb">lb</option>
                        <option value="kg">kg</option>
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Daily activity / exercise</label>
                <select x-model="activity" class="w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500">
                    <option value="0">Little / none</option>
                    <option value="0.5">~30 min</option>
                    <option value="1">~60 min</option>
                    <option value="1.5">90+ min</option>
                </select>
            </div>
        </div>
        <div class="p-6 sm:p-8 bg-surface-50 border-t md:border-t-0 md:border-l border-gray-200 flex flex-col">
            <div class="flex items-center justify-between mb-4">
                <p class="text-sm font-medium text-gray-500">Daily water target</p>
                @include('calculators.partials._result-actions')
            </div>
            <div class="bg-white rounded-xl border border-gray-200 p-5 text-center">
                <p class="text-xs uppercase tracking-wide text-gray-400 mb-1">Aim for about</p>
                <p class="text-4xl font-bold" style="color: {{ $config['accent'] }};"><span x-text="litres"></span> <span class="text-lg">L</span></p>
                <p class="text-sm text-gray-500 mt-1"><span x-text="ounces"></span> fl oz · <span x-text="cups"></span> cups</p>
            </div>
            <p class="text-xs text-gray-400 mt-auto pt-4">~35 mL/kg baseline plus an exercise allowance. Increase in heat or heavy sweat.</p>
        </div>
    </div>
</div>

<script>
    function waterCalc() {
        const defaults = { weight: 175, unit: 'lb', activity: '0.5' };
        return {
            ...defaults, copied: false,
            reset() { Object.assign(this, defaults); },
            copy() { try { navigator.clipboard.writeText(`Water target: ${this.litres} L (${this.ounces} oz)`); } catch (e) {} this.copied = true; setTimeout(() => this.copied = false, 1500); },
            get kg() { return this.unit === 'kg' ? this.weight : this.weight * 0.453592; },
            get litresRaw() { return (this.kg * 35) / 1000 + parseFloat(this.activity || 0) * 0.5; },
            get litres() { return Math.round(this.litresRaw * 10) / 10; },
            get ounces() { return Math.round(this.litresRaw * 33.814); },
            get cups() { return Math.round(this.litresRaw * 33.814 / 8); },
        };
    }
</script>
