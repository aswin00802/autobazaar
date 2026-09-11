<?php

namespace App\Http\Controllers\admin\service;

use Illuminate\Http\Request;
use App\Models\Services\Insurance;
use App\Http\Controllers\Controller;

class InsuranceController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:insurance'])->only('index');
        // $this->middleware(['permission:add_gas_station'])->only(['create','store']);
        // $this->middleware(['permission:edit_gas_station'])->only(['edit', 'update']);
        // $this->middleware(['permission:delete_gas_station'])->only(['destroy']);
    }

    public function index()
    {
        $insurances = Insurance::all();
        return view('admin.service.insurance.index', compact('insurances'));
    }
}
