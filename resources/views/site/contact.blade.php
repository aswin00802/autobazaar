@extends('site.layout')

@section('title', 'Contact Us')

@section('content')

<x-ui.page-hero title="Contact Us"
                lede="Call, message or visit — whichever suits you. We answer {{ $site['contact']['hours'] }}."
                :breadcrumb="[
                    ['label' => 'Home', 'href' => route('site.home')],
                    ['label' => 'Contact'],
                ]" />

<section class="ab-container py-10">
    <div class="grid gap-6 lg:grid-cols-12">

        {{-- Form --}}
        <div class="min-w-0 lg:col-span-7">
            <div class="ab-card flex h-full flex-col p-6" data-reveal="left">
                <h2 class="text-lg font-extrabold">Send us a message</h2>
                <p class="mt-1 text-sm text-muted">We usually reply within one working day.</p>

                <form @submit.prevent class="mt-5 flex flex-1 flex-col gap-4">
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label for="ct-name" class="ab-label">Full Name <span class="text-danger">*</span></label>
                            <input id="ct-name" type="text" required placeholder="Enter your full name" class="ab-field">
                        </div>
                        <div>
                            <label for="ct-phone" class="ab-label">Mobile Number <span class="text-danger">*</span></label>
                            <input id="ct-phone" type="tel" required placeholder="Enter mobile number" class="ab-field">
                        </div>
                        <div>
                            <label for="ct-email" class="ab-label">Email Address</label>
                            <input id="ct-email" type="email" placeholder="Enter your email (optional)" class="ab-field">
                        </div>
                        <div>
                            <label for="ct-topic" class="ab-label">Topic</label>
                            <select id="ct-topic" class="ab-field">
                                <option>Vehicle enquiry</option>
                                <option>Finance &amp; EMI</option>
                                <option>Government schemes</option>
                                <option>Accessories order</option>
                                <option>Something else</option>
                            </select>
                        </div>
                    </div>

                    <div class="flex flex-1 flex-col">
                        <label for="ct-msg" class="ab-label">Message</label>
                        <textarea id="ct-msg" rows="6" class="ab-field min-h-36 flex-1 resize-none"
                                  placeholder="How can we help?"></textarea>
                    </div>

                    <button type="submit" class="ab-btn ab-btn-primary w-full py-3">
                        <x-ui.icon name="arrow-right" :size="18" /> Send Message
                    </button>
                </form>
            </div>
        </div>

        {{-- Details --}}
        <aside class="min-w-0 space-y-4 lg:col-span-5">
            <div class="ab-card p-5">
                <h2 class="text-base font-extrabold">Reach us directly</h2>

                <ul class="mt-4 space-y-4">
                    @foreach ([
                        ['phone-call', 'Call Us', $site['contact']['phone'], 'tel:' . $site['contact']['phone_e164']],
                        ['whatsapp', 'Chat on WhatsApp', $site['contact']['phone'], 'https://wa.me/' . $site['contact']['whatsapp']],
                        ['mail', 'Email Us', $site['contact']['email'], 'mailto:' . $site['contact']['email']],
                        ['pin', 'Visit Our Showroom', $site['contact']['address'], null],
                        ['clock', 'Support Hours', $site['contact']['hours'], null],
                    ] as [$icon, $label, $value, $href])
                        <li class="flex items-start gap-3">
                            <x-ui.tone-icon :icon="$icon" tone="brand" :size="17" shape="circle" />
                            <span class="leading-tight">
                                <span class="block text-[11px] text-muted">{{ $label }}</span>
                                @if ($href)
                                    <a href="{{ $href }}" class="block text-sm font-bold hover:text-brand-500">{{ $value }}</a>
                                @else
                                    <span class="block text-sm font-bold">{{ $value }}</span>
                                @endif
                            </span>
                        </li>
                    @endforeach
                </ul>
            </div>

            <div class="ab-card bg-accent-50 p-5">
                <p class="flex items-center gap-2 text-sm font-extrabold">
                    <x-ui.icon name="pin" :size="17" class="text-brand-500" />
                    Direct purchase districts
                </p>
                <ul class="mt-3 grid grid-cols-2 gap-2">
                    @foreach ($site['service_area']['districts'] as $district)
                        <li class="flex items-center gap-2 text-xs font-semibold">
                            <x-ui.icon name="check" :size="13" class="text-success" /> {{ $district }}
                        </li>
                    @endforeach
                </ul>
                <p class="mt-3 text-[11px] text-muted">{{ $site['service_area']['note'] }}</p>
            </div>

            {{-- Map placeholder: the built site embeds Google Maps here --}}
            <div class="ab-card grid h-48 place-items-center bg-canvas text-center">
                <div>
                    <x-ui.icon name="map" :size="28" class="mx-auto text-line" />
                    <p class="mt-2 text-xs font-semibold text-muted">Showroom map</p>
                    <p class="text-[10px] text-muted">Google Maps embed goes here</p>
                </div>
            </div>
        </aside>
    </div>
</section>

@endsection
