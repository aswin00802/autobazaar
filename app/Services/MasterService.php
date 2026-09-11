<?php
namespace App\Services;

use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\Model;
use App\Services\ResponseService;

class MasterService
{
    public static function getActiveRecords($model , string $message){
        try {
            $records = $model::where('status', 1)->get();
            
            if ($records->isEmpty()) {
                return ResponseService::error("No records found for {$message}.", [], 404);
            }
            return ResponseService::success($records, "{$message} Listed Successfully.");
        } catch (\Exception $e) {
            return ResponseService::error('An error occurred. Please try again.', [], 500);
        }
    }
 
}