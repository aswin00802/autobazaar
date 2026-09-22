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
         // One business number, managed in Admin > Settings > General (see App\Support\SiteData).
         'lines' => array_values(array_filter([
             ['text' => $contact['phone'], 'href' => 'tel:' . $contact['phone_e164']],
             ! empty($contact['phone_alt'])
                 ? ['text' => $contact['phone_alt'], 'href' => 'tel:+91' . preg_replace('/\D/', '', $contact['phone_alt'])]
                 : null,
         ]))],
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
                    <img src="{{ asset('assets/site/app-qr-play.svg') }}" alt="QR code: scan to get the AutoBazaar app on Google Play"
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

    {{-- Browse links: real pages for every brand and the most popular models, plus the
         towns we serve. Helps visitors jump straight in, and gives search engines a
         crawlable path to each model from every page. Cached for an hour. --}}
    @if (config('site_design.footer_browse_links'))
    @php
        $footLinks = \Illuminate\Support\Facades\Cache::remember('site_footer_browse_links', 3600, function () {
            try {
                $vehicles = collect(app(\App\Services\VehicleCatalogService::class)->all());

                return [
                    'brands' => $vehicles->filter(fn ($v) => ! empty($v['brand_slug']))
                        ->unique('brand_slug')->map(fn ($v) => ['label' => $v['brand'] . ' Autos', 'url' => route('site.brand', $v['brand_slug'])])->values()->all(),
                    'models' => $vehicles->filter(fn ($v) => ! empty($v['brand_slug']) && ! empty($v['model_slug']))
                        ->take(8)->map(fn ($v) => ['label' => $v['name'] . ' Price', 'url' => route('site.model', [$v['brand_slug'], $v['model_slug']])])->values()->all(),
                ];
            } catch (\Throwable $e) {
                return ['brands' => [], 'models' => []];
            }
        });
        $footAreas = $site['service_area']['districts'] ?? [];
    @endphp

    @if (count($footLinks['brands']) || count($footLinks['models']))
        {{-- Layout only. Headings, underline, arrows and link text reuse the exact classes of
             the Quick Menu columns above, so this row reads as part of the footer. --}}
        <style>
            .ab-foot-browse { border-top: 1px solid #E2E7E4; padding: 30px 0 38px; display: grid; gap: 28px 32px; }
            @media (min-width: 1024px) { .ab-foot-browse { grid-template-columns: 5fr 7fr; } }
            .ab-foot-browse ul { display: flex; flex-wrap: wrap; gap: 10px 22px; margin: 16px 0 0; padding: 0; list-style: none; }
            .ab-foot-areas { grid-column: 1 / -1; display: flex; align-items: flex-start; gap: 8px; margin: 0; font-size: 13px; line-height: 1.6; color: #6B7671; }
        </style>
        <nav class="ab-container" aria-label="Browse autos">
            <div class="ab-foot-browse">
                @foreach ([['Popular Brands', $footLinks['brands']], ['Popular Models', $footLinks['models']]] as [$browseHeading, $browseLinks])
                    @if (count($browseLinks))
                        <div>
                            <h3 class="text-xs font-extrabold uppercase tracking-wider text-ink">{{ $browseHeading }}</h3>
                            <span class="mt-2 block h-0.5 w-8 rounded bg-accent-500"></span>
                            <ul>
                                @foreach ($browseLinks as $link)
                                    <li>
                                        <a href="{{ $link['url'] }}"
                                           class="group inline-flex items-center gap-1.5 text-sm font-semibold text-ink-soft
                                                  transition-colors hover:text-brand-500">
                                            <x-ui.icon name="chevron-right" :size="13"
                                                       class="shrink-0 text-brand-500 transition-transform duration-200 group-hover:translate-x-1" />
                                            {{ $link['label'] }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                @endforeach

                @if (count($footAreas))
                    <p class="ab-foot-areas">
                        <x-ui.icon name="pin" :size="16" class="mt-0.5 shrink-0 text-brand-500" />
                        <span>Autorickshaw sales, finance and accessories in {{ implode(', ', $footAreas) }} and across Tamil Nadu.</span>
                    </p>
                @endif
            </div>
        </nav>
    @endif
    @endif
    {{-- Bottom bar --}}
    <div class="bg-brand-700 text-white">
        <div class="ab-container flex flex-col items-center justify-between gap-3 py-4 text-center text-xs sm:flex-row sm:text-left">
            <p class="text-white/85">
                &copy; {{ date('Y') }} Auto Bazaar. All Rights Reserved.
                {{-- Wording comes from config/site_design.php → footer_credit --}}
                @php $credit = config('site_design.footer_credit', []); @endphp
                @if (! empty($credit['name']))
                    {{ $credit['text'] ?? 'Crafted by' }}
                    <a href="{{ $credit['url'] ?? '#' }}" target="_blank" rel="noopener"
                       class="font-bold text-accent-500 underline-offset-2 hover:underline">{{ $credit['name'] }}</a>
                @endif
            </p>
            <div class="flex flex-wrap items-center justify-center gap-2.5">
                <x-ui.store-badge store="google" tone="dark" />
                <x-ui.store-badge store="apple" tone="dark" />
            </div>
        </div>
    </div>
</footer>
