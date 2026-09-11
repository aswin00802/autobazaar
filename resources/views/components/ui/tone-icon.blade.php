@props(['icon', 'tone' => 'brand', 'size' => 24, 'shape' => 'plain'])

{{-- Tinted icon used by the quick-action tiles, scheme cards and feature rows. --}}

@php
    $tones = [
        'brand' => 'text-brand-500',
        'accent' => 'text-accent-600',
        'info' => 'text-info',
        'danger' => 'text-danger',
        'warn' => 'text-warn',
        'violet' => 'text-violet-600',
        'muted' => 'text-muted',
    ];

    $bgs = [
        'brand' => 'bg-brand-50',
        'accent' => 'bg-accent-50',
        'info' => 'bg-blue-50',
        'danger' => 'bg-red-50',
        'warn' => 'bg-orange-50',
        'violet' => 'bg-violet-50',
        'muted' => 'bg-canvas',
    ];

    $color = $tones[$tone] ?? $tones['brand'];
    $bg = $bgs[$tone] ?? $bgs['brand'];
@endphp

@if ($shape === 'circle')
    <span {{ $attributes->merge(['class' => "grid place-items-center rounded-full {$bg} {$color}"]) }}
          style="width: {{ $size * 2 }}px; height: {{ $size * 2 }}px">
        <x-ui.icon :name="$icon" :size="$size" />
    </span>
@else
    <x-ui.icon :name="$icon" :size="$size" {{ $attributes->merge(['class' => $color]) }} />
@endif
