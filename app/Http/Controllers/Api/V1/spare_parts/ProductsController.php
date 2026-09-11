<?php

namespace App\Http\Controllers\Api\V1\spare_parts;

use Illuminate\Http\Request;
use App\Services\ResponseService;
use App\Models\spareparts\Product;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\spareparts\SparepartOrder;

class ProductsController extends Controller
{
    public function getSubCategoriesProducts($subCategoryId)
    {
        $products = Product::with(['productBrandModel.images', 'Category', 'subCategory'])
            ->where('subcategory_id', $subCategoryId)
            ->where('status_id',1)
            ->latest()
            ->get();

        // Optional: format response nicely
        $response = $products->map(function($product){
            return [
                'id'           => $product->id,
                'name'         => $product->name,
                'description'  => $product->description,
                'image'        => $product->image,
                'category'     => $product->Category->name ?? null,
                'subcategory'  => $product->subCategory->name ?? null,
                'brand_models' => $product->productBrandModel->map(function($bm){
                    return [
                        'id'           => $bm->id,
                        'brand_id'     => $bm->brand_id,
                        'brand_name'   => $bm->autoBrands->brand_name ?? null,
                        'model_id'     => $bm->brand_model_id,
                        'model_name'   => $bm->autoModel->model_name ?? null,
                        'price'        => $bm->price,
                        'offer_price'  => $bm->offer_price,
                        'images'       => $bm->images->pluck('image'), // returns array of images
                    ];
                }),
            ];
        });
        return ResponseService::success($response, 'Products Successfully Get.');
        // return response()->json([
        //     'success' => true,
        //     'data'    => $response
        // ]);
    }

    public function sendOrder(Request $request,$productId)
    {
        try{
            $validated = $request->validate([
                'quantity' => 'required|integer'
            ]);
            $userId             = Auth::user()->id;
            $orderId            = 'ORD-' . strtoupper(uniqid());  //create randam order id
            $orders             = new SparepartOrder();
            $orders->user_id    = $userId;
            $orders->product_id = $productId;
            $orders->qnty       = $request->quantity;
            $orders->order_id   = $orderId;
            $orders->save();
            return ResponseService::success( 'Your Order Successfully Placed.Contact Soon.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return ResponseService::validationError('Validation failed.', $e->errors(), 422);
        }  catch (\Exception $e) {
            return ResponseService::error('An error occurred. Please try again.', ['error' => $e->getMessage()], 500);
        }
    }

    public function getuserOrders(Request $request)
    {
        $userId = Auth::user()->id;
        // $orders = SparepartOrder::with([
        //     'product.product' => function($query) { // nested relation
        //         $query->select('id', 'name', 'category_id', 'subcategory_id')
        //               ->with([
        //                   'Category:id,name',
        //                   'subCategory:id,name'
        //               ]);
        //     },
        //     'product.images:id,product_brand_model_id,image'
        // ])
        // ->select('id', 'user_id', 'product_id', 'order_status')
        // ->where('user_id', $userId)
        // ->get();
        $orders = SparepartOrder::with([
            'product' => function ($query) {
                $query->with([
                    'product' => function ($q) {
                        $q->select('id', 'name', 'category_id', 'subcategory_id')
                          ->with([
                              'Category:id,name',
                              'subCategory:id,name'
                          ]);
                    },
                    'autoBrands:id,brand_name',
                    'autoModel:id,model_name',
                    'images:id,product_brand_model_id,image'
                ]);
            }
        ])
        ->select('id', 'user_id', 'product_id', 'order_status')
        ->where('user_id', $userId)
        ->get();

        // $response = $orders->map(function($order) {
        //     $pbm = $order->product;
        //     $product = $pbm->product ?? null;
        
        //     return [
        //         'order_id' => $order->id,
        //         'user_id' => $order->user_id,
        //         'product_id' => $order->product_id,
        //         'order_status' => $order->order_status,
        //         'product' => $product ? [
        //             'id' => $product->id,
        //             'name' => $product->name,
        //             'category' => [
        //                 'id' => $product->Category->id ?? null,
        //                 'name' => $product->Category->name ?? null,
        //             ],
        //             'subcategory' => [
        //                 'id' => $product->subCategory->id ?? null,
        //                 'name' => $product->subCategory->name ?? null,
        //             ],
        //             'images' => $pbm->images->pluck('image') // only images from ProductBrandModel
        //         ] : null
        //     ];
        // });
        $response = $orders->map(function($order) {
            $pbm = $order->product; // ProductBrandModel
            $product = $pbm->product ?? null;
        
            return [
                'order_id' => $order->id,
                'user_id' => $order->user_id,
                'product_id' => $order->product_id,
                'order_status' => $order->order_status,
                'product' => $product ? [
                    'id' => $product->id,
                    'name' => $product->name,
                    'category' => [
                        'id' => $product->Category->id ?? null,
                        'name' => $product->Category->name ?? null,
                    ],
                    'subcategory' => [
                        'id' => $product->subCategory->id ?? null,
                        'name' => $product->subCategory->name ?? null,
                    ],
                    'brand' => [
                        'id' => $pbm->autoBrands->id ?? null,
                        'name' => $pbm->autoBrands->brand_name ?? null,
                    ],
                    'model' => [
                        'id' => $pbm->autoModel->id ?? null,
                        'name' => $pbm->autoModel->model_name ?? null,
                    ],
                    'images' => $pbm->images->pluck('image')
                ] : null
            ];
        });
        
        
        return ResponseService::success( $response,'Products List with fields');
        // return ResponseService::success( $response,'Products List with fields');

        
    }

    public function getProducts(Request $request)
    {
        $products = Product::with(['productBrandModel.images', 'Category', 'subCategory'])
            ->where('status_id',1)
            ->latest()
            ->get();

        // Optional: format response nicely
        $response = $products->map(function($product){
            return [
                'id'           => $product->id,
                'name'         => $product->name,
                'description'  => $product->description,
                'image'        => $product->image,
                'category'     => $product->Category->name ?? null,
                'subcategory'  => $product->subCategory->name ?? null,
                'brand_models' => $product->productBrandModel->map(function($bm){
                    return [
                        'id'           => $bm->id,
                        'brand_id'     => $bm->brand_id,
                        'brand_name'   => $bm->autoBrands->brand_name ?? null,
                        'model_id'     => $bm->brand_model_id,
                        'model_name'   => $bm->autoModel->model_name ?? null,
                        'price'        => $bm->price,
                        'offer_price'  => $bm->offer_price,
                        'images'       => $bm->images->pluck('image'), // returns array of images
                    ];
                }),
            ];
        });
        return ResponseService::success($response, 'Products Successfully Get.');
    }
}
