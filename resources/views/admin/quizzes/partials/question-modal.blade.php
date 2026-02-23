<!-- Question / Slide Modal -->
@php
    // Available tags for option tagging (Klaviyo segmentation)
    $availableTagsJson = json_encode(\App\Models\QuizQuestion::OPTION_TAGS);

    // Build slide list for skip_to and show_conditions dropdowns
    // IDs are cast to string for consistent Alpine x-model matching
    $allSlides = $quiz->questions->sortBy('order')->values();
    $slidesJson = $allSlides->map(fn ($q) => [
        'id' => (string) $q->id,
        'order' => $q->order,
        'label' => '#' . $q->order . ' — ' . Str::limit($q->question_text ?: $q->content_title ?: \App\Models\QuizQuestion::getSlideTypeLabel($q->slide_type ?? 'question'), 40),
        'slide_type' => $q->slide_type ?? 'question',
        'options' => collect($q->options ?? [])->map(fn ($o) => [
            'value' => $o['value'] ?? $o['id'] ?? '',
            'label' => $o['text'] ?? $o['label'] ?? $o['value'] ?? '',
        ])->values()->toArray(),
    ])->toJson();
@endphp

<div id="question-modal" class="fixed inset-0 bg-black/50 z-50 hidden" x-data="questionModal()">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
            <form :action="formAction" method="POST" @submit.prevent="submitForm" novalidate>
                @csrf
                <div x-show="isEdit"><input type="hidden" name="_method" value="PUT"></div>

                <div class="p-6 border-b">
                    <h3 class="text-lg font-semibold" x-text="isEdit ? 'Edit Slide' : 'Add Slide'"></h3>
                    <p class="text-xs text-gray-400 mt-1">Each slide in the quiz can be a question, informational content, or a special interaction. Configure the fields below based on the slide type you choose.</p>
                </div>

                <div class="p-6 space-y-4">
                    <!-- Slide Type Selector -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Slide Type</label>
                        <select name="slide_type" x-model="question.slide_type"
                            class="w-full rounded-lg border-gray-300 focus:border-brand-gold focus:ring-brand-gold">
                            <option value="question">Question (Choice)</option>
                            <option value="question_text">Question (Text Input)</option>
                            <option value="intermission">Intermission</option>
                            <option value="loading">Loading Screen</option>
                            <option value="email_capture">Email Capture</option>
                            <option value="peptide_reveal">Peptide Reveal</option>
                            <option value="vendor_reveal">Vendor Reveal</option>
                            <option value="bridge">Bridge (CTA)</option>
                        </select>
                        <p class="text-xs text-gray-400 mt-1" x-text="slideTypeHelp[question.slide_type] || ''"></p>
                    </div>

                    <!-- Question Text (for question types) -->
                    <div x-show="['question', 'question_text'].includes(question.slide_type)">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Question Text</label>
                        <textarea name="question_text" x-model="question.question_text" rows="2"
                            class="w-full rounded-lg border-gray-300 focus:border-brand-gold focus:ring-brand-gold"
                            placeholder="e.g. What is your primary health goal?"
                            :required="['question', 'question_text'].includes(question.slide_type)"></textarea>
                        <p class="text-xs text-gray-400 mt-1">The question displayed to the user. Keep it clear and conversational. Avoid jargon so all experience levels can understand.</p>
                    </div>

                    <!-- Question Type + Klaviyo (for choice questions) -->
                    <div class="grid grid-cols-2 gap-4" x-show="question.slide_type === 'question'">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Question Type</label>
                            <select name="question_type" x-model="question.question_type"
                                class="w-full rounded-lg border-gray-300 focus:border-brand-gold focus:ring-brand-gold">
                                <option value="single_choice">Single Choice</option>
                                <option value="multiple_choice">Multiple Choice</option>
                            </select>
                            <p class="text-xs text-gray-400 mt-1">Single = user picks one answer. Multiple = user can select several before continuing.</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Klaviyo Property</label>
                            <input type="text" name="klaviyo_property" x-model="question.klaviyo_property"
                                placeholder="e.g. pp_health_goal"
                                class="w-full rounded-lg border-gray-300 focus:border-brand-gold focus:ring-brand-gold">
                            <p class="text-xs text-gray-400 mt-1">Klaviyo profile property name to store the answer. Use snake_case with "pp_" prefix (e.g. pp_health_goal, pp_experience_level).</p>
                        </div>
                    </div>

                    <!-- Klaviyo for text questions -->
                    <div x-show="question.slide_type === 'question_text'">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Klaviyo Property</label>
                        <input type="text" name="klaviyo_property" x-model="question.klaviyo_property"
                            placeholder="e.g. pp_custom_answer"
                            class="w-full rounded-lg border-gray-300 focus:border-brand-gold focus:ring-brand-gold">
                        <p class="text-xs text-gray-400 mt-1">Klaviyo profile property to store this free-text answer. Use snake_case with "pp_" prefix.</p>
                    </div>

                    <!-- Options Editor (for choice questions) -->
                    <div x-show="question.slide_type === 'question' && (question.question_type === 'single_choice' || question.question_type === 'multiple_choice')">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Answer Options</label>
                        <p class="text-xs text-gray-400 mb-2">Add at least 2 options. <strong>Label</strong> = text shown to the user. <strong>Value</strong> = internal key (auto-generated from label if blank). <strong>TOF/MOF/BOF</strong> = funnel segment scores (higher score = stronger signal for that segment).</p>
                        <template x-for="(option, index) in question.options" :key="index">
                            <div class="border rounded-lg p-3 mb-2 bg-gray-50/50">
                                <!-- Row 1: Label + Value + Delete -->
                                <div class="flex gap-2 items-end">
                                    <div class="flex-1">
                                        <label class="block text-[10px] font-medium text-gray-500 mb-0.5">Answer Text <span class="font-normal text-gray-400">— what the quiz taker sees</span></label>
                                        <input type="text" x-model="option.label" placeholder="e.g. Weight Loss" required
                                            class="w-full rounded-lg border-gray-300 text-sm">
                                    </div>
                                    <div class="w-28">
                                        <label class="block text-[10px] font-medium text-gray-500 mb-0.5">Value <span class="font-normal text-gray-400">— auto from label</span></label>
                                        <input type="text" x-model="option.value" placeholder="weight_loss"
                                            class="w-full rounded-lg border-gray-300 text-sm">
                                    </div>
                                    <button type="button" @click="removeOption(index)" class="text-red-500 p-2 mb-0.5" title="Remove this option">
                                        <svg aria-hidden="true" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                </div>
                                <!-- Row 2: Funnel Scores -->
                                <div class="flex gap-2 mt-2">
                                    <div class="w-20">
                                        <label class="block text-[10px] font-medium text-gray-500 mb-0.5">TOF <span class="font-normal text-gray-400">— just learning</span></label>
                                        <input type="number" x-model.number="option.score_tof" placeholder="0"
                                            class="w-full rounded-lg border-gray-300 text-sm">
                                    </div>
                                    <div class="w-20">
                                        <label class="block text-[10px] font-medium text-gray-500 mb-0.5">MOF <span class="font-normal text-gray-400">— researching</span></label>
                                        <input type="number" x-model.number="option.score_mof" placeholder="0"
                                            class="w-full rounded-lg border-gray-300 text-sm">
                                    </div>
                                    <div class="w-20">
                                        <label class="block text-[10px] font-medium text-gray-500 mb-0.5">BOF <span class="font-normal text-gray-400">— ready to buy</span></label>
                                        <input type="number" x-model.number="option.score_bof" placeholder="0"
                                            class="w-full rounded-lg border-gray-300 text-sm">
                                    </div>
                                </div>
                                <!-- Row 3: Skip-to -->
                                <div class="mt-2 flex items-center gap-2">
                                    <label class="text-xs text-gray-500 whitespace-nowrap">Skip to:</label>
                                    <select x-model="option.skip_to_question"
                                        x-init="$nextTick(() => { if(option.skip_to_question) $el.value = option.skip_to_question })"
                                        class="flex-1 rounded border-gray-300 text-xs py-1">
                                        <option value="">Continue normally</option>
                                        <template x-for="slide in allSlides" :key="slide.id">
                                            <option :value="slide.id" x-text="slide.label"></option>
                                        </template>
                                    </select>
                                </div>
                                <p class="text-[10px] text-gray-400 mt-0.5" x-show="option.skip_to_question">Jumps to selected slide, skipping everything in between.</p>
                                <!-- Row 4: Tags (searchable multi-select) -->
                                <div class="mt-2" x-data="{ tagSearch: '', tagOpen: false }">
                                    <label class="block text-[10px] font-medium text-gray-500 mb-0.5">Tags <span class="font-normal text-gray-400">— Klaviyo segmentation labels for this answer</span></label>
                                    <!-- Selected tags as chips -->
                                    <div class="flex flex-wrap gap-1 mb-1" x-show="option.tags && option.tags.length > 0">
                                        <template x-for="(tag, ti) in (option.tags || [])" :key="ti">
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-indigo-100 text-indigo-700 rounded text-xs">
                                                <span x-text="tag"></span>
                                                <button type="button" @click="option.tags.splice(ti, 1)" class="hover:text-red-500">&times;</button>
                                            </span>
                                        </template>
                                    </div>
                                    <!-- Search input -->
                                    <div class="relative">
                                        <input type="text" x-model="tagSearch" @focus="tagOpen = true" @click.away="tagOpen = false"
                                            placeholder="Search tags..." class="w-full rounded border-gray-300 text-xs py-1">
                                        <!-- Dropdown -->
                                        <div x-show="tagOpen" x-cloak class="absolute z-20 w-full mt-1 bg-white border rounded shadow-lg max-h-40 overflow-y-auto">
                                            <template x-for="t in availableTags.filter(t => t.includes(tagSearch.toLowerCase()) && !(option.tags || []).includes(t))" :key="t">
                                                <button type="button"
                                                    @mousedown.prevent="if(!option.tags) option.tags = []; option.tags.push(t); tagSearch = '';"
                                                    class="block w-full text-left px-3 py-1.5 text-xs hover:bg-indigo-50 cursor-pointer"
                                                    x-text="t"></button>
                                            </template>
                                            <div x-show="availableTags.filter(t => t.includes(tagSearch.toLowerCase()) && !(option.tags || []).includes(t)).length === 0"
                                                class="px-3 py-2 text-xs text-gray-400">No matching tags</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>
                        <button type="button" @click="addOption" class="text-sm text-brand-gold hover:underline">+ Add Option</button>
                    </div>

                    <!-- Content Title (for intermission, email_capture, loading, bridge) -->
                    <div x-show="['intermission', 'email_capture', 'loading', 'bridge'].includes(question.slide_type)">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                        <input type="text" name="content_title" x-model="question.content_title"
                            :placeholder="titlePlaceholders[question.slide_type] || 'Slide title'"
                            class="w-full rounded-lg border-gray-300 focus:border-brand-gold focus:ring-brand-gold">
                        <p class="text-xs text-gray-400 mt-1" x-text="titleHelp[question.slide_type] || 'Main heading displayed at the top of this slide.'"></p>
                    </div>

                    <!-- Content Body (for intermission, loading, bridge) -->
                    <div x-show="['intermission', 'loading', 'bridge'].includes(question.slide_type)">
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            <span x-show="question.slide_type === 'loading'">Checklist Items (one per line)</span>
                            <span x-show="question.slide_type !== 'loading'">Content Body</span>
                        </label>
                        <textarea name="content_body" x-model="question.content_body" rows="4"
                            :placeholder="bodyPlaceholders[question.slide_type] || 'Slide content...'"
                            class="w-full rounded-lg border-gray-300 focus:border-brand-gold focus:ring-brand-gold"></textarea>
                        <p class="text-xs text-gray-400 mt-1" x-text="bodyHelp[question.slide_type] || ''"></p>
                    </div>

                    <!-- Content Body for email_capture (subtitle text) -->
                    <div x-show="question.slide_type === 'email_capture'">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Subtitle Text</label>
                        <textarea name="content_body" x-model="question.content_body" rows="2"
                            placeholder="We'll send your personalized peptide recommendation to your inbox..."
                            class="w-full rounded-lg border-gray-300 focus:border-brand-gold focus:ring-brand-gold"></textarea>
                        <p class="text-xs text-gray-400 mt-1">Persuasive text shown below the title to encourage email submission. Explain the value they'll receive (e.g. personalized results, exclusive content).</p>
                    </div>

                    <!-- Content Source (for intermission) -->
                    <div x-show="question.slide_type === 'intermission'">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Source Citation (optional)</label>
                        <input type="text" name="content_source" x-model="question.content_source"
                            placeholder="e.g. Journal of Clinical Endocrinology, 2024"
                            class="w-full rounded-lg border-gray-300 focus:border-brand-gold focus:ring-brand-gold">
                        <p class="text-xs text-gray-400 mt-1">Adds credibility. Cite the study, journal, or organization backing the claim shown in the content body. Shown in smaller text below the content.</p>
                    </div>

                    <!-- Auto-advance seconds (for loading) -->
                    <div x-show="question.slide_type === 'loading'">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Auto-advance after (seconds)</label>
                        <input type="number" name="auto_advance_seconds" x-model.number="question.auto_advance_seconds"
                            min="1" max="30" placeholder="5"
                            class="w-32 rounded-lg border-gray-300 focus:border-brand-gold focus:ring-brand-gold">
                        <p class="text-xs text-gray-400 mt-1">The loading screen will automatically move to the next slide after this many seconds. Recommended: 4-8 seconds. The checklist items animate in sequence during this time.</p>
                    </div>

                    <!-- CTA fields (for reveal/bridge slides) -->
                    <div x-show="['peptide_reveal', 'vendor_reveal', 'bridge'].includes(question.slide_type)" class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">CTA Button Text</label>
                            <input type="text" name="cta_text" x-model="question.cta_text"
                                placeholder="e.g. See My Results"
                                class="w-full rounded-lg border-gray-300 focus:border-brand-gold focus:ring-brand-gold">
                            <p class="text-xs text-gray-400 mt-1">Text on the call-to-action button. Use action-oriented language (e.g. "See My Results", "Book Consultation", "Compare Prices").</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">CTA URL</label>
                            <input type="text" name="cta_url" x-model="question.cta_url"
                                placeholder="https://..."
                                class="w-full rounded-lg border-gray-300 focus:border-brand-gold focus:ring-brand-gold">
                            <p class="text-xs text-gray-400 mt-1">Destination URL when the CTA button is clicked. Use full URL including https://. Leave empty if the CTA just advances to the next slide.</p>
                        </div>
                    </div>

                    <!-- Dynamic Content (for intermission slides) -->
                    <div x-show="question.slide_type === 'intermission'" class="border-t pt-4 mt-4">
                        <h4 class="text-sm font-semibold text-gray-700 mb-2">Dynamic Content (optional)</h4>
                        <p class="text-xs text-gray-400 mb-3">Make this slide show different content based on a previous answer. Leave empty to always show the same content.</p>

                        <div class="mb-3">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Answer Key</label>
                            <input type="text" name="dynamic_content_key" x-model="question.dynamic_content_key"
                                placeholder="e.g. health_goal"
                                class="w-full rounded-lg border-gray-300 focus:border-brand-gold focus:ring-brand-gold">
                            <p class="text-xs text-gray-400 mt-1">The <code>klaviyo_property</code> name of the question whose answer determines which content to show. Must match exactly (e.g. "health_goal").</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Content Variants</label>
                            <template x-for="(variant, vi) in question.dynamic_variants" :key="vi">
                                <div class="border rounded-lg p-3 mb-2 bg-gray-50/50">
                                    <div class="flex gap-2 items-center mb-2">
                                        <input type="text" x-model="variant.key" placeholder="Answer value (e.g. fat_loss)"
                                            class="w-40 rounded-lg border-gray-300 text-sm">
                                        <span class="text-xs text-gray-400 flex-1">or use <code>_default</code> for fallback</span>
                                        <button type="button" @click="question.dynamic_variants.splice(vi, 1)" class="text-red-500 p-1">
                                            <svg aria-hidden="true" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
                                        </button>
                                    </div>
                                    <input type="text" x-model="variant.title" placeholder="Title for this variant"
                                        class="w-full rounded-lg border-gray-300 text-sm mb-2">
                                    <textarea x-model="variant.body" placeholder="Body content for this variant" rows="2"
                                        class="w-full rounded-lg border-gray-300 text-sm"></textarea>
                                </div>
                            </template>
                            <button type="button" @click="question.dynamic_variants.push({ key: '', title: '', body: '' })"
                                class="text-sm text-brand-gold hover:underline">+ Add Variant</button>
                            <p class="text-xs text-gray-400 mt-2">Tip: Use <code>@{{health_goal}}</code> or <code>@{{peptide_name}}</code> tokens in titles/bodies &mdash; they'll be replaced with the user's actual answers at runtime.</p>
                        </div>
                    </div>

                    <!-- Show Conditions (Branching) -->
                    <div class="border-t pt-4 mt-4">
                        <div class="flex items-center justify-between mb-2">
                            <label class="block text-sm font-medium text-gray-700">Show Conditions (optional)</label>
                            <button type="button" @click="addCondition"
                                class="text-xs text-brand-gold hover:underline">+ Add Condition</button>
                        </div>
                        <p class="text-xs text-gray-400 mb-3">Control when this slide appears based on previous answers. Leave empty to always show this slide. Use AND to require all conditions, OR to require any one.</p>

                        <template x-if="question.show_conditions.conditions.length > 0">
                            <div>
                                <!-- AND/OR toggle -->
                                <div class="flex items-center gap-2 mb-3" x-show="question.show_conditions.conditions.length > 1">
                                    <span class="text-xs text-gray-500">Match:</span>
                                    <select x-model="question.show_conditions.type"
                                        class="rounded border-gray-300 text-xs py-1">
                                        <option value="and">ALL conditions (AND)</option>
                                        <option value="or">ANY condition (OR)</option>
                                    </select>
                                    <span class="text-xs text-gray-400" x-text="question.show_conditions.type === 'and' ? 'All conditions must be true to show this slide.' : 'At least one condition must be true to show this slide.'"></span>
                                </div>

                                <template x-for="(cond, ci) in question.show_conditions.conditions" :key="ci">
                                    <div class="flex gap-2 mb-2 items-center">
                                        <span class="text-xs text-gray-400 w-8 text-right" x-text="ci > 0 ? question.show_conditions.type.toUpperCase() : 'IF'"></span>
                                        <!-- Question selector -->
                                        <select x-model="cond.question_id" @change="cond.option_value = ''"
                                            x-init="$nextTick(() => { if(cond.question_id) $el.value = cond.question_id })"
                                            class="flex-1 rounded border-gray-300 text-xs py-1"
                                            title="Select a previous question slide to check the user's answer against">
                                            <option value="">Select question...</option>
                                            <template x-for="slide in allSlides.filter(s => s.slide_type === 'question')" :key="slide.id">
                                                <option :value="slide.id" x-text="slide.label"></option>
                                            </template>
                                        </select>
                                        <span class="text-xs text-gray-400">=</span>
                                        <!-- Answer selector -->
                                        <select x-model="cond.option_value"
                                            x-init="$nextTick(() => { if(cond.option_value) $el.value = cond.option_value })"
                                            class="flex-1 rounded border-gray-300 text-xs py-1"
                                            title="The specific answer that must have been selected for this condition to be met">
                                            <option value="">Select answer...</option>
                                            <template x-for="opt in getOptionsForQuestion(cond.question_id)" :key="opt.value">
                                                <option :value="opt.value" x-text="opt.label"></option>
                                            </template>
                                        </select>
                                        <button type="button" @click="removeCondition(ci)" class="text-red-400 hover:text-red-600" title="Remove this condition">
                                            <svg aria-hidden="true" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
                                        </button>
                                    </div>
                                </template>
                            </div>
                        </template>
                    </div>
                </div>

                <div class="p-6 border-t bg-gray-50 flex justify-end gap-3">
                    <button type="button" @click="closeModal" class="btn btn-secondary">Cancel</button>
                    <button type="submit" class="btn btn-primary" x-text="isEdit ? 'Update' : 'Add Slide'"></button>
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
        allSlides: {!! $slidesJson !!},
        availableTags: {!! $availableTagsJson !!},
        question: {
            slide_type: 'question',
            question_text: '',
            question_type: 'single_choice',
            klaviyo_property: '',
            options: [{ label: '', value: '', score_tof: 0, score_mof: 0, score_bof: 0, skip_to_question: '', tags: [] }],
            content_title: '',
            content_body: '',
            content_source: '',
            auto_advance_seconds: 5,
            cta_text: '',
            cta_url: '',
            show_conditions: { type: 'and', conditions: [] },
        },
        slideTypeHelp: {
            question: 'Multiple-choice question with scoring. Each answer adds points to TOF (awareness), MOF (consideration), or BOF (purchase-ready) segments to classify the user.',
            question_text: 'Free-text input question. The user types their answer instead of choosing from options. Useful for open-ended questions like "What is your biggest challenge?"',
            intermission: 'Informational slide shown between questions. Use it to share a relevant stat, testimonial, or educational fact that builds trust before the next question.',
            loading: 'Animated checklist screen that simulates processing. Each line appears sequentially, then auto-advances. Creates anticipation before results.',
            email_capture: 'Email collection form. Users can enter their email or skip. Collected emails are stored as subscribers and synced to Klaviyo if configured.',
            peptide_reveal: 'Displays the personalized peptide recommendation based on the user\'s health goal and experience level. Powered by the Results Bank.',
            vendor_reveal: 'Shows the recommended vendor (Telehealth or Research) with product details, pricing, and a CTA to the vendor\'s site.',
            bridge: 'Final slide with next-steps content and a CTA button. Use it to guide the user to the price comparison tool or vendor page.',
        },
        titlePlaceholders: {
            intermission: 'e.g. Did You Know?',
            email_capture: 'e.g. Get Your Personalized Results',
            loading: 'e.g. Analyzing Your Profile',
            bridge: 'e.g. What Happens Next',
        },
        titleHelp: {
            intermission: 'Bold heading shown at the top of the intermission. Keep it attention-grabbing (e.g. "Did You Know?", "The Science Behind It").',
            email_capture: 'Heading shown above the email form. Make it value-focused (e.g. "Get Your Personalized Results", "Unlock Your Recommendation").',
            loading: 'Heading shown while the checklist animates. Use action-oriented text (e.g. "Analyzing Your Profile", "Finding Your Match").',
            bridge: 'Heading for the final CTA slide. Summarize next steps (e.g. "What Happens Next", "Your Personalized Plan").',
        },
        bodyPlaceholders: {
            intermission: 'e.g. Research shows peptides can improve recovery by up to 40%...',
            loading: 'Checking your health profile...\nMatching peptide options...\nCalculating optimal dosage...',
            bridge: 'e.g. 1. Review your personalized recommendation\n2. Compare trusted vendors\n3. Start your peptide journey',
        },
        bodyHelp: {
            intermission: 'The main informational content. Can include stats, testimonials, or educational facts. Keep it concise (1-3 sentences) for maximum impact.',
            loading: 'One checklist item per line. Each line animates in sequence during the countdown. Use 3-5 items that suggest thorough analysis (e.g. "Analyzing health profile...", "Matching peptides...").',
            bridge: 'Numbered steps or bullet points explaining what the user should do next. Builds confidence before the final CTA click.',
        },
        addOption() {
            this.question.options.push({ label: '', value: '', score_tof: 0, score_mof: 0, score_bof: 0, skip_to_question: '', tags: [] });
        },
        removeOption(index) {
            this.question.options.splice(index, 1);
        },
        addCondition() {
            this.question.show_conditions.conditions.push({ question_id: '', option_value: '' });
        },
        removeCondition(index) {
            this.question.show_conditions.conditions.splice(index, 1);
        },
        getOptionsForQuestion(questionId) {
            if (!questionId) return [];
            const slide = this.allSlides.find(s => s.id == questionId);
            return slide ? slide.options : [];
        },
        closeModal() {
            document.getElementById('question-modal').classList.add('hidden');
        },
        resetForm() {
            this.question = {
                slide_type: 'question',
                question_text: '',
                question_type: 'single_choice',
                klaviyo_property: '',
                options: [{ label: '', value: '', score_tof: 0, score_mof: 0, score_bof: 0, skip_to_question: '', tags: [] }],
                content_title: '',
                content_body: '',
                content_source: '',
                auto_advance_seconds: 5,
                cta_text: '',
                cta_url: '',
                dynamic_content_key: '',
                dynamic_variants: [],
                show_conditions: { type: 'and', conditions: [] },
            };
        },
        async submitForm(e) {
            const formData = new FormData();
            formData.append('_token', '{{ csrf_token() }}');
            formData.append('slide_type', this.question.slide_type);
            formData.append('question_text', this.question.question_text || this.question.content_title || 'Slide');
            formData.append('question_type', this.question.question_type);
            formData.append('klaviyo_property', this.question.klaviyo_property || '');
            formData.append('content_title', this.question.content_title || '');
            formData.append('content_body', this.question.content_body || '');
            formData.append('content_source', this.question.content_source || '');
            formData.append('auto_advance_seconds', this.question.auto_advance_seconds || '');
            formData.append('cta_text', this.question.cta_text || '');
            formData.append('cta_url', this.question.cta_url || '');

            // Dynamic content fields (intermission slides)
            if (this.question.dynamic_content_key) {
                formData.append('dynamic_content_key', this.question.dynamic_content_key);
            }
            if (this.question.dynamic_variants && this.question.dynamic_variants.length > 0) {
                const validVariants = this.question.dynamic_variants.filter(v => v.key);
                validVariants.forEach((v, i) => {
                    formData.append(`dynamic_variants[${i}][key]`, v.key);
                    formData.append(`dynamic_variants[${i}][title]`, v.title || '');
                    formData.append(`dynamic_variants[${i}][body]`, v.body || '');
                });
            }

            // Only send options for choice questions
            if (this.question.slide_type === 'question') {
                this.question.options.forEach((opt, i) => {
                    formData.append(`options[${i}][label]`, opt.label);
                    formData.append(`options[${i}][value]`, opt.value || opt.label.toLowerCase().replace(/\s+/g, '_'));
                    formData.append(`options[${i}][score_tof]`, opt.score_tof || 0);
                    formData.append(`options[${i}][score_mof]`, opt.score_mof || 0);
                    formData.append(`options[${i}][score_bof]`, opt.score_bof || 0);
                    if (opt.skip_to_question) {
                        formData.append(`options[${i}][skip_to_question]`, opt.skip_to_question);
                    }
                    if (opt.tags && opt.tags.length > 0) {
                        opt.tags.forEach((tag, ti) => {
                            formData.append(`options[${i}][tags][${ti}]`, tag);
                        });
                    }
                });
            }

            // Show conditions
            const conds = this.question.show_conditions;
            const validConds = (conds.conditions || []).filter(c => c.question_id && c.option_value);
            if (validConds.length > 0) {
                formData.append('show_conditions_type', conds.type || 'and');
                validConds.forEach((c, i) => {
                    formData.append(`show_conditions_question_id[${i}]`, c.question_id);
                    formData.append(`show_conditions_option_value[${i}]`, c.option_value);
                });
            }

            if (this.isEdit) formData.append('_method', 'PUT');

            await fetch(this.formAction, { method: 'POST', body: formData });
            window.location.reload();
        }
    }
}

function showAddQuestion() {
    const modal = document.getElementById('question-modal');
    const data = Alpine.$data(modal);
    data.isEdit = false;
    data.formAction = '{{ route("admin.quizzes.questions.store", $quiz) }}';
    data.resetForm();
    modal.classList.remove('hidden');
}

function editQuestion(id, questionData) {
    const modal = document.getElementById('question-modal');
    const data = Alpine.$data(modal);
    data.isEdit = true;
    data.formAction = '{{ url("admin/quizzes/" . $quiz->id . "/questions") }}/' + id;

    // Parse show_conditions from DB format
    const showConds = questionData.show_conditions || {};
    const parsedConds = {
        type: showConds.type || 'and',
        conditions: (showConds.conditions || []).map(c => ({
            question_id: c.question_id ? String(c.question_id) : '',
            option_value: c.option_value || '',
        })),
    };

    // Parse dynamic_content_map back into variants array for the editor
    const dynamicMap = questionData.dynamic_content_map || {};
    const dynamicVariants = Object.entries(dynamicMap).map(([key, val]) => ({
        key: key,
        title: val.title || '',
        body: val.body || '',
    }));

    // Populate form with existing data
    data.question = {
        slide_type: questionData.slide_type || 'question',
        question_text: questionData.question_text || '',
        question_type: questionData.question_type || 'single_choice',
        klaviyo_property: questionData.klaviyo_property || '',
        options: questionData.options && questionData.options.length > 0
            ? questionData.options.map(o => ({
                label: o.label || o.text || '',
                value: o.value || '',
                score_tof: o.score_tof || 0,
                score_mof: o.score_mof || 0,
                score_bof: o.score_bof || 0,
                skip_to_question: o.skip_to_question ? String(o.skip_to_question) : '',
                tags: o.tags || [],
            }))
            : [{ label: '', value: '', score_tof: 0, score_mof: 0, score_bof: 0, skip_to_question: '', tags: [] }],
        content_title: questionData.content_title || '',
        content_body: questionData.content_body || '',
        content_source: questionData.content_source || '',
        auto_advance_seconds: questionData.auto_advance_seconds || 5,
        cta_text: questionData.cta_text || '',
        cta_url: questionData.cta_url || '',
        dynamic_content_key: questionData.dynamic_content_key || '',
        dynamic_variants: dynamicVariants,
        show_conditions: parsedConds,
    };

    modal.classList.remove('hidden');
}
</script>
