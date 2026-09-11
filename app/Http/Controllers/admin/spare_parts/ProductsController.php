<?php

namespace App\Http\Controllers\admin\spare_parts;

use Illuminate\Http\Request;
use App\Models\Masters\AutoBrand;
use App\Models\spareparts\Product;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\spareparts\ProductBrandModel;
use App\Models\spareparts\SparepartsCategories;
use App\Models\spareparts\ProductBrandModelImage;
use App\Models\spareparts\SparepartsSubCategories;

class ProductsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:sparepart_product'])->only(['index']);
        $this->middleware(['permission:add_sparepart_product'])->only(['create','store']);
        $this->middleware(['permission:edit_sparepart_product'])->only(['edit','update']);
        $this->middleware(['permission:delete_sparepart_product'])->only(['delete']);
    
    }
    public function index()
    {
        $products = Product::where('status_id',1)->get(['id','name','category_id','subcategory_id','image']);
        return view('admin.spare_parts.products.index',compact('products'));
    }

    public function create()
    {
        $categories     = SparepartsCategories::where('status_id',1)->get();
        $subcategories  = SparepartsSubCategories::where('status_id',1)->get();
        $brands         = AutoBrand::where('status',1)->get();
        return view('admin.spare_parts.products.create',compact('categories','subcategories','brands'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_id'                   => 'required',
            'subcategory_id'                => 'required',
            'name'                          => 'required',
            'description'                   => 'nullable',
            // Validate brand_models array
            'brand_models'                  => 'nullable|array|min:1',
            'brand_models.*.brand_id'       => 'nullable|exists:auto_brands,id',
            'brand_models.*.model_id'       => 'nullable|exists:auto_models,id',
            'brand_models.*.price'          => 'nullable|numeric|min:0',
            'brand_models.*.offer_price'    => 'nullable|numeric|lte:brand_models.*.price',
            // Images validation
            'brand_models.*.images'         => 'nullable|array|min:1',
            'brand_models.*.images.*'       => 'image|mimes:jpg,jpeg,png',
        ]);
        $exitCount = Product::latest()->first();
        if($exitCount){
            $fileNameCreate = $exitCount->id + 1;
        } else {
            $fileNameCreate = 1;
        }
        $product                    = new Product();
        $product->category_id       = $request->category_id;
        $product->subcategory_id    = $request->subcategory_id;
        $product->name              = $request->name;
        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $product->image         = uploadedAsset($request, 'image', 'products'.$fileNameCreate, 'spareparts/products');
        }
        $product->description       = $request->description;
        $product->created_by        = Auth::user()->id;
        $product->ip_address        = $request->ip();
        $product->save();

        // Multiple brand + model + price insert
        if($request->brand_models){
            foreach($request->brand_models as $index => $bm){
                if (!empty($bm['offer_price']) && $bm['offer_price'] > $bm['price']) {
                    return back()->withErrors([
                        "brand_models.$index.offer_price" => "Offer price must be less than or equal to price"
                    ])->withInput();
                }
                $productBrandModel = ProductBrandModel::create([
                    'product_id'    => $product->id,
                    'brand_id'      => $bm['brand_id'],
                    'brand_model_id'=> $bm['model_id'],
                    'price'         => $bm['price'],
                    'offer_price'   => $bm['offer_price'] ?? null,
                    'created_by'    => Auth::user()->id,
                    'ip_address'    => $request->ip(),
                ]);

                // multiple images save
                if(isset($bm['images'])){
                    foreach ($bm['images'] as $image) {
                        $fileName = uploadedAssetFile($image,'products_'.time().rand(100,999),'spareparts/products');
                        ProductBrandModelImage::create([
                            'product_brand_model_id' => $productBrandModel->id,
                            'image'                  => $fileName,
                        ]);
                    }
                }
            }
        }

        return redirect()->route('spare-parts.product')->with('success','Spare Parts Product Created Successfully');

    }

    public function edit($id)
    {
        $categories     = SparepartsCategories::where('status_id',1)->get();
        $subcategories  = SparepartsSubCategories::where('status_id',1)->get();
        $brands         = AutoBrand::where('status',1)->get();
        $product        = Product::findOrFail($id);
        return view('admin.spare_parts.products.edit',compact('product','categories','subcategories','brands'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'category_id'                   => 'required',
            'subcategory_id'                => 'required',
            'name'                          => 'required',
            'description'                   => 'nullable',
            // Validate brand_models array
            'brand_models'                  => 'nullable|array|min:1',
            'brand_models.*.brand_id'       => 'nullable|exists:auto_brands,id',
            'brand_models.*.model_id'       => 'nullable|exists:auto_models,id',
            'brand_models.*.price'          => 'nullable|numeric|min:0',
            'brand_models.*.offer_price'    => 'nullable|numeric|lte:brand_models.*.price',
            // Images validation
            'brand_models.*.images'         => 'nullable|array|min:1',
            'brand_models.*.images.*'       => 'image|mimes:jpg,jpeg,png',
        ]);
        $product = Product::findOrFail($request->id);
         // Update product basic details
        $product->update([
            'category_id'    => $request->category_id,
            'subcategory_id' => $request->subcategory_id,
            'name'           => $request->name,
            'description'    => $request->description,
            'created_by'     => Auth::user()->id,
            'ip_address'     => $request->ip(),
        ]);
        // Only update image if file exists
        if ($request->hasFile('image')) {
            $path = uploadedAsset($request, 'image', 'products'.$product->id, 'spareparts/products');
            $product->update(['image' => $path]);
        }
        // Existing records from DB
        $existing = $product->productBrandModel()->get()->keyBy(function($bm){
            return $bm->brand_id.'_'.$bm->brand_model_id;
        });
            // New input data
        $newData = collect($request->brand_models ?? [])
            ->keyBy(function($bm){
            return $bm['brand_id'].'_'.$bm['model_id'];
        });
        // 1. Update existing or insert new
        foreach ($newData as $key => $bm) {
            $productBrandModel = null;
            if ($existing->has($key)) {
                // update
                $existing[$key]->update([
                    'price'       => $bm['price'],
                    'offer_price' => $bm['offer_price'] ?? null,
                ]);
            } else {
                // insert
                $productBrandModel = ProductBrandModel::create([
                    'product_id'     => $product->id,
                    'brand_id'       => $bm['brand_id'],
                    'brand_model_id' => $bm['model_id'],
                    'price'          => $bm['price'],
                    'offer_price'    => $bm['offer_price'] ?? null,
                    'created_by'     => Auth::user()->id,
                    'ip_address'     => $request->ip(),
                ]);
            }
             // ✅ Only process images if isset
            if (isset($bm['images']) && is_array($bm['images'])) {
                foreach ($bm['images'] as $image) {
                    $fileName = uploadedAssetFile($image,'products_' . time() . rand(100, 999),'spareparts/products');
                    ProductBrandModelImage::create([
                        'product_brand_model_id' => $productBrandModel->id,
                        'image'                  => $fileName,
                    ]);
                }
            }
        }

        // 2. Delete removed records
        $toDelete = $existing->keys()->diff($newData->keys());
        if ($toDelete->count()) {
            ProductBrandModel::where('product_id', $product->id)
                ->where(function($q) use ($toDelete){
                    foreach ($toDelete as $key) {
                        [$brandId, $modelId] = explode('_', $key);
                        $q->orWhere(function($sub) use ($brandId, $modelId){
                            $sub->where('brand_id', $brandId)
                                ->where('brand_model_id', $modelId);
                        });
                    }
                }) ->update(['status_id' => 2]);
        }
        return redirect()->route('spare-parts.product')->with('success','Spare Parts Product Updated Successfully');

    }

    public function delete(Request $request)
    {
        $product = Product::find($request->id);
        if ($product) {
            $product->status_id      = 2;
            $product->created_by     = Auth::user()->id;
            $product->ip_address     = $request->ip();
            $product->save();
            return response()->json(array('success' => true, 'message' => 'Spareparts Product Deleted Successfully'));
        } else {
            return response()->json(array('success' => false, 'message' => 'Spareparts Product Not Found?...'));
        }
    }

    public function get_Subcategory(Request $request)
    {
        $subCategory = SparepartsSubCategories::where('category_id',$request->category_id)->where('status_id',1)->get(['id','name']);
        if(!$subCategory->isEmpty()){
            return response()->json(array('success' => true, 'data' => $subCategory));
        } else {
            return response()->json(array('success' => false, 'message' => 'Spareparts Sub Categories Not Found?...'));
        }
    }

    public function getDetails_old($id)
    {
        $product = Product::with([
            'Category:id,name',
            'subCategory:id,name',
            'productBrandModel.autoBrands:id,brand_name',
            'productBrandModel.autoModel:id,model_name',
            'productBrandModel.images'
        ])->findOrFail($id);

        return response()->json($product);
    }

    public function getDetails($id)
    {
        $product = ProductBrandModel::with([
            'product.Category:id,name',
            'product.subCategory:id,name',
            'autoBrands:id,brand_name',
            'autoModel:id,model_name',
            'images'
        ])->findOrFail($id);

        return response()->json($product);
    }
}
