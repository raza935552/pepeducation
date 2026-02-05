@props(['title' => 'Account'])

<x-public-layout>
    <div class="min-h-screen bg-cream-50 dark:bg-brown-900">
        {{-- Header --}}
        <div class="bg-gradient-to-b from-brown-800 to-brown-900 dark:from-brown-950 dark:to-brown-900 pt-8 pb-16">
            <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
                <h1 class="text-2xl font-bold text-cream-100">Account Settings</h1>
                <p class="text-cream-400 mt-1">Manage your profile and preferences</p>
            </div>
        </div>

        {{-- Tabs + Content --}}
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 -mt-8">
            {{-- Tab Navigation --}}
            <nav class="bg-white dark:bg-brown-800 rounded-xl shadow-sm border border-cream-200 dark:border-brown-700 p-1.5 flex gap-1 mb-6">
                <a href="{{ route('account.profile') }}"
                   class="flex-1 flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('account.profile') ? 'bg-gold-500 text-white' : 'text-gray-600 dark:text-cream-300 hover:bg-cream-100 dark:hover:bg-brown-700' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    <span class="hidden sm:inline">Profile</span>
                </a>
                <a href="{{ route('account.bookmarks') }}"
                   class="flex-1 flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('account.bookmarks') ? 'bg-gold-500 text-white' : 'text-gray-600 dark:text-cream-300 hover:bg-cream-100 dark:hover:bg-brown-700' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/>
                    </svg>
                    <span class="hidden sm:inline">Bookmarks</span>
                </a>
                <a href="{{ route('account.preferences') }}"
                   class="flex-1 flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('account.preferences') ? 'bg-gold-500 text-white' : 'text-gray-600 dark:text-cream-300 hover:bg-cream-100 dark:hover:bg-brown-700' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <span class="hidden sm:inline">Preferences</span>
                </a>
                <a href="{{ route('account.contributions') }}"
                   class="flex-1 flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('account.contributions') ? 'bg-gold-500 text-white' : 'text-gray-600 dark:text-cream-300 hover:bg-cream-100 dark:hover:bg-brown-700' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    <span class="hidden sm:inline">Contributions</span>
                </a>
            </nav>

            {{-- Flash Messages --}}
            @if(session('success'))
                <div class="mb-6 p-4 bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 rounded-xl text-green-700 dark:text-green-300">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Content --}}
            {{ $slot }}
        </div>

        <div class="h-16"></div>
    </div>
</x-public-layout>
