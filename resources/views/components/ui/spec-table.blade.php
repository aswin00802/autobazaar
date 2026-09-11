@props(['specifications' => [], 'href' => null, 'full' => false])

@php
    // The overview tab shows a preview; the Specifications tab shows everything.
    $rows = $full ? $specifications : array_slice($specifications, 0, 12);
@endphp

<div {{ $attributes->merge(['class' => 'ab-card p-5']) }}>
    <div class="mb-4 flex items-center justify-between gap-3">
        <h3 class="flex items-center gap-2 text-base font-bold">
            <x-ui.icon name="gear" :size="18" class="text-brand-500" />
            Specifications
        </h3>

        @if ($href && ! $full)
            <a href="{{ $href }}#specifications"
               class="flex items-center gap-1 text-xs font-semibold text-brand-500 underline-offset-2 hover:underline">
                View Complete Specs <x-ui.icon name="arrow-right" :size="14" />
            </a>
        @endif
    </div>

    <dl class="divide-y divide-line {{ $full ? 'sm:grid sm:grid-cols-2 sm:gap-x-8 sm:divide-y-0' : '' }}">
        @foreach ($rows as $spec)
            <div class="flex items-baseline justify-between gap-4 py-2 {{ $full ? 'border-b border-line' : '' }}">
                <dt class="text-xs text-muted">{{ $spec['label'] }}</dt>
                <dd class="text-right text-xs font-semibold">{{ $spec['value'] }}</dd>
            </div>
        @endforeach
    </dl>
</div>
