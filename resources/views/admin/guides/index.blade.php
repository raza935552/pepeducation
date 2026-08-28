<x-admin-layout>
    <x-slot name="header">Guide Library</x-slot>

    <div class="mb-6 rounded-lg bg-blue-50 border border-blue-200 px-4 py-3 text-sm text-blue-800">
        SKU-based education PDFs, one guide per product size. Stored privately (no public or bot access), served only
        here in admin and via signed customer-delivery links. <span class="font-semibold">{{ $guides->count() }}</span>
        guide{{ $guides->count() === 1 ? '' : 's' }} available.
    </div>

    @if($guides->isEmpty())
        <div class="rounded-lg border border-gray-200 bg-white p-10 text-center text-gray-500">
            No guide PDFs found in storage.
        </div>
    @else
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($guides as $g)
                <div class="flex flex-col rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                    <div class="flex items-start gap-3">
                        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-lg bg-red-50 text-red-600">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                            </svg>
                        </div>
                        <div class="min-w-0">
                            <h3 class="truncate text-base font-semibold text-gray-900">
                                {{ $g->name }}
                                @if($g->size_label)
                                    <span class="ml-1 inline-flex items-center rounded bg-gray-100 px-1.5 py-0.5 text-xs font-medium text-gray-600">{{ $g->size_label }}</span>
                                @endif
                            </h3>
                            <p class="truncate text-xs text-gray-500">
                                {{ $g->key }}.pdf · {{ number_format($g->bytes / 1024, 0) }} KB
                            </p>
                            @if($g->bundle)
                                <p class="truncate text-xs text-gray-400">bundle: {{ $g->bundle }}</p>
                            @endif
                        </div>
                    </div>

                    <div class="mt-4 flex gap-2">
                        <a href="{{ route('admin.guides.download', $g->key) }}" target="_blank" rel="noopener"
                           class="inline-flex flex-1 items-center justify-center gap-1.5 rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            Preview
                        </a>
                        <a href="{{ route('admin.guides.download', [$g->key, 'dl' => 1]) }}"
                           class="inline-flex flex-1 items-center justify-center gap-1.5 rounded-lg bg-admin-primary-600 px-3 py-2 text-sm font-semibold text-white hover:bg-admin-primary-700">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                            </svg>
                            Download
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</x-admin-layout>
