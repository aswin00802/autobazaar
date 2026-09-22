{{--
    Design add-ons. Every block is wrapped in its own switch from config/site_design.php,
    so turning a switch off removes that look completely.

    Inline on purpose: the main stylesheet (resources/css/app.css) is untouched and no
    `npm run build` is needed. Colours are the brand tokens written as hex.

    Hooks used in the markup (they do nothing on their own):
      .ab-photo   the photo link inside an auto card
      .ab-price   the price on an auto card
      .ab-h       a section heading
      [data-ab-count]  a trust-bar number
--}}
@php $design = config('site_design', []); @endphp

<style>
@if (in_array($design['font'] ?? 'default', ['poppins', 'roboto'], true))
    /* ---------- Typeface ----------
       The whole site reads its font from this one variable, so changing it here
       restyles every page without touching the main stylesheet. */
    :root { --font-sans: '{{ $design['font'] === 'poppins' ? 'Poppins' : 'Roboto' }}', ui-sans-serif, system-ui, -apple-system, 'Segoe UI', sans-serif; }
@if (($design['font'] ?? '') === 'poppins')
    /* Poppins runs wider than the original face: pull headings and the menu in slightly. */
    h1, h2, h3 { letter-spacing: -.02em; }
    nav[aria-label="Main"] a, .ab-more-btn { letter-spacing: -.01em; }
@endif
@endif

@if (!empty($design['button_glow']))
    /* ---------- Button glow ---------- */
    .ab-btn, .ab-glow {
        position: relative; overflow: hidden; isolation: isolate;
        transition: background-color .2s ease, color .2s ease, border-color .2s ease, box-shadow .28s ease, transform .2s ease;
    }
    /* light sweep */
    .ab-btn::after, .ab-glow::after {
        content: ""; position: absolute; top: 0; bottom: 0; left: -60%; width: 45%; z-index: -1;
        background: linear-gradient(100deg, transparent, rgba(255, 255, 255, .38), transparent);
        transform: skewX(-18deg); opacity: 0;
    }
    @media (hover: hover) {
        .ab-btn:hover, .ab-glow:hover { transform: translateY(-1px); }
        .ab-btn:hover::after, .ab-glow:hover::after { opacity: 1; left: 120%; transition: left .6s ease, opacity .2s ease; }
        .ab-btn-primary:hover { box-shadow: 0 0 0 3px rgba(11, 93, 59, .16), 0 10px 26px -8px rgba(11, 93, 59, .65); }
        .ab-btn-accent:hover, .ab-glow:hover { box-shadow: 0 0 0 3px rgba(255, 193, 7, .28), 0 10px 28px -8px rgba(255, 170, 0, .8); }
        .ab-btn-outline:hover { box-shadow: 0 0 0 3px rgba(11, 93, 59, .12), 0 8px 22px -10px rgba(11, 93, 59, .5); }
        .ab-btn-ghost:hover { box-shadow: 0 0 0 3px rgba(26, 29, 27, .06), 0 8px 20px -10px rgba(26, 29, 27, .35); }
    }
    .ab-btn:active, .ab-glow:active { transform: translateY(0) scale(.985); }
    .ab-btn:disabled, .ab-btn[disabled] { transform: none !important; box-shadow: none !important; }
    .ab-btn:disabled::after, .ab-btn[disabled]::after { display: none; }
    @media (prefers-reduced-motion: reduce) {
        .ab-btn, .ab-glow { transition: background-color .2s ease, box-shadow .2s ease; }
        .ab-btn:hover, .ab-glow:hover, .ab-btn:active, .ab-glow:active { transform: none; }
        .ab-btn::after, .ab-glow::after { display: none; }
    }
@endif

@if (!empty($design['card_hover']))
    /* ---------- Card hover ---------- */
    /* Scrolling card rows clip anything that rises above them. Padding gives the lifted
       card and its shadow room; the equal negative margin keeps the page layout unchanged. */
    .ab-scroll-x { padding-top: 10px; margin-top: -10px; padding-bottom: 26px; margin-bottom: -18px; }
    .ab-card .ab-photo { position: relative; isolation: isolate; }
    .ab-card .ab-photo::before {
        content: ""; position: absolute; left: 50%; top: 50%; z-index: -1;
        width: 120px; height: 120px; border-radius: 50%;
        background: radial-gradient(circle at 50% 40%, #E8F3EE 0%, #DCEEE5 100%);
        transform: translate(-50%, -50%) scale(.86); opacity: .75;
        transition: transform .35s cubic-bezier(.2, .7, .2, 1), opacity .35s ease, background-color .35s ease;
    }
    @media (hover: hover) {
        .ab-card.ab-lift:hover {
            transform: translateY(-6px);
            border-color: #9BCBB4;
            box-shadow: 0 20px 38px -16px rgba(11, 93, 59, .38);
        }
        .ab-card:hover .ab-photo::before { transform: translate(-50%, -50%) scale(1.16); opacity: 1; }
        .ab-card:hover .ab-photo img { scale: 1.13; translate: 0 -3px; }
    }
    @media (prefers-reduced-motion: reduce) {
        .ab-card .ab-photo::before { transition: none; }
        .ab-card:hover .ab-photo img { scale: 1; translate: none; }
        .ab-card.ab-lift:hover { transform: none; }
    }
@endif

@if (!empty($design['fuel_chips']))
    /* ---------- Fuel chips ---------- */
    .ab-fuels { display: flex; flex-wrap: wrap; gap: 4px; margin-top: 6px; }
    .ab-fuel {
        display: inline-flex; align-items: center; gap: 4px;
        padding: 2px 8px; border-radius: 999px;
        font-size: 10px; font-weight: 700; line-height: 1.5; letter-spacing: .01em;
        background: #EEF1F0; color: #3F4744;
    }
    .ab-fuel::before { content: ""; width: 6px; height: 6px; border-radius: 50%; background: currentColor; opacity: .85; }
    .ab-fuel--cng      { background: #E3F4EA; color: #12743F; }
    .ab-fuel--electric { background: #E4EEFD; color: #1A5FC4; }
    .ab-fuel--petrol   { background: #FDEBDD; color: #B8540B; }
    .ab-fuel--diesel   { background: #E9ECEF; color: #39424A; }
    .ab-fuel--lpg      { background: #F1E8FB; color: #6B2FB3; }
@endif

@if (!empty($design['amber_accents']))
    /* ---------- More yellow ---------- */
    .ab-h::after {
        content: ""; display: block; width: 46px; height: 4px; margin-top: 8px;
        border-radius: 4px; background: linear-gradient(90deg, #FFC107, #FFD54F);
    }
    .ab-price { padding: 0 3px; border-radius: 3px; background: linear-gradient(transparent 58%, #FFE082 58%); }
    [data-ab-count] { color: #FFC107; }
    @media (hover: hover) {
        a.ab-card.ab-lift { position: relative; }
        a.ab-card.ab-lift::after {
            content: ""; position: absolute; left: 14px; right: 14px; top: 0; height: 3px;
            border-radius: 0 0 3px 3px; background: #FFC107;
            transform: scaleX(0); transition: transform .25s ease;
        }
        a.ab-card.ab-lift:hover::after { transform: scaleX(1); }
    }
    @media (prefers-reduced-motion: reduce) { a.ab-card.ab-lift::after { transition: none; } }
@endif

@if (!empty($design['short_menu']))
    /* ---------- "More" dropdown in the desktop menu ---------- */
    .ab-more { position: relative; display: flex; }
    .ab-more-btn {
        display: flex; align-items: center; gap: 6px; height: 100%;
        padding: 12px 12px; font-size: 13px; font-weight: 600; white-space: nowrap;
        color: rgba(255, 255, 255, .9); background: transparent; border: 0; cursor: pointer;
        transition: background-color .15s ease, color .15s ease;
    }
    @media (min-width: 1280px) { .ab-more-btn { padding-left: 16px; padding-right: 16px; } }
    .ab-more:hover .ab-more-btn, .ab-more:focus-within .ab-more-btn { background: #0A5334; color: #fff; }
    .ab-more-btn.is-active { background: #FFC107; color: #1A1D1B; }
    .ab-more-btn svg { transition: transform .2s ease; }
    .ab-more:hover .ab-more-btn svg, .ab-more:focus-within .ab-more-btn svg { transform: rotate(180deg); }
    .ab-more-menu {
        position: absolute; top: 100%; left: 0; z-index: 60; min-width: 220px;
        margin: 0; padding: 6px; list-style: none;
        background: #fff; border: 1px solid #E2E7E4; border-top: 3px solid #FFC107; border-radius: 0 0 12px 12px;
        box-shadow: 0 18px 36px -14px rgba(6, 55, 34, .35);
        opacity: 0; visibility: hidden; transform: translateY(6px);
        transition: opacity .18s ease, transform .18s ease, visibility .18s;
    }
    .ab-more:hover .ab-more-menu, .ab-more:focus-within .ab-more-menu { opacity: 1; visibility: visible; transform: translateY(0); }
    .ab-more-menu a {
        display: block; padding: 9px 12px; border-radius: 8px;
        font-size: 13px; font-weight: 600; color: #1A1D1B; text-decoration: none;
        transition: background-color .15s ease, color .15s ease;
    }
    .ab-more-menu a:hover, .ab-more-menu a:focus-visible { background: #E8F3EE; color: #0B5D3B; }
    .ab-more-menu a[aria-current="page"] { background: #FFF8E1; color: #0B5D3B; }
    @media (prefers-reduced-motion: reduce) { .ab-more-menu, .ab-more-btn svg { transition: none; } }
@endif

</style>

@if (!empty($design['count_up']))
<script>
    /* Trust-bar numbers count up once, when they first scroll into view. */
    document.addEventListener('DOMContentLoaded', function () {
        var nodes = document.querySelectorAll('[data-ab-count]');
        if (!nodes.length) { return; }
        if (window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches) { return; }

        function run(node) {
            var original = node.textContent.trim();
            // "10,000+" -> number 10000, suffix "+";  "4.5 ★" -> 4.5, " ★";  "Trusted by" -> skipped
            var match = original.match(/^([\d,]+(?:\.\d+)?)(.*)$/);
            if (!match) { return; }

            var target = parseFloat(match[1].replace(/,/g, ''));
            var decimals = (match[1].split('.')[1] || '').length;
            var suffix = match[2];
            if (!isFinite(target) || target <= 0) { return; }

            var duration = 1300, start = null;
            function frame(now) {
                if (start === null) { start = now; }
                var progress = Math.min((now - start) / duration, 1);
                var eased = 1 - Math.pow(1 - progress, 3);
                var value = target * eased;
                node.textContent = (decimals ? value.toFixed(decimals) : Math.round(value).toLocaleString('en-IN')) + suffix;
                if (progress < 1) { requestAnimationFrame(frame); } else { node.textContent = original; }
            }
            node.style.fontVariantNumeric = 'tabular-nums';
            requestAnimationFrame(frame);
        }

        if (!('IntersectionObserver' in window)) { return; }   // old browser: leave the numbers as they are
        var observer = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (!entry.isIntersecting) { return; }
                observer.unobserve(entry.target);
                run(entry.target);
            });
        }, { threshold: 0.6 });
        nodes.forEach(function (node) { observer.observe(node); });
    });
</script>
@endif
