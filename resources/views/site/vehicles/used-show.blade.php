@extends('site.layout')

@php
    $heading = 'Used ' . $listing['name'] . ($listing['year'] ? ' ' . $listing['year'] : '');
    $facts = array_filter([$listing['km_label'], $listing['owner'], $listing['fuel'], $listing['location']]);
    $pageUrl = route('site.used-auto', [$listing['id'], $listing['slug']]);
@endphp

{{-- Two autos of the same model and year exist, so the title carries the km (or the listing id) --}}
@php $titleExtra = $listing['km_label'] ?: $listing['ref']; @endphp
@section('title', $heading . ', ' . $titleExtra . ' for Sale')
@section('description', $heading . ' for sale at AutoBazaar' . ($facts ? ': ' . implode(', ', $facts) : '') . '. ' . $listing['price_label'] . '. RC, FC and permit status shown. Call or WhatsApp to see it.')
@section('og_type', 'product')
@if (! empty($listing['images'][0]))
    @section('og_image', asset($listing['images'][0]))
@endif

@section('schema')
    @php
        $usedSchema = array_filter([
            '@context'    => 'https://schema.org',
            '@type'       => 'Product',
            'name'        => $heading,
            'description' => $listing['description'] ?: ($heading . ($facts ? ' — ' . implode(', ', $facts) : '')),
            'image'       => array_map(fn ($i) => asset($i), $listing['images']) ?: null,
            'category'    => 'Used Autorickshaw',
            'sku'         => $listing['ref'],
            'brand'       => ['@type' => 'Brand', 'name' => $listing['brand']],
            'offers'      => $listing['price'] > 0 ? [
                '@type'         => 'Offer',
                'url'           => $pageUrl,
                'priceCurrency' => 'INR',
                'price'         => $listing['price'],
                'availability'  => 'https://schema.org/InStock',
                'itemCondition' => 'https://schema.org/UsedCondition',
                'seller'        => ['@id' => url('/') . '#organization'],
            ] : null,
        ]);
    @endphp
    <script type="application/ld+json">{!! json_encode($usedSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP) !!}</script>
@endsection

@section('content')

@php
    $contact = $site['contact'];
    $waText = 'Hi AutoBazaar, I am interested in the ' . $heading . ' (ID ' . $listing['ref'] . ', ' . $listing['price_label'] . '). ' . $pageUrl;
    $docTone = [
        'valid' => ['bg-brand-50 text-brand-600', 'check-circle'],
        'expired' => ['bg-red-50 text-danger', 'warning'],
        'unknown' => ['bg-canvas text-muted', 'info'],
    ];
@endphp

<style>
    .ab-ud-frame { position: relative; height: 380px; overflow: hidden; border-radius: 12px; background: #F5F7F6; }
    @media (max-width: 640px) { .ab-ud-frame { height: 250px; } }
    .ab-ud-frame img.is-photo { width: 100%; height: 100%; object-fit: contain; }
    .ab-ud-frame .is-logo { position: absolute; inset: 0; display: grid; place-items: center; align-content: center; gap: 10px; color: #6B7671; font-size: 13px; font-weight: 600; text-align: center; }
    .ab-ud-frame .is-logo img { max-height: 70px; max-width: 180px; opacity: .85; }
    .ab-ud-year { position: absolute; left: 14px; top: 14px; z-index: 1; padding: 3px 10px; border-radius: 5px; background: #0B5D3B; color: #fff; font-size: 12px; font-weight: 700; }
    .ab-ud-thumbs { display: flex; gap: 8px; margin-top: 10px; overflow-x: auto; padding-bottom: 4px; }
    .ab-ud-thumbs button { flex: none; width: 76px; height: 58px; padding: 0; overflow: hidden; border-radius: 8px; border: 2px solid transparent; background: #F5F7F6; cursor: pointer; }
    .ab-ud-thumbs button.is-on { border-color: #0B5D3B; }
    .ab-ud-thumbs img { width: 100%; height: 100%; object-fit: cover; }
    .ab-ud-facts { display: flex; flex-wrap: wrap; gap: 6px; margin: 12px 0 0; padding: 0; list-style: none; }
    .ab-ud-facts li { padding: 4px 10px; border-radius: 999px; background: #EEF1F0; font-size: 12px; font-weight: 600; color: #3F4744; }
    .ab-ud-price { margin: 16px 0 0; font-size: 32px; font-weight: 800; line-height: 1.1; color: #0B5D3B; }
    .ab-ud-docs { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 8px; margin: 16px 0 0; padding: 0; list-style: none; }
    .ab-ud-docs li { display: flex; align-items: center; gap: 8px; padding: 9px 12px; border-radius: 10px; font-size: 12px; font-weight: 600; }
    .ab-ud-docs small { display: block; font-size: 10px; font-weight: 700; letter-spacing: .05em; text-transform: uppercase; opacity: .8; }
    .ab-ud-cta { display: grid; gap: 8px; margin-top: 18px; }
    @media (min-width: 480px) { .ab-ud-cta { grid-template-columns: 1fr 1fr; } }
    .ab-ud-wa { background: #25D366 !important; color: #fff !important; }
    .ab-ud-wa:hover { background: #1ebe5b !important; }
    .ab-ud-table { width: 100%; border-collapse: collapse; font-size: 13px; }
    .ab-ud-table th, .ab-ud-table td { padding: 10px 4px; border-bottom: 1px solid #E2E7E4; text-align: left; vertical-align: top; }
    .ab-ud-table th { width: 44%; font-weight: 500; color: #6B7671; }
    .ab-ud-table td { font-weight: 700; }
    .ab-ud-table tr:last-child th, .ab-ud-table tr:last-child td { border-bottom: 0; }
    .ab-ud-note { margin: 14px 0 0; font-size: 11px; line-height: 1.6; color: #6B7671; }
    .ab-ud-mini { position: relative; display: block; height: 130px; overflow: hidden; background: #F5F7F6; }
    .ab-ud-mini img.is-photo { width: 100%; height: 100%; object-fit: cover; }
    .ab-ud-mini .is-logo { position: absolute; inset: 0; display: grid; place-items: center; }
    .ab-ud-mini .is-logo img { max-height: 40px; max-width: 110px; opacity: .85; }
</style>

<section class="ab-container py-6">

    <x-ui.breadcrumb :items="[
        ['label' => 'Home', 'href' => route('site.home')],
        ['label' => 'Used Autos', 'href' => route('site.used-autos')],
        ['label' => $listing['name']],
    ]" />

    <div class="mt-5 grid gap-6 lg:grid-cols-12">

        {{-- ------------------------------------------------------------ photos --}}
        <div class="min-w-0 lg:col-span-7" x-data="{ active: 0 }">
            <div class="ab-card p-3">
                <div class="ab-ud-frame">
                    @if ($listing['year'])
                        <span class="ab-ud-year">{{ $listing['year'] }}</span>
                    @endif

                    @forelse ($listing['images'] as $i => $image)
                        <img src="{{ asset($image) }}" alt="{{ $heading }} — photo {{ $i + 1 }}" class="is-photo"
                             x-show="active === {{ $i }}" @if ($i > 0) x-cloak loading="lazy" @endif>
                    @empty
                        <span class="is-logo">
                            @if ($listing['brand_logo'])
                                <img src="{{ asset($listing['brand_logo']) }}" alt="{{ $listing['brand'] }}">
                            @else
                                <x-ui.icon name="auto" :size="48" />
                            @endif
                            Photos on request.<br>WhatsApp us and we will send them.
                        </span>
                    @endforelse
                </div>

                @if (count($listing['images']) > 1)
                    <div class="ab-ud-thumbs" role="tablist" aria-label="Photos">
                        @foreach ($listing['images'] as $i => $image)
                            <button type="button" @click="active = {{ $i }}" :class="active === {{ $i }} && 'is-on'"
                                    role="tab" aria-label="Show photo {{ $i + 1 }}">
                                <img src="{{ asset($image) }}" alt="" loading="lazy">
                            </button>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- ------------------------------------------------------- details --}}
            <div class="ab-card mt-5 p-5">
                <h2 class="ab-h text-base font-extrabold">Vehicle Details</h2>
                <table class="ab-ud-table mt-4">
                    <tbody>
                        @foreach ($listing['details'] as $label => $value)
                            <tr><th scope="row">{{ $label }}</th><td>{{ $value }}</td></tr>
                        @endforeach
                    </tbody>
                </table>

                @if ($listing['description'])
                    <h3 class="mt-5 text-sm font-bold">Seller notes</h3>
                    <p class="mt-1.5 text-sm leading-relaxed text-ink-soft">{{ $listing['description'] }}</p>
                @endif
            </div>
        </div>

        {{-- ------------------------------------------------------ price + CTA --}}
        <aside class="min-w-0 lg:col-span-5">
            <div class="ab-card p-5">
                <p class="text-xs font-bold uppercase tracking-wider text-muted">Used · {{ $listing['brand'] }}</p>
                <h1 class="mt-1 text-2xl font-extrabold tracking-tight">{{ $heading }}</h1>

                @if ($facts)
                    <ul class="ab-ud-facts">
                        @foreach ($facts as $fact)<li>{{ $fact }}</li>@endforeach
                    </ul>
                @endif

                <p class="ab-ud-price">{{ $listing['price_label'] }}</p>
                @if ($listing['price'] > 0)
                    <p class="text-[11px] text-muted">Asking price. Transfer and RTO charges are extra.</p>
                @endif

                <ul class="ab-ud-docs">
                    @foreach ($listing['documents'] as $doc => $status)
                        <li class="{{ $docTone[$status['state']][0] }}">
                            <x-ui.icon :name="$docTone[$status['state']][1]" :size="17" class="shrink-0" />
                            <span><small>{{ $doc }}</small>{{ $status['label'] }}</span>
                        </li>
                    @endforeach
                </ul>

                <div class="ab-ud-cta">
                    <a href="https://wa.me/{{ $contact['whatsapp'] }}?text={{ rawurlencode($waText) }}" target="_blank" rel="noopener"
                       class="ab-btn ab-ud-wa">
                        <x-ui.icon name="whatsapp" :size="18" /> WhatsApp Us
                    </a>
                    <a href="tel:{{ $contact['phone_e164'] }}" class="ab-btn ab-btn-primary">
                        <x-ui.icon name="phone-call" :size="17" /> Call {{ $contact['phone'] }}
                    </a>
                </div>
                <a href="{{ route('site.contact') }}" class="ab-btn ab-btn-ghost mt-2 w-full">Request a call back</a>

                <p class="ab-ud-note">
                    Quote listing ID <strong>{{ $listing['ref'] }}</strong> when you call. Documents are as declared by the seller;
                    we check the originals with you before any payment.
                </p>
            </div>

            <div class="ab-card mt-5 bg-accent-50 p-5">
                <h2 class="flex items-center gap-2 text-sm font-bold">
                    <x-ui.icon name="calculator" :size="17" class="text-brand-500" /> Need finance for this auto?
                </h2>
                <p class="mt-1.5 text-xs leading-relaxed text-ink-soft">We arrange loans on used autos too. Check what the monthly EMI could look like.</p>
                <a href="{{ route('site.finance') }}" class="mt-3 inline-flex items-center gap-1.5 text-sm font-semibold text-brand-500 hover:text-brand-600">
                    Open the EMI calculator <x-ui.icon name="arrow-right" :size="15" />
                </a>
            </div>
        </aside>
    </div>

    {{-- ---------------------------------------------------------- more autos --}}
    @if (count($similar))
        <div class="mt-10">
            <x-ui.section-heading title="More Used Autos" :href="route('site.used-autos')" />
            <ul class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($similar as $other)
                    @php $otherUrl = route('site.used-auto', [$other['id'], $other['slug']]); @endphp
                    <li class="ab-card flex flex-col overflow-hidden ab-lift">
                        <a href="{{ $otherUrl }}" class="ab-ud-mini" aria-label="View {{ $other['name'] }}">
                            @if ($other['image'])
                                <img src="{{ asset($other['image']) }}" alt="Used {{ $other['name'] }}" class="is-photo" loading="lazy">
                            @elseif ($other['brand_logo'])
                                <span class="is-logo"><img src="{{ asset($other['brand_logo']) }}" alt="{{ $other['brand'] }}" loading="lazy"></span>
                            @endif
                        </a>
                        <div class="p-4">
                            <h3 class="text-sm font-bold"><a href="{{ $otherUrl }}" class="hover:text-brand-500">{{ $other['name'] }}{{ $other['year'] ? ' · ' . $other['year'] : '' }}</a></h3>
                            <p class="text-[11px] text-muted">{{ implode(' · ', array_filter([$other['km_label'], $other['owner']])) }}</p>
                            <p class="mt-1.5 text-base font-extrabold">{{ $other['price_label'] }}</p>
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif
</section>

@endsection
