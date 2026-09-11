@props(['scheme'])

@php
    $styles = [
        'brand' => ['bg' => 'bg-brand-50', 'accent' => 'text-brand-600', 'btn' => 'bg-brand-500 text-white hover:bg-brand-600'],
        'info' => ['bg' => 'bg-blue-50', 'accent' => 'text-info', 'btn' => 'bg-info text-white hover:brightness-95'],
        'accent' => ['bg' => 'bg-accent-50', 'accent' => 'text-accent-700', 'btn' => 'bg-accent-500 text-ink hover:bg-accent-600'],
        'danger' => ['bg' => 'bg-red-50', 'accent' => 'text-danger', 'btn' => 'bg-danger text-white hover:brightness-95'],
        'violet' => ['bg' => 'bg-violet-50', 'accent' => 'text-violet-600', 'btn' => 'bg-violet-600 text-white hover:brightness-95'],
    ][$scheme['tone']] ?? [
        'bg' => 'bg-canvas', 'accent' => 'text-brand-600', 'btn' => 'bg-brand-500 text-white hover:bg-brand-600',
    ];
@endphp

<article {{ $attributes->merge(['class' => "ab-card flex h-full flex-col p-4 {$styles['bg']}"]) }}>

    <div class="mb-2 flex items-start gap-2.5">
        <x-ui.tone-icon :icon="$scheme['icon']" :tone="$scheme['tone']" :size="18" shape="circle" />

        <div class="flex-1">
            <h3 class="text-sm font-extrabold leading-snug">{{ $scheme['title'] }}</h3>
            <p class="text-[11px] text-muted">{{ $scheme['authority'] }}</p>
        </div>

        @if ($scheme['featured'])
            <span class="rounded-full bg-accent-500 px-2 py-0.5 text-[9px] font-bold text-ink">Featured</span>
        @endif
    </div>

    @if ($scheme['benefit_label'])
        <p class="mt-1 text-[11px] text-muted">{{ $scheme['benefit_label'] }}</p>
    @endif

    <p class="text-xl font-extrabold {{ $styles['accent'] }}">{{ $scheme['benefit'] }}</p>

    @if ($scheme['benefit_note'])
        <p class="text-[11px] text-muted">{{ $scheme['benefit_note'] }}</p>
    @endif

    <ul class="mt-3 space-y-1.5">
        @foreach ($scheme['bullets'] as $bullet)
            <li class="flex items-start gap-2 text-[11px]">
                <x-ui.icon name="check" :size="12" class="mt-0.5 shrink-0 {{ $styles['accent'] }}" />
                {{ $bullet }}
            </li>
        @endforeach
    </ul>

    <p class="ab-script mt-3 text-base leading-tight {{ $styles['accent'] }}">{{ $scheme['strap'] }}</p>

    {{-- mt-auto pins the CTA to the card foot so a row of cards lines up --}}
    <a href="{{ route('site.enquiry') }}"
       class="ab-btn mt-auto w-full text-xs {{ $styles['btn'] }}">
        {{ $scheme['cta'] }}
        <x-ui.icon name="arrow-right" :size="13" />
    </a>
</article>
