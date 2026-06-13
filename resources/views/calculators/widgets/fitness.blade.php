{{-- Fitness calculator widget — BMR / TDEE / calories / macros --}}
<div x-data="fitnessCalc()" class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
    <div class="grid md:grid-cols-2">
        {{-- Inputs --}}
        <div class="p-6 sm:p-8 space-y-5">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Age</label>
                    <input type="number" min="0" x-model.number="age" class="w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Sex</label>
                    <select x-model="sex" class="w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500">
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                    </select>
                </div>
            </div>
            <div>
                <span class="block text-sm font-medium text-gray-700 mb-1.5">Units</span>
                <div class="inline-flex rounded-lg border border-gray-300 p-1">
                    <button type="button" @click="units='metric'" :class="units==='metric' ? 'bg-primary-500 text-white' : 'text-gray-600'" class="px-4 py-1.5 rounded-md text-sm font-medium">Metric</button>
                    <button type="button" @click="units='imperial'" :class="units==='imperial' ? 'bg-primary-500 text-white' : 'text-gray-600'" class="px-4 py-1.5 rounded-md text-sm font-medium">Imperial</button>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5"><span x-text="units==='metric' ? 'Height (cm)' : 'Height (in)'"></span></label>
                    <input type="number" min="0" x-model.number="height" class="w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5"><span x-text="units==='metric' ? 'Weight (kg)' : 'Weight (lb)'"></span></label>
                    <input type="number" min="0" step="0.1" x-model.number="weight" class="w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Activity level</label>
                <select x-model="activity" class="w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500">
                    <option value="1.2">Sedentary (little/no exercise)</option>
                    <option value="1.375">Light (1–3 days/week)</option>
                    <option value="1.55">Moderate (3–5 days/week)</option>
                    <option value="1.725">Active (6–7 days/week)</option>
                    <option value="1.9">Extremely active (physical job + training)</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Goal</label>
                <div class="inline-flex rounded-lg border border-gray-300 p-1 w-full">
                    <button type="button" @click="goal='cut'" :class="goal==='cut' ? 'bg-primary-500 text-white' : 'text-gray-600'" class="flex-1 px-2 py-1.5 rounded-md text-sm font-medium">Cut</button>
                    <button type="button" @click="goal='maintain'" :class="goal==='maintain' ? 'bg-primary-500 text-white' : 'text-gray-600'" class="flex-1 px-2 py-1.5 rounded-md text-sm font-medium">Maintain</button>
                    <button type="button" @click="goal='bulk'" :class="goal==='bulk' ? 'bg-primary-500 text-white' : 'text-gray-600'" class="flex-1 px-2 py-1.5 rounded-md text-sm font-medium">Bulk</button>
                </div>
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

            <div class="flex items-center gap-5 mb-4">
                <div class="relative w-28 h-28 shrink-0 rounded-full" :style="`background: conic-gradient(#10b981 0 ${mPct.protein}%, #f59e0b 0 ${mPct.protein + mPct.carbs}%, #fb7185 0 100%); transition: background .3s`">
                    <div class="absolute inset-[14px] bg-surface-50 rounded-full flex flex-col items-center justify-center">
                        <span class="text-lg font-bold" style="color: {{ $config['accent'] }};" x-text="targetCals"></span>
                        <span class="text-[10px] text-gray-400 -mt-0.5">kcal/day</span>
                    </div>
                </div>
                <dl class="flex-1 space-y-2 text-sm">
                    <div class="flex items-center justify-between"><dt class="flex items-center gap-2 text-gray-600"><span class="w-3 h-3 rounded-full bg-emerald-500"></span>Protein</dt><dd class="font-semibold text-gray-900"><span x-text="macros.protein"></span> g</dd></div>
                    <div class="flex items-center justify-between"><dt class="flex items-center gap-2 text-gray-600"><span class="w-3 h-3 rounded-full bg-amber-400"></span>Carbs</dt><dd class="font-semibold text-gray-900"><span x-text="macros.carbs"></span> g</dd></div>
                    <div class="flex items-center justify-between"><dt class="flex items-center gap-2 text-gray-600"><span class="w-3 h-3 rounded-full bg-rose-400"></span>Fat</dt><dd class="font-semibold text-gray-900"><span x-text="macros.fat"></span> g</dd></div>
                </dl>
            </div>

            <dl class="text-sm space-y-2 mt-auto">
                <div class="flex justify-between"><dt class="text-gray-500">BMR</dt><dd class="font-semibold text-gray-900"><span x-text="bmr"></span> kcal</dd></div>
                <div class="flex justify-between"><dt class="text-gray-500">TDEE (maintenance)</dt><dd class="font-semibold text-gray-900"><span x-text="tdee"></span> kcal</dd></div>
            </dl>
        </div>
    </div>
</div>

<script>
    function fitnessCalc() {
        const defaults = { age: 30, sex: 'male', units: 'metric', height: 175, weight: 75, activity: '1.55', goal: 'maintain' };
        return {
            ...defaults, copied: false,
            reset() { Object.assign(this, defaults); },
            copy() { const m = this.macros; try { navigator.clipboard.writeText(`${this.targetCals} kcal — P ${m.protein}g / C ${m.carbs}g / F ${m.fat}g (TDEE ${this.tdee})`); } catch (e) {} this.copied = true; setTimeout(() => this.copied = false, 1500); },
            get cm() { return this.units === 'metric' ? (this.height || 0) : (this.height || 0) * 2.54; },
            get kg() { return this.units === 'metric' ? (this.weight || 0) : (this.weight || 0) * 0.453592; },
            get bmrRaw() { return (10 * this.kg) + (6.25 * this.cm) - (5 * (this.age || 0)) + (this.sex === 'male' ? 5 : -161); },
            get bmr() { return Math.max(0, Math.round(this.bmrRaw)).toLocaleString(); },
            get tdeeRaw() { return this.bmrRaw * parseFloat(this.activity); },
            get tdee() { return Math.max(0, Math.round(this.tdeeRaw)).toLocaleString(); },
            get targetRaw() { const m = this.goal === 'cut' ? 0.80 : this.goal === 'bulk' ? 1.12 : 1.0; return this.tdeeRaw * m; },
            get targetCals() { return Math.max(0, Math.round(this.targetRaw)).toLocaleString(); },
            get macros() {
                const cals = this.targetRaw;
                const protein = this.kg * 2;
                const fatCals = cals * 0.25;
                const fat = fatCals / 9;
                const carbs = Math.max(0, (cals - (protein * 4) - fatCals) / 4);
                return { protein: Math.round(protein), carbs: Math.round(carbs), fat: Math.round(fat) };
            },
            get mPct() {
                const p = this.macros.protein * 4, c = this.macros.carbs * 4, f = this.macros.fat * 9;
                const t = p + c + f || 1;
                return { protein: Math.round(p / t * 100), carbs: Math.round(c / t * 100), fat: Math.round(f / t * 100) };
            },
        };
    }
</script>
