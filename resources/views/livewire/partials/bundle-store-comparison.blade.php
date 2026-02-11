@php
    $bundleStores = $bundle->stores->where('pivot.is_in_stock', true);
    $hasMultipleStores = $bundleStores->count() > 1;
    $cheapestPrice = $hasMultipleStores ? $bundleStores->min('pivot.price') : null;
@endphp

@if($bundleStores->count() > 0)
    <div class="space-y-1.5">
        @foreach($bundleStores as $store)
            @php
                $isCheapest = $hasMultipleStores && (float) $store->pivot->price === (float) $cheapestPrice;
                $isRecommended = $store->pivot->is_recommended !== null ? (bool) $store->pivot->is_recommended : (bool) $store->is_recommended;
                $visitUrl = '#';
                if ($store->pivot->outbound_link_id) {
                    $outboundLink = \App\Models\OutboundLink::find($store->pivot->outbound_link_id);
                    if ($outboundLink) {
                        $visitUrl = $outboundLink->getTrackingUrl();
                    }
                } elseif ($store->pivot->url) {
                    $visitUrl = $store->pivot->url;
                }

                // Row styling priority: cheapest (green) > recommended (amber) > default
                $rowClass = 'bg-cream-50 border border-cream-200 hover:border-cream-300 hover:shadow-sm';
                if ($isCheapest) {
                    $rowClass = 'bg-green-50 border border-green-200 hover:border-green-300 hover:shadow-sm';
                } elseif ($isRecommended) {
                    $rowClass = 'bg-amber-50 border border-amber-200 hover:border-amber-300 hover:shadow-sm';
                }

                $nameClass = $isCheapest ? 'text-green-800' : ($isRecommended ? 'text-amber-800' : 'text-gray-700');
                $priceClass = $isCheapest ? 'text-green-700' : 'text-gray-900';
                $iconBgClass = $isCheapest ? 'bg-green-100 text-green-600' : ($isRecommended ? 'bg-amber-100 text-amber-600' : 'bg-cream-200 text-cream-500');
                $btnClass = $isCheapest ? 'bg-green-600 text-white group-hover:bg-green-700' : ($isRecommended ? 'bg-amber-500 text-white group-hover:bg-amber-600' : 'bg-gold-500 text-white group-hover:bg-gold-600');
            @endphp
            <a href="{{ $visitUrl }}" target="_blank" rel="noopener noreferrer"
               class="group flex items-center gap-2 px-2.5 py-2 rounded-lg transition-all duration-200 {{ $rowClass }}">
                {{-- Store icon --}}
                @if($store->logo)
                    <img src="{{ Storage::url($store->logo) }}" alt="{{ $store->name }}" class="w-5 h-5 object-contain rounded flex-shrink-0 hidden sm:block">
                @else
                    <div class="w-5 h-5 rounded flex-shrink-0 items-center justify-center hidden sm:flex {{ $iconBgClass }}">
                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/></svg>
                    </div>
                @endif

                {{-- Store name --}}
                <span class="text-xs font-semibold truncate min-w-0 flex-1 {{ $nameClass }}">{{ $store->name }}</span>

                {{-- Best badge --}}
                @if($isCheapest)
                    <span class="px-1.5 py-0.5 bg-green-100 text-green-700 text-[9px] font-bold rounded uppercase tracking-wide flex-shrink-0">Best</span>
                @endif

                {{-- Recommended badge --}}
                @if($isRecommended)
                    <span class="px-1.5 py-0.5 bg-amber-100 text-amber-700 text-[9px] font-bold rounded uppercase tracking-wide flex-shrink-0">Recommended</span>
                @endif

                {{-- Price --}}
                <span class="text-sm font-bold tabular-nums flex-shrink-0 {{ $priceClass }}">${{ number_format($store->pivot->price, 2) }}</span>

                {{-- Visit button --}}
                <span class="inline-flex items-center gap-0.5 px-2.5 py-1 rounded-md text-[11px] font-bold flex-shrink-0 transition-all duration-200 {{ $btnClass }}">
                    Visit
                    <svg class="w-2.5 h-2.5 transition-transform group-hover:translate-x-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
                </span>
            </a>
        @endforeach
    </div>
@elseif($bundle->outbound_link_id || $bundle->external_url)
    {{-- Legacy fallback: single link from bundle itself --}}
    @php
        $legacyUrl = '#';
        if ($bundle->outbound_link_id && $bundle->outboundLink) {
            $legacyUrl = $bundle->outboundLink->getTrackingUrl();
        } elseif ($bundle->external_url) {
            $legacyUrl = $bundle->external_url;
        }
    @endphp
    <a href="{{ $legacyUrl }}" target="_blank" rel="noopener noreferrer"
       class="group flex items-center justify-between gap-2 px-2.5 py-2 rounded-lg bg-cream-50 border border-cream-200 hover:border-cream-300 hover:shadow-sm transition-all duration-200">
        <span class="text-sm font-bold text-gray-900">${{ number_format($bundle->bundle_price, 2) }}</span>
        <span class="inline-flex items-center gap-0.5 px-2.5 py-1 rounded-md text-[11px] font-bold bg-gold-500 text-white group-hover:bg-gold-600 transition-all duration-200">
            Visit Store
            <svg class="w-2.5 h-2.5 transition-transform group-hover:translate-x-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
        </span>
    </a>
@else
    <div class="p-3 rounded-xl bg-cream-50 border border-cream-200 text-center">
        <span class="text-sm text-gray-400">Store links coming soon</span>
    </div>
@endif
