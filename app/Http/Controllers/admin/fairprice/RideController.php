<?php

namespace App\Http\Controllers\admin\fairprice;

use App\Http\Controllers\Controller;
use App\Models\RideRequest;
use App\Models\User;
use App\Models\Customer;
use Illuminate\Http\Request;

class RideController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:fairprice_ride_list'])->only(['index', 'show']);
    }
    public function index(Request $request)
    {
        $rides = RideRequest::with(['customer:id,name,phone', 'driver:id,name,phone_number'])
            ->when($request->filled('customer_id'), function ($query) use ($request) {
                $query->where('customer_id', $request->customer_id);
            })
            ->when($request->filled('driver_id'), function ($query) use ($request) {
                $query->where('driver_id', $request->driver_id);
            })
            ->latest('id')
            ->get();

        $filterLabel = null;
        if ($request->filled('customer_id')) {
            $customer = Customer::select('id', 'name')->find($request->customer_id);
            $filterLabel = 'Customer: ' . ($customer->name ?? 'N/A');
        } elseif ($request->filled('driver_id')) {
            $driver = User::select('id', 'name')->find($request->driver_id);
            $filterLabel = 'Driver: ' . ($driver->name ?? 'N/A');
        }

        return view('admin.fairprice.rides.index', [
            'rides' => $rides,
            'historyType' => $request->input('history_type'),
            'filterLabel' => $filterLabel,
            'isFiltered' => $request->filled('customer_id') || $request->filled('driver_id'),
        ]);
    }

    public function show($id)
    {
        $ride = RideRequest::with(['customer', 'driver.userInfo'])
            ->findOrFail($id);

        $customerHistoryCount = RideRequest::where('customer_id', $ride->customer_id)->count();
        $driverHistoryCount = $ride->driver_id
            ? RideRequest::where('driver_id', $ride->driver_id)->count()
            : 0;

        return view('admin.fairprice.rides.show', compact('ride', 'customerHistoryCount', 'driverHistoryCount'));
    }
}
