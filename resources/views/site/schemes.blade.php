@extends('site.layout')

@section('title', 'Government Schemes')
@section('description', 'Latest government schemes, subsidies, loans and benefits for auto rickshaw, electric vehicle and 2-wheeler buyers.')

@section('content')

{{-- ==================================================================== hero --}}
<section class="relative overflow-hidden bg-gradient-to-r from-brand-50 via-blue-50 to-canvas">
    <div class="ab-container py-8 lg:py-10">
        <div class="grid gap-8 lg:grid-cols-12 lg:items-center">

            <div class="lg:col-span-6">
                <h1 class="text-3xl font-extrabold tracking-tight sm:text-4xl">Government Schemes</h1>
                <p class="mt-1 text-lg font-bold text-brand-600">Support for a Better Tomorrow</p>
                <p class="mt-2 max-w-lg text-sm text-muted">
                    Explore latest government schemes, subsidies, loans and benefits for
                    Auto Rickshaw, Electric Vehicles and 2 Wheelers.
                </p>

                <ul class="mt-5 flex flex-wrap gap-x-6 gap-y-3">
                    @foreach ([
                        ['rupee', 'Financial Support', 'Higher Savings'],
                        ['leaf', 'Go Green', 'Cleaner Environment'],
                        ['users', 'Self Employment', 'Better Livelihood'],
                        ['shield', 'Government Approved', 'Trusted & Genuine'],
                    ] as [$icon, $title, $note])
                        <li class="flex items-center gap-2">
                            <x-ui.tone-icon :icon="$icon" tone="brand" :size="16" shape="circle" />
                            <span class="leading-tight">
                                <span class="block text-xs font-bold">{{ $title }}</span>
                                <span class="block text-[10px] text-muted">{{ $note }}</span>
                            </span>
                        </li>
                    @endforeach
                </ul>
            </div>

            <div class="hidden items-center justify-end gap-4 lg:col-span-6 lg:flex">
                @foreach (['tvs.png', 'montra_auto1f.png', 'bajaj_auto.png'] as $img)
                    <img src="{{ asset('assets/image/auto_brands/' . $img) }}" alt="" aria-hidden="true"
                         class="h-28 w-auto object-contain" loading="lazy">
                @endforeach

                <p class="ab-script text-xl leading-tight text-brand-600">
                    People Move<br>Tamil Nadu Moves
                </p>
            </div>
        </div>
    </div>
</section>

<section class="ab-container py-8" x-data="{ category: 'all', query: '' }">
    <div class="grid gap-6 lg:grid-cols-12">

        <div class="lg:col-span-9">

            {{-- Category tabs --}}
            <div class="ab-scroll-x mb-5" role="tablist" aria-label="Scheme categories">
                @foreach ($categories as $cat)
                    <button type="button" role="tab"
                            @click="category = @js($cat['slug'])"
                            :aria-selected="category === @js($cat['slug'])"
                            :class="category === @js($cat['slug'])
                                ? 'bg-brand-500 text-white border-brand-500'
                                : 'bg-surface text-ink-soft border-line hover:border-brand-300'"
                            class="shrink-0 rounded-lg border px-3.5 py-2 text-xs font-semibold transition-colors">
                        {{ $cat['label'] }}
                    </button>
                @endforeach
            </div>

            {{-- Scheme cards --}}
            <ul class="grid gap-4 md:grid-cols-2 xl:grid-cols-3" data-reveal-group>
                @foreach ($schemes as $scheme)
                    <li x-show="(category === 'all' || {{ Js::from($scheme['categories']) }}.includes(category))
                                && (query === '' || @js(strtolower($scheme['title'])).includes(query.toLowerCase()))" data-reveal>
                        <x-ui.scheme-card :scheme="$scheme" />
                    </li>
                @endforeach
            </ul>

            {{-- How to avail --}}
            <div class="ab-card mt-6 bg-brand-50 p-5" data-reveal>
                <div class="flex flex-col gap-5 lg:flex-row lg:items-center">
                    <div class="flex shrink-0 items-start gap-3 lg:w-64">
                        <x-ui.icon name="doc" :size="22" class="mt-0.5 text-brand-500" />
                        <div>
                            <h2 class="text-sm font-extrabold">How to Avail Government Schemes?</h2>
                            <p class="text-xs text-muted">A simple process to get started</p>
                        </div>
                    </div>

                    <ol class="grid flex-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
                        @foreach ($steps as $step)
                            <li class="flex items-start gap-2.5">
                                <span class="grid h-7 w-7 shrink-0 place-items-center rounded-full bg-brand-500 text-[11px] font-bold text-white">
                                    {{ $step['step'] }}
                                </span>
                                <span class="leading-tight">
                                    <span class="block text-xs font-bold">{{ $step['title'] }}</span>
                                    <span class="block text-[10px] text-muted">{{ $step['note'] }}</span>
                                </span>
                            </li>
                        @endforeach
                    </ol>
                </div>
            </div>
        </div>

        {{-- ============================================================ aside --}}
        <aside class="space-y-4 lg:col-span-3">

            <div class="relative">
                <x-ui.icon name="search" :size="16"
                           class="absolute right-3 top-1/2 -translate-y-1/2 text-muted" />
                <input type="search" x-model="query" placeholder="Search schemes..."
                       aria-label="Search schemes" class="ab-field pr-9">
            </div>

            <div class="ab-card p-4">
                <p class="mb-1 flex items-center gap-2 text-sm font-bold">
                    <x-ui.icon name="headset" :size="17" class="text-brand-500" />
                    Need Help with Schemes?
                </p>
                <p class="text-xs text-muted">
                    Our team will guide you with the right scheme based on your needs.
                </p>

                <a href="https://wa.me/{{ $site['contact']['whatsapp'] }}" target="_blank" rel="noopener"
                   class="ab-btn ab-btn-primary mt-3 w-full text-xs">
                    <x-ui.icon name="whatsapp" :size="16" />
                    Chat on WhatsApp
                </a>

                <ul class="mt-3 space-y-2.5">
                    <li class="flex items-start gap-2.5">
                        <x-ui.icon name="phone-call" :size="16" class="mt-0.5 text-brand-500" />
                        <span class="leading-tight">
                            <span class="block text-[11px] text-muted">Call Us</span>
                            <span class="block text-sm font-bold">{{ $site['contact']['phone'] }}</span>
                        </span>
                    </li>
                    <li class="flex items-start gap-2.5">
                        <x-ui.icon name="pin" :size="16" class="mt-0.5 text-brand-500" />
                        <span class="leading-tight">
                            <span class="block text-[11px] text-muted">Visit Our Showroom</span>
                            <span class="block text-sm font-bold">{{ $site['contact']['address'] }}</span>
                        </span>
                    </li>
                </ul>

                <p class="mt-3 flex items-start gap-2 rounded-lg bg-brand-50 px-3 py-2.5 text-[11px] text-ink-soft">
                    <x-ui.icon name="handshake" :size="15" class="mt-0.5 shrink-0 text-brand-500" />
                    We assist for {{ implode(', ', $site['service_area']['districts']) }}.
                    Other districts — we will guide and assist.
                </p>
            </div>

            <div class="ab-card flex items-center gap-3 bg-brand-700 p-4 text-white">
                <x-ui.icon name="bell" :size="20" class="text-accent-500" />
                <span class="leading-tight">
                    <span class="block text-sm font-bold">Stay Updated</span>
                    <span class="block text-[11px] text-white/75">Get latest scheme updates on WhatsApp</span>
                </span>
            </div>
        </aside>
    </div>
</section>

@endsection
