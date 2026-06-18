@props(['user', 'showLabel' => false])

@php
    $seen = $user?->last_seen_at;
    $online = $seen && $seen->gt(now()->subMinutes(10));
    $recent = $seen && $seen->gt(now()->subDay());
    $color = $online ? 'bg-emerald-500' : ($recent ? 'bg-amber-400' : 'bg-gray-300');
    $label = $online ? 'Active now' : ($recent ? 'Active today' : ($seen ? 'Active ' . $seen->diffForHumans() : 'Member'));
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center gap-1']) }} title="{{ $label }}">
    <span class="inline-block h-2 w-2 rounded-full {{ $color }}"></span>
    @if($showLabel)<span class="text-xs text-gray-400">{{ $label }}</span>@endif
</span>
