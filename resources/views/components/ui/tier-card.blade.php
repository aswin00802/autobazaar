@props(['tier', 'compact' => false])

@php
    $styles = [
        'brand' => ['bg' => 'bg-brand-50', 'badge' => 'bg-brand-500 text-white', 'icon' => 'text-brand-500',
                    'btn' => 'bg-brand-500 text-white hover:bg-brand-600', 'tick' => 'text-brand-500'],
        'info' => ['bg' => 'bg-blue-50', 'badge' => 'bg-info text-white', 'icon' => 'text-info',
                   'btn' => 'bg-info text-white hover:brightness-95', 'tick' => 'text-info'],
        'warn' => ['bg' => 'bg-orange-50', 'badge' => 'bg-warn text-white', 'icon' => 'text-warn',
                   'btn' => 'bg-warn text-white hover:brightness-95', 'tick' => 'text-warn'],
    ][$tier['tone']];
@endphp

<article {{ $attributes->merge(['class' => "ab-card flex flex-col p-5 {$styles['bg']}"]) }}>

    <div class="mb-3 flex flex-wrap items-start justify-between gap-2">
        <h3 class="text-sm font-bold leading-snug">{{ $tier['title'] }}</h3>
        <span class="rounded-full px-2.5 py-1 text-[10px] font-bold whitespace-nowrap {{ $styles['badge'] }}">
            {{ $tier['badge'] }}
        </span>
    </div>

    <p class="text-xs text-ink-soft">{{ $tier['note'] }}</p>

    <div class="mt-4 flex gap-4">
        <span class="hidden h-16 w-16 shrink-0 place-items-center rounded-full bg-surface sm:grid {{ $styles['icon'] }}">
            <x-ui.icon :name="$tier['icon']" :size="30" />
        </span>

        <ul class="flex-1 space-y-1.5">
            @foreach ($tier['bullets'] as $bullet)
                <li class="flex items-start gap-2 text-xs">
                    <x-ui.icon name="check" :size="13" class="mt-0.5 shrink-0 {{ $styles['tick'] }}" />
                    {{ $bullet }}
                </li>
            @endforeach
        </ul>
    </div>

    <a href="{{ route('site.enquiry') }}"
       class="ab-btn mt-5 w-full text-xs {{ $styles['btn'] }}">
        {{ $tier['cta'] }}
        <x-ui.icon name="arrow-right" :size="15" />
    </a>
</article>
