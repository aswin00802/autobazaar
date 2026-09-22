<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Chat;
use App\Models\Services\EmergencyService;
use App\Models\Services\Finance;
use App\Models\Services\GasStation;
use App\Models\Services\Insurance;
use App\Models\Services\Mechanic;
use App\Models\Services\ReFinance;
use App\Models\Services\RtoService;
use App\Models\User;
use App\Services\CommonFirebaseNotification;
use App\Services\ResponseService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ServiceController extends Controller
{
    public function getGasStation(Request $request, $type=NULL,$id=NULL)
    {
        try{
            // $gas_station = GasStation::with('fuel')->groupBy('fuel_id')->get();
            $query = GasStation::with('fuel');
            if($type){
                $query->where('fuel_id',$type);
            }
            if($id){
                $query->where('id',$id);
            }
            if(!$type){
                $query->groupBy('fuel_id');
            }
            $result = $query->get()
            ->map(fn ($gas) => [
                'id'        => $gas->id,
                'fuel_id'   => $gas->fuel_id,
                'fuel_type' => optional($gas->fuel)->name,
                'name'      => $gas->name,
                'location'  => $gas->location,
                'address'   => $gas->address,
                'map_link'  => $gas->map_link,
            ]);

            if ($result->isEmpty()) { // Check if $post is empty
                return ResponseService::success($result, 'No GasStation is Available.');
            } else {
                return ResponseService::success($result, "GasStation Listed Successfully.");
            }

            // $gas_station = GasStation::with('fuel')
            // ->orderBy('name')
            // ->get()
            // ->map(function($gas){
            //     return[
            //         'fuel_type' => $gas->fuel->name ?? Null,
            //         'name'      => $gas->name ?? Null,
            //         'location'  => $gas->location ?? Null,
            //         'address'   => $gas->address ?? Null,
            //         'map_link'  => $gas->map_link ?? Null,
            //         ];
            // });

            // if ($gas_station->isEmpty()) { // Check if $post is empty
            //     return ResponseService::success($gas_station, 'No GasStation is Available.');
            // } else {
            //     return ResponseService::success($gas_station, "GasStation Listed Successfully.");
            // }

        }  catch (\Exception $e) {
            return ResponseService::error('An error occurred. Please try again.', ['error' => safeApiMessage($e)], 500);
        }

    }

    public function getMechanic(){
        try{
            $mechanic = Mechanic::orderBy('shop_name')->get();

            if ($mechanic->isEmpty()) { // Check if $post is empty
                return ResponseService::success($mechanic, 'No GasStation is Available.');
            } else {
                return ResponseService::success($mechanic, "GasStation Listed Successfully.");
            }

        }  catch (\Exception $e) {
            return ResponseService::error('An error occurred. Please try again.', ['error' => safeApiMessage($e)], 500);
        }

    }

    public function storeInsurance(Request $request){
        $validate = $request->validate([
            'brand_id'              =>'nullable',
            'specific_model'        =>'nullable',
            'registration_year'     =>'nullable',
            'registration_number'   =>'nullable',
            'status'                =>'nullable',
        ]);
        $validate['user_id']= Auth::id();
        $insurance = Insurance::create($validate);
        if($insurance){
            return ResponseService::success([], 'Insurance Request Submitted Successfully.');

        }else{
            return ResponseService::error('sorry something went wrong on your request.');
        }
    }

    public function storeReFinance(Request $request)
    {
        try {

            $validated = $request->validate([
                'brand_id'              => 'required',
                'specific_model'        => 'required',
                'registration_year'     => 'required',
                'registration_number'   => 'required',
            ]);


            $validated['user_id']    = Auth::id();
            $validated['created_at'] = Carbon::now();
            $finance = ReFinance::create($validated);
            if($finance){
                return ResponseService::success([], 'Finance Request Submitted Successfully.');
            }else{
                return ResponseService::error('sorry something went wrong on your request.');
            }

        } catch (\Illuminate\Validation\ValidationException $e) {
          return ResponseService::error('sorry something went wrong on your request.');
        } catch (\Exception $e) {
          return ResponseService::error('sorry something went wrong on your request.');
        }
    }

    public function getFinance(){
        try{

            $finance = Finance::all();
            if ($finance->isEmpty()) { // Check if $post is empty
                return ResponseService::success($finance, 'Finance Displayed Available.');
            } else {
                return ResponseService::success($finance, "Finance is empty.");
            }

        }  catch (\Exception $e) {
            return ResponseService::error('An error occurred. Please try again.', ['error' => safeApiMessage($e)], 500);
        }

    }

    public function storeRtoService(Request $request)
    {
        try {
            $validated = $request->validate([
                'rto_number'        => 'required',
                'service_type'      => 'required',
            ]);
            $validated['user_id']       = Auth::id();
            $validated['status']        = 'requested';
            $validated['created_at']    = Carbon::now();
            $rto = RtoService::create($validated);
            if($rto){
                return ResponseService::success([], 'RTO Service Request Submitted Successfully.');
            }else{
                return ResponseService::error('sorry something went wrong on your request.');
            }

        } catch (\Illuminate\Validation\ValidationException $e) {
          return ResponseService::error('sorry something went wrong on your request.');
        } catch (\Exception $e) {
          return ResponseService::error('sorry something went wrong on your request.');
        }
    }

    public function storeEmergencyService(Request $request)
    {
        try {
            $validated = $request->validate([
                'emergency_type' => 'required',
                'vehicle_status' => 'required',
                'vehicle_number' => 'required',
                'description'    => 'required',
                'location'       => 'required|string',
                'land_mark'      => 'nullable|string',
            ]);

            $validated['user_id']    = Auth::id();
            $validated['status']     = 'requested';
            $validated['created_at'] = Carbon::now();

            $emergency = EmergencyService::create($validated);

            $chat = new Chat();
            $chat->user_id= $emergency->user_id;
            $chat->message = 'Emergency Alert! Name: '.$emergency->user->name .' Mobile No: '.$emergency->user->phone_number. 'vehicle Number: '.$emergency->vehicle_number.' Emergency Type:'. $emergency->emergency_type.' Emergency Location:'.$emergency->location.' Emergency Description: '.$emergency->description.'.';
            $chat->message_type = 'text';
            $now = Carbon::now();
            $chat->message_date = $now->toDateString(); // e.g., 2025-06-05
            $chat->message_time = $now->toTimeString(); // e.g., 16:42:00
            $chat->save();

            //emergency notification send
            $lat       = $request->latitude;
            $lng       = $request->longitude;
            $city      = $request->city;
            $senderId  = Auth::id();
            //2km distance user
            $users = User::selectRaw("
                        id, device_token,
                        ( 6371 * acos(
                            cos(radians(?)) *
                            cos(radians(latitude)) *
                            cos(radians(longitude) - radians(?)) +
                            sin(radians(?)) *
                            sin(radians(latitude))
                        )) AS distance
                    ", [$lat, $lng, $lat])
                    ->where('role_id', 1000)
                    ->whereNotNull('device_token')
                    ->where('id', '!=', $senderId)
                    ->where('latitude', '!=', 0)
                    ->where('longitude', '!=', 0)
                    ->having('distance', '<=', 2)
                    ->orderBy('distance')
                    ->get();
            //5km distance user
            if ($users->isEmpty()) {
                $users = User::selectRaw("
                        id, device_token,
                        ( 6371 * acos(
                            cos(radians(?)) *
                            cos(radians(latitude)) *
                            cos(radians(longitude) - radians(?)) +
                            sin(radians(?)) *
                            sin(radians(latitude))
                        )) AS distance
                    ", [$lat, $lng, $lat])
                    ->where('role_id', 1000)
                    ->whereNotNull('device_token')
                    ->where('id', '!=', $senderId)
                    ->where('latitude', '!=', 0)
                    ->where('longitude', '!=', 0)
                    ->having('distance', '<=', 5)
                    ->orderBy('distance')
                    ->get();
            }
            //city users
            if ($users->isEmpty()) {
                $users = User::where('role_id', 1000)
                    ->whereNotNull('device_token')
                    ->where('id', '!=', $senderId)
                    ->where('latitude', '!=', 0)
                    ->where('longitude', '!=', 0)
                    // ->where('city', 'LIKE', $city.'%')
                    ->where(function ($q) use ($city) {
                        $q->where('city', 'LIKE', $city.'%')
                        ->orWhere('city', 'LIKE', '% '.$city.'%')
                        ->orWhere('city', 'LIKE', '%'.$city);
                    })
                    ->get();
            }

            if ($users->isEmpty()) {
                return ResponseService::success(
                    [],
                    'Emergency Service Request Submitted Successfully.'
                );
            }

            //end emergency notificaiton send

            // $users = User::where('role_id', 1000)
            //     ->whereNotNull('device_token')
            //     ->where('id','1846')
            //     ->get();
            $tokens   = $users->pluck('device_token')->toArray();
            $userIds  = $users->pluck('id')->toArray();
            $title    = '🚨 EMERGENCY ALERT';
            $body     = Auth::user()->name.' is in an emergency near'.$request->location .'. Udane help pannunga ';
            $dataPayload = [
                'type'               => 'emergency',
                'emergency_id'       => $emergency->id,
                'url'                => 'get/'.$emergency->id.'/emergency-service',
                'android_channel_id' => 'emergency_v5',
            ];
            $firebase = new CommonFirebaseNotification();
            $firebase->sendCommonNotification($tokens, $title, $body, ['payload' => json_encode($dataPayload)], $userIds);

            if ($emergency) {
                return ResponseService::success([], 'Emergency Service Request Submitted Successfully.');
            } else {
                return ResponseService::error('Sorry, something went wrong with your request.');
            }

        } catch (\Illuminate\Validation\ValidationException $e) {
            return ResponseService::error('Validation failed: ' . safeApiMessage($e));
        } catch (\Exception $e) {
            return ResponseService::error('Sorry, something went wrong with your request.');
        }
    }

    public function getEmergency(Request $request,$id)
    {
        $emergency = EmergencyService::find($id);
        if(!$emergency){
            return ResponseService::error('Sorry, No Emergency Service.');
        }
        $data = [
            'id'             => $emergency->id,
            'name'           => $emergency->user->name ?? '',
            'phone'          => $emergency->user->phone_number ?? '',
            'emergency_type' => $emergency->emergency_type,
            'location'       => $emergency->location,
            'description'    => $emergency->description,
            'vehicle_number' => $emergency->vehicle_number,
            'land_mark'      => $emergency->land_mark,
        ];
        // $chat = new Chat();
        // $chat->user_id= $emergency->user_id;
        // $chat->message = 'Emergency Alert! Name: '.$emergency->user->name .' Mobile No: '.$emergency->user->phone_number. ' Emergency Type:'. $emergency->emergency_type.' Emergency Location:'.$emergency->location.' Emergency Description: '.$emergency->description.'.';
        // $chat->message_type = 'text';
        // $now = Carbon::now();
        // $chat->message_date = $now->toDateString(); // e.g., 2025-06-05
        // $chat->message_time = $now->toTimeString(); // e.g., 16:42:00
        // $chat->save();
        return response()->json([
            'success' => true,
            'message' => 'Message sent successfully.',
            'data'    => $data
        ]);

    }
}
