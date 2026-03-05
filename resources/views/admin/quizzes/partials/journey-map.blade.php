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
