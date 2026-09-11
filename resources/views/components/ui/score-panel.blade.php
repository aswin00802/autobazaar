@props(['scores'])

@php
    // Dial geometry: a 44px-radius circle, dash-offset driven by the score.
    $radius = 44;
    $circumference = 2 * M_PI * $radius;
    $progress = $circumference * ($scores['overall'] / 10);

    $barTones = [
        'brand' => 'bg-brand-500',
        'accent' => 'bg-accent-500',
        'info' => 'bg-info',
        'violet' => 'bg-violet-500',
    ];
@endphp

<div {{ $attributes->merge(['class' => 'ab-card p-5']) }}>
    <h3 class="mb-4 flex items-center gap-2 text-base font-bold">
        <x-ui.icon name="chart" :size="18" class="text-brand-500" />
        AutoBazaar Score & Rating
    </h3>

    <div class="grid gap-6 sm:grid-cols-5 sm:items-center">

        {{-- Overall dial --}}
        <div class="sm:col-span-2">
            <p class="mb-2 text-center text-xs font-semibold text-muted">Overall Score</p>

            <div class="relative mx-auto h-28 w-28">
                <svg viewBox="0 0 100 100" class="h-full w-full -rotate-90" aria-hidden="true">
                    <circle cx="50" cy="50" r="{{ $radius }}" fill="none"
                            stroke="var(--color-line)" stroke-width="8" />
                    <circle cx="50" cy="50" r="{{ $radius }}" fill="none"
                            stroke="var(--color-brand-500)" stroke-width="8" stroke-linecap="round"
                            stroke-dasharray="{{ $circumference }}"
                            stroke-dashoffset="{{ $circumference - $progress }}" />
                </svg>

                <div class="absolute inset-0 grid place-content-center text-center">
                    <span class="block text-3xl font-extrabold leading-none">{{ $scores['overall'] }}</span>
                    <span class="block text-xs text-muted">/ 10</span>
                </div>
            </div>

            <p class="mx-auto mt-3 w-fit rounded-full bg-brand-50 px-3 py-1 text-[11px] font-bold text-brand-600">
                {{ $scores['rank_note'] }}
            </p>
        </div>

        {{-- Breakdown bars --}}
        <ul class="space-y-2 sm:col-span-3">
            @foreach ($scores['breakdown'] as $item)
                <li class="grid grid-cols-[1fr_auto] items-center gap-x-3">
                    <span class="text-xs text-ink-soft">{{ $item['label'] }}</span>
                    <span class="text-xs font-bold tabular-nums">{{ number_format($item['score'], 1) }}</span>

                    <div class="col-span-2 h-1.5 overflow-hidden rounded-full bg-line">
                        <div class="h-full rounded-full {{ $barTones[$item['tone']] ?? $barTones['brand'] }}"
                             style="width: {{ $item['score'] * 10 }}%"></div>
                    </div>
                </li>
            @endforeach
        </ul>
    </div>

    <p class="mt-5 flex items-start gap-2 rounded-lg bg-accent-50 px-3 py-2.5 text-xs text-ink-soft">
        <x-ui.icon name="trophy" :size="16" class="mt-0.5 shrink-0 text-accent-600" />
        {{ $scores['summary'] }}
    </p>
</div>
