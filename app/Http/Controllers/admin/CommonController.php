<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Masters\AutoModel;
use Illuminate\Http\Request;

class CommonController extends Controller
{
    public function get_model(Request $request)
    {
        $models = AutoModel::where('brand_id',$request->brand_id)->get();
        return response()->json(array('success' => true, 'data' => $models));
    }
}
