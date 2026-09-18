<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\ResponseService;
use App\Services\VehicleCatalogService;
use App\Services\VehicleLeadService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

/**
 * New-vehicle catalogue for the mobile app. Public — browsing, EMI maths and
 * lead forms need no login, exactly like the website.
 */
class VehicleController extends Controller
{
    public function __construct(
        private VehicleCatalogService $catalog,
        private VehicleLeadService $leads,
    ) {
    }

    /** GET /api/vehicles?brand=tvs */
    public function index(Request $request)
    {
        $brand = trim((string) $request->query('brand', ''));
        $vehicles = $brand !== '' ? $this->catalog->byBrand($brand) : $this->catalog->all();

        return ResponseService::success([
            'count' => count($vehicles),
            'brand' => $brand ?: null,
            'vehicles' => $vehicles,
        ], 'Vehicles fetched successfully');
    }

    /** GET /api/vehicles/{slug} */
    public function show(string $slug)
    {
        $model = $this->catalog->findBySlug($slug);
        if (! $model) {
            return ResponseService::error('Vehicle not found', [], 404);
        }

        return ResponseService::success([
            'vehicle' => $this->catalog->detail($model),
            'similar' => $this->catalog->similar($model, 4),
            'lead_options' => [
                'sources' => VehicleLeadService::SOURCES,
                'time_slots' => VehicleLeadService::TIME_SLOTS,
            ],
        ], 'Vehicle fetched successfully');
    }

    /** POST /api/vehicles/emi-calculate  {loan_amount, rate, tenure_months} */
    public function emiCalculate(Request $request)
    {
        try {
            $data = $request->validate([
                'loan_amount' => 'required|numeric|min:1|max:100000000',
                'rate' => 'required|numeric|min:0|max:50',
                'tenure_months' => 'required|integer|min:1|max:120',
            ]);
        } catch (ValidationException $e) {
            return ResponseService::validationError('Validation failed.', $e->errors(), 422);
        }

        $loan = (float) $data['loan_amount'];
        $rate = (float) $data['rate'];
        $months = (int) $data['tenure_months'];

        $emi = $this->catalog->emi(0, $rate, $months, $loan);

        return ResponseService::success([
            'loan_amount' => $loan,
            'rate' => $rate,
            'tenure_months' => $months,
            'emi' => $emi,
            'total_interest' => max(0, $emi * $months - $loan),
            'total_payment' => $emi * $months,
            // Same loan at the standard tenures, for a comparison table in the app.
            'table' => $this->catalog->emiTable($loan, $rate, [12, 24, 36, 48, 60]),
        ], 'EMI calculated successfully');
    }

    /** POST /api/vehicles/{slug}/leads */
    public function storeLead(Request $request, string $slug)
    {
        $model = $this->catalog->findBySlug($slug);
        if (! $model) {
            return ResponseService::error('Vehicle not found', [], 404);
        }

        try {
            $data = $request->validate($this->leads->leadRules(), $this->leads->leadMessages());
        } catch (ValidationException $e) {
            return ResponseService::validationError('Validation failed.', $e->errors(), 422);
        }

        $lead = $this->leads->storeLead($model, $data, $request);

        return ResponseService::success([
            'enquiry_no' => $lead->enquiry_no,
            'source' => $lead->source,
            'lead_status' => $lead->lead_status,
        ], $this->leads->successMessage($lead->source));
    }

    /** POST /api/vehicles/{slug}/reviews */
    public function storeReview(Request $request, string $slug)
    {
        $model = $this->catalog->findBySlug($slug);
        if (! $model) {
            return ResponseService::error('Vehicle not found', [], 404);
        }

        try {
            $data = $request->validate($this->leads->reviewRules(), $this->leads->reviewMessages());
        } catch (ValidationException $e) {
            return ResponseService::validationError('Validation failed.', $e->errors(), 422);
        }

        $review = $this->leads->storeReview($model, $data, $request);

        return ResponseService::success([
            'review_id' => $review->id,
            'review_status' => $review->review_status,
        ], VehicleLeadService::REVIEW_MESSAGE);
    }
}
