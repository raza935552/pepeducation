{{-- BMR (Mifflin–St Jeor) + TDEE widget --}}
<div x-data="bmrCalc()" class="space-y-6">
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="grid md:grid-cols-2">
            <div class="p-6 sm:p-8 space-y-5">
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Units</label>
                        <select x-model="unit" class="w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500">
                            <option value="metric">Metric</option>
                            <option value="imperial">Imperial</option>
                        </select>
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
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Age</label>
                    <input type="number" min="0" step="1" x-model.number="age" class="w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500">
                </div>
                {{-- Metric height/weight --}}
                <template x-if="unit === 'metric'">
                    <div class="grid grid-cols-2 gap-3">
                        <div><label class="block text-sm font-medium text-gray-700 mb-1.5">Height (cm)</label><input type="number" min="0" step="1" x-model.number="cm" class="w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500"></div>
                        <div><label class="block text-sm font-medium text-gray-700 mb-1.5">Weight (kg)</label><input type="number" min="0" step="0.5" x-model.number="kg" class="w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500"></div>
                    </div>
                </template>
                {{-- Imperial height/weight --}}
                <template x-if="unit === 'imperial'">
                    <div class="grid grid-cols-3 gap-3">
                        <div><label class="block text-sm font-medium text-gray-700 mb-1.5">Ft</label><input type="number" min="0" step="1" x-model.number="ft" class="w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500"></div>
                        <div><label class="block text-sm font-medium text-gray-700 mb-1.5">In</label><input type="number" min="0" step="1" x-model.number="inch" class="w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500"></div>
                        <div><label class="block text-sm font-medium text-gray-700 mb-1.5">Lb</label><input type="number" min="0" step="1" x-model.number="lb" class="w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500"></div>
                    </div>
                </template>
            </div>
            <div class="p-6 sm:p-8 bg-surface-50 border-t md:border-t-0 md:border-l border-gray-200 flex flex-col">
                <div class="flex items-center justify-between mb-4">
                    <p class="text-sm font-medium text-gray-500">Basal metabolic rate</p>
                    @include('calculators.partials._result-actions')
                </div>
                <div class="bg-white rounded-xl border border-gray-200 p-5 text-center">
                    <p class="text-xs uppercase tracking-wide text-gray-400 mb-1">BMR (at rest)</p>
                    <p class="text-4xl font-bold" style="color: {{ $config['accent'] }};" x-text="bmr"></p>
                    <p class="text-sm text-gray-500 mt-1">calories / day</p>
                </div>
                <p class="text-xs text-gray-400 mt-auto pt-4">Mifflin–St Jeor equation. Multiply by an activity factor (right) for your real daily burn (TDEE).</p>
            </div>
        </div>
    </div>

    {{-- TDEE by activity --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        <h3 class="font-bold text-gray-900 px-6 pt-5">Maintenance calories (TDEE) by activity</h3>
        <p class="text-sm text-gray-500 px-6 pb-3">BMR × activity factor.</p>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-surface-50 text-gray-500"><tr><th class="text-left font-medium px-6 py-2">Activity level</th><th class="text-left font-medium px-6 py-2">Factor</th><th class="text-left font-medium px-6 py-2">TDEE</th></tr></thead>
                <tbody>
                    <template x-for="row in tdee" :key="row.label">
                        <tr class="border-t border-gray-100">
                            <td class="px-6 py-2.5 text-gray-700" x-text="row.label"></td>
                            <td class="px-6 py-2.5 text-gray-500" x-text="row.factor"></td>
                            <td class="px-6 py-2.5 font-semibold" style="color: {{ $config['accent'] }};" x-text="row.value"></td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    function bmrCalc() {
        const defaults = { unit: 'metric', sex: 'male', age: 30, cm: 178, kg: 80, ft: 5, inch: 10, lb: 176 };
        return {
            ...defaults, copied: false,
            reset() { Object.assign(this, defaults); },
            copy() { try { navigator.clipboard.writeText(`BMR ${this.bmr} kcal/day (Mifflin–St Jeor)`); } catch (e) {} this.copied = true; setTimeout(() => this.copied = false, 1500); },
            get kgVal() { return this.unit === 'metric' ? this.kg : this.lb * 0.453592; },
            get cmVal() { return this.unit === 'metric' ? this.cm : (this.ft * 12 + this.inch) * 2.54; },
            get bmrRaw() {
                const base = 10 * this.kgVal + 6.25 * this.cmVal - 5 * (this.age || 0);
                return Math.max(0, base + (this.sex === 'male' ? 5 : -161));
            },
            get bmr() { return Math.round(this.bmrRaw); },
            get tdee() {
                const f = [['Sedentary (little/no exercise)', 1.2], ['Light (1–3 days/wk)', 1.375], ['Moderate (3–5 days/wk)', 1.55], ['Active (6–7 days/wk)', 1.725], ['Very active (hard daily)', 1.9]];
                return f.map(([label, factor]) => ({ label, factor, value: Math.round(this.bmrRaw * factor) }));
            },
        };
    }
</script>
