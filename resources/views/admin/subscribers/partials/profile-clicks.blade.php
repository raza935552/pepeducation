<div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
    <div class="px-4 py-3 border-b border-gray-200">
        <h3 class="font-semibold text-gray-900">Outbound Clicks</h3>
    </div>
    <div class="divide-y divide-gray-200">
        @forelse($subscriber->outboundClicks()->with('outboundLink')->latest('created_at')->take(5)->get() as $click)
            <div class="p-4">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-sm font-medium text-gray-900">
                            {{ $click->outboundLink?->name ?? 'Unknown Link' }}
                        </p>
                        <p class="text-xs text-gray-500 mt-1">
                            From: {{ $click->source_page ?? 'Unknown' }}
                        </p>
                    </div>
                    <div class="text-right">
                        <p class="text-xs text-gray-500">
                            {{ $click->created_at?->diffForHumans() }}
                        </p>
                        @if($click->converted)
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs bg-emerald-100 text-emerald-700 mt-1">
                                Converted
                            </span>
                        @endif
                    </div>
                </div>
                @if($click->final_url)
                    <p class="text-xs text-gray-400 mt-2 truncate">
                        {{ $click->final_url }}
                    </p>
                @endif
            </div>
        @empty
            <div class="p-4 text-center text-sm text-gray-500">
                No outbound clicks recorded
            </div>
        @endforelse
    </div>
</div>
