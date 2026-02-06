<div class="bg-white rounded-xl border border-gray-200 p-6">
    <div class="flex items-start justify-between">
        <div class="flex items-center gap-4">
            {{-- Avatar --}}
            <div class="w-16 h-16 rounded-full bg-gradient-to-br from-amber-500 to-orange-600 flex items-center justify-center text-white font-bold text-2xl">
                {{ strtoupper(substr($subscriber->email, 0, 1)) }}
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ $subscriber->email }}</h1>
                <div class="flex items-center gap-3 mt-1">
                    {{-- Status Badge --}}
                    <span class="px-2 py-0.5 rounded-full text-xs font-medium
                        {{ $subscriber->status === 'active' ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-600' }}">
                        {{ ucfirst($subscriber->status) }}
                    </span>
                    {{-- Segment Badge --}}
                    @if($subscriber->segment)
                        <span class="px-2 py-0.5 rounded-full text-xs font-medium
                            {{ $subscriber->segment === 'bof' ? 'bg-red-100 text-red-700' :
                               ($subscriber->segment === 'mof' ? 'bg-yellow-100 text-yellow-700' :
                               'bg-blue-100 text-blue-700') }}">
                            {{ strtoupper($subscriber->segment) }}
                        </span>
                    @endif
                    {{-- Tier Badge --}}
                    @if($subscriber->engagement_tier)
                        <span class="px-2 py-0.5 rounded-full text-xs font-medium
                            {{ $subscriber->engagement_tier === 'hot' ? 'bg-orange-100 text-orange-700' :
                               ($subscriber->engagement_tier === 'warm' ? 'bg-amber-100 text-amber-700' :
                               'bg-gray-100 text-gray-600') }}">
                            {{ ucfirst($subscriber->engagement_tier) }} Lead
                        </span>
                    @endif
                </div>
            </div>
        </div>
        <div class="text-right text-sm text-gray-500">
            <p>Subscribed {{ $subscriber->subscribed_at?->diffForHumans() }}</p>
            @if($subscriber->last_activity_at)
                <p>Last active {{ $subscriber->last_activity_at->diffForHumans() }}</p>
            @endif
        </div>
    </div>
</div>
