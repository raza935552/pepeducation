@if($peptide->overview || $peptide->mechanism_of_action)
<div class="card">
    <h2 class="section-heading-lg">
        <span class="section-icon bg-gradient-to-br from-gold-400 to-gold-600 shadow-gold-500/30">
            <svg aria-hidden="true" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </span>
        Overview
    </h2>

    @if($peptide->overview)
        <p class="text-gray-600 leading-relaxed text-lg mb-6">
            {{ $peptide->overview }}
        </p>
    @endif

    @if($peptide->mechanism_of_action)
        <div class="p-4 bg-gradient-to-r from-gold-50 to-caramel-50 rounded-xl border border-gold-100">
            <h3 class="text-lg font-semibold text-gray-900 mb-2 flex items-center gap-2">
                <svg aria-hidden="true" class="w-5 h-5 text-gold-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                </svg>
                Mechanism of Action
            </h3>
            <p class="text-gray-600 leading-relaxed">
                {{ $peptide->mechanism_of_action }}
            </p>
        </div>
    @endif
</div>
@endif
