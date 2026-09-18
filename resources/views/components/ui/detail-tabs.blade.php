@props(['tabs'])

{{--
    Sticky section tabs. $tabs: [['id' => 'overview', 'label' => 'Overview', 'icon' => 'grid'], …]
    Scrolls to the section; the active state follows the scroll position.
    Sits directly under the sticky site header — the offset is measured in JS.
--}}

<nav x-data="detailTabs({ tabs: {{ Js::from($tabs) }} })"
     :style="`top:${top}px`"
     {{ $attributes->merge(['class' => 'sticky z-30 -mx-4 border-y border-line bg-surface/95 backdrop-blur sm:-mx-6 lg:mx-0 lg:rounded-xl lg:border']) }}
     aria-label="Page sections">
    <ul class="ab-tabs-scroll flex overflow-x-auto px-2 lg:justify-between lg:px-2" role="list">
        @foreach ($tabs as $tab)
            <li class="shrink-0 lg:flex-1">
                <a href="#{{ $tab['id'] }}" @click.prevent="go(@js($tab['id']))"
                   :aria-current="isActive(@js($tab['id'])) ? 'location' : null"
                   :class="isActive(@js($tab['id']))
                        ? 'border-brand-500 text-brand-600'
                        : 'border-transparent text-muted hover:text-ink'"
                   class="flex flex-col items-center gap-1 whitespace-nowrap border-b-2 px-3 py-2.5 text-[11px] font-semibold transition-colors sm:flex-row sm:gap-1.5 sm:text-xs lg:justify-center lg:text-[13px]">
                    <x-ui.icon :name="$tab['icon']" :size="17" />
                    {{ $tab['label'] }}
                </a>
            </li>
        @endforeach
    </ul>
</nav>
