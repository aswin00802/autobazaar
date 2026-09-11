@extends('site.layout')

@section('title', 'Latest Offers')
@section('description', 'Exchange bonuses, low down payment finance and festival offers across every autorickshaw brand.')

@section('content')

<x-ui.page-hero title="Latest Offers"
                lede="Exchange bonuses, low down payment finance and limited-period festival offers across every brand."
                :breadcrumb="[
                    ['label' => 'Home', 'href' => route('site.home')],
                    ['label' => 'Offers'],
                ]" />

<section class="ab-container py-8">
    <ul class="grid gap-4 lg:grid-cols-3" data-reveal-group>
        @foreach ($offers as $offer)
            <li class="ab-card flex h-full flex-col overflow-hidden
                       {{ $offer['tone'] === 'accent' ? 'bg-accent-50'
                          : ($offer['tone'] === 'info' ? 'bg-blue-50' : 'bg-orange-50') }}" data-reveal>

                <div class="flex items-start gap-4 p-5">
                    <div class="flex-1">
                        <img src="{{ asset($offer['brand_logo']) }}" alt="{{ $offer['brand'] }}"
                             class="mb-2 h-5 w-auto object-contain object-left">

                        <h2 class="text-lg font-extrabold leading-tight">{{ $offer['title'] }}</h2>
                        @if ($offer['subtitle'])
                            <p class="text-lg font-extrabold leading-tight">{{ $offer['subtitle'] }}</p>
                        @endif

                        @if ($offer['benefit'])
                            <p class="mt-2 text-xs text-muted">{{ $offer['benefit_label'] }}</p>
                            <p class="text-3xl font-extrabold text-brand-600">{{ $offer['benefit'] }}</p>
                        @endif
                    </div>

                    <img src="{{ asset($offer['image']) }}" alt="" aria-hidden="true"
                         class="h-24 w-28 shrink-0 object-contain" loading="lazy">
                </div>

                @if (! empty($offer['bullets']))
                    <ul class="space-y-1.5 px-5">
                        @foreach ($offer['bullets'] as $bullet)
                            <li class="flex items-start gap-2 text-xs">
                                <x-ui.icon name="check" :size="13" class="mt-0.5 shrink-0 text-success" />
                                {{ $bullet }}
                            </li>
                        @endforeach
                    </ul>
                @endif

                <div class="mt-auto p-5">
                    <a href="{{ route('site.enquiry') }}" class="ab-btn ab-btn-primary w-full text-xs">
                        {{ $offer['cta'] }} <x-ui.icon name="arrow-right" :size="14" />
                    </a>
                    @if ($offer['note'])
                        <p class="mt-1.5 text-right text-[10px] text-muted">{{ $offer['note'] }}</p>
                    @endif
                </div>
            </li>
        @endforeach
    </ul>

    <x-ui.section-heading title="Models with Active Offers" class="mt-10"
                          :href="route('site.new-autos')" />

    <ul class="grid grid-cols-2 gap-4 sm:grid-cols-3 xl:grid-cols-4">
        @foreach (collect($vehicles)->filter(fn ($v) => ! empty($v['offers'])) as $vehicle)
            <li><x-ui.vehicle-card :vehicle="$vehicle" class="h-full" /></li>
        @endforeach
    </ul>
</section>

@endsection
