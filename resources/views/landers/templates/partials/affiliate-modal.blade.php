{{-- Shared code modal + tracking script for the affiliate-guide templates (A and B).
     Expects: $modal, $aff, $bl, $store, $trackSlug. --}}
{{-- modal --}}
<div class="ov" id="agModal" aria-hidden="true">
  <div class="md" role="dialog" aria-modal="true" aria-labelledby="agTitle">
    <button class="md-close" type="button" data-close-modal aria-label="Close">&times;</button>

    <div data-step="1">
      <p class="step-lab">Step 1 of 2</p>
      <h3 id="agTitle">{{ $modal['title'] }}</h3>
      <p class="sub">{{ $modal['sub'] }}</p>
      <p style="margin-top:16px;font-weight:700">{{ $modal['q'] }}</p>
      <div class="opts">
        @foreach($modal['options'] as $o)
        <button type="button" class="opt" data-dest="{{ $bl($o['url'] ?? $store, !empty($o['auto_add'])) }}" data-label="{{ $o['label'] ?? '' }}">
          <span><b>{{ $o['label'] ?? '' }}</b><span>{{ $o['sub'] ?? '' }}</span></span><i>&rarr;</i>
        </button>
        @endforeach
      </div>
    </div>

    <div data-step="2" hidden>
      <p class="step-lab">Step 2 of 2</p>
      <h3>Where should we send it?</h3>
      <p class="sub">Your code shows on screen right after this. Email is only for follow-ups from Professor Peptides.</p>
      <form id="agForm" novalidate>
        <div class="fld"><label for="agEmail">Email</label><input type="email" id="agEmail" name="email" autocomplete="email" inputmode="email" required placeholder="you@email.com"></div>
        <div class="fld"><label for="agPhone">Phone (optional)</label><input type="tel" id="agPhone" name="phone" autocomplete="tel" inputmode="tel" placeholder="For order help only"></div>
        <button class="btn primary" type="submit" id="agSubmit">Show my code</button>
        <p class="fine">By continuing you agree to receive the code and occasional research-education emails from Professor Peptides. Unsubscribe any time. No spam, no selling your data.</p>
      </form>
    </div>

    <div data-step="3" hidden>
      <p class="step-lab">Done</p>
      <h3>{{ $modal['success_title'] }}</h3>
      <p class="sub">{{ $modal['success_body'] }}</p>
      <div class="code-box"><span class="val" id="agCode">{{ $aff['code'] }}</span><button type="button" class="cp" id="agCopy">Copy</button></div>
      <a class="btn ink" id="agGo" href="{{ $bl($store . '/best-sellers') }}">Open BioLinx with {{ $aff['discount'] }} applied</a>
      <p class="fine">Research use only. 21+ and a research-use agreement are required at checkout.</p>
    </div>
  </div>
</div>

@verbatim<script>
(function(){
  var slug = @endverbatim'{{ $trackSlug }}'@verbatim, code = @endverbatim'{{ $aff['code'] }}'@verbatim;
  var LS = 'pp_ag_' + slug;
  var modal = document.getElementById('agModal');
  var chosen = { dest: document.getElementById('agGo').getAttribute('href'), label: 'default' };
  var placement = 'unknown';

  function ph(ev, props){ if(window.posthog){ try{ posthog.capture(ev, Object.assign({lander: slug}, props||{})); }catch(e){} } }
  function step(n){ modal.querySelectorAll('[data-step]').forEach(function(s){ s.hidden = String(s.dataset.step)!==String(n); }); }
  function open(p){
    placement = p || placement;
    modal.classList.add('is-open'); modal.setAttribute('aria-hidden','false'); document.body.classList.add('pp-modal-open');
    var st = 1; try{ if(localStorage.getItem(LS)==='done') st = 3; }catch(e){}
    step(st);
    ph('modal_opened', {placement: placement});
  }
  function close(){ modal.classList.remove('is-open'); modal.setAttribute('aria-hidden','true'); document.body.classList.remove('pp-modal-open'); try{ if(localStorage.getItem(LS)!=='done') localStorage.setItem(LS,'dismissed'); }catch(e){} }

  document.querySelectorAll('[data-open-modal]').forEach(function(b){ b.addEventListener('click', function(){ open(b.dataset.placement); }); });
  document.querySelectorAll('[data-close-modal]').forEach(function(b){ b.addEventListener('click', close); });
  modal.addEventListener('click', function(e){ if(e.target===modal) close(); });
  document.addEventListener('keydown', function(e){ if(e.key==='Escape' && modal.classList.contains('is-open')) close(); });

  modal.querySelectorAll('.opt').forEach(function(o){
    o.addEventListener('click', function(){
      modal.querySelectorAll('.opt').forEach(function(x){ x.classList.remove('on'); }); o.classList.add('on');
      chosen = { dest: o.dataset.dest, label: o.dataset.label };
      document.getElementById('agGo').setAttribute('href', chosen.dest);
      ph('quiz_answered', {answer: chosen.label});
      step(2); setTimeout(function(){ var e=document.getElementById('agEmail'); if(e) e.focus(); }, 60);
    });
  });

  document.getElementById('agForm').addEventListener('submit', function(e){
    e.preventDefault();
    var email = document.getElementById('agEmail'), phone = document.getElementById('agPhone');
    if(!email.value || !email.checkValidity()){ email.reportValidity(); return; }
    var btn = document.getElementById('agSubmit'); btn.disabled = true; btn.textContent = 'One second…';
    var leadId = 'lead_' + Date.now() + '_' + Math.random().toString(36).slice(2,10);
    var meta = document.querySelector('meta[name="csrf-token"]'), csrf = meta ? meta.getAttribute('content') : '';
    if(window.fbq){ try{ fbq('track','Lead',{content_name: slug},{eventID: leadId}); }catch(err){} }
    ph('lead_submitted', {placement: placement, answer: chosen.label});
    var done = function(){
      try{ localStorage.setItem(LS,'done'); }catch(err){}
      document.querySelectorAll('.corner-code').forEach(function(c){ c.classList.add('revealed'); });
      step(3); btn.disabled = false; btn.textContent = 'Show my code';
      ph('code_revealed', {code: code});
    };
    try{
      fetch('/subscriber/sync', {method:'POST', headers:{'Content-Type':'application/json','X-CSRF-TOKEN':csrf,'Accept':'application/json'}, credentials:'same-origin',
        body: JSON.stringify({email: email.value, phone: phone.value || '', source: 'lp-' + slug + '-code-modal', lead_event_id: leadId, quiz_answer: chosen.label, affiliate_code: code})
      }).then(done).catch(done);
    }catch(err){ done(); }
  });

  document.getElementById('agCopy').addEventListener('click', function(){
    var b = this; try{ navigator.clipboard.writeText(code); }catch(e){}
    b.textContent = 'Copied'; setTimeout(function(){ b.textContent = 'Copy'; }, 1500);
  });
  document.getElementById('agGo').addEventListener('click', function(){ ph('go_clicked', {answer: chosen.label, placement: 'modal'}); });

  // Outbound click tracking on compound + kit links
  document.querySelectorAll('[data-track]').forEach(function(a){ a.addEventListener('click', function(){ ph('outbound_clicked', {type: a.dataset.track, name: a.dataset.name}); }); });

  // Corner-card "reveal" buttons open the modal; already-converted visitors see the code directly
  try{ if(localStorage.getItem(LS)==='done'){ document.querySelectorAll('.corner-code').forEach(function(c){ c.classList.add('revealed'); }); } }catch(e){}

  // Soft triggers: once per visitor, 55% scroll (all devices) or exit intent (desktop). Never if dismissed or done.
  var auto = false;
  function maybeAuto(p){ if(auto) return; try{ var s = localStorage.getItem(LS); if(s==='done'||s==='dismissed') return; }catch(e){} if(modal.classList.contains('is-open')) return; auto = true; open(p); }
  window.addEventListener('scroll', function(){ var d = (window.scrollY + window.innerHeight) / document.documentElement.scrollHeight; if(d > 0.55) maybeAuto('scroll-55'); }, {passive:true});
  document.addEventListener('mouseout', function(e){ if(!e.relatedTarget && e.clientY < 8 && window.innerWidth > 900) maybeAuto('exit-intent'); });

  // Scroll reveals (respect reduced motion via CSS)
  var rvs = Array.prototype.slice.call(document.querySelectorAll('.rv'));
  var showAll = function(){ rvs.forEach(function(el){ el.classList.add('in'); }); };
  if('IntersectionObserver' in window){
    var io = new IntersectionObserver(function(es){ es.forEach(function(en){ if(en.isIntersecting){ en.target.classList.add('in'); io.unobserve(en.target); } }); }, {rootMargin:'0px 0px 12% 0px', threshold: 0.01});
    rvs.forEach(function(el){ io.observe(el); });
    // Safety net: nothing on this page may stay invisible because an observer never fired.
    setTimeout(showAll, 2500);
  } else { showAll(); }
})();
</script>@endverbatim
