@props(['account', 'active' => null])

{{-- Sidebar + content shell for every account page (my_orders.jpeg). --}}

<div class="ab-container py-6">
    <div class="grid gap-5 lg:grid-cols-12">

        {{-- ============================================================ sidebar --}}
        <aside class="lg:col-span-2" x-data="{ open: false }">
            <button type="button" @click="open = !open" class="ab-btn ab-btn-ghost mb-3 w-full lg:hidden">
                <x-ui.icon name="menu" :size="16" /> My Account
            </button>

            <div :class="open ? 'block' : 'hidden lg:block'" class="hidden lg:block">
                <div class="ab-card overflow-hidden">
                    <div class="flex items-center gap-3 border-b border-line p-4">
                        <span class="grid h-10 w-10 shrink-0 place-items-center rounded-full bg-brand-500 text-sm font-bold text-white">
                            {{ $account['user']['initial'] }}
                        </span>
                        <span class="min-w-0 leading-tight">
                            <span class="block truncate text-sm font-bold">{{ $account['user']['name'] }}</span>
                            <span class="block truncate text-[11px] text-muted">{{ $account['user']['email'] }}</span>
                            <a href="{{ route('site.account.section', 'profile') }}"
                               class="text-[11px] font-semibold text-brand-500 underline underline-offset-2">Edit Profile</a>
                        </span>
                    </div>

                    <nav aria-label="Account">
                        <ul class="py-1">
                            @foreach ($account['menu'] as $item)
                                @php
                                    $href = isset($item['param'])
                                        ? route($item['route'], $item['param'])
                                        : route($item['route']);
                                    $isActive = $active === $item['label'];
                                @endphp
                                <li>
                                    <a href="{{ $href }}"
                                       @if($isActive) aria-current="page" @endif
                                       class="flex items-center gap-2.5 px-4 py-2.5 text-xs font-semibold transition-colors
                                              {{ $isActive
                                                   ? 'bg-brand-500 text-white'
                                                   : 'text-ink-soft hover:bg-canvas' }}">
                                        <x-ui.icon :name="$item['icon']" :size="16" />
                                        {{ $item['label'] }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </nav>
                </div>

                {{-- Refer & Earn promo --}}
                <div class="ab-card mt-4 bg-accent-50 p-4">
                    <x-ui.icon name="tag" :size="22" class="text-warn" />
                    <p class="mt-2 text-sm font-extrabold leading-tight">Drive Together<br>Earn Rewards!</p>
                    <p class="mt-1 text-[11px] text-muted">
                        Refer your friends and earn exciting benefits.
                    </p>
                    <a href="{{ route('site.account.section', 'refer') }}"
                       class="ab-btn ab-btn-primary mt-3 w-full px-3 py-2 text-xs">
                        Refer Now <x-ui.icon name="arrow-right" :size="13" />
                    </a>
                </div>
            </div>
        </aside>

        {{-- ============================================================ content --}}
        <div class="lg:col-span-10">
            {{ $slot }}
        </div>
    </div>
</div>
