<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Auto\Auto;
use App\Helpers\GenerateId;
use Illuminate\Http\Request;
use App\Services\AutoService;
use App\Models\Masters\AutoModel;
use App\Services\ResponseService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Services\FirebaseNotificationService;

class AutoDetailsController extends Controller
{
    protected $firebaseService , $autoservice;

    public function __construct(FirebaseNotificationService $firebaseService, AutoService $autoservice)
    {
        $this->firebaseService = $firebaseService;
        $this->autoservice = $autoservice;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
       return response()->json(Auto::all(), 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try{
            // Validate input data
            $validated = $request->validate([
                'auto_brand_id'             => 'nullable|string',
                'auto_model_id'             => 'nullable|integer',
                // 'specific_model'            => 'nullable',
                'registration_year'         => 'nullable|integer|min:1900|max:' . date('Y'),
                'registration_number'       => 'nullable|string',
                'owner'                     => 'nullable|string',
                'fuel_type_id'              => 'nullable|string',
                'kilometer'                 => 'nullable|integer',
                'price_expectations'        => 'nullable|string',
                'city'                      => 'nullable|string',
                'address'                   => 'nullable|string',
                'name'                      => 'nullable|string',
                'mobile_number'             => 'nullable|string',
                'auto_usage_status'         => 'nullable|string',
                'descriptions'              => 'nullable|string',
                'rto'                       => 'nullable|string',
                'condition'                 => 'nullable|string',
                'rc_status'                 => 'nullable|string',
                'loan_status'               => 'nullable|string',
                'noc_status'                => 'nullable|string',
                'fc_status'                 => 'nullable|string',
                'permit_status'             => 'nullable|string',
                'insurance'                 => 'nullable|string',
                'financial_availability'    => 'nullable|string',
                'loan_amount'               => 'nullable|string',
                'first_payment'             => 'nullable|string',
                'emi_amount'                => 'nullable|string',
                'no_of_months'              => 'nullable|string',
                'chellan_status'            => 'nullable|string',
                'accident_status'           => 'nullable|string',
                'orp'                       => 'nullable|string',
                'millage'                   => 'nullable|string',
                'crass_weight'              => 'nullable|string',
                'passenger_capacity'        => 'nullable|string',
                'ground_clearance'          => 'nullable|string',
                'gear'                      => 'nullable|string',
                'vehicle_suitable'          => 'nullable|string',
                'engine_cc'                 => 'nullable|string',
                'free_service'              => 'nullable|string',
                'finance_arrangements'      => 'nullable|string',
                'image_1' => 'nullable|file|image|mimes:jpeg,png,jpg,gif|max:2048',
                'image_2' => 'nullable|file|image|mimes:jpeg,png,jpg,gif|max:2048',
                'image_3' => 'nullable|file|image|mimes:jpeg,png,jpg,gif|max:2048',
                'image_4' => 'nullable|file|image|mimes:jpeg,png,jpg,gif|max:2048',
                'image_5' => 'nullable|file|image|mimes:jpeg,png,jpg,gif|max:2048',
                'image_6' => 'nullable|file|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            $autoModel = AutoModel::findOrFail($request->auto_model_id);
            if($autoModel){
                $validated['specific_model'] = $autoModel->model_name;
            }

            $validated['auto_unique_id']= GenerateId::generateId(new Auto, 'auto_unique_id', 4, 'AT-');

            // new code
            $validated['user_id'] = Auth::id();
            if(Auth::user()->role_id == 1000){ //user post auto staus
                $validated['auto_status'] = "pending";
                $validated['status'] = 2;
                $validated['post_type'] = 'U';
            } else { // admin post auto status
                $validated['auto_status'] = "active";
                $validated['status'] = 1;
                $validated['post_type'] = 'A';
            }
            // end new code

            // $validated['user_id'] = Auth::id();
            // $validated['auto_status'] = "active";
            // $validated['status'] = 1;
            $destinationPath = public_path('uploads/auto_images');

            // Handle image uploads with old file deletion
            foreach (['image_1', 'image_2', 'image_3', 'image_4', 'image_5', 'image_6'] as $imageField) {
                if ($request->hasFile($imageField) && $request->file($imageField)->isValid()) {
                    // Save the new file
                    $file = $request->file($imageField);
                    $fileName = uniqid() . '.' . $file->getClientOriginalExtension();
                    $file->move($destinationPath, $fileName);
                    $validated[$imageField] = 'uploads/auto_images/' . $fileName;
                }
            }
            $auto = Auto::create($validated);
            // $response = $this->firebaseService->sendNotification();
            if(Auth::user()->role_id == 1001){
                $response = $this->firebaseService->sendNotification();
            }
            if ($auto) { // Check if $post is empty
                return ResponseService::success($auto, 'Auto Posted Successfully.');
            } else {
                return ResponseService::error($auto, "Auto not posted, something went wrong.");
            }
        }catch (\Illuminate\Validation\ValidationException $e) {
            return ResponseService::validationError('Validation failed.', $e->errors(), 422);
        }  catch (\Exception $e) {
            return ResponseService::error('An error occurred. Please try again.', ['error' => $e->getMessage()], 500);
        }
    }

    public function getAutoPost(Request $request, $id)
    {
         try {
            $post_auto = Auto::with(['autoBrands', 'autoFuelType', 'autoPriceRange', 'getfavourites' =>function ($query){
                $query->where('user_id', Auth::id())
                ->where('fav_status',1);
                }, 'getEnquiry'])
                ->where('id', $id)
                ->first(); // Fetch a single record


            if ($post_auto) {
                $post = [
                    'auto_id'               => $post_auto->id,
                    'auto_model'            => $post_auto->specific_model ?? null,
                    'user_id'               => $post_auto->user_id,
                    'user_mobile'           => $post_auto->user->phone_number,
                    'registration_year'     => $post_auto->registration_year,
                    'registration_number'   => $post_auto->registration_number,
                    'owner'                 => $post_auto->owner,
                    'image_1'               => $post_auto->image_1,
                    'image_2'               => $post_auto->image_2,
                    'image_3'               => $post_auto->image_3,
                    'image_4'               => $post_auto->image_4,
                    'image_5'               => $post_auto->image_5,
                    'image_6'               => $post_auto->image_6,
                    'kilometer'             => $post_auto->kilometer,
                    'rto'                   => $post_auto->rto,
                    'condition'             => $post_auto->condition,
                    'rc_status'             => $post_auto->rc_status,
                    'noc_status'            => $post_auto->noc_status,
                    'fc_status'             => $post_auto->fc_status,
                    'permit_status'         => $post_auto->permit_status,
                    'insurance'             => $post_auto->insurance,
                    'loan_amount'           => $post_auto->loan_amount,
                    'chellan_status'        => $post_auto->chellan_status,
                    'accident_status'       => $post_auto->accident_status,
                    'loan_status'           => $post_auto->loan_status,
                    'emi_amount'            => $post_auto->emi_amount,
                    'first_payment'         => $post_auto->first_payment,
                    'no_of_month'           => $post_auto->no_of_months,
                    'price_expectations'    => ($post_auto->auto_usage_status === 'new_auto') ? $post_auto->orp : $post_auto->price_expectations ?? null,
                    'descriptions'          => $post_auto->descriptions,
                    'city'                  => $post_auto->city,
                    'address'               => $post_auto->address,
                    'name'                  => $post_auto->name,
                    'mobile_number'         => $post_auto->mobile_number,
                    'auto_usage_status'     => $post_auto->auto_usage_status,
                    'auto_brand'            => $post_auto->autoBrands->brand_name ?? null,
                    'auto_fuel_type'        => $post_auto->autoFuelType->name ?? null,
                    'fav_status'            => $post_auto->getfavourites->status ?? null,
                    'enquiry_status' => $post_auto->getEnquiry()
                        ->where('user_id', Auth::id())
                        ->first()
                        ->interested_status ?? null,


                    'orp'                   => $post_auto->orp ?? null,
                    'millage'               => $post_auto->millage ?? null,
                    'crass_weight'          => $post_auto->crass_weight ?? null,
                    'passenger_capacity'    => $post_auto->passenger_capacity ?? null,
                    'ground_clearance'      => $post_auto->ground_clearance ?? null,
                    'gear'                  => $post_auto->gear ?? null,
                    'vehicle_suitable'      => $post_auto->vehicle_suitable ?? null,

                    'engine_cc'             => $post_auto->engine_cc ?? null,
                    'free_service'          => $post_auto->free_service ?? null,
                    'finance_arrangements'  => $post_auto->finance_arrangements ?? null,
                    'financial_availability' => $post_auto->financial_availability ?? null,
                ];

                if($post_auto->financial_availability === 'true'){

                    $post['loan_amount']    = $post_auto->loan_amount ?? null;
                    $post['first_payment']  = $post_auto->first_payment ?? null;
                    $post['emi_amount']     = $post_auto->emi_amount ?? null;
                    $post['no_of_months']   = $post_auto->no_of_months ?? null;
                }
            } else {
                $post = null; // Return null if no record is found
            }

            if ($post) { // Check if $post is empty
                return ResponseService::success($post, 'No auto posts found.');
            } else {
                return ResponseService::success($post, "Auto posts listed successfully.");
            }
        }catch (\Illuminate\Validation\ValidationException $e) {
            return ResponseService::validationError('Validation failed.', $e->errors(), 422);
        }  catch (\Exception $e) {
            return ResponseService::error('An error occurred. Please try again.', ['error' => $e->getMessage()], 500);
        }
    }

    public function filterBySearch(Request $request){
        $auto_brands = $request->input('auto_brands');
        $auto_details = Auto::with(['autoBrands', 'autoFuelType'])->where('auto_status','active')->where('auto_brand_id',$auto_brands)->get();
        $filter_auto = $this->autoservice->autoDetails($auto_details);
        if($filter_auto){

        return ResponseService::success($filter_auto, "Auto posts listed successfully.");
        }else{
            return ResponseService::success([], "No posts available.");
        }
    }

    public function filters($type, $id){
        $query = Auto::with(['autoBrands', 'autoFuelType'])
                    ->where('auto_status','active');
        if($type == 'fuel'){
            $auto = $query->where('fuel_type_id',$id)->get();
        }else if($type == 'year'){
            $auto = $query->where('registration_year',$id)->get();
        }else if($type == 'price'){
            $auto = $query->where('price_expectations','<=',(int)$id)->get();
        }else if($type == 'model'){
            $auto = $query->where('specific_model','like','%'.$id.'%')->get();
        }else if($type == 'autostatus'){
            $auto = $query->where('auto_usage_status',$id)->get();
        }else{
            $auto = $query->get();
        }
        $data = $this->autoservice->autoDetails($auto);
        if($data){
            return ResponseService::success($data, "Auto posts listed successfully.");
        }else{
            return ResponseService::success([], "No posts available.");
        }
    }


    public function getAllAutoPosts(Request $request)
    {
        try {
            $used_auto_posts = Auto::with(['autoBrands','autoFuelType', 'getfavourites','autoPriceRange'])
                ->where('auto_usage_status', 'used_auto')
                ->where('auto_status', 'active')
                ->orderBy('id','DESC')
                ->get()
                ->shuffle(); // Shuffle the collection

            $used_auto = $this->autoservice->autoDetails($used_auto_posts);

            $new_auto_posts = Auto::with(['autoBrands', 'autoFuelType', 'getfavourites','autoPriceRange'])
                ->where('auto_usage_status', 'new_auto')
                ->where('auto_status', 'active')
                ->orderBy('id','DESC')
                ->get()
            ->shuffle(); // Shuffle the collection
            $new_auto = $this->autoservice->autoDetails($new_auto_posts);

            $bajaj_auto_posts = Auto::with(['autoBrands', 'autoFuelType', 'getfavourites','autoPriceRange'])
                ->where('auto_usage_status', 'bajaj_refinance')
                ->where('auto_status', 'active')
                ->orderBy('id','DESC')
                ->get()
            ->shuffle(); // Shuffle the collection
            $bajaj_auto = $this->autoservice->autoDetails($bajaj_auto_posts);

            $result = [
                'used_auto'         => $used_auto,
                'new_auto'          => $new_auto,
                'bajaj_refinance'   => $bajaj_auto
            ];
            return ResponseService::success($result, "Auto posts listed successfully.");
        }  catch (\Exception $e) {
            return ResponseService::error('An error occurred. Please try again.', ['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Auto $auto)
    {
        return response()->json($auto);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Auto $auto)
    {
        $auto->delete();
        return response()->json(null, 204);
    }

    public function userPostAutos(Request $request)
    {
        try {
            $userId = Auth::user()->id;
            $pending = Auto::with(['autoBrands','autoFuelType', 'AutoPriceRange','cities', 'autoOwners'])
            ->where('user_id',$userId)->where('status',2)->get();

            $active = Auto::with(['autoBrands','autoFuelType', 'AutoPriceRange','cities', 'autoOwners'])
            ->where('user_id',$userId)->where('status',1)->get();

            $reject = Auto::with(['autoBrands','autoFuelType', 'AutoPriceRange','cities', 'autoOwners'])
            ->where('user_id',$userId)->where('status',3)->get();

            $sold = Auto::with(['autoBrands','autoFuelType', 'AutoPriceRange','cities', 'autoOwners'])
            ->where('user_id',$userId)->where('status',0)->get();

            $result = [
                'pending'   => $this->autoservice->autoDetails($pending),
                'active'    => $this->autoservice->autoDetails($active),
                'sold'      => $this->autoservice->autoDetails($sold),
                'reject'    => $this->autoservice->autoDetails($reject),
            ];
            return ResponseService::success($result, "Auto posts listed successfully.");
        }  catch (\Exception $e) {
            return ResponseService::error('An error occurred. Please try again.', ['error' => $e->getMessage()], 500);
        }
    }

    public function userPostAutoUpdate(Request $request,$id)
    {
        try{
            $auto = Auto::findOrFail($id);
            if($auto){

                if($auto->user_id !== Auth::user()->id) {
                    return ResponseService::error($auto, "Unauthorized");
                }

                $validated = $request->validate([
                    'auto_brand_id'             => 'nullable|string',
                    'auto_model_id'             => 'nullable|integer',
                    // 'specific_model'            => 'nullable',
                    'registration_year'         => 'nullable|integer|min:1900|max:' . date('Y'),
                    'registration_number'       => 'nullable|string',
                    'owner'                     => 'nullable|string',
                    'fuel_type_id'              => 'nullable|string',

                    'kilometer'                 => 'nullable|integer',
                    'price_expectations'        => 'nullable|string',
                    'city'                      => 'nullable|string',
                    'address'                   => 'nullable|string',
                    'name'                      => 'nullable|string',
                    'mobile_number'             => 'nullable|string',
                    'auto_usage_status'         => 'nullable|string',
                    'descriptions'              => 'nullable|string',
                    'rto'                       => 'nullable|string',
                    'condition'                 => 'nullable|string',
                    'rc_status'                 => 'nullable|string',
                    'loan_status'               => 'nullable|string',
                    'noc_status'                => 'nullable|string',
                    'fc_status'                 => 'nullable|string',
                    'permit_status'             => 'nullable|string',
                    'insurance'                 => 'nullable|string',

                    'financial_availability'    => 'nullable|string',
                    'loan_amount'               => 'nullable|string',
                    'first_payment'             => 'nullable|string',
                    'emi_amount'                => 'nullable|string',
                    'no_of_months'              => 'nullable|string',

                    'chellan_status'            => 'nullable|string',
                    'accident_status'           => 'nullable|string',

                    'orp'                       => 'nullable|string',
                    'millage'                   => 'nullable|string',
                    'crass_weight'              => 'nullable|string',
                    'passenger_capacity'        => 'nullable|string',
                    'ground_clearance'          => 'nullable|string',
                    'gear'                      => 'nullable|string',
                    'vehicle_suitable'          => 'nullable|string',
                    'engine_cc'                 => 'nullable|string',
                    'free_service'              => 'nullable|string',
                    'finance_arrangements'      => 'nullable|string',

                    'image_1' => 'nullable|file|image|mimes:jpeg,png,jpg,gif|max:2048',
                    'image_2' => 'nullable|file|image|mimes:jpeg,png,jpg,gif|max:2048',
                    'image_3' => 'nullable|file|image|mimes:jpeg,png,jpg,gif|max:2048',
                    'image_4' => 'nullable|file|image|mimes:jpeg,png,jpg,gif|max:2048',
                    'image_5' => 'nullable|file|image|mimes:jpeg,png,jpg,gif|max:2048',
                    'image_6' => 'nullable|file|image|mimes:jpeg,png,jpg,gif|max:2048',
                ]);
                $autoModel = AutoModel::findOrFail($request->auto_model_id);
                if($autoModel){
                    $validated['specific_model'] = $autoModel->model_name;
                }
                if (Auth::user()->role_id == 1000) {
                    $validated['auto_status'] = "pending";
                    $validated['status'] = 2;
                } else {
                    $validated['auto_status'] = "active";
                    $validated['status'] = 1;
                }
                $destinationPath = public_path('uploads/auto_images');
                // Handle image updates
                foreach (['image_1', 'image_2', 'image_3', 'image_4', 'image_5', 'image_6'] as $imageField) {
                    if ($request->hasFile($imageField) && $request->file($imageField)->isValid()) {
                        // Delete old file if exists
                        if (!empty($auto->$imageField) && file_exists(public_path($auto->$imageField))) {
                            unlink(public_path($auto->$imageField));
                        }
                        // Save new file
                        $file = $request->file($imageField);
                        $fileName = uniqid() . '.' . $file->getClientOriginalExtension();
                        $file->move($destinationPath, $fileName);
                        $validated[$imageField] = 'uploads/auto_images/' . $fileName;
                    }
                }
                $auto->update($validated);
                if ($auto) {
                    return ResponseService::success($auto, 'Auto Updated Successfully.');
                } else {
                    return ResponseService::error($auto, "Auto not update, something went wrong.");
                }
            } else {
                return ResponseService::error($auto, "Auto Not Found.");
            }

        } catch (\Illuminate\Validation\ValidationException $e) {
            return ResponseService::validationError('Validation failed.', $e->errors(), 422);

        } catch (\Exception $e) {
            return ResponseService::error('An error occurred. Please try again.', ['error' => $e->getMessage()], 500);
        }
    }
}
