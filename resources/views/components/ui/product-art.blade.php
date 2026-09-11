@props(['art' => 'grid', 'class' => ''])

{{--
    Accessory artwork placeholder.

    The project ships no photography for accessories, so rather than render
    broken <img> tags each product gets a simple line drawing keyed off its
    `art` value. These are placeholders — swap for real product photography
    when the client supplies it.
--}}

@php
    $shapes = [
        'mat' => '<rect x="10" y="18" width="34" height="28" rx="3"/><rect x="46" y="18" width="24" height="28" rx="3"/><path d="M16 26h22M16 32h22M16 38h22M52 26h12M52 32h12M52 38h12"/>',
        'seat' => '<path d="M22 46V24a8 8 0 0 1 8-8h6a8 8 0 0 1 8 8v22"/><rect x="18" y="44" width="34" height="12" rx="4"/><path d="M30 22v18M40 22v18"/>',
        'curtain' => '<path d="M16 14h38v40H16z"/><path d="M22 14v40M28 14v40M34 14v40M40 14v40M46 14v40"/>',
        'cover' => '<path d="M12 42c0-12 8-22 23-22s23 10 23 22"/><path d="M12 42h46v6H12z"/><path d="M24 22l-3 20M46 22l3 20"/>',
        'holder' => '<rect x="26" y="12" width="18" height="30" rx="3"/><path d="M31 46h8M35 42v10M26 22h-6M44 22h6"/>',
        'led' => '<rect x="12" y="26" width="46" height="14" rx="3"/><circle cx="22" cy="33" r="3.5"/><circle cx="32" cy="33" r="3.5"/><circle cx="42" cy="33" r="3.5"/><circle cx="52" cy="33" r="3.5"/><path d="M22 20v-5M42 20v-5"/>',
        'horn' => '<path d="M18 26v14l14 8V18l-14 8Z"/><path d="M32 24c8 2 12 5 12 9s-4 7-12 9"/><path d="M12 28h6v10h-6z"/>',
        'mirror' => '<ellipse cx="26" cy="26" rx="12" ry="9"/><path d="M26 35v10M20 48h12"/><ellipse cx="50" cy="34" rx="9" ry="7"/><path d="M50 41v7"/>',
        'shield' => '<path d="M35 12 18 19v13c0 10 7 18 17 22 10-4 17-12 17-22V19l-17-7Z"/><path d="m28 33 5 5 10-10"/>',
        'spray' => '<rect x="22" y="22" width="16" height="30" rx="3"/><path d="M26 22v-6h8v6M38 28h8M46 24v8"/><rect x="46" y="30" width="12" height="22" rx="3"/>',
        'lock' => '<rect x="20" y="30" width="30" height="22" rx="4"/><path d="M27 30v-6a8 8 0 0 1 16 0v6"/><circle cx="35" cy="40" r="3"/>',
        'grip' => '<rect x="14" y="28" width="18" height="10" rx="5"/><rect x="38" y="28" width="18" height="10" rx="5"/><path d="M32 33h6"/><path d="M18 28v-4M24 28v-4M46 28v-4M52 28v-4"/>',
        'cushion' => '<rect x="16" y="24" width="38" height="22" rx="8"/><path d="M24 24v22M35 24v22M46 24v22"/>',
        'firstaid' => '<rect x="14" y="22" width="42" height="28" rx="4"/><path d="M28 22v-4h14v4M35 30v12M29 36h12"/>',
        'filter' => '<rect x="20" y="16" width="12" height="34" rx="3"/><rect x="36" y="20" width="10" height="26" rx="3"/><rect x="50" y="24" width="8" height="18" rx="3"/>',
        'grid' => '<rect x="16" y="16" width="16" height="16" rx="3"/><rect x="38" y="16" width="16" height="16" rx="3"/><rect x="16" y="38" width="16" height="16" rx="3"/><rect x="38" y="38" width="16" height="16" rx="3"/>',
    ];

    $shape = $shapes[$art] ?? $shapes['grid'];
@endphp

<svg viewBox="0 0 70 68" fill="none" stroke="currentColor" stroke-width="2"
     stroke-linecap="round" stroke-linejoin="round"
     role="img" aria-label="Product illustration placeholder"
     {{ $attributes->merge(['class' => 'text-ink-soft/45']) }}>
    {!! $shape !!}
</svg>
