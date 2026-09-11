<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Models\Auto\Quotation;
use App\Services\ResponseService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Auto\Auto;
use App\Services\GoogleSheetService;

class QuotationController extends Controller
{
    public function storeQuotation(Request $request, GoogleSheetService $sheetService)
    {
        try{
            $validatedData = $request->validate([
                                'post_id' => 'nullable|integer'
                            ]);
            // $validatedData['user_id'] = Auth::user()->id;
            $quotation = new Quotation();
            $quotation->user_id = Auth::user()->id;
            $quotation->post_id = $request->post_id;
            $quotation->save();

            $auto = Auto::find($request->post_id);
            $user = Auth::user();
            if($auto && $user){
                if($auto->auto_usage_status === 'new_auto'){
                    $type       = 'Quotation';
                    $name       = $user->name ?? '';
                    $mobile     = $user->phone_number ?? '';
                    $brand      = $auto->autoBrands?->brand_name ?? '';
                    $model      = $auto->autoModel?->model_name ?? '';
                    $fuel_type  = $auto->autoFueltype?->name ?? '';
                    $capacity   = $auto->passenger_capacity ?? '';
                    $mileage    = $auto->millage ?? '';
                    $location   = $user->autoAreas?->name ?? '';
                    $created_at = $quotation->created_at->format('Y-m-d H:i:s') ?? '';
                    $usageType  = $auto->vehicle_suitable ?? '';

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

            // Quotation::create($validatedData);
            return ResponseService::success("Your Free Quotation Generate successfully. Our team will call you soon.");


            // $enquiry = Enquiry::create($validatedData);
            // return ResponseService::success("Your Enquiries sent successfully, Our team will call back to you soon.");
        }catch (\Illuminate\Validation\ValidationException $e) {
            return ResponseService::validationError('Validation failed.', $e->errors(), 422);
        } catch(\Exception $e){
            return ResponseService::error("An error occurred: " . $e->getMessage());
        }
    }
}
