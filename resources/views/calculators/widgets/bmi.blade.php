{{-- BMI calculator widget --}}
<div x-data="bmiCalc()" class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
    <div class="grid md:grid-cols-2">
        {{-- Inputs --}}
        <div class="p-6 sm:p-8 space-y-5">
            <div>
                <span class="block text-sm font-medium text-gray-700 mb-1.5">Units</span>
                <div class="inline-flex rounded-lg border border-gray-300 p-1">
                    <button type="button" @click="units='metric'" :class="units==='metric' ? 'bg-primary-500 text-white' : 'text-gray-600'" class="px-4 py-1.5 rounded-md text-sm font-medium transition-colors">Metric</button>
                    <button type="button" @click="units='imperial'" :class="units==='imperial' ? 'bg-primary-500 text-white' : 'text-gray-600'" class="px-4 py-1.5 rounded-md text-sm font-medium transition-colors">Imperial</button>
                </div>
            </div>

            <template x-if="units==='metric'">
                <div class="space-y-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Height (cm)</label>
                        <input type="number" min="0" x-model.number="cm" class="w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Weight (kg)</label>
                        <input type="number" min="0" step="0.1" x-model.number="kg" class="w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500">
                    </div>
                </div>
            </template>

            <template x-if="units==='imperial'">
                <div class="space-y-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Height</label>
                        <div class="flex gap-2">
                            <div class="flex-1"><input type="number" min="0" x-model.number="ft" placeholder="ft" class="w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500"><span class="text-xs text-gray-400">feet</span></div>
                            <div class="flex-1"><input type="number" min="0" x-model.number="inch" placeholder="in" class="w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500"><span class="text-xs text-gray-400">inches</span></div>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Weight (lb)</label>
                        <input type="number" min="0" step="0.1" x-model.number="lb" class="w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500">
                    </div>
                </div>
            </template>

            <button type="button" @click="reset()" class="text-xs text-gray-400 hover:text-gray-600 inline-flex items-center gap-1">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                Reset
            </button>
        </div>

        {{-- Results --}}
        <div class="p-6 sm:p-8 bg-surface-50 border-t md:border-t-0 md:border-l border-gray-200 flex flex-col">
            <div class="flex items-center justify-between mb-4">
                <p class="text-sm font-medium text-gray-500">Your result</p>
                <button type="button" @click="copy()" class="text-xs inline-flex items-center gap-1 px-2 py-1 rounded-md border border-gray-200 text-gray-500 hover:bg-white transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                    <span x-text="copied ? 'Copied!' : 'Copy'"></span>
                </button>
            </div>
            <div class="bg-white rounded-xl border border-gray-200 p-5 mb-4 text-center">
                <p class="text-xs uppercase tracking-wide text-gray-400 mb-1">Body Mass Index</p>
                <p class="text-5xl font-bold tracking-tight" :style="`color: ${categoryColor}`" x-text="bmi"></p>
                <p class="text-sm font-semibold mt-1" :style="`color: ${categoryColor}`" x-text="category"></p>
            </div>

            {{-- BMI scale with marker --}}
            <div class="mb-4">
                <div class="relative pt-3">
                    <div class="absolute top-0 -translate-x-1/2 transition-all duration-300" :style="`left: ${markerPct}%`">
                        <svg class="w-3 h-3" :style="`color: ${categoryColor}`" fill="currentColor" viewBox="0 0 12 12"><path d="M6 12L0 4h12z"/></svg>
                    </div>
                    <div class="h-3 rounded-full overflow-hidden flex">
                        <div class="bg-blue-400" style="width:18.5%"></div>
                        <div class="bg-green-500" style="width:21%"></div>
                        <div class="bg-amber-400" style="width:20%"></div>
                        <div class="bg-red-500" style="width:40.5%"></div>
                    </div>
                </div>
                <div class="flex justify-between text-[11px] text-gray-400 mt-1"><span>15</span><span>18.5</span><span>25</span><span>30</span><span>40</span></div>
            </div>

            <dl class="text-sm space-y-2 mt-auto">
                <div class="flex justify-between"><dt class="text-gray-500">Healthy weight range</dt><dd class="font-semibold text-gray-900" x-text="healthyRange"></dd></div>
                <div class="flex justify-between"><dt class="text-gray-500" x-text="deltaLabel"></dt><dd class="font-semibold" :style="`color: ${categoryColor}`" x-text="deltaValue"></dd></div>
            </dl>
        </div>
    </div>
</div>

<script>
    function bmiCalc() {
        const defaults = { units: 'metric', cm: 175, kg: 75, ft: 5, inch: 9, lb: 165 };
        return {
            ...defaults, copied: false,
            reset() { Object.assign(this, defaults); },
            copy() { try { navigator.clipboard.writeText(`BMI ${this.bmi} — ${this.category}`); } catch (e) {} this.copied = true; setTimeout(() => this.copied = false, 1500); },
            get heightM() {
                if (this.units === 'metric') return (this.cm || 0) / 100;
                return (((this.ft || 0) * 12) + (this.inch || 0)) * 0.0254;
            },
            get weightKg() { return this.units === 'metric' ? (this.kg || 0) : (this.lb || 0) * 0.453592; },
            get bmiRaw() { return this.heightM > 0 ? this.weightKg / (this.heightM * this.heightM) : 0; },
            get bmi() { return this.bmiRaw ? this.bmiRaw.toFixed(1) : '0.0'; },
            get markerPct() { return Math.max(0, Math.min(100, (this.bmiRaw - 15) / (40 - 15) * 100)); },
            get category() {
                const b = this.bmiRaw; if (!b) return '—';
                if (b < 18.5) return 'Underweight';
                if (b < 25) return 'Healthy weight';
                if (b < 30) return 'Overweight';
                return 'Obese';
            },
            get categoryColor() {
                const b = this.bmiRaw; if (!b) return '#6b7280';
                if (b < 18.5) return '#60a5fa';
                if (b < 25) return '#22c55e';
                if (b < 30) return '#f59e0b';
                return '#ef4444';
            },
            get hi() { return 24.9 * this.heightM * this.heightM; },
            get lo() { return 18.5 * this.heightM * this.heightM; },
            get healthyRange() {
                if (this.heightM <= 0) return '—';
                if (this.units === 'metric') return `${this.lo.toFixed(1)}–${this.hi.toFixed(1)} kg`;
                return `${(this.lo / 0.453592).toFixed(0)}–${(this.hi / 0.453592).toFixed(0)} lb`;
            },
            fmt(kg) { return this.units === 'metric' ? kg.toFixed(1) + ' kg' : (kg / 0.453592).toFixed(1) + ' lb'; },
            get deltaLabel() { const b = this.bmiRaw; if (!b) return 'To healthy range'; if (b < 18.5) return 'To reach healthy'; if (b < 25) return 'Status'; return 'Above healthy by'; },
            get deltaValue() {
                const b = this.bmiRaw, w = this.weightKg; if (!b) return '—';
                if (b < 18.5) return '+' + this.fmt(this.lo - w);
                if (b < 25) return 'In range ✓';
                return this.fmt(w - this.hi);
            },
        };
    }
</script>
