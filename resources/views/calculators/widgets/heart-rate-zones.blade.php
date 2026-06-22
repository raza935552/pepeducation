{{-- Heart rate zones widget --}}
<div x-data="hrCalc()" class="space-y-6">
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="grid md:grid-cols-2">
            <div class="p-6 sm:p-8 space-y-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Age</label>
                    <input type="number" min="0" max="120" step="1" x-model.number="age" class="w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Resting heart rate <span class="text-gray-400">(optional — enables Karvonen)</span></label>
                    <input type="number" min="0" max="120" step="1" x-model.number="resting" placeholder="e.g. 60" class="w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500">
                </div>
            </div>
            <div class="p-6 sm:p-8 bg-surface-50 border-t md:border-t-0 md:border-l border-gray-200 flex flex-col">
                <div class="flex items-center justify-between mb-4">
                    <p class="text-sm font-medium text-gray-500">Maximum heart rate</p>
                    @include('calculators.partials._result-actions')
                </div>
                <div class="bg-white rounded-xl border border-gray-200 p-5 text-center">
                    <p class="text-xs uppercase tracking-wide text-gray-400 mb-1">Estimated max (220 − age)</p>
                    <p class="text-4xl font-bold" style="color: {{ $config['accent'] }};"><span x-text="maxHr"></span> <span class="text-lg">bpm</span></p>
                </div>
                <p class="text-xs text-gray-400 mt-auto pt-4" x-text="resting > 0 ? 'Zones use the Karvonen (heart-rate-reserve) method with your resting HR.' : 'Add a resting HR for personalised Karvonen zones.'"></p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        <h3 class="font-bold text-gray-900 px-6 pt-5">Your five training zones</h3>
        <p class="text-sm text-gray-500 px-6 pb-3">Target heart-rate band for each intensity.</p>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-surface-50 text-gray-500"><tr><th class="text-left font-medium px-6 py-2">Zone</th><th class="text-left font-medium px-6 py-2">Intensity</th><th class="text-left font-medium px-6 py-2">bpm range</th><th class="text-left font-medium px-6 py-2">Trains</th></tr></thead>
                <tbody>
                    <template x-for="z in zones" :key="z.zone">
                        <tr class="border-t border-gray-100">
                            <td class="px-6 py-2.5 font-semibold text-gray-900" x-text="'Zone ' + z.zone"></td>
                            <td class="px-6 py-2.5 text-gray-500" x-text="z.range"></td>
                            <td class="px-6 py-2.5 font-semibold" style="color: {{ $config['accent'] }};"><span x-text="z.low"></span>–<span x-text="z.high"></span></td>
                            <td class="px-6 py-2.5 text-gray-500" x-text="z.trains"></td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    function hrCalc() {
        const defaults = { age: 35, resting: null };
        return {
            ...defaults, copied: false,
            reset() { Object.assign(this, defaults); },
            copy() { try { navigator.clipboard.writeText(`Max HR ~${this.maxHr} bpm; Zone 2: ${this.zones[1].low}–${this.zones[1].high} bpm`); } catch (e) {} this.copied = true; setTimeout(() => this.copied = false, 1500); },
            get maxHr() { return Math.max(0, 220 - (this.age || 0)); },
            bpmAt(pct) {
                if (this.resting > 0) return Math.round((this.maxHr - this.resting) * pct + this.resting); // Karvonen
                return Math.round(this.maxHr * pct);
            },
            get zones() {
                const defs = [
                    [1, '50–60%', 0.50, 0.60, 'Recovery'],
                    [2, '60–70%', 0.60, 0.70, 'Fat-burn / endurance'],
                    [3, '70–80%', 0.70, 0.80, 'Aerobic'],
                    [4, '80–90%', 0.80, 0.90, 'Threshold'],
                    [5, '90–100%', 0.90, 1.00, 'Max effort'],
                ];
                return defs.map(([zone, range, lo, hi, trains]) => ({ zone, range, trains, low: this.bpmAt(lo), high: this.bpmAt(hi) }));
            },
        };
    }
</script>
