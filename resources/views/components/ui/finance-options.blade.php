@props(['vehicle', 'id' => 'price-emi'])

{{--
    Finance Options — lender tabs, loan amount + tenure, live EMI and a
    tenure comparison table. Rates come from finance_lender_rates through
    VehicleCatalogService::lenders(); nothing here is hard-coded.
--}}

@php
    $finance = $vehicle['finance'] ?? ['lenders' => [], 'loan_amount' => 0, 'default_tenure' => 36];
    $lenders = array_map(fn ($l) => $l + ['logo_url' => $l['logo'] ? asset($l['logo']) : null], $finance['lenders']);
@endphp

<div id="{{ $id }}" {{ $attributes->merge(['class' => 'ab-card p-5']) }}
     x-data="financeOptions({
        lenders: {{ Js::from($lenders) }},
        onRoad: {{ (int) $vehicle['on_road_price'] }},
        loanAmount: {{ (int) $finance['loan_amount'] }},
        tenure: {{ (int) $finance['default_tenure'] }},
     })">

    <div class="mb-4 flex items-center justify-between gap-3">
        <h2 class="flex items-center gap-2 text-base font-bold">
            <x-ui.tone-icon icon="bank" tone="brand" :size="18" shape="circle" />
            Finance Options
        </h2>
        <span class="hidden text-[11px] text-muted sm:block">On-road ₹{{ number_format($vehicle['on_road_price']) }}</span>
    </div>

    @if (empty($lenders))
        <p class="rounded-lg bg-canvas px-3 py-3 text-sm text-muted">
            Finance partners will be listed here soon. Ask us on WhatsApp for current loan offers.
        </p>
    @else
        {{-- Lender tabs --}}
        <div class="ab-tabs-scroll -mx-1 flex gap-2 overflow-x-auto px-1 pb-1" role="tablist" aria-label="Finance partners">
            <template x-for="(lender, i) in lenders" :key="lender.id">
                <button type="button" role="tab"
                        @click="selectLender(i)"
                        :aria-selected="lenderIndex === i"
                        :class="lenderIndex === i
                            ? 'border-brand-500 bg-brand-50 ring-1 ring-brand-500'
                            : 'border-line bg-surface hover:border-brand-300'"
                        class="flex h-12 min-w-24 shrink-0 items-center justify-center rounded-lg border px-3 transition-colors"
                        :title="lender.name">
                    <img x-show="lender.logo_url && logoOk(i)" :src="lender.logo_url" :alt="lender.name"
                         x-on:error="markLogoFailed(i)" class="max-h-8 w-auto max-w-24 object-contain" loading="lazy">
                    <span x-show="!lender.logo_url || !logoOk(i)" x-cloak
                          class="max-w-28 truncate text-xs font-bold text-ink-soft" x-text="lender.name"></span>
                </button>
            </template>
        </div>

        <p class="mt-2 text-[11px] text-muted">
            <span class="font-semibold text-ink-soft" x-text="lender?.name"></span>
            <span x-show="lender?.type"> · <span x-text="lender?.type"></span></span>
        </p>

        {{-- Inputs --}}
        <div class="mt-4 grid gap-3 sm:grid-cols-3">
            <div>
                <label class="ab-label" :for="$id('loan')">Loan Amount</label>
                <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-sm text-muted">₹</span>
                    <input :id="$id('loan')" type="number" min="0" step="1000" inputmode="numeric"
                           x-model.number="loanAmount" class="ab-field pl-7" :aria-invalid="overLimit">
                </div>
            </div>

            <div>
                <label class="ab-label" :for="$id('tenure')">Tenure</label>
                <select :id="$id('tenure')" x-model.number="tenure" class="ab-field">
                    <template x-for="m in tenures" :key="m">
                        <option :value="m" x-text="`${tenureLabel(m)} (${m} Months)`"></option>
                    </template>
                </select>
            </div>

            <div>
                <label class="ab-label">Rate of Interest</label>
                <div class="ab-field flex items-center justify-between bg-canvas font-semibold" aria-live="polite">
                    <span><span x-text="rate"></span>% p.a.</span>
                    <span class="text-[10px] font-normal text-muted">lender rate</span>
                </div>
            </div>
        </div>

        <p x-show="overLimit" x-cloak class="mt-2 flex items-start gap-1.5 text-[11px] text-warn">
            <x-ui.icon name="warning" :size="14" class="mt-0.5" />
            <span>This lender funds up to <span x-text="lender?.max_loan_pct"></span>% of on-road price
                (<span x-text="money(maxLoan)"></span>). Higher amounts may need a co-applicant.</span>
        </p>

        {{-- Live result --}}
        <div class="mt-4 grid grid-cols-3 gap-2 rounded-lg bg-brand-50 p-3 text-center" aria-live="polite">
            <div>
                <p class="text-lg font-extrabold text-brand-600" x-text="money(emi)"></p>
                <p class="text-[10px] text-muted">EMI / Month</p>
            </div>
            <div class="border-x border-brand-100">
                <p class="text-lg font-extrabold" x-text="money(totalInterest)"></p>
                <p class="text-[10px] text-muted">Total Interest</p>
            </div>
            <div>
                <p class="text-lg font-extrabold" x-text="money(totalPayment)"></p>
                <p class="text-[10px] text-muted">Total Payable</p>
            </div>
        </div>

        {{-- Tenure comparison --}}
        <div class="mt-4 overflow-hidden rounded-lg border border-line">
            <table class="w-full text-xs">
                <caption class="sr-only">EMI for every tenure offered by the selected lender</caption>
                <thead class="bg-canvas text-[11px] uppercase tracking-wide text-muted">
                    <tr>
                        <th scope="col" class="px-3 py-2 text-left font-semibold">Tenure</th>
                        <th scope="col" class="px-3 py-2 text-right font-semibold">EMI / Month</th>
                        <th scope="col" class="px-3 py-2 text-right font-semibold">Total Interest</th>
                        <th scope="col" class="px-3 py-2 text-right font-semibold">Total Payment</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-line">
                    <template x-for="row in table" :key="row.months">
                        <tr @click="tenure = row.months"
                            :class="isSelected(row.months) ? 'bg-accent-50 font-bold text-ink' : 'cursor-pointer hover:bg-canvas'"
                            class="transition-colors">
                            <td class="px-3 py-2">
                                <span x-text="row.label"></span>
                                <span x-show="isSelected(row.months)" class="ml-1 rounded bg-accent-500 px-1.5 py-0.5 text-[9px] font-bold uppercase">Selected</span>
                            </td>
                            <td class="px-3 py-2 text-right tabular-nums" :class="isSelected(row.months) && 'text-brand-600'" x-text="money(row.emi)"></td>
                            <td class="px-3 py-2 text-right tabular-nums" x-text="money(row.totalInterest)"></td>
                            <td class="px-3 py-2 text-right tabular-nums" x-text="money(row.totalPayment)"></td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

        <button type="button" @click="applyLoan()" class="ab-btn ab-btn-primary mt-4 w-full">
            Apply for Loan / Check Eligibility <x-ui.icon name="arrow-right" :size="16" />
        </button>

        {{-- Loan limits + documents --}}
        <p class="mt-3 flex items-start gap-1.5 text-[11px] text-muted">
            <x-ui.icon name="info" :size="14" class="mt-0.5 shrink-0" />
            <span>
                Loan up to <strong class="text-ink-soft"><span x-text="lender?.max_loan_pct"></span>%</strong> of on-road price with CIBIL
                (<span x-text="money(maxLoan)"></span>)<template x-if="maxLoanNoCibil > 0"><span>,
                up to <strong class="text-ink-soft"><span x-text="lender?.max_loan_pct_no_cibil"></span>%</strong> without CIBIL
                (<span x-text="money(maxLoanNoCibil)"></span>)</span></template>.
                <template x-if="lender?.processing_fee_pct > 0"><span>Processing fee <span x-text="lender?.processing_fee_pct"></span>%.</span></template>
            </span>
        </p>

        <div class="mt-2 border-t border-line pt-2" x-show="lender?.documents?.length">
            <button type="button" @click="docsOpen = !docsOpen" :aria-expanded="docsOpen"
                    class="flex w-full items-center justify-between py-1 text-xs font-semibold text-brand-500">
                <span class="flex items-center gap-1.5"><x-ui.icon name="doc" :size="14" /> Documents required</span>
                <x-ui.icon name="chevron-down" :size="16" ::class="docsOpen && 'rotate-180'" class="transition-transform" />
            </button>
            <ul x-show="docsOpen" x-transition.opacity x-cloak class="grid gap-1 pb-1 pt-1 sm:grid-cols-2">
                <template x-for="doc in lender?.documents ?? []" :key="doc">
                    <li class="flex items-start gap-1.5 text-xs text-ink-soft">
                        <x-ui.icon name="check" :size="13" class="mt-0.5 text-success" /> <span x-text="doc"></span>
                    </li>
                </template>
            </ul>
        </div>

        <p class="mt-2 text-[10px] text-muted">*EMI is indicative. Final rate and amount depend on the lender's approval.</p>
    @endif
</div>
