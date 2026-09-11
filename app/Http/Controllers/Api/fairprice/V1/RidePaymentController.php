<?php

namespace App\Http\Controllers\Api\fairprice\V1;

use App\Http\Controllers\Controller;
use App\Models\RideRequest;
use App\Services\FairPriceRideFlowService;
use App\Services\RazorpayPaymentService;
use Illuminate\Http\Request;

class RidePaymentController extends Controller
{
    private RazorpayPaymentService $paymentService;
    private FairPriceRideFlowService $flowService;

    public function __construct(
        RazorpayPaymentService $paymentService,
        FairPriceRideFlowService $flowService
    ) {
        $this->paymentService = $paymentService;
        $this->flowService = $flowService;
    }

    public function createOrder(Request $request)
    {
        $validated = $request->validate([
            'ride_id' => 'required|integer',
        ]);

        $customer = auth('customer')->user();
        $ride = RideRequest::where('id', $validated['ride_id'])
            ->where('customer_id', $customer->id)
            ->whereIn('booking_type', ['prebook', 'hire', 'tour'])
            ->where('status', 'scheduled')
            ->first();

        if (!$ride) {
            return response()->json([
                'status' => false,
                'message' => 'Advance payment ride not found',
            ], 404);
        }

        if ($ride->payment_status === 'paid') {
            return response()->json([
                'status' => false,
                'message' => 'Payment already completed for this ride',
            ], 422);
        }

        try {
            $order = $this->paymentService->createRideOrder($ride);

            return response()->json([
                'status' => true,
                'message' => 'Payment order created',
                'ride_id' => (int) $ride->id,
                'booking_type' => $ride->booking_type,
                'amount' => (int) $order['amount'],
                'currency' => $order['currency'],
                'razorpay_order_id' => $order['order_id'],
                'razorpay_key' => $order['key'],
                'estimated_fare' => round((float) $ride->estimated_fare, 2),
                'advance_amount' => round((float) $ride->advance_amount, 2),
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function verifyPayment(Request $request)
    {
        $validated = $request->validate([
            'ride_id' => 'required|integer',
            'razorpay_order_id' => 'required|string',
            'razorpay_payment_id' => 'required|string',
            'razorpay_signature' => 'required|string',
        ]);

        $customer = auth('customer')->user();
        $ride = RideRequest::where('id', $validated['ride_id'])
            ->where('customer_id', $customer->id)
            ->whereIn('booking_type', ['prebook', 'hire', 'tour'])
            ->where('status', 'scheduled')
            ->first();

        if (!$ride) {
            return response()->json([
                'status' => false,
                'message' => 'Advance payment ride not found',
            ], 404);
        }

        if ($ride->payment_status === 'paid') {
            return response()->json([
                'status' => true,
                'message' => 'Payment already verified',
                'ride_id' => (int) $ride->id,
                'payment_status' => $ride->payment_status,
            ]);
        }

        try {
            $this->paymentService->verifyAndCapture(
                $ride,
                $validated['razorpay_order_id'],
                $validated['razorpay_payment_id'],
                $validated['razorpay_signature']
            );

            $ride = $ride->fresh();
            $paidAmount = (float) ($ride->advance_amount ?? 0);

            $this->flowService->notifyCustomer(
                $ride,
                'Payment Confirmed ✅',
                '₹' . number_format($paidAmount, 2) . ' advance received. Your '
                    . $ride->booking_type . ' is confirmed.',
                'advance_payment_confirmed',
                [
                    'amount' => (string) $paidAmount,
                    'booking_type' => (string) $ride->booking_type,
                    'scheduled_at' => (string) $ride->scheduled_at,
                ]
            );

            return response()->json([
                'status' => true,
                'message' => 'Payment verified successfully',
                'ride_id' => (int) $ride->id,
                'booking_type' => $ride->booking_type,
                'payment_status' => 'paid',
                'advance_amount' => round((float) $ride->advance_amount, 2),
                'scheduled_at' => $ride->scheduled_at,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => false,
                'message' => 'Payment verification failed: ' . $e->getMessage(),
            ], 422);
        }
    }
}
