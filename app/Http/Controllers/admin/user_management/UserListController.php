<?php

namespace App\Http\Controllers\admin\user_management;

use App\Models\User;
use App\Models\Auto\Auto;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Crypt;

class UserListController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:user_list'])->only(['index']);
        $this->middleware(['permission:user_info'])->only(['user_info']);
    }
    public function index(Request $request)
    {
        $admin = User::where('phone_number','9751535189')->first();
        $playstore_test = User::where('phone_number','9094262603')->first();

        /*
         * One person is often several things at once — a driver who also buys
         * accessories and sells his old auto. So there is no "type" column to
         * set; what someone is, is worked out from what they have actually
         * done, and the counts come back as part of the same query rather than
         * one lookup per row.
         */
        $query = User::with(['userInfo', 'autoAreas'])
                ->withCount(['userInfo as is_driver', 'auto as listings_count', 'sparepartOrders as app_orders_count'])
                ->when($admin, fn ($q) => $q->where('id', '!=', $admin->id))
                ->when($playstore_test, fn ($q) => $q->where('id', '!=', $playstore_test->id))
                ->orderBy('created_at', 'desc');

        // Tabs across the top of the list.
        $show = $request->query('show');

        $query->when($show === 'drivers', fn ($q) => $q->has('userInfo'))
              ->when($show === 'sellers', fn ($q) => $q->has('auto'))
              ->when($show === 'buyers', fn ($q) => $q->has('sparepartOrders'))
              ->when($show === 'online', fn ($q) => $q->where('last_seen_at', '>=', now()->subMinutes(5)))
              ->when($show === 'inactive', fn ($q) => $q->where(fn ($w) => $w->whereNull('last_seen_at')
                                                                             ->orWhere('last_seen_at', '<', now()->subDays(90))));

        // Searching runs over every user, not just the page on screen.
        if ($search = trim((string) $request->query('q'))) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone_number', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhereHas('autoAreas', fn ($a) => $a->where('name', 'like', "%{$search}%"));
            });
        }

        // Shows every user in one page, with the table's own search and export,
        // like the rest of the admin lists (config/admin_lists.php, 0 = all).
        $perPage = (int) config('admin_lists.per_page.users', 0);
        $users = $perPage > 0 ? $query->paginate($perPage)->withQueryString() : $query->get();

        // Numbers for the tabs. One small query each, not one per row.
        $exclude = array_filter([$admin?->id, $playstore_test?->id]);
        $base = fn () => User::query()->whereNotIn('id', $exclude ?: [0]);

        $tabCounts = [
            'all'      => $base()->count(),
            'drivers'  => $base()->has('userInfo')->count(),
            'sellers'  => $base()->has('auto')->count(),
            'buyers'   => $base()->has('sparepartOrders')->count(),
            'online'   => $base()->where('last_seen_at', '>=', now()->subMinutes(5))->count(),
            'inactive' => $base()->where(fn ($w) => $w->whereNull('last_seen_at')
                                                      ->orWhere('last_seen_at', '<', now()->subDays(90)))->count(),
        ];

        return view('admin.user_management.userlist',
            compact('admin','playstore_test','users','tabCounts') + ['show' => $show ?: 'all']);
    }

    public function user_info($id)
    {
        $userId = Crypt::decryptString($id);
        $user = User::findOrFail($userId);
        $autos = Auto::where('user_id',$userId)->get();
        return view('admin.user_management.user_info',compact('user','autos'));
    }
}
