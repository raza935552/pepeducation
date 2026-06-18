<form method="GET" action="{{ route('community.search') }}" class="relative">
    <input type="search" name="q" value="{{ $q ?? request('q') }}"
           placeholder="Search discussions…"
           class="w-full rounded-full border-gray-300 pl-10 pr-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-indigo-500">
    <svg class="absolute left-3 top-1/2 -translate-y-1/2 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/>
    </svg>
</form>
