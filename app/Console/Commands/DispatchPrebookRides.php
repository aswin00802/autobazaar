<?php

namespace App\Console\Commands;

use App\Models\Customer;
use App\Models\RideRequest;
use App\Services\FairPriceRideDispatchService;
use App\Services\FairPriceRideFlowService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DispatchPrebookRides extends Command
{
    protected $signature = 'fairprice:dispatch-prebook-rides';

    protected $description = 'Dispatch scheduled prebook/hire/tour rides (cash or paid) at scheduled time';

    public function handle(
        FairPriceRideDispatchService $dispatchService,
        FairPriceRideFlowService $flowService
    ): int {
        $rides = RideRequest::whereIn('booking_type', ['prebook', 'hire', 'tour'])
            ->where('status', 'scheduled')
            ->whereIn('payment_status', ['paid', 'not_required'])
            ->whereNull('dispatched_at')
            ->whereNotNull('scheduled_at')
            ->where('scheduled_at', '<=', Carbon::now())
            ->get();

        if ($rides->isEmpty()) {
            $this->info('No scheduled rides ready for dispatch.');

            return self::SUCCESS;
        }

        foreach ($rides as $ride) {
            DB::beginTransaction();

            try {
                $ride = RideRequest::where('id', $ride->id)->lockForUpdate()->first();
                if (
                    !$ride ||
                    $ride->status !== 'scheduled' ||
                    !in_array($ride->payment_status, ['paid', 'not_required'], true) ||
                    $ride->dispatched_at
                ) {
                    DB::rollBack();
                    continue;
                }

                $customer = Customer::find($ride->customer_id);
                if (!$customer) {
                    DB::rollBack();
                    continue;
                }

                $driverCount = $dispatchService->dispatch($ride, $customer);

                $ride->update([
                    'status' => 'pending',
                    'dispatched_at' => now(),
                ]);

                $flowService->notifyCustomer(
                    $ride->fresh(),
                    'Finding Your Driver 🔍',
                    'Your scheduled ride is now active. We are connecting you with nearby drivers.',
                    'ride_dispatched'
                );

                DB::commit();

                $this->info("Ride #{$ride->id} dispatched to {$driverCount} driver(s).");
            } catch (\Throwable $e) {
                DB::rollBack();

                Log::error('fairprice_prebook_dispatch.failed', [
                    'ride_id' => $ride->id ?? null,
                    'message' => $e->getMessage(),
                ]);

                $this->error("Failed dispatch for ride #{$ride->id}: {$e->getMessage()}");
            }
        }

        return self::SUCCESS;
    }
}
