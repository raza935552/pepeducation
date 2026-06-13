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
        </div>

        {{-- Results --}}
        <div class="p-6 sm:p-8 bg-surface-50 border-t md:border-t-0 md:border-l border-gray-200 flex flex-col">
            <p class="text-sm font-medium text-gray-500 mb-4">Your result</p>
            <div class="bg-white rounded-xl border border-gray-200 p-5 mb-4 text-center">
                <p class="text-xs uppercase tracking-wide text-gray-400 mb-1">Daily calorie target</p>
                <p class="text-4xl font-bold" style="color: {{ $config['accent'] }};"><span x-text="targetCals"></span> <span class="text-lg font-medium text-gray-400">kcal</span></p>
            </div>
            <div class="grid grid-cols-2 gap-3 mb-4">
                <div class="bg-white rounded-xl border border-gray-200 p-3 text-center"><p class="text-xs text-gray-400">BMR</p><p class="font-bold text-gray-900"><span x-text="bmr"></span></p></div>
                <div class="bg-white rounded-xl border border-gray-200 p-3 text-center"><p class="text-xs text-gray-400">TDEE</p><p class="font-bold text-gray-900"><span x-text="tdee"></span></p></div>
            </div>
            <p class="text-sm font-medium text-gray-500 mb-2">Macros</p>
            <dl class="text-sm space-y-2 mt-auto">
                <div class="flex justify-between"><dt class="text-gray-500">Protein</dt><dd class="font-semibold text-gray-900"><span x-text="macros.protein"></span> g</dd></div>
                <div class="flex justify-between"><dt class="text-gray-500">Carbs</dt><dd class="font-semibold text-gray-900"><span x-text="macros.carbs"></span> g</dd></div>
                <div class="flex justify-between"><dt class="text-gray-500">Fat</dt><dd class="font-semibold text-gray-900"><span x-text="macros.fat"></span> g</dd></div>
            </dl>
        </div>
    </div>
</div>

<script>
    function fitnessCalc() {
        return {
            age: 30, sex: 'male', units: 'metric', height: 175, weight: 75, activity: '1.55', goal: 'maintain',
            get cm() { return this.units === 'metric' ? (this.height || 0) : (this.height || 0) * 2.54; },
            get kg() { return this.units === 'metric' ? (this.weight || 0) : (this.weight || 0) * 0.453592; },
            get bmrRaw() { return (10 * this.kg) + (6.25 * this.cm) - (5 * (this.age || 0)) + (this.sex === 'male' ? 5 : -161); },
            get bmr() { return Math.max(0, Math.round(this.bmrRaw)).toLocaleString(); },
            get tdeeRaw() { return this.bmrRaw * parseFloat(this.activity); },
            get tdee() { return Math.max(0, Math.round(this.tdeeRaw)).toLocaleString(); },
            get targetRaw() {
                const m = this.goal === 'cut' ? 0.80 : this.goal === 'bulk' ? 1.12 : 1.0;
                return this.tdeeRaw * m;
            },
            get targetCals() { return Math.max(0, Math.round(this.targetRaw)).toLocaleString(); },
            get macros() {
                const cals = this.targetRaw;
                const protein = this.kg * 2;            // 2 g/kg
                const fatCals = cals * 0.25;            // 25% of calories
                const fat = fatCals / 9;
                const carbCals = cals - (protein * 4) - fatCals;
                const carbs = Math.max(0, carbCals / 4);
                return { protein: Math.round(protein), carbs: Math.round(carbs), fat: Math.round(fat) };
            },
        };
    }
</script>
