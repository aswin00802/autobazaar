<?php

namespace App\Http\Controllers\admin\auto_management;

use App\Models\Auto\Auto;
use App\Helpers\GenerateId;
use Illuminate\Http\Request;
use App\Models\Auto\SoldAuto;
use App\Models\Masters\AutoBrand;
use App\Models\Masters\AutoModel;
use App\Models\Masters\AutoOwners;
use App\Http\Controllers\Controller;
use App\Models\Masters\AutoFuelType;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;

class PrivateCargoAutoController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:private_cargo_auto'])->only(['index']);
        $this->middleware(['permission:add_private_cargo_auto'])->only(['create','store']);
        $this->middleware(['permission:edit_private_cargo_auto'])->only(['edit','update']);
        $this->middleware(['permission:sell_private_cargo_auto'])->only(['sell_auto']);
        $this->middleware(['permission:delete_private_cargo_auto'])->only(['delete']);
        $this->middleware(['permission:view_details_private_cargo_auto'])->only(['viewDetails']);
    
    }

    public function index()
    {
        // $used_autos = Auto::with(['autoBrands','autoFuelType', 'AutoPriceRange','cities', 'autoOwners'])
        // ->where('auto_status','active')->where('auto_usage_status','used_auto')->orderBy('id','desc')->get();
        $used_autos = Auto::where('auto_status','active')
                            ->where('auto_usage_status','private_cargo')
                            ->orderBy('id','desc')
                            ->get(['id','auto_unique_id','image_1','auto_brand_id','auto_model_id','fuel_type_id','registration_year','registration_number','kilometer','price_expectations','created_at']);
        return view('admin.auto_management.private_cargo.index',compact('used_autos'));
    }

    public function viewDetails($id)
    {
        $id = Crypt::decryptString($id);
        $auto = Auto::findOrFail($id);
        return view('admin.auto_management.private_cargo.view',compact('auto'));
    }

    public function create()
    {
        $brands = AutoBrand::where('status',1)->get(['id','brand_name']);
        $models  = AutoModel::where('status_id',1)->get(['id','model_name']);
        $fuels  = AutoFuelType::where('status',1)->get(['id','name']);
        $owners = AutoOwners::where('status',1)->get(['id','name']);
        return view('admin.auto_management.private_cargo.create',compact('brands','models','fuels','owners'));
    }

    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'auto_usage_status'     => 'required',
                'auto_brand_id'         => 'required',
                'auto_model_id'         => 'required',
                'registration_year'     => 'required',
                'registration_number'   => 'required',
                'rto'                   => 'required',
                'fuel_type_id'          => 'required',
                'kilometer'             => 'required',
                'price_expectations'    => 'required',
                'owner'                 => 'nullable',
                'name'                  => 'nullable',
                'mobile_number'         => 'nullable',
                'chellan_status'        => 'nullable',
                'accident_status'       => 'nullable',
                'noc_status'            => 'nullable',
                'loan_status'           => 'nullable',
                'fc_status'             => 'nullable',
                'permit_status'         => 'nullable',
                'insurance'             => 'nullable',
                'condition'             => 'nullable',
                'financial_availability' => 'nullable',
                'loan_amount'           => 'nullable',
                'first_payment'         => 'nullable',
                'emi_amount'            => 'nullable',
                'no_of_months'          => 'nullable',
                'image_1'               => 'nullable|image|mimes:jpeg,png,jpg,gif|dimensions:width=500,height=500',
                'image_2'               => 'nullable|file|image|mimes:jpeg,png,jpg,gif',
                'image_3'               => 'nullable|file|image|mimes:jpeg,png,jpg,gif',
                'image_4'               => 'nullable|file|image|mimes:jpeg,png,jpg,gif',
                'image_5'               => 'nullable|file|image|mimes:jpeg,png,jpg,gif',
                'image_6'               => 'nullable|file|image|mimes:jpeg,png,jpg,gif',
            ],[
                //image1 Custom messages
                'image_1.dimensions' => 'Image 1 must be exactly 500x500 pixels.',
            ]);
            $model = AutoModel::findOrFail($request->auto_model_id);
            if($model){
                $validatedData['specific_model'] = $model->model_name;
            }
            if ($request->financial_availability == 1) {
                $validatedData['financial_availability'] = 'true';
            } else {
                $validatedData['financial_availability'] = 'false';
            }
            $validatedData['user_id'] = Auth::user()->id;
            $validatedData['auto_status'] = 'active';
            $validatedData['post_type'] = 'A';
            $validatedData['auto_unique_id']= GenerateId::generateId(new Auto, 'auto_unique_id', 4, 'AT-');
            $validatedData['status'] = 1;
            $destinationPath = public_path('uploads/auto_images');
            // Handle image uploads with old file deletion
            foreach (['image_1', 'image_2', 'image_3', 'image_4', 'image_5', 'image_6'] as $imageField) {
                if ($request->hasFile($imageField) && $request->file($imageField)->isValid()) {            
                    // Save the new file
                    $file = $request->file($imageField);
                    $fileName = uniqid() . '.' . $file->getClientOriginalExtension();
                    $file->move($destinationPath, $fileName);
                    $validatedData[$imageField] = 'uploads/auto_images/' . $fileName;
                }
            }
            $auto = Auto::create($validatedData);
            return redirect()->route('auto-management.private-cargo-auto')->with('success','Private Cargo Auto details Created successfully!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->route('auto-management.private-cargo-auto.create')
                 ->withErrors($e->errors());
        } catch (\Exception $e) {
            return redirect()->route('auto-management.private-cargo-auto.create')
                 ->withErrors($e->getMessage());
        }
    }

    public function edit($id)
    {
        $auto = Auto::findOrFail($id);
        $brands = AutoBrand::where('status',1)->get(['id','brand_name']);
        $models  = AutoModel::where('status_id',1)->get(['id','model_name']);
        $fuels  = AutoFuelType::where('status',1)->get(['id','name']);
        $owners = AutoOwners::where('status',1)->get(['id','name']);
        return view('admin.auto_management.private_cargo.edit',compact('brands','models','fuels','owners','auto'));
    }

    public function update(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'auto_usage_status'     => 'required',
                'auto_brand_id'         => 'required',
                'auto_model_id'         => 'required',
                'registration_year'     => 'required',
                'registration_number'   => 'required',
                'rto'                   => 'required',
                'fuel_type_id'          => 'required',
                'kilometer'             => 'required',
                'price_expectations'    => 'required',
                'owner'                 => 'nullable',
                'name'                  => 'nullable',
                'mobile_number'         => 'nullable',
                'chellan_status'        => 'nullable',
                'accident_status'       => 'nullable',
                'noc_status'            => 'nullable',
                'loan_status'           => 'nullable',
                'fc_status'             => 'nullable',
                'permit_status'         => 'nullable',
                'insurance'             => 'nullable',
                'condition'             => 'nullable',
                'financial_availability' => 'nullable',
                'loan_amount'           => 'nullable',
                'first_payment'         => 'nullable',
                'emi_amount'            => 'nullable',
                'no_of_months'          => 'nullable',
                'image_1'               => 'nullable|image|mimes:jpeg,png,jpg,gif|dimensions:width=500,height=500',
                'image_2'               => 'nullable|file|image|mimes:jpeg,png,jpg,gif',
                'image_3'               => 'nullable|file|image|mimes:jpeg,png,jpg,gif',
                'image_4'               => 'nullable|file|image|mimes:jpeg,png,jpg,gif',
                'image_5'               => 'nullable|file|image|mimes:jpeg,png,jpg,gif',
                'image_6'               => 'nullable|file|image|mimes:jpeg,png,jpg,gif',
            ],[
                //image1 Custom messages
                'image_1.dimensions' => 'Image 1 must be exactly 500x500 pixels.',
            ]);
            $auto = Auto::findOrFail($request->id);
            $model = AutoModel::findOrFail($request->auto_model_id);
            if($model){
                $validatedData['specific_model'] = $model->model_name;
            }
            if ($request->financial_availability == 1) {
                $validatedData['financial_availability'] = 'true';
            } else {
                $validatedData['financial_availability'] = 'false';
            }
            
            $destinationPath = public_path('uploads/auto_images');
            // Handle image uploads with old file deletion
            foreach (['image_1', 'image_2', 'image_3', 'image_4', 'image_5', 'image_6'] as $imageField) {
                if ($request->hasFile($imageField) && $request->file($imageField)->isValid()) {            
                    // Save the new file
                    $file = $request->file($imageField);
                    $fileName = uniqid() . '.' . $file->getClientOriginalExtension();
                    $file->move($destinationPath, $fileName);
                    $validatedData[$imageField] = 'uploads/auto_images/' . $fileName;
                }
            }
            $validated = $validatedData;
            $auto->update($validated);
            return redirect()->route('auto-management.private-cargo-auto')->with('success','Private Cargo Auto details Updated successfully!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->route('auto-management.private-cargo-auto.update')
                 ->withErrors($e->errors());
        } catch (\Exception $e) {
            return redirect()->route('auto-management.private-cargo-auto.update')
                 ->withErrors($e->getMessage());
        }
    }

    public function delete(Request $request)
    {
        $auto = Auto::find($request->id);
        if ($auto) {
            $auto->status       = 4;
            $auto->auto_status  = 'deleted';
            $auto->save();
            return response()->json(array('success' => true, 'message' => 'Private Cargo Auto Deleted Successfully'));
        } else {
            return response()->json(array('success' => false, 'message' => 'Private Cargo Auto Not Found?...'));
        }
    }

    public function sell_auto(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'buyer_name'    =>'required|string',
                'date'          =>'required|date',
                'post_id'       =>'required|integer',
                'sold_amount'   =>'required|string',
            ]);
            $validatedData['user_id']= Auth::user()->id;
            $auto = Auto::where('id',$validatedData['post_id'])->first();
            if ($auto->auto_status === 'sold') {
                return redirect()->route('auto-management.private-cargo-auto')
                 ->withErrors('This auto is already marked as sold');
            }
            $sold_auto = SoldAuto::create($validatedData);
            if ($sold_auto) {
                $auto->auto_status = 'sold';
                $auto->status = 0; // You may want to mark it as inactive when sold
                $auto->save();
                return redirect()->route('auto-management.private-cargo-auto')->with('success','Auto has been marked as sold successfully!');
            }  else{
                return redirect()->route('auto-management.private-cargo-auto')
                 ->withErrors('Something went wrong on sold this auto, please try again later.');
            } 
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->route('auto-management.private-cargo-auto')
                 ->withErrors($e->errors());
        } catch (\Exception $e) {
            return redirect()->route('auto-management.private-cargo-auto')
                 ->withErrors($e->getMessage());
        }
    }
}
