{{-- Protocol tool widget — multi-peptide planner --}}
<div x-data="protocolCalc(@js($peptides->map(fn($p) => ['id' => $p->id, 'name' => $p->name])->values()))" class="space-y-6">

    {{-- Add peptide --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 sm:p-8">
        <label class="block text-sm font-medium text-gray-700 mb-1.5">Add a peptide to your protocol</label>
        <div class="flex flex-col sm:flex-row gap-2">
            <select x-model="picker" class="flex-1 rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500">
                <option value="">Select a peptide…</option>
                <template x-for="p in available" :key="p.id">
                    <option :value="p.id" x-text="p.name"></option>
                </template>
            </select>
            <button type="button" @click="addPeptide()" :disabled="!picker"
                    class="px-5 py-2.5 rounded-lg text-white font-semibold disabled:opacity-40"
                    style="background-color: {{ $config['accent'] }};">Add</button>
        </div>
        <p x-show="rows.length === 0" class="text-sm text-gray-400 mt-3">No peptides added yet. Add one or more to build your plan.</p>
    </div>

    {{-- Peptide rows --}}
    <template x-for="(row, idx) in rows" :key="row.uid">
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-bold text-gray-900" x-text="row.name"></h3>
                <button type="button" @click="removeRow(idx)" class="text-sm text-red-500 hover:text-red-700">Remove</button>
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
                <div><label class="block text-xs text-gray-500 mb-1">Vial (mg)</label><input type="number" min="0" step="0.5" x-model.number="row.mg" class="w-full rounded-lg border-gray-300 text-sm focus:border-primary-500 focus:ring-primary-500"></div>
                <div><label class="block text-xs text-gray-500 mb-1">Water (mL)</label><input type="number" min="0" step="0.5" x-model.number="row.water" class="w-full rounded-lg border-gray-300 text-sm focus:border-primary-500 focus:ring-primary-500"></div>
                <div><label class="block text-xs text-gray-500 mb-1">Dose (mcg)</label><input type="number" min="0" step="10" x-model.number="row.dose" class="w-full rounded-lg border-gray-300 text-sm focus:border-primary-500 focus:ring-primary-500"></div>
                <div><label class="block text-xs text-gray-500 mb-1">Days/week</label><input type="number" min="1" max="7" x-model.number="row.days" class="w-full rounded-lg border-gray-300 text-sm focus:border-primary-500 focus:ring-primary-500"></div>
                <div><label class="block text-xs text-gray-500 mb-1">Timing</label>
                    <select x-model="row.timing" class="w-full rounded-lg border-gray-300 text-sm focus:border-primary-500 focus:ring-primary-500"><option value="AM">AM</option><option value="PM">PM</option><option value="Both">AM+PM</option></select>
                </div>
                <div class="flex flex-col justify-end">
                    <label class="block text-xs text-gray-500 mb-1">Draw</label>
                    <span class="text-lg font-bold" style="color: {{ $config['accent'] }};" x-text="units(row) + ' u'"></span>
                </div>
            </div>
            <p class="text-xs text-gray-400 mt-2"><span x-text="conc(row).toLocaleString()"></span> mcg/mL · <span x-text="dosesPerVial(row)"></span> doses per vial</p>
        </div>
    </template>

    {{-- Weekly grid --}}
    <div x-show="rows.length > 0" class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        <h3 class="font-bold text-gray-900 px-6 pt-5">Weekly schedule</h3>
        <p class="text-sm text-gray-500 px-6 pb-3">Illustrative — fills the first N days of the week at each peptide's timing slot.</p>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-surface-50 text-gray-500">
                    <tr>
                        <th class="text-left font-medium px-4 py-2">Peptide</th>
                        <template x-for="d in dayLabels" :key="d"><th class="font-medium px-2 py-2 text-center" x-text="d"></th></template>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="(row, idx) in rows" :key="row.uid">
                        <tr class="border-t border-gray-100">
                            <td class="px-4 py-2.5 font-medium text-gray-900" x-text="row.name"></td>
                            <template x-for="d in 7" :key="d">
                                <td class="px-2 py-2.5 text-center">
                                    <span x-show="d <= row.days" class="inline-block text-[10px] font-semibold px-1.5 py-0.5 rounded"
                                          style="background-color: {{ $config['accent'] }}1f; color: {{ $config['accent'] }};" x-text="row.timing"></span>
                                    <span x-show="d > row.days" class="text-gray-300">·</span>
                                </td>
                            </template>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    function protocolCalc(peptides) {
        return {
            peptides: peptides, picker: '', rows: [], nextUid: 1,
            dayLabels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
            get available() { return this.peptides; },
            addPeptide() {
                if (!this.picker) return;
                const p = this.peptides.find(x => x.id == this.picker);
                if (!p) return;
                this.rows.push({ uid: this.nextUid++, name: p.name, mg: 5, water: 2, dose: 250, days: 3, timing: 'AM' });
                this.picker = '';
            },
            removeRow(i) { this.rows.splice(i, 1); },
            conc(row) { return row.water > 0 ? Math.round((row.mg * 1000) / row.water) : 0; },
            units(row) { const c = this.conc(row); return c > 0 ? (Math.round((row.dose / (c / 100)) * 10) / 10) : 0; },
            dosesPerVial(row) { return row.dose > 0 ? Math.floor((row.mg * 1000) / row.dose) : 0; },
        };
    }
</script>
