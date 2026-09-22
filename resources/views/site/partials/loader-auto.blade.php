{{--
    The AutoBazaar autorickshaw, side view facing right. Used by partials/loader.blade.php.
    What makes it read as an auto rather than a car: the tall soft-top hood, the
    open passenger side, the partition behind the driver, handlebar steering, and
    a nose that tapers down to one small front wheel.
--}}
<svg viewBox="0 0 128 85" xmlns="http://www.w3.org/2000/svg" focusable="false">
    {{-- soft-top hood (whole cabin silhouette), rear quarter a shade darker --}}
    <path d="M13 55C11 28 21 11 44 10h27c14 0 21 8 27 22l8 23z" fill="#FFC107"/>
    <path d="M13 55C11 30 19 14 38 11v44z" fill="#E6AD00"/>
    <path d="M13 55C11 28 21 11 44 10h27c14 0 21 8 27 22" fill="none" stroke="#BF9000" stroke-width="1.6" stroke-linecap="round"/>
    {{-- hood ribs --}}
    <path d="M24 22c3-5 8-8 14-10M18 34c1-4 2-7 4-10" fill="none" stroke="#BF9000" stroke-width="1.2" stroke-linecap="round" opacity=".7"/>

    {{-- open side: passenger bay + driver bay --}}
    <path d="M42 20h29c9 0 14 5 18 14l7 21H42z" fill="#F5F7F6"/>
    {{-- windshield --}}
    <path d="M90 33l7-1 8 23h-8z" fill="#CFE8FA"/>
    <path d="M97 32l8 23" stroke="#3F4744" stroke-width="1.6" stroke-linecap="round"/>

    {{-- passenger bench --}}
    <rect x="44" y="33" width="6" height="22" rx="2.5" fill="#6BB08F"/>
    <rect x="44" y="46" width="19" height="9" rx="3" fill="#9BCBB4"/>
    {{-- partition behind the driver --}}
    <rect x="65" y="20" width="3.5" height="35" fill="#E6AD00"/>

    {{-- driver + handlebar --}}
    <circle cx="80" cy="31" r="5.2" fill="#3F4744"/>
    <path d="M71 55c1-10 4-15 9-15s8 5 9 15z" fill="#3F4744"/>
    <path d="M85 44l9 3" stroke="#3F4744" stroke-width="2.6" stroke-linecap="round"/>
    <path d="M94 42v10" stroke="#1A1D1B" stroke-width="2.4" stroke-linecap="round"/>

    {{-- body tub, tapering into the nose --}}
    <path d="M9 55h98c8 2 13 8 13 16v4H9z" fill="#0B5D3B"/>
    <path d="M9 55h98c8 2 13 8 13 16" fill="none" stroke="#08462C" stroke-width="1.8"/>
    <path d="M9 61h105c2 1 3 3 4 4H9z" fill="#FFC107"/>
    {{-- headlight + rear lamp + footboard --}}
    <circle cx="116.5" cy="60" r="3.4" fill="#FFF8E1" stroke="#E6AD00" stroke-width="1.2"/>
    <rect x="9" y="57" width="3" height="5" rx="1" fill="#DC2626"/>
    <rect x="46" y="73" width="38" height="3" rx="1.5" fill="#08462C"/>

    {{-- mudguards --}}
    <path d="M23 75a13 13 0 0 1 26 0z" fill="#08462C"/>
    <path d="M92 76a11 11 0 0 1 22 0z" fill="#08462C"/>

    {{-- wheels: big at the back, small single wheel under the nose.
         Rotation centres are given in SVG units so every browser agrees. --}}
    <g class="ab-wheel" style="transform-origin: 36px 74px">
        <circle cx="36" cy="74" r="10" fill="#1A1D1B"/>
        <circle cx="36" cy="74" r="4.4" fill="#E2E7E4"/>
        <path d="M36 65v18M27 74h18M29.6 67.6l12.8 12.8M42.4 67.6L29.6 80.4" stroke="#6B7671" stroke-width="1.4"/>
    </g>
    <g class="ab-wheel" style="transform-origin: 103px 76px">
        <circle cx="103" cy="76" r="8" fill="#1A1D1B"/>
        <circle cx="103" cy="76" r="3.5" fill="#E2E7E4"/>
        <path d="M103 69v14M96 76h14M98 71l10 10M108 71L98 81" stroke="#6B7671" stroke-width="1.3"/>
    </g>
</svg>
