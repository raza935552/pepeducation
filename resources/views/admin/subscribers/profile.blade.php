<x-admin-layout>
    <x-slot name="title">Subscriber Profile</x-slot>

    <div class="space-y-6">
        {{-- Header --}}
        <div class="flex items-center justify-between">
            <a href="{{ route('admin.subscribers.index') }}"
               class="inline-flex items-center gap-2 text-gray-600 hover:text-gray-900">
                <svg aria-hidden="true" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Subscribers
            </a>
            @if($subscriber->klaviyo_id)
                <span class="px-3 py-1 bg-emerald-100 text-emerald-700 rounded-full text-sm">
                    Synced to Klaviyo
                </span>
            @endif
        </div>

        {{-- Profile Header Card --}}
        @include('admin.subscribers.partials.profile-header')

        {{-- Stats Grid --}}
        @include('admin.subscribers.partials.profile-stats')

        {{-- Two Column Layout --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- Left Column --}}
            <div class="space-y-6">
                @include('admin.subscribers.partials.profile-attribution')
                @include('admin.subscribers.partials.profile-sessions')
            </div>

            {{-- Right Column --}}
            <div class="space-y-6">
                @include('admin.subscribers.partials.profile-quizzes')
                @include('admin.subscribers.partials.profile-clicks')
            </div>
        </div>

        {{-- Full Width: Downloads & Timeline --}}
        @include('admin.subscribers.partials.profile-downloads')
        @include('admin.subscribers.partials.profile-timeline')
    </div>
</x-admin-layout>
