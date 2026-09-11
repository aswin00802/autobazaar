<?php

namespace App\Http\Controllers\admin\service;

use App\Http\Controllers\Controller;
use App\Models\Masters\AutoFuelType;
use App\Models\Services\GasStation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class GasStationController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:gas_station'])->only('index');
        $this->middleware(['permission:add_gas_station'])->only(['create','store']);
        $this->middleware(['permission:edit_gas_station'])->only(['edit', 'update']);
        $this->middleware(['permission:delete_gas_station'])->only(['destroy']);
        $this->middleware(['permission:upload_gas_station'])->only(['blukUpload']);
    }

    public function index()
    {
        $gasStations = GasStation::where('status_id',1)->get();
        return view('admin.service.gas.index', compact('gasStations'));
    }

    public function create()
    {
        $fuels = AutoFuelType::all();
        return view('admin.service.gas.create',compact('fuels'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'fuel_id'   => 'required',
            'name'      => 'required',
            'location'  => 'required',
            'address'   => 'required',
            'map_link'  => 'required'
        ]);
        
        if(GasStation::where('name',$request->name)->exists()){
            return redirect()->route('services.gas-station.create')->with('error','This Auto GasStation Already Exit');
        } else {
            $gastation = new GasStation();
            $gastation->user_id     = Auth::user()->id;
            $gastation->fuel_id     = $request->fuel_id;
            $gastation->name        = $request->name;
            $gastation->address     = $request->address;
            $gastation->location    = $request->location;
            $gastation->map_link    = $request->map_link;
            $gastation->save();
            return redirect()->route('services.gas-station')->with('success','Auto GasStation Successfully Created.');
        }
        
    }

    public function edit($id)
    {
        $fuels = AutoFuelType::all();
        $gasStation   = GasStation::findOrFail($id);
        return view('admin.service.gas.edit',compact('gasStation','fuels'));
    }

    public function update(Request $request)
    {
        $validatedData = $request->validate([
            'fuel_id'   => 'required',
            'name'      => 'required',
            'location'  => 'required',
            'address'   => 'required',
            'map_link'  => 'required'
        ]);
        $gastation              = GasStation::findOrFail($request->id);
        $gastation->fuel_id     = $request->fuel_id;
        $gastation->name        = $request->name;
        $gastation->address     = $request->address;
        $gastation->location    = $request->location;
        $gastation->map_link    = $request->map_link;
        $gastation->save();
        return redirect()->route('services.gas-station')->with('success','Auto GasStation Successfully Updated.');
    }

    public function destroy(Request $request)
    {
        $finance = GasStation::find($request->id);
        if ($finance) {
            $finance->status_id = 2;
            $finance->save();
            return response()->json(array('success' => true, 'message' => 'Auto GasStation Deleted Successfully'));
        } else {
            return response()->json(array('success' => false, 'message' => 'Auto GasStation Not Found?...'));
        }
    }

    public function blukUpload(Request $request)
    {
        $request->validate([
            'upload_file' => 'required|file|mimes:xlsx,xls|max:5120'
        ]);
        $rows = Excel::toArray([], $request->file('upload_file'))[0];

        if (count($rows) <= 1) {
            return response()->json([
                'status' => false,
                'message' => 'Excel file is empty'
            ], 422);
        }

       

        unset($rows[0]); //Remove header row

        $missingMasters = [];
        $duplicateRows  = [];
        $insertData     = [];

        foreach ($rows as $index => $row) {

            $rowNo = $index + 1;

            $fuelType = trim($row[1] ?? '');
            $name     = trim($row[2] ?? '');
            $location = trim($row[3] ?? '');
            $address  = trim($row[4] ?? '');
            $mapLink  = trim($row[5] ?? '');

            //Required validation
            if (!$fuelType || !$name || !$location || !$address) {
                return response()->json([
                    'status' => false,
                    'message' => "Required fields missing at row {$rowNo}"
                ], 422);
            }

            //Fuel type validation
            if (!in_array($fuelType, ['Petrol','Diesel','CNG','LPG','Electric'])) {
                return response()->json([
                    'status' => false,
                    'message' => "Invalid fuel type at row {$rowNo}"
                ], 422);
            }

            //Master lookup
            $master = AutoFuelType::where('name', $fuelType)
                ->first();

            if (!$master) {
                $missingMasters[] = "$name - $location (Row $rowNo)";
                continue;
            }

            //Duplicate check
            $exists = GasStation::where('fuel_id', $master->id)
                ->where('name', $name)
                ->where('location', $location)
                ->exists();

            if ($exists) {
                $duplicateRows[] = "$name - $location ($fuelType) - Row $rowNo";
                continue;
            }

            // ✅ Prepare insert
            $insertData[] = [
                'fuel_id'       => $master->id,
                'name'          => $name,
                'location'      => $location,
                'address'       => $address,
                'map_link'      => $mapLink,
                'user_id'       => Auth::user()->id,
                'created_at'    => now(),
                'updated_at'    => now(),
            ];
        }

        //Missing master response
        if (!empty($missingMasters)) {
            return response()->json([
                'status' => false,
                'message' => 'Gas station master not found',
                'missing_masters' => [
                    'Gas Station' => $missingMasters
                ]
            ], 422);
        }

        //Duplicate response
        if (!empty($duplicateRows)) {
            return response()->json([
                'status' => false,
                'message' => 'Duplicate fuel type already exists',
                'missing_masters' => [
                    'Duplicate Entry' => $duplicateRows
                ]
            ], 422);
        }

        //Bulk insert
        GasStation::insert($insertData);

        return response()->json([
            'status' => true,
            'message' => 'Gas station data uploaded successfully'
        ]);
    }
}
