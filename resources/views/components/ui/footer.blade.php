@props(['site'])

@php
    /*
     * Footer with the same columns and business details as the original
     * site (Quick Menu · Auto Bazaar · Social Media · Connect With Us),
     * re-set in the new design system. Columns fade in as a staggered wave
     * via [data-reveal-group]; motion is dropped for reduced-motion users.
     *
     * Kept light so the dark trust bar above it on the home page stays
     * distinct instead of merging into one tall dark block.
     */
    $contact = $site['contact'];

    $href = fn (array $i) => isset($i['param'])
        ? route($i['route'], $i['param'])
        : route($i['route']);

    $connect = [
        ['icon' => 'phone-call', 'label' => 'Call us',
         'lines' => [
             ['text' => $contact['phone'], 'href' => 'tel:' . $contact['phone_e164']],
             ['text' => $contact['phone_alt'], 'href' => 'tel:+91' . preg_replace('/\D/', '', $contact['phone_alt'])],
         ]],
        ['icon' => 'mail', 'label' => 'Email',
         'lines' => [['text' => $contact['email'], 'href' => 'mailto:' . $contact['email']]]],
        ['icon' => 'clock', 'label' => 'Working hours',
         'lines' => [['text' => $contact['hours']]]],
        ['icon' => 'pin', 'label' => 'Address',
         'lines' => [['text' => $contact['address']]]],
    ];
@endphp

<footer class="border-t border-line bg-surface">
    <div class="ab-container py-10 lg:py-12">
        <div class="grid gap-x-8 gap-y-9 sm:grid-cols-2 lg:grid-cols-12" data-reveal-group>

            {{-- Brand --}}
            <div class="lg:col-span-3" data-reveal>
                <a href="{{ route('site.home') }}" class="inline-block">
                    <x-ui.logo :site="$site" size="sm" />
                </a>
                <p class="ab-script mt-3 text-xl text-brand-500">{{ $site['brand']['tagline'] }}</p>
                <p class="mt-2 max-w-xs text-sm leading-relaxed text-muted">
                    New and used autorickshaws, genuine accessories, finance help and
                    government scheme guidance — all in one place.
                </p>
            </div>

            {{-- Link columns: Quick Menu, Auto Bazaar --}}
            @foreach ($site['footer'] as $column)
                <div class="lg:col-span-2" data-reveal>
                    <h3 class="text-xs font-extrabold uppercase tracking-wider text-ink">
                        {{ $column['heading'] }}
                    </h3>
                    <span class="mt-2 block h-0.5 w-8 rounded bg-accent-500"></span>
                    <ul class="mt-4 space-y-2.5">
                        @foreach ($column['links'] as $item)
                            <li>
                                <a href="{{ $href($item) }}"
                                   class="group inline-flex items-center gap-1.5 text-sm font-semibold text-ink-soft
                                          transition-colors hover:text-brand-500">
                                    <x-ui.icon name="chevron-right" :size="13"
                                               class="shrink-0 text-brand-500 transition-transform duration-200 group-hover:translate-x-1" />
                                    {{ $item['label'] }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endforeach

            {{-- Social Media + app QR --}}
            <div class="lg:col-span-2" data-reveal>
                <h3 class="text-xs font-extrabold uppercase tracking-wider text-ink">Social Media</h3>
                <span class="mt-2 block h-0.5 w-8 rounded bg-accent-500"></span>
                <ul class="mt-4 flex items-center gap-2">
                    @foreach ($site['socials'] as $social)
                        <li>
                            <a href="{{ $social['url'] }}" target="_blank" rel="noopener"
                               aria-label="{{ $social['label'] }}" title="{{ $social['label'] }}"
                               class="grid h-9 w-9 place-items-center rounded-lg bg-canvas text-ink-soft
                                      transition-all duration-200 hover:-translate-y-0.5 hover:bg-brand-500
                                      hover:text-white hover:shadow-md">
                                <x-ui.icon :name="$social['icon']" :size="18" />
                            </a>
                        </li>
                    @endforeach
                </ul>

                <a href="{{ route('site.app') }}" class="group mt-5 inline-block" aria-label="Download the AutoBazaar app">
                    <img src="{{ asset('assets/site/app-qr.png') }}" alt="Scan to download the AutoBazaar app"
                         width="112" height="112" loading="lazy"
                         class="h-28 w-28 rounded-lg border border-line bg-white p-1.5
                                transition-transform duration-200 group-hover:scale-105">
                    <span class="mt-1.5 block text-[11px] font-semibold text-muted group-hover:text-brand-500">
                        Scan to download the app
                    </span>
                </a>
            </div>

            {{-- Connect With Us --}}
            <div class="lg:col-span-3" data-reveal>
                <h3 class="text-xs font-extrabold uppercase tracking-wider text-ink">Connect With Us</h3>
                <span class="mt-2 block h-0.5 w-8 rounded bg-accent-500"></span>
                <ul class="mt-4 space-y-3">
                    @foreach ($connect as $row)
                        <li class="flex items-start gap-3">
                            <span class="mt-0.5 grid h-8 w-8 shrink-0 place-items-center rounded-lg bg-brand-50 text-brand-500">
                                <x-ui.icon :name="$row['icon']" :size="16" />
                            </span>
                            <span class="min-w-0 text-sm leading-snug">
                                <span class="sr-only">{{ $row['label'] }}:</span>
                                @foreach ($row['lines'] as $line)
                                    @if (isset($line['href']))
                                        <a href="{{ $line['href'] }}"
                                           class="block font-semibold text-ink-soft transition-colors hover:text-brand-500">
                                            {{ $line['text'] }}
                                        </a>
                                    @else
                                        <span class="block text-ink-soft">{{ $line['text'] }}</span>
                                    @endif
                                @endforeach
                            </span>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>

    {{-- Bottom bar --}}
    <div class="bg-brand-700 text-white">
        <div class="ab-container flex flex-col items-center justify-between gap-3 py-4 text-center text-xs sm:flex-row sm:text-left">
            <p class="text-white/85">
                &copy; {{ date('Y') }} Auto Bazaar. All Rights Reserved.
                Designed by
                <a href="https://zigainfotech.com" target="_blank" rel="noopener"
                   class="font-bold text-accent-500 underline-offset-2 hover:underline">Ziga Infotech</a>
            </p>
            <x-ui.store-badge store="google" tone="dark" />
        </div>
    </div>
</footer>
