@if(\App\Models\Setting::getValue('community', 'enabled', false))
    @auth
        @php($ctaUrl = route('community.index'))
        @php($ctaLabel = 'Open the Community')
    @else
        @php($ctaUrl = route('register'))
        @php($ctaLabel = 'Join free to take part')
    @endauth

    <div class="my-10 rounded-2xl bg-gradient-to-br from-indigo-600 to-violet-700 p-6 sm:p-8 text-white">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-5">
            <div>
                <p class="text-indigo-200 text-xs font-semibold uppercase tracking-wide">Members-only community</p>
                <h3 class="text-xl font-bold mt-1">Have a question about this? Ask the community.</h3>
                <p class="text-indigo-100 text-sm mt-1.5 max-w-xl">
                    Join other researchers to discuss the science, compare notes, and get answers — a private space, never indexed or public.
                </p>
            </div>
            <a href="{{ $ctaUrl }}"
               class="inline-flex items-center justify-center gap-1.5 rounded-full bg-white text-indigo-700 font-semibold px-6 py-3 hover:bg-indigo-50 transition shrink-0 whitespace-nowrap">
                {{ $ctaLabel }}
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
            </a>
        </div>
    </div>
@endif
