<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Auto\Auto;
use Illuminate\Http\Request;
use App\Services\ResponseService;
use App\Http\Controllers\Controller;

class SearchController extends Controller
{
    public function getKeywordSearch(Request $request)
    {
        try {
            // Retrieve the search query
            $search = $request->input('search');

            // Start building the query
            $query = Auto::with(['autoBrands', 'autoFuelType', 'autoPriceRange']);

            if ($search) {
                $keywords = explode(' ', $search); // Split search term into words

                $query->where(function ($q) use ($keywords) {
                    foreach ($keywords as $word) {
                        $q->orWhereHas('autoBrands', function ($subQuery) use ($word) {
                            $subQuery->where('brand_name', 'LIKE', "%{$word}%");
                        });
                        $q->orWhereHas('autoFuelType', function ($subQuery) use ($word) {
                            $subQuery->where('name', 'LIKE', "%{$word}%");
                        });

                        // Add more general fields if needed
                        $q->orWhere('price_expectations', '<=', (int)$word); // If numeric, treat as price
                    }
                });
            }

            // Execute the query
            $autos = $query->get();

            if ($autos->isEmpty()) {
                return ResponseService::error('No matching autos found.', [], 404);
            }

            // Transform the data (optional)
            $result = $autos->map(function ($auto) {
                return [
                    'auto_id' => $auto->id,
                    'brand_name' => $auto->autoBrands->brand_name ?? null,
                    'price_expectations' => $auto->price_expectations ?? null,
                
                    'fuel_type' => $auto->autoFuelType->name ?? null,
                    // Add other fields as needed
                ];
            });

            return ResponseService::success($result, "Autos matched successfully.");
        } catch (\Exception $e) {
            return ResponseService::error('An error occurred. Please try again.', ['error' => safeApiMessage($e)], 500);
        }
    }
}
