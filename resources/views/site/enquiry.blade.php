@extends('site.layout')

@section('title', 'Enquire About This Vehicle')

@section('content')

@php $default = $locations['default']; @endphp

<div class="ab-container py-4">
    <x-ui.breadcrumb :items="[
        ['label' => 'Home', 'href' => route('site.home')],
        ['label' => 'Vehicles', 'href' => route('site.new-autos')],
        ['label' => $vehicle['brand'], 'href' => route('site.brand', $vehicle['brand_slug'])],
        ['label' => $vehicle['name'], 'href' => route('site.model', [$vehicle['brand_slug'], $vehicle['model_slug']])],
        ['label' => 'Enquire Now'],
    ]" />
</div>

<section class="ab-container pb-10">
    <div class="grid gap-6 lg:grid-cols-12">

        {{-- ====================================================== left rail --}}
        <aside class="space-y-3 lg:col-span-3">
            <div class="ab-card bg-accent-500 p-4">
                <p class="flex items-start gap-2.5 text-sm font-bold leading-snug">
                    <x-ui.icon name="pin" :size="22" class="mt-0.5 shrink-0" />
                    <span>
                        {{ implode(' | ', $site['service_area']['districts']) }}<br>
                        Direct Purchase Available
                    </span>
                </p>
            </div>

            <div class="ab-card flex items-center gap-3 p-4">
                <x-ui.tone-icon icon="map" tone="info" :size="18" shape="circle" />
                <span class="leading-tight">
                    <span class="block text-sm font-bold">Other Districts in Tamil Nadu</span>
                    <span class="block text-xs text-muted">Get Buying Assistance</span>
                </span>
            </div>

            <div class="ab-card flex items-center gap-3 p-4">
                <x-ui.tone-icon icon="flag" tone="warn" :size="18" shape="circle" />
                <span class="leading-tight">
                    <span class="block text-sm font-bold">Other States in India</span>
                    <span class="block text-xs text-muted">We will connect you with a nearby dealer</span>
                </span>
            </div>

            <div class="relative overflow-hidden rounded-xl bg-brand-50 p-4">
                <img src="{{ asset($vehicle['image']) }}" alt="" aria-hidden="true"
                     class="w-full max-w-56 object-contain" loading="lazy">
                <p class="ab-script mt-1 text-xl leading-tight text-brand-600">
                    Better Miles<br>Brighter Tomorrow
                </p>
            </div>

            <ul class="ab-card grid grid-cols-4 gap-1 p-3 text-center">
                @foreach ([['shield', 'Trusted Brands'], ['headset', 'Expert Guidance'], ['rupee', 'Best Finance Options'], ['gear', 'Support Across India']] as [$icon, $label])
                    <li>
                        <x-ui.icon :name="$icon" :size="18" class="mx-auto text-brand-500" />
                        <span class="mt-1 block text-[9px] font-semibold leading-tight">{{ $label }}</span>
                    </li>
                @endforeach
            </ul>
        </aside>

        {{-- ========================================================== form --}}
        <div class="lg:col-span-6">
            <div class="ab-card p-5 sm:p-6" data-reveal>
                <div class="mb-5 flex items-start gap-3">
                    <x-ui.tone-icon icon="doc" tone="brand" :size="20" shape="circle" />
                    <div>
                        <h1 class="text-xl font-extrabold tracking-tight sm:text-2xl">Enquire About This Vehicle</h1>
                        <p class="mt-0.5 text-sm text-muted">
                            Fill in your details and we will get back to you shortly.
                        </p>
                    </div>
                </div>

                {{-- Prototype only: no action, nothing is submitted or stored. --}}
                <form @submit.prevent x-data="{ agreed: true }" class="space-y-4">

                    <div class="grid gap-4 sm:grid-cols-3">
                        <div>
                            <label for="eq-name" class="ab-label">Full Name <span class="text-danger">*</span></label>
                            <div class="relative">
                                <x-ui.icon name="user" :size="16"
                                           class="absolute left-3 top-1/2 -translate-y-1/2 text-muted" />
                                <input id="eq-name" type="text" required placeholder="Enter your full name" class="ab-field pl-9">
                            </div>
                        </div>

                        <div>
                            <label for="eq-mobile" class="ab-label">Mobile Number <span class="text-danger">*</span></label>
                            <div class="relative">
                                <x-ui.icon name="phone-call" :size="16"
                                           class="absolute left-3 top-1/2 -translate-y-1/2 text-muted" />
                                <input id="eq-mobile" type="tel" required placeholder="Enter mobile number" class="ab-field pl-9">
                            </div>
                        </div>

                        <div>
                            <label for="eq-email" class="ab-label">Email Address</label>
                            <div class="relative">
                                <x-ui.icon name="mail" :size="16"
                                           class="absolute left-3 top-1/2 -translate-y-1/2 text-muted" />
                                <input id="eq-email" type="email" placeholder="Enter your email (optional)" class="ab-field pl-9">
                            </div>
                        </div>
                    </div>

                    {{-- Location, with dependent dropdowns --}}
                    <div class="grid gap-4 sm:grid-cols-4"
                         x-data="locationPicker({
                            states: {{ Js::from($locations['states']) }},
                            directPurchaseDistricts: {{ Js::from($locations['direct_purchase_districts']) }},
                            state: @js($default['state']),
                            district: @js($default['district']),
                            city: @js($default['city']),
                         })">
                        <div>
                            <label for="eq-state" class="ab-label">State <span class="text-danger">*</span></label>
                            <select id="eq-state" class="ab-field" x-model="state" @change="onStateChange()">
                                <template x-for="s in states" :key="s.name">
                                    <option :value="s.name" x-text="s.name"></option>
                                </template>
                            </select>
                        </div>

                        <div>
                            <label for="eq-district" class="ab-label">District <span class="text-danger">*</span></label>
                            <select id="eq-district" class="ab-field" x-model="district" @change="onDistrictChange()">
                                <template x-for="d in districts" :key="d.name">
                                    <option :value="d.name" x-text="d.name"></option>
                                </template>
                            </select>
                        </div>

                        <div>
                            <label for="eq-city" class="ab-label">City <span class="text-danger">*</span></label>
                            <select id="eq-city" class="ab-field" x-model="city">
                                <template x-for="c in cities" :key="c.name">
                                    <option :value="c.name" x-text="c.name"></option>
                                </template>
                            </select>
                        </div>

                        <div>
                            <label for="eq-pin" class="ab-label">Pincode <span class="text-danger">*</span></label>
                            <div class="relative">
                                <x-ui.icon name="pin" :size="16"
                                           class="absolute left-3 top-1/2 -translate-y-1/2 text-muted" />
                                <input id="eq-pin" type="text" inputmode="numeric" maxlength="6"
                                       placeholder="Enter pincode" class="ab-field pl-9" x-model="pincode">
                            </div>
                        </div>

                        {{-- Live tier feedback, so the form explains itself --}}
                        <p class="sm:col-span-4 flex items-center gap-2 rounded-lg px-3 py-2 text-xs font-semibold"
                           :class="{
                               'bg-brand-50 text-brand-600': tier === 'direct_purchase',
                               'bg-blue-50 text-info': tier === 'buying_assistance',
                               'bg-orange-50 text-warn': tier === 'dealer_connect',
                           }">
                            <span x-text="tierLabel"></span>
                            <span class="font-normal opacity-80">for this location</span>
                        </p>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-3">
                        <div>
                            <label for="eq-model" class="ab-label">Interested In <span class="text-danger">*</span></label>
                            <select id="eq-model" class="ab-field">
                                @foreach ($vehicles as $v)
                                    <option @selected($v['slug'] === $vehicle['slug'])>{{ $v['name'] }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="eq-fuel" class="ab-label">Fuel Type</label>
                            <select id="eq-fuel" class="ab-field">
                                @foreach ($vehicle['variants'] as $v)
                                    <option>{{ $v['label'] }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="eq-when" class="ab-label">Expected Buying Time</label>
                            <select id="eq-when" class="ab-field">
                                <option value="">Select option</option>
                                @foreach ($locations['buying_timeframes'] as $frame)
                                    <option>{{ $frame }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <fieldset>
                            <legend class="ab-label">Preferred Buying Option</legend>
                            @foreach ($locations['buying_options'] as $option)
                                <label class="flex items-center gap-2 py-1 text-sm">
                                    <input type="checkbox" value="{{ $option['value'] }}"
                                           class="h-4 w-4 rounded border-line text-brand-500 focus:ring-brand-400">
                                    {{ $option['label'] }}
                                </label>
                            @endforeach
                        </fieldset>

                        <div>
                            <label for="eq-msg" class="ab-label">Message (Optional)</label>
                            <textarea id="eq-msg" rows="6" class="ab-field resize-none"
                                      placeholder="Tell us your requirements or any questions..."></textarea>
                        </div>
                    </div>

                    <label class="flex items-start gap-2.5 text-sm">
                        <input type="checkbox" x-model="agreed"
                               class="mt-0.5 h-4 w-4 rounded border-line text-brand-500 focus:ring-brand-400">
                        <span>
                            I have read and agree to the
                            <a href="{{ route('site.page', 'buying-policy') }}"
                               class="font-semibold text-brand-500 underline underline-offset-2">AutoBazaar Vehicle Buying Policy</a>
                            and
                            <a href="{{ route('site.page', 'terms-conditions') }}"
                               class="font-semibold text-brand-500 underline underline-offset-2">Terms &amp; Conditions</a>.
                        </span>
                    </label>

                    <button type="submit" :disabled="! agreed"
                            class="ab-btn ab-btn-primary w-full py-3 disabled:cursor-not-allowed disabled:opacity-50">
                        <x-ui.icon name="arrow-right" :size="18" />
                        Submit Enquiry
                    </button>

                    <p class="flex items-center justify-center gap-1.5 text-xs text-muted">
                        <x-ui.icon name="lock" :size="13" />
                        Your information is safe with us. We will contact you for the next steps.
                    </p>
                </form>
            </div>
        </div>

        {{-- ===================================================== right rail --}}
        <aside class="space-y-3 lg:col-span-3">
            <div class="ab-card p-4">
                <h2 class="mb-3 text-sm font-bold">Selected Vehicle</h2>

                <img src="{{ asset($vehicle['brand_logo']) }}" alt="{{ $vehicle['brand'] }}"
                     class="mb-2 h-5 w-auto object-contain object-right ml-auto">

                <img src="{{ asset($vehicle['image']) }}" alt="{{ $vehicle['name'] }}"
                     class="mx-auto h-24 w-auto object-contain" loading="lazy">

                <p class="mt-2 text-base font-bold">{{ $vehicle['name'] }}</p>
                <p class="text-xs text-muted">{{ collect($vehicle['variants'])->pluck('label')->implode(' | ') }}</p>

                <x-ui.rating :rating="$vehicle['rating']" :reviews="$vehicle['reviews']" class="mt-1.5" />

                <p class="mt-2 text-sm">
                    <span class="text-muted">From</span>
                    <span class="font-extrabold">₹{{ number_format($vehicle['from_price'] / 100000, 2) }} Lakh*</span>
                </p>

                <a href="{{ route('site.model', [$vehicle['brand_slug'], $vehicle['model_slug']]) }}"
                   class="mt-2 inline-flex items-center gap-1 text-xs font-semibold text-brand-500 underline underline-offset-2">
                    View Full Details <x-ui.icon name="arrow-right" :size="13" />
                </a>
            </div>

            <div class="ab-card bg-brand-50 p-4">
                <p class="mb-1.5 flex items-center gap-2 text-sm font-bold">
                    <x-ui.icon name="pin" :size="16" class="text-brand-500" /> Your Location Benefits
                </p>
                <p class="text-xs text-ink-soft">
                    Enter your location to see the exact buying options, price and availability in your area.
                </p>
            </div>

            <div class="ab-card bg-accent-50 p-4">
                <p class="mb-2.5 flex items-center gap-2 text-sm font-bold">
                    <x-ui.icon name="info" :size="16" class="text-accent-600" /> Important Information
                </p>
                <ul class="space-y-2">
                    @foreach ([
                        'Direct vehicle purchase available in ' . implode(', ', $site['service_area']['districts']) . '.',
                        'For other districts in Tamil Nadu, we provide buying assistance and guidance.',
                        'For other states, we will connect you with a suitable nearby dealer.',
                        'Final price, offers and availability may vary based on your location.',
                    ] as $note)
                        <li class="flex items-start gap-2 text-xs">
                            <x-ui.icon name="check" :size="13" class="mt-0.5 shrink-0 text-warn" />
                            {{ $note }}
                        </li>
                    @endforeach
                </ul>

                <a href="{{ route('site.page', 'buying-policy') }}"
                   class="mt-3 inline-flex items-center gap-1 text-xs font-semibold text-brand-500 underline underline-offset-2">
                    View Full Buying Policy <x-ui.icon name="arrow-right" :size="13" />
                </a>
            </div>
        </aside>
    </div>
</section>

{{-- =============================================================== trust bar --}}
<section class="border-t border-line bg-surface">
    <div class="ab-container grid gap-5 py-6 sm:grid-cols-2 lg:grid-cols-4">
        @foreach ([
            ['shield', '100% Genuine Information', 'Accurate and updated details'],
            ['bolt', 'Quick Response', "We'll get back to you soon"],
            ['rupee', 'Multiple Buying Options', 'Cash, Finance, Bank Loan'],
            ['users', 'Support Across India', 'Guidance wherever you are'],
        ] as [$icon, $title, $note])
            <div class="flex items-center gap-3">
                <x-ui.icon :name="$icon" :size="24" class="text-brand-500" />
                <span class="leading-tight">
                    <span class="block text-sm font-bold">{{ $title }}</span>
                    <span class="block text-[11px] text-muted">{{ $note }}</span>
                </span>
            </div>
        @endforeach
    </div>
</section>

@endsection
