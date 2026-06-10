{{--
    Giveaway email-capture popup for paid-ad LANDERS.

    Self-contained (own markup + scoped CSS + vanilla JS — no Alpine/Livewire), so it
    drops into any standalone lander template. Gated per-lander via the content flag
    $lander->c('giveaway_popup.enabled'), so it only appears where marketing turns it on
    (currently /lp/hunger-fullness) and inherits automatically on future landers.

    Submitting the email POSTs to /subscriber/sync (route subscriber.sync — CSRF-exempt),
    which runs SubscriberService::subscribe() → identifies the person in Customer.io,
    sets subscribed=true, and fires the "Subscribed" event. The giveaway is distinguished
    by source = "giveaway:{lander-slug}" so it can be segmented in Customer.io.

    All copy/colour/timing are editable in Admin → Marketing → Landers (the
    "Giveaway popup" card on the research-confidence editor); sensible defaults below.
--}}
@props(['lander' => null])
@php
    $slug = $lander?->slug ?? (request()->segment(2) ?: 'lander');
    // pull a content value, falling back to the default when the admin field is blank
    $cfg = fn (string $k, string $d = '') => ($lander && filled($lander->c("giveaway_popup.$k"))) ? $lander->c("giveaway_popup.$k") : $d;

    $tag         = $cfg('tag', 'Giveaway');
    $headline    = $cfg('headline', 'Win a free research-use-only peptide.');
    $subhead     = $cfg('subhead', 'One winner picked each month.');
    $btn         = $cfg('button_text', 'Enter Now');
    $placeholder = $cfg('placeholder', 'Email address');
    $sTitle      = $cfg('success_title', "You're in.");
    $sBody       = $cfg('success_body', "We'll reach out if you win. A new winner is chosen each month.");
    $fine        = $cfg('fine_print', 'Research use only · Not for human consumption · No purchase necessary');
    $decline     = $cfg('decline_text', 'No thanks');
    $accent      = $cfg('accent', '#da3f76');
    $delay       = (int) ($cfg('delay_seconds', '6') ?: 6);

    $variant  = session('lander_variant');
    $source   = 'giveaway:' . $slug . ($variant ? ':' . $variant : '');
    $endpoint = route('subscriber.sync');
    $skey     = 'ppgv_' . preg_replace('/[^a-z0-9]+/i', '_', $slug);
@endphp

<div class="ppgv-overlay" id="ppgv-overlay" role="dialog" aria-modal="true" aria-label="{{ $headline }}"
     style="--ppgv-accent: {{ $accent }};">
  <div class="ppgv-card">
    {{-- Form state --}}
    <div id="ppgv-form">
      <button class="ppgv-x" type="button" aria-label="Close">&times;</button>
      <p class="ppgv-tag">{{ $tag }}</p>
      <h2 class="ppgv-title">{{ $headline }}</h2>
      <p class="ppgv-sub">{{ $subhead }}</p>
      <input type="email" id="ppgv-email" class="ppgv-input" placeholder="{{ $placeholder }}"
             autocomplete="email" inputmode="email" />
      <p class="ppgv-error" id="ppgv-error" hidden>Please enter a valid email.</p>
      <button class="ppgv-enter" type="button" id="ppgv-submit">{{ $btn }}</button>
      <p class="ppgv-fine">{{ $fine }}</p>
      <button class="ppgv-skip" type="button" id="ppgv-skip">{{ $decline }}</button>
    </div>
    {{-- Success state --}}
    <div id="ppgv-success" hidden>
      <p class="ppgv-tag">You're in</p>
      <h2 class="ppgv-title">{{ $sTitle }}</h2>
      <p class="ppgv-sub">{{ $sBody }}</p>
    </div>
  </div>
</div>

@verbatim<style>
  .ppgv-overlay{position:fixed;inset:0;z-index:9999;display:none;align-items:center;justify-content:center;padding:20px;background:rgba(7,29,58,.55);-webkit-backdrop-filter:blur(2px);backdrop-filter:blur(2px);font-family:Inter,system-ui,-apple-system,Segoe UI,sans-serif}
  .ppgv-card{position:relative;width:100%;max-width:380px;background:#fff;border:1px solid #eef0f5;border-radius:20px;padding:38px 32px 26px;text-align:center;box-shadow:0 30px 90px rgba(7,29,58,.28);animation:ppgv-pop .22s ease}
  @keyframes ppgv-pop{from{opacity:0;transform:translateY(10px) scale(.98)}to{opacity:1;transform:none}}
  .ppgv-x{position:absolute;top:12px;right:14px;background:none;border:0;font-size:24px;line-height:1;color:#c2c8d4;cursor:pointer;padding:2px 6px}
  .ppgv-x:hover{color:#475067}
  .ppgv-tag{display:inline-block;font-size:10px;font-weight:800;letter-spacing:.18em;text-transform:uppercase;color:var(--ppgv-accent);margin:0 0 14px}
  .ppgv-title{font-family:'Playfair Display','DM Serif Display',Georgia,serif;font-size:29px;font-weight:800;line-height:1.15;letter-spacing:-.02em;color:#071d3a;margin:0 0 9px}
  .ppgv-sub{font-size:13.5px;color:#7b8396;line-height:1.55;margin:0 0 22px}
  .ppgv-input{display:block;width:100%;padding:13px 14px;border:2px solid #d7dbe4;border-radius:11px;font-size:14px;color:#071d3a;background:#fff;outline:none;margin-bottom:10px;font-family:inherit}
  .ppgv-input:focus{border-color:var(--ppgv-accent)}
  .ppgv-input::placeholder{color:#aab1bf}
  .ppgv-error{color:var(--ppgv-accent);font-size:12px;font-weight:600;margin:0 0 10px;text-align:left}
  .ppgv-enter{display:block;width:100%;padding:14px;background:#071d3a;color:#fff;border:0;border-radius:11px;font-size:15px;font-weight:800;cursor:pointer;font-family:inherit;transition:background .15s ease}
  .ppgv-enter:hover{background:var(--ppgv-accent)}
  .ppgv-enter:disabled{opacity:.65;cursor:default}
  .ppgv-fine{font-size:10.5px;color:#b7bdc9;margin:14px 0 0;line-height:1.6}
  .ppgv-skip{display:block;width:100%;background:none;border:0;font-size:11.5px;color:#aab1bf;text-decoration:underline;cursor:pointer;margin-top:10px;font-family:inherit}
  .ppgv-skip:hover{color:#5a6378}
  @media(max-width:420px){.ppgv-card{padding:32px 22px 22px}.ppgv-title{font-size:25px}}
</style>@endverbatim

<script>
(function () {
  var root = document.getElementById('ppgv-overlay');
  if (!root) return;

  var skey     = @json($skey);
  var delayMs  = @json($delay) * 1000;
  var endpoint = @json($endpoint);
  var source   = @json($source);
  var landerSlug = @json($slug);
  var btnLabel = @json($btn);
  var shown = false, submitting = false;

  function lsGet(k){ try { return window.localStorage.getItem(k); } catch (e) { return null; } }
  function lsSet(k, v){ try { window.localStorage.setItem(k, v); } catch (e) {} }

  var force = false;
  try { force = new URLSearchParams(location.search).get('giveaway') === 'force'; } catch (e) {}

  function suppressed(){
    if (force) return false;
    if (lsGet(skey + '_done')) return true;                       // already entered → never show again
    var d = lsGet(skey + '_dismiss');
    if (d && (Date.now() - parseInt(d, 10)) < 7 * 24 * 60 * 60 * 1000) return true; // dismissed < 7 days ago
    return false;
  }

  function open(){
    if (shown || suppressed()) return;
    shown = true;
    root.style.display = 'flex';
    document.body.style.overflow = 'hidden';
    var email = document.getElementById('ppgv-email');
    setTimeout(function (){ try { email && email.focus(); } catch (e) {} }, 60);
    try { if (window.posthog) window.posthog.capture('giveaway_popup_shown', { lander: landerSlug }); } catch (e) {}
  }

  function close(dismiss){
    root.style.display = 'none';
    document.body.style.overflow = '';
    if (dismiss) lsSet(skey + '_dismiss', String(Date.now()));
  }

  function validEmail(v){ return /^[^@\s]+@[^@\s]+\.[^@\s]+$/.test(v); }

  function submit(){
    if (submitting) return;
    var input = document.getElementById('ppgv-email');
    var err   = document.getElementById('ppgv-error');
    var btn   = document.getElementById('ppgv-submit');
    var email = (input.value || '').trim();

    if (!validEmail(email)) { err.textContent = 'Please enter a valid email.'; err.hidden = false; input.focus(); return; }

    err.hidden = true; submitting = true; btn.disabled = true; btn.textContent = 'Entering…';

    var headers = { 'Content-Type': 'application/json', 'Accept': 'application/json' };
    var meta = document.querySelector('meta[name="csrf-token"]');
    if (meta) headers['X-CSRF-TOKEN'] = meta.content; // route is CSRF-exempt; sent only if a token exists

    fetch(endpoint, {
      method: 'POST', headers: headers, credentials: 'same-origin',
      body: JSON.stringify({ email: email, source: source })
    })
    .then(function (r){ return r.ok ? r.json().catch(function(){ return { ok: true }; }) : Promise.reject(r); })
    .then(function (){
      lsSet(skey + '_done', '1');
      document.getElementById('ppgv-form').hidden = true;
      document.getElementById('ppgv-success').hidden = false;
      try { if (window.posthog) window.posthog.capture('giveaway_entered', { lander: landerSlug, email_domain: (email.split('@')[1] || '') }); } catch (e) {}
      try { if (window.fbq) window.fbq('track', 'Lead', { content_name: 'giveaway', content_category: landerSlug }); } catch (e) {}
      setTimeout(function (){ close(false); }, 4000);
    })
    .catch(function (){
      submitting = false; btn.disabled = false; btn.textContent = btnLabel;
      err.textContent = 'Something went wrong - please try again.'; err.hidden = false;
    });
  }

  root.querySelector('.ppgv-x').addEventListener('click', function (){ close(true); });
  document.getElementById('ppgv-skip').addEventListener('click', function (){ close(true); });
  document.getElementById('ppgv-submit').addEventListener('click', submit);
  document.getElementById('ppgv-email').addEventListener('keydown', function (e){ if (e.key === 'Enter') { e.preventDefault(); submit(); } });
  root.addEventListener('click', function (e){ if (e.target === root) close(true); });
  document.addEventListener('keydown', function (e){ if (e.key === 'Escape' && root.style.display === 'flex') close(true); });

  if (suppressed()) return;
  if (force) { open(); return; }

  setTimeout(open, delayMs);
  // Exit-intent (desktop): cursor leaves through the top of the viewport
  document.addEventListener('mouseout', function (e){ if (!e.relatedTarget && e.clientY <= 0) open(); });
})();
</script>
