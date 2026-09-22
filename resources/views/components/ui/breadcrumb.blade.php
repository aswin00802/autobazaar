@props(['items' => []])

{{-- $items: [['label' => 'Home', 'href' => '...'], ['label' => 'TVS King Deluxe']] --}}

@if (count($items) > 1)
    @push('schema')
        @php
            $crumbSchema = [
                '@context' => 'https://schema.org',
                '@type' => 'BreadcrumbList',
                'itemListElement' => collect($items)->values()->map(fn ($item, $i) => array_filter([
                    '@type' => 'ListItem',
                    'position' => $i + 1,
                    'name' => $item['label'],
                    'item' => $item['href'] ?? url()->current(),
                ]))->all(),
            ];
        @endphp
        <script type="application/ld+json">{!! json_encode($crumbSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP) !!}</script>
    @endpush
@endif

<nav aria-label="Breadcrumb" {{ $attributes->merge(['class' => 'text-xs']) }}>
    <ol class="flex flex-wrap items-center gap-1.5">
        @foreach ($items as $index => $item)
            <li class="flex items-center gap-1.5">
                @if (! empty($item['href']) && ! $loop->last)
                    <a href="{{ $item['href'] }}" class="text-muted transition-colors hover:text-brand-500">
                        {{ $item['label'] }}
                    </a>
                @else
                    <span class="font-semibold text-ink" @if($loop->last) aria-current="page" @endif>
                        {{ $item['label'] }}
                    </span>
                @endif

                @unless ($loop->last)
                    <x-ui.icon name="chevron-right" :size="12" class="text-muted" />
                @endunless
            </li>
        @endforeach
    </ol>
</nav>
