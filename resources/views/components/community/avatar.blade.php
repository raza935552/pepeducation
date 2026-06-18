@props(['user', 'size' => 40])

@php
    // Deterministic accent from the user id so each member has a consistent colour.
    // These are literal class strings so Tailwind's content scanner keeps them.
    $palette = ['bg-indigo-500','bg-emerald-500','bg-rose-500','bg-amber-500','bg-sky-500','bg-violet-500','bg-teal-500','bg-fuchsia-500','bg-cyan-600','bg-orange-500'];
    $color = $palette[($user?->id ?? 0) % count($palette)];
    $px = (int) $size;
@endphp

@if($user?->avatar_url)
    <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}"
         style="height: {{ $px }}px; width: {{ $px }}px;"
         {{ $attributes->merge(['class' => 'rounded-full object-cover ring-2 ring-white shrink-0']) }}>
@else
    <span style="height: {{ $px }}px; width: {{ $px }}px; font-size: {{ max(11, (int) round($px * 0.4)) }}px;"
          {{ $attributes->merge(['class' => "inline-flex items-center justify-center rounded-full {$color} text-white font-semibold ring-2 ring-white shrink-0"]) }}>
        {{ $user?->initials() ?? 'U' }}
    </span>
@endif
