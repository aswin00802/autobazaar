@props(['vehicle'])

<div {{ $attributes->merge(['class' => 'ab-card p-5']) }}
     x-data="operatingCost({ fuels: {{ Js::from($vehicle['operating_cost_fuels']) }}, dailyKm: 100 })">

    <h3 class="mb-4 flex items-center gap-2 text-base font-bold">
        <x-ui.icon name="fuel" :size="18" class="text-brand-500" />
        Operating Cost Calculator
    </h3>

    {{-- Fuel tabs --}}
    <div class="mb-4 flex flex-wrap gap-1.5">
        <template x-for="fuel in fuels" :key="fuel.key">
            <button type="button" @click="selectFuel(fuel.key)"
                    :class="active === fuel.key
                        ? 'bg-brand-500 text-white'
                        : 'bg-canvas text-ink-soft hover:bg-line'"
                    class="rounded-lg px-3 py-1.5 text-xs font-semibold transition-colors"
                    x-text="fuel.label"></button>
        </template>
    </div>

    <div class="grid gap-3 sm:grid-cols-3">
        <div>
            <label class="ab-label" :for="$id('fp')">Fuel Price (₹/<span x-text="activeFuel.unit?.split('/')[1] ?? 'litre'"></span>)</label>
            <input :id="$id('fp')" type="number" min="0" step="0.01" x-model.number="fuelPrice" class="ab-field">
        </div>

        <div>
            <label class="ab-label" :for="$id('mi')">Mileage (<span x-text="activeFuel.unit ?? 'km/litre'"></span>)</label>
            <input :id="$id('mi')" type="number" min="1" step="0.1" x-model.number="mileage" class="ab-field">
        </div>

        <div>
            <label class="ab-label" :for="$id('km')">Daily Kilometres</label>
            <input :id="$id('km')" type="number" min="0" step="5" x-model.number="dailyKm" class="ab-field">
        </div>
    </div>

    {{-- Results --}}
    <div class="mt-4 grid grid-cols-2 gap-2 rounded-lg bg-brand-50 p-3 text-center sm:grid-cols-4">
        <div>
            <p class="text-base font-extrabold text-brand-600" x-text="perKmLabel()"></p>
            <p class="text-[10px] text-muted">Cost per KM</p>
        </div>
        <div>
            <p class="text-base font-extrabold" x-text="money(dailyCost)"></p>
            <p class="text-[10px] text-muted">Daily Cost</p>
        </div>
        <div>
            <p class="text-base font-extrabold" x-text="money(monthlyCost)"></p>
            <p class="text-[10px] text-muted">Monthly Cost</p>
        </div>
        <div>
            <p class="text-base font-extrabold" x-text="money(yearlyCost)"></p>
            <p class="text-[10px] text-muted">Yearly Cost</p>
        </div>
    </div>

    <p class="mt-2 text-[10px] text-muted">
        Monthly assumes 30 days and yearly 360 days of running.
    </p>
</div>
