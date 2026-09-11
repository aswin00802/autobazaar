<?php

namespace App\Http\Controllers\Web\Auth;

use Illuminate\Http\Request;
use App\Models\Masters\AutoAreas;
use App\Http\Controllers\Controller;

class RegisterController extends Controller
{
    public function index()
    {
        $areas = AutoAreas::all();
        return view('web.auth.register',compact('areas'));
    }
}
