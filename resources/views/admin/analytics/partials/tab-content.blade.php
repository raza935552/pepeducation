{{-- Content Tab: deep content performance with engagement, conversion, and gap analysis --}}

@php
    $fmtTime = function ($s) {
        $s = (int) $s;
        if ($s <= 0) return '—';
        if ($s < 60) return $s.'s';
        return floor($s / 60).'m '.($s % 60).'s';
    };
@endphp

{{-- Enhanced Blog Performance --}}
<div class="card p-4 mb-6">
    <div class="flex items-center justify-between mb-3">
        <div>
            <h3 class="text-sm font-semibold text-gray-700">Blog Performance Deep Dive</h3>
            <p class="text-xs text-gray-500">Views, engagement, and BioLinx conversion per post. Sortable by clicking column headers.</p>
        </div>
        <span class="text-xs text-gray-400">Period: {{ $period }}</span>
    </div>
    @if(!empty($enhancedBlogPerformance))
        <div class="overflow-x-auto" x-data="{ sort: 'views', dir: 'desc', rows: {{ \Illuminate\Support\Js::from($enhancedBlogPerformance) }} }">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-200 bg-gray-50">
                        <th class="text-left py-2 px-2 text-gray-500 font-medium">Post</th>
                        <th class="text-left py-2 px-2 text-gray-500 font-medium w-32">Author</th>
                        <th @click="dir = (sort==='views' && dir==='desc') ? 'asc' : 'desc'; sort='views'" class="text-right py-2 px-2 text-gray-500 font-medium cursor-pointer hover:text-gray-700 w-20">Views</th>
                        <th @click="dir = (sort==='avg_time' && dir==='desc') ? 'asc' : 'desc'; sort='avg_time'" class="text-right py-2 px-2 text-gray-500 font-medium cursor-pointer hover:text-gray-700 w-24">Avg Time</th>
                        <th @click="dir = (sort==='avg_scroll' && dir==='desc') ? 'asc' : 'desc'; sort='avg_scroll'" class="text-right py-2 px-2 text-gray-500 font-medium cursor-pointer hover:text-gray-700 w-20">Scroll</th>
                        <th @click="dir = (sort==='engagement' && dir==='desc') ? 'asc' : 'desc'; sort='engagement'" class="text-right py-2 px-2 text-gray-500 font-medium cursor-pointer hover:text-gray-700 w-24">Engagement</th>
                        <th @click="dir = (sort==='buy_clicks' && dir==='desc') ? 'asc' : 'desc'; sort='buy_clicks'" class="text-right py-2 px-2 text-gray-500 font-medium cursor-pointer hover:text-gray-700 w-20">Clicks</th>
                        <th @click="dir = (sort==='click_rate' && dir==='desc') ? 'asc' : 'desc'; sort='click_rate'" class="text-right py-2 px-2 text-gray-500 font-medium cursor-pointer hover:text-gray-700 w-24">CTR</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="row in rows.slice().sort((a,b)=>{ const m=dir==='desc'?-1:1; return m*((a[sort]||0)-(b[sort]||0)); })" :key="row.url">
                        <tr class="border-b border-gray-100 hover:bg-gray-50">
                            <td class="py-2 px-2">
                                <a :href="row.url" target="_blank" class="text-blue-600 hover:underline" x-text="row.title"></a>
                            </td>
                            <td class="py-2 px-2 text-gray-500 text-xs" x-text="row.author || '—'"></td>
                            <td class="py-2 px-2 text-right font-semibold" x-text="row.views.toLocaleString()"></td>
                            <td class="py-2 px-2 text-right text-gray-600">
                                <span x-text="row.avg_time >= 60 ? Math.floor(row.avg_time/60)+'m '+(row.avg_time%60)+'s' : row.avg_time+'s'"></span>
                            </td>
                            <td class="py-2 px-2 text-right">
                                <span :class="row.avg_scroll >= 75 ? 'text-green-600' : (row.avg_scroll >= 50 ? 'text-yellow-600' : 'text-gray-500')" x-text="row.avg_scroll+'%'"></span>
                            </td>
                            <td class="py-2 px-2 text-right">
                                <div class="inline-flex items-center gap-1">
                                    <div class="w-12 h-1.5 bg-gray-200 rounded-full overflow-hidden">
                                        <div class="h-full" :class="row.engagement >= 70 ? 'bg-green-500' : (row.engagement >= 40 ? 'bg-yellow-500' : 'bg-red-500')" :style="`width: ${row.engagement}%`"></div>
                                    </div>
                                    <span class="text-xs font-medium text-gray-600" x-text="row.engagement"></span>
                                </div>
                            </td>
                            <td class="py-2 px-2 text-right font-semibold text-cyan-700" x-text="row.buy_clicks"></td>
                            <td class="py-2 px-2 text-right">
                                <span :class="row.click_rate >= 2 ? 'text-green-600 font-semibold' : (row.click_rate >= 0.5 ? 'text-yellow-600' : 'text-gray-500')" x-text="row.click_rate+'%'"></span>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    @else
        <p class="text-gray-500 text-sm text-center py-4">No blog performance data for this period.</p>
    @endif
</div>

{{-- Enhanced Peptide Performance --}}
<div class="card p-4 mb-6">
    <div class="flex items-center justify-between mb-3">
        <div>
            <h3 class="text-sm font-semibold text-gray-700">Peptide Page Performance</h3>
            <p class="text-xs text-gray-500">Views, time on page, and BioLinx click-through per peptide.</p>
        </div>
    </div>
    @if(!empty($enhancedPeptidePerformance))
        <div class="overflow-x-auto" x-data="{ sort: 'views', dir: 'desc', rows: {{ \Illuminate\Support\Js::from($enhancedPeptidePerformance) }} }">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-200 bg-gray-50">
                        <th class="text-left py-2 px-2 text-gray-500 font-medium">Peptide</th>
                        <th @click="dir = (sort==='views' && dir==='desc') ? 'asc' : 'desc'; sort='views'" class="text-right py-2 px-2 text-gray-500 font-medium cursor-pointer hover:text-gray-700 w-20">Views</th>
                        <th @click="dir = (sort==='avg_time' && dir==='desc') ? 'asc' : 'desc'; sort='avg_time'" class="text-right py-2 px-2 text-gray-500 font-medium cursor-pointer hover:text-gray-700 w-24">Avg Time</th>
                        <th @click="dir = (sort==='avg_scroll' && dir==='desc') ? 'asc' : 'desc'; sort='avg_scroll'" class="text-right py-2 px-2 text-gray-500 font-medium cursor-pointer hover:text-gray-700 w-20">Scroll</th>
                        <th @click="dir = (sort==='buy_clicks' && dir==='desc') ? 'asc' : 'desc'; sort='buy_clicks'" class="text-right py-2 px-2 text-gray-500 font-medium cursor-pointer hover:text-gray-700 w-20">Clicks</th>
                        <th @click="dir = (sort==='click_rate' && dir==='desc') ? 'asc' : 'desc'; sort='click_rate'" class="text-right py-2 px-2 text-gray-500 font-medium cursor-pointer hover:text-gray-700 w-24">CTR</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="row in rows.slice().sort((a,b)=>{ const m=dir==='desc'?-1:1; return m*((a[sort]||0)-(b[sort]||0)); })" :key="row.url">
                        <tr class="border-b border-gray-100 hover:bg-gray-50">
                            <td class="py-2 px-2">
                                <a :href="row.url" target="_blank" class="text-blue-600 hover:underline" x-text="row.name"></a>
                            </td>
                            <td class="py-2 px-2 text-right font-semibold" x-text="row.views.toLocaleString()"></td>
                            <td class="py-2 px-2 text-right text-gray-600">
                                <span x-text="row.avg_time >= 60 ? Math.floor(row.avg_time/60)+'m '+(row.avg_time%60)+'s' : row.avg_time+'s'"></span>
                            </td>
                            <td class="py-2 px-2 text-right">
                                <span :class="row.avg_scroll >= 75 ? 'text-green-600' : (row.avg_scroll >= 50 ? 'text-yellow-600' : 'text-gray-500')" x-text="row.avg_scroll+'%'"></span>
                            </td>
                            <td class="py-2 px-2 text-right font-semibold text-cyan-700" x-text="row.buy_clicks"></td>
                            <td class="py-2 px-2 text-right">
                                <span :class="row.click_rate >= 2 ? 'text-green-600 font-semibold' : (row.click_rate >= 0.5 ? 'text-yellow-600' : 'text-gray-500')" x-text="row.click_rate+'%'"></span>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    @else
        <p class="text-gray-500 text-sm text-center py-4">No peptide page data for this period.</p>
    @endif
</div>

{{-- Content Gaps and Hidden Gems --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    {{-- Gaps: high views, low engagement (need quality work) --}}
    <div class="card p-4 border-l-4 border-red-400">
        <h3 class="text-sm font-semibold text-gray-700 mb-1 flex items-center gap-2">
            <svg aria-hidden="true" class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            Content Gaps
        </h3>
        <p class="text-xs text-gray-500 mb-3">High traffic, low engagement. Rewrite or improve these.</p>
        @if(!empty($contentGapsGems['gaps']))
            <table class="w-full text-xs">
                <thead>
                    <tr class="text-gray-500">
                        <th class="text-left py-1">Page</th>
                        <th class="text-right py-1 w-16">Views</th>
                        <th class="text-right py-1 w-16">Engage</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($contentGapsGems['gaps'] as $row)
                        <tr class="border-t border-gray-100">
                            <td class="py-1.5 truncate max-w-[280px]" title="{{ $row['title'] }}">
                                <a href="{{ $row['url'] }}" target="_blank" class="text-blue-600 hover:underline">{{ \Illuminate\Support\Str::limit($row['title'], 50) }}</a>
                            </td>
                            <td class="py-1.5 text-right font-medium">{{ number_format($row['views']) }}</td>
                            <td class="py-1.5 text-right text-red-600 font-medium">{{ $row['engagement'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p class="text-gray-400 text-xs italic">No clear content gaps identified yet. Need more traffic data.</p>
        @endif
    </div>

    {{-- Gems: low views, high engagement (promote) --}}
    <div class="card p-4 border-l-4 border-emerald-400">
        <h3 class="text-sm font-semibold text-gray-700 mb-1 flex items-center gap-2">
            <svg aria-hidden="true" class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z"/></svg>
            Hidden Gems
        </h3>
        <p class="text-xs text-gray-500 mb-3">High engagement, low traffic. Promote or link to these more.</p>
        @if(!empty($contentGapsGems['gems']))
            <table class="w-full text-xs">
                <thead>
                    <tr class="text-gray-500">
                        <th class="text-left py-1">Page</th>
                        <th class="text-right py-1 w-16">Views</th>
                        <th class="text-right py-1 w-16">Engage</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($contentGapsGems['gems'] as $row)
                        <tr class="border-t border-gray-100">
                            <td class="py-1.5 truncate max-w-[280px]" title="{{ $row['title'] }}">
                                <a href="{{ $row['url'] }}" target="_blank" class="text-blue-600 hover:underline">{{ \Illuminate\Support\Str::limit($row['title'], 50) }}</a>
                            </td>
                            <td class="py-1.5 text-right font-medium">{{ number_format($row['views']) }}</td>
                            <td class="py-1.5 text-right text-emerald-600 font-medium">{{ $row['engagement'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p class="text-gray-400 text-xs italic">No clear gems identified yet. Need more traffic data.</p>
        @endif
    </div>
</div>

{{-- Search Gaps - what people search for that we don't have --}}
<div class="card p-4 mb-6 border-l-4 border-amber-400">
    <h3 class="text-sm font-semibold text-gray-700 mb-1 flex items-center gap-2">
        <svg aria-hidden="true" class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
        Internal Search Gaps (Zero-Result Queries)
    </h3>
    <p class="text-xs text-gray-500 mb-3">What people searched for and we did not have. Direct content-gap signal.</p>
    @if(!empty($searchGaps))
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-2">
            @foreach($searchGaps as $row)
                <div class="flex items-center justify-between p-2 rounded-lg bg-amber-50 border border-amber-200">
                    <span class="text-sm text-gray-800 font-mono">{{ $row['query'] }}</span>
                    <span class="text-xs font-bold text-amber-700">{{ $row['searches'] }}</span>
                </div>
            @endforeach
        </div>
    @else
        <p class="text-gray-400 text-xs italic">No zero-result searches in this period. Either search coverage is solid or no data yet.</p>
    @endif
</div>

{{-- Charts: Scroll Depth + Avg Time --}}
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
