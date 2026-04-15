@php
    $protocols = old('protocols', $peptide?->protocols ?? []);
    $compatiblePeptides = old('compatible_peptides', $peptide?->compatible_peptides ?? []);
    $reconstitutionSteps = old('reconstitution_steps', $peptide?->reconstitution_steps ?? []);
    $qualityIndicators = old('quality_indicators', $peptide?->quality_indicators ?? []);
    $effectivenessRatings = old('effectiveness_ratings', $peptide?->effectiveness_ratings ?? []);
    $references = old('references', $peptide?->references ?? []);
@endphp

<!-- Protocols -->
<div class="card">
    <h3 class="text-lg font-semibold text-gray-900 mb-4">Protocols</h3>

    <div x-data="{ items: {{ json_encode($protocols ?: []) }} }">
        <div class="space-y-3 mb-3">
            <template x-for="(item, index) in items" :key="index">
                <div class="p-3 bg-gray-50 rounded-lg border">
                    <div class="flex justify-between items-start mb-2">
                        <span class="text-sm font-medium text-gray-500" x-text="'Protocol #' + (index + 1)"></span>
                        <button type="button" @click="items.splice(index, 1)" class="text-red-400 hover:text-red-600">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                        <div>
                            <label class="text-xs text-gray-500">Goal</label>
                            <input type="text" x-model="item.goal" class="input w-full text-sm" placeholder="e.g., Joint healing">
                            <input type="hidden" :name="'protocols['+index+'][goal]'" :value="item.goal">
                        </div>
                        <div>
                            <label class="text-xs text-gray-500">Dose</label>
                            <input type="text" x-model="item.dose" class="input w-full text-sm" placeholder="e.g., 250-500mcg">
                            <input type="hidden" :name="'protocols['+index+'][dose]'" :value="item.dose">
                        </div>
                        <div>
                            <label class="text-xs text-gray-500">Frequency</label>
                            <input type="text" x-model="item.frequency" class="input w-full text-sm" placeholder="e.g., 1-2x daily">
                            <input type="hidden" :name="'protocols['+index+'][frequency]'" :value="item.frequency">
                        </div>
                        <div>
                            <label class="text-xs text-gray-500">Route</label>
                            <input type="text" x-model="item.route" class="input w-full text-sm" placeholder="e.g., SubQ near injury">
                            <input type="hidden" :name="'protocols['+index+'][route]'" :value="item.route">
                        </div>
                    </div>
                </div>
            </template>
        </div>
        <button type="button" @click="items.push({goal:'',dose:'',frequency:'',route:''})" class="btn btn-secondary text-sm">+ Add Protocol</button>
    </div>
</div>

<!-- Compatible Peptides -->
<div class="card">
    <h3 class="text-lg font-semibold text-gray-900 mb-4">Compatible Peptides</h3>

    <div x-data="{ items: {{ json_encode($compatiblePeptides ?: []) }} }">
        <div class="space-y-2 mb-3">
            <template x-for="(item, index) in items" :key="index">
                <div class="flex gap-2 items-center">
                    <input type="text" x-model="item.name" class="input flex-1 text-sm" placeholder="Peptide name">
                    <input type="hidden" :name="'compatible_peptides['+index+'][name]'" :value="item.name">
                    <select x-model="item.relationship" class="input w-40 text-sm">
                        <option value="Compatible">Compatible</option>
                        <option value="Synergistic">Synergistic</option>
                        <option value="Complementary">Complementary</option>
                        <option value="Stacking">Stacking</option>
                        <option value="Being Studied">Being Studied</option>
                        <option value="Avoid">Avoid</option>
                    </select>
                    <input type="hidden" :name="'compatible_peptides['+index+'][relationship]'" :value="item.relationship">
                    <button type="button" @click="items.splice(index, 1)" class="text-red-400 hover:text-red-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            </template>
        </div>
        <button type="button" @click="items.push({name:'',relationship:'Compatible'})" class="btn btn-secondary text-sm">+ Add Compatible Peptide</button>
    </div>
</div>

<!-- Reconstitution Steps -->
<div class="card">
    <h3 class="text-lg font-semibold text-gray-900 mb-4">Reconstitution Steps</h3>

    <div x-data="{ items: {{ json_encode($reconstitutionSteps ?: []) }} }">
        <div class="space-y-2 mb-3">
            <template x-for="(item, index) in items" :key="index">
                <div class="flex gap-2 items-center">
                    <span class="text-sm font-medium text-gray-400 w-6" x-text="(index + 1) + '.'"></span>
                    <input type="text" :name="'reconstitution_steps[]'" x-model="items[index]" class="input flex-1 text-sm" placeholder="Step description...">
                    <button type="button" @click="items.splice(index, 1)" class="text-red-400 hover:text-red-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            </template>
        </div>
        <button type="button" @click="items.push('')" class="btn btn-secondary text-sm">+ Add Step</button>
    </div>
</div>

<!-- Quality Indicators -->
<div class="card">
    <h3 class="text-lg font-semibold text-gray-900 mb-4">Quality Indicators</h3>

    <div x-data="{ items: {{ json_encode($qualityIndicators ?: []) }} }">
        <div class="space-y-3 mb-3">
            <template x-for="(item, index) in items" :key="index">
                <div class="p-3 bg-gray-50 rounded-lg border">
                    <div class="flex justify-between items-start mb-2">
                        <div class="flex items-center gap-2">
                            <select x-model="item.status" class="input w-16 text-sm text-center">
                                <option value="✓">✓</option>
                                <option value="!">!</option>
                                <option value="✗">✗</option>
                            </select>
                            <input type="hidden" :name="'quality_indicators['+index+'][status]'" :value="item.status">
                        </div>
                        <button type="button" @click="items.splice(index, 1)" class="text-red-400 hover:text-red-600">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                    <div class="space-y-2">
                        <div>
                            <input type="text" x-model="item.title" class="input w-full text-sm" placeholder="Title (e.g., White Fluffy Cake)">
                            <input type="hidden" :name="'quality_indicators['+index+'][title]'" :value="item.title">
                        </div>
                        <div>
                            <input type="text" x-model="item.description" class="input w-full text-sm" placeholder="Description...">
                            <input type="hidden" :name="'quality_indicators['+index+'][description]'" :value="item.description">
                        </div>
                    </div>
                </div>
            </template>
        </div>
        <button type="button" @click="items.push({status:'✓',title:'',description:''})" class="btn btn-secondary text-sm">+ Add Indicator</button>
    </div>
</div>

<!-- Effectiveness Ratings -->
<div class="card">
    <h3 class="text-lg font-semibold text-gray-900 mb-4">Effectiveness Ratings</h3>

    <div x-data="{ items: {{ json_encode(collect($effectivenessRatings ?: [])->map(function($v, $k) { return ['category' => $k, 'rating' => $v]; })->values()) }} }">
        <div class="space-y-2 mb-3">
            <template x-for="(item, index) in items" :key="index">
                <div class="flex gap-2 items-center">
                    <input type="text" x-model="item.category" class="input flex-1 text-sm" placeholder="Category (e.g., muscle_growth)">
                    <input type="hidden" :name="'effectiveness_ratings['+index+'][category]'" :value="item.category">
                    <input type="number" x-model="item.rating" min="0" max="10" class="input w-20 text-sm text-center" placeholder="0-10">
                    <input type="hidden" :name="'effectiveness_ratings['+index+'][rating]'" :value="item.rating">
                    <span class="text-xs text-gray-400">/10</span>
                    <button type="button" @click="items.splice(index, 1)" class="text-red-400 hover:text-red-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            </template>
        </div>
        <button type="button" @click="items.push({category:'',rating:5})" class="btn btn-secondary text-sm">+ Add Rating</button>
    </div>
</div>

<!-- References -->
<div class="card">
    <h3 class="text-lg font-semibold text-gray-900 mb-4">References</h3>

    <div x-data="{ items: {{ json_encode($references ?: []) }} }">
        <div class="space-y-3 mb-3">
            <template x-for="(item, index) in items" :key="index">
                <div class="p-3 bg-gray-50 rounded-lg border">
                    <div class="flex justify-between items-start mb-2">
                        <span class="text-sm font-medium text-gray-500" x-text="'Ref #' + (index + 1)"></span>
                        <button type="button" @click="items.splice(index, 1)" class="text-red-400 hover:text-red-600">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                    <div class="space-y-2">
                        <div>
                            <label class="text-xs text-gray-500">Title</label>
                            <input type="text" x-model="item.title" class="input w-full text-sm" placeholder="Study title">
                            <input type="hidden" :name="'references['+index+'][title]'" :value="item.title">
                        </div>
                        <div>
                            <label class="text-xs text-gray-500">URL</label>
                            <input type="url" x-model="item.url" class="input w-full text-sm" placeholder="https://doi.org/...">
                            <input type="hidden" :name="'references['+index+'][url]'" :value="item.url">
                        </div>
                        <div>
                            <label class="text-xs text-gray-500">Details</label>
                            <input type="text" x-model="item.details" class="input w-full text-sm" placeholder="Study details...">
                            <input type="hidden" :name="'references['+index+'][details]'" :value="item.details">
                        </div>
                        <div>
                            <label class="text-xs text-gray-500">Description</label>
                            <input type="text" x-model="item.description" class="input w-full text-sm" placeholder="Brief description...">
                            <input type="hidden" :name="'references['+index+'][description]'" :value="item.description">
                        </div>
                    </div>
                </div>
            </template>
        </div>
        <button type="button" @click="items.push({title:'',url:'',details:'',description:''})" class="btn btn-secondary text-sm">+ Add Reference</button>
    </div>
</div>
