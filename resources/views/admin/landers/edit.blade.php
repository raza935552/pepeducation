<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <span>Edit lander — {{ $lander->name }}</span>
            <div class="flex items-center gap-3">
                <a href="{{ $lander->url }}" target="_blank" class="text-sm text-gray-500 hover:text-gray-700">Preview ↗</a>
                <a href="{{ route('admin.landers.index') }}" class="text-sm text-gray-500 hover:text-gray-700">← All landers</a>
            </div>
        </div>
    </x-slot>

    @php
        // Small helpers so the form prefills from saved content (old() wins on validation errors).
        $v  = fn ($path, $fallback = '') => old('content.' . $path, $lander->c($path, $fallback));
        $lbl = 'block text-xs font-semibold text-gray-600 mb-1';
        $inp = 'w-full rounded-lg border-gray-300 text-sm';
        $ta  = 'w-full rounded-lg border-gray-300 text-sm';
    @endphp

    @if(session('success'))
        <div class="mb-4 rounded-lg bg-green-50 border border-green-200 text-green-700 px-4 py-3 text-sm">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="mb-4 rounded-lg bg-red-50 border border-red-200 text-red-700 px-4 py-3 text-sm">Please check the fields below.</div>
    @endif

    <form method="POST" action="{{ route('admin.landers.update', $lander) }}" class="space-y-6 max-w-4xl pb-16">
        @csrf @method('PUT')

        {{-- Page settings --}}
        <div class="card p-5">
            <h3 class="text-sm font-semibold text-gray-900 mb-4">Page settings</h3>
            <div class="grid sm:grid-cols-2 gap-4">
                <div><label class="{{ $lbl }}">Admin name</label><input name="name" value="{{ old('name', $lander->name) }}" class="{{ $inp }}"></div>
                <div class="flex items-end gap-5">
                    <label class="inline-flex items-center gap-2 text-sm"><input type="hidden" name="is_active" value="0"><input type="checkbox" name="is_active" value="1" {{ $lander->is_active ? 'checked' : '' }} class="rounded"> Active (live)</label>
                    <label class="inline-flex items-center gap-2 text-sm"><input type="hidden" name="noindex" value="0"><input type="checkbox" name="noindex" value="1" {{ $lander->noindex ? 'checked' : '' }} class="rounded"> noindex</label>
                </div>
                <div><label class="{{ $lbl }}">SEO title</label><input name="content[meta][title]" value="{{ $v('meta.title') }}" class="{{ $inp }}"></div>
                <div><label class="{{ $lbl }}">SEO description</label><input name="content[meta][description]" value="{{ $v('meta.description') }}" class="{{ $inp }}"></div>
            </div>
        </div>

        {{-- Tracking (UTM on the CTA outbound link) --}}
        <div class="card p-5">
            <h3 class="text-sm font-semibold text-gray-900 mb-1">Tracking (UTM)</h3>
            <p class="text-xs text-gray-400 mb-4">Applied to every Biolinx link on this lander (via <code>/go/{{ $lander->outbound_slug }}</code>).</p>
            <div class="grid sm:grid-cols-4 gap-4">
                <div><label class="{{ $lbl }}">utm_source</label><input name="utm_source" value="{{ old('utm_source', $outbound->utm_source ?? '') }}" class="{{ $inp }}"></div>
                <div><label class="{{ $lbl }}">utm_medium</label><input name="utm_medium" value="{{ old('utm_medium', $outbound->utm_medium ?? '') }}" class="{{ $inp }}"></div>
                <div><label class="{{ $lbl }}">utm_campaign</label><input name="utm_campaign" value="{{ old('utm_campaign', $outbound->utm_campaign ?? '') }}" class="{{ $inp }}"></div>
                <div><label class="{{ $lbl }}">utm_content</label><input name="utm_content" value="{{ old('utm_content', $outbound->utm_content ?? '') }}" class="{{ $inp }}"></div>
            </div>
        </div>

        {{-- Brand + Hero --}}
        <div class="card p-5">
            <h3 class="text-sm font-semibold text-gray-900 mb-4">Header &amp; hero</h3>
            <div class="grid sm:grid-cols-2 gap-4">
                <div><label class="{{ $lbl }}">Brand name</label><input name="content[brand][name]" value="{{ $v('brand.name') }}" class="{{ $inp }}"></div>
                <div><label class="{{ $lbl }}">Brand tagline</label><input name="content[brand][tagline]" value="{{ $v('brand.tagline') }}" class="{{ $inp }}"></div>
                <div><label class="{{ $lbl }}">Eyebrow</label><input name="content[hero][eyebrow]" value="{{ $v('hero.eyebrow') }}" class="{{ $inp }}"></div>
                <div><label class="{{ $lbl }}">Hero image URL</label><input name="content[hero][image_url]" value="{{ $v('hero.image_url') }}" class="{{ $inp }}"></div>
                <div><label class="{{ $lbl }}">Headline</label><input name="content[hero][headline]" value="{{ $v('hero.headline') }}" class="{{ $inp }}"></div>
                <div><label class="{{ $lbl }}">Headline (pink part)</label><input name="content[hero][headline_highlight]" value="{{ $v('hero.headline_highlight') }}" class="{{ $inp }}"></div>
                <div class="sm:col-span-2"><label class="{{ $lbl }}">Lede</label><input name="content[hero][lede]" value="{{ $v('hero.lede') }}" class="{{ $inp }}"></div>
                <div class="sm:col-span-2"><label class="{{ $lbl }}">Paragraph 1</label><textarea name="content[hero][para1]" rows="2" class="{{ $ta }}">{{ $v('hero.para1') }}</textarea></div>
                <div class="sm:col-span-2"><label class="{{ $lbl }}">Paragraph 2</label><textarea name="content[hero][para2]" rows="2" class="{{ $ta }}">{{ $v('hero.para2') }}</textarea></div>
                <div><label class="{{ $lbl }}">Primary button text</label><input name="content[hero][primary_cta]" value="{{ $v('hero.primary_cta') }}" class="{{ $inp }}"></div>
                <div><label class="{{ $lbl }}">Ghost button text</label><input name="content[hero][ghost_cta]" value="{{ $v('hero.ghost_cta') }}" class="{{ $inp }}"></div>
                <div class="sm:col-span-2"><label class="{{ $lbl }}">Micro disclaimer</label><input name="content[hero][disclaimer]" value="{{ $v('hero.disclaimer') }}" class="{{ $inp }}"></div>
            </div>
        </div>

        {{-- Science (4 fixed) --}}
        <div class="card p-5">
            <h3 class="text-sm font-semibold text-gray-900 mb-4">Science section</h3>
            <div class="grid sm:grid-cols-2 gap-4 mb-4">
                <div><label class="{{ $lbl }}">Heading</label><input name="content[science][heading]" value="{{ $v('science.heading') }}" class="{{ $inp }}"></div>
                <div><label class="{{ $lbl }}">Sub-heading</label><input name="content[science][sub]" value="{{ $v('science.sub') }}" class="{{ $inp }}"></div>
            </div>
            @for($i = 0; $i < 4; $i++)
                <div class="grid sm:grid-cols-3 gap-3 mb-2 items-start">
                    <div><label class="{{ $lbl }}">Item {{ $i+1 }} title</label><input name="content[science][items][{{ $i }}][title]" value="{{ $v("science.items.$i.title") }}" class="{{ $inp }}"></div>
                    <div class="sm:col-span-2"><label class="{{ $lbl }}">Body</label><input name="content[science][items][{{ $i }}][body]" value="{{ $v("science.items.$i.body") }}" class="{{ $inp }}"></div>
                </div>
            @endfor
        </div>

        {{-- Framework checklist (5 fixed) --}}
        <div class="card p-5">
            <h3 class="text-sm font-semibold text-gray-900 mb-4">Checklist section</h3>
            <div class="grid sm:grid-cols-2 gap-4 mb-4">
                <div><label class="{{ $lbl }}">Heading</label><input name="content[framework][heading]" value="{{ $v('framework.heading') }}" class="{{ $inp }}"></div>
                <div><label class="{{ $lbl }}">Sub-heading</label><input name="content[framework][sub]" value="{{ $v('framework.sub') }}" class="{{ $inp }}"></div>
            </div>
            @for($i = 0; $i < 5; $i++)
                <div class="grid sm:grid-cols-3 gap-3 mb-2 items-start">
                    <div><label class="{{ $lbl }}">#{{ $i+1 }} title</label><input name="content[framework][items][{{ $i }}][title]" value="{{ $v("framework.items.$i.title") }}" class="{{ $inp }}"></div>
                    <div class="sm:col-span-2"><label class="{{ $lbl }}">Body</label><input name="content[framework][items][{{ $i }}][body]" value="{{ $v("framework.items.$i.body") }}" class="{{ $inp }}"></div>
                </div>
            @endfor
        </div>

        {{-- Compounds / product cards (3 fixed) --}}
        <div class="card p-5">
            <h3 class="text-sm font-semibold text-gray-900 mb-4">Compound cards</h3>
            <div class="grid sm:grid-cols-3 gap-4 mb-4">
                <div><label class="{{ $lbl }}">Eyebrow</label><input name="content[compounds][eyebrow]" value="{{ $v('compounds.eyebrow') }}" class="{{ $inp }}"></div>
                <div><label class="{{ $lbl }}">Heading</label><input name="content[compounds][heading]" value="{{ $v('compounds.heading') }}" class="{{ $inp }}"></div>
                <div><label class="{{ $lbl }}">Sub-heading</label><input name="content[compounds][sub]" value="{{ $v('compounds.sub') }}" class="{{ $inp }}"></div>
            </div>
            @for($i = 0; $i < 3; $i++)
                <div class="border-t border-gray-100 pt-3 mt-3">
                    <p class="text-xs font-semibold text-gray-500 mb-2">Card {{ $i+1 }}</p>
                    <div class="grid sm:grid-cols-2 gap-3">
                        <div><label class="{{ $lbl }}">Name</label><input name="content[compounds][products][{{ $i }}][name]" value="{{ $v("compounds.products.$i.name") }}" class="{{ $inp }}"></div>
                        <div><label class="{{ $lbl }}">Button text</label><input name="content[compounds][products][{{ $i }}][cta_text]" value="{{ $v("compounds.products.$i.cta_text") }}" class="{{ $inp }}"></div>
                        <div class="sm:col-span-2"><label class="{{ $lbl }}">Body</label><input name="content[compounds][products][{{ $i }}][body]" value="{{ $v("compounds.products.$i.body") }}" class="{{ $inp }}"></div>
                        <div><label class="{{ $lbl }}">Image URL</label><input name="content[compounds][products][{{ $i }}][image_url]" value="{{ $v("compounds.products.$i.image_url") }}" class="{{ $inp }}"></div>
                        <div><label class="{{ $lbl }}">Biolinx destination URL</label><input name="content[compounds][products][{{ $i }}][dest_url]" value="{{ $v("compounds.products.$i.dest_url") }}" class="{{ $inp }}"></div>
                    </div>
                </div>
            @endfor
        </div>

        {{-- Trust strip (5 fixed) --}}
        <div class="card p-5">
            <h3 class="text-sm font-semibold text-gray-900 mb-4">Trust strip</h3>
            @for($i = 0; $i < 5; $i++)
                <div class="grid sm:grid-cols-3 gap-3 mb-2 items-start">
                    <div><label class="{{ $lbl }}">Item {{ $i+1 }} title</label><input name="content[trust][items][{{ $i }}][title]" value="{{ $v("trust.items.$i.title") }}" class="{{ $inp }}"></div>
                    <div class="sm:col-span-2"><label class="{{ $lbl }}">Body</label><input name="content[trust][items][{{ $i }}][body]" value="{{ $v("trust.items.$i.body") }}" class="{{ $inp }}"></div>
                </div>
            @endfor
        </div>

        {{-- Final CTA (3 links) --}}
        <div class="card p-5">
            <h3 class="text-sm font-semibold text-gray-900 mb-4">Final CTA</h3>
            <div class="grid sm:grid-cols-3 gap-4 mb-4">
                <div><label class="{{ $lbl }}">Eyebrow</label><input name="content[final][eyebrow]" value="{{ $v('final.eyebrow') }}" class="{{ $inp }}"></div>
                <div class="sm:col-span-2"><label class="{{ $lbl }}">Heading</label><input name="content[final][heading]" value="{{ $v('final.heading') }}" class="{{ $inp }}"></div>
                <div class="sm:col-span-3"><label class="{{ $lbl }}">Body</label><textarea name="content[final][body]" rows="2" class="{{ $ta }}">{{ $v('final.body') }}</textarea></div>
            </div>
            @for($i = 0; $i < 3; $i++)
                <div class="grid sm:grid-cols-2 gap-3 mb-2">
                    <div><label class="{{ $lbl }}">Link {{ $i+1 }} label</label><input name="content[final][links][{{ $i }}][label]" value="{{ $v("final.links.$i.label") }}" class="{{ $inp }}"></div>
                    <div><label class="{{ $lbl }}">Biolinx destination URL</label><input name="content[final][links][{{ $i }}][dest_url]" value="{{ $v("final.links.$i.dest_url") }}" class="{{ $inp }}"></div>
                </div>
            @endfor
        </div>

        {{-- Legal --}}
        <div class="card p-5">
            <h3 class="text-sm font-semibold text-gray-900 mb-2">Legal note</h3>
            <textarea name="content[legal]" rows="3" class="{{ $ta }}">{{ $v('legal') }}</textarea>
        </div>

        <div class="sticky bottom-0 bg-white border-t border-gray-200 py-3 flex justify-end">
            <button type="submit" class="btn btn-primary">Save lander</button>
        </div>
    </form>
</x-admin-layout>
