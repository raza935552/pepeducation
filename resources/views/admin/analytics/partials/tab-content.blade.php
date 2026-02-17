{{-- Content Tab: Top pages, blog performance, scroll depth, avg time on page --}}

{{-- Top Pages --}}
<div class="card p-4 mb-6">
    <h3 class="text-sm font-semibold text-gray-700 mb-3">Top Pages</h3>
    @if(!empty($topPages))
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-200">
                        <th class="text-left py-2 px-2 text-gray-500 font-medium">Page</th>
                        <th class="text-right py-2 px-2 text-gray-500 font-medium">Views</th>
                        <th class="text-right py-2 px-2 text-gray-500 font-medium">Avg Scroll</th>
                        <th class="text-right py-2 px-2 text-gray-500 font-medium">Avg Time</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($topPages as $page)
                        <tr class="border-b border-gray-100">
                            <td class="py-2 px-2 text-gray-700 truncate max-w-[250px]" title="{{ $page['url'] }}">
                                @php try { $path = parse_url($page['url'], PHP_URL_PATH) ?: $page['url']; } catch (\Throwable $e) { $path = $page['url']; } @endphp
                                {{ $path }}
                            </td>
                            <td class="py-2 px-2 text-right font-medium">{{ number_format($page['views']) }}</td>
                            <td class="py-2 px-2 text-right">
                                <span class="{{ $page['avgScroll'] >= 75 ? 'text-green-600' : ($page['avgScroll'] >= 50 ? 'text-yellow-600' : 'text-gray-500') }}">
                                    {{ $page['avgScroll'] }}%
                                </span>
                            </td>
                            <td class="py-2 px-2 text-right text-gray-600">
                                @if($page['avgTime'] >= 60)
                                    {{ floor($page['avgTime'] / 60) }}m {{ $page['avgTime'] % 60 }}s
                                @else
                                    {{ $page['avgTime'] }}s
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <p class="text-gray-500 text-sm text-center py-4">No page view data</p>
    @endif
</div>

{{-- Blog Performance --}}
<div class="card p-4 mb-6">
    <h3 class="text-sm font-semibold text-gray-700 mb-3">Blog Performance</h3>
    @if(!empty($blogPerformance))
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-200">
                        <th class="text-left py-2 px-2 text-gray-500 font-medium">Post</th>
                        <th class="text-right py-2 px-2 text-gray-500 font-medium">Views</th>
                        <th class="text-right py-2 px-2 text-gray-500 font-medium">Avg Scroll</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($blogPerformance as $post)
                        <tr class="border-b border-gray-100">
                            <td class="py-2 px-2">
                                <div class="text-gray-700 font-medium truncate max-w-[300px]">{{ $post['title'] }}</div>
                            </td>
                            <td class="py-2 px-2 text-right font-medium">{{ number_format($post['views']) }}</td>
                            <td class="py-2 px-2 text-right">
                                <span class="{{ $post['avgScroll'] >= 75 ? 'text-green-600' : ($post['avgScroll'] >= 50 ? 'text-yellow-600' : 'text-gray-500') }}">
                                    {{ $post['avgScroll'] }}%
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <p class="text-gray-500 text-sm text-center py-4">No blog performance data</p>
    @endif
</div>

{{-- Charts Row: Scroll Depth + Avg Time on Page --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="card p-4">
        <h3 class="text-sm font-semibold text-gray-700 mb-3">Scroll Depth Distribution</h3>
        <div id="scroll-chart"></div>
    </div>

    <div class="card p-4">
        <h3 class="text-sm font-semibold text-gray-700 mb-3">Avg Time on Page (Top 10)</h3>
        <div id="time-chart"></div>
    </div>
</div>
