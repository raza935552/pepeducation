<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.outbound-links.index') }}" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <span>Edit: {{ $outboundLink->name }}</span>
        </div>
    </x-slot>

    @if(session('success'))
        <div class="mb-6 rounded-lg bg-green-50 p-4 text-green-700">{{ session('success') }}</div>
    @endif

    @include('admin.outbound-links.partials.form', ['link' => $outboundLink, 'recentClicks' => $recentClicks])
</x-admin-layout>
