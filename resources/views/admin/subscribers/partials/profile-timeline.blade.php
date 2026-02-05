<div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
    <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700">
        <h3 class="font-semibold text-gray-900 dark:text-white">Activity Timeline</h3>
    </div>
    <div class="p-4">
        @php
            $events = collect();

            // Add subscription event
            if($subscriber->subscribed_at) {
                $events->push([
                    'type' => 'subscribed',
                    'date' => $subscriber->subscribed_at,
                    'title' => 'Subscribed',
                    'detail' => 'via ' . ($subscriber->source ?? 'unknown'),
                ]);
            }

            // Add quiz responses
            foreach($subscriber->quizResponses as $response) {
                $events->push([
                    'type' => 'quiz',
                    'date' => $response->created_at,
                    'title' => 'Completed Quiz',
                    'detail' => $response->quiz?->name ?? 'Unknown Quiz',
                ]);
            }

            // Add outbound clicks
            foreach($subscriber->outboundClicks as $click) {
                $events->push([
                    'type' => 'click',
                    'date' => $click->created_at,
                    'title' => 'Clicked to Shop',
                    'detail' => $click->outboundLink?->name ?? 'Unknown Link',
                ]);
            }

            // Add downloads
            foreach($subscriber->leadMagnetDownloads as $download) {
                $events->push([
                    'type' => 'download',
                    'date' => $download->created_at,
                    'title' => 'Downloaded Lead Magnet',
                    'detail' => $download->leadMagnet?->title ?? 'Unknown',
                ]);
            }

            // Sort by date descending
            $events = $events->sortByDesc('date')->take(10);
        @endphp

        @if($events->isEmpty())
            <p class="text-center text-sm text-gray-500 dark:text-gray-400 py-4">
                No activity recorded yet
            </p>
        @else
            <div class="space-y-4">
                @foreach($events as $event)
                    <div class="flex gap-3">
                        {{-- Icon --}}
                        <div class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center
                            @if($event['type'] === 'subscribed') bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400
                            @elseif($event['type'] === 'quiz') bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400
                            @elseif($event['type'] === 'click') bg-amber-100 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400
                            @elseif($event['type'] === 'download') bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400
                            @else bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400
                            @endif">
                            @if($event['type'] === 'subscribed')
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                                </svg>
                            @elseif($event['type'] === 'quiz')
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                            @elseif($event['type'] === 'click')
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                </svg>
                            @elseif($event['type'] === 'download')
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            @endif
                        </div>
                        {{-- Content --}}
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $event['title'] }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $event['detail'] }}</p>
                        </div>
                        {{-- Time --}}
                        <div class="text-xs text-gray-400 dark:text-gray-500">
                            {{ $event['date']->diffForHumans() }}
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
