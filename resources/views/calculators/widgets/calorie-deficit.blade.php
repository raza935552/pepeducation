{{-- Calorie deficit widget --}}
<div x-data="deficitCalc()" class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
    <div class="grid md:grid-cols-2">
        <div class="p-6 sm:p-8 space-y-5">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Maintenance calories (TDEE)</label>
                <input type="number" min="0" step="50" x-model.number="maintenance" class="w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Weekly loss goal</label>
                <div class="flex gap-2">
                    <input type="number" min="0" step="0.1" x-model.number="weeklyLoss" class="w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500">
                    <select x-model="unit" class="rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500">
                        <option value="lb">lb/wk</option>
                        <option value="kg">kg/wk</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="p-6 sm:p-8 bg-surface-50 border-t md:border-t-0 md:border-l border-gray-200 flex flex-col">
            <div class="flex items-center justify-between mb-4">
                <p class="text-sm font-medium text-gray-500">Your numbers</p>
                @include('calculators.partials._result-actions')
            </div>
            <div class="bg-white rounded-xl border border-gray-200 p-5 mb-4 text-center">
                <p class="text-xs uppercase tracking-wide text-gray-400 mb-1">Eat per day</p>
                <p class="text-4xl font-bold" style="color: {{ $config['accent'] }};" x-text="targetIntake"></p>
                <p class="text-sm text-gray-500 mt-1">calories</p>
            </div>
            <dl class="text-sm space-y-2 mt-auto">
                <div class="flex justify-between"><dt class="text-gray-500">Daily deficit</dt><dd class="font-semibold text-gray-900"><span x-text="dailyDeficit"></span> kcal</dd></div>
                <div class="flex justify-between"><dt class="text-gray-500">Weekly deficit</dt><dd class="font-semibold text-gray-900"><span x-text="weeklyDeficit"></span> kcal</dd></div>
            </dl>
            <p x-show="aggressive" x-cloak class="mt-3 text-xs text-amber-600">⚠ That target is very low — consider a gentler weekly goal.</p>
        </div>
    </div>
</div>

<script>
    function deficitCalc() {
        const defaults = { maintenance: 2200, weeklyLoss: 1, unit: 'lb' };
        return {
            ...defaults, copied: false,
            reset() { Object.assign(this, defaults); },
            copy() { try { navigator.clipboard.writeText(`Target ${this.targetIntake} kcal/day (deficit ${this.dailyDeficit} kcal)`); } catch (e) {} this.copied = true; setTimeout(() => this.copied = false, 1500); },
            get kcalPerUnit() { return this.unit === 'kg' ? 7700 : 3500; },
            get weeklyDeficitRaw() { return (this.weeklyLoss || 0) * this.kcalPerUnit; },
            get weeklyDeficit() { return Math.round(this.weeklyDeficitRaw); },
            get dailyDeficit() { return Math.round(this.weeklyDeficitRaw / 7); },
            get targetIntake() { return Math.max(0, Math.round((this.maintenance || 0) - this.weeklyDeficitRaw / 7)); },
            get aggressive() { return this.targetIntake > 0 && this.targetIntake < 1200; },
        };
    }
</script>
