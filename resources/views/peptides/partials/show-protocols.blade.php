@if($peptide->protocols && count($peptide->protocols))
<div class="card">
    <h2 class="section-heading-lg">
        <span class="section-icon bg-gradient-to-br from-purple-400 to-purple-600 shadow-purple-500/30">
            <svg aria-hidden="true" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
            </svg>
        </span>
        Protocols
    </h2>

    <div class="space-y-4">
        @foreach($peptide->protocols as $protocol)
            <div class="p-4 rounded-xl bg-purple-50 border border-purple-200">
                @if(!empty($protocol['goal']))
                    <h3 class="font-semibold text-gray-900 mb-3">{{ $protocol['goal'] }}</h3>
                @endif
                <dl class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    @if(!empty($protocol['dose']))
                    <div>
                        <dt class="text-xs font-medium text-purple-600 uppercase tracking-wide">Dose</dt>
                        <dd class="text-sm text-gray-700 mt-0.5">{{ $protocol['dose'] }}</dd>
                    </div>
                    @endif
                    @if(!empty($protocol['frequency']))
                    <div>
                        <dt class="text-xs font-medium text-purple-600 uppercase tracking-wide">Frequency</dt>
                        <dd class="text-sm text-gray-700 mt-0.5">{{ $protocol['frequency'] }}</dd>
                    </div>
                    @endif
                    @if(!empty($protocol['route']))
                    <div>
                        <dt class="text-xs font-medium text-purple-600 uppercase tracking-wide">Route</dt>
                        <dd class="text-sm text-gray-700 mt-0.5">{{ $protocol['route'] }}</dd>
                    </div>
                    @endif
                </dl>
            </div>
        @endforeach
    </div>
</div>
@endif
