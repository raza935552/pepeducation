{{-- One-rep max (Epley) widget --}}
<div x-data="ormCalc()" class="space-y-6">
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="grid md:grid-cols-2">
            <div class="p-6 sm:p-8 space-y-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Weight lifted</label>
                    <div class="flex gap-2">
                        <input type="number" min="0" step="2.5" x-model.number="weight" class="w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500">
                        <select x-model="unit" class="rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500">
                            <option value="lb">lb</option>
                            <option value="kg">kg</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Reps completed</label>
                    <input type="number" min="1" max="20" step="1" x-model.number="reps" class="w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500">
                </div>
            </div>
            <div class="p-6 sm:p-8 bg-surface-50 border-t md:border-t-0 md:border-l border-gray-200 flex flex-col">
                <div class="flex items-center justify-between mb-4">
                    <p class="text-sm font-medium text-gray-500">Estimated 1RM</p>
                    @include('calculators.partials._result-actions')
                </div>
                <div class="bg-white rounded-xl border border-gray-200 p-5 text-center">
                    <p class="text-xs uppercase tracking-wide text-gray-400 mb-1">One-rep max (Epley)</p>
                    <p class="text-4xl font-bold" style="color: {{ $config['accent'] }};"><span x-text="orm"></span> <span class="text-lg" x-text="unit"></span></p>
                </div>
                <p class="text-xs text-gray-400 mt-auto pt-4">Most accurate for ~3–10 reps. Re-test from a fresh set as you progress.</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        <h3 class="font-bold text-gray-900 px-6 pt-5">Training loads by percentage</h3>
        <p class="text-sm text-gray-500 px-6 pb-3">Common working percentages of your estimated 1RM.</p>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-surface-50 text-gray-500"><tr><th class="text-left font-medium px-6 py-2">% of 1RM</th><th class="text-left font-medium px-6 py-2">Load</th><th class="text-left font-medium px-6 py-2">Typical use</th></tr></thead>
                <tbody>
                    <template x-for="row in table" :key="row.pct">
                        <tr class="border-t border-gray-100">
                            <td class="px-6 py-2.5 font-semibold text-gray-900" x-text="row.pct + '%'"></td>
                            <td class="px-6 py-2.5 font-semibold" style="color: {{ $config['accent'] }};"><span x-text="row.load"></span> <span x-text="unit"></span></td>
                            <td class="px-6 py-2.5 text-gray-500" x-text="row.use"></td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    function ormCalc() {
        const defaults = { weight: 225, unit: 'lb', reps: 5 };
        return {
            ...defaults, copied: false,
            reset() { Object.assign(this, defaults); },
            copy() { try { navigator.clipboard.writeText(`Estimated 1RM: ${this.orm} ${this.unit}`); } catch (e) {} this.copied = true; setTimeout(() => this.copied = false, 1500); },
            get ormRaw() { return (this.weight || 0) * (1 + (this.reps || 0) / 30); },
            get orm() { return Math.round(this.ormRaw); },
            get table() {
                const rows = [[95, 'Heavy singles/doubles'], [90, 'Strength (2–4 reps)'], [85, 'Strength (4–6)'], [80, 'Hypertrophy (6–8)'], [75, 'Hypertrophy (8–10)'], [70, 'Volume (10–12)'], [60, 'Light / technique']];
                return rows.map(([pct, use]) => ({ pct, use, load: Math.round(this.ormRaw * pct / 100) }));
            },
        };
    }
</script>
