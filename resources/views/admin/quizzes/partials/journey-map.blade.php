@php
    $slideColors = [
        'question'       => 'bg-blue-100 text-blue-700 border-blue-200',
        'question_text'  => 'bg-blue-100 text-blue-700 border-blue-200',
        'intermission'   => 'bg-amber-100 text-amber-700 border-amber-200',
        'loading'        => 'bg-purple-100 text-purple-700 border-purple-200',
        'email_capture'  => 'bg-green-100 text-green-700 border-green-200',
        'peptide_reveal' => 'bg-pink-100 text-pink-700 border-pink-200',
        'vendor_reveal'  => 'bg-indigo-100 text-indigo-700 border-indigo-200',
        'bridge'         => 'bg-orange-100 text-orange-700 border-orange-200',
    ];
    $typeAbbrev = [
        'question'       => 'Q',
        'question_text'  => 'Qt',
        'intermission'   => 'I',
        'loading'        => 'L',
        'email_capture'  => 'E',
        'peptide_reveal' => 'P',
        'vendor_reveal'  => 'V',
        'bridge'         => 'B',
    ];

    $branchMeta = [
        'tof' => ['label' => 'TOF Path', 'subtitle' => 'Brand new to peptides', 'border' => 'border-blue-400', 'bg' => 'bg-blue-50', 'text' => 'text-blue-700', 'dot' => 'bg-blue-400', 'outcomeBorder' => 'border-l-blue-400'],
        'mof' => ['label' => 'MOF Path', 'subtitle' => 'Researching',          'border' => 'border-yellow-400', 'bg' => 'bg-yellow-50', 'text' => 'text-yellow-700', 'dot' => 'bg-yellow-400', 'outcomeBorder' => 'border-l-yellow-400'],
        'bof' => ['label' => 'BOF Path', 'subtitle' => 'Ready to buy',         'border' => 'border-green-400', 'bg' => 'bg-green-50', 'text' => 'text-green-700', 'dot' => 'bg-green-400', 'outcomeBorder' => 'border-l-green-400'],
    ];

    $collapseThreshold = 8;
@endphp

<div class="card p-6">
    <h3 class="text-lg font-semibold mb-6">Journey Map</h3>

    {{-- ===== SEGMENTATION ONBOARDING (empty segmentation quiz) ===== --}}
    @if($quiz->type === 'segmentation' && $quiz->questions->isEmpty())
        <div x-data="{ approach: 'simple' }" class="space-y-4">
            <div class="rounded-xl border-2 border-dashed border-amber-300 bg-amber-50/50 p-6">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 rounded-full bg-amber-100 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    </div>
                    <div>
                        <h4 class="font-semibold text-gray-900">Build Your Segmentation Quiz</h4>
                        <p class="text-xs text-gray-500">Classify users into TOF (Explorer) / MOF (Researcher) / BOF (Ready to Buy), then show tailored outcomes.</p>
                    </div>
                </div>

                {{-- Approach Picker --}}
                <div class="mb-5">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Choose your approach</p>
                    <div class="grid grid-cols-2 gap-3">
                        <button type="button" @click="approach = 'simple'"
                            :class="approach === 'simple' ? 'border-amber-400 bg-white ring-1 ring-amber-300' : 'border-gray-200 hover:border-gray-300'"
                            class="rounded-lg border p-3 text-left transition-all">
                            <p class="text-sm font-semibold text-gray-900">Simple Scoring</p>
                            <p class="text-[10px] text-gray-500 mt-0.5">Everyone sees the same questions. Scores classify them. Different outcomes per segment.</p>
                            <span class="inline-block mt-1 text-[9px] px-1.5 py-0.5 rounded bg-green-100 text-green-700 font-medium">Recommended for most quizzes</span>
                        </button>
                        <button type="button" @click="approach = 'branching'"
                            :class="approach === 'branching' ? 'border-amber-400 bg-white ring-1 ring-amber-300' : 'border-gray-200 hover:border-gray-300'"
                            class="rounded-lg border p-3 text-left transition-all">
                            <p class="text-sm font-semibold text-gray-900">Branching Paths</p>
                            <p class="text-[10px] text-gray-500 mt-0.5">Different users see different slides based on an early answer. Like quiz 2's TOF/MOF/BOF lanes.</p>
                            <span class="inline-block mt-1 text-[9px] px-1.5 py-0.5 rounded bg-gray-100 text-gray-600 font-medium">Advanced</span>
                        </button>
                    </div>
                </div>

                {{-- ===== SIMPLE SCORING STEPS ===== --}}
                <div x-show="approach === 'simple'" x-cloak>
                    <div class="space-y-4">
                        <div class="flex gap-3">
                            <div class="flex flex-col items-center">
                                <div class="w-7 h-7 rounded-full bg-blue-500 text-white text-xs font-bold flex items-center justify-center">1</div>
                                <div class="w-px flex-1 bg-gray-300 mt-1"></div>
                            </div>
                            <div class="pb-3">
                                <p class="text-sm font-medium text-gray-900">Add Question slides</p>
                                <p class="text-xs text-gray-500 mt-0.5">Go to any phase tab &rarr; click <strong>"+ Add Slide"</strong> &rarr; choose <strong>Question</strong>. Write your question and add answer options.</p>
                            </div>
                        </div>

                        <div class="flex gap-3">
                            <div class="flex flex-col items-center">
                                <div class="w-7 h-7 rounded-full bg-amber-500 text-white text-xs font-bold flex items-center justify-center">2</div>
                                <div class="w-px flex-1 bg-gray-300 mt-1"></div>
                            </div>
                            <div class="pb-3">
                                <p class="text-sm font-medium text-gray-900">Set TOF/MOF/BOF scores on each answer</p>
                                <p class="text-xs text-gray-500 mt-0.5">Each answer option shows
                                    <span class="inline-flex items-center gap-0.5"><span class="w-1.5 h-1.5 rounded-full bg-blue-400"></span> Beginner&nbsp;(TOF)</span>,
                                    <span class="inline-flex items-center gap-0.5"><span class="w-1.5 h-1.5 rounded-full bg-yellow-400"></span> Researching&nbsp;(MOF)</span>,
                                    <span class="inline-flex items-center gap-0.5"><span class="w-1.5 h-1.5 rounded-full bg-green-400"></span> Ready&nbsp;to&nbsp;Buy&nbsp;(BOF)</span>
                                    score inputs. Put <strong>1-3</strong> in the matching segment, leave others at 0.</p>
                                <div class="mt-2 bg-white rounded-lg border border-gray-200 p-3 text-xs">
                                    <p class="font-medium text-gray-700 mb-1.5">Example: "What best describes you?"</p>
                                    <div class="space-y-1.5 text-gray-500">
                                        <div class="flex items-center gap-2">
                                            <span class="text-gray-700">"Just curious"</span>
                                            <span class="text-[10px] px-1.5 py-0.5 rounded bg-blue-50 text-blue-700 font-medium">TOF = 3</span>
                                            <span class="text-[10px] text-gray-300">MOF = 0, BOF = 0</span>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <span class="text-gray-700">"Comparing options"</span>
                                            <span class="text-[10px] text-gray-300">TOF = 0</span>
                                            <span class="text-[10px] px-1.5 py-0.5 rounded bg-yellow-50 text-yellow-700 font-medium">MOF = 3</span>
                                            <span class="text-[10px] text-gray-300">BOF = 0</span>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <span class="text-gray-700">"Ready to buy"</span>
                                            <span class="text-[10px] text-gray-300">TOF = 0, MOF = 0</span>
                                            <span class="text-[10px] px-1.5 py-0.5 rounded bg-green-50 text-green-700 font-medium">BOF = 3</span>
                                        </div>
                                    </div>
                                    <p class="text-[10px] text-gray-400 mt-2 border-t pt-1.5">After all questions, scores are totaled. Highest segment wins &rarr; that user's outcome is shown.</p>
                                </div>
                            </div>
                        </div>

                        <div class="flex gap-3">
                            <div class="flex flex-col items-center">
                                <div class="w-7 h-7 rounded-full bg-green-500 text-white text-xs font-bold flex items-center justify-center">3</div>
                                <div class="w-px flex-1 bg-gray-300 mt-1"></div>
                            </div>
                            <div class="pb-3">
                                <p class="text-sm font-medium text-gray-900">Create 3 outcomes (one per segment)</p>
                                <p class="text-xs text-gray-500 mt-0.5">In the <strong>Outcomes</strong> panel (right sidebar) &rarr; click <strong>"+ Add"</strong> &rarr; choose <strong>"When user's segment is..."</strong> &rarr; pick TOF, MOF, or BOF. Set the headline/body for each.</p>
                            </div>
                        </div>

                        <div class="flex gap-3">
                            <div class="flex flex-col items-center">
                                <div class="w-7 h-7 rounded-full bg-purple-500 text-white text-xs font-bold flex items-center justify-center">4</div>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">Add a fallback outcome</p>
                                <p class="text-xs text-gray-500 mt-0.5">One more outcome with <strong>"Always (default)"</strong> &rarr; drag it to the bottom. Catches ties and edge cases.</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ===== BRANCHING PATHS STEPS ===== --}}
                <div x-show="approach === 'branching'" x-cloak>
                    <div class="space-y-4">
                        <div class="flex gap-3">
                            <div class="flex flex-col items-center">
                                <div class="w-7 h-7 rounded-full bg-blue-500 text-white text-xs font-bold flex items-center justify-center">1</div>
                                <div class="w-px flex-1 bg-gray-300 mt-1"></div>
                            </div>
                            <div class="pb-3">
                                <p class="text-sm font-medium text-gray-900">Create the branching question (first slide)</p>
                                <p class="text-xs text-gray-500 mt-0.5">Add a Question slide as your <strong>first slide</strong>. This is the "fork in the road" that splits users into paths. Give each answer option TOF/MOF/BOF scores.</p>
                                <div class="mt-2 bg-white rounded-lg border border-gray-200 p-3 text-xs">
                                    <p class="font-medium text-gray-700 mb-1">Example: "Where are you in your peptide journey?"</p>
                                    <div class="space-y-1 text-gray-500">
                                        <p>"Brand new to peptides" &rarr; <span class="text-blue-600 font-medium">TOF = 3</span></p>
                                        <p>"Been researching" &rarr; <span class="text-yellow-600 font-medium">MOF = 3</span></p>
                                        <p>"Know what I want" &rarr; <span class="text-green-600 font-medium">BOF = 3</span></p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex gap-3">
                            <div class="flex flex-col items-center">
                                <div class="w-7 h-7 rounded-full bg-amber-500 text-white text-xs font-bold flex items-center justify-center">2</div>
                                <div class="w-px flex-1 bg-gray-300 mt-1"></div>
                            </div>
                            <div class="pb-3">
                                <p class="text-sm font-medium text-gray-900">Add path-specific slides with conditions</p>
                                <p class="text-xs text-gray-500 mt-0.5">For each subsequent slide, go to <strong>Advanced Settings</strong> &rarr; <strong>"Show this slide when..."</strong> &rarr; select your branching question &rarr; pick which answer triggers this slide.</p>
                                <div class="mt-2 bg-white rounded-lg border border-gray-200 p-3 text-xs">
                                    <p class="font-medium text-gray-700 mb-1">Example:</p>
                                    <div class="space-y-1 text-gray-500">
                                        <p>Slide "What's your #1 concern?" &rarr; Show when Q1 = "Brand new" <span class="text-blue-600">(TOF path)</span></p>
                                        <p>Slide "What have you tried?" &rarr; Show when Q1 = "Been researching" <span class="text-yellow-600">(MOF path)</span></p>
                                        <p>Slide "Which peptide interests you?" &rarr; Show when Q1 = "Know what I want" <span class="text-green-600">(BOF path)</span></p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex gap-3">
                            <div class="flex flex-col items-center">
                                <div class="w-7 h-7 rounded-full bg-green-500 text-white text-xs font-bold flex items-center justify-center">3</div>
                                <div class="w-px flex-1 bg-gray-300 mt-1"></div>
                            </div>
                            <div class="pb-3">
                                <p class="text-sm font-medium text-gray-900">The Journey Map auto-groups your paths</p>
                                <p class="text-xs text-gray-500 mt-0.5">Once you set conditions, this map will automatically show <span class="text-blue-600 font-medium">TOF</span> / <span class="text-yellow-600 font-medium">MOF</span> / <span class="text-green-600 font-medium">BOF</span> lanes. Slides without conditions go to "Shared Start" (seen by everyone).</p>
                            </div>
                        </div>

                        <div class="flex gap-3">
                            <div class="flex flex-col items-center">
                                <div class="w-7 h-7 rounded-full bg-purple-500 text-white text-xs font-bold flex items-center justify-center">4</div>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">Add outcomes for each segment</p>
                                <p class="text-xs text-gray-500 mt-0.5">Same as simple scoring: create outcomes using <strong>"When user's segment is..."</strong> + a fallback with <strong>"Always"</strong>.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-5 pt-4 border-t border-amber-200">
                    <div class="flex items-start gap-2">
                        <svg class="w-4 h-4 text-amber-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <p class="text-xs text-amber-700">
                            <strong>How scoring works:</strong> As users answer questions, their TOF/MOF/BOF scores add up across all answers. The segment with the highest total determines which outcome they see. Ties: BOF > MOF > TOF.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- ===== SECTION 1: Shared Start ===== --}}
    @if(isset($phases['shared']))
        <div class="mb-2">
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Shared Start &middot; {{ $phases['shared']['slides']->count() }} slides</p>
            <div class="flex flex-wrap gap-2">
                @foreach($phases['shared']['slides']->sortBy('order') as $slide)
                    @php
                        $color = $slideColors[$slide->slide_type] ?? 'bg-gray-100 text-gray-700 border-gray-200';
                        $abbr  = $typeAbbrev[$slide->slide_type] ?? '?';
                        $label = Str::limit($slide->question_text ?: $slide->content_title ?: ucfirst(str_replace('_', ' ', $slide->slide_type)), 20);
                    @endphp
                    <button
                        @click="activeTab = 'shared'"
                        class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-full border text-xs font-medium hover:shadow-sm transition-shadow {{ $color }}"
                        title="#{{ $slide->order }} {{ $slide->question_text ?: $slide->content_title ?: '' }}"
                    >
                        <span class="font-bold">{{ $abbr }}</span>
                        <span class="text-[10px] opacity-60">#{{ $slide->order }}</span>
                        <span class="truncate max-w-[120px]">{{ $label }}</span>
                    </button>
                @endforeach
            </div>

            {{-- Branching indicator --}}
            @php
                $lastQuestion = $phases['shared']['slides']->sortByDesc('order')->first(fn($s) => $s->slide_type === 'question');
            @endphp
            @if($lastQuestion)
                <div class="mt-3 flex items-center gap-2 text-xs text-gray-400">
                    <span class="inline-flex gap-1">
                        <span class="w-2 h-2 rounded-full bg-blue-400"></span>
                        <span class="w-2 h-2 rounded-full bg-yellow-400"></span>
                        <span class="w-2 h-2 rounded-full bg-green-400"></span>
                    </span>
                    Branches after #{{ $lastQuestion->order }}
                </div>
            @endif
        </div>

        {{-- ===== SECTION 2: Fork Connector ===== --}}
        {{-- Desktop: vertical stem + horizontal bar --}}
        <div class="hidden lg:flex justify-center my-4">
            <div class="w-px h-6 bg-gray-300"></div>
        </div>
        {{-- Mobile: simple down arrow --}}
        <div class="lg:hidden flex justify-center my-4">
            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg>
        </div>
    @endif

    {{-- ===== SECTION 3: Branch Lanes ===== --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        @foreach(['tof', 'mof', 'bof'] as $phaseKey)
            @if(isset($phases[$phaseKey]))
                @php
                    $meta   = $branchMeta[$phaseKey];
                    $slides = $phases[$phaseKey]['slides']->sortBy('order');
                    $count  = $slides->count();
                    $needsCollapse = $count > $collapseThreshold;
                    $segOutcomes = $outcomesBySegment[$phaseKey] ?? collect();
                @endphp

                <div class="rounded-xl border-2 {{ $meta['border'] }} overflow-hidden" x-data="{ expanded: false }">
                    {{-- Lane Header --}}
                    <button
                        @click="activeTab = '{{ $phaseKey }}'"
                        class="w-full px-4 py-3 {{ $meta['bg'] }} text-left hover:brightness-95 transition-all"
                    >
                        <div class="flex items-center justify-between">
                            <span class="font-semibold text-sm {{ $meta['text'] }}">{{ $meta['label'] }}</span>
                            <span class="text-xs {{ $meta['text'] }} opacity-70">{{ $count }} slides</span>
                        </div>
                        <p class="text-xs text-gray-500 mt-0.5">{{ $meta['subtitle'] }}</p>
                    </button>

                    {{-- Slide Timeline (railroad) --}}
                    <div class="px-4 py-3">
                        @php
                            if ($needsCollapse) {
                                $visibleFirst = $slides->take(4);
                                $visibleLast  = $slides->slice(-4);
                                $hidden       = $slides->slice(4, $count - 8);
                                $hiddenCount  = $hidden->count();
                            }
                        @endphp

                        <div class="space-y-0.5">
                            @php $renderSlide = function($slide) use ($slideColors, $typeAbbrev, $phaseKey) {
                                $color = $slideColors[$slide->slide_type] ?? 'bg-gray-100 text-gray-700 border-gray-200';
                                $abbr  = $typeAbbrev[$slide->slide_type] ?? '?';
                                $label = Str::limit($slide->question_text ?: $slide->content_title ?: ucfirst(str_replace('_', ' ', $slide->slide_type)), 22);
                                return compact('color', 'abbr', 'label');
                            }; @endphp

                            @foreach($needsCollapse ? $visibleFirst : $slides as $slide)
                                @php $s = $renderSlide($slide); @endphp
                                <button @click="activeTab = '{{ $phaseKey }}'" class="flex items-center gap-2 py-1 w-full text-left group">
                                    <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[10px] font-bold border {{ $s['color'] }}">{{ $s['abbr'] }}</span>
                                    <span class="text-xs text-gray-400">#{{ $slide->order }}</span>
                                    <span class="text-xs text-gray-700 truncate group-hover:text-gray-900">{{ $s['label'] }}</span>
                                </button>
                            @endforeach

                            @if($needsCollapse)
                                {{-- Collapsed indicator --}}
                                <div x-show="!expanded" class="py-1">
                                    <button @click.stop="expanded = true" class="text-xs text-gray-400 hover:text-gray-600">
                                        +{{ $hiddenCount }} more slides
                                    </button>
                                </div>

                                {{-- Hidden slides --}}
                                <template x-if="expanded">
                                    <div>
                                        @foreach($hidden as $slide)
                                            @php $s = $renderSlide($slide); @endphp
                                            <button @click="activeTab = '{{ $phaseKey }}'" class="relative flex items-center gap-2 py-1 w-full text-left group">
                                                <span class="absolute -left-5 w-3.5 h-3.5 rounded-full border-2 border-white {{ $meta['dot'] }} z-10"></span>
                                                <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[10px] font-bold border {{ $s['color'] }}">{{ $s['abbr'] }}</span>
                                                <span class="text-xs text-gray-400">#{{ $slide->order }}</span>
                                                <span class="text-xs text-gray-700 truncate group-hover:text-gray-900">{{ $s['label'] }}</span>
                                            </button>
                                        @endforeach
                                    </div>
                                </template>

                                {{-- Last 4 always visible --}}
                                @foreach($visibleLast as $slide)
                                    @php $s = $renderSlide($slide); @endphp
                                    <button @click="activeTab = '{{ $phaseKey }}'" class="relative flex items-center gap-2 py-1 w-full text-left group">
                                        <span class="absolute -left-5 w-3.5 h-3.5 rounded-full border-2 border-white {{ $meta['dot'] }} z-10"></span>
                                        <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[10px] font-bold border {{ $s['color'] }}">{{ $s['abbr'] }}</span>
                                        <span class="text-xs text-gray-400">#{{ $slide->order }}</span>
                                        <span class="text-xs text-gray-700 truncate group-hover:text-gray-900">{{ $s['label'] }}</span>
                                    </button>
                                @endforeach
                            @endif
                        </div>
                    </div>

                    {{-- Outcome Footer --}}
                    @if($segOutcomes->isNotEmpty())
                        <div class="border-t border-dashed border-gray-200 px-4 py-3 bg-gray-50/50">
                            <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider mb-2">Outcomes</p>
                            @foreach($segOutcomes as $outcome)
                                <div class="border-l-2 {{ $meta['outcomeBorder'] }} pl-2 mb-2 last:mb-0">
                                    <p class="text-xs font-semibold text-gray-800">{{ $outcome->name }}</p>
                                    @if($outcome->result_title)
                                        <p class="text-[10px] text-gray-500">{{ Str::limit($outcome->result_title, 40) }}</p>
                                    @endif
                                    @if($outcome->redirect_url)
                                        <p class="text-[10px] text-blue-500 truncate">&rarr; {{ $outcome->redirect_url }}</p>
                                    @elseif($outcome->product_link)
                                        <p class="text-[10px] text-blue-500 truncate">&rarr; {{ $outcome->product_link }}</p>
                                    @endif
                                    @if($outcome->shown_count > 0)
                                        <p class="text-[10px] text-gray-400">Shown {{ number_format($outcome->shown_count) }}x</p>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endif
        @endforeach
    </div>

    {{-- "Other" segment outcomes --}}
    @if(isset($outcomesBySegment['other']) && $outcomesBySegment['other']->isNotEmpty())
        <div class="mt-4 rounded-lg border border-gray-200 p-4">
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Other Outcomes</p>
            @foreach($outcomesBySegment['other'] as $outcome)
                <div class="border-l-2 border-l-gray-300 pl-2 mb-2 last:mb-0">
                    <p class="text-xs font-semibold text-gray-800">{{ $outcome->name }}</p>
                    @if($outcome->result_title)
                        <p class="text-[10px] text-gray-500">{{ Str::limit($outcome->result_title, 40) }}</p>
                    @endif
                </div>
            @endforeach
        </div>
    @endif
</div>
