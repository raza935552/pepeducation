@php
    // ORIGINAL light/editorial premium-clinical template (Variant B: Systemic Repair).
    // Deliberately different layout + palette from `recovery-protocol` so the A/B
    // test compares DESIGN, not just copy. Keeps the full tracking loop intact.
    $go = fn (string $dest) => $lander->outbound_slug
        ? route('outbound.track', $lander->outbound_slug) . '?dest=' . urlencode($dest)
        : $dest;
    $blend = 'https://biolinxlabs.com/products/bpc-157-tb-500-blend-20-mg';
    $primaryDest = $lander->c('hero.primary_dest') ?: $blend;
    $products = $lander->c('compounds.products', []);
    $faqs     = $lander->c('faq.items', []);
    $badges = $lander->c('badges') ?: ['>99% Purity', 'COA on Every Batch', 'Third-Party Tested', 'U.S. Fulfillment'];
    $stats  = $lander->c('stats') ?: [
        ['n' => '2', 'l' => 'complementary mechanisms'],
        ['n' => '1', 'l' => 'systemic research protocol'],
        ['n' => '>99%', 'l' => 'HPLC-verified purity'],
    ];
    $feature = $lander->c('proof.featured') ?: [
        'quote' => 'I standardized my systemic-repair research on the BPC-157 + TB-500 pairing. The purity is consistent and the COA is always there — exactly what reproducible work needs.',
        'name'  => 'Verified research customer',
    ];
    if (! $faqs) {
        $faqs = [
            ['q' => 'Are these for human use?', 'a' => 'No. All compounds are sold strictly for laboratory research use only — not for human consumption.'],
            ['q' => 'What does "systemic" mean here?', 'a' => 'BPC-157 is studied across gut, vascular, and connective-tissue research models — broad, system-wide coverage — which is why the literature describes it as systemic.'],
            ['q' => 'Why pair BPC-157 with TB-500?', 'a' => 'The mechanisms are complementary: one studied for repair-pathway signaling, the other for cell migration and tissue remodeling. Together they form a single research protocol.'],
            ['q' => 'What purity and documentation?', 'a' => 'Research-grade, HPLC-verified to >99%, with a Certificate of Analysis available per batch.'],
        ];
    }
    // numbered protocol steps (the layout signature of this template)
    $steps = [
        ['k' => '01', 'name' => $products[0]['name'] ?? 'BPC-157 Research', 't' => 'Systemic signaling', 'b' => $products[0]['body'] ?? 'Studied across gut, vascular, and connective-tissue research models.'],
        ['k' => '02', 'name' => $products[1]['name'] ?? 'TB-500 Research', 't' => 'System-wide cell migration', 'b' => $products[1]['body'] ?? 'A Thymosin-β4 fragment studied for cell migration and tissue remodeling.'],
        ['k' => '03', 'name' => 'The Protocol', 't' => 'One combined research stack', 'b' => 'Complementary, not redundant — which is why advanced labs standardize on the pairing.'],
    ];
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
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Space+Grotesk:wght@500;600;700&display=swap" rel="stylesheet">
  @verbatim<style>
:root{
  --bg:#f5f6fb;--paper:#ffffff;--ink:#0c1430;--ink-2:#27314f;--muted:#626c85;
  --acc:#3a4ff0;--acc-2:#6b7bff;--acc-soft:#eef0ff;--acc-ink:#fff;
  --line:#e6e9f3;--line-2:#d9deec;--ok:#0fae7a;
  --sans:Inter,system-ui,-apple-system,Segoe UI,sans-serif;--disp:'Space Grotesk',Inter,sans-serif;
  --radius:20px;--shadow:0 24px 60px rgba(16,28,70,.10);--shadow-sm:0 10px 28px rgba(16,28,70,.08);
}
*{box-sizing:border-box}html{scroll-behavior:smooth}
body{margin:0;background:var(--bg);color:var(--ink);font-family:var(--sans);font-size:16px;line-height:1.65;-webkit-font-smoothing:antialiased}
a{text-decoration:none;color:inherit}
h1,h2,h3,h4,p{margin:0}
.wrap{width:min(100% - 40px,1140px);margin:0 auto}
.wrap-n{width:min(100% - 40px,860px);margin:0 auto}
.eyebrow{display:inline-flex;align-items:center;gap:9px;color:var(--acc);font:700 12px/1 var(--sans);letter-spacing:.18em;text-transform:uppercase}
.eyebrow:before{content:"";width:6px;height:6px;border-radius:50%;background:var(--acc)}
.btn{display:inline-flex;align-items:center;justify-content:center;gap:8px;font:700 15px/1 var(--sans);border-radius:12px;padding:15px 26px;min-height:52px;cursor:pointer;transition:transform .15s ease,box-shadow .15s ease,background .15s ease,border-color .15s}
.btn-acc{background:var(--acc);color:#fff;box-shadow:0 14px 30px rgba(58,79,240,.26)}
.btn-acc:hover{transform:translateY(-2px);box-shadow:0 20px 40px rgba(58,79,240,.36)}
.btn-line{background:#fff;color:var(--ink);border:1px solid var(--line-2)}
.btn-line:hover{border-color:var(--acc);color:var(--acc)}
/* top bar */
.topbar{position:sticky;top:0;z-index:60;background:rgba(245,246,251,.86);backdrop-filter:blur(12px);border-bottom:1px solid var(--line)}
.topbar .wrap{display:flex;align-items:center;justify-content:space-between;padding:14px 0}
.brand{display:flex;align-items:center;gap:11px}
.brand-mark{width:38px;height:38px;display:grid;place-items:center;border-radius:11px;background:var(--ink);color:#fff;font:700 18px/1 var(--disp)}
.brand-text{display:grid;line-height:1.1}
.brand-text strong{font:700 13px/1 var(--sans);letter-spacing:.02em}
.brand-text em{font-style:normal;color:var(--muted);font-size:10px;letter-spacing:.14em;text-transform:uppercase}
.nav-cta{padding:11px 20px;min-height:auto;font-size:14px}
/* hero (centered editorial) */
.hero{text-align:center;padding:64px 0 34px}
h1{font-family:var(--disp);font-weight:700;font-size:clamp(38px,5.4vw,68px);line-height:1.02;letter-spacing:-.02em;margin:18px auto 0;max-width:14ch}
h1 .hl{color:var(--acc)}
.hero .lede{margin:22px auto 0;font-size:20px;color:var(--ink-2);font-weight:500;max-width:620px}
.hero-actions{display:flex;gap:12px;justify-content:center;flex-wrap:wrap;margin-top:30px}
.badge-row{display:flex;gap:10px;justify-content:center;flex-wrap:wrap;margin-top:26px}
.badge{display:inline-flex;align-items:center;gap:7px;font:600 13px/1 var(--sans);color:var(--ink-2);background:#fff;border:1px solid var(--line);border-radius:999px;padding:9px 14px;box-shadow:var(--shadow-sm)}
.badge svg{width:14px;height:14px;stroke:var(--acc);fill:none;stroke-width:2.6}
.hero-disc{margin-top:20px;color:#8c95a8;font-size:12px;font-weight:600}
/* hero image band */
.hero-band{margin-top:40px}
.hero-band .frame{position:relative;border-radius:26px;overflow:hidden;border:1px solid var(--line);box-shadow:var(--shadow);aspect-ratio:21/9}
.hero-band img{width:100%;height:100%;object-fit:cover;display:block}
.hero-band .frame:after{content:"";position:absolute;inset:0;background:linear-gradient(90deg,rgba(12,20,48,.35),rgba(12,20,48,0) 45%)}
.hero-band .cap{position:absolute;left:28px;bottom:24px;z-index:2;color:#fff;font:700 14px/1.3 var(--sans);max-width:320px;text-shadow:0 2px 18px rgba(0,0,0,.4)}
/* stat row */
.stats{display:grid;grid-template-columns:repeat(3,1fr);gap:18px;margin-top:46px}
.stat{background:var(--paper);border:1px solid var(--line);border-radius:var(--radius);padding:26px 22px;text-align:center;box-shadow:var(--shadow-sm)}
.stat .n{font-family:var(--disp);font-weight:700;font-size:clamp(34px,4.4vw,52px);line-height:1;color:var(--acc)}
.stat .l{margin-top:8px;color:var(--muted);font-size:13.5px;font-weight:600}
/* sections */
section{padding:62px 0}
.sec-head{max-width:680px}
.sec-head.center{margin:0 auto;text-align:center}
h2{font-family:var(--disp);font-weight:700;font-size:clamp(28px,4vw,46px);line-height:1.05;letter-spacing:-.018em;margin-top:13px}
h2 .hl{color:var(--acc)}
.sec-head p{margin-top:15px;color:var(--muted);font-size:16px}
/* protocol steps */
.steps{display:grid;grid-template-columns:repeat(3,1fr);gap:18px;margin-top:38px;counter-reset:s}
.step{position:relative;background:var(--paper);border:1px solid var(--line);border-radius:var(--radius);padding:30px 26px;box-shadow:var(--shadow-sm)}
.step .k{font-family:var(--disp);font-weight:700;font-size:15px;color:var(--acc);letter-spacing:.04em}
.step .nm{display:inline-block;margin-top:14px;font:700 11px/1 var(--sans);letter-spacing:.14em;text-transform:uppercase;color:var(--muted);border:1px solid var(--line-2);border-radius:999px;padding:6px 10px}
.step h3{font-family:var(--disp);font-weight:700;font-size:21px;margin-top:14px}
.step p{margin-top:9px;color:var(--ink-2);font-size:14.5px}
.step:not(:last-child):after{content:"→";position:absolute;right:-13px;top:50%;transform:translateY(-50%);z-index:2;color:var(--acc);font-weight:800;background:var(--bg);padding:0 3px}
/* essay */
.essay{margin-top:30px;max-width:760px;padding-left:24px;border-left:3px solid var(--acc)}
.essay p{color:var(--ink-2);font-size:17px;line-height:1.85;margin-bottom:16px}
.essay p:first-child{font-size:20px;color:var(--ink);font-weight:700}
.essay p:last-child{margin-bottom:0}
/* compound rows */
.rows{margin-top:36px;display:grid;gap:14px}
.row{display:grid;grid-template-columns:120px 1fr auto;gap:24px;align-items:center;background:var(--paper);border:1px solid var(--line);border-radius:var(--radius);padding:20px 24px;box-shadow:var(--shadow-sm);transition:transform .15s ease,border-color .15s ease,box-shadow .15s}
.row:hover{transform:translateY(-2px);border-color:var(--acc);box-shadow:var(--shadow)}
.row.feat{border-color:var(--acc);background:linear-gradient(180deg,#fff,var(--acc-soft))}
.row-img{width:120px;height:120px;display:grid;place-items:center;background:var(--bg);border-radius:14px}
.row-img img{max-width:100%;max-height:104px;object-fit:contain}
.row-info h3{font-family:var(--disp);font-weight:700;font-size:20px;display:flex;align-items:center;gap:10px}
.row-info .tag{font:700 10px/1 var(--sans);letter-spacing:.12em;text-transform:uppercase;color:var(--acc);background:var(--acc-soft);border-radius:999px;padding:5px 9px}
.row-info p{margin-top:7px;color:var(--muted);font-size:14px;max-width:62ch}
.row .go{white-space:nowrap}
/* featured quote */
.feature{background:linear-gradient(135deg,#0c1430,#1d2a6b);border-radius:28px;padding:54px 48px;text-align:center;color:#fff;box-shadow:var(--shadow)}
.feature .stars{display:inline-flex;gap:3px;margin-bottom:18px}
.feature .stars svg{width:20px;height:20px;fill:#ffc24b}
.feature blockquote{margin:0;font-family:var(--disp);font-weight:500;font-size:clamp(20px,2.6vw,30px);line-height:1.4;letter-spacing:-.01em;max-width:18ch;margin:0 auto;color:#fff}
.feature .by{margin-top:22px;color:#aeb8e6;font-size:13px;font-weight:700;letter-spacing:.04em;text-transform:uppercase}
/* standard strip */
.std{display:grid;grid-template-columns:repeat(5,1fr);gap:14px;margin-top:36px}
.std .c{background:var(--paper);border:1px solid var(--line);border-radius:16px;padding:24px 18px;text-align:center;box-shadow:var(--shadow-sm)}
.std .ic{width:42px;height:42px;margin:0 auto 14px;border-radius:12px;display:grid;place-items:center;background:var(--acc-soft)}
.std .ic svg{width:20px;height:20px;stroke:var(--acc);fill:none;stroke-width:2.4}
.std h4{font-size:14px;font-weight:800}
.std p{margin-top:7px;color:var(--muted);font-size:12.5px}
/* faq */
.faq{margin-top:32px;max-width:820px}
.faq .qa{border-bottom:1px solid var(--line);padding:22px 0}
.faq .qa:first-child{border-top:1px solid var(--line)}
.faq h4{font-family:var(--disp);font-weight:700;font-size:18px;display:flex;gap:12px}
.faq h4 .n{color:var(--acc)}
.faq p{margin-top:10px;color:var(--ink-2);font-size:14.5px;padding-left:30px}
/* final */
.final{border-radius:28px;background:linear-gradient(135deg,var(--acc),var(--acc-2));padding:56px 44px;text-align:center;color:#fff;box-shadow:0 30px 70px rgba(58,79,240,.3)}
.final h2{color:#fff}
.final .eyebrow{color:#dfe4ff;justify-content:center}
.final .eyebrow:before{background:#dfe4ff}
.final p.sub{margin:16px auto 0;color:#eaedff;max-width:560px;font-size:16px}
.final-links{display:flex;gap:12px;justify-content:center;flex-wrap:wrap;margin-top:30px}
.final .btn-acc{background:#fff;color:var(--acc);box-shadow:0 14px 30px rgba(0,0,0,.18)}
.final .btn-line{background:rgba(255,255,255,.08);color:#fff;border-color:rgba(255,255,255,.5)}
.final .btn-line:hover{background:rgba(255,255,255,.16);color:#fff;border-color:#fff}
/* footer */
.foot{padding:42px 0 110px;text-align:center;border-top:1px solid var(--line);margin-top:62px}
.legal-note{max-width:900px;margin:0 auto;color:#8c95a8;font-size:12px;line-height:1.7}
.foot-links{margin-top:16px;color:var(--muted);font-size:13px}
.foot-links a{color:var(--ink-2);text-decoration:underline;text-underline-offset:3px}
.foot-links a:hover{color:var(--acc)}
/* sticky mobile cta */
.sticky-cta{position:fixed;left:12px;right:12px;bottom:calc(12px + env(safe-area-inset-bottom));z-index:90;display:none;align-items:center;justify-content:center;gap:8px;padding:15px 18px;border-radius:14px;font:700 15px/1 var(--sans);color:#fff;background:var(--acc);box-shadow:0 16px 40px rgba(58,79,240,.4)}
@media(max-width:980px){
  .stats{grid-template-columns:1fr;gap:12px}
  .steps{grid-template-columns:1fr;gap:14px}
  .step:not(:last-child):after{content:"↓";right:auto;left:50%;top:auto;bottom:-13px;transform:translateX(-50%)}
  .row{grid-template-columns:80px 1fr;gap:16px}
  .row .go{grid-column:1/-1;justify-self:start}
  .row-img{width:80px;height:80px}
  .std{grid-template-columns:repeat(2,1fr)}
  .std .c:nth-child(5){grid-column:1/-1}
  .hero-band .frame{aspect-ratio:16/10}
}
@media(max-width:560px){
  .wrap{width:min(100% - 28px,720px)}
  .nav-cta{display:none}
  section{padding:46px 0}
  body{padding-bottom:84px}
  .sticky-cta{display:flex}
  .hero{padding:46px 0 26px}
  .hero .lede{font-size:17px}
  .feature{padding:38px 24px}
  .final{padding:40px 22px;border-radius:22px}
  .hero-band .cap{left:16px;bottom:14px;font-size:12px}
}
</style>@endverbatim
  <x-meta-pixel />
  <x-posthog-lander />
</head>
<body>
  <a class="sticky-cta" href="{{ $go($primaryDest) }}" rel="nofollow noopener">{{ $lander->c('hero.primary_cta') ?: 'Get the Repair Stack' }} →</a>

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

  <!-- hero (centered) -->
  <section class="hero">
    <div class="wrap-n">
      <span class="eyebrow">{{ $lander->c('hero.eyebrow', 'Systemic repair & resilience research') }}</span>
      <h1>{{ $lander->c('hero.headline') }} <span class="hl">{{ $lander->c('hero.headline_highlight') }}</span></h1>
      <p class="lede">{{ $lander->c('hero.lede') }}</p>
      <div class="hero-actions">
        <a class="btn btn-acc" href="{{ $go($primaryDest) }}" rel="nofollow noopener">{{ $lander->c('hero.primary_cta') ?: 'Get the Repair Stack' }} →</a>
        <a class="btn btn-line" href="#compounds">{{ $lander->c('hero.ghost_cta', 'Compare the compounds') }} ↓</a>
      </div>
      <div class="badge-row">
        @foreach($badges as $b)
          <span class="badge"><svg viewBox="0 0 24 24"><path d="M20 6 9 17l-5-5"/></svg>{{ $b }}</span>
        @endforeach
      </div>
      <p class="hero-disc">{{ $lander->c('hero.disclaimer', 'Research use only. Not for human consumption. Educational information only.') }}</p>
    </div>
    <div class="wrap hero-band">
      <div class="frame">
        <img src="{{ $lander->c('hero.image_url') }}" alt="{{ $lander->c('hero.headline') }}" loading="eager" fetchpriority="high" decoding="async" />
        <span class="cap">{{ $lander->c('hero.para1') }}</span>
      </div>
      <div class="stats">
        @foreach($stats as $s)
          <div class="stat"><div class="n">{{ $s['n'] }}</div><div class="l">{{ $s['l'] }}</div></div>
        @endforeach
      </div>
    </div>
  </section>

  <!-- protocol steps -->
  <section style="padding-top:18px">
    <div class="wrap">
      <div class="sec-head center">
        <span class="eyebrow">The protocol</span>
        <h2>Two mechanisms. <span class="hl">One systemic stack.</span></h2>
        <p>{{ $lander->c('hero.para2') }}</p>
      </div>
      <div class="steps">
        @foreach($steps as $st)
          <article class="step">
            <div class="k">{{ $st['k'] }}</div>
            <span class="nm">{{ $st['name'] }}</span>
            <h3>{{ $st['t'] }}</h3>
            <p>{{ $st['b'] }}</p>
          </article>
        @endforeach
      </div>
    </div>
  </section>

  <!-- science essay -->
  <section style="padding-top:0">
    <div class="wrap">
      <div class="sec-head">
        <span class="eyebrow">The science</span>
        <h2>{{ $lander->c('science.heading', 'One systemic research protocol') }}</h2>
      </div>
      <div class="essay">
        @foreach(preg_split('/\n\n+/', (string) $lander->c('science.sub')) as $para)
          @if(trim($para) !== '')<p>{{ trim($para) }}</p>@endif
        @endforeach
      </div>
    </div>
  </section>

  <!-- compounds compare (rows) -->
  <section id="compounds" style="padding-top:0">
    <div class="wrap">
      <div class="sec-head">
        <span class="eyebrow">{{ $lander->c('compounds.eyebrow', 'Research compounds in the systemic-repair category') }}</span>
        <h2>{{ $lander->c('compounds.heading', 'Compare the repair compounds') }}</h2>
        <p>{{ $lander->c('compounds.sub') }}</p>
      </div>
      <div class="rows">
        @foreach($products as $i => $p)
          <div class="row {{ $i === 2 ? 'feat' : '' }}">
            <div class="row-img"><img src="{{ $p['image_url'] ?? '' }}" alt="{{ $p['name'] ?? '' }}" loading="lazy" /></div>
            <div class="row-info">
              <h3>{{ $p['name'] ?? '' }}@if($i === 2)<span class="tag">The Stack</span>@endif</h3>
              <p>{{ $p['body'] ?? '' }}</p>
            </div>
            <a class="btn btn-acc go" href="{{ $go($p['dest_url'] ?? '#') }}" rel="nofollow noopener">{{ $p['cta_text'] ?? 'View Research →' }}</a>
          </div>
        @endforeach
      </div>
    </div>
  </section>

  <!-- featured quote -->
  <section style="padding-top:0">
    <div class="wrap-n">
      <div class="feature">
        <span class="stars">@for($i=0;$i<5;$i++)<svg viewBox="0 0 24 24"><path d="M12 2l3 6.9 7.5.6-5.7 4.9 1.8 7.3L12 17.8 5.1 21.7l1.8-7.3L1.2 9.5l7.5-.6z"/></svg>@endfor</span>
        <blockquote>“{{ $feature['quote'] }}”</blockquote>
        <div class="by">{{ $feature['name'] }}</div>
      </div>
    </div>
  </section>

  <!-- standard / trust strip -->
  <section style="padding-top:0">
    <div class="wrap">
      <div class="sec-head center">
        <span class="eyebrow">The standard</span>
        <h2>Research-grade, <span class="hl">or it doesn’t ship.</span></h2>
      </div>
      <div class="std">
        @foreach(array_slice($lander->c('trust.items', []) ?: [], 0, 5) as $t)
          <div class="c">
            <span class="ic"><svg viewBox="0 0 24 24"><path d="M20 6 9 17l-5-5"/></svg></span>
            <h4>{{ $t['title'] ?? '' }}</h4>
            <p>{{ $t['body'] ?? '' }}</p>
          </div>
        @endforeach
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
        @foreach($faqs as $idx => $f)
          <div class="qa">
            <h4><span class="n">{{ sprintf('%02d', $idx + 1) }}</span>{{ $f['q'] ?? '' }}</h4>
            <p>{{ $f['a'] ?? '' }}</p>
          </div>
        @endforeach
      </div>
    </div>
  </section>

  <!-- final cta -->
  <section style="padding-top:0">
    <div class="wrap">
      <div class="final">
        <span class="eyebrow">{{ $lander->c('final.eyebrow', 'Ready to run the repair protocol?') }}</span>
        <h2>{{ $lander->c('final.heading', 'Choose the compound you want to review.') }}</h2>
        <p class="sub">{{ $lander->c('final.body') }}</p>
        <div class="final-links">
          <a class="btn btn-acc" href="{{ $go($primaryDest) }}" rel="nofollow noopener">{{ $lander->c('hero.primary_cta') ?: 'Get the Repair Stack' }} →</a>
          @foreach(array_slice($lander->c('final.links', []), 0, 3) as $l)
            <a class="btn btn-line" href="{{ $go($l['dest_url'] ?? '#') }}" rel="nofollow noopener">{{ $l['label'] ?? '' }}</a>
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
