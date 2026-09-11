@props(['site', 'tone' => 'dark', 'size' => 'md', 'lockup' => true])

{{--
    Brand lockup: the client's badge + a typeset wordmark beside it.

    The supplied logo (WhatsApp/autobazaar logo.png) is a SQUARE badge with
    "AUTO BAZAAR" and the tagline drawn inside it. At header size (~40px) that
    internal text is far too small to read, so the badge is used as the mark
    and the name is set in type next to it — which is also what the reference
    screens do.

    The source PNG had no transparency (a white square behind the circle);
    it has been cut to a transparent circle and optimised to 44 KB. It is
    still the BLUE/ORANGE identity — swap this one file when the green/yellow
    version arrives and every placement updates.

    Pass :lockup="false" for a mark-only badge (favicon-style placements).
--}}

@php
    $scale = [
        'sm' => ['mark' => 'h-8 w-8',   'name' => 'text-base',        'tag' => 'text-[8px]'],
        'md' => ['mark' => 'h-11 w-11', 'name' => 'text-xl sm:text-2xl', 'tag' => 'text-[10px]'],
        'lg' => ['mark' => 'h-20 w-20', 'name' => 'text-3xl',         'tag' => 'text-xs'],
    ][$size];

    $nameColor = $tone === 'light' ? 'text-white' : 'text-brand-500';
    $altColor  = $tone === 'light' ? 'text-accent-500' : 'text-ink';
    $tagColor  = $tone === 'light' ? 'text-white/70' : 'text-muted';
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center gap-2.5']) }}>
    <img src="{{ asset('assets/site/autobazaar-logo.png') }}"
         alt="{{ $lockup ? '' : $site['brand']['name'] }}"
         @if($lockup) aria-hidden="true" @endif
         width="512" height="512"
         class="{{ $scale['mark'] }} shrink-0 object-contain">

    @if ($lockup)
        <span class="leading-none">
            <span class="{{ $scale['name'] }} {{ $nameColor }} block font-extrabold tracking-tight">
                Auto<span class="{{ $altColor }}">Bazaar</span>
            </span>
            <span class="{{ $scale['tag'] }} {{ $tagColor }} mt-1 block font-semibold uppercase tracking-[0.14em]">
                {{ $site['brand']['tagline'] }}
            </span>
        </span>
    @endif
</span>
