{{-- The "corner card": who is vouching, the disclosed deal, and the code. Used twice by
     the affiliate-guide template (inline on mobile, sticky aside on desktop). --}}
<div class="corner">
  <span class="tape">{{ $tape ?? 'From your corner' }}</span>
  <div class="corner-head">
    @if(!empty($aff['photo']))
      <img class="corner-photo" src="{{ $aff['photo'] }}" alt="{{ $aff['name'] }}" width="64" height="64" loading="lazy">
    @else
      <span class="corner-photo ph" aria-hidden="true"><svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><circle cx="12" cy="8" r="4"/><path d="M4 21c0-4 3.6-7 8-7s8 3 8 7"/></svg></span>
    @endif
    <div>
      <div class="corner-name">{{ $aff['name'] }}</div>
      <div class="corner-role">{{ $aff['role'] }}</div>
    </div>
  </div>
  <p class="corner-note">{{ $fill($aff['note']) }}</p>
  <div class="corner-code">
    <div><div class="lab">{{ $aff['discount'] }} off · code</div><div class="val">{{ $aff['code'] }}</div></div>
    <button type="button" class="btn ink sm" data-open-modal data-placement="corner-card">Reveal</button>
  </div>
  <p class="corner-disc">{{ $fill($aff['first'] . ' is paid a commission by BioLinx Labs on orders placed with this code. Research use only.') }}</p>
</div>
