@php
    // CMS-driven render (B variant). Images, outbound /go links, hero image, tracking,
    // giveaway popup, robots and footer links are ALL held constant via $lander (identical
    // accessors to the control). Only the LAYOUT + COPY below is the changed test variable,
    // and the B copy is hardcoded on purpose because this is a fixed A/B test variant.
    //
    // DESIGN: a deliberately DIFFERENT visual identity from the control's soft-pink Playfair
    // editorial. This B is a clinical-yet-warm "comparison-app" look: geometric grotesk type
    // (Space Grotesk display + Sora body), an electric-indigo accent with a mint signal chip,
    // crisp 1px-bordered cards, layered soft shadows, and a sticky "Compare" bar.
    $go = fn (string $dest) => $lander->outbound_slug
        ? route('outbound.track', $lander->outbound_slug) . '?dest=' . urlencode($dest)
        : $dest;
    $icons = [
        // science (4) + trust (5) line-icon svg paths, fixed to the layout slots
        'science' => [
            '<path d="M48 18v60M45 19c-5-7-17-5-21 3-7 0-13 6-13 14 0 3 1 6 3 8-5 3-8 8-8 15 0 10 8 18 18 18h21M51 19c5-7 17-5 21 3 7 0 13 6 13 14 0 3-1 6-3 8 5 3 8 8 8 15 0 10-8 18-18 18H51"/><path d="M25 34c5 0 9 3 10 8M22 58c5-1 10 1 13 5M72 34c-5 0-9 3-10 8M75 58c-5-1-10 1-13 5"/>',
            '<path d="M42 10v23c0 10 16 10 24 22 11 17-1 35-23 35-17 0-32-10-32-27 0-14 12-21 25-29V10h6Z"/><path d="M52 58c-7 5-15 6-23 4"/>',
            '<circle cx="32" cy="32" r="14"/><circle cx="64" cy="32" r="14"/><circle cx="32" cy="64" r="14"/><circle cx="64" cy="64" r="14"/><path d="M31 31c4-4 9-3 12 1M63 31c4-4 9-3 12 1M31 63c4-4 9-3 12 1M63 63c4-4 9-3 12 1"/>',
            '<path d="M49 88c-21 0-35-13-35-33 0-16 12-28 24-42 0 15 8 20 9 31 9-10 15-24 14-38 18 13 27 31 27 50 0 19-15 32-39 32Z"/><path d="M50 82c10 0 18-7 18-16 0-8-5-14-11-21-2 8-7 14-14 19 0-7-4-10-8-15-5 6-8 11-8 17 0 9 10 16 23 16Z"/>',
        ],
        'trust' => [
            '<path d="M32 8c13 0 24 11 24 24S45 56 32 56 8 45 8 32 19 8 32 8Z"/><path d="M23 41 41 23"/><circle cx="24" cy="24" r="3"/><circle cx="40" cy="40" r="3"/>',
            '<path d="M32 9c13 12 22 25 22 36 0 8-7 14-22 14S10 53 10 45c0-11 9-24 22-36Z"/><path d="M23 38l6 6 13-15"/>',
            '<path d="M22 12h20l6 6v34H16V12h6Z"/><path d="M22 29h20M22 39h12M25 47l5 4 10-13"/>',
            '<circle cx="32" cy="32" r="24"/><path d="M22 33.5 29 40l14-16"/>',
            '<path d="M32 8 52 17v15c0 13-8 22-20 26C20 54 12 45 12 32V17l20-9Z"/><path d="M23 33l6 6 13-16"/>',
        ],
    ];

    // Held-constant test variables pulled from the SAME $lander source as the control:
    // outbound /go destinations + product images + hero image. We never hardcode these,
    // so the outbound target set and images are byte-for-byte identical to the control.
    $products = $lander->c('compounds.products', []);
    // B copy overlay for each compound card, matched by index to the held-constant product set.
    $cardCopy = [
        ['focus' => 'A single GLP-1 receptor pathway',     'why' => 'Appetite signaling, satiety response, and metabolic regulation are active areas of study around this pathway.'],
        ['focus' => 'Two receptor pathways (GLP-1 + GIP)',  'why' => 'Dual-pathway signaling involved in nutrient processing and appetite-related research.'],
        ['focus' => 'An emerging multi-receptor pathway',   'why' => 'A newer area of laboratory research involving more than one receptor target.'],
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
  <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;600;700&family=Sora:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  @verbatim<style>
/* ============================================================
   B VARIANT: "Compare" comparison-app look.
   Type:    Space Grotesk (display) + Sora (geometric body).
   Palette: near-black ink, off-white canvas, electric indigo
            accent (#4f46e5) + mint "signal" chip (#16c098).
            Deliberately NOT the control's pink Playfair editorial.
   Surfaces: crisp 1px borders, layered soft shadows, 12-22px radii.
   ============================================================ */
:root{
  --ink:#0c0f1a;--ink-2:#1d2333;--body:#3c4357;--muted:#71798c;
  --canvas:#f3f4f8;--surface:#ffffff;--surface-2:#f8f9fc;
  --indigo:#4f46e5;--indigo-2:#6d63ff;--indigo-soft:#ecebff;--indigo-ink:#3026c4;
  --signal:#16c098;--signal-soft:#dbf7ee;--signal-ink:#0a7a5e;
  --line:#e5e7f0;--line-2:#d4d8e6;
  --sh-sm:0 1px 2px rgba(12,15,26,.05),0 4px 12px rgba(12,15,26,.05);
  --sh:0 10px 28px rgba(12,15,26,.08),0 2px 6px rgba(12,15,26,.05);
  --sh-lg:0 30px 64px rgba(29,35,51,.18);
  --r-lg:22px;--r-md:14px;--r-sm:10px;
  --disp:'Space Grotesk',ui-sans-serif,system-ui,sans-serif;
  --sans:'Sora',ui-sans-serif,system-ui,-apple-system,Segoe UI,sans-serif;
}
*{box-sizing:border-box}
html{scroll-behavior:smooth}
body{margin:0;background:var(--canvas);color:var(--ink);font-family:var(--sans);font-size:16px;line-height:1.6;-webkit-font-smoothing:antialiased}
a{text-decoration:none;color:inherit}
img{display:block;max-width:100%}
h1,h2,h3,h4,p{margin:0}
.disp{font-family:var(--disp)}
.skip-link{position:absolute;left:-999px}
.skip-link:focus{left:12px;top:12px;z-index:999;background:#fff;padding:12px;border-radius:10px}
.wrap{width:min(100% - 40px,1180px);margin:0 auto}

/* ---- top app bar ---- */
.appbar{position:sticky;top:0;z-index:60;background:rgba(243,244,248,.82);backdrop-filter:blur(12px);border-bottom:1px solid var(--line)}
.appbar-in{display:flex;align-items:center;justify-content:space-between;gap:20px;height:64px}
.logo{display:flex;align-items:center;gap:11px}
.logo-mark{width:38px;height:38px;border-radius:11px;background:var(--ink);color:#fff;display:grid;place-items:center;font-family:var(--disp);font-weight:700;font-size:15px;letter-spacing:.02em;box-shadow:var(--sh-sm)}
.logo-txt{display:flex;flex-direction:column;line-height:1.05}
.logo-txt b{font-family:var(--disp);font-weight:700;font-size:15px;letter-spacing:-.01em;color:var(--ink)}
.logo-txt span{font-size:9.5px;letter-spacing:.16em;text-transform:uppercase;color:var(--muted);font-weight:600}
.appnav{display:flex;gap:26px;font-size:14px;font-weight:500;color:var(--body)}
.appnav a:hover{color:var(--indigo)}
.appbar-cta{display:inline-flex;align-items:center;gap:8px;padding:11px 18px;border-radius:999px;background:var(--indigo);color:#fff;font-weight:600;font-size:14px;box-shadow:0 8px 20px rgba(79,70,229,.28)}
.appbar-cta:hover{background:var(--indigo-ink)}

/* ---- buttons / chips ---- */
.btn{display:inline-flex;align-items:center;justify-content:center;gap:9px;font-weight:600;border-radius:999px;font-family:var(--sans);cursor:pointer;line-height:1;transition:transform .12s ease,box-shadow .12s ease,background .12s ease}
.btn-lg{padding:17px 26px;font-size:16px}
.btn-md{padding:13px 20px;font-size:14px}
.btn-primary{background:var(--indigo);color:#fff;box-shadow:0 14px 30px rgba(79,70,229,.30)}
.btn-primary:hover{transform:translateY(-2px);box-shadow:0 18px 36px rgba(79,70,229,.36)}
.btn-ink{background:var(--ink);color:#fff;box-shadow:0 12px 26px rgba(12,15,26,.20)}
.btn-ink:hover{transform:translateY(-2px);background:var(--indigo-ink)}
.btn-ghost{background:#fff;color:var(--ink);border:1px solid var(--line-2);box-shadow:var(--sh-sm)}
.btn-ghost:hover{border-color:var(--indigo);color:var(--indigo)}
.chip{display:inline-flex;align-items:center;gap:7px;padding:6px 12px;border-radius:999px;font-family:var(--disp);font-size:11px;font-weight:600;letter-spacing:.04em;text-transform:uppercase}
.chip-signal{background:var(--signal-soft);color:var(--signal-ink)}
.eyebrow{display:inline-flex;align-items:center;gap:8px;font-family:var(--disp);font-size:12px;font-weight:600;letter-spacing:.18em;text-transform:uppercase;color:var(--indigo)}
.eyebrow:before{content:'';width:22px;height:2px;border-radius:2px;background:var(--indigo)}

/* ---- hero ---- */
.hero{padding:46px 0 36px}
.hero-grid{display:grid;grid-template-columns:1.08fr .92fr;gap:48px;align-items:center}
.hero h1{font-family:var(--disp);font-weight:700;font-size:clamp(33px,4.6vw,58px);line-height:1.04;letter-spacing:-.03em;margin-top:18px;color:var(--ink)}
.hero h1 .hl{color:var(--indigo)}
.hero .sub{margin-top:20px;font-size:18px;line-height:1.65;color:var(--body);max-width:560px}
.hero-quotes{display:flex;gap:10px;flex-wrap:wrap;margin-top:24px}
.hero-quote{flex:1 1 0;min-width:150px;background:#fff;border:1px solid var(--line);border-radius:var(--r-md);padding:14px 16px;box-shadow:var(--sh-sm)}
.hero-quote .sig{font-family:var(--disp);font-size:11px;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:.1em}
.hero-quote .said{margin-top:5px;font-family:var(--disp);font-weight:700;font-size:20px;color:var(--ink);letter-spacing:-.01em}
.hero-quote:nth-child(2) .said{color:var(--signal-ink)}
.hero-sub2{margin-top:18px;font-size:15.5px;line-height:1.65;color:var(--body);max-width:560px}
.hero-actions{display:flex;gap:12px;flex-wrap:wrap;margin-top:26px}
.micro{margin-top:18px;font-size:12px;color:var(--muted);font-weight:500;display:flex;align-items:center;gap:8px}
.micro:before{content:'';width:7px;height:7px;border-radius:50%;background:var(--signal);box-shadow:0 0 0 4px var(--signal-soft);flex:0 0 auto}
.hero-visual{position:relative}
.hero-frame{position:relative;border-radius:24px;overflow:hidden;border:1px solid var(--line);box-shadow:var(--sh-lg);background:#fff}
.hero-frame:before{content:'';position:absolute;inset:0;z-index:2;pointer-events:none;background:linear-gradient(150deg,rgba(79,70,229,.10),rgba(22,192,152,.05) 60%,transparent)}
.hero-frame img{width:100%;height:480px;object-fit:cover;object-position:center 42%}
.hero-tag{position:absolute;z-index:3;left:16px;bottom:16px;display:flex;align-items:center;gap:11px;padding:11px 15px;border-radius:14px;background:rgba(12,15,26,.80);backdrop-filter:blur(6px);color:#fff;box-shadow:var(--sh)}
.hero-tag svg{width:28px;height:28px;fill:none;stroke:var(--signal);stroke-width:5;stroke-linecap:round;stroke-linejoin:round;flex:0 0 auto}
.hero-tag b{font-family:var(--disp);font-size:13px;font-weight:700;display:block}
.hero-tag em{font-style:normal;font-size:11px;opacity:.72}

/* ---- section frame ---- */
.section{padding:56px 0}
.section.tint{background:var(--surface-2);border-block:1px solid var(--line)}
.section.dark{background:var(--ink)}
.sec-head{max-width:700px;margin-bottom:30px}
.sec-head.center{margin-inline:auto;text-align:center}
.sec-head h2{font-family:var(--disp);font-weight:700;font-size:clamp(26px,3.4vw,40px);line-height:1.08;letter-spacing:-.025em;margin-top:14px;color:var(--ink)}
.sec-head p{margin-top:14px;font-size:16px;line-height:1.6;color:var(--body)}

/* ---- COMPARE: the 3 product cards (primary conversion) ---- */
.compare-note{display:flex;align-items:center;gap:10px;justify-content:center;margin:0 auto 26px;max-width:780px;padding:12px 18px;border-radius:var(--r-md);background:#fff;border:1px dashed var(--line-2);color:var(--muted);font-size:13px;font-weight:500;text-align:center;line-height:1.5}
.compare-note span:first-child{color:var(--amber,#e08a00);font-size:15px}
.cards{display:grid;grid-template-columns:repeat(3,1fr);gap:18px}
.card{display:flex;flex-direction:column;background:#fff;border:1px solid var(--line);border-radius:var(--r-lg);overflow:hidden;box-shadow:var(--sh);transition:transform .16s ease,box-shadow .16s ease,border-color .16s ease}
.card:hover{transform:translateY(-5px);box-shadow:var(--sh-lg);border-color:var(--indigo-2)}
.card-top{position:relative;height:182px;background:radial-gradient(120% 120% at 50% 0,#fff 0,var(--indigo-soft) 100%);display:grid;place-items:center;border-bottom:1px solid var(--line)}
.card-top img{max-height:150px;max-width:78%;object-fit:contain;filter:drop-shadow(0 14px 22px rgba(29,35,51,.18))}
.card-idx{position:absolute;top:12px;left:12px;width:30px;height:30px;border-radius:9px;background:var(--ink);color:#fff;font-family:var(--disp);font-weight:700;font-size:14px;display:grid;place-items:center}
.card-chip{position:absolute;top:14px;right:12px}
.card-body{display:flex;flex-direction:column;flex:1;padding:20px 20px 22px}
.card-body h3{font-family:var(--disp);font-weight:700;font-size:21px;letter-spacing:-.01em;color:var(--ink)}
.card-label{margin-top:12px;font-family:var(--disp);font-size:11px;font-weight:600;letter-spacing:.06em;text-transform:uppercase;color:var(--indigo)}
.card-focus{margin-top:5px;font-weight:600;font-size:14.5px;color:var(--ink-2)}
.card-body p{margin-top:9px;font-size:13.5px;line-height:1.6;color:var(--body)}
.card-go{margin-top:auto;padding-top:18px}
.card-go .btn{width:100%}
.card:hover .card-go .btn-ink{background:var(--indigo)}

/* ---- principle / honesty block ---- */
.principle-lead{max-width:740px}
.principle-lead h2{font-family:var(--disp);font-weight:700;font-size:clamp(24px,3.2vw,36px);letter-spacing:-.025em;line-height:1.1;margin-top:14px;color:var(--ink)}
.principle-lead p{margin-top:16px;font-size:16.5px;line-height:1.75;color:var(--body)}
.principle-rows{display:grid;grid-template-columns:repeat(3,1fr);gap:14px;margin-top:28px}
.prow{background:#fff;border:1px solid var(--line);border-radius:var(--r-md);padding:22px;box-shadow:var(--sh-sm)}
.prow .pi{width:42px;height:42px;border-radius:12px;background:var(--indigo-soft);color:var(--indigo);display:grid;place-items:center;margin-bottom:14px}
.prow .pi svg{width:22px;height:22px;fill:none;stroke:currentColor;stroke-width:1.9;stroke-linecap:round;stroke-linejoin:round}
.prow b{font-family:var(--disp);font-weight:700;font-size:15px;color:var(--ink);display:block}
.prow span{display:block;margin-top:7px;font-size:13.5px;color:var(--body);line-height:1.55}
.inline-cta{display:inline-flex;align-items:center;gap:7px;font-family:var(--disp);font-weight:600;font-size:14px;color:var(--indigo);margin-top:26px}
.inline-cta:hover{gap:11px}

/* ---- science (messenger analogy + signals grid) ---- */
.sci-intro{max-width:780px}
.sci-lead{font-family:var(--disp);font-weight:700;font-size:clamp(20px,2.6vw,26px);line-height:1.3;letter-spacing:-.02em;color:var(--ink);margin-bottom:16px}
.sci-intro p{font-size:16.5px;line-height:1.8;color:var(--body);margin-bottom:14px}
.sci-intro p:last-child{margin-bottom:0}
.sci-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-top:36px}
.sci-item{background:#fff;border:1px solid var(--line);border-radius:var(--r-md);padding:22px 20px;box-shadow:var(--sh-sm)}
.sci-item .ic{width:54px;height:54px;border-radius:14px;background:var(--surface-2);display:grid;place-items:center;margin-bottom:14px;border:1px solid var(--line)}
.sci-item .ic svg{width:32px;height:32px;fill:none;stroke:var(--indigo);stroke-width:4;stroke-linecap:round;stroke-linejoin:round}
.sci-item h3{font-family:var(--disp);font-weight:700;font-size:15px;color:var(--ink)}
.sci-item p{margin-top:8px;font-size:13px;line-height:1.55;color:var(--body)}

/* ---- checklist (buyer tool) ---- */
.steps{display:grid;grid-template-columns:repeat(5,1fr);gap:14px}
.step{position:relative;background:#fff;border:1px solid var(--line);border-radius:var(--r-md);padding:22px 18px 20px;box-shadow:var(--sh-sm)}
.step .num{font-family:var(--disp);font-weight:700;font-size:13px;color:var(--indigo);background:var(--indigo-soft);width:36px;height:36px;border-radius:10px;display:grid;place-items:center;margin-bottom:14px}
.step h3{font-family:var(--disp);font-weight:700;font-size:14.5px;color:var(--ink);line-height:1.2}
.step p{margin-top:8px;font-size:12.5px;line-height:1.55;color:var(--body)}
.steps-cta{margin-top:32px;text-align:center}

/* ---- trust strip ---- */
.trust{display:grid;grid-template-columns:repeat(5,1fr);gap:14px}
.titem{text-align:center;padding:26px 16px;background:#fff;border:1px solid var(--line);border-radius:var(--r-md);box-shadow:var(--sh-sm)}
.titem .ts{width:46px;height:46px;margin:0 auto 14px;fill:none;stroke:var(--signal);stroke-width:4;stroke-linecap:round;stroke-linejoin:round}
.titem h3{font-family:var(--disp);font-weight:700;font-size:14px;color:var(--ink)}
.titem p{margin-top:8px;font-size:12px;line-height:1.5;color:var(--muted)}

/* ---- final dark CTA band ---- */
.section.dark .sec-head h2{color:#fff}
.section.dark .sec-head p{color:#aab2c6}
.section.dark .eyebrow{color:var(--signal)}
.section.dark .eyebrow:before{background:var(--signal)}
.final-grid{display:grid;grid-template-columns:1fr 1fr;gap:44px;align-items:center}
.final-grid .lead h2{font-family:var(--disp);font-weight:700;font-size:clamp(26px,3.4vw,40px);letter-spacing:-.025em;line-height:1.08;margin-top:14px;color:#fff}
.final-grid .lead p{margin-top:14px;font-size:16px;line-height:1.6;color:#aab2c6;max-width:460px}
.final-links{display:grid;gap:12px}
.flink{display:flex;align-items:center;justify-content:space-between;gap:14px;padding:18px 22px;border-radius:var(--r-md);background:#171c2b;border:1px solid #2a3043;color:#fff;font-family:var(--disp);font-weight:700;font-size:17px;transition:background .14s ease,transform .14s ease,border-color .14s ease}
.flink:hover{background:var(--indigo);border-color:var(--indigo-2);transform:translateX(4px)}
.flink .arrow{width:34px;height:34px;border-radius:9px;background:rgba(255,255,255,.1);display:grid;place-items:center;font-size:16px;flex:0 0 auto}

/* ---- legal + footer ---- */
.legal{padding:42px 0 30px;text-align:center}
.legal p{max-width:880px;margin:0 auto;font-size:12px;line-height:1.7;color:var(--muted)}
.legal .links{margin-top:14px;font-size:12px;font-weight:600;color:var(--body)}
.legal .links a{color:var(--body)}
.legal .links a:hover{color:var(--indigo)}

/* ---- sticky compare bar (mobile) ---- */
.sticky-bar{position:fixed;left:12px;right:12px;bottom:calc(12px + env(safe-area-inset-bottom));z-index:80;display:none;align-items:center;gap:12px;padding:11px 12px 11px 18px;border-radius:999px;background:var(--ink);color:#fff;box-shadow:0 18px 46px rgba(12,15,26,.42)}
.sticky-bar .lbl{font-family:var(--disp);font-weight:700;font-size:14px;flex:1}
.sticky-bar .go{padding:11px 18px;border-radius:999px;background:var(--indigo);color:#fff;font-weight:600;font-size:13px;white-space:nowrap}

/* ---- responsive ---- */
@media(max-width:1080px){
  .hero-grid{grid-template-columns:1fr;gap:30px}
  .hero-visual{order:-1}
  .hero-frame img{height:340px}
  .sci-grid{grid-template-columns:repeat(2,1fr)}
}
@media(max-width:860px){
  .appnav{display:none}
  .cards{grid-template-columns:1fr;gap:14px}
  .card{flex-direction:row;align-items:stretch}
  .card-top{width:132px;flex:0 0 132px;height:auto;border-bottom:0;border-right:1px solid var(--line)}
  .card-top img{max-height:108px}
  .card-body{padding:16px}
  .steps{grid-template-columns:repeat(2,1fr)}
  .step:last-child{grid-column:1/-1}
  .trust{grid-template-columns:repeat(2,1fr)}
  .titem:last-child{grid-column:1/-1}
  .principle-rows{grid-template-columns:1fr}
  .final-grid{grid-template-columns:1fr;gap:24px}
}
@media(max-width:560px){
  .wrap{width:min(100% - 28px,560px)}
  body{padding-bottom:80px}
  .appbar-cta{display:none}
  .sticky-bar{display:flex}
  .hero{padding:30px 0 24px}
  .hero h1{font-size:30px}
  .hero .sub{font-size:16px}
  .hero-actions .btn{flex:1 1 100%}
  .hero-frame img{height:280px}
  .hero-quote .said{font-size:18px}
  .section{padding:42px 0}
  .sci-grid{grid-template-columns:1fr}
  .card-top{width:106px;flex:0 0 106px}
  .card-top img{max-height:88px}
  .card-body h3{font-size:18px}
  .flink{font-size:15px;padding:15px 18px}
}
  </style>@endverbatim
  <x-meta-pixel />
  <x-posthog-lander />
</head>
<body>
  <a class="skip-link" href="#main">Skip to content</a>

  <header class="appbar" aria-label="Main header">
    <div class="wrap appbar-in">
      <a class="logo" href="#top">
        <span class="logo-mark" aria-hidden="true">PP</span>
        <span class="logo-txt"><b>{{ $lander->c('brand.name') }}</b><span>{{ $lander->c('brand.tagline') }}</span></span>
      </a>
      <nav class="appnav" aria-label="Main navigation">
        <a href="#compare">Compare</a>
        <a href="#science">The Biology</a>
        <a href="#framework">Source Check</a>
        <a href="#source">Standards</a>
      </nav>
      <a class="appbar-cta" href="#compare">Compare 3 Compounds &rarr;</a>
    </div>
  </header>

  <a class="sticky-bar" href="#compare" aria-label="Jump to compound comparison">
    <span class="lbl">Compare research compounds</span>
    <span class="go">Open &rarr;</span>
  </a>

  <main id="main">

    {{-- 1. HERO (held-constant hero image; B curiosity-led copy; primary CTA jumps to #compare) --}}
    <section id="top" class="hero">
      <div class="wrap hero-grid">
        <div class="hero-copy">
          <p class="eyebrow">Women's research education &middot; Research use only</p>
          <h1>One signal says &ldquo;eat.&rdquo; Another says &ldquo;enough.&rdquo; Here are the three compounds researchers study in connection with that <span class="hl">conversation.</span></h1>
          <p class="sub">Ghrelin and GLP-1 are the two chemical messengers behind hunger and fullness.</p>

          <div class="hero-quotes" aria-hidden="false">
            <div class="hero-quote"><span class="sig">Signal A</span><span class="said">&ldquo;Eat.&rdquo;</span></div>
            <div class="hero-quote"><span class="sig">Signal B</span><span class="said">&ldquo;Enough.&rdquo;</span></div>
          </div>

          <p class="hero-sub2">Below, compare the three research compounds being studied in connection with those pathways, then review the documentation yourself before you decide. No essay required first.</p>

          <div class="hero-actions">
            <a class="btn btn-lg btn-primary" href="#compare">Compare the 3 compounds &rarr;</a>
            <a class="btn btn-lg btn-ghost" href="#science">How the signaling works</a>
          </div>
          <p class="micro">Research use only. Not for human consumption. Educational information only.</p>
        </div>

        <div class="hero-visual">
          <div class="hero-frame">
            <img src="{{ $lander->c('hero.image_url') }}" alt="{{ $lander->c('hero.headline') }}" loading="eager" fetchpriority="high" decoding="async" />
            <div class="hero-tag">
              <svg viewBox="0 0 48 48" aria-hidden="true"><path d="M14 24a10 10 0 0 1 20 0M24 6v8M24 34v8M6 24h8M34 24h8"/></svg>
              <span><b>Ghrelin &harr; GLP-1</b><em>the hunger / fullness conversation</em></span>
            </div>
          </div>
        </div>
      </div>
    </section>

    {{-- 2. COMPARE (moved to position 2; primary conversion). Images + /go links held constant from $lander. --}}
    <section id="compare" class="section tint">
      <div class="wrap">
        <div class="sec-head center">
          <p class="eyebrow">Research categories currently being studied</p>
          <h2>Three compounds. One question each: which pathway do researchers study it in connection with?</h2>
        </div>

        <div class="compare-note">
          <span aria-hidden="true">&#9888;</span>
          <span>For laboratory research use only. Not for human consumption. Not approved for human use. Open any card to review specifications and documentation on the research store.</span>
        </div>

        <div class="cards">
          @foreach($products as $i => $p)
          <a class="card" href="{{ $go($p['dest_url'] ?? '#') }}" rel="nofollow noopener">
            <div class="card-top">
              <span class="card-idx" aria-hidden="true">{{ $i + 1 }}</span>
              <span class="card-chip chip chip-signal">Pathway focus</span>
              <img src="{{ $p['image_url'] ?? '' }}" alt="{{ $p['name'] ?? '' }}" loading="lazy" decoding="async" />
            </div>
            <div class="card-body">
              <h3>{{ $p['name'] ?? '' }}</h3>
              <div class="card-label">Researched in connection with</div>
              <div class="card-focus">{{ $cardCopy[$i]['focus'] ?? '' }}</div>
              <p>{{ $cardCopy[$i]['why'] ?? '' }}</p>
              <div class="card-go">
                <span class="btn btn-md btn-ink">Review specs and COA &rarr;</span>
              </div>
            </div>
          </a>
          @endforeach
        </div>
      </div>
    </section>

    {{-- 3. HONESTY / PRINCIPLE trust block (borrowed lever, de-personalized) --}}
    <section id="principle" class="section">
      <div class="wrap">
        <div class="principle-lead">
          <p class="eyebrow">How this page is written</p>
          <h2>Plain language. Source-first. No health claims, on purpose.</h2>
          <p>This page exists to help you compare, not to hype. We do not make health, weight, or medical claims about any compound, because these are research-use-only materials and that is the honest, lawful way to talk about them. What matters more than any marketing line is how a source is documented: testing, batch records, and transparency you can actually check. If a source is vague about what it sells or who it sells to, treat that as the answer.</p>
        </div>
        <div class="principle-rows">
          <div class="prow">
            <div class="pi"><svg viewBox="0 0 24 24"><path d="M4 6h16M4 12h12M4 18h8"/></svg></div>
            <b>Plain language, no hype</b>
            <span>The science explained like a person talking to you, not a brochure.</span>
          </div>
          <div class="prow">
            <div class="pi"><svg viewBox="0 0 24 24"><path d="M12 3l7 3v5c0 4.5-3 7.6-7 9-4-1.4-7-4.5-7-9V6l7-3Z"/><path d="M8.6 12l2.4 2.4L16 9"/></svg></div>
            <b>Claims-free by principle</b>
            <span>Research framing only, every time. No outcomes promised.</span>
          </div>
          <div class="prow">
            <div class="pi"><svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path d="M16.5 16.5 21 21"/></svg></div>
            <b>Source-first thinking</b>
            <span>Documentation matters more than slogans. Always.</span>
          </div>
        </div>
        <a class="inline-cta" href="#compare">Take me back to the compounds &rarr;</a>
      </div>
    </section>

    {{-- 4. SCIENCE (messenger analogy, compressed) + 5. signals grid --}}
    <section id="science" class="section tint">
      <div class="wrap">
        <div class="sec-head">
          <p class="eyebrow">The biology behind hunger and fullness</p>
          <h2>Why hunger is biology, not willpower</h2>
        </div>
        <div class="sci-intro">
          <p class="sci-lead">Think of your stomach as a text messenger. When it runs low it pings the brain: time to send food down here. That message is a real chemical called ghrelin. You did not decide to feel hungry; the body sent the text on its own.</p>
          <p>Once you have eaten, the gut sends a different message using a chemical called GLP-1. That one basically says, okay, we are good up here. It happens automatically too.</p>
          <p>Here is the interesting part. These signals are not identical for everyone. They vary from person to person and even day to day, like two phones on the same network with slightly different timing on when the texts land. That is normal variation, not something gone wrong.</p>
          <p>Ghrelin and GLP-1 are part of a real, measurable conversation inside the body, which is exactly why appetite signaling is such an active research area. The three compounds above are studied in connection with that conversation.</p>
        </div>

        <div class="sci-grid">
          <article class="sci-item">
            <div class="ic"><svg viewBox="0 0 96 96" aria-hidden="true">{!! $icons['science'][0] !!}</svg></div>
            <h3>Hunger signals</h3><p>How gut-to-brain communication relates to hunger cues.</p>
          </article>
          <article class="sci-item">
            <div class="ic"><svg viewBox="0 0 96 96" aria-hidden="true">{!! $icons['science'][1] !!}</svg></div>
            <h3>Satiety response</h3><p>Signals connected to fullness and satisfaction after food intake.</p>
          </article>
          <article class="sci-item">
            <div class="ic"><svg viewBox="0 0 96 96" aria-hidden="true">{!! $icons['science'][2] !!}</svg></div>
            <h3>Metabolic function</h3><p>Pathways involved in nutrient processing and energy regulation.</p>
          </article>
          <article class="sci-item">
            <div class="ic"><svg viewBox="0 0 96 96" aria-hidden="true">{!! $icons['science'][3] !!}</svg></div>
            <h3>Why it varies</h3><p>Age, sleep, stress, and hormonal shifts may influence how these systems are studied.</p>
          </article>
        </div>
      </div>
    </section>

    {{-- 6. Source-quality checklist (5-step), reframed as a buyer tool, with a mid-page CTA --}}
    <section id="framework" class="section">
      <div class="wrap">
        <div class="sec-head center">
          <p class="eyebrow">Use this before you add anything to a cart</p>
          <h2>Five checkpoints to separate a clear research source from a vague one</h2>
        </div>
        <div class="steps">
          <article class="step"><span class="num" aria-hidden="true">01</span><h3>Understand the pathway</h3><p>Start with the mechanism being researched, not social-media hype.</p></article>
          <article class="step"><span class="num" aria-hidden="true">02</span><h3>Compare the compounds</h3><p>Know what each one is studied in connection with, in a lab context (you just did this above).</p></article>
          <article class="step"><span class="num" aria-hidden="true">03</span><h3>Evaluate source quality</h3><p>Look for transparency, testing practices, and consistency.</p></article>
          <article class="step"><span class="num" aria-hidden="true">04</span><h3>Check documentation</h3><p>COAs and batch records should be current and easy to review.</p></article>
          <article class="step"><span class="num" aria-hidden="true">05</span><h3>Decide with clarity</h3><p>Research use only means clarity matters before anything is purchased.</p></article>
        </div>
        <div class="steps-cta">
          <a class="btn btn-lg btn-primary" href="#compare">Compare the 3 compounds &rarr;</a>
        </div>
      </div>
    </section>

    {{-- 7. Source-quality trust strip (5 process icons, kept as-is) --}}
    <section id="source" class="section tint">
      <div class="wrap">
        <div class="sec-head center">
          <p class="eyebrow">Source-quality standards</p>
          <h2>What a clear research source looks like</h2>
        </div>
        <div class="trust">
          <div class="titem"><svg class="ts" viewBox="0 0 64 64" aria-hidden="true">{!! $icons['trust'][0] !!}</svg><h3>Research Use Only</h3><p>Clearly positioned for laboratory research purposes.</p></div>
          <div class="titem"><svg class="ts" viewBox="0 0 64 64" aria-hidden="true">{!! $icons['trust'][1] !!}</svg><h3>Third-Party Tested</h3><p>Testing information and COAs are part of the evaluation process.</p></div>
          <div class="titem"><svg class="ts" viewBox="0 0 64 64" aria-hidden="true">{!! $icons['trust'][2] !!}</svg><h3>Transparent Documentation</h3><p>Researchers can review product and batch documentation before deciding.</p></div>
          <div class="titem"><svg class="ts" viewBox="0 0 64 64" aria-hidden="true">{!! $icons['trust'][3] !!}</svg><h3>Batch Consistency</h3><p>Consistency matters when comparing research sources.</p></div>
          <div class="titem"><svg class="ts" viewBox="0 0 64 64" aria-hidden="true">{!! $icons['trust'][4] !!}</svg><h3>Secure and Discreet</h3><p>Clear ordering experience with responsible research-use framing.</p></div>
        </div>
      </div>
    </section>

    {{-- 9. Final CTA band (dark) repeats the 3 outbound /go links, held constant from $lander --}}
    <section class="section dark">
      <div class="wrap final-grid">
        <div class="lead">
          <p class="eyebrow">Ready to compare research options?</p>
          <h2>Pick the compound you came to review.</h2>
          <p>Skip the overwhelming catalog. Open the research category you wanted to compare, then review its specifications, documentation, and batch testing.</p>
        </div>
        <div class="final-links">
          @foreach($lander->c('final.links', []) as $link)
          <a class="flink" href="{{ $go($link['dest_url'] ?? '#') }}" rel="nofollow noopener">
            <span>{{ $link['label'] ?? '' }}</span>
            <span class="arrow" aria-hidden="true">&rarr;</span>
          </a>
          @endforeach
        </div>
      </div>
    </section>

    {{-- 10. Legal note + footer (held constant from control) --}}
    <div class="legal">
      <div class="wrap">
        <p><strong>Important:</strong> {{ $lander->c('legal') }}</p>
        <p class="links" style="margin-top:14px">
          <a href="{{ route('privacy') }}" style="text-decoration:underline">Privacy</a> &middot;
          <a href="{{ route('terms') }}" style="text-decoration:underline">Terms</a> &middot;
          <a href="{{ route('disclaimer') }}" style="text-decoration:underline">Research-Use Policy</a> &middot; 18+
        </p>
      </div>
    </div>

  </main>

  @if($lander->c('giveaway_popup.enabled'))
    <x-giveaway-popup :lander="$lander" />
  @endif
</body>
</html>
