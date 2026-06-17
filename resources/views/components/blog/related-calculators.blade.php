@props(['post'])

@php
    $calculators = app(\App\Services\Blog\CalculatorMatcher::class)->forPost($post, 3);
    $pillarSlug  = 'peptides-101-beginner-complete-guide';
    $showPillar  = $post->slug !== $pillarSlug;
@endphp

@if(!empty($calculators) || $showPillar)
    <section class="mt-12 not-prose" aria-label="Related calculators">
        <div class="rounded-2xl border border-surface-200 bg-surface-50 p-6">
            @if($showPillar)
                <a href="{{ route('blog.show', $pillarSlug) }}"
                   class="flex items-center gap-3 mb-5 rounded-xl bg-white border border-surface-200 p-3.5 hover:border-primary-300 hover:shadow-sm transition-all group">
                    <span class="text-xl" aria-hidden="true">📘</span>
                    <span class="flex-1">
                        <span class="block text-xs font-semibold uppercase tracking-wide text-primary-600">New to peptides?</span>
                        <span class="block text-sm font-medium text-gray-900 group-hover:text-primary-600 transition-colors">Start with Peptides 101 — A Beginner's Complete Guide</span>
                    </span>
                    <svg aria-hidden="true" class="w-4 h-4 text-primary-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                </a>
            @endif

            @if(!empty($calculators))
            <div class="flex items-center gap-2 mb-1">
                <span class="text-lg" aria-hidden="true">🧮</span>
                <h2 class="text-lg font-bold text-gray-900">Related Calculators &amp; Tools</h2>
            </div>
            <p class="text-sm text-gray-500 mb-5">Free tools to put this into practice.</p>
            @endif

            @if(!empty($calculators))
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-3">
                @foreach($calculators as $calc)
                    <a href="{{ $calc['url'] }}"
                       class="group flex flex-col h-full rounded-xl bg-white border border-surface-200 p-4 hover:border-primary-300 hover:shadow-sm transition-all">
                        <div class="flex items-center gap-2 mb-1.5">
                            <span class="text-xl" aria-hidden="true">{{ $calc['emoji'] }}</span>
                            <span class="font-semibold text-gray-900 text-sm group-hover:text-primary-600 transition-colors">{{ $calc['name'] }}</span>
                        </div>
                        <p class="text-xs text-gray-500 leading-snug flex-1">{{ $calc['tagline'] }}</p>
                        <span class="mt-3 inline-flex items-center gap-1 text-xs font-medium text-primary-600">
                            Open tool
                            <svg aria-hidden="true" class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                        </span>
                    </a>
                @endforeach
            </div>
            @endif
        </div>
    </section>
@endif
