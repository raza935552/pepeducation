<!-- Question / Slide Modal — Redesigned for Marketers -->
@php
    $availableTagsJson = json_encode(\App\Models\QuizQuestion::OPTION_TAGS);
    $allSlides = $quiz->questions->sortBy('order')->values();
    $slidesJson = $allSlides->map(fn ($q) => [
        'id' => (string) $q->id,
        'order' => $q->order,
        'label' => '#' . $q->order . ' — ' . Str::limit($q->question_text ?: $q->content_title ?: \App\Models\QuizQuestion::getSlideTypeLabel($q->slide_type ?? 'question'), 40),
        'slide_type' => $q->slide_type ?? 'question',
        'klaviyo_property' => $q->klaviyo_property,
        'options' => collect($q->options ?? [])->map(fn ($o) => [
            'value' => $o['value'] ?? $o['id'] ?? '',
            'label' => $o['text'] ?? $o['label'] ?? $o['value'] ?? '',
        ])->values()->toArray(),
    ])->toJson();
@endphp

<div id="question-modal" class="fixed inset-0 bg-black/50 z-50 hidden" x-data="questionModal()">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto ring-1 ring-gray-200">
            <form :action="formAction" method="POST" @submit.prevent="submitForm" novalidate>
                @csrf
                <div x-show="isEdit"><input type="hidden" name="_method" value="PUT"></div>

                {{-- Header --}}
                <div class="px-6 py-5 border-b border-gray-100 bg-gray-50/50">
                    <h3 class="text-lg font-semibold text-gray-900" x-text="isEdit ? 'Edit Slide' : 'Add New Slide'"></h3>
                    <p class="text-sm text-gray-500 mt-0.5">Configure what the quiz taker sees on this slide.</p>
                </div>

                <div class="p-6 space-y-6">

                    {{-- ═══════════ SLIDE TYPE SELECTOR (card grid) ═══════════ --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Slide Type</label>
                        <div class="grid grid-cols-4 gap-2">
                            <template x-for="st in slideTypes" :key="st.value">
                                <button type="button"
                                    @click="question.slide_type = st.value"
                                    :class="question.slide_type === st.value
                                        ? 'border-brand-gold bg-brand-gold/5 ring-1 ring-brand-gold/30'
                                        : 'border-gray-200 hover:border-gray-300 hover:bg-gray-50'"
                                    class="relative flex flex-col items-center gap-1 px-2 py-3 rounded-lg border text-center transition-all cursor-pointer group">
                                    <span class="text-lg" x-html="st.icon"></span>
                                    <span class="text-[11px] font-semibold leading-tight"
                                        :class="question.slide_type === st.value ? 'text-brand-gold' : 'text-gray-700'"
                                        x-text="st.label"></span>
                                    <span class="text-[9px] text-gray-400 leading-tight hidden sm:block" x-text="st.sub"></span>
                                    {{-- Selected indicator --}}
                                    <div x-show="question.slide_type === st.value"
                                        class="absolute -top-1 -right-1 w-4 h-4 rounded-full bg-brand-gold flex items-center justify-center">
                                        <svg class="w-2.5 h-2.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                    </div>
                                </button>
                            </template>
                        </div>
                        <p class="text-xs text-gray-400 mt-2" x-text="slideTypeHelp[question.slide_type] || ''"></p>
                    </div>

                    {{-- ═══════════ CONTENT SECTION ═══════════ --}}
                    <div>
                        <div class="flex items-center gap-2 mb-3">
                            <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Content</span>
                            <div class="flex-1 h-px bg-gray-200"></div>
                        </div>

                        {{-- Question Text (question types) --}}
                        <div x-show="['question', 'question_text'].includes(question.slide_type)" class="space-y-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Question Text</label>
                                <textarea name="question_text" x-model="question.question_text" rows="2"
                                    class="w-full rounded-lg border-gray-300 focus:border-brand-gold focus:ring-brand-gold"
                                    placeholder="e.g. What is your primary health goal?"
                                    :required="['question', 'question_text'].includes(question.slide_type)"></textarea>
                                <p class="text-xs text-gray-400 mt-1">The question displayed to the user. Keep it clear and conversational.</p>
                            </div>

                            {{-- Question Subtext (#1) --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Subtext <span class="font-normal text-gray-400">(optional)</span></label>
                                <textarea x-model="question.question_subtext" rows="1"
                                    class="w-full rounded-lg border-gray-300 focus:border-brand-gold focus:ring-brand-gold text-sm"
                                    placeholder="e.g. Select the one that best describes you"></textarea>
                                <p class="text-xs text-gray-400 mt-1">Helper text shown below the question.</p>
                            </div>

                            {{-- Placeholder Text (#6) — text input slides only --}}
                            <div x-show="question.slide_type === 'question_text'" x-cloak>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Placeholder Text <span class="font-normal text-gray-400">(optional)</span></label>
                                <input type="text" x-model="question.settings.placeholder"
                                    class="w-full rounded-lg border-gray-300 focus:border-brand-gold focus:ring-brand-gold text-sm"
                                    placeholder="Type your answer...">
                                <p class="text-xs text-gray-400 mt-1">Placeholder shown inside the text input before the user types.</p>
                            </div>

                            {{-- Answer Style (only for choice questions) --}}
                            <div x-show="question.slide_type === 'question'">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Answer Style</label>
                                <div class="flex gap-2">
                                    <button type="button" @click="question.question_type = 'single_choice'"
                                        :class="question.question_type === 'single_choice' ? 'border-brand-gold bg-brand-gold/5 text-brand-gold' : 'border-gray-200 text-gray-600 hover:border-gray-300'"
                                        class="flex-1 px-3 py-2 rounded-lg border text-sm font-medium transition-colors">
                                        Pick One
                                    </button>
                                    <button type="button" @click="question.question_type = 'multiple_choice'"
                                        :class="question.question_type === 'multiple_choice' ? 'border-brand-gold bg-brand-gold/5 text-brand-gold' : 'border-gray-200 text-gray-600 hover:border-gray-300'"
                                        class="flex-1 px-3 py-2 rounded-lg border text-sm font-medium transition-colors">
                                        Pick Multiple
                                    </button>
                                </div>
                                <input type="hidden" name="question_type" :value="question.question_type">
                            </div>
                        </div>

                        {{-- Title (content slides) --}}
                        <div x-show="['intermission', 'email_capture', 'loading', 'bridge'].includes(question.slide_type)" class="space-y-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Headline</label>
                                <input type="text" name="content_title" x-model="question.content_title"
                                    :placeholder="titlePlaceholders[question.slide_type] || 'Slide headline'"
                                    class="w-full rounded-lg border-gray-300 focus:border-brand-gold focus:ring-brand-gold">
                                <p class="text-xs text-gray-400 mt-1" x-text="titleHelp[question.slide_type] || 'Main heading shown at the top of this slide.'"></p>
                            </div>

                            {{-- Body (intermission, loading, bridge) --}}
                            <div x-show="['intermission', 'loading', 'bridge'].includes(question.slide_type)">
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    <span x-show="question.slide_type === 'loading'">Checklist Items (one per line)</span>
                                    <span x-show="question.slide_type !== 'loading'">Body Text</span>
                                </label>
                                <textarea name="content_body" x-model="question.content_body" rows="3"
                                    :placeholder="bodyPlaceholders[question.slide_type] || 'Slide content...'"
                                    class="w-full rounded-lg border-gray-300 focus:border-brand-gold focus:ring-brand-gold"></textarea>
                                <p class="text-xs text-gray-400 mt-1" x-text="bodyHelp[question.slide_type] || ''"></p>
                            </div>

                            {{-- Subtitle for email_capture --}}
                            <div x-show="question.slide_type === 'email_capture'">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Subtitle Text</label>
                                <textarea name="content_body" x-model="question.content_body" rows="2"
                                    placeholder="We'll send your personalized peptide recommendation to your inbox..."
                                    class="w-full rounded-lg border-gray-300 focus:border-brand-gold focus:ring-brand-gold"></textarea>
                                <p class="text-xs text-gray-400 mt-1">Persuasive text below the headline to encourage email submission.</p>
                            </div>

                            {{-- Source Citation (intermission only) --}}
                            <div x-show="question.slide_type === 'intermission'">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Source Citation <span class="font-normal text-gray-400">(optional)</span></label>
                                <input type="text" name="content_source" x-model="question.content_source"
                                    placeholder="e.g. Journal of Clinical Endocrinology, 2024"
                                    class="w-full rounded-lg border-gray-300 focus:border-brand-gold focus:ring-brand-gold">
                                <p class="text-xs text-gray-400 mt-1">Shown in small text below the content for credibility.</p>
                            </div>
                        </div>

                        {{-- Peptide/Vendor reveal have no editable content fields --}}
                        <div x-show="['peptide_reveal', 'vendor_reveal'].includes(question.slide_type)">
                            <div class="rounded-lg bg-gray-50 border border-gray-200 px-4 py-3">
                                <p class="text-sm text-gray-600">
                                    <span x-show="question.slide_type === 'peptide_reveal'">This slide automatically shows the personalized peptide recommendation based on the user's answers. Content is powered by the Results Bank.</span>
                                    <span x-show="question.slide_type === 'vendor_reveal'">This slide automatically shows the recommended vendor with product details and pricing.</span>
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- ═══════════ ANSWERS SECTION (question type only) ═══════════ --}}
                    <div x-show="question.slide_type === 'question'" x-cloak>
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center gap-2">
                                <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Answers</span>
                                <div class="flex-1 h-px bg-gray-200 w-8"></div>
                            </div>
                            <span class="text-xs text-gray-400" x-text="question.options.length + ' option' + (question.options.length !== 1 ? 's' : '')"></span>
                        </div>

                        <div class="space-y-2">
                            <template x-for="(option, index) in question.options" :key="index">
                                <div class="border rounded-lg bg-white overflow-hidden transition-shadow"
                                    :class="option._expanded ? 'shadow-sm border-gray-300' : 'border-gray-200'"
                                    x-data="{ }">

                                    {{-- Compact row: label + subtext + scoring indicator + expand + delete --}}
                                    <div class="flex items-start gap-2 px-3 py-2">
                                        <span class="text-xs font-bold text-gray-300 w-5 text-center flex-shrink-0 mt-2" x-text="index + 1"></span>
                                        <div class="flex-1 min-w-0 space-y-1">
                                            <input type="text" x-model="option.label" placeholder="Answer text..."
                                                class="w-full rounded border-gray-200 text-sm py-1.5 focus:border-brand-gold focus:ring-brand-gold"
                                                required>
                                            {{-- Answer Subtext (#2) --}}
                                            <input type="text" x-model="option.subtext" placeholder="Description (optional)"
                                                class="w-full rounded border-gray-200 text-xs py-1 text-gray-500 focus:border-brand-gold focus:ring-brand-gold">
                                        </div>

                                        {{-- Scoring indicator (shows if any score > 0) --}}
                                        <template x-if="(option.score_tof || 0) + (option.score_mof || 0) + (option.score_bof || 0) > 0">
                                            <span class="flex-shrink-0 text-[9px] px-1.5 py-0.5 rounded-full bg-amber-50 text-amber-600 border border-amber-200 font-medium whitespace-nowrap"
                                                x-text="[
                                                    option.score_tof > 0 ? 'T' + option.score_tof : '',
                                                    option.score_mof > 0 ? 'M' + option.score_mof : '',
                                                    option.score_bof > 0 ? 'B' + option.score_bof : ''
                                                ].filter(Boolean).join(' ')">
                                            </span>
                                        </template>

                                        {{-- Expand toggle --}}
                                        <button type="button" @click="option._expanded = !option._expanded"
                                            class="p-1 rounded text-gray-400 hover:text-gray-600 hover:bg-gray-100 flex-shrink-0"
                                            :title="option._expanded ? 'Collapse scoring' : 'Expand scoring & routing'">
                                            <svg :class="option._expanded ? 'rotate-180' : ''" class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                        </button>

                                        {{-- Delete --}}
                                        <button type="button" @click="removeOption(index)"
                                            class="p-1 rounded text-gray-300 hover:text-red-500 hover:bg-red-50 flex-shrink-0" title="Remove">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                        </button>
                                    </div>

                                    {{-- Expanded panel: scoring + skip + tags --}}
                                    <div x-show="option._expanded" x-collapse class="border-t border-gray-100 bg-gray-50/70 px-3 py-3 space-y-3">

                                        {{-- Internal value (power users) --}}
                                        <div>
                                            <label class="block text-[10px] font-medium text-gray-500 mb-0.5">Internal Value <span class="font-normal text-gray-400">auto-generated if blank</span></label>
                                            <input type="text" x-model="option.value" placeholder="auto from label"
                                                class="w-40 rounded border-gray-200 text-xs py-1">
                                        </div>

                                        {{-- Funnel Scoring --}}
                                        <div>
                                            <label class="block text-[10px] font-medium text-gray-500 mb-1.5">Funnel Scoring <span class="font-normal text-gray-400">which buyer stage does this answer suggest?</span></label>
                                            <div class="flex gap-2">
                                                <div class="flex-1">
                                                    <div class="flex items-center gap-1 mb-0.5">
                                                        <span class="w-2 h-2 rounded-full bg-blue-400"></span>
                                                        <span class="text-[10px] text-gray-500">Beginner (TOF)</span>
                                                    </div>
                                                    <input type="number" x-model.number="option.score_tof" placeholder="0" min="0"
                                                        class="w-full rounded border-gray-200 text-sm py-1">
                                                </div>
                                                <div class="flex-1">
                                                    <div class="flex items-center gap-1 mb-0.5">
                                                        <span class="w-2 h-2 rounded-full bg-yellow-400"></span>
                                                        <span class="text-[10px] text-gray-500">Researching (MOF)</span>
                                                    </div>
                                                    <input type="number" x-model.number="option.score_mof" placeholder="0" min="0"
                                                        class="w-full rounded border-gray-200 text-sm py-1">
                                                </div>
                                                <div class="flex-1">
                                                    <div class="flex items-center gap-1 mb-0.5">
                                                        <span class="w-2 h-2 rounded-full bg-green-400"></span>
                                                        <span class="text-[10px] text-gray-500">Ready to Buy (BOF)</span>
                                                    </div>
                                                    <input type="number" x-model.number="option.score_bof" placeholder="0" min="0"
                                                        class="w-full rounded border-gray-200 text-sm py-1">
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Jump to slide --}}
                                        <div>
                                            <label class="block text-[10px] font-medium text-gray-500 mb-0.5">Jump to Slide <span class="font-normal text-gray-400">skip ahead when this answer is picked</span></label>
                                            <select x-model="option.skip_to_question"
                                                x-init="$nextTick(() => { if(option.skip_to_question) $el.value = option.skip_to_question })"
                                                class="w-full rounded border-gray-200 text-xs py-1.5">
                                                <option value="">Continue normally</option>
                                                <template x-for="slide in allSlides" :key="slide.id">
                                                    <option :value="slide.id" x-text="slide.label"></option>
                                                </template>
                                            </select>
                                        </div>

                                        {{-- Tags --}}
                                        <div x-data="{ tagSearch: '', tagOpen: false }">
                                            <label class="block text-[10px] font-medium text-gray-500 mb-0.5">Tags <span class="font-normal text-gray-400">Klaviyo segmentation labels</span></label>
                                            <div class="flex flex-wrap gap-1 mb-1" x-show="option.tags && option.tags.length > 0">
                                                <template x-for="(tag, ti) in (option.tags || [])" :key="ti">
                                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-indigo-100 text-indigo-700 rounded text-[10px]">
                                                        <span x-text="tag"></span>
                                                        <button type="button" @click="option.tags.splice(ti, 1)" class="hover:text-red-500">&times;</button>
                                                    </span>
                                                </template>
                                            </div>
                                            <div class="relative">
                                                <input type="text" x-model="tagSearch" @focus="tagOpen = true" @click.away="tagOpen = false"
                                                    placeholder="Search tags..." class="w-full rounded border-gray-200 text-xs py-1">
                                                <div x-show="tagOpen" x-cloak class="absolute z-20 w-full mt-1 bg-white border rounded shadow-lg max-h-32 overflow-y-auto">
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
                                </div>
                            </template>
                        </div>
                        <button type="button" @click="addOption" class="mt-2 text-sm text-brand-gold hover:underline font-medium">+ Add Answer</button>
                    </div>

                    {{-- ═══════════ BUTTON SECTION (CTA slides) ═══════════ --}}
                    <div x-show="['peptide_reveal', 'vendor_reveal', 'bridge', 'intermission'].includes(question.slide_type)" x-cloak>
                        <div class="flex items-center gap-2 mb-3">
                            <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Button</span>
                            <div class="flex-1 h-px bg-gray-200"></div>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Button Text</label>
                                <input type="text" name="cta_text" x-model="question.cta_text"
                                    placeholder="e.g. See My Results"
                                    class="w-full rounded-lg border-gray-300 focus:border-brand-gold focus:ring-brand-gold">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Button Link <span class="font-normal text-gray-400">(optional)</span></label>
                                <input type="text" name="cta_url" x-model="question.cta_url"
                                    placeholder="https://..."
                                    class="w-full rounded-lg border-gray-300 focus:border-brand-gold focus:ring-brand-gold">
                            </div>
                        </div>
                        <p class="text-xs text-gray-400 mt-1">Leave the link empty if the button just advances to the next slide.</p>
                    </div>

                    {{-- ═══════════ TIMING SECTION (loading only) ═══════════ --}}
                    <div x-show="question.slide_type === 'loading'" x-cloak>
                        <div class="flex items-center gap-2 mb-3">
                            <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Timing</span>
                            <div class="flex-1 h-px bg-gray-200"></div>
                        </div>
                        <div class="flex items-center gap-3">
                            <label class="text-sm font-medium text-gray-700">Auto-advance after</label>
                            <input type="number" name="auto_advance_seconds" x-model.number="question.auto_advance_seconds"
                                min="1" max="30" placeholder="5"
                                class="w-20 rounded-lg border-gray-300 focus:border-brand-gold focus:ring-brand-gold text-center">
                            <span class="text-sm text-gray-500">seconds</span>
                        </div>
                        <p class="text-xs text-gray-400 mt-1">Checklist items animate in sequence during this time. Recommended: 4-8 seconds.</p>
                    </div>

                    {{-- ═══════════ ADVANCED SETTINGS (collapsible) ═══════════ --}}
                    <div class="border-t border-gray-200 pt-4">
                        <button type="button" @click="advancedOpen = !advancedOpen"
                            class="flex items-center gap-2 w-full text-left group">
                            <svg :class="advancedOpen ? 'rotate-90' : ''" class="w-4 h-4 text-gray-400 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider group-hover:text-gray-700">Advanced Settings</span>
                            {{-- Indicator dots when advanced settings are configured --}}
                            <div class="flex items-center gap-1 ml-2">
                                <span x-show="question.show_conditions.conditions.length > 0"
                                    class="w-1.5 h-1.5 rounded-full bg-cyan-400" title="Has conditions"></span>
                                <span x-show="question.klaviyo_property"
                                    class="w-1.5 h-1.5 rounded-full bg-purple-400" title="Has Klaviyo sync"></span>
                                <span x-show="question.is_required === false"
                                    class="w-1.5 h-1.5 rounded-full bg-red-400" title="Not required"></span>
                                <span x-show="question.max_selections"
                                    class="w-1.5 h-1.5 rounded-full bg-blue-400" title="Has max selections"></span>
                                <span x-show="question.dynamic_content_key"
                                    class="w-1.5 h-1.5 rounded-full bg-orange-400" title="Has content variants"></span>
                            </div>
                        </button>

                        <div x-show="advancedOpen" x-collapse class="mt-4 space-y-5">

                            {{-- Required Toggle (#3) --}}
                            <div x-show="['question', 'question_text', 'email_capture'].includes(question.slide_type)">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <label class="text-sm font-medium text-gray-700">Required</label>
                                        <p class="text-xs text-gray-400">User must answer before proceeding.</p>
                                    </div>
                                    <button type="button" @click="question.is_required = !question.is_required"
                                        :class="question.is_required ? 'bg-brand-gold' : 'bg-gray-200'"
                                        class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors focus:outline-none focus:ring-2 focus:ring-brand-gold focus:ring-offset-2">
                                        <span :class="question.is_required ? 'translate-x-6' : 'translate-x-1'"
                                            class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform shadow-sm"></span>
                                    </button>
                                </div>
                            </div>

                            {{-- Max Selections (#4) --}}
                            <div x-show="question.question_type === 'multiple_choice' && question.slide_type === 'question'">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Max Selections <span class="font-normal text-gray-400">(optional)</span></label>
                                <input type="number" x-model.number="question.max_selections" min="1" max="20" placeholder="No limit"
                                    class="w-32 rounded-lg border-gray-300 focus:border-brand-gold focus:ring-brand-gold text-sm">
                                <p class="text-xs text-gray-400 mt-1">Limit how many options can be selected (e.g. "Pick up to 3").</p>
                            </div>

                            {{-- Show Conditions --}}
                            <div>
                                <div class="flex items-center justify-between mb-2">
                                    <label class="text-sm font-medium text-gray-700">Show this slide when...</label>
                                    <button type="button" @click="addCondition"
                                        class="text-xs text-brand-gold hover:underline font-medium">+ Add Rule</button>
                                </div>
                                <p class="text-xs text-gray-400 mb-3">Only show this slide if a previous answer matches. Leave empty to always show.</p>

                                <template x-if="question.show_conditions.conditions.length > 0">
                                    <div>
                                        <div class="flex items-center gap-2 mb-3" x-show="question.show_conditions.conditions.length > 1">
                                            <span class="text-xs text-gray-500">Require:</span>
                                            <select x-model="question.show_conditions.type" class="rounded border-gray-300 text-xs py-1">
                                                <option value="and">ALL rules match (AND)</option>
                                                <option value="or">ANY rule matches (OR)</option>
                                            </select>
                                        </div>

                                        <template x-for="(cond, ci) in question.show_conditions.conditions" :key="ci">
                                            <div class="flex gap-2 mb-2 items-center">
                                                <span class="text-xs font-medium w-8 text-right flex-shrink-0"
                                                    :class="ci > 0 ? 'text-gray-400' : 'text-cyan-600'"
                                                    x-text="ci > 0 ? question.show_conditions.type.toUpperCase() : 'IF'"></span>
                                                <select x-model="cond.question_id" @change="cond.option_value = ''"
                                                    x-init="$nextTick(() => { if(cond.question_id) $el.value = cond.question_id })"
                                                    class="flex-1 rounded border-gray-300 text-xs py-1">
                                                    <option value="">Select question...</option>
                                                    <template x-for="slide in allSlides.filter(s => s.slide_type === 'question')" :key="slide.id">
                                                        <option :value="slide.id" x-text="slide.label"></option>
                                                    </template>
                                                </select>
                                                <span class="text-xs text-gray-400">=</span>
                                                <select x-model="cond.option_value"
                                                    x-init="$nextTick(() => { if(cond.option_value) $el.value = cond.option_value })"
                                                    class="flex-1 rounded border-gray-300 text-xs py-1">
                                                    <option value="">Select answer...</option>
                                                    <template x-for="opt in getOptionsForQuestion(cond.question_id)" :key="opt.value">
                                                        <option :value="opt.value" x-text="opt.label"></option>
                                                    </template>
                                                </select>
                                                <button type="button" @click="removeCondition(ci)" class="text-red-400 hover:text-red-600 flex-shrink-0">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                                </button>
                                            </div>
                                        </template>
                                    </div>
                                </template>
                            </div>

                            {{-- Klaviyo Sync --}}
                            <div x-show="['question', 'question_text', 'email_capture'].includes(question.slide_type)">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Sync Answer to Profile</label>
                                <input type="text" name="klaviyo_property" x-model="question.klaviyo_property"
                                    placeholder="e.g. pp_health_goal"
                                    class="w-full rounded-lg border-gray-300 focus:border-brand-gold focus:ring-brand-gold">
                                <p class="text-xs text-gray-400 mt-1">Store the user's answer in their Klaviyo profile. Use snake_case with <code class="bg-gray-100 px-1 rounded">pp_</code> prefix.</p>
                            </div>

                            {{-- Dynamic Content Variants (intermission) --}}
                            <div x-show="question.slide_type === 'intermission'">
                                <div class="flex items-center justify-between mb-2">
                                    <label class="text-sm font-medium text-gray-700">Content Variants</label>
                                </div>
                                <p class="text-xs text-gray-400 mb-3">Show different content based on a previous answer. Leave empty to always show the same content.</p>

                                <div class="mb-3">
                                    <label class="block text-[10px] font-medium text-gray-500 mb-0.5">Based on answer to</label>
                                    <select x-model="question.dynamic_content_key"
                                        class="w-full rounded border-gray-200 text-sm py-1.5">
                                        <option value="">Select a synced property...</option>
                                        <template x-for="slide in allSlides.filter(s => s.klaviyo_property)" :key="slide.id">
                                            <option :value="slide.klaviyo_property" x-text="slide.klaviyo_property + ' (' + slide.label + ')'"></option>
                                        </template>
                                    </select>
                                    <p class="text-[10px] text-gray-400 mt-0.5">The Klaviyo property from a previous slide whose value determines which variant to show.</p>
                                </div>

                                <template x-for="(variant, vi) in question.dynamic_variants" :key="vi">
                                    <div class="border rounded-lg p-3 mb-2 bg-gray-50/50">
                                        <div class="flex gap-2 items-center mb-2">
                                            <input type="text" x-model="variant.key" placeholder="Answer value (e.g. fat_loss)"
                                                class="w-40 rounded border-gray-200 text-sm">
                                            <span class="text-[10px] text-gray-400 flex-1">or <code class="bg-gray-100 px-1 rounded">_default</code> for fallback</span>
                                            <button type="button" @click="question.dynamic_variants.splice(vi, 1)" class="text-red-400 hover:text-red-600 p-1">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                            </button>
                                        </div>
                                        <input type="text" x-model="variant.title" placeholder="Variant headline"
                                            class="w-full rounded border-gray-200 text-sm mb-2">
                                        <textarea x-model="variant.body" placeholder="Variant body text" rows="2"
                                            class="w-full rounded border-gray-200 text-sm"></textarea>
                                    </div>
                                </template>
                                <button type="button" @click="question.dynamic_variants.push({ key: '', title: '', body: '' })"
                                    class="text-sm text-brand-gold hover:underline font-medium">+ Add Variant</button>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Footer --}}
                <div class="px-6 py-4 border-t border-gray-200 bg-gray-50/50 flex justify-end gap-3 rounded-b-xl">
                    <button type="button" @click="closeModal" class="btn btn-secondary text-sm">Cancel</button>
                    <button type="submit" class="btn btn-primary text-sm" x-text="isEdit ? 'Save Changes' : 'Add Slide'"></button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function questionModal() {
    return {
        isEdit: false,
        advancedOpen: false,
        formAction: '{{ route("admin.quizzes.questions.store", $quiz) }}',
        allSlides: {!! $slidesJson !!},
        availableTags: {!! $availableTagsJson !!},
        slideTypes: [
            { value: 'question', label: 'Question', sub: 'Multiple choice', icon: '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>' },
            { value: 'question_text', label: 'Text Input', sub: 'Free-text answer', icon: '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>' },
            { value: 'intermission', label: 'Info Break', sub: 'Educational content', icon: '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>' },
            { value: 'loading', label: 'Loading', sub: 'Animated checklist', icon: '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>' },
            { value: 'email_capture', label: 'Email', sub: 'Collect address', icon: '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>' },
            { value: 'peptide_reveal', label: 'Peptide', sub: 'Recommendation', icon: '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>' },
            { value: 'vendor_reveal', label: 'Vendor', sub: 'Product details', icon: '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>' },
            { value: 'bridge', label: 'Next Steps', sub: 'Final CTA', icon: '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>' },
        ],
        question: {
            slide_type: 'question',
            question_text: '',
            question_subtext: '',
            question_type: 'single_choice',
            klaviyo_property: '',
            is_required: true,
            max_selections: null,
            settings: {},
            options: [{ label: '', value: '', subtext: '', score_tof: 0, score_mof: 0, score_bof: 0, skip_to_question: '', tags: [], _expanded: false }],
            content_title: '',
            content_body: '',
            content_source: '',
            auto_advance_seconds: 5,
            cta_text: '',
            cta_url: '',
            dynamic_content_key: '',
            dynamic_variants: [],
            show_conditions: { type: 'and', conditions: [] },
        },
        slideTypeHelp: {
            question: 'Multiple-choice question with scoring. Each answer adds points to funnel segments (Beginner/Researching/Ready) to classify the user.',
            question_text: 'Free-text input. The user types their answer instead of choosing options. Great for open-ended questions.',
            intermission: 'Informational slide shown between questions. Share a relevant stat, testimonial, or fact to build trust.',
            loading: 'Animated checklist that simulates processing. Each line appears in sequence, then auto-advances. Creates anticipation.',
            email_capture: 'Email collection form. Users enter their email or skip. Synced to Klaviyo if configured.',
            peptide_reveal: 'Shows the personalized peptide recommendation based on answers. Powered by the Results Bank.',
            vendor_reveal: 'Shows the recommended vendor with product details, pricing, and a link to their site.',
            bridge: 'Final slide with next-steps content and a call-to-action button.',
        },
        titlePlaceholders: {
            intermission: 'e.g. Did You Know?',
            email_capture: 'e.g. Get Your Personalized Results',
            loading: 'e.g. Analyzing Your Profile',
            bridge: 'e.g. What Happens Next',
        },
        titleHelp: {
            intermission: 'Bold heading at the top. Keep it attention-grabbing.',
            email_capture: 'Heading above the email form. Make it value-focused.',
            loading: 'Heading shown while the checklist animates.',
            bridge: 'Heading for the final CTA slide.',
        },
        bodyPlaceholders: {
            intermission: 'e.g. Research shows peptides can improve recovery by up to 40%...',
            loading: 'Checking your health profile...\nMatching peptide options...\nCalculating optimal dosage...',
            bridge: 'e.g. 1. Review your recommendation\n2. Compare vendors\n3. Start your journey',
        },
        bodyHelp: {
            intermission: 'Main content. Stats, testimonials, or facts. Keep it concise (1-3 sentences).',
            loading: 'One checklist item per line. 3-5 items that suggest thorough analysis.',
            bridge: 'Steps or bullet points explaining what happens next.',
        },
        addOption() {
            this.question.options.push({ label: '', value: '', subtext: '', score_tof: 0, score_mof: 0, score_bof: 0, skip_to_question: '', tags: [], _expanded: false });
        },
        removeOption(index) {
            this.question.options.splice(index, 1);
        },
        addCondition() {
            this.question.show_conditions.conditions.push({ question_id: '', option_value: '' });
            this.advancedOpen = true;
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
            this.advancedOpen = false;
            this.question = {
                slide_type: 'question',
                question_text: '',
                question_subtext: '',
                question_type: 'single_choice',
                klaviyo_property: '',
                is_required: true,
                max_selections: null,
                settings: {},
                options: [{ label: '', value: '', subtext: '', score_tof: 0, score_mof: 0, score_bof: 0, skip_to_question: '', tags: [], _expanded: false }],
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
            formData.append('question_subtext', this.question.question_subtext || '');
            formData.append('question_type', this.question.question_type);
            formData.append('klaviyo_property', this.question.klaviyo_property || '');
            formData.append('is_required', this.question.is_required ? '1' : '0');
            if (this.question.max_selections) {
                formData.append('max_selections', this.question.max_selections);
            }
            if (this.question.settings && this.question.settings.placeholder) {
                formData.append('settings[placeholder]', this.question.settings.placeholder);
            }
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
                    if (opt.subtext) {
                        formData.append(`options[${i}][subtext]`, opt.subtext);
                    }
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

    // Parse dynamic_content_map back into variants array
    const dynamicMap = questionData.dynamic_content_map || {};
    const dynamicVariants = Object.entries(dynamicMap).map(([key, val]) => ({
        key: key,
        title: val.title || '',
        body: val.body || '',
    }));

    // Auto-expand advanced if relevant data exists
    const hasAdvanced = (parsedConds.conditions.length > 0)
        || (questionData.klaviyo_property)
        || (questionData.dynamic_content_key)
        || (questionData.is_required === false)
        || (questionData.max_selections);
    data.advancedOpen = hasAdvanced;

    // Populate form
    data.question = {
        slide_type: questionData.slide_type || 'question',
        question_text: questionData.question_text || '',
        question_subtext: questionData.question_subtext || '',
        question_type: questionData.question_type || 'single_choice',
        klaviyo_property: questionData.klaviyo_property || '',
        is_required: questionData.is_required !== undefined ? questionData.is_required : true,
        max_selections: questionData.max_selections || null,
        settings: questionData.settings || {},
        options: questionData.options && questionData.options.length > 0
            ? questionData.options.map(o => ({
                label: o.label || o.text || '',
                value: o.value || '',
                subtext: o.subtext || '',
                score_tof: o.score_tof || 0,
                score_mof: o.score_mof || 0,
                score_bof: o.score_bof || 0,
                skip_to_question: o.skip_to_question ? String(o.skip_to_question) : '',
                tags: o.tags || [],
                _expanded: false,
            }))
            : [{ label: '', value: '', subtext: '', score_tof: 0, score_mof: 0, score_bof: 0, skip_to_question: '', tags: [], _expanded: false }],
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
