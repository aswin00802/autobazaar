<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\AutoMeter;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PDF;

class AutoMeterController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:auto_meter'])->only(['index']);
    }
    public function index(Request $request)
    {
        $from_date = $request->from_date ?? Carbon::today()->format('Y-m-d');
        $to_date   = $request->to_date ?? Carbon::today()->format('Y-m-d');
        // $auto_meters = AutoMeter::with('user')
        // ->select(
        //     'user_id',
        //     'invoice_no',
        //     DB::raw('SUM(total_km) as total_km'),
        //     DB::raw('SUM(total_amount) as total_amount'),
        //     DB::raw('SUM(margin_amount) as margin_amount'),
        //     DB::raw('MAX(date) as date')
        // )
        // ->whereDate('date', '>=', $from_date)
        // ->whereDate('date', '<=', $to_date)
        // ->groupBy('user_id')
        // ->get();
        $auto_meters = AutoMeter::with('user')
                        ->whereDate('date', '>=', $from_date)
                        ->whereDate('date', '<=', $to_date)
                        ->get();
        return view('admin.auto_meter.index',compact('auto_meters','from_date','to_date'));
    }

    public function invoice($id)
    {
        $data = AutoMeter::with([
            'user',
            'user.userInfo',
            'user.userInfo.autoBrand',
            'user.userInfo.autoModel',
            'user.userInfo.autoFueltype'
        ])->findOrFail($id);

        $pdf = PDF::loadView('admin.auto_meter.invoice_pdf', compact('data'))->setPaper('a4', 'portrait');;

        return $pdf->stream('invoice.pdf');
    }

    public function invoiceDownload($id)
    {
        $data = AutoMeter::with([
            'user',
            'user.userInfo',
            'user.userInfo.autoBrand',
            'user.userInfo.autoModel',
            'user.userInfo.autoFueltype'
        ])->findOrFail($id);

        $pdf = PDF::loadView('admin.auto_meter.invoice_pdf', compact('data'))->setPaper('a4', 'portrait');;

        return $pdf->download('Invoice-'.$data->invoice_no.'.pdf');
    }
}
