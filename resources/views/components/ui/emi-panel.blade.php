@props(['vehicle'])

@php
    // Defaults match the worked example printed on new_autos.jpeg.
    $price = $vehicle['on_road_price'];
    $down = (int) round($price * 0.2 / 1000) * 1000;
@endphp

<div {{ $attributes->merge(['class' => 'ab-card p-5']) }}
     x-data="emiCalculator({ price: {{ $price }}, downPayment: {{ $down }}, rate: 9.5, tenure: 36 })">

    <h3 class="mb-4 flex items-center gap-2 text-base font-bold">
        <x-ui.icon name="calculator" :size="18" class="text-brand-500" />
        EMI Calculator
    </h3>

    <div class="grid gap-3 sm:grid-cols-2">
        <div>
            <label class="ab-label" :for="$id('price')">Vehicle Price</label>
            <div class="relative">
                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-sm text-muted">₹</span>
                <input :id="$id('price')" type="number" min="0" step="1000" x-model.number="price"
                       class="ab-field pl-7">
            </div>
        </div>

        <div>
            <label class="ab-label" :for="$id('down')">Down Payment</label>
            <div class="relative">
                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-sm text-muted">₹</span>
                <input :id="$id('down')" type="number" min="0" step="1000" x-model.number="downPayment"
                       class="ab-field pl-7">
            </div>
            <p class="mt-1 text-[10px] text-muted">
                <span x-text="downPaymentPercent"></span>% of vehicle price
            </p>
        </div>

        <div>
            <label class="ab-label">Loan Amount</label>
            <div class="ab-field flex items-center bg-canvas font-semibold" x-text="money(loanAmount)"></div>
        </div>

        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="ab-label" :for="$id('rate')">Interest Rate (%)</label>
                <input :id="$id('rate')" type="number" min="0" max="30" step="0.1" x-model.number="rate"
                       class="ab-field">
            </div>
            <div>
                <label class="ab-label" :for="$id('tenure')">Loan Tenure</label>
                <select :id="$id('tenure')" x-model.number="tenure" class="ab-field">
                    <template x-for="t in tenures" :key="t">
                        <option :value="t" x-text="`${t} Months`"></option>
                    </template>
                </select>
            </div>
        </div>
    </div>

    {{-- Results --}}
    <div class="mt-4 grid grid-cols-3 gap-2 rounded-lg bg-brand-50 p-3 text-center">
        <div>
            <p class="text-base font-extrabold text-brand-600 sm:text-lg" x-text="money(monthlyEmi)"></p>
            <p class="text-[10px] text-muted">Monthly EMI</p>
        </div>
        <div class="border-x border-brand-100">
            <p class="text-base font-extrabold sm:text-lg" x-text="money(totalInterest)"></p>
            <p class="text-[10px] text-muted">Total Interest</p>
        </div>
        <div>
            <p class="text-base font-extrabold sm:text-lg" x-text="money(totalPayment)"></p>
            <p class="text-[10px] text-muted">Total Payment</p>
        </div>
    </div>
</div>
