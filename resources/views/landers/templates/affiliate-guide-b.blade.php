@php
    // ------------------------------------------------------------------
    // "Affiliate Guide" VARIANT B: long-form advertorial. Same lander row and
    // fields as A (affiliate, compounds, kits, faq, modal, legal) plus `story.*`
    // slots for the narrative. Preview with ?v=b; 50/50 test via content.ab_test.enabled.
    //
    // Deliberately NOT in this version: dosing or usage instructions, invented
    // testimonials, invented statistics. Everything else goes as hard as it can.
    // ------------------------------------------------------------------
    $c = fn (string $path, $default = '') => $lander->c($path, $default);

    $aff = [
        'name'     => $c('affiliate.name', 'Your name here'),
        'first'    => $c('affiliate.first_name') ?: explode(' ', trim($c('affiliate.name', 'Your')))[0],
        'role'     => $c('affiliate.role', 'Amateur MMA · Your gym, Your city'),
        'photo'    => $c('affiliate.photo_url', ''),
        'note'     => $c('affiliate.note', "Every week someone at the gym asks me about peptides. Usually after they already paid a site with no address, no test results and a chat box nobody answers. I made this page so I can stop repeating myself. If you decide to buy, my code takes {discount} off and BioLinx pays me a commission. That is the whole deal. Nothing here is medical advice and I am not going to tell you what to do with your body."),
        'code'     => strtoupper($c('affiliate.code', 'YOURCODE')),
        'discount' => $c('affiliate.discount', '15%'),
        'idev_id'  => $c('affiliate.idev_id', ''),
    ];
    $fill = fn (string $s) => str_replace(['{first}', '{name}', '{discount}', '{code}'], [$aff['first'], $aff['name'], $aff['discount'], $aff['code']], $s);

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
    $heroImage = $c('hero.image_url', 'https://pub-0a9781e86a6b4f2d9b5bfbe22904ad3c.r2.dev/media/e44bf71a-0bed-45cb-9474-18a666054235.jpg');
    $coaImage  = $c('vetting.image_url', 'https://pub-0a9781e86a6b4f2d9b5bfbe22904ad3c.r2.dev/media/805c18e6-6a8f-4f80-9847-a587affb89da.jpg');

    // Narrative slots (editable in admin under "B variant"). Defaults are the full story.
    $story = [
        'kicker'   => $c('story.kicker', $fill('{first}\'s stack · research use only')),
        'headline' => $c('story.headline', 'This is my stack'),
        'deck'     => $c('story.deck', $fill('What is in it, why the research world cannot stop studying these compounds, and how I make sure what is in the vial is what is on the label. By {name}.')),
        'p1_title' => $c('story.p1_title', 'The nod'),
        'p1'       => $c('story.p1', "You know the moment. Somebody in the locker room pulls a little glass vial out of a shaker bag, holds it up like it is a secret, and says \"this is what everyone is running now.\" And you nod. You have no idea what it is. You are not going to ask, because asking means admitting you do not know, and the guy holding it clearly does.\n\nHere is the thing I learned the hard way: he usually does not know either. He knows a name. He knows a website. He knows what a stranger on a forum told him. That is the whole chain of custody for something he is putting in a vial with his name on it.\n\nI got tired of the nod. So I went and read the actual research, called the actual suppliers, and wrote down what I found. This is that."),
        'p2_title' => $c('story.p2_title', 'What I found when I actually looked'),
        'p2'       => $c('story.p2', "The first surprise is that these are not mystery substances. They are peptides: short chains of amino acids, the same building blocks as the protein in your shake. Some are fragments of proteins your body already makes. Others are synthetic cousins built in a lab to be studied. Universities have been publishing on them for decades.\n\nThe second surprise is what the research actually looks like. Almost all of it is cells in a dish and rodents in a cage. Researchers watch how cells migrate, how they signal each other, how genes switch on and off, how energy gets made and spent. It is fascinating work. It is also not the same as a finished human study, and anyone who tells you otherwise is selling you something.\n\nThat gap is the whole story of this market. The science is real and early. The marketing is loud and finished. Your job is to stand in the gap with your eyes open. Below is each compound people ask me about, in the order they ask."),
        'p3_title' => $c('story.p3_title', 'The part that made me angry'),
        'p3'       => $c('story.p3', "Once you know what these compounds are, the next question is where the vial came from. And this is where people get hurt, not by the molecule, by the seller.\n\nThere are sites out there with no address, no phone, no test results, and a chat widget that has never been answered by a human. They take card payments through a processor in another country, or they only take crypto, which should tell you everything. The vial that arrives, if it arrives, has a printed label and nothing behind it. Nobody knows what is in it. Nobody can check.\n\nI have watched people I train with spend real money on that. Not because they were stupid. Because nobody told them what to look for. So here is what to look for."),
        'p4_title' => $c('story.p4_title', 'How I buy now'),
        'p4'       => $c('story.p4', $fill("I run five checks on any supplier, and I run them on BioLinx too, every time. Certificate of analysis from a third-party lab, batch number that matches the vial. A permanent verify link printed on the label, scan it and the real COA opens. Ships from a US warehouse to US addresses only, with a real business address and a person on the chat. Normal payment options, card, Zelle, bank transfer, nothing sketchy. And label language that says research use only and means it.\n\nBioLinx passes all five. That is why my code goes there and nowhere else.")),
        'p5_title' => $c('story.p5_title', 'My stack, and the deal'),
        'p5'       => $c('story.p5', $fill("BioLinx put my stack together as one package: the four compounds from this page plus three bacteriostatic water, so you are not hunting through a store guessing what goes with what. Use my code, {code}, and you get {discount} off it. BioLinx pays me a commission on that order. You do not pay more, they pay me out of their side. I do not see your name, your address or what you bought. I get a notice that a sale happened with my code. That is it.\n\nIf you never buy anything, this page still did its job: next time somebody holds up a vial in the locker room, you will know exactly what questions to ask.")),
        'ps'       => $c('story.ps', 'P.S. If you only remember one thing: never send money to a site that cannot show you a certificate of analysis for the exact batch. Not a stock photo of one. The real one, with the number that matches your vial.'),
    ];

    $compounds = $c('compounds.items') ?: [
        ['name' => 'BPC-157', 'tag' => $fill('{first}\'s pick · sold on its own'), 'image' => 'https://assets.sticky.io/images/originals/2026-06-03-16-00-00/GsUOiu6iQyRa7yhe4iKjWudrXNMwjfFMkxnnm7ip.jpg',
         'what' => 'A synthetic 15-amino-acid fragment of a protein found in gastric juice. The blend below pairs it with TB-500; this is the single compound by itself.',
         'studied' => 'Cell signaling and how cells move, in cell culture and rodent models. The single most asked-about peptide in any gym, and still without a completed human trial.',
         'url' => $store . '/products/bpc-157-10-mg', 'price' => '$64.93 · 10 mg'],
        ['name' => 'BPC-157 / TB-500 Blend', 'tag' => 'Two peptides, one vial', 'image' => 'https://assets.sticky.io/images/originals/2026-03-04-15-00-00/NrccqLaqcaXiJ6RYQgN6VNftL4mhYxs7thnMYPSX.jpg',
         'what' => 'A synthetic 15-amino-acid fragment originally derived from a gastric protein, combined with a synthetic fragment of thymosin beta-4.',
         'studied' => 'How cells signal each other and migrate, in culture and in rodent models. One of the most searched peptide pairings online, and one of the least understood.',
         'url' => $store . '/products/bpc-157-tb-500-blend-10-mg', 'price' => '$79.93 · 10 mg'],
        ['name' => 'GHK-Cu', 'tag' => 'Copper tripeptide', 'image' => 'https://assets.sticky.io/images/originals/2026-03-04-14-00-00/kxUj8zIjNVkVD5Zu3AYGfp8gObLHeOWUVQrTR20o.jpg',
         'what' => 'Glycyl-histidyl-lysine bound to copper, a tripeptide that occurs naturally in plasma.',
         'studied' => 'How a copper-bound peptide switches genes on and off in skin cells in culture. The one you will see in serious skincare.',
         'url' => $store . '/products/ghk-cu-50-mg', 'price' => '$49.93 · 50 mg'],
        ['name' => 'CJC-1295 (no DAC) + Ipamorelin', 'tag' => 'Secretagogue analog pair', 'image' => 'https://assets.sticky.io/images/originals/2026-06-03-16-00-00/1oIZpaZn6asah6I1hg7k5PLASUU92exKdgG1Zj3o.jpg',
         'what' => 'Two synthetic analogs that are usually studied together because they act on related receptors.',
         'studied' => 'Growth hormone signaling in animal models: how the pituitary answers a nudge. Studied as a pair because they act on related receptors.',
         'url' => $store . '/products/cjc-1295-wo-dac-ipamorelin-10mg', 'price' => '$79.93 · 10 mg'],
        ['name' => 'MOTS-c', 'tag' => 'Mitochondrial peptide', 'image' => 'https://assets.sticky.io/images/originals/2026-03-04-14-00-00/10qdl1PXP0W95myLg6fvyzgxP6SxkCd9BNVuBNfp.jpg',
         'what' => 'A 16-amino-acid peptide encoded in mitochondrial DNA, discovered in 2015.',
         'studied' => 'How cells make and spend energy, in cell and rodent models. Most of what you have read about it online comes from mouse studies.',
         'url' => $store . '/products/mots-c-10-mg', 'price' => '$49.93 · 10 mg'],
    ];

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
        'title'   => $c('modal.title', $fill('Get {first}\'s code')),
        'sub'     => $c('modal.sub', $fill('{discount} off at BioLinx Labs. One tap, your email, done.')),
        'q'       => $c('modal.question', 'What are you looking at?'),
        'options' => $c('modal.options') ?: [
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
    $paras = fn (string $t) => array_values(array_filter(array_map('trim', preg_split('/\n\s*\n/', $t))));
@endphp
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  @if($lander->noindex)<meta name="robots" content="noindex,nofollow" />@endif
  <title>{{ $c('meta.title', $fill('The vial from the shaker bag: {first}\'s guide | Professor Peptides')) }}</title>
  <meta name="description" content="{{ $c('meta.description', $fill('What the guys at your gym are actually running, what the research says, how to not get robbed buying it, and {first}\'s {discount} code for BioLinx Labs.')) }}" />
  <meta property="og:type" content="article" />
  <meta property="og:site_name" content="Professor Peptides" />
  <meta property="og:title" content="{{ $story['headline'] }}" />
  <meta property="og:description" content="{{ $story['deck'] }}" />
  <meta property="og:image" content="{{ $heroImage }}" />
  <meta property="og:url" content="{{ url()->current() }}" />
  <meta name="twitter:card" content="summary_large_image" />
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="preconnect" href="https://pub-0a9781e86a6b4f2d9b5bfbe22904ad3c.r2.dev" crossorigin>
  <link rel="preconnect" href="https://assets.sticky.io" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Big+Shoulders+Display:wght@800;900&family=Source+Serif+4:ital,opsz,wght@0,8..60,400;0,8..60,600;1,8..60,400&family=IBM+Plex+Sans:wght@500;600;700&family=IBM+Plex+Mono:wght@400;500;600&display=swap" rel="stylesheet">
  @verbatim<style>
:root{
  --paper:#F4F5F3;--paper-2:#FFFFFF;--slab:#E4E7E3;--line:#D3D8D2;
  --ink:#15181B;--ink-2:#2B3136;--steel:#5B6670;--muted:#7C8791;
  --red:#C41E1E;--red-2:#A51717;--red-tint:#FBECEC;
  --display:'Big Shoulders Display',Impact,'Arial Narrow',sans-serif;
  --serif:'Source Serif 4',Georgia,'Times New Roman',serif;
  --sans:'IBM Plex Sans',system-ui,-apple-system,Segoe UI,Roboto,sans-serif;
  --mono:'IBM Plex Mono',ui-monospace,SFMono-Regular,Menlo,monospace;
  --radius:14px;--shadow:0 18px 50px rgba(21,24,27,.10);
}
*{box-sizing:border-box}
html{scroll-behavior:smooth;-webkit-text-size-adjust:100%}
body{margin:0;background:var(--paper);color:var(--ink);font-family:var(--serif);font-size:19px;line-height:1.65}
img{max-width:100%;display:block}
a{color:inherit}
h1,h2,h3,p{margin:0}
h1,h2{font-family:var(--display);text-transform:uppercase;line-height:.92;font-weight:900}
h1{font-size:clamp(52px,10vw,120px)}
h2{font-size:clamp(34px,5.4vw,56px)}
h3{font-family:var(--sans);font-weight:700;font-size:19px;line-height:1.25}
.mono{font-family:var(--mono)}
.eyebrow{font-family:var(--mono);font-size:12px;font-weight:600;letter-spacing:.14em;text-transform:uppercase;color:var(--steel)}
.eyebrow.red{color:var(--red)}
.wrap{width:min(100% - 32px,1160px);margin:0 auto}
.col{width:min(100% - 32px,720px);margin:0 auto}
.skip{position:absolute;left:-999px}.skip:focus{left:12px;top:12px;z-index:999;background:#fff;padding:10px;border-radius:8px}
.progress{position:fixed;top:0;left:0;height:3px;width:0;background:var(--red);z-index:80}

.top{position:sticky;top:0;z-index:60;background:rgba(244,245,243,.94);backdrop-filter:blur(10px);border-bottom:1px solid var(--line)}
.top .wrap{display:flex;align-items:center;justify-content:space-between;gap:16px;min-height:56px}
.brand{display:flex;align-items:center;gap:10px;text-decoration:none}
.brand-mark{width:30px;height:30px;border:2px solid var(--ink);display:grid;place-items:center;font-family:var(--display);font-weight:900;font-size:16px;border-radius:8px}
.brand-text{font-family:var(--mono);font-size:11px;letter-spacing:.16em;text-transform:uppercase;line-height:1.15}
.brand-text em{display:block;font-style:normal;color:var(--muted);font-size:9px}
.btn{display:inline-flex;align-items:center;justify-content:center;gap:10px;min-height:52px;padding:14px 22px;border-radius:999px;border:0;cursor:pointer;text-decoration:none;font-family:var(--sans);font-weight:700;font-size:16px;line-height:1;transition:transform .12s ease,background .12s ease}
.btn:focus-visible,button:focus-visible,a:focus-visible,input:focus-visible{outline:3px solid var(--red);outline-offset:3px}
.btn.primary{background:var(--red);color:#fff;box-shadow:0 10px 24px rgba(196,30,30,.25)}.btn.primary:hover{background:var(--red-2);transform:translateY(-1px)}
.btn.ink{background:var(--ink);color:#fff}.btn.ink:hover{background:#000}
.btn.sm{min-height:42px;padding:10px 16px;font-size:14px}
.top .btn{display:none}

/* masthead */
.mast{padding:40px 0 10px;text-align:left}
.mast .eyebrow{margin-bottom:14px}
.mast h1{max-width:9ch}
.mast h1 .u{position:relative;display:inline-block}
.mast h1 .u:after{content:'';position:absolute;left:0;right:0;bottom:.05em;height:.13em;background:var(--red);z-index:-1}
.deck{margin-top:18px;font-size:22px;line-height:1.45;color:var(--ink-2);max-width:32ch;font-style:italic}
.byline{display:flex;align-items:center;gap:12px;margin-top:22px;font-family:var(--sans);font-size:14px;color:var(--steel)}
.byline img,.byline .ph{width:44px;height:44px;border-radius:50%;object-fit:cover;background:var(--slab);border:2px solid #fff;display:grid;place-items:center;color:var(--muted)}
.byline b{color:var(--ink)}
.byline .disc{font-family:var(--mono);font-size:11px;color:var(--muted);display:block}
.mast-media{margin:26px 0 8px;border-radius:var(--radius);overflow:hidden;aspect-ratio:16/9;background:var(--slab)}
.mast-media img{width:100%;height:100%;object-fit:cover}
.mast-cap{font-family:var(--mono);font-size:11px;color:var(--muted);letter-spacing:.06em}

/* story */
.story{padding:30px 0 20px}
.part{padding:34px 0 10px}
.part-k{display:flex;align-items:center;gap:14px;margin-bottom:14px}
.part-k .n{font-family:var(--display);font-size:52px;font-weight:900;color:var(--red);line-height:.85}
.part-k h2{font-size:clamp(30px,4.6vw,46px)}
.part p{margin-top:16px;color:var(--ink-2)}
.part p:first-of-type{margin-top:0}
.part p.lead:first-letter{font-family:var(--display);font-size:3.6em;line-height:.8;float:left;padding:8px 10px 0 0;color:var(--ink)}
.pull{margin:26px 0;padding:18px 22px;border-left:4px solid var(--red);background:var(--paper-2);border-radius:0 12px 12px 0;font-size:24px;line-height:1.35;font-weight:600}
.pull small{display:block;margin-top:8px;font-family:var(--mono);font-size:11px;color:var(--steel);font-weight:400;letter-spacing:.06em}
.inline-cta{margin:26px 0;display:flex;gap:12px;flex-wrap:wrap;align-items:center}
.inline-cta .mono{font-size:12px;color:var(--steel)}

/* compounds inside the story */
.cards{display:grid;gap:12px;margin-top:22px;font-family:var(--sans);font-size:15px}
.card{display:flex;gap:14px;background:var(--paper-2);border:1px solid var(--line);border-radius:var(--radius);padding:14px}
.card .im{flex:0 0 64px;width:64px;height:64px;border-radius:10px;border:1px solid var(--line);background:#fff;display:grid;place-items:center;overflow:hidden}
.card .im img{width:100%;height:100%;object-fit:contain;padding:5px}
.card h3{font-family:var(--display);font-size:24px;text-transform:uppercase;font-weight:900;line-height:1}
.card .tg{font-family:var(--mono);font-size:11px;color:var(--steel);margin-left:8px}
.card p{margin-top:6px;color:var(--ink-2);line-height:1.5}
.card p b{font-family:var(--mono);font-size:11px;letter-spacing:.1em;text-transform:uppercase;color:var(--steel);font-weight:600;display:block;margin-bottom:2px}
.card .ft{margin-top:8px;display:flex;justify-content:space-between;align-items:center;gap:10px;flex-wrap:wrap}
.card .ft .pr{font-family:var(--mono);font-size:12px;color:var(--steel)}
.card .ft a{font-weight:700;font-size:14px;color:var(--ink);text-decoration:none;border-bottom:2px solid var(--red)}

/* checks */
.checks{margin-top:18px;font-family:var(--sans);font-size:16px;counter-reset:ck}
.check{display:grid;grid-template-columns:40px 1fr;gap:12px;padding:12px 0;border-top:1px solid var(--line)}
.check:first-child{border-top:0}
.check .n{font-family:var(--display);font-size:32px;font-weight:900;color:var(--red);line-height:.9}
.check p{color:var(--ink-2);margin-top:2px;font-size:15px}
.figure{margin:22px 0;border-radius:var(--radius);overflow:hidden;background:var(--slab);aspect-ratio:4/5;max-height:460px}
.figure img{width:100%;height:100%;object-fit:cover}
.proof{margin-top:16px;background:var(--ink);color:#fff;border-radius:var(--radius);padding:16px 18px;font-family:var(--sans);font-size:15px}
.proof .mono{font-size:12px;color:#B9C2CB;display:block;margin-bottom:6px}
.proof a{color:#fff}

/* offer */
.offer{margin-top:22px}
.kits{display:grid;gap:12px;font-family:var(--sans)}
.kit{position:relative;background:var(--paper-2);border:1px solid var(--line);border-radius:var(--radius);padding:20px;display:grid;gap:6px}
.kit.featured{border-color:var(--ink);box-shadow:var(--shadow)}
.kit .flag{position:absolute;top:-11px;left:18px;background:var(--ink);color:#fff;font-family:var(--mono);font-size:10px;letter-spacing:.14em;text-transform:uppercase;padding:5px 9px;border-radius:4px}
.kit h3{font-family:var(--display);font-size:28px;text-transform:uppercase;font-weight:900;line-height:1}
.kit .contents{font-size:14px;color:var(--steel)}
.kit .price{font-family:var(--mono);font-size:22px;font-weight:600;margin-top:4px}
.kit .price small{font-size:12px;color:var(--steel);font-weight:400;margin-left:8px}
.kit .note{font-family:var(--mono);font-size:11px;color:var(--muted)}
.kit .btn{margin-top:8px}
.fine{font-family:var(--mono);font-size:11px;color:var(--muted);margin-top:12px;line-height:1.5}

/* single stack package */
.stack{background:var(--paper-2);border:1.5px solid var(--ink);border-radius:var(--radius);overflow:hidden;box-shadow:var(--shadow);display:grid;font-family:var(--sans);margin-top:18px}
.stack-img{background:var(--slab);aspect-ratio:16/9}
.stack-img img{width:100%;height:100%;object-fit:cover}
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

/* two or three package cards */
.stack-grid{display:grid;gap:14px}
.stack.compact{grid-template-columns:1fr}
.stack.compact .stack-img{aspect-ratio:16/9;min-height:0}
.stack.compact .stack-body h3{font-size:30px}
.stack.compact:not(.featured){border-color:var(--line);box-shadow:none}
.stack.compact:not(.featured) .flag{background:var(--ink)}
@media (min-width:700px){ .stack-grid{grid-template-columns:1.25fr 1fr;align-items:stretch} }

/* faq */
.faq{margin-top:10px;border-top:1px solid var(--line);font-family:var(--sans)}
.faq details{border-bottom:1px solid var(--line)}
.faq summary{list-style:none;cursor:pointer;padding:16px 0;font-weight:700;display:flex;justify-content:space-between;gap:12px;align-items:center;font-size:17px}
.faq summary::-webkit-details-marker{display:none}
.faq summary:after{content:'+';font-family:var(--mono);color:var(--red);font-size:20px;flex:0 0 auto}
.faq details[open] summary:after{content:'\2013'}
.faq .a{padding:0 0 18px;color:var(--ink-2);font-size:15px}

.ps{margin:30px 0 0;padding:20px 22px;background:var(--red-tint);border-radius:var(--radius);font-size:18px}
.corner-wrap{margin:34px 0 0}

/* corner card */
.corner{position:relative;background:var(--paper-2);border:1px solid var(--line);border-radius:18px;padding:22px 20px 20px;box-shadow:var(--shadow);font-family:var(--sans)}
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

.foot{border-top:2px solid var(--ink);margin-top:40px;padding:26px 0 120px;font-family:var(--sans);font-size:13px;color:var(--steel)}
.foot .links{display:flex;gap:14px;flex-wrap:wrap;font-family:var(--mono);font-size:12px;margin-bottom:12px}
.foot .links a{color:var(--ink);text-decoration:none;border-bottom:1px solid var(--line)}
.foot p{max-width:90ch;margin-top:6px;line-height:1.55}

.bar{position:fixed;left:12px;right:12px;bottom:calc(12px + env(safe-area-inset-bottom));z-index:70;display:flex;align-items:center;justify-content:space-between;gap:12px;background:var(--ink);color:#fff;border-radius:999px;padding:8px 8px 8px 18px;box-shadow:0 18px 40px rgba(21,24,27,.3);font-family:var(--sans)}
.bar .t{font-family:var(--mono);font-size:12px;line-height:1.25}.bar .t b{display:block;font-size:13px}
.bar .btn{min-height:44px;padding:10px 16px;font-size:14px}

/* modal (shared partial) */
.pp-modal-open{overflow:hidden}
.ov{position:fixed;inset:0;z-index:90;background:rgba(21,24,27,.62);display:none;align-items:flex-end;justify-content:center;padding:0;font-family:var(--sans)}
.ov.is-open{display:flex}
.md{position:relative;width:100%;max-width:520px;background:var(--paper-2);border-radius:22px 22px 0 0;padding:24px 22px 28px;box-shadow:0 -10px 60px rgba(0,0,0,.35);max-height:92vh;overflow:auto}
.md-close{position:absolute;top:12px;right:12px;width:38px;height:38px;border-radius:50%;border:0;background:var(--slab);font-size:22px;line-height:1;cursor:pointer}
.md h3{font-family:var(--display);font-size:34px;text-transform:uppercase;font-weight:900;line-height:.95}
.md .sub{color:var(--steel);margin-top:8px;font-size:15px}
.md .step-lab{font-family:var(--mono);font-size:11px;letter-spacing:.14em;text-transform:uppercase;color:var(--red);margin-bottom:8px}
.opts{display:grid;gap:10px;margin-top:16px}
.opt{display:flex;align-items:center;justify-content:space-between;gap:10px;text-align:left;background:var(--paper);border:1.5px solid var(--line);border-radius:14px;padding:14px 16px;cursor:pointer;font-family:var(--sans)}
.opt:hover,.opt.on{border-color:var(--ink)}
.opt b{font-size:16px;display:block}.opt span{font-family:var(--mono);font-size:12px;color:var(--steel)}.opt i{font-style:normal;color:var(--red);font-weight:700}
.fld{display:grid;gap:6px;margin-top:12px}
.fld label{font-family:var(--mono);font-size:11px;letter-spacing:.12em;text-transform:uppercase;color:var(--steel)}
.fld input{width:100%;font:inherit;font-size:16px;padding:13px 14px;border:1.5px solid var(--line);border-radius:12px;background:#fff}
.fld input:focus{border-color:var(--ink)}
.md .btn{width:100%;margin-top:14px}
.md .fine{margin-top:10px}
.code-box{margin-top:16px;background:var(--ink);color:#fff;border-radius:16px;padding:18px;display:flex;align-items:center;justify-content:space-between;gap:12px}
.code-box .val{font-family:var(--mono);font-size:28px;font-weight:600;letter-spacing:.08em}
.code-box .cp{background:#fff;color:var(--ink);border:0;border-radius:999px;padding:10px 14px;font-weight:700;cursor:pointer;font-family:var(--sans)}

@media (prefers-reduced-motion:no-preference){
  .rv{opacity:0;transform:translateY(12px);transition:opacity .5s ease,transform .5s ease}
  .rv.in{opacity:1;transform:none}
}
@media (min-width:700px){
  .top .btn{display:inline-flex}
  .kits{grid-template-columns:repeat(3,1fr)}
  .ov{align-items:center;padding:20px}.md{border-radius:22px}
  .mast{padding-top:56px}
}
@media (min-width:1024px){ .bar{display:none} .foot{padding-bottom:40px} }
  </style>@endverbatim
  <x-meta-pixel />
  <x-posthog-lander />
</head>
<body>
<a class="skip" href="#story">Skip to the story</a>
<div class="progress" id="agProgress" aria-hidden="true"></div>

<header class="top">
  <div class="wrap">
    <a class="brand" href="{{ url('/') }}" aria-label="Professor Peptides">
      <span class="brand-mark">P</span>
      <span class="brand-text">Professor Peptides<em>Sponsored guide · BioLinx Labs · Research use only</em></span>
    </a>
    <button class="btn primary sm" type="button" data-open-modal data-placement="topbar">{{ $fill('Get {first}\'s code') }}</button>
  </div>
</header>

<main>
  <section class="mast">
    <div class="col">
      <p class="eyebrow red">{{ $story['kicker'] }}</p>
      <h1>{!! preg_replace('/\b(my stack)\b/i', '<span class="u">$1</span>', e($story['headline'])) !!}</h1>
      <p class="deck">{{ $story['deck'] }}</p>
      <div class="byline">
        @if($aff['photo'])<img src="{{ $aff['photo'] }}" alt="{{ $aff['name'] }}" width="44" height="44">@else<span class="ph" aria-hidden="true"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><circle cx="12" cy="8" r="4"/><path d="M4 21c0-4 3.6-7 8-7s8 3 8 7"/></svg></span>@endif
        <span><b>{{ $aff['name'] }}</b> · {{ $aff['role'] }}<span class="disc">{{ $fill('Affiliate partner. Paid a commission by BioLinx Labs on orders with code {code}.') }}</span></span>
      </div>
      <div class="mast-media"><img src="{{ $heroImage }}" alt="BioLinx Labs research vials on a lab bench" fetchpriority="high" width="1600" height="900"></div>
      <p class="mast-cap">Research use only · Not for human consumption · 21+ · US shipping only</p>
    </div>
  </section>

  <article class="story" id="story">
    <div class="col">

      <section class="part rv">
        <div class="part-k"><span class="n">1</span><h2>{{ $story['p1_title'] }}</h2></div>
        @foreach($paras($story['p1']) as $i => $para)<p class="{{ $i === 0 ? 'lead' : '' }}">{{ $para }}</p>@endforeach
        <div class="pull">"{{ $fill('I got tired of the nod.') }}"<small>{{ $aff['name'] }}</small></div>
      </section>

      <section class="part rv" id="compounds">
        <div class="part-k"><span class="n">2</span><h2>{{ $story['p2_title'] }}</h2></div>
        @foreach($paras($story['p2']) as $para)<p>{{ $para }}</p>@endforeach
        <div class="cards">
          @foreach($compounds as $cp)
          <div class="card">
            <div class="im"><img src="{{ $cp['image'] ?? '' }}" alt="{{ $cp['name'] ?? '' }} vial" width="64" height="64"></div>
            <div style="flex:1;min-width:0">
              <h3>{{ $cp['name'] ?? '' }}<span class="tg">{{ $cp['tag'] ?? '' }}</span></h3>
              <p><b>What it is</b>{{ $cp['what'] ?? '' }}</p>
              <p><b>Why researchers care</b>{{ $cp['studied'] ?? '' }}</p>
              <div class="ft"><span class="pr">{{ $cp['price'] ?? '' }}</span>@if(!empty($cp['url']))<a href="{{ $bl($cp['url']) }}" data-track="compound" data-name="{{ $cp['name'] ?? '' }}">See it at BioLinx with the code</a>@endif</div>
            </div>
          </div>
          @endforeach
        </div>
        <div class="inline-cta"><button class="btn primary" type="button" data-open-modal data-placement="after-compounds">{{ $fill('Get {first}\'s code') }}</button><span class="mono">{{ $aff['discount'] }} off at BioLinx · commission disclosed</span></div>
      </section>

      <section class="part rv">
        <div class="part-k"><span class="n">3</span><h2>{{ $story['p3_title'] }}</h2></div>
        @foreach($paras($story['p3']) as $para)<p>{{ $para }}</p>@endforeach
        <div class="figure"><img src="{{ $coaImage }}" alt="A certificate of analysis beside a BioLinx vial with a QR code on the cap" width="800" height="1000"></div>
        <div class="checks">
          <div class="check"><span class="n">1</span><div><h3>Ask for the COA before you pay</h3><p>Third-party lab, batch number that matches the vial. No COA, no order. In-house "testing" does not count.</p></div></div>
          <div class="check"><span class="n">2</span><div><h3>Scan the code on the bottle</h3><p>BioLinx prints a permanent QR link on every label. Scan it and the current COA for that exact batch opens.</p></div></div>
          <div class="check"><span class="n">3</span><div><h3>Check where it ships from</h3><p>US warehouse, US-only shipping, a real business address and a support chat with a person behind it.</p></div></div>
          <div class="check"><span class="n">4</span><div><h3>Look at how they take payment</h3><p>Card, Zelle and bank transfer are normal. Crypto-only or gift cards mean there is no one to call when the box never shows up.</p></div></div>
          <div class="check"><span class="n">5</span><div><h3>Read the label language</h3><p>Legit suppliers say "research use only" and mean it. Anyone promising you results, doses or "protocols" is selling something the law says they cannot.</p></div></div>
        </div>
      </section>

      <section class="part rv">
        <div class="part-k"><span class="n">4</span><h2>{{ $story['p4_title'] }}</h2></div>
        @foreach($paras($story['p4']) as $para)<p>{{ $para }}</p>@endforeach
        <div class="proof"><span class="mono">HOW BIOLINX PASSES ALL FIVE</span>Third-party COA on every batch, a permanent QR verify link printed on each label, US warehouse and US-only shipping, live chat with a human, and card, Zelle or bank transfer at checkout. <a href="{{ $bl($store . '/verify') }}">See a verify page</a>.</div>
      </section>

      <section class="part rv" id="kits">
        <div class="part-k"><span class="n">5</span><h2>{{ $story['p5_title'] }}</h2></div>
        @foreach($paras($story['p5']) as $para)<p>{{ $para }}</p>@endforeach
        <div class="offer">
          @if(count($kits) === 1)
          @php $k = $kits[0]; @endphp
          <article class="stack">
            <div class="stack-img"><img src="{{ $k['image'] ?? $heroImage }}" alt="{{ $k['name'] ?? '' }}: the full bundle" width="1200" height="675"></div>
            <div class="stack-body">
              <span class="flag">{{ $c('kits.flag', 'One package') }}</span>
              <h3>{{ $k['name'] ?? '' }}</h3>
              <p class="contents">{{ $k['contents'] ?? '' }}</p>
              <div class="stack-price"><span class="was">{{ $k['price'] ?? '' }}</span><span class="now">{{ $k['price_with_code'] ?? $k['price'] ?? '' }}</span><span class="lab">with {{ $aff['code'] }}</span></div>
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
          <p class="fine">Research use only. Not for human consumption. 21+ and a research-use agreement are required at checkout.</p>
        </div>
        <div class="corner-wrap">
          @include('landers.templates.partials.affiliate-corner', ['aff' => $aff, 'fill' => $fill, 'tape' => 'From your corner'])
        </div>
      </section>

      <section class="part rv" id="faq">
        <p class="eyebrow">Straight answers</p>
        <div class="faq">
          @foreach($faqs as $f)<details><summary>{{ $f['q'] ?? '' }}</summary><div class="a">{{ $f['a'] ?? '' }}</div></details>@endforeach
        </div>
        <p class="ps">{{ $story['ps'] }}</p>
      </section>

    </div>
  </article>
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

<div class="bar" role="region" aria-label="Discount code">
  <div class="t">{{ $aff['discount'] }} off at BioLinx<b>with {{ $aff['first'] }}'s code</b></div>
  <button class="btn primary" type="button" data-open-modal data-placement="sticky-bar">Get code</button>
</div>

@include('landers.templates.partials.affiliate-modal', ['modal' => $modal, 'aff' => $aff, 'bl' => $bl, 'store' => $store, 'trackSlug' => $trackSlug])
<script>
(function(){ var p=document.getElementById('agProgress'); if(!p) return; var t=function(){ var h=document.documentElement; var d=(h.scrollTop)/(h.scrollHeight-h.clientHeight); p.style.width=Math.max(0,Math.min(1,d))*100+'%'; }; window.addEventListener('scroll',t,{passive:true}); t(); })();
</script>
</body>
</html>
