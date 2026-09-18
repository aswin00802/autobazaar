<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\VehicleCatalogService;
use App\Services\VehicleLeadService;
use Illuminate\Http\Request;

/**
 * Lead forms on the vehicle detail page: Enquire / Quotation / Test drive /
 * Loan, plus customer reviews. Answers JSON to the Alpine modal (fetch) and
 * redirects back with a flash when JavaScript is off.
 */
class VehicleLeadController extends Controller
{
    public function __construct(
        private VehicleCatalogService $catalog,
        private VehicleLeadService $leads,
    ) {
    }

    public function storeLead(Request $request, string $slug)
    {
        $model = $this->catalog->findBySlug($slug);
        abort_if(! $model, 404);

        $data = $request->validate($this->leads->leadRules(), $this->leads->leadMessages());

        $lead = $this->leads->storeLead($model, $data, $request);
        $message = $this->leads->successMessage($lead->source);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'enquiry_no' => $lead->enquiry_no,
                'source' => $lead->source,
            ]);
        }

        return redirect()->back()->with('success', $message . ' Reference: ' . $lead->enquiry_no);
    }

    public function storeReview(Request $request, string $slug)
    {
        $model = $this->catalog->findBySlug($slug);
        abort_if(! $model, 404);

        $data = $request->validate($this->leads->reviewRules(), $this->leads->reviewMessages());

        $review = $this->leads->storeReview($model, $data, $request);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => VehicleLeadService::REVIEW_MESSAGE,
                'review_id' => $review->id,
            ]);
        }

        return redirect()->back()->with('success', VehicleLeadService::REVIEW_MESSAGE);
    }
}
