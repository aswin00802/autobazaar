@extends('site.layout')

@section('title', 'Find Your Buying Options')
@section('description', 'Enter your location to see the buying options, pricing and assistance available in your area.')

@section('content')

@php $default = $locations['default']; @endphp

{{-- ==================================================================== hero --}}
<section class="relative overflow-hidden bg-gradient-to-br from-brand-50 via-canvas to-blue-50">
    <div class="ab-container py-8 lg:py-10">
        <div class="grid gap-8 lg:grid-cols-12 lg:items-center">

            <div class="lg:col-span-4" data-reveal="left">
                <p class="mb-3 inline-block rounded border border-brand-200 px-2.5 py-1 text-[10px] font-bold uppercase tracking-[0.14em] text-brand-600">
                    Location Based Buying
                </p>
                <h1 class="text-3xl font-extrabold tracking-tight sm:text-4xl">Find Your Buying Options</h1>
                <p class="mt-2 text-sm text-muted">
                    Enter your location to see available purchase options
                </p>

                <img src="{{ asset('assets/image/auto_brands/tvs.png') }}" alt=""
                     aria-hidden="true" class="mt-6 hidden w-full max-w-xs lg:block" loading="lazy">
            </div>

            {{-- Location form --}}
            <div class="lg:col-span-5"
                 x-data="locationPicker({
                    states: {{ Js::from($locations['states']) }},
                    directPurchaseDistricts: {{ Js::from($locations['direct_purchase_districts']) }},
                    state: @js($default['state']),
                    district: @js($default['district']),
                    city: @js($default['city']),
                    pincode: @js($default['pincode']),
                 })">

                <div class="ab-card p-5 shadow-lg" data-reveal="scale">
                    <div class="mb-4 flex items-start gap-3">
                        <x-ui.tone-icon icon="pin" tone="brand" :size="20" shape="circle" />
                        <div>
                            <h2 class="text-lg font-extrabold">Select Your Location</h2>
                            <p class="text-xs text-muted">
                                Please enter your location to view the correct price and buying options.
                            </p>
                        </div>
                    </div>

                    <div class="grid gap-3 sm:grid-cols-2">
                        <div>
                            <label for="bo-state" class="ab-label">State <span class="text-danger">*</span></label>
                            <select id="bo-state" class="ab-field" x-model="state" @change="onStateChange()">
                                <template x-for="s in states" :key="s.name">
                                    <option :value="s.name" x-text="s.name"></option>
                                </template>
                            </select>
                        </div>

                        <div>
                            <label for="bo-district" class="ab-label">District <span class="text-danger">*</span></label>
                            <select id="bo-district" class="ab-field" x-model="district" @change="onDistrictChange()">
                                <template x-for="d in districts" :key="d.name">
                                    <option :value="d.name" x-text="d.name"></option>
                                </template>
                            </select>
                        </div>

                        <div>
                            <label for="bo-city" class="ab-label">City</label>
                            <select id="bo-city" class="ab-field" x-model="city">
                                <template x-for="c in cities" :key="c.name">
                                    <option :value="c.name" x-text="c.name"></option>
                                </template>
                            </select>
                        </div>

                        <div>
                            <label for="bo-pin" class="ab-label">Pincode</label>
                            <input id="bo-pin" type="text" inputmode="numeric" maxlength="6"
                                   class="ab-field" x-model="pincode">
                        </div>
                    </div>

                    <button type="button" @click="apply()" class="ab-btn ab-btn-primary mt-4 w-full py-3">
                        <x-ui.icon name="pin" :size="18" />
                        Show Available Options
                    </button>

                    <p class="mt-3 flex items-center justify-center gap-1.5 rounded-lg bg-canvas px-3 py-2 text-[11px] text-muted">
                        <x-ui.icon name="lock" :size="13" />
                        Your location helps us provide the right buying options, accurate pricing and better assistance.
                    </p>

                    {{-- Result, once a location is applied --}}
                    <div x-show="resolved" x-cloak x-transition
                         class="mt-3 rounded-lg px-4 py-3 text-sm font-semibold"
                         :class="{
                             'bg-brand-50 text-brand-600': tier === 'direct_purchase',
                             'bg-blue-50 text-info': tier === 'buying_assistance',
                             'bg-orange-50 text-warn': tier === 'dealer_connect',
                         }">
                        <span x-text="`${label}: ${tierLabel}`"></span>
                    </div>
                </div>
            </div>

            {{-- Service-area callout --}}
            <div class="lg:col-span-3">
                <div class="ab-card bg-accent-500 p-5">
                    <p class="text-sm font-extrabold leading-snug">
                        Direct Vehicle Purchase<br>Available in 4 Districts Only
                    </p>
                    <p class="mt-2 text-xs font-semibold">
                        {{ implode(' | ', $site['service_area']['districts']) }}
                    </p>
                </div>

                <ul class="mt-4 space-y-2">
                    @foreach ($site['service_area']['districts'] as $district)
                        <li class="flex items-center gap-2 text-sm font-semibold">
                            <span class="h-2 w-2 rounded-full bg-brand-500"></span>
                            {{ $district }}
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</section>

{{-- =========================================================== how it works --}}
<section class="ab-container py-8">
    <div class="flex flex-col gap-6 lg:flex-row lg:items-center">
        <h2 class="flex shrink-0 items-center gap-2 text-lg font-extrabold">
            <x-ui.icon name="pin" :size="20" class="text-brand-500" />
            How It Works?
        </h2>

        <ol class="grid flex-1 gap-4 sm:grid-cols-3" data-reveal-group>
            @foreach ($locations['how_it_works'] as $step)
                <li class="flex items-center gap-3">
                    <span class="grid h-8 w-8 shrink-0 place-items-center rounded-full bg-brand-500 text-xs font-bold text-white">
                        {{ $step['step'] }}
                    </span>
                    <span class="leading-tight">
                        <span class="block text-sm font-bold">{{ $step['title'] }}</span>
                        <span class="block text-[11px] text-muted">{{ $step['note'] }}</span>
                    </span>

                    @unless ($loop->last)
                        <x-ui.icon name="arrow-right" :size="18" class="ml-auto hidden text-line sm:block" />
                    @endunless
                </li>
            @endforeach
        </ol>
    </div>
</section>

{{-- ============================================================= tier cards --}}
<section class="ab-container pb-10">
    <div class="grid gap-4 md:grid-cols-3" data-reveal-group>
        @foreach ($locations['tiers'] as $tier)
            <x-ui.tier-card :tier="$tier" />
        @endforeach
    </div>
</section>

{{-- =============================================================== trust bar --}}
<section class="border-t border-line bg-surface">
    <div class="ab-container grid gap-5 py-6 sm:grid-cols-2 lg:grid-cols-4">
        @foreach ([
            ['shield', 'Trusted Information', 'Accurate and updated details'],
            ['users', 'Customer Support', 'Guidance at every step'],
            ['rupee', 'Better Buying Experience', 'Right options for your location'],
            ['headset', 'Need Help?', 'Call or WhatsApp ' . $site['contact']['phone']],
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
