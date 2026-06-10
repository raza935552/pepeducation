<x-admin-layout>
    <x-slot name="header"><span>Landers</span></x-slot>

    <style>[x-cloak]{display:none!important}</style>

    @if(session('success'))
        <div class="mb-4 rounded-lg bg-green-50 border border-green-200 text-green-700 px-4 py-3 text-sm">{{ session('success') }}</div>
    @endif

    {{-- How the tracking flows (so the marketer knows WHY the ad UTMs matter) --}}
    <div class="mb-6 rounded-xl border border-blue-100 bg-blue-50/60 px-5 py-4 text-sm text-blue-900">
        <div class="font-semibold mb-1">Ad → Lander → Biolinx tracking</div>
        <p class="text-blue-800/90 leading-relaxed">
            Paste a lander's <strong>Ad URL</strong> (below) as the destination of your Meta ad. When someone clicks the ad they land here
            carrying your <code class="bg-white/70 px-1 rounded">utm_*</code> + <code class="bg-white/70 px-1 rounded">fbclid</code>;
            the CTA forwards <strong>those exact ad params + the lander identity</strong> to biolinxlabs.com, so Biolinx attributes
            the sale back to this ad &amp; lander (and Meta CAPI matches it). Keep the campaign/content names consistent across ads.
        </p>
    </div>

    {{-- Editable (CMS) landers --}}
    <div class="card overflow-hidden mb-6">
        <div class="px-6 py-3 border-b border-gray-100 text-sm font-semibold text-gray-900">Editable landers (CMS)</div>
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">URL</th>
                    <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($cmsLanders as $l)
                    <tr class="hover:bg-gray-50" x-data="{ ad: false }">
                        <td class="px-6 py-4 align-top"><div class="font-medium text-gray-900">{{ $l->name }}</div><div class="text-xs text-gray-400 font-mono">template: {{ $l->template }}</div></td>
                        <td class="px-6 py-4 text-sm text-gray-600 font-mono align-top">/lp/{{ $l->slug }}</td>
                        <td class="px-6 py-4 text-center align-top">
                            @if($l->is_active)<span class="inline-flex px-2 py-0.5 rounded-full text-xs bg-green-100 text-green-700">Active</span>
                            @else<span class="inline-flex px-2 py-0.5 rounded-full text-xs bg-gray-100 text-gray-500">Off</span>@endif
                        </td>
                        <td class="px-6 py-4 text-right whitespace-nowrap align-top">
                            <button type="button" @click="ad = !ad" class="text-sm text-blue-600 hover:text-blue-800 mr-3">Ad URL ▾</button>
                            <a href="{{ $l->url }}" target="_blank" class="text-sm text-gray-500 hover:text-gray-700 mr-3">View ↗</a>
                            <a href="{{ route('admin.landers.edit', $l) }}" class="btn btn-primary btn-sm">Edit content</a>
                            <div x-show="ad" x-cloak class="mt-3 text-left">
                                <x-lander-ad-url :base="$l->url" :default-campaign="$l->slug" />
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="px-6 py-8 text-center text-sm text-gray-400">No CMS landers yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Legacy static landers --}}
    <div class="card overflow-hidden">
        <div class="px-6 py-3 border-b border-gray-100 text-sm font-semibold text-gray-900">Legacy landers <span class="font-normal text-gray-400">(static — copy the ad URL to run paid traffic)</span></div>
        <div class="divide-y divide-gray-100">
            @foreach($staticLanders as $slug)
                <div class="px-6 py-4" x-data="{ ad: false }">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-mono text-gray-700">/lp/{{ $slug }}</span>
                        <div class="whitespace-nowrap">
                            <button type="button" @click="ad = !ad" class="text-sm text-blue-600 hover:text-blue-800 mr-3">Ad URL ▾</button>
                            <a href="{{ url('/lp/'.$slug) }}" target="_blank" class="text-sm text-gray-500 hover:text-gray-700">View ↗</a>
                        </div>
                    </div>
                    <div x-show="ad" x-cloak class="mt-3">
                        <x-lander-ad-url :base="url('/lp/'.$slug)" :default-campaign="$slug" />
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</x-admin-layout>
