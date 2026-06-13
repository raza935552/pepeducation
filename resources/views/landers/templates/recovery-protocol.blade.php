@php
    // ORIGINAL high-converting dark/clinical-athletic template (Variant A: Recovery).
    // CMS-driven: all copy/links/images come from $lander->content; layout is fixed.
    // Keeps the full tracking loop: x-meta-pixel + x-posthog-lander + /go outbound.
    $go = fn (string $dest) => $lander->outbound_slug
        ? route('outbound.track', $lander->outbound_slug) . '?dest=' . urlencode($dest)
        : $dest;
    $blend = 'https://biolinxlabs.com/products/bpc-157-tb-500-blend-20-mg';
    $primaryDest = $lander->c('hero.primary_dest') ?: $blend;
    $products = $lander->c('compounds.products', []);
    $proof    = $lander->c('proof.items', []);
    $faqs     = $lander->c('faq.items', []);
    // Sensible defaults so the page is rich even before these keys are edited in admin.
    $badges = $lander->c('badges') ?: ['>99% Purity', 'COA on Every Batch', 'Third-Party Tested', 'Fast Discreet Shipping'];
    $stats  = $lander->c('stats') ?: [
        ['n' => '2', 'l' => 'compounds, one research protocol'],
        ['n' => '>99%', 'l' => 'HPLC-verified purity'],
        ['n' => '100%', 'l' => 'batches ship with a COA'],
    ];
    if (! $proof) {
        $proof = [
            ['quote' => 'Purity and documentation are exactly what I need for reproducible work. The COA is right there every time.', 'name' => 'Verified research customer'],
            ['quote' => 'I standardized my recovery research on the BPC/TB pairing. Consistent material, batch after batch.', 'name' => 'Verified research customer'],
            ['quote' => 'Fast, discreet, and the lot tracking is transparent. Reordered three times now.', 'name' => 'Verified research customer'],
        ];
    }
    if (! $faqs) {
        $faqs = [
            ['q' => 'Are these for human use?', 'a' => 'No. All compounds are sold strictly for laboratory research use only — not for human consumption.'],
            ['q' => 'What purity can I expect?', 'a' => 'Research-grade, HPLC-verified to >99%, with a Certificate of Analysis available per batch.'],
            ['q' => 'Why study BPC-157 and TB-500 together?', 'a' => 'Their mechanisms are complementary — one studied for repair-pathway signaling, the other for cell migration — which is why the pairing is so widely researched.'],
            ['q' => 'How fast does it ship?', 'a' => 'Orders ship quickly and discreetly from the U.S. with lot-tracked documentation.'],
        ];
    }
@endphp
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  @if($lander->noindex)<meta name="robots" content="noindex,nofollow" />@endif
  <title>{{ $lander->c('meta.title') }}</title>
  <meta name="description" content="{{ $lander->c('meta.description') }}" />
  <meta property="og:type" content="website" />
  <meta property="og:site_name" content="Professor Peptides" />
  <meta property="og:title" content="{{ $lander->c('meta.title') }}" />
  <meta property="og:description" content="{{ $lander->c('meta.description') }}" />
  <meta property="og:url" content="{{ url()->current() }}" />
  <meta name="twitter:card" content="summary" />
  <meta name="twitter:title" content="{{ $lander->c('meta.title') }}" />
  <meta name="twitter:description" content="{{ $lander->c('meta.description') }}" />
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="preconnect" href="https://pub-0a9781e86a6b4f2d9b5bfbe22904ad3c.r2.dev" crossorigin>
  <link rel="preconnect" href="https://assets.sticky.io" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=Archivo:wght@600;700;800;900&display=swap" rel="stylesheet">
  @verbatim<style>
:root{
  --bg:#070a0f;--bg-2:#0b1016;--panel:#0f151d;--card:#131a24;--card-2:#0d131b;
  --line:#1f2733;--line-2:#283342;--ink:#eef3f9;--ink-2:#cdd6e2;--muted:#8a97a8;
  --acc:#21e6ad;--acc-2:#13c08e;--acc-ink:#04140f;--amber:#ffb24d;
  --sans:Inter,system-ui,-apple-system,Segoe UI,sans-serif;--disp:Archivo,Inter,sans-serif;
  --radius:22px;--radius-sm:14px;--shadow:0 30px 80px rgba(0,0,0,.5);
}
*{box-sizing:border-box}html{scroll-behavior:smooth}
body{margin:0;background:radial-gradient(1100px 620px at 78% -8%,rgba(33,230,173,.13),transparent 60%),radial-gradient(900px 600px at 0% 0%,rgba(33,230,173,.06),transparent 55%),var(--bg);color:var(--ink);font-family:var(--sans);font-size:16px;line-height:1.6;-webkit-font-smoothing:antialiased}
a{text-decoration:none;color:inherit}
h1,h2,h3,p{margin:0}
.wrap{width:min(100% - 40px,1200px);margin:0 auto}
.eyebrow{display:inline-flex;align-items:center;gap:8px;color:var(--acc);font:800 12px/1 var(--sans);letter-spacing:.2em;text-transform:uppercase}
.eyebrow:before{content:"";width:22px;height:2px;background:var(--acc);border-radius:2px}
/* top bar */
.topbar{position:sticky;top:0;z-index:60;background:rgba(7,10,15,.82);backdrop-filter:blur(14px);border-bottom:1px solid var(--line)}
.topbar .wrap{display:flex;align-items:center;justify-content:space-between;gap:18px;padding:14px 0}
.brand{display:flex;align-items:center;gap:11px}
.brand-mark{width:38px;height:38px;display:grid;place-items:center;border-radius:11px;background:linear-gradient(135deg,var(--acc),var(--acc-2));color:var(--acc-ink);font:900 18px/1 var(--disp)}
.brand-text{display:grid;line-height:1.05}
.brand-text strong{font:800 13px/1 var(--sans);letter-spacing:.04em}
.brand-text em{font-style:normal;color:var(--muted);font-size:10px;letter-spacing:.16em;text-transform:uppercase}
.btn{display:inline-flex;align-items:center;justify-content:center;gap:8px;font:800 15px/1 var(--sans);border-radius:999px;padding:14px 24px;min-height:50px;cursor:pointer;transition:transform .15s ease,box-shadow .15s ease,background .15s ease}
.btn-acc{background:linear-gradient(135deg,var(--acc),var(--acc-2));color:var(--acc-ink);box-shadow:0 14px 34px rgba(33,230,173,.28)}
.btn-acc:hover{transform:translateY(-2px);box-shadow:0 20px 44px rgba(33,230,173,.4)}
.btn-ghost{background:transparent;color:var(--ink);border:1px solid var(--line-2)}
.btn-ghost:hover{border-color:var(--acc);color:var(--acc)}
.nav-cta{padding:11px 20px;min-height:auto;font-size:14px}
/* hero */
.hero{position:relative;overflow:hidden}
.hero .wrap{display:grid;grid-template-columns:minmax(0,1.05fr) minmax(0,.95fr);gap:46px;align-items:center;padding:70px 0 56px}
.hero-pills{display:flex;gap:8px;flex-wrap:wrap;margin:22px 0 0}
.pill{display:inline-flex;align-items:center;gap:7px;padding:8px 13px;border:1px solid var(--line-2);border-radius:999px;font:700 12.5px/1 var(--sans);color:var(--ink-2);background:rgba(255,255,255,.02)}
.pill svg{width:14px;height:14px;stroke:var(--acc);fill:none;stroke-width:2.4}
h1{font-family:var(--disp);font-weight:900;font-size:clamp(40px,5.6vw,72px);line-height:.98;letter-spacing:-.02em;margin-top:18px}
h1 .hl{color:var(--acc)}
.lede{margin-top:20px;font-size:19px;font-weight:600;color:var(--ink-2);max-width:560px}
.subcopy{margin-top:14px;font-size:15.5px;color:var(--muted);max-width:560px}
.hero-actions{display:flex;gap:12px;flex-wrap:wrap;margin-top:30px}
.rating{display:flex;align-items:center;gap:11px;margin-top:22px;color:var(--muted);font-size:13px;font-weight:600}
.stars{display:inline-flex;gap:2px}
.stars svg{width:17px;height:17px;fill:var(--amber)}
.hero-disc{margin-top:18px;color:#69748a;font-size:11.5px;font-weight:700;letter-spacing:.02em}
.hero-visual{position:relative}
.hero-frame{position:relative;border-radius:24px;overflow:hidden;border:1px solid var(--line-2);box-shadow:var(--shadow);aspect-ratio:4/5}
.hero-frame img{width:100%;height:100%;object-fit:cover;display:block;filter:contrast(1.04) saturate(.96)}
.hero-frame:after{content:"";position:absolute;inset:0;background:linear-gradient(180deg,rgba(7,10,15,.05) 0%,rgba(7,10,15,.0) 30%,rgba(7,10,15,.55) 100%)}
.float-card{position:absolute;z-index:3;background:rgba(13,19,27,.82);backdrop-filter:blur(10px);border:1px solid var(--line-2);border-radius:16px;padding:13px 16px;box-shadow:0 18px 44px rgba(0,0,0,.5)}
.float-tl{left:-18px;top:24px}
.float-br{right:-16px;bottom:26px}
.float-card .k{font:900 26px/1 var(--disp);color:var(--acc)}
.float-card .v{font-size:11.5px;color:var(--muted);font-weight:700;margin-top:5px;max-width:130px}
/* stat band */
.statband{border-top:1px solid var(--line);border-bottom:1px solid var(--line);background:linear-gradient(180deg,var(--bg-2),var(--bg))}
.statband .wrap{display:grid;grid-template-columns:repeat(3,1fr);gap:18px;padding:30px 0}
.stat{text-align:center;padding:8px 16px;border-right:1px solid var(--line)}
.stat:last-child{border-right:0}
.stat .n{font-family:var(--disp);font-weight:900;font-size:clamp(40px,5vw,60px);line-height:1;background:linear-gradient(135deg,#fff,var(--acc));-webkit-background-clip:text;background-clip:text;-webkit-text-fill-color:transparent}
.stat .l{margin-top:8px;color:var(--muted);font-size:13px;font-weight:700;letter-spacing:.02em}
/* sections */
section{padding:64px 0}
.sec-head{max-width:760px}
.sec-head.center{margin:0 auto;text-align:center}
h2{font-family:var(--disp);font-weight:900;font-size:clamp(30px,4.2vw,50px);line-height:1.02;letter-spacing:-.02em;margin-top:14px}
h2 .hl{color:var(--acc)}
.sec-head p{margin-top:16px;color:var(--muted);font-size:16px}
/* pairing */
.pair{display:grid;grid-template-columns:1fr auto 1fr;gap:20px;align-items:stretch;margin-top:38px}
.mol{background:linear-gradient(180deg,var(--card),var(--card-2));border:1px solid var(--line-2);border-radius:var(--radius);padding:30px 28px}
.mol .tag{display:inline-block;font:800 11px/1 var(--sans);letter-spacing:.16em;text-transform:uppercase;color:var(--acc);border:1px solid var(--line-2);border-radius:999px;padding:7px 11px}
.mol h3{font-family:var(--disp);font-weight:800;font-size:24px;margin-top:16px}
.mol p{margin-top:10px;color:var(--ink-2);font-size:14.5px}
.plus{display:grid;place-items:center;width:60px;font-family:var(--disp);font-weight:900;font-size:38px;color:var(--acc);align-self:center}
/* science essay */
.essay{margin-top:30px;max-width:840px}
.essay p{color:var(--ink-2);font-size:17px;line-height:1.85;margin-bottom:16px}
.essay p:first-child{font-size:20px;color:var(--ink);font-weight:700}
.essay p:last-child{margin-bottom:0}
/* compounds */
.prod-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:18px;margin-top:38px}
.prod{display:flex;flex-direction:column;background:linear-gradient(180deg,var(--card),var(--card-2));border:1px solid var(--line-2);border-radius:var(--radius);overflow:hidden;transition:transform .16s ease,border-color .16s ease,box-shadow .16s ease}
.prod:hover{transform:translateY(-4px);border-color:var(--acc);box-shadow:0 26px 60px rgba(0,0,0,.5)}
.prod.feat{border-color:rgba(33,230,173,.55)}
.prod-img{height:200px;display:grid;place-items:center;background:radial-gradient(420px 220px at 50% 10%,rgba(33,230,173,.12),transparent 70%);padding:18px}
.prod-img img{max-height:180px;max-width:100%;object-fit:contain;filter:drop-shadow(0 18px 30px rgba(0,0,0,.55))}
.prod-body{padding:6px 22px 24px;display:flex;flex-direction:column;flex:1}
.prod h3{font-family:var(--disp);font-weight:800;font-size:20px}
.prod-body p{margin-top:9px;color:var(--muted);font-size:13.5px;flex:1}
.prod .go{margin-top:18px;display:inline-flex;align-items:center;justify-content:center;gap:7px;font:800 14px/1 var(--sans);border-radius:999px;padding:13px 18px;background:var(--acc);color:var(--acc-ink)}
.prod.feat .badge{display:inline-block;align-self:flex-start;margin:18px 0 -2px 22px;font:800 10.5px/1 var(--sans);letter-spacing:.14em;text-transform:uppercase;color:var(--acc-ink);background:var(--acc);border-radius:999px;padding:6px 11px}
/* proof */
.proof-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:18px;margin-top:36px}
.quote{background:linear-gradient(180deg,var(--card),var(--card-2));border:1px solid var(--line-2);border-radius:var(--radius);padding:26px 24px}
.quote .stars{margin-bottom:14px}
.quote p{color:var(--ink);font-size:15px;line-height:1.6;font-weight:600}
.quote .by{margin-top:16px;color:var(--muted);font-size:12.5px;font-weight:700;display:flex;align-items:center;gap:8px}
.quote .by:before{content:"";width:18px;height:18px;border-radius:50%;background:linear-gradient(135deg,var(--acc),var(--acc-2))}
/* standard / trust */
.standard{background:linear-gradient(135deg,#0c1119,#0a1f1a);border:1px solid var(--line-2);border-radius:28px;padding:46px 40px;margin-top:8px;display:grid;grid-template-columns:1.1fr 1fr;gap:40px;align-items:center}
.std-list{display:grid;gap:14px}
.std-list .row{display:flex;gap:13px;align-items:flex-start}
.std-list .ic{flex:0 0 26px;width:26px;height:26px;border-radius:8px;display:grid;place-items:center;background:rgba(33,230,173,.12)}
.std-list .ic svg{width:15px;height:15px;stroke:var(--acc);fill:none;stroke-width:2.6}
.std-list h4{font-size:15px;font-weight:800;margin:0}
.std-list p{margin:4px 0 0;color:var(--muted);font-size:13.5px}
/* faq */
.faq{margin-top:34px;max-width:840px}
.faq details{border:1px solid var(--line-2);border-radius:14px;padding:0;margin-bottom:12px;background:var(--card-2);overflow:hidden}
.faq summary{list-style:none;cursor:pointer;padding:20px 22px;font-weight:800;font-size:16px;display:flex;justify-content:space-between;gap:16px;align-items:center}
.faq summary::-webkit-details-marker{display:none}
.faq summary .x{flex:0 0 auto;width:22px;height:22px;position:relative;transition:transform .2s ease}
.faq summary .x:before,.faq summary .x:after{content:"";position:absolute;background:var(--acc);border-radius:2px}
.faq summary .x:before{left:0;right:0;top:10px;height:2px}
.faq summary .x:after{top:0;bottom:0;left:10px;width:2px}
.faq details[open] summary .x{transform:rotate(45deg)}
.faq details p{margin:0;padding:0 22px 22px;color:var(--ink-2);font-size:14.5px;line-height:1.7}
/* final */
.final{position:relative;border-radius:30px;overflow:hidden;background:linear-gradient(135deg,#0a1f1a 0%,#0b1016 60%);border:1px solid var(--line-2);padding:56px 44px;text-align:center;margin-top:8px}
.final:before{content:"";position:absolute;inset:0;background:radial-gradient(600px 280px at 50% -10%,rgba(33,230,173,.22),transparent 70%)}
.final>*{position:relative}
.final p.sub{margin:16px auto 0;color:var(--ink-2);max-width:560px;font-size:16px}
.final-links{display:flex;gap:12px;justify-content:center;flex-wrap:wrap;margin-top:30px}
.final-links a{flex:0 1 auto}
/* footer */
.foot{border-top:1px solid var(--line);padding:40px 0 110px;text-align:center}
.legal-note{max-width:900px;margin:0 auto;color:#69748a;font-size:12px;line-height:1.7}
.foot-links{margin-top:16px;color:var(--muted);font-size:13px}
.foot-links a{color:var(--ink-2);text-decoration:underline;text-underline-offset:3px}
.foot-links a:hover{color:var(--acc)}
/* sticky mobile cta */
.sticky-cta{position:fixed;left:12px;right:12px;bottom:calc(12px + env(safe-area-inset-bottom));z-index:90;display:none;align-items:center;justify-content:center;gap:8px;padding:15px 18px;border-radius:999px;font:800 15px/1 var(--sans);color:var(--acc-ink);background:linear-gradient(135deg,var(--acc),var(--acc-2));box-shadow:0 18px 50px rgba(33,230,173,.34)}
/* responsive */
@media(max-width:980px){
  .hero .wrap{grid-template-columns:1fr;gap:30px;padding:48px 0 40px}
  .hero-visual{order:-1}
  .hero-frame{aspect-ratio:16/10}
  .float-tl{left:10px;top:10px}.float-br{right:10px;bottom:10px}
  .statband .wrap{grid-template-columns:1fr;gap:0}
  .stat{border-right:0;border-bottom:1px solid var(--line);padding:18px}
  .stat:last-child{border-bottom:0}
  .pair{grid-template-columns:1fr}.plus{width:auto;padding:6px 0;transform:rotate(90deg)}
  .prod-grid{grid-template-columns:1fr}
  .proof-grid{grid-template-columns:1fr}
  .standard{grid-template-columns:1fr;padding:34px 26px;gap:26px}
}
@media(max-width:560px){
  .wrap{width:min(100% - 28px,720px)}
  .nav-cta{display:none}
  section{padding:46px 0}
  .lede{font-size:17px}
  body{padding-bottom:84px}
  .sticky-cta{display:flex}
  .final{padding:42px 22px;border-radius:22px}
  .float-card{display:none}
}
</style>@endverbatim
  <x-meta-pixel />
  <x-posthog-lander />
</head>
<body>
  <a class="sticky-cta" href="{{ $go($primaryDest) }}" rel="nofollow noopener">{{ $lander->c('hero.primary_cta') ?: 'Get the Recovery Stack' }} →</a>

  <!-- top bar -->
  <header class="topbar">
    <div class="wrap">
      <a class="brand" href="/">
        <span class="brand-mark">P</span>
        <span class="brand-text"><strong>{{ $lander->c('brand.name', 'Professor Peptides') }}</strong><em>{{ $lander->c('brand.tagline', 'Peptide Research Education') }}</em></span>
      </a>
      <a class="btn btn-acc nav-cta" href="{{ $go($primaryDest) }}" rel="nofollow noopener">{{ $lander->c('hero.primary_cta') ?: 'Get the Stack' }} →</a>
    </div>
  </header>

  <!-- hero -->
  <section class="hero">
    <div class="wrap">
      <div class="hero-copy">
        <span class="eyebrow">{{ $lander->c('hero.eyebrow', 'Recovery & tissue-repair research') }}</span>
        <h1>{{ $lander->c('hero.headline') }} <span class="hl">{{ $lander->c('hero.headline_highlight') }}</span></h1>
        <p class="lede">{{ $lander->c('hero.lede') }}</p>
        <p class="subcopy">{{ $lander->c('hero.para1') }}</p>
        <div class="hero-pills">
          @foreach($badges as $b)
            <span class="pill"><svg viewBox="0 0 24 24"><path d="M20 6 9 17l-5-5"/></svg>{{ $b }}</span>
          @endforeach
        </div>
        <div class="hero-actions">
          <a class="btn btn-acc" href="{{ $go($primaryDest) }}" rel="nofollow noopener">{{ $lander->c('hero.primary_cta') ?: 'Get the Recovery Stack' }} →</a>
          <a class="btn btn-ghost" href="#compounds">{{ $lander->c('hero.ghost_cta', 'Compare the compounds') }} ↓</a>
        </div>
        <div class="rating">
          <span class="stars">
            @for($i=0;$i<5;$i++)<svg viewBox="0 0 24 24"><path d="M12 2l3 6.9 7.5.6-5.7 4.9 1.8 7.3L12 17.8 5.1 21.7l1.8-7.3L1.2 9.5l7.5-.6z"/></svg>@endfor
          </span>
          <span>{{ $lander->c('proof.rating_text', 'Trusted by thousands of research customers') }}</span>
        </div>
        <p class="hero-disc">{{ $lander->c('hero.disclaimer', 'Research use only. Not for human consumption. Educational information only.') }}</p>
      </div>
      <div class="hero-visual">
        <div class="hero-frame">
          <img src="{{ $lander->c('hero.image_url') }}" alt="{{ $lander->c('hero.headline') }}" loading="eager" fetchpriority="high" decoding="async" />
        </div>
        <div class="float-card float-tl"><div class="k">&gt;99%</div><div class="v">HPLC-verified purity</div></div>
        <div class="float-card float-br"><div class="k">COA</div><div class="v">on every research batch</div></div>
      </div>
    </div>
  </section>

  <!-- stat band (giant-number social proof) -->
  <div class="statband">
    <div class="wrap">
      @foreach($stats as $s)
        <div class="stat"><div class="n">{{ $s['n'] }}</div><div class="l">{{ $s['l'] }}</div></div>
      @endforeach
    </div>
  </div>

  <!-- the pairing -->
  <section>
    <div class="wrap">
      <div class="sec-head">
        <span class="eyebrow">Why the pairing</span>
        <h2>Two mechanisms researchers <span class="hl">study together.</span></h2>
        <p>{{ $lander->c('hero.para2') }}</p>
      </div>
      <div class="pair">
        <div class="mol">
          <span class="tag">{{ $products[0]['name'] ?? 'BPC-157 Research' }}</span>
          <h3>Repair-pathway signaling</h3>
          <p>{{ $products[0]['body'] ?? 'Body Protection Compound. Studied for its role in repair-pathway signaling across connective-tissue and gut research models.' }}</p>
        </div>
        <div class="plus">+</div>
        <div class="mol">
          <span class="tag">{{ $products[1]['name'] ?? 'TB-500 Research' }}</span>
          <h3>Cell migration &amp; remodeling</h3>
          <p>{{ $products[1]['body'] ?? 'A Thymosin-β4 fragment. Studied for cell migration and tissue-remodeling research.' }}</p>
        </div>
      </div>
    </div>
  </section>

  <!-- science essay -->
  <section style="padding-top:0">
    <div class="wrap">
      <div class="sec-head">
        <span class="eyebrow">The science</span>
        <h2>{{ $lander->c('science.heading', 'The research behind the recovery pairing') }}</h2>
      </div>
      <div class="essay">
        @foreach(preg_split('/\n\n+/', (string) $lander->c('science.sub')) as $para)
          @if(trim($para) !== '')<p>{{ trim($para) }}</p>@endif
        @endforeach
      </div>
    </div>
  </section>

  <!-- compounds compare -->
  <section id="compounds" style="padding-top:0">
    <div class="wrap">
      <div class="sec-head">
        <span class="eyebrow">{{ $lander->c('compounds.eyebrow', 'Research compounds in the recovery category') }}</span>
        <h2>{{ $lander->c('compounds.heading', 'Compare the recovery compounds') }}</h2>
        <p>{{ $lander->c('compounds.sub') }}</p>
      </div>
      <div class="prod-grid">
        @foreach($products as $i => $p)
          <div class="prod {{ $i === 2 ? 'feat' : '' }}">
            @if($i === 2)<span class="badge">The Stack · Both Compounds</span>@endif
            <div class="prod-img"><img src="{{ $p['image_url'] ?? '' }}" alt="{{ $p['name'] ?? '' }}" loading="lazy" /></div>
            <div class="prod-body">
              <h3>{{ $p['name'] ?? '' }}</h3>
              <p>{{ $p['body'] ?? '' }}</p>
              <a class="go" href="{{ $go($p['dest_url'] ?? '#') }}" rel="nofollow noopener">{{ $p['cta_text'] ?? 'View Research →' }}</a>
            </div>
          </div>
        @endforeach
      </div>
    </div>
  </section>

  <!-- mid-page social proof -->
  <section style="padding-top:0">
    <div class="wrap">
      <div class="sec-head center">
        <span class="eyebrow">What researchers say</span>
        <h2>The pairing they <span class="hl">reorder.</span></h2>
      </div>
      <div class="proof-grid">
        @foreach(array_slice($proof, 0, 3) as $q)
          <figure class="quote">
            <span class="stars">@for($i=0;$i<5;$i++)<svg viewBox="0 0 24 24"><path d="M12 2l3 6.9 7.5.6-5.7 4.9 1.8 7.3L12 17.8 5.1 21.7l1.8-7.3L1.2 9.5l7.5-.6z"/></svg>@endfor</span>
            <p>“{{ $q['quote'] ?? '' }}”</p>
            <figcaption class="by">{{ $q['name'] ?? 'Verified research customer' }}</figcaption>
          </figure>
        @endforeach
      </div>
    </div>
  </section>

  <!-- the standard (authority / trust) -->
  <section style="padding-top:0">
    <div class="wrap">
      <div class="standard">
        <div>
          <span class="eyebrow">The standard</span>
          <h2 style="font-size:clamp(26px,3.4vw,38px)">Research-grade, or it doesn’t ship.</h2>
          <p style="margin-top:14px;color:var(--ink-2);font-size:15.5px">Every batch is held to the same bar so your research starts with material you can trust — and verify.</p>
        </div>
        <div class="std-list">
          @php $std = $lander->c('trust.items', []); @endphp
          @foreach(array_slice($std ?: [], 0, 5) as $t)
            <div class="row">
              <span class="ic"><svg viewBox="0 0 24 24"><path d="M20 6 9 17l-5-5"/></svg></span>
              <div><h4>{{ $t['title'] ?? '' }}</h4><p>{{ $t['body'] ?? '' }}</p></div>
            </div>
          @endforeach
        </div>
      </div>
    </div>
  </section>

  <!-- faq -->
  <section style="padding-top:0">
    <div class="wrap">
      <div class="sec-head">
        <span class="eyebrow">Before you decide</span>
        <h2>Questions researchers ask.</h2>
      </div>
      <div class="faq">
        @foreach($faqs as $f)
          <details>
            <summary>{{ $f['q'] ?? '' }}<span class="x"></span></summary>
            <p>{{ $f['a'] ?? '' }}</p>
          </details>
        @endforeach
      </div>
    </div>
  </section>

  <!-- final cta -->
  <section style="padding-top:0">
    <div class="wrap">
      <div class="final">
        <span class="eyebrow" style="justify-content:center">{{ $lander->c('final.eyebrow', 'Ready to compare the recovery stack?') }}</span>
        <h2>{{ $lander->c('final.heading', 'Choose the compound you want to review.') }}</h2>
        <p class="sub">{{ $lander->c('final.body') }}</p>
        <div class="final-links">
          <a class="btn btn-acc" href="{{ $go($primaryDest) }}" rel="nofollow noopener">{{ $lander->c('hero.primary_cta') ?: 'Get the Recovery Stack' }} →</a>
          @foreach(array_slice($lander->c('final.links', []), 0, 3) as $l)
            <a class="btn btn-ghost" href="{{ $go($l['dest_url'] ?? '#') }}" rel="nofollow noopener">{{ $l['label'] ?? '' }}</a>
          @endforeach
        </div>
      </div>
    </div>
  </section>

  <!-- footer -->
  <footer class="foot">
    <div class="wrap">
      <p class="legal-note">{{ $lander->c('legal') }}</p>
      <p class="foot-links">
        <a href="{{ route('privacy') }}">Privacy</a> ·
        <a href="{{ route('terms') }}">Terms</a> ·
        <a href="{{ route('disclaimer') }}">Research-Use Policy</a> · 18+
      </p>
    </div>
  </footer>

  @if($lander->c('giveaway_popup.enabled'))
    <x-giveaway-popup :lander="$lander" />
  @endif
</body>
</html>
