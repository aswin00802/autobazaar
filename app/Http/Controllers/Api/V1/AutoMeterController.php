<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\AutoMeter;
use App\Models\Masters\AutoFuelType;
use App\Models\Slogan;
use App\Models\UserInformation;
use App\Services\ResponseService;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Http\Resources\InvoiceResource;

class AutoMeterController extends Controller
{
    public function autoMeterSave_old(Request $request)
    {
        try{
            $validatedData = $request->validate([
                'brand_id'      => 'required|integer',
                'model_id'      => 'required|integer',
                'fuel_id'       => 'required|integer',
                'vehicle_no'    => 'required|string',
                'millage'       => 'required|integer',
                'total_km'      => 'required|integer',
                'time'          => 'required|string',
                'from_location' => 'required|string',
                'to_location'   => 'required|string',
                'tips'          => 'required|integer',
            ]);
            $user = Auth::user();
            if(!$user){
                return ResponseService::error('Sorry, User Not Found!....');
            }
            //user information update
            $userProfile = UserInformation::firstOrNew(
                ['user_id' => $user->id]
            )->tap(function($profile) use ($request) {
                $data = [
                    'brand_id'   => $request->brand_id,
                    'model_id'   => $request->model_id,
                    'fuel_id'    => $request->fuel_id,
                    'vehicle_no' => $request->vehicle_no,
                    'millage'    => $request->millage,
                ];
                // Only assign changed values
                foreach($data as $key => $value){
                    if($profile->$key != $value){
                        $profile->$key = $value;
                    }
                }
                // Save only if new or dirty
                if(!$profile->exists || $profile->isDirty()){
                    $profile->save();
                }
            });
            //invoice no
            $invoiceNo = 1;
            //km price calculation
            $basePrice          = 50;
            $baseKm             = 1.8;
            $afterbase_km_price = 18;
            if($request->total_km > $baseKm){
                $totalKm = ($request->total_km - $baseKm);
                $kmPrice = ($totalKm * $afterbase_km_price);
            } else {
                $totalKm = $baseKm;
                $kmPrice = $basePrice;
            }
            //fuel expance
            $fuel           = AutoFuelType::findOrFail($request->fuel_id);
            $fuelPrice      = $fuel->price ?? 0;
            $fuelUsed       = ($request->total_km / $request->millage); // liters used
            $fuelExpense    = $fuelUsed * $fuelPrice; // total cost
            //friction losses
            $friction_losses = 0;
            //driver wages
            $totalHours     = 10;       // total hours
            $totalCost      = 1200;     // total cost
            $usedMinutes    = $request->time; // minutes used
            $hourlyRate     = $totalCost / $totalHours; //hourly rate
            $timeUsedHr     = $usedMinutes / 60; //used time
            $driverwages    = round($hourlyRate * $timeUsedHr,2); //total wages cost

            $totalExpance   = $fuelExpense + $friction_losses + $driverwages;
            $totalIncome    = $basePrice + $kmPrice + $request->tips;
            $dutyMargin     = $totalIncome - $totalExpance;
            
        }catch (\Illuminate\Validation\ValidationException $e) {
            return ResponseService::validationError('Validation failed.', $e->errors(), 422);
        } catch(\Exception $e){
            return ResponseService::error("An error occurred: " . $e->getMessage());
        }
    }

    public function autoMeterSave(Request $request)
    {
        try{
            $data = $request->validate([
                'brand_id'      => 'required|integer',
                'model_id'      => 'required|integer',
                'fuel_id'       => 'required|integer',
                'vehicle_no'    => 'required|string',
                'millage'       => 'required|integer',
                'total_km'      => 'required|integer',
                'time'          => 'required|string',
                'from_location' => 'required|string',
                'to_location'   => 'required|string',
                'tips'          => 'required|integer',
            ]);
            $user = Auth::user();
            if(!$user){
                return ResponseService::error('Sorry, User Not Found!....');
            }
            //user information update
            UserInformation::updateOrCreate(
                ['user_id' => $user->id],
                Arr::only($data, [
                    'brand_id','model_id','fuel_id','vehicle_no','millage'
                ])
            );
            //invoice no
            $invoice = $this->generateInvoiceNumber();

            list($hours, $minutes) = explode(':', $data['time']);
            $usedMinutes = ($hours * 60) + $minutes;
            // Fuel
            $fuelPrice = AutoFuelType::where('id', $data['fuel_id'])->value('price') ?? 0;
            // Calculations
            $kmIncome        = $this->calculateKmPrice($data['total_km']);
            $fuelExpense     = $this->calculateFuelExpense($data['total_km'], $data['millage'], $fuelPrice);
            $driverWages     = $this->calculateDriverWages($usedMinutes);
            $tips            = $data['tips'] ?? 0;
            // $friction_losses = (3000 / $request->total_km);
            $friction_losses = $this->calculateFrictionLoss($request->total_km);

            $totalExpense = $fuelExpense + $driverWages + $friction_losses;
            $totalIncome  = $kmIncome + $tips;
            $dutyMargin   = round($totalIncome - $totalExpense, 2);

            $autoMeter = AutoMeter::create([
                'user_id'           => $user->id,
                'invoice_no'        => $invoice['invoice_no'],
                'invoice_inc_id'    => $invoice['invoice_seq'],
                'from_location'     => $request->from_location,
                'to_location'       => $request->to_location,
                'total_km'          => $request->total_km, 
                'total_time'        => $request->time,
                'km_amount'         => $kmIncome, 
                'fuel_amount'       => $fuelExpense, 
                'friction_amount'   => $friction_losses, 
                'wages_amount'      => $driverWages, 
                'tips_amount'       => $tips, 
                'margin_amount'     => $dutyMargin, 
                'total_amount'      => $totalIncome, 
                'date'              => date('Y-m-d'),
                'status_id'         => 2,
            ]);
            $autoMeter->load([
                'user:id,name,phone_number',
                'user.userinfo:id,user_id,brand_id,model_id,fuel_id,vehicle_no,millage',
            ]);
            $slogan = Slogan::inRandomOrder()->first();
            return response()->json([
                'status' => true,
                'message' => 'Auto meter created successfully',
                'data' => $autoMeter,
                'slogan' => [
                    'line_1' => $slogan->line_1 ?? '',
                    'line_2' => $slogan->line_2 ?? '',
                ]
            ]);
            
        }catch (\Illuminate\Validation\ValidationException $e) {
            return ResponseService::validationError('Validation failed.', $e->errors(), 422);
        } catch(\Exception $e){
            return ResponseService::error("An error occurred: " . $e->getMessage());
        }
    }

    private function calculateKmPrice($totalKm)
    {
        $basePrice = 50;
        $baseKm = 1.8;
        $afterBaseKmPrice = 18;

        if ($totalKm <= $baseKm) {
            return $basePrice;
        }

        return $basePrice + (($totalKm - $baseKm) * $afterBaseKmPrice);
    }

    private function calculateFuelExpense($totalKm, $millage, $fuelPrice)
    {
        if ($millage <= 0) return 0;

        return round(($totalKm / $millage) * $fuelPrice, 2);
    }

    private function calculateDriverWages($usedMinutes)
    {
        $totalHours = 10;
        $totalCost = 1200;

        $hourlyRate = $totalCost / $totalHours;
        return round(($usedMinutes / 60) * $hourlyRate, 2);
    }


    private function generateInvoiceNumber()
    {
        $year = date('Y');

        $lastInvoice = DB::table('auto_meters')
            ->latest()
            ->value('invoice_inc_id');

        if ($lastInvoice) {
            $newNumber = $lastInvoice + 1;
        } else {
            $newNumber = 1;
        }
        //INV-2026-000123
        return [
            'invoice_seq' => $newNumber,
            'invoice_no'  => 'INV-' . $year . '-' . str_pad($newNumber, 5, '0', STR_PAD_LEFT),
        ];
        // return 'INV-' . $year . '-' . str_pad($newNumber, 6, '0', STR_PAD_LEFT);
    }

    private function calculateFrictionLoss($tripKm)
    {
        $monthlyMaintenance = 3000;
        $monthlyKm = 6000; // average monthly running km

        $perKmMaintenance = $monthlyMaintenance / $monthlyKm;

        return round($tripKm * $perKmMaintenance, 2);
    }

    public function getInvoiceHistroy(Request $request)
    {
        try{
            $data = $request->validate([
                // 'from_date' => 'required|date_format:d-m-Y',
                // 'to_date'   => 'required|date_format:d-m-Y|after_or_equal:from_date',
                'from_date' => 'nullable|date_format:d-m-Y',
                'to_date'   => 'nullable|date_format:d-m-Y',
                'invoice_id' => 'nullable|integer',
            ]);
            $user = Auth::user();
            if(!$user){
                return ResponseService::error('Sorry, User Not Found!....');
            }
            $userInfo = UserInformation::where('user_id',$user->id)->first();
            // $user->load([
            //     'userInformation.autoBrand',
            //     'userInformation.autoModel',
            //     'userInformation.autoFueltype'
            // ]);
            $autoInformation = [
                'name'        => $user->name,
                'phone'       => $user->phone_number,
                'vehicle_no'  => $userInfo->vehicle_no ?? null,
                'millage'     => $userInfo->millage ?? null,
                'brand'       => $userInfo->autoBrand->brand_name ?? null,
                'model'       => $userInfo->autoModel->model_name ?? null,
                'fuel'        => $userInfo->autoFueltype->name ?? null,
            ];
            $slogan = Slogan::inRandomOrder()->first();
            if($request->invoice_id){

                $invoice = AutoMeter::where('id',$request->invoice_id)
                            ->where('user_id',$user->id)
                            ->first();

                if(!$invoice){
                    return ResponseService::error('Invoice not found');
                }

                return ResponseService::success([
                    'auto_information' => $autoInformation,
                    'invoice' => new InvoiceResource($invoice),
                    'slogan' => [
                        'line_1' => $slogan->line_1 ?? '',
                        'line_2' => $slogan->line_2 ?? '',
                    ]
                ],'Invoice Details');
            }
            // $fromDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->format('Y-m-d');
            // $toDate   = Carbon::createFromFormat('d-m-Y', $request->to_date)->format('Y-m-d');
            // $fromDate = $request->from_date
            //     ? Carbon::createFromFormat('d-m-Y', $request->from_date)->format('Y-m-d')
            //     : Carbon::now()->startOfMonth()->format('Y-m-d');
            $fromDate = $request->from_date
                ? Carbon::createFromFormat('d-m-Y', $request->from_date)->format('Y-m-d')
                : Carbon::now()->format('Y-m-d');

            $toDate = $request->to_date
                ? Carbon::createFromFormat('d-m-Y', $request->to_date)->format('Y-m-d')
                : Carbon::now()->format('Y-m-d');

            $invoices = AutoMeter::whereDate('date', '>=', $fromDate)
                                ->whereDate('date', '<=', $toDate)
                                ->where('user_id',$user->id)
                                ->where('status_id', 2)
                                ->orderBy('invoice_inc_id')
                                ->get();
            

            if ($invoices->isEmpty()) {
                return ResponseService::error('Data not found');
            }
             $totals = [
                'km_amount'       => $invoices->sum('km_amount'),
                'margin_amount'   => $invoices->sum('margin_amount'),
                'fuel_amount'     => $invoices->sum('fuel_amount'),
                'driver_wages'    => $invoices->sum('wages_amount'),
                'friction_amount' => $invoices->sum('friction_amount'),
                'tips_amount'     => $invoices->sum('tips_amount'),
                'total_amount'    => $invoices->sum('total_amount'),
            ];
            
            return ResponseService::success([
                'auto_information' => $autoInformation,
                'totals'   => $totals,
                'invoices' => InvoiceResource::collection($invoices),
                'slogan' => [
                    'line_1' => $slogan->line_1 ?? '',
                    'line_2' => $slogan->line_2 ?? '',
                ]
            ],'Invoice History');

        } catch (\Illuminate\Validation\ValidationException $e) {
            return ResponseService::validationError('Validation failed.', $e->errors(), 422);
        } catch(\Exception $e){
            return ResponseService::error("An error occurred: " . $e->getMessage());
        }
    }
}
