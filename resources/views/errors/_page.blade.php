{{--
    Shared shell for the branded error pages (403, 405, 419, 429, 500, 503).
    Expects: $code, $title, $lede and optionally $retry (show a "Try Again" button).

    Self-contained on purpose — no layout, no database, no Vite requirement —
    because an error page that can itself fail is worse than none.
    The 404 page keeps its own richer template.
--}}
@php
    $site = \App\Support\SiteData::site();
    $hasBuild = file_exists(public_path('build/manifest.json'));
    $retry = $retry ?? false;
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>{{ $title }} — {{ $site['brand']['name'] }}</title>
    <style>
        :root {
            --ab-brand: #0B5D3B; --ab-brand-700: #08462C; --ab-accent: #FFC107;
            --ab-ink: #1A1D1B; --ab-muted: #6B7671; --ab-line: #E2E7E4; --ab-canvas: #F5F7F6;
        }
        *, *::before, *::after { box-sizing: border-box; }
        body {
            margin: 0; min-height: 100vh; display: flex; flex-direction: column;
            background: var(--ab-canvas); color: var(--ab-ink);
            font-family: 'Plus Jakarta Sans', ui-sans-serif, system-ui, -apple-system, 'Segoe UI', sans-serif;
            -webkit-font-smoothing: antialiased;
        }
        .e-wrap { flex: 1; display: grid; place-items: center; padding: 48px 20px; }
        .e-inner { width: 100%; max-width: 640px; text-align: center; }
        .e-code { font-size: clamp(72px, 17vw, 140px); font-weight: 800; line-height: .9; letter-spacing: -.04em; color: var(--ab-brand); margin: 0; }
        .e-title { font-size: clamp(20px, 4vw, 28px); font-weight: 800; margin: 18px 0 0; }
        .e-lede { margin: 10px auto 0; max-width: 480px; font-size: 15px; line-height: 1.6; color: var(--ab-muted); }
        .e-actions { display: flex; flex-wrap: wrap; gap: 10px; justify-content: center; margin-top: 26px; }
        .e-btn { display: inline-flex; align-items: center; border-radius: 10px; padding: 11px 20px; font: inherit; font-size: 14px; font-weight: 700; text-decoration: none; border: 1px solid transparent; cursor: pointer; }
        .e-btn-primary { background: var(--ab-brand); color: #fff; }
        .e-btn-primary:hover { background: var(--ab-brand-700); }
        .e-btn-accent { background: var(--ab-accent); color: var(--ab-ink); }
        .e-btn-ghost { background: #fff; color: var(--ab-ink); border-color: var(--ab-line); }
        .e-foot { border-top: 1px solid var(--ab-line); background: #fff; padding: 16px 20px; text-align: center; font-size: 12px; color: var(--ab-muted); }
        .e-foot a { color: var(--ab-brand); font-weight: 700; text-decoration: none; }
        @media (prefers-reduced-motion: no-preference) {
            .e-inner { animation: e-rise .45s cubic-bezier(.22,.61,.36,1) both; }
            @keyframes e-rise { from { opacity: 0; transform: translateY(14px); } to { opacity: 1; transform: none; } }
        }
    </style>
</head>
<body>
<main class="e-wrap">
    <div class="e-inner">
        <p class="e-code">{{ $code }}</p>
        <h1 class="e-title">{{ $title }}</h1>
        <p class="e-lede">{{ $lede }}</p>

        <div class="e-actions">
            @if ($retry)
                <a class="e-btn e-btn-accent" href="javascript:location.reload()">Try Again</a>
            @endif
            <a class="e-btn e-btn-primary" href="{{ url('/') }}">Back to Home</a>
            <a class="e-btn e-btn-ghost" href="javascript:history.back()">Go Back</a>
        </div>
    </div>
</main>

<footer class="e-foot">
    Need help? Call or WhatsApp
    <a href="https://wa.me/{{ $site['contact']['whatsapp'] }}" target="_blank" rel="noopener">{{ $site['contact']['phone'] }}</a>
    &nbsp;·&nbsp; {{ $site['brand']['name'] }}
</footer>
</body>
</html>
