@props(['promises' => [], 'script' => 'Happy Drivers Happy Journeys!'])

<section class="border-t border-line bg-surface">
    <div class="ab-container flex flex-wrap items-center gap-x-8 gap-y-5 py-6">
        @foreach ($promises as $promise)
            <div class="flex items-center gap-3">
                <x-ui.icon :name="$promise['icon']" :size="24" class="text-brand-500" />
                <span class="leading-tight">
                    <span class="block text-sm font-bold">{{ $promise['title'] }}</span>
                    <span class="block text-[11px] text-muted">{{ $promise['note'] }}</span>
                </span>
            </div>
        @endforeach

        @if ($script)
            <p class="ab-script ml-auto hidden text-2xl text-brand-500 xl:block">{{ $script }}</p>
        @endif
    </div>
</section>
