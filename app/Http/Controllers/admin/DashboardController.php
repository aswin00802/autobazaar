<?php

namespace App\Http\Controllers\admin;

use App\Models\User;
use App\Models\Enquiry;
use App\Models\Auto\Auto;
use Illuminate\Http\Request;
use App\Models\Auto\SoldAuto;
use Illuminate\Support\Carbon;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:dashboard']);
    }

    public function index()
    {
        $dt    = date('Y-m-d'); //get current date
        $month = Carbon::now()->month; //get current month
        $year  = Carbon::now()->year; //get current year
        //get today active user
        $todayUsers = User::whereDate('created_at', $dt)->where('status',1)->get(['name','phone_number','created_at']);
        //get total sold autos
        $soldAutos = SoldAuto::count();
        //total Enquirys
        $totalEnquirys = Enquiry::count();
        //current month Enquirys
        $monthlyEnquirys = Enquiry::whereMonth('created_at', $month)
                    ->whereYear('created_at', $year)
                    ->count();
        //total Autos
        $totalAutos = Auto::count();
        //bajaj refinance autos
        $totalBajajRefinanceAutos = Auto::where('auto_usage_status','bajaj_refinance')->where('auto_status','active')->count();
        //private cargo autos
        $totalPrivateCargoAutos = Auto::where('auto_usage_status','private_cargo')->where('auto_status','active')->count();
        //total Used Autos
        $totalUsedAutos = Auto::where('auto_usage_status','used_auto')->where('auto_status','active')->count();
        //total new autos
        $totalNewAutos = Auto::where('auto_usage_status','new_auto')->where('auto_status','active')->count();
        //total active autos
        $totalActiveAutos = Auto::whereIn('auto_usage_status', ['used_auto', 'new_auto'])->where('auto_status','active')->count();
        //total user
        $totalUsers = User::count();
        //active users
        $activeUsers = User::where('status',1)->count();
        //monthly user
        $monthlyUsers = User::whereMonth('created_at', $month)
                    ->whereYear('created_at', $year)
                    ->count();
        return view('admin.dashboard.dashboard',compact('todayUsers','soldAutos','totalEnquirys','monthlyEnquirys','totalAutos','totalBajajRefinanceAutos','totalPrivateCargoAutos','totalUsedAutos','totalNewAutos','totalActiveAutos','totalUsers','activeUsers','monthlyUsers'));
    }
}
