<!-- Question Modal -->
<div id="question-modal" class="fixed inset-0 bg-black/50 z-50 hidden" x-data="questionModal()">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
            <form :action="formAction" method="POST" @submit.prevent="submitForm">
                @csrf
                <div x-show="isEdit"><input type="hidden" name="_method" value="PUT"></div>

                <div class="p-6 border-b">
                    <h3 class="text-lg font-semibold" x-text="isEdit ? 'Edit Question' : 'Add Question'"></h3>
                </div>

                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Question Text</label>
                        <textarea name="question_text" x-model="question.question_text" rows="2" required
                            class="w-full rounded-lg border-gray-300 focus:border-brand-gold focus:ring-brand-gold"></textarea>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Question Type</label>
                            <select name="question_type" x-model="question.question_type"
                                class="w-full rounded-lg border-gray-300 focus:border-brand-gold focus:ring-brand-gold">
                                <option value="single_choice">Single Choice</option>
                                <option value="multiple_choice">Multiple Choice</option>
                                <option value="text">Text Input</option>
                                <option value="email">Email Input</option>
                                <option value="scale">Scale (1-10)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Klaviyo Property</label>
                            <input type="text" name="klaviyo_property" x-model="question.klaviyo_property"
                                placeholder="e.g. pp_health_goal"
                                class="w-full rounded-lg border-gray-300 focus:border-brand-gold focus:ring-brand-gold">
                        </div>
                    </div>

                    <!-- Options (for choice questions) -->
                    <div x-show="question.question_type === 'single_choice' || question.question_type === 'multiple_choice'">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Answer Options</label>
                        <template x-for="(option, index) in question.options" :key="index">
                            <div class="flex gap-2 mb-2 items-start">
                                <input type="text" x-model="option.label" placeholder="Label" required
                                    class="flex-1 rounded-lg border-gray-300 text-sm">
                                <input type="text" x-model="option.value" placeholder="Value"
                                    class="w-24 rounded-lg border-gray-300 text-sm">
                                <input type="number" x-model.number="option.score_tof" placeholder="TOF" title="TOF Score"
                                    class="w-16 rounded-lg border-gray-300 text-sm">
                                <input type="number" x-model.number="option.score_mof" placeholder="MOF" title="MOF Score"
                                    class="w-16 rounded-lg border-gray-300 text-sm">
                                <input type="number" x-model.number="option.score_bof" placeholder="BOF" title="BOF Score"
                                    class="w-16 rounded-lg border-gray-300 text-sm">
                                <button type="button" @click="removeOption(index)" class="text-red-500 p-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>
                        </template>
                        <button type="button" @click="addOption" class="text-sm text-brand-gold hover:underline">+ Add Option</button>
                    </div>
                </div>

                <div class="p-6 border-t bg-gray-50 flex justify-end gap-3">
                    <button type="button" @click="closeModal" class="btn btn-secondary">Cancel</button>
                    <button type="submit" class="btn btn-primary" x-text="isEdit ? 'Update' : 'Add Question'"></button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function questionModal() {
    return {
        isEdit: false,
        formAction: '{{ route("admin.quizzes.questions.store", $quiz) }}',
        question: {
            question_text: '',
            question_type: 'single_choice',
            klaviyo_property: '',
            options: [{ label: '', value: '', score_tof: 0, score_mof: 0, score_bof: 0 }]
        },
        addOption() {
            this.question.options.push({ label: '', value: '', score_tof: 0, score_mof: 0, score_bof: 0 });
        },
        removeOption(index) {
            this.question.options.splice(index, 1);
        },
        closeModal() {
            document.getElementById('question-modal').classList.add('hidden');
        },
        async submitForm(e) {
            const form = e.target;
            const formData = new FormData();
            formData.append('_token', '{{ csrf_token() }}');
            formData.append('question_text', this.question.question_text);
            formData.append('question_type', this.question.question_type);
            formData.append('klaviyo_property', this.question.klaviyo_property);
            this.question.options.forEach((opt, i) => {
                formData.append(`options[${i}][label]`, opt.label);
                formData.append(`options[${i}][value]`, opt.value || opt.label.toLowerCase().replace(/\s+/g, '_'));
                formData.append(`options[${i}][score_tof]`, opt.score_tof || 0);
                formData.append(`options[${i}][score_mof]`, opt.score_mof || 0);
                formData.append(`options[${i}][score_bof]`, opt.score_bof || 0);
            });
            if (this.isEdit) formData.append('_method', 'PUT');

            await fetch(this.formAction, { method: 'POST', body: formData });
            window.location.reload();
        }
    }
}

function showAddQuestion() {
    const modal = document.getElementById('question-modal');
    modal.classList.remove('hidden');
    Alpine.evaluate(modal, 'isEdit = false');
    Alpine.evaluate(modal, 'formAction = "{{ route("admin.quizzes.questions.store", $quiz) }}"');
}

function editQuestion(id) {
    // Fetch question data and populate modal
    alert('Edit question ' + id + ' - implement fetch');
}
</script>
