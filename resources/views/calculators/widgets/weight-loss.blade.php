{{-- GLP-1 weight-loss calculator widget — goal-reverse + timeline --}}
<div x-data="weightLossCalc()" class="space-y-6">
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 sm:p-8">
        <div class="grid sm:grid-cols-2 lg:grid-cols-5 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Compound</label>
                <select x-model="compound" class="w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500">
                    <option value="semaglutide">Semaglutide</option>
                    <option value="tirzepatide">Tirzepatide</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Units</label>
                <div class="inline-flex rounded-lg border border-gray-300 p-1 w-full">
                    <button type="button" @click="units='metric'" :class="units==='metric' ? 'bg-primary-500 text-white' : 'text-gray-600'" class="flex-1 px-2 py-1.5 rounded-md text-sm font-medium">Metric</button>
                    <button type="button" @click="units='imperial'" :class="units==='imperial' ? 'bg-primary-500 text-white' : 'text-gray-600'" class="flex-1 px-2 py-1.5 rounded-md text-sm font-medium">Imperial</button>
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5"><span x-text="units==='metric' ? 'Height (cm)' : 'Height (in)'"></span></label>
                <input type="number" min="0" x-model.number="height" class="w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Current <span x-text="units==='metric' ? '(kg)' : '(lb)'"></span></label>
                <input type="number" min="0" step="0.1" x-model.number="current" class="w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Goal <span x-text="units==='metric' ? '(kg)' : '(lb)'"></span></label>
                <input type="number" min="0" step="0.1" x-model.number="goal" class="w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500">
            </div>
        </div>

        <div class="mt-6 grid grid-cols-2 sm:grid-cols-4 gap-3">
            <div class="bg-surface-50 rounded-xl p-4 text-center">
                <p class="text-xs uppercase tracking-wide text-gray-400">To lose</p>
                <p class="text-2xl font-bold text-gray-900" x-text="toLose"></p>
            </div>
            <div class="bg-surface-50 rounded-xl p-4 text-center">
                <p class="text-xs uppercase tracking-wide text-gray-400">Est. time to goal</p>
                <p class="text-2xl font-bold" style="color: {{ $config['accent'] }};" x-text="weeksToGoal"></p>
            </div>
            <div class="bg-surface-50 rounded-xl p-4 text-center">
                <p class="text-xs uppercase tracking-wide text-gray-400">Goal BMI</p>
                <p class="text-2xl font-bold text-gray-900" x-text="goalBmi"></p>
            </div>
            <div class="bg-surface-50 rounded-xl p-4 text-center">
                <p class="text-xs uppercase tracking-wide text-gray-400">Required loss</p>
                <p class="text-2xl font-bold text-gray-900" x-text="requiredPct"></p>
            </div>
        </div>
    </div>

    {{-- Trajectory chart --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
        <div class="flex items-center justify-between mb-1">
            <h3 class="font-bold text-gray-900">Weight trajectory</h3>
            @include('calculators.partials._result-actions')
        </div>
        <p class="text-sm text-gray-500 mb-4">Average curve over 68 weeks on <span x-text="compound"></span>.</p>
        <svg viewBox="0 0 300 80" class="w-full h-24" preserveAspectRatio="none">
            <polyline :points="spark.area" fill="{{ $config['accent'] }}" opacity="0.08"/>
            <polyline :points="spark.line" fill="none" stroke="{{ $config['accent'] }}" stroke-width="2.5" stroke-linejoin="round" stroke-linecap="round" style="transition: all .3s"/>
        </svg>
        <div class="flex justify-between text-[11px] text-gray-400 mt-1"><span>Start</span><span>Week 24</span><span>Week 68</span></div>
    </div>

    {{-- Milestone timeline --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        <h3 class="font-bold text-gray-900 px-6 pt-5">Projected milestones</h3>
        <p class="text-sm text-gray-500 px-6 pb-3">Average trajectory from the published trials. Individual results vary widely.</p>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-surface-50 text-gray-500"><tr><th class="text-left font-medium px-6 py-2">Milestone</th><th class="text-left font-medium px-6 py-2">Est. loss</th><th class="text-left font-medium px-6 py-2">Weight</th><th class="text-left font-medium px-6 py-2">BMI</th></tr></thead>
                <tbody>
                    <template x-for="(m, i) in timeline" :key="i">
                        <tr class="border-t border-gray-100" :class="m.reached && 'bg-green-50'">
                            <td class="px-6 py-2.5 text-gray-700" x-text="m.label"></td>
                            <td class="px-6 py-2.5 text-gray-500" x-text="m.pct"></td>
                            <td class="px-6 py-2.5 font-semibold text-gray-900" x-text="m.weight"></td>
                            <td class="px-6 py-2.5 text-gray-700" x-text="m.bmi"></td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    function weightLossCalc() {
        const defaults = { compound: 'semaglutide', units: 'metric', height: 175, current: 95, goal: 80 };
        return {
            ...defaults, copied: false,
            reset() { Object.assign(this, defaults); },
            copy() { try { navigator.clipboard.writeText(`Lose ${this.toLose} on ${this.compound} — est. ${this.weeksToGoal} (goal BMI ${this.goalBmi})`); } catch (e) {} this.copied = true; setTimeout(() => this.copied = false, 1500); },
            get spark() {
                const c = this.curve, w0 = this.curKg;
                const ws = c.map(p => w0 * (1 - p[1]));
                const minW = Math.min(...ws), maxW = Math.max(...ws), range = (maxW - minW) || 1;
                const pts = c.map((p, i) => `${((p[0] / 68) * 300).toFixed(1)},${(72 - ((ws[i] - minW) / range) * 60).toFixed(1)}`);
                return { line: pts.join(' '), area: `0,80 ${pts.join(' ')} 300,80` };
            },
            // cumulative average % loss at [week, fraction]
            curves: {
                semaglutide: [[0,0],[4,0.02],[12,0.06],[24,0.10],[52,0.135],[68,0.15]],
                tirzepatide: [[0,0],[4,0.03],[12,0.075],[24,0.15],[52,0.19],[68,0.21]],
            },
            get heightM() { return this.units === 'metric' ? (this.height||0)/100 : (this.height||0)*0.0254; },
            get curKg() { return this.units === 'metric' ? (this.current||0) : (this.current||0)*0.453592; },
            get goalKg() { return this.units === 'metric' ? (this.goal||0) : (this.goal||0)*0.453592; },
            bmiOf(kg) { return this.heightM>0 ? kg/(this.heightM*this.heightM) : 0; },
            get goalBmi() { return this.bmiOf(this.goalKg).toFixed(1); },
            get toLose() { const d = this.curKg - this.goalKg; return d>0 ? (this.units==='metric' ? d.toFixed(1)+' kg' : (d/0.453592).toFixed(0)+' lb') : '—'; },
            get requiredFrac() { return this.curKg>0 ? (this.curKg - this.goalKg)/this.curKg : 0; },
            get requiredPct() { return this.requiredFrac>0 ? Math.round(this.requiredFrac*100)+'%' : '—'; },
            get curve() { return this.curves[this.compound]; },
            get weeksToGoal() {
                const r = this.requiredFrac;
                if (r <= 0) return 'At goal';
                const c = this.curve, max = c[c.length-1][1];
                if (r > max) return '> 68 wks';
                for (let i = 1; i < c.length; i++) {
                    if (r <= c[i][1]) {
                        const [w0,f0] = c[i-1], [w1,f1] = c[i];
                        const wk = w0 + (r - f0) / (f1 - f0) * (w1 - w0);
                        return '~' + Math.round(wk) + ' wks';
                    }
                }
                return '> 68 wks';
            },
            fmtW(kg) { return this.units==='metric' ? kg.toFixed(1)+' kg' : (kg/0.453592).toFixed(0)+' lb'; },
            get timeline() {
                return this.curve.filter(p => p[0] > 0).map(([wk, frac]) => {
                    const w = this.curKg * (1 - frac);
                    return { label: 'Week ' + wk, pct: '−' + Math.round(frac*100) + '%', weight: this.fmtW(w), bmi: this.bmiOf(w).toFixed(1), reached: frac >= this.requiredFrac && this.requiredFrac > 0 };
                });
            },
        };
    }
</script>
