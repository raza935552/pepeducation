{{-- Protein intake widget --}}
<div x-data="proteinCalc()" class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
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
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Goal</label>
                <select x-model="goal" class="w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500">
                    <option value="maintain">Maintain / general health</option>
                    <option value="lose">Lose fat (preserve muscle)</option>
                    <option value="build">Build muscle</option>
                </select>
            </div>
        </div>
        <div class="p-6 sm:p-8 bg-surface-50 border-t md:border-t-0 md:border-l border-gray-200 flex flex-col">
            <div class="flex items-center justify-between mb-4">
                <p class="text-sm font-medium text-gray-500">Daily protein target</p>
                @include('calculators.partials._result-actions')
            </div>
            <div class="bg-white rounded-xl border border-gray-200 p-5 text-center">
                <p class="text-xs uppercase tracking-wide text-gray-400 mb-1">Recommended range</p>
                <p class="text-4xl font-bold" style="color: {{ $config['accent'] }};"><span x-text="low"></span>–<span x-text="high"></span></p>
                <p class="text-sm text-gray-500 mt-1">grams / day</p>
            </div>
            <p class="text-xs text-gray-400 mt-auto pt-4">Based on <span x-text="range[0]"></span>–<span x-text="range[1]"></span> g/kg for your goal.</p>
        </div>
    </div>
</div>

<script>
    function proteinCalc() {
        const defaults = { weight: 175, unit: 'lb', goal: 'lose' };
        return {
            ...defaults, copied: false,
            reset() { Object.assign(this, defaults); },
            copy() { try { navigator.clipboard.writeText(`Protein target: ${this.low}–${this.high} g/day`); } catch (e) {} this.copied = true; setTimeout(() => this.copied = false, 1500); },
            get kg() { return this.unit === 'kg' ? this.weight : this.weight * 0.453592; },
            get range() { return { maintain: [0.8, 1.2], lose: [1.6, 2.2], build: [1.6, 2.2] }[this.goal]; },
            get low() { return Math.round(this.kg * this.range[0]); },
            get high() { return Math.round(this.kg * this.range[1]); },
        };
    }
</script>
