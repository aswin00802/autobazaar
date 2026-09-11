@props(['site'])

@php
    // The reference footer is a single slim light row, not a tall dark
    // multi-column block: logo, one line of policy links, socials, store
    // badge, copyright. Keeping it light is also what lets the trust bar
    // above it stay dark green without the two merging.
    $links = [
        ['label' => 'About Us',           'route' => 'site.about'],
        ['label' => 'Buying Policy',      'route' => 'site.page', 'param' => 'buying-policy'],
        ['label' => 'Privacy Policy',     'route' => 'site.page', 'param' => 'privacy-policy'],
        ['label' => 'Terms & Conditions', 'route' => 'site.page', 'param' => 'terms-conditions'],
        ['label' => 'Refund Policy',      'route' => 'site.page', 'param' => 'refund-policy'],
        ['label' => 'Contact',            'route' => 'site.contact'],
    ];

    $href = fn (array $i) => isset($i['param'])
        ? route($i['route'], $i['param'])
        : route($i['route']);
@endphp

<footer class="border-t border-line bg-surface">
    <div class="ab-container flex flex-wrap items-center justify-between gap-x-8 gap-y-5 py-5">

        <a href="{{ route('site.home') }}" class="shrink-0">
            <x-ui.logo :site="$site" size="sm" />
        </a>

        <nav aria-label="Footer">
            <ul class="flex flex-wrap items-center gap-x-6 gap-y-2">
                @foreach ($links as $item)
                    <li>
                        <a href="{{ $href($item) }}"
                           class="text-xs font-semibold text-ink-soft transition-colors hover:text-brand-500">
                            {{ $item['label'] }}
                        </a>
                    </li>
                @endforeach
            </ul>
        </nav>

        <div class="flex items-center gap-4">
            <ul class="flex items-center gap-2">
                @foreach ($site['socials'] as $social)
                    <li>
                        <a href="{{ $social['url'] }}" target="_blank" rel="noopener"
                           aria-label="{{ $social['label'] }}"
                           class="grid h-8 w-8 place-items-center rounded-lg bg-canvas text-ink-soft
                                  transition-colors hover:bg-brand-50 hover:text-brand-500">
                            <x-ui.icon :name="$social['icon']" :size="17" />
                        </a>
                    </li>
                @endforeach
            </ul>

            <x-ui.store-badge store="google" tone="light" />
        </div>

        <p class="text-[11px] text-muted">{{ $site['brand']['copyright'] }}</p>
    </div>
</footer>
