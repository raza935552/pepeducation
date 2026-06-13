{{-- Body Fat calculator widget — U.S. Navy method --}}
<div x-data="bodyFatCalc()" class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
    <div class="grid md:grid-cols-2">
        {{-- Inputs --}}
        <div class="p-6 sm:p-8 space-y-5">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Sex</label>
                    <select x-model="sex" class="w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500">
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                    </select>
                </div>
                <div>
                    <span class="block text-sm font-medium text-gray-700 mb-1.5">Units</span>
                    <div class="inline-flex rounded-lg border border-gray-300 p-1 w-full">
                        <button type="button" @click="units='metric'" :class="units==='metric' ? 'bg-primary-500 text-white' : 'text-gray-600'" class="flex-1 px-2 py-1.5 rounded-md text-sm font-medium">cm</button>
                        <button type="button" @click="units='imperial'" :class="units==='imperial' ? 'bg-primary-500 text-white' : 'text-gray-600'" class="flex-1 px-2 py-1.5 rounded-md text-sm font-medium">in</button>
                    </div>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Height</label>
                    <input type="number" min="0" step="0.1" x-model.number="height" class="w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Weight (<span x-text="units==='metric' ? 'kg' : 'lb'"></span>)</label>
                    <input type="number" min="0" step="0.1" x-model.number="weight" class="w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Neck</label>
                    <input type="number" min="0" step="0.1" x-model.number="neck" class="w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Waist</label>
                    <input type="number" min="0" step="0.1" x-model.number="waist" class="w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500">
                </div>
            </div>
            <div x-show="sex==='female'">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Hip</label>
                <input type="number" min="0" step="0.1" x-model.number="hip" class="w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500">
            </div>
            <p class="text-xs text-gray-400">Measure relaxed, on bare skin: neck below the larynx, waist at the navel<span x-show="sex==='female'">, hip at the widest point</span>.</p>
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
                <p class="text-xs uppercase tracking-wide text-gray-400 mb-1">Estimated body fat</p>
                <p class="text-5xl font-bold" :style="`color: ${categoryColor}`"><span x-text="bodyFat"></span><span class="text-2xl">%</span></p>
                <p class="text-sm font-semibold mt-1" :style="`color: ${categoryColor}`" x-text="category"></p>
            </div>

            {{-- Fat vs lean composition bar --}}
            <div class="mb-4">
                <div class="flex h-5 rounded-full overflow-hidden text-[10px] font-semibold text-white">
                    <div class="flex items-center justify-center" :style="`width: ${bfRaw}%; background-color: ${categoryColor}; transition: width .3s`"><span x-show="bfRaw >= 12" x-text="bodyFat + '%'"></span></div>
                    <div class="flex items-center justify-center bg-gray-300 text-gray-600" :style="`width: ${100 - bfRaw}%`"><span x-show="bfRaw <= 88">Lean</span></div>
                </div>
                <div class="flex justify-between text-[11px] text-gray-400 mt-1"><span>Fat</span><span>Lean mass</span></div>
            </div>

            <dl class="text-sm space-y-2 mt-auto">
                <div class="flex justify-between"><dt class="text-gray-500">Fat mass</dt><dd class="font-semibold text-gray-900" x-text="fatMass"></dd></div>
                <div class="flex justify-between"><dt class="text-gray-500">Lean mass</dt><dd class="font-semibold text-gray-900" x-text="leanMass"></dd></div>
            </dl>
        </div>
    </div>
</div>

<script>
    function bodyFatCalc() {
        const defaults = { sex: 'male', units: 'metric', height: 178, weight: 80, neck: 38, waist: 86, hip: 95 };
        return {
            ...defaults, copied: false,
            reset() { Object.assign(this, defaults); },
            copy() { try { navigator.clipboard.writeText(`Body fat ${this.bodyFat}% — ${this.category} (fat ${this.fatMass}, lean ${this.leanMass})`); } catch (e) {} this.copied = true; setTimeout(() => this.copied = false, 1500); },
            toIn(v) { return this.units === 'metric' ? (v || 0) / 2.54 : (v || 0); },
            get bfRaw() {
                const h = this.toIn(this.height), n = this.toIn(this.neck), w = this.toIn(this.waist), hp = this.toIn(this.hip);
                let bf = 0;
                if (this.sex === 'male') {
                    if (w - n <= 0 || h <= 0) return 0;
                    bf = 495 / (1.0324 - 0.19077 * Math.log10(w - n) + 0.15456 * Math.log10(h)) - 450;
                } else {
                    if (w + hp - n <= 0 || h <= 0) return 0;
                    bf = 495 / (1.29579 - 0.35004 * Math.log10(w + hp - n) + 0.22100 * Math.log10(h)) - 450;
                }
                return bf > 0 && isFinite(bf) ? bf : 0;
            },
            get bodyFat() { return this.bfRaw ? this.bfRaw.toFixed(1) : '0.0'; },
            get category() {
                const b = this.bfRaw; if (!b) return '—';
                const t = this.sex === 'male' ? [6, 14, 18, 25] : [14, 21, 25, 32];
                if (b < t[0]) return 'Essential';
                if (b < t[1]) return 'Athletic';
                if (b < t[2]) return 'Fitness';
                if (b < t[3]) return 'Average';
                return 'Above average';
            },
            get categoryColor() {
                const b = this.bfRaw; if (!b) return '#6b7280';
                const t = this.sex === 'male' ? [6, 14, 18, 25] : [14, 21, 25, 32];
                if (b < t[1]) return '#22c55e';
                if (b < t[2]) return '#0ea5e9';
                if (b < t[3]) return '#f59e0b';
                return '#ef4444';
            },
            fmtMass(kg) { return this.units === 'metric' ? kg.toFixed(1) + ' kg' : (kg / 0.453592).toFixed(1) + ' lb'; },
            get weightKg() { return this.units === 'metric' ? (this.weight || 0) : (this.weight || 0) * 0.453592; },
            get fatMass() { return this.bfRaw ? this.fmtMass(this.weightKg * this.bfRaw / 100) : '—'; },
            get leanMass() { return this.bfRaw ? this.fmtMass(this.weightKg * (1 - this.bfRaw / 100)) : '—'; },
        };
    }
</script>
