<div class="card">
    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Content</h3>

    <!-- Overview -->
    <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Overview</label>
        <textarea name="overview" rows="4" class="input w-full"
                  placeholder="Brief overview of the peptide...">{{ old('overview', $peptide?->overview) }}</textarea>
    </div>

    <!-- Mechanism of Action -->
    <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Mechanism of Action</label>
        <textarea name="mechanism_of_action" rows="3" class="input w-full"
                  placeholder="How does this peptide work?">{{ old('mechanism_of_action', $peptide?->mechanism_of_action) }}</textarea>
    </div>

    <!-- Key Benefits -->
    <div class="mb-4" x-data="{ items: {{ json_encode(old('key_benefits', $peptide?->key_benefits ?? [])) }} }">
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Key Benefits</label>
        <div class="space-y-2 mb-2">
            <template x-for="(item, index) in items" :key="index">
                <div class="flex gap-2">
                    <input type="text" name="key_benefits[]" x-model="items[index]" class="input flex-1">
                    <button type="button" @click="items.splice(index, 1)" class="btn btn-ghost text-red-500">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </button>
                </div>
            </template>
        </div>
        <button type="button" @click="items.push('')" class="btn btn-secondary text-sm">+ Add Benefit</button>
    </div>

    <!-- What to Expect -->
    <div class="mb-4" x-data="{ items: {{ json_encode(old('what_to_expect', $peptide?->what_to_expect ?? [])) }} }">
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">What to Expect (Timeline)</label>
        <div class="space-y-2 mb-2">
            <template x-for="(item, index) in items" :key="index">
                <div class="flex gap-2">
                    <input type="text" name="what_to_expect[]" x-model="items[index]" class="input flex-1"
                           placeholder="e.g., Week 1-2: Initial effects">
                    <button type="button" @click="items.splice(index, 1)" class="btn btn-ghost text-red-500">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </button>
                </div>
            </template>
        </div>
        <button type="button" @click="items.push('')" class="btn btn-secondary text-sm">+ Add Timeline Entry</button>
    </div>

    <!-- Safety Warnings -->
    <div x-data="{ items: {{ json_encode(old('safety_warnings', $peptide?->safety_warnings ?? [])) }} }">
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Safety Warnings</label>
        <div class="space-y-2 mb-2">
            <template x-for="(item, index) in items" :key="index">
                <div class="flex gap-2">
                    <input type="text" name="safety_warnings[]" x-model="items[index]" class="input flex-1">
                    <button type="button" @click="items.splice(index, 1)" class="btn btn-ghost text-red-500">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </button>
                </div>
            </template>
        </div>
        <button type="button" @click="items.push('')" class="btn btn-secondary text-sm">+ Add Warning</button>
    </div>
</div>
