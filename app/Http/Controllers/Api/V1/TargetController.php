<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\AutoMeter;
use App\Models\Target;
use App\Models\Trip;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TargetController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'target_amount' => 'required|numeric|min:1|max:99999999'
        ]);

        try {

            DB::beginTransaction();

            $target = Target::updateOrCreate(
                [
                    'user_id' => Auth::id(),
                    'date'    => now()->toDateString()
                ],
                [
                    'target_amount' => $request->target_amount
                ]
            );
            $target = $target->fresh();
            DB::commit();

            // $date = now()->toDateString();
            $automeet = AutoMeter::where('user_id', Auth::user()->id)
                        ->whereDate('date', now())
                        ->selectRaw('SUM(total_amount) as total_amount, COUNT(*) as total_trips,SUM(total_km) as total_km')
                        ->first();
            $totalMeterAmount = $automeet->total_amount ?? 0;
            $totalMeterTrips  = $automeet->total_trips;
            $totalMeterkm     = $automeet->total_km ?? 0;

            $lastAmount = Trip::where('user_id', Auth::user()->id)
                            ->where('target_id', $target->id)
                            ->latest()
                            ->value('amount') ?? 0;

            return response()->json([
                'status'        => true,
                'message'       => 'Target saved successfully',
                'data'          => $target,
                'meterAmount'   => $totalMeterAmount,
                'meterTrip'     => $totalMeterTrips,
                'meterkm'       => $totalMeterkm,
                'lasttripamount'=> $lastAmount,
            ]);

        } catch (\Exception $e) {

            DB::rollback();

            return response()->json([
                'status'    => false,
                'message'   => 'Something went wrong',
                'error'     => $e->getMessage()
            ], 500);
        }

    }

    public function updateEarning(Request $request)
    {
        $validated = $request->validate([
            'target_id'         => 'required|exists:targets,id',
            'earning_amount'    => 'required|numeric|min:1|max:99999999',
            'source'            => 'required|string',
            'meter_amount'      => 'required|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();
            $target = Target::findOrFail($request->target_id);
            if(!$target){
                return response()->json([
                    'status'    => false,
                    'message'   => 'Something went wrong',
                    'error'     => 'Target Amount Not Found.....'
                ], 500);
            }
            $trip = Trip::create([
                'user_id'   => Auth::user()->id,
                'target_id' => $target->id,
                'amount'    => $request->earning_amount,
                'source'    => $request->source,
            ]);
            $totalEarningAmount = Trip::where('target_id', $target->id)
                                        ->sum('amount');

            $target->update([
                'meter_amount'      => $request->meter_amount,
                'earning_amount'    => $totalEarningAmount,
                'total_amount'      => ($request->meter_amount + $totalEarningAmount),
                'is_completed'      => ($request->meter_amount + $totalEarningAmount) >= $target->target_amount
            ]);

            DB::commit();

            $automeet = AutoMeter::where('user_id', Auth::id())
                            ->whereDate('date', today())
                            ->selectRaw('
                                COALESCE(SUM(total_amount),0) as total_amount,
                                COUNT(*) as total_trips,
                                COALESCE(SUM(total_km),0) as total_km
                            ')
                            ->first();

            return response()->json([
                'status'        => true,
                'message'       => 'Earning updated successfully',
                'data'          => $target,
                'meterAmount'   => $automeet->total_amount ?? 0,
                'meterTrip'     => $automeet->total_trips,
                'meterkm'       => $automeet->total_km ?? 0,
                'lasttripamount'=> $trip->amount,
            ]);
        } catch (\Exception $e) {

            DB::rollback();

            return response()->json([
                'status'    => false,
                'message'   => 'Something went wrong',
                'error'     => $e->getMessage()
            ], 500);
        }
    }

    public function getTodayTarget_old(Request $request)
    {
        try {
            $userId = Auth::id();

            $target = Target::where('user_id', $userId)
                        ->whereDate('date', today())
                        ->first();

            if(!$target){
                return response()->json([
                    'status'    => false,
                    'message'   => 'Something went wrong',
                    'error'     => 'Target Amount Not Set Today.....'
                ], 500);
            }

            // Today Meter
            $automeet = AutoMeter::where('user_id', $userId)
                            ->whereDate('date', today())
                            ->selectRaw('
                                COALESCE(SUM(total_amount),0) as total_amount,
                                COUNT(*) as total_trips,
                                COALESCE(SUM(total_km),0) as total_km
                            ')
                            ->first();

            // Last Trip
            $lastAmount = Trip::where('user_id', $userId)
                            ->where('target_id', $target->id)
                            ->latest('id')
                            ->value('amount') ?? 0;

            // Total Trips
            $totalTrips = Trip::where('user_id', $userId)->count();

            // Total Target
            $totalTarget = Target::where('user_id', $userId)->sum('total_amount');

            // Total Achieved
            $totalAchieved = Trip::where('user_id', $userId)->sum('amount');

            // Today Achieve %
            $todayAchieve = 0;
            if($target->total_amount > 0){
                $todayAchieve = ($automeet->total_amount / $target->total_amount) * 100;
            }

            // Monthly Target
            $monthlyTarget = Target::where('user_id', $userId)
                            ->whereMonth('date', now()->month)
                            ->whereYear('date', now()->year)
                            ->sum('total_amount');

            // Monthly Achieve
            $monthlyAchieve = Trip::where('user_id', $userId)
                            ->whereMonth('created_at', now()->month)
                            ->whereYear('created_at', now()->year)
                            ->sum('amount');

            // Remaining Target
            $remainingTarget = $monthlyTarget - $monthlyAchieve;


            /*
            |--------------------------------------------------------------------------
            | Leaderboard
            |--------------------------------------------------------------------------
            */

            $leaderboard = Target::with('user')
                ->whereDate('date', today())
                ->withCount('trips as total_trips')
                ->withSum('trips as achieved_amount', 'amount')
                ->get()
                ->map(function ($item) {

                    $achieved = $item->achieved_amount ?? 0;
                    $target   = $item->total_amount ?? 0;

                    $percent = $target > 0 ? ($achieved / $target) * 100 : 0;

                    return [
                        'user_id' => $item->user->id ?? null,
                        'name' => $item->user->name ?? '',
                        'target_amount' => $target,
                        'achieved' => $achieved,
                        'total_trips' => $item->total_trips,
                        'achieve_percent' => round($percent,2),
                    ];
                })
                ->sortByDesc('achieve_percent')
                ->sortByDesc('total_trips')
                ->sortByDesc('achieved')
                ->values();

            // Rank Assign
            $leaderboard = $leaderboard->map(function ($item, $key) {
                $item['rank'] = $key + 1;
                return $item;
            });

            // Top Performer
            $topPerformer = $leaderboard->first();

            // User Rank
            $userRank = collect($leaderboard)
                        ->where('user_id', $userId)
                        ->first();


            return response()->json([
                'status'        => true,
                'message'       => 'Data fetched successfully',
                'data'          => $target,

                // Today
                'meterAmount'   => $automeet->total_amount ?? 0,
                'meterTrip'     => $automeet->total_trips ?? 0,
                'meterkm'       => $automeet->total_km ?? 0,
                'lasttripamount'=> $lastAmount,
                'todayAchieve'  => round($todayAchieve,2),

                // Overall
                'totalTrips'    => $totalTrips,
                'totalTarget'   => $totalTarget,
                'totalAchieved' => $totalAchieved,

                // Monthly
                'monthlyTarget' => $monthlyTarget,
                'monthlyAchieve'=> $monthlyAchieve,
                'remainingTarget'=> max($remainingTarget,0),

                // Leaderboard
                'top_performer' => $topPerformer,
                'user_rank'     => $userRank,
                'leaderboard'   => $leaderboard,

            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status'    => false,
                'message'   => 'Something went wrong',
                'error'     => $e->getMessage()
            ], 500);
        }
    }

    public function getTodayTarget(Request $request)
    {
        try {

            $userId = Auth::id();

            /*
            |--------------------------------------------------------------------------
            | Today Target
            |--------------------------------------------------------------------------
            */

            $target = Target::where('user_id', $userId)
                        ->whereDate('date', today())
                        ->first();

            if(!$target){
                return response()->json([
                    'status'    => false,
                    'message'   => 'Target Amount Not Set Today.....'
                ], 200);
            }

            /*
            |--------------------------------------------------------------------------
            | Today Meter
            |--------------------------------------------------------------------------
            */

            $automeet = AutoMeter::where('user_id', $userId)
                            ->whereDate('date', today())
                            ->selectRaw('
                                COALESCE(SUM(total_amount),0) as total_amount,
                                COUNT(*) as total_trips,
                                COALESCE(SUM(total_km),0) as total_km
                            ')
                            ->first();

            /*
            |--------------------------------------------------------------------------
            | Last Trip
            |--------------------------------------------------------------------------
            */

            $lastAmount = Trip::where('user_id', $userId)
                            ->where('target_id', $target->id)
                            ->latest('id')
                            ->value('amount') ?? 0;

            /*
            |--------------------------------------------------------------------------
            | Overall Stats
            |--------------------------------------------------------------------------
            */

            $totalTrips = Trip::where('user_id', $userId)->count();

            $totalTarget = Target::where('user_id', $userId)
                            ->sum('total_amount');

            $totalAchieved = Trip::where('user_id', $userId)
                            ->sum('amount');

            /*
            |--------------------------------------------------------------------------
            | Today Achieve %
            |--------------------------------------------------------------------------
            */

            $todayAchieve = 0;

            if($target->total_amount > 0){
                $todayAchieve = ($automeet->total_amount / $target->total_amount) * 100;
            }

            /*
            |--------------------------------------------------------------------------
            | Monthly Target
            |--------------------------------------------------------------------------
            */

            $monthlyTarget = Target::where('user_id', $userId)
                            ->whereMonth('date', now()->month)
                            ->whereYear('date', now()->year)
                            ->sum('total_amount');

            /*
            |--------------------------------------------------------------------------
            | Monthly Achieve
            |--------------------------------------------------------------------------
            */

            $monthlyAchieve = Trip::where('user_id', $userId)
                            ->whereMonth('created_at', now()->month)
                            ->whereYear('created_at', now()->year)
                            ->sum('amount');

            /*
            |--------------------------------------------------------------------------
            | Remaining Target
            |--------------------------------------------------------------------------
            */

            $remainingTarget = $monthlyTarget - $monthlyAchieve;


            /*
            |--------------------------------------------------------------------------
            | Leaderboard
            |--------------------------------------------------------------------------
            */

            $leaderboard = Target::with('user')
                ->whereDate('date', today())
                ->withCount('trips as total_trips')
                ->withSum('trips as achieved_amount', 'amount')
                ->get()
                ->map(function ($item) {

                    $achieved = $item->achieved_amount ?? 0;
                    $target   = $item->total_amount ?? 0;

                    $percent = $target > 0 
                            ? ($achieved / $target) * 100 
                            : 0;

                    return [
                        'user_id' => $item->user->id ?? null,
                        'name' => $item->user->name ?? '',
                        'target_amount' => (float) $target,
                        'achieved' => (float) $achieved,
                        'total_trips' => $item->total_trips ?? 0,
                        'achieve_percent' => round($percent,2),
                    ];
                })

                // Remove zero data
                ->filter(function ($item) {
                    return !(
                        $item['target_amount'] == 0 &&
                        $item['achieved'] == 0 &&
                        $item['total_trips'] == 0
                    );
                })

                ->sortByDesc('achieve_percent')
                ->sortByDesc('total_trips')
                ->sortByDesc('achieved')
                ->values();


            /*
            |--------------------------------------------------------------------------
            | Rank Assign
            |--------------------------------------------------------------------------
            */

            if($leaderboard->count() > 0){

                $leaderboard = $leaderboard->map(function ($item, $key) {
                    $item['rank'] = $key + 1;
                    return $item;
                });

            } else {

                $leaderboard = collect([]);
            }


            /*
            |--------------------------------------------------------------------------
            | Top Performer
            |--------------------------------------------------------------------------
            */

            $topPerformer = $leaderboard->first();

            if(!$topPerformer){
                $topPerformer = null;
            }


            /*
            |--------------------------------------------------------------------------
            | User Rank
            |--------------------------------------------------------------------------
            */

            $userRank = $leaderboard
                        ->where('user_id', $userId)
                        ->first();

            if(!$userRank){
                $userRank = null;
            }


            /*
            |--------------------------------------------------------------------------
            | Final Response
            |--------------------------------------------------------------------------
            */

            return response()->json([

                'status' => true,
                'message' => 'Data fetched successfully',

                'data' => $target,

                // Today
                'meterAmount' => $automeet->total_amount ?? 0,
                'meterTrip' => $automeet->total_trips ?? 0,
                'meterkm' => $automeet->total_km ?? 0,
                'lasttripamount'=> $lastAmount,
                'todayAchieve' => round($todayAchieve,2),

                // Overall
                'totalTrips' => $totalTrips,
                'totalTarget' => $totalTarget,
                'totalAchieved' => $totalAchieved,

                // Monthly
                'monthlyTarget' => $monthlyTarget,
                'monthlyAchieve'=> $monthlyAchieve,
                'remainingTarget'=> max($remainingTarget,0),

                // Leaderboard
                'top_performer'=> $topPerformer,
                'user_rank'=> $userRank,
                'leaderboard'=> $leaderboard

            ]);

        } catch (\Exception $e) {

            return response()->json([
                'status'=> false,
                'message'=> 'Something went wrong',
                'error'=> $e->getMessage()
            ],500);

        }
    }

    public function updateTodayTargetStatus(Request $request)
    {
        try {
            $userId = Auth::id();
    
            // Fetch today's target
            $target = Target::where('user_id', $userId)
                            ->whereDate('date', today())
                            ->where('status_id',1)
                            ->first();
    
            if (!$target) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Today\'s target not found'
                ], 404);
            }
    
            // Update status to 2
            $target->status_id = 2;
            $target->save();
    
            return response()->json([
                'status'  => true,
                'message' => 'Today target status updated successfully',
                'data'    => $target
            ]);
    
        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Something went wrong',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
}
