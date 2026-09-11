@props(['vehicle'])

@php
    // Placeholder reviews — the real system needs moderation and a
    // verified-buyer check (see the plan's open question on reviews).
    $reviews = [
        ['name' => 'Murugan S', 'city' => 'Tiruvallur', 'rating' => 5, 'date' => '28 Aug 2026', 'verified' => true,
         'title' => 'Excellent mileage, low running cost',
         'body' => 'Running it for eight months now on CNG. The mileage is exactly as claimed and servicing has been cheap. Very happy with the purchase.'],
        ['name' => 'Selvam K', 'city' => 'Kanchipuram', 'rating' => 4, 'date' => '14 Aug 2026', 'verified' => true,
         'title' => 'Good for city, average on highway',
         'body' => 'Perfect for daily city routes. On longer runs the pickup drops with a full load, but for my usage it is more than enough.'],
        ['name' => 'Prakash R', 'city' => 'Chengalpattu', 'rating' => 5, 'date' => '02 Aug 2026', 'verified' => false,
         'title' => 'Comfortable and reliable',
         'body' => 'Seating is comfortable for passengers and I have had no breakdowns so far. Service centre nearby which makes it easy.'],
    ];

    // Distribution shaped to land on the model's headline rating.
    $distribution = [5 => 68, 4 => 24, 3 => 5, 2 => 2, 1 => 1];
@endphp

<div {{ $attributes->merge(['class' => 'grid gap-5 lg:grid-cols-3']) }}>

    {{-- Summary --}}
    <div class="ab-card h-fit p-5">
        <p class="text-4xl font-extrabold">{{ number_format($vehicle['rating'], 1) }}</p>
        <x-ui.rating :rating="$vehicle['rating']" :show-value="false" :size="16" class="mt-1" />
        <p class="mt-1 text-xs text-muted">Based on {{ $vehicle['reviews'] }} reviews</p>

        <ul class="mt-4 space-y-1.5">
            @foreach ($distribution as $stars => $percent)
                <li class="flex items-center gap-2">
                    <span class="w-8 text-[11px] text-muted">{{ $stars }} ★</span>
                    <span class="h-1.5 flex-1 overflow-hidden rounded-full bg-line">
                        <span class="block h-full rounded-full bg-accent-500" style="width: {{ $percent }}%"></span>
                    </span>
                    <span class="w-8 text-right text-[11px] text-muted">{{ $percent }}%</span>
                </li>
            @endforeach
        </ul>

        <button type="button" class="ab-btn ab-btn-primary mt-5 w-full text-xs">Write a Review</button>
    </div>

    {{-- Review list --}}
    <ul class="space-y-3 lg:col-span-2">
        @foreach ($reviews as $review)
            <li class="ab-card p-4">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="grid h-8 w-8 place-items-center rounded-full bg-brand-50 text-xs font-bold text-brand-600">
                        {{ substr($review['name'], 0, 1) }}
                    </span>
                    <span class="text-sm font-bold">{{ $review['name'] }}</span>
                    <span class="text-xs text-muted">· {{ $review['city'] }}</span>

                    @if ($review['verified'])
                        <span class="flex items-center gap-1 rounded-full bg-brand-50 px-2 py-0.5 text-[10px] font-bold text-brand-600">
                            <x-ui.icon name="check" :size="11" /> Verified Buyer
                        </span>
                    @endif

                    <span class="ml-auto text-[11px] text-muted">{{ $review['date'] }}</span>
                </div>

                <x-ui.rating :rating="$review['rating']" :show-value="false" :size="13" class="mt-2.5" />

                <p class="mt-1.5 text-sm font-bold">{{ $review['title'] }}</p>
                <p class="mt-1 text-sm leading-relaxed text-ink-soft">{{ $review['body'] }}</p>
            </li>
        @endforeach
    </ul>
</div>
