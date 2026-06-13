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
                <div class="flex flex-wrap gap-1.5 mt-2">
                    <template x-for="v in [2, 5, 10, 15, 20]" :key="v">
                        <button type="button" @click="mg = v"
                                class="px-2.5 py-1 rounded-full text-xs font-medium border transition-colors"
                                :class="mg === v ? 'text-white border-transparent' : 'text-gray-500 border-gray-200 hover:border-gray-300'"
                                :style="mg === v ? 'background-color: {{ $accent }}' : ''"
                                x-text="v + ' mg'"></button>
                    </template>
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Bacteriostatic water (mL)</label>
                <input type="number" min="0" step="0.5" x-model.number="water"
                       class="w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500">
                <div class="flex flex-wrap gap-1.5 mt-2">
                    <template x-for="v in [1, 2, 3, 5]" :key="v">
                        <button type="button" @click="water = v"
                                class="px-2.5 py-1 rounded-full text-xs font-medium border transition-colors"
                                :class="water === v ? 'text-white border-transparent' : 'text-gray-500 border-gray-200 hover:border-gray-300'"
                                :style="water === v ? 'background-color: {{ $accent }}' : ''"
                                x-text="v + ' mL'"></button>
                    </template>
                </div>
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
                <p class="text-xs uppercase tracking-wide text-gray-400 mb-1">Draw this many units</p>
                <p class="text-5xl font-bold tracking-tight" style="color: {{ $accent }};" x-text="units"></p>
                <p class="text-sm text-gray-500 mt-1"><span x-text="drawMl"></span> mL on a U-100 syringe</p>
            </div>

            {{-- Syringe illustration --}}
            <div class="mb-3">
                <svg viewBox="0 0 280 56" class="w-full h-12">
                    <rect x="0" y="22" width="12" height="12" rx="1.5" fill="#9ca3af"/>
                    <rect x="11" y="16" width="6" height="24" rx="1" fill="#6b7280"/>
                    <rect x="18" y="15" width="208" height="26" rx="3" fill="#ffffff" stroke="#d1d5db"/>
                    <rect x="20" y="17" :width="Math.max(0, Math.min(204, (fillPct/100)*204))" height="22" rx="2" fill="{{ $accent }}" opacity="0.85" style="transition: width .25s"/>
                    <template x-for="t in 9" :key="t"><line :x1="18 + t*20.8" y1="15" :x2="18 + t*20.8" y2="22" stroke="#cbd5e1" stroke-width="1"/></template>
                    <polygon points="226,22 244,28 226,34" fill="#9ca3af"/>
                    <rect x="244" y="27" width="34" height="2" rx="1" fill="#9ca3af"/>
                </svg>
                <div class="flex justify-between text-[11px] text-gray-400 -mt-1">
                    <span>0</span><span><span x-text="syringe"></span> units</span>
                </div>
            </div>

            {{-- Over-capacity warning --}}
            <div x-show="overCapacity" x-cloak class="mb-3 flex gap-2 rounded-lg bg-amber-50 border border-amber-200 px-3 py-2">
                <svg class="w-4 h-4 text-amber-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                <p class="text-xs text-amber-700">This dose needs <span class="font-semibold" x-text="units"></span> units — more than your <span x-text="syringe"></span>-unit syringe holds. Add more water, split the dose, or use a larger syringe.</p>
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
        const defaults = { mg: seed.mg ?? 5, water: seed.water ?? 2, dose: seed.dose ?? 250, doseUnit: seed.doseUnit ?? 'mcg', syringe: 100 };
        return {
            ...defaults, copied: false,
            reset() { Object.assign(this, defaults); },
            copy() {
                const txt = `Draw ${this.units} units (${this.drawMl} mL) — ${this.concentration} mcg/mL`;
                try { navigator.clipboard.writeText(txt); } catch (e) {}
                this.copied = true; setTimeout(() => this.copied = false, 1500);
            },
            get doseMcg() { return (this.doseUnit === 'mg' ? this.dose * 1000 : this.dose) || 0; },
            get concRaw() { return this.water > 0 ? (this.mg * 1000) / this.water : 0; },
            get concentration() { return this.concRaw ? Math.round(this.concRaw).toLocaleString() : '0'; },
            get mcgPerUnit() { return (this.concRaw / 100).toFixed(1); },
            get unitsRaw() { return this.concRaw > 0 ? this.doseMcg / (this.concRaw / 100) : 0; },
            get units() { return this.unitsRaw ? (Math.round(this.unitsRaw * 10) / 10) : 0; },
            get drawMl() { return (this.unitsRaw / 100).toFixed(3); },
            get fillPct() { return Math.min(100, this.syringe > 0 ? (this.unitsRaw / this.syringe) * 100 : 0); },
            get overCapacity() { return this.unitsRaw > this.syringe; },
            get dosesPerVial() { return this.doseMcg > 0 ? Math.floor((this.mg * 1000) / this.doseMcg) : 0; },
        };
    }
</script>
