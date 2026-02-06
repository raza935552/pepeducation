<div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-6 lg:gap-8">
    <div class="flex-1">
        <!-- Breadcrumb -->
        <nav class="flex items-center gap-2 text-sm text-cream-400 mb-4">
            <a href="{{ route('peptides.index') }}" class="hover:text-white transition-colors">Peptides</a>
            <svg aria-hidden="true" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            <span class="text-white">{{ $peptide->name }}</span>
        </nav>

        <div class="flex flex-wrap items-center gap-3 mb-3">
            @if($peptide->abbreviation)
                <span class="px-3 py-1 rounded-full bg-white/10 text-sm font-mono text-gold-300">
                    {{ $peptide->abbreviation }}
                </span>
            @endif
            <span class="text-sm text-cream-400">{{ $peptide->type }}</span>
        </div>

        <h1 class="text-3xl md:text-4xl lg:text-5xl font-bold mb-2 bg-gradient-to-r from-white to-cream-200 bg-clip-text text-transparent">
            {{ $peptide->name }}
        </h1>
        @if($peptide->full_name && $peptide->full_name !== $peptide->name)
            <p class="text-lg md:text-xl text-cream-300 mb-4 lg:mb-6">{{ $peptide->full_name }}</p>
        @endif

        <!-- Categories -->
        <div class="flex flex-wrap gap-2">
            @foreach($peptide->categories as $category)
                <a href="{{ route('peptides.index', ['category' => $category->slug]) }}"
                   class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs md:text-sm font-medium transition-all hover:scale-105"
                   style="background: linear-gradient(135deg, {{ $category->color }}40, {{ $category->color }}20); color: {{ $category->color }}; border: 1px solid {{ $category->color }}40;">
                    <span class="w-1.5 h-1.5 rounded-full" style="background-color: {{ $category->color }}"></span>
                    {{ $category->name }}
                </a>
            @endforeach
        </div>
    </div>

    <!-- Right Side: Actions + Research Status -->
    <div class="shrink-0 w-full lg:w-auto space-y-3">
        <div class="flex gap-2">
            <!-- Bookmark Button -->
            @auth
            @php $isBookmarked = auth()->user()->hasBookmarked($peptide); @endphp
            <form action="{{ route('bookmarks.toggle', $peptide) }}" method="POST">
                @csrf
                <button type="submit" class="flex-1 flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl transition-all {{ $isBookmarked ? 'bg-gold-500 text-white hover:bg-gold-600' : 'bg-white/10 text-cream-200 hover:bg-white/20 border border-white/10' }}">
                    <svg aria-hidden="true" class="w-5 h-5" fill="{{ $isBookmarked ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/>
                    </svg>
                    {{ $isBookmarked ? 'Saved' : 'Save' }}
                </button>
            </form>
        @else
            <a href="{{ route('login') }}" class="flex-1 flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-white/10 text-cream-200 hover:bg-white/20 border border-white/10 transition-all">
                <svg aria-hidden="true" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/>
                </svg>
                Save
            </a>
        @endauth

            <!-- Suggest Edit Button -->
            <button type="button"
                    onclick="Livewire.dispatch('openEditSuggestionModal', { peptideId: {{ $peptide->id }}, section: '' })"
                    class="flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-white/10 text-cream-200 hover:bg-white/20 border border-white/10 transition-all"
                    title="Suggest an edit">
                <svg aria-hidden="true" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
            </button>
        </div>

        <!-- Research Status Card -->
        @php $badge = $peptide->research_status_badge; @endphp
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-white/10 to-white/5 backdrop-blur-xl border border-white/10 p-4 lg:p-6 lg:min-w-[200px]">
            <div class="absolute inset-0 bg-gradient-to-br from-gold-500/20 to-transparent"></div>
            <div class="relative flex lg:block items-center justify-between gap-4">
                <div>
                    <div class="text-xs uppercase tracking-wider text-cream-400 mb-1 lg:mb-2">Research Status</div>
                    <div class="text-xl lg:text-2xl font-bold
                        {{ $badge['color'] === 'blue' ? 'text-blue-400' : '' }}
                        {{ $badge['color'] === 'green' ? 'text-emerald-400' : '' }}
                        {{ $badge['color'] === 'yellow' ? 'text-gold-400' : '' }}
                        {{ $badge['color'] === 'gray' ? 'text-cream-400' : '' }}">
                        {{ $badge['label'] }}
                    </div>
                </div>
                <div class="flex gap-1 lg:mt-3">
                    @for($i = 0; $i < 4; $i++)
                        <div class="w-6 lg:w-8 h-1.5 rounded-full {{ $i < ($badge['color'] === 'blue' ? 4 : ($badge['color'] === 'green' ? 3 : ($badge['color'] === 'yellow' ? 2 : 1))) ? ($badge['color'] === 'yellow' ? 'bg-gold-400' : 'bg-'.$badge['color'].'-400') : 'bg-brown-700' }}"></div>
                    @endfor
                </div>
            </div>
        </div>
    </div>
</div>
