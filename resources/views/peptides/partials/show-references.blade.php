@if($peptide->references && count($peptide->references))
<div class="card">
    <h2 class="section-heading-lg">
        <span class="section-icon bg-gradient-to-br from-amber-400 to-amber-600 shadow-amber-500/30">
            <svg aria-hidden="true" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
            </svg>
        </span>
        References
    </h2>

    <div class="space-y-4">
        @foreach($peptide->references as $index => $ref)
            <div class="p-4 rounded-xl bg-amber-50 border border-amber-100">
                <div class="flex items-start gap-3">
                    <span class="shrink-0 flex items-center justify-center w-6 h-6 rounded-full bg-amber-100 text-amber-700 text-xs font-bold mt-0.5">
                        {{ $index + 1 }}
                    </span>
                    <div class="min-w-0">
                        @if(!empty($ref['title']))
                            <p class="font-medium text-gray-900">
                                @if(!empty($ref['url']))
                                    <a href="{{ $ref['url'] }}" target="_blank" rel="noopener noreferrer" class="hover:text-gold-600 underline decoration-gold-300">
                                        {{ $ref['title'] }}
                                    </a>
                                @else
                                    {{ $ref['title'] }}
                                @endif
                            </p>
                        @endif
                        @if(!empty($ref['details']))
                            <p class="text-sm text-gray-500 mt-0.5">{{ $ref['details'] }}</p>
                        @endif
                        @if(!empty($ref['description']))
                            <p class="text-sm text-gray-600 mt-1">{{ $ref['description'] }}</p>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endif
