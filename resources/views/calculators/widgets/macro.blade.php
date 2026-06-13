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
                <select x-model="diet" class="w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500">
                    <option value="balanced">Balanced (30P / 40C / 30F)</option>
                    <option value="high-protein">High protein</option>
                    <option value="low-carb">Low carb</option>
                    <option value="keto">Ketogenic</option>
                </select>
            </div>
        </div>

        {{-- Results --}}
        <div class="p-6 sm:p-8 bg-surface-50 border-t md:border-t-0 md:border-l border-gray-200 flex flex-col">
            <p class="text-sm font-medium text-gray-500 mb-4">Your daily macros</p>

            {{-- Split bar --}}
            <div class="h-4 rounded-full overflow-hidden flex mb-4">
                <div class="bg-emerald-500" :style="`width: ${pct.protein}%`"></div>
                <div class="bg-amber-400" :style="`width: ${pct.carbs}%`"></div>
                <div class="bg-rose-400" :style="`width: ${pct.fat}%`"></div>
            </div>

            <div class="space-y-3">
                <div class="bg-white rounded-xl border border-gray-200 p-4 flex items-center justify-between">
                    <span class="flex items-center gap-2"><span class="w-3 h-3 rounded-full bg-emerald-500"></span><span class="font-medium text-gray-700">Protein</span></span>
                    <span><span class="text-xl font-bold text-gray-900" x-text="g.protein"></span><span class="text-sm text-gray-400"> g · <span x-text="pct.protein"></span>%</span></span>
                </div>
                <div class="bg-white rounded-xl border border-gray-200 p-4 flex items-center justify-between">
                    <span class="flex items-center gap-2"><span class="w-3 h-3 rounded-full bg-amber-400"></span><span class="font-medium text-gray-700">Carbs</span></span>
                    <span><span class="text-xl font-bold text-gray-900" x-text="g.carbs"></span><span class="text-sm text-gray-400"> g · <span x-text="pct.carbs"></span>%</span></span>
                </div>
                <div class="bg-white rounded-xl border border-gray-200 p-4 flex items-center justify-between">
                    <span class="flex items-center gap-2"><span class="w-3 h-3 rounded-full bg-rose-400"></span><span class="font-medium text-gray-700">Fat</span></span>
                    <span><span class="text-xl font-bold text-gray-900" x-text="g.fat"></span><span class="text-sm text-gray-400"> g · <span x-text="pct.fat"></span>%</span></span>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function macroCalc() {
        return {
            calories: 2200, weight: 75, units: 'metric', diet: 'balanced',
            get kg() { return this.units === 'metric' ? (this.weight || 0) : (this.weight || 0) * 0.453592; },
            // [protein g/kg, fat % of calories]
            get profile() {
                return ({
                    'balanced':     [1.8, 0.30],
                    'high-protein': [2.2, 0.25],
                    'low-carb':     [2.0, 0.40],
                    'keto':         [1.8, 0.70],
                })[this.diet];
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
