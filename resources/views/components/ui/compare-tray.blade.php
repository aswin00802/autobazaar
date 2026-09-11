{{--
    Sticky compare tray.

    Reads $store.compare, so it stays in sync with the "Add to Compare" button
    on every vehicle card, wherever that card appears.
--}}

<div x-data
     x-init="$store.compare.baseUrl = @js(route('site.compare'))"
     x-show="$store.compare.count > 0"
     x-cloak
     x-transition:enter="transition ease-out duration-200"
     x-transition:enter-start="translate-y-full"
     x-transition:enter-end="translate-y-0"
     class="fixed inset-x-0 bottom-0 z-30 border-t border-line bg-surface"
     style="box-shadow: var(--shadow-rail)"
     role="region"
     aria-label="Comparison tray">

    <div class="ab-container flex items-center gap-3 py-3 sm:gap-4">

        <p class="hidden shrink-0 text-sm font-bold sm:block">
            Compare
            <span class="text-muted">
                (<span x-text="$store.compare.count"></span>/<span x-text="$store.compare.max"></span>)
            </span>
        </p>

        {{-- Selected models --}}
        <ul class="flex flex-1 gap-2 overflow-x-auto">
            <template x-for="item in $store.compare.items" :key="item.slug">
                <li class="flex shrink-0 items-center gap-2 rounded-lg border border-line bg-canvas py-1.5 pl-2.5 pr-1">
                    <span class="max-w-32 truncate text-xs font-semibold sm:max-w-40" x-text="item.name"></span>
                    <button type="button" @click="$store.compare.remove(item.slug)"
                            class="rounded p-1 text-muted transition-colors hover:bg-line hover:text-danger"
                            :aria-label="`Remove ${item.name} from comparison`">
                        <x-ui.icon name="close" :size="14" />
                    </button>
                </li>
            </template>

            {{-- Empty slots, so the "up to 4" limit is visible --}}
            <template x-for="n in $store.compare.emptySlots" :key="'slot-' + n">
                <li class="hidden shrink-0 items-center rounded-lg border border-dashed border-line px-3 py-1.5
                           text-xs text-muted sm:flex">
                    Add model
                </li>
            </template>
        </ul>

        <div class="flex shrink-0 items-center gap-2">
            <button type="button" @click="$store.compare.clear()"
                    class="hidden text-xs font-semibold text-muted underline-offset-2 hover:underline sm:block">
                Clear
            </button>

            {{-- Disabled until there are at least two models to compare --}}
            <a :href="$store.compare.compareUrl ?? '#'"
               :class="$store.compare.count < 2 ? 'pointer-events-none opacity-40' : ''"
               :aria-disabled="$store.compare.count < 2"
               class="ab-btn ab-btn-primary px-3 text-xs sm:px-4 sm:text-sm">
                Compare
                <x-ui.icon name="arrow-right" :size="16" />
            </a>
        </div>
    </div>
</div>
