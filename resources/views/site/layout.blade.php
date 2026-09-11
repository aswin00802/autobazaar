<!DOCTYPE html>
<html lang="en" class="antialiased">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @unless (app()->environment('production'))
        <meta name="robots" content="noindex, nofollow">
    @endunless
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', $site['brand']['name']) — {{ $site['brand']['name'] }}</title>
    <meta name="description" content="@yield('description', 'India\'s trusted auto marketplace. Compare autorickshaws, check on-road price, calculate EMI and shop accessories.')">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Caveat:wght@600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Reveal animations start at opacity 0 and are switched on by JS. If the
         bundle fails to load, this makes sure the page is still readable
         rather than a screen of invisible content. --}}
    <noscript>
        <style>[data-reveal]{opacity:1!important;transform:none!important}</style>
    </noscript>
</head>
{{-- Cart count is rendered server-side so the badge is correct on first paint,
     before Alpine has booted. --}}
<body class="min-h-screen bg-canvas text-ink"
      data-cart-count="{{ app(\App\Services\CartService::class)->itemCount() }}">

    {{-- Skip link: the header is tall, keyboard users need a way past it. --}}
    <a href="#main"
       class="sr-only focus:not-sr-only focus:absolute focus:left-4 focus:top-4 focus:z-50
              focus:rounded-lg focus:bg-brand-500 focus:px-4 focus:py-2 focus:text-white">
        Skip to content
    </a>

    <x-ui.header :site="$site" :locations="$locations" />

    <main id="main">
        @yield('content')
    </main>

    <x-ui.footer :site="$site" />

    {{-- Spacer so the fixed compare tray never covers the footer's last row --}}
    <div x-data x-show="$store.compare.count > 0" x-cloak class="h-16" aria-hidden="true"></div>

    {{-- Persistent compare tray + floating WhatsApp button --}}
    <x-ui.compare-tray />
    <x-ui.whatsapp-fab :contact="$site['contact']" />


    @stack('scripts')
</body>
</html>
