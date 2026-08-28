{{-- Editable content fields for the `affiliate-guide` template. Blank fields fall back to the template defaults. --}}

<div class="card p-5">
    <h3 class="text-sm font-semibold text-gray-900 mb-1">Affiliate</h3>
    <p class="text-xs text-gray-400 mb-4">Placeholders available in most text fields: <code>{first}</code> <code>{name}</code> <code>{code}</code> <code>{discount}</code>.</p>
    <div class="grid sm:grid-cols-2 gap-4">
        <div><label class="{{ $lbl }}">Full name</label><input name="content[affiliate][name]" value="{{ $v('affiliate.name') }}" class="{{ $inp }}" placeholder="e.g. Marcus Reed"></div>
        <div><label class="{{ $lbl }}">First name (used in copy)</label><input name="content[affiliate][first_name]" value="{{ $v('affiliate.first_name') }}" class="{{ $inp }}" placeholder="Marcus"></div>
        <div><label class="{{ $lbl }}">Role line</label><input name="content[affiliate][role]" value="{{ $v('affiliate.role') }}" class="{{ $inp }}" placeholder="Amateur MMA · Gym name, City"></div>
        <div><label class="{{ $lbl }}">Photo URL (square, from Media Library)</label><input name="content[affiliate][photo_url]" value="{{ $v('affiliate.photo_url') }}" class="{{ $inp }}"></div>
        <div><label class="{{ $lbl }}">Coupon code (must exist on BioLinx, flagged as affiliate code)</label><input name="content[affiliate][code]" value="{{ $v('affiliate.code') }}" class="{{ $inp }}" placeholder="MARCUS15"></div>
        <div><label class="{{ $lbl }}">Discount label</label><input name="content[affiliate][discount]" value="{{ $v('affiliate.discount') }}" class="{{ $inp }}" placeholder="15%"></div>
        <div><label class="{{ $lbl }}">iDevAffiliate ID (optional, for link-based credit)</label><input name="content[affiliate][idev_id]" value="{{ $v('affiliate.idev_id') }}" class="{{ $inp }}" placeholder="171"></div>
        <div class="sm:col-span-2"><label class="{{ $lbl }}">Their note (no claims about results; say why they buy here and disclose the commission)</label><textarea name="content[affiliate][note]" rows="4" class="{{ $ta }}">{{ $v('affiliate.note') }}</textarea></div>
    </div>
</div>

<div class="card p-5">
    <h3 class="text-sm font-semibold text-gray-900 mb-4">Meta &amp; hero</h3>
    <div class="grid sm:grid-cols-2 gap-4">
        <div><label class="{{ $lbl }}">Page title</label><input name="content[meta][title]" value="{{ $v('meta.title') }}" class="{{ $inp }}"></div>
        <div><label class="{{ $lbl }}">Meta description</label><input name="content[meta][description]" value="{{ $v('meta.description') }}" class="{{ $inp }}"></div>
        <div><label class="{{ $lbl }}">Eyebrow</label><input name="content[hero][eyebrow]" value="{{ $v('hero.eyebrow') }}" class="{{ $inp }}"></div>
        <div><label class="{{ $lbl }}">Hero image URL</label><input name="content[hero][image_url]" value="{{ $v('hero.image_url') }}" class="{{ $inp }}"></div>
        <div class="sm:col-span-2"><label class="{{ $lbl }}">Headline</label><input name="content[hero][headline]" value="{{ $v('hero.headline') }}" class="{{ $inp }}"></div>
        <div class="sm:col-span-2"><label class="{{ $lbl }}">Sub headline</label><textarea name="content[hero][sub]" rows="2" class="{{ $ta }}">{{ $v('hero.sub') }}</textarea></div>
        <div><label class="{{ $lbl }}">Primary button</label><input name="content[hero][primary_cta]" value="{{ $v('hero.primary_cta') }}" class="{{ $inp }}"></div>
        <div><label class="{{ $lbl }}">Secondary button</label><input name="content[hero][secondary_cta]" value="{{ $v('hero.secondary_cta') }}" class="{{ $inp }}"></div>
    </div>
</div>

<div class="card p-5">
    <h3 class="text-sm font-semibold text-gray-900 mb-4">Compounds (6 fixed)</h3>
    <div class="grid sm:grid-cols-2 gap-4 mb-4">
        <div><label class="{{ $lbl }}">Eyebrow</label><input name="content[compounds][eyebrow]" value="{{ $v('compounds.eyebrow') }}" class="{{ $inp }}"></div>
        <div><label class="{{ $lbl }}">Heading</label><input name="content[compounds][heading]" value="{{ $v('compounds.heading') }}" class="{{ $inp }}"></div>
        <div class="sm:col-span-2"><label class="{{ $lbl }}">Intro</label><input name="content[compounds][sub]" value="{{ $v('compounds.sub') }}" class="{{ $inp }}"></div>
    </div>
    @for($i = 0; $i < 6; $i++)
        <div class="border-t border-gray-100 pt-3 mt-3 grid sm:grid-cols-2 gap-3">
            <div><label class="{{ $lbl }}">#{{ $i+1 }} name</label><input name="content[compounds][items][{{ $i }}][name]" value="{{ $v("compounds.items.$i.name") }}" class="{{ $inp }}"></div>
            <div><label class="{{ $lbl }}">Tag</label><input name="content[compounds][items][{{ $i }}][tag]" value="{{ $v("compounds.items.$i.tag") }}" class="{{ $inp }}"></div>
            <div class="sm:col-span-2"><label class="{{ $lbl }}">What it is</label><input name="content[compounds][items][{{ $i }}][what]" value="{{ $v("compounds.items.$i.what") }}" class="{{ $inp }}"></div>
            <div><label class="{{ $lbl }}">Studied for (lab models only)</label><input name="content[compounds][items][{{ $i }}][studied]" value="{{ $v("compounds.items.$i.studied") }}" class="{{ $inp }}"></div>
            <div><label class="{{ $lbl }}">BioLinx URL</label><input name="content[compounds][items][{{ $i }}][url]" value="{{ $v("compounds.items.$i.url") }}" class="{{ $inp }}"></div>
            <div><label class="{{ $lbl }}">Price label</label><input name="content[compounds][items][{{ $i }}][price]" value="{{ $v("compounds.items.$i.price") }}" class="{{ $inp }}"></div>
            <div class="sm:col-span-2"><label class="{{ $lbl }}">Image URL</label><input name="content[compounds][items][{{ $i }}][image]" value="{{ $v("compounds.items.$i.image") }}" class="{{ $inp }}"></div>
        </div>
    @endfor
</div>

<div class="card p-5">
    <h3 class="text-sm font-semibold text-gray-900 mb-4">Supplier checks (5 fixed)</h3>
    <div class="grid sm:grid-cols-2 gap-4 mb-4">
        <div><label class="{{ $lbl }}">Eyebrow</label><input name="content[vetting][eyebrow]" value="{{ $v('vetting.eyebrow') }}" class="{{ $inp }}"></div>
        <div><label class="{{ $lbl }}">Heading</label><input name="content[vetting][heading]" value="{{ $v('vetting.heading') }}" class="{{ $inp }}"></div>
        <div class="sm:col-span-2"><label class="{{ $lbl }}">Intro</label><input name="content[vetting][sub]" value="{{ $v('vetting.sub') }}" class="{{ $inp }}"></div>
        <div class="sm:col-span-2"><label class="{{ $lbl }}">Image URL</label><input name="content[vetting][image_url]" value="{{ $v('vetting.image_url') }}" class="{{ $inp }}"></div>
        <div class="sm:col-span-2"><label class="{{ $lbl }}">"How BioLinx passes this list" text</label><input name="content[vetting][proof]" value="{{ $v('vetting.proof') }}" class="{{ $inp }}"></div>
    </div>
    @for($i = 0; $i < 5; $i++)
        <div class="grid sm:grid-cols-3 gap-3 mb-2 items-start">
            <div><label class="{{ $lbl }}">#{{ $i+1 }} title</label><input name="content[vetting][items][{{ $i }}][title]" value="{{ $v("vetting.items.$i.title") }}" class="{{ $inp }}"></div>
            <div class="sm:col-span-2"><label class="{{ $lbl }}">Body</label><input name="content[vetting][items][{{ $i }}][body]" value="{{ $v("vetting.items.$i.body") }}" class="{{ $inp }}"></div>
        </div>
    @endfor
</div>

<div class="card p-5">
    <h3 class="text-sm font-semibold text-gray-900 mb-1">The stack (package section)</h3>
    <p class="text-xs text-gray-400 mb-4">Fill only item #1 for the single "one package" layout (image, price, price with code). Filling 2 or 3 switches to the three-card layout.</p>
    <div class="grid sm:grid-cols-2 gap-4 mb-4">
        <div><label class="{{ $lbl }}">Eyebrow</label><input name="content[kits][eyebrow]" value="{{ $v('kits.eyebrow') }}" class="{{ $inp }}"></div>
        <div><label class="{{ $lbl }}">Heading</label><input name="content[kits][heading]" value="{{ $v('kits.heading') }}" class="{{ $inp }}"></div>
        <div class="sm:col-span-2"><label class="{{ $lbl }}">Intro</label><input name="content[kits][sub]" value="{{ $v('kits.sub') }}" class="{{ $inp }}"></div>
        <div><label class="{{ $lbl }}">Package flag</label><input name="content[kits][flag]" value="{{ $v('kits.flag') }}" class="{{ $inp }}" placeholder="One package"></div>
        <div><label class="{{ $lbl }}">Button text</label><input name="content[kits][cta]" value="{{ $v('kits.cta') }}" class="{{ $inp }}" placeholder="Add Sebastian's Stack with the code"></div>
        <div><label class="{{ $lbl }}">Alt line text</label><input name="content[kits][alt_text]" value="{{ $v('kits.alt_text') }}" class="{{ $inp }}" placeholder="Only want the blend?"></div>
        <div><label class="{{ $lbl }}">Alt link text</label><input name="content[kits][alt_cta]" value="{{ $v('kits.alt_cta') }}" class="{{ $inp }}" placeholder="Get it with the code"></div>
    </div>
    @for($i = 0; $i < 3; $i++)
        <div class="border-t border-gray-100 pt-3 mt-3 grid sm:grid-cols-3 gap-3">
            <div><label class="{{ $lbl }}">#{{ $i+1 }} name</label><input name="content[kits][items][{{ $i }}][name]" value="{{ $v("kits.items.$i.name") }}" class="{{ $inp }}"></div>
            <div class="sm:col-span-2"><label class="{{ $lbl }}">Contents</label><input name="content[kits][items][{{ $i }}][contents]" value="{{ $v("kits.items.$i.contents") }}" class="{{ $inp }}"></div>
            <div><label class="{{ $lbl }}">Price label</label><input name="content[kits][items][{{ $i }}][price]" value="{{ $v("kits.items.$i.price") }}" class="{{ $inp }}"></div>
            <div><label class="{{ $lbl }}">Price with code (single layout)</label><input name="content[kits][items][{{ $i }}][price_with_code]" value="{{ $v("kits.items.$i.price_with_code") }}" class="{{ $inp }}"></div>
            <div><label class="{{ $lbl }}">Image URL (single layout)</label><input name="content[kits][items][{{ $i }}][image]" value="{{ $v("kits.items.$i.image") }}" class="{{ $inp }}"></div>
            <div><label class="{{ $lbl }}">BioLinx URL (product or bundle)</label><input name="content[kits][items][{{ $i }}][url]" value="{{ $v("kits.items.$i.url") }}" class="{{ $inp }}"></div>
            <div><label class="{{ $lbl }}">Note</label><input name="content[kits][items][{{ $i }}][note]" value="{{ $v("kits.items.$i.note") }}" class="{{ $inp }}"></div>
            <div class="sm:col-span-3 flex gap-6 text-sm">
                <label class="inline-flex items-center gap-2"><input type="hidden" name="content[kits][items][{{ $i }}][auto_add]" value="0"><input type="checkbox" name="content[kits][items][{{ $i }}][auto_add]" value="1" {{ $v("kits.items.$i.auto_add") ? 'checked' : '' }} class="rounded"> Auto-add to cart on arrival (bundles only)</label>
                <label class="inline-flex items-center gap-2"><input type="hidden" name="content[kits][items][{{ $i }}][featured]" value="0"><input type="checkbox" name="content[kits][items][{{ $i }}][featured]" value="1" {{ $v("kits.items.$i.featured") ? 'checked' : '' }} class="rounded"> Highlight as "Most chosen"</label>
            </div>
        </div>
    @endfor
</div>

<div class="card p-5">
    <h3 class="text-sm font-semibold text-gray-900 mb-4">FAQ (6 fixed)</h3>
    @for($i = 0; $i < 6; $i++)
        <div class="grid sm:grid-cols-3 gap-3 mb-2 items-start">
            <div><label class="{{ $lbl }}">Q{{ $i+1 }}</label><input name="content[faq][items][{{ $i }}][q]" value="{{ $v("faq.items.$i.q") }}" class="{{ $inp }}"></div>
            <div class="sm:col-span-2"><label class="{{ $lbl }}">Answer</label><input name="content[faq][items][{{ $i }}][a]" value="{{ $v("faq.items.$i.a") }}" class="{{ $inp }}"></div>
        </div>
    @endfor
</div>

<div class="card p-5">
    <h3 class="text-sm font-semibold text-gray-900 mb-4">Code modal</h3>
    <div class="grid sm:grid-cols-2 gap-4 mb-4">
        <div><label class="{{ $lbl }}">Title</label><input name="content[modal][title]" value="{{ $v('modal.title') }}" class="{{ $inp }}"></div>
        <div><label class="{{ $lbl }}">Sub</label><input name="content[modal][sub]" value="{{ $v('modal.sub') }}" class="{{ $inp }}"></div>
        <div class="sm:col-span-2"><label class="{{ $lbl }}">Question</label><input name="content[modal][question]" value="{{ $v('modal.question') }}" class="{{ $inp }}"></div>
    </div>
    @for($i = 0; $i < 3; $i++)
        <div class="grid sm:grid-cols-4 gap-3 mb-2 items-end">
            <div><label class="{{ $lbl }}">Option {{ $i+1 }} label</label><input name="content[modal][options][{{ $i }}][label]" value="{{ $v("modal.options.$i.label") }}" class="{{ $inp }}"></div>
            <div><label class="{{ $lbl }}">Sub</label><input name="content[modal][options][{{ $i }}][sub]" value="{{ $v("modal.options.$i.sub") }}" class="{{ $inp }}"></div>
            <div><label class="{{ $lbl }}">BioLinx URL</label><input name="content[modal][options][{{ $i }}][url]" value="{{ $v("modal.options.$i.url") }}" class="{{ $inp }}"></div>
            <label class="inline-flex items-center gap-2 text-sm pb-2"><input type="hidden" name="content[modal][options][{{ $i }}][auto_add]" value="0"><input type="checkbox" name="content[modal][options][{{ $i }}][auto_add]" value="1" {{ $v("modal.options.$i.auto_add") ? 'checked' : '' }} class="rounded"> Auto-add</label>
        </div>
    @endfor
</div>

<div class="card p-5">
    <h3 class="text-sm font-semibold text-gray-900 mb-4">Legal footer</h3>
    <div class="grid sm:grid-cols-3 gap-4">
        <div><label class="{{ $lbl }}">Legal entity</label><input name="content[legal][entity]" value="{{ $v('legal.entity') }}" class="{{ $inp }}"></div>
        <div><label class="{{ $lbl }}">Postal address</label><input name="content[legal][address]" value="{{ $v('legal.address') }}" class="{{ $inp }}"></div>
        <div><label class="{{ $lbl }}">Contact email</label><input name="content[legal][email]" value="{{ $v('legal.email') }}" class="{{ $inp }}"></div>
        <div class="sm:col-span-3"><label class="{{ $lbl }}">Statement</label><textarea name="content[legal][statement]" rows="3" class="{{ $ta }}">{{ $v('legal.statement') }}</textarea></div>
    </div>
</div>

<div class="card p-5">
    <h3 class="text-sm font-semibold text-gray-900 mb-1">B variant (long-form story)</h3>
    <p class="text-xs text-gray-400 mb-4">Preview at <code>{{ $lander->url }}?v=b</code>. Blank fields use the built-in story. Separate paragraphs with a blank line. Enable the 50/50 test below to send half of visitors to B.</p>
    <label class="inline-flex items-center gap-2 text-sm mb-4"><input type="hidden" name="content[ab_test][enabled]" value="0"><input type="checkbox" name="content[ab_test][enabled]" value="1" {{ $v('ab_test.enabled') ? 'checked' : '' }} class="rounded"> Run A/B test (50/50, 30-day cookie)</label>
    <div class="grid sm:grid-cols-2 gap-4">
        <div><label class="{{ $lbl }}">Kicker</label><input name="content[story][kicker]" value="{{ $v('story.kicker') }}" class="{{ $inp }}"></div>
        <div><label class="{{ $lbl }}">Headline</label><input name="content[story][headline]" value="{{ $v('story.headline') }}" class="{{ $inp }}"></div>
        <div class="sm:col-span-2"><label class="{{ $lbl }}">Deck (italic intro line)</label><input name="content[story][deck]" value="{{ $v('story.deck') }}" class="{{ $inp }}"></div>
        @foreach([1 => 'Part 1', 2 => 'Part 2 (before the compound cards)', 3 => 'Part 3 (before the supplier checks)', 4 => 'Part 4', 5 => 'Part 5 (before the kits)'] as $n => $label)
            <div><label class="{{ $lbl }}">{{ $label }} title</label><input name="content[story][p{{ $n }}_title]" value="{{ $v("story.p{$n}_title") }}" class="{{ $inp }}"></div>
            <div class="sm:col-span-2"><label class="{{ $lbl }}">{{ $label }} text</label><textarea name="content[story][p{{ $n }}]" rows="5" class="{{ $ta }}">{{ $v("story.p{$n}") }}</textarea></div>
        @endforeach
        <div class="sm:col-span-2"><label class="{{ $lbl }}">P.S.</label><textarea name="content[story][ps]" rows="2" class="{{ $ta }}">{{ $v('story.ps') }}</textarea></div>
    </div>
</div>
