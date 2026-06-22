{{-- Peptide cycle planner widget --}}
<div x-data="cyclePlanner()" class="space-y-6">
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="grid md:grid-cols-2">
            {{-- Inputs --}}
            <div class="p-6 sm:p-8 space-y-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Cycle length (weeks on)</label>
                    <input type="number" min="1" step="1" x-model.number="cycleWeeks" class="w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Break length (weeks off)</label>
                    <input type="number" min="0" step="1" x-model.number="breakWeeks" class="w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Start date</label>
                    <input type="date" x-model="start" class="w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Cycles to plan</label>
                    <input type="number" min="1" max="12" step="1" x-model.number="cycles" class="w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500">
                </div>
            </div>
            {{-- Result --}}
            <div class="p-6 sm:p-8 bg-surface-50 border-t md:border-t-0 md:border-l border-gray-200 flex flex-col">
                <div class="flex items-center justify-between mb-4">
                    <p class="text-sm font-medium text-gray-500">Next cycle starts</p>
                    @include('calculators.partials._result-actions')
                </div>
                <div class="bg-white rounded-xl border border-gray-200 p-5 text-center">
                    <p class="text-xs uppercase tracking-wide text-gray-400 mb-1">After cycle 1’s break</p>
                    <p class="text-2xl font-bold" style="color: {{ $config['accent'] }};" x-text="nextStart"></p>
                </div>
                <p class="text-xs text-gray-400 mt-auto pt-4">One “round” = <span x-text="cycleWeeks"></span> weeks on + <span x-text="breakWeeks"></span> weeks off = <span x-text="cycleWeeks + breakWeeks"></span> weeks.</p>
            </div>
        </div>
    </div>

    {{-- Schedule --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        <h3 class="font-bold text-gray-900 px-6 pt-5">Cycle schedule</h3>
        <p class="text-sm text-gray-500 px-6 pb-3">On and off date ranges for each planned cycle.</p>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-surface-50 text-gray-500"><tr><th class="text-left font-medium px-6 py-2">Cycle</th><th class="text-left font-medium px-6 py-2">On (dosing)</th><th class="text-left font-medium px-6 py-2">Off (break)</th></tr></thead>
                <tbody>
                    <template x-for="row in schedule" :key="row.n">
                        <tr class="border-t border-gray-100">
                            <td class="px-6 py-2.5 font-semibold text-gray-900" x-text="row.n"></td>
                            <td class="px-6 py-2.5 text-gray-700"><span x-text="row.onStart"></span> → <span x-text="row.onEnd"></span></td>
                            <td class="px-6 py-2.5 text-gray-500" x-text="row.breakWeeks > 0 ? (row.offStart + ' → ' + row.offEnd) : 'no break'"></td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    function cyclePlanner() {
        const today = new Date();
        const iso = today.toISOString().slice(0, 10);
        const defaults = { cycleWeeks: 8, breakWeeks: 4, start: iso, cycles: 4 };
        return {
            ...defaults, copied: false,
            reset() { Object.assign(this, defaults); },
            copy() { try { navigator.clipboard.writeText(`Next cycle starts ${this.nextStart} (${this.cycleWeeks}w on / ${this.breakWeeks}w off)`); } catch (e) {} this.copied = true; setTimeout(() => this.copied = false, 1500); },
            addWeeks(date, w) { const d = new Date(date); d.setDate(d.getDate() + w * 7); return d; },
            fmt(d) { return d.toLocaleDateString(undefined, { year: 'numeric', month: 'short', day: 'numeric' }); },
            get schedule() {
                const out = [];
                if (!this.start) return out;
                let cursor = new Date(this.start);
                const n = Math.min(Math.max(this.cycles || 1, 1), 12);
                for (let i = 1; i <= n; i++) {
                    const onStart = new Date(cursor);
                    const onEnd = this.addWeeks(onStart, this.cycleWeeks);
                    const offStart = new Date(onEnd);
                    const offEnd = this.addWeeks(offStart, this.breakWeeks);
                    out.push({
                        n: i, breakWeeks: this.breakWeeks,
                        onStart: this.fmt(onStart), onEnd: this.fmt(onEnd),
                        offStart: this.fmt(offStart), offEnd: this.fmt(offEnd),
                    });
                    cursor = offEnd;
                }
                return out;
            },
            get nextStart() {
                if (!this.start) return '—';
                const onEnd = this.addWeeks(new Date(this.start), this.cycleWeeks);
                return this.fmt(this.addWeeks(onEnd, this.breakWeeks));
            },
        };
    }
</script>
