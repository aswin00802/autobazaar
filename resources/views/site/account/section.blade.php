@extends('site.layout')

@section('title', $heading)

@section('content')

@php
    // One shell, per-section content. Kept lightweight — these are Tier 2
    // screens, present so the account sidebar has no dead links.
    $labels = [
        'enquiries' => 'Enquiries',
        'saved' => 'Saved Vehicles',
        'comparisons' => 'My Comparisons',
        'addresses' => 'Addresses',
        'payment-methods' => 'Payment Methods',
        'notifications' => 'Notifications',
        'refer' => 'Refer & Earn',
        'support' => 'Support',
        'profile' => 'Profile',
    ];
    $active = $labels[$section] ?? $heading;
@endphp

<x-ui.account-shell :account="$account" :active="$active">

    <h1 class="text-xl font-extrabold">{{ $active }}</h1>

    {{-- ============================================================ enquiries --}}
    @if ($section === 'enquiries')
        <p class="mt-1 text-sm text-muted">Every enquiry you have sent, and where it stands.</p>

        <ul class="mt-5 space-y-3">
            @foreach ([
                ['TVS King Deluxe', '03 Sep 2026', 'Contacted', 'brand'],
                ['Bajaj RE', '27 Aug 2026', 'In Progress', 'info'],
                ['Mahindra Treo Plus', '12 Aug 2026', 'Closed', 'muted'],
            ] as $i => [$model, $date, $status, $tone])
                @php $vehicle = $vehicles[$i] ?? $vehicles[0]; @endphp
                <li class="ab-card flex flex-wrap items-center gap-4 p-4">
                    <img src="{{ asset($vehicle['image']) }}" alt="" aria-hidden="true"
                         class="h-14 w-20 shrink-0 object-contain" loading="lazy">

                    <span class="min-w-40 flex-1 leading-tight">
                        <span class="block text-sm font-bold">{{ $model }}</span>
                        <span class="block text-xs text-muted">Enquired on {{ $date }}</span>
                    </span>

                    <span class="rounded-full px-2.5 py-1 text-[10px] font-bold
                                 {{ $tone === 'brand' ? 'bg-brand-50 text-brand-600'
                                    : ($tone === 'info' ? 'bg-blue-50 text-info' : 'bg-canvas text-muted') }}">
                        {{ $status }}
                    </span>
                </li>
            @endforeach
        </ul>

    {{-- ================================================================ saved --}}
    @elseif ($section === 'saved')
        <p class="mt-1 text-sm text-muted">Models you have saved for later.</p>

        <ul class="mt-5 grid grid-cols-2 gap-4 sm:grid-cols-3 xl:grid-cols-4">
            @foreach (array_slice($vehicles, 0, 4) as $vehicle)
                <li><x-ui.vehicle-card :vehicle="$vehicle" class="h-full" /></li>
            @endforeach
        </ul>

    {{-- ========================================================== comparisons --}}
    @elseif ($section === 'comparisons')
        <p class="mt-1 text-sm text-muted">Comparison sets you have saved.</p>

        <ul class="mt-5 space-y-3">
            @foreach ([
                ['Petrol vs CNG shortlist', [0, 1, 2]],
                ['Electric options', [3, 5]],
            ] as [$name, $indexes])
                <li class="ab-card p-4">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <p class="text-sm font-bold">{{ $name }}</p>
                        <a href="{{ route('site.compare') }}"
                           class="flex items-center gap-1 text-xs font-semibold text-brand-500 underline underline-offset-2">
                            Open Comparison <x-ui.icon name="arrow-right" :size="13" />
                        </a>
                    </div>

                    <ul class="mt-3 flex flex-wrap gap-3">
                        @foreach ($indexes as $i)
                            @if (isset($vehicles[$i]))
                                <li class="flex items-center gap-2 rounded-lg bg-canvas px-3 py-2">
                                    <img src="{{ asset($vehicles[$i]['image']) }}" alt="" aria-hidden="true"
                                         class="h-8 w-11 object-contain" loading="lazy">
                                    <span class="text-xs font-semibold">{{ $vehicles[$i]['name'] }}</span>
                                </li>
                            @endif
                        @endforeach
                    </ul>
                </li>
            @endforeach
        </ul>

    {{-- ============================================================ addresses --}}
    @elseif ($section === 'addresses')
        <p class="mt-1 text-sm text-muted">Where we deliver your orders.</p>

        <ul class="mt-5 grid gap-3 sm:grid-cols-2">
            @foreach ($addresses as $address)
                <li class="ab-card p-4">
                    <div class="flex items-center justify-between gap-2">
                        <span class="flex items-center gap-2">
                            <span class="text-sm font-bold">{{ $address['label'] }}</span>
                            @if ($address['is_default'])
                                <span class="rounded-full bg-brand-50 px-2 py-0.5 text-[10px] font-bold text-brand-600">
                                    Default
                                </span>
                            @endif
                        </span>
                        <button type="button" class="text-[11px] font-semibold text-brand-500 underline underline-offset-2">
                            Edit
                        </button>
                    </div>

                    <p class="mt-2 text-xs">{{ $address['name'] }}</p>
                    @foreach ($address['lines'] as $line)
                        <p class="text-xs text-muted">{{ $line }}</p>
                    @endforeach
                    <p class="text-xs text-muted">{{ $address['phone'] }}</p>
                </li>
            @endforeach

            <li>
                <button type="button"
                        class="flex h-full w-full items-center justify-center gap-2 rounded-xl border border-dashed
                               border-line p-6 text-sm font-semibold text-brand-500 transition-colors hover:bg-canvas">
                    <x-ui.icon name="plus" :size="18" /> Add New Address
                </button>
            </li>
        </ul>

    {{-- ====================================================== payment methods --}}
    @elseif ($section === 'payment-methods')
        <p class="mt-1 text-sm text-muted">Saved ways to pay.</p>

        <ul class="mt-5 grid gap-3 sm:grid-cols-2">
            @foreach ($paymentMethods as $method)
                <li class="ab-card flex items-center gap-3 p-4">
                    <x-ui.icon name="card" :size="20" class="text-brand-500" />
                    <span class="flex-1 leading-tight">
                        <span class="block text-sm font-bold">{{ $method['label'] }}</span>
                        @if ($method['note'])
                            <span class="block text-[11px] text-muted">{{ $method['note'] }}</span>
                        @endif
                    </span>
                    <button type="button" class="text-[11px] font-semibold text-muted underline underline-offset-2">
                        Remove
                    </button>
                </li>
            @endforeach
        </ul>

    {{-- ======================================================== notifications --}}
    @elseif ($section === 'notifications')
        <p class="mt-1 text-sm text-muted">Updates on your orders, enquiries and offers.</p>

        <ul class="mt-5 space-y-2">
            @foreach ([
                ['box', 'Your vehicle is ready for delivery', 'Order ABZ20250905C001 · 2 hours ago', true],
                ['tag', 'New festival offer from TVS', 'Benefits up to ₹25,000 · Yesterday', true],
                ['doc', 'Your enquiry has been received', 'Bajaj RE · 27 Aug 2026', false],
                ['check-circle', 'Payment confirmed', '₹50,000 advance received · 05 Sep 2026', false],
            ] as [$icon, $title, $meta, $unread])
                <li class="ab-card flex items-start gap-3 p-4 {{ $unread ? 'bg-brand-50' : '' }}">
                    <x-ui.icon :name="$icon" :size="18" class="mt-0.5 shrink-0 text-brand-500" />
                    <span class="flex-1 leading-tight">
                        <span class="block text-sm font-semibold">{{ $title }}</span>
                        <span class="block text-[11px] text-muted">{{ $meta }}</span>
                    </span>
                    @if ($unread)
                        <span class="mt-1.5 h-2 w-2 shrink-0 rounded-full bg-brand-500" aria-label="Unread"></span>
                    @endif
                </li>
            @endforeach
        </ul>

    {{-- ================================================================ refer --}}
    @elseif ($section === 'refer')
        <p class="mt-1 text-sm text-muted">Invite other drivers and earn rewards on their purchase.</p>

        <div class="ab-card mt-5 bg-accent-50 p-6 text-center">
            <x-ui.icon name="users" :size="34" class="mx-auto text-warn" />
            <p class="mt-3 text-lg font-extrabold">Drive Together, Earn Rewards</p>
            <p class="mt-1 text-sm text-muted">Share your code. You both get benefits when they buy.</p>

            <div class="mx-auto mt-5 flex max-w-sm gap-2">
                <input type="text" value="SIVAM2026" readonly aria-label="Your referral code"
                       class="ab-field text-center font-bold tracking-widest">
                <button type="button" class="ab-btn ab-btn-primary px-4 text-xs">Copy</button>
            </div>

            <ul class="mx-auto mt-6 grid max-w-lg gap-3 sm:grid-cols-3">
                @foreach ([['share', 'Share your code'], ['users', 'Friend buys an auto'], ['tag', 'You both earn']] as $i => [$icon, $label])
                    <li class="rounded-lg bg-surface p-3">
                        <x-ui.icon :name="$icon" :size="18" class="mx-auto text-brand-500" />
                        <p class="mt-1.5 text-[11px] font-semibold">{{ $i + 1 }}. {{ $label }}</p>
                    </li>
                @endforeach
            </ul>
        </div>

    {{-- ============================================================== support --}}
    @elseif ($section === 'support')
        <p class="mt-1 text-sm text-muted">We are here Mon–Sat, {{ $site['contact']['hours'] }}.</p>

        <div class="mt-5 grid gap-4 lg:grid-cols-2">
            <div class="ab-card p-5">
                <h2 class="text-sm font-bold">Raise a Support Ticket</h2>

                <form @submit.prevent class="mt-4 space-y-3">
                    <div>
                        <label for="sup-topic" class="ab-label">Topic</label>
                        <select id="sup-topic" class="ab-field">
                            <option>Order or delivery</option>
                            <option>Payment or refund</option>
                            <option>Vehicle enquiry</option>
                            <option>Accessories</option>
                            <option>Something else</option>
                        </select>
                    </div>
                    <div>
                        <label for="sup-msg" class="ab-label">How can we help?</label>
                        <textarea id="sup-msg" rows="5" class="ab-field resize-none"
                                  placeholder="Describe your issue..."></textarea>
                    </div>
                    <button type="submit" class="ab-btn ab-btn-primary w-full text-xs">Submit Ticket</button>
                </form>
            </div>

            <div class="ab-card h-fit p-5">
                <h2 class="text-sm font-bold">Reach Us Directly</h2>
                <ul class="mt-4 space-y-3">
                    @foreach ([
                        ['phone-call', 'Call Us', $site['contact']['phone']],
                        ['whatsapp', 'WhatsApp', $site['contact']['phone']],
                        ['mail', 'Email', $site['contact']['email']],
                        ['pin', 'Showroom', $site['contact']['address']],
                    ] as [$icon, $label, $value])
                        <li class="flex items-start gap-2.5">
                            <x-ui.icon :name="$icon" :size="17" class="mt-0.5 shrink-0 text-brand-500" />
                            <span class="leading-tight">
                                <span class="block text-[11px] text-muted">{{ $label }}</span>
                                <span class="block text-sm font-bold">{{ $value }}</span>
                            </span>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>

    {{-- ============================================================== profile --}}
    @else
        <p class="mt-1 text-sm text-muted">Your personal details.</p>

        <div class="ab-card mt-5 max-w-2xl p-5">
            <form @submit.prevent class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label for="pr-name" class="ab-label">Full Name</label>
                    <input id="pr-name" type="text" value="{{ $account['user']['name'] }}" class="ab-field">
                </div>
                <div>
                    <label for="pr-email" class="ab-label">Email Address</label>
                    <input id="pr-email" type="email" value="{{ $account['user']['email'] }}" class="ab-field">
                </div>
                <div>
                    <label for="pr-phone" class="ab-label">Mobile Number</label>
                    <input id="pr-phone" type="tel" value="+91 90922 14143" class="ab-field">
                </div>
                <div>
                    <label for="pr-city" class="ab-label">City</label>
                    <input id="pr-city" type="text" value="Chennai" class="ab-field">
                </div>

                <button type="submit" class="ab-btn ab-btn-primary sm:col-span-2">Save Changes</button>
            </form>
        </div>
    @endif
</x-ui.account-shell>

@endsection
