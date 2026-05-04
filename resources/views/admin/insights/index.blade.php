<x-admin-layout>
    <x-slot name="header">Insights & Analytics</x-slot>

    <div class="space-y-6">

        {{-- Buy CTA Click Analytics --}}
        <div class="card p-6 border-l-4 border-cyan-400">
            <div class="flex items-center justify-between mb-1">
                <h3 class="text-lg font-semibold flex items-center gap-2">
                    <svg aria-hidden="true" class="w-6 h-6 text-cyan-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    Buy CTA Click Analytics
                </h3>
                <div class="text-sm text-gray-500">
                    <span class="font-semibold text-gray-900">{{ number_format($totalClicks) }}</span> total
                    &middot;
                    <span class="font-semibold text-gray-900">{{ number_format($clicksLast30) }}</span> last 30 days
                    &middot;
                    <span class="font-semibold text-gray-900">{{ number_format($clicksLast7) }}</span> last 7 days
                </div>
            </div>
            <p class="text-sm text-gray-500 mb-4">Clicks on the BioLinx Labs CTAs. Use to measure which placements and peptides drive partner traffic.</p>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div>
                    <h4 class="text-sm font-semibold text-gray-700 mb-2">Clicks by Placement</h4>
                    @if($clicksByContext->isEmpty())
                        <p class="text-sm text-gray-400 italic">No clicks recorded yet.</p>
                    @else
                        <div class="border rounded-lg overflow-hidden">
                            <table class="w-full text-sm">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500">Placement</th>
                                        <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 w-24">Clicks</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @foreach($clicksByContext as $row)
                                        <tr>
                                            <td class="px-3 py-2 text-gray-900 font-mono text-xs">{{ $row->context ?: '(unknown)' }}</td>
                                            <td class="px-3 py-2 text-right font-semibold">{{ number_format($row->clicks) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>

                <div>
                    <h4 class="text-sm font-semibold text-gray-700 mb-2">Top Peptides by Clicks</h4>
                    @if($topClickedPeptides->isEmpty())
                        <p class="text-sm text-gray-400 italic">No peptide-specific clicks recorded yet.</p>
                    @else
                        <div class="border rounded-lg overflow-hidden">
                            <table class="w-full text-sm">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500">Peptide</th>
                                        <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 w-24">Clicks</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @foreach($topClickedPeptides as $row)
                                        <tr>
                                            <td class="px-3 py-2">
                                                <a href="{{ route('peptides.show', $row->slug) }}" target="_blank" class="text-blue-600 hover:underline">{{ $row->name }}</a>
                                            </td>
                                            <td class="px-3 py-2 text-right font-semibold">{{ number_format($row->clicks) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Search Analytics --}}
        <div class="card p-6 border-l-4 border-blue-400">
            <div class="flex items-center justify-between mb-1">
                <h3 class="text-lg font-semibold flex items-center gap-2">
                    <svg aria-hidden="true" class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    Site Search Analytics
                </h3>
                <div class="text-sm text-gray-500">
                    <span class="font-semibold text-gray-900">{{ number_format($totalSearches) }}</span> total
                    &middot;
                    <span class="font-semibold text-gray-900">{{ number_format($searchesLast30) }}</span> last 30 days
                </div>
            </div>
            <p class="text-sm text-gray-500 mb-4">What people are searching for - use this to find content gaps.</p>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div>
                    <h4 class="text-sm font-semibold text-gray-700 mb-2">Top Searches</h4>
                    @if($topSearches->isEmpty())
                        <p class="text-sm text-gray-400 italic">No searches recorded yet.</p>
                    @else
                        <div class="border rounded-lg overflow-hidden">
                            <table class="w-full text-sm">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500">Query</th>
                                        <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 w-24">Searches</th>
                                        <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 w-24">Avg Results</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @foreach($topSearches as $row)
                                        <tr>
                                            <td class="px-3 py-2 text-gray-900 font-mono text-xs">{{ $row->query }}</td>
                                            <td class="px-3 py-2 text-right font-semibold">{{ number_format($row->searches) }}</td>
                                            <td class="px-3 py-2 text-right text-gray-500">{{ number_format($row->avg_results, 1) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>

                <div>
                    <h4 class="text-sm font-semibold text-red-700 mb-2">Zero-Result Searches (Content Gaps)</h4>
                    @if($zeroResultSearches->isEmpty())
                        <p class="text-sm text-gray-400 italic">No zero-result searches.</p>
                    @else
                        <div class="border rounded-lg overflow-hidden">
                            <table class="w-full text-sm">
                                <thead class="bg-red-50">
                                    <tr>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-red-700">Query</th>
                                        <th class="px-3 py-2 text-right text-xs font-medium text-red-700 w-24">Searches</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-red-100">
                                    @foreach($zeroResultSearches as $row)
                                        <tr class="bg-red-50/30">
                                            <td class="px-3 py-2 text-gray-900 font-mono text-xs">{{ $row->query }}</td>
                                            <td class="px-3 py-2 text-right font-semibold text-red-700">{{ number_format($row->searches) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Author Performance --}}
        <div class="card p-6 border-l-4 border-purple-400">
            <h3 class="text-lg font-semibold mb-1 flex items-center gap-2">
                <svg aria-hidden="true" class="w-6 h-6 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                Author Performance
            </h3>
            <p class="text-sm text-gray-500 mb-4">Which authors' posts get the most views.</p>

            <div class="border rounded-lg overflow-hidden">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500">Author</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500">Credentials</th>
                            <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 w-24">Posts</th>
                            <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 w-32">Total Views</th>
                            <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 w-24">Avg/Post</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($authorStats as $author)
                            <tr>
                                <td class="px-3 py-2">
                                    <a href="{{ route('author.show', $author->slug) }}" target="_blank" class="font-medium text-blue-600 hover:underline">{{ $author->name }}</a>
                                </td>
                                <td class="px-3 py-2 text-gray-500 text-xs">{{ $author->credentials }}</td>
                                <td class="px-3 py-2 text-right font-semibold">{{ number_format($author->published_count) }}</td>
                                <td class="px-3 py-2 text-right font-semibold">{{ number_format($author->total_views ?? 0) }}</td>
                                <td class="px-3 py-2 text-right text-gray-600">
                                    {{ $author->published_count > 0 ? number_format(($author->total_views ?? 0) / $author->published_count, 0) : '-' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Top Posts --}}
        <div class="card p-6 border-l-4 border-emerald-400">
            <h3 class="text-lg font-semibold mb-1 flex items-center gap-2">
                <svg aria-hidden="true" class="w-6 h-6 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 13l4 4L19 5"/>
                </svg>
                Top 15 Blog Posts by Views
            </h3>

            <div class="border rounded-lg overflow-hidden mt-3">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 w-12">#</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500">Title</th>
                            <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 w-24">Views</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($topPosts as $i => $post)
                            <tr>
                                <td class="px-3 py-2 text-gray-400 font-mono">{{ $i + 1 }}</td>
                                <td class="px-3 py-2">
                                    <a href="{{ route('blog.show', $post->slug) }}" target="_blank" class="text-blue-600 hover:underline">{{ $post->title }}</a>
                                </td>
                                <td class="px-3 py-2 text-right font-semibold">{{ number_format($post->views_count) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Peptides by Category --}}
        <div class="card p-6 border-l-4 border-amber-400">
            <h3 class="text-lg font-semibold mb-1 flex items-center gap-2">
                <svg aria-hidden="true" class="w-6 h-6 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14-4H5m14 8H5m14 4H5"/>
                </svg>
                Peptides per Category
            </h3>

            <div class="grid grid-cols-2 md:grid-cols-5 gap-3 mt-3">
                @foreach($peptidesByCat as $cat)
                    <div class="p-3 bg-gray-50 rounded-lg border border-gray-200">
                        <p class="text-xs text-gray-500">{{ $cat->name }}</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $cat->count }}</p>
                    </div>
                @endforeach
            </div>
        </div>

    </div>
</x-admin-layout>
