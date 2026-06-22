{{-- Waist-to-height ratio widget --}}
<div x-data="whtrCalc()" class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
    <div class="grid md:grid-cols-2">
        <div class="p-6 sm:p-8 space-y-5">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Units</label>
                <select x-model="unit" class="w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500">
                    <option value="in">Inches</option>
                    <option value="cm">Centimetres</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Waist (<span x-text="unit"></span>)</label>
                <input type="number" min="0" step="0.5" x-model.number="waist" class="w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Height (<span x-text="unit"></span>)</label>
                <input type="number" min="0" step="0.5" x-model.number="height" class="w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500">
            </div>
        </div>
        <div class="p-6 sm:p-8 bg-surface-50 border-t md:border-t-0 md:border-l border-gray-200 flex flex-col">
            <div class="flex items-center justify-between mb-4">
                <p class="text-sm font-medium text-gray-500">Your ratio</p>
                @include('calculators.partials._result-actions')
            </div>
            <div class="bg-white rounded-xl border border-gray-200 p-5 text-center">
                <p class="text-xs uppercase tracking-wide text-gray-400 mb-1">Waist ÷ height</p>
                <p class="text-4xl font-bold" :style="`color:${categoryColor}`" x-text="ratio"></p>
                <p class="text-sm font-medium mt-1" :style="`color:${categoryColor}`" x-text="category"></p>
            </div>
            <p class="text-xs text-gray-400 mt-auto pt-4">Guideline: keep your waist under half your height (ratio &lt; 0.5).</p>
        </div>
    </div>
</div>

<script>
    function whtrCalc() {
        const defaults = { unit: 'in', waist: 34, height: 70 };
        return {
            ...defaults, copied: false,
            reset() { Object.assign(this, defaults); },
            copy() { try { navigator.clipboard.writeText(`Waist-to-height ratio ${this.ratio} — ${this.category}`); } catch (e) {} this.copied = true; setTimeout(() => this.copied = false, 1500); },
            get ratioRaw() { return this.height > 0 ? this.waist / this.height : 0; },
            get ratio() { return this.ratioRaw.toFixed(2); },
            get category() {
                const r = this.ratioRaw;
                if (r <= 0) return '—';
                if (r < 0.4) return 'Low';
                if (r < 0.5) return 'Healthy';
                if (r < 0.6) return 'Increased risk';
                return 'High risk';
            },
            get categoryColor() {
                const r = this.ratioRaw;
                if (r <= 0) return '#9CA3AF';
                if (r < 0.4) return '#0891B2';
                if (r < 0.5) return '#16A34A';
                if (r < 0.6) return '#D97706';
                return '#DC2626';
            },
        };
    }
</script>
