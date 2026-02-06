<!-- Categories - Horizontal Scroll -->
<div class="card !p-4">
    <h3 class="font-semibold text-gray-900 mb-3 text-sm">Categories</h3>
    <div class="flex flex-wrap gap-2">
        <a href="{{ route('peptides.index', array_filter(['search' => request('search'), 'research' => request('research')])) }}"
           class="px-3 py-1.5 rounded-full text-xs font-medium transition-colors {{ !request('category') ? 'bg-gold-500 text-white' : 'bg-cream-100 text-gray-600' }}">
            All
        </a>
        @foreach($categories->take(10) as $cat)
            <a href="{{ route('peptides.index', array_filter(['category' => $cat->slug, 'search' => request('search'), 'research' => request('research')])) }}"
               class="px-3 py-1.5 rounded-full text-xs font-medium transition-colors {{ request('category') === $cat->slug ? 'bg-gold-500 text-white' : 'bg-cream-100 text-gray-600' }}">
                {{ $cat->name }}
            </a>
        @endforeach
        @if($categories->count() > 10)
            <span class="px-3 py-1.5 text-xs text-cream-500">+{{ $categories->count() - 10 }} more</span>
        @endif
    </div>
</div>

<!-- Research Level -->
<div class="card !p-4">
    <h3 class="font-semibold text-gray-900 mb-3 text-sm">Research Level</h3>
    <div class="flex flex-wrap gap-2">
        @foreach(['extensive' => 'Extensive', 'well' => 'Well Researched', 'emerging' => 'Emerging', 'limited' => 'Limited'] as $val => $label)
            <a href="{{ route('peptides.index', array_filter(['research' => request('research') === $val ? null : $val, 'category' => request('category'), 'search' => request('search')])) }}"
               class="px-3 py-1.5 rounded-full text-xs font-medium transition-colors {{ request('research') === $val ? 'bg-gold-500 text-white' : 'bg-cream-100 text-gray-600' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>
</div>

@if(request()->hasAny(['search', 'category', 'research']))
    <a href="{{ route('peptides.index') }}" class="block text-center text-sm text-gold-600 hover:underline">
        Clear All Filters
    </a>
@endif
