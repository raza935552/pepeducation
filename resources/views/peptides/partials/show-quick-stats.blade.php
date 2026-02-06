<div class="card">
    <h3 class="section-heading">
        <span class="section-icon-sm bg-gradient-to-br from-gold-400 to-gold-600 shadow-gold-500/30">
            <svg aria-hidden="true" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
        </span>
        Quick Reference
    </h3>

    <dl class="space-y-3">
        @if($peptide->typical_dose)
        <div class="flex items-center justify-between py-2 border-b border-cream-200">
            <dt class="text-sm text-gray-500">Typical Dose</dt>
            <dd class="text-gray-900 font-semibold">{{ $peptide->typical_dose }}</dd>
        </div>
        @endif

        @if($peptide->dose_frequency)
        <div class="flex items-center justify-between py-2 border-b border-cream-200">
            <dt class="text-sm text-gray-500">Frequency</dt>
            <dd class="text-gray-900 font-semibold">{{ $peptide->dose_frequency }}</dd>
        </div>
        @endif

        @if($peptide->route)
        <div class="flex items-center justify-between py-2 border-b border-cream-200">
            <dt class="text-sm text-gray-500">Route</dt>
            <dd class="text-gray-900 font-semibold">{{ $peptide->route }}</dd>
        </div>
        @endif

        @if($peptide->injection_sites && count($peptide->injection_sites))
        <div class="py-2 border-b border-cream-200">
            <dt class="text-sm text-gray-500 mb-2">Injection Sites</dt>
            <dd class="flex flex-wrap gap-1.5">
                @foreach($peptide->injection_sites as $site)
                    <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium bg-gold-100 text-gold-700">
                        {{ $site }}
                    </span>
                @endforeach
            </dd>
        </div>
        @endif

        @if($peptide->cycle)
        <div class="flex items-center justify-between py-2 border-b border-cream-200">
            <dt class="text-sm text-gray-500">Typical Cycle</dt>
            <dd class="text-gray-900 font-semibold">{{ $peptide->cycle }}</dd>
        </div>
        @endif

        @if($peptide->storage)
        <div class="flex items-center justify-between py-2">
            <dt class="text-sm text-gray-500">Storage</dt>
            <dd class="text-gray-900 font-semibold">{{ $peptide->storage }}</dd>
        </div>
        @endif
    </dl>
</div>
