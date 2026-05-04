<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <span>SEO & AI Content</span>
            <a href="{{ route('admin.settings.index') }}" class="text-sm text-gray-500 hover:text-gray-700 flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                Back to Settings
            </a>
        </div>
    </x-slot>

    @if(session('success'))
        <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded-lg text-sm">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm">{{ session('error') }}</div>
    @endif

    <div class="space-y-6">

        {{-- AI Provider Config --}}
        <form action="{{ route('admin.settings.seo.update') }}" method="POST">
            @csrf
            @method('PUT')
            <div class="card p-6 border-l-4 border-purple-400" x-data="aiConfig()">
                <h3 class="text-lg font-semibold mb-1 flex items-center gap-2">
                    <svg aria-hidden="true" class="w-6 h-6 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                    </svg>
                    Claude AI Configuration
                </h3>
                <p class="text-sm text-gray-500 mb-4">Connect Claude to auto-generate SEO meta, rewrite content, and create blog outlines.</p>

                <div class="space-y-4">
                    {{-- API Key --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Claude API Key</label>
                        <div class="flex gap-2">
                            <input type="password" name="claude_api_key" x-model="apiKey"
                                placeholder="{{ $hasKey ? '********  (saved — enter new key to replace)' : 'sk-ant-api03-...' }}"
                                class="flex-1 rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500 font-mono text-sm">
                            <button type="button" @click="testKey()" :disabled="testing || !apiKey"
                                class="px-4 py-2 text-sm font-medium rounded-lg border transition"
                                :class="testing ? 'bg-gray-100 text-gray-400' : 'bg-white text-gray-700 hover:bg-gray-50 border-gray-300'">
                                <span x-show="!testing">Test</span>
                                <span x-show="testing">Testing...</span>
                            </button>
                        </div>
                        @if($hasKey)
                            <p class="mt-1 text-xs text-green-600 flex items-center gap-1">
                                <span class="w-1.5 h-1.5 bg-green-500 rounded-full"></span>
                                Key saved: {{ $maskedKey }}
                            </p>
                        @else
                            <p class="mt-1 text-xs text-gray-400">Get key from <a href="https://console.anthropic.com" target="_blank" class="underline">console.anthropic.com</a></p>
                        @endif
                        <p x-show="testResult" class="mt-1 text-xs" :class="testSuccess ? 'text-green-600' : 'text-red-600'" x-text="testResult"></p>
                    </div>

                    {{-- Model --}}
                    <div class="max-w-xs">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Model</label>
                        <select name="claude_model" class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500 text-sm">
                            @foreach($claudeModels as $value => $label)
                                <option value="{{ $value }}" {{ $claudeModel === $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Auto-generate toggle --}}
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="hidden" name="auto_generate_on_save" value="0">
                        <input type="checkbox" name="auto_generate_on_save" value="1" {{ $autoGenerate ? 'checked' : '' }}
                            class="w-5 h-5 rounded border-gray-300 text-purple-500 focus:ring-purple-500">
                        <div>
                            <span class="font-medium text-gray-700">Auto-generate SEO on save</span>
                            <p class="text-sm text-gray-500">When saving a peptide without meta data, auto-generate using AI.</p>
                        </div>
                    </label>

                    <div class="pt-4 border-t border-gray-200">
                        <button type="submit" class="btn btn-primary">Save Settings</button>
                    </div>
                </div>
            </div>
        </form>

        {{-- Bulk SEO Generator --}}
        <div class="card p-6 border-l-4 border-blue-400" x-data="bulkGenerator()">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold mb-1 flex items-center gap-2">
                    <svg aria-hidden="true" class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                    </svg>
                    AI SEO Generator
                </h3>
                <a href="{{ route('admin.settings.seo.review') }}" class="text-sm text-blue-600 hover:text-blue-800 underline">Review All Pages</a>
            </div>
            <p class="text-sm text-gray-600 mb-4">
                <span class="font-medium text-gray-900">{{ $missingCount }}</span> of {{ $totalPeptides }} peptides need SEO meta.
                Generates title + description using Claude AI.
            </p>

            <div class="flex items-center gap-3 mb-4">
                <button type="button" @click="startBulk()" :disabled="running || {{ $missingCount }} === 0"
                    class="px-5 py-2 text-sm font-medium rounded-lg transition"
                    :class="running ? 'bg-gray-300 text-gray-500 cursor-not-allowed' : 'bg-blue-600 text-white hover:bg-blue-700'">
                    <span x-show="!running">Generate All Missing SEO</span>
                    <span x-show="running">Generating...</span>
                </button>
                <button type="button" @click="startAll()" :disabled="running"
                    class="px-5 py-2 text-sm font-medium rounded-lg border transition"
                    :class="running ? 'bg-gray-100 text-gray-400' : 'bg-white text-gray-700 hover:bg-gray-50 border-gray-300'">
                    Regenerate All {{ $totalPeptides }}
                </button>
                <button type="button" x-show="running" @click="cancelled = true"
                    class="px-4 py-2 text-sm font-medium text-red-600 border border-red-300 rounded-lg hover:bg-red-50 transition">
                    Stop
                </button>
            </div>

            {{-- Progress --}}
            <div x-show="started" x-cloak class="mb-4">
                <div class="flex justify-between text-xs text-gray-500 mb-1">
                    <span x-text="`${completed + failed} / ${total}`"></span>
                    <span x-text="running ? 'Processing...' : (cancelled ? 'Stopped' : 'Complete')"></span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="h-2 rounded-full transition-all duration-300"
                        :class="failed > 0 ? 'bg-yellow-500' : 'bg-green-500'"
                        :style="`width: ${total ? ((completed + failed) / total * 100) : 0}%`"></div>
                </div>
                <div class="flex gap-4 mt-2 text-xs">
                    <span class="text-green-600" x-text="`${completed} generated`"></span>
                    <span class="text-red-600" x-show="failed > 0" x-text="`${failed} failed`"></span>
                </div>
            </div>

            {{-- Results Table --}}
            <div x-show="results.length > 0" x-cloak class="border rounded-lg max-h-96 overflow-y-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 sticky top-0">
                        <tr>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 w-40">Peptide</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500">Meta Title</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500">Meta Description</th>
                            <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 w-16">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <template x-for="r in results" :key="r.peptide_id">
                            <tr :class="r.success ? '' : 'bg-red-50'">
                                <td class="px-3 py-2 text-xs font-medium text-gray-900 truncate max-w-[160px]" x-text="r.name"></td>
                                <td class="px-3 py-2 text-xs text-gray-700 truncate max-w-[200px]" x-text="r.meta_title || '-'"></td>
                                <td class="px-3 py-2 text-xs text-gray-600 truncate max-w-[250px]" x-text="r.meta_description || '-'"></td>
                                <td class="px-3 py-2 text-center">
                                    <span x-show="r.success" class="text-green-600 text-xs font-bold">OK</span>
                                    <span x-show="!r.success" class="text-red-600 text-xs font-bold">FAIL</span>
                                </td>
                            </tr>
                        </template>
                        <tr x-show="currentItem" class="bg-blue-50">
                            <td class="px-3 py-2 text-xs font-medium text-blue-700" x-text="currentItem"></td>
                            <td colspan="2" class="px-3 py-2 text-xs text-blue-500">
                                <span class="inline-flex items-center gap-1">
                                    <svg class="animate-spin h-3 w-3" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                                    Claude is generating...
                                </span>
                            </td>
                            <td class="px-3 py-2 text-center"><span class="text-blue-500 text-xs">...</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Blog Outline Generator --}}
        <div class="card p-6 border-l-4 border-emerald-400" x-data="blogOutlineGen()">
            <h3 class="text-lg font-semibold mb-1 flex items-center gap-2">
                <svg aria-hidden="true" class="w-6 h-6 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Blog Outline Generator
            </h3>
            <p class="text-sm text-gray-500 mb-4">Generate SEO-optimized blog post outlines with target keywords and internal linking suggestions.</p>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Topic / Title</label>
                    <input type="text" x-model="topic" placeholder="e.g. BPC-157 vs TB-500: Which Healing Peptide Is Better?"
                        class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Target Keyword (optional)</label>
                    <input type="text" x-model="keyword" placeholder="e.g. bpc-157 vs tb-500"
                        class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                </div>
            </div>
            <button type="button" @click="generate()" :disabled="generating || !topic"
                class="px-5 py-2 text-sm font-medium rounded-lg transition"
                :class="generating ? 'bg-gray-300 text-gray-500' : 'bg-emerald-600 text-white hover:bg-emerald-700'">
                <span x-show="!generating">Generate Outline</span>
                <span x-show="generating" class="inline-flex items-center gap-1">
                    <svg class="animate-spin h-4 w-4" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                    Generating...
                </span>
            </button>

            <div x-show="outline" x-cloak class="mt-4 p-4 bg-gray-50 rounded-lg border">
                <div class="flex justify-between items-center mb-2">
                    <span class="text-sm font-medium text-gray-700">Generated Outline</span>
                    <button type="button" @click="navigator.clipboard.writeText(outline)" class="text-xs text-blue-600 hover:underline">Copy</button>
                </div>
                <pre class="text-sm text-gray-800 whitespace-pre-wrap font-sans" x-text="outline"></pre>
            </div>
            <p x-show="error" x-cloak class="mt-2 text-sm text-red-600" x-text="error"></p>
        </div>

        {{-- Webmaster Verification --}}
        <form action="{{ route('admin.settings.seo.webmaster') }}" method="POST">
            @csrf
            @method('PUT')
            <div class="card p-6 border-l-4 border-orange-400">
                <h3 class="text-lg font-semibold mb-1 flex items-center gap-2">
                    <svg aria-hidden="true" class="w-6 h-6 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                    Webmaster Verification
                </h3>
                <p class="text-sm text-gray-500 mb-4">Add verification meta tags so you can claim ownership in each search engine's webmaster console.</p>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Google Search Console</label>
                        <input type="text" name="google_verification" value="{{ $googleVerification }}"
                            placeholder="abc123-XYZ_..."
                            class="w-full rounded-lg border-gray-300 focus:border-orange-500 focus:ring-orange-500 font-mono text-sm">
                        <p class="mt-1 text-xs text-gray-500">From <a href="https://search.google.com/search-console" target="_blank" class="text-blue-600 underline">search.google.com/search-console</a> -> "HTML tag" verification method. Paste the value from the content="..." attribute.</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Bing Webmaster (msvalidate.01)</label>
                        <input type="text" name="bing_verification" value="{{ $bingVerification }}"
                            placeholder="A1B2C3D4..."
                            class="w-full rounded-lg border-gray-300 focus:border-orange-500 focus:ring-orange-500 font-mono text-sm">
                        <p class="mt-1 text-xs text-gray-500">From <a href="https://www.bing.com/webmasters" target="_blank" class="text-blue-600 underline">bing.com/webmasters</a> -> "Meta tag" method. Paste the content="..." value.</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Yandex Webmaster</label>
                        <input type="text" name="yandex_verification" value="{{ $yandexVerification }}"
                            placeholder="abc123def456..."
                            class="w-full rounded-lg border-gray-300 focus:border-orange-500 focus:ring-orange-500 font-mono text-sm">
                        <p class="mt-1 text-xs text-gray-500">From <a href="https://webmaster.yandex.com" target="_blank" class="text-blue-600 underline">webmaster.yandex.com</a> -> "Meta tag" method. Paste the content="..." value.</p>
                    </div>
                </div>

                <div class="mt-6 pt-4 border-t border-gray-200">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Yandex Metrica Counter ID</label>
                    <input type="text" name="yandex_metrica_id" value="{{ $yandexMetricaId }}"
                        placeholder="12345678"
                        class="max-w-xs rounded-lg border-gray-300 focus:border-orange-500 focus:ring-orange-500 font-mono text-sm">
                    <p class="mt-1 text-xs text-gray-500">Numeric counter ID from <a href="https://metrika.yandex.com" target="_blank" class="text-blue-600 underline">metrika.yandex.com</a> (Russia/CIS analytics, like GA4). Loads counter only when set.</p>
                </div>

                <div class="mt-4 pt-4 border-t border-gray-200">
                    <button type="submit" class="btn btn-primary">Save Verification Settings</button>
                </div>
            </div>
        </form>

        {{-- IndexNow --}}
        <div class="card p-6 border-l-4 border-cyan-400">
            <h3 class="text-lg font-semibold mb-1 flex items-center gap-2">
                <svg aria-hidden="true" class="w-6 h-6 text-cyan-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
                IndexNow (Bing + Yandex Instant Indexing)
            </h3>
            <p class="text-sm text-gray-500 mb-4">Submit URLs to Bing and Yandex the moment you publish. Cuts indexing time from weeks to minutes. Auto-pings on peptide and blog publish/update.</p>

            <div class="space-y-4">
                {{-- Status --}}
                <div class="rounded-lg bg-gray-50 border border-gray-200 p-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="text-gray-500">API Key:</span>
                            @if($indexnowKey)
                                <span class="font-mono text-xs text-gray-800 break-all ml-1">{{ $indexnowKey }}</span>
                            @else
                                <span class="ml-1 text-gray-400 italic">not generated</span>
                            @endif
                        </div>
                        <div>
                            <span class="text-gray-500">Verification file:</span>
                            @if($indexnowKeyUrl)
                                <a href="{{ $indexnowKeyUrl }}" target="_blank" class="text-blue-600 underline ml-1 break-all text-xs">{{ $indexnowKeyUrl }}</a>
                            @else
                                <span class="ml-1 text-gray-400 italic">-</span>
                            @endif
                        </div>
                        <div>
                            <span class="text-gray-500">Auto-ping on save:</span>
                            <span class="ml-1 font-medium {{ $indexnowEnabled ? 'text-green-600' : 'text-gray-400' }}">
                                {{ $indexnowEnabled ? 'Enabled' : 'Disabled' }}
                            </span>
                        </div>
                        <div>
                            <span class="text-gray-500">Last manual submit:</span>
                            <span class="ml-1 text-gray-700 text-xs">{{ $indexnowLastPing ? \Carbon\Carbon::parse($indexnowLastPing)->diffForHumans() : 'never' }}</span>
                        </div>
                    </div>
                </div>

                {{-- Toggle --}}
                <form action="{{ route('admin.settings.seo.indexnow') }}" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="indexnow_enabled" value="0">
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" name="indexnow_enabled" value="1" {{ $indexnowEnabled ? 'checked' : '' }}
                            class="w-5 h-5 rounded border-gray-300 text-cyan-500 focus:ring-cyan-500">
                        <div>
                            <span class="font-medium text-gray-700">Auto-ping when peptide or blog post is saved</span>
                            <p class="text-sm text-gray-500">Recommended. Generates a key automatically on first enable.</p>
                        </div>
                    </label>
                    <div class="mt-3">
                        <button type="submit" class="btn btn-primary text-sm">Save IndexNow Settings</button>
                    </div>
                </form>

                {{-- Actions --}}
                <div class="pt-4 border-t border-gray-200 flex flex-wrap gap-3">
                    <form action="{{ route('admin.settings.seo.indexnow.generate') }}" method="POST" onsubmit="return confirm('Generate a NEW key? The old one will stop working immediately and you will need to refresh search consoles.');">
                        @csrf
                        <button type="submit" class="px-4 py-2 text-sm font-medium rounded-lg border border-gray-300 bg-white text-gray-700 hover:bg-gray-50">
                            {{ $indexnowKey ? 'Regenerate Key' : 'Generate Key' }}
                        </button>
                    </form>
                    <form action="{{ route('admin.settings.seo.indexnow.submit-all') }}" method="POST" onsubmit="return confirm('Submit ALL published peptides and blog posts to IndexNow now?');">
                        @csrf
                        <button type="submit" class="px-4 py-2 text-sm font-medium rounded-lg bg-cyan-600 text-white hover:bg-cyan-700" {{ $indexnowKey ? '' : 'disabled' }}>
                            Submit All Published URLs Now
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Sitemap submission helper --}}
        <div class="card p-6 border-l-4 border-indigo-400">
            <h3 class="text-lg font-semibold mb-1 flex items-center gap-2">
                <svg aria-hidden="true" class="w-6 h-6 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                </svg>
                Sitemap Submission
            </h3>
            <p class="text-sm text-gray-500 mb-4">Submit your sitemap to each search engine once after claiming ownership.</p>

            <div class="rounded-lg bg-indigo-50 border border-indigo-200 p-4 mb-4">
                <p class="text-sm text-gray-700">Your sitemap:</p>
                <a href="{{ $sitemapUrl }}" target="_blank" class="text-indigo-700 underline font-mono text-sm break-all">{{ $sitemapUrl }}</a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                <a href="https://search.google.com/search-console/sitemaps" target="_blank" class="block p-4 rounded-lg border border-gray-200 hover:border-blue-400 hover:bg-blue-50 transition">
                    <div class="font-medium text-gray-900">Google Search Console</div>
                    <div class="text-xs text-gray-500 mt-1">Sitemaps -> Add new sitemap -> paste sitemap.xml</div>
                </a>
                <a href="https://www.bing.com/webmasters/sitemaps" target="_blank" class="block p-4 rounded-lg border border-gray-200 hover:border-cyan-400 hover:bg-cyan-50 transition">
                    <div class="font-medium text-gray-900">Bing Webmaster</div>
                    <div class="text-xs text-gray-500 mt-1">Sitemaps -> Submit sitemap -> paste sitemap.xml</div>
                </a>
                <a href="https://webmaster.yandex.com" target="_blank" class="block p-4 rounded-lg border border-gray-200 hover:border-red-400 hover:bg-red-50 transition">
                    <div class="font-medium text-gray-900">Yandex Webmaster</div>
                    <div class="text-xs text-gray-500 mt-1">Indexing -> Sitemap files -> Add sitemap.xml</div>
                </a>
            </div>
        </div>

    </div>

    @push('scripts')
    <script>
    function aiConfig() {
        return {
            apiKey: '',
            testing: false,
            testResult: '',
            testSuccess: false,
            async testKey() {
                if (!this.apiKey) return;
                this.testing = true;
                this.testResult = '';
                try {
                    const res = await fetch('{{ route("admin.settings.seo.test") }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({ api_key: this.apiKey }),
                    });
                    const data = await res.json();
                    this.testResult = data.message;
                    this.testSuccess = data.success;
                } catch (e) {
                    this.testResult = 'Network error: ' + e.message;
                    this.testSuccess = false;
                }
                this.testing = false;
            }
        };
    }

    function bulkGenerator() {
        return {
            running: false, started: false, cancelled: false,
            total: 0, completed: 0, failed: 0,
            results: [], currentItem: '',

            async startBulk() { await this.run('{{ route("admin.settings.seo.pending") }}'); },
            async startAll() {
                if (!confirm('This will regenerate SEO for ALL {{ $totalPeptides }} peptides. Continue?')) return;
                await this.run('{{ route("admin.settings.seo.pending") }}?all=1');
            },

            async run(pendingUrl) {
                this.running = true; this.started = true; this.cancelled = false;
                this.results = []; this.completed = 0; this.failed = 0;

                try {
                    const res = await fetch(pendingUrl, { headers: { 'Accept': 'application/json' } });
                    const data = await res.json();
                    const items = data.items;
                    this.total = items.length;

                    for (const item of items) {
                        if (this.cancelled) break;
                        this.currentItem = item.name;

                        try {
                            const genRes = await fetch('{{ route("admin.settings.seo.generate-one") }}', {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json',
                                },
                                body: JSON.stringify({ peptide_id: item.id }),
                            });
                            const result = await genRes.json();
                            this.results.push(result);
                            result.success ? this.completed++ : this.failed++;
                        } catch (e) {
                            this.results.push({ success: false, peptide_id: item.id, name: item.name, error: 'Network error' });
                            this.failed++;
                        }
                    }
                } catch (e) { console.error('Failed to fetch pending items', e); }

                this.running = false;
                this.currentItem = '';
            }
        };
    }

    function blogOutlineGen() {
        return {
            topic: '', keyword: '', outline: '', error: '', generating: false,
            async generate() {
                this.generating = true; this.outline = ''; this.error = '';
                try {
                    const res = await fetch('{{ route("admin.settings.seo.blog-outline") }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({ topic: this.topic, keyword: this.keyword }),
                    });
                    const data = await res.json();
                    if (data.success) { this.outline = data.outline; }
                    else { this.error = data.error || 'Generation failed.'; }
                } catch (e) { this.error = 'Network error: ' + e.message; }
                this.generating = false;
            }
        };
    }
    </script>
    @endpush
</x-admin-layout>
