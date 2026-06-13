{{-- Macro calculator widget --}}
<div x-data="macroCalc()" class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
    <div class="grid md:grid-cols-2">
        {{-- Inputs --}}
        <div class="p-6 sm:p-8 space-y-5">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Daily calories (kcal)</label>
                <input type="number" min="0" step="10" x-model.number="calories" class="w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500">
                <p class="text-xs text-gray-400 mt-1">Not sure? Use the <a href="{{ route('calculators.show', 'tdee') }}" class="text-primary-600 hover:underline">TDEE calculator</a> first.</p>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Weight</label>
                    <input type="number" min="0" step="0.1" x-model.number="weight" class="w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500">
                </div>
                <div>
                    <span class="block text-sm font-medium text-gray-700 mb-1.5">Units</span>
                    <div class="inline-flex rounded-lg border border-gray-300 p-1 w-full">
                        <button type="button" @click="units='metric'" :class="units==='metric' ? 'bg-primary-500 text-white' : 'text-gray-600'" class="flex-1 px-2 py-1.5 rounded-md text-sm font-medium">kg</button>
                        <button type="button" @click="units='imperial'" :class="units==='imperial' ? 'bg-primary-500 text-white' : 'text-gray-600'" class="flex-1 px-2 py-1.5 rounded-md text-sm font-medium">lb</button>
                    </div>
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Diet style</label>
                <div class="grid grid-cols-2 gap-2">
                    <template x-for="d in diets" :key="d.id">
                        <button type="button" @click="diet = d.id"
                                class="px-3 py-2 rounded-lg text-sm font-medium border text-left transition-colors"
                                :class="diet === d.id ? 'border-transparent text-white' : 'border-gray-200 text-gray-600 hover:border-gray-300'"
                                :style="diet === d.id ? 'background-color: {{ $config['accent'] }}' : ''"
                                x-text="d.label"></button>
                    </template>
                </div>
            </div>
        </div>

        {{-- Results --}}
        <div class="p-6 sm:p-8 bg-surface-50 border-t md:border-t-0 md:border-l border-gray-200 flex flex-col">
            <div class="flex items-center justify-between mb-4">
                <p class="text-sm font-medium text-gray-500">Your daily macros</p>
                <button type="button" @click="copy()" class="text-xs inline-flex items-center gap-1 px-2 py-1 rounded-md border border-gray-200 text-gray-500 hover:bg-white transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                    <span x-text="copied ? 'Copied!' : 'Copy'"></span>
                </button>
            </div>

            {{-- Donut --}}
            <div class="flex items-center gap-5 mb-5">
                <div class="relative w-28 h-28 shrink-0 rounded-full" :style="`background: conic-gradient(#10b981 0 ${pct.protein}%, #f59e0b 0 ${pct.protein + pct.carbs}%, #fb7185 0 100%); transition: background .3s`">
                    <div class="absolute inset-[14px] bg-surface-50 rounded-full flex flex-col items-center justify-center">
                        <span class="text-lg font-bold text-gray-900" x-text="(calories||0).toLocaleString()"></span>
                        <span class="text-[10px] text-gray-400 -mt-0.5">kcal</span>
                    </div>
                </div>
                <dl class="flex-1 space-y-2 text-sm">
                    <div class="flex items-center justify-between"><dt class="flex items-center gap-2 text-gray-600"><span class="w-3 h-3 rounded-full bg-emerald-500"></span>Protein</dt><dd class="font-semibold text-gray-900"><span x-text="g.protein"></span> g <span class="text-gray-400 font-normal">· <span x-text="pct.protein"></span>%</span></dd></div>
                    <div class="flex items-center justify-between"><dt class="flex items-center gap-2 text-gray-600"><span class="w-3 h-3 rounded-full bg-amber-400"></span>Carbs</dt><dd class="font-semibold text-gray-900"><span x-text="g.carbs"></span> g <span class="text-gray-400 font-normal">· <span x-text="pct.carbs"></span>%</span></dd></div>
                    <div class="flex items-center justify-between"><dt class="flex items-center gap-2 text-gray-600"><span class="w-3 h-3 rounded-full bg-rose-400"></span>Fat</dt><dd class="font-semibold text-gray-900"><span x-text="g.fat"></span> g <span class="text-gray-400 font-normal">· <span x-text="pct.fat"></span>%</span></dd></div>
                </dl>
            </div>
            <p class="text-xs text-gray-400 mt-auto">Protein is set from your body weight first, then carbs and fat fill the remaining calories for your chosen diet style.</p>
        </div>
    </div>
</div>

<script>
    function macroCalc() {
        const defaults = { calories: 2200, weight: 75, units: 'metric', diet: 'balanced' };
        return {
            ...defaults, copied: false,
            diets: [
                { id: 'balanced', label: 'Balanced' },
                { id: 'high-protein', label: 'High protein' },
                { id: 'low-carb', label: 'Low carb' },
                { id: 'keto', label: 'Keto' },
            ],
            reset() { Object.assign(this, defaults); },
            copy() { const g = this.g; try { navigator.clipboard.writeText(`${this.calories} kcal — P ${g.protein}g / C ${g.carbs}g / F ${g.fat}g`); } catch (e) {} this.copied = true; setTimeout(() => this.copied = false, 1500); },
            get kg() { return this.units === 'metric' ? (this.weight || 0) : (this.weight || 0) * 0.453592; },
            get profile() {
                return ({ 'balanced': [1.8, 0.30], 'high-protein': [2.2, 0.25], 'low-carb': [2.0, 0.40], 'keto': [1.8, 0.70] })[this.diet];
            },
            get g() {
                const cals = this.calories || 0;
                const [pPerKg, fatPct] = this.profile;
                let protein = this.kg * pPerKg;
                let proteinCals = protein * 4;
                let fatCals = cals * fatPct;
                let carbCals = cals - proteinCals - fatCals;
                if (carbCals < 0) { carbCals = 0; fatCals = Math.max(0, cals - proteinCals); }
                return { protein: Math.round(protein), carbs: Math.round(carbCals / 4), fat: Math.round(fatCals / 9) };
            },
            get pct() {
                const p = this.g.protein * 4, c = this.g.carbs * 4, f = this.g.fat * 9;
                const total = p + c + f || 1;
                return { protein: Math.round(p / total * 100), carbs: Math.round(c / total * 100), fat: Math.round(f / total * 100) };
            },
        };
    }
</script>
