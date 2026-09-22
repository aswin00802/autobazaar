<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Auto\Auto;
use App\Models\RideRequest;
use App\Models\Shop\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;

/**
 * Top-bar search across users, autos, rides and shop orders.
 * Each group is only searched when the admin has that module's permission,
 * and every result links to a page they are allowed to open.
 */
class SearchController extends Controller
{
    private const PER_GROUP = 5;

    /** auto_usage_status => [detail route, permission] */
    private const AUTO_ROUTES = [
        'used_auto'       => ['auto-management.used-auto.view-details', 'view_details_used_auto'],
        'new_auto'        => ['auto-management.new-auto.view-details', 'view_details_new_auto'],
        'private_cargo'   => ['auto-management.private-cargo-auto.view-details', 'view_details_private_cargo_auto'],
        'bajaj_refinance' => ['auto-management.bajaj-refinance-auto.view-details', 'view_details_bajaj_refinance_auto'],
    ];

    public function index(Request $request)
    {
        $term = trim((string) $request->query('q', ''));

        if (mb_strlen($term) < 2) {
            return response()->json(['groups' => []]);
        }

        $term = mb_substr($term, 0, 60);
        $like = '%' . addcslashes($term, '%_\\') . '%';
        $user = $request->user();

        $groups = array_values(array_filter([
            $user->can('user_info') ? $this->users($like) : null,
            $this->autos($like, $user),
            $user->can('fairprice_ride_list') ? $this->rides($term, $like) : null,
            $user->can('ecommerce_orders') ? $this->orders($like) : null,
        ], fn ($group) => $group && count($group['items'])));

        return response()->json(['groups' => $groups]);
    }

    private function users(string $like): ?array
    {
        return $this->group('Users', 'ri-user-line', fn () => User::query()
            ->where(fn ($q) => $q->where('name', 'like', $like)
                ->orWhere('phone_number', 'like', $like)
                ->orWhere('email', 'like', $like))
            ->latest('id')->limit(self::PER_GROUP)
            ->get(['id', 'name', 'phone_number', 'status'])
            ->map(fn ($u) => [
                'title' => $u->name ?: 'Unnamed user',
                'meta'  => trim(($u->phone_number ?: 'No phone') . ' · ' . ((int) $u->status === 1 ? 'Active' : 'Inactive')),
                'url'   => route('user-management.users-info', Crypt::encryptString((string) $u->id)),
            ])->all());
    }

    private function autos(string $like, $user): ?array
    {
        $allowed = array_keys(array_filter(self::AUTO_ROUTES, fn ($route) => $user->can($route[1])));

        if (! $allowed) {
            return null;
        }

        return $this->group('Autos', 'ri-roadster-line', fn () => Auto::query()
            ->whereIn('auto_usage_status', $allowed)
            ->where('auto_status', '!=', 'deleted')
            ->where(fn ($q) => $q->where('auto_unique_id', 'like', $like)
                ->orWhere('registration_number', 'like', $like)
                ->orWhere('specific_model', 'like', $like)
                ->orWhere('name', 'like', $like)
                ->orWhere('mobile_number', 'like', $like))
            ->latest('id')->limit(self::PER_GROUP)
            ->get(['id', 'auto_unique_id', 'registration_number', 'specific_model', 'auto_usage_status', 'auto_status'])
            ->map(fn ($a) => [
                'title' => trim(($a->auto_unique_id ?: '#' . $a->id) . ' ' . ($a->specific_model ?: '')),
                'meta'  => trim(($a->registration_number ?: 'No reg. number') . ' · ' . str_replace('_', ' ', $a->auto_usage_status) . ' · ' . $a->auto_status),
                'url'   => route(self::AUTO_ROUTES[$a->auto_usage_status][0], Crypt::encryptString((string) $a->id)),
            ])->all());
    }

    private function rides(string $term, string $like): ?array
    {
        return $this->group('FairPrice Rides', 'ri-taxi-line', fn () => RideRequest::query()
            ->with('customer:id,name,phone')
            ->where(function ($q) use ($term, $like) {
                if (ctype_digit($term)) {
                    $q->where('id', (int) $term);
                }
                $q->orWhere('pickup', 'like', $like)
                    ->orWhereHas('customer', fn ($c) => $c->where('phone', 'like', $like)->orWhere('name', 'like', $like));
            })
            ->latest('id')->limit(self::PER_GROUP)
            ->get(['id', 'customer_id', 'pickup', 'status', 'booking_type'])
            ->map(fn ($r) => [
                'title' => 'Ride #' . $r->id . ' · ' . ($r->customer->name ?? $r->customer->phone ?? 'Customer'),
                'meta'  => trim(str_replace('_', ' ', $r->booking_type ?: 'instant') . ' · ' . $r->status . ($r->pickup ? ' · ' . mb_strimwidth($r->pickup, 0, 40, '…') : '')),
                'url'   => route('fairprice.rides.show', $r->id),
            ])->all());
    }

    private function orders(string $like): ?array
    {
        return $this->group('Shop Orders', 'ri-shopping-cart-2-line', fn () => Order::query()
            ->where(fn ($q) => $q->where('order_number', 'like', $like)
                ->orWhere('shipping_mobile', 'like', $like)
                ->orWhere('shipping_name', 'like', $like))
            ->latest('id')->limit(self::PER_GROUP)
            ->get(['id', 'order_number', 'shipping_name', 'total_amount', 'order_status'])
            ->map(fn ($o) => [
                'title' => ($o->order_number ?: 'Order #' . $o->id) . ' · ' . ($o->shipping_name ?: 'Customer'),
                'meta'  => '₹' . number_format((float) $o->total_amount, 2) . ' · ' . $o->order_status,
                'url'   => route('ecommerce.orders.view', $o->id),
            ])->all());
    }

    /**
     * A failing group (missing table/column on one server) is skipped, not fatal.
     */
    private function group(string $label, string $icon, callable $items): ?array
    {
        try {
            return ['label' => $label, 'icon' => $icon, 'items' => $items()];
        } catch (\Throwable $e) {
            Log::warning('admin_search.group_failed', ['group' => $label, 'message' => $e->getMessage()]);

            return null;
        }
    }
}
