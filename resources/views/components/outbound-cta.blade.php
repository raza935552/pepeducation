@props([
    'slug',
    'text' => 'Shop Now',
    'variant' => 'primary', // primary, secondary, gold
    'size' => 'md', // sm, md, lg
    'class' => '',
    'newTab' => true,
])

@php
    $baseClasses = 'inline-flex items-center justify-center font-semibold rounded-lg transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2';

    $variants = [
        'primary' => 'bg-brand-navy text-white hover:bg-brand-navy/90 focus:ring-brand-navy',
        'secondary' => 'bg-white text-brand-navy border-2 border-brand-navy hover:bg-brand-navy hover:text-white focus:ring-brand-navy',
        'gold' => 'bg-brand-gold text-white hover:bg-brand-gold/90 focus:ring-brand-gold',
    ];

    $sizes = [
        'sm' => 'px-4 py-2 text-sm',
        'md' => 'px-6 py-3 text-base',
        'lg' => 'px-8 py-4 text-lg',
    ];

    $variantClass = $variants[$variant] ?? $variants['primary'];
    $sizeClass = $sizes[$size] ?? $sizes['md'];
@endphp

<a
    href="{{ route('outbound.track', $slug) }}"
    @if($newTab) target="_blank" rel="noopener noreferrer" @endif
    {{ $attributes->merge(['class' => "$baseClasses $variantClass $sizeClass $class"]) }}
>
    {{ $text }}
    @if($newTab)
        <svg class="ml-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
        </svg>
    @endif
</a>
