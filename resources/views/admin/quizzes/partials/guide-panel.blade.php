{{-- Sidebar Quick-Reference Guide --}}
<div class="card" x-data="{ guideOpen: false }">
    <button @click="guideOpen = !guideOpen" class="flex items-center justify-between w-full p-4 text-left">
        <div class="flex items-center gap-2">
            <svg class="w-4 h-4 text-brand-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
            <h3 class="text-sm font-semibold">Quiz Builder Guide</h3>
        </div>
        <svg :class="guideOpen ? 'rotate-180' : ''" class="w-5 h-5 text-gray-400 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
        </svg>
    </button>
    <div x-show="guideOpen" x-collapse>
        <div class="px-4 pb-4 space-y-4 text-xs text-gray-600">

            {{-- Slide Types --}}
            <div>
                <h4 class="font-semibold text-gray-900 mb-1.5">Slide Types</h4>
                <dl class="space-y-1">
                    <div class="flex gap-1.5"><dt class="font-medium text-gray-700 shrink-0">Question</dt><dd>— Multiple choice with scoring</dd></div>
                    <div class="flex gap-1.5"><dt class="font-medium text-gray-700 shrink-0">Text Input</dt><dd>— Free text answer</dd></div>
                    <div class="flex gap-1.5"><dt class="font-medium text-gray-700 shrink-0">Intermission</dt><dd>— Info screen, no input</dd></div>
                    <div class="flex gap-1.5"><dt class="font-medium text-gray-700 shrink-0">Loading</dt><dd>— Animated progress, auto-advances</dd></div>
                    <div class="flex gap-1.5"><dt class="font-medium text-gray-700 shrink-0">Email Capture</dt><dd>— Collects email for Klaviyo</dd></div>
                    <div class="flex gap-1.5"><dt class="font-medium text-gray-700 shrink-0">Peptide Reveal</dt><dd>— Shows recommended peptide</dd></div>
                    <div class="flex gap-1.5"><dt class="font-medium text-gray-700 shrink-0">Vendor Reveal</dt><dd>— Shows where to buy</dd></div>
                    <div class="flex gap-1.5"><dt class="font-medium text-gray-700 shrink-0">Bridge</dt><dd>— Final CTA before results</dd></div>
                </dl>
            </div>

            <hr class="border-gray-200">

            {{-- Segments --}}
            <div>
                <h4 class="font-semibold text-gray-900 mb-1.5">Segments</h4>
                <div class="space-y-1">
                    <div class="flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-blue-500 shrink-0"></span>
                        <span><span class="font-medium text-gray-700">TOF</span> = Explorer (just learning)</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-yellow-500 shrink-0"></span>
                        <span><span class="font-medium text-gray-700">MOF</span> = Researcher (comparing options)</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-green-500 shrink-0"></span>
                        <span><span class="font-medium text-gray-700">BOF</span> = Ready to Buy (high intent)</span>
                    </div>
                </div>
                <p class="text-[10px] text-gray-400 mt-1">Highest scoring bucket wins. Ties: BOF > MOF > TOF.</p>
            </div>

            <hr class="border-gray-200">

            {{-- Outcomes --}}
            <div>
                <h4 class="font-semibold text-gray-900 mb-1.5">Outcomes</h4>
                <p>First matching outcome wins. Drag to reorder priority.</p>
            </div>

            {{-- Conditions --}}
            <div>
                <h4 class="font-semibold text-gray-900 mb-1.5">Conditions</h4>
                <dl class="space-y-1">
                    <div class="flex gap-1.5"><dt class="font-medium text-gray-700 shrink-0">Always</dt><dd>— Fallback (put last)</dd></div>
                    <div class="flex gap-1.5"><dt class="font-medium text-gray-700 shrink-0">Segment</dt><dd>— Matches awareness level</dd></div>
                    <div class="flex gap-1.5"><dt class="font-medium text-gray-700 shrink-0">Answer</dt><dd>— Matches specific quiz answer</dd></div>
                </dl>
            </div>

            <hr class="border-gray-200">

            {{-- Coverage --}}
            <div>
                <h4 class="font-semibold text-gray-900 mb-1.5">Coverage Panel</h4>
                <div class="space-y-1">
                    <div class="flex items-center gap-1.5">
                        <svg class="w-3 h-3 text-green-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                        <span>Green = outcome exists, ready</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <span class="text-yellow-600 font-medium text-[10px] shrink-0">+ Add</span>
                        <span>Yellow = missing, needs an outcome</span>
                    </div>
                </div>
            </div>

            {{-- Tags --}}
            <div>
                <h4 class="font-semibold text-gray-900 mb-1.5">Tags</h4>
                <p>Labels attached to answer options. Used for Klaviyo segmentation and targeted email campaigns.</p>
            </div>

            {{-- Full Guide Link --}}
            <a href="{{ route('admin.quizzes.guide') }}" class="flex items-center gap-1 text-brand-gold hover:underline font-medium mt-2">
                View Full Guide
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
        </div>
    </div>
</div>
