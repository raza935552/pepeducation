{{-- TDEE calculator widget --}}
<div x-data="tdeeCalc()" class="space-y-6">
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
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
            </div>

            {{-- Results --}}
            <div class="p-6 sm:p-8 bg-surface-50 border-t md:border-t-0 md:border-l border-gray-200 flex flex-col">
                <div class="flex items-center justify-between mb-4">
                    <p class="text-sm font-medium text-gray-500">Your result</p>
                    <div class="flex gap-1.5">
                        <button type="button" @click="reset()" class="text-xs inline-flex items-center gap-1 px-2 py-1 rounded-md border border-gray-200 text-gray-400 hover:bg-white transition-colors"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>Reset</button>
                        <button type="button" @click="copy()" class="text-xs inline-flex items-center gap-1 px-2 py-1 rounded-md border border-gray-200 text-gray-500 hover:bg-white transition-colors"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg><span x-text="copied ? 'Copied!' : 'Copy'"></span></button>
                    </div>
                </div>
                <div class="bg-white rounded-xl border border-gray-200 p-5 mb-4 text-center">
                    <p class="text-xs uppercase tracking-wide text-gray-400 mb-1">Total Daily Energy Expenditure</p>
                    <p class="text-4xl font-bold" style="color: {{ $config['accent'] }};"><span x-text="tdee"></span> <span class="text-lg font-medium text-gray-400">kcal</span></p>
                    <p class="text-sm text-gray-500 mt-1">BMR: <span x-text="bmr"></span> kcal</p>
                </div>
                <p class="text-sm font-medium text-gray-500 mb-2">Calorie targets</p>
                <dl class="text-sm space-y-2">
                    <div class="flex justify-between"><dt class="text-gray-500">Fat loss (−20%)</dt><dd class="font-semibold text-gray-900" x-text="cut"></dd></div>
                    <div class="flex justify-between"><dt class="text-gray-500">Maintenance</dt><dd class="font-semibold text-gray-900" x-text="tdee"></dd></div>
                    <div class="flex justify-between"><dt class="text-gray-500">Muscle gain (+12%)</dt><dd class="font-semibold text-gray-900" x-text="bulk"></dd></div>
                </dl>
            </div>
        </div>
    </div>

    {{-- TDEE by activity level --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        <h3 class="font-bold text-gray-900 px-6 pt-5">TDEE at every activity level</h3>
        <p class="text-sm text-gray-500 px-6 pb-3">How your daily burn changes with activity.</p>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-surface-50 text-gray-500"><tr><th class="text-left font-medium px-6 py-2">Activity level</th><th class="text-left font-medium px-6 py-2">Multiplier</th><th class="text-left font-medium px-6 py-2">TDEE</th></tr></thead>
                <tbody>
                    <template x-for="(row, i) in levels" :key="i">
                        <tr class="border-t border-gray-100"><td class="px-6 py-2.5 text-gray-700" x-text="row.label"></td><td class="px-6 py-2.5 text-gray-500" x-text="'×' + row.mult"></td><td class="px-6 py-2.5 font-semibold text-gray-900" x-text="row.value"></td></tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    function tdeeCalc() {
        const defaults = { age: 30, sex: 'male', units: 'metric', height: 175, weight: 75, activity: '1.55' };
        return {
            ...defaults, copied: false,
            reset() { Object.assign(this, defaults); },
            copy() { try { navigator.clipboard.writeText(`TDEE ${this.tdee} kcal (BMR ${this.bmr})`); } catch (e) {} this.copied = true; setTimeout(() => this.copied = false, 1500); },
            get cm() { return this.units === 'metric' ? (this.height || 0) : (this.height || 0) * 2.54; },
            get kg() { return this.units === 'metric' ? (this.weight || 0) : (this.weight || 0) * 0.453592; },
            get bmrRaw() { return (10 * this.kg) + (6.25 * this.cm) - (5 * (this.age || 0)) + (this.sex === 'male' ? 5 : -161); },
            get bmr() { return Math.max(0, Math.round(this.bmrRaw)).toLocaleString(); },
            get tdeeRaw() { return this.bmrRaw * parseFloat(this.activity); },
            get tdee() { return Math.max(0, Math.round(this.tdeeRaw)).toLocaleString(); },
            get cut() { return Math.max(0, Math.round(this.tdeeRaw * 0.8)).toLocaleString(); },
            get bulk() { return Math.max(0, Math.round(this.tdeeRaw * 1.12)).toLocaleString(); },
            get levels() {
                const map = [['Sedentary', 1.2], ['Light', 1.375], ['Moderate', 1.55], ['Active', 1.725], ['Extremely active', 1.9]];
                return map.map(([label, mult]) => ({ label, mult, value: Math.max(0, Math.round(this.bmrRaw * mult)).toLocaleString() }));
            },
        };
    }
</script>
