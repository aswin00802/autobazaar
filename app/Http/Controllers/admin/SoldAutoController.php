<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Models\Auto\SoldAuto;
use App\Http\Controllers\Controller;

class SoldAutoController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:solid_autos_list'])->only(['index']);
    }
    public function index()
    {
        $soldAutos = SoldAuto::latest()->get();
        return view('admin.sold_auto.index',compact('soldAutos'));
    }
}
