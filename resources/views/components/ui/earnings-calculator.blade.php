@props(['vehicle', 'open' => false])

{{--
    "Calculate Your Earnings" — fare per km × km/day minus the running cost
    per km from VehicleCatalogService::runningCost(). Collapsed until opened.
--}}

@php
    $running = $vehicle['running_cost'] ?? null;
@endphp

<div {{ $attributes->merge(['class' => 'ab-card overflow-hidden']) }}
     x-data="earningsCalculator({
        farePerKm: 12,
        kmPerDay: {{ (int) ($running['daily_km'] ?? 150) }},
        costPerKm: {{ (float) ($running['per_km'] ?? 0) }},
        open: {{ $open ? 'true' : 'false' }},
     })">

    <button type="button" @click="toggle()" :aria-expanded="open"
            class="flex w-full items-center justify-between gap-3 bg-accent-50 px-5 py-4 text-left transition-colors hover:bg-accent-100">
        <span class="flex items-center gap-2 text-sm font-bold">
            <x-ui.tone-icon icon="chart" tone="accent" :size="18" shape="circle" />
            Calculate Your Earnings
        </span>
        <x-ui.icon name="chevron-down" :size="18" ::class="open && 'rotate-180'" class="text-muted transition-transform" />
    </button>

    <div x-show="open" x-transition.opacity x-cloak x-ref="panel" class="p-5">
        <div class="grid gap-3 sm:grid-cols-3">
            <div>
                <label class="ab-label" :for="$id('fare')">Fare per km (₹)</label>
                <input :id="$id('fare')" type="number" min="0" step="0.5" inputmode="decimal" x-model.number="farePerKm" class="ab-field">
            </div>
            <div>
                <label class="ab-label" :for="$id('km')">Kilometres per day</label>
                <input :id="$id('km')" type="number" min="0" step="10" inputmode="numeric" x-model.number="kmPerDay" class="ab-field">
            </div>
            <div>
                <label class="ab-label" :for="$id('other')">Other expenses / day (₹)</label>
                <input :id="$id('other')" type="number" min="0" step="10" inputmode="numeric" x-model.number="otherDaily" class="ab-field"
                       placeholder="Permit, parking…">
            </div>
        </div>

        <dl class="mt-4 space-y-1.5 rounded-lg bg-canvas px-4 py-3 text-xs">
            <div class="flex justify-between"><dt class="text-muted">Daily fare income</dt><dd class="font-semibold" x-text="money(dailyGross)"></dd></div>
            <div class="flex justify-between">
                <dt class="text-muted">Fuel cost (₹{{ number_format((float) ($running['per_km'] ?? 0), 2) }}/km{{ $running ? ', ' . $running['fuel'] : '' }})</dt>
                <dd class="font-semibold text-danger">− <span x-text="money(dailyFuel)"></span></dd>
            </div>
            <div class="flex justify-between" x-show="otherDaily > 0"><dt class="text-muted">Other expenses</dt><dd class="font-semibold text-danger">− <span x-text="money(otherDaily)"></span></dd></div>
        </dl>

        <div class="mt-3 grid grid-cols-3 gap-2 rounded-lg bg-brand-50 p-3 text-center" aria-live="polite">
            <div>
                <p class="text-base font-extrabold text-brand-600 sm:text-lg" x-text="money(dailyNet)"></p>
                <p class="text-[10px] text-muted">Net / Day</p>
            </div>
            <div class="border-x border-brand-100">
                <p class="text-base font-extrabold sm:text-lg" x-text="money(monthlyNet)"></p>
                <p class="text-[10px] text-muted">Net / Month</p>
            </div>
            <div>
                <p class="text-base font-extrabold sm:text-lg" x-text="money(annualNet)"></p>
                <p class="text-[10px] text-muted">Net / Year</p>
            </div>
        </div>

        <p class="mt-2 text-[10px] text-muted">
            Estimate only — month = 30 days, year = 360 days. Excludes EMI, insurance and maintenance.
        </p>
    </div>
</div>
