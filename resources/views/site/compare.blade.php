@extends('site.layout')

@php
    $compareNames = collect($selected)->pluck('name')->all();
    $compareReady = count($selected) >= 2;          // a comparison needs at least two autos
@endphp
@section('title', $compareReady ? implode(' vs ', $compareNames) . ' — Compare' : 'Compare Auto Rickshaws')
@section('description', $compareReady
    ? 'Compare ' . implode(' vs ', $compareNames) . ': price, EMI, mileage, specifications, running cost and which one suits you best.'
    : 'Pick 2 to 4 autorickshaw models and compare specifications, price, mileage, features and running cost side by side.')

@section('content')

@php
    // Spec rows rendered down the compare table.
    $rows = [
        ['key' => 'engine', 'label' => 'Engine / Motor'],
        ['key' => 'power', 'label' => 'Power'],
        ['key' => 'mileage', 'label' => 'Mileage / Range'],
        ['key' => 'seating', 'label' => 'Seating Capacity'],
        ['key' => 'fuel', 'label' => 'Fuel Type'],
        ['key' => 'ex_showroom_label', 'label' => 'Ex-Showroom Price', 'strong' => true],
        ['key' => 'emi', 'label' => 'EMI (Approx.)', 'money' => true],
        ['key' => 'maintenance', 'label' => 'Maintenance Cost'],
        ['key' => 'best_for', 'label' => 'Best For'],
    ];

    $maxEmi = collect($selected)->max(fn ($v) => $v['compare']['emi']) ?: 1;
    $maxFuel = collect($selected)->max(fn ($v) => $v['compare']['monthly_fuel']) ?: 1;

    // Saving = the gap between the thirstiest and the cheapest to run.
    $cheapest = collect($selected)->sortBy(fn ($v) => $v['compare']['monthly_fuel'])->first();
    $dearest = collect($selected)->sortByDesc(fn ($v) => $v['compare']['monthly_fuel'])->first();
    $saving = $dearest && $cheapest ? $dearest['compare']['monthly_fuel'] - $cheapest['compare']['monthly_fuel'] : 0;

    // "Expert Recommendation" = highest overall AutoBazaar score.
    $pick = collect($selected)->sortByDesc(fn ($v) => $v['scores']['overall'])->first();
@endphp

{{-- ==================================================================== hero --}}
<section class="relative overflow-hidden bg-gradient-to-br from-brand-50 to-canvas">
    <div class="ab-container py-8 lg:py-10">
        <div class="grid items-center gap-6 lg:grid-cols-12">
            <div class="min-w-0 lg:col-span-7">
                <h1 class="text-3xl font-extrabold tracking-tight sm:text-4xl">Compare Auto Rickshaws</h1>
                <p class="mt-2 max-w-xl text-sm text-muted">
                    Find the right auto for your needs. Compare specs, price, mileage, features and more.
                </p>
            </div>

            <div class="hidden items-center justify-end gap-6 lg:col-span-5 lg:flex">
                <p class="ab-script text-2xl leading-tight text-brand-500">
                    Better Choice<br>Brighter Tomorrow
                </p>
                <ul class="space-y-2">
                    @foreach ([['scales', 'Compare'], ['check-circle', 'Choose'], ['auto', 'Drive Better']] as [$icon, $label])
                        <li class="flex items-center gap-2 text-sm font-semibold">
                            <x-ui.tone-icon :icon="$icon" tone="brand" :size="16" shape="circle" />
                            {{ $label }}
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</section>

@php
    // Selection drives the URL, and the server re-renders the table from it.
    // Doing this client-side would mean duplicating every spec row in JS.
    $selectedSlugs = collect($selected)->pluck('slug')->all();

    $urlFor = function (array $slugs) {
        $slugs = array_values(array_unique($slugs));
        // Every selection keeps its own URL, including a single auto. The old rule sent
        // "fewer than two" to the plain page, which used to reload a default four.
        return count($slugs) >= 1
            ? route('site.compare.combo', implode('-vs-', $slugs))
            : route('site.compare');
    };

    // The rail filters the pool of models you can add to the comparison.
    $filterItems = collect($all)->map(fn ($v) => [
        'id' => $v['slug'],
        'brand' => $v['brand'],
        'fuels' => collect($v['variants'])->pluck('label')->values(),
        'seating' => $v['seating'],
        'use_case' => $v['use_case'],
        'price' => $v['from_price'],
        'rating' => $v['rating'],
        'popularity' => $v['popularity'],
    ])->values();
@endphp

<section class="ab-container py-8"
         x-data="filterRail({
            items: {{ Js::from($filterItems) }},
            groups: ['brand', 'fuels', 'seating', 'use_case'],
            minPrice: 100000,
            maxPrice: 500000,
         })">
    <div class="grid gap-6 lg:grid-cols-12">

        {{-- =========================================================== filters --}}
        <aside class="min-w-0 lg:col-span-2">
            <x-ui.vehicle-filters :vehicles="$all" />
        </aside>

        {{-- ===================================================== compare table --}}
        <div class="min-w-0 lg:col-span-7">
            @if (! $compareReady)
                @php
                    // Ready-made pairs so nobody faces a blank page, and Google gets a path to each comparison.
                    $popularPairs = collect([[0, 1], [0, 2], [1, 2], [0, 5], [3, 4], [2, 5]])
                        ->filter(fn ($p) => isset($all[$p[0]], $all[$p[1]]))
                        ->map(fn ($p) => ['label' => $all[$p[0]]['name'] . ' vs ' . $all[$p[1]]['name'], 'url' => $urlFor([$all[$p[0]]['slug'], $all[$p[1]]['slug']])])
                        ->values();
                @endphp
                <style>
                    .ab-cmp-start { padding: 34px 24px; text-align: center; }
                    .ab-cmp-badge { display: inline-grid; place-items: center; width: 64px; height: 64px; border-radius: 50%; background: #E8F3EE; color: #0B5D3B; }
                    .ab-cmp-start h2 { margin: 14px 0 6px; font-size: 20px; font-weight: 800; letter-spacing: -.01em; }
                    .ab-cmp-start p { margin: 0 auto; max-width: 460px; font-size: 14px; line-height: 1.6; color: #6B7671; }
                    .ab-cmp-steps { display: flex; flex-wrap: wrap; justify-content: center; gap: 8px 22px; margin: 18px 0 0; padding: 0; list-style: none; font-size: 13px; font-weight: 600; color: #3F4744; }
                    .ab-cmp-steps b { display: inline-grid; place-items: center; width: 20px; height: 20px; margin-right: 6px; border-radius: 50%; background: #FFC107; color: #1A1D1B; font-size: 11px; }
                    .ab-cmp-start .ab-cmp-picked { max-width: none; color: #1A1D1B; display: inline-flex; align-items: center; gap: 8px; margin: 20px auto 0; padding: 8px 14px; border-radius: 999px; background: #FFF8E1; border: 1px solid #FFD54F; font-size: 13px; font-weight: 700; }
                    .ab-cmp-pairs { display: flex; flex-wrap: wrap; justify-content: center; gap: 8px; margin: 12px 0 0; padding: 0; list-style: none; }
                    .ab-cmp-pairs a { display: inline-block; padding: 7px 13px; border-radius: 999px; border: 1px solid #C9E3D6; background: #fff; font-size: 12px; font-weight: 600; color: #0B5D3B; text-decoration: none; transition: background-color .15s ease, border-color .15s ease; }
                    .ab-cmp-pairs a:hover { background: #E8F3EE; border-color: #6BB08F; }
                    .ab-cmp-start .ab-cmp-or { max-width: none; margin: 26px auto 0; font-size: 11px; font-weight: 800; letter-spacing: .08em; text-transform: uppercase; color: #6B7671; }
                </style>
                <div class="ab-card ab-cmp-start" data-reveal>
                    <span class="ab-cmp-badge"><x-ui.icon name="scales" :size="30" /></span>
                    <h2>Pick 2 to 4 autos to compare</h2>
                    <p>Choose from the list below. As soon as you pick two, price, EMI, mileage, specifications and running cost line up side by side.</p>

                    <ol class="ab-cmp-steps">
                        <li><b>1</b>Pick your autos below</li>
                        <li><b>2</b>See them side by side</li>
                        <li><b>3</b>Enquire on the winner</li>
                    </ol>

                    @if (count($selected) === 1)
                        <p class="ab-cmp-picked">
                            <x-ui.icon name="check" :size="15" class="text-brand-500" />
                            {{ $selected[0]['name'] }} selected · add one more to compare
                        </p>
                    @endif

                    @if ($popularPairs->isNotEmpty())
                        <p class="ab-cmp-or">Or start with a popular comparison</p>
                        <ul class="ab-cmp-pairs">
                            @foreach ($popularPairs as $pair)
                                <li><a href="{{ $pair['url'] }}">{{ $pair['label'] }}</a></li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            @else
            <p class="mb-2 flex items-center gap-1.5 text-[11px] font-semibold text-muted lg:hidden"><x-ui.icon name="arrow-right" :size="12" /> Swipe sideways to see all models</p>
            <div class="ab-card overflow-x-auto" data-reveal>
                <table class="w-full min-w-3xl border-collapse text-xs">
                    <caption class="sr-only">Specification comparison across selected autorickshaw models</caption>

                    <thead>
                        <tr>
                            <th scope="col" class="w-32 border-b border-line p-3"><span class="sr-only">Specification</span></th>

                            @foreach ($selected as $vehicle)
                                <th scope="col" class="relative border-b border-l border-line p-3 align-top">
                                    {{-- Removing re-renders the table from a narrower URL --}}
                                    <a href="{{ $urlFor(array_diff($selectedSlugs, [$vehicle['slug']])) }}"
                                       class="absolute right-2 top-2 rounded p-1 text-muted transition-colors hover:bg-canvas hover:text-danger"
                                       aria-label="Remove {{ $vehicle['name'] }} from comparison">
                                        <x-ui.icon name="close" :size="14" />
                                    </a>

                                    <p class="pr-5 text-sm font-bold">{{ $vehicle['name'] }}</p>

                                    <img src="{{ asset($vehicle['brand_logo']) }}" alt="{{ $vehicle['brand'] }}"
                                         class="mx-auto mt-1.5 h-4 w-auto object-contain">

                                    <div class="relative">
                                        <img src="{{ asset($vehicle['image']) }}" alt="{{ $vehicle['name'] }}"
                                             class="mx-auto mt-2 h-20 w-auto object-contain" loading="lazy">

                                        @if ($vehicle['badge'] === 'EV')
                                            <span class="absolute right-0 top-0 rounded bg-info px-1.5 py-0.5 text-[9px] font-bold text-white">EV</span>
                                        @endif
                                    </div>
                                </th>
                            @endforeach
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($rows as $row)
                            <tr class="even:bg-canvas/60">
                                <th scope="row" class="border-b border-line p-3 text-left font-semibold text-muted">
                                    {{ $row['label'] }}
                                </th>

                                @foreach ($selected as $vehicle)
                                    @php $value = $vehicle['compare'][$row['key']]; @endphp
                                    <td class="border-b border-l border-line p-3 text-center
                                               {{ ! empty($row['strong']) ? 'font-extrabold' : '' }}">
                                        @if (! empty($row['money']))
                                            ₹{{ number_format($value) }}/month
                                        @else
                                            {!! nl2br(e($value)) !!}
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach

                        <tr>
                            <td class="p-3"></td>
                            @foreach ($selected as $vehicle)
                                <td class="border-l border-line p-3">
                                    <a href="{{ route('site.model', [$vehicle['brand_slug'], $vehicle['model_slug']]) }}"
                                       class="ab-btn ab-btn-primary w-full px-2 py-2 text-[11px]">
                                        View Details <x-ui.icon name="arrow-right" :size="13" />
                                    </a>
                                </td>
                            @endforeach
                        </tr>
                    </tbody>
                </table>
            </div>
            @endif

            {{-- ------------------------------------------- add another model --}}
            <div class="ab-card mt-4 p-4">
                <div class="mb-3 flex flex-wrap items-center justify-between gap-2">
                    <h2 class="text-sm font-bold">
                        {{ $compareReady ? 'Add a Model to Compare' : 'Choose Autos to Compare' }}
                        <span class="font-normal text-muted">(<span x-text="resultCount"></span> match your filters)</span>
                    </h2>
                    <span class="text-[11px] text-muted">
                        {{ count($selectedSlugs) }} of 4 selected
                        @if (count($selectedSlugs) < 2)
                            <span class="font-semibold text-brand-600">· pick at least 2</span>
                        @endif
                    </span>
                </div>

                <ul class="ab-scroll-x">
                    @foreach ($all as $vehicle)
                        @php
                            $isIn = in_array($vehicle['slug'], $selectedSlugs, true);
                            $atLimit = count($selectedSlugs) >= 4;
                            $href = $isIn
                                ? $urlFor(array_diff($selectedSlugs, [$vehicle['slug']]))
                                : $urlFor([...$selectedSlugs, $vehicle['slug']]);
                        @endphp
                        <li x-show="results.some(r => r.id === @js($vehicle['slug']))" class="shrink-0">
                            <a href="{{ $isIn || ! $atLimit ? $href : '#' }}"
                               @if(! $isIn && $atLimit) aria-disabled="true" @endif
                               class="flex w-28 flex-col items-center gap-1 rounded-xl border px-2 py-2.5 transition-colors
                                      {{ $isIn ? 'border-brand-500 bg-brand-50'
                                         : ($atLimit ? 'pointer-events-none border-line opacity-40'
                                                     : 'border-line hover:border-brand-300') }}">
                                <img src="{{ asset($vehicle['image']) }}" alt="" aria-hidden="true"
                                     class="h-10 w-auto object-contain" loading="lazy">
                                <span class="text-center text-[10px] font-semibold leading-tight">{{ $vehicle['name'] }}</span>
                                <span class="text-[10px] font-bold {{ $isIn ? 'text-danger' : 'text-brand-600' }}">
                                    {{ $isIn ? '− Remove' : ($atLimit ? 'Full' : '+ Add') }}
                                </span>
                            </a>
                        </li>
                    @endforeach
                </ul>

                <p x-show="resultCount === 0" x-cloak class="py-4 text-center text-xs text-muted">
                    No models match these filters.
                    <button type="button" @click="clearAll()"
                            class="font-semibold text-brand-500 underline underline-offset-2">Clear all</button>
                </p>
            </div>

            {{-- ------------------------------------------------------- charts --}}
            @if ($compareReady)
            <div class="mt-5 grid gap-4 sm:grid-cols-2" data-reveal-group>

                {{-- EMI comparison --}}
                <div class="ab-card p-4">
                    <h2 class="flex items-center gap-2 text-sm font-bold">
                        <x-ui.icon name="calculator" :size="16" class="text-info" /> EMI Comparison
                    </h2>
                    <p class="mt-0.5 text-[11px] text-muted">Estimated EMI for 3 years (Down Payment: 20%)</p>

                    <ul class="mt-4 flex h-40 items-end gap-3">
                        @foreach ($selected as $i => $vehicle)
                            @php
                                $emi = $vehicle['compare']['emi'];
                                $height = max(12, round(($emi / $maxEmi) * 100));
                                $fills = ['bg-accent-500', 'bg-brand-300', 'bg-brand-500', 'bg-info'];
                            @endphp
                            <li class="flex h-full flex-1 flex-col items-center justify-end gap-1">
                                <span class="text-[11px] font-bold">₹{{ number_format($emi) }}</span>
                                <span class="w-full rounded-t {{ $fills[$i % 4] }}"
                                      style="height: {{ $height }}%"
                                      role="img"
                                      aria-label="{{ $vehicle['name'] }}: ₹{{ number_format($emi) }} per month"></span>
                                <span class="line-clamp-2 text-center text-[9px] leading-tight text-muted">
                                    {{ $vehicle['name'] }}
                                </span>
                            </li>
                        @endforeach
                    </ul>
                </div>

                {{-- Monthly fuel cost --}}
                <div class="ab-card p-4">
                    <h2 class="flex items-center gap-2 text-sm font-bold">
                        <x-ui.icon name="fuel" :size="16" class="text-brand-500" /> Monthly Fuel Cost
                    </h2>
                    <p class="mt-0.5 text-[11px] text-muted">Approximate, at 100 km per day</p>

                    <ul class="mt-4 space-y-2.5">
                        @foreach ($selected as $i => $vehicle)
                            @php
                                $cost = $vehicle['compare']['monthly_fuel'];
                                $width = max(6, round(($cost / $maxFuel) * 100));
                                $fills = ['bg-accent-500', 'bg-brand-300', 'bg-info', 'bg-brand-500'];
                            @endphp
                            <li>
                                <div class="flex items-center justify-between gap-2 text-[10px]">
                                    <span class="truncate text-muted">
                                        {{ $vehicle['name'] }} ({{ $vehicle['compare']['fuel_note'] }})
                                    </span>
                                    <span class="font-bold">₹{{ number_format($cost) }}</span>
                                </div>
                                <span class="mt-1 block h-3 rounded {{ $fills[$i % 4] }}"
                                      style="width: {{ $width }}%"
                                      role="img"
                                      aria-label="{{ $vehicle['name'] }}: ₹{{ number_format($cost) }} per month"></span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
            @endif
        </div>

        {{-- ============================================================ aside --}}
        <aside class="min-w-0 space-y-4 lg:col-span-3">

            <div class="ab-card p-4">
                <h2 class="mb-3 flex items-center gap-2 text-sm font-bold">
                    <x-ui.icon name="lightbulb" :size="17" class="text-accent-600" />
                    Which One Is Best for You?
                </h2>
                <ul class="space-y-2">
                    @foreach ([
                        'Compare real specifications',
                        'Check on-road price in ' . $locations['default']['city'],
                        'See EMI and running cost',
                        'Choose based on your usage',
                        'Get expert recommendation',
                    ] as $point)
                        <li class="flex items-start gap-2 text-xs">
                            <x-ui.icon name="check-circle" :size="14" class="mt-0.5 shrink-0 text-success" />
                            {{ $point }}
                        </li>
                    @endforeach
                </ul>
            </div>

            {{-- Expert recommendation --}}
            @if ($compareReady && $pick)
                <div class="ab-card bg-accent-50 p-4">
                    <h2 class="mb-2 flex items-center gap-2 text-sm font-bold">
                        <x-ui.icon name="trophy" :size="17" class="text-accent-600" />
                        Expert Recommendation
                    </h2>

                    <img src="{{ asset($pick['image']) }}" alt="{{ $pick['name'] }}"
                         class="mx-auto h-20 w-auto object-contain" loading="lazy">

                    <p class="mt-1 text-sm font-extrabold text-brand-600">{{ $pick['name'] }}</p>
                    <p class="text-[11px] font-semibold text-warn">Best Overall Choice</p>

                    <ul class="mt-2.5 space-y-1.5">
                        @foreach (array_slice($pick['suitable_for'], 0, 4) as $tag)
                            <li class="flex items-center gap-2 text-xs">
                                <x-ui.icon name="check" :size="13" class="text-success" /> {{ $tag }}
                            </li>
                        @endforeach
                    </ul>

                    <a href="{{ route('site.enquiry', $pick['slug']) }}"
                       class="ab-btn ab-btn-accent mt-4 w-full text-xs">
                        Enquire Now <x-ui.icon name="arrow-right" :size="14" />
                    </a>
                </div>
            @endif

            {{-- Running-cost saving --}}
            @if ($compareReady && $saving > 0)
                <div class="ab-card bg-violet-50 p-4">
                    <h2 class="mb-1 flex items-center gap-2 text-sm font-bold">
                        <x-ui.icon name="gear" :size="17" class="text-violet-600" />
                        Running Cost Advantage
                    </h2>
                    <p class="text-[11px] text-muted">Lower running costs. Higher savings.</p>

                    <p class="mt-3 text-xs">Save up to</p>
                    <p class="text-3xl font-extrabold text-violet-600">₹{{ number_format($saving) }}</p>
                    <p class="text-xs">
                        per month with <span class="font-bold">{{ $cheapest['compare']['fuel_note'] }}</span>
                    </p>
                    <p class="mt-1.5 text-[10px] text-muted">
                        Comparing {{ $cheapest['name'] }} against {{ $dearest['name'] }}.
                    </p>
                </div>
            @endif
        </aside>
    </div>
</section>

{{-- ============================================================== trust strip --}}
<section class="border-y border-line bg-surface">
    <div class="ab-container grid gap-5 py-6 sm:grid-cols-2 lg:grid-cols-4">
        @foreach ([
            ['shield', '100% Genuine Information', 'Compare. Choose. Drive Confidently.'],
            ['rupee', 'Best Deals', 'Exclusive Offers in ' . $locations['default']['city']],
            ['wrench', 'Expert Support', 'Our team is here to help'],
            ['users', 'Trusted by Auto Drivers', '10,000+ Happy Customers'],
        ] as [$icon, $title, $note])
            <div class="flex items-center gap-3">
                <x-ui.icon :name="$icon" :size="24" class="text-brand-500" />
                <span class="leading-tight">
                    <span class="block text-sm font-bold">{{ $title }}</span>
                    <span class="block text-[11px] text-muted">{{ $note }}</span>
                </span>
            </div>
        @endforeach
    </div>
</section>

@endsection
