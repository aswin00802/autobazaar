<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Auto\Auto;
use App\Models\Auto\Quotation;
use App\Models\Auto\SoldAuto;
use App\Models\DriverRequest;
use App\Models\Enquiry;
use App\Models\RideRequest;
use App\Models\Services\EmergencyService;
use App\Models\Services\RtoService;
use App\Models\Shop\Order;
use App\Models\SosAlert;
use App\Models\spareparts\Product;
use App\Models\spareparts\SparepartOrder;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    /** Tile counts + chart: ~30 queries, so they are shared between admins for a minute. */
    private const STATS_CACHE_KEY = 'admin_dashboard_stats';
    private const STATS_CACHE_SECONDS = 60;

    /** The attention strip is polled by open dashboards, so it refreshes faster. */
    private const ATTENTION_CACHE_KEY = 'admin_dashboard_attention';
    private const ATTENTION_CACHE_SECONDS = 20;

    public function __construct()
    {
        $this->middleware(['permission:dashboard']);
    }

    public function index()
    {
        $stats     = Cache::remember(self::STATS_CACHE_KEY, self::STATS_CACHE_SECONDS, fn () => $this->buildStats());
        $attention = $this->attentionItems();

        $sections = $stats['sections'];
        $chart    = $stats['chart'];

        $quickActions = [
            ['label' => 'Add Used Auto', 'icon' => 'ri-add-circle-line', 'route' => 'auto-management.used-auto.create', 'can' => 'add_used_auto'],
            ['label' => 'Add New Auto', 'icon' => 'ri-add-circle-line', 'route' => 'auto-management.new-auto.create', 'can' => 'add_new_auto'],
            ['label' => 'Add Product', 'icon' => 'ri-box-3-line', 'route' => 'spare-parts.product.create', 'can' => 'add_sparepart_product'],
            ['label' => 'New POS Quotation', 'icon' => 'ri-file-add-line', 'route' => 'pos-quotation.create', 'can' => 'pos_quotation'],
            ['label' => 'Create Event', 'icon' => 'ri-calendar-event-line', 'route' => 'events.create', 'can' => 'add_events_announce'],
            ['label' => 'Fare Settings', 'icon' => 'ri-money-rupee-circle-line', 'route' => 'fairprice.fare-settings', 'can' => 'fairprice_fare_setting'],
        ];

        // Recent activity is three small indexed queries, so it stays live.
        $today = Carbon::today();

        $todayUsers = $this->safe(fn () => User::whereDate('created_at', $today)->where('status', 1)
            ->latest()->get(['id', 'name', 'phone_number', 'created_at']), collect());

        $recentEnquiries = $this->safe(fn () => Enquiry::with(['user:id,name,phone_number'])
            ->latest()->limit(6)->get(), collect());

        $recentRides = $this->safe(fn () => RideRequest::with(['customer:id,name,phone'])
            ->latest()->limit(6)->get(), collect());

        return view('admin.dashboard.dashboard', compact(
            'attention', 'sections', 'quickActions', 'chart', 'todayUsers', 'recentEnquiries', 'recentRides'
        ));
    }

    /**
     * Polled by the dashboard every 30s so the "Needs Attention" strip stays live.
     * Only items the signed-in admin is allowed to open are returned.
     */
    public function attention()
    {
        $user = auth()->user();

        $items = collect($this->attentionItems())
            ->filter(fn ($item) => ! $item['can'] || $user->can($item['can']))
            ->map(fn ($item) => [
                'key'   => $item['key'],
                'label' => $item['label'],
                'value' => $item['value'],
                'icon'  => $item['icon'],
                'color' => $item['color'],
                'url'   => route($item['route']),
            ])
            ->values();

        return response()->json([
            'items' => $items,
            'total' => $items->sum('value'),
            'as_of' => now()->format('h:i:s A'),
        ]);
    }

    /**
     * Items waiting on an admin. The view only shows the non-zero ones.
     */
    private function attentionItems(): array
    {
        return Cache::remember(self::ATTENTION_CACHE_KEY, self::ATTENTION_CACHE_SECONDS, fn () => [
            ['key' => 'pending_autos'] + $this->card('Autos pending approval', fn () => Auto::where('auto_status', 'pending')->count(),
                'ri-time-line', 'warning', 'user-management.users-post-auto-list', 'user_post_auto_list'),
            ['key' => 'app_orders'] + $this->card('Pending app orders', fn () => SparepartOrder::where('order_status', 'pending')->count(),
                'ri-shopping-bag-3-line', 'info', 'spare-parts.orders.pending', 'sparepart_pendingorders'),
            ['key' => 'shop_orders'] + $this->card('Open shop orders', fn () => Order::whereNotIn('order_status', ['delivered', 'cancelled'])->count(),
                'ri-store-2-line', 'primary', 'ecommerce.orders', 'ecommerce_orders'),
            ['key' => 'driver_requests'] + $this->card('Driver requests', fn () => DriverRequest::where('status', 0)->count(),
                'ri-steering-2-line', 'primary', 'driver.request.list', 'auto_driver_list'),
            ['key' => 'emergency'] + $this->card('Emergency requests', fn () => EmergencyService::where('status', 'requested')->count() + SosAlert::where('status', 'pending')->count(),
                'ri-alarm-warning-line', 'danger', 'emergency.request.list', 'auto_emergency_list'),
            ['key' => 'rto'] + $this->card('RTO requests', fn () => RtoService::where('status', 'requested')->count(),
                'ri-file-list-3-line', 'secondary', 'services.rto', 'rto'),
        ]);
    }

    /**
     * Everything that is identical for every admin: plain arrays only, so it caches cleanly.
     */
    private function buildStats(): array
    {
        $today = Carbon::today();
        $month = Carbon::now()->month;
        $year  = Carbon::now()->year;

        $activeRideStatuses = ['pending', 'scheduled', 'accepted', 'arrived', 'started'];

        $sections = [
            [
                'title' => 'Users',
                'icon'  => 'ri-group-line',
                'cards' => [
                    $this->card('Total Users', fn () => User::count(), 'ri-group-line', 'primary', 'user-management.users-list', 'user_list',
                        $this->weeklyTrend(fn () => User::query())),
                    $this->card('Active Users', fn () => User::where('status', 1)->count(), 'ri-user-follow-line', 'success', 'user-management.users-list', 'user_list'),
                    $this->card('Registered Today', fn () => User::whereDate('created_at', $today)->count(), 'ri-user-add-line', 'info', 'user-management.users-list', 'user_list'),
                    $this->card('Registered This Month', fn () => User::whereMonth('created_at', $month)->whereYear('created_at', $year)->count(), 'ri-calendar-check-line', 'warning', 'user-management.users-list', 'user_list'),
                ],
            ],
            [
                'title' => 'Listings',
                'icon'  => 'ri-roadster-line',
                'cards' => [
                    $this->card('Active Autos', fn () => Auto::whereIn('auto_usage_status', ['used_auto', 'new_auto'])->where('auto_status', 'active')->count(), 'ri-e-bike-2-line', 'primary', 'auto-management.used-auto', 'used_auto'),
                    $this->card('Used Autos', fn () => Auto::where('auto_usage_status', 'used_auto')->where('auto_status', 'active')->count(), 'ri-roadster-line', 'success', 'auto-management.used-auto', 'used_auto'),
                    $this->card('New Autos', fn () => Auto::where('auto_usage_status', 'new_auto')->where('auto_status', 'active')->count(), 'ri-sparkling-line', 'info', 'auto-management.new-auto', 'new_auto'),
                    $this->card('Sold Autos', fn () => SoldAuto::count(), 'ri-hand-coin-line', 'warning', 'sold-auto.list', 'solid_autos_list'),
                    $this->card('Private Cargo Autos', fn () => Auto::where('auto_usage_status', 'private_cargo')->where('auto_status', 'active')->count(), 'ri-truck-line', 'secondary', 'auto-management.private-cargo-auto', 'private_cargo_auto'),
                    $this->card('Bajaj ReFinance Autos', fn () => Auto::where('auto_usage_status', 'bajaj_refinance')->where('auto_status', 'active')->count(), 'ri-bank-line', 'danger', 'auto-management.bajaj-refinance-auto', 'bajaj_refinance_auto'),
                    $this->card('Total Autos', fn () => Auto::count(), 'ri-stack-line', 'primary', 'auto-management.used-auto', 'used_auto',
                        $this->weeklyTrend(fn () => Auto::query())),
                ],
            ],
            [
                'title' => 'Leads',
                'icon'  => 'ri-question-answer-line',
                'cards' => [
                    $this->card('Total Enquiries', fn () => Enquiry::count(), 'ri-question-answer-line', 'success', 'enquiry-auto.list', 'auto_enquiry_list',
                        $this->weeklyTrend(fn () => Enquiry::query())),
                    $this->card('Enquiries This Month', fn () => Enquiry::whereMonth('created_at', $month)->whereYear('created_at', $year)->count(), 'ri-chat-new-line', 'warning', 'enquiry-auto.list', 'auto_enquiry_list'),
                    $this->card('Quotation Requests', fn () => Quotation::count(), 'ri-file-text-line', 'info', 'quotation-list', 'quotation',
                        $this->weeklyTrend(fn () => Quotation::query())),
                ],
            ],
            [
                'title' => 'FairPrice Rides',
                'icon'  => 'ri-taxi-line',
                'cards' => [
                    $this->card('Rides Today', fn () => RideRequest::whereDate('created_at', $today)->count(), 'ri-map-pin-time-line', 'info', 'fairprice.rides', 'fairprice_ride_list'),
                    $this->card('Active Rides', fn () => RideRequest::whereIn('status', $activeRideStatuses)->count(), 'ri-route-line', 'primary', 'fairprice.rides', 'fairprice_ride_list'),
                    $this->card('Completed Rides', fn () => RideRequest::where('status', 'completed')->count(), 'ri-checkbox-circle-line', 'success', 'fairprice.rides', 'fairprice_ride_list'),
                    $this->card('Total Rides', fn () => RideRequest::count(), 'ri-taxi-line', 'warning', 'fairprice.rides', 'fairprice_ride_list',
                        $this->weeklyTrend(fn () => RideRequest::query())),
                ],
            ],
            [
                'title' => 'Store',
                'icon'  => 'ri-store-2-line',
                'cards' => [
                    $this->card('Products', fn () => Product::count(), 'ri-box-3-line', 'primary', 'spare-parts.product', 'sparepart_product'),
                    $this->card('Shop Orders', fn () => Order::count(), 'ri-shopping-cart-2-line', 'success', 'ecommerce.orders', 'ecommerce_orders',
                        $this->weeklyTrend(fn () => Order::query())),
                    $this->card('App Orders', fn () => SparepartOrder::count(), 'ri-smartphone-line', 'info', 'spare-parts.orders.pending', 'sparepart_pendingorders',
                        $this->weeklyTrend(fn () => SparepartOrder::query())),
                ],
            ],
        ];

        // Last 7 days: registrations vs enquiries.
        $days = collect(range(6, 0))->map(fn ($i) => Carbon::today()->subDays($i));
        $from = $days->first()->copy()->startOfDay();

        $userByDay = $this->safe(fn () => User::where('created_at', '>=', $from)
            ->selectRaw('DATE(created_at) d, COUNT(*) c')->groupBy('d')->pluck('c', 'd')->all(), []);
        $enquiryByDay = $this->safe(fn () => Enquiry::where('created_at', '>=', $from)
            ->selectRaw('DATE(created_at) d, COUNT(*) c')->groupBy('d')->pluck('c', 'd')->all(), []);

        $chart = [
            'labels'    => $days->map(fn ($d) => $d->format('d M'))->values()->all(),
            'users'     => $days->map(fn ($d) => (int) ($userByDay[$d->toDateString()] ?? 0))->values()->all(),
            'enquiries' => $days->map(fn ($d) => (int) ($enquiryByDay[$d->toDateString()] ?? 0))->values()->all(),
        ];

        return compact('sections', 'chart');
    }

    /**
     * New records in the last 7 days against the 7 days before that.
     * Returns null when there is nothing to compare, so the tile shows no badge.
     */
    private function weeklyTrend(callable $query): ?array
    {
        return $this->safe(function () use ($query) {
            $now = Carbon::now();

            $current  = $query()->where('created_at', '>=', $now->copy()->subDays(7))->count();
            $previous = $query()->whereBetween('created_at', [$now->copy()->subDays(14), $now->copy()->subDays(7)])->count();

            if ($current === 0 && $previous === 0) {
                return null;
            }

            $percent = $previous > 0 ? (int) round((($current - $previous) / $previous) * 100) : null;

            return [
                'current'   => $current,
                'previous'  => $previous,
                'percent'   => $percent,           // null = no earlier data, show "new" instead of a %
                'direction' => $current > $previous ? 'up' : ($current < $previous ? 'down' : 'flat'),
            ];
        }, null);
    }

    /**
     * One dashboard tile: a count plus where clicking it goes.
     * A null $can means the link needs no specific permission.
     */
    private function card(string $label, callable $count, string $icon, string $color, string $route, ?string $can, ?array $trend = null): array
    {
        return [
            'label' => $label,
            'value' => (int) $this->safe($count),
            'icon'  => $icon,
            'color' => $color,
            'route' => $route,
            'can'   => $can,
            'trend' => $trend,
        ];
    }

    /**
     * Run a dashboard query without letting one broken table take the page down.
     */
    private function safe(callable $query, $default = 0)
    {
        try {
            return $query();
        } catch (\Throwable $e) {
            Log::warning('dashboard.query_failed', ['message' => $e->getMessage()]);

            return $default;
        }
    }
}
