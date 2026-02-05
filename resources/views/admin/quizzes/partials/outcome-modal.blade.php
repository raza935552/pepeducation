<!-- Outcome Modal -->
<div id="outcome-modal" class="fixed inset-0 bg-black/50 z-50 hidden">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-xl">
            <form id="outcome-form" action="{{ route('admin.quizzes.outcomes.store', $quiz) }}" method="POST">
                @csrf
                <div id="outcome-method"></div>

                <div class="p-6 border-b">
                    <h3 class="text-lg font-semibold" id="outcome-modal-title">Add Outcome</h3>
                </div>

                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Outcome Name</label>
                        <input type="text" name="name" id="outcome-name" required
                            class="w-full rounded-lg border-gray-300 focus:border-brand-gold focus:ring-brand-gold">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Segment</label>
                            <select name="segment" id="outcome-segment"
                                class="w-full rounded-lg border-gray-300 focus:border-brand-gold focus:ring-brand-gold">
                                <option value="">No segment</option>
                                <option value="tof">TOF (Top of Funnel)</option>
                                <option value="mof">MOF (Middle of Funnel)</option>
                                <option value="bof">BOF (Bottom of Funnel)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Min Score</label>
                            <input type="number" name="min_score" id="outcome-min-score" value="0" min="0"
                                class="w-full rounded-lg border-gray-300 focus:border-brand-gold focus:ring-brand-gold">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Headline</label>
                        <input type="text" name="headline" id="outcome-headline"
                            class="w-full rounded-lg border-gray-300 focus:border-brand-gold focus:ring-brand-gold">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Body Text</label>
                        <textarea name="body" id="outcome-body" rows="3"
                            class="w-full rounded-lg border-gray-300 focus:border-brand-gold focus:ring-brand-gold"></textarea>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">CTA Text</label>
                            <input type="text" name="cta_text" id="outcome-cta-text" value="Continue"
                                class="w-full rounded-lg border-gray-300 focus:border-brand-gold focus:ring-brand-gold">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">CTA URL</label>
                            <input type="text" name="cta_url" id="outcome-cta-url"
                                class="w-full rounded-lg border-gray-300 focus:border-brand-gold focus:ring-brand-gold">
                        </div>
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
function showAddOutcome() {
    document.getElementById('outcome-modal').classList.remove('hidden');
    document.getElementById('outcome-modal-title').textContent = 'Add Outcome';
    document.getElementById('outcome-submit-btn').textContent = 'Add Outcome';
    document.getElementById('outcome-form').action = '{{ route("admin.quizzes.outcomes.store", $quiz) }}';
    document.getElementById('outcome-method').innerHTML = '';
    document.getElementById('outcome-name').value = '';
    document.getElementById('outcome-segment').value = '';
    document.getElementById('outcome-min-score').value = '0';
    document.getElementById('outcome-headline').value = '';
    document.getElementById('outcome-body').value = '';
    document.getElementById('outcome-cta-text').value = 'Continue';
    document.getElementById('outcome-cta-url').value = '';
}

function closeOutcomeModal() {
    document.getElementById('outcome-modal').classList.add('hidden');
}

function editOutcome(id) {
    alert('Edit outcome ' + id + ' - implement fetch');
}
</script>
