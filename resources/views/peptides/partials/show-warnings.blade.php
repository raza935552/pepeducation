@if($peptide->safety_warnings && count($peptide->safety_warnings))
<div class="card border-l-4 border-l-amber-500">
    <h2 class="section-heading-lg">
        <span class="section-icon bg-gradient-to-br from-amber-400 to-amber-600 shadow-amber-500/30">
            <svg aria-hidden="true" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
        </span>
        Safety Warnings
    </h2>

    <div class="space-y-3">
        @foreach($peptide->safety_warnings as $warning)
            <div class="flex items-start gap-3 p-4 rounded-xl bg-amber-50 border border-amber-100">
                <span class="shrink-0 mt-0.5 flex items-center justify-center w-5 h-5 rounded-full bg-amber-100">
                    <svg aria-hidden="true" class="w-3 h-3 text-amber-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                </span>
                <span class="text-gray-700">{{ $warning }}</span>
            </div>
        @endforeach
    </div>
</div>
@endif
