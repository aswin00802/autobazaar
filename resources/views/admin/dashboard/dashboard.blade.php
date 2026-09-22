@extends('admin.layouts.app')
@section('title')
    Dashboard
@endsection

@push('css')
<style>
    .dash-hero {
        background: linear-gradient(120deg, rgba(var(--bs-primary-rgb), .14), rgba(var(--bs-primary-rgb), .03));
        border: 1px solid rgba(var(--bs-primary-rgb), .18);
    }
    .dash-tile {
        display: block;
        height: 100%;
        color: inherit;
        text-decoration: none;
        transition: transform .15s ease, box-shadow .15s ease, border-color .15s ease;
        border: 1px solid transparent;
    }
    a.dash-tile:hover,
    a.dash-tile:focus-visible {
        color: inherit;
        transform: translateY(-3px);
        box-shadow: 0 .5rem 1.25rem rgba(0, 0, 0, .12);
        border-color: rgba(var(--bs-primary-rgb), .35);
    }
    .dash-tile .dash-arrow {
        opacity: 0;
        transform: translateX(-4px);
        transition: opacity .15s ease, transform .15s ease;
    }
    a.dash-tile:hover .dash-arrow,
    a.dash-tile:focus-visible .dash-arrow {
        opacity: 1;
        transform: translateX(0);
    }
    .dash-section-title {
        font-size: .8125rem;
        letter-spacing: .06em;
        text-transform: uppercase;
    }
    .dash-attention {
        display: flex;
        align-items: center;
        gap: .75rem;
        padding: .75rem 1rem;
        border-radius: .5rem;
        color: inherit;
        text-decoration: none;
        background: var(--bs-body-bg);
        border: 1px solid var(--bs-border-color);
        transition: border-color .15s ease, background-color .15s ease;
    }
    a.dash-attention:hover {
        color: inherit;
        border-color: rgba(var(--bs-primary-rgb), .5);
        background: rgba(var(--bs-primary-rgb), .05);
    }
    /* Entrance: tiles rise in one after another. --i is the tile's position on the page. */
    @keyframes dashRise {
        from { opacity: 0; transform: translateY(12px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    .dash-animate {
        opacity: 0;
        animation: dashRise .38s cubic-bezier(.2, .7, .2, 1) forwards;
        animation-delay: calc(var(--i, 0) * 35ms);
    }
    /* A count that just went up gets a brief ring so it catches the eye. */
    @keyframes dashPulse {
        0%   { box-shadow: 0 0 0 0 rgba(var(--bs-danger-rgb), .45); }
        100% { box-shadow: 0 0 0 12px rgba(var(--bs-danger-rgb), 0); }
    }
    .dash-attention.is-bumped { animation: dashPulse 1s ease-out 2; border-color: rgba(var(--bs-danger-rgb), .6); }
    .dash-trend { font-size: .75rem; font-weight: 600; }
    .dash-live-dot {
        display: inline-block; width: .5rem; height: .5rem; border-radius: 50%;
        background: var(--bs-success); margin-right: .35rem; vertical-align: middle;
    }
    .dash-live-dot.is-stale { background: var(--bs-secondary-color); }
    /* Chart placeholder until ApexCharts paints. */
    @keyframes dashShimmer { from { background-position: 200% 0; } to { background-position: -200% 0; } }
    .dash-skeleton {
        height: 280px; border-radius: .5rem;
        background: linear-gradient(90deg, rgba(var(--bs-secondary-rgb), .08) 25%, rgba(var(--bs-secondary-rgb), .18) 37%, rgba(var(--bs-secondary-rgb), .08) 63%);
        background-size: 400% 100%;
        animation: dashShimmer 1.4s ease infinite;
    }
    @media (prefers-reduced-motion: reduce) {
        .dash-animate { opacity: 1; animation: none; }
        .dash-attention.is-bumped, .dash-skeleton { animation: none; }
        .dash-tile, .dash-tile .dash-arrow { transition: none; }
        a.dash-tile:hover, a.dash-tile:focus-visible { transform: none; }
    }
    .dash-row-link { cursor: pointer; }
    .dash-ellipsis {
        max-width: 220px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
</style>
@endpush

@section('content')
@php
    $hour = now()->hour;
    $greeting = $hour < 12 ? 'Good morning' : ($hour < 17 ? 'Good afternoon' : 'Good evening');
    $visibleAttention = collect($attention)->filter(fn ($item) => $item['value'] > 0 && (!$item['can'] || auth()->user()->can($item['can'])));
    $rideStatusColors = [
        'pending' => 'warning', 'scheduled' => 'info', 'accepted' => 'primary', 'arrived' => 'primary',
        'started' => 'primary', 'completed' => 'success', 'cancelled' => 'danger', 'rejected' => 'danger',
    ];
@endphp

{{-- Welcome + quick actions --}}
<div class="card dash-hero shadow-none mb-6">
    <div class="card-body d-flex flex-wrap align-items-center justify-content-between gap-4">
        <div>
            <h4 class="mb-1">{{ $greeting }}, {{ auth()->user()->name }}</h4>
            <p class="mb-0 text-body-secondary">{{ now()->format('l, d F Y') }} &middot; Here is what is happening across {{ getSetting('business_name', 'your business') }}.</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            @foreach ($quickActions as $action)
                @can($action['can'])
                    <a href="{{ route($action['route']) }}" class="btn btn-sm {{ $loop->first ? 'btn-primary' : 'btn-outline-primary' }} waves-effect">
                        <i class="icon-base ri {{ $action['icon'] }} icon-16px me-1"></i>{{ $action['label'] }}
                    </a>
                @endcan
            @endforeach
        </div>
    </div>
</div>

{{-- Needs attention --}}
<div class="card mb-6">
    <div class="card-header d-flex align-items-center justify-content-between pb-2">
        <h5 class="mb-0"><i class="icon-base ri ri-notification-3-line me-2 text-warning"></i>Needs Attention</h5>
        <div class="d-flex align-items-center gap-3">
            <small class="text-body-secondary d-none d-sm-inline" title="Refreshes automatically every 30 seconds">
                <span class="dash-live-dot" id="dashLiveDot"></span><span id="dashLiveText">Live</span>
            </small>
            <span class="badge bg-label-warning rounded-pill" id="dashAttentionTotal" @if ($visibleAttention->isEmpty()) hidden @endif>{{ number_format($visibleAttention->sum('value')) }} open</span>
        </div>
    </div>
    <div class="card-body">
        <p class="mb-0 text-body-secondary" id="dashAttentionEmpty" @if ($visibleAttention->isNotEmpty()) hidden @endif><i class="icon-base ri ri-checkbox-circle-line text-success me-1"></i>All caught up. Nothing is waiting on you right now.</p>
        <div class="row g-3" id="dashAttention" data-url="{{ route('dashboard.attention') }}">
            @foreach ($visibleAttention as $item)
                <div class="col-sm-6 col-xl-4 dash-animate" style="--i: {{ $loop->index }}">
                    <a href="{{ route($item['route']) }}" class="dash-attention" data-key="{{ $item['key'] }}" data-value="{{ $item['value'] }}">
                        <div class="avatar avatar-sm flex-shrink-0">
                            <div class="avatar-initial bg-label-{{ $item['color'] }} rounded">
                                <i class="icon-base ri {{ $item['icon'] }} icon-18px"></i>
                            </div>
                        </div>
                        <span class="flex-grow-1">{{ $item['label'] }}</span>
                        <span class="badge bg-{{ $item['color'] }} rounded-pill">{{ number_format($item['value']) }}</span>
                        <i class="icon-base ri ri-arrow-right-s-line text-body-secondary"></i>
                    </a>
                </div>
            @endforeach
        </div>
    </div>
</div>

{{-- Stat sections: every tile opens its list page --}}
@php $tileIndex = 0; @endphp
@foreach ($sections as $section)
    @php
        $cards = collect($section['cards']);
    @endphp
    <div class="d-flex align-items-center mb-3">
        <i class="icon-base ri {{ $section['icon'] }} me-2 text-primary"></i>
        <h6 class="dash-section-title mb-0 text-body-secondary">{{ $section['title'] }}</h6>
    </div>
    <div class="row g-4 mb-6">
        @foreach ($cards as $card)
            @php
                $clickable = !$card['can'] || auth()->user()->can($card['can']);
                $tag = $clickable ? 'a' : 'div';
            @endphp
            <div class="col-sm-6 col-xl-3 dash-animate" style="--i: {{ $tileIndex++ }}">
                <{{ $tag }} @if ($clickable) href="{{ route($card['route']) }}" title="Open {{ $card['label'] }}" @endif class="card dash-tile">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="me-1">
                                <p class="text-heading mb-1">{{ $card['label'] }}</p>
                                <div class="d-flex align-items-baseline flex-wrap gap-2">
                                    <h4 class="mb-0" data-count="{{ $card['value'] }}">{{ number_format($card['value']) }}</h4>
                                    @if (!empty($card['trend']))
                                        @php
                                            $trend = $card['trend'];
                                            $trendColor = ['up' => 'success', 'down' => 'danger', 'flat' => 'secondary'][$trend['direction']];
                                            $trendIcon = ['up' => 'ri-arrow-up-line', 'down' => 'ri-arrow-down-line', 'flat' => 'ri-subtract-line'][$trend['direction']];
                                            $trendText = $trend['percent'] === null ? 'new' : abs($trend['percent']) . '%';
                                        @endphp
                                        <span class="dash-trend text-{{ $trendColor }}" title="{{ $trend['current'] }} added in the last 7 days, {{ $trend['previous'] }} in the 7 days before">
                                            <i class="icon-base ri {{ $trendIcon }} icon-14px"></i>{{ $trendText }}
                                        </span>
                                    @endif
                                </div>
                                @if (!empty($card['trend']))
                                    <small class="text-body-secondary">+{{ number_format($card['trend']['current']) }} this week</small>
                                @endif
                            </div>
                            <div class="avatar">
                                <div class="avatar-initial bg-label-{{ $card['color'] }} rounded-3">
                                    <i class="icon-base ri {{ $card['icon'] }} icon-26px"></i>
                                </div>
                            </div>
                        </div>
                        @if ($clickable)
                            <small class="text-primary d-inline-flex align-items-center mt-3">
                                View details <i class="icon-base ri ri-arrow-right-line icon-14px ms-1 dash-arrow"></i>
                            </small>
                        @endif
                    </div>
                </{{ $tag }}>
            </div>
        @endforeach
    </div>
@endforeach

{{-- Trend + recent enquiries --}}
<div class="row g-6 mb-6">
    <div class="col-lg-7">
        <div class="card h-100">
            <div class="card-header d-flex align-items-center justify-content-between">
                <div>
                    <h5 class="mb-0">Last 7 Days</h5>
                    <small class="text-body-secondary">New registrations and enquiries per day</small>
                </div>
            </div>
            <div class="card-body">
                <div id="dashTrendChart" style="min-height: 280px;"><div class="dash-skeleton" aria-hidden="true"></div></div>
            </div>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="card h-100">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="mb-0">Recent Enquiries</h5>
                @can('auto_enquiry_list')
                    <a href="{{ route('enquiry-auto.list') }}" class="btn btn-sm btn-text-primary">View all</a>
                @endcan
            </div>
            <div class="card-body pt-0">
                @forelse ($recentEnquiries as $enquiry)
                    <div class="d-flex align-items-center py-2 @if (!$loop->last) border-bottom @endif">
                        <div class="avatar avatar-sm me-3 flex-shrink-0">
                            <div class="avatar-initial bg-label-success rounded-circle">
                                {{ strtoupper(substr($enquiry->user->name ?? 'U', 0, 1)) }}
                            </div>
                        </div>
                        <div class="flex-grow-1 overflow-hidden">
                            <h6 class="mb-0 text-truncate">{{ $enquiry->user->name ?? 'Unknown user' }}</h6>
                            <small class="text-body-secondary">{{ $enquiry->user->phone_number ?? 'No phone' }}</small>
                        </div>
                        <small class="text-body-secondary text-nowrap ms-2">{{ optional($enquiry->created_at)->diffForHumans() }}</small>
                    </div>
                @empty
                    <p class="mb-0 text-body-secondary">No enquiries yet.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

{{-- Recent rides + today's users --}}
<div class="row g-6">
    <div class="col-lg-7">
        <div class="card h-100">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="mb-0">Recent FairPrice Rides</h5>
                @can('fairprice_ride_list')
                    <a href="{{ route('fairprice.rides') }}" class="btn btn-sm btn-text-primary">View all</a>
                @endcan
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Customer</th>
                            <th>Pickup</th>
                            <th>Type</th>
                            <th>Fare</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($recentRides as $ride)
                            @php $canOpenRide = auth()->user()->can('fairprice_ride_list'); @endphp
                            <tr @if ($canOpenRide) class="dash-row-link" data-href="{{ route('fairprice.rides.show', $ride->id) }}" title="Open ride #{{ $ride->id }}" @endif>
                                <td>{{ $ride->id }}</td>
                                <td>{{ $ride->customer->name ?? $ride->customer->phone ?? '—' }}</td>
                                <td><div class="dash-ellipsis" title="{{ $ride->pickup }}">{{ $ride->pickup ?: '—' }}</div></td>
                                <td class="text-capitalize">{{ str_replace('_', ' ', $ride->booking_type ?: 'instant') }}</td>
                                <td>₹{{ number_format((float) $ride->estimated_fare, 2) }}</td>
                                <td><span class="badge bg-label-{{ $rideStatusColors[$ride->status] ?? 'secondary' }} text-capitalize">{{ $ride->status }}</span></td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center text-body-secondary py-4">No rides yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="card h-100">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="mb-0">Registered Today <span class="badge bg-label-primary rounded-pill ms-1">{{ $todayUsers->count() }}</span></h5>
                @can('user_list')
                    <a href="{{ route('user-management.users-list') }}" class="btn btn-sm btn-text-primary">View all</a>
                @endcan
            </div>
            <div class="card-body pt-0">
                @forelse ($todayUsers->take(6) as $todayUser)
                    <div class="d-flex align-items-center py-2 @if (!$loop->last) border-bottom @endif">
                        <div class="avatar avatar-sm me-3 flex-shrink-0">
                            <div class="avatar-initial bg-label-primary rounded-circle">
                                {{ strtoupper(substr($todayUser->name ?: 'U', 0, 1)) }}
                            </div>
                        </div>
                        <div class="flex-grow-1 overflow-hidden">
                            <h6 class="mb-0 text-truncate">{{ $todayUser->name ?: 'Unnamed user' }}</h6>
                            <small class="text-body-secondary">{{ $todayUser->phone_number }}</small>
                        </div>
                        <small class="text-body-secondary text-nowrap ms-2">{{ optional($todayUser->created_at)->format('h:i A') }}</small>
                    </div>
                @empty
                    <p class="mb-0 text-body-secondary">No new registrations today.</p>
                @endforelse
                @if ($todayUsers->count() > 6)
                    <small class="text-body-secondary d-block mt-2">and {{ $todayUsers->count() - 6 }} more today</small>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{asset('admin/assets/vendor/libs/apex-charts/apexcharts.js')}}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Whole ride row opens the ride.
        document.querySelectorAll('.dash-row-link').forEach(function (row) {
            row.addEventListener('click', function () {
                window.location.href = row.dataset.href;
            });
        });

        var reduceMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

        // Numbers count up from zero as the tiles arrive.
        if (!reduceMotion) {
            document.querySelectorAll('[data-count]').forEach(function (node) {
                var target = parseInt(node.dataset.count, 10) || 0;
                if (target <= 0) { return; }
                var duration = 700, start = null;
                node.textContent = '0';
                function tick(now) {
                    if (start === null) { start = now; }
                    var progress = Math.min((now - start) / duration, 1);
                    var eased = 1 - Math.pow(1 - progress, 3);
                    node.textContent = Math.round(target * eased).toLocaleString('en-IN');
                    if (progress < 1) { requestAnimationFrame(tick); }
                }
                requestAnimationFrame(tick);
            });
        }

        // Needs Attention refreshes itself; a count that rises gets a pulse.
        (function liveAttention() {
            var box = document.getElementById('dashAttention');
            if (!box || !window.fetch) { return; }

            var total = document.getElementById('dashAttentionTotal');
            var empty = document.getElementById('dashAttentionEmpty');
            var dot = document.getElementById('dashLiveDot');
            var text = document.getElementById('dashLiveText');

            function escapeHtml(value) {
                var div = document.createElement('div');
                div.textContent = value;
                return div.innerHTML;
            }

            function render(data) {
                var previous = {};
                box.querySelectorAll('.dash-attention').forEach(function (a) { previous[a.dataset.key] = parseInt(a.dataset.value, 10) || 0; });

                var open = data.items.filter(function (item) { return item.value > 0; });

                box.innerHTML = open.map(function (item) {
                    var bumped = previous[item.key] !== undefined && item.value > previous[item.key];
                    var isNew = previous[item.key] === undefined;
                    return '<div class="col-sm-6 col-xl-4">' +
                        '<a href="' + escapeHtml(item.url) + '" class="dash-attention' + ((bumped || isNew) ? ' is-bumped' : '') + '" data-key="' + escapeHtml(item.key) + '" data-value="' + item.value + '">' +
                        '<div class="avatar avatar-sm flex-shrink-0"><div class="avatar-initial bg-label-' + escapeHtml(item.color) + ' rounded"><i class="icon-base ri ' + escapeHtml(item.icon) + ' icon-18px"></i></div></div>' +
                        '<span class="flex-grow-1">' + escapeHtml(item.label) + '</span>' +
                        '<span class="badge bg-' + escapeHtml(item.color) + ' rounded-pill">' + item.value.toLocaleString('en-IN') + '</span>' +
                        '<i class="icon-base ri ri-arrow-right-s-line text-body-secondary"></i></a></div>';
                }).join('');

                var sum = open.reduce(function (carry, item) { return carry + item.value; }, 0);
                total.hidden = open.length === 0;
                total.textContent = sum.toLocaleString('en-IN') + ' open';
                empty.hidden = open.length > 0;
                dot.classList.remove('is-stale');
                text.textContent = 'Live · ' + data.as_of;
            }

            function refresh() {
                if (document.hidden) { return; } // no polling from background tabs
                fetch(box.dataset.url, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }, credentials: 'same-origin' })
                    .then(function (response) { if (!response.ok) { throw new Error(response.status); } return response.json(); })
                    .then(render)
                    .catch(function () { dot.classList.add('is-stale'); text.textContent = 'Paused'; });
            }

            setInterval(refresh, 30000);
            document.addEventListener('visibilitychange', function () { if (!document.hidden) { refresh(); } });
        })();

        var el = document.getElementById('dashTrendChart');
        if (!el || typeof ApexCharts === 'undefined') { return; }
        el.innerHTML = '';

        var css = getComputedStyle(document.documentElement);
        var primary = (css.getPropertyValue('--bs-primary') || '#8c57ff').trim();
        var success = (css.getPropertyValue('--bs-success') || '#56ca00').trim();
        var muted = (css.getPropertyValue('--bs-secondary-color') || '#8a8d93').trim();
        var border = (css.getPropertyValue('--bs-border-color') || '#e6e6e8').trim();

        new ApexCharts(el, {
            chart: { type: 'area', height: 280, toolbar: { show: false }, fontFamily: 'inherit', animations: { enabled: !reduceMotion } },
            series: [
                { name: 'Registrations', data: @json($chart['users']) },
                { name: 'Enquiries', data: @json($chart['enquiries']) }
            ],
            colors: [primary, success],
            stroke: { curve: 'smooth', width: 2 },
            fill: { type: 'gradient', gradient: { opacityFrom: .35, opacityTo: .02 } },
            dataLabels: { enabled: false },
            markers: { size: 3 },
            grid: { borderColor: border, strokeDashArray: 4 },
            legend: { position: 'top', horizontalAlign: 'right', labels: { colors: muted } },
            xaxis: {
                categories: @json($chart['labels']),
                labels: { style: { colors: muted } },
                axisBorder: { show: false }, axisTicks: { show: false }
            },
            yaxis: { min: 0, forceNiceScale: true, labels: { style: { colors: muted }, formatter: function (v) { return Math.round(v); } } },
            tooltip: { shared: true, intersect: false }
        }).render();
    });
</script>
@endpush
