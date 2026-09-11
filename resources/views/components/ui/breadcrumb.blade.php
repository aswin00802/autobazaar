@props(['items' => []])

{{-- $items: [['label' => 'Home', 'href' => '...'], ['label' => 'TVS King Deluxe']] --}}

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
