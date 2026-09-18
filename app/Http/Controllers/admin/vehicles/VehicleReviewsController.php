<?php

namespace App\Http\Controllers\admin\vehicles;

use App\Http\Controllers\Controller;
use App\Models\Vehicle\VehicleReview;
use Illuminate\Http\Request;

/** Customer reviews on catalogue models: approve / reject / delete. */
class VehicleReviewsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:vehicle_reviews']);
    }

    public function index(Request $request)
    {
        $status = $request->get('status', 'all');

        $reviews = VehicleReview::with('model:id,name')
            ->when(in_array($status, VehicleReview::STATUSES), fn ($q) => $q->where('review_status', $status))
            ->latest('id')
            ->get();

        $counts = VehicleReview::selectRaw('review_status, COUNT(*) as c')->groupBy('review_status')->pluck('c', 'review_status');

        return view('admin.vehicles.reviews.index', compact('reviews', 'status', 'counts'));
    }

    /** approve | reject */
    public function status(Request $request)
    {
        $request->validate([
            'id'            => 'required|integer',
            'review_status' => 'required|in:approved,rejected,pending',
        ]);

        $review = VehicleReview::findOrFail($request->id);
        $review->review_status = $request->review_status;
        $review->save();

        if ($review->model) {
            $review->model->refreshRating();
        }

        return redirect()->back()->with('success', 'Review ' . $review->review_status);
    }

    public function delete(Request $request)
    {
        $request->validate(['id' => 'required|integer']);

        $review = VehicleReview::findOrFail($request->id);
        $model = $review->model;
        $review->delete();

        if ($model) {
            $model->refreshRating();
        }

        return redirect()->back()->with('success', 'Review deleted');
    }
}
