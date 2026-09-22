{{--
    AutoBazaar page loader.

    1. Full-screen "driving auto" splash (#ab-loader). How often it shows is a setting:
         every_page  = every page load and refresh (shorter after the first page)
         first_visit = only the first page of a visit
         off         = never (the top bar goes too)
    2. Slim top bar with a mini auto riding it (#ab-nav), shown while leaving a page.

    Everything here is inline on purpose: the splash has to paint before the
    CSS/JS bundle arrives, so it cannot depend on Tailwind or Alpine. Colours
    are the brand tokens from resources/css/app.css, written out as hex.

    Opt a link or form out with  data-no-loader.
--}}
@php
    $loaderBrand = $site['brand']['name'] ?? 'AutoBazaar';
    // 'every_page' | 'first_visit' | 'off'  — set in config/site_design.php
    $loaderMode = config('site_design.loader', 'every_page');
    if (! in_array($loaderMode, ['every_page', 'first_visit', 'off'], true)) { $loaderMode = 'every_page'; }
@endphp

@if ($loaderMode !== 'off')

<style>
    #ab-loader {
        position: fixed; inset: 0; z-index: 9999;
        display: grid; place-items: center;
        background: radial-gradient(120% 90% at 50% 35%, #FFFFFF 0%, #F5F7F6 55%, #E8F3EE 100%);
        transition: opacity .38s ease, visibility .38s ease;
    }
    #ab-loader.is-done { opacity: 0; visibility: hidden; pointer-events: none; }
    .ab-splash-stage { width: min(78vw, 300px); text-align: center; }
    .ab-scene { position: relative; height: 132px; overflow: hidden; }

    /* The auto: gentle suspension bounce while "driving". */
    .ab-auto { position: absolute; left: 50%; bottom: 14px; width: 156px; margin-left: -78px; animation: abBounce .42s ease-in-out infinite alternate; }
    .ab-auto svg { display: block; width: 100%; height: auto; overflow: visible; }
    .ab-wheel { animation: abSpin .55s linear infinite; }

    /* Road: a solid line plus dashes streaming backwards. */
    .ab-road { position: absolute; left: 0; right: 0; bottom: 12px; height: 3px; border-radius: 3px; background: #0B5D3B; opacity: .9; }
    .ab-dashes { position: absolute; left: 0; bottom: 3px; width: 200%; height: 3px;
        background: repeating-linear-gradient(90deg, #9BCBB4 0 22px, transparent 22px 48px);
        animation: abRoad .5s linear infinite; }

    /* Speed lines + exhaust puffs behind the auto. */
    .ab-speed { position: absolute; right: 50%; margin-right: 78px; height: 3px; border-radius: 3px; background: #FFC107; opacity: 0; animation: abSpeed .9s ease-out infinite; }
    .ab-speed.s1 { bottom: 74px; width: 34px; }
    .ab-speed.s2 { bottom: 58px; width: 22px; animation-delay: .3s; }
    .ab-speed.s3 { bottom: 42px; width: 28px; animation-delay: .6s; }
    .ab-puff { position: absolute; right: 50%; margin-right: 80px; bottom: 24px; width: 9px; height: 9px; border-radius: 50%; background: #C9E3D6; opacity: 0; animation: abPuff 1.1s ease-out infinite; }
    .ab-puff.p2 { animation-delay: .55s; }

    .ab-name { margin: 14px 0 2px; font: 800 22px/1.1 var(--font-sans, 'Plus Jakarta Sans', ui-sans-serif, system-ui, sans-serif); letter-spacing: .02em; color: #0B5D3B; }
    .ab-name span { color: #1A1D1B; }
    .ab-tip { margin: 0; min-height: 20px; font: 500 13px/1.5 var(--font-sans, 'Plus Jakarta Sans', ui-sans-serif, system-ui, sans-serif); color: #6B7671; transition: opacity .25s ease; }

    @keyframes abSpin   { to { transform: rotate(360deg); } }
    @keyframes abBounce { from { transform: translateY(0); } to { transform: translateY(-2.5px); } }
    @keyframes abRoad   { to { transform: translateX(-48px); } }
    @keyframes abSpeed  { 0% { opacity: 0; transform: translateX(16px); } 25% { opacity: .9; } 100% { opacity: 0; transform: translateX(-34px); } }
    @keyframes abPuff   { 0% { opacity: .85; transform: translate(0, 0) scale(.6); } 100% { opacity: 0; transform: translate(-30px, -16px) scale(1.9); } }

    /* ---- Top navigation bar ---- */
    #ab-nav { position: fixed; top: 0; left: 0; right: 0; z-index: 9998; height: 3px; pointer-events: none; opacity: 0; transition: opacity .2s ease; }
    #ab-nav.is-on { opacity: 1; }
    #ab-nav .ab-nav-bar { position: relative; height: 100%; width: 0; background: linear-gradient(90deg, #0B5D3B, #3D8B68 60%, #FFC107); border-radius: 0 3px 3px 0; box-shadow: 0 0 8px rgba(11, 93, 59, .45); }
    #ab-nav.is-on .ab-nav-bar { width: 88%; transition: width 9s cubic-bezier(.08, .7, .2, 1); }
    #ab-nav.is-finishing .ab-nav-bar { width: 100%; transition: width .2s ease-out; }
    #ab-nav .ab-nav-auto { position: absolute; right: -6px; top: 1px; width: 34px; animation: abBounce .35s ease-in-out infinite alternate; }
    #ab-nav .ab-nav-auto svg { display: block; width: 100%; height: auto; overflow: visible; }

    @media (prefers-reduced-motion: reduce) {
        .ab-auto, .ab-wheel, .ab-dashes, .ab-speed, .ab-puff, #ab-nav .ab-nav-auto { animation: none; }
        .ab-speed, .ab-puff { display: none; }
        #ab-loader { transition-duration: .01s; }
    }
</style>

<div id="ab-loader" role="status" aria-live="polite" aria-label="Loading {{ $loaderBrand }}">
    <div class="ab-splash-stage">
        <div class="ab-scene" aria-hidden="true">
            <span class="ab-speed s1"></span><span class="ab-speed s2"></span><span class="ab-speed s3"></span>
            <span class="ab-puff"></span><span class="ab-puff p2"></span>
            <div class="ab-auto">@include('site.partials.loader-auto')</div>
            <div class="ab-road"></div>
            <div class="ab-dashes"></div>
        </div>
        <p class="ab-name">AUTO <span>BAZAAR</span></p>
        <p class="ab-tip" id="ab-tip">Starting the meter…</p>
    </div>
</div>

<div id="ab-nav" aria-hidden="true">
    <div class="ab-nav-bar"><span class="ab-nav-auto">@include('site.partials.loader-auto')</span></div>
</div>

<noscript><style>#ab-loader, #ab-nav { display: none !important; }</style></noscript>

<script>
    (function () {
        var splash = document.getElementById('ab-loader');
        var nav = document.getElementById('ab-nav');
        var SEEN = 'ab_loader_seen';
        var MODE = @json($loaderMode);   // 'every_page' or 'first_visit' (config/site_design.php → loader)
        var MAX_MS = 4000;               // never trap the visitor

        /* ---------- 1. Splash ---------- */
        var seen = false;
        try { seen = sessionStorage.getItem(SEEN) === '1'; } catch (e) {}

        // The first page of a visit gets the full moment; later pages and refreshes
        // get a shorter one so the site never feels slow.
        var MIN_MS = seen ? 450 : 700;

        if (seen && MODE === 'first_visit') {
            splash.parentNode.removeChild(splash);   // runs before first paint: no flash
            splash = null;
        } else {
            try { sessionStorage.setItem(SEEN, '1'); } catch (e) {}

            var shownAt = Date.now();
            var tips = ['Starting the meter…', 'Comparing on-road prices…', 'Lining up the best autos…'];
            var tipNode = document.getElementById('ab-tip');
            var tipIndex = 0;
            var tipTimer = setInterval(function () {
                tipIndex = (tipIndex + 1) % tips.length;
                tipNode.style.opacity = 0;
                setTimeout(function () { tipNode.textContent = tips[tipIndex]; tipNode.style.opacity = 1; }, 250);
            }, 1300);

            var finished = false;
            var finish = function () {
                if (finished) { return; }
                finished = true;
                var wait = Math.max(0, MIN_MS - (Date.now() - shownAt));
                setTimeout(function () {
                    clearInterval(tipTimer);
                    splash.classList.add('is-done');
                    splash.setAttribute('aria-hidden', 'true');
                    setTimeout(function () { if (splash && splash.parentNode) { splash.parentNode.removeChild(splash); } }, 450);
                }, wait);
            };

            // Ready = page is usable. Deliberately not window.load, which waits for every image.
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', finish);
            } else {
                finish();
            }
            setTimeout(finish, MAX_MS);
        }

        /* ---------- 2. Top bar: every later navigation ---------- */
        var navTimer = null;

        function startNav() {
            nav.classList.remove('is-finishing');
            // restart the width transition from zero
            nav.classList.remove('is-on');
            void nav.offsetWidth;
            nav.classList.add('is-on');
            clearTimeout(navTimer);
            navTimer = setTimeout(stopNav, 12000);   // navigation was cancelled or blocked
        }

        function stopNav() {
            clearTimeout(navTimer);
            if (!nav.classList.contains('is-on')) { return; }
            nav.classList.add('is-finishing');
            setTimeout(function () { nav.classList.remove('is-on', 'is-finishing'); }, 260);
        }

        function leavesPage(link) {
            if (!link || link.hasAttribute('download') || link.hasAttribute('data-no-loader')) { return false; }
            if (link.target && link.target !== '_self') { return false; }
            var href = link.getAttribute('href') || '';
            if (!href || href.charAt(0) === '#' || /^(mailto:|tel:|javascript:|whatsapp:)/i.test(href)) { return false; }
            if (link.origin !== window.location.origin) { return false; }
            // same page, only the #hash differs
            if (link.pathname === window.location.pathname && link.search === window.location.search && link.hash) { return false; }
            return true;
        }

        document.addEventListener('click', function (event) {
            if (event.button !== 0 || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) { return; }
            var link = event.target.closest ? event.target.closest('a[href]') : null;
            if (!leavesPage(link)) { return; }
            // Let Alpine / page scripts cancel first; only then show the bar.
            setTimeout(function () { if (!event.defaultPrevented) { startNav(); } }, 0);
        });

        document.addEventListener('submit', function (event) {
            var form = event.target;
            if (form.hasAttribute('data-no-loader') || (form.target && form.target !== '_self')) { return; }
            setTimeout(function () { if (!event.defaultPrevented) { startNav(); } }, 0);
        });

        // Back/forward cache restores the old DOM with the bar still running: clear it.
        window.addEventListener('pageshow', stopNav);
    })();
</script>
@endif
