@props(['vehicle', 'compact' => false])

@php
    $url = route('site.model', [$vehicle['brand_slug'], $vehicle['model_slug']]);
    $fuels = collect($vehicle['variants'])->pluck('label')->implode(' | ');
@endphp

<article {{ $attributes->merge(['class' => 'ab-card group flex flex-col overflow-hidden ab-lift']) }}>

    {{-- Brand row --}}
    <div class="flex items-center justify-between px-4 pt-4">
        <img src="{{ asset($vehicle['brand_logo']) }}" alt="{{ $vehicle['brand'] }}"
             class="h-5 w-auto max-w-24 object-contain object-left" loading="lazy">

        @if ($vehicle['badge'])
            <span class="rounded-full bg-danger px-2 py-0.5 text-[10px] font-bold uppercase tracking-wide text-white">
                {{ $vehicle['badge'] }}
            </span>
        @endif
    </div>

    {{-- Image --}}
    <a href="{{ $url }}" class="ab-photo block px-4 py-3">
        <img src="{{ asset($vehicle['image']) }}" alt="{{ $vehicle['name'] }}"
             class="mx-auto h-28 w-auto object-contain transition-transform duration-300 group-hover:scale-105"
             loading="lazy">
    </a>

    {{-- Body --}}
    <div class="flex flex-1 flex-col px-4 pb-4">
        <h3 class="text-[15px] font-bold leading-tight">
            <a href="{{ $url }}" class="transition-colors hover:text-brand-500">{{ $vehicle['name'] }}</a>
        </h3>

        @if (config('site_design.fuel_chips'))
            <ul class="ab-fuels" aria-label="Fuel options">
                @foreach (collect($vehicle['variants'])->pluck('label')->unique() as $fuel)
                    <li class="ab-fuel ab-fuel--{{ \Illuminate\Support\Str::slug($fuel) }}">{{ $fuel }}</li>
                @endforeach
            </ul>
        @else
            <p class="mt-1 line-clamp-1 text-[11px] text-muted">{{ $fuels }}</p>
        @endif

        <x-ui.rating :rating="$vehicle['rating']" :reviews="$vehicle['reviews']" :size="13" compact class="mt-2" />

        <p class="mt-2 text-sm">
            <span class="text-muted">From</span>
            <span class="ab-price font-extrabold">₹{{ number_format($vehicle['from_price'] / 100000, 2) }} Lakh*</span>
        </p>

        @unless ($compact)
            <div class="mt-auto grid grid-cols-2 gap-1.5 pt-3">
                <a href="{{ $url }}"
                   class="ab-btn ab-btn-primary px-1 py-2 text-[11px] whitespace-nowrap">View Details</a>
                <a href="{{ route('site.enquiry', $vehicle['slug']) }}"
                   class="ab-btn ab-btn-outline px-1 py-2 text-[11px] whitespace-nowrap">Enquire Now</a>
            </div>

            {{-- Add to comparison — shared state, so the tray updates too --}}
            <div x-data class="pt-2">
                <button type="button"
                        @click="$store.compare.toggle({ slug: @js($vehicle['slug']), name: @js($vehicle['name']) })"
                        :class="$store.compare.has(@js($vehicle['slug'])) ? 'text-brand-500' : 'text-muted hover:text-brand-500'"
                        :aria-pressed="$store.compare.has(@js($vehicle['slug']))"
                        class="flex w-full items-center justify-center gap-1.5 text-[11px] font-semibold transition-colors">
                    <x-ui.icon name="scales" :size="14" />
                    <span x-text="$store.compare.has(@js($vehicle['slug'])) ? 'Added to Compare' : 'Add to Compare'">Add to Compare</span>
                </button>
            </div>
        @endunless
    </div>
</article>
