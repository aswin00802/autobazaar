@props(['rating' => 0, 'reviews' => null, 'size' => 14, 'showValue' => true, 'compact' => false])

@php
    $rating = (float) $rating;
    $full = (int) floor($rating);
    $hasHalf = ($rating - $full) >= 0.25 && ($rating - $full) < 0.75;
    $roundedUp = ($rating - $full) >= 0.75;
    $solid = $roundedUp ? $full + 1 : $full;
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center gap-1.5']) }}>
    <span class="inline-flex items-center gap-0.5 text-accent-500"
          role="img"
          aria-label="Rated {{ $rating }} out of 5">
        @for ($i = 1; $i <= 5; $i++)
            @if ($i <= $solid)
                <x-ui.icon name="star" :size="$size" />
            @elseif ($i === $solid + 1 && $hasHalf)
                {{-- Half star: the filled half overlays a muted full star --}}
                <span class="relative inline-block" style="width: {{ $size }}px; height: {{ $size }}px">
                    <x-ui.icon name="star" :size="$size" class="absolute inset-0 text-line" />
                    <x-ui.icon name="star-half" :size="$size" class="absolute inset-0" />
                </span>
            @else
                <x-ui.icon name="star" :size="$size" class="text-line" />
            @endif
        @endfor
    </span>

    @if ($showValue)
        <span class="text-xs font-bold">{{ number_format($rating, 1) }}</span>
    @endif

    @if ($reviews !== null)
        {{-- Narrow cards get just the number; the word does not fit --}}
        <span class="text-[11px] whitespace-nowrap text-muted">
            ({{ $reviews }}{{ $compact ? '' : ' reviews' }})
        </span>
    @endif
</span>
