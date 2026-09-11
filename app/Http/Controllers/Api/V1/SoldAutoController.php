<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Interfaces\AutoInterface;
use App\Services\ResponseService;
use App\Http\Controllers\Controller;

class SoldAutoController extends Controller
{
    protected $auto_interface;
    public function __construct(AutoInterface $auto_interface){
        $this->auto_interface = $auto_interface;
    }

    public function getSoldAutos()
    {
        try{
            $soldauto = $this->auto_interface->getSoldAutos();
            if ($soldauto) { // Check if $post is empty
                return ResponseService::success($soldauto, 'No Sold Auto Available.');
            } else {
                return ResponseService::success($soldauto, "Sold Auto listed successfully.");
            }
        }catch (\Exception $e) {
            return ResponseService::error('An error occurred. Please try again.', ['error' => $e->getMessage()], 500);
        }  
    }
}
