{{-- Semaglutide titration schedule widget --}}
<div x-data="semaTitration()" class="space-y-6">
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="grid md:grid-cols-2">
            <div class="p-6 sm:p-8 space-y-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Start date</label>
                    <input type="date" x-model="start" class="w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Weeks per step</label>
                    <input type="number" min="1" max="12" step="1" x-model.number="weeksPerStep" class="w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Maintenance (max) dose</label>
                    <select x-model.number="maxDose" class="w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500">
                        <template x-for="d in ladder" :key="d"><option :value="d" x-text="d + ' mg'"></option></template>
                    </select>
                </div>
            </div>
            <div class="p-6 sm:p-8 bg-surface-50 border-t md:border-t-0 md:border-l border-gray-200 flex flex-col">
                <div class="flex items-center justify-between mb-4">
                    <p class="text-sm font-medium text-gray-500">Reach maintenance by</p>
                    @include('calculators.partials._result-actions')
                </div>
                <div class="bg-white rounded-xl border border-gray-200 p-5 text-center">
                    <p class="text-xs uppercase tracking-wide text-gray-400 mb-1"><span x-text="maxDose"></span> mg weekly from</p>
                    <p class="text-2xl font-bold" style="color: {{ $config['accent'] }};" x-text="maintenanceDate"></p>
                </div>
                <p class="text-xs text-gray-400 mt-auto pt-4">Standard ladder steps up about every <span x-text="weeksPerStep"></span> weeks. Hold longer if a step isn’t well tolerated.</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        <h3 class="font-bold text-gray-900 px-6 pt-5">Week-by-week schedule</h3>
        <p class="text-sm text-gray-500 px-6 pb-3">Each dose and the date it begins.</p>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-surface-50 text-gray-500"><tr><th class="text-left font-medium px-6 py-2">Step</th><th class="text-left font-medium px-6 py-2">Starts week</th><th class="text-left font-medium px-6 py-2">Date</th><th class="text-left font-medium px-6 py-2">Weekly dose</th></tr></thead>
                <tbody>
                    <template x-for="row in schedule" :key="row.step">
                        <tr class="border-t border-gray-100">
                            <td class="px-6 py-2.5 font-semibold text-gray-900" x-text="row.step"></td>
                            <td class="px-6 py-2.5 text-gray-500" x-text="'Week ' + row.week"></td>
                            <td class="px-6 py-2.5 text-gray-700" x-text="row.date"></td>
                            <td class="px-6 py-2.5 font-semibold" style="color: {{ $config['accent'] }};"><span x-text="row.dose"></span> mg<span x-show="row.last" class="text-gray-400 font-normal"> (maintenance)</span></td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    function semaTitration() {
        const ladder = [0.25, 0.5, 1.0, 1.7, 2.4];
        const today = new Date().toISOString().slice(0, 10);
        const defaults = { start: today, weeksPerStep: 4, maxDose: 2.4 };
        return {
            ...defaults, ladder, copied: false,
            reset() { Object.assign(this, defaults); },
            copy() { try { navigator.clipboard.writeText(this.schedule.map(r => `Week ${r.week}: ${r.dose} mg (${r.date})`).join('\n')); } catch (e) {} this.copied = true; setTimeout(() => this.copied = false, 1500); },
            addWeeks(date, w) { const d = new Date(date); d.setDate(d.getDate() + w * 7); return d; },
            fmt(d) { return d.toLocaleDateString(undefined, { year: 'numeric', month: 'short', day: 'numeric' }); },
            get steps() { return this.ladder.filter(d => d <= this.maxDose); },
            get schedule() {
                if (!this.start) return [];
                return this.steps.map((dose, i) => ({
                    step: i + 1,
                    week: i * this.weeksPerStep + 1,
                    date: this.fmt(this.addWeeks(new Date(this.start), i * this.weeksPerStep)),
                    dose,
                    last: i === this.steps.length - 1,
                }));
            },
            get maintenanceDate() {
                const s = this.schedule;
                return s.length ? s[s.length - 1].date : '—';
            },
        };
    }
</script>
