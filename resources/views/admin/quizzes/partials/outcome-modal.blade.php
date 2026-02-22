<!-- Outcome Modal -->
<div id="outcome-modal" class="fixed inset-0 bg-black/50 z-50 hidden">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-xl">
            <form id="outcome-form" action="{{ route('admin.quizzes.outcomes.store', $quiz) }}" method="POST">
                @csrf
                <input type="hidden" name="_method" id="outcome-method-input" value="POST">
                <input type="hidden" name="condition_type" id="outcome-condition-type" value="">

                <div class="p-6 border-b">
                    <h3 class="text-lg font-semibold" id="outcome-modal-title">Add Outcome</h3>
                </div>

                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Outcome Name</label>
                        <input type="text" name="name" id="outcome-name" required
                            class="w-full rounded-lg border-gray-300 focus:border-brand-gold focus:ring-brand-gold">
                    </div>

                    <!-- Condition Type Selector -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Condition Type</label>
                        <div class="flex gap-4">
                            <label class="inline-flex items-center">
                                <input type="radio" name="condition_type_radio" value="" onchange="switchConditionType(this.value)" class="text-brand-gold focus:ring-brand-gold">
                                <span class="ml-1.5 text-sm">None</span>
                            </label>
                            <label class="inline-flex items-center">
                                <input type="radio" name="condition_type_radio" value="segment" onchange="switchConditionType(this.value)" class="text-brand-gold focus:ring-brand-gold">
                                <span class="ml-1.5 text-sm">Segment</span>
                            </label>
                            <label class="inline-flex items-center">
                                <input type="radio" name="condition_type_radio" value="score" onchange="switchConditionType(this.value)" class="text-brand-gold focus:ring-brand-gold">
                                <span class="ml-1.5 text-sm">Score</span>
                            </label>
                            <label class="inline-flex items-center">
                                <input type="radio" name="condition_type_radio" value="answer" onchange="switchConditionType(this.value)" class="text-brand-gold focus:ring-brand-gold">
                                <span class="ml-1.5 text-sm">Answer</span>
                            </label>
                        </div>
                    </div>

                    <!-- Segment fields -->
                    <div id="condition-segment-fields" class="hidden">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Segment</label>
                        <select name="segment" id="outcome-segment"
                            class="w-full rounded-lg border-gray-300 focus:border-brand-gold focus:ring-brand-gold">
                            <option value="">Select segment</option>
                            <option value="tof">TOF (Top of Funnel)</option>
                            <option value="mof">MOF (Middle of Funnel)</option>
                            <option value="bof">BOF (Bottom of Funnel)</option>
                        </select>
                    </div>

                    <!-- Score fields -->
                    <div id="condition-score-fields" class="hidden">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Min Score</label>
                        <input type="number" name="min_score" id="outcome-min-score" value="0" min="0"
                            class="w-full rounded-lg border-gray-300 focus:border-brand-gold focus:ring-brand-gold">
                    </div>

                    <!-- Answer fields -->
                    <div id="condition-answer-fields" class="hidden space-y-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Answer Question <span class="text-xs text-gray-400">(klaviyo_property)</span></label>
                            <input type="text" name="answer_question" id="outcome-answer-question" placeholder="e.g. awareness_level"
                                class="w-full rounded-lg border-gray-300 focus:border-brand-gold focus:ring-brand-gold">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Answer Value</label>
                            <input type="text" name="answer_value" id="outcome-answer-value" placeholder="e.g. brand_new"
                                class="w-full rounded-lg border-gray-300 focus:border-brand-gold focus:ring-brand-gold">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Headline</label>
                        <input type="text" name="result_title" id="outcome-headline"
                            class="w-full rounded-lg border-gray-300 focus:border-brand-gold focus:ring-brand-gold">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Body Text</label>
                        <textarea name="result_message" id="outcome-body" rows="3"
                            class="w-full rounded-lg border-gray-300 focus:border-brand-gold focus:ring-brand-gold"></textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Redirect URL</label>
                        <input type="text" name="redirect_url" id="outcome-redirect-url" placeholder="/peptides or https://..."
                            class="w-full rounded-lg border-gray-300 focus:border-brand-gold focus:ring-brand-gold">
                    </div>
                </div>

                <div class="p-6 border-t bg-gray-50 flex justify-end gap-3">
                    <button type="button" onclick="closeOutcomeModal()" class="btn btn-secondary">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="outcome-submit-btn">Add Outcome</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function switchConditionType(type) {
    document.getElementById('outcome-condition-type').value = type;
    document.getElementById('condition-segment-fields').classList.toggle('hidden', type !== 'segment');
    document.getElementById('condition-score-fields').classList.toggle('hidden', type !== 'score');
    document.getElementById('condition-answer-fields').classList.toggle('hidden', type !== 'answer');
}

function showAddOutcome() {
    document.getElementById('outcome-modal').classList.remove('hidden');
    document.getElementById('outcome-modal-title').textContent = 'Add Outcome';
    document.getElementById('outcome-submit-btn').textContent = 'Add Outcome';
    document.getElementById('outcome-form').action = '{{ route("admin.quizzes.outcomes.store", $quiz) }}';
    document.getElementById('outcome-method-input').value = 'POST';
    document.getElementById('outcome-condition-type').value = '';
    document.getElementById('outcome-name').value = '';
    document.getElementById('outcome-segment').value = '';
    document.getElementById('outcome-min-score').value = '0';
    document.getElementById('outcome-answer-question').value = '';
    document.getElementById('outcome-answer-value').value = '';
    document.getElementById('outcome-headline').value = '';
    document.getElementById('outcome-body').value = '';
    document.getElementById('outcome-redirect-url').value = '';

    // Reset radio buttons and hide all condition fields
    var radios = document.querySelectorAll('input[name="condition_type_radio"]');
    radios.forEach(function(r) { r.checked = r.value === ''; });
    switchConditionType('');
}

function closeOutcomeModal() {
    document.getElementById('outcome-modal').classList.add('hidden');
}

function editOutcome(id, outcomeData) {
    var conditions = outcomeData.conditions || {};
    var condType = conditions.type || '';

    document.getElementById('outcome-modal').classList.remove('hidden');
    document.getElementById('outcome-modal-title').textContent = 'Edit Outcome';
    document.getElementById('outcome-submit-btn').textContent = 'Update Outcome';
    document.getElementById('outcome-form').action = '{{ url("admin/quizzes/" . $quiz->id . "/outcomes") }}/' + id;
    document.getElementById('outcome-method-input').value = 'PUT';
    document.getElementById('outcome-condition-type').value = condType;
    document.getElementById('outcome-name').value = outcomeData.name || '';
    document.getElementById('outcome-segment').value = conditions.segment || '';
    document.getElementById('outcome-min-score').value = conditions.min_score || 0;
    document.getElementById('outcome-answer-question').value = conditions.question || '';
    document.getElementById('outcome-answer-value').value = conditions.value || '';
    document.getElementById('outcome-headline').value = outcomeData.result_title || '';
    document.getElementById('outcome-body').value = outcomeData.result_message || '';
    document.getElementById('outcome-redirect-url').value = outcomeData.redirect_url || '';

    // Set the correct radio button and show matching fields
    var radios = document.querySelectorAll('input[name="condition_type_radio"]');
    radios.forEach(function(r) { r.checked = r.value === condType; });
    switchConditionType(condType);
}
</script>
