<?php

namespace App\Http\Controllers\admin\spare_parts;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\spareparts\SparepartOrder;

class OrdersController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:sparepart_pendingorders'])->only(['pendingOrders']);
        $this->middleware(['permission:sparepart_successorders'])->only(['successOrders']);
        $this->middleware(['permission:sparepart_cancelorders'])->only(['cancelOrders']);
        $this->middleware(['permission:sparepart_deleteorders'])->only(['deleteOrder']);
    }
    public function pendingOrders()
    {
        // $orders = SparepartOrder::whereIn('order_status', ['pending', 'shipped','contacted'])->latest()->get();
        $orders = SparepartOrder::with([
            'user:id,name,phone_number',
            'product.product:id,name',
            'product.autoBrands:id,brand_name',
            'product.autoModel:id,model_name'
        ])
        // ->whereIn('order_status', ['pending', 'shipped', 'contacted'])
        ->whereIn('order_status', ['pending', 'shipped'])
        ->where('stauts_id',1)
        ->latest()
        ->get(['id', 'user_id', 'product_id', 'qnty', 'order_status']); // select only needed columns
        return view('admin.spare_parts.orders.pendingorder',compact('orders'));
    }

    public function successOrders()
    {
        // $orders = SparepartOrder::where('order_status','success')->get();
        $orders = SparepartOrder::with([
            'user:id,name,phone_number',
            'product.product:id,name',
            'product.autoBrands:id,brand_name',
            'product.autoModel:id,model_name'
        ])
        // ->whereIn('order_status', ['pending', 'shipped', 'contacted'])
        ->whereIn('order_status', ['contacted'])
        ->latest()
        ->get(['id', 'user_id', 'product_id', 'qnty', 'order_status']); // select only needed columns
        return view('admin.spare_parts.orders.successorder',compact('orders'));
    }

    public function cancelOrders()
    {
        // $orders = SparepartOrder::where('order_status','cancel')->get();
        $orders = SparepartOrder::with([
            'user:id,name,phone_number',
            'product.product:id,name',
            'product.autoBrands:id,brand_name',
            'product.autoModel:id,model_name'
        ])
        // ->whereIn('order_status', ['pending', 'shipped', 'contacted'])
        ->whereIn('order_status', ['cancel'])
        ->latest()
        ->get(['id', 'user_id', 'product_id', 'qnty', 'order_status']); // select only needed columns
        return view('admin.spare_parts.orders.cancelorder',compact('orders'));
    }

    public function  statusUpdate(Request $request)
    {
        $emergency = SparepartOrder::findOrFail($request->id);
        $emergency->order_status = $request->status;
        $emergency->save();
        return response()->json(array('success' => true, 'message' => 'Order Status Updated Successfully'));
    }

    public function deleteOrder(Request $request)
    {
        $order = SparepartOrder::findOrFail($request->id);
        if(!$order){
            return response()->json(array('success' => false, 'message' => 'Order Not Found!'));
        }
        $order->stauts_id = 3;
        $order->save();
        return response()->json(array('success' => true, 'message' => 'Order Deleted Successfully!'));
    }
}
