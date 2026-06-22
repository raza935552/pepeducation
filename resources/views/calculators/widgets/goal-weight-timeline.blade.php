{{-- Goal weight & timeline widget --}}
<div x-data="goalWeightCalc()" class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
    <div class="grid md:grid-cols-2">
        <div class="p-6 sm:p-8 space-y-5">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Units</label>
                <select x-model="unit" class="w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500">
                    <option value="lb">Pounds (lb)</option>
                    <option value="kg">Kilograms (kg)</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Current weight (<span x-text="unit"></span>)</label>
                <input type="number" min="0" step="0.5" x-model.number="current" class="w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Goal weight (<span x-text="unit"></span>)</label>
                <input type="number" min="0" step="0.5" x-model.number="goal" class="w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Weekly loss rate (<span x-text="unit"></span>/week)</label>
                <input type="number" min="0" step="0.1" x-model.number="rate" class="w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500">
            </div>
        </div>
        <div class="p-6 sm:p-8 bg-surface-50 border-t md:border-t-0 md:border-l border-gray-200 flex flex-col">
            <div class="flex items-center justify-between mb-4">
                <p class="text-sm font-medium text-gray-500">Your timeline</p>
                @include('calculators.partials._result-actions')
            </div>
            <template x-if="valid">
                <div>
                    <div class="bg-white rounded-xl border border-gray-200 p-5 mb-4 text-center">
                        <p class="text-xs uppercase tracking-wide text-gray-400 mb-1">Time to goal</p>
                        <p class="text-4xl font-bold" style="color: {{ $config['accent'] }};"><span x-text="weeks"></span> <span class="text-lg">weeks</span></p>
                        <p class="text-sm text-gray-500 mt-1">≈ <span x-text="months"></span> months</p>
                    </div>
                    <dl class="text-sm space-y-2">
                        <div class="flex justify-between"><dt class="text-gray-500">Total to lose</dt><dd class="font-semibold text-gray-900"><span x-text="totalToLose"></span> <span x-text="unit"></span></dd></div>
                        <div class="flex justify-between"><dt class="text-gray-500">Target date</dt><dd class="font-semibold" style="color: {{ $config['accent'] }};" x-text="targetDate"></dd></div>
                    </dl>
                </div>
            </template>
            <template x-if="!valid">
                <p class="text-sm text-gray-400 my-auto">Enter a goal below your current weight and a weekly rate above zero.</p>
            </template>
        </div>
    </div>
</div>

<script>
    function goalWeightCalc() {
        const defaults = { unit: 'lb', current: 200, goal: 170, rate: 1.5 };
        return {
            ...defaults, copied: false,
            reset() { Object.assign(this, defaults); },
            copy() { try { navigator.clipboard.writeText(`${this.totalToLose} ${this.unit} in ${this.weeks} weeks — target ${this.targetDate}`); } catch (e) {} this.copied = true; setTimeout(() => this.copied = false, 1500); },
            get valid() { return this.current > this.goal && this.rate > 0; },
            get totalToLoseRaw() { return this.current - this.goal; },
            get totalToLose() { return Math.round(this.totalToLoseRaw * 10) / 10; },
            get weeksRaw() { return this.valid ? this.totalToLoseRaw / this.rate : 0; },
            get weeks() { return Math.ceil(this.weeksRaw); },
            get months() { return Math.round((this.weeksRaw / 4.345) * 10) / 10; },
            get targetDate() {
                const d = new Date(); d.setDate(d.getDate() + Math.ceil(this.weeksRaw) * 7);
                return d.toLocaleDateString(undefined, { year: 'numeric', month: 'short', day: 'numeric' });
            },
        };
    }
</script>
