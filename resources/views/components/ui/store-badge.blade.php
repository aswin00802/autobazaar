@props(['store' => 'google', 'tone' => 'dark'])

@php
    $isLight = $tone === 'light';
@endphp

{{-- App-store badge. Drawn inline rather than shipping the official PNGs,
     which are trademarked assets the client would need to supply. --}}

@php
    $config = [
        'google' => ['top' => 'GET IT ON', 'name' => 'Google Play'],
        'apple' => ['top' => 'Download on the', 'name' => 'App Store'],
    ][$store];
@endphp

<a href="#" {{ $attributes->merge([
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
        <svg viewBox="0 0 24 24" width="22" height="22" fill="currentColor" class="text-white" aria-hidden="true">
            <path d="M16.4 12.7c0-2.2 1.8-3.3 1.9-3.4-1-1.5-2.6-1.7-3.2-1.7-1.4-.1-2.7.8-3.3.8-.7 0-1.7-.8-2.8-.8-1.5 0-2.8.8-3.6 2.1-1.5 2.6-.4 6.5 1.1 8.6.7 1 1.6 2.2 2.7 2.2 1.1 0 1.5-.7 2.8-.7s1.6.7 2.8.7c1.2 0 1.9-1 2.6-2.1.8-1.2 1.2-2.4 1.2-2.5-.1 0-2.2-.9-2.2-3.2ZM14.2 5.8c.6-.7 1-1.7.9-2.8-.9 0-2 .6-2.6 1.3-.6.6-1.1 1.7-.9 2.7 1 .1 2-.5 2.6-1.2Z"/>
        </svg>
    @endif

    <span class="leading-none text-white">
        <span class="block text-[9px] uppercase tracking-wide opacity-80">{{ $config['top'] }}</span>
        <span class="mt-0.5 block text-sm font-semibold">{{ $config['name'] }}</span>
    </span>
</a>
