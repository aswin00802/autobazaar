@props(['vehicles' => []])

{{--
    Vehicle filter rail. Presentational only — the parent page owns the
    `filterRail(...)` Alpine scope so the same state drives both this rail and
    the results grid beside it.
--}}

@php
    $brands = collect($vehicles)->pluck('brand')->unique()->values();

    $fuels = collect($vehicles)
        ->flatMap(fn ($v) => collect($v['variants'])->pluck('label'))
        ->unique()->values();

    $seating = collect($vehicles)->pluck('seating')->unique()->sort()->values();

    $useCases = [
        'personal' => 'Personal Use',
        'commercial' => 'Commercial',
        'high-mileage' => 'High Mileage',
        'low-maintenance' => 'Low Maintenance',
    ];
@endphp

<div {{ $attributes }}>
    <button type="button" @click="mobileOpen = !mobileOpen" class="ab-btn ab-btn-ghost mb-3 w-full lg:hidden">
        <x-ui.icon name="filter" :size="16" />
        Filters
        <span x-show="activeCount > 0" x-cloak x-text="`(${activeCount})`" class="font-bold text-brand-500"></span>
    </button>

    <div :class="mobileOpen ? 'block' : 'hidden lg:block'" class="hidden lg:block">
        <div class="ab-card p-4">
            <div class="mb-4 flex items-center justify-between">
                <h2 class="text-sm font-bold">Filters</h2>
                <button type="button" @click="clearAll()"
                        class="text-[11px] font-semibold text-brand-500 underline underline-offset-2">
                    Clear All
                </button>
            </div>

            <fieldset class="mb-4">
                <legend class="ab-label">Fuel Type</legend>
                @foreach ($fuels as $fuel)
                    <label class="flex items-center gap-2 py-1 text-xs">
                        <input type="checkbox"
                               :checked="isChecked('fuels', @js($fuel))"
                               @change="toggle('fuels', @js($fuel))"
                               class="h-3.5 w-3.5 rounded border-line text-brand-500 focus:ring-brand-400">
                        {{ $fuel }}
                    </label>
                @endforeach
            </fieldset>

            <fieldset class="mb-4">
                <legend class="ab-label">Brand</legend>
                @foreach ($brands as $brand)
                    <label class="flex items-center gap-2 py-1 text-xs">
                        <input type="checkbox"
                               :checked="isChecked('brand', @js($brand))"
                               @change="toggle('brand', @js($brand))"
                               class="h-3.5 w-3.5 rounded border-line text-brand-500 focus:ring-brand-400">
                        {{ $brand }}
                    </label>
                @endforeach
            </fieldset>

            <fieldset class="mb-4">
                <legend class="ab-label">Seating Capacity</legend>
                @foreach ($seating as $seats)
                    <label class="flex items-center gap-2 py-1 text-xs">
                        <input type="checkbox"
                               :checked="isChecked('seating', {{ $seats }})"
                               @change="toggle('seating', {{ $seats }})"
                               class="h-3.5 w-3.5 rounded border-line text-brand-500 focus:ring-brand-400">
                        {{ $seats }} Seater
                    </label>
                @endforeach
            </fieldset>

            <div class="mb-4">
                <span class="ab-label">Price Range</span>
                <input type="range" min="100000" max="500000" step="10000"
                       x-model.number="maxPrice" @input="onMaxChange()"
                       class="w-full accent-brand-500" aria-label="Maximum price">
                <p class="mt-1 text-xs font-semibold">
                    ₹<span x-text="(minPrice / 100000).toFixed(1)"></span> Lakh –
                    ₹<span x-text="(maxPrice / 100000).toFixed(1)"></span> Lakh
                </p>
            </div>

            <fieldset>
                <legend class="ab-label">Use Case</legend>
                @foreach ($useCases as $key => $label)
                    <label class="flex items-center gap-2 py-1 text-xs">
                        <input type="checkbox"
                               :checked="isChecked('use_case', @js($key))"
                               @change="toggle('use_case', @js($key))"
                               class="h-3.5 w-3.5 rounded border-line text-brand-500 focus:ring-brand-400">
                        {{ $label }}
                    </label>
                @endforeach
            </fieldset>
        </div>
    </div>
</div>
