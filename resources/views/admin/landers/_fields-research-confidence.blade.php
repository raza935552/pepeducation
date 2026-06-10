{{-- Editable content fields for the `research-confidence` template (hunger-fullness). --}}

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

{{-- Giveaway email-capture popup (subscribes to Customer.io as source "giveaway:{slug}") --}}
<div class="card p-5">
    <h3 class="text-sm font-semibold text-gray-900 mb-1">Giveaway popup</h3>
    <p class="text-xs text-gray-500 mb-4">Shows an email-capture popup on this lander. Submissions subscribe the visitor in Customer.io (source <code>giveaway:{{ $lander->slug }}</code>) and fire the <code>Subscribed</code> event. Leave any field blank to use its default.</p>

    <label class="flex items-center gap-2 mb-4 cursor-pointer">
        <input type="hidden" name="content[giveaway_popup][enabled]" value="0">
        <input type="checkbox" name="content[giveaway_popup][enabled]" value="1" {{ $v('giveaway_popup.enabled') ? 'checked' : '' }}
               class="rounded border-gray-300 text-pink-600 focus:ring-pink-500">
        <span class="text-sm font-medium text-gray-700">Enable giveaway popup on this lander</span>
    </label>

    <div class="grid sm:grid-cols-2 gap-4">
        <div><label class="{{ $lbl }}">Tag / eyebrow</label><input name="content[giveaway_popup][tag]" value="{{ $v('giveaway_popup.tag') }}" placeholder="Giveaway" class="{{ $inp }}"></div>
        <div><label class="{{ $lbl }}">Button text</label><input name="content[giveaway_popup][button_text]" value="{{ $v('giveaway_popup.button_text') }}" placeholder="Enter Now" class="{{ $inp }}"></div>
        <div class="sm:col-span-2"><label class="{{ $lbl }}">Headline</label><input name="content[giveaway_popup][headline]" value="{{ $v('giveaway_popup.headline') }}" placeholder="Win a free research-use-only peptide." class="{{ $inp }}"></div>
        <div class="sm:col-span-2"><label class="{{ $lbl }}">Sub-headline</label><input name="content[giveaway_popup][subhead]" value="{{ $v('giveaway_popup.subhead') }}" placeholder="One winner picked each month." class="{{ $inp }}"></div>
        <div><label class="{{ $lbl }}">Email placeholder</label><input name="content[giveaway_popup][placeholder]" value="{{ $v('giveaway_popup.placeholder') }}" placeholder="Email address" class="{{ $inp }}"></div>
        <div><label class="{{ $lbl }}">Decline link text</label><input name="content[giveaway_popup][decline_text]" value="{{ $v('giveaway_popup.decline_text') }}" placeholder="No thanks" class="{{ $inp }}"></div>
        <div class="sm:col-span-2"><label class="{{ $lbl }}">Fine print</label><input name="content[giveaway_popup][fine_print]" value="{{ $v('giveaway_popup.fine_print') }}" placeholder="Research use only · Not for human consumption · No purchase necessary" class="{{ $inp }}"></div>
        <div><label class="{{ $lbl }}">Success title</label><input name="content[giveaway_popup][success_title]" value="{{ $v('giveaway_popup.success_title') }}" placeholder="You're in." class="{{ $inp }}"></div>
        <div><label class="{{ $lbl }}">Success body</label><input name="content[giveaway_popup][success_body]" value="{{ $v('giveaway_popup.success_body') }}" placeholder="We'll reach out if you win…" class="{{ $inp }}"></div>
        <div><label class="{{ $lbl }}">Accent colour (hex)</label><input name="content[giveaway_popup][accent]" value="{{ $v('giveaway_popup.accent') }}" placeholder="#da3f76" class="{{ $inp }}"></div>
        <div><label class="{{ $lbl }}">Delay before showing (seconds)</label><input type="number" min="0" name="content[giveaway_popup][delay_seconds]" value="{{ $v('giveaway_popup.delay_seconds') }}" placeholder="6" class="{{ $inp }}"></div>
    </div>
</div>
