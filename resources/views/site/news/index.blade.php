@extends('site.layout')

@section('title', 'Auto News')
@section('description', 'Launches, policy changes, finance updates and practical tips for autorickshaw drivers.')

@section('content')

@php
    $featured = collect($posts)->firstWhere('featured', true) ?? $posts[0];
    $rest = collect($posts)->reject(fn ($p) => $p['slug'] === $featured['slug']);
@endphp

<x-ui.page-hero title="Auto News"
                lede="Launches, policy changes, finance updates and practical tips for auto drivers."
                :breadcrumb="[
                    ['label' => 'Home', 'href' => route('site.home')],
                    ['label' => 'Auto News'],
                ]" />

<section class="ab-container py-8" x-data="{ category: 'All' }">

    {{-- Category tabs --}}
    <div class="ab-scroll-x mb-6" role="tablist" aria-label="News categories">
        @foreach ($categories as $cat)
            <button type="button" role="tab"
                    @click="category = @js($cat)"
                    :aria-selected="category === @js($cat)"
                    :class="category === @js($cat)
                        ? 'bg-brand-500 text-white border-brand-500'
                        : 'bg-surface text-ink-soft border-line hover:border-brand-300'"
                    class="shrink-0 rounded-lg border px-3.5 py-2 text-xs font-semibold transition-colors">
                {{ $cat }}
            </button>
        @endforeach
    </div>

    {{-- Featured --}}
    <a href="{{ route('site.news.article', $featured['slug']) }}"
       class="ab-card mb-6 grid gap-5 overflow-hidden ab-lift lg:grid-cols-2"
       x-show="category === 'All' || category === @js($featured['category'])">

        <img src="{{ asset($featured['image']) }}" alt=""
             class="h-56 w-full bg-canvas object-contain p-6 lg:h-full" loading="lazy">

        <div class="flex flex-col justify-center p-6">
            <span class="w-fit rounded-full bg-brand-50 px-2.5 py-1 text-[10px] font-bold text-brand-600">
                {{ $featured['category'] }}
            </span>

            <h2 class="mt-3 text-xl font-extrabold leading-snug sm:text-2xl">{{ $featured['title'] }}</h2>
            <p class="mt-2 text-sm leading-relaxed text-muted">{{ $featured['excerpt'] }}</p>

            <p class="mt-4 text-[11px] text-muted">
                {{ $featured['author'] }} · {{ $featured['published'] }} · {{ $featured['read_time'] }}
            </p>
        </div>
    </a>

    {{-- Grid --}}
    <ul class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3" data-reveal-group>
        @foreach ($rest as $post)
            <li x-show="category === 'All' || category === @js($post['category'])" data-reveal>
                <a href="{{ route('site.news.article', $post['slug']) }}"
                   class="ab-card flex h-full flex-col overflow-hidden ab-lift">

                    <img src="{{ asset($post['image']) }}" alt=""
                         class="h-36 w-full bg-canvas object-contain p-4" loading="lazy">

                    <div class="flex flex-1 flex-col p-4">
                        <span class="w-fit rounded-full bg-canvas px-2 py-0.5 text-[10px] font-bold text-muted">
                            {{ $post['category'] }}
                        </span>

                        <h2 class="mt-2 text-sm font-bold leading-snug">{{ $post['title'] }}</h2>
                        <p class="mt-1.5 line-clamp-3 flex-1 text-xs leading-relaxed text-muted">{{ $post['excerpt'] }}</p>

                        <p class="mt-3 text-[10px] text-muted">
                            {{ $post['published'] }} · {{ $post['read_time'] }}
                        </p>
                    </div>
                </a>
            </li>
        @endforeach
    </ul>
</section>

@endsection
