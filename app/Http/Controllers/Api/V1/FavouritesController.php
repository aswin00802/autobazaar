<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Models\Auto\Favourites;
use App\Services\ResponseService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class FavouritesController extends Controller
{
    public function addFavorite(Request $request)
    {
        try {
            $request->validate([
                'post_id'       => 'required|exists:auto_posts,id',
                'fav_status'    => 'required|boolean',
            ]);
            $userId = Auth::id();
            $favorite = Favourites::updateOrCreate(
                ['user_id'  => $userId, 'post_id' => $request->post_id],
                ['fav_status'   => $request->fav_status]
            );
            $message = $favorite->fav_status ? 'Auto added to your favorites Successfully' : 'Removed from favorites Successfully';
            return ResponseService::success($message);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return ResponseService::validationError('Validation failed.', $e->errors(), 422);
        } catch (\Exception $e) {
            return ResponseService::error('An error occurred. Please try again.', [], 500);
        }
    }

    
     public function getUserFavorites()
    {
        try {
            $userId = Auth::user()->id;
            if (!$userId) {
                return ResponseService::CheckUser();
            }

            $favorites = Favourites::select('post_id', 'fav_status')
                ->with(['auto' => function ($query) {
                    $query->with(['autoBrands','autoOwners', 'autoFueltype', 'autoPriceRange']);
                }])
                ->where('user_id', $userId)
                ->where('fav_status', 1)
                ->orderBy('id','desc')
                ->get();
            $result = $favorites->map(function ($favorite) {
                $auto = $favorite->auto;
                return [
                    'post_id'               => $favorite->post_id ?? null,
                    'fav_status'            => $favorite->fav_status ?? null,
                    
                    'user_id'               => $auto->user_id ?? null,
                    'auto_model'            => $auto->specific_model ?? null, 
                    'registration_year'     => $auto->registration_year ?? null,
                    'registration_number'   => $auto->registration_number ?? null,
                    'owner'                 => $auto->autoOwners->owner ?? null,
                    'image_1'               => $auto->image_1 ?? null,
                    'image_2'               => $auto->image_2 ?? null,
                    'image_3'               => $auto->image_3 ?? null,
                    'image_4'               => $auto->image_4 ?? null,
                    'image_5'               => $auto->image_5 ?? null,
                    'image_6'               => $auto->image_6 ?? null,
                    'kilometer'             => $auto->kilometer ?? null,
                    'rto'                   => $auto->rto ?? null,
                    'condition'             => $auto->condition ?? null,
                    'rc_status'             => $auto->rc_status ?? null,
                    'noc_status'            => $auto->noc_status ?? null,
                    'fc_status'             => $auto->fc_status ?? null,
                    'permit_status'         => $auto->permit_status ?? null,
                    'insurance'             => $auto->insurance ?? null,
                  
                    'price_expectations'    => ($auto && $auto->auto_usage_status === 'new_auto') ? $auto->orp : ($auto->price_expectations ?? null),
                    // 'descriptions'       => $auto->descriptions,
                    'city'                  => $auto->city ?? null,
                    'address'               => $auto->address ?? null,
                    'name'                  => $auto->name ?? null,
                    'mobile_number'         => $auto->mobile_number ?? null,
                    'auto_usage_status'     => $auto->auto_usage_status ?? null,
                    'auto_brand'            => $auto->autoBrands->brand_name ?? null, // Directly select brand name

                    'orp'                   => $auto->orp ?? null,
                    'crass_weight'          => $auto->crass_weight ?? null,
                    'passenger_capacity'    => $auto->passenger_capacity ?? null,
                    'ground_clearance'      => $auto->ground_clearance ?? null,
                    'gear'                  => $auto->gear ?? null,
                    'vehicle_suitable'      => $auto->vehicle_suitable ?? null,
                    
                    'engine_cc'             => $post_auto->engine_cc ?? null,
                    'free_service'          => $post_auto->free_service ?? null,
                    'finance_arrangements'  => $post_auto->finance_arrangements ?? null,
                    'auto_fuel_type'        => $auto->autoFueltype->name ?? null, // Directly select fuel type name
                    'financial_availability'=> $auto->financial_availability ?? null, // Directly select fuel type name
                    
                ];
            });
            if ($result->isEmpty()) {
                return ResponseService::success($result, "No Data Available.");
            } else {
                return ResponseService::success($result, "Favourites Listed Successfully."); // Return the flattened result here
            }
        } catch (\Exception $e) {
            return ResponseService::error("An error occurred: " . safeApiMessage($e));
        }
    }
}
