<?php

namespace App\Http\Controllers\admin\auto_management;

use App\Helpers\GenerateId;
use App\Http\Controllers\Controller;
use App\Models\Auto\Auto;
use App\Models\Masters\AutoBrand;
use App\Models\Masters\AutoFuelType;
use App\Models\Masters\AutoModel;
use App\Models\POSQuotation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class NewAutoController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:new_auto'])->only(['index', 'getQuotation']);
        $this->middleware(['permission:add_new_auto'])->only(['create','store']);
        $this->middleware(['permission:edit_new_auto'])->only(['edit','update']);
        $this->middleware(['permission:delete_new_auto'])->only(['delete']);
        $this->middleware(['permission:view_details_new_auto'])->only(['viewDetails']);

    }

    public function index()
    {
        // $used_autos = Auto::with(['autoBrands','autoFuelType', 'AutoPriceRange','cities', 'autoOwners'])
        // ->where('auto_status','active')->where('auto_usage_status','used_auto')->orderBy('id','desc')->get();
        $new_autos = Auto::NewAuto()->where('auto_status','active')
                            // ->where('auto_usage_status','used_auto')
                            ->orderBy('id','desc')
                            ->get(['id','auto_unique_id','image_1','auto_brand_id','auto_model_id','fuel_type_id','passenger_capacity','orp','millage','created_at']);
        return view('admin.auto_management.new_auto.index',compact('new_autos'));
    }

    public function viewDetails($id)
    {
        $id = Crypt::decryptString($id);
        $auto = Auto::findOrFail($id);
        return view('admin.auto_management.new_auto.view',compact('auto'));
    }

    public function create()
    {
        $brands = AutoBrand::where('status',1)->get(['id','brand_name']);
        $models  = AutoModel::where('status_id',1)->get(['id','model_name']);
        $fuels  = AutoFuelType::where('status',1)->get(['id','name']);
        return view('admin.auto_management.new_auto.create',compact('brands','models','fuels'));
    }
    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'auto_usage_status'     => 'required',
                'auto_brand_id'         => 'required',
                'auto_model_id'         => 'required',
                'fuel_type_id'          => 'required',
                'orp'                   => 'required',
                'passenger_capacity'    => 'required',
                'gear'                  => 'required',
                'millage'               => 'required',
                'engine_cc'             => 'required',
                'free_service'          => 'required',
                'finance_arrangements'  => 'required',
                'vehicle_suitable'      => 'required',
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
                'image_1.dimensions' => 'Image 1 must be exactly 500x500 pixels.',
            ]);
            $model = AutoModel::findOrFail($request->auto_model_id);
            if($model){
                $validatedData['specific_model'] = $model->model_name;
            }

            $validatedData['user_id'] = Auth::user()->id;
            $validatedData['auto_status'] = 'active';
            $validatedData['post_type'] = 'A';
            $validatedData['auto_unique_id']= GenerateId::generateId(new Auto, 'auto_unique_id', 4, 'AT-');
            $validatedData['status'] = 1;
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
            $auto = Auto::create($validatedData);
            return redirect()->route('auto-management.new-auto')->with('success','Auto details Created successfully!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->route('auto-management.new-auto.create')
                 ->withErrors($e->errors());
        } catch (\Exception $e) {
            return redirect()->route('auto-management.new-auto.create')
                 ->withErrors($e->getMessage());
        }
    }

    public function edit($id)
    {
        $auto = Auto::findOrFail($id);
        $brands = AutoBrand::where('status',1)->get(['id','brand_name']);
        $models  = AutoModel::where('status_id',1)->get(['id','model_name']);
        $fuels  = AutoFuelType::where('status',1)->get(['id','name']);
        return view('admin.auto_management.new_auto.edit',compact('brands','models','fuels','auto'));
    }

    public function update(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'auto_usage_status'     => 'required',
                'auto_brand_id'         => 'required',
                'auto_model_id'         => 'required',
                'fuel_type_id'          => 'required',
                'orp'                   => 'required',
                'passenger_capacity'    => 'required',
                'gear'                  => 'required',
                'millage'               => 'required',
                'engine_cc'             => 'required',
                'free_service'          => 'required',
                'finance_arrangements'  => 'nullable',
                'vehicle_suitable'      => 'required',
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
            return redirect()->route('auto-management.new-auto')->with('success','Auto details Updated successfully!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->route('auto-management.new-auto.update')
                 ->withErrors($e->errors());
        } catch (\Exception $e) {
            return redirect()->route('auto-management.new-auto.update')
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
            return response()->json(array('success' => true, 'message' => 'Auto Deleted Successfully'));
        } else {
            return response()->json(array('success' => false, 'message' => 'Auto Not Found?...'));
        }
    }

    public function getQuotation(Request $request)
    {
        $request->validate([
            'autoId'             => 'required|exists:auto_posts,id',
            'name'               => 'required|string|max:255',
            'email'              => 'nullable|email',
            'mobile'             => 'required|string|max:15',
            'address'            => 'nullable|string',
            'discount_amount'    => 'required',
            'total_amount'       => 'required',
            'loan_percentage'    => 'required',
            'total_loan_amount'  => 'required',
            'interest'           => 'required',
            'emi_months'         => 'required',
            'emi_amount'         => 'required',
            'down_payment'       => 'required',
            'fitting_fee'        => 'nullable',
            'permit_fee'         => 'nullable',
            'loan_process_fee'   => 'nullable',
            'gifts'              => 'nullable|array',
            'gifts.*'            => 'string',
        ]);
        $quotation = DB::transaction(function () use ($request) {

            $year = Carbon::now()->year;

            //Get last quotation_no of current year
            $lastQuotation = POSQuotation::where('quotation_no', 'like', "POS-$year-%")
                ->lockForUpdate()
                ->orderBy('quotation_no', 'desc')
                ->first();

            if ($lastQuotation) {
                // POS-2026-0007 → 0007
                $lastSeq = (int) substr($lastQuotation->quotation_no, -4);
                $nextSeq = $lastSeq + 1;
            } else {
                // First quotation of the year
                $nextSeq = 1;
            }

            //Generate new quotation no
            $quotationNo = 'POS-' . $year . '-' . str_pad($nextSeq, 4, '0', STR_PAD_LEFT);

            // ✅ Store
            $quotation = POSQuotation::create([
                'quotation_no'      => $quotationNo,

                'auto_id'           => $request->autoId,
                'name'              => $request->name,
                'email'             => $request->email,
                'mobile'            => $request->mobile,
                'address'           => $request->address,

                'discount_amount'   => $request->discount_amount ?? 0,
                'total_amount'      => $request->total_amount,
                'loan_percentage'   => $request->loan_percentage,
                'total_loan_amount' => $request->total_loan_amount,
                'interest'          => $request->interest,
                'emi_months'        => $request->emi_months,
                'emi_amount'        => $request->emi_amount,
                'down_payment'      => $request->down_payment,

                'fitting_fee'       => $request->fitting_fee ?? 0,
                'permit_fee'        => $request->permit_fee ?? 0,
                'loan_process_fee'  => $request->loan_process_fee ?? 0,

                'gifts'             => !empty($request->gifts) ? $request->gifts : null,

                'status_id'         => 1,
                'created_by'        => auth()->id(),
                'ip_address'        => $request->ip(),
            ]);
            return $quotation;
        });
        $quotation->load('auto');

        //Generate PDF
        $pdf = Pdf::loadView('admin.auto_management.new_auto.pos-quotation', compact('quotation'))->setPaper('A4', 'portrait');

        //Download PDF
        return $pdf->download($quotation->quotation_no . '.pdf');
    }
}
