@extends('site.layout')

@section('title', $title)

@section('content')

@php
    // Placeholder policy copy. Real text is authored in the admin CMS
    // (see the `pages` table in the plan) — these are structural stand-ins.
    $intros = [
        'buying-policy' => 'How buying an autorickshaw through AutoBazaar works, what we commit to, and what we need from you.',
        'privacy-policy' => 'What information we collect, why we collect it, and the choices you have.',
        'terms-conditions' => 'The terms that apply when you use AutoBazaar.',
        'refund-policy' => 'When a refund applies to a vehicle booking, and how it is processed.',
        'shipping-policy' => 'How accessory orders are packed, dispatched and delivered across India.',
        'returns-refunds' => 'How to return an accessory and how refunds are issued.',
    ];

    $sections = [
        'Scope' => 'This section explains what the policy covers and who it applies to. In the built site this content is authored in the admin panel so it can be updated without a deployment.',
        'What we commit to' => 'A plain-language list of the obligations AutoBazaar takes on — accurate pricing, genuine information, and clear communication at each step of the process.',
        'What we need from you' => 'The documents and details required to complete a purchase, along with the timeframes that apply.',
        'Location-based differences' => 'Direct purchase is available in ' . implode(', ', $site['service_area']['districts']) . '. Elsewhere in Tamil Nadu we provide buying assistance, and in other states we connect you with a nearby dealer. Terms differ by tier.',
        'Changes to this policy' => 'How and when this policy may be updated, and how we tell you about material changes.',
        'Contact' => 'Questions about this policy can go to ' . $site['contact']['email'] . ' or ' . $site['contact']['phone'] . '.',
    ];
@endphp

<x-ui.page-hero :title="$title"
                :lede="$intros[$slug] ?? null"
                :breadcrumb="[
                    ['label' => 'Home', 'href' => route('site.home')],
                    ['label' => $title],
                ]" />

<section class="ab-container py-10">
    <div class="grid gap-8 lg:grid-cols-12">

        {{-- Contents --}}
        <aside class="min-w-0 lg:col-span-3">
            <nav aria-label="On this page" class="ab-card sticky top-40 p-4">
                <p class="mb-2 text-xs font-bold uppercase tracking-wide text-muted">On this page</p>
                <ul class="space-y-1.5">
                    @foreach ($sections as $heading => $body)
                        <li>
                            <a href="#{{ Str::slug($heading) }}"
                               class="block text-xs text-ink-soft transition-colors hover:text-brand-500">
                                {{ $heading }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </nav>
        </aside>

        {{-- Body --}}
        <div class="min-w-0 lg:col-span-9">
            <p class="mb-6 flex items-start gap-2 rounded-lg bg-accent-50 px-4 py-3 text-xs text-ink-soft">
                <x-ui.icon name="info" :size="15" class="mt-0.5 shrink-0 text-accent-600" />
                This is placeholder copy for the design prototype. Final policy text is supplied by the
                client and managed in the admin panel.
            </p>

            <div class="space-y-7">
                @foreach ($sections as $heading => $body)
                    <section id="{{ Str::slug($heading) }}">
                        <h2 class="text-lg font-extrabold">{{ $heading }}</h2>
                        <p class="mt-2 text-sm leading-relaxed text-ink-soft">{{ $body }}</p>
                    </section>
                @endforeach
            </div>

            <p class="mt-8 border-t border-line pt-5 text-xs text-muted">
                Last updated: 10 September 2026
            </p>
        </div>
    </div>
</section>

@endsection
