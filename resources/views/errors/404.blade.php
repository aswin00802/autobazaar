@php
    /**
     * Branded 404.
     *
     * Deliberately self-contained rather than extending a layout:
     *  - the preview layout needs $site / $locations, which an error view is
     *    never handed;
     *  - the admin layout reads Auth::user()->name, which is null for a logged
     *    out visitor and would turn this 404 into a 500.
     *
     * It also degrades if the Vite build is missing — see $hasBuild below.
     * An error page that can itself error is worse than no error page.
     */
    $site = require resource_path('fixtures/site.php');

    $isAdmin = request()->is('dashboard*') || request()->is('masters/*')
        || request()->is('auto-management/*') || request()->is('spare-parts/*')
        || request()->is('ecommerce/*') || request()->is('user-management/*')
        || request()->is('settings/*') || request()->is('services/*')
        || request()->is('roles*') || request()->is('permissions*')
        || request()->is('fairprice/*');

    $home = route('site.home');

    $links = [
            ['New Autos', route('site.new-autos')],
            ['Compare', route('site.compare')],
            ['Accessories Shop', route('site.accessories.shop')],
            ['Government Schemes', route('site.schemes')],
            ['Contact', route('site.contact')],
    ];

    $hasBuild = file_exists(public_path('build/manifest.json'));
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>Page Not Found — {{ $site['brand']['name'] }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&family=Caveat:wght@700&display=swap" rel="stylesheet">

    @if ($hasBuild)
        @vite(['resources/css/app.css'])
    @endif

    {{-- Enough styling to stand on its own if the build is not present. --}}
    <style>
        :root {
            --ab-brand: #0B5D3B; --ab-brand-700: #08462C; --ab-brand-50: #E8F3EE;
            --ab-accent: #FFC107; --ab-ink: #1A1D1B; --ab-muted: #6B7671;
            --ab-line: #E2E7E4; --ab-canvas: #F5F7F6;
        }
        *, *::before, *::after { box-sizing: border-box; }
        body {
            margin: 0; min-height: 100vh; display: flex; flex-direction: column;
            background: var(--ab-canvas); color: var(--ab-ink);
            font-family: 'Plus Jakarta Sans', ui-sans-serif, system-ui, sans-serif;
            -webkit-font-smoothing: antialiased;
        }
        .e-wrap { flex: 1; display: grid; place-items: center; padding: 48px 20px; }
        .e-inner { width: 100%; max-width: 940px; text-align: center; }
        .e-code {
            font-size: clamp(76px, 18vw, 150px); font-weight: 800; line-height: .9;
            letter-spacing: -.04em; color: var(--ab-brand); margin: 0;
        }
        .e-title { font-size: clamp(20px, 4vw, 30px); font-weight: 800; margin: 18px 0 0; }
        .e-lede {
            margin: 10px auto 0; max-width: 520px; font-size: 15px; line-height: 1.6;
            color: var(--ab-muted);
        }
        .e-script {
            font-family: 'Caveat', cursive; font-size: 26px; color: var(--ab-brand);
            margin: 6px 0 0;
        }
        .e-actions {
            display: flex; flex-wrap: wrap; gap: 10px; justify-content: center; margin-top: 26px;
        }
        .e-btn {
            display: inline-flex; align-items: center; gap: 8px; border-radius: 10px;
            padding: 11px 20px; font-size: 14px; font-weight: 700; text-decoration: none;
            border: 1px solid transparent; transition: filter .15s ease, background .15s ease;
        }
        .e-btn-primary { background: var(--ab-brand); color: #fff; }
        .e-btn-primary:hover { background: var(--ab-brand-700); }
        .e-btn-accent { background: var(--ab-accent); color: var(--ab-ink); }
        .e-btn-accent:hover { filter: brightness(.94); }
        .e-btn-ghost { background: #fff; color: var(--ab-ink); border-color: var(--ab-line); }
        .e-btn-ghost:hover { background: var(--ab-canvas); }
        .e-search { margin: 26px auto 0; max-width: 440px; display: flex;
            border: 1px solid var(--ab-line); border-radius: 10px; overflow: hidden; background: #fff; }
        .e-search input { flex: 1; border: 0; padding: 12px 14px; font: inherit; font-size: 14px; outline: none; }
        .e-search button { border: 0; background: var(--ab-brand); color: #fff; padding: 0 18px;
            font: inherit; font-size: 14px; font-weight: 700; cursor: pointer; }
        .e-links { margin: 30px 0 0; padding: 0; list-style: none;
            display: flex; flex-wrap: wrap; gap: 8px 22px; justify-content: center; }
        .e-links a { font-size: 13px; font-weight: 600; color: var(--ab-muted); text-decoration: none; }
        .e-links a:hover { color: var(--ab-brand); }
        .e-foot { border-top: 1px solid var(--ab-line); background: #fff;
            padding: 16px 20px; text-align: center; font-size: 12px; color: var(--ab-muted); }
        .e-foot a { color: var(--ab-brand); font-weight: 700; text-decoration: none; }
        .e-art { width: 100%; max-width: 360px; height: auto; margin: 0 auto 4px; display: block; }
        @media (prefers-reduced-motion: no-preference) {
            .e-inner { animation: e-rise .45s cubic-bezier(.22,.61,.36,1) both; }
            @keyframes e-rise { from { opacity: 0; transform: translateY(14px); } to { opacity: 1; transform: none; } }
        }
    </style>
</head>
<body>

<main class="e-wrap">
    <div class="e-inner">

        {{-- A wrong turn, drawn rather than fetched so the page has no dependencies --}}
        <svg class="e-art" viewBox="0 0 460 200" fill="none" role="img"
             aria-label="An autorickshaw at a road sign pointing the other way">
            <defs>
                <linearGradient id="road404" x1="0" y1="0" x2="0" y2="1">
                    <stop offset="0%" stop-color="#E2E7E4"/><stop offset="100%" stop-color="#CFD8D3"/>
                </linearGradient>
            </defs>

            <ellipse cx="230" cy="176" rx="180" ry="14" fill="#0A3D26" opacity=".07"/>
            <path d="M40 172 L170 120 L300 120 L430 172 Z" fill="url(#road404)"/>
            <path d="M214 126 h20 l2 10 h-24 z M206 146 h34 l4 16 h-42 z" fill="#fff" opacity=".85"/>

            {{-- Signpost pointing back --}}
            <rect x="86" y="86" width="7" height="74" rx="3" fill="#6B7671"/>
            <path d="M30 78 L84 78 L84 104 L30 104 L18 91 Z" fill="#FFC107"/>
            <path d="M40 88 h30 M40 95 h22" stroke="#1A1D1B" stroke-width="4" stroke-linecap="round"/>

            {{-- Autorickshaw, facing the sign --}}
            <g transform="translate(228 66) scale(.46)">
                <path d="M118 100 Q122 60 172 57 L332 57 Q374 60 378 104 L378 124 L118 124 Z" fill="#1B2420"/>
                <path d="M152 124 L378 124 L378 236 Q378 256 356 256 L172 256 Q152 256 152 236 Z" fill="#F5B324"/>
                <path d="M152 196 L378 196 L378 236 Q378 256 356 256 L172 256 Q152 256 152 236 Z" fill="#DC9C1C"/>
                <path d="M152 124 L152 236 Q152 256 132 256 L96 256 Q60 254 52 214 L50 172 Q50 146 70 132 L118 104 Z" fill="#F5B324"/>
                <path d="M78 146 L120 118 L142 118 L142 176 L68 176 Z" fill="#A8D2EC" opacity=".9"/>
                <rect x="180" y="134" width="128" height="70" rx="10" fill="#12201A" opacity=".5"/>
                <circle cx="62" cy="196" r="14" fill="#FFF6D5"/>
                <circle cx="104" cy="258" r="42" fill="#1A211E"/><circle cx="104" cy="258" r="19" fill="#98A29E"/>
                <circle cx="332" cy="258" r="42" fill="#1A211E"/><circle cx="332" cy="258" r="19" fill="#98A29E"/>
            </g>
        </svg>

        <p class="e-code">404</p>

        <h1 class="e-title">
            @if ($isAdmin)
                That admin page does not exist
            @else
                Looks like you have taken a wrong turn
            @endif
        </h1>

        @unless ($isAdmin)
            <p class="e-script">Wrong road, right destination</p>
        @endunless

        <p class="e-lede">
            @if ($isAdmin)
                The link may be out of date, or the record you were looking for has been removed.
            @else
                The page you are looking for has moved, or the link you followed is no longer
                correct. Let us get you back on route.
            @endif
        </p>

        <div class="e-actions">
            @if ($isAdmin)
                <a class="e-btn e-btn-primary" href="{{ url('/dashboard') }}">Back to Dashboard</a>
                <a class="e-btn e-btn-ghost" href="javascript:history.back()">Go Back</a>
            @else
                <a class="e-btn e-btn-primary" href="{{ $home }}">Back to Home</a>
                @unless ($isAdmin)
                    <a class="e-btn e-btn-accent" href="{{ route('site.new-autos') }}">Browse New Autos</a>
                @endunless
                <a class="e-btn e-btn-ghost" href="javascript:history.back()">Go Back</a>
            @endif
        </div>

        @unless ($isAdmin)
            <form class="e-search" action="{{ route('site.search') }}" method="GET" role="search">
                <input type="search" name="q" aria-label="Search AutoBazaar"
                       placeholder="Search autos, accessories, news, schemes...">
                <button type="submit">Search</button>
            </form>
        @endunless

        @unless ($isAdmin)
            <ul class="e-links">
                @foreach ($links as [$label, $url])
                    <li><a href="{{ $url }}">{{ $label }}</a></li>
                @endforeach
            </ul>
        @endunless
    </div>
</main>

<footer class="e-foot">
    @if ($isAdmin)
        {{ $site['brand']['name'] }} Admin
    @else
        Still stuck? Call or WhatsApp
        <a href="https://wa.me/{{ $site['contact']['whatsapp'] }}" target="_blank" rel="noopener">
            {{ $site['contact']['phone'] }}
        </a>
        &nbsp;·&nbsp; {{ $site['brand']['copyright'] }}
    @endif
</footer>

</body>
</html>
