@props(['vehicle'])

<div {{ $attributes->merge(['class' => 'grid gap-4 lg:grid-cols-2']) }}>

    <div class="ab-card p-5">
        <h3 class="mb-3 flex items-center gap-2 text-base font-bold">
            <x-ui.icon name="thumbs-up" :size="18" class="text-success" />
            Best Suitable For
        </h3>

        <ul class="flex flex-wrap gap-2">
            @foreach ($vehicle['suitable_for'] as $tag)
                <li class="rounded-full bg-brand-50 px-3 py-1.5 text-xs font-semibold text-brand-600">
                    {{ $tag }}
                </li>
            @endforeach
        </ul>
    </div>

    <div class="ab-card p-5">
        <h3 class="mb-3 flex items-center gap-2 text-base font-bold">
            <x-ui.icon name="thumbs-down" :size="18" class="text-danger" />
            Not Recommended For
        </h3>

        <ul class="flex flex-wrap gap-2">
            @foreach ($vehicle['not_recommended_for'] as $tag)
                <li class="rounded-full bg-red-50 px-3 py-1.5 text-xs font-semibold text-danger">
                    {{ $tag }}
                </li>
            @endforeach
        </ul>
    </div>
</div>
