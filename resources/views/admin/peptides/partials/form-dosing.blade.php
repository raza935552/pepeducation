<div class="card">
    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Dosing Information</h3>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <!-- Typical Dose -->
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Typical Dose</label>
            <input type="text" name="typical_dose" value="{{ old('typical_dose', $peptide?->typical_dose) }}"
                   class="input w-full" placeholder="e.g., 250-500 mcg">
        </div>

        <!-- Dose Frequency -->
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Dose Frequency</label>
            <input type="text" name="dose_frequency" value="{{ old('dose_frequency', $peptide?->dose_frequency) }}"
                   class="input w-full" placeholder="e.g., 1-2x daily">
        </div>

        <!-- Route -->
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Route</label>
            <input type="text" name="route" value="{{ old('route', $peptide?->route) }}"
                   class="input w-full" placeholder="e.g., Subcutaneous">
        </div>

        <!-- Cycle -->
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Cycle</label>
            <input type="text" name="cycle" value="{{ old('cycle', $peptide?->cycle) }}"
                   class="input w-full" placeholder="e.g., 4-12 weeks">
        </div>

        <!-- Storage -->
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Storage</label>
            <input type="text" name="storage" value="{{ old('storage', $peptide?->storage) }}"
                   class="input w-full" placeholder="e.g., 2-8°C refrigerated">
        </div>
    </div>

    <!-- Injection Sites -->
    <div class="mt-4" x-data="{ sites: {{ json_encode(old('injection_sites', $peptide?->injection_sites ?? [])) }} }">
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Injection Sites</label>
        <div class="flex flex-wrap gap-2 mb-2">
            <template x-for="(site, index) in sites" :key="index">
                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-gray-100 dark:bg-gray-700 text-sm">
                    <span x-text="site"></span>
                    <input type="hidden" name="injection_sites[]" :value="site">
                    <button type="button" @click="sites.splice(index, 1)" class="text-gray-400 hover:text-red-500">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </span>
            </template>
        </div>
        <div class="flex gap-2">
            <input type="text" x-ref="newSite" class="input flex-1" placeholder="Add injection site...">
            <button type="button" @click="if($refs.newSite.value.trim()) { sites.push($refs.newSite.value.trim()); $refs.newSite.value = '' }"
                    class="btn btn-secondary">Add</button>
        </div>
    </div>
</div>
