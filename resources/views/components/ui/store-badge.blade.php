@props(['store' => 'google', 'tone' => 'dark'])

@php
    $isLight = $tone === 'light';
@endphp

{{-- App-store badge. Drawn inline rather than shipping the official PNGs,
     which are trademarked assets the client would need to supply.

     Links come from config/site_links.php:
       google -> play_url, opens the Play Store listing in a new tab
       apple  -> ios_url; while that is null there is no iPhone app yet, so the
                 badge plays a "Coming soon" animation instead of opening a dead link --}}

@php
    $config = [
        'google' => ['top' => 'GET IT ON', 'name' => 'Google Play'],
        'apple' => ['top' => 'Download on the', 'name' => 'App Store'],
    ][$store];

    $url = $store === 'google' ? config('site_links.play_url') : config('site_links.ios_url');
    $comingSoon = empty($url);
    $fullWidth = str_contains((string) $attributes->get('class'), 'w-full');
@endphp

@once
<style>
    .ab-soon-wrap { position: relative; display: inline-block; vertical-align: middle; }
    .ab-soon-wrap.is-full { display: block; width: 100%; }
    .ab-soon-tag {
        position: absolute; top: -7px; right: -6px; z-index: 2;
        padding: 1px 6px; border-radius: 999px;
        font-size: 8px; font-weight: 700; line-height: 1.5; letter-spacing: .06em; text-transform: uppercase;
        background: #FFC107; color: #1A1D1B; pointer-events: none;
    }
    .ab-soon-bubble {
        position: absolute; left: 50%; bottom: calc(100% + 12px); z-index: 70;
        display: flex; align-items: center; gap: 8px; white-space: nowrap;
        padding: 9px 14px; border-radius: 12px;
        font-size: 12px; font-weight: 700; color: #1A1D1B;
        background: linear-gradient(135deg, #FFF8E1, #FFE082);
        border: 1px solid #FFD54F;
        box-shadow: 0 14px 30px -10px rgba(0, 0, 0, .45);
        opacity: 0; visibility: hidden; transform: translate(var(--ab-soon-x, -50%), 10px) scale(.85);
        pointer-events: none;
    }
    .ab-soon-bubble::after {
        content: ""; position: absolute; left: 50%; top: 100%; margin-left: -6px;
        border: 6px solid transparent; border-top-color: #FFE082;
    }
    /* Inline badges often sit in a corner (footer): hang the bubble off the badge's right edge
       so it can never run off screen. Full-width badges keep it centred. */
    .ab-soon-wrap:not(.is-full) .ab-soon-bubble { left: auto; right: 0; --ab-soon-x: 0px; }
    .ab-soon-wrap:not(.is-full) .ab-soon-bubble::after { left: auto; right: 26px; margin-left: 0; }
    .ab-soon-bubble .ab-soon-dot { width: 7px; height: 7px; border-radius: 50%; background: #0B5D3B; }
    .ab-soon-wrap.is-showing .ab-soon-bubble { visibility: visible; animation: abSoonPop 2.6s cubic-bezier(.2, 1.4, .4, 1) forwards; }
    .ab-soon-wrap.is-showing .ab-soon-dot { animation: abSoonBlink .7s ease-in-out infinite alternate; }
    .ab-soon-wrap.is-showing .ab-soon-icon { animation: abSoonWiggle .6s ease-in-out 2; transform-origin: 50% 80%; }
    .ab-soon-wrap.is-showing .ab-soon-tag { animation: abSoonTag .5s ease-out 3; }

    @keyframes abSoonPop {
        0%   { opacity: 0; transform: translate(var(--ab-soon-x, -50%), 10px) scale(.85); }
        12%  { opacity: 1; transform: translate(var(--ab-soon-x, -50%), -4px) scale(1.05); }
        20%  { transform: translate(var(--ab-soon-x, -50%), 0) scale(1); }
        82%  { opacity: 1; transform: translate(var(--ab-soon-x, -50%), 0) scale(1); }
        100% { opacity: 0; transform: translate(var(--ab-soon-x, -50%), -8px) scale(.96); }
    }
    @keyframes abSoonWiggle { 0%, 100% { transform: rotate(0); } 25% { transform: rotate(-14deg); } 75% { transform: rotate(14deg); } }
    @keyframes abSoonBlink { from { opacity: .35; transform: scale(.8); } to { opacity: 1; transform: scale(1.15); } }
    @keyframes abSoonTag { 0%, 100% { transform: scale(1); } 50% { transform: scale(1.18); } }

    @media (prefers-reduced-motion: reduce) {
        .ab-soon-wrap.is-showing .ab-soon-bubble { animation: none; opacity: 1; transform: translate(var(--ab-soon-x, -50%), 0); }
        .ab-soon-wrap.is-showing .ab-soon-icon, .ab-soon-wrap.is-showing .ab-soon-dot, .ab-soon-wrap.is-showing .ab-soon-tag { animation: none; }
    }
</style>
@endonce

@if ($comingSoon)
<span class="ab-soon-wrap {{ $fullWidth ? 'is-full' : '' }}" data-ab-soon>
    <span class="ab-soon-tag">Soon</span>
    <span class="ab-soon-bubble" role="status" aria-live="polite">
        <span class="ab-soon-dot"></span>
        {{ $config['name'] }} app is coming soon
    </span>
@endif

<a @if ($comingSoon) href="#" role="button" aria-label="{{ $config['name'] }} app: coming soon"
   @else href="{{ $url }}" target="_blank" rel="noopener" aria-label="Get the AutoBazaar app on {{ $config['name'] }}"
   @endif
   {{ $attributes->merge([
        'class' => 'inline-flex items-center gap-2.5 rounded-lg px-3 py-2 transition-colors '
                   . ($isLight
                       ? 'border border-line bg-ink hover:bg-ink-soft'
                       : 'border border-white/25 bg-black/40 hover:bg-black/60'),
   ]) }}>
    @if ($store === 'google')
        <svg viewBox="0 0 24 24" width="22" height="22" aria-hidden="true">
            <path fill="#00D2FF" d="M3.6 2.3a1.6 1.6 0 0 0-.6 1.3v16.8c0 .5.2 1 .6 1.3l9-9.7-9-9.7Z"/>
            <path fill="#FFCE00" d="m17.3 8.6-3.4-2L12.6 12l1.3 5.4 3.4-2c1.3-.7 1.3-2.7 0-3.4v-3.4Z"/>
            <path fill="#00F076" d="m3.6 2.3 9 9.7 1.3-5.4-8.6-5c-.6-.3-1.3-.2-1.7.7Z"/>
            <path fill="#FF3A44" d="m3.6 21.7 9-9.7 1.3 5.4-8.6 5c-.6.3-1.3.2-1.7-.7Z"/>
        </svg>
    @else
        <svg viewBox="0 0 24 24" width="22" height="22" fill="currentColor" class="ab-soon-icon text-white" aria-hidden="true">
            <path d="M16.4 12.7c0-2.2 1.8-3.3 1.9-3.4-1-1.5-2.6-1.7-3.2-1.7-1.4-.1-2.7.8-3.3.8-.7 0-1.7-.8-2.8-.8-1.5 0-2.8.8-3.6 2.1-1.5 2.6-.4 6.5 1.1 8.6.7 1 1.6 2.2 2.7 2.2 1.1 0 1.5-.7 2.8-.7s1.6.7 2.8.7c1.2 0 1.9-1 2.6-2.1.8-1.2 1.2-2.4 1.2-2.5-.1 0-2.2-.9-2.2-3.2ZM14.2 5.8c.6-.7 1-1.7.9-2.8-.9 0-2 .6-2.6 1.3-.6.6-1.1 1.7-.9 2.7 1 .1 2-.5 2.6-1.2Z"/>
        </svg>
    @endif

    <span class="leading-none text-white">
        <span class="block text-[9px] uppercase tracking-wide opacity-80">{{ $config['top'] }}</span>
        <span class="mt-0.5 block text-sm font-semibold">{{ $config['name'] }}</span>
    </span>
</a>

@if ($comingSoon)
</span>
@endif

@once
@push('scripts')
<script>
    /* "Coming soon" store badges: play the animation instead of following the dead link. */
    document.addEventListener('click', function (event) {
        var wrap = event.target.closest ? event.target.closest('[data-ab-soon]') : null;
        if (!wrap) { return; }
        event.preventDefault();
        wrap.classList.remove('is-showing');
        void wrap.offsetWidth;                 // restart the animation on repeat clicks
        wrap.classList.add('is-showing');
        clearTimeout(wrap.__abSoonTimer);
        wrap.__abSoonTimer = setTimeout(function () { wrap.classList.remove('is-showing'); }, 2700);
    });
</script>
@endpush
@endonce
