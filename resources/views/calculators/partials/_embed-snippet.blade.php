{{-- "Embed this calculator" — copyable iframe snippet with auto-resize + attribution backlink. --}}
@php
    $embedUrl = route('calculators.embed', $config['slug']);
    $showUrl  = route('calculators.show', $config['slug']);
    $id       = 'ppcalc-' . $config['slug'];
    $snippet  = '<iframe id="' . $id . '" src="' . $embedUrl . '" width="100%" height="640" style="border:1px solid #e5e7eb;border-radius:16px;max-width:680px" title="' . e($config['name']) . '" loading="lazy"></iframe>' . "\n"
        . '<script>window.addEventListener("message",function(e){if(e.data&&e.data.ppCalc==="' . $config['slug'] . '"){var f=document.getElementById("' . $id . '");if(f&&e.data.height)f.style.height=e.data.height+"px";}});</' . 'script>' . "\n"
        . '<p style="font-size:12px;color:#6b7280;text-align:center">Free calculator by <a href="' . $showUrl . '" target="_blank" rel="noopener">Professor Peptides</a></p>';
@endphp

<section class="py-10 bg-surface-50">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div x-data="{ copied: false, code: @js($snippet), copy() { navigator.clipboard.writeText(this.code).then(() => { this.copied = true; setTimeout(() => this.copied = false, 1600); }); } }"
             class="rounded-2xl border border-gray-200 bg-white p-6 sm:p-8">
            <div class="flex items-start gap-3 mb-4">
                <span class="w-9 h-9 rounded-lg bg-gray-100 flex items-center justify-center shrink-0 text-gray-500">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>
                </span>
                <div>
                    <h2 class="text-lg font-bold text-gray-900">Embed this calculator — free</h2>
                    <p class="text-sm text-gray-600">Add the {{ $config['name'] }} to your own site. Just copy the code below (a credit link back to us keeps it free).</p>
                </div>
            </div>
            <textarea readonly rows="5" onclick="this.select()"
                      class="w-full text-xs font-mono rounded-lg border-gray-200 bg-surface-50 text-gray-600 focus:border-primary-500 focus:ring-primary-500 resize-none">{{ $snippet }}</textarea>
            <div class="mt-3 flex items-center gap-3">
                <button type="button" @click="copy()"
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-gray-900 text-white font-semibold text-sm hover:bg-gray-800 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                    <span x-text="copied ? 'Copied!' : 'Copy embed code'"></span>
                </button>
                <a href="{{ $embedUrl }}" target="_blank" rel="noopener" class="text-sm text-gray-500 hover:text-gray-700">Preview ↗</a>
            </div>
        </div>
    </div>
</section>
