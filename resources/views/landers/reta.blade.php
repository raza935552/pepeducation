<!DOCTYPE html>
<html lang="en">
<head>
@include('partials.gtm-head')
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<meta name="csrf-token" content="{{ csrf_token() }}">
<meta name="robots" content="noindex,nofollow">
<title>Retatrutide: The Triple-Hormone Peptide Redefining Weight Loss | The Metabolic Journal</title>
<meta name="description" content="Ozempic hits one hormone. Retatrutide hits three. What it is, what the trials showed, and how to start." />
<meta property="og:type" content="website">
<meta property="og:title" content="Retatrutide: The Triple-Hormone Peptide Redefining Weight Loss">
<meta property="og:description" content="Ozempic hits one hormone. Retatrutide hits three. What it is, what the trials showed, and how to start.">
<meta property="og:url" content="{{ url()->current() }}">
<meta name="twitter:card" content="summary">
<meta name="twitter:title" content="Retatrutide: The Triple-Hormone Peptide Redefining Weight Loss">
<meta name="twitter:description" content="Ozempic hits one hormone. Retatrutide hits three. What it is, what the trials showed, and how to start.">
<script src="https://cdn.tailwindcss.com"></script>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Source+Serif+4:ital,opsz,wght@0,8..60,400;0,8..60,500;0,8..60,600;0,8..60,700;0,8..60,800;1,8..60,400;1,8..60,500&display=swap" rel="stylesheet">
@verbatim
<style>
:root{--bg:#FAF7F2;--paper:#FFFFFF;--ink:#0F0F0A;--ink2:#1f1f18;--body:#2a2a22;--muted:#6E6E68;--faint:#B5B5AD;--line:#E5E4DD;--lsoft:#EFEEE8;--cta:#C41E1E;--cta-dark:#9A1616;--cta-soft:#FDE8E8;--gold:#B8860B;--gold-soft:#FCF3C8;--green:#1F6F4A;--green-soft:#DDF1E5;--blue:#2E5CBA;--bsoft:#EDF1FA;--bmid:#C4D2F0;--warn-bg:#FFF6D6;--warn-bd:#E2B400}
*{-webkit-font-smoothing:antialiased;box-sizing:border-box;margin:0}
html{background:var(--bg);scroll-behavior:smooth}
body{color:var(--body);font-family:'Source Serif 4',Georgia,serif}
.ui{font-family:'Inter',system-ui,sans-serif}
a{text-decoration:none}img{display:block;max-width:100%}
.progress-bar{position:fixed;top:0;left:0;height:3px;background:var(--cta);width:0;z-index:200}
.top-strip{background:var(--ink);color:#D4D4CC;padding:9px 0;font-size:12px;font-weight:500;letter-spacing:.02em}
.masthead{background:var(--paper);border-bottom:2px solid var(--ink);padding:22px 0 18px;transition:max-height .6s,padding .6s,opacity .4s;overflow:hidden}
.masthead.collapsed{max-height:0;padding:0;opacity:0;border:none;pointer-events:none}
.masthead-title{font-weight:800;font-style:italic;font-size:36px;letter-spacing:-.02em;line-height:1;color:var(--ink)}
@media(min-width:768px){.masthead-title{font-size:48px}.masthead.collapsed{max-height:none;padding:22px 0 18px;opacity:1;border-bottom:2px solid var(--ink);pointer-events:auto}}
.section-nav{background:var(--paper);border-bottom:1px solid var(--line);position:sticky;top:0;z-index:80}
.section-nav a{font-size:12px;font-weight:600;color:var(--muted);padding:14px 0;letter-spacing:.02em;transition:color .15s}
.section-nav a:hover,.section-nav a.active{color:var(--ink)}
.section-nav a.active{box-shadow:inset 0 -2px 0 var(--cta);color:var(--cta)}
.popular-strip{background:var(--paper);border-bottom:1px solid var(--line);padding:18px 0}
@media(max-width:767px){.popular-strip{display:none}}
.popular-strip h4{font-size:10px;font-weight:800;letter-spacing:.16em;text-transform:uppercase;color:var(--muted)}
.popular-num{font-style:italic;font-weight:700;font-size:28px;line-height:1;color:var(--cta);flex-shrink:0;opacity:.5}
.popular-title{font-weight:600;font-size:14px;line-height:1.3;color:var(--ink)}
.popular-title:hover{color:var(--cta)}
main{background:var(--paper);padding:40px 0 80px}
.pre-headline{font-size:12px;font-weight:800;letter-spacing:.14em;text-transform:uppercase;color:var(--cta);display:inline-flex;align-items:center;gap:8px;padding:7px 14px;background:var(--cta-soft);border:1.5px solid var(--cta);border-radius:999px}
.headline{font-weight:700;font-size:36px;line-height:1.06;letter-spacing:-.025em;color:var(--ink);margin:20px 0 0}
@media(min-width:768px){.headline{font-size:58px}}
.subhead{font-style:italic;font-size:20px;line-height:1.4;color:var(--ink2);font-weight:500;margin:20px 0 0}
@media(min-width:768px){.subhead{font-size:23px}}
.byline{display:flex;align-items:center;gap:14px;padding:18px 0;border-top:1px solid var(--line);border-bottom:1px solid var(--line);margin:28px 0;flex-wrap:wrap}
.byline-avatar{width:46px;height:46px;border-radius:999px;background:var(--ink);color:#fff;display:grid;place-items:center;font-weight:700;font-size:14px;flex-shrink:0}
.byline .name{font-weight:700;font-size:14px;color:var(--ink)}
.byline .role{font-size:12.5px;color:var(--muted);margin-top:2px}
.byline .meta{font-size:12.5px;color:var(--muted);display:flex;gap:10px;align-items:center;margin-left:auto}
.byline .dot{width:3px;height:3px;border-radius:999px;background:var(--muted)}
.hero-wrap{position:relative;margin:28px 0}
.hero-image{width:100%;aspect-ratio:16/9;object-fit:cover;border-radius:4px;display:block}
.article-body{font-size:18px;line-height:1.7;color:var(--body)}
.article-body p{margin-bottom:1.4em}
.article-body strong{color:var(--ink);font-weight:700}
.article-body p.lede{font-size:21px;line-height:1.55;color:var(--ink);font-weight:500}
.article-body p.lede strong{font-weight:600}
.article-body p.lede::first-letter{font-weight:800;font-size:68px;line-height:.85;float:left;padding:5px 12px 0 0;color:var(--cta)}
.article-body h2{font-weight:700;font-size:28px;line-height:1.15;color:var(--ink);letter-spacing:-.015em;margin-top:2em;margin-bottom:.5em}
@media(min-width:768px){.article-body h2{font-size:32px}}
.section-rule{border:none;border-top:1px solid var(--line);margin:2.5em 0 0}
.highlight-box{background:var(--gold-soft);border-left:4px solid var(--gold);padding:18px 22px;margin:1.8em 0;font-size:16px;line-height:1.6}
.highlight-box strong{color:var(--ink)}
.pullquote{border-left:4px solid var(--cta);padding:6px 0 6px 24px;margin:2em 0;font-style:italic;font-size:24px;line-height:1.35;color:var(--ink);font-weight:600}
@media(min-width:768px){.pullquote{font-size:27px}}
.figure{margin:2em 0}.figure img{width:100%;border-radius:4px}
.figure-caption{font-size:12px;color:var(--muted);margin-top:8px;font-weight:500}
.stat-strip{display:flex;gap:14px;margin:1.8em 0;flex-wrap:wrap}
.stat-block{flex:1;min-width:100px;background:var(--paper);border:1.5px solid var(--ink);border-radius:6px;padding:16px;text-align:center}
.stat-num{font-weight:800;font-size:26px;color:var(--cta);line-height:1}
.stat-label{font-size:11px;color:var(--muted);margin-top:4px;font-weight:600}
.compare-section{margin:2em 0;border:1.5px solid var(--ink);border-radius:6px;overflow:hidden}
.compare-header{background:var(--ink);color:#fff;padding:14px 20px;display:flex;align-items:center;justify-content:space-between}
.compare-header-title{font-weight:700;font-size:18px}
.compare-header-sub{font-size:11px;color:#999;font-weight:500}
.compare-body{display:grid;grid-template-columns:1fr 1fr}
@media(max-width:520px){.compare-body{grid-template-columns:1fr}}
.compare-col{padding:20px 22px}
.compare-col.old{background:var(--paper);border-right:1px solid var(--line)}
@media(max-width:520px){.compare-col.old{border-right:none;border-bottom:1px solid var(--line)}}
.compare-col.new{background:var(--cta-soft)}
.compare-col-label{font-size:11px;font-weight:800;letter-spacing:.12em;text-transform:uppercase;margin-bottom:14px;padding-bottom:10px;display:flex;align-items:center;gap:6px}
.compare-col.old .compare-col-label{color:var(--muted);border-bottom:1px solid var(--line)}
.compare-col.new .compare-col-label{color:var(--cta);border-bottom:1px solid #F0C4C4}
.compare-col ul{list-style:none;padding:0;margin:0}
.compare-col li{font-size:15px;line-height:1.5;padding:9px 0;border-bottom:1px solid var(--lsoft);display:flex;align-items:flex-start;gap:8px}
.compare-col li:last-child{border-bottom:none}
.compare-col.old li{color:var(--body)}
.compare-col.new li{color:var(--ink);font-weight:600}
.compare-icon{flex-shrink:0;width:22px;height:22px;border-radius:999px;display:grid;place-items:center;font-size:11px;font-weight:800;margin-top:1px}
.compare-col.old .compare-icon{background:var(--bg);color:var(--faint);border:1px solid var(--line)}
.compare-col.new .compare-icon{background:var(--cta);color:#fff}
.editorial-band{background:var(--paper);margin:0 -20px;padding:32px 20px;border-left:4px solid var(--cta);border-top:1px solid var(--line);border-bottom:1px solid var(--line)}
@media(min-width:768px){.editorial-band{margin:0 -40px;padding:36px 40px}}
.editorial-band h2{margin-top:0}
.key-insight{background:var(--gold-soft);border-left:4px solid var(--gold);padding:18px 22px;margin:1.8em 0;font-size:15.5px;line-height:1.6}
.key-insight strong{color:var(--ink)}
.key-insight .label{font-size:10px;font-weight:800;letter-spacing:.14em;text-transform:uppercase;color:var(--gold);margin-bottom:6px;display:block}
.carousel-section{background:var(--bg);border-top:1.5px solid var(--ink);border-bottom:1.5px solid var(--ink);margin:2em -20px;padding:28px 0;position:relative;overflow:hidden}
@media(min-width:768px){.carousel-section{margin:2em -40px;padding:32px 0}}
.carousel-section-title{font-size:11px;font-weight:800;letter-spacing:.14em;text-transform:uppercase;color:var(--cta);padding:0 20px;margin-bottom:16px}
.carousel-track{display:flex;gap:14px;animation:scroll-carousel 28s linear infinite;width:max-content;padding:0 20px}
.carousel-track:hover{animation-play-state:paused}
@keyframes scroll-carousel{0%{transform:translateX(0)}100%{transform:translateX(-50%)}}
.carousel-slide{flex:0 0 300px;background:var(--paper);border:1px solid var(--line);border-radius:6px;overflow:hidden;display:flex;flex-direction:column}
.carousel-img{width:100%;aspect-ratio:1/1;object-fit:cover}
.carousel-body{padding:16px;display:flex;flex-direction:column;gap:6px;flex:1}
.carousel-name{font-weight:700;font-size:14px;color:var(--ink);line-height:1.2}
.carousel-meta{font-size:11px;color:var(--muted)}
.carousel-quote{font-size:14px;line-height:1.55;font-style:italic;color:var(--body);flex:1;margin-top:4px}
.carousel-slide{border-top:3px solid var(--cta)}
.carousel-body::before{content:'\201C';font-family:Georgia,serif;font-size:40px;line-height:.1;color:var(--cta);opacity:.55;display:block;height:22px;margin-top:2px}
.inline-cta{background:var(--ink);color:#fff;padding:28px;margin:2.5em 0;border-radius:6px;text-align:center}
.inline-cta h3{font-weight:700;font-size:26px;margin-bottom:8px;line-height:1.2}
@media(min-width:768px){.inline-cta h3{font-size:30px}}
.inline-cta p{font-size:14.5px;opacity:.85;margin-bottom:18px;max-width:460px;margin-left:auto;margin-right:auto}
.cta-button{display:inline-block;background:var(--cta);color:#fff;padding:16px 32px;border-radius:6px;font-size:15px;font-weight:800;letter-spacing:.02em;text-transform:uppercase;box-shadow:0 5px 0 var(--cta-dark),0 8px 20px rgba(196,30,30,.3);transition:transform .08s,box-shadow .08s}
.cta-button:hover{transform:translateY(2px);box-shadow:0 3px 0 var(--cta-dark),0 6px 14px rgba(196,30,30,.35)}
.cta-trust{font-size:12px;opacity:.7;margin-top:14px;display:flex;gap:18px;justify-content:center;flex-wrap:wrap}
.cta-trust span::before{content:'✓ ';color:#6CD297;font-weight:700}
.value-stack{background:var(--paper);border:2px solid var(--ink);border-radius:6px;padding:26px;margin:2.5em 0}
@media(min-width:768px){.value-stack{padding:28px 32px}}
.value-stack-title{font-size:12px;font-weight:800;letter-spacing:.16em;text-transform:uppercase;color:var(--cta);margin-bottom:6px}
.value-stack-h{font-weight:700;font-size:24px;line-height:1.2;color:var(--ink);margin-bottom:18px}
@media(min-width:768px){.value-stack-h{font-size:26px}}
.value-row{display:flex;justify-content:space-between;align-items:center;padding:12px 0;border-top:1px dashed var(--line);gap:12px}
.value-row:first-of-type{border-top:none}
.value-row .label{font-size:14.5px;color:var(--ink);font-weight:600;flex:1;padding-right:10px}
.value-row .label small{display:block;font-weight:400;color:var(--muted);font-size:12.5px;margin-top:2px}
.value-row .price{font-size:14px;color:var(--muted);font-weight:700;text-decoration:line-through;min-width:70px;text-align:right}
.value-total{display:flex;justify-content:space-between;align-items:center;padding-top:16px;margin-top:8px;border-top:2px solid var(--ink)}
.value-total .label{font-size:14px;font-weight:800;letter-spacing:.06em;text-transform:uppercase;color:var(--ink)}
.value-total .price{font-size:22px;font-weight:800;color:var(--ink)}
.value-today{display:flex;justify-content:space-between;align-items:center;padding:14px 16px;margin-top:12px;background:var(--cta-soft);border:1.5px solid var(--cta);border-radius:6px}
.value-today .label{font-size:14px;font-weight:800;color:var(--cta)}
.value-today .price{font-size:26px;font-weight:900;color:var(--cta)}
.value-cta-wrap{text-align:center;margin-top:22px}
.product-shelf{margin:2.5em 0;padding-top:2em;border-top:1px solid var(--line)}
.product-shelf-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:16px}
.product-shelf-title{font-weight:700;font-size:20px;color:var(--ink);letter-spacing:-.01em}
.product-shelf-sub{font-size:12px;color:var(--muted);margin-top:2px}
.product-shelf-track-wrap{overflow:hidden}
.product-shelf-track{display:flex;gap:12px;animation:scroll-shelf 22s linear infinite;width:max-content}
.product-shelf-track:hover{animation-play-state:paused}
@keyframes scroll-shelf{0%{transform:translateX(0)}100%{transform:translateX(-50%)}}
.product-card{flex:0 0 200px;background:var(--paper);border:1px solid var(--line);border-radius:6px;padding:14px;text-align:center;transition:border-color .15s}
.product-card:hover{border-color:var(--cta)}
@media(max-width:520px){.product-card{flex:0 0 164px;padding:12px}}
.product-card img{width:100%;aspect-ratio:1/1;object-fit:contain;border-radius:4px;margin-bottom:10px;background:var(--bg)}
.product-card-name{font-size:13px;font-weight:600;color:var(--ink);line-height:1.3;margin-bottom:3px}
.product-card-price{font-size:12px;color:var(--muted);margin-bottom:8px}
.product-card-cta{display:block;padding:9px 10px;background:var(--cta);color:#fff;border-radius:6px;font-size:11px;font-weight:700;letter-spacing:.02em;text-transform:uppercase;box-shadow:0 3px 0 var(--cta-dark);transition:transform .08s,box-shadow .08s}
.product-card-cta:hover{transform:translateY(1px);box-shadow:0 2px 0 var(--cta-dark)}
.product-card.featured{border:2px solid var(--cta);position:relative}
.product-card.featured .product-card-price{color:var(--ink);font-weight:700;font-size:14px}
.product-card.featured::after{content:'MOST POPULAR';position:absolute;top:-1px;left:50%;transform:translateX(-50%);background:var(--cta);color:#fff;font-size:8px;font-weight:800;letter-spacing:.12em;padding:3px 10px;border-radius:0 0 4px 4px}
.guarantee{display:flex;gap:18px;align-items:center;background:var(--green-soft);border:1.5px solid var(--green);border-radius:8px;padding:22px;margin:2.2em 0}
@media(max-width:520px){.guarantee{flex-direction:column;text-align:center}}
.guarantee-badge{width:88px;height:88px;border-radius:999px;background:var(--green);color:#fff;display:grid;place-items:center;flex-shrink:0;text-align:center;line-height:1.05;font-weight:800;padding:8px}
.guarantee-badge .num{font-size:26px}.guarantee-badge .lbl{font-size:9px;letter-spacing:.1em;text-transform:uppercase;font-weight:700}
.guarantee h3{font-weight:700;font-size:19px;color:var(--ink);margin-bottom:4px}
.guarantee p{font-size:14px;color:var(--body);line-height:1.5;margin:0}
.faq{margin:2.5em 0}
.faq-item{border:1px solid var(--line);border-radius:6px;margin-bottom:10px;overflow:hidden;background:var(--paper)}
.faq-q{width:100%;text-align:left;padding:16px 20px;background:var(--paper);border:none;cursor:pointer;font-weight:700;font-size:15.5px;color:var(--ink);display:flex;justify-content:space-between;align-items:center;gap:14px;transition:background .12s}
.faq-q:hover{background:var(--bg)}
.faq-q .icon{width:26px;height:26px;background:var(--ink);color:#fff;border-radius:999px;display:grid;place-items:center;font-size:16px;font-weight:800;flex-shrink:0;transition:transform .18s,background .18s}
.faq-item.open .faq-q .icon{transform:rotate(45deg);background:var(--cta)}
.faq-a{padding:0 20px;max-height:0;overflow:hidden;transition:max-height .25s,padding .25s;font-size:16px;line-height:1.6;color:var(--body)}
.faq-item.open .faq-a{padding:4px 20px 18px;max-height:600px}
.ps-block{background:var(--warn-bg);border-left:4px solid var(--warn-bd);padding:18px 22px;margin:1.6em 0;font-size:16px;line-height:1.55}
.ps-block strong{color:var(--ink);font-weight:800}
.sidebar-box{background:var(--paper);padding:22px;border:1px solid var(--line);margin-bottom:22px;border-radius:6px}
.sidebar-label{font-size:11px;font-weight:800;letter-spacing:.14em;text-transform:uppercase;color:var(--muted);margin-bottom:14px;padding-bottom:10px;border-bottom:2px solid var(--ink)}
.sidebar-box h3{font-size:18px;font-weight:700;margin-bottom:12px;color:var(--ink);line-height:1.25}
.sidebar-box p{font-size:14px;line-height:1.6;margin-bottom:12px;color:var(--body)}
.sidebar-cta{display:block;background:var(--cta);color:#fff;padding:14px;text-align:center;border-radius:6px;font-weight:800;font-size:14px;letter-spacing:.02em;text-transform:uppercase;margin-bottom:12px;box-shadow:0 4px 0 var(--cta-dark);transition:transform .08s}
.sidebar-cta:hover{transform:translateY(1px);box-shadow:0 3px 0 var(--cta-dark)}
footer{background:var(--ink);color:#fff;padding:40px 0}
footer .container{max-width:1100px;margin:0 auto;padding:0 20px}
.footer-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:36px;margin-bottom:32px}
footer h4{font-size:14px;font-weight:800;letter-spacing:.08em;text-transform:uppercase;margin-bottom:12px}
footer p{font-size:12.5px;margin-bottom:12px;opacity:.9;line-height:1.5}
footer a{color:#fff;opacity:.8}footer a:hover{opacity:1}
.footer-bottom{border-top:1px solid rgba(255,255,255,.12);padding-top:18px;margin-top:32px;text-align:center;font-size:11px;opacity:.6}
.sticky-cta{position:fixed;left:12px;right:12px;bottom:12px;background:var(--cta);color:#fff;padding:14px 18px;text-align:center;border-radius:6px;font-weight:800;font-size:15px;letter-spacing:.02em;text-transform:uppercase;z-index:50;display:none;box-shadow:0 6px 0 var(--cta-dark),0 12px 24px rgba(196,30,30,.4);cursor:pointer}
.sticky-cta.show{display:block}
.sticky-cta:hover{transform:translateY(2px);box-shadow:0 4px 0 var(--cta-dark),0 8px 16px rgba(196,30,30,.4)}
.email-form{display:flex;gap:6px}
.email-form input{flex:1;padding:10px 12px;border:1px solid var(--line);border-radius:4px;font-size:13px}
.email-form button{padding:10px 18px;background:var(--ink);color:#fff;border:none;border-radius:4px;font-size:13px;font-weight:600;cursor:pointer}
.email-form button:hover{background:#333}
.pp-capture-success{margin-top:6px}
.pp-capture-success p{font-size:14px;font-weight:600;margin-bottom:12px}
.pp-modal-overlay{position:fixed;inset:0;z-index:1000;display:flex;align-items:center;justify-content:center;padding:20px;background:rgba(15,15,10,.6);backdrop-filter:blur(3px);opacity:0;visibility:hidden;transition:opacity .2s ease,visibility .2s ease}
.pp-modal-overlay.is-open{opacity:1;visibility:visible}
body.pp-modal-open{overflow:hidden}
.pp-modal{position:relative;width:100%;max-width:440px;max-height:88vh;overflow-y:auto;background:var(--paper);border-radius:10px;border-top:5px solid var(--cta);padding:34px 28px 28px;text-align:center;box-shadow:0 30px 70px rgba(10,10,5,.4);transform:translateY(14px) scale(.98);transition:transform .2s ease}
.pp-modal-overlay.is-open .pp-modal{transform:translateY(0) scale(1)}
.pp-modal-close{position:absolute;top:10px;right:12px;width:32px;height:32px;border:none;background:none;color:var(--muted);font-size:24px;font-weight:700;cursor:pointer;line-height:1}
.pp-modal-close:hover{color:var(--ink)}
.pp-modal-kicker{font-size:11px;font-weight:800;letter-spacing:.16em;text-transform:uppercase;color:var(--cta);margin-bottom:10px}
.pp-modal h3{font-family:'Source Serif 4',Georgia,serif;font-weight:700;font-size:24px;line-height:1.2;color:var(--ink);margin-bottom:10px}
.pp-modal>p,.pp-modal .pp-modal-body p{font-size:14.5px;line-height:1.55;color:var(--body);margin-bottom:16px}
.pp-modal form{display:flex;flex-direction:column;gap:8px}
.pp-modal input[type=email]{padding:14px 16px;border:1.5px solid var(--line);border-radius:6px;font-size:15px;width:100%}
.pp-modal button[type=submit]{padding:14px 18px;background:var(--cta);color:#fff;border:none;border-radius:6px;font-size:14px;font-weight:800;letter-spacing:.02em;text-transform:uppercase;cursor:pointer;box-shadow:0 4px 0 var(--cta-dark)}
.pp-modal button[type=submit]:hover{transform:translateY(1px);box-shadow:0 3px 0 var(--cta-dark)}
.pp-modal-trust{font-size:11.5px;color:var(--muted);margin-top:14px}
.goal-pill{display:flex;align-items:center;justify-content:space-between;gap:10px;width:100%;padding:14px 18px;border-radius:8px;border:1.5px solid var(--cta);background:var(--cta-soft);color:var(--cta);font-family:'Inter',sans-serif;font-weight:800;font-size:14.5px;text-align:left;cursor:pointer;transition:background .15s,color .15s,transform .1s,box-shadow .15s}
.goal-pill::after{content:'→';font-weight:800;color:var(--cta);transition:transform .15s,color .15s}
.goal-pill:hover{background:#FBD5D5;transform:translateY(-1px);box-shadow:0 5px 14px rgba(196,30,30,.16)}
.goal-pill:hover::after{transform:translateX(3px)}
.goal-pill.active{background:var(--cta);border-color:var(--cta);color:#fff;box-shadow:0 4px 0 var(--cta-dark),0 8px 18px rgba(196,30,30,.28);transform:none}
.goal-pill.active::after{content:'✓';color:#fff;transform:none}
.ms-form{margin:2.5em 0;padding:1.9em 1.6em;border:1px solid var(--line);border-radius:10px;background:var(--paper);scroll-margin-top:64px}
.ms-progress{display:flex;gap:8px;justify-content:center;margin-bottom:22px}
.ms-dot{width:40px;height:5px;border-radius:99px;background:var(--line);transition:background .2s}
.ms-dot.is-active{background:var(--cta)}
.ms-h{font-weight:700;font-size:21px;color:var(--ink);letter-spacing:-.01em;text-align:center;line-height:1.2}
.ms-sub{font-size:13px;color:var(--muted);text-align:center;margin:7px auto 22px;line-height:1.5;max-width:46ch}
.ms-options{display:flex;flex-direction:column;gap:10px;max-width:420px;margin:0 auto}
#msKits{max-width:480px;margin:0 auto}
.stack-result-card{display:none;gap:18px;align-items:center;padding:16px;border:1.5px solid var(--cta);border-radius:8px;background:var(--cta-soft)}
.stack-result-card.is-active{display:flex}
.stack-result-card img{width:92px;height:92px;object-fit:contain;border-radius:6px;background:var(--bg);flex:0 0 92px}
.stack-result-card .product-card-name{font-size:15px;line-height:1.35;margin-bottom:6px}
.stack-result-card .product-card-price{font-size:16px;font-weight:700;color:var(--ink);margin-bottom:12px}
.ms-nav{display:flex;justify-content:space-between;align-items:center;gap:12px;margin:20px auto 0;max-width:480px}
.ms-back{background:none;border:none;color:var(--muted);font-weight:600;cursor:pointer;font-size:14px;padding:8px}
.ms-back:hover{color:var(--ink)}
.ms-next{background:var(--cta);color:#fff;border:none;border-radius:6px;padding:13px 26px;font-weight:800;text-transform:uppercase;font-size:13px;letter-spacing:.03em;cursor:pointer;box-shadow:0 4px 0 var(--cta-dark);transition:transform .08s}
.ms-next:hover{transform:translateY(1px);box-shadow:0 3px 0 var(--cta-dark)}
.mobile-avail{display:none}
@media(max-width:767px){.mobile-avail{display:block;margin:2em 0 0}}
@media(min-width:768px){.stack-result-card{margin:0 auto}}
</style>
@endverbatim
<x-meta-pixel />
<x-posthog-lander />
</head>
<body>
@include('partials.gtm-body')
<div class="progress-bar" id="progress"></div>

<div class="top-strip ui"><div class="max-w-7xl mx-auto px-5 md:px-8 flex items-center justify-between"><div class="flex items-center gap-3"><span style="color:#FFD66B">●</span><span>Metabolic Health Report · January 2026</span></div><div class="hidden md:flex items-center gap-3 opacity-80"><span>11,400+ readers · No paywall</span></div></div></div>

<div class="masthead" id="masthead">
<div style="background:var(--cta);height:3px"></div>
<div class="max-w-7xl mx-auto px-5 md:px-8 flex items-center justify-between" style="padding-top:16px">
<div><div class="ui" style="font-size:9px;font-weight:800;letter-spacing:.2em;text-transform:uppercase;color:var(--cta);margin-bottom:3px">Independent Reporting</div><a href="/" class="masthead-title">The Metabolic Journal</a></div>
</div></div>

<div class="section-nav ui"><div class="max-w-7xl mx-auto px-5 md:px-8 flex items-center gap-7 overflow-x-auto whitespace-nowrap"><a href="#stackQuiz">Weight</a><a href="#stackQuiz">Metabolism</a><a href="#stackQuiz" class="active">Retatrutide</a><a href="#protocol">How-To</a><a href="#stackQuiz">Results</a></div></div>

<div class="popular-strip"><div class="max-w-7xl mx-auto px-5 md:px-8"><h4 class="ui mb-4">Most Read This Week</h4><div class="grid grid-cols-1 md:grid-cols-4 gap-4"><a href="#stackQuiz" class="flex gap-3 items-start"><span class="popular-num">1</span><div><div class="popular-title ui">The Triple-Hormone Peptide Redefining Weight Loss</div><div class="ui text-[11px] text-[var(--muted)] mt-1">Weight · 9 min</div></div></a><a href="#stackQuiz" class="flex gap-3 items-start"><span class="popular-num">2</span><div><div class="popular-title ui">Why One Hormone Was Never Enough</div><div class="ui text-[11px] text-[var(--muted)] mt-1">Analysis · 6 min</div></div></a><a href="#stackQuiz" class="flex gap-3 items-start"><span class="popular-num">3</span><div><div class="popular-title ui">GLP-1, GIP, Glucagon: What Retatrutide Actually Hits</div><div class="ui text-[11px] text-[var(--muted)] mt-1">Guide · 8 min</div></div></a><a href="#stackQuiz" class="flex gap-3 items-start"><span class="popular-num">4</span><div><div class="popular-title ui">How To Start Retatrutide: Step-by-Step</div><div class="ui text-[11px] text-[var(--muted)] mt-1">How-To · 7 min</div></div></a></div></div></div>

<main><div class="max-w-7xl mx-auto px-5 md:px-8"><div class="grid grid-cols-1 md:grid-cols-3 gap-10">
<div class="md:col-span-2"><div class="max-w-2xl">

<span class="pre-headline ui">⚠ Field Report · Metabolic Health &amp; Weight</span>
<h1 class="headline">The Triple-Hormone Peptide Quietly Redefining What's Possible for Weight Loss</h1>
<p class="subhead">Ozempic and Wegovy hit one hormone. Retatrutide hits three at once. Here's what it actually is, what the trials showed, and how to start.</p>

<div class="byline ui"><div class="byline-avatar">MF</div><div class="flex-1"><div class="name">Dr. Marcus Feld · Metabolic Science Editor</div><div class="role">11 yrs covering metabolic &amp; incretin research · 180+ practitioner interviews</div></div><div class="meta"><span>Updated Jan 2026</span><span class="dot"></span><span>9 min read</span><span class="dot"></span><span style="color:var(--green);font-weight:600">✓ Fact-checked</span></div></div>

<div class="hero-wrap"><img src="https://pub-0a9781e86a6b4f2d9b5bfbe22904ad3c.r2.dev/media/ed358dc5-44ed-45ec-a390-feaddc1981ce.jpg" alt="Radiant skin" class="hero-image"></div>

<div class="article-body">
<p class="lede"><strong>Most weight-loss drugs pull one lever. Retatrutide pulls three at once, and in its human trials that difference showed up on the scale in a way the single-hormone drugs never matched.</strong></p>
<p><strong>Retatrutide is a triple-hormone peptide.</strong></p>
<p>It activates three of the body's own metabolic receptors at the same time: <strong>GLP-1, GIP, and glucagon.</strong> Ozempic and Wegovy hit just one. That's the reason people are quietly rethinking what's actually possible.</p>

<div class="highlight-box"><strong>The headline number:</strong> in a phase 2 trial, adults on the highest dose lost about <strong>24% of their body weight in 48 weeks</strong>, with the curve still heading down. That is the most weight loss reported for any drug in this class.</div>

<hr class="section-rule">
<h2>The Problem Everyone Already Knows</h2>
<p>Diets fail because hunger wins. Willpower is no match for the hormones that tell your brain to eat. That is biology, not weakness.</p>
<p>The first GLP-1 drugs changed the game by quieting that hunger signal. But they hit a single receptor, and for many people the results plateau. <strong>Retatrutide was built to go further.</strong></p>
<p>By hitting three metabolic receptors instead of one, it works on appetite and on how the body burns energy at the same time.</p>

<div class="figure"><img src="https://assets.sticky.io/images/originals/2026-03-04-14-00-00/hdfsWqacCyBE3Y7aFw2MWZLTjxiuuEvXKSh8uXml.jpg" alt="Retatrutide (G3-R) vial" style="aspect-ratio:16/9;object-fit:contain;background:var(--bg)"><p class="figure-caption ui">Retatrutide, sold as G3-R. A GLP-1 / GIP / glucagon triple agonist.</p></div>

<hr class="section-rule">
<h2>What Retatrutide Actually Is</h2>
<p>Your gut releases hormones after you eat that tell your brain you are full and tell your body how to handle energy. Retatrutide is a peptide that mimics three of them at once:</p>

<h2 style="font-size:22px;margin-top:1.2em">1. GLP-1 — the fullness signal</h2>
<p>This is the same receptor Ozempic and Wegovy target. It <strong>slows the stomach and quiets appetite</strong>, so you feel full sooner and stay full longer.</p>

<h2 style="font-size:22px;margin-top:1.2em">2. GIP — the metabolism partner</h2>
<p>GIP works alongside GLP-1 to <strong>improve how the body handles food and blood sugar</strong>. Adding it is what took Tirzepatide past Semaglutide. Retatrutide keeps it.</p>

<h2 style="font-size:22px;margin-top:1.2em">3. Glucagon — the energy-burn lever</h2>
<p>The third receptor is the new one. Glucagon activation <strong>nudges the body to burn stored energy</strong>, not just eat less. That third lever is why Retatrutide stands apart from every drug before it.</p>

<div class="stat-strip ui">
<div class="stat-block"><div class="stat-num">3</div><div class="stat-label">Hormone receptors, one shot</div></div>
<div class="stat-block"><div class="stat-num">~24%</div><div class="stat-label">Body weight lost in 48 wks (trial)</div></div>
<div class="stat-block"><div class="stat-num">1x/wk</div><div class="stat-label">A single weekly injection</div></div>
</div>

<hr class="section-rule">
<h2>One Hormone vs Three: The Critical Difference</h2>
<p>The first-generation drugs quiet hunger through a single receptor. That works, until the body adapts and the loss plateaus.</p>
<p><strong>Retatrutide works differently.</strong> Three receptors, working together on appetite and energy burn, is why its trial numbers went where no single-hormone drug had gone.</p>

<div class="pullquote">"Ozempic pulls one lever. Retatrutide pulls three at the same time. That is the whole story on the scale."</div>

<div class="compare-section ui">
<div class="compare-header"><div class="compare-header-title" style="font-family:'Source Serif 4',serif">How It Compares</div><div class="compare-header-sub">Side-by-side breakdown</div></div>
<div class="compare-body">
<div class="compare-col old"><div class="compare-col-label">Single-Hormone GLP-1 💉</div><ul><li><span class="compare-icon">✕</span>Hits one receptor</li><li><span class="compare-icon">✕</span>Appetite only</li><li><span class="compare-icon">✕</span>Results often plateau</li></ul></div>
<div class="compare-col new"><div class="compare-col-label">Retatrutide 🧬</div><ul><li><span class="compare-icon">✓</span>Hits three receptors</li><li><span class="compare-icon">✓</span>Appetite + energy burn</li><li><span class="compare-icon">✓</span>Highest loss in its class (trial)</li></ul></div>
</div></div>

<hr class="section-rule">
</div><!-- close article-body for editorial band -->

<div class="editorial-band">
<div class="article-body" style="max-width:680px">
<h2>Why You Haven't Heard About It</h2>
<p>Retatrutide isn't sold at the pharmacy yet. <strong>Most people only hear about it through metabolic clinics or word of mouth.</strong></p>
<p>Some research labs legally sell the same compounds under a "For Research Use Only" label. Not every lab holds the same quality bar. <strong>That's why verified testing matters.</strong></p>

<div class="key-insight">
<span class="label ui">Key Sourcing Rule</span>
<strong>Trusted facilities provide a Certificate of Analysis (COA)</strong> confirming purity and sterility for every batch. If a supplier can't show a recent batch-specific COA, walk away.
</div>

<p>That's why we only source from vetted research facilities such as <strong>BioLinx Labs</strong>.</p>
</div></div>

<div class="article-body">

<hr class="section-rule">
<h2>What The Research Actually Shows</h2>
<p>This is the real, published human-trial finding behind Retatrutide. <strong>Research use only, not a promise of results:</strong></p>
<div class="key-insight">
<span class="label ui">The Evidence</span>
In a phase 2 human trial, adults with obesity on the highest retatrutide dose lost about <strong>24% of their body weight over 48 weeks</strong>, and the weight was still dropping at the end of the study (Jastreboff et al., New England Journal of Medicine, 2023). That is the largest weight loss reported for a drug in this class to date. Larger phase 3 trials are ongoing; it is not yet FDA-approved.
</div>
</div>

<div class="carousel-section"><div class="carousel-section-title ui">Real Stories · Verified Users</div><div class="carousel-track">
<div class="carousel-slide"><div class="carousel-body ui"><div><div class="carousel-name">Rachel, 43</div><div class="carousel-meta">Verified user · 3 months in</div></div><p class="carousel-quote">"Two GLP-1s stalled me for a year. Three months on this and the scale is finally moving again. The hunger just... quiets down."</p></div></div>
<div class="carousel-slide"><div class="carousel-body ui"><div><div class="carousel-name">Mark, 51</div><div class="carousel-meta">Verified user · recovery focus</div></div><p class="carousel-quote">"The food noise is gone. I used to think about snacks all day. Now I forget to eat lunch. That is the part nothing else touched."</p></div></div>
<div class="carousel-slide"><div class="carousel-body ui"><div><div class="carousel-name">Angela, 49</div><div class="carousel-meta">Verified user · bloodwork improved</div></div><p class="carousel-quote">"Down two belt notches. My bloodwork looked better at my last check-up than it has in a decade."</p></div></div>
<div class="carousel-slide"><div class="carousel-body ui"><div><div class="carousel-name">Chris, 45</div><div class="carousel-meta">Verified user · once a week</div></div><p class="carousel-quote">"One small shot a week. That is the whole routine. I reconstitute it once and I am set for the month."</p></div></div>
<div class="carousel-slide"><div class="carousel-body ui"><div><div class="carousel-name">Rachel, 43</div><div class="carousel-meta">Verified user · 3 months in</div></div><p class="carousel-quote">"Three months on this and the scale is finally moving again after a year stuck."</p></div></div>
<div class="carousel-slide"><div class="carousel-body ui"><div><div class="carousel-name">Mark, 51</div><div class="carousel-meta">Verified user · recovery focus</div></div><p class="carousel-quote">"The food noise is gone. That is the part nothing else touched."</p></div></div>
</div></div>

<div class="article-body">
<div class="inline-cta"><h3>Ready to see exactly how to start?</h3><p>Get the full Retatrutide sourcing guide, reconstitution walkthrough, titration schedule and side-effect protocol. Direct to your inbox in 60 seconds.</p><a href="#protocol" class="cta-button ui">Get The Full Protocol Free →</a><div class="cta-trust ui"><span>No spam, ever</span><span>Read in 6 minutes</span><span>11,400+ readers</span></div></div>

<hr class="section-rule">
<h2>How To Start (The Right Way)</h2>
<p>Retatrutide arrives as a powder. You mix it once with bacteriostatic water (the guide shows the exact ratio), keep it in the fridge, and take one small weekly shot with a tiny insulin needle. <strong>Most people don't even feel it.</strong> You start low and step the dose up slowly, which is the whole point of the titration schedule in the guide.</p>

<div class="key-insight">
<span class="label ui">What You Need To Know</span>
<strong>You don't need a prescription.</strong> The full guide walks you through everything: what to buy, how to reconstitute it, the exact week-by-week titration, and what is normal as you step up.
</div>

<h2 id="protocol" style="scroll-margin-top:64px;text-align:center;font-family:'Source Serif 4',serif;font-weight:700;font-size:clamp(28px,5.5vw,40px);line-height:1.08;letter-spacing:-.015em;color:var(--ink);margin:2.4em 0 .6em">Get the Protocol - <span style="color:var(--green)">FREE</span></h2>
<div class="value-stack ui">
<div class="value-stack-title">The Complete Retatrutide Starter System</div>
<div class="value-stack-h">Everything you need to start safely, in one guide</div>
<div class="value-row"><div class="label">The Retatrutide Sourcing Guide<small>How to verify any supplier in 90 seconds</small></div><div class="price">$49 value</div></div>
<div class="value-row"><div class="label">Reconstitution &amp; Injection Walkthrough<small>Step-by-step, beginner-friendly, with the exact ratio</small></div><div class="price">$59 value</div></div>
<div class="value-row"><div class="label">Week-by-Week Titration Schedule (PDF)<small>The exact dose ramp, so you never step up too fast</small></div><div class="price">$39 value</div></div>
<div class="value-row"><div class="label">Side-Effect Playbook<small>Nausea, what's normal, what's not, when to call a doctor</small></div><div class="price">$29 value</div></div>
<div class="value-row"><div class="label" style="color:var(--cta)"><strong>BONUS: GLP-1 Comparison Guide</strong><small>Retatrutide vs Tirzepatide vs Semaglutide, side by side · usually $89 standalone</small></div><div class="price">$89 value</div></div>
<div class="value-total"><div class="label">Total Value</div><div class="price">$265</div></div>
<div class="value-today"><div class="label">Your Price Today</div><div class="price">FREE*</div></div>
<div class="value-cta-wrap"><a href="#" onclick="pmOpen();return false;" class="cta-button ui">Send Me The Complete Protocol →</a><div class="cta-trust ui" style="color:var(--muted);opacity:1"><span>Instant email delivery</span><span>No credit card required</span><span>Unsubscribe anytime</span></div><p class="ui" style="font-size:11px;color:var(--muted);margin-top:12px">*Free with newsletter signup.</p></div>
</div>

@php
    $bx = 'https://biolinxlabs.com';
    $go = fn ($dest) => route('outbound.track', ['slug' => 'lp-reta', 'dest' => $dest]);
    // Full image URLs (BioLinx product CDN) so no R2 id juggling.
    $shelf = [
        ['img' => 'https://assets.sticky.io/images/originals/2026-03-04-14-00-00/hdfsWqacCyBE3Y7aFw2MWZLTjxiuuEvXKSh8uXml.jpg', 'name' => 'G3-R (Retatrutide) · 10 mg', 'price' => '$79.93', 'dest' => $bx.'/products/g3-r-10-mg', 'contents' => 'Retatrutide 10mg · GLP-1/GIP/glucagon'],
        ['img' => 'https://assets.sticky.io/images/originals/2026-03-04-14-00-00/hdfsWqacCyBE3Y7aFw2MWZLTjxiuuEvXKSh8uXml.jpg', 'name' => 'Retatrutide Starter Kit', 'price' => '$94.86', 'dest' => $bx.'/bundles/retatrutide-10mg-bac-water?add=1', 'featured' => true, 'contents' => 'Retatrutide 10mg · Bacteriostatic Water 3ml'],
        ['img' => 'https://assets.sticky.io/images/originals/2026-03-04-14-00-00/1BVIU0DzYOg3E4ZS3LwvM6HiKTEh2yLT3EsuljVk.jpg', 'name' => 'G3-R (Retatrutide) · 30 mg', 'price' => '$179.93', 'dest' => $bx.'/products/g3-r-30-mg', 'contents' => 'Retatrutide 30mg · full-cycle size'],
        ['img' => 'https://assets.sticky.io/images/originals/2026-03-04-14-00-00/nIkAtvXmnV2ALWe4qJXEklgXoFfaVrhWGpixDxPs.jpg', 'name' => 'G2-T (Tirzepatide) · 30 mg', 'price' => '$179.93', 'dest' => $bx.'/products/g2-t-30-mg', 'contents' => 'Tirzepatide 30mg · dual agonist'],
        ['img' => 'https://assets.sticky.io/images/originals/2026-03-04-14-00-00/F1uQuaBUxIev17YKYCmCANGCmOzLoWAgKtIJwvJ2.jpg', 'name' => 'G1-S (Semaglutide) · 10 mg', 'price' => '$79.93', 'dest' => $bx.'/products/g1-s-10-mg', 'contents' => 'Semaglutide 10mg · GLP-1'],
    ];
    $quiz = [
        ['key' => 'max',    'label' => 'Maximum results (Retatrutide)', 'idx' => 1],
        ['key' => 'proven', 'label' => 'Proven middle ground (Tirzepatide)', 'idx' => 3],
        ['key' => 'simple', 'label' => 'Start simple (Semaglutide)', 'idx' => 4],
        ['key' => 'none',   'label' => 'Just G3-R 10 mg',            'idx' => 0],
    ];
@endphp

{{-- Mobile-only "Available At" purchase box --}}
<div class="mobile-avail">
<div class="sidebar-box" style="background:linear-gradient(180deg,var(--cta-soft) 0%,#FFF 100%);border:1.5px solid var(--cta)"><div class="sidebar-label ui" style="color:var(--cta);border-color:var(--cta)">Available At</div><h3>Retatrutide at BioLinx Labs</h3><p style="font-size:13.5px;line-height:1.55">Third-party HPLC tested · 99%+ purity · COA on every batch · Ships in 24 hrs.</p><a href="#" onclick="pmOpen();return false;" class="sidebar-cta ui">View Product →</a><img src="https://assets.sticky.io/images/originals/2026-03-04-14-00-00/hdfsWqacCyBE3Y7aFw2MWZLTjxiuuEvXKSh8uXml.jpg" alt="Retatrutide" style="width:100%;border-radius:6px;aspect-ratio:1/1;object-fit:contain;background:var(--bg);margin-top:14px" loading="lazy"></div>
</div>

<div class="ms-form ui" id="stackQuiz">
    <div class="ms-progress"><span class="ms-dot is-active" data-dot="1"></span><span class="ms-dot" data-dot="2"></span></div>
    <div class="ms-step" data-step="1">
        <div class="ms-h" style="font-family:'Source Serif 4',serif">Which GLP-1 fits your goal?</div>
        <div class="ms-sub">We'll curate the exact kit for you. Pick a goal to start.</div>
        <div class="ms-options">
            @foreach($quiz as $q)
            <button type="button" class="goal-pill" data-goal="{{ $q['key'] }}" onclick="msPick('{{ $q['key'] }}')">{!! $q['label'] !!}</button>
            @endforeach
        </div>
    </div>
    <div class="ms-step" data-step="2" hidden>
        <div class="ms-h" style="font-family:'Source Serif 4',serif">Your curated GLP-1 kit</div>
        <div class="ms-sub">Hand-picked for your goal. Third-party HPLC tested, COA on every batch, ships from the US.</div>
        <div id="msKits">
            @foreach($quiz as $q)
            @php $it = $shelf[$q['idx']]; @endphp
            <div class="stack-result-card" data-goal="{{ $q['key'] }}" data-url="{{ $go($it['dest']) }}">
                <img src="{{ $it['img'] }}" alt="{{ $it['name'] }}" loading="lazy">
                <div>
                    <div class="product-card-name">{{ $it['name'] }}</div>
                    <div class="product-card-price">{{ $it['price'] }}</div>
                </div>
            </div>
            @endforeach
        </div>
        <div class="ms-nav">
            <button type="button" class="ms-back" onclick="msGo(1)">&larr; Back</button>
            <a id="msViewBundle" href="{{ route('outbound.track', 'lp-reta') }}" class="ms-next" style="text-decoration:none;display:inline-block">View at BioLinx &rarr;</a>
        </div>
    </div>
</div>

<script>
function msGo(step){var f=document.getElementById('stackQuiz');if(!f)return;f.querySelectorAll('.ms-step').forEach(function(s){s.hidden=String(s.dataset.step)!==String(step);});f.querySelectorAll('.ms-dot').forEach(function(d){d.classList.toggle('is-active',Number(d.dataset.dot)<=Number(step));});if(Number(step)>1){try{f.scrollIntoView({behavior:'smooth',block:'start'});}catch(e){}}}
function msPick(goal){var f=document.getElementById('stackQuiz');if(!f)return;f.querySelectorAll('.goal-pill').forEach(function(b){b.classList.toggle('active',b.dataset.goal===goal);});var url=null;f.querySelectorAll('#msKits .stack-result-card').forEach(function(k){var on=k.dataset.goal===goal;k.classList.toggle('is-active',on);if(on)url=k.dataset.url;});var vb=document.getElementById('msViewBundle');if(vb&&url)vb.setAttribute('href',url);msGo(2);}
function pmOpen(){var o=document.getElementById('protocolModal');if(!o)return;o.classList.add('is-open');document.body.classList.add('pp-modal-open');var e=document.getElementById('pmEmail');if(e)setTimeout(function(){e.focus();},50);}
function pmClose(){var o=document.getElementById('protocolModal');if(!o)return;o.classList.remove('is-open');document.body.classList.remove('pp-modal-open');}
function pmShow(n){document.querySelectorAll('#protocolModal [data-pm]').forEach(function(s){s.hidden=String(s.dataset.pm)!==String(n);});}
function pmPick(goal){var m=document.getElementById('protocolModal');if(!m)return;m.querySelectorAll('.goal-pill').forEach(function(b){b.classList.toggle('active',b.dataset.goal===goal);});m.querySelectorAll('#pmKits .pm-kit').forEach(function(k){k.hidden=k.dataset.goal!==goal;});}
function pmSubmit(e){e.preventDefault();var email=document.getElementById('pmEmail'),phone=document.getElementById('pmPhone');if(!email||!email.value||!email.checkValidity()){if(email)email.reportValidity();return false;}var btn=document.getElementById('pmSubmitBtn');if(btn){btn.disabled=true;btn.textContent='Sending...';}var leadId='lead_'+Date.now()+'_'+Math.random().toString(36).slice(2,10);var meta=document.querySelector('meta[name="csrf-token"]'),csrf=meta?meta.getAttribute('content'):'';try{localStorage.setItem('reta_entered','1');}catch(err){}if(window.fbq){try{fbq('track','Lead',{},{eventID:leadId});}catch(err){}}if(window.posthog){try{posthog.capture('lead_submitted',{lander:'reta',placement:'protocol-modal'});}catch(err){}}var done=function(){pmShow(2);if(btn){btn.disabled=false;btn.textContent='Send Me The Complete Protocol →';}};try{fetch('/subscriber/sync',{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':csrf,'Accept':'application/json'},credentials:'same-origin',body:JSON.stringify({email:email.value,phone:(phone&&phone.value)||'',source:'lp-reta-protocol-modal',lead_event_id:leadId})}).then(done).catch(done);}catch(err){done();}return false;}
document.addEventListener('click',function(e){var o=document.getElementById('protocolModal');if(o&&o.classList.contains('is-open')&&e.target===o){pmClose();}});
document.addEventListener('keydown',function(e){if(e.key==='Escape'){pmClose();}});
</script>

<div class="guarantee ui"><div class="guarantee-badge"><div><div class="num">100%</div><div class="lbl">Money<br>Back</div></div></div><div><h3>BioLinx Labs 60-Day Quality Guarantee</h3><p>Every batch is third-party tested. If your COA doesn't match the label, or anything is off, you get a full refund. No questions asked. Sourcing peptides shouldn't be a leap of faith.</p></div></div>

<hr class="section-rule">
<h2>Frequently Asked Questions</h2>
<div class="faq ui">
<div class="faq-item"><button class="faq-q">Is Retatrutide legal? <span class="icon">+</span></button><div class="faq-a">Retatrutide is legally sold for research purposes under "For Research Use Only" labeling at quality labs. It is not yet FDA-approved as a medicine. This article is for education only, not medical advice. Talk to your doctor first.</div></div>
<div class="faq-item"><button class="faq-q">How is it different from Ozempic or Mounjaro? <span class="icon">+</span></button><div class="faq-a">Ozempic/Wegovy (semaglutide) hit one receptor, GLP-1. Mounjaro/Zepbound (tirzepatide) hit two, GLP-1 and GIP. Retatrutide hits three, adding glucagon, which is why its phase 2 weight-loss numbers were the highest of the group.</div></div>
<div class="faq-item"><button class="faq-q">How do I take it? <span class="icon">+</span></button><div class="faq-a">You reconstitute the powder once with bacteriostatic water (the free guide shows the exact ratio), store it in the fridge, and take one small weekly subcutaneous shot with an insulin needle. You start on a low dose and step up slowly on the titration schedule.</div></div>
<div class="faq-item"><button class="faq-q">What about side effects? <span class="icon">+</span></button><div class="faq-a">The most common are gastrointestinal: nausea, reduced appetite, and sometimes constipation or loose stools, especially in the first weeks after a dose increase. Titrating slowly is how you keep them mild. Always talk to your doctor first, and never use it if you have a personal or family history of medullary thyroid cancer or MEN 2.</div></div>
<div class="faq-item"><button class="faq-q">How fast does it work? <span class="icon">+</span></button><div class="faq-a">Appetite usually quiets within the first week or two. Meaningful weight change builds over months of consistent weekly dosing as you titrate up, exactly what the guide maps out.</div></div>
</div>

<div id="order" class="inline-cta" style="background:var(--cta);border:none"><h3 style="font-size:28px">Get The Complete Retatrutide Protocol, Free</h3><p style="opacity:.92">Sourcing, reconstitution, week-by-week titration, side-effect management. Everything. Direct to your inbox.</p>
<form class="ui pp-capture" data-form="order" onsubmit="return false;" style="display:flex;gap:8px;max-width:460px;margin:14px auto 0;flex-wrap:wrap">
<input type="email" name="email" placeholder="you@email.com" required style="flex:1;min-width:200px;padding:16px 18px;border:none;border-radius:6px;font-size:15px;font-weight:500">
<button type="submit" style="padding:16px 26px;background:var(--ink);color:#fff;border:none;border-radius:6px;font-size:15px;font-weight:800;letter-spacing:.02em;text-transform:uppercase;cursor:pointer;box-shadow:0 4px 0 #000">Send It Now →</button>
</form>
<div class="pp-capture-success ui" hidden style="max-width:460px;margin:14px auto 0"><p style="color:#fff;opacity:.95">✓ Check your inbox. Your Retatrutide protocol is on the way.</p><a href="{{ route('outbound.track', 'lp-reta') }}" class="cta-button ui" style="background:var(--ink);box-shadow:0 4px 0 #000">View Retatrutide At BioLinx →</a></div>
<div class="cta-trust ui" style="opacity:.92"><span>Limited launch pricing</span><span>No spam, ever</span></div></div>

<div class="ps-block"><strong>P.S.</strong> If a single-hormone GLP-1 stalled for you, this is why. Retatrutide adds a third lever the older drugs never had. The phase 2 human data is real, and the guide walks you through everything step by step.</div>

<div class="ps-block"><strong>P.P.S.</strong> The reconstitution and titration walkthrough alone is worth getting the guide. Most first-timers mix their first vial wrong or step the dose up too fast. The guide shows you exactly how to do it right.</div>

<p style="font-size:13px;color:var(--muted);margin-top:32px"><strong>Disclaimer:</strong> Retatrutide is not FDA-approved for human use in the United States. This article is educational and for research purposes only. It is not medical advice. Consult your doctor before using any new treatment.</p>
</div></div></div>

<!-- Sidebar -->
<div class="hidden md:block md:col-span-1"><div class="sticky top-14">
<div class="sidebar-box" style="background:linear-gradient(180deg,var(--cta-soft) 0%,#FFF 100%);border:1.5px solid var(--cta)"><div class="sidebar-label ui" style="color:var(--cta);border-color:var(--cta)">Available At</div><h3>Retatrutide at BioLinx Labs</h3><p style="font-size:13.5px;line-height:1.55">Third-party HPLC tested · 99%+ purity · COA on every batch · Ships in 24 hrs.</p><a href="#" onclick="pmOpen();return false;" class="sidebar-cta ui">View Product →</a><img src="https://assets.sticky.io/images/originals/2026-03-04-14-00-00/hdfsWqacCyBE3Y7aFw2MWZLTjxiuuEvXKSh8uXml.jpg" alt="Retatrutide" style="width:100%;border-radius:6px;aspect-ratio:1/1;object-fit:contain;background:var(--bg);margin-top:14px"></div>

<div class="sidebar-box"><div class="sidebar-label ui">Free Protocol Guide</div><h3>The Complete Retatrutide Starter Kit</h3><p>Sourcing, reconstitution, dosing, cycling, side-effect management. Everything in one place.</p><form class="ui email-form pp-capture" data-form="sidebar" onsubmit="return false;" style="flex-direction:column;gap:6px"><input type="email" name="email" placeholder="you@email.com" required><button type="submit" style="width:100%">Send The Guide →</button></form><div class="pp-capture-success ui" hidden><p style="color:var(--green)">✓ Check your inbox. Your guide is on the way.</p><a href="{{ route('outbound.track', 'lp-reta') }}" class="sidebar-cta ui" style="margin-bottom:0">View Retatrutide →</a></div></div>

<div class="sidebar-box"><div class="sidebar-label ui">Why Readers Trust This Desk</div><p style="font-size:13.5px;line-height:1.6">No corporate partnerships. No pharma money. We only recommend research labs with publicly verifiable COAs. Field reporting on the metabolic compounds people are actually using.</p></div>
</div></div>
</div></div></main>

<footer><div class="container"><div class="footer-grid ui"><div><h4>The Metabolic Journal</h4><p>Field reporting on real metabolic science and the incretin drugs changing weight loss. No paywall. No pharma sponsorship.</p></div><div><h4>Resources</h4><p><a href="#stackQuiz">Retatrutide Getting Started</a></p><p><a href="#stackQuiz">Sourcing &amp; COA Audit</a></p><p><a href="#stackQuiz">The Research</a></p></div><div><h4>Legal</h4><p><a href="/disclaimer">Disclaimer</a></p><p><a href="/privacy">Privacy</a></p><p><a href="/terms">Terms</a></p></div></div><div class="footer-bottom ui">The Metabolic Journal · © 2026 · Independent Field Reporting · Not medical advice</div></div></footer>

<a href="#protocol" class="sticky-cta ui" id="stickyCta">Get The Free Protocol →</a>

<!-- Complete-protocol modal: Step 1 email+phone -> Step 2 offer + goal quiz + curated kit (20% off) -->
<div class="pp-modal-overlay" id="protocolModal" aria-hidden="true">
  <div class="pp-modal" style="max-width:520px;text-align:left">
    <button class="pp-modal-close" onclick="pmClose()" aria-label="Close">&times;</button>
    <div data-pm="1">
      <div class="pp-modal-kicker" style="text-align:center">Free Protocol</div>
      <h3 style="text-align:center">Get the Complete Retatrutide Protocol, Free</h3>
      <p style="text-align:center;font-size:14px;color:var(--muted);margin-bottom:18px">Sourcing, reconstitution, titration, and your discount code. Straight to your inbox.</p>
      <form id="pmForm" onsubmit="return pmSubmit(event)" style="display:flex;flex-direction:column;gap:10px">
        <input type="email" id="pmEmail" required placeholder="Email address (required)" autocomplete="email" style="padding:12px 14px;border:1px solid var(--line);border-radius:6px;font-size:14px">
        <input type="tel" id="pmPhone" placeholder="Phone number (optional, for text updates)" autocomplete="tel" style="padding:12px 14px;border:1px solid var(--line);border-radius:6px;font-size:14px">
        <button type="submit" id="pmSubmitBtn" class="cta-button ui" style="width:100%">Send Me The Complete Protocol &rarr;</button>
        <p style="font-size:11px;color:var(--muted);text-align:center;margin:2px 0 0">No spam. Unsubscribe anytime.</p>
      </form>
    </div>
    <div data-pm="2" hidden>
      <div class="pp-modal-kicker" style="text-align:center;color:var(--green)">&#10003; Protocol on its way</div>
      <h3 style="text-align:center;font-size:20px;line-height:1.25">Want to view these on BioLinx with a LIMITED TIME 20% OFF coupon?</h3>
      <p style="text-align:center;font-size:13px;color:var(--muted);margin-bottom:16px">Pick your goal and we'll show the exact kit, 20% off applied.</p>
      <div class="ms-options" style="max-width:none">
        @foreach($quiz as $q)
        <button type="button" class="goal-pill" data-goal="{{ $q['key'] }}" onclick="pmPick('{{ $q['key'] }}')">{!! $q['label'] !!}</button>
        @endforeach
      </div>
      <div id="pmKits" style="margin-top:16px">
        @foreach($quiz as $q)
        @php $it = $shelf[$q['idx']]; $sep = str_contains($it['dest'], '?') ? '&' : '?'; @endphp
        <div class="pm-kit" data-goal="{{ $q['key'] }}" hidden style="border:1.5px solid var(--cta);border-radius:8px;padding:14px;background:var(--cta-soft)">
          <div style="display:flex;gap:14px;align-items:center">
            <img src="{{ $it['img'] }}" alt="{{ $it['name'] }}" loading="lazy" style="width:80px;height:80px;object-fit:contain;border-radius:6px;background:var(--bg);flex:0 0 80px">
            <div>
              <div class="product-card-name" style="font-size:15px;line-height:1.3">{{ $it['name'] }}</div>
              <div style="font-size:15px;font-weight:700;color:var(--ink);margin-top:3px">{{ $it['price'] }} <span style="color:var(--green);font-size:11.5px;font-weight:700">20% OFF applied</span></div>
            </div>
          </div>
          <div style="font-size:12.5px;color:var(--body);margin-top:11px;line-height:1.5"><strong>Includes:</strong> {{ $it['contents'] }}</div>
          <a href="{{ $go($it['dest'] . $sep . 'discount=PROTOCOL20') }}" class="cta-button ui" style="display:block;text-align:center;margin-top:12px;width:100%">View at BioLinx · 20% Off &rarr;</a>
        </div>
        @endforeach
      </div>
      <button type="button" onclick="pmClose()" style="display:block;margin:14px auto 0;background:none;border:none;color:var(--muted);font-size:13px;font-weight:600;cursor:pointer">No thanks, I just want the protocol</button>
    </div>
  </div>
</div>

<!-- Protocol popup: email capture, auto-shows after delay + on exit intent -->
<div class="pp-modal-overlay" id="ppModal" aria-hidden="true">
<div class="pp-modal ui" role="dialog" aria-modal="true" aria-labelledby="ppModalTitle">
<button class="pp-modal-close" type="button" id="ppModalClose" aria-label="Close">&times;</button>
<div class="pp-modal-kicker">Free Reader Download</div>
<h3 id="ppModalTitle">Get The Complete Retatrutide Protocol, Free</h3>
<p>Sourcing, reconstitution, titration schedule. Everything in one guide, sent to your inbox in 60 seconds.</p>
<form class="pp-capture" data-form="popup" onsubmit="return false;">
<input type="email" name="email" placeholder="you@email.com" required>
<button type="submit">Send It Now →</button>
</form>
<div class="pp-capture-success" hidden><p style="color:var(--green)">✓ Check your inbox. Your protocol is on the way.</p><a href="{{ route('outbound.track', 'lp-reta') }}" class="cta-button ui" style="width:100%">View Retatrutide At BioLinx →</a></div>
<div class="pp-modal-trust">No spam, ever · Unsubscribe anytime</div>
</div>
</div>

<script>window.PP_GO = @json(route('outbound.track', 'lp-reta'));</script>
@verbatim
<script>
const progress=document.getElementById('progress'),sticky=document.getElementById('stickyCta');
document.addEventListener('scroll',()=>{const h=document.documentElement.scrollHeight-document.documentElement.clientHeight,p=(window.scrollY/h)*100;progress.style.width=p+'%';if(window.scrollY>600&&window.scrollY<h-200)sticky.classList.add('show');else sticky.classList.remove('show')});
document.querySelectorAll('.faq-q').forEach(b=>b.addEventListener('click',()=>b.parentElement.classList.toggle('open')));
if(innerWidth<768)setTimeout(()=>{const m=document.getElementById('masthead');if(m)m.classList.add('collapsed')},2000);
(function(){
  function showSuccess(form){var wrap=form.parentElement;form.hidden=true;var s=wrap?wrap.querySelector('.pp-capture-success'):null;if(s)s.hidden=false;}
  document.querySelectorAll('form.pp-capture').forEach(function(form){
    form.addEventListener('submit',function(e){
      e.preventDefault();
      if(!form.checkValidity()){form.reportValidity();return;}
      var btn=form.querySelector('button[type="submit"]');if(btn){btn.disabled=true;btn.textContent='Sending...';}
      var email=(form.querySelector('input[type="email"]')||{}).value||'';
      var leadEventId='lead_'+Date.now()+'_'+Math.random().toString(36).slice(2,10);
      var meta=document.querySelector('meta[name="csrf-token"]');var csrf=meta?meta.getAttribute('content'):'';
      try{localStorage.setItem('reta_entered','1');}catch(err){}
      if(window.fbq){try{fbq('track','Lead',{},{eventID:leadEventId});}catch(err){}}
      if(window.posthog){try{posthog.capture('lead_submitted',{lander:'reta',placement:form.getAttribute('data-form')||''});}catch(err){}}
      var done=function(){showSuccess(form);};
      try{fetch('/subscriber/sync',{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':csrf,'Accept':'application/json'},credentials:'same-origin',body:JSON.stringify({email:email,source:'lp-reta',lead_event_id:leadEventId})}).then(done).catch(done);}catch(err){done();}
    });
  });
  var overlay=document.getElementById('ppModal'),closeBtn=document.getElementById('ppModalClose');
  if(!overlay)return;
  function openModal(){overlay.classList.add('is-open');document.body.classList.add('pp-modal-open');}
  function closeModal(){overlay.classList.remove('is-open');document.body.classList.remove('pp-modal-open');try{localStorage.setItem('reta_dismissed_at',String(Date.now()));}catch(err){}}
  if(closeBtn)closeBtn.addEventListener('click',closeModal);
  overlay.addEventListener('click',function(e){if(e.target===overlay)closeModal();});
  document.addEventListener('keydown',function(e){if(e.key==='Escape'&&overlay.classList.contains('is-open'))closeModal();});
  function suppressed(){try{if(/[?&]popup=force/.test(location.search))return false;if(localStorage.getItem('reta_entered'))return true;var d=parseInt(localStorage.getItem('reta_dismissed_at')||'0',10);return !!d&&(Date.now()-d)<7*24*60*60*1000;}catch(err){return false;}}
  function autoOpen(){if(overlay.classList.contains('is-open')||suppressed())return;openModal();}
  setTimeout(autoOpen,9000);
  document.addEventListener('mouseout',function(e){if(!e.relatedTarget&&e.clientY<=0&&window.matchMedia('(pointer:fine)').matches)autoOpen();});
})();
</script>
@endverbatim
</body>
</html>
