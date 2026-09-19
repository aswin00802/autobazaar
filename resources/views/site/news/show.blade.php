@extends('site.layout')

@section('title', $post['title'])
@section('description', $post['excerpt'])

@section('content')

<article class="ab-container py-8">
    <x-ui.breadcrumb :items="[
        ['label' => 'Home', 'href' => route('site.home')],
        ['label' => 'Auto News', 'href' => route('site.news')],
        ['label' => $post['category']],
    ]" class="mb-4" />

    <div class="grid gap-8 lg:grid-cols-12">

        <div class="min-w-0 lg:col-span-8">
            <span class="rounded-full bg-brand-50 px-2.5 py-1 text-[10px] font-bold text-brand-600">
                {{ $post['category'] }}
            </span>

            <h1 class="mt-3 text-2xl font-extrabold leading-tight tracking-tight sm:text-3xl">
                {{ $post['title'] }}
            </h1>

            <p class="mt-2 text-xs text-muted">
                {{ $post['author'] }} · {{ $post['published'] }} · {{ $post['read_time'] }}
            </p>

            <img src="{{ asset($post['image']) }}" alt=""
                 class="ab-card mt-5 h-64 w-full bg-canvas object-contain p-8" loading="lazy">

            {{-- Body: placeholder copy for the prototype --}}
            <div class="mt-6 space-y-4 text-sm leading-relaxed text-ink-soft">
                <p class="text-base font-semibold text-ink">{{ $post['excerpt'] }}</p>

                <p>
                    This article is placeholder copy for the design prototype. In the built site the body
                    is authored in the admin panel and rendered here with headings, images, pull quotes
                    and inline links.
                </p>

                <p>
                    The layout is sized for a comfortable reading measure — roughly 65 to 75 characters
                    per line at desktop width — with the related-model rail alongside so a reader can move
                    straight from a story into the catalogue.
                </p>

                <blockquote class="border-l-4 border-brand-500 bg-brand-50 py-3 pl-4 pr-3 text-sm italic">
                    Pull quotes and callouts get their own treatment so long articles stay scannable.
                </blockquote>

                <p>
                    Sections, lists and tables all inherit the same type scale as the rest of the site,
                    so editorial content never looks bolted on.
                </p>
            </div>

            {{-- Share --}}
            <div class="mt-8 flex items-center gap-3 border-t border-line pt-5">
                <span class="text-xs font-semibold text-muted">Share:</span>
                @foreach ($site['socials'] as $social)
                    <a href="{{ $social['url'] }}" aria-label="Share on {{ $social['label'] }}"
                       class="grid h-8 w-8 place-items-center rounded-lg bg-canvas text-ink-soft
                              transition-colors hover:bg-brand-50 hover:text-brand-500">
                        <x-ui.icon :name="$social['icon']" :size="16" />
                    </a>
                @endforeach
            </div>
        </div>

        {{-- Related --}}
        <aside class="min-w-0 lg:col-span-4">
            <h2 class="mb-4 text-base font-extrabold">Related Stories</h2>

            <ul class="space-y-3">
                @foreach ($related as $other)
                    <li>
                        <a href="{{ route('site.news.article', $other['slug']) }}"
                           class="ab-card flex gap-3 p-3 ab-lift">
                            <img src="{{ asset($other['image']) }}" alt=""
                                 class="h-16 w-20 shrink-0 rounded bg-canvas object-contain p-1.5" loading="lazy">
                            <span class="min-w-0">
                                <span class="block text-[10px] font-bold text-brand-600">{{ $other['category'] }}</span>
                                <span class="mt-0.5 line-clamp-3 block text-xs font-semibold leading-snug">
                                    {{ $other['title'] }}
                                </span>
                                <span class="mt-1 block text-[10px] text-muted">{{ $other['published'] }}</span>
                            </span>
                        </a>
                    </li>
                @endforeach
            </ul>
        </aside>
    </div>
</article>

@endsection
