@if($peptide->what_to_expect && count($peptide->what_to_expect))
<div class="card">
    <h2 class="section-heading-lg">
        <span class="section-icon bg-gradient-to-br from-blue-400 to-blue-600 shadow-blue-500/30">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </span>
        What to Expect
    </h2>

    <div class="space-y-3">
        @foreach($peptide->what_to_expect as $index => $expectation)
            <div class="bg-cream-50 dark:bg-brown-700/50 rounded-xl p-4">
                <p class="text-gray-700 dark:text-cream-300">{{ $expectation }}</p>
            </div>
        @endforeach
    </div>
</div>
@endif
