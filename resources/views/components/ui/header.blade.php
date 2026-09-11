@props(['site', 'locations'])

{{--
    Three-row header from the reference screens:
      1. logo · global search · location · WhatsApp · account · cart
      2. green nav bar + yellow "Enquire Now" CTA
      3. (mobile) a drawer holding rows 1 and 2

    The location switcher and cart badge are Alpine-backed so the prototype
    behaves like the real thing.
--}}

@php
    $default = $locations['default'];
@endphp

<header x-data="{ mobileOpen: false }" class="sticky top-0 z-40 shadow-sm">

    {{-- ---------------------------------------------------------- row 1 --}}
    <div class="border-b border-line bg-surface">
        <div class="ab-container flex items-center gap-4 py-3">

            <a href="{{ route('site.home') }}" class="shrink-0">
                <x-ui.logo :site="$site" />
            </a>

            {{-- Global search: hidden on small screens, moved into the drawer --}}
            <form action="{{ route('site.search') }}" method="GET"
                  class="hidden flex-1 lg:flex" role="search">
                <div class="flex w-full max-w-2xl overflow-hidden rounded-lg border border-line bg-canvas
                            focus-within:border-brand-400 focus-within:ring-2 focus-within:ring-brand-100">
                    <input type="search" name="q"
                           placeholder="Search autos, accessories, news, schemes..."
                           aria-label="Search AutoBazaar"
                           class="w-full bg-transparent px-4 py-2.5 text-sm outline-none placeholder:text-muted">
                    <button type="submit"
                            class="grid w-12 place-items-center bg-brand-500 text-white transition-colors hover:bg-brand-600"
                            aria-label="Search">
                        <x-ui.icon name="search" />
                    </button>
                </div>
            </form>

            <div class="ml-auto flex items-center gap-1 sm:gap-3">

                {{-- Location switcher --}}
                <div x-data="locationPicker({
                        states: {{ Js::from($locations['states']) }},
                        directPurchaseDistricts: {{ Js::from($locations['direct_purchase_districts']) }},
                        state: @js($default['state']),
                        district: @js($default['district']),
                        city: @js($default['city']),
                        pincode: @js($default['pincode']),
                     })"
                     class="relative hidden md:block">

                    <button type="button" @click="open = !open"
                            class="flex items-center gap-2 rounded-lg px-2 py-1.5 text-left transition-colors hover:bg-canvas">
                        <x-ui.icon name="pin" class="text-brand-500" />
                        <span class="leading-tight">
                            <span class="block text-sm font-semibold" x-text="label">{{ $default['city'] }}</span>
                            <span class="block text-[11px] text-muted">Change Location</span>
                        </span>
                    </button>

                    {{-- Location panel --}}
                    <div x-show="open" x-cloak x-transition.opacity
                         @click.outside="open = false"
                         class="ab-card absolute right-0 z-50 mt-2 w-80 p-4">
                        <p class="mb-3 text-sm font-bold">Select Your Location</p>

                        <label class="ab-label">State</label>
                        <select class="ab-field mb-3" x-model="state" @change="onStateChange()">
                            <template x-for="s in states" :key="s.name">
                                <option :value="s.name" x-text="s.name"></option>
                            </template>
                        </select>

                        <label class="ab-label">District</label>
                        <select class="ab-field mb-3" x-model="district" @change="onDistrictChange()">
                            <template x-for="d in districts" :key="d.name">
                                <option :value="d.name" x-text="d.name"></option>
                            </template>
                        </select>

                        <label class="ab-label">City</label>
                        <select class="ab-field mb-3" x-model="city">
                            <template x-for="c in cities" :key="c.name">
                                <option :value="c.name" x-text="c.name"></option>
                            </template>
                        </select>

                        {{-- Live serviceability feedback --}}
                        <p class="mb-3 flex items-center gap-2 rounded-lg px-3 py-2 text-xs font-semibold"
                           :class="{
                               'bg-brand-50 text-brand-600': tier === 'direct_purchase',
                               'bg-blue-50 text-info': tier === 'buying_assistance',
                               'bg-orange-50 text-warn': tier === 'dealer_connect',
                           }">
                            <span x-text="tierLabel"></span>
                        </p>

                        <button type="button" @click="apply()" class="ab-btn ab-btn-primary w-full">
                            Apply Location
                        </button>
                    </div>
                </div>

                {{-- WhatsApp --}}
                <a href="https://wa.me/{{ $site['contact']['whatsapp'] }}" target="_blank" rel="noopener"
                   class="hidden items-center gap-2 rounded-lg px-2 py-1.5 transition-colors hover:bg-canvas xl:flex">
                    <x-ui.icon name="whatsapp" class="text-[#25D366]" :size="22" />
                    <span class="leading-tight">
                        <span class="block text-sm font-bold">{{ $site['contact']['phone'] }}</span>
                        <span class="block text-[11px] text-muted">Chat on WhatsApp</span>
                    </span>
                </a>

                {{-- Account: real OTP login for guests, account area once signed in --}}
                @auth
                    <a href="{{ route('site.account') }}"
                       class="flex items-center gap-2 rounded-lg px-2 py-1.5 transition-colors hover:bg-canvas">
                        <x-ui.icon name="user" class="text-ink-soft" />
                        <span class="hidden leading-tight sm:block">
                            <span class="block text-sm font-semibold">Hi, {{ \Illuminate\Support\Str::before(auth()->user()->name ?: 'there', ' ') }}</span>
                            <span class="block text-[11px] text-muted">My Account</span>
                        </span>
                    </a>
                @else
                    <a href="{{ route('user.login') }}"
                       class="flex items-center gap-2 rounded-lg px-2 py-1.5 transition-colors hover:bg-canvas">
                        <x-ui.icon name="user" class="text-ink-soft" />
                        <span class="hidden text-sm font-semibold sm:block">Login / Register</span>
                    </a>
                @endauth

                {{-- Cart --}}
                <a href="{{ route('site.checkout') }}" class="relative rounded-lg p-2 transition-colors hover:bg-canvas"
                   aria-label="Cart">
                    <x-ui.icon name="cart" class="text-ink-soft" :size="22" />
                    {{-- Re-triggers the pop by swapping the class off and on --}}
                    <span x-data="{ pop: false }"
                          x-init="$watch('$store.cart.count', () => { pop = false; $nextTick(() => pop = true) })"
                          x-show="$store.cart.count > 0" x-cloak
                          :class="pop && 'ab-pop'"
                          x-text="$store.cart.count"
                          class="absolute -right-0.5 -top-0.5 grid h-4.5 min-w-4.5 place-items-center rounded-full
                                 bg-danger px-1 text-[10px] font-bold text-white"></span>
                </a>

                {{-- Mobile menu toggle --}}
                <button type="button" @click="mobileOpen = true"
                        class="rounded-lg p-2 transition-colors hover:bg-canvas lg:hidden"
                        aria-label="Open menu">
                    <x-ui.icon name="menu" :size="22" />
                </button>
            </div>
        </div>
    </div>

    {{-- ---------------------------------------------------------- row 2 --}}
    <nav class="hidden bg-brand-500 lg:block" aria-label="Main">
        <div class="ab-container flex items-stretch">
            <ul class="flex flex-1 items-stretch">
                @foreach ($site['nav'] as $item)
                    @php $isActive = request()->routeIs($item['route']); @endphp
                    <li>
                        <a href="{{ route($item['route']) }}"
                           @if($isActive) aria-current="page" @endif
                           class="flex h-full items-center whitespace-nowrap px-3 py-3 text-[13px] font-semibold
                                  transition-colors xl:px-4
                                  {{ $isActive
                                       ? 'bg-accent-500 text-ink'
                                       : 'text-white/90 hover:bg-brand-600 hover:text-white' }}">
                            {{ $item['label'] }}
                        </a>
                    </li>
                @endforeach
            </ul>

            <a href="{{ route('site.enquiry') }}"
               class="my-1.5 ml-3 flex items-center rounded-md bg-accent-500 px-6 text-sm font-bold text-ink
                      transition-colors hover:bg-accent-600">
                Enquire Now
            </a>
        </div>
    </nav>

    {{-- ------------------------------------------------------ mobile drawer --}}
    <div x-show="mobileOpen" x-cloak class="lg:hidden">
        <div class="fixed inset-0 z-50 bg-ink/50" @click="mobileOpen = false" x-transition.opacity></div>

        <div class="fixed inset-y-0 right-0 z-50 flex w-[88%] max-w-sm flex-col bg-surface"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="translate-x-full"
             x-transition:enter-end="translate-x-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="translate-x-0"
             x-transition:leave-end="translate-x-full">

            <div class="flex items-center justify-between border-b border-line p-4">
                <x-ui.logo :site="$site" size="sm" />
                <button type="button" @click="mobileOpen = false" class="rounded-lg p-2 hover:bg-canvas"
                        aria-label="Close menu">
                    <x-ui.icon name="close" />
                </button>
            </div>

            <form action="{{ route('site.search') }}" method="GET" class="border-b border-line p-4" role="search">
                <div class="flex overflow-hidden rounded-lg border border-line bg-canvas">
                    <input type="search" name="q" placeholder="Search autos, accessories..."
                           aria-label="Search"
                           class="w-full bg-transparent px-3 py-2.5 text-sm outline-none">
                    <button type="submit" class="grid w-11 place-items-center bg-brand-500 text-white" aria-label="Search">
                        <x-ui.icon name="search" :size="18" />
                    </button>
                </div>
            </form>

            <ul class="flex-1 overflow-y-auto py-2">
                @foreach ($site['nav'] as $item)
                    <li>
                        <a href="{{ route($item['route']) }}"
                           class="flex items-center justify-between px-4 py-3 text-sm font-semibold
                                  {{ request()->routeIs($item['route']) ? 'bg-brand-50 text-brand-600' : 'hover:bg-canvas' }}">
                            {{ $item['label'] }}
                            <x-ui.icon name="chevron-right" :size="16" class="text-muted" />
                        </a>
                    </li>
                @endforeach
            </ul>

            <div class="space-y-2 border-t border-line p-4">
                <a href="{{ route('site.enquiry') }}" class="ab-btn ab-btn-accent w-full">Enquire Now</a>
                <a href="https://wa.me/{{ $site['contact']['whatsapp'] }}" target="_blank" rel="noopener"
                   class="ab-btn ab-btn-outline w-full">
                    <x-ui.icon name="whatsapp" :size="18" />
                    {{ $site['contact']['phone'] }}
                </a>
            </div>
        </div>
    </div>
</header>
