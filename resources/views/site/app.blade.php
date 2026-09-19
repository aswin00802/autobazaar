@extends('site.layout')

@section('title', 'AutoBazaar App')
@section('description', 'Buy, sell, compare and book autorickshaws from your phone. Download the AutoBazaar app.')

@section('content')

{{-- ==================================================================== hero --}}
<section class="relative overflow-hidden bg-gradient-to-br from-blue-50 via-canvas to-brand-50">
    <div class="ab-container py-8 lg:py-10">
        <div class="grid gap-10 lg:grid-cols-12 lg:items-center">

            {{-- Copy --}}
            <div class="min-w-0 lg:col-span-5" data-reveal="left">
                <p class="text-xs font-bold uppercase tracking-[0.14em] text-ink-soft">Introducing the All-New</p>
                <h1 class="mt-1 text-4xl font-extrabold tracking-tight text-brand-600 sm:text-5xl">
                    AutoBazaar App
                </h1>
                <p class="mt-2 text-lg font-extrabold">Buy. Sell. Compare. Book. All in One App.</p>
                <p class="mt-2 max-w-md text-sm text-muted">
                    Your trusted platform for Autos, Accessories and Government Schemes — now in your pocket!
                </p>

                {{-- Feature grid --}}
                <ul class="mt-7 grid grid-cols-2 gap-5 sm:grid-cols-3">
                    @foreach ([
                        ['search', 'Search & Compare', 'Find the best autos'],
                        ['rupee', 'Check Price & EMI', 'On-road price, EMI & fuel cost'],
                        ['tag', 'Latest Offers', 'Never miss a great deal'],
                        ['doc', 'Government Schemes', 'Stay updated with new benefits'],
                        ['pin', 'Book & Buy', 'Easy booking across our 4 districts'],
                        ['gear', 'Auto Accessories', 'Upgrade your ride'],
                    ] as [$icon, $title, $note])
                        <li>
                            <x-ui.tone-icon :icon="$icon" tone="brand" :size="20" shape="circle" />
                            <p class="mt-2 text-xs font-bold leading-tight">{{ $title }}</p>
                            <p class="text-[11px] leading-tight text-muted">{{ $note }}</p>
                        </li>
                    @endforeach
                </ul>
            </div>

            {{-- Phone mockup --}}
            <div class="relative lg:col-span-4" data-reveal="scale">
                <p class="ab-script mb-2 text-center text-2xl leading-tight text-brand-600 lg:text-left">
                    Same Roads<br>Bigger Possibilities!
                </p>

                <div class="mx-auto w-64 rounded-[2rem] border-8 border-ink bg-surface p-3 shadow-2xl">
                    <div class="mb-3 flex items-center gap-2">
                        <x-ui.icon name="auto" :size="18" class="text-brand-500" />
                        <span class="text-sm font-extrabold text-brand-600">AutoBazaar</span>
                    </div>

                    <div class="mb-3 flex items-center gap-2 rounded-lg bg-canvas px-2.5 py-2">
                        <x-ui.icon name="search" :size="14" class="text-muted" />
                        <span class="text-[10px] text-muted">Search autos, accessories...</span>
                    </div>

                    <ul class="mb-3 grid grid-cols-3 gap-1.5">
                        @foreach ([
                            ['auto', 'New Autos', 'brand'], ['auto', 'Used Autos', 'accent'], ['scales', 'Compare', 'info'],
                            ['gear', 'Accessories', 'muted'], ['tag', 'Offers', 'danger'], ['doc', 'Schemes', 'violet'],
                        ] as [$icon, $label, $tone])
                            <li class="flex flex-col items-center gap-1 rounded-lg bg-canvas px-1 py-2 text-center">
                                <x-ui.tone-icon :icon="$icon" :tone="$tone" :size="14" />
                                <span class="text-[8px] font-semibold leading-tight">{{ $label }}</span>
                            </li>
                        @endforeach
                    </ul>

                    <div class="flex items-center gap-2 rounded-lg bg-brand-500 p-2.5 text-white">
                        <span class="flex-1 text-[10px] font-bold leading-tight">Best Deals<br>Near You →</span>
                        <img src="{{ asset('assets/image/auto_brands/tvs.png') }}" alt=""
                             aria-hidden="true" class="h-9 w-auto object-contain">
                    </div>

                    <ul class="mt-3 grid grid-cols-2 gap-y-2 border-t border-line pt-2 text-center sm:grid-cols-4">
                        @foreach ([['grid', 'Home'], ['scales', 'Compare'], ['doc', 'Enquiries'], ['user', 'Profile']] as [$icon, $label])
                            <li>
                                <x-ui.icon :name="$icon" :size="13" class="mx-auto text-muted" />
                                <span class="mt-0.5 block text-[7px] text-muted">{{ $label }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>

            {{-- Download panel --}}
            <div class="rounded-2xl bg-brand-700 p-6 text-white lg:col-span-3">
                <p class="ab-script text-2xl leading-tight text-accent-500">
                    Download Now &amp; Drive Smarter!
                </p>

                <div class="mt-5 grid grid-cols-2 gap-3">
                    @foreach (['Google Play', 'App Store'] as $store)
                        <div class="rounded-lg bg-white p-2">
                            {{-- Placeholder QR: a real code is generated at build time --}}
                            <svg viewBox="0 0 21 21" class="w-full" shape-rendering="crispEdges"
                                 role="img" aria-label="{{ $store }} QR code placeholder">
                                <rect width="21" height="21" fill="#fff"/>
                                <path fill="#0B5D3B" d="M0 0h7v7H0zM14 0h7v7h-7zM0 14h7v7H0zM2 2h3v3H2zM16 2h3v3h-3zM2 16h3v3H2zM9 0h1v2H9zM11 1h1v3h-1zM9 3h2v1H9zM13 8h1v2h-1zM9 9h3v1H9zM10 11h1v2h-1zM0 9h2v1H0zM3 9h2v1H3zM6 10h2v1H6zM9 14h2v2H9zM12 15h2v1h-2zM15 17h2v1h-2zM9 18h4v1H9zM14 19h3v1h-3zM18 9h3v1h-3zM17 11h2v1h-2zM19 12h2v2h-2z"/>
                            </svg>
                        </div>
                    @endforeach
                </div>

                <div class="mt-4 space-y-2">
                    <x-ui.store-badge store="google" class="w-full !justify-start" />
                    <x-ui.store-badge store="apple" class="w-full !justify-start" />
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ============================================ service area + why choose us --}}
<section class="ab-container py-8">
    <div class="grid gap-6 lg:grid-cols-12">

        {{-- Service area --}}
        <div class="ab-card bg-accent-50 p-5 lg:col-span-4">
            <p class="flex items-start gap-2.5">
                <x-ui.icon name="pin" :size="20" class="mt-0.5 shrink-0 text-brand-500" />
                <span>
                    <span class="block text-sm font-semibold text-ink-soft">Auto Buying Available Only in</span>
                    <span class="mt-1 block text-lg font-extrabold leading-snug">
                        {{ implode(', ', array_slice($site['service_area']['districts'], 0, 2)) }},<br>
                        {{ implode(' & ', array_slice($site['service_area']['districts'], 2)) }}
                    </span>
                </span>
            </p>

            <p class="mt-3 text-xs text-muted">
                For other districts, you can browse, compare and send enquiries.
            </p>

            <a href="{{ route('site.buying-options') }}" class="ab-btn ab-btn-primary mt-4 text-xs">
                Check Your Location <x-ui.icon name="arrow-right" :size="13" />
            </a>
        </div>

        {{-- Why choose --}}
        <div class="min-w-0 lg:col-span-8">
            <h2 class="mb-4 text-lg font-extrabold">Why Choose AutoBazaar App?</h2>

            <ul class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-5" data-reveal-group>
                @foreach ([
                    ['shield', 'Trusted Platform', 'Safe & Secure'],
                    ['users', 'Direct Purchase', 'No Middlemen'],
                    ['rupee', 'Best Price', 'Transparent Deals'],
                    ['phone', 'Easy to Use', 'Simple & Fast'],
                    ['headset', 'Dedicated Support', "We're always with you"],
                ] as [$icon, $title, $note])
                    <li class="ab-card flex flex-col items-center gap-1.5 px-2 py-4 text-center">
                        <x-ui.tone-icon :icon="$icon" tone="brand" :size="18" shape="circle" />
                        <span class="mt-1 text-xs font-bold leading-tight">{{ $title }}</span>
                        <span class="text-[10px] leading-tight text-muted">{{ $note }}</span>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
</section>

{{-- ============================================================ testimonials --}}
<section class="ab-container pb-10">
    <div class="grid gap-6 lg:grid-cols-12">

        <div class="min-w-0 lg:col-span-7">
            <h2 class="mb-4 text-lg font-extrabold">Our Users Love Us</h2>

            <ul class="grid gap-3 sm:grid-cols-3" data-reveal-group>
                @foreach ($site['testimonials'] as $testimonial)
                    <li class="ab-card flex h-full flex-col p-4">
                        <x-ui.icon name="quote" :size="20" class="text-line" />

                        <p class="mt-2 flex-1 text-xs leading-relaxed text-ink-soft">
                            "{{ $testimonial['quote'] }}"
                        </p>

                        <p class="mt-3 text-xs font-bold">
                            – {{ $testimonial['name'] }}, {{ $testimonial['city'] }}
                        </p>

                        <x-ui.rating :rating="$testimonial['rating']" :show-value="false" :size="12" class="mt-1" />
                    </li>
                @endforeach
            </ul>
        </div>

        {{-- Community + help --}}
        <div class="min-w-0 space-y-4 lg:col-span-5">
            <div class="ab-card flex items-center gap-4 p-5">
                <x-ui.icon name="users" :size="34" class="shrink-0 text-brand-500" />
                <div class="flex-1">
                    <p class="text-sm font-extrabold">Be a Part of the AutoBazaar Community</p>
                    <p class="mt-0.5 text-[11px] text-muted">
                        Smarter Drivers. Stronger Community. Download the app today!
                    </p>
                    <button type="button" class="ab-btn ab-btn-primary mt-3 w-full text-xs">
                        Download Now <x-ui.icon name="arrow-right" :size="14" />
                    </button>
                </div>
            </div>

            <a href="https://wa.me/{{ $site['contact']['whatsapp'] }}" target="_blank" rel="noopener"
               class="ab-card flex items-center gap-3 p-5 ab-lift">
                <x-ui.icon name="whatsapp" :size="30" class="text-[#25D366]" />
                <span class="leading-tight">
                    <span class="block text-sm font-extrabold">Need Help?</span>
                    <span class="block text-xs text-muted">Chat on WhatsApp</span>
                    <span class="block text-base font-extrabold text-brand-600">{{ $site['contact']['phone'] }}</span>
                </span>
            </a>
        </div>
    </div>
</section>

@endsection
