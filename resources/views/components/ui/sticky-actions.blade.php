@props(['vehicle', 'site'])

{{--
    Sticky action bar: WhatsApp · Enquire Now · Call Seller · Book Test Drive.

    Mobile: full-width, fixed to the bottom edge. Desktop: a floating pill on
    the right. Lifts above the compare tray when that is open. The global
    WhatsApp FAB is hidden on this page by CSS (#vehicle-sticky-actions).
--}}

@php
    $text = rawurlencode("Hi, I am interested in {$vehicle['name']} (on-road ₹" . number_format($vehicle['on_road_price']) . " in {$vehicle['price_location']}). Please share the best offer and EMI options.");
    $wa = 'https://wa.me/' . $site['contact']['whatsapp'] . '?text=' . $text;
@endphp

<div id="vehicle-sticky-actions"
     x-data
     :class="$store.compare.count > 0 ? 'bottom-16 lg:bottom-24' : 'bottom-0 lg:bottom-6'"
     class="fixed inset-x-0 bottom-0 z-30 border-t border-line bg-surface/95 backdrop-blur transition-all
            lg:inset-x-auto lg:right-8 lg:w-auto lg:rounded-2xl lg:border"
     style="box-shadow: var(--shadow-rail)"
     role="region" aria-label="Quick actions">

    <div class="ab-container grid grid-cols-4 gap-2 py-2 lg:flex lg:items-center lg:gap-3 lg:px-4 lg:py-3">

        <div class="hidden min-w-0 pr-2 lg:block">
            <p class="truncate text-sm font-bold">{{ $vehicle['name'] }}</p>
            <p class="text-xs text-muted">₹{{ number_format($vehicle['on_road_price']) }} on-road · EMI from ₹{{ number_format($vehicle['emi_from']) }}*</p>
        </div>

        <a href="{{ $wa }}" target="_blank" rel="noopener"
           class="ab-btn flex-col gap-0.5 bg-[#25D366] px-2 py-2 text-[11px] text-white hover:bg-[#1ebe5b] lg:flex-row lg:gap-2 lg:px-4 lg:text-sm">
            <x-ui.icon name="whatsapp" :size="20" /> WhatsApp
        </a>

        <button type="button"
                @click="window.dispatchEvent(new CustomEvent('lead-open', { detail: { source: 'enquiry' } }))"
                class="ab-btn ab-btn-accent flex-col gap-0.5 px-2 py-2 text-[11px] lg:flex-row lg:gap-2 lg:px-4 lg:text-sm">
            <x-ui.icon name="message" :size="20" /> Enquire Now
        </button>

        <a href="tel:{{ $site['contact']['phone_e164'] }}"
           class="ab-btn ab-btn-primary flex-col gap-0.5 px-2 py-2 text-[11px] lg:flex-row lg:gap-2 lg:px-4 lg:text-sm">
            <x-ui.icon name="phone-call" :size="20" /> Call Seller
        </a>

        <button type="button"
                @click="window.dispatchEvent(new CustomEvent('lead-open', { detail: { source: 'test_drive' } }))"
                class="ab-btn ab-btn-outline flex-col gap-0.5 px-2 py-2 text-[11px] lg:flex-row lg:gap-2 lg:px-4 lg:text-sm">
            <x-ui.icon name="calendar" :size="20" /> <span class="whitespace-nowrap">Book Test Drive</span>
        </button>
    </div>
</div>
