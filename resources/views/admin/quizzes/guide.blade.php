<x-admin-layout title="Quiz Builder Guide">
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ url()->previous() }}" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <span>Quiz Builder Guide</span>
        </div>
    </x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        {{-- Main Content --}}
        <div class="lg:col-span-2 space-y-12">

            {{-- ============================================================ --}}
            {{-- Section 1: How the Quiz Works --}}
            {{-- ============================================================ --}}
            <section id="how-it-works">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">1. How the Quiz Works (Big Picture)</h2>
                <div class="prose prose-sm max-w-none text-gray-700 space-y-4">
                    <p>A quiz is a series of <strong>slides</strong> that the user steps through. Some slides ask questions, some show information, and one collects the user's email. Here's the flow:</p>

                    <div class="bg-gray-50 rounded-xl p-5 space-y-3">
                        <div class="flex items-start gap-3">
                            <span class="flex items-center justify-center w-7 h-7 rounded-full bg-brand-gold text-white text-xs font-bold shrink-0">1</span>
                            <p class="mt-0.5"><strong>User answers questions</strong> — each answer can add points to 3 scoring buckets: TOF, MOF, BOF.</p>
                        </div>
                        <div class="flex items-start gap-3">
                            <span class="flex items-center justify-center w-7 h-7 rounded-full bg-brand-gold text-white text-xs font-bold shrink-0">2</span>
                            <p class="mt-0.5"><strong>System totals the scores</strong> — whichever bucket is highest becomes the user's <em>segment</em>.</p>
                        </div>
                        <div class="flex items-start gap-3">
                            <span class="flex items-center justify-center w-7 h-7 rounded-full bg-brand-gold text-white text-xs font-bold shrink-0">3</span>
                            <p class="mt-0.5"><strong>System checks outcomes top-to-bottom</strong> — the <em>first matching</em> outcome is shown as the result page.</p>
                        </div>
                        <div class="flex items-start gap-3">
                            <span class="flex items-center justify-center w-7 h-7 rounded-full bg-brand-gold text-white text-xs font-bold shrink-0">4</span>
                            <p class="mt-0.5"><strong>Product recommendation</strong> — the system looks up the Results Bank to find the best peptide for that user's health goal and experience level.</p>
                        </div>
                    </div>

                    <div class="bg-blue-50 border border-blue-200 rounded-xl p-5">
                        <h4 class="font-semibold text-blue-900 mb-2">Example Walkthrough</h4>
                        <ol class="list-decimal list-inside space-y-1 text-blue-800 text-sm">
                            <li>User answers "I'm brand new to peptides" on the awareness slide &rarr; <strong>+3 TOF</strong></li>
                            <li>User answers "Fat Loss & Metabolism" on the health goal slide</li>
                            <li>After all questions, final scores: <strong>TOF=8, MOF=3, BOF=1</strong></li>
                            <li>Segment = <strong>TOF (Explorer)</strong> because TOF is highest</li>
                            <li>System checks outcomes: Outcome #1 says "when answer awareness_level = brand_new" &rarr; <strong>MATCH</strong> &rarr; shows this result page</li>
                            <li>Results Bank lookup: health_goal=fat_loss + beginner &rarr; recommends <strong>Tirzepatide</strong></li>
                        </ol>
                    </div>
                </div>
            </section>

            {{-- ============================================================ --}}
            {{-- Section 2: Quiz Types & Settings --}}
            {{-- ============================================================ --}}
            <section id="quiz-settings">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">2. Quiz Types & Settings</h2>

                <div class="space-y-6">
                    <div class="prose prose-sm max-w-none text-gray-700">
                        <h3 class="text-lg font-semibold text-gray-900">Quiz Types</h3>
                        <p>Every quiz has a <strong>type</strong> that determines how results are calculated:</p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div class="rounded-xl border-2 border-indigo-200 bg-indigo-50 p-4">
                            <h4 class="font-bold text-indigo-900 mb-2">Segmentation</h4>
                            <p class="text-xs text-indigo-800">Uses TOF/MOF/BOF scoring to determine the user's segment, then matches outcomes by segment or answer. <strong>Most common type.</strong></p>
                        </div>
                        <div class="rounded-xl border-2 border-emerald-200 bg-emerald-50 p-4">
                            <h4 class="font-bold text-emerald-900 mb-2">Product</h4>
                            <p class="text-xs text-emerald-800">Directly recommends a product based on answers. Skips segment scoring &mdash; outcomes match by answer conditions only.</p>
                        </div>
                        <div class="rounded-xl border-2 border-gray-200 bg-gray-50 p-4">
                            <h4 class="font-bold text-gray-900 mb-2">Custom</h4>
                            <p class="text-xs text-gray-600">Flexible type for non-standard quizzes. Define your own outcome logic using any combination of conditions.</p>
                        </div>
                    </div>

                    <div class="prose prose-sm max-w-none text-gray-700">
                        <h3 class="text-lg font-semibold text-gray-900">Quiz-Level Settings</h3>
                        <p>These settings apply to the entire quiz and are configured in the quiz edit page:</p>
                    </div>

                    <div class="space-y-3">
                        <div class="card p-4">
                            <h4 class="font-semibold text-gray-900">Require Email</h4>
                            <p class="text-sm text-gray-600 mt-1">When enabled, the quiz includes an email capture step. Users who have already submitted their email (via <code class="bg-gray-100 px-1 rounded text-xs">pp_email</code> cookie) automatically skip this step.</p>
                        </div>
                        <div class="card p-4">
                            <h4 class="font-semibold text-gray-900">Show Progress Bar</h4>
                            <p class="text-sm text-gray-600 mt-1">Displays a progress indicator at the top of the quiz showing how far along the user is.</p>
                        </div>
                        <div class="card p-4">
                            <h4 class="font-semibold text-gray-900">Allow Back</h4>
                            <p class="text-sm text-gray-600 mt-1">Shows a back button so users can revisit previous slides. The back button respects skip-to branches &mdash; it retraces the user's actual path, not just the slide order.</p>
                        </div>
                        <div class="card p-4">
                            <h4 class="font-semibold text-gray-900">Shuffle Options</h4>
                            <p class="text-sm text-gray-600 mt-1">Randomizes the order of answer options on question slides. Helps reduce position bias in responses.</p>
                        </div>
                    </div>

                    <div class="prose prose-sm max-w-none text-gray-700">
                        <h3 class="text-lg font-semibold text-gray-900">Design Settings</h3>
                        <p>Visual customization for the quiz player appearance:</p>
                    </div>

                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-sm">
                        <div class="bg-white rounded-lg p-3 border border-gray-200 text-center">
                            <span class="text-gray-500 text-xs">Primary Color</span>
                            <p class="font-medium mt-1">Buttons, accents</p>
                        </div>
                        <div class="bg-white rounded-lg p-3 border border-gray-200 text-center">
                            <span class="text-gray-500 text-xs">Background Color</span>
                            <p class="font-medium mt-1">Quiz background</p>
                        </div>
                        <div class="bg-white rounded-lg p-3 border border-gray-200 text-center">
                            <span class="text-gray-500 text-xs">Text Color</span>
                            <p class="font-medium mt-1">Question text</p>
                        </div>
                        <div class="bg-white rounded-lg p-3 border border-gray-200 text-center">
                            <span class="text-gray-500 text-xs">Animation</span>
                            <p class="font-medium mt-1">Slide transitions</p>
                        </div>
                    </div>
                </div>
            </section>

            {{-- ============================================================ --}}
            {{-- Section 3: Slide Types --}}
            {{-- ============================================================ --}}
            <section id="slide-types">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">3. Slide Types</h2>
                <p class="text-sm text-gray-600 mb-6">Every screen in the quiz is a "slide." Each slide has a type that determines what the user sees and what settings are available. There are <strong>9 slide types</strong> total.</p>

                <div class="space-y-4">
                    {{-- Question --}}
                    <div class="card p-5">
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 rounded-lg bg-indigo-100 flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900">Question</h3>
                                <p class="text-sm text-gray-600 mt-1">Choice question — user picks from options. Supports <strong>single-choice</strong> (pick one) and <strong>multiple-choice</strong> (pick several, with optional <code class="bg-gray-100 px-1 rounded text-xs">max_selections</code> limit). Each option can add TOF/MOF/BOF scores.</p>
                                <p class="text-xs text-gray-500 mt-2"><strong>Example:</strong> "What's your primary health goal?" with options like Fat Loss, Muscle Growth, Recovery, etc.</p>
                                <p class="text-xs text-gray-500 mt-1"><strong>Settings:</strong> Klaviyo Property, Question Type (single/multiple), Max Selections, Scores (TOF/MOF/BOF per option), Skip To, Tags, Subtext</p>
                            </div>
                        </div>
                    </div>

                    {{-- Text Input --}}
                    <div class="card p-5">
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 rounded-lg bg-purple-100 flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900">Text Input</h3>
                                <p class="text-sm text-gray-600 mt-1">Free text answer — user types a response instead of picking from options.</p>
                                <p class="text-xs text-gray-500 mt-2"><strong>Example:</strong> "What peptide are you currently using?"</p>
                                <p class="text-xs text-gray-500 mt-1"><strong>Settings:</strong> Klaviyo Property</p>
                            </div>
                        </div>
                    </div>

                    {{-- Intermission --}}
                    <div class="card p-5">
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 rounded-lg bg-sky-100 flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900">Intermission</h3>
                                <p class="text-sm text-gray-600 mt-1">Info slide with no user input. Shows a title, body text, and optional source citation.</p>
                                <p class="text-xs text-gray-500 mt-2"><strong>Example:</strong> "Did you know? Peptides are short chains of amino acids..." with a PubMed citation</p>
                                <p class="text-xs text-gray-500 mt-1"><strong>Settings:</strong> Content Title, Body, Source</p>
                            </div>
                        </div>
                    </div>

                    {{-- Loading --}}
                    <div class="card p-5">
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 rounded-lg bg-amber-100 flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900">Loading</h3>
                                <p class="text-sm text-gray-600 mt-1">Animated progress bar that auto-advances after a set number of seconds. Shows a checklist of items ticking off.</p>
                                <p class="text-xs text-gray-500 mt-2"><strong>Example:</strong> "Analyzing your answers..." with items like "Checking health profile", "Matching peptides"</p>
                                <p class="text-xs text-gray-500 mt-1"><strong>Settings:</strong> Auto-advance seconds, Content items (one per line)</p>
                            </div>
                        </div>
                    </div>

                    {{-- Email Capture --}}
                    <div class="card p-5">
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 rounded-lg bg-emerald-100 flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900">Email Capture</h3>
                                <p class="text-sm text-gray-600 mt-1">Collects the user's email address and sends it to Klaviyo.</p>
                                <p class="text-xs text-gray-500 mt-2"><strong>Example:</strong> "Get your personalized results via email"</p>
                                <p class="text-xs text-gray-500 mt-1"><strong>Settings:</strong> Content Title, Body</p>
                            </div>
                        </div>
                    </div>

                    {{-- Peptide Reveal --}}
                    <div class="card p-5">
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 rounded-lg bg-rose-100 flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900">Peptide Reveal</h3>
                                <p class="text-sm text-gray-600 mt-1">Shows the recommended peptide based on the user's answers. Uses dynamic content to change by health goal.</p>
                                <p class="text-xs text-gray-500 mt-2"><strong>Example:</strong> "Your #1 Match: BPC-157" with a description of why it's the best fit</p>
                                <p class="text-xs text-gray-500 mt-1"><strong>Settings:</strong> Dynamic Content (changes by health goal answer)</p>
                            </div>
                        </div>
                    </div>

                    {{-- Vendor Reveal --}}
                    <div class="card p-5">
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 rounded-lg bg-teal-100 flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/></svg>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900">Vendor Reveal</h3>
                                <p class="text-sm text-gray-600 mt-1">Shows where to buy the recommended peptide, with vendor comparison info.</p>
                                <p class="text-xs text-gray-500 mt-2"><strong>Example:</strong> "Where to Get Your Peptide" with pricing and vendor details</p>
                                <p class="text-xs text-gray-500 mt-1"><strong>Settings:</strong> Dynamic Content</p>
                            </div>
                        </div>
                    </div>

                    {{-- Bridge --}}
                    <div class="card p-5">
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 rounded-lg bg-orange-100 flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900">Bridge</h3>
                                <p class="text-sm text-gray-600 mt-1">Final CTA slide before results. Gives the user a clear next step.</p>
                                <p class="text-xs text-gray-500 mt-2"><strong>Example:</strong> "Ready to start? Here's your next step" with a button</p>
                                <p class="text-xs text-gray-500 mt-1"><strong>Settings:</strong> CTA Text, CTA URL</p>
                            </div>
                        </div>
                    </div>

                    {{-- Peptide Search --}}
                    <div class="card p-5">
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 rounded-lg bg-cyan-100 flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900">Peptide Search</h3>
                                <p class="text-sm text-gray-600 mt-1">Lets the user search and select a specific peptide from the full database. Used in <strong>Path 1</strong> for high-intent users who already know what they want.</p>
                                <p class="text-xs text-gray-500 mt-2"><strong>Example:</strong> "Which peptide are you interested in?" with a searchable dropdown of all peptides</p>
                                <p class="text-xs text-gray-500 mt-1"><strong>Settings:</strong> Klaviyo Property. Pulls peptides from the Stack Products database with vendor/pricing data.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            {{-- ============================================================ --}}
            {{-- Section 4: Scoring & Segments --}}
            {{-- ============================================================ --}}
            <section id="scoring">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">4. Scoring & Segments</h2>

                <div class="space-y-6">
                    <div class="prose prose-sm max-w-none text-gray-700">
                        <h3 class="text-lg font-semibold text-gray-900">What are scores?</h3>
                        <p>Every answer option on a <strong>Question</strong> slide has 3 score fields: <strong>TOF</strong>, <strong>MOF</strong>, and <strong>BOF</strong>. When a user picks that option, those points are added to their running totals.</p>
                    </div>

                    <div class="bg-gray-50 rounded-xl p-5">
                        <h4 class="font-semibold text-gray-900 mb-3">Example: Awareness Level Question</h4>
                        <div class="space-y-2">
                            <div class="flex items-center gap-3 bg-white rounded-lg p-3 border border-gray-200">
                                <span class="text-sm font-medium text-gray-700 w-48">"I'm brand new to peptides"</span>
                                <div class="flex gap-3 text-xs">
                                    <span class="px-2 py-0.5 rounded bg-blue-100 text-blue-700 font-medium">TOF +3</span>
                                    <span class="px-2 py-0.5 rounded bg-gray-100 text-gray-400">MOF +0</span>
                                    <span class="px-2 py-0.5 rounded bg-gray-100 text-gray-400">BOF +0</span>
                                </div>
                            </div>
                            <div class="flex items-center gap-3 bg-white rounded-lg p-3 border border-gray-200">
                                <span class="text-sm font-medium text-gray-700 w-48">"I've been researching"</span>
                                <div class="flex gap-3 text-xs">
                                    <span class="px-2 py-0.5 rounded bg-gray-100 text-gray-400">TOF +0</span>
                                    <span class="px-2 py-0.5 rounded bg-yellow-100 text-yellow-700 font-medium">MOF +3</span>
                                    <span class="px-2 py-0.5 rounded bg-gray-100 text-gray-400">BOF +0</span>
                                </div>
                            </div>
                            <div class="flex items-center gap-3 bg-white rounded-lg p-3 border border-gray-200">
                                <span class="text-sm font-medium text-gray-700 w-48">"I know what I want"</span>
                                <div class="flex gap-3 text-xs">
                                    <span class="px-2 py-0.5 rounded bg-gray-100 text-gray-400">TOF +0</span>
                                    <span class="px-2 py-0.5 rounded bg-gray-100 text-gray-400">MOF +0</span>
                                    <span class="px-2 py-0.5 rounded bg-green-100 text-green-700 font-medium">BOF +3</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="prose prose-sm max-w-none text-gray-700">
                        <h3 class="text-lg font-semibold text-gray-900">How segments are determined</h3>
                        <p>After all questions, the system totals each bucket. The <strong>highest bucket wins</strong>. Ties favor <strong>BOF > MOF > TOF</strong> (we assume higher intent when tied).</p>
                    </div>

                    {{-- Visual score bar --}}
                    <div class="bg-gray-50 rounded-xl p-5">
                        <h4 class="font-semibold text-gray-900 mb-3">Example: Final Scores</h4>
                        <div class="space-y-3">
                            <div class="flex items-center gap-3">
                                <span class="text-xs font-medium text-gray-600 w-12">TOF</span>
                                <div class="flex-1 bg-gray-200 rounded-full h-5 overflow-hidden">
                                    <div class="bg-blue-500 h-full rounded-full flex items-center justify-end pr-2" style="width: 20%">
                                        <span class="text-[10px] font-bold text-white">2</span>
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="text-xs font-medium text-gray-600 w-12">MOF</span>
                                <div class="flex-1 bg-gray-200 rounded-full h-5 overflow-hidden">
                                    <div class="bg-yellow-500 h-full rounded-full flex items-center justify-end pr-2" style="width: 50%">
                                        <span class="text-[10px] font-bold text-white">5</span>
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="text-xs font-medium text-gray-600 w-12">BOF</span>
                                <div class="flex-1 bg-gray-200 rounded-full h-5 overflow-hidden">
                                    <div class="bg-green-500 h-full rounded-full flex items-center justify-end pr-2" style="width: 80%">
                                        <span class="text-[10px] font-bold text-white">8</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <p class="text-sm font-medium text-green-700 mt-3">Result: Segment = <strong>BOF (Ready to Buy)</strong> — highest score wins</p>
                    </div>

                    {{-- What segments mean --}}
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div class="rounded-xl border-2 border-blue-200 bg-blue-50 p-4">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="w-3 h-3 rounded-full bg-blue-500"></span>
                                <h4 class="font-bold text-blue-900">TOF — Explorer</h4>
                            </div>
                            <p class="text-xs text-blue-800">Just learning about peptides. Low buying intent. Show <strong>educational content</strong>, build trust, explain basics.</p>
                        </div>
                        <div class="rounded-xl border-2 border-yellow-200 bg-yellow-50 p-4">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="w-3 h-3 rounded-full bg-yellow-500"></span>
                                <h4 class="font-bold text-yellow-900">MOF — Researcher</h4>
                            </div>
                            <p class="text-xs text-yellow-800">Comparing options, reading studies. Medium intent. Show <strong>comparisons, research, testimonials</strong>.</p>
                        </div>
                        <div class="rounded-xl border-2 border-green-200 bg-green-50 p-4">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="w-3 h-3 rounded-full bg-green-500"></span>
                                <h4 class="font-bold text-green-900">BOF — Ready to Buy</h4>
                            </div>
                            <p class="text-xs text-green-800">Ready to purchase. High intent. Show <strong>product recommendations, vendor links, pricing</strong>.</p>
                        </div>
                    </div>
                </div>
            </section>

            {{-- ============================================================ --}}
            {{-- Section 5: Question Options --}}
            {{-- ============================================================ --}}
            <section id="question-options">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">5. Question Options (Every Setting)</h2>
                <p class="text-sm text-gray-600 mb-6">When you create a Question slide, each answer option has these settings:</p>

                <div class="space-y-3">
                    <div class="card p-4">
                        <h4 class="font-semibold text-gray-900">Label</h4>
                        <p class="text-sm text-gray-600 mt-1">What the user sees on screen.</p>
                        <p class="text-xs text-gray-500 mt-1">Example: <code class="bg-gray-100 px-1 rounded">Fat Loss & Metabolism</code></p>
                    </div>
                    <div class="card p-4">
                        <h4 class="font-semibold text-gray-900">Value</h4>
                        <p class="text-sm text-gray-600 mt-1">Internal identifier used for condition matching. Not shown to users.</p>
                        <p class="text-xs text-gray-500 mt-1">Example: <code class="bg-gray-100 px-1 rounded">fat_loss</code></p>
                    </div>
                    <div class="card p-4">
                        <h4 class="font-semibold text-gray-900">Klaviyo Value</h4>
                        <p class="text-sm text-gray-600 mt-1">What gets sent to Klaviyo as the profile property value. Usually the same as the label, but can be customized.</p>
                        <p class="text-xs text-gray-500 mt-1">Example: <code class="bg-gray-100 px-1 rounded">Fat Loss & Metabolism</code></p>
                    </div>
                    <div class="card p-4">
                        <h4 class="font-semibold text-gray-900">Scores (TOF / MOF / BOF)</h4>
                        <p class="text-sm text-gray-600 mt-1">Points added to each segment bucket when the user picks this option. Set all to 0 if scoring doesn't matter for this question (e.g., the health goal question doesn't need to affect segment scoring).</p>
                        <p class="text-xs text-gray-500 mt-1">Example: <code class="bg-gray-100 px-1 rounded">TOF=0, MOF=0, BOF=3</code> for "I know what I want"</p>
                    </div>
                    <div class="card p-4">
                        <h4 class="font-semibold text-gray-900">Skip To</h4>
                        <p class="text-sm text-gray-600 mt-1">If the user picks this option, skip ahead to a specific slide instead of going to the next one. Used for branching paths.</p>
                        <p class="text-xs text-gray-500 mt-1">Example: User picks "I know what I want" &rarr; skip to the product selection slide, bypassing educational slides.</p>
                    </div>
                    <div class="card p-4">
                        <h4 class="font-semibold text-gray-900">Subtext</h4>
                        <p class="text-sm text-gray-600 mt-1">Small text displayed under the option label to provide additional context.</p>
                        <p class="text-xs text-gray-500 mt-1">Example: <code class="bg-gray-100 px-1 rounded">Includes GLP-1 agonists like Semaglutide</code></p>
                    </div>
                    <div class="card p-4">
                        <h4 class="font-semibold text-gray-900">Tags</h4>
                        <p class="text-sm text-gray-600 mt-1">Labels attached to this option for Klaviyo segmentation. When a user picks this option, these tags are added to their Klaviyo profile for targeted email campaigns.</p>
                        <p class="text-xs text-gray-500 mt-1">Example: <code class="bg-gray-100 px-1 rounded">["weight_management", "glp1"]</code> on the Fat Loss option</p>
                    </div>
                </div>
            </section>

            {{-- ============================================================ --}}
            {{-- Section 6: Show Conditions --}}
            {{-- ============================================================ --}}
            <section id="show-conditions">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">6. Show Conditions (Branching Logic)</h2>

                <div class="space-y-6">
                    <div class="prose prose-sm max-w-none text-gray-700">
                        <p>Show conditions control <strong>when a slide appears</strong>. If a slide has show conditions, it will only be shown to users who match those conditions. Slides without conditions are shown to everyone.</p>
                    </div>

                    <div class="bg-gray-50 rounded-xl p-5">
                        <h4 class="font-semibold text-gray-900 mb-2">AND vs OR Logic</h4>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-3">
                            <div class="bg-white rounded-lg p-3 border border-gray-200">
                                <span class="text-xs font-bold text-indigo-600 uppercase">AND</span>
                                <p class="text-sm text-gray-600 mt-1"><strong>ALL</strong> conditions must match for the slide to appear.</p>
                                <p class="text-xs text-gray-500 mt-1">Example: health_goal = fat_loss <strong>AND</strong> experience = advanced</p>
                            </div>
                            <div class="bg-white rounded-lg p-3 border border-gray-200">
                                <span class="text-xs font-bold text-emerald-600 uppercase">OR</span>
                                <p class="text-sm text-gray-600 mt-1"><strong>ANY</strong> condition can match for the slide to appear.</p>
                                <p class="text-xs text-gray-500 mt-1">Example: awareness = brand_new <strong>OR</strong> awareness = researching</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-indigo-50 border border-indigo-200 rounded-xl p-5">
                        <h4 class="font-semibold text-indigo-900 mb-2">Example: Branching Paths</h4>
                        <div class="space-y-2 text-sm text-indigo-800">
                            <p>The awareness question has 3 options: <strong>Brand New</strong>, <strong>Researching</strong>, <strong>Know What I Want</strong></p>
                            <div class="bg-white/60 rounded-lg p-3 mt-2 space-y-2">
                                <p>Slide "Tell us more about your research..." has show condition:</p>
                                <p class="font-medium">&rarr; "Show when awareness_level = researching"</p>
                                <p class="text-xs text-indigo-600">This slide ONLY appears for researchers. Brand new and know-what-I-want users skip it entirely.</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-amber-50 border border-amber-200 rounded-xl p-5">
                        <h4 class="font-semibold text-amber-900 mb-2">Example: Multi-Condition (AND)</h4>
                        <div class="text-sm text-amber-800 space-y-2">
                            <p>A slide with two AND conditions:</p>
                            <p class="font-medium">"Show when health_goal = fat_loss AND experience_level = advanced"</p>
                            <p class="text-xs text-amber-600">This slide only shows for advanced users interested in fat loss. Everyone else skips it.</p>
                        </div>
                    </div>
                </div>
            </section>

            {{-- ============================================================ --}}
            {{-- Section 7: Outcomes --}}
            {{-- ============================================================ --}}
            <section id="outcomes">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">7. Outcomes (Results Pages)</h2>

                <div class="space-y-6">
                    <div class="prose prose-sm max-w-none text-gray-700">
                        <p>An <strong>outcome</strong> is the final result page shown after quiz completion. It contains a headline, body text, and optional redirect URL. The system checks outcomes <strong>top to bottom</strong> — the first one that matches wins.</p>
                    </div>

                    {{-- Condition types --}}
                    <div class="space-y-3">
                        <h3 class="text-lg font-semibold text-gray-900">Condition Types</h3>
                        <div class="card p-4 border-l-4 border-gray-400">
                            <h4 class="font-semibold text-gray-900">Always (Default)</h4>
                            <p class="text-sm text-gray-600 mt-1">No conditions — shows if nothing else matches. <strong>Put this last</strong> as your safety net. Every quiz should have one.</p>
                        </div>
                        <div class="card p-4 border-l-4 border-blue-400">
                            <h4 class="font-semibold text-gray-900">When user's segment is...</h4>
                            <p class="text-sm text-gray-600 mt-1">Matches the user's calculated segment (TOF, MOF, or BOF).</p>
                            <p class="text-xs text-gray-500 mt-1">Example: "When segment = BOF" &rarr; shows for high-intent, ready-to-buy users.</p>
                        </div>
                        <div class="card p-4 border-l-4 border-green-400">
                            <h4 class="font-semibold text-gray-900">When user answered...</h4>
                            <p class="text-sm text-gray-600 mt-1">Matches a specific answer the user gave to a question.</p>
                            <p class="text-xs text-gray-500 mt-1">Example: "When Health Goal = Fat Loss" &rarr; shows the fat loss result page.</p>
                        </div>
                        <div class="card p-4 border-l-4 border-purple-400">
                            <h4 class="font-semibold text-gray-900">When score reaches...</h4>
                            <p class="text-sm text-gray-600 mt-1">Matches when a specific score bucket (TOF, MOF, BOF, or total) meets a minimum threshold.</p>
                            <p class="text-xs text-gray-500 mt-1">Example: "When BOF score >= 10" &rarr; shows for users with very high buying intent regardless of segment.</p>
                        </div>
                    </div>

                    {{-- Step by step --}}
                    <div class="bg-gray-50 rounded-xl p-5">
                        <h3 class="font-semibold text-gray-900 mb-3">Step-by-Step: Creating an Outcome</h3>
                        <ol class="space-y-2 text-sm text-gray-700">
                            <li class="flex gap-3">
                                <span class="flex items-center justify-center w-6 h-6 rounded-full bg-brand-gold text-white text-xs font-bold shrink-0">1</span>
                                <span>Click <strong>"+ Add"</strong> in the Outcomes section of the sidebar</span>
                            </li>
                            <li class="flex gap-3">
                                <span class="flex items-center justify-center w-6 h-6 rounded-full bg-brand-gold text-white text-xs font-bold shrink-0">2</span>
                                <span>Give it a descriptive name, e.g., <em>"Fat Loss — Beginner Result"</em></span>
                            </li>
                            <li class="flex gap-3">
                                <span class="flex items-center justify-center w-6 h-6 rounded-full bg-brand-gold text-white text-xs font-bold shrink-0">3</span>
                                <span>Pick condition: <strong>"When user answered..."</strong> &rarr; Health Goal question &rarr; Fat Loss & Metabolism</span>
                            </li>
                            <li class="flex gap-3">
                                <span class="flex items-center justify-center w-6 h-6 rounded-full bg-brand-gold text-white text-xs font-bold shrink-0">4</span>
                                <span>Fill in <strong>Headline</strong>: "Your #1 Match: Tirzepatide"</span>
                            </li>
                            <li class="flex gap-3">
                                <span class="flex items-center justify-center w-6 h-6 rounded-full bg-brand-gold text-white text-xs font-bold shrink-0">5</span>
                                <span>Fill in <strong>Body</strong>: description of why this peptide matches their goals</span>
                            </li>
                            <li class="flex gap-3">
                                <span class="flex items-center justify-center w-6 h-6 rounded-full bg-brand-gold text-white text-xs font-bold shrink-0">6</span>
                                <span>Optional: set a <strong>redirect URL</strong> to the product page</span>
                            </li>
                            <li class="flex gap-3">
                                <span class="flex items-center justify-center w-6 h-6 rounded-full bg-brand-gold text-white text-xs font-bold shrink-0">7</span>
                                <span><strong>Save</strong> &rarr; drag to the correct priority position in the sidebar</span>
                            </li>
                        </ol>
                    </div>

                    <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-5">
                        <h4 class="font-semibold text-emerald-900 mb-2">Auto-fill from Results Bank</h4>
                        <p class="text-sm text-emerald-800">When your outcome condition is a health goal, toggle <strong>"Auto-fill from Results Bank"</strong> to pull the peptide name and description automatically. This saves time and ensures consistency with your Results Bank data.</p>
                    </div>

                    <div class="bg-gray-50 rounded-xl p-5">
                        <h4 class="font-semibold text-gray-900 mb-3">Additional Outcome Fields</h4>
                        <div class="space-y-3 text-sm">
                            <div class="flex items-start gap-3">
                                <span class="font-medium text-gray-700 w-36 shrink-0">Result Image</span>
                                <span class="text-gray-600">Optional image displayed on the results page (e.g., peptide photo or infographic).</span>
                            </div>
                            <div class="flex items-start gap-3">
                                <span class="font-medium text-gray-700 w-36 shrink-0">Recommended Peptide</span>
                                <span class="text-gray-600">Link to a specific peptide from the database. Shown on the result page with full details.</span>
                            </div>
                            <div class="flex items-start gap-3">
                                <span class="font-medium text-gray-700 w-36 shrink-0">Product Link</span>
                                <span class="text-gray-600">Direct link to buy the recommended peptide from a vendor.</span>
                            </div>
                            <div class="flex items-start gap-3">
                                <span class="font-medium text-gray-700 w-36 shrink-0">Redirect URL</span>
                                <span class="text-gray-600">Optional URL to redirect users to after viewing the result (e.g., a landing page).</span>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            {{-- ============================================================ --}}
            {{-- Section 8: Outcome Coverage --}}
            {{-- ============================================================ --}}
            <section id="coverage">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">8. Outcome Coverage</h2>

                <div class="space-y-6">
                    <div class="prose prose-sm max-w-none text-gray-700">
                        <p>The <strong>Outcome Coverage</strong> panel in the sidebar shows a checklist of all segments and health goals, indicating which ones have matching outcomes.</p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="rounded-xl border-2 border-green-200 bg-green-50 p-4 flex items-start gap-3">
                            <svg class="w-5 h-5 text-green-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                            <div>
                                <h4 class="font-semibold text-green-900">Green Check</h4>
                                <p class="text-sm text-green-800 mt-1">This segment or health goal has an active outcome ready. Users who match will see a relevant result.</p>
                            </div>
                        </div>
                        <div class="rounded-xl border-2 border-yellow-200 bg-yellow-50 p-4 flex items-start gap-3">
                            <span class="text-yellow-600 font-bold text-sm shrink-0 mt-0.5">+ Add</span>
                            <div>
                                <h4 class="font-semibold text-yellow-900">Yellow "+ Add"</h4>
                                <p class="text-sm text-yellow-800 mt-1">No outcome exists for this segment/goal. Click to create one with the condition pre-filled.</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-amber-50 border border-amber-200 rounded-xl p-5">
                        <h4 class="font-semibold text-amber-900 mb-2">Why Coverage Matters</h4>
                        <p class="text-sm text-amber-800">If a user completes the quiz and <strong>no outcome matches</strong>, they see the fallback outcome (or nothing if there's no fallback). Full coverage means every user gets a relevant, personalized result.</p>
                        <p class="text-sm text-amber-800 mt-2 font-medium">Goal: All segments green + all health goals green + one "Always" fallback outcome.</p>
                    </div>
                </div>
            </section>

            {{-- ============================================================ --}}
            {{-- Section 9: Results Bank --}}
            {{-- ============================================================ --}}
            <section id="results-bank">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">9. Product Recommendations & Results Bank</h2>

                <div class="space-y-6">
                    <div class="prose prose-sm max-w-none text-gray-700">
                        <h3 class="text-lg font-semibold text-gray-900">Where products live</h3>
                        <p>The <strong>Results Bank</strong> (Admin &rarr; Marketing &rarr; Results Bank) stores one peptide per health goal per experience level. This is the master database of product recommendations.</p>

                        <h3 class="text-lg font-semibold text-gray-900 mt-4">How matching works</h3>
                        <p>When the quiz ends, the system looks up: <strong>User's health goal answer + experience level</strong> &rarr; finds the matching peptide recommendation.</p>

                        <h3 class="text-lg font-semibold text-gray-900 mt-4">Experience level mapping</h3>
                        <p><strong>TOF/MOF users = Beginner</strong> recommendations. <strong>BOF users = Advanced</strong> recommendations. This ensures beginners get simpler, well-studied peptides while experienced users get more specialized options.</p>
                    </div>

                    <div class="bg-gray-50 rounded-xl p-5">
                        <h4 class="font-semibold text-gray-900 mb-3">What Each Results Bank Entry Contains</h4>
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 text-sm">
                            <div class="bg-white rounded-lg p-3 border border-gray-200 text-center">
                                <span class="text-gray-500 text-xs">Peptide Name</span>
                                <p class="font-medium mt-1">Tirzepatide</p>
                            </div>
                            <div class="bg-white rounded-lg p-3 border border-gray-200 text-center">
                                <span class="text-gray-500 text-xs">Description</span>
                                <p class="font-medium mt-1">Why it works</p>
                            </div>
                            <div class="bg-white rounded-lg p-3 border border-gray-200 text-center">
                                <span class="text-gray-500 text-xs">Star Rating</span>
                                <p class="font-medium mt-1">4.8 / 5</p>
                            </div>
                            <div class="bg-white rounded-lg p-3 border border-gray-200 text-center">
                                <span class="text-gray-500 text-xs">Testimonial</span>
                                <p class="font-medium mt-1">User quote</p>
                            </div>
                            <div class="bg-white rounded-lg p-3 border border-gray-200 text-center">
                                <span class="text-gray-500 text-xs">Benefits List</span>
                                <p class="font-medium mt-1">Key benefits</p>
                            </div>
                            <div class="bg-white rounded-lg p-3 border border-gray-200 text-center">
                                <span class="text-gray-500 text-xs">Vendor Link</span>
                                <p class="font-medium mt-1">Where to buy</p>
                            </div>
                        </div>
                    </div>

                    {{-- Same-category exclusion --}}
                    <div class="bg-rose-50 border border-rose-200 rounded-xl p-5">
                        <h4 class="font-semibold text-rose-900 mb-2">Same-Category Exclusion (Path 3)</h4>
                        <p class="text-sm text-rose-800">If a user says "I'm already using Tirzepatide" and picks "Stay in same category," the system finds a <strong>DIFFERENT</strong> fat loss peptide (e.g., Retatrutide) instead of recommending what they're already on.</p>
                        <div class="bg-white/60 rounded-lg p-3 mt-3 space-y-2 text-sm text-rose-800">
                            <p><strong>How it works:</strong> <code class="bg-rose-100 px-1 rounded text-xs">resolveExcluding()</code> filters out the user's current peptide by slug, then finds the next best match in the same health goal category.</p>
                            <p class="text-rose-600 font-medium">Important: Each health goal needs at least 2 peptides in the Results Bank for exclusion to work. If there's only 1, the user gets no alternative recommendation.</p>
                        </div>
                    </div>

                    <div class="bg-gray-50 rounded-xl p-5">
                        <h4 class="font-semibold text-gray-900 mb-2">Product Mapping Panel (Sidebar)</h4>
                        <p class="text-sm text-gray-600">In the quiz editor sidebar, the Product Mapping panel shows which peptide is assigned to each health goal. Toggle <strong>Beginner/Advanced</strong> to see both sets. Yellow = missing, needs a Results Bank entry.</p>
                    </div>
                </div>
            </section>

            {{-- ============================================================ --}}
            {{-- Section 10: The 3 Quiz Paths --}}
            {{-- ============================================================ --}}
            <section id="quiz-paths">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">10. The 3 Quiz Paths (PeptideFinderPro)</h2>
                <p class="text-sm text-gray-600 mb-6">The PeptideFinderPro quiz uses a branching awareness question to route users into one of three paths:</p>

                <div class="space-y-4">
                    {{-- Path 1 --}}
                    <div class="card p-5 border-l-4 border-green-500">
                        <h3 class="font-semibold text-gray-900">Path 1 — "I know what I want" (BOF-A)</h3>
                        <p class="text-sm text-gray-600 mt-2">User picks their peptide directly from a list &rarr; gets that peptide's details + vendor info. No scoring needed — this is the most direct path for high-intent users.</p>
                        <div class="bg-green-50 rounded-lg p-3 mt-3">
                            <p class="text-xs text-green-700">Flow: Awareness &rarr; Pick Peptide &rarr; Peptide Reveal &rarr; Vendor Reveal &rarr; Email &rarr; Results</p>
                        </div>
                    </div>

                    {{-- Path 2 --}}
                    <div class="card p-5 border-l-4 border-blue-500">
                        <h3 class="font-semibold text-gray-900">Path 2 — "I know my goal" (BOF-B)</h3>
                        <p class="text-sm text-gray-600 mt-2">User picks their health goal &rarr; system recommends the best peptide for that goal based on their experience level.</p>
                        <div class="bg-blue-50 rounded-lg p-3 mt-3">
                            <p class="text-xs text-blue-700">Flow: Awareness &rarr; Health Goal &rarr; Experience Level &rarr; Loading &rarr; Peptide Reveal &rarr; Vendor Reveal &rarr; Email &rarr; Results</p>
                        </div>
                    </div>

                    {{-- Path 3 --}}
                    <div class="card p-5 border-l-4 border-purple-500">
                        <h3 class="font-semibold text-gray-900">Path 3 — "I want something new" (BOF-C)</h3>
                        <p class="text-sm text-gray-600 mt-2">User names their current peptide &rarr; picks same or different category &rarr; system recommends an alternative.</p>
                        <div class="bg-purple-50 rounded-lg p-3 mt-3 space-y-2">
                            <p class="text-xs text-purple-700"><strong>Same category:</strong> Excludes the user's current peptide and recommends another in the same health goal.</p>
                            <p class="text-xs text-purple-700"><strong>Different category:</strong> Asks for a new health goal, then recommends the best peptide for that goal.</p>
                        </div>
                    </div>
                </div>
            </section>

            {{-- ============================================================ --}}
            {{-- Section 10b: Building a Custom Branching Quiz (Step-by-Step) --}}
            {{-- ============================================================ --}}
            <section id="custom-quiz-guide">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">10b. Building a Custom Branching Quiz (Step-by-Step)</h2>

                <div class="space-y-6">
                    <div class="bg-amber-50 border border-amber-200 rounded-xl p-5">
                        <p class="text-sm text-amber-800"><strong>This is for you, marketing team.</strong> Custom quizzes don't use the TOF/MOF/BOF scoring system. Instead you control the flow using <strong>Skip To</strong> (on answer options) and <strong>Show Conditions</strong> (on slides). Think of it as building a choose-your-own-adventure.</p>
                    </div>

                    {{-- Step 1 --}}
                    <div class="card p-5 border-l-4 border-indigo-500">
                        <div class="flex items-center gap-2 mb-3">
                            <span class="bg-indigo-500 text-white text-xs font-bold w-6 h-6 rounded-full flex items-center justify-center">1</span>
                            <h4 class="font-bold text-gray-900">Create the quiz</h4>
                        </div>
                        <p class="text-sm text-gray-600">Go to <strong>Quizzes &rarr; Create New</strong>. Set the type to <strong>Custom</strong>. Give it a name and save. You'll land on the quiz editor.</p>
                    </div>

                    {{-- Step 2 --}}
                    <div class="card p-5 border-l-4 border-indigo-500">
                        <div class="flex items-center gap-2 mb-3">
                            <span class="bg-indigo-500 text-white text-xs font-bold w-6 h-6 rounded-full flex items-center justify-center">2</span>
                            <h4 class="font-bold text-gray-900">Plan your branching tree on paper first</h4>
                        </div>
                        <p class="text-sm text-gray-600 mb-3">Before adding slides, sketch out the paths. Example:</p>
                        <div class="bg-gray-50 rounded-lg p-4 font-mono text-xs text-gray-700 space-y-1">
                            <p>Q1: "What's your goal?" (3 options)</p>
                            <p class="pl-4">&rarr; "Fat Loss" &rarr; Q2a (fat loss questions)</p>
                            <p class="pl-4">&rarr; "Muscle" &rarr; Q2b (muscle questions)</p>
                            <p class="pl-4">&rarr; "Recovery" &rarr; Q2c (recovery questions)</p>
                            <p class="pl-8">&rarr; All paths merge at Q5 (email capture)</p>
                            <p class="pl-8">&rarr; Then show the right outcome</p>
                        </div>
                    </div>

                    {{-- Step 3 --}}
                    <div class="card p-5 border-l-4 border-indigo-500">
                        <div class="flex items-center gap-2 mb-3">
                            <span class="bg-indigo-500 text-white text-xs font-bold w-6 h-6 rounded-full flex items-center justify-center">3</span>
                            <h4 class="font-bold text-gray-900">Add the branching question (the "router")</h4>
                        </div>
                        <p class="text-sm text-gray-600 mb-3">Create a <strong>Question</strong> slide. This is the decision point. For each answer option:</p>
                        <div class="space-y-2">
                            <div class="bg-gray-50 rounded-lg p-3">
                                <p class="text-xs font-semibold text-gray-700">Option settings to fill in:</p>
                                <ul class="mt-2 space-y-1 text-xs text-gray-600">
                                    <li>&bull; <strong>Label:</strong> "Fat Loss" (what user sees)</li>
                                    <li>&bull; <strong>Value:</strong> "fat_loss" (internal key &mdash; used in show conditions later)</li>
                                    <li>&bull; <strong>Klaviyo Property</strong> (on the question): "goal" (so we can match outcomes by answer)</li>
                                    <li>&bull; <strong>Skip To:</strong> Select which slide this path should jump to</li>
                                    <li>&bull; <strong>Scores:</strong> Leave all at 0 for custom quizzes (scoring isn't used)</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    {{-- Step 4 --}}
                    <div class="card p-5 border-l-4 border-indigo-500">
                        <div class="flex items-center gap-2 mb-3">
                            <span class="bg-indigo-500 text-white text-xs font-bold w-6 h-6 rounded-full flex items-center justify-center">4</span>
                            <h4 class="font-bold text-gray-900">Add branch-specific slides with Show Conditions</h4>
                        </div>
                        <p class="text-sm text-gray-600 mb-3">Create slides that only appear for a specific path. On each slide:</p>
                        <div class="bg-gray-50 rounded-lg p-4 space-y-3">
                            <div>
                                <p class="text-xs font-semibold text-gray-700">Using "Skip To" (on the router question):</p>
                                <p class="text-xs text-gray-600 mt-1">If "Fat Loss" should jump to slide #4, set <strong>Skip To = slide #4</strong> on the Fat Loss option. Slides #2 and #3 will be skipped for that user.</p>
                            </div>
                            <div class="border-t pt-3">
                                <p class="text-xs font-semibold text-gray-700">Using "Show Conditions" (on the target slide):</p>
                                <p class="text-xs text-gray-600 mt-1">On slide #4, add a show condition: <strong>"Show when [Router Question] = fat_loss"</strong>. Now this slide is hidden from users who picked "Muscle" or "Recovery".</p>
                            </div>
                            <div class="bg-cyan-50 border border-cyan-200 rounded-lg p-3">
                                <p class="text-xs text-cyan-800"><strong>When to use which?</strong></p>
                                <p class="text-xs text-cyan-700 mt-1">&bull; <strong>Skip To</strong> = "Jump ahead to this slide" (skips everything in between)</p>
                                <p class="text-xs text-cyan-700">&bull; <strong>Show Conditions</strong> = "Only show this slide IF..." (the slide is in sequence but hidden from wrong paths)</p>
                                <p class="text-xs text-cyan-700 mt-1">You can use <strong>both together</strong> for maximum control.</p>
                            </div>
                        </div>
                    </div>

                    {{-- Step 5 --}}
                    <div class="card p-5 border-l-4 border-indigo-500">
                        <div class="flex items-center gap-2 mb-3">
                            <span class="bg-indigo-500 text-white text-xs font-bold w-6 h-6 rounded-full flex items-center justify-center">5</span>
                            <h4 class="font-bold text-gray-900">Create outcomes for each path</h4>
                        </div>
                        <p class="text-sm text-gray-600 mb-3">Since custom quizzes don't use segments, use <strong>"When user answered..."</strong> conditions on your outcomes:</p>
                        <div class="space-y-2">
                            <div class="bg-gray-50 rounded-lg p-3 flex items-center gap-3">
                                <span class="text-xs font-bold text-green-600 w-24 shrink-0">Outcome 1</span>
                                <span class="text-xs text-gray-600">"When goal = fat_loss" &rarr; Fat Loss result page</span>
                            </div>
                            <div class="bg-gray-50 rounded-lg p-3 flex items-center gap-3">
                                <span class="text-xs font-bold text-blue-600 w-24 shrink-0">Outcome 2</span>
                                <span class="text-xs text-gray-600">"When goal = muscle" &rarr; Muscle result page</span>
                            </div>
                            <div class="bg-gray-50 rounded-lg p-3 flex items-center gap-3">
                                <span class="text-xs font-bold text-purple-600 w-24 shrink-0">Outcome 3</span>
                                <span class="text-xs text-gray-600">"When goal = recovery" &rarr; Recovery result page</span>
                            </div>
                            <div class="bg-gray-50 rounded-lg p-3 flex items-center gap-3">
                                <span class="text-xs font-bold text-gray-500 w-24 shrink-0">Fallback</span>
                                <span class="text-xs text-gray-600">"Always (default)" &rarr; Generic result page. <strong>Always add this last!</strong></span>
                            </div>
                        </div>
                        <p class="text-xs text-gray-500 mt-3">The "question" in the outcome condition dropdown should be the Klaviyo Property name you set in step 3 (e.g., "goal"). The "value" is the option value (e.g., "fat_loss").</p>
                    </div>

                    {{-- Step 6 --}}
                    <div class="card p-5 border-l-4 border-indigo-500">
                        <div class="flex items-center gap-2 mb-3">
                            <span class="bg-indigo-500 text-white text-xs font-bold w-6 h-6 rounded-full flex items-center justify-center">6</span>
                            <h4 class="font-bold text-gray-900">Test with the simulator</h4>
                        </div>
                        <p class="text-sm text-gray-600">Use the <strong>Quiz Simulator</strong> in the sidebar to walk through each path. It shows which slides are skipped and which outcome gets matched. Test every branch before going live.</p>
                    </div>

                    {{-- Visual example --}}
                    <div class="bg-indigo-50 border border-indigo-200 rounded-xl p-5">
                        <h4 class="font-semibold text-indigo-900 mb-3">Full Example: 3-Path Custom Quiz</h4>
                        <div class="font-mono text-xs text-indigo-800 space-y-1">
                            <p><strong>Slide #1</strong> — Question: "What's your goal?" (klaviyo_property: "goal")</p>
                            <p class="pl-4">Option "Fat Loss" (value: fat_loss, skip_to: slide #2)</p>
                            <p class="pl-4">Option "Muscle" (value: muscle, skip_to: slide #4)</p>
                            <p class="pl-4">Option "Recovery" (value: recovery, skip_to: slide #6)</p>
                            <p class="mt-2"><strong>Slide #2</strong> — Question: "Diet type?" (show_condition: goal = fat_loss)</p>
                            <p><strong>Slide #3</strong> — Intermission: "Great choice!" (show_condition: goal = fat_loss)</p>
                            <p><strong>Slide #4</strong> — Question: "Training style?" (show_condition: goal = muscle)</p>
                            <p><strong>Slide #5</strong> — Intermission: "Building muscle!" (show_condition: goal = muscle)</p>
                            <p><strong>Slide #6</strong> — Question: "Injury type?" (show_condition: goal = recovery)</p>
                            <p><strong>Slide #7</strong> — Intermission: "Healing matters!" (show_condition: goal = recovery)</p>
                            <p class="mt-2"><strong>Slide #8</strong> — Email Capture (no conditions — all paths see this)</p>
                            <p class="mt-2"><strong>Outcomes:</strong></p>
                            <p class="pl-4">Priority 1: "When goal = fat_loss" &rarr; Fat Loss Results</p>
                            <p class="pl-4">Priority 2: "When goal = muscle" &rarr; Muscle Results</p>
                            <p class="pl-4">Priority 3: "When goal = recovery" &rarr; Recovery Results</p>
                            <p class="pl-4">Priority 4: "Always" &rarr; Generic Results (safety net)</p>
                        </div>
                    </div>

                    {{-- Common mistakes --}}
                    <div class="bg-red-50 border border-red-200 rounded-xl p-5">
                        <h4 class="font-semibold text-red-900 mb-3">Common Mistakes</h4>
                        <ul class="space-y-2 text-sm text-red-800">
                            <li>&bull; <strong>No "Always" outcome</strong> — If no outcome matches, the user sees a generic "Thank you" page. Always add a fallback outcome as your last priority.</li>
                            <li>&bull; <strong>Missing show conditions on branch slides</strong> — Without conditions, ALL users see ALL slides, even ones meant for other paths.</li>
                            <li>&bull; <strong>Forgot to set Klaviyo Property</strong> — The router question MUST have a Klaviyo Property set (e.g., "goal") so outcomes can match by answer. Without it, "When user answered..." conditions can't find the answer.</li>
                            <li>&bull; <strong>Skip To pointing backward</strong> — Skip To can only jump forward. It can't loop back to a previous slide.</li>
                            <li>&bull; <strong>Using scores in custom quizzes</strong> — TOF/MOF/BOF scores still work but segment-based outcomes won't do anything useful in custom quizzes. Use answer-based outcomes instead.</li>
                        </ul>
                    </div>
                </div>
            </section>

            {{-- ============================================================ --}}
            {{-- Section 10c: Building a Segmentation Quiz from Scratch --}}
            {{-- ============================================================ --}}
            <section id="segmentation-guide">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">10c. Building a Segmentation Quiz from Scratch</h2>

                <div class="space-y-6">
                    <div class="prose prose-sm max-w-none text-gray-700">
                        <p>A <strong>segmentation quiz</strong> classifies each user as <span class="text-blue-600 font-semibold">TOF (Explorer)</span>, <span class="text-yellow-600 font-semibold">MOF (Researcher)</span>, or <span class="text-green-600 font-semibold">BOF (Ready to Buy)</span> and shows them a tailored outcome. There are two ways to build one.</p>
                    </div>

                    {{-- Approach A: Simple Scoring --}}
                    <div class="bg-green-50 border border-green-200 rounded-xl p-5">
                        <div class="flex items-center gap-2 mb-3">
                            <span class="px-2 py-0.5 rounded bg-green-200 text-green-800 text-xs font-bold">APPROACH A</span>
                            <h3 class="text-lg font-semibold text-green-900">Simple Scoring (Recommended)</h3>
                        </div>
                        <p class="text-sm text-green-800 mb-4">Everyone sees the same questions. Each answer adds TOF/MOF/BOF points. The segment with the highest total score determines which outcome the user gets.</p>

                        <div class="space-y-4">
                            <div class="bg-white rounded-lg border border-green-200 p-4">
                                <h4 class="font-semibold text-gray-900 mb-2">Step 1: Create your quiz</h4>
                                <ol class="list-decimal list-inside space-y-1 text-sm text-gray-700">
                                    <li>Go to <strong>Quizzes &rarr; + New Quiz</strong></li>
                                    <li>Set <strong>Type = Segmentation</strong></li>
                                    <li>Give it a name and slug, save</li>
                                </ol>
                            </div>

                            <div class="bg-white rounded-lg border border-green-200 p-4">
                                <h4 class="font-semibold text-gray-900 mb-2">Step 2: Add 3-5 question slides</h4>
                                <ol class="list-decimal list-inside space-y-1 text-sm text-gray-700">
                                    <li>Click <strong>"+ Add Slide"</strong> &rarr; choose <strong>Question</strong></li>
                                    <li>Write your question (e.g., "What best describes you?")</li>
                                    <li>Add 3-4 answer options</li>
                                    <li>For each answer, set the <strong>Funnel Scoring</strong> (the TOF/MOF/BOF number inputs visible below each option)</li>
                                </ol>
                                <div class="mt-3 border-t pt-3">
                                    <p class="text-xs font-semibold text-gray-600 mb-2">Scoring cheat sheet:</p>
                                    <table class="w-full text-xs">
                                        <thead>
                                            <tr class="text-left border-b">
                                                <th class="pb-1">Answer feels like...</th>
                                                <th class="pb-1">Set this score</th>
                                            </tr>
                                        </thead>
                                        <tbody class="text-gray-600">
                                            <tr><td class="py-1">"I'm new / just exploring / curious"</td><td><span class="text-blue-600 font-bold">TOF = 2-3</span>, others = 0</td></tr>
                                            <tr><td class="py-1">"I'm researching / comparing / learning"</td><td><span class="text-yellow-600 font-bold">MOF = 2-3</span>, others = 0</td></tr>
                                            <tr><td class="py-1">"I know what I want / ready to buy"</td><td><span class="text-green-600 font-bold">BOF = 2-3</span>, others = 0</td></tr>
                                            <tr><td class="py-1">Ambiguous / could be multiple</td><td>Split points (e.g., TOF=1, MOF=2)</td></tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="bg-white rounded-lg border border-green-200 p-4">
                                <h4 class="font-semibold text-gray-900 mb-2">Step 3: Create outcomes for each segment</h4>
                                <ol class="list-decimal list-inside space-y-1 text-sm text-gray-700">
                                    <li>In the <strong>Outcomes panel</strong> (right sidebar), click <strong>"+ Add"</strong></li>
                                    <li>Pick <strong>"When user's segment is..."</strong></li>
                                    <li>Select <strong>TOF</strong> (Explorer card)</li>
                                    <li>Set the headline, body text, and optional redirect URL</li>
                                    <li>Repeat for <strong>MOF</strong> and <strong>BOF</strong></li>
                                    <li>Add one more outcome with <strong>"Always (default)"</strong> as a fallback &mdash; drag it to the bottom</li>
                                </ol>
                            </div>

                            <div class="bg-white rounded-lg border border-green-200 p-4">
                                <h4 class="font-semibold text-gray-900 mb-2">Step 4: Test with Preview</h4>
                                <ol class="list-decimal list-inside space-y-1 text-sm text-gray-700">
                                    <li>Click <strong>"Preview Quiz"</strong> (top right)</li>
                                    <li>Answer all questions picking "beginner" answers &rarr; should see the TOF outcome</li>
                                    <li>Reset &rarr; answer with "ready to buy" answers &rarr; should see the BOF outcome</li>
                                    <li>Check that the segment indicator matches expectations</li>
                                </ol>
                            </div>
                        </div>
                    </div>

                    {{-- Approach B: Branching Paths --}}
                    <div class="bg-blue-50 border border-blue-200 rounded-xl p-5">
                        <div class="flex items-center gap-2 mb-3">
                            <span class="px-2 py-0.5 rounded bg-blue-200 text-blue-800 text-xs font-bold">APPROACH B</span>
                            <h3 class="text-lg font-semibold text-blue-900">Branching Paths (Advanced)</h3>
                        </div>
                        <p class="text-sm text-blue-800 mb-4">Different users see <strong>different slides</strong> based on an early answer. Creates separate question paths for TOF/MOF/BOF users, like quiz 2 (PeptideFinderPro). Use this when you want to ask beginners different questions than experienced users.</p>

                        <div class="space-y-4">
                            <div class="bg-white rounded-lg border border-blue-200 p-4">
                                <h4 class="font-semibold text-gray-900 mb-2">Step 1: Create the branching question</h4>
                                <ol class="list-decimal list-inside space-y-1 text-sm text-gray-700">
                                    <li>Add a Question slide as your <strong>first slide</strong></li>
                                    <li>This is the "fork in the road" &mdash; e.g., "Where are you in your peptide journey?"</li>
                                    <li>Give each answer a clear value and a high score in its matching segment:</li>
                                </ol>
                                <div class="mt-2 bg-gray-50 rounded p-3 text-xs font-mono space-y-1">
                                    <p>"Brand new" &rarr; value: <code>brand_new</code>, <span class="text-blue-600">TOF = 3</span></p>
                                    <p>"Researching" &rarr; value: <code>researching</code>, <span class="text-yellow-600">MOF = 3</span></p>
                                    <p>"Ready to buy" &rarr; value: <code>ready_to_buy</code>, <span class="text-green-600">BOF = 3</span></p>
                                </div>
                            </div>

                            <div class="bg-white rounded-lg border border-blue-200 p-4">
                                <h4 class="font-semibold text-gray-900 mb-2">Step 2: Add path-specific slides</h4>
                                <ol class="list-decimal list-inside space-y-1 text-sm text-gray-700">
                                    <li>Add a new Question slide for TOF users</li>
                                    <li>Open <strong>Advanced Settings</strong> (bottom of the slide editor)</li>
                                    <li>Under <strong>"Show this slide when..."</strong>, click <strong>"+ Add Rule"</strong></li>
                                    <li>Select your branching question &rarr; select "Brand new"</li>
                                    <li>Save. This slide will <strong>only</strong> appear to users who picked "Brand new"</li>
                                    <li>Repeat with different conditions for MOF and BOF slides</li>
                                </ol>
                                <p class="text-xs text-blue-600 mt-2"><strong>Tip:</strong> Slides without any conditions appear to <strong>everyone</strong> (shared start). Only add conditions to slides you want path-specific.</p>
                            </div>

                            <div class="bg-white rounded-lg border border-blue-200 p-4">
                                <h4 class="font-semibold text-gray-900 mb-2">Step 3: Watch the Journey Map update</h4>
                                <p class="text-sm text-gray-700">Once you set show conditions, the Journey Map on the edit page will automatically group your slides into TOF/MOF/BOF lanes. This is a visual preview of the branching — slides with no conditions go to "Shared Start".</p>
                            </div>

                            <div class="bg-white rounded-lg border border-blue-200 p-4">
                                <h4 class="font-semibold text-gray-900 mb-2">Step 4: Add outcomes (same as Approach A)</h4>
                                <p class="text-sm text-gray-700">Create segment-based outcomes for TOF, MOF, BOF, and a fallback. The scoring from both shared and path-specific answers determines the final segment.</p>
                            </div>
                        </div>
                    </div>

                    {{-- When to use which --}}
                    <div class="bg-gray-50 border border-gray-200 rounded-xl p-5">
                        <h3 class="font-semibold text-gray-900 mb-3">When to use which?</h3>
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b text-left">
                                    <th class="pb-2">Use case</th>
                                    <th class="pb-2">Approach</th>
                                </tr>
                            </thead>
                            <tbody class="text-gray-700">
                                <tr class="border-b border-gray-100"><td class="py-2">Quick "find my level" quiz (3-5 questions)</td><td class="py-2"><span class="text-green-600 font-semibold">Simple Scoring</span></td></tr>
                                <tr class="border-b border-gray-100"><td class="py-2">Same questions for all, different outcome</td><td class="py-2"><span class="text-green-600 font-semibold">Simple Scoring</span></td></tr>
                                <tr class="border-b border-gray-100"><td class="py-2">Full-length quiz with different question paths</td><td class="py-2"><span class="text-blue-600 font-semibold">Branching Paths</span></td></tr>
                                <tr><td class="py-2">Beginners get education, buyers get product recs</td><td class="py-2"><span class="text-blue-600 font-semibold">Branching Paths</span></td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>

            {{-- ============================================================ --}}
            {{-- Section 11: Dynamic Content --}}
            {{-- ============================================================ --}}
            <section id="dynamic-content">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">11. Dynamic Content</h2>

                <div class="space-y-6">
                    <div class="prose prose-sm max-w-none text-gray-700">
                        <p><strong>Dynamic content</strong> lets a slide's title and body change based on a previous answer. Instead of creating 10 separate slides for 10 health goals, you create one slide with 10 content variants.</p>
                    </div>

                    <div class="bg-indigo-50 border border-indigo-200 rounded-xl p-5">
                        <h4 class="font-semibold text-indigo-900 mb-2">Example</h4>
                        <p class="text-sm text-indigo-800">The Peptide Reveal slide has dynamic content keyed on <code class="bg-indigo-100 px-1 rounded text-xs">health_goal</code>:</p>
                        <div class="mt-3 space-y-2">
                            <div class="bg-white/60 rounded-lg p-3 flex items-center gap-3">
                                <span class="text-xs font-medium text-indigo-600 w-24 shrink-0">fat_loss</span>
                                <span class="text-sm text-indigo-800">"Your Match: <strong>Tirzepatide</strong>"</span>
                            </div>
                            <div class="bg-white/60 rounded-lg p-3 flex items-center gap-3">
                                <span class="text-xs font-medium text-indigo-600 w-24 shrink-0">muscle_growth</span>
                                <span class="text-sm text-indigo-800">"Your Match: <strong>CJC-1295</strong>"</span>
                            </div>
                            <div class="bg-white/60 rounded-lg p-3 flex items-center gap-3">
                                <span class="text-xs font-medium text-indigo-600 w-24 shrink-0">_default</span>
                                <span class="text-sm text-indigo-800">"Your Match: <strong>BPC-157</strong>" (fallback)</span>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 rounded-xl p-5">
                        <h4 class="font-semibold text-gray-900 mb-3">How to Set It Up</h4>
                        <ol class="space-y-2 text-sm text-gray-700">
                            <li class="flex gap-3">
                                <span class="flex items-center justify-center w-6 h-6 rounded-full bg-brand-gold text-white text-xs font-bold shrink-0">1</span>
                                <span>Set <strong>"Dynamic Content Key"</strong> to the klaviyo_property you want to react to (e.g., <code class="bg-gray-200 px-1 rounded text-xs">health_goal</code>)</span>
                            </li>
                            <li class="flex gap-3">
                                <span class="flex items-center justify-center w-6 h-6 rounded-full bg-brand-gold text-white text-xs font-bold shrink-0">2</span>
                                <span>Add <strong>variants</strong>: for each possible answer value, set a custom title and body</span>
                            </li>
                            <li class="flex gap-3">
                                <span class="flex items-center justify-center w-6 h-6 rounded-full bg-brand-gold text-white text-xs font-bold shrink-0">3</span>
                                <span>Add a <code class="bg-gray-200 px-1 rounded text-xs">_default</code> variant as a fallback in case the answer doesn't match any variant</span>
                            </li>
                        </ol>
                    </div>
                </div>
            </section>

            {{-- ============================================================ --}}
            {{-- Section 12: Klaviyo Integration --}}
            {{-- ============================================================ --}}
            <section id="klaviyo">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">12. Klaviyo Integration</h2>

                <div class="space-y-6">
                    <div class="prose prose-sm max-w-none text-gray-700">
                        <p>The quiz integrates with Klaviyo at <strong>three levels</strong>: quiz-wide settings, per-question properties, and per-outcome events. This is how quiz data flows into your email marketing.</p>
                    </div>

                    <div class="space-y-3">
                        <h3 class="text-lg font-semibold text-gray-900">Quiz-Level</h3>
                        <div class="card p-4">
                            <h4 class="font-semibold text-gray-900">Klaviyo List ID</h4>
                            <p class="text-sm text-gray-600 mt-1">The Klaviyo list that quiz completers are added to. Set this on the quiz edit page.</p>
                        </div>
                        <div class="card p-4">
                            <h4 class="font-semibold text-gray-900">Start Event / Complete Event</h4>
                            <p class="text-sm text-gray-600 mt-1">Custom Klaviyo event names fired when a user starts or completes the quiz. Useful for triggering automated flows.</p>
                            <p class="text-xs text-gray-500 mt-1">Example: <code class="bg-gray-100 px-1 rounded">Quiz Started - PeptideFinderPro</code>, <code class="bg-gray-100 px-1 rounded">Quiz Completed - PeptideFinderPro</code></p>
                        </div>
                    </div>

                    <div class="space-y-3">
                        <h3 class="text-lg font-semibold text-gray-900">Question-Level</h3>
                        <div class="card p-4">
                            <h4 class="font-semibold text-gray-900">Klaviyo Property</h4>
                            <p class="text-sm text-gray-600 mt-1">Each question slide has a <code class="bg-gray-100 px-1 rounded text-xs">klaviyo_property</code> that becomes the profile property name in Klaviyo. The user's answer becomes the value.</p>
                            <p class="text-xs text-gray-500 mt-1">Example: Question with property <code class="bg-gray-100 px-1 rounded">health_goal</code> + user picks "Fat Loss" &rarr; Klaviyo profile gets <code class="bg-gray-100 px-1 rounded">health_goal = Fat Loss & Metabolism</code></p>
                        </div>
                        <div class="card p-4">
                            <h4 class="font-semibold text-gray-900">Klaviyo Value (per option)</h4>
                            <p class="text-sm text-gray-600 mt-1">Override what gets sent to Klaviyo for a specific option. By default the label is sent, but you can set a custom value (e.g., send "Fat Loss" instead of "Fat Loss & Metabolism").</p>
                        </div>
                    </div>

                    <div class="space-y-3">
                        <h3 class="text-lg font-semibold text-gray-900">Outcome-Level</h3>
                        <div class="card p-4">
                            <h4 class="font-semibold text-gray-900">Outcome Klaviyo Event</h4>
                            <p class="text-sm text-gray-600 mt-1">Fire a custom event when this specific outcome is shown. Useful for outcome-specific email flows.</p>
                        </div>
                        <div class="card p-4">
                            <h4 class="font-semibold text-gray-900">Outcome Klaviyo Properties</h4>
                            <p class="text-sm text-gray-600 mt-1">Extra key-value pairs added to the user's Klaviyo profile when this outcome is shown (e.g., <code class="bg-gray-100 px-1 rounded text-xs">recommended_peptide = BPC-157</code>).</p>
                        </div>
                        <div class="card p-4">
                            <h4 class="font-semibold text-gray-900">Outcome Klaviyo List ID</h4>
                            <p class="text-sm text-gray-600 mt-1">Override the quiz-level list &mdash; add users who get this specific outcome to a different Klaviyo list.</p>
                        </div>
                    </div>

                    <div class="bg-blue-50 border border-blue-200 rounded-xl p-5">
                        <h4 class="font-semibold text-blue-900 mb-2">Tags &rarr; Klaviyo</h4>
                        <p class="text-sm text-blue-800">Tags on answer options are also sent to Klaviyo as profile properties. When a user picks an option with tags like <code class="bg-blue-100 px-1 rounded text-xs">["weight_management", "glp1"]</code>, those tags are added to their Klaviyo profile for segmentation and targeted campaigns.</p>
                    </div>
                </div>
            </section>

            {{-- ============================================================ --}}
            {{-- Section 13: Session & Email Behavior --}}
            {{-- ============================================================ --}}
            <section id="session-behavior">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">13. Session & Email Behavior</h2>

                <div class="space-y-6">
                    <div class="space-y-3">
                        <div class="card p-4">
                            <h4 class="font-semibold text-gray-900">Session Resume</h4>
                            <p class="text-sm text-gray-600 mt-1">If a user abandons the quiz partway through, they can <strong>resume where they left off</strong> within 24 hours. The system tracks progress via a session cookie and restores their answers and navigation history.</p>
                        </div>
                        <div class="card p-4">
                            <h4 class="font-semibold text-gray-900">Email Auto-Skip</h4>
                            <p class="text-sm text-gray-600 mt-1">If a user has already submitted their email (stored in the <code class="bg-gray-100 px-1 rounded text-xs">pp_email</code> cookie from a previous quiz or form), the email capture slide is <strong>automatically skipped</strong>. No need to ask twice.</p>
                        </div>
                        <div class="card p-4">
                            <h4 class="font-semibold text-gray-900">UTM Tracking</h4>
                            <p class="text-sm text-gray-600 mt-1">UTM parameters (<code class="bg-gray-100 px-1 rounded text-xs">utm_source</code>, <code class="bg-gray-100 px-1 rounded text-xs">utm_medium</code>, <code class="bg-gray-100 px-1 rounded text-xs">utm_campaign</code>) are automatically captured from the URL and stored with the quiz response for attribution tracking.</p>
                        </div>
                        <div class="card p-4">
                            <h4 class="font-semibold text-gray-900">Segment Cookie</h4>
                            <p class="text-sm text-gray-600 mt-1">After quiz completion, the user's segment (TOF/MOF/BOF) is stored in a <code class="bg-gray-100 px-1 rounded text-xs">pp_segment</code> cookie for 30 days. This allows other parts of the site to personalize content based on the user's quiz result.</p>
                        </div>
                    </div>
                </div>
            </section>

            {{-- ============================================================ --}}
            {{-- Section 14: Warning Badges & Troubleshooting --}}
            {{-- ============================================================ --}}
            <section id="warnings">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">14. Warning Badges & Troubleshooting</h2>

                <div class="space-y-3">
                    <div class="card p-4 flex items-start gap-3">
                        <div class="w-8 h-8 rounded-lg bg-orange-100 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-900">Orange triangle on a slide</h4>
                            <p class="text-sm text-gray-600 mt-1">A show condition or skip-to link references a slide that was deleted.</p>
                            <p class="text-xs text-gray-500 mt-1"><strong>Fix:</strong> Edit the slide, update or remove the broken condition/skip-to reference.</p>
                        </div>
                    </div>

                    <div class="card p-4 flex items-start gap-3">
                        <div class="w-8 h-8 rounded-lg bg-orange-100 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-900">Orange triangle on an outcome</h4>
                            <p class="text-sm text-gray-600 mt-1">The answer condition references a klaviyo_property that no slide currently has.</p>
                            <p class="text-xs text-gray-500 mt-1"><strong>Fix:</strong> Either re-create the missing slide or change the outcome condition to match an existing slide's property.</p>
                        </div>
                    </div>

                    <div class="card p-4 flex items-start gap-3">
                        <div class="w-8 h-8 rounded-lg bg-gray-200 flex items-center justify-center shrink-0">
                            <span class="text-[10px] font-bold text-gray-500">OFF</span>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-900">"Inactive" badge</h4>
                            <p class="text-sm text-gray-600 mt-1">This outcome exists but won't show to users. It's disabled.</p>
                            <p class="text-xs text-gray-500 mt-1"><strong>Fix:</strong> Edit the outcome and toggle <code class="bg-gray-100 px-1 rounded">is_active</code> to enable it.</p>
                        </div>
                    </div>

                    <div class="card p-4 flex items-start gap-3">
                        <div class="w-8 h-8 rounded-lg bg-red-100 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-900">Cascade delete warning</h4>
                            <p class="text-sm text-gray-600 mt-1">When you try to delete a slide, the system checks if other slides or outcomes depend on it. If so, you'll see a warning listing everything that will break.</p>
                            <p class="text-xs text-gray-500 mt-1"><strong>What to do:</strong> You can still delete, but fix the broken references on the affected slides/outcomes afterward.</p>
                        </div>
                    </div>
                </div>
            </section>

            {{-- ============================================================ --}}
            {{-- Section 15: Analytics & Admin Tools --}}
            {{-- ============================================================ --}}
            <section id="admin-tools">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">15. Analytics & Admin Tools</h2>

                <div class="space-y-6">
                    <div class="prose prose-sm max-w-none text-gray-700">
                        <h3 class="text-lg font-semibold text-gray-900">Quiz Analytics</h3>
                        <p>Each quiz has a dedicated analytics page (click <strong>"Analytics"</strong> on the quiz list or editor). It shows:</p>
                    </div>

                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 text-sm">
                        <div class="bg-white rounded-lg p-3 border border-gray-200 text-center">
                            <span class="text-gray-500 text-xs">Total Started</span>
                            <p class="font-medium mt-1">How many users began</p>
                        </div>
                        <div class="bg-white rounded-lg p-3 border border-gray-200 text-center">
                            <span class="text-gray-500 text-xs">Completion Rate</span>
                            <p class="font-medium mt-1">% who finished</p>
                        </div>
                        <div class="bg-white rounded-lg p-3 border border-gray-200 text-center">
                            <span class="text-gray-500 text-xs">Abandonment Rate</span>
                            <p class="font-medium mt-1">% who left early</p>
                        </div>
                        <div class="bg-white rounded-lg p-3 border border-gray-200 text-center">
                            <span class="text-gray-500 text-xs">Avg Duration</span>
                            <p class="font-medium mt-1">Time to complete</p>
                        </div>
                        <div class="bg-white rounded-lg p-3 border border-gray-200 text-center">
                            <span class="text-gray-500 text-xs">Drop-off Per Slide</span>
                            <p class="font-medium mt-1">Where users quit</p>
                        </div>
                        <div class="bg-white rounded-lg p-3 border border-gray-200 text-center">
                            <span class="text-gray-500 text-xs">Segment Breakdown</span>
                            <p class="font-medium mt-1">TOF vs MOF vs BOF</p>
                        </div>
                    </div>

                    <div class="bg-gray-50 rounded-xl p-5">
                        <h4 class="font-semibold text-gray-900 mb-3">Also Includes</h4>
                        <ul class="text-sm text-gray-600 space-y-1.5">
                            <li class="flex items-start gap-2">
                                <span class="text-brand-gold mt-1">&bull;</span>
                                <span><strong>Outcome distribution</strong> &mdash; which result pages users are seeing most</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="text-brand-gold mt-1">&bull;</span>
                                <span><strong>Avg questions before abandon</strong> &mdash; how far users get before quitting</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="text-brand-gold mt-1">&bull;</span>
                                <span><strong>Recent responses</strong> &mdash; individual user sessions with their answers</span>
                            </li>
                        </ul>
                    </div>

                    <div class="prose prose-sm max-w-none text-gray-700">
                        <h3 class="text-lg font-semibold text-gray-900">Duplicate Quiz</h3>
                        <p>Click the <strong>"Duplicate"</strong> button on any quiz to create a full copy including all slides, options, scores, conditions, and outcomes. The copy starts as <strong>inactive</strong> with zeroed-out stats. Useful for creating variations or A/B tests without rebuilding from scratch.</p>
                    </div>
                </div>
            </section>

            {{-- ============================================================ --}}
            {{-- Section 16: Quiz Preview --}}
            {{-- ============================================================ --}}
            <section id="preview">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">16. Quiz Preview (Simulator)</h2>

                <div class="space-y-6">
                    <div class="prose prose-sm max-w-none text-gray-700">
                        <p>The quiz simulator lets you test the full quiz flow without saving any real data. Click the <strong>"Preview Quiz"</strong> button in the top right corner of the quiz editor to open it.</p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="card p-4">
                            <h4 class="font-semibold text-gray-900 mb-2">Admin Panel (Left Side)</h4>
                            <ul class="text-sm text-gray-600 space-y-1.5">
                                <li class="flex items-start gap-2">
                                    <span class="text-brand-gold mt-1">&bull;</span>
                                    <span>Current slide info and type</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <span class="text-brand-gold mt-1">&bull;</span>
                                    <span>Running segment scores (TOF/MOF/BOF)</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <span class="text-brand-gold mt-1">&bull;</span>
                                    <span>Live outcome preview (which would match right now)</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <span class="text-brand-gold mt-1">&bull;</span>
                                    <span>Full answer history</span>
                                </li>
                            </ul>
                        </div>
                        <div class="card p-4">
                            <h4 class="font-semibold text-gray-900 mb-2">Phone Preview (Right Side)</h4>
                            <ul class="text-sm text-gray-600 space-y-1.5">
                                <li class="flex items-start gap-2">
                                    <span class="text-brand-gold mt-1">&bull;</span>
                                    <span>Shows exactly what users see on their phone</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <span class="text-brand-gold mt-1">&bull;</span>
                                    <span>Real animations and transitions</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <span class="text-brand-gold mt-1">&bull;</span>
                                    <span>Click options to advance through the quiz</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <span class="text-brand-gold mt-1">&bull;</span>
                                    <span>Use "Try Different Path" to restart and test another route</span>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
                        <p class="text-sm text-blue-800"><strong>Note:</strong> No real data is saved during preview. You can test as many times as you want without affecting quiz analytics or creating real responses.</p>
                    </div>
                </div>
            </section>

            {{-- ============================================================ --}}
            {{-- Section 17: Glossary --}}
            {{-- ============================================================ --}}
            <section id="glossary">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">17. Glossary</h2>
                <p class="text-sm text-gray-600 mb-6">Every technical term used in the quiz builder, in plain English.</p>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b-2 border-gray-200">
                                <th class="text-left py-3 pr-4 font-semibold text-gray-900 w-44">Term</th>
                                <th class="text-left py-3 pr-4 font-semibold text-gray-900">What it means</th>
                                <th class="text-left py-3 font-semibold text-gray-900 w-56">Example</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <tr>
                                <td class="py-3 pr-4 font-medium text-gray-900">Slide</td>
                                <td class="py-3 pr-4 text-gray-600">One screen in the quiz</td>
                                <td class="py-3 text-gray-500 text-xs">A question, an info page, the loading animation</td>
                            </tr>
                            <tr>
                                <td class="py-3 pr-4 font-medium text-gray-900">Klaviyo Property</td>
                                <td class="py-3 pr-4 text-gray-600">The label for this answer in your email tool (Klaviyo)</td>
                                <td class="py-3 text-gray-500 text-xs"><code class="bg-gray-100 px-1 rounded">health_goal</code>, <code class="bg-gray-100 px-1 rounded">awareness_level</code>, <code class="bg-gray-100 px-1 rounded">email</code></td>
                            </tr>
                            <tr>
                                <td class="py-3 pr-4 font-medium text-gray-900">Segment</td>
                                <td class="py-3 pr-4 text-gray-600">User category based on their quiz answers</td>
                                <td class="py-3 text-gray-500 text-xs">TOF (Explorer), MOF (Researcher), BOF (Ready to Buy)</td>
                            </tr>
                            <tr>
                                <td class="py-3 pr-4 font-medium text-gray-900">TOF</td>
                                <td class="py-3 pr-4 text-gray-600">Top of Funnel &mdash; user is just starting to learn</td>
                                <td class="py-3 text-gray-500 text-xs">User answered "brand new to peptides"</td>
                            </tr>
                            <tr>
                                <td class="py-3 pr-4 font-medium text-gray-900">MOF</td>
                                <td class="py-3 pr-4 text-gray-600">Middle of Funnel &mdash; user is actively researching</td>
                                <td class="py-3 text-gray-500 text-xs">User answered "I've been reading about peptides"</td>
                            </tr>
                            <tr>
                                <td class="py-3 pr-4 font-medium text-gray-900">BOF</td>
                                <td class="py-3 pr-4 text-gray-600">Bottom of Funnel &mdash; user is ready to buy</td>
                                <td class="py-3 text-gray-500 text-xs">User answered "I know what I want"</td>
                            </tr>
                            <tr>
                                <td class="py-3 pr-4 font-medium text-gray-900">Score</td>
                                <td class="py-3 pr-4 text-gray-600">Points added per answer to TOF, MOF, or BOF buckets</td>
                                <td class="py-3 text-gray-500 text-xs">Option "brand new" adds TOF +3</td>
                            </tr>
                            <tr>
                                <td class="py-3 pr-4 font-medium text-gray-900">Outcome</td>
                                <td class="py-3 pr-4 text-gray-600">The result page shown after quiz completion</td>
                                <td class="py-3 text-gray-500 text-xs">"Your #1 Match: BPC-157" with a description</td>
                            </tr>
                            <tr>
                                <td class="py-3 pr-4 font-medium text-gray-900">Priority</td>
                                <td class="py-3 pr-4 text-gray-600">Order in which outcomes are checked (first match wins)</td>
                                <td class="py-3 text-gray-500 text-xs">#1 checked first, #2 second, etc.</td>
                            </tr>
                            <tr>
                                <td class="py-3 pr-4 font-medium text-gray-900">Condition</td>
                                <td class="py-3 pr-4 text-gray-600">Rule that controls when an outcome is shown</td>
                                <td class="py-3 text-gray-500 text-xs">"Show when segment = BOF"</td>
                            </tr>
                            <tr>
                                <td class="py-3 pr-4 font-medium text-gray-900">Show Condition</td>
                                <td class="py-3 pr-4 text-gray-600">Rule that controls when a slide appears during the quiz</td>
                                <td class="py-3 text-gray-500 text-xs">"Show when awareness_level = researching"</td>
                            </tr>
                            <tr>
                                <td class="py-3 pr-4 font-medium text-gray-900">Skip To</td>
                                <td class="py-3 pr-4 text-gray-600">An option that jumps to a specific slide when selected</td>
                                <td class="py-3 text-gray-500 text-xs">"Know what I want" &rarr; skip to product selector</td>
                            </tr>
                            <tr>
                                <td class="py-3 pr-4 font-medium text-gray-900">Tags</td>
                                <td class="py-3 pr-4 text-gray-600">Labels on options for Klaviyo segmentation and targeting</td>
                                <td class="py-3 text-gray-500 text-xs"><code class="bg-gray-100 px-1 rounded">["weight_management", "glp1"]</code></td>
                            </tr>
                            <tr>
                                <td class="py-3 pr-4 font-medium text-gray-900">Results Bank</td>
                                <td class="py-3 pr-4 text-gray-600">Database of peptide recommendations by health goal</td>
                                <td class="py-3 text-gray-500 text-xs">10 health goals x 2 experience levels = 20 entries</td>
                            </tr>
                            <tr>
                                <td class="py-3 pr-4 font-medium text-gray-900">Auto-fill</td>
                                <td class="py-3 pr-4 text-gray-600">Pull peptide info from Results Bank into an outcome</td>
                                <td class="py-3 text-gray-500 text-xs">Toggle on &rarr; click Apply &rarr; headline & body filled</td>
                            </tr>
                            <tr>
                                <td class="py-3 pr-4 font-medium text-gray-900">Dynamic Content</td>
                                <td class="py-3 pr-4 text-gray-600">Slide content that changes based on a previous answer</td>
                                <td class="py-3 text-gray-500 text-xs">Peptide Reveal shows different peptide per health goal</td>
                            </tr>
                            <tr>
                                <td class="py-3 pr-4 font-medium text-gray-900">Coverage</td>
                                <td class="py-3 pr-4 text-gray-600">Checklist showing which segments/goals have outcomes</td>
                                <td class="py-3 text-gray-500 text-xs">Green = done, Yellow = needs an outcome</td>
                            </tr>
                            <tr>
                                <td class="py-3 pr-4 font-medium text-gray-900">Same-category exclusion</td>
                                <td class="py-3 pr-4 text-gray-600">Recommend a different peptide from the same category</td>
                                <td class="py-3 text-gray-500 text-xs">User on Tirzepatide &rarr; get Retatrutide (both fat loss)</td>
                            </tr>
                            <tr>
                                <td class="py-3 pr-4 font-medium text-gray-900">Quiz Type</td>
                                <td class="py-3 pr-4 text-gray-600">How the quiz calculates results &mdash; segmentation, product, or custom</td>
                                <td class="py-3 text-gray-500 text-xs">Segmentation uses TOF/MOF/BOF scoring</td>
                            </tr>
                            <tr>
                                <td class="py-3 pr-4 font-medium text-gray-900">Peptide Search</td>
                                <td class="py-3 pr-4 text-gray-600">Slide type where users search and select a peptide from the database</td>
                                <td class="py-3 text-gray-500 text-xs">Used in Path 1 for direct peptide selection</td>
                            </tr>
                            <tr>
                                <td class="py-3 pr-4 font-medium text-gray-900">Multiple Choice</td>
                                <td class="py-3 pr-4 text-gray-600">Question type allowing users to pick more than one option</td>
                                <td class="py-3 text-gray-500 text-xs">Set max_selections to limit how many</td>
                            </tr>
                            <tr>
                                <td class="py-3 pr-4 font-medium text-gray-900">Session Resume</td>
                                <td class="py-3 pr-4 text-gray-600">Abandoned quizzes can be continued within 24 hours</td>
                                <td class="py-3 text-gray-500 text-xs">Tracked via session cookie</td>
                            </tr>
                            <tr>
                                <td class="py-3 pr-4 font-medium text-gray-900">Email Auto-Skip</td>
                                <td class="py-3 pr-4 text-gray-600">Email capture slide skipped for returning users</td>
                                <td class="py-3 text-gray-500 text-xs">Detected via <code class="bg-gray-100 px-1 rounded">pp_email</code> cookie</td>
                            </tr>
                            <tr>
                                <td class="py-3 pr-4 font-medium text-gray-900">UTM Parameters</td>
                                <td class="py-3 pr-4 text-gray-600">Marketing attribution data captured from the URL</td>
                                <td class="py-3 text-gray-500 text-xs"><code class="bg-gray-100 px-1 rounded">utm_source</code>, <code class="bg-gray-100 px-1 rounded">utm_medium</code>, <code class="bg-gray-100 px-1 rounded">utm_campaign</code></td>
                            </tr>
                            <tr>
                                <td class="py-3 pr-4 font-medium text-gray-900">Duplicate</td>
                                <td class="py-3 pr-4 text-gray-600">Copy an entire quiz with all slides, outcomes, and conditions</td>
                                <td class="py-3 text-gray-500 text-xs">Copy starts inactive with zeroed stats</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>

        </div>

        {{-- Sticky TOC Sidebar --}}
        <div class="hidden lg:block">
            <nav class="sticky top-24 space-y-1">
                <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">On This Page</h3>
                <a href="#how-it-works" class="block text-sm text-gray-600 hover:text-brand-gold py-1 transition-colors">1. How the Quiz Works</a>
                <a href="#quiz-settings" class="block text-sm text-gray-600 hover:text-brand-gold py-1 transition-colors">2. Quiz Types & Settings</a>
                <a href="#slide-types" class="block text-sm text-gray-600 hover:text-brand-gold py-1 transition-colors">3. Slide Types</a>
                <a href="#scoring" class="block text-sm text-gray-600 hover:text-brand-gold py-1 transition-colors">4. Scoring & Segments</a>
                <a href="#question-options" class="block text-sm text-gray-600 hover:text-brand-gold py-1 transition-colors">5. Question Options</a>
                <a href="#show-conditions" class="block text-sm text-gray-600 hover:text-brand-gold py-1 transition-colors">6. Show Conditions</a>
                <a href="#outcomes" class="block text-sm text-gray-600 hover:text-brand-gold py-1 transition-colors">7. Outcomes</a>
                <a href="#coverage" class="block text-sm text-gray-600 hover:text-brand-gold py-1 transition-colors">8. Outcome Coverage</a>
                <a href="#results-bank" class="block text-sm text-gray-600 hover:text-brand-gold py-1 transition-colors">9. Results Bank</a>
                <a href="#quiz-paths" class="block text-sm text-gray-600 hover:text-brand-gold py-1 transition-colors">10. The 3 Quiz Paths</a>
                <a href="#custom-quiz-guide" class="block text-sm text-amber-600 hover:text-brand-gold py-1 transition-colors font-medium">10b. Custom Quiz Guide</a>
                <a href="#segmentation-guide" class="block text-sm text-green-600 hover:text-brand-gold py-1 transition-colors font-medium">10c. Segmentation Guide</a>
                <a href="#dynamic-content" class="block text-sm text-gray-600 hover:text-brand-gold py-1 transition-colors">11. Dynamic Content</a>
                <a href="#klaviyo" class="block text-sm text-gray-600 hover:text-brand-gold py-1 transition-colors">12. Klaviyo Integration</a>
                <a href="#session-behavior" class="block text-sm text-gray-600 hover:text-brand-gold py-1 transition-colors">13. Session & Email</a>
                <a href="#warnings" class="block text-sm text-gray-600 hover:text-brand-gold py-1 transition-colors">14. Warnings</a>
                <a href="#admin-tools" class="block text-sm text-gray-600 hover:text-brand-gold py-1 transition-colors">15. Analytics & Tools</a>
                <a href="#preview" class="block text-sm text-gray-600 hover:text-brand-gold py-1 transition-colors">16. Quiz Preview</a>
                <a href="#glossary" class="block text-sm text-gray-600 hover:text-brand-gold py-1 transition-colors">17. Glossary</a>

                <hr class="my-4 border-gray-200">
                <a href="{{ route('admin.quizzes.index') }}" class="block text-sm text-brand-gold hover:underline font-medium">&larr; Back to Quizzes</a>
            </nav>
        </div>

    </div>

    @push('scripts')
    <script>
    // Smooth scroll for TOC links
    document.querySelectorAll('a[href^="#"]').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                history.pushState(null, '', this.getAttribute('href'));
            }
        });
    });
    </script>
    @endpush
</x-admin-layout>
