@props([
    'title',
    'lede' => null,
    'href' => null,
    'linkLabel' => 'View All',
    'level' => 'h2',
])

<div data-reveal {{ $attributes->merge(['class' => 'mb-5 flex flex-wrap items-end justify-between gap-3']) }}>
    <div>
        <{{ $level }} class="text-xl font-extrabold tracking-tight sm:text-2xl">{{ $title }}</{{ $level }}>

        @if ($lede)
            <p class="mt-1.5 max-w-2xl text-sm text-muted">{{ $lede }}</p>
        @endif
    </div>

    @if ($href)
        <a href="{{ $href }}"
           class="flex items-center gap-1.5 text-sm font-semibold text-brand-500 transition-colors hover:text-brand-600">
            {{ $linkLabel }}
            <x-ui.icon name="arrow-right" :size="16" />
        </a>
    @endif
</div>
