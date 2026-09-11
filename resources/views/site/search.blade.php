@extends('site.layout')

@section('title', $query ? 'Search: ' . $query : 'Search')

@section('content')

<x-ui.page-hero :title="$query ? 'Results for &quot;' . $query . '&quot;' : 'Search AutoBazaar'"
                lede="Autos, accessories, government schemes and news — all in one place."
                :breadcrumb="[
                    ['label' => 'Home', 'href' => route('site.home')],
                    ['label' => 'Search'],
                ]">

    <form action="{{ route('site.search') }}" method="GET" class="mt-5 max-w-2xl" role="search">
        <div class="flex overflow-hidden rounded-lg border border-line bg-surface
                    focus-within:border-brand-400 focus-within:ring-2 focus-within:ring-brand-100">
            <input type="search" name="q" value="{{ $query }}"
                   placeholder="Search autos, accessories, news, schemes..."
                   aria-label="Search AutoBazaar"
                   class="w-full bg-transparent px-4 py-3 text-sm outline-none">
            <button type="submit" class="grid w-14 place-items-center bg-brand-500 text-white hover:bg-brand-600"
                    aria-label="Search">
                <x-ui.icon name="search" />
            </button>
        </div>
    </form>
</x-ui.page-hero>

<section class="ab-container py-8" x-data="{ tab: 'autos' }">

    {{-- Result-type tabs --}}
    <div class="ab-scroll-x mb-6" role="tablist" aria-label="Result types">
        @foreach ([
            ['autos', 'Autos', count($vehicles)],
            ['accessories', 'Accessories', count($products)],
            ['schemes', 'Schemes', count($schemes)],
            ['news', 'News', count($posts)],
        ] as [$key, $label, $count])
            <button type="button" role="tab" @click="tab = @js($key)"
                    :aria-selected="tab === @js($key)"
                    :class="tab === @js($key)
                        ? 'bg-brand-500 text-white border-brand-500'
                        : 'bg-surface text-ink-soft border-line hover:border-brand-300'"
                    class="shrink-0 rounded-lg border px-4 py-2 text-xs font-semibold transition-colors">
                {{ $label }} <span class="opacity-70">({{ $count }})</span>
            </button>
        @endforeach
    </div>

    {{-- Autos --}}
    <ul x-show="tab === 'autos'" class="grid grid-cols-2 gap-4 sm:grid-cols-3 xl:grid-cols-5">
        @foreach ($vehicles as $vehicle)
            <li><x-ui.vehicle-card :vehicle="$vehicle" class="h-full" /></li>
        @endforeach
    </ul>

    {{-- Accessories --}}
    <ul x-show="tab === 'accessories'" x-cloak class="grid grid-cols-2 gap-3 sm:grid-cols-3 xl:grid-cols-6">
        @foreach ($products as $product)
            <li><x-ui.product-card :product="$product" class="h-full" /></li>
        @endforeach
    </ul>

    {{-- Schemes --}}
    <ul x-show="tab === 'schemes'" x-cloak class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
        @foreach ($schemes as $scheme)
            <li><x-ui.scheme-card :scheme="$scheme" /></li>
        @endforeach
    </ul>

    {{-- News --}}
    <ul x-show="tab === 'news'" x-cloak class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        @foreach ($posts as $post)
            <li>
                <a href="{{ route('site.news.article', $post['slug']) }}"
                   class="ab-card flex h-full gap-3 p-3 ab-lift">
                    <img src="{{ asset($post['image']) }}" alt=""
                         class="h-16 w-20 shrink-0 rounded bg-canvas object-contain p-1.5" loading="lazy">
                    <span class="min-w-0">
                        <span class="block text-[10px] font-bold text-brand-600">{{ $post['category'] }}</span>
                        <span class="mt-0.5 line-clamp-3 block text-xs font-semibold leading-snug">{{ $post['title'] }}</span>
                        <span class="mt-1 block text-[10px] text-muted">{{ $post['published'] }}</span>
                    </span>
                </a>
            </li>
        @endforeach
    </ul>
</section>

@endsection
