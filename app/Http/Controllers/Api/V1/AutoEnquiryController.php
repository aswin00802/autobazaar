<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Auto\Auto;
use App\Models\Enquiry;
use App\Models\User;
use App\Services\ResponseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\GoogleSheetService;

class AutoEnquiryController extends Controller
{
    public function autoenquiries(Request $request, GoogleSheetService $sheetService)
    {
        try{
            $validatedData = $request->validate([
                                'post_id' => 'required|integer'
                            ]);
            $validatedData['user_id'] = Auth::user()->id;
            $validatedData['interested_status'] = 1;
            $alreadyEnquired = Enquiry::where('post_id', $validatedData['post_id'])
                ->where('user_id', $validatedData['user_id'])
                ->where('interested_status', 1)
                ->exists();

            if ($alreadyEnquired) {
                return ResponseService::success("You already enquired about this auto.");
            }

            $enquiry = Enquiry::create($validatedData);
            $auto = Auto::find($request->post_id);
            $user = Auth::user();
            if($auto && $user){
                if($auto->auto_usage_status === 'new_auto'){
                    $type       = 'Enquiry';
                    $name       = $user->name ?? '';
                    $mobile     = $user->phone_number ?? '';
                    $brand      = $auto->autoBrands?->brand_name ?? '';
                    $model      = $auto->autoModel?->model_name ?? '';
                    $fuel_type  = $auto->autoFueltype?->name ?? '';
                    $capacity   = $auto->passenger_capacity ?? '';
                    $mileage    = $auto->millage ?? '';
                    $location   = $user->autoAreas?->name ?? '';
                    $created_at = $enquiry->created_at->format('Y-m-d H:i:s') ?? '';
                    $usageType  = $$auto->vehicle_suitable ?? '';

                    $spreadsheetId = '1DlOg9Ca-dr3WtuyJIALAwDVZP-ANpWZW3kk2I5eiMsg';
                    $range = 'Sheet1!A:K';
                    // $range = 'Sheet1!B2:K2';
                    $sheetService->insertSingleRow($spreadsheetId, $range, [
                        $type,
                        $name,
                        $mobile,
                        $brand,
                        $model,
                        $fuel_type,
                        $capacity,
                        $mileage,
                        $location,
                        $created_at,
                        $usageType
                    ]);
                } 
                
            }
            return ResponseService::success("Your enquiry has been sent successfully. Our team will call you soon.");

            
            // $enquiry = Enquiry::create($validatedData);
            // return ResponseService::success("Your Enquiries sent successfully, Our team will call back to you soon.");
        }catch (\Illuminate\Validation\ValidationException $e) {
            return ResponseService::validationError('Validation failed.', $e->errors(), 422);
        } catch(\Exception $e){
            return ResponseService::error("An error occurred: " . $e->getMessage());
        }
    }

    public function getAutoEnquiries(Request $request)
    {
        try {
            $enquiries = Enquiry::with(['getAutoPost' => function ($query) {
                $query->with(['autoBrands', 'autoOwners', 'autoFueltype','autoPriceRange']);
            }])->where('user_id', Auth::user()->id)->orderBy('id','desc')->get();

            $result = $enquiries->map(function ($enquiry) {
                $auto = $enquiry->getAutoPost;

                return [
                    'id'                    => $enquiry->id,                   
                    'post_id'               => $enquiry->post_id,                  
                    'status'                => $enquiry->interested_status,
                    'owner'                 => $auto->autoOwners->name ?? '',
                    'image_1'               => $auto->image_1 ?? null,
                    'image_2'               => $auto->image_2 ?? null,
                    'image_3'               => $auto->image_3 ?? null,
                    'kilometer'             => $auto->kilometer ?? null,
                     'price_expectations'   => ($auto->auto_usage_status === 'new_auto') ? $auto->orp : $auto->price_expectations ?? null,
                    // 'price_expectations' => $auto->price_expectations ?? null,
                    'descriptions'          => $auto->descriptions ?? '',
                    'rto'                   => $auto->rto ?? null,
                    'condition'             => $auto->condition ?? null,
                    'rc_status'             => $auto->rc_status ?? null,
                    'noc_status'            => $auto->noc_status ?? null,
                    'fc_status'             => $auto->fc_status ?? null,
                    'permit_status'         => $auto->permit_status ?? null,
                    'insurance'             => $auto->insurance ?? null,
                    'auto_usage_status'     => $auto->auto_usage_status ?? null,
                    'auto_brand'            => $auto->autoBrands->brand_name ?? null,
                    'auto_fuel_type'        => $auto->autoFueltype->name ?? null,
                    'orp'                   => $auto->orp ?? null,
                    'crass_weight'          => $auto->crass_weight ?? null,
                    'passenger_capacity'    => $auto->passenger_capacity ?? null,
                    'ground_clearance'      => $auto->ground_clearance ?? null,
                    'gear'                  => $auto->gear ?? null,
                    'vehicle_suitable'      => $auto->vehicle_suitable ?? null,
                    
                    'engine_cc'             => $auto->engine_cc ?? null,
                    'free_service'          => $auto->free_service ?? null,
                    'finance_arrangements'  => $auto->finance_arrangements ?? null,
                ];
            });
            if ($result) {
                return ResponseService::success($result, "Enquiries Listed Successfully");
            } else {
                return ResponseService::success($result, "No Record Found");
            }
        }catch (\Illuminate\Validation\ValidationException $e) {
            return ResponseService::validationError('Validation failed.', $e->errors(), 422);
        } catch(\Exception $e){
            return ResponseService::error("An error occurred: " . $e->getMessage());
        }
    }
}
