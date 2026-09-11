@props(['contact'])

{{-- Floating WhatsApp CTA — present on every reference screen. --}}

{{-- Lifts clear of the compare tray when it is on screen --}}
<a href="https://wa.me/{{ $contact['whatsapp'] }}" target="_blank" rel="noopener"
   x-data
   :class="$store.compare.count > 0 ? 'bottom-24' : 'bottom-5'"
   class="group fixed bottom-5 right-5 z-40 flex items-center gap-3 rounded-full bg-[#25D366] py-3 pl-3.5 pr-4
          text-white shadow-lg transition-all hover:scale-105 sm:rounded-2xl sm:py-3 sm:pl-4">
    <x-ui.icon name="whatsapp" :size="26" />
    <span class="hidden leading-tight sm:block">
        <span class="block text-[11px] opacity-90">Need Help? Chat on WhatsApp</span>
        <span class="block text-sm font-bold">{{ $contact['phone'] }}</span>
    </span>
    <span class="sr-only sm:hidden">Chat on WhatsApp {{ $contact['phone'] }}</span>
</a>
