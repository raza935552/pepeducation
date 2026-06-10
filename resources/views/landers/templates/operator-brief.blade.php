{{--
    Operator Brief — editable CMS template (shared by the 5 legacy landers).
    Every text/image/UTM slot comes from $lander->content (fixed slot counts:
    4 flag-blocks, 4 checklist rows, 3 reference dropdowns) so marketing can edit
    anything via Admin → Landers without breaking the layout. CSS + chrome are fixed.
    Tracking is identical to every other lander: <x-meta-pixel/> + CTA → /go.
--}}
@php
    $c = fn($path, $default = '') => $lander->c($path, $default);
    $flags = $lander->c('flags', []);
    $checklist = $lander->c('closing.checklist', []);
    $dropdowns = $lander->c('refs.dropdowns', []);
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
        @verbatim<style>:root { --paper: #ffffff; --page: #f3f2ee; --ink: #1a1a1a; --ink-2: #3f3f3f; --ink-3: #6f6f6f; --faint: #9a9a9a; --rule: #e6e4dd; --rule-2: #d6d3ca; --go: #16734a; --go-dark: #115a3a; --go-soft: #e7f1ec; --warn: #b23b2e; --warn-soft: #fbece9; --hi: #fff6cf; --r: 8px; --r-sm: 5px; --col: 680px; } * { box-sizing: border-box; margin: 0; padding: 0; } html { scroll-behavior: smooth; } body { background: var(--page); color: var(--ink); font-family: -apple-system,"Segoe UI",Roboto,Helvetica,Arial,sans-serif; line-height: 1.62; font-size: 18px; -webkit-font-smoothing: antialiased; } /* ── top chrome ── */.notice { background: #26302b; color: #dfe7e2; text-align: center; font-size: 11.5px; letter-spacing: .16em; text-transform: uppercase; font-weight: 600; padding: 9px 16px; } .masthead { background: var(--paper); border-bottom: 1px solid var(--rule); padding: 14px 20px; text-align: center; } .masthead .pub { font-family: Georgia,"Times New Roman",serif; font-size: 21px; letter-spacing: -.01em; color: var(--ink); } .masthead .pub b { color: var(--go); } .masthead .tag { font-size: 10px; letter-spacing: .2em; text-transform: uppercase; color: var(--faint); margin-top: 3px; } /* ── article shell ── */.article { max-width: var(--col); margin: 0 auto; background: var(--paper); padding: 36px 20px 48px; } footer { max-width: var(--col); margin: 0 auto; background: var(--paper); border-top: 2px solid var(--rule-2); padding: 26px 20px 48px; } /* mobile: no side borders, tighter padding */@media (max-width:599px){ .article {  padding: 24px 18px 40px; }  footer {  padding: 22px 18px 40px; } } /* tablet 600–839px */@media (min-width:600px){ :root {  --col: 660px; }  .article {  padding: 38px 32px 52px;  border-left: 1px solid var(--rule);  border-right: 1px solid var(--rule); }  footer {  padding: 28px 32px 52px;  border-left: 1px solid var(--rule);  border-right: 1px solid var(--rule); } } /* small desktop 840–1099px */@media (min-width:840px){ :root {  --col: 780px; }  .article {  padding: 52px 72px 64px; }  footer {  padding: 32px 72px 60px; }  h1 {  font-size: 44px!important;  line-height: 1.12!important; }  .dek {  font-size: 21px!important; }  .lead {  font-size: 22px!important; }  .article p {  font-size: 19px; }  h2 {  font-size: 27px!important; }  .byline {  font-size: 14px; } } /* standard desktop 1100–1399px */@media (min-width:1100px){ :root {  --col: 880px; }  .article {  padding: 60px 88px 72px; }  footer {  padding: 36px 88px 64px; }  h1 {  font-size: 50px!important;  line-height: 1.1!important; }  .dek {  font-size: 22px!important; }  .lead {  font-size: 23px!important; }  .article p {  font-size: 19.5px!important; }  h2 {  font-size: 29px!important; }  .byline {  font-size: 14.5px; }  .masthead .pub {  font-size: 24px; } } /* wide desktop 1400px+ */@media (min-width:1400px){ :root {  --col: 960px; }  .article {  padding: 68px 100px 80px; }  footer {  padding: 40px 100px 72px; }  h1 {  font-size: 54px!important;  line-height: 1.09!important; }  .dek {  font-size: 23px!important; }  .lead {  font-size: 24px!important; }  .article p {  font-size: 20px!important; }  h2 {  font-size: 31px!important; }  .byline {  font-size: 15px; } } /* ── type ── */.eyebrow { font-size: 11px; letter-spacing: .16em; text-transform: uppercase; color: var(--go); font-weight: 700; margin-bottom: 14px; } h1 { font-family: Georgia,"Times New Roman",serif; font-size: 33px; line-height: 1.2; letter-spacing: -.01em; color: var(--ink); font-weight: 700; margin-bottom: 16px; } @media (max-width:599px){ h1 {  font-size: 26px; } } .dek { font-size: 19px; line-height: 1.5; color: var(--ink-2); margin-bottom: 22px; } .byline { display: flex; align-items: center; gap: 11px; flex-wrap: wrap; font-size: 13px; color: var(--ink-3); border-top: 1px solid var(--rule); border-bottom: 1px solid var(--rule); padding: 12px 0; margin-bottom: 0; } .byline .av { width: 38px; height: 38px; border-radius: 50%; background: linear-gradient(135deg,#3a3530,#16734a); flex-shrink: 0; } .byline b { color: var(--ink); } .byline .dot { color: var(--faint); } .article p { font-size: 18px; line-height: 1.68; color: var(--ink-2); margin-bottom: 18px; } .article p strong { color: var(--ink); font-weight: 700; } .lead { font-size: 20px!important; color: var(--ink)!important; } mark { background-color: transparent; background-image: linear-gradient(180deg,transparent 12%,#fbf0a3 12%,#fbf0a3 90%,transparent 90%); color: var(--ink); font-weight: 600; padding: 0 .1em; border-radius: 1px; -webkit-box-decoration-break: clone; box-decoration-break: clone; } h2 { font-family: Georgia,"Times New Roman",serif; font-size: 24px; line-height: 1.28; color: var(--ink); font-weight: 700; margin: 34px 0 6px; } /* ── editorial images ── */.article-img { display: block; width: 100%; height: auto; border-radius: var(--r); margin: 28px 0; border: 1px solid var(--rule-2); } @media (min-width:840px){ .article-img {  margin: 36px 0;  border-radius: 10px; } } /* ── flags / sections ── */.flagk { display: inline-flex; align-items: center; gap: 9px; font-size: 13px; letter-spacing: .1em; text-transform: uppercase; color: var(--warn); font-weight: 800; margin: 34px 0 6px; border: 1.5px solid var(--warn); background: var(--warn-soft); border-radius: 99px; padding: 7px 15px 7px 13px; } .flagk::before { content: "⚑"; font-size: 15px; line-height: 1; } .flag-block { border-top: 1px solid var(--rule-2); padding-top: 22px; margin-top: 26px; } .flag-block .flagk { margin: 0 0 12px; } .flag-block h2 { margin: 0 0 12px; } .flag-block p:last-child { margin-bottom: 0; } .skip { margin: 14px 0 0; font-size: 14px; font-weight: 700; } .skip a { color: var(--go); text-decoration: underline; text-underline-offset: 3px; text-decoration-thickness: 1px; } @media (hover:hover) and (pointer:fine){ .skip a:hover {  opacity: .65; }  .cta:hover {  transform: translateY(-1px);  box-shadow: 0 4px 10px rgba(22,115,74,.28); } } /* ── references / dropdowns ── */.refs { margin-top: 40px; padding-top: 22px; border-top: 1px solid var(--rule-2); } .refs-h { font-size: 11px; letter-spacing: .16em; text-transform: uppercase; color: var(--faint); font-weight: 700; margin-bottom: 14px; } .refs .drop { margin: 0 0 9px; background: #fbfbf9; } .refs .drop summary { padding: 11px 14px; font-size: 13.5px; font-weight: 600; color: var(--ink-2); } .refs .drop summary::after { font-size: 20px; } .refs .drop .db { padding: 0 14px 14px; } .refs .drop .db p { font-size: 13.5px; margin: 2px 0 10px; } .refs .drop ol li { font-size: 13.5px; padding: 9px 0 9px 36px; } .refs .drop ol li::before { font-size: 14px; } .refs .drop .src { font-size: 11.5px; } blockquote { border-left: 3px solid var(--go); background: var(--go-soft); padding: 14px 18px; border-radius: 0 var(--r-sm) var(--r-sm) 0; margin: 18px 0; font-size: 17px; color: var(--ink); font-style: italic; } .check { border: 1px solid var(--rule-2); border-radius: var(--r); overflow: hidden; margin: 18px 0 24px; } .check .crow { display: flex; gap: 11px; padding: 13px 16px; font-size: 15.5px; border-bottom: 1px solid var(--rule); align-items: flex-start; line-height: 1.5; } .check .crow:last-child { border-bottom: none; } .check .ic { flex-shrink: 0; width: 22px; height: 22px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 13px; font-weight: 800; color: #fff; margin-top: 1px; } .check .crow.y .ic { background: var(--go); } .check b { color: var(--ink); } .drop { border: 1px solid var(--rule-2); border-radius: var(--r); background: #fbfaf6; margin: 22px 0 24px; overflow: hidden; } .drop summary { cursor: pointer; list-style: none; padding: 16px 18px; font-weight: 700; color: var(--ink); font-size: 16.5px; display: flex; justify-content: space-between; align-items: center; gap: 12px; } .drop summary::-webkit-details-marker { display: none; } .drop summary::after { content: "+"; color: var(--go); font-weight: 800; font-size: 24px; line-height: 1; } .drop[open] summary::after { content: "–"; } .drop .db { padding: 0 18px 18px; } .drop .db p { font-size: 15.5px; line-height: 1.6; margin: 2px 0 12px; color: var(--ink-2); } .drop ol { margin: 0; padding: 0; list-style: none; counter-reset: s; } .drop ol li { position: relative; padding: 11px 0 11px 40px; border-top: 1px solid #efeadd; font-size: 15.5px; line-height: 1.5; color: var(--ink-2); } .drop ol li:first-child { border-top: none; } .drop ol li::before { counter-increment: s; content: counter(s); position: absolute; left: 9px; top: 11px; color: var(--go); font-weight: 800; font-size: 16px; } .drop ol li b { color: var(--ink); } .drop .src { font-size: 12px; color: var(--faint); margin-top: 14px; font-style: italic; line-height: 1.5; } /* ── CTA / gate ── */.cta-wrap { margin: 26px 0 30px; text-align: center; } .cta { display: block; background: var(--go); color: #fff; font-size: 18px; font-weight: 700; padding: 17px 24px; border: none; border-radius: var(--r); text-decoration: none; box-shadow: 0 2px 0 var(--go-dark); transition: transform .12s,box-shadow .12s; cursor: pointer; font-family: inherit; } .cta.locked { background: #b9c4bd; box-shadow: none; cursor: not-allowed; } .gate { background: #26302b; color: #e7ece9; border-radius: var(--r); padding: 30px 24px; margin: 30px 0 10px; text-align: center; } .gate h2 { color: #fff; margin-top: 0; } .gate p { color: #c3ccc6; font-size: 16px; margin-bottom: 18px; } .gate .chk { display: flex; gap: 11px; text-align: left; align-items: flex-start; background: rgba(255,255,255,.05); border: 1px solid rgba(255,255,255,.12); border-radius: var(--r-sm); padding: 13px 14px; margin-bottom: 18px; font-size: 13.5px; line-height: 1.5; color: #d8ded9; cursor: pointer; } .gate .chk input { width: 19px; height: 19px; flex-shrink: 0; margin-top: 1px; accent-color: var(--go); } /* ── footer ── */.disc { font-size: 12.5px; color: var(--ink-3); line-height: 1.65; margin-bottom: 14px; } .disc b { color: var(--ink-2); } .flinks { font-size: 12px; color: var(--faint); } .flinks a { color: var(--ink-3); margin: 0 7px; } /* ── age gate ── */.age { position: fixed; inset: 0; background: rgba(20,22,20,.97); z-index: 200; display: flex; align-items: center; justify-content: center; padding: 24px; } .age.hidden { display: none; } .age .card { background: #fff; border-radius: var(--r); max-width: 420px; padding: 34px 30px; text-align: center; } .age .card .b { font-family: Georgia,serif; font-size: 22px; margin-bottom: 8px; } .age .card .b b { color: var(--go); } .age .card p { font-size: 14px; color: var(--ink-2); margin-bottom: 20px; line-height: 1.55; } .age .row { display: flex; gap: 10px; justify-content: center; } .age button { font-family: inherit; font-size: 14px; font-weight: 700; padding: 11px 22px; border-radius: var(--r-sm); cursor: pointer; border: 1px solid var(--rule-2); } .age .yes { background: var(--go); color: #fff; border-color: var(--go); } .age .no { background: #fff; color: var(--ink-3); } /* ── desktop: subtle page-level polish ── */@media (min-width:840px){ body {  background: #eae8e2; }  .article, footer {  box-shadow: 0 1px 3px rgba(0,0,0,.07),0 4px 24px rgba(0,0,0,.05); }  .article {  border-radius: var(--r) var(--r) 0 0;  margin-top: 32px; }  footer {  border-radius: 0 0 var(--r) var(--r);  margin-bottom: 48px; }  .notice {  border-radius: 0; } }</style>@endverbatim
    <x-meta-pixel />

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
        <div class="notice">{{ $c('chrome.notice') }}</div>
        <div class="masthead">
            <div class="pub">{!! $c('chrome.pub', 'The Operator <b>Brief</b>') !!}</div>
            <div class="tag">{{ $c('chrome.masthead_tag') }}</div>
        </div>
        <article class="article">
            <div class="eyebrow">{{ $c('hero.eyebrow') }}</div>
            <h1>{{ $c('hero.h1') }}</h1>
            <p class="dek">{!! $c('hero.dek') !!}</p>
            @if($c('hero.image_url'))
                <img class="article-img" src="{{ $c('hero.image_url') }}" alt="{{ $c('hero.image_alt') }}" loading="eager">
            @endif
            <p class="skip"><a href="#gate">{{ $c('hero.skip_text', 'Take me to the recommended supplier →') }}</a></p>
            <div class="byline">
                <div class="av"></div>
                <span>By <b>{{ $c('byline.author', 'Professor Peps') }}</b> · {{ $c('byline.role') }}</span>
                <span class="dot">·</span>
                <span>{{ $c('byline.cred') }}</span>
                <span class="dot">·</span>
                <span>{{ $c('byline.read_time', '5 min read') }}</span>
            </div>
            <p class="lead">{!! $c('intro.lead') !!}</p>
            {!! $c('intro.body') !!}
            @if($c('intro.image_url'))
                <img class="article-img" src="{{ $c('intro.image_url') }}" alt="{{ $c('intro.image_alt') }}" loading="lazy">
            @endif
            <p class="skip"><a href="#gate">{{ $c('hero.skip_text', 'Take me to the recommended supplier →') }}</a></p>

            @foreach($flags as $flag)
                <div class="flag-block">
                    <div class="flagk">{{ data_get($flag, 'label') }}</div>
                    <h2>{{ data_get($flag, 'heading') }}</h2>
                    {!! data_get($flag, 'body') !!}
                    @if(data_get($flag, 'image_url'))
                        <img class="article-img" src="{{ data_get($flag, 'image_url') }}" alt="{{ data_get($flag, 'image_alt') }}" loading="lazy">
                    @endif
                    <p class="skip"><a href="#gate">{{ $c('hero.skip_text', 'Take me to the recommended supplier →') }}</a></p>
                </div>
            @endforeach

            <h2>{{ $c('closing.heading') }}</h2>
            {!! $c('closing.body') !!}
            @if($c('closing.checklist_intro'))
                <p>{{ $c('closing.checklist_intro') }}</p>
            @endif
            @if(count($checklist))
                <div class="check">
                    @foreach($checklist as $row)
                        @if(trim((string) $row) !== '')
                            <div class="crow y"><span class="ic">✓</span><span>{!! $row !!}</span></div>
                        @endif
                    @endforeach
                </div>
            @endif

            <div class="gate" id="gate">
                <h2>{!! $c('gate.heading') !!}</h2>
                <p>{{ $c('gate.body') }}</p>
                {{-- Micro-commitment checkbox: kept for the editorial "confirm the
                     context" beat, but it never blocks the click — the CTA is always
                     live and ticks the box on the way out, so no visitor is ever
                     stuck on a dead button. --}}
                <label class="chk">
                    <input type="checkbox" id="q">
                    <span>{{ $c('gate.consent') }}</span>
                </label>
                <a href="{{ route('outbound.track', $lander->outbound_slug) }}" id="go" class="cta" rel="nofollow" onclick="document.getElementById('q').checked=true">{{ $c('gate.cta', 'Take me to the recommended supplier →') }}</a>
                <div class="cta-sub" style="color:#9fb0a7;margin-top:10px">{{ $c('gate.sub') }}</div>
            </div>

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
        </article>
        <footer>
            <p class="disc">{!! $c('footer.disclaimer') !!}</p>
            <div class="flinks">{!! $c('footer.copyright', '© 2026 The Operator Brief') !!} · <a href="{{ route('privacy') }}">Privacy</a> · <a href="{{ route('terms') }}">Terms</a> · <a href="{{ route('disclaimer') }}">Research-Use Policy</a> · 18+</div>
        </footer>
    </body>
</html>
