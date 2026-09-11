<?php

namespace App\Http\Controllers\admin\fairprice;

use App\Http\Controllers\Controller;
use App\Models\FairpriceFareSetting;
use Illuminate\Http\Request;

class FareSettingController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:fairprice_fare_setting'])->only(['index', 'update']);
    }

    public function index()
    {
        $settings = FairpriceFareSetting::where('is_active', 1)->latest('id')->first()
            ?? FairpriceFareSetting::latest('id')->first();

        return view('admin.fairprice.fare_settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'trip_per_km_rate' => 'required|numeric|min:0',
            'pickup_per_km_rate' => 'required|numeric|min:0',
            'free_pickup_km' => 'required|numeric|min:0',
            'base_fare' => 'required|numeric|min:0',
            'base_km' => 'required|numeric|min:0',
            'passenger_1_base_fare' => 'required|numeric|min:0',
            'passenger_1_per_km' => 'required|numeric|min:0',
            'passenger_2_base_fare' => 'required|numeric|min:0',
            'passenger_2_per_km' => 'required|numeric|min:0',
            'passenger_3_base_fare' => 'required|numeric|min:0',
            'passenger_3_per_km' => 'required|numeric|min:0',
            'waiting_free_mins' => 'required|integer|min:0',
            'waiting_per_min_rate' => 'required|numeric|min:0',
            'tour_per_km_rate' => 'required|numeric|min:0',
            'tour_min_km' => 'required|numeric|min:0',
            'hire_daily_per_km_rate' => 'required|numeric|min:0',
            'hire_monthly_per_km_rate' => 'required|numeric|min:0',
            'hire_tour_advance_percent' => 'required|integer|min:1|max:100',
        ]);

        FairpriceFareSetting::where('is_active', 1)->update(['is_active' => 0]);

        FairpriceFareSetting::create(array_merge($validated, [
            'min_billable_trip_km' => $validated['base_km'],
            'is_active' => 1,
        ]));

        return redirect()
            ->route('fairprice.fare-settings')
            ->with('success', 'FairPrice fare settings updated successfully.');
    }
}
