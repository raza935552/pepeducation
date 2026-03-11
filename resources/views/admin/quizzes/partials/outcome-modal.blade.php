@php
    // Build question data for outcome answer dropdowns (enhanced with friendly labels)
    // Deduplicate by klaviyo_property — keep the first slide with the most options
    $outcomeSlides = $quiz->questions->sortBy('order')->values()->filter(fn ($q) =>
        in_array($q->slide_type, ['question', 'question_text']) && $q->klaviyo_property
    )->unique('klaviyo_property')->map(fn ($q) => [
        'id' => $q->id,
        'klaviyo_property' => $q->klaviyo_property,
        'label' => $q->question_text ?: $q->content_title ?: 'Slide #' . $q->order,
        'friendly_label' => \Str::of($q->klaviyo_property)->replace('_', ' ')->title()->toString() . ' question',
        'options' => collect($q->options ?? [])->map(fn ($o) => [
            'value' => $o['klaviyo_value'] ?? $o['label'] ?? $o['value'] ?? '',
            'label' => $o['label'] ?? $o['text'] ?? $o['value'] ?? '',
        ])->values()->toArray(),
    ])->values()->toArray();

    // ResultsBank lookup grouped by health_goal for auto-fill feature
    $resultsBankLookup = \App\Models\ResultsBank::where('is_active', true)
        ->get()
        ->groupBy('health_goal')
        ->map(fn ($entries) => $entries->first())
        ->map(fn ($entry) => [
            'peptide_name' => $entry->peptide_name,
            'description' => $entry->description,
            'star_rating' => $entry->star_rating,
            'rating_label' => $entry->rating_label,
            'goal_label' => $entry->goal_label,
        ])
        ->toArray();

    // Friendly labels for health goal values
    $healthGoalLabels = \App\Models\ResultsBank::allHealthGoals();
@endphp

{{-- Outcome Modal — Alpine.js --}}
<div x-data="outcomeModal()" x-show="showModal" x-cloak
     @open-outcome-modal.window="openModal($event.detail)"
     @keydown.escape.window="closeModal()"
     class="fixed inset-0 bg-black/50 z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-xl max-h-[90vh] overflow-y-auto"
             @click.outside="closeModal()">
            <form :action="formAction" method="POST" @submit.prevent="submitForm($event)">
                @csrf
                <input type="hidden" name="_method" :value="isEditing ? 'PUT' : 'POST'">
                <input type="hidden" name="condition_type" :value="conditionTypeForSubmit">
                <input type="hidden" name="segment" :value="segment">
                <input type="hidden" name="answer_question" :value="answerQuestion">
                <input type="hidden" name="answer_value" :value="answerValue">

                {{-- Header --}}
                <div class="p-6 border-b">
                    <h3 class="text-lg font-semibold" x-text="isEditing ? 'Edit Outcome' : 'Add Outcome'"></h3>
                </div>

                <div class="p-6 space-y-5">
                    {{-- Outcome Name --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Outcome Name</label>
                        <input type="text" name="name" x-model="name" required
                            class="w-full rounded-lg border-gray-300 focus:border-brand-gold focus:ring-brand-gold"
                            placeholder="e.g. Fat Loss — Beginner">
                    </div>

                    {{-- Plain English Condition Builder --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Show this result...</label>
                        <div class="flex flex-wrap gap-2">
                            <button type="button" @click="conditionMode = 'always'"
                                :class="conditionMode === 'always' ? 'bg-brand-gold text-white border-brand-gold' : 'bg-white text-gray-700 border-gray-300 hover:border-brand-gold'"
                                class="px-3 py-2 text-sm rounded-lg border transition-colors">
                                Always (default)
                            </button>
                            <button type="button" @click="conditionMode = 'segment'"
                                :class="conditionMode === 'segment' ? 'bg-brand-gold text-white border-brand-gold' : 'bg-white text-gray-700 border-gray-300 hover:border-brand-gold'"
                                class="px-3 py-2 text-sm rounded-lg border transition-colors">
                                When user's segment is...
                            </button>
                            <button type="button" @click="conditionMode = 'answer'"
                                :class="conditionMode === 'answer' ? 'bg-brand-gold text-white border-brand-gold' : 'bg-white text-gray-700 border-gray-300 hover:border-brand-gold'"
                                class="px-3 py-2 text-sm rounded-lg border transition-colors">
                                When user answered...
                            </button>
                        </div>
                    </div>

                    {{-- Segment Picker (visual cards) --}}
                    <div x-show="conditionMode === 'segment'" x-collapse>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Which segment?</label>
                        <div class="grid grid-cols-3 gap-3">
                            <button type="button" @click="segment = 'tof'"
                                :class="segment === 'tof' ? 'ring-2 ring-blue-400 border-blue-400' : 'border-gray-200 hover:border-blue-300'"
                                class="flex flex-col items-center p-3 rounded-lg border transition-all">
                                <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center mb-1">
                                    <span class="text-blue-600 text-xs font-bold">TOF</span>
                                </div>
                                <span class="text-xs font-medium text-gray-800">Explorer</span>
                                <span class="text-[10px] text-gray-400">Top of Funnel</span>
                            </button>
                            <button type="button" @click="segment = 'mof'"
                                :class="segment === 'mof' ? 'ring-2 ring-yellow-400 border-yellow-400' : 'border-gray-200 hover:border-yellow-300'"
                                class="flex flex-col items-center p-3 rounded-lg border transition-all">
                                <div class="w-8 h-8 rounded-full bg-yellow-100 flex items-center justify-center mb-1">
                                    <span class="text-yellow-600 text-xs font-bold">MOF</span>
                                </div>
                                <span class="text-xs font-medium text-gray-800">Researcher</span>
                                <span class="text-[10px] text-gray-400">Middle of Funnel</span>
                            </button>
                            <button type="button" @click="segment = 'bof'"
                                :class="segment === 'bof' ? 'ring-2 ring-green-400 border-green-400' : 'border-gray-200 hover:border-green-300'"
                                class="flex flex-col items-center p-3 rounded-lg border transition-all">
                                <div class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center mb-1">
                                    <span class="text-green-600 text-xs font-bold">BOF</span>
                                </div>
                                <span class="text-xs font-medium text-gray-800">Ready to Buy</span>
                                <span class="text-[10px] text-gray-400">Bottom of Funnel</span>
                            </button>
                        </div>
                    </div>

                    {{-- Answer Picker (cascading dropdowns with friendly labels) --}}
                    <div x-show="conditionMode === 'answer'" x-collapse>
                        <div class="space-y-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Which question?</label>
                                <select x-model="answerQuestion" @change="answerValue = ''"
                                    x-ref="questionSelect"
                                    x-effect="populateQuestionDropdown($refs.questionSelect, slides, answerQuestion)"
                                    class="w-full rounded-lg border-gray-300 focus:border-brand-gold focus:ring-brand-gold">
                                </select>
                            </div>
                            <div x-show="answerQuestion">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Which answer?</label>
                                <select x-model="answerValue"
                                    x-ref="answerSelect"
                                    x-effect="populateAnswerDropdown($refs.answerSelect, selectedSlideOptions, answerValue, answerQuestion)"
                                    class="w-full rounded-lg border-gray-300 focus:border-brand-gold focus:ring-brand-gold">
                                </select>
                            </div>
                        </div>
                    </div>

                    {{-- Auto-fill from Results Bank (2.2) --}}
                    <div x-show="conditionMode === 'answer' && answerQuestion === 'health_goal' && answerValue && resultsBankEntry" x-collapse>
                        <div class="border border-amber-200 rounded-lg bg-amber-50 p-4">
                            <div class="flex items-center justify-between mb-2">
                                <label class="flex items-center gap-2">
                                    <input type="checkbox" x-model="autoFillFromBank"
                                        class="rounded border-gray-300 text-brand-gold focus:ring-brand-gold">
                                    <span class="text-sm font-medium text-amber-800">Auto-fill from Results Bank</span>
                                </label>
                            </div>
                            <div x-show="autoFillFromBank" x-collapse>
                                <div class="bg-white rounded-lg border border-amber-200 p-3 mb-3">
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="text-sm font-bold text-gray-900" x-text="resultsBankEntry?.peptide_name"></span>
                                        <template x-if="resultsBankEntry?.star_rating">
                                            <span class="text-xs text-amber-500" x-text="'★ ' + resultsBankEntry.star_rating"></span>
                                        </template>
                                    </div>
                                    <p class="text-xs text-gray-500 line-clamp-2" x-text="resultsBankEntry?.description"></p>
                                </div>
                                <button type="button" @click="applyAutoFill()"
                                    class="btn btn-secondary text-xs w-full">
                                    Apply to Headline &amp; Body
                                </button>
                            </div>
                        </div>
                    </div>

                    <hr class="border-gray-200">

                    {{-- Result Content --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Headline</label>
                        <input type="text" name="result_title" x-model="resultTitle"
                            class="w-full rounded-lg border-gray-300 focus:border-brand-gold focus:ring-brand-gold"
                            placeholder="Your #1 peptide match is...">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Body Text</label>
                        <textarea name="result_message" x-model="resultMessage" rows="3"
                            class="w-full rounded-lg border-gray-300 focus:border-brand-gold focus:ring-brand-gold"
                            placeholder="Description of the recommendation..."></textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Redirect URL</label>
                        <input type="text" name="redirect_url" x-model="redirectUrl" placeholder="/peptides or https://..."
                            class="w-full rounded-lg border-gray-300 focus:border-brand-gold focus:ring-brand-gold">
                    </div>
                </div>

                {{-- Footer --}}
                <div class="p-6 border-t bg-gray-50 flex justify-end gap-3">
                    <button type="button" @click="closeModal()" class="btn btn-secondary">Cancel</button>
                    <button type="submit" class="btn btn-primary" x-text="isEditing ? 'Update Outcome' : 'Add Outcome'"></button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function outcomeModal() {
    return {
        showModal: false,
        isEditing: false,
        editId: null,

        // Form fields
        name: '',
        conditionMode: 'always', // 'always' | 'segment' | 'answer'
        segment: '',
        answerQuestion: '',
        answerValue: '',
        resultTitle: '',
        resultMessage: '',
        redirectUrl: '',
        autoFillFromBank: false,

        // Data
        slides: @json($outcomeSlides),
        resultsBank: @json($resultsBankLookup),
        healthGoalLabels: @json($healthGoalLabels),

        // Computed
        get formAction() {
            if (this.isEditing && this.editId) {
                return '{{ url("admin/quizzes/" . $quiz->id . "/outcomes") }}/' + this.editId;
            }
            return '{{ route("admin.quizzes.outcomes.store", $quiz) }}';
        },

        get conditionTypeForSubmit() {
            return this.conditionMode === 'always' ? '' : this.conditionMode;
        },

        get selectedSlideOptions() {
            if (!this.answerQuestion) return [];
            const slide = this.slides.find(s => s.klaviyo_property === this.answerQuestion);
            return slide ? slide.options : [];
        },

        get resultsBankEntry() {
            if (this.answerQuestion !== 'health_goal' || !this.answerValue) return null;
            return this.resultsBank[this.answerValue] || null;
        },

        // Methods
        openModal(detail) {
            detail = detail || {};
            this.isEditing = !!detail.id;
            this.editId = detail.id || null;

            if (this.isEditing) {
                this.name = detail.name || '';
                this.resultTitle = detail.result_title || '';
                this.resultMessage = detail.result_message || '';
                this.redirectUrl = detail.redirect_url || '';

                const conditions = detail.conditions || {};
                const condType = conditions.type || '';
                if (condType === 'segment') {
                    this.conditionMode = 'segment';
                    this.segment = conditions.segment || '';
                    this.answerQuestion = '';
                    this.answerValue = '';
                } else if (condType === 'answer') {
                    this.conditionMode = 'answer';
                    this.answerQuestion = conditions.question || '';
                    this.answerValue = conditions.value || '';
                    this.segment = '';
                } else {
                    this.conditionMode = 'always';
                    this.segment = '';
                    this.answerQuestion = '';
                    this.answerValue = '';
                }
            } else {
                // Reset for new outcome, but allow pre-filled values
                this.name = '';
                this.resultTitle = '';
                this.resultMessage = '';
                this.redirectUrl = '';

                if (detail.conditionMode) {
                    this.conditionMode = detail.conditionMode;
                    this.segment = detail.segment || '';
                    this.answerQuestion = detail.answerQuestion || '';
                    this.answerValue = detail.answerValue || '';
                } else {
                    this.conditionMode = 'always';
                    this.segment = '';
                    this.answerQuestion = '';
                    this.answerValue = '';
                }
            }

            this.autoFillFromBank = false;
            this.showModal = true;
        },

        applyAutoFill() {
            const entry = this.resultsBankEntry;
            if (!entry) return;
            this.resultTitle = entry.peptide_name;
            this.resultMessage = entry.description || '';
        },

        closeModal() {
            this.showModal = false;
        },

        submitForm(event) {
            event.target.action = this.formAction;
            event.target.submit();
        },

        friendlyAnswerLabel(opt) {
            if (this.answerQuestion === 'health_goal' && this.healthGoalLabels[opt.value]) {
                return this.healthGoalLabels[opt.value];
            }
            return opt.label || opt.value;
        },

        populateQuestionDropdown(select, slides, currentValue) {
            if (!select) return;
            // Build options: blank + each slide
            const needed = [{ value: '', label: 'Select a question...' }];
            (slides || []).forEach(s => needed.push({ value: s.klaviyo_property, label: s.friendly_label }));
            // Only rebuild if options changed
            const currentKeys = [...select.options].map(o => o.value).join('|');
            const newKeys = needed.map(o => o.value).join('|');
            if (currentKeys === newKeys) {
                // Just ensure the correct option is selected
                select.value = currentValue;
                return;
            }
            select.innerHTML = '';
            needed.forEach(item => {
                const opt = document.createElement('option');
                opt.value = item.value;
                opt.textContent = item.label;
                select.appendChild(opt);
            });
            select.value = currentValue;
        },

        populateAnswerDropdown(select, options, currentValue, question) {
            if (!select) return;
            const needed = [{ value: '', label: question ? 'Select an answer...' : 'Select question first...' }];
            (options || []).forEach(item => {
                needed.push({ value: item.value, label: this.friendlyAnswerLabel(item) });
            });
            // Only rebuild if options changed
            const currentKeys = [...select.options].map(o => o.value).join('|');
            const newKeys = needed.map(o => o.value).join('|');
            if (currentKeys === newKeys) {
                select.value = currentValue;
                return;
            }
            select.innerHTML = '';
            needed.forEach(item => {
                const opt = document.createElement('option');
                opt.value = item.value;
                opt.textContent = item.label;
                select.appendChild(opt);
            });
            select.value = currentValue;
        },
    };
}
</script>
