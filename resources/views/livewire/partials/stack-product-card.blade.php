<div class="relative rounded-2xl border-2 transition-all duration-300 flex flex-col group/card {{ $product->is_featured ? 'border-gold-500/40 bg-white hover:shadow-lg hover:shadow-gold-500/10' : 'border-cream-200 bg-white hover:border-cream-300 hover:shadow-lg hover:shadow-cream-500/10' }}"
     wire:key="product-card-{{ $product->id }}">

    {{-- Featured Badge --}}
    @if($product->is_featured)
        <div class="absolute top-3 left-3 z-10 px-2.5 py-1 bg-gold-500 text-white text-xs font-bold rounded-full shadow-sm">
            Professor's Pick
        </div>
    @endif

    {{-- Image / Placeholder --}}
    @if($product->image)
        <div class="relative overflow-hidden rounded-t-2xl">
            <img src="{{ Storage::url($product->image) }}" alt="{{ $product->name }}" class="w-full h-44 object-cover transition-transform duration-500 group-hover/card:scale-105">
            <div class="absolute inset-0 bg-gradient-to-t from-black/10 to-transparent"></div>
        </div>
    @else
        <div class="relative overflow-hidden rounded-t-2xl bg-gradient-to-br {{ $product->is_featured ? 'from-gold-500/10 via-cream-100 to-caramel-400/10' : 'from-cream-100 via-cream-50 to-cream-100' }} h-32 flex items-center justify-center">
            <svg class="w-12 h-12 {{ $product->is_featured ? 'text-gold-500/30' : 'text-cream-300' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 3.104v5.714a2.25 2.25 0 01-.659 1.591L5 14.5M9.75 3.104c-.251.023-.501.05-.75.082m.75-.082a24.301 24.301 0 014.5 0m0 0v5.714c0 .597.237 1.17.659 1.591L19.8 15.3M14.25 3.104c.251.023.501.05.75.082M19.8 15.3l-1.57.393A9.065 9.065 0 0112 15a9.065 9.065 0 00-6.23.693L5 14.5m14.8.8l1.402 1.402c1.232 1.232.65 3.318-1.067 3.611A48.309 48.309 0 0112 21c-2.773 0-5.491-.235-8.135-.687-1.718-.293-2.3-2.379-1.067-3.61L5 14.5"/>
            </svg>
        </div>
    @endif

    {{-- Content --}}
    <div class="p-5 flex flex-col flex-1">
        {{-- Product Name (prominent) --}}
        <h4 class="text-lg font-bold text-gray-900 leading-tight">{{ $product->name }}</h4>

        @if($product->subtitle)
            <p class="text-sm text-gray-500 mt-1">{{ $product->subtitle }}</p>
        @endif

        @if($product->dosing_info)
            <p class="text-xs text-cream-400 mt-1 font-medium">{{ $product->dosing_info }}</p>
        @endif

        @if($product->key_benefits && count($product->key_benefits) > 0)
            <ul class="mt-3 space-y-1.5 flex-1">
                @foreach(array_slice($product->key_benefits, 0, 3) as $benefit)
                    <li class="flex items-start gap-2 text-sm text-gray-600">
                        <svg class="w-4 h-4 text-green-500 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        {{ $benefit }}
                    </li>
                @endforeach
            </ul>
        @endif

        {{-- Store Comparison --}}
        <div class="mt-4 pt-4 border-t border-cream-200">
            @include('livewire.partials.store-comparison', ['product' => $product])
        </div>

        @if($product->related_peptide_id)
            <a href="{{ route('peptides.show', $product->relatedPeptide) }}" class="text-xs text-gold-500 hover:text-gold-600 hover:underline mt-3 inline-flex items-center gap-1 font-medium transition-colors" target="_blank">
                Learn More
                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
            </a>
        @endif
    </div>
</div>
