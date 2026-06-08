@php
    // Each Biolinx product link routes through /go (forwards UTM + fbclid/fbp/fbc to
    // Biolinx for the Purchase CAPI match) and lands on the specific product via ?dest=.
    $go = fn (string $product) => route('outbound.track', 'lp-hunger-fullness') . '?dest=' . urlencode($product);
    $semaglutide = 'https://biolinxlabs.com/products/g1-s-5-mg';
    $tirzepatide = 'https://biolinxlabs.com/products/g2-t-10-mg';
    $retatrutide = 'https://biolinxlabs.com/products/g3-r-10-mg';
@endphp
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  {{-- Paid-ad bridge lander: not for organic indexing (also reduces compliance surface). --}}
  <meta name="robots" content="noindex,nofollow" />
  <title>Research With Confidence | Hunger & Fullness Research</title>
  <meta name="description" content="Educational research-use-only landing page about hunger, fullness, appetite signaling, and source-quality evaluation." />
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=Playfair+Display:wght@700;800;900&display=swap" rel="stylesheet">
  @verbatim<style>
:root{--ink:#071d3a;--ink-2:#223655;--muted:#52617d;--pink:#da3f76;--pink-2:#ffeaf1;--pink-3:#fff5f8;--line:#e9edf4;--soft:#f7f8fb;--white:#fff;--shadow:0 24px 80px rgba(7,29,58,.10);--radius:28px;--radius-sm:16px;--serif:'Playfair Display',Georgia,serif;--sans:Inter,system-ui,-apple-system,Segoe UI,sans-serif}
*{box-sizing:border-box}html{scroll-behavior:smooth}body{margin:0;background:linear-gradient(180deg,#fff 0%,#f7f8fb 100%);color:var(--ink);font-family:var(--sans);font-size:16px;line-height:1.6}.skip-link{position:absolute;left:-999px}.skip-link:focus{left:12px;top:12px;z-index:999;background:#fff;padding:12px;border-radius:10px}.page-shell{width:min(100% - 40px,1280px);margin:0 auto;padding:26px 0 70px}.topbar{position:sticky;top:16px;z-index:50;display:flex;align-items:center;gap:28px;justify-content:space-between;padding:14px 18px;background:rgba(255,255,255,.92);border:1px solid var(--line);border-radius:999px;box-shadow:0 12px 44px rgba(7,29,58,.08);backdrop-filter:blur(14px)}a{text-decoration:none;color:inherit}.brand{display:flex;align-items:center;gap:12px;min-width:290px}.brand-mark{width:40px;height:40px;display:grid;place-items:center;border:2px solid var(--pink);border-radius:14px;color:var(--pink);font-weight:900}.brand-text{display:grid;text-transform:uppercase;letter-spacing:.16em}.brand strong{font-size:12px;line-height:1.1}.brand em{font-style:normal;font-size:9px;color:#718097}.nav-links{display:flex;gap:28px;font-size:14px;font-weight:800;color:#35445f}.nav-links a:hover{color:var(--pink)}.nav-cta,.button,.product-card span,.final-links a{display:inline-flex;align-items:center;justify-content:center;border-radius:12px;font-weight:900}.nav-cta{padding:13px 22px;background:var(--pink);color:white;box-shadow:0 14px 28px rgba(218,63,118,.22)}.section-card{overflow:hidden;margin-top:22px;background:#fff;border:1px solid var(--line);border-radius:var(--radius);box-shadow:var(--shadow)}h1,h2,h3,p{margin:0}h1,h2{font-family:var(--serif);letter-spacing:-.055em;line-height:.96}h1{font-size:clamp(54px,6.6vw,92px)}h2{font-size:clamp(36px,5vw,62px)}h1 span,h2 span{color:var(--pink)}h3{font-size:18px;line-height:1.2}p{color:var(--muted)}.eyebrow{color:var(--pink);font-size:12px;font-weight:900;letter-spacing:.22em;text-transform:uppercase}.hero{display:grid;grid-template-columns:minmax(0,1.02fr) minmax(420px,.98fr);align-items:stretch;min-height:720px}.hero-copy{padding:72px 38px 54px 72px;max-width:720px;align-self:center}.hero-copy h1{max-width:680px}.hero-lede{margin-top:24px;color:var(--ink);font-size:20px;font-weight:900}.hero-copy p:not(.eyebrow):not(.hero-lede):not(.micro-disclaimer){max-width:720px;margin-top:18px;font-size:17px}.hero-actions{display:flex;gap:12px;flex-wrap:wrap;margin-top:30px}.button{min-height:54px;padding:14px 22px}.button.primary{background:var(--ink);color:#fff}.button.ghost{border:1px solid var(--line);background:#fff;color:var(--ink)}.micro-disclaimer{margin-top:18px;color:#7b8396;font-size:12px;font-weight:800}.hero-media{position:relative;min-height:720px;background:linear-gradient(130deg,#fff 0%,#fff5f8 100%);overflow:hidden}.hero-media img{width:100%;height:100%;object-fit:cover;object-position:64% 48%;display:block}.hero-media:before{content:'';position:absolute;inset:0 auto 0 0;width:32%;z-index:1;background:linear-gradient(90deg,#fff 0%,rgba(255,255,255,.86) 34%,rgba(255,255,255,0) 100%);pointer-events:none}.hero-media:after{content:'';position:absolute;inset:auto 0 0;height:22%;z-index:1;background:linear-gradient(180deg,rgba(255,255,255,0),#fff);pointer-events:none}.molecule-badge{position:absolute;left:8%;top:14%;z-index:2;width:190px;height:190px;border-radius:50%;background:rgba(252,224,234,.78);display:grid;place-items:center}.molecule-badge svg{width:118px;height:118px;fill:none;stroke:var(--pink);stroke-width:5;stroke-linecap:round;stroke-linejoin:round}.section-heading{padding:54px 36px 24px}.section-heading.centered{text-align:center;margin:0 auto}.section-heading.narrow{max-width:900px}.section-heading p{max-width:740px;margin:16px auto 0}.science-grid{display:grid;grid-template-columns:repeat(4,1fr);padding:18px 34px 52px}.science-item{text-align:center;padding:18px 24px;border-right:1px solid var(--line)}.science-item:last-child{border-right:0}.line-icon,.trust-svg{fill:none;stroke:var(--pink);stroke-width:4;stroke-linecap:round;stroke-linejoin:round}.line-icon{width:82px;height:82px;margin:0 auto 18px}.science-item p{font-size:14px;max-width:225px;margin:10px auto 0}.pale{background:linear-gradient(180deg,#fff 0%,var(--pink-3) 100%)}.check-grid{display:grid;grid-template-columns:repeat(5,1fr);gap:14px;padding:8px 34px 52px}.check-grid article{background:#fff;border:1px solid #f3d9e3;border-radius:20px;padding:24px 18px;text-align:center}.round-number{display:grid;place-items:center;width:48px;height:48px;margin:0 auto 14px;color:var(--pink);background:#fff4f8;border:1px solid #f2cad8;border-radius:50%;font-family:var(--serif);font-size:28px;font-weight:900}.check-grid p{font-size:13px;margin-top:10px}.product-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:18px;padding:10px 36px 56px}.product-card{display:flex;flex-direction:column;align-items:center;text-align:center;background:#fff;border:1px solid var(--line);border-radius:22px;padding:24px 18px;transition:transform .16s ease,box-shadow .16s ease}.product-card:hover{transform:translateY(-3px);box-shadow:0 18px 44px rgba(8,26,54,.11)}.product-image-wrap{height:180px;width:100%;display:grid;place-items:center;margin-bottom:16px}.product-card img{max-width:100%;max-height:180px;object-fit:contain}.product-card p{font-size:13px;margin-top:10px;max-width:310px}.product-card span{margin-top:auto;padding:10px 16px;color:#fff;background:var(--pink);font-size:13px}.trust-strip{display:grid;grid-template-columns:repeat(5,1fr);padding:30px 18px}.trust-item{text-align:center;padding:12px 22px;border-right:1px solid var(--line)}.trust-item:last-child{border-right:0}.trust-svg{width:54px;height:54px;margin:0 auto 12px}.trust-item h3{font-size:15px}.trust-item p{font-size:12px;margin-top:8px}.navy{display:flex;align-items:center;justify-content:space-between;gap:34px;padding:44px;color:#fff;background:linear-gradient(135deg,#071d3a 0%,#0c2d57 100%)}.navy h2,.navy p{color:#fff}.navy .eyebrow{color:#ff7fac}.navy p:not(.eyebrow){opacity:.84;max-width:720px}.final-links{display:grid;gap:12px;min-width:300px}.final-links a{padding:14px 18px;background:var(--pink);color:white}.legal-note{max-width:980px;margin:22px auto 0;padding:0 16px 18px;color:#6f7788;font-size:12px;line-height:1.65;text-align:center}.sticky-compounds{position:fixed;left:14px;right:14px;bottom:calc(14px + env(safe-area-inset-bottom));z-index:80;display:none;align-items:center;justify-content:center;min-height:50px;padding:13px 18px;border-radius:999px;color:#fff;background:linear-gradient(135deg,var(--ink) 0%,var(--pink) 100%);box-shadow:0 18px 50px rgba(8,26,54,.25);font-weight:900}
@media(max-width:980px){.page-shell{width:min(100% - 24px,760px)}.hero{grid-template-columns:1fr;min-height:auto}.topbar{top:0;margin:0;border-radius:22px;flex-wrap:wrap}.nav-links{display:none}.brand{min-width:auto}.hero-media{order:-1;min-height:410px}.hero-media img{object-position:center center}.hero-media:before{display:none}.hero-copy{padding:34px 28px 38px}.molecule-badge{width:150px;height:150px;left:6%;top:8%}.science-grid{grid-template-columns:repeat(2,minmax(0,1fr));gap:12px;padding:14px 20px 32px}.science-item{border:1px solid var(--line)!important;border-radius:18px;padding:18px 12px;background:#fff}.check-grid{grid-template-columns:repeat(2,minmax(0,1fr));gap:12px;padding:8px 20px 38px}.check-grid article{padding:18px 12px;min-height:172px}.check-grid article:nth-child(5){grid-column:1/-1;min-height:auto}.product-grid{grid-template-columns:repeat(3,minmax(0,1fr));gap:10px;padding-inline:14px}.product-card{padding:14px 8px;border-radius:16px}.product-image-wrap{height:120px}.product-card img{max-height:120px}.product-card h3{font-size:14px}.product-card p{display:none}.product-card span{width:100%;font-size:11px;padding:9px 8px}.trust-strip{grid-template-columns:repeat(2,minmax(0,1fr));gap:12px;padding:18px}.trust-item{border:1px solid var(--line)!important;border-radius:18px;padding:18px 12px}.trust-item:nth-child(5){grid-column:1/-1}.navy{flex-direction:column;align-items:flex-start}.final-links{width:100%;grid-template-columns:repeat(3,1fr)}}
@media(max-width:560px){body{padding-bottom:78px}.page-shell{width:100%;padding-top:0}.topbar{border-radius:0;border-left:0;border-right:0;padding:12px 14px}.brand strong{font-size:11px}.brand em{font-size:7px}.brand-mark{width:34px;height:34px;border-radius:12px}.nav-cta{display:none}.sticky-compounds{display:flex}.section-card{border-radius:0;margin-top:0;border-left:0;border-right:0}h1{font-size:42px}h2{font-size:34px}.hero-media{min-height:340px}.hero-media img{object-position:center center}.hero-copy{padding:30px 22px 34px}.hero-actions{flex-direction:column}.button{width:100%}.section-heading{padding:36px 22px 16px}.science-grid{grid-template-columns:repeat(2,minmax(0,1fr));gap:10px;padding:10px 14px 28px}.line-icon{width:58px;height:58px}.science-item h3{font-size:14px}.science-item p{font-size:11px;line-height:1.45}.check-grid{gap:10px;padding-inline:14px}.check-grid article{min-height:162px}.check-grid h3{font-size:14px}.check-grid p{font-size:11px}.round-number{width:42px;height:42px;font-size:24px}.product-grid{grid-template-columns:repeat(3,minmax(0,1fr));gap:8px}.product-image-wrap{height:88px}.product-card img{max-height:88px}.product-card h3{font-size:12px}.product-card span{font-size:10px}.trust-strip{grid-template-columns:repeat(2,minmax(0,1fr));gap:10px}.trust-svg{width:42px;height:42px}.trust-item h3{font-size:13px}.trust-item p{font-size:10.5px}.navy{padding:34px 22px}.final-links{grid-template-columns:1fr}.legal-note{padding-bottom:28px}}
@media(min-width:981px){.hero{max-height:none}.hero-copy .eyebrow{margin-bottom:12px}.hero-media{border-left:1px solid var(--line)}.hero-actions .button{white-space:nowrap}}
@media(min-width:981px) and (max-width:1180px){.hero{grid-template-columns:1fr .9fr}.hero-copy{padding:60px 28px 48px 52px}h1{font-size:clamp(52px,6vw,78px)}.hero-media{min-height:650px}.molecule-badge{width:160px;height:160px}}
  </style>@endverbatim
  <x-meta-pixel />
</head>
<body>
  <a class="skip-link" href="#main">Skip to content</a>
  <div class="page-shell">
    <header class="topbar" aria-label="Main header">
      <a class="brand" href="#top" aria-label="Research With Confidence home">
        <span class="brand-mark" aria-hidden="true">✦</span>
        <span class="brand-text"><strong>Research With Confidence</strong><em>Peptide Research Education</em></span>
      </a>
      <nav class="nav-links" aria-label="Main navigation">
        <a href="#science">Education</a>
        <a href="#compounds">Research</a>
        <a href="#source">Source Quality</a>
        <a href="#framework">Checklist</a>
      </nav>
      <a class="nav-cta" href="#framework">Get the Guide</a>
    </header>

    <a class="sticky-compounds" href="#compounds">Compare Research Compounds ↓</a>

    <main id="main">
      <section id="top" class="hero section-card">
        <div class="hero-copy">
          <p class="eyebrow">Women's research education</p>
          <h1>Why more women are exploring <span>hunger &amp; fullness research.</span></h1>
          <p class="hero-lede">The science. The pathways. The source-quality checklist.</p>
          <p>After 30, many women become more curious about why hunger signals, cravings, and satisfaction can feel different than before.</p>
          <p>Researchers are studying compounds and biological pathways connected to appetite signaling, satiety response, and metabolic research.</p>
          <div class="hero-actions">
            <a class="button primary" href="#science">Understand the Science</a>
            <a class="button ghost" href="#compounds">View Research Compounds</a>
          </div>
          <p class="micro-disclaimer">Research use only. Not for human consumption. Educational information only.</p>
        </div>
        <div class="hero-media" aria-label="Woman writing in notebook while researching">
          <img src="https://pub-0a9781e86a6b4f2d9b5bfbe22904ad3c.r2.dev/media/ff937fd8-c45b-4f4a-a486-73f22cc0a3e9.png" alt="Woman writing research notes at a desk" loading="eager" />
          <div class="molecule-badge" aria-hidden="true">
            <svg viewBox="0 0 120 120"><circle cx="22" cy="56" r="7"/><circle cx="45" cy="42" r="7"/><circle cx="65" cy="61" r="7"/><circle cx="86" cy="41" r="7"/><circle cx="55" cy="84" r="7"/><circle cx="91" cy="81" r="7"/><path d="M29 53 38 46M51 47 59 56M71 56 81 47M62 68 57 78M72 66 84 76"/></svg>
          </div>
        </div>
      </section>

      <section id="science" class="section-card science-block">
        <div class="section-heading centered">
          <h2>The body science behind hunger &amp; fullness</h2>
          <p>Educational research areas commonly discussed in appetite, satiety, and metabolic pathway research.</p>
        </div>
        <div class="science-grid">
          <article class="science-item">
            <svg class="line-icon" viewBox="0 0 96 96" aria-hidden="true"><path d="M48 18v60M45 19c-5-7-17-5-21 3-7 0-13 6-13 14 0 3 1 6 3 8-5 3-8 8-8 15 0 10 8 18 18 18h21M51 19c5-7 17-5 21 3 7 0 13 6 13 14 0 3-1 6-3 8 5 3 8 8 8 15 0 10-8 18-18 18H51"/><path d="M25 34c5 0 9 3 10 8M22 58c5-1 10 1 13 5M72 34c-5 0-9 3-10 8M75 58c-5-1-10 1-13 5"/></svg>
            <h3>Hunger Signals</h3><p>Researchers study how communication between the gut and brain relates to hunger cues.</p>
          </article>
          <article class="science-item">
            <svg class="line-icon" viewBox="0 0 96 96" aria-hidden="true"><path d="M42 10v23c0 10 16 10 24 22 11 17-1 35-23 35-17 0-32-10-32-27 0-14 12-21 25-29V10h6Z"/><path d="M52 58c-7 5-15 6-23 4"/></svg>
            <h3>Satiety Response</h3><p>Satiety research explores signals connected to fullness and satisfaction after food intake.</p>
          </article>
          <article class="science-item">
            <svg class="line-icon" viewBox="0 0 96 96" aria-hidden="true"><circle cx="32" cy="32" r="14"/><circle cx="64" cy="32" r="14"/><circle cx="32" cy="64" r="14"/><circle cx="64" cy="64" r="14"/><path d="M31 31c4-4 9-3 12 1M63 31c4-4 9-3 12 1M31 63c4-4 9-3 12 1M63 63c4-4 9-3 12 1"/></svg>
            <h3>Metabolic Function</h3><p>Metabolic research looks at pathways involved in nutrient processing and energy regulation.</p>
          </article>
          <article class="science-item">
            <svg class="line-icon" viewBox="0 0 96 96" aria-hidden="true"><path d="M49 88c-21 0-35-13-35-33 0-16 12-28 24-42 0 15 8 20 9 31 9-10 15-24 14-38 18 13 27 31 27 50 0 19-15 32-39 32Z"/><path d="M50 82c10 0 18-7 18-16 0-8-5-14-11-21-2 8-7 14-14 19 0-7-4-10-8-15-5 6-8 11-8 17 0 9 10 16 23 16Z"/></svg>
            <h3>Why It Changes</h3><p>Age, sleep, stress, and hormonal shifts may influence how these systems are studied.</p>
          </article>
        </div>
      </section>

      <section id="framework" class="section-card framework pale">
        <div class="section-heading centered narrow">
          <h2>Before choosing a research source, here's what informed researchers compare.</h2>
          <p>Not all peptide sources are evaluated the same way. These five checkpoints help separate clear, research-focused sources from vague ones.</p>
        </div>
        <div class="check-grid">
          <article><span class="round-number">1</span><h3>Understand the Science</h3><p>Start with the pathway being researched, not hype or social media claims.</p></article>
          <article><span class="round-number">2</span><h3>Compare Compounds</h3><p>Review what each compound is being studied for in a laboratory research context.</p></article>
          <article><span class="round-number">3</span><h3>Evaluate Source Quality</h3><p>Look for source transparency, testing practices, documentation access, and consistency.</p></article>
          <article><span class="round-number">4</span><h3>Check Documentation</h3><p>COAs, batch records, and testing information should be current and easy to review.</p></article>
          <article><span class="round-number">5</span><h3>Make Informed Decisions</h3><p>Research use only means clarity matters before anything is added to a cart.</p></article>
        </div>
      </section>

      <section id="compounds" class="section-card compounds">
        <div class="section-heading centered">
          <p class="eyebrow">Research categories currently being studied</p>
          <h2>Compare research compounds</h2>
          <p>For laboratory research use only. Not for human consumption.</p>
        </div>
        <div class="product-grid">
          <a class="product-card" href="{{ $go($semaglutide) }}" rel="nofollow noopener">
            <div class="product-image-wrap"><img src="https://assets.sticky.io/images/originals/2026-03-04-14-00-00/c9XaACnJmzZUJUY8s5RqxHG3uGWwJbdyIz0YhRSP.jpg" alt="Semaglutide research product" loading="lazy" /></div>
            <h3>Semaglutide Research</h3>
            <p>Researchers are studying pathways related to appetite signaling, satiety response, and metabolic regulation.</p>
            <span>View Research →</span>
          </a>
          <a class="product-card" href="{{ $go($tirzepatide) }}" rel="nofollow noopener">
            <div class="product-image-wrap"><img src="https://assets.sticky.io/images/originals/2026-03-04-14-00-00/1XKIKRV4BcmndAjFy0HRLazYws5lhJ7yVcX35s0j.jpg" alt="Tirzepatide research product" loading="lazy" /></div>
            <h3>Tirzepatide Research</h3>
            <p>Researchers are exploring multiple biological pathways involved in nutrient processing and appetite-related signaling.</p>
            <span>View Research →</span>
          </a>
          <a class="product-card" href="{{ $go($retatrutide) }}" rel="nofollow noopener">
            <div class="product-image-wrap"><img src="https://assets.sticky.io/images/originals/2026-03-04-14-00-00/hdfsWqacCyBE3Y7aFw2MWZLTjxiuuEvXKSh8uXml.jpg" alt="Retatrutide research product" loading="lazy" /></div>
            <h3>Retatrutide Research</h3>
            <p>An emerging area of research involving multiple receptor pathways currently being studied in laboratory settings.</p>
            <span>View Research →</span>
          </a>
        </div>
      </section>

      <section id="source" class="section-card trust-strip" aria-label="Source quality signals">
        <div class="trust-item"><svg class="trust-svg" viewBox="0 0 64 64" aria-hidden="true"><path d="M32 8c13 0 24 11 24 24S45 56 32 56 8 45 8 32 19 8 32 8Z"/><path d="M23 41 41 23"/><circle cx="24" cy="24" r="3"/><circle cx="40" cy="40" r="3"/></svg><h3>Research Use Only</h3><p>Clearly positioned for laboratory research purposes.</p></div>
        <div class="trust-item"><svg class="trust-svg" viewBox="0 0 64 64" aria-hidden="true"><path d="M32 9c13 12 22 25 22 36 0 8-7 14-22 14S10 53 10 45c0-11 9-24 22-36Z"/><path d="M23 38l6 6 13-15"/></svg><h3>Third-Party Tested</h3><p>Testing information and COAs are part of the evaluation process.</p></div>
        <div class="trust-item"><svg class="trust-svg" viewBox="0 0 64 64" aria-hidden="true"><path d="M22 12h20l6 6v34H16V12h6Z"/><path d="M22 29h20M22 39h12M25 47l5 4 10-13"/></svg><h3>Transparent Documentation</h3><p>Researchers can review product and batch documentation before deciding.</p></div>
        <div class="trust-item"><svg class="trust-svg" viewBox="0 0 64 64" aria-hidden="true"><circle cx="32" cy="32" r="24"/><path d="M22 33.5 29 40l14-16"/></svg><h3>Batch Consistency</h3><p>Consistency matters when comparing research sources.</p></div>
        <div class="trust-item"><svg class="trust-svg" viewBox="0 0 64 64" aria-hidden="true"><path d="M32 8 52 17v15c0 13-8 22-20 26C20 54 12 45 12 32V17l20-9Z"/><path d="M23 33l6 6 13-16"/></svg><h3>Secure &amp; Discreet</h3><p>Clear ordering experience with responsible research-use framing.</p></div>
      </section>

      <section class="section-card navy final-cta">
        <div>
          <p class="eyebrow">Ready to compare research options?</p>
          <h2>Choose the compound you want to review.</h2>
          <p>Skip the overwhelming catalog. Start with the research category you came here to compare, then review product details and documentation.</p>
        </div>
        <div class="final-links">
          <a href="{{ $go($semaglutide) }}" rel="nofollow noopener">Semaglutide →</a>
          <a href="{{ $go($tirzepatide) }}" rel="nofollow noopener">Tirzepatide →</a>
          <a href="{{ $go($retatrutide) }}" rel="nofollow noopener">Retatrutide →</a>
        </div>
      </section>

      <p class="legal-note"><strong>Important:</strong> This page is for educational and informational purposes only. Products referenced are intended for laboratory research use only and are not for human consumption, medical use, diagnosis, treatment, or prevention of disease.</p>
    </main>
  </div>
</body>
</html>
