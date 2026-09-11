<?php
namespace App\Services;

use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\Model;
use App\Services\ResponseService;
use Illuminate\Support\Facades\Auth;

class AutoService
{
    public static function autoDetails($auto_details){
        $auto_details=  $auto_details->map(function ($auto){
            $statusMap = [
                'used_auto'        => 'Used Auto',
                'new_auto'         => 'New Auto',
                'bajaj_refinance'  => 'Bajaj Refininance Auto',
                'private_cargo'    => 'Private Cargo Auto',
            ];
            $auto_info =  [
                    'auto_id' => $auto->id ?? null,
                    'auto_model' => $auto->specific_model ?? null,
                    'registration_year' => $auto->registration_year ?? null,
                    'registration_number' => $auto->registration_number ?? null,
                    'image_1' => $auto->image_1 ?? null,
                    'image_2' => $auto->image_2 ?? null,
                    'image_3' => $auto->image_3 ?? null,
                    'image_4' => $auto->image_4 ?? null,
                    'image_5' => $auto->image_5 ?? null,
                    'image_6' => $auto->image_6 ?? null,
                    'owner' => $auto->owner ?? null,
                    'kilometer' => $auto->kilometer ?? null,
                    'price_expectations' => ($auto->auto_usage_status === 'new_auto') ? $auto->orp : $auto->price_expectations ?? null,
                    'descriptions' => $auto->descriptions ?? null,
                    'rto' => $auto->rto ?? null,
                    'condition' => $auto->condition ?? null,
                    'rc_status' => $auto->rc_status ?? null,
                    'noc_status' => $auto->noc_status ?? null,
                    'fc_status' => $auto->fc_status ?? null,
                    'permit_status' => $auto->permit_status ?? null,
                    'insurance' => $auto->insurance ?? null,
                    'financial_availability' => $auto->financial_availability ?? null,
                    'city' => $auto->city ?? null,
                    'address' => $auto->address ?? null,
                    'name' => $auto->name ?? null,
                    'mobile_number' => $auto->mobile_number ?? null,
                    // 'auto_usage_status' => ($auto->auto_usage_status === 'used_auto') ? 'Used Auto' : 'New Auto' ?? null,
                    'auto_usage_status' => $statusMap[$auto->auto_usage_status] ?? null,
                    'auto_brand' => $auto->autoBrands->brand_name ?? null, // Directly select brand name
                    'auto_body_type' => $auto->autoBodytype->name ?? null, // Directly select body type name
                    'auto_fuel_type' => $auto->autoFueltype->name ?? null, // Directly select fuel type name
                    'auto_status' => $auto->auto_status ?? null, // Directly select fuel type name

                    'orp' => $auto->orp ?? null,
                    'millage' => $auto->millage ?? null,
                    'crass_weight' => $auto->crass_weight ?? null,
                    'passenger_capacity' => $auto->passenger_capacity ?? null,
                    'ground_clearance' => $auto->ground_clearance ?? null,
                    'gear' => $auto->gear ?? null,
                    'vehicle_suitable' => $auto->vehicle_suitable ?? null,
                     'engine_cc' => $post_auto->engine_cc ?? null,
                    'free_service' => $post_auto->free_service ?? null,
                    'finance_arrangements' => $post_auto->finance_arrangements ?? null,

                    'fav_user_id' => $auto->getfavourites->user_id ?? 0,
                    'fav_status' => $auto->getfavourites()
                        ->where('user_id', Auth::id())
                        ->pluck('fav_status')
                        ->first() ?? 0, // Retrieve the status or default to 0 if not found

            ];
            if($auto->financial_availability ==='true'){
                        $auto_info['loan_amount'] =$auto->loan_amount;
                        $auto_info['first_payment'] =$auto->first_payment;
                        $auto_info['emi_amount'] =$auto->emi_amount;
                        $auto_info['no_of_months'] =$auto->no_of_months;
                    }
            return $auto_info;
        });

        return $auto_details;


    }

}
