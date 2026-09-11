@props(['title', 'lede' => null, 'breadcrumb' => [], 'tone' => 'light'])

<section class="{{ $tone === 'brand' ? 'bg-brand-700 text-white' : 'bg-gradient-to-br from-brand-50 to-canvas' }}">
    <div class="ab-container py-8 lg:py-10">
        @if ($breadcrumb)
            <x-ui.breadcrumb :items="$breadcrumb" class="mb-3" />
        @endif

        <h1 class="text-3xl font-extrabold tracking-tight sm:text-4xl" data-reveal>{{ $title }}</h1>

        @if ($lede)
            <p data-reveal class="mt-2 max-w-2xl text-sm {{ $tone === 'brand' ? 'text-white/80' : 'text-muted' }}">
                {{ $lede }}
            </p>
        @endif

        {{ $slot }}
    </div>
</section>
