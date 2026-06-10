{{-- Editable content fields for the `operator-brief` template (the 5 legacy landers).
     Fields marked “HTML ok” accept inline tags like <b>, <mark>, <strong>; the rest
     are plain text. Fixed slot counts (4 flags, 6 checklist, 3 dropdowns) keep the
     layout intact no matter what is typed. --}}
@php $hint = 'text-[11px] text-gray-400 mt-0.5'; @endphp

{{-- Top chrome --}}
<div class="card p-5">
    <h3 class="text-sm font-semibold text-gray-900 mb-4">Top bar &amp; masthead</h3>
    <div class="grid sm:grid-cols-2 gap-4">
        <div class="sm:col-span-2"><label class="{{ $lbl }}">Top notice bar</label><input name="content[chrome][notice]" value="{{ $v('chrome.notice') }}" class="{{ $inp }}"></div>
        <div><label class="{{ $lbl }}">Publication name <span class="text-gray-400">(HTML ok)</span></label><input name="content[chrome][pub]" value="{{ $v('chrome.pub') }}" class="{{ $inp }}"></div>
        <div><label class="{{ $lbl }}">Masthead tagline</label><input name="content[chrome][masthead_tag]" value="{{ $v('chrome.masthead_tag') }}" class="{{ $inp }}"></div>
    </div>
</div>

{{-- Age gate --}}
<div class="card p-5">
    <h3 class="text-sm font-semibold text-gray-900 mb-4">Age gate (18+ overlay)</h3>
    <div class="grid gap-4">
        <div><label class="{{ $lbl }}">Title <span class="text-gray-400">(HTML ok)</span></label><input name="content[age_gate][title]" value="{{ $v('age_gate.title') }}" class="{{ $inp }}"></div>
        <div><label class="{{ $lbl }}">Body</label><textarea name="content[age_gate][body]" rows="2" class="{{ $ta }}">{{ $v('age_gate.body') }}</textarea></div>
    </div>
</div>

{{-- Hero --}}
<div class="card p-5">
    <h3 class="text-sm font-semibold text-gray-900 mb-4">Hero</h3>
    <div class="grid sm:grid-cols-2 gap-4">
        <div><label class="{{ $lbl }}">Eyebrow</label><input name="content[hero][eyebrow]" value="{{ $v('hero.eyebrow') }}" class="{{ $inp }}"></div>
        <div><label class="{{ $lbl }}">Skip-link text</label><input name="content[hero][skip_text]" value="{{ $v('hero.skip_text') }}" class="{{ $inp }}"></div>
        <div class="sm:col-span-2"><label class="{{ $lbl }}">Headline (H1)</label><input name="content[hero][h1]" value="{{ $v('hero.h1') }}" class="{{ $inp }}"></div>
        <div class="sm:col-span-2"><label class="{{ $lbl }}">Deck / sub-headline <span class="text-gray-400">(HTML ok)</span></label><input name="content[hero][dek]" value="{{ $v('hero.dek') }}" class="{{ $inp }}"></div>
        <div><label class="{{ $lbl }}">Hero image URL</label><input name="content[hero][image_url]" value="{{ $v('hero.image_url') }}" class="{{ $inp }}"></div>
        <div><label class="{{ $lbl }}">Hero image alt</label><input name="content[hero][image_alt]" value="{{ $v('hero.image_alt') }}" class="{{ $inp }}"></div>
    </div>
</div>

{{-- Byline --}}
<div class="card p-5">
    <h3 class="text-sm font-semibold text-gray-900 mb-4">Byline</h3>
    <div class="grid sm:grid-cols-2 gap-4">
        <div><label class="{{ $lbl }}">Author</label><input name="content[byline][author]" value="{{ $v('byline.author') }}" class="{{ $inp }}"></div>
        <div><label class="{{ $lbl }}">Role</label><input name="content[byline][role]" value="{{ $v('byline.role') }}" class="{{ $inp }}"></div>
        <div><label class="{{ $lbl }}">Credential</label><input name="content[byline][cred]" value="{{ $v('byline.cred') }}" class="{{ $inp }}"></div>
        <div><label class="{{ $lbl }}">Read time</label><input name="content[byline][read_time]" value="{{ $v('byline.read_time') }}" class="{{ $inp }}"></div>
    </div>
</div>

{{-- Intro --}}
<div class="card p-5">
    <h3 class="text-sm font-semibold text-gray-900 mb-4">Intro</h3>
    <div class="grid gap-4">
        <div><label class="{{ $lbl }}">Lead paragraph <span class="text-gray-400">(HTML ok)</span></label><textarea name="content[intro][lead]" rows="3" class="{{ $ta }}">{{ $v('intro.lead') }}</textarea></div>
        <div><label class="{{ $lbl }}">Body paragraphs <span class="text-gray-400">(HTML — keep &lt;p&gt;…&lt;/p&gt; per paragraph)</span></label><textarea name="content[intro][body]" rows="5" class="{{ $ta }} font-mono text-xs">{{ $v('intro.body') }}</textarea></div>
        <div class="grid sm:grid-cols-2 gap-4">
            <div><label class="{{ $lbl }}">Intro image URL</label><input name="content[intro][image_url]" value="{{ $v('intro.image_url') }}" class="{{ $inp }}"></div>
            <div><label class="{{ $lbl }}">Intro image alt</label><input name="content[intro][image_alt]" value="{{ $v('intro.image_alt') }}" class="{{ $inp }}"></div>
        </div>
    </div>
</div>

{{-- Flag blocks (4 fixed) --}}
<div class="card p-5">
    <h3 class="text-sm font-semibold text-gray-900 mb-4">Red-flag sections (4)</h3>
    @for($i = 0; $i < 4; $i++)
        <div class="border-t border-gray-100 pt-3 mt-3 first:border-t-0 first:pt-0 first:mt-0">
            <p class="text-xs font-semibold text-gray-500 mb-2">Flag {{ $i+1 }}</p>
            <div class="grid sm:grid-cols-2 gap-3">
                <div><label class="{{ $lbl }}">Label (badge)</label><input name="content[flags][{{ $i }}][label]" value="{{ $v("flags.$i.label") }}" class="{{ $inp }}"></div>
                <div><label class="{{ $lbl }}">Heading</label><input name="content[flags][{{ $i }}][heading]" value="{{ $v("flags.$i.heading") }}" class="{{ $inp }}"></div>
                <div class="sm:col-span-2"><label class="{{ $lbl }}">Body <span class="text-gray-400">(HTML — keep &lt;p&gt;…&lt;/p&gt; per paragraph)</span></label><textarea name="content[flags][{{ $i }}][body]" rows="4" class="{{ $ta }} font-mono text-xs">{{ $v("flags.$i.body") }}</textarea></div>
                <div><label class="{{ $lbl }}">Image URL <span class="text-gray-400">(optional)</span></label><input name="content[flags][{{ $i }}][image_url]" value="{{ $v("flags.$i.image_url") }}" class="{{ $inp }}"></div>
                <div><label class="{{ $lbl }}">Image alt</label><input name="content[flags][{{ $i }}][image_alt]" value="{{ $v("flags.$i.image_alt") }}" class="{{ $inp }}"></div>
            </div>
        </div>
    @endfor
</div>

{{-- Closing + checklist --}}
<div class="card p-5">
    <h3 class="text-sm font-semibold text-gray-900 mb-4">Closing section + checklist</h3>
    <div class="grid gap-4 mb-4">
        <div><label class="{{ $lbl }}">Heading</label><input name="content[closing][heading]" value="{{ $v('closing.heading') }}" class="{{ $inp }}"></div>
        <div><label class="{{ $lbl }}">Body <span class="text-gray-400">(HTML — keep &lt;p&gt;…&lt;/p&gt; per paragraph)</span></label><textarea name="content[closing][body]" rows="4" class="{{ $ta }} font-mono text-xs">{{ $v('closing.body') }}</textarea></div>
        <div><label class="{{ $lbl }}">Checklist intro line</label><input name="content[closing][checklist_intro]" value="{{ $v('closing.checklist_intro') }}" class="{{ $inp }}"></div>
    </div>
    <p class="text-xs font-semibold text-gray-500 mb-2">Checklist rows (leave blank to hide a row) — HTML ok</p>
    @for($i = 0; $i < 6; $i++)
        <div class="mb-2"><label class="{{ $lbl }}">Row {{ $i+1 }}</label><input name="content[closing][checklist][{{ $i }}]" value="{{ $v("closing.checklist.$i") }}" class="{{ $inp }}"></div>
    @endfor
</div>

{{-- Gate --}}
<div class="card p-5">
    <h3 class="text-sm font-semibold text-gray-900 mb-4">CTA gate (the conversion block)</h3>
    <div class="grid gap-4">
        <div><label class="{{ $lbl }}">Heading <span class="text-gray-400">(HTML ok)</span></label><input name="content[gate][heading]" value="{{ $v('gate.heading') }}" class="{{ $inp }}"></div>
        <div><label class="{{ $lbl }}">Body</label><textarea name="content[gate][body]" rows="2" class="{{ $ta }}">{{ $v('gate.body') }}</textarea></div>
        <div><label class="{{ $lbl }}">Consent checkbox text</label><textarea name="content[gate][consent]" rows="2" class="{{ $ta }}">{{ $v('gate.consent') }}</textarea></div>
        <div class="grid sm:grid-cols-2 gap-4">
            <div><label class="{{ $lbl }}">Button text</label><input name="content[gate][cta]" value="{{ $v('gate.cta') }}" class="{{ $inp }}"></div>
            <div><label class="{{ $lbl }}">Sub-text (under button)</label><input name="content[gate][sub]" value="{{ $v('gate.sub') }}" class="{{ $inp }}"></div>
        </div>
        <p class="{{ $hint }}">The button always points to your Biolinx CTA link via <code>/go/{{ $lander->outbound_slug }}</code> — set the UTM in the Tracking section above.</p>
    </div>
</div>

{{-- References (3 dropdowns) --}}
<div class="card p-5">
    <h3 class="text-sm font-semibold text-gray-900 mb-4">Sources &amp; methodology (3 dropdowns)</h3>
    <div class="mb-4"><label class="{{ $lbl }}">Section title</label><input name="content[refs][title]" value="{{ $v('refs.title') }}" class="{{ $inp }}"></div>
    @for($d = 0; $d < 3; $d++)
        <div class="border-t border-gray-100 pt-3 mt-3 first:border-t-0 first:pt-0 first:mt-0">
            <p class="text-xs font-semibold text-gray-500 mb-2">Dropdown {{ $d+1 }}</p>
            <div class="grid gap-3">
                <div><label class="{{ $lbl }}">Summary (clickable title)</label><input name="content[refs][dropdowns][{{ $d }}][summary]" value="{{ $v("refs.dropdowns.$d.summary") }}" class="{{ $inp }}"></div>
                <div><label class="{{ $lbl }}">Intro line</label><input name="content[refs][dropdowns][{{ $d }}][intro]" value="{{ $v("refs.dropdowns.$d.intro") }}" class="{{ $inp }}"></div>
                <div>
                    <label class="{{ $lbl }}">List items (leave blank to hide) — HTML ok</label>
                    @for($it = 0; $it < 8; $it++)
                        <input name="content[refs][dropdowns][{{ $d }}][items][{{ $it }}]" value="{{ $v("refs.dropdowns.$d.items.$it") }}" class="{{ $inp }} mb-1.5" placeholder="Item {{ $it+1 }}">
                    @endfor
                </div>
                <div><label class="{{ $lbl }}">Source note (footer line)</label><input name="content[refs][dropdowns][{{ $d }}][src]" value="{{ $v("refs.dropdowns.$d.src") }}" class="{{ $inp }}"></div>
            </div>
        </div>
    @endfor
</div>

{{-- Footer --}}
<div class="card p-5">
    <h3 class="text-sm font-semibold text-gray-900 mb-4">Footer</h3>
    <div class="grid gap-4">
        <div><label class="{{ $lbl }}">Disclaimer <span class="text-gray-400">(HTML ok)</span></label><textarea name="content[footer][disclaimer]" rows="3" class="{{ $ta }}">{{ $v('footer.disclaimer') }}</textarea></div>
        <div><label class="{{ $lbl }}">Copyright line</label><input name="content[footer][copyright]" value="{{ $v('footer.copyright') }}" class="{{ $inp }}"></div>
    </div>
</div>
