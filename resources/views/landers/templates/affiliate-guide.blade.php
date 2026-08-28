@php
    // ------------------------------------------------------------------
    // "Affiliate Guide" template: one honest buyer's guide handed from an
    // affiliate to the people they know. Reusable: every affiliate = one
    // `landers` row with their name, photo, note, code and iDev id.
    //
    // Compliance posture (see .claude/skills/compliance-review): real names,
    // no testimonials, no outcome or dosing language, plain offer, legal footer.
    // Tracking: x-meta-pixel + x-posthog-lander + every Biolinx link via /go.
    // ------------------------------------------------------------------
    $c = fn (string $path, $default = '') => $lander->c($path, $default);

    $aff = [
        'name'       => $c('affiliate.name', 'Your name here'),
        'first'      => $c('affiliate.first_name') ?: explode(' ', trim($c('affiliate.name', 'Your')))[0],
        'role'       => $c('affiliate.role', 'Amateur MMA · Your gym, Your city'),
        'photo'      => $c('affiliate.photo_url', ''),
        'note'       => $c('affiliate.note', "Every week someone at the gym asks me about peptides. Usually after they already paid a site with no address, no test results and a chat box nobody answers. I made this page so I can stop repeating myself. It tells you what these compounds are, what the research actually looks at, and how I check a supplier before I spend a dollar. If you decide to buy, my code takes {discount} off and BioLinx pays me a commission. That is the whole deal. Nothing here is medical advice and I am not going to tell you what to do with your body."),
        'code'       => strtoupper($c('affiliate.code', 'YOURCODE')),
        'discount'   => $c('affiliate.discount', '15%'),
        'idev_id'    => $c('affiliate.idev_id', ''),
    ];

    // Biolinx link builder: coupon + iDev id (+ optional auto-add) on the destination,
    // then routed through /go so UTMs, fbclid/fbp/fbc, pp_lander and email are forwarded.
    $bl = function (string $url, bool $add = false) use ($aff, $lander): string {
        $q = ['discount' => $aff['code']];
        if ($aff['idev_id'] !== '') { $q['idev_id'] = $aff['idev_id']; }
        if ($add) { $q['add'] = 1; }
        $dest = $url . (str_contains($url, '?') ? '&' : '?') . http_build_query($q);
        return $lander->outbound_slug
            ? route('outbound.track', $lander->outbound_slug) . '?dest=' . urlencode($dest)
            : $dest;
    };
    $store = 'https://biolinxlabs.com';

    $hero = [
        'eyebrow'   => $c('hero.eyebrow', '{first}\'s stack · research use only'),
        'headline'  => $c('hero.headline', 'This is my stack.'),
        'sub'       => $c('hero.sub', 'Four compounds, one supplier, and the reason I stopped buying from random sites. What each one is, why researchers study it, how I check a supplier before I spend a dollar, and my code for {discount} off at BioLinx Labs. BioLinx pays me a commission when you use it. Everything here is sold for laboratory research only.'),
        'primary'   => $c('hero.primary_cta', 'Get {first}\'s code'),
        'secondary' => $c('hero.secondary_cta', 'Read the guide first'),
        'image'     => $c('hero.image_url', 'https://pub-0a9781e86a6b4f2d9b5bfbe22904ad3c.r2.dev/media/e44bf71a-0bed-45cb-9474-18a666054235.jpg'),
        'image_alt' => $c('hero.image_alt', 'Sebastian\'s Stack: four BioLinx Labs research vials, research use only'),
    ];
    $fill = fn (string $s) => str_replace(['{first}', '{name}', '{discount}', '{code}'], [$aff['first'], $aff['name'], $aff['discount'], $aff['code']], $s);

    $compounds = $c('compounds.items') ?: [
        ['name' => 'BPC-157', 'tag' => $fill('{first}\'s pick · sold on its own'), 'image' => 'https://assets.sticky.io/images/originals/2026-06-03-16-00-00/GsUOiu6iQyRa7yhe4iKjWudrXNMwjfFMkxnnm7ip.jpg',
         'what' => 'A synthetic 15-amino-acid fragment of a protein found in gastric juice. The blend below pairs it with TB-500; this is the single compound by itself.',
         'studied' => 'Cell signaling and how cells move, in cell culture and rodent models. The single most asked-about peptide in any gym, and still without a completed human trial.',
         'url' => $store . '/products/bpc-157-10-mg', 'price' => '$64.93 · 10 mg'],
        ['name' => 'BPC-157 / TB-500 Blend', 'tag' => 'Two peptides, one vial', 'image' => 'https://assets.sticky.io/images/originals/2026-03-04-15-00-00/NrccqLaqcaXiJ6RYQgN6VNftL4mhYxs7thnMYPSX.jpg',
         'what' => 'A synthetic 15-amino-acid fragment originally derived from a gastric protein, combined with a synthetic fragment of thymosin beta-4.',
         'studied' => 'How cells signal each other and migrate, in culture and in rodent models. One of the most searched peptide pairings online, and one of the least understood.',
         'not' => 'No completed human trials. Not approved by the FDA for any use.',
         'url' => $store . '/products/bpc-157-tb-500-blend-10-mg', 'price' => '$79.93 · 10 mg'],
        ['name' => 'GHK-Cu', 'tag' => 'Copper tripeptide', 'image' => 'https://assets.sticky.io/images/originals/2026-03-04-14-00-00/kxUj8zIjNVkVD5Zu3AYGfp8gObLHeOWUVQrTR20o.jpg',
         'what' => 'Glycyl-histidyl-lysine bound to copper, a tripeptide that occurs naturally in plasma.',
         'studied' => 'How a copper-bound peptide switches genes on and off in skin cells in culture. The one you will see in serious skincare.',
         'not' => 'Cosmetic use is common; effects of research-grade material are not established.',
         'url' => $store . '/products/ghk-cu-50-mg', 'price' => '$49.93 · 50 mg'],
        ['name' => 'CJC-1295 (no DAC) + Ipamorelin', 'tag' => 'Secretagogue analog pair', 'image' => 'https://assets.sticky.io/images/originals/2026-06-03-16-00-00/1oIZpaZn6asah6I1hg7k5PLASUU92exKdgG1Zj3o.jpg',
         'what' => 'Two synthetic analogs that are usually studied together because they act on related receptors.',
         'studied' => 'Growth hormone signaling in animal models: how the pituitary answers a nudge. Studied as a pair because they act on related receptors.',
         'not' => 'Neither is approved for any use. Long-term data does not exist.',
         'url' => $store . '/products/cjc-1295-wo-dac-ipamorelin-10mg', 'price' => '$79.93 · 10 mg'],
        ['name' => 'MOTS-c', 'tag' => 'Mitochondrial peptide', 'image' => 'https://assets.sticky.io/images/originals/2026-03-04-14-00-00/10qdl1PXP0W95myLg6fvyzgxP6SxkCd9BNVuBNfp.jpg',
         'what' => 'A 16-amino-acid peptide encoded in mitochondrial DNA, discovered in 2015.',
         'studied' => 'How cells make and spend energy, in cell and rodent models. Discovered in 2015 inside mitochondrial DNA. Most of what you have read about it online comes from mouse studies.',
         'not' => 'First human trials are early-stage. Nothing is approved.',
         'url' => $store . '/products/mots-c-10-mg', 'price' => '$49.93 · 10 mg'],
    ];

    $vetting = $c('vetting.items') ?: [
        ['title' => 'Ask for the COA before you pay', 'body' => 'A certificate of analysis from a third-party lab, with a batch number that matches the vial. No COA, no order. In-house "testing" does not count.'],
        ['title' => 'Scan the code on the bottle', 'body' => 'BioLinx prints a permanent QR link on every label. Scan it and the current COA for that exact batch opens. If a supplier cannot do this, ask why.'],
        ['title' => 'Check where it ships from', 'body' => 'US warehouse, US-only shipping, a real business address and a support chat with a person behind it. Overseas drop-shippers are where most horror stories start.'],
        ['title' => 'Look at how they take payment', 'body' => 'Card, Zelle and bank transfer are normal. Crypto-only or gift cards mean there is no one to call when the box never shows up.'],
        ['title' => 'Read the label language', 'body' => 'Legit suppliers say "research use only" and mean it. Anyone promising you results, doses or "protocols" is selling something the law says they cannot.'],
    ];
    $vettingImage = $c('vetting.image_url', 'https://pub-0a9781e86a6b4f2d9b5bfbe22904ad3c.r2.dev/media/805c18e6-6a8f-4f80-9847-a587affb89da.jpg');

    $kits = $c('kits.items') ?: [
        ['name' => $fill('{first}\'s Stack'), 'contents' => 'BPC-157 / TB-500 Blend 10 mg ×2 · GHK-Cu 50 mg · CJC-1295 + Ipamorelin 10 mg · MOTS-c 10 mg · Bacteriostatic water 10 ml ×3',
         'price' => '$399.44', 'price_with_code' => '$339.52', 'note' => 'Free US shipping. Research use only.', 'image' => 'https://pub-0a9781e86a6b4f2d9b5bfbe22904ad3c.r2.dev/media/149350ca-ec7c-4f9b-9bc9-0072417e955b.jpg',
         'url' => $store . '/bundles/sebastians-stack', 'auto_add' => true, 'featured' => true],
        ['name' => 'BPC-157 on its own', 'contents' => 'BPC-157 10 mg, single vial. ' . $fill('{first}\'s personal pick.'),
         'price' => '$64.93', 'price_with_code' => '$55.19', 'note' => 'Add bacteriostatic water at checkout if you need it.', 'image' => 'https://pub-0a9781e86a6b4f2d9b5bfbe22904ad3c.r2.dev/media/bcc22d77-4639-4776-a7e7-e8c3bca36c9e.jpg',
         'url' => $store . '/products/bpc-157-10-mg', 'auto_add' => false, 'featured' => false],
    ];
    $blendUrl = $store . '/products/bpc-157-tb-500-blend-10-mg';

    $faqs = $c('faq.items') ?: [
        ['q' => 'Is it legal to buy this?', 'a' => 'These compounds are sold in the US as research chemicals, labeled research use only and not for human consumption. BioLinx requires you to be 21+ and to accept a research-use agreement at checkout. This page is education, not medical advice. Talk to a licensed clinician about anything health-related.'],
        ['q' => 'What does the code actually do?', 'a' => $fill('It takes {discount} off your order at biolinxlabs.com. Enter {code} at checkout, or use any button on this page and it is applied for you.')],
        ['q' => $fill('Does {first} see my order?'), 'a' => $fill('No. {first} gets a commission notice from BioLinx that a sale happened with his code. Your name, address and order details stay with BioLinx.')],
        ['q' => 'How do I verify what I received?', 'a' => 'Scan the QR code on the label. It opens the certificate of analysis for that batch on biolinxlabs.com. You can also open the COA from the product page before you order.'],
        ['q' => 'Shipping and payment?', 'a' => 'US addresses only, shipped from the US, free shipping on orders over $200. Pay by card, Zelle or bank transfer (ACH).'],
        ['q' => 'Why is this page on Professor Peptides and not BioLinx?', 'a' => 'Professor Peptides is the education side; it sells nothing. BioLinx Labs is the supplier. Keeping the explanation separate from the store is on purpose.'],
    ];

    $modal = [
        'title'  => $c('modal.title', $fill('Get {first}\'s code')),
        'sub'    => $c('modal.sub', $fill('{discount} off at BioLinx Labs. One tap, your email, done.')),
        'q'      => $c('modal.question', 'What are you looking at?'),
        'options'=> $c('modal.options') ?: [
            ['label' => $fill('{first}\'s Stack'), 'sub' => 'The whole package, 7 vials', 'url' => $store . '/bundles/sebastians-stack', 'auto_add' => true],
            ['label' => 'Just BPC-157', 'sub' => $fill('{first}\'s pick, single vial'), 'url' => $store . '/products/bpc-157-10-mg', 'auto_add' => false],
            ['label' => 'Just browsing', 'sub' => 'Show me the store', 'url' => $store . '/best-sellers', 'auto_add' => false],
        ],
        'success_title' => $c('modal.success_title', 'Your code'),
        'success_body'  => $c('modal.success_body', $fill('Copy it, or open BioLinx with it already applied.')),
    ];

    $legal = [
        'entity'    => $c('legal.entity', 'BioLinx Labs'),
        'address'   => $c('legal.address', ''),
        'email'     => $c('legal.email', 'support@biolinxlabs.com'),
        'statement' => $c('legal.statement', 'All products referenced are sold for laboratory research use only and are not for human consumption, medical use, diagnosis, treatment or prevention of disease. Nothing on this page is medical advice. This page contains affiliate links: the person named above is paid a commission by BioLinx Labs on purchases made with their code.'),
    ];
    $trackSlug = $lander->slug;
@endphp
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  @if($lander->noindex)<meta name="robots" content="noindex,nofollow" />@endif
  <title>{{ $c('meta.title', $fill('Before you buy peptides: {first}\'s guide | Professor Peptides')) }}</title>
  <meta name="description" content="{{ $c('meta.description', $fill('A plain-English guide to research peptides from {name}: what they are, what the research says, how to check a supplier, and a {discount} code for BioLinx Labs.')) }}" />
  <meta property="og:type" content="website" />
  <meta property="og:site_name" content="Professor Peptides" />
  <meta property="og:title" content="{{ $c('meta.title', $fill('Before you buy peptides: {first}\'s guide')) }}" />
  <meta property="og:description" content="{{ $c('meta.description', $fill('What research peptides are, what the research says, how to check a supplier, and {first}\'s {discount} code for BioLinx Labs.')) }}" />
  <meta property="og:image" content="{{ $hero['image'] }}" />
  <meta property="og:url" content="{{ url()->current() }}" />
  <meta name="twitter:card" content="summary_large_image" />
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="preconnect" href="https://pub-0a9781e86a6b4f2d9b5bfbe22904ad3c.r2.dev" crossorigin>
  <link rel="preconnect" href="https://assets.sticky.io" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Big+Shoulders+Display:wght@700;800;900&family=IBM+Plex+Sans:ital,wght@0,400;0,500;0,600;0,700;1,400&family=IBM+Plex+Mono:wght@400;500;600&display=swap" rel="stylesheet">
  @verbatim<style>
:root{
  --paper:#F4F5F3;--paper-2:#FFFFFF;--slab:#E4E7E3;--line:#D3D8D2;
  --ink:#15181B;--ink-2:#2B3136;--steel:#5B6670;--muted:#7C8791;
  --red:#C41E1E;--red-2:#A51717;--red-tint:#FBECEC;
  --display:'Big Shoulders Display',Impact,'Arial Narrow',sans-serif;
  --sans:'IBM Plex Sans',system-ui,-apple-system,Segoe UI,Roboto,sans-serif;
  --mono:'IBM Plex Mono',ui-monospace,SFMono-Regular,Menlo,monospace;
  --radius:14px;--shadow:0 18px 50px rgba(21,24,27,.10);
}
*{box-sizing:border-box}
html{scroll-behavior:smooth;-webkit-text-size-adjust:100%}
body{margin:0;background:var(--paper);color:var(--ink);font-family:var(--sans);font-size:17px;line-height:1.6}
img{max-width:100%;display:block}
a{color:inherit}
h1,h2,h3,p{margin:0}
h1,h2{font-family:var(--display);text-transform:uppercase;line-height:.92;letter-spacing:.005em;font-weight:900}
h1{font-size:clamp(46px,8.6vw,96px)}
h2{font-size:clamp(34px,5.4vw,62px)}
h3{font-family:var(--sans);font-weight:700;font-size:19px;line-height:1.25}
.mono{font-family:var(--mono)}
.eyebrow{font-family:var(--mono);font-size:12px;font-weight:600;letter-spacing:.14em;text-transform:uppercase;color:var(--steel)}
.eyebrow.red{color:var(--red)}
.wrap{width:min(100% - 32px,1160px);margin:0 auto}
.skip{position:absolute;left:-999px}.skip:focus{left:12px;top:12px;z-index:999;background:#fff;padding:10px;border-radius:8px}

/* top bar */
.top{position:sticky;top:0;z-index:60;background:rgba(244,245,243,.92);backdrop-filter:blur(10px);border-bottom:1px solid var(--line)}
.top .wrap{display:flex;align-items:center;justify-content:space-between;gap:16px;min-height:58px}
.brand{display:flex;align-items:center;gap:10px;text-decoration:none}
.brand-mark{width:30px;height:30px;border:2px solid var(--ink);display:grid;place-items:center;font-family:var(--display);font-weight:900;font-size:16px;border-radius:8px}
.brand-text{font-family:var(--mono);font-size:11px;letter-spacing:.16em;text-transform:uppercase;line-height:1.15}
.brand-text em{display:block;font-style:normal;color:var(--muted);font-size:9px}
.top-note{font-family:var(--mono);font-size:11px;color:var(--steel);letter-spacing:.06em}
.top-note b{color:var(--ink)}
.btn{display:inline-flex;align-items:center;justify-content:center;gap:10px;min-height:52px;padding:14px 22px;border-radius:999px;border:0;cursor:pointer;text-decoration:none;font-family:var(--sans);font-weight:700;font-size:16px;line-height:1;transition:transform .12s ease,box-shadow .12s ease,background .12s ease}
.btn:focus-visible,button:focus-visible,a:focus-visible,input:focus-visible{outline:3px solid var(--red);outline-offset:3px}
.btn.primary{background:var(--red);color:#fff;box-shadow:0 10px 24px rgba(196,30,30,.25)}
.btn.primary:hover{background:var(--red-2);transform:translateY(-1px)}
.btn.ink{background:var(--ink);color:#fff}
.btn.ink:hover{background:#000;transform:translateY(-1px)}
.btn.ghost{background:transparent;color:var(--ink);border:1.5px solid var(--ink)}
.btn.ghost:hover{background:var(--ink);color:#fff}
.btn.sm{min-height:42px;padding:10px 16px;font-size:14px}
.top .btn{display:none}

/* hero */
.hero{padding:44px 0 26px}
.hero-grid{display:grid;gap:28px;align-items:end}
.hero h1{max-width:12ch;margin-top:12px}
.hero h1 .u{display:inline-block;position:relative}
.hero h1 .u:after{content:'';position:absolute;left:0;right:0;bottom:.04em;height:.14em;background:var(--red);z-index:-1}
.hero-sub{margin-top:18px;max-width:58ch;font-size:18px;color:var(--ink-2)}
.hero-actions{display:flex;gap:12px;flex-wrap:wrap;margin-top:24px}
.hero-fine{margin-top:14px;font-family:var(--mono);font-size:12px;color:var(--muted)}
.hero-media{position:relative;border-radius:var(--radius);overflow:hidden;background:var(--slab);aspect-ratio:16/9}
.hero-media img{width:100%;height:100%;object-fit:cover}
.hero-media .tag{position:absolute;left:14px;bottom:14px;background:rgba(21,24,27,.85);color:#fff;font-family:var(--mono);font-size:11px;letter-spacing:.1em;text-transform:uppercase;padding:8px 10px;border-radius:6px}

/* layout: main + sticky corner card */
.layout{display:grid;gap:28px;padding:18px 0 60px}
.main>section{padding:44px 0 8px;border-top:1px solid var(--line)}
.main>section:first-child{border-top:0;padding-top:20px}
.sec-head{display:grid;gap:10px;margin-bottom:22px}
.sec-head p{color:var(--steel);max-width:62ch}

/* corner card (signature) */
.corner{position:relative;background:var(--paper-2);border:1px solid var(--line);border-radius:18px;padding:22px 20px 20px;box-shadow:var(--shadow)}
.corner:before{content:'';position:absolute;top:10px;left:50%;transform:translateX(-50%);width:44px;height:8px;border-radius:999px;background:var(--slab)}
.corner .tape{position:absolute;top:-12px;right:18px;background:var(--red);color:#fff;font-family:var(--mono);font-size:10px;letter-spacing:.14em;text-transform:uppercase;padding:6px 10px;border-radius:4px;transform:rotate(-2deg)}
.corner-head{display:flex;align-items:center;gap:14px;margin-top:10px}
.corner-photo{width:64px;height:64px;border-radius:50%;object-fit:cover;background:var(--slab);border:2px solid var(--paper)}
.corner-photo.ph{display:grid;place-items:center;color:var(--muted)}
.corner-name{font-family:var(--display);font-size:26px;text-transform:uppercase;line-height:1;font-weight:900}
.corner-role{font-family:var(--mono);font-size:11px;color:var(--steel);margin-top:4px;letter-spacing:.04em}
.corner-note{margin-top:14px;font-size:15px;color:var(--ink-2);border-left:3px solid var(--red);padding-left:12px}
.corner-code{margin-top:16px;background:var(--paper);border:1px dashed var(--line);border-radius:12px;padding:12px 14px;display:flex;align-items:center;justify-content:space-between;gap:10px}
.corner-code .lab{font-family:var(--mono);font-size:10px;letter-spacing:.14em;text-transform:uppercase;color:var(--muted)}
.corner-code .val{font-family:var(--mono);font-weight:600;font-size:18px;letter-spacing:.06em;filter:blur(5px);user-select:none}
.corner-code.revealed .val{filter:none;user-select:text}
.corner .btn{width:100%;margin-top:12px}
.corner-disc{margin-top:12px;font-family:var(--mono);font-size:11px;color:var(--muted);line-height:1.5}
aside .corner{position:sticky;top:78px}

/* note section (mobile inline version of the corner card) */
.note-inline{display:block}

/* compounds: tale-of-the-tape rows */
.tape-list{display:grid;gap:14px}
.tape{display:block;background:var(--paper-2);border:1px solid var(--line);border-radius:var(--radius);padding:16px}
.tape-head{display:flex;align-items:center;gap:14px;margin-bottom:10px}
.tape-img{flex:0 0 auto;width:64px;height:64px;border-radius:10px;background:#fff;border:1px solid var(--line);display:grid;place-items:center;overflow:hidden}
.tape-img img{width:100%;height:100%;object-fit:contain;padding:6px}
.tape-name{display:flex;flex-direction:column;gap:2px}
.tape-name h3{font-family:var(--display);font-size:26px;text-transform:uppercase;font-weight:900;line-height:1}
.tape-name .tg{font-family:var(--mono);font-size:11px;color:var(--steel);letter-spacing:.06em}
.tape-rows{display:grid;gap:0}
.tape-row{display:grid;grid-template-columns:1fr;gap:2px;font-size:15px;line-height:1.45;padding:8px 0;border-top:1px dashed var(--line)}
.tape-row .k{font-family:var(--mono);font-size:11px;letter-spacing:.1em;text-transform:uppercase;color:var(--steel);padding-top:3px}
.tape-row.not .k{color:var(--red)}
.tape-foot{margin-top:10px;display:flex;justify-content:space-between;align-items:center;gap:10px;flex-wrap:wrap}
.tape-foot .price{font-family:var(--mono);font-size:13px;color:var(--steel)}
.tape-foot a{font-weight:700;font-size:14px;color:var(--ink);text-decoration:none;border-bottom:2px solid var(--red)}

/* vetting */
.vet{display:grid;gap:22px}
.vet-img{border-radius:var(--radius);overflow:hidden;background:var(--slab);aspect-ratio:4/5;max-height:420px}
.vet-img img{width:100%;height:100%;object-fit:cover}
.vet-list{display:grid;gap:0;counter-reset:vet}
.vet-item{display:grid;grid-template-columns:44px 1fr;gap:12px;padding:14px 0;border-top:1px solid var(--line)}
.vet-item:first-child{border-top:0;padding-top:0}
.vet-item .n{font-family:var(--display);font-size:34px;font-weight:900;line-height:.9;color:var(--red)}
.vet-item p{color:var(--ink-2);font-size:15px;margin-top:4px}
.vet-proof{margin-top:16px;background:var(--ink);color:#fff;border-radius:var(--radius);padding:16px 18px;display:flex;gap:14px;align-items:flex-start}
.vet-proof .mono{font-size:12px;color:#B9C2CB}
.vet-proof a{color:#fff}

/* kits */
.kits{display:grid;gap:14px}
.kit{position:relative;background:var(--paper-2);border:1px solid var(--line);border-radius:var(--radius);padding:20px;display:grid;gap:8px}
.kit.featured{border-color:var(--ink);box-shadow:var(--shadow)}
.kit .flag{position:absolute;top:-11px;left:18px;background:var(--ink);color:#fff;font-family:var(--mono);font-size:10px;letter-spacing:.14em;text-transform:uppercase;padding:5px 9px;border-radius:4px}
.kit h3{font-family:var(--display);font-size:28px;text-transform:uppercase;font-weight:900;line-height:1}
.kit .contents{font-size:14px;color:var(--steel)}
.kit .price{font-family:var(--mono);font-size:22px;font-weight:600;margin-top:4px}
.kit .price small{font-size:12px;color:var(--steel);font-weight:400;margin-left:8px}
.kit .note{font-family:var(--mono);font-size:11px;color:var(--muted)}
.kit .btn{margin-top:8px}
.kits-fine{margin-top:12px;font-family:var(--mono);font-size:11px;color:var(--muted)}

/* single stack package */
.stack{background:var(--paper-2);border:1.5px solid var(--ink);border-radius:var(--radius);overflow:hidden;box-shadow:var(--shadow);display:grid}
.stack-img{background:var(--slab);aspect-ratio:16/9}
.stack-img img{width:100%;height:100%;object-fit:cover;object-position:left center}
.stack-body{padding:20px;display:grid;gap:8px;position:relative}
.stack-body .flag{position:static;display:inline-block;width:max-content;margin-bottom:2px;background:var(--red);color:#fff;font-family:var(--mono);font-size:10px;letter-spacing:.14em;text-transform:uppercase;padding:5px 9px;border-radius:4px}
.stack-body h3{font-family:var(--display);font-size:38px;text-transform:uppercase;font-weight:900;line-height:.95;margin-top:2px}
.stack-body .contents{font-size:14px;color:var(--steel);line-height:1.5}
.stack-price{display:flex;align-items:baseline;gap:10px;flex-wrap:wrap;margin-top:4px}
.stack-price .was{font-family:var(--mono);font-size:15px;color:var(--muted);text-decoration:line-through}
.stack-price .now{font-family:var(--mono);font-size:30px;font-weight:600}
.stack-price .lab{font-family:var(--mono);font-size:11px;letter-spacing:.1em;text-transform:uppercase;color:var(--red)}
.stack-body .note{font-family:var(--mono);font-size:11px;color:var(--muted)}
.stack-body .btn{margin-top:6px}
.stack-body .alt{font-size:13px;color:var(--steel)}
.stack-body .alt a{color:var(--ink);font-weight:700;border-bottom:2px solid var(--red);text-decoration:none}
@media (min-width:700px){ .stack{grid-template-columns:1.1fr .9fr} .stack-img{aspect-ratio:auto;min-height:100%} }

/* two or three package cards */
.stack-grid{display:grid;gap:14px}
.stack.compact{grid-template-columns:1fr}
.stack.compact .stack-img{aspect-ratio:16/9;min-height:0}
.stack.compact .stack-body h3{font-size:30px}
.stack.compact:not(.featured){border-color:var(--line);box-shadow:none}
.stack.compact:not(.featured) .flag{background:var(--ink)}
@media (min-width:700px){ .stack-grid{grid-template-columns:1.25fr 1fr;align-items:stretch} }

/* faq */
.faq{display:grid;gap:0;border-top:1px solid var(--line)}
.faq details{border-bottom:1px solid var(--line)}
.faq summary{list-style:none;cursor:pointer;padding:16px 0;font-weight:700;display:flex;justify-content:space-between;gap:12px;align-items:center}
.faq summary::-webkit-details-marker{display:none}
.faq summary:after{content:'+';font-family:var(--mono);color:var(--red);font-size:20px;flex:0 0 auto}
.faq details[open] summary:after{content:'\2013'}
.faq .a{padding:0 0 18px;color:var(--ink-2);font-size:15px;max-width:70ch}

/* footer */
.foot{border-top:2px solid var(--ink);padding:26px 0 120px;font-size:13px;color:var(--steel)}
.foot .links{display:flex;gap:14px;flex-wrap:wrap;font-family:var(--mono);font-size:12px;margin-bottom:12px}
.foot .links a{color:var(--ink);text-decoration:none;border-bottom:1px solid var(--line)}
.foot p{max-width:90ch;margin-top:6px;line-height:1.55}

/* sticky mobile bar */
.bar{position:fixed;left:12px;right:12px;bottom:calc(12px + env(safe-area-inset-bottom));z-index:70;display:flex;align-items:center;justify-content:space-between;gap:12px;background:var(--ink);color:#fff;border-radius:999px;padding:8px 8px 8px 18px;box-shadow:0 18px 40px rgba(21,24,27,.3)}
.bar .t{font-family:var(--mono);font-size:12px;line-height:1.25}
.bar .t b{display:block;font-size:13px}
.bar .btn{min-height:44px;padding:10px 16px;font-size:14px}

/* modal */
.pp-modal-open{overflow:hidden}
.ov{position:fixed;inset:0;z-index:90;background:rgba(21,24,27,.62);display:none;align-items:flex-end;justify-content:center;padding:0}
.ov.is-open{display:flex}
.md{position:relative;width:100%;max-width:520px;background:var(--paper-2);border-radius:22px 22px 0 0;padding:24px 22px 28px;box-shadow:0 -10px 60px rgba(0,0,0,.35);max-height:92vh;overflow:auto}
.md-close{position:absolute;top:12px;right:12px;width:38px;height:38px;border-radius:50%;border:0;background:var(--slab);font-size:22px;line-height:1;cursor:pointer}
.md h3{font-family:var(--display);font-size:34px;text-transform:uppercase;font-weight:900;line-height:.95}
.md .sub{color:var(--steel);margin-top:8px;font-size:15px}
.md .step-lab{font-family:var(--mono);font-size:11px;letter-spacing:.14em;text-transform:uppercase;color:var(--red);margin-bottom:8px}
.opts{display:grid;gap:10px;margin-top:16px}
.opt{display:flex;align-items:center;justify-content:space-between;gap:10px;text-align:left;background:var(--paper);border:1.5px solid var(--line);border-radius:14px;padding:14px 16px;cursor:pointer;font-family:var(--sans)}
.opt:hover,.opt.on{border-color:var(--ink)}
.opt b{font-size:16px;display:block}
.opt span{font-family:var(--mono);font-size:12px;color:var(--steel)}
.opt i{font-style:normal;color:var(--red);font-weight:700}
.fld{display:grid;gap:6px;margin-top:12px}
.fld label{font-family:var(--mono);font-size:11px;letter-spacing:.12em;text-transform:uppercase;color:var(--steel)}
.fld input{width:100%;font:inherit;font-size:16px;padding:13px 14px;border:1.5px solid var(--line);border-radius:12px;background:#fff}
.fld input:focus{border-color:var(--ink)}
.md .btn{width:100%;margin-top:14px}
.md .fine{font-family:var(--mono);font-size:11px;color:var(--muted);margin-top:10px;line-height:1.5}
.code-box{margin-top:16px;background:var(--ink);color:#fff;border-radius:16px;padding:18px;display:flex;align-items:center;justify-content:space-between;gap:12px}
.code-box .val{font-family:var(--mono);font-size:28px;font-weight:600;letter-spacing:.08em}
.code-box .cp{background:#fff;color:var(--ink);border:0;border-radius:999px;padding:10px 14px;font-weight:700;cursor:pointer;font-family:var(--sans)}

/* reveal animation */
@media (prefers-reduced-motion:no-preference){
  .rv{opacity:0;transform:translateY(14px);transition:opacity .5s ease,transform .5s ease}
  .rv.in{opacity:1;transform:none}
}

/* tablet+ */
@media (min-width:700px){
  .hero-grid{grid-template-columns:1.05fr .95fr;align-items:center}
  .vet{grid-template-columns:.8fr 1.2fr;align-items:start}
  .kits{grid-template-columns:repeat(3,1fr)}
  .kit{padding:24px 20px}
  .top .btn{display:inline-flex}
  .ov{align-items:center;padding:20px}
  .md{border-radius:22px}
}
/* desktop: main + sticky corner card */
@media (min-width:1024px){
  .layout{grid-template-columns:minmax(0,1fr) 340px;gap:48px}
  .note-inline{display:none}
  .bar{display:none}
  .foot{padding-bottom:40px}
  .tape{padding:20px}
  .tape-img{width:88px;height:88px}
  .tape-name{flex-direction:row;align-items:baseline;flex-wrap:wrap;gap:8px 12px}
  .tape-row{grid-template-columns:150px 1fr;gap:12px}
}
@media (max-width:1023px){ aside{display:none} }
  </style>@endverbatim
  <x-meta-pixel />
  <x-posthog-lander />
</head>
<body>
<a class="skip" href="#guide">Skip to the guide</a>

<header class="top">
  <div class="wrap">
    <a class="brand" href="{{ url('/') }}" aria-label="Professor Peptides">
      <span class="brand-mark">P</span>
      <span class="brand-text">Professor Peptides<em>Sponsored guide · BioLinx Labs</em></span>
    </a>
    <span class="top-note">Code <b>{{ $aff['code'] }}</b> · {{ $aff['discount'] }} off</span>
    <button class="btn primary sm" type="button" data-open-modal data-placement="topbar">{{ $fill($hero['primary']) }}</button>
  </div>
</header>

<main>
  <section class="hero">
    <div class="wrap hero-grid">
      <div>
        <p class="eyebrow red">{{ $fill($hero['eyebrow']) }}</p>
        <h1>{!! preg_replace('/\b(my stack)\b/i', '<span class="u">$1</span>', e($fill($hero['headline']))) !!}</h1>
        <p class="hero-sub">{{ $fill($hero['sub']) }}</p>
        <div class="hero-actions">
          <button class="btn primary" type="button" data-open-modal data-placement="hero">{{ $fill($hero['primary']) }}</button>
          <a class="btn ghost" href="#guide">{{ $fill($hero['secondary']) }}</a>
        </div>
        <p class="hero-fine">Research use only · 21+ · US shipping only · Affiliate link disclosed</p>
      </div>
      <div class="hero-media">
        <img src="{{ $hero['image'] }}" alt="{{ $hero['image_alt'] }}" fetchpriority="high" width="1600" height="900">
        <span class="tag">Sold by BioLinx Labs · research use only</span>
      </div>
    </div>
  </section>

  <div class="wrap layout" id="guide">
    <div class="main">

      {{-- Affiliate note (inline on mobile; the sticky corner card carries it on desktop) --}}
      <section class="note-inline rv">
        @include('landers.templates.partials.affiliate-corner', ['aff' => $aff, 'fill' => $fill, 'tape' => 'From your corner'])
      </section>

      {{-- Kits --}}
      <section id="kits" class="rv">
        <div class="sec-head">
          <p class="eyebrow">{{ $c('kits.eyebrow', 'One package, one code') }}</p>
          <h2>{{ $c('kits.heading', $fill('{first}\'s Stack')) }}</h2>
          <p>{{ $c('kits.sub', $fill('The whole stack in one package: four compounds and three bacteriostatic water, from the one supplier that passes every check below. The button applies {code} for {discount} off and lands you at BioLinx with it already in the cart. No countdown, no fake stock counter.')) }}</p>
        </div>
        @if(count($kits) === 1)
        @php $k = $kits[0]; @endphp
        <article class="stack">
          <div class="stack-img"><img src="{{ $k['image'] ?? $hero['image'] }}" alt="{{ $k['name'] ?? '' }}: the full bundle" width="1200" height="675"></div>
          <div class="stack-body">
            <span class="flag">{{ $c('kits.flag', 'One package') }}</span>
            <h3>{{ $k['name'] ?? '' }}</h3>
            <p class="contents">{{ $k['contents'] ?? '' }}</p>
            <div class="stack-price">
              <span class="was">{{ $k['price'] ?? '' }}</span>
              <span class="now">{{ $k['price_with_code'] ?? $k['price'] ?? '' }}</span>
              <span class="lab">with {{ $aff['code'] }}</span>
            </div>
            @if(!empty($k['note']))<p class="note">{{ $k['note'] }}</p>@endif
            <a class="btn primary" href="{{ $bl($k['url'] ?? $store, !empty($k['auto_add'])) }}" data-track="kit" data-name="{{ $k['name'] ?? '' }}">{{ $c('kits.cta', $fill('Add {first}\'s Stack')) }}</a>
            <p class="alt">{{ $c('kits.alt_text', 'Only want the blend?') }} <a href="{{ $bl($blendUrl) }}" data-track="kit" data-name="Blend only">{{ $c('kits.alt_cta', 'Get it with the code') }}</a></p>
          </div>
        </article>
        @else
        <div class="stack-grid">
          @foreach($kits as $k)
          <article class="stack compact {{ !empty($k['featured']) ? 'featured' : '' }}">
            <div class="stack-img"><img src="{{ $k['image'] ?? '' }}" alt="{{ $k['name'] ?? '' }}" width="800" height="450"></div>
            <div class="stack-body">
              <span class="flag">{{ !empty($k['featured']) ? $c('kits.flag', 'One package') : $c('kits.flag_alt', 'Single vial') }}</span>
              <h3>{{ $k['name'] ?? '' }}</h3>
              <p class="contents">{{ $k['contents'] ?? '' }}</p>
              <div class="stack-price"><span class="was">{{ $k['price'] ?? '' }}</span><span class="now">{{ $k['price_with_code'] ?? $k['price'] ?? '' }}</span><span class="lab">with {{ $aff['code'] }}</span></div>
              @if(!empty($k['note']))<p class="note">{{ $k['note'] }}</p>@endif
              <a class="btn {{ !empty($k['featured']) ? 'primary' : 'ink' }}" href="{{ $bl($k['url'] ?? $store, !empty($k['auto_add'])) }}" data-track="kit" data-name="{{ $k['name'] ?? '' }}">{{ !empty($k['featured']) ? $c('kits.cta', $fill('Add {first}\'s Stack')) : $c('kits.cta_alt', 'Get it with the code') }}</a>
            </div>
          </article>
          @endforeach
        </div>
        @endif
        <p class="kits-fine">{{ $c('kits.fine', 'Research use only. Not for human consumption. 21+ and a research-use agreement are required at checkout.') }}</p>
      </section>

      {{-- Compounds --}}
      <section id="compounds" class="rv">
        <div class="sec-head">
          <p class="eyebrow">{{ $c('compounds.eyebrow', 'The stack, vial by vial') }}</p>
          <h2>{{ $c('compounds.heading', 'What is in it and why') }}</h2>
          <p>{{ $c('compounds.sub', 'Four research compounds plus bacteriostatic water. Here is what each one actually is and why researchers keep studying it, in plain English. All of them are research chemicals.') }}</p>
        </div>
        <div class="tape-list">
          @foreach($compounds as $cp)
          <article class="tape">
            <div class="tape-head">
              <div class="tape-img"><img src="{{ $cp['image'] ?? '' }}" alt="{{ $cp['name'] ?? '' }} vial" width="110" height="110"></div>
              <div class="tape-name"><h3>{{ $cp['name'] ?? '' }}</h3><span class="tg">{{ $cp['tag'] ?? '' }}</span></div>
            </div>
            <div>
              <div class="tape-rows">
                <div class="tape-row"><span class="k">What it is</span><span>{{ $cp['what'] ?? '' }}</span></div>
                <div class="tape-row"><span class="k">Studied for</span><span>{{ $cp['studied'] ?? '' }}</span></div>
              </div>
              <div class="tape-foot">
                <span class="price">{{ $cp['price'] ?? '' }}</span>
                @if(!empty($cp['url']))<a href="{{ $bl($cp['url']) }}" data-track="compound" data-name="{{ $cp['name'] ?? '' }}">See it at BioLinx with the code</a>@endif
              </div>
            </div>
          </article>
          @endforeach
        </div>
      </section>

      {{-- Vetting --}}
      <section id="vetting" class="rv">
        <div class="sec-head">
          <p class="eyebrow">{{ $c('vetting.eyebrow', 'The part nobody told me') }}</p>
          <h2>{{ $c('vetting.heading', 'How I check a supplier before I pay') }}</h2>
          <p>{{ $c('vetting.sub', 'The compound is rarely the problem. The supplier is. Wire money to the wrong site and you get a vial of who knows what and a support address that bounces. Run these five on anyone, including the store this page sends you to.') }}</p>
        </div>
        <div class="vet">
          <div class="vet-img"><img src="{{ $vettingImage }}" alt="A certificate of analysis beside a BioLinx vial with a QR code on the cap" width="800" height="1000"></div>
          <div>
            <div class="vet-list">
              @foreach($vetting as $i => $v)
              <div class="vet-item"><span class="n">{{ $i + 1 }}</span><div><h3>{{ $v['title'] ?? '' }}</h3><p>{{ $v['body'] ?? '' }}</p></div></div>
              @endforeach
            </div>
            <div class="vet-proof">
              <div>
                <p class="mono">HOW BIOLINX PASSES THIS LIST</p>
                <p style="margin-top:6px;font-size:15px">{{ $c('vetting.proof', 'Third-party COA on every batch, a permanent QR verify link printed on each label, US warehouse and US-only shipping, live chat with a human, and card, Zelle or bank transfer at checkout.') }} <a href="{{ $bl($store . '/verify') }}">See a verify page</a>.</p>
              </div>
            </div>
          </div>
        </div>
      </section>

      {{-- FAQ --}}
      <section id="faq" class="rv">
        <div class="sec-head">
          <p class="eyebrow">{{ $c('faq.eyebrow', 'Straight answers') }}</p>
          <h2>{{ $c('faq.heading', 'Questions people actually ask') }}</h2>
        </div>
        <div class="faq">
          @foreach($faqs as $f)
          <details><summary>{{ $f['q'] ?? '' }}</summary><div class="a">{{ $f['a'] ?? '' }}</div></details>
          @endforeach
        </div>
      </section>

    </div>

    <aside>
      @include('landers.templates.partials.affiliate-corner', ['aff' => $aff, 'fill' => $fill, 'tape' => 'From your corner'])
    </aside>
  </div>
</main>

<footer class="foot">
  <div class="wrap">
    <div class="links">
      <a href="{{ route('privacy') }}">Privacy</a>
      <a href="{{ route('terms') }}">Terms</a>
      <a href="{{ route('disclaimer') }}">Research-use policy</a>
      <span>21+</span>
    </div>
    <p><strong>{{ $legal['entity'] }}</strong>@if($legal['address'] !== '') · {{ $legal['address'] }}@endif · <a href="mailto:{{ $legal['email'] }}">{{ $legal['email'] }}</a></p>
    <p>{{ $legal['statement'] }}</p>
    <p>&copy; {{ date('Y') }} Professor Peptides. Sponsored guide. Products are sold by BioLinx Labs.</p>
  </div>
</footer>

{{-- mobile sticky bar --}}
<div class="bar" role="region" aria-label="Discount code">
  <div class="t">{{ $aff['discount'] }} off at BioLinx<b>with {{ $aff['first'] }}'s code</b></div>
  <button class="btn primary" type="button" data-open-modal data-placement="sticky-bar">Get code</button>
</div>

@include('landers.templates.partials.affiliate-modal', ['modal' => $modal, 'aff' => $aff, 'bl' => $bl, 'store' => $store, 'trackSlug' => $trackSlug])

</body>
</html>
