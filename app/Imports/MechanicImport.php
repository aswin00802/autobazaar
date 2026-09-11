<?php
namespace App\Imports;

use App\Models\Services\Mechanic;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;

class MechanicImport implements ToModel, WithHeadingRow
{
    private $duplicates = [];

    public function model(array $row)
    {
       
         
        $row = array_change_key_case(array_map('trim', $row), CASE_LOWER);
        
        if ($this->isEmptyRow($row)) {
            return null; // Skip empty rows
        }
       

        // Check if the mechanic shop already exists
        $existingMechanic = Mechanic::where('shop_name', $row['shop_name'] ?? ($row[1] ?? null))->where('location', $row['location'] ?? ($row[2] ?? null))->first();

        if ($existingMechanic) {
            // Store duplicate records in session to ask for confirmation
            $this->duplicates[] = [
                'shop_name' => $row['shop_name'] ?? ($row[1] ?? null),
                'location'  => $row['location'] ?? ($row[2] ?? null),
                'address'   => $row['address'] ?? ($row[3] ?? null),
                'contact'   => $row['contact'] ?? ($row[4] ?? null),
                'alternate' => $row['alternate'] ?? ($row[5] ?? null),
                'category'  => $row['category'] ?? ($row[6] ?? null),
            ];
            return null; // Skip saving duplicate records for now
        }
        // Save the mechanic record
        $mechanic =  new Mechanic([
            'shop_name'   => $row['shop_name'] ?? null,
            'location'    => $row['location'] ?? null,
            'address'     => $row['address'] ?? null,
            'contact'     => $row['contact'] ?? null,
            'alternate'   => $row['alternate'] ?? null,
            'category'    => $row['category'] ?? null,
            'user_id'     => Auth::id(),
            'created_at'  => Carbon::now(),
        ]);
        
        return $mechanic;
    }

    public function __destruct()
    {
        if (!empty($this->duplicates)) {
            Session::put('duplicate_mechanics', $this->duplicates);
        }
    }

    private function isEmptyRow($row)
    {
        return empty($row['fuel']) && empty($row['name']) && empty($row['location']) 
            && empty($row['address']) && empty($row['map']);
    }
}

// <?php
// namespace App\Imports;

// use App\Models\Services\Mechanic;
// use App\Models\AutoFuelType;
// use Maatwebsite\Excel\Concerns\ToModel;
// use Maatwebsite\Excel\Concerns\WithHeadingRow;
// use Illuminate\Support\Facades\Auth;
// use Carbon\Carbon;

// class MechanicImport implements ToModel, WithHeadingRow
// {
//     public function model(array $row)
//     {
//         // Normalize column keys (trim and lowercase)
//         $row = array_change_key_case(array_map('trim', $row), CASE_LOWER);

//         if ($this->isEmptyRow($row)) {
//             return null; // Skip empty rows
//         }

//         $mechanic =  new Mechanic([
//             'shop_name'      => isset($row['name']) ? $row['name'] : ($row[0]?? null),
//             'location'  => isset($row['location'])? $row['location'] :($row[1] ?? null),
//             'address'   =>isset($row['address'])? $row['address'] :($row[2] ?? null),
//             'contact'   => isset($row['contact'])? $row['contact'] :($row[3] ?? null),
//             'alternate'   => isset($row['alternate'])? $row['alternate'] :($row[4] ?? null),
//             'category'   => isset($row['category'])? $row['category'] :($row[5] ?? null),
//             'user_id'   => Auth::id(),
//             'created_at'=> Carbon::now(),
//         ]);
//         return $mechanic;
//     }

//     /**
//      * Validate if a row has meaningful data.
//      */
//     private function isEmptyRow($row)
//     {
//         return empty($row['fuel']) && empty($row['name']) && empty($row['location']) 
//             && empty($row['address']) && empty($row['map']);
//     }
// }
