{{--
    SEO + social sharing tags for every website page. Included once from site/layout.

    A page can set any of these sections; everything has a sensible fallback:
      title        page title                 (already set on every page)
      description  1–2 sentence summary       (falls back to the site description)
      og_image     absolute image URL         (falls back to the logo)
      og_type      'website' | 'product' | 'article'
      robots       e.g. 'noindex, nofollow'   for private pages (cart, account…)
      schema       extra <script type="application/ld+json"> blocks for that page

    Outside production the whole site stays hidden from search engines.
--}}
@php
    $brandName   = $site['brand']['name'] ?? 'AutoBazaar';
    // Sections arrive HTML-escaped; decode once so {{ }} below escapes exactly once.
    $seoTitle    = trim(html_entity_decode($__env->yieldContent('title', $brandName), ENT_QUOTES));
    $seoFullTitle = $seoTitle === $brandName ? $brandName : $seoTitle . ' — ' . $brandName;
    // Google cuts titles at roughly 60-65 characters. A long headline keeps its words and loses the suffix.
    if (mb_strlen($seoFullTitle) > 65) {
        $seoFullTitle = $seoTitle;
    }
    $seoDesc     = trim(preg_replace('/\s+/', ' ', strip_tags(html_entity_decode(
                        $__env->yieldContent('description', "India's trusted auto marketplace. Compare autorickshaws, check on-road price, calculate EMI and shop accessories."),
                        ENT_QUOTES
                   ))));
    $seoDesc     = \Illuminate\Support\Str::limit($seoDesc, 300, '…');
    $seoImage    = trim($__env->yieldContent('og_image')) ?: asset('assets/site/autobazaar-logo.png');
    $seoType     = trim($__env->yieldContent('og_type', 'website'));
    // Canonical = this page without tracking/query noise, so filters and ?utm links don't split ranking.
    $seoUrl      = url()->current();
    $seoRobots   = app()->environment('production')
                        ? trim($__env->yieldContent('robots', 'index, follow, max-image-preview:large'))
                        : 'noindex, nofollow';

    $contact  = $site['contact'] ?? [];
    $sameAs   = collect($site['socials'] ?? [])->pluck('url')->filter(fn ($u) => is_string($u) && str_starts_with($u, 'http'))->values()->all();

    $orgSchema = array_filter([
        '@context'    => 'https://schema.org',
        '@type'       => 'AutoDealer',
        '@id'         => url('/') . '#organization',
        'name'        => $brandName,
        'url'         => url('/'),
        'logo'        => asset('assets/site/autobazaar-logo.png'),
        'image'       => asset('assets/site/autobazaar-logo.png'),
        'description' => "New and used autorickshaws, genuine accessories, finance help and government scheme guidance.",
        'telephone'   => $contact['phone_e164'] ?? null,
        'openingHours' => 'Mo-Sa 09:30-19:00',
        'email'       => $contact['email'] ?? null,
        'address'     => ! empty($contact['address']) ? [
            '@type'           => 'PostalAddress',
            'streetAddress'   => $contact['address'],
            'addressRegion'   => 'Tamil Nadu',
            'addressCountry'  => 'IN',
        ] : null,
        'areaServed'  => $site['service_area']['districts'] ?? null,
        'sameAs'      => $sameAs ?: null,
    ]);

    $siteSchema = [
        '@context' => 'https://schema.org',
        '@type'    => 'WebSite',
        '@id'      => url('/') . '#website',
        'name'     => $brandName,
        'url'      => url('/'),
        'inLanguage' => 'en-IN',
        'publisher'  => ['@id' => url('/') . '#organization'],
        'potentialAction' => [
            '@type'       => 'SearchAction',
            'target'      => route('site.search') . '?q={search_term_string}',
            'query-input' => 'required name=search_term_string',
        ],
    ];

    $jsonFlags = JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP;
@endphp

    <title>{{ $seoFullTitle }}</title>
    <meta name="description" content="{{ $seoDesc }}">
    <meta name="robots" content="{{ $seoRobots }}">
    <link rel="canonical" href="{{ $seoUrl }}">

    {{-- Link previews: WhatsApp, Facebook, LinkedIn --}}
    <meta property="og:site_name" content="{{ $brandName }}">
    <meta property="og:type" content="{{ $seoType }}">
    <meta property="og:title" content="{{ $seoFullTitle }}">
    <meta property="og:description" content="{{ $seoDesc }}">
    <meta property="og:url" content="{{ $seoUrl }}">
    <meta property="og:image" content="{{ $seoImage }}">
    <meta property="og:image:alt" content="{{ $seoTitle }}">
    <meta property="og:locale" content="en_IN">

    {{-- X / Twitter --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $seoFullTitle }}">
    <meta name="twitter:description" content="{{ $seoDesc }}">
    <meta name="twitter:image" content="{{ $seoImage }}">

    {{-- Browser chrome + icons --}}
    <meta name="theme-color" content="#0B5D3B">
    <meta name="application-name" content="{{ $brandName }}">
    <meta name="apple-mobile-web-app-title" content="{{ $brandName }}">
    <link rel="icon" href="{{ asset(getSetting('fav_icon') ?: 'assets/site/autobazaar-logo.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('assets/site/autobazaar-logo.png') }}">

    {{-- Who we are + the site search box, for Google --}}
    <script type="application/ld+json">{!! json_encode($orgSchema, $jsonFlags) !!}</script>
    <script type="application/ld+json">{!! json_encode($siteSchema, $jsonFlags) !!}</script>

    {{-- Page-specific structured data (vehicle, FAQ, article…) --}}
    @yield('schema')
    @stack('schema')
