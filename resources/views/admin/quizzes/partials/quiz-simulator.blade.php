{{-- Quiz Simulator Modal --}}
<div x-data="quizSimulator()" x-show="open" x-cloak
     @open-simulator.window="open = true; reset()"
     class="fixed inset-0 z-50 overflow-hidden"
     @keydown.escape.window="close()">

    {{-- Backdrop --}}
    <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm" @click="close()"></div>

    {{-- Modal Container --}}
    <div class="relative flex h-full items-stretch justify-center p-4 lg:p-8"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100">

        {{-- Admin Sidebar --}}
        <div class="hidden lg:flex flex-col w-80 bg-white rounded-l-2xl border-r border-gray-200 overflow-hidden">
            <div class="p-4 border-b border-gray-200 bg-gray-50">
                <h3 class="font-semibold text-sm text-gray-700">Admin Panel</h3>
            </div>
            <div class="flex-1 overflow-y-auto p-4 space-y-4">

                {{-- Current Slide Info --}}
                <div>
                    <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Current Slide</h4>
                    <div class="bg-gray-50 rounded-lg p-3">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="text-xs font-mono text-gray-500" x-text="'#' + currentSlide.order"></span>
                            <span class="px-1.5 py-0.5 text-xs rounded border font-medium"
                                  :class="slideTypeColor(currentSlide.slide_type)"
                                  x-text="slideTypeLabel(currentSlide.slide_type)"></span>
                        </div>
                        <p class="text-sm text-gray-800 font-medium" x-text="currentSlide.question_text || currentSlide.content_title || slideTypeLabel(currentSlide.slide_type)"></p>
                    </div>
                </div>

                {{-- Running Scores --}}
                <div>
                    <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Segment Scores</h4>
                    <div class="space-y-2">
                        <template x-for="seg in ['tof', 'mof', 'bof']" :key="seg">
                            <div class="flex items-center gap-2">
                                <span class="text-xs font-mono w-8 text-gray-500 uppercase" x-text="seg"></span>
                                <div class="flex-1 h-2.5 bg-gray-200 rounded-full overflow-hidden">
                                    <div class="h-full rounded-full transition-all duration-300"
                                         :class="{
                                             'bg-blue-500': seg === 'tof',
                                             'bg-yellow-500': seg === 'mof',
                                             'bg-green-500': seg === 'bof'
                                         }"
                                         :style="'width: ' + Math.min((scores[seg] / Math.max(maxScore, 1)) * 100, 100) + '%'">
                                    </div>
                                </div>
                                <span class="text-xs font-mono w-6 text-right text-gray-600" x-text="scores[seg]"></span>
                            </div>
                        </template>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Segment: <span class="font-semibold uppercase" x-text="segment"></span></p>
                </div>

                {{-- Live Outcome Preview --}}
                <div>
                    <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Live Outcome Preview</h4>
                    <div class="rounded-lg border-2 p-3 transition-colors duration-300"
                         :class="liveOutcome ? 'border-brand-gold/40 bg-brand-gold/5' : 'border-gray-200 bg-gray-50'">
                        <template x-if="liveOutcome">
                            <div>
                                <p class="text-xs font-semibold text-brand-gold mb-1">Currently Matching:</p>
                                <p class="text-sm font-bold text-gray-800" x-text="liveOutcome.name"></p>
                                <hr class="my-2 border-gray-200">
                                <p class="text-xs text-gray-500">
                                    Condition: <span class="font-medium" x-text="liveOutcome.conditions.type || 'fallback'"></span>
                                </p>
                                <template x-if="liveOutcome.conditions.type === 'answer'">
                                    <p class="text-xs text-gray-500" x-text="liveOutcome.conditions.question + ' = ' + liveOutcome.conditions.value"></p>
                                </template>
                                <template x-if="liveOutcome.conditions.type === 'segment'">
                                    <p class="text-xs text-gray-500" x-text="'segment = ' + liveOutcome.conditions.segment"></p>
                                </template>
                                <template x-if="liveOutcome.result_title">
                                    <p class="text-xs text-gray-600 mt-1 italic" x-text="'→ ' + liveOutcome.result_title"></p>
                                </template>
                                <template x-if="liveOutcome.redirect_url">
                                    <p class="text-xs text-blue-500 mt-1 truncate" x-text="'→ ' + liveOutcome.redirect_url"></p>
                                </template>
                            </div>
                        </template>
                        <template x-if="!liveOutcome">
                            <p class="text-xs text-gray-400 italic">No match yet</p>
                        </template>
                    </div>
                </div>

                {{-- Answer History --}}
                <div>
                    <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">
                        Answer History
                        <span class="text-gray-400 font-normal" x-text="'(' + Object.keys(answers).length + ')'"></span>
                    </h4>
                    <div class="space-y-1.5 max-h-48 overflow-y-auto">
                        <template x-for="(answer, qId) in answers" :key="qId">
                            <div class="text-xs bg-gray-50 rounded p-2">
                                <span class="text-gray-500" x-text="'#' + answer.order"></span>
                                <span class="text-gray-700 font-medium" x-text="answer.label.substring(0, 30)"></span>
                                <span class="text-gray-400">→</span>
                                <span class="text-gray-800" x-text="answer.value_label"></span>
                                <template x-if="answer.score_impact">
                                    <span class="text-green-600 ml-1" x-text="answer.score_impact"></span>
                                </template>
                            </div>
                        </template>
                        <template x-if="Object.keys(answers).length === 0">
                            <p class="text-xs text-gray-400 italic">No answers yet</p>
                        </template>
                    </div>
                </div>
            </div>
        </div>

        {{-- Phone Frame --}}
        <div class="flex flex-col w-full max-w-lg bg-white lg:rounded-r-2xl lg:rounded-l-none rounded-2xl overflow-hidden shadow-2xl">
            {{-- Header --}}
            <div class="flex items-center justify-between px-4 py-3 border-b border-gray-200 bg-gray-50">
                <div class="flex items-center gap-3 flex-1 min-w-0">
                    {{-- Progress bar --}}
                    <div class="flex-1 h-1.5 bg-gray-200 rounded-full overflow-hidden">
                        <div class="h-full bg-brand-gold rounded-full transition-all duration-500"
                             :style="'width: ' + progress + '%'"></div>
                    </div>
                    <span class="text-xs text-gray-500 whitespace-nowrap" x-text="slideCounter"></span>
                </div>
                <button @click="close()" class="ml-3 text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            {{-- Content Area --}}
            <div class="flex-1 overflow-y-auto p-6">

                {{-- Completion Screen --}}
                <template x-if="completed">
                    <div class="text-center py-4">
                        <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <h2 class="text-2xl font-bold mb-2">Quiz Complete!</h2>

                        <div class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-sm font-semibold mb-4"
                             :class="{
                                 'bg-blue-100 text-blue-700': segment === 'tof',
                                 'bg-yellow-100 text-yellow-700': segment === 'mof',
                                 'bg-green-100 text-green-700': segment === 'bof'
                             }">
                            <span x-text="segment.toUpperCase()"></span>
                            <span class="font-normal" x-text="segmentLabel"></span>
                        </div>

                        <div class="flex justify-center gap-4 text-sm text-gray-600 mb-6">
                            <span>TOF <strong x-text="scores.tof"></strong></span>
                            <span>MOF <strong x-text="scores.mof"></strong></span>
                            <span>BOF <strong x-text="scores.bof"></strong></span>
                        </div>

                        {{-- Matched Outcome --}}
                        <template x-if="matchedOutcome">
                            <div class="border-2 border-brand-gold/40 rounded-xl p-5 text-left bg-brand-gold/5 mb-6">
                                <p class="text-xs font-semibold text-brand-gold uppercase tracking-wide mb-1">Matched Outcome</p>
                                <p class="text-lg font-bold text-gray-900" x-text="matchedOutcome.name"></p>

                                <template x-if="matchedOutcome.result_title">
                                    <p class="text-sm text-gray-700 mt-2" x-text="matchedOutcome.result_title"></p>
                                </template>
                                <template x-if="matchedOutcome.result_message">
                                    <p class="text-sm text-gray-500 mt-1" x-text="matchedOutcome.result_message"></p>
                                </template>

                                <hr class="my-3 border-gray-200">

                                <div class="space-y-1 text-xs text-gray-500">
                                    <p>
                                        Condition: <span class="font-semibold text-gray-700" x-text="matchedOutcome.conditions.type || 'fallback (first by priority)'"></span>
                                    </p>
                                    <template x-if="matchedOutcome.conditions.type === 'answer'">
                                        <p x-text="matchedOutcome.conditions.question + ' = ' + matchedOutcome.conditions.value"></p>
                                    </template>
                                    <template x-if="matchedOutcome.conditions.type === 'segment'">
                                        <p x-text="'segment = ' + matchedOutcome.conditions.segment"></p>
                                    </template>
                                    <template x-if="matchedOutcome.conditions.type === 'score'">
                                        <p x-text="'min_score = ' + matchedOutcome.conditions.min_score + ' (type: ' + (matchedOutcome.conditions.score_type || 'total') + ')'"></p>
                                    </template>
                                    <template x-if="matchedOutcome.redirect_url">
                                        <p class="text-blue-600">Redirects to: <span x-text="matchedOutcome.redirect_url"></span></p>
                                    </template>
                                    <template x-if="matchedOutcome.product_link">
                                        <p class="text-blue-600">Product: <span x-text="matchedOutcome.product_link"></span></p>
                                    </template>
                                </div>
                            </div>
                        </template>
                        <template x-if="!matchedOutcome">
                            <div class="border rounded-xl p-4 text-center bg-gray-50 mb-6">
                                <p class="text-sm text-gray-500">No outcome matched. Check outcome conditions.</p>
                            </div>
                        </template>

                        <div class="flex gap-3 justify-center">
                            <button @click="reset()" class="btn btn-primary text-sm">Restart</button>
                            <button @click="reset(); selectRandomPath = true" class="btn bg-gray-200 text-gray-700 hover:bg-gray-300 text-sm">Try Different Path</button>
                        </div>
                    </div>
                </template>

                {{-- Slide Renderers --}}
                <template x-if="!completed && currentSlide">
                    <div>
                        {{-- Question (Choice) Slide --}}
                        <template x-if="currentSlide.slide_type === 'question'">
                            <div>
                                <h2 class="text-xl font-semibold mb-5" x-text="currentSlide.question_text"></h2>
                                <template x-if="isMultipleChoice">
                                    <p class="text-sm text-gray-500 mb-3">Select all that apply</p>
                                </template>
                                <div class="space-y-2.5">
                                    <template x-for="(option, oi) in (currentSlide.options || [])" :key="oi">
                                        <button
                                            @click="isMultipleChoice ? toggleMultiSelection(option) : selectOption(option)"
                                            class="w-full text-left p-3.5 rounded-lg border-2 transition-all"
                                            :class="isOptionSelected(option) ? 'border-brand-gold bg-brand-gold/10' : 'border-gray-200 hover:border-brand-gold/50 hover:bg-gray-50'">
                                            <div class="flex items-center gap-3">
                                                {{-- Checkbox for multiple, nothing for single --}}
                                                <template x-if="isMultipleChoice">
                                                    <span class="flex-shrink-0 w-5 h-5 rounded border-2 flex items-center justify-center"
                                                          :class="isOptionSelected(option) ? 'border-brand-gold bg-brand-gold' : 'border-gray-300'">
                                                        <svg x-show="isOptionSelected(option)" class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                                        </svg>
                                                    </span>
                                                </template>
                                                <div>
                                                    <span class="font-medium text-sm" x-text="option.text || option.label || option.value"></span>
                                                    <template x-if="option.subtext">
                                                        <span class="block text-xs text-gray-500 mt-0.5" x-text="option.subtext"></span>
                                                    </template>
                                                </div>
                                            </div>
                                        </button>
                                    </template>
                                </div>
                                {{-- Continue for multiple choice --}}
                                <template x-if="isMultipleChoice">
                                    <div class="flex justify-end mt-4">
                                        <button @click="submitMultipleChoice()"
                                                :disabled="multiSelections.length === 0"
                                                class="btn text-sm"
                                                :class="multiSelections.length ? 'btn-primary' : 'bg-gray-300 text-gray-500 cursor-not-allowed'">
                                            Continue
                                        </button>
                                    </div>
                                </template>
                            </div>
                        </template>

                        {{-- Question (Text Input) Slide --}}
                        <template x-if="currentSlide.slide_type === 'question_text'">
                            <div>
                                <h2 class="text-xl font-semibold mb-5" x-text="currentSlide.question_text"></h2>
                                <textarea x-model="textInputs[currentSlide.id]" rows="3"
                                          placeholder="Type your answer..."
                                          class="w-full rounded-lg border-gray-300 focus:border-brand-gold focus:ring-brand-gold text-sm"></textarea>
                                <div class="flex justify-end mt-4">
                                    <button @click="submitText()"
                                            :disabled="!(textInputs[currentSlide.id] || '').trim()"
                                            class="btn text-sm"
                                            :class="(textInputs[currentSlide.id] || '').trim() ? 'btn-primary' : 'bg-gray-300 text-gray-500 cursor-not-allowed'">
                                        Continue
                                    </button>
                                </div>
                            </div>
                        </template>

                        {{-- Intermission Slide --}}
                        <template x-if="currentSlide.slide_type === 'intermission'">
                            <div>
                                <template x-if="resolvedContent.title">
                                    <h2 class="text-xl font-bold mb-4" x-text="resolvedContent.title"></h2>
                                </template>
                                <template x-if="resolvedContent.body">
                                    <div class="text-gray-700 text-sm leading-relaxed space-y-2 mb-4" x-html="nl2br(resolvedContent.body)"></div>
                                </template>
                                <template x-if="currentSlide.content_source">
                                    <p class="text-xs text-gray-400 italic mb-4" x-text="'Source: ' + currentSlide.content_source"></p>
                                </template>
                            </div>
                        </template>

                        {{-- Loading Slide --}}
                        <template x-if="currentSlide.slide_type === 'loading'">
                            <div class="text-center"
                                 x-data="{ loadingItem: 0, loadingDone: false, loadingProgress: 0 }"
                                 x-init="(() => {
                                     let el = $el;
                                     let items = (resolvedContent.body || '').split('\n').filter(l => l.trim());
                                     let total = 2000;
                                     let elapsed = 0;
                                     let timer = setInterval(() => {
                                         elapsed += 50;
                                         loadingProgress = Math.min((elapsed / total) * 100, 100);
                                         loadingItem = Math.floor((elapsed / total) * items.length);
                                         if (elapsed >= total) { clearInterval(timer); loadingDone = true; setTimeout(() => advanceContinue(), 400); }
                                     }, 50);
                                 })()">
                                <h2 class="text-xl font-bold mb-5" x-text="resolvedContent.title || 'Analyzing your answers...'"></h2>
                                <div class="space-y-2 max-w-xs mx-auto text-left mb-6">
                                    <template x-for="(item, i) in (resolvedContent.body || '').split('\n').filter(l => l.trim())" :key="i">
                                        <div class="flex items-center gap-2 transition-all duration-300"
                                             :class="loadingItem >= i ? 'opacity-100' : 'opacity-0'">
                                            <div class="w-5 h-5 rounded-full flex items-center justify-center text-white flex-shrink-0"
                                                 :class="loadingItem > i ? 'bg-green-500' : 'bg-brand-gold/30'">
                                                <svg x-show="loadingItem > i" class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                                </svg>
                                                <span x-show="loadingItem === i" class="w-1.5 h-1.5 rounded-full bg-brand-gold animate-pulse"></span>
                                            </div>
                                            <span class="text-sm" x-text="item"></span>
                                        </div>
                                    </template>
                                </div>
                                <div class="w-full max-w-xs mx-auto h-2 bg-gray-200 rounded-full overflow-hidden">
                                    <div class="h-full rounded-full transition-all duration-200"
                                         :class="loadingDone ? 'bg-green-500' : 'bg-brand-gold'"
                                         :style="'width: ' + loadingProgress + '%'"></div>
                                </div>
                                <p class="text-xs text-gray-500 mt-2" x-show="!loadingDone">Please wait...</p>
                                <p class="text-xs text-green-600 font-semibold mt-2" x-show="loadingDone">Complete!</p>
                            </div>
                        </template>

                        {{-- Email Capture Slide --}}
                        <template x-if="currentSlide.slide_type === 'email_capture'">
                            <div>
                                <h2 class="text-xl font-semibold mb-4" x-text="resolvedContent.title || 'Get your personalized results via email'"></h2>
                                <template x-if="resolvedContent.body">
                                    <p class="text-gray-600 text-sm mb-4" x-text="resolvedContent.body"></p>
                                </template>
                                <input type="email" disabled placeholder="[Preview mode — email not collected]"
                                       class="w-full rounded-lg border-gray-300 bg-gray-100 text-sm text-gray-400 mb-4 cursor-not-allowed">
                                <div class="flex gap-2">
                                    <button @click="advanceContinue()" class="btn btn-primary flex-1 text-sm">Continue</button>
                                    <button @click="advanceContinue()" class="btn bg-gray-200 text-gray-700 hover:bg-gray-300 text-sm">Skip</button>
                                </div>
                            </div>
                        </template>

                        {{-- Peptide Reveal Slide --}}
                        <template x-if="currentSlide.slide_type === 'peptide_reveal'">
                            <div class="text-center">
                                <div class="w-14 h-14 bg-brand-gold/10 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <svg class="w-7 h-7 text-brand-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 3.104v5.714a2.25 2.25 0 01-.659 1.591L5 14.5M9.75 3.104c-.251.023-.501.05-.75.082m.75-.082a24.301 24.301 0 014.5 0m0 0v5.714c0 .597.237 1.17.659 1.591L19.8 15.3M14.25 3.104c.251.023.501.05.75.082"/>
                                    </svg>
                                </div>
                                <p class="text-xs text-brand-gold font-semibold uppercase tracking-wide mb-1">Your Personalized Recommendation</p>
                                <h2 class="text-2xl font-bold mb-3" x-text="resolvedContent.title || currentSlide.content_title || 'Your Peptide Match'"></h2>
                                <template x-if="resolvedContent.body">
                                    <div class="text-gray-600 text-sm mb-4 max-w-sm mx-auto" x-html="nl2br(resolvedContent.body)"></div>
                                </template>
                                <template x-if="currentSlide.cta_text">
                                    <span class="inline-block btn btn-primary text-sm opacity-75 cursor-default" x-text="currentSlide.cta_text"></span>
                                </template>
                            </div>
                        </template>

                        {{-- Vendor Reveal Slide --}}
                        <template x-if="currentSlide.slide_type === 'vendor_reveal'">
                            <div class="text-center">
                                <div class="w-14 h-14 bg-indigo-50 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <svg class="w-7 h-7 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.5 21v-7.5a.75.75 0 01.75-.75h3a.75.75 0 01.75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349m-16.5 11.65V9.35"/>
                                    </svg>
                                </div>
                                <p class="text-xs text-indigo-600 font-semibold uppercase tracking-wide mb-1">Trusted Vendors</p>
                                <h2 class="text-xl font-bold mb-3" x-text="resolvedContent.title || currentSlide.content_title || 'Where to Get Your Peptide'"></h2>
                                <template x-if="resolvedContent.body">
                                    <div class="text-gray-600 text-sm mb-4 max-w-sm mx-auto" x-html="nl2br(resolvedContent.body)"></div>
                                </template>
                                <p class="text-xs text-gray-400 italic mb-4">[Vendor comparison not available in preview]</p>
                                <template x-if="currentSlide.cta_text">
                                    <span class="inline-block btn bg-indigo-600 text-white text-sm opacity-75 cursor-default" x-text="currentSlide.cta_text"></span>
                                </template>
                            </div>
                        </template>

                        {{-- Bridge Slide --}}
                        <template x-if="currentSlide.slide_type === 'bridge'">
                            <div class="text-center">
                                <div class="w-14 h-14 bg-brand-gold/10 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <svg class="w-7 h-7 text-brand-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12.75L11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 01-1.043 3.296 3.745 3.745 0 01-3.296 1.043A3.745 3.745 0 0112 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 01-3.296-1.043 3.745 3.745 0 01-1.043-3.296A3.745 3.745 0 013 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 011.043-3.296 3.746 3.746 0 013.296-1.043A3.746 3.746 0 0112 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 013.296 1.043 3.746 3.746 0 011.043 3.296A3.745 3.745 0 0121 12z"/>
                                    </svg>
                                </div>
                                <h2 class="text-xl font-bold mb-3" x-text="resolvedContent.title || currentSlide.content_title || 'What Happens Next'"></h2>
                                <template x-if="resolvedContent.body">
                                    <div class="text-gray-600 text-sm leading-relaxed mb-4 max-w-sm mx-auto" x-html="nl2br(resolvedContent.body)"></div>
                                </template>
                                <template x-if="currentSlide.cta_text">
                                    <span class="inline-block btn btn-primary text-sm opacity-75 cursor-default mb-2" x-text="currentSlide.cta_text"></span>
                                </template>
                            </div>
                        </template>

                        {{-- Navigation (for non-auto-advance slides) --}}
                        <template x-if="currentSlide.slide_type !== 'loading' && currentSlide.slide_type !== 'question'">
                            <div class="flex items-center justify-between mt-6 pt-4 border-t border-gray-100">
                                <template x-if="history.length > 0">
                                    <button @click="goBack()" class="flex items-center gap-1 text-sm text-gray-500 hover:text-gray-700">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                        </svg>
                                        Back
                                    </button>
                                </template>
                                <template x-if="history.length === 0">
                                    <span></span>
                                </template>
                                {{-- Continue button for non-question, non-loading slides --}}
                                <template x-if="!['question_text', 'email_capture', 'loading'].includes(currentSlide.slide_type)">
                                    <button @click="advanceContinue()" class="btn btn-primary text-sm flex items-center gap-1">
                                        Continue
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                        </svg>
                                    </button>
                                </template>
                            </div>
                        </template>

                        {{-- Back button for question slides --}}
                        <template x-if="currentSlide.slide_type === 'question' && history.length > 0">
                            <div class="mt-6 pt-4 border-t border-gray-100">
                                <button @click="goBack()" class="flex items-center gap-1 text-sm text-gray-500 hover:text-gray-700">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                    </svg>
                                    Back
                                </button>
                            </div>
                        </template>
                    </div>
                </template>
            </div>

            {{-- Mobile: Mini sidebar toggle --}}
            <div class="lg:hidden border-t border-gray-200 bg-gray-50 px-4 py-2 flex items-center justify-between text-xs text-gray-500">
                <span>
                    Scores: T<strong x-text="scores.tof"></strong> M<strong x-text="scores.mof"></strong> B<strong x-text="scores.bof"></strong>
                    | <span class="uppercase" x-text="segment"></span>
                </span>
                <template x-if="liveOutcome">
                    <span class="text-brand-gold font-medium truncate ml-2" x-text="liveOutcome.name"></span>
                </template>
            </div>
        </div>
    </div>
</div>

<script>
function quizSimulator() {
    return {
        open: false,
        slides: @json($questionsJson),
        outcomes: @json($outcomesJson),
        currentIndex: 0,
        answers: {},
        scores: { tof: 0, mof: 0, bof: 0 },
        history: [],
        completed: false,
        matchedOutcome: null,
        textInputs: {},
        multiSelections: [],
        selectRandomPath: false,

        // --- Computed Properties ---

        get currentSlide() {
            return this.slides[this.currentIndex] || {};
        },

        get segment() {
            const { tof, mof, bof } = this.scores;
            const max = Math.max(tof, mof, bof);
            if (bof >= max) return 'bof';
            if (mof >= max) return 'mof';
            return 'tof';
        },

        get segmentLabel() {
            return { tof: '(Explorer)', mof: '(Researcher)', bof: '(Ready to Start)' }[this.segment] || '';
        },

        get maxScore() {
            return Math.max(this.scores.tof, this.scores.mof, this.scores.bof, 1);
        },

        get progress() {
            if (this.completed) return 100;
            const inputSlides = this.slides.filter(s => ['question', 'question_text', 'email_capture'].includes(s.slide_type));
            const answered = Object.keys(this.answers).length;
            return inputSlides.length > 0 ? Math.round((answered / inputSlides.length) * 100) : 0;
        },

        get slideCounter() {
            return (this.currentIndex + 1) + ' / ' + this.slides.length;
        },

        get isMultipleChoice() {
            return (this.currentSlide.question_type || '') === 'multiple_choice';
        },

        get resolvedContent() {
            const slide = this.currentSlide;
            const dynamic = this.resolveDynamicContent(slide);
            return {
                title: dynamic?.title || this.interpolateTokens(slide.content_title || ''),
                body: dynamic?.body || this.interpolateTokens(slide.content_body || ''),
            };
        },

        get liveOutcome() {
            return this.determineOutcome();
        },

        // --- Actions ---

        selectOption(option) {
            const slide = this.currentSlide;
            const optionKey = option.value || option.id || '';
            const optionLabel = option.text || option.label || optionKey;

            // Record scores
            const scoreTof = parseInt(option.score_tof || 0);
            const scoreMof = parseInt(option.score_mof || 0);
            const scoreBof = parseInt(option.score_bof || 0);

            // Build score impact string
            let impacts = [];
            if (scoreTof) impacts.push('TOF +' + scoreTof);
            if (scoreMof) impacts.push('MOF +' + scoreMof);
            if (scoreBof) impacts.push('BOF +' + scoreBof);

            // Store answer
            this.answers[slide.id] = {
                order: slide.order,
                label: slide.question_text || '',
                value: optionKey,
                value_label: optionLabel,
                klaviyo_property: slide.klaviyo_property || '',
                klaviyo_value: option.klaviyo_value || optionLabel,
                score_impact: impacts.join(', ') || null,
                scores: { tof: scoreTof, mof: scoreMof, bof: scoreBof },
            };

            // Add scores
            this.scores.tof += scoreTof;
            this.scores.mof += scoreMof;
            this.scores.bof += scoreBof;

            // Check skip_to_question
            const skipTo = option.skip_to_question || null;

            // Push to history
            this.history.push(this.currentIndex);

            // Navigate
            this.navigateNext(skipTo);
        },

        toggleMultiSelection(option) {
            const key = option.value || option.id || '';
            const idx = this.multiSelections.indexOf(key);
            if (idx >= 0) {
                this.multiSelections.splice(idx, 1);
            } else {
                this.multiSelections.push(key);
            }
        },

        isOptionSelected(option) {
            const key = option.value || option.id || '';
            if (this.isMultipleChoice) {
                return this.multiSelections.includes(key);
            }
            const answer = this.answers[this.currentSlide.id];
            return answer && answer.value === key;
        },

        submitMultipleChoice() {
            if (this.multiSelections.length === 0) return;

            const slide = this.currentSlide;
            const options = slide.options || [];
            const selected = options.filter(o => this.multiSelections.includes(o.value || o.id || ''));

            let totalTof = 0, totalMof = 0, totalBof = 0;
            const labels = [];
            selected.forEach(o => {
                totalTof += parseInt(o.score_tof || 0);
                totalMof += parseInt(o.score_mof || 0);
                totalBof += parseInt(o.score_bof || 0);
                labels.push(o.text || o.label || o.value);
            });

            let impacts = [];
            if (totalTof) impacts.push('TOF +' + totalTof);
            if (totalMof) impacts.push('MOF +' + totalMof);
            if (totalBof) impacts.push('BOF +' + totalBof);

            this.answers[slide.id] = {
                order: slide.order,
                label: slide.question_text || '',
                value: this.multiSelections.join(', '),
                value_label: labels.join(', '),
                klaviyo_property: slide.klaviyo_property || '',
                klaviyo_value: labels.join(', '),
                score_impact: impacts.join(', ') || null,
                scores: { tof: totalTof, mof: totalMof, bof: totalBof },
            };

            this.scores.tof += totalTof;
            this.scores.mof += totalMof;
            this.scores.bof += totalBof;

            this.history.push(this.currentIndex);
            this.multiSelections = [];
            this.navigateNext(null);
        },

        submitText() {
            const slide = this.currentSlide;
            const text = (this.textInputs[slide.id] || '').trim();
            if (!text) return;

            this.answers[slide.id] = {
                order: slide.order,
                label: slide.question_text || '',
                value: text,
                value_label: text.substring(0, 40) + (text.length > 40 ? '...' : ''),
                klaviyo_property: slide.klaviyo_property || '',
                klaviyo_value: text,
                score_impact: null,
                scores: { tof: 0, mof: 0, bof: 0 },
            };

            this.history.push(this.currentIndex);
            this.navigateNext(null);
        },

        advanceContinue() {
            this.history.push(this.currentIndex);
            this.navigateNext(null);
        },

        navigateNext(skipToQuestionId) {
            let startIndex = this.currentIndex + 1;

            // Handle skip_to_question
            if (skipToQuestionId) {
                const targetIdx = this.findSlideIndexById(skipToQuestionId);
                if (targetIdx !== null && targetIdx > this.currentIndex) {
                    startIndex = targetIdx;
                }
            }

            // Walk forward, skip slides whose conditions aren't met
            for (let i = startIndex; i < this.slides.length; i++) {
                if (this.shouldShowSlide(this.slides[i])) {
                    this.currentIndex = i;
                    this.multiSelections = [];
                    return;
                }
            }

            // No more slides — complete
            this.completed = true;
            this.matchedOutcome = this.determineOutcome();
        },

        goBack() {
            if (this.history.length === 0) return;

            const prevIndex = this.history.pop();

            // Revert answer and scores if we're leaving a slide we answered
            const currentSlideId = this.currentSlide.id;
            if (this.answers[currentSlideId]) {
                const a = this.answers[currentSlideId];
                this.scores.tof -= (a.scores?.tof || 0);
                this.scores.mof -= (a.scores?.mof || 0);
                this.scores.bof -= (a.scores?.bof || 0);
                delete this.answers[currentSlideId];
            }

            // Also revert the answer for the slide we're going back to (so user can re-answer)
            const prevSlideId = this.slides[prevIndex]?.id;
            if (prevSlideId && this.answers[prevSlideId]) {
                const a = this.answers[prevSlideId];
                this.scores.tof -= (a.scores?.tof || 0);
                this.scores.mof -= (a.scores?.mof || 0);
                this.scores.bof -= (a.scores?.bof || 0);
                delete this.answers[prevSlideId];
            }

            this.currentIndex = prevIndex;
            this.completed = false;
            this.matchedOutcome = null;
            this.multiSelections = [];
        },

        reset() {
            this.currentIndex = 0;
            this.answers = {};
            this.scores = { tof: 0, mof: 0, bof: 0 };
            this.history = [];
            this.completed = false;
            this.matchedOutcome = null;
            this.textInputs = {};
            this.multiSelections = [];

            // Find first visible slide
            for (let i = 0; i < this.slides.length; i++) {
                if (this.shouldShowSlide(this.slides[i])) {
                    this.currentIndex = i;
                    return;
                }
            }
        },

        close() {
            this.open = false;
        },

        // --- Condition Evaluation (JS port of QuizFunnelEngine) ---

        findSlideIndexById(questionId) {
            for (let i = 0; i < this.slides.length; i++) {
                if (String(this.slides[i].id) === String(questionId)) {
                    return i;
                }
            }
            return null;
        },

        shouldShowSlide(slide) {
            const conditions = slide.show_conditions;
            if (!conditions || !conditions.conditions || conditions.conditions.length === 0) {
                return true;
            }
            return this.evaluateConditions(conditions);
        },

        evaluateConditions(conditionGroup) {
            const type = conditionGroup.type || 'and';
            const conditions = conditionGroup.conditions || [];
            if (conditions.length === 0) return true;

            for (const condition of conditions) {
                const met = this.evaluateSingleCondition(condition);
                if (type === 'or' && met) return true;
                if (type === 'and' && !met) return false;
            }
            return type === 'and';
        },

        evaluateSingleCondition(condition) {
            const questionId = condition.question_id;
            const expectedValue = condition.option_value;
            if (!questionId || !expectedValue) return true;

            // Find the answer for this question ID
            const answer = this.answers[questionId];
            if (!answer) return false;

            // Check if the answer value matches (support multi-select comma-separated)
            if (answer.value === expectedValue) return true;
            if (answer.value && answer.value.includes(',')) {
                return answer.value.split(', ').includes(expectedValue);
            }
            return false;
        },

        // --- Outcome Matching (JS port of QuizPlayer::determineOutcome) ---

        determineOutcome() {
            const activeOutcomes = this.outcomes.filter(o => o.is_active);
            if (activeOutcomes.length === 0) return null;

            // Build answers array in the format outcome matching expects
            const answersArray = Object.values(this.answers).map(a => ({
                klaviyo_property: a.klaviyo_property,
                klaviyo_value: a.klaviyo_value,
            }));

            // 1. Answer-based matching
            for (const outcome of activeOutcomes) {
                if (this.outcomeMatchesAnswer(outcome, answersArray)) return outcome;
            }

            // 2. Segment-based matching
            const seg = this.segment;
            for (const outcome of activeOutcomes) {
                if (this.outcomeMatchesSegment(outcome, seg)) return outcome;
            }

            // 3. Score-based matching
            const totalScore = this.scores.tof + this.scores.mof + this.scores.bof;
            for (const outcome of activeOutcomes) {
                if (this.outcomeMatchesScore(outcome, totalScore)) return outcome;
            }

            // 4. Fallback
            return activeOutcomes[0] || null;
        },

        outcomeMatchesAnswer(outcome, answers) {
            const cond = outcome.conditions || {};
            if (cond.type !== 'answer') return false;
            const targetProp = cond.question;
            const targetVal = cond.value;
            if (!targetProp || !targetVal) return false;

            return answers.some(a => a.klaviyo_property === targetProp && a.klaviyo_value === targetVal);
        },

        outcomeMatchesSegment(outcome, segment) {
            const cond = outcome.conditions || {};
            if (cond.type !== 'segment') return false;
            return (cond.segment || '').toLowerCase() === segment.toLowerCase();
        },

        outcomeMatchesScore(outcome, totalScore) {
            const cond = outcome.conditions || {};
            if (cond.type !== 'score') return false;
            const minScore = parseInt(cond.min_score || 0);
            return totalScore >= minScore;
        },

        // --- Dynamic Content & Token Interpolation ---

        resolveDynamicContent(slide) {
            const key = slide.dynamic_content_key;
            const map = slide.dynamic_content_map;
            if (!key || !map || typeof map !== 'object') return null;

            // Find the user's answer value for this key
            let answerValue = null;
            for (const a of Object.values(this.answers)) {
                if (a.klaviyo_property === key) {
                    answerValue = a.klaviyo_value;
                    break;
                }
            }

            if (answerValue && map[answerValue]) return map[answerValue];
            return map['_default'] || null;
        },

        interpolateTokens(text) {
            if (!text) return '';
            const tokens = {};
            for (const a of Object.values(this.answers)) {
                if (a.klaviyo_property) {
                    tokens[a.klaviyo_property] = a.value_label || a.klaviyo_value || '';
                }
            }
            return text.replace(/\{\{(\w+)\}\}/g, (match, key) => tokens[key] || match);
        },

        // --- Helpers ---

        nl2br(text) {
            if (!text) return '';
            return text.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/\n/g, '<br>');
        },

        slideTypeLabel(type) {
            const labels = {
                question: 'Question', question_text: 'Text Input', intermission: 'Intermission',
                loading: 'Loading', email_capture: 'Email', peptide_reveal: 'Peptide Reveal',
                vendor_reveal: 'Vendor Reveal', bridge: 'Bridge',
            };
            return labels[type] || type;
        },

        slideTypeColor(type) {
            const colors = {
                question: 'bg-blue-100 text-blue-700 border-blue-200',
                question_text: 'bg-blue-100 text-blue-700 border-blue-200',
                intermission: 'bg-amber-100 text-amber-700 border-amber-200',
                loading: 'bg-purple-100 text-purple-700 border-purple-200',
                email_capture: 'bg-green-100 text-green-700 border-green-200',
                peptide_reveal: 'bg-pink-100 text-pink-700 border-pink-200',
                vendor_reveal: 'bg-indigo-100 text-indigo-700 border-indigo-200',
                bridge: 'bg-orange-100 text-orange-700 border-orange-200',
            };
            return colors[type] || 'bg-gray-200 text-gray-700 border-gray-300';
        },
    };
}
</script>
