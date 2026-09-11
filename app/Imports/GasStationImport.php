<?php
namespace App\Imports;

use App\Models\Services\GasStation;
use App\Models\Masters\AutoFuelType;
// use Maatwebsite\Excel\Concerns\ToModel;
// use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

use Maatwebsite\Excel\Concerns\{
    ToModel,
    WithHeadingRow,
    WithValidation,
    SkipsOnFailure,
    SkipsFailures
};

class GasStationImport implements ToModel,WithHeadingRow,WithValidation,SkipsOnFailure
{
    use SkipsFailures;

    protected array $missingMasters = [];
    protected array $duplicateRows = [];

    public function model(array $row)
    {
        $fuelType = trim($row['fuel_type']);
        $name     = trim($row['gas_station_name']);
        $location = trim($row['location']);

        // 🔍 MASTER LOOKUP
        $master = AutoFuelType::where('name', $fuelType)->first();

        if (!$master) {
            $this->missingMasters[] = "$fuelType";
            return null;
        }

        // 🔥 DUPLICATE CHECK
        $exists = GasStation::where('fuel_id', $master->id)
            ->where('name', $name)
             ->where('location', $location)
            ->exists();

        if ($exists) {
            $this->duplicateRows[] = "$name - $location ($fuelType)";
            return null;
        }

        return new GasStation([
            'fuel_id'   => $master->id,  
            'name'      => $name,
            'location'  => $location,
            'address'   => trim($row['address']),
            'map_link'  => trim($row['map_link']),  
            'user_id'   => Auth::id(),
            'created_at'=> Carbon::now(),
        ]);
    }

    public function rules(): array
    {
        return [
            '*.fuel_type'        => 'required|in:Petrol,Diesel,CNG,EV',
            '*.gas_station_name' => 'required|string',
            '*.location'         => 'required|string',
            '*.address'          => 'required|string',
            '*.map_link'         => 'nullable|url',
        ];
    }

    public function getMissingMasters(): array
    {
        return $this->missingMasters;
    }

    public function getDuplicateRows(): array
    {
        return $this->duplicateRows;
    }
}
