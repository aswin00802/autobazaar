@props(['vehicle', 'site', 'url'])

{{--
    One modal for every lead type on the vehicle page — Enquire / Quotation /
    Test drive / Loan. Open it from anywhere with:

        window.dispatchEvent(new CustomEvent('lead-open', { detail: { source: 'test_drive' } }))

    Posts JSON to preview.vehicles.lead; the enquiry number comes back on success.
--}}

@php
    $variants = collect($vehicle['variants'] ?? []);
    $defaultVariant = $variants->firstWhere('is_default', true) ?? $variants->first();
    $slots = \App\Services\VehicleLeadService::TIME_SLOTS;
@endphp

<div x-data="leadForm({
        url: @js($url),
        csrf: @js(csrf_token()),
        modal: true,
        defaults: { vehicle_variant_id: @js($defaultVariant['id'] ?? '') },
     })"
     x-show="open" x-cloak
     @keydown.escape.window="close()"
     @keydown.window="trap($event)"
     class="fixed inset-0 z-50 flex items-end justify-center sm:items-center sm:p-4"
     role="dialog" aria-modal="true" :aria-labelledby="$id('lead-title')">

    {{-- Backdrop --}}
    <div class="absolute inset-0 bg-ink/60" @click="close()" x-transition.opacity aria-hidden="true"></div>

    {{-- Panel --}}
    <div x-ref="panel"
         x-show="open"
         x-transition:enter="transition duration-200 ease-out"
         x-transition:enter-start="translate-y-6 opacity-0 sm:translate-y-0 sm:scale-95"
         x-transition:enter-end="translate-y-0 opacity-100 sm:scale-100"
         class="relative flex max-h-[92vh] w-full max-w-lg flex-col overflow-hidden rounded-t-2xl bg-surface shadow-2xl sm:rounded-2xl">

        {{-- Header --}}
        <div class="flex items-start gap-3 border-b border-line bg-brand-500 px-5 py-4 text-white">
            <div class="min-w-0 flex-1">
                <h2 :id="$id('lead-title')" class="text-base font-bold" x-text="title"></h2>
                <p class="mt-0.5 text-xs text-white/80" x-text="lede"></p>
                <p class="mt-1.5 truncate text-[11px] font-semibold text-accent-300">{{ $vehicle['name'] }} · ₹{{ number_format($vehicle['on_road_price']) }} on-road</p>
            </div>
            <button type="button" @click="close()" class="rounded-lg p-1.5 text-white/80 transition-colors hover:bg-white/10 hover:text-white"
                    aria-label="Close">
                <x-ui.icon name="close" :size="20" />
            </button>
        </div>

        <div class="overflow-y-auto px-5 py-4">

            {{-- Success --}}
            <div x-show="state === 'success'" x-cloak class="py-4 text-center" role="status">
                <span class="mx-auto grid h-14 w-14 place-items-center rounded-full bg-brand-50 text-brand-500">
                    <x-ui.icon name="check-circle" :size="34" />
                </span>
                <p class="mt-3 text-base font-bold" x-text="message"></p>
                <p x-show="enquiryNo" class="mt-2 text-sm text-muted">
                    Your reference number is
                    <span class="rounded bg-accent-50 px-2 py-0.5 font-mono font-bold text-ink" x-text="enquiryNo"></span>
                </p>
                <p class="mt-3 text-xs text-muted">
                    Need it faster? Call
                    <a href="tel:{{ $site['contact']['phone_e164'] }}" class="font-semibold text-brand-500">{{ $site['contact']['phone'] }}</a>
                </p>
                <button type="button" @click="close()" class="ab-btn ab-btn-primary mt-5 w-full">Done</button>
            </div>

            {{-- Form --}}
            <form x-show="state !== 'success'" @submit.prevent="submit()" novalidate class="space-y-3">

                {{-- Honeypot: hidden from people, tempting for bots --}}
                <div class="absolute -left-[9999px] top-0 h-0 w-0 overflow-hidden" aria-hidden="true">
                    <label>Website <input type="text" name="website" x-model="fields.website" tabindex="-1" autocomplete="off"></label>
                </div>

                <p x-show="message && state !== 'success'" x-cloak
                   :class="state === 'error' ? 'bg-red-50 text-danger' : 'bg-orange-50 text-warn'"
                   class="rounded-lg px-3 py-2 text-xs font-semibold" role="alert" x-text="message"></p>

                <div class="grid gap-3 sm:grid-cols-2">
                    <div>
                        <label class="ab-label" :for="$id('name')">Full Name <span class="text-danger">*</span></label>
                        <input :id="$id('name')" type="text" x-model="fields.name" class="ab-field" autocomplete="name"
                               :aria-invalid="hasError('name')" :class="hasError('name') && 'border-danger'" required>
                        <p x-show="hasError('name')" class="mt-1 text-[11px] text-danger" x-text="error('name')"></p>
                    </div>
                    <div>
                        <label class="ab-label" :for="$id('mobile')">Mobile Number <span class="text-danger">*</span></label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-sm text-muted">+91</span>
                            <input :id="$id('mobile')" type="tel" inputmode="numeric" maxlength="10" x-model="fields.mobile"
                                   class="ab-field pl-11" autocomplete="tel-national" placeholder="10-digit mobile"
                                   :aria-invalid="hasError('mobile')" :class="hasError('mobile') && 'border-danger'" required>
                        </div>
                        <p x-show="hasError('mobile')" class="mt-1 text-[11px] text-danger" x-text="error('mobile')"></p>
                    </div>
                </div>

                <div class="grid gap-3 sm:grid-cols-2">
                    <div>
                        <label class="ab-label" :for="$id('email')">Email <span class="font-normal text-muted">(optional)</span></label>
                        <input :id="$id('email')" type="email" x-model="fields.email" class="ab-field" autocomplete="email"
                               :aria-invalid="hasError('email')" :class="hasError('email') && 'border-danger'">
                        <p x-show="hasError('email')" class="mt-1 text-[11px] text-danger" x-text="error('email')"></p>
                    </div>
                    <div class="grid grid-cols-5 gap-2">
                        <div class="col-span-3">
                            <label class="ab-label" :for="$id('city')">City</label>
                            <input :id="$id('city')" type="text" x-model="fields.city" class="ab-field" autocomplete="address-level2">
                        </div>
                        <div class="col-span-2">
                            <label class="ab-label" :for="$id('pin')">Pincode</label>
                            <input :id="$id('pin')" type="text" inputmode="numeric" maxlength="6" x-model="fields.pincode" class="ab-field"
                                   autocomplete="postal-code" :aria-invalid="hasError('pincode')" :class="hasError('pincode') && 'border-danger'">
                            <p x-show="hasError('pincode')" class="mt-1 text-[11px] text-danger" x-text="error('pincode')"></p>
                        </div>
                    </div>
                </div>

                @if ($variants->count() > 1)
                    <div>
                        <label class="ab-label" :for="$id('variant')">Variant</label>
                        <select :id="$id('variant')" x-model="fields.vehicle_variant_id" class="ab-field">
                            @foreach ($variants as $v)
                                <option value="{{ $v['id'] }}">{{ $v['label'] }} — ₹{{ number_format($v['ex_showroom']) }} ex-showroom</option>
                            @endforeach
                        </select>
                    </div>
                @endif

                {{-- Test drive --}}
                <div x-show="source === 'test_drive'" x-cloak class="grid gap-3 rounded-lg bg-canvas p-3 sm:grid-cols-2">
                    <div>
                        <label class="ab-label" :for="$id('date')">Preferred Date <span class="text-danger">*</span></label>
                        <input :id="$id('date')" type="date" min="{{ now()->toDateString() }}" x-model="fields.preferred_at" class="ab-field"
                               :aria-invalid="hasError('preferred_at')" :class="hasError('preferred_at') && 'border-danger'">
                        <p x-show="hasError('preferred_at')" class="mt-1 text-[11px] text-danger" x-text="error('preferred_at')"></p>
                    </div>
                    <div>
                        <label class="ab-label" :for="$id('slot')">Time Slot <span class="text-danger">*</span></label>
                        <select :id="$id('slot')" x-model="fields.time_slot" class="ab-field"
                                :aria-invalid="hasError('time_slot')" :class="hasError('time_slot') && 'border-danger'">
                            <option value="">Select a slot</option>
                            @foreach ($slots as $slot)
                                <option value="{{ $slot }}">{{ str_replace('-', ' – ', $slot) }}</option>
                            @endforeach
                        </select>
                        <p x-show="hasError('time_slot')" class="mt-1 text-[11px] text-danger" x-text="error('time_slot')"></p>
                    </div>
                </div>

                {{-- Loan --}}
                <div x-show="source === 'loan'" x-cloak class="rounded-lg bg-canvas p-3">
                    <label class="ab-label" :for="$id('loan')">Loan Amount Needed <span class="text-danger">*</span></label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-sm text-muted">₹</span>
                        <input :id="$id('loan')" type="number" min="1000" step="1000" inputmode="numeric" x-model.number="fields.loan_amount"
                               class="ab-field pl-7" :aria-invalid="hasError('loan_amount')" :class="hasError('loan_amount') && 'border-danger'">
                    </div>
                    <p x-show="hasError('loan_amount')" class="mt-1 text-[11px] text-danger" x-text="error('loan_amount')"></p>
                </div>

                <div>
                    <label class="ab-label" :for="$id('msg')">Message <span class="font-normal text-muted">(optional)</span></label>
                    <textarea :id="$id('msg')" rows="2" x-model="fields.message" class="ab-field"
                              placeholder="Anything we should know?"></textarea>
                </div>

                <button type="submit" :disabled="state === 'saving'" class="ab-btn ab-btn-accent w-full disabled:opacity-60">
                    <span x-show="state !== 'saving'" x-text="title"></span>
                    <span x-show="state === 'saving'" x-cloak>Sending…</span>
                </button>

                <p class="text-center text-[10px] text-muted">
                    By submitting you agree to be contacted by AutoBazaar about this vehicle.
                </p>
            </form>
        </div>
    </div>
</div>
