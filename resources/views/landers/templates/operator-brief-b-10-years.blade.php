{{--
    Operator Brief — B VARIANT ("Intelligence Dossier" re-skin).
    SAME CMS slots as the control (operator-brief.blade.php): $lander->content drives
    every text/image/UTM slot (4 flag-blocks, 4 checklist rows, 3 reference dropdowns),
    so marketing edits everything via Admin → Landers. Tracking, images, outbound /go
    target, giveaway popup and age gate are HELD CONSTANT (identical to control).
    Only the design system (fonts, palette, layout, components) + section architecture
    differ: dark fintech-grade dossier, sticky verdict CTA, monospaced data accents,
    curiosity-first with the gate reachable inside the first viewport via #gate anchor.
--}}
@php
    $c = fn($path, $default = '') => $lander->c($path, $default);
    $flags = $lander->c('flags', []);
    $checklist = $lander->c('closing.checklist', []);
    $dropdowns = $lander->c('refs.dropdowns', []);
    // Same outbound mechanism as control: route('outbound.track', $lander->outbound_slug).
    $go = fn() => route('outbound.track', $lander->outbound_slug);
@endphp
<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="robots" content="{{ ($lander->noindex || $c('meta.noindex')) ? 'noindex,nofollow' : 'index,follow' }}">
        <title>{{ $c('meta.title', $lander->name) }}</title>
        <meta name="description" content="{{ $c('meta.description') }}">
        <meta property="og:type" content="article">
        <meta property="og:site_name" content="The Operator Brief">
        <meta property="og:title" content="{{ $c('meta.title', $lander->name) }}">
        <meta property="og:description" content="{{ $c('meta.description') }}">
        <meta property="og:url" content="{{ url()->current() }}">
        <meta name="twitter:card" content="summary">
        <meta name="twitter:title" content="{{ $c('meta.title', $lander->name) }}">
        <meta name="twitter:description" content="{{ $c('meta.description') }}">
        <link rel="preconnect" href="https://pub-0a9781e86a6b4f2d9b5bfbe22904ad3c.r2.dev" crossorigin>
        @if($c('hero.image_url'))<link rel="preload" as="image" href="{{ $c('hero.image_url') }}">@endif
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500;700&display=swap" rel="stylesheet">
        @verbatim<style>
        :root{
            --bg:#0a0e16; --bg-2:#0e1420; --panel:#121a28; --panel-2:#16202f;
            --line:#1f2b3d; --line-2:#2a3a52; --hair:#16202f;
            --txt:#e8eef7; --txt-2:#a7b4c8; --txt-3:#6c7c95; --faint:#4a5871;
            --cy:#2de2c6; --cy-2:#13b89c; --cy-soft:rgba(45,226,198,.10);
            --amber:#f5b544; --red:#ff6a5c; --red-soft:rgba(255,106,92,.10);
            --r:14px; --r-sm:9px; --r-lg:20px; --col:760px;
            --mono:'JetBrains Mono',ui-monospace,SFMono-Regular,Menlo,monospace;
            --disp:'Space Grotesk',system-ui,sans-serif;
            --sans:'Inter',system-ui,-apple-system,sans-serif;
        }
        *{box-sizing:border-box;margin:0;padding:0}
        html{scroll-behavior:smooth}
        body{
            background:var(--bg); color:var(--txt); font-family:var(--sans);
            line-height:1.62; font-size:17px; -webkit-font-smoothing:antialiased;
            background-image:
                radial-gradient(900px 500px at 80% -8%, rgba(45,226,198,.07), transparent 60%),
                radial-gradient(700px 500px at -10% 12%, rgba(40,90,160,.10), transparent 55%);
            background-attachment:fixed;
        }
        a{color:inherit}
        .wrap{max-width:var(--col); margin:0 auto; padding:0 18px}

        /* ── top status bar (re-skinned notice) ── */
        .statusbar{
            background:#06090f; border-bottom:1px solid var(--line);
            color:var(--txt-2); font-family:var(--mono); font-size:11px;
            letter-spacing:.14em; text-transform:uppercase; font-weight:500;
        }
        .statusbar .wrap{display:flex; align-items:center; gap:10px; padding:9px 18px; justify-content:center; flex-wrap:wrap}
        .statusbar .live{display:inline-flex; align-items:center; gap:7px; color:var(--cy)}
        .statusbar .live::before{content:""; width:7px; height:7px; border-radius:50%; background:var(--cy); box-shadow:0 0 0 0 rgba(45,226,198,.6); animation:pulse 2s infinite}
        @keyframes pulse{0%{box-shadow:0 0 0 0 rgba(45,226,198,.55)}70%{box-shadow:0 0 0 7px rgba(45,226,198,0)}100%{box-shadow:0 0 0 0 rgba(45,226,198,0)}}
        .statusbar .sep{color:var(--faint)}

        /* ── masthead ── */
        .masthead{background:rgba(8,11,18,.72); backdrop-filter:blur(10px); border-bottom:1px solid var(--line); position:sticky; top:0; z-index:40}
        .masthead .wrap{display:flex; align-items:center; justify-content:space-between; padding:13px 18px; gap:14px}
        .masthead .pub{font-family:var(--disp); font-weight:700; font-size:18px; letter-spacing:-.02em; color:var(--txt); display:flex; align-items:center; gap:9px}
        .masthead .pub .mk{width:24px; height:24px; border-radius:7px; background:linear-gradient(135deg,var(--cy),var(--cy-2)); display:inline-flex; align-items:center; justify-content:center; color:#06120e; font-weight:700; font-size:13px; box-shadow:0 0 18px rgba(45,226,198,.4)}
        .masthead .pub b{color:var(--cy)}
        .masthead .tag{font-family:var(--mono); font-size:10px; letter-spacing:.14em; text-transform:uppercase; color:var(--txt-3); text-align:right}
        @media (max-width:560px){ .masthead .tag{display:none} }

        /* ── shell ── */
        .doc{padding:30px 0 130px}
        .eyebrow{display:inline-flex; align-items:center; gap:9px; font-family:var(--mono); font-size:11px; letter-spacing:.16em; text-transform:uppercase; color:var(--cy); font-weight:500; border:1px solid var(--line-2); background:var(--cy-soft); padding:6px 12px; border-radius:99px; margin-bottom:20px}
        .eyebrow::before{content:"●"; font-size:8px; color:var(--cy)}

        h1{font-family:var(--disp); font-weight:700; font-size:33px; line-height:1.12; letter-spacing:-.025em; color:#fff; margin-bottom:18px}
        .dek{font-size:18px; line-height:1.55; color:var(--txt-2); margin-bottom:24px; max-width:62ch}
        @media (max-width:560px){ h1{font-size:27px} .dek{font-size:16.5px} }

        /* ── hero figure ── */
        figure{margin:0}
        .doc-img{display:block; width:100%; height:auto; border-radius:var(--r); border:1px solid var(--line-2); background:var(--panel)}
        .figframe{position:relative; border-radius:var(--r-lg); padding:10px; background:linear-gradient(180deg,var(--panel),var(--bg-2)); border:1px solid var(--line); margin:26px 0; box-shadow:0 24px 60px rgba(0,0,0,.45)}
        .figframe::before{content:"REF // FIELD ASSET"; position:absolute; top:-9px; left:18px; font-family:var(--mono); font-size:9.5px; letter-spacing:.16em; color:var(--txt-3); background:var(--bg); padding:0 8px; border:1px solid var(--line); border-radius:5px; line-height:18px}

        /* ── byline ── */
        .byline{display:flex; align-items:center; gap:12px; flex-wrap:wrap; font-size:13.5px; color:var(--txt-3); border-top:1px solid var(--line); border-bottom:1px solid var(--line); padding:14px 0; margin:6px 0 26px}
        .byline .av{width:40px; height:40px; border-radius:11px; background:linear-gradient(135deg,#1f3148,var(--cy-2)); flex-shrink:0; border:1px solid var(--line-2)}
        .byline b{color:var(--txt)}
        .byline .dot{color:var(--faint)}
        .byline .mono{font-family:var(--mono); font-size:12px; letter-spacing:.04em}

        /* ── body copy ── */
        .copy p{font-size:17px; line-height:1.7; color:var(--txt-2); margin-bottom:17px}
        .copy p strong{color:var(--txt); font-weight:600}
        .lead{font-size:19.5px !important; line-height:1.55 !important; color:var(--txt) !important; font-weight:500}
        .copy mark{background:transparent; color:var(--cy); font-weight:600; padding:0}

        /* ── at-a-glance proof strip ── */
        .glance{border:1px solid var(--line); background:linear-gradient(180deg,var(--panel),var(--bg-2)); border-radius:var(--r-lg); padding:22px 20px 8px; margin:30px 0 8px}
        .glance .gh{font-family:var(--mono); font-size:11px; letter-spacing:.14em; text-transform:uppercase; color:var(--txt-3); margin-bottom:14px; display:flex; align-items:center; gap:9px}
        .glance .gh::after{content:""; flex:1; height:1px; background:linear-gradient(90deg,var(--line-2),transparent)}
        .glist{list-style:none; counter-reset:g}
        .glist li{position:relative; padding:13px 0 13px 46px; border-top:1px solid var(--hair); font-size:15.5px; line-height:1.5; color:var(--txt-2)}
        .glist li:first-child{border-top:none}
        .glist li::before{counter-increment:g; content:counter(g,decimal-leading-zero); position:absolute; left:0; top:13px; font-family:var(--mono); font-weight:700; font-size:13px; color:var(--cy); background:var(--cy-soft); border:1px solid var(--line-2); border-radius:7px; width:30px; height:26px; display:flex; align-items:center; justify-content:center}
        .glist li b{color:#fff; font-weight:600}

        /* ── flag cards ── */
        .flag{position:relative; border:1px solid var(--line); background:var(--panel); border-radius:var(--r); padding:22px 20px; margin:18px 0; overflow:hidden}
        .flag::before{content:""; position:absolute; left:0; top:0; bottom:0; width:3px; background:linear-gradient(180deg,var(--red),var(--amber))}
        .flagk{display:inline-flex; align-items:center; gap:8px; font-family:var(--mono); font-size:11px; letter-spacing:.12em; text-transform:uppercase; color:var(--red); font-weight:700; background:var(--red-soft); border:1px solid rgba(255,106,92,.28); border-radius:99px; padding:5px 12px; margin-bottom:13px}
        .flagk::before{content:"⚑"; font-size:12px}
        .flag h2{font-family:var(--disp); font-weight:600; font-size:21px; line-height:1.25; color:#fff; margin-bottom:11px; letter-spacing:-.01em}
        .flag .copy p{font-size:16px; line-height:1.65; margin-bottom:13px}
        .flag .copy p:last-child{margin-bottom:0}

        /* ── inline jump CTA ── */
        .jump{display:inline-flex; align-items:center; gap:8px; margin-top:15px; font-family:var(--mono); font-size:13px; font-weight:500; letter-spacing:.02em; color:var(--cy); text-decoration:none; border-bottom:1px dashed rgba(45,226,198,.45); padding-bottom:2px}
        .jump::after{content:"→"; transition:transform .15s}
        @media (hover:hover){ .jump:hover{color:#fff} .jump:hover::after{transform:translateX(4px)} }

        /* ── reframe panel ── */
        .reframe{border:1px solid var(--line-2); border-radius:var(--r-lg); padding:26px 22px; margin:34px 0; background:radial-gradient(140% 120% at 0% 0%, rgba(45,226,198,.08), transparent 55%), var(--bg-2)}
        .reframe .rk{font-family:var(--mono); font-size:10.5px; letter-spacing:.16em; text-transform:uppercase; color:var(--cy); margin-bottom:12px}
        .reframe h2{font-family:var(--disp); font-weight:600; font-size:23px; color:#fff; line-height:1.22; margin-bottom:13px; letter-spacing:-.01em}
        .reframe .copy p{font-size:16.5px; margin-bottom:13px}
        .reframe .copy p:last-child{margin-bottom:0}

        /* ── survivor checklist ── */
        .surv-h{font-family:var(--disp); font-weight:600; font-size:21px; color:#fff; margin:34px 0 4px; letter-spacing:-.01em}
        .check{border:1px solid var(--line); border-radius:var(--r); overflow:hidden; margin:16px 0 26px; background:var(--panel)}
        .crow{display:flex; gap:13px; padding:14px 17px; font-size:15.5px; line-height:1.5; border-top:1px solid var(--hair); align-items:flex-start; color:var(--txt-2)}
        .crow:first-child{border-top:none}
        .crow .ic{flex-shrink:0; width:24px; height:24px; border-radius:7px; display:flex; align-items:center; justify-content:center; font-size:13px; font-weight:700; color:#06120e; background:linear-gradient(135deg,var(--cy),var(--cy-2)); margin-top:1px; box-shadow:0 0 14px rgba(45,226,198,.25)}
        .crow b{color:#fff; font-weight:600}

        /* ── the gate / verdict ── */
        .gate{position:relative; border:1px solid var(--line-2); border-radius:var(--r-lg); padding:34px 26px 30px; margin:36px 0 14px; background:linear-gradient(180deg,#101b29,#0a121d); box-shadow:0 0 0 1px rgba(45,226,198,.08), 0 30px 80px rgba(0,0,0,.5); text-align:center; overflow:hidden}
        .gate::before{content:""; position:absolute; inset:0 0 auto 0; height:3px; background:linear-gradient(90deg,transparent,var(--cy),transparent)}
        .gate .vk{font-family:var(--mono); font-size:10.5px; letter-spacing:.18em; text-transform:uppercase; color:var(--cy); margin-bottom:14px}
        .gate h2{font-family:var(--disp); font-weight:700; font-size:25px; color:#fff; line-height:1.18; letter-spacing:-.02em; margin-bottom:13px}
        @media (max-width:560px){ .gate h2{font-size:21px} }
        .gate .gp{color:var(--txt-2); font-size:16px; margin-bottom:20px; max-width:50ch; margin-left:auto; margin-right:auto}
        .gate .chk{display:flex; gap:12px; text-align:left; align-items:flex-start; background:rgba(255,255,255,.03); border:1px solid var(--line); border-radius:var(--r-sm); padding:14px 15px; margin-bottom:18px; font-size:13.5px; line-height:1.5; color:var(--txt-2); cursor:pointer; max-width:460px; margin-left:auto; margin-right:auto}
        .gate .chk input{width:20px; height:20px; flex-shrink:0; margin-top:1px; accent-color:var(--cy)}

        .cta{display:flex; align-items:center; justify-content:center; gap:10px; max-width:460px; margin:0 auto; background:linear-gradient(135deg,var(--cy),var(--cy-2)); color:#04120d; font-family:var(--disp); font-size:18px; font-weight:700; letter-spacing:-.01em; padding:18px 26px; border:none; border-radius:var(--r-sm); text-decoration:none; cursor:pointer; transition:transform .12s, box-shadow .12s, opacity .12s; box-shadow:0 0 30px rgba(45,226,198,.32)}
        .cta.locked{background:#1c2738; color:var(--txt-3); box-shadow:none; cursor:not-allowed}
        @media (hover:hover){ .cta:not(.locked):hover{transform:translateY(-2px); box-shadow:0 0 42px rgba(45,226,198,.5)} }
        .gate .sub{font-family:var(--mono); font-size:12px; letter-spacing:.04em; color:var(--txt-3); margin-top:14px}

        /* ── references / dossier appendix ── */
        .refs{margin-top:40px; padding-top:24px; border-top:1px solid var(--line)}
        .refs-h{font-family:var(--mono); font-size:11px; letter-spacing:.16em; text-transform:uppercase; color:var(--txt-3); margin-bottom:16px}
        .drop{border:1px solid var(--line); border-radius:var(--r); background:var(--bg-2); margin:0 0 10px; overflow:hidden}
        .drop summary{cursor:pointer; list-style:none; padding:15px 17px; font-weight:600; color:var(--txt); font-size:15px; display:flex; justify-content:space-between; align-items:center; gap:12px}
        .drop summary::-webkit-details-marker{display:none}
        .drop summary::after{content:"+"; font-family:var(--mono); color:var(--cy); font-weight:700; font-size:20px; line-height:1}
        .drop[open] summary::after{content:"–"}
        .drop[open] summary{border-bottom:1px solid var(--hair)}
        .drop .db{padding:14px 17px 17px}
        .drop .db p{font-size:14.5px; line-height:1.6; margin:0 0 12px; color:var(--txt-2)}
        .drop ol{margin:0; padding:0; list-style:none; counter-reset:s}
        .drop ol li{position:relative; padding:11px 0 11px 38px; border-top:1px solid var(--hair); font-size:14.5px; line-height:1.5; color:var(--txt-2)}
        .drop ol li:first-child{border-top:none}
        .drop ol li::before{counter-increment:s; content:counter(s); position:absolute; left:8px; top:11px; font-family:var(--mono); color:var(--cy); font-weight:700; font-size:14px}
        .drop ol li b{color:#fff}
        .drop .src{font-family:var(--mono); font-size:11.5px; color:var(--faint); margin-top:14px; line-height:1.55}

        /* ── footer ── */
        footer{border-top:1px solid var(--line); background:#06090f; padding:30px 0 110px}
        .disc{font-size:12.5px; color:var(--txt-3); line-height:1.66; margin-bottom:14px}
        .disc b{color:var(--txt-2)}
        .flinks{font-family:var(--mono); font-size:11.5px; color:var(--faint); letter-spacing:.03em}
        .flinks a{color:var(--txt-3); margin:0 6px; text-decoration:none}
        @media (hover:hover){ .flinks a:hover{color:var(--cy)} }

        /* ── sticky verdict CTA bar (B signature component) ── */
        .stickybar{position:fixed; left:0; right:0; bottom:0; z-index:60; background:rgba(8,11,18,.86); backdrop-filter:blur(12px); border-top:1px solid var(--line-2); box-shadow:0 -12px 40px rgba(0,0,0,.5)}
        .stickybar .wrap{display:flex; align-items:center; gap:14px; padding:11px 18px}
        .stickybar .lbl{font-family:var(--mono); font-size:11px; letter-spacing:.1em; text-transform:uppercase; color:var(--txt-3); line-height:1.3}
        .stickybar .lbl b{display:block; font-family:var(--sans); font-size:14px; letter-spacing:0; text-transform:none; color:var(--txt); font-weight:600; margin-top:2px}
        .stickybar .sbtn{margin-left:auto; flex-shrink:0; display:inline-flex; align-items:center; gap:8px; background:linear-gradient(135deg,var(--cy),var(--cy-2)); color:#04120d; font-family:var(--disp); font-weight:700; font-size:15px; padding:13px 20px; border-radius:10px; text-decoration:none; box-shadow:0 0 24px rgba(45,226,198,.3); white-space:nowrap}
        .stickybar .sbtn::after{content:"→"}
        @media (hover:hover){ .stickybar .sbtn:hover{transform:translateY(-1px)} }
        @media (max-width:560px){ .stickybar .lbl{display:none} .stickybar .sbtn{margin-left:0; flex:1; justify-content:center} }

        /* ── age gate ── */
        .age{position:fixed; inset:0; background:rgba(4,6,11,.96); backdrop-filter:blur(6px); z-index:200; display:flex; align-items:center; justify-content:center; padding:24px}
        .age.hidden{display:none}
        .age .card{background:linear-gradient(180deg,var(--panel),var(--bg-2)); border:1px solid var(--line-2); border-radius:var(--r-lg); max-width:430px; padding:34px 30px; text-align:center; box-shadow:0 30px 80px rgba(0,0,0,.6)}
        .age .card .b{font-family:var(--disp); font-weight:700; font-size:22px; color:#fff; margin-bottom:10px}
        .age .card .b b{color:var(--cy)}
        .age .card p{font-size:14px; color:var(--txt-2); margin-bottom:22px; line-height:1.6}
        .age .row{display:flex; gap:10px; justify-content:center}
        .age button{font-family:var(--disp); font-size:14px; font-weight:600; padding:12px 22px; border-radius:10px; cursor:pointer; border:1px solid var(--line-2); background:transparent; color:var(--txt-2)}
        .age .yes{background:linear-gradient(135deg,var(--cy),var(--cy-2)); color:#04120d; border-color:transparent; box-shadow:0 0 24px rgba(45,226,198,.3)}

        /* ── responsive ── */
        @media (min-width:840px){
            :root{ --col:800px; }
            h1{font-size:46px}
            .dek{font-size:20px}
            .lead{font-size:21px !important}
            .copy p{font-size:18px}
            .flag h2{font-size:23px}
            .gate h2{font-size:29px}
        }
        @media (min-width:1200px){
            :root{ --col:880px; }
            h1{font-size:52px}
        }
        </style>@endverbatim
        <x-meta-pixel />
        <x-posthog-lander />
    </head>
    <body>
        <div class="age" id="age">
            <div class="card">
                <div class="b">{!! $c('age_gate.title', 'The Operator <b>Brief</b>') !!}</div>
                <p>{{ $c('age_gate.body') }}</p>
                <div class="row">
                    <button class="yes" onclick="try{localStorage.setItem('pp_age_ok','1')}catch(e){};document.getElementById('age').classList.add('hidden')">I am 18+ · Continue</button>
                    <button class="no" onclick="location.href='https://www.google.com'">Exit</button>
                </div>
            </div>
        </div>
        {{-- Remember the 18+ acknowledgment so the gate doesn't re-prompt every load (reduces paid-traffic friction). --}}
        <script>try{if(localStorage.getItem('pp_age_ok')==='1'){document.getElementById('age').classList.add('hidden')}}catch(e){}</script>

        {{-- top status bar (re-skin of control .notice) --}}
        <div class="statusbar">
            <div class="wrap">
                <span class="live">Live dossier</span>
                <span class="sep">//</span>
                <span>{{ $c('chrome.notice') }}</span>
            </div>
        </div>

        {{-- masthead --}}
        <div class="masthead">
            <div class="wrap">
                <div class="pub"><span class="mk">OB</span> {!! $c('chrome.pub', 'The Operator <b>Brief</b>') !!}</div>
                <div class="tag">{{ $c('chrome.masthead_tag') }}</div>
            </div>
        </div>

        <main class="doc">
            <div class="wrap">

                {{-- HERO + image 1 + first-viewport CTA (B: original bold direct-response copy) --}}
                <div class="eyebrow">Founder's Field Notes // Supplier Vetting</div>
                <h1>I ran 4 checks on every peptide supplier I could find. Almost all of them failed at least one.</h1>
                <p class="dek">Ten years vetting vendors for risk taught me the four that actually matter. Run them in 60 seconds below, then see the one supplier I quietly point people to.</p>

                @if($c('hero.image_url'))
                    <figure class="figframe">
                        <img class="doc-img" src="{{ $c('hero.image_url') }}" alt="{{ $c('hero.image_alt') }}" loading="eager">
                    </figure>
                @endif

                {{-- CTA #1: first viewport, anchor jump to #gate --}}
                <p style="margin:4px 0 0"><a class="jump" href="#gate">Show me the one supplier that passed all four</a></p>

                {{-- byline --}}
                <div class="byline">
                    <div class="av"></div>
                    <span>By <b>Professor Peps</b> · founder &amp; operator</span>
                    <span class="dot">·</span>
                    <span>a decade vetting vendors and supply chains for risk</span>
                    <span class="dot">·</span>
                    <span class="mono">3 min read</span>
                </div>

                {{-- lead + origin beat (B: hardcoded bold direct-response copy) --}}
                <div class="copy">
                    <p class="lead">When I moved into the research peptide space, I did not change my process. I vetted suppliers the same way I had vetted vendors for years: four checks, run in the same order, every single time. Fail one, and I am out. I do not care how good the price looks.</p>
                    <p>I learned to run them in that order the hard way. Early on I read a clean certificate of analysis, saw a tidy purity figure, and waved the vendor through. The paper looked perfect. The catch: it described a <strong>different batch than the one that actually showed up</strong>. Same supplier, same letterhead, a different lot number stamped on the vial. The certificate proved nothing about the thing in the box.</p>
                    <p>I was evaluating, not consuming, so nothing came of it. But it rewired how I check everything. Most suppliers look identical on the surface. Run the four checks, and only a handful hold up. <mark>Here is the exact framework, and then the short list it produced.</mark></p>
                </div>

                @if($c('intro.image_url'))
                    <figure class="figframe">
                        <img class="doc-img" src="{{ $c('intro.image_url') }}" alt="{{ $c('intro.image_alt') }}" loading="lazy">
                    </figure>
                @endif

                {{-- AT-A-GLANCE proof strip (B: hardcoded bold 4-check copy) + CTA #2 --}}
                <div class="glance">
                    <div class="gh">The 4 checks // at a glance</div>
                    <ol class="glist">
                        <li><b>Who signed the COA?</b> An independent third-party lab, or the supplier grading its own homework.</li>
                        <li><b>Is the purity number too clean?</b> Real chromatography lands on 98.4 percent, not a suspiciously tidy 99.</li>
                        <li><b>Does the vial's batch number match the COA?</b> A certificate for a different batch proves nothing about your vial.</li>
                        <li><b>Was this vendor here a year ago?</b> Track record is the one thing a brand-new name cannot fake.</li>
                    </ol>
                </div>
                <p style="margin:14px 0 0"><a class="jump" href="#gate">Skip the breakdown, show me who passed</a></p>

                {{-- FLAG CARDS (B: hardcoded bold copy; flag image accessors preserved by index) --}}
                <div class="flag">
                    <div class="flagk">Red Flag #1</div>
                    <h2>Who actually signed the certificate of analysis?</h2>
                    <div class="copy">
                        <p>The first thing I check is who issued the COA. If it came from the supplier itself, their letterhead, their stamp, that is not verification. <strong>That is homework graded by the student.</strong></p>
                        <p>A real COA comes from an independent third-party lab with a name, like Janoshik or Ascend. Anyone can print a document that says "99 percent pure." Only an outside lab makes that number mean a thing.</p>
                    </div>
                    @if(data_get($flags[0] ?? [], 'image_url'))
                        <figure class="figframe" style="margin-bottom:6px">
                            <img class="doc-img" src="{{ data_get($flags[0] ?? [], 'image_url') }}" alt="{{ data_get($flags[0] ?? [], 'image_alt') }}" loading="lazy">
                        </figure>
                    @endif
                    <a class="jump" href="#gate">Take me to the one that passed</a>
                </div>

                <div class="flag">
                    <div class="flagk">Red Flag #2</div>
                    <h2>Is the purity a suspiciously round number?</h2>
                    <div class="copy">
                        <p>Watch the number itself. If the COA says a flat 98 percent or 99 percent, I get suspicious. Real chromatography does not land on round numbers. It lands on 98.4, or 99.1, or 97.8.</p>
                        <p><strong>A clean, round figure usually means someone typed it, not measured it.</strong> The messy decimal is the one I trust.</p>
                    </div>
                    @if(data_get($flags[1] ?? [], 'image_url'))
                        <figure class="figframe" style="margin-bottom:6px">
                            <img class="doc-img" src="{{ data_get($flags[1] ?? [], 'image_url') }}" alt="{{ data_get($flags[1] ?? [], 'image_alt') }}" loading="lazy">
                        </figure>
                    @endif
                    <a class="jump" href="#gate">Take me to the one that passed</a>
                </div>

                <div class="flag">
                    <div class="flagk">Red Flag #3</div>
                    <h2>Does the batch number on the vial match the COA?</h2>
                    <div class="copy">
                        <p>This is the one that started everything for me. The COA is for batch #23-A488. The vial in the box reads #23-A491. Different batch. Which means the certificate you were handed proves nothing about the thing you actually received.</p>
                        <p><strong>A COA for a different batch is not a COA. It is a prop.</strong> Now it is the first match I make, every time, before anything else.</p>
                    </div>
                    @if(data_get($flags[2] ?? [], 'image_url'))
                        <figure class="figframe" style="margin-bottom:6px">
                            <img class="doc-img" src="{{ data_get($flags[2] ?? [], 'image_url') }}" alt="{{ data_get($flags[2] ?? [], 'image_alt') }}" loading="lazy">
                        </figure>
                    @endif
                    <a class="jump" href="#gate">Take me to the one that passed</a>
                </div>

                <div class="flag">
                    <div class="flagk">Red Flag #4</div>
                    <h2>Was this vendor here a year ago, and will they be here next year?</h2>
                    <div class="copy">
                        <p>The last check is not on the product. It is on the company. A wave of well-known names went dark between 2024 and 2025. Operators who had built on them were left stranded mid-cycle, scrambling for a new source.</p>
                        <p><strong>Track record is the one thing a new vendor cannot fake.</strong> If they were not around twelve months ago, I wait and watch.</p>
                    </div>
                    @if(data_get($flags[3] ?? [], 'image_url'))
                        <figure class="figframe" style="margin-bottom:6px">
                            <img class="doc-img" src="{{ data_get($flags[3] ?? [], 'image_url') }}" alt="{{ data_get($flags[3] ?? [], 'image_alt') }}" loading="lazy">
                        </figure>
                    @endif
                    <a class="jump" href="#gate">Take me to the one that passed</a>
                </div>

                {{-- REFRAME: it was never about price (B: hardcoded bold copy) --}}
                <div class="reframe">
                    <div class="rk">The real test</div>
                    <h2>Notice what is missing from that list: price.</h2>
                    <div class="copy">
                        <p>Not one of the four checks is about cost. The cheapest vendor fails most of them: no third-party lab, no batch lineage, gone within a year. So does the overpriced reseller three middlemen deep who never once touched the product either.</p>
                        <p>What the four flags really test is a single thing: <strong>is anyone actually accountable for what is in the bottle?</strong> That is not a price question. It is a trust question. The suppliers this framework approves are not the cheapest. They are the ones who can answer it.</p>
                    </div>
                </div>

                {{-- SURVIVOR checklist (B: hardcoded bold copy) --}}
                <p class="surv-h">The few that survived all four share the same four traits.</p>
                <div class="check">
                    <div class="crow"><span class="ic">✓</span><span>A <b>named third-party lab</b> on the COA, never a certificate the supplier issued to itself.</span></div>
                    <div class="crow"><span class="ic">✓</span><span><b>Specific purity numbers</b>, the messy decimal that gets measured, not the round one that gets typed.</span></div>
                    <div class="crow"><span class="ic">✓</span><span><b>Batch lineage that matches</b>, vial to COA, on every single order, no exceptions.</span></div>
                    <div class="crow"><span class="ic">✓</span><span><b>COAs issued recently and often</b>. The supplier I point people to refreshes theirs constantly.</span></div>
                </div>

                {{-- THE GATE — single outbound CTA, target/markup held constant --}}
                <div class="gate" id="gate">
                    <div class="vk">Verdict // the one that passed</div>
                    <h2>Start with the one I vetted quietly. The one that cleared all four.</h2>
                    <p class="gp">When people ask me where to actually start, I send them to a single supplier that passes every check: independent third-party lab, specific purity numbers, batch lineage that matches the vial, and a real track record. Confirm the context below and I will point you to it.</p>
                    {{-- Micro-commitment checkbox: same behavior as control. The CTA ticks
                         the box on the way out so no visitor is ever stuck on a dead button. --}}
                    <label class="chk">
                        <input type="checkbox" id="q">
                        <span>I am 18 or older, I am evaluating in a research / operator context, and I understand nothing here is medical, legal, financial, or business advice.</span>
                    </label>
                    <a href="{{ $go() }}" id="go" class="cta" rel="nofollow" onclick="document.getElementById('q').checked=true">Show me the supplier that passed →</a>
                    <div class="sub">Opens the supplier I recommend · biolinxlabs.com</div>
                </div>

                {{-- SOURCES & METHODOLOGY drop-downs --}}
                @if(count($dropdowns))
                    <div class="refs">
                        <div class="refs-h">{{ $c('refs.title') }}</div>
                        @foreach($dropdowns as $d)
                            <details class="drop">
                                <summary>{{ data_get($d, 'summary') }}</summary>
                                <div class="db">
                                    @if(data_get($d, 'intro'))<p>{{ data_get($d, 'intro') }}</p>@endif
                                    @php $items = data_get($d, 'items', []); @endphp
                                    @php $items = array_filter($items, fn($i) => trim((string) $i) !== ''); @endphp
                                    @if(count($items))
                                        <ol>
                                            @foreach($items as $item)
                                                <li>{!! $item !!}</li>
                                            @endforeach
                                        </ol>
                                    @endif
                                    @if(data_get($d, 'src'))<div class="src">{{ data_get($d, 'src') }}</div>@endif
                                </div>
                            </details>
                        @endforeach
                    </div>
                @endif

            </div>
        </main>

        <footer>
            <div class="wrap">
                <p class="disc">{!! $c('footer.disclaimer') !!}</p>
                <div class="flinks">{!! $c('footer.copyright', '© 2026 The Operator Brief') !!} · <a href="{{ route('privacy') }}">Privacy</a> · <a href="{{ route('terms') }}">Terms</a> · <a href="{{ route('disclaimer') }}">Research-Use Policy</a> · 18+</div>
            </div>
        </footer>

        {{-- STICKY VERDICT BAR — B signature; anchors to #gate (no new outbound target) --}}
        <div class="stickybar" id="stickybar">
            <div class="wrap">
                <div class="lbl">4 checks · 1 passed<b>See the supplier that cleared all four</b></div>
                <a class="sbtn" href="#gate">Show me who passed</a>
            </div>
        </div>
        {{-- Hide the sticky bar once the gate itself is on screen (avoids a redundant double CTA). --}}
        <script>
        (function(){
            var bar=document.getElementById('stickybar'), gate=document.getElementById('gate');
            if(!bar||!gate||!('IntersectionObserver' in window)) return;
            bar.style.transition='opacity .25s';
            new IntersectionObserver(function(es){
                es.forEach(function(e){ bar.style.opacity=e.isIntersecting?'0':'1'; bar.style.pointerEvents=e.isIntersecting?'none':'auto'; });
            },{threshold:0.15}).observe(gate);
        })();
        </script>

        @if($lander->c('giveaway_popup.enabled'))
            <x-giveaway-popup :lander="$lander" />
        @endif
    </body>
</html>
