<?php
namespace App\Repositories;

use App\Interfaces\AutoInterface;
use App\Services\AutoService;
use App\Models\Auto\Auto;

class AutoRepository implements AutoInterface{
    protected $autoservice;
    public function __construct(AutoService $autoservice){
        $this->autoservice = $autoservice;
    }
    
    public function getAutoById($id){
        $post_auto = Auto::with(['autoBrands', 'autoFuelType', 'autoPriceRange', 'getfavourites', 'getEnquiry'])
                ->where('id', $id)
                ->first();
        return $post_auto;

    }

    public function getSoldAutos(){
        $sold_auto = Auto::with(['autoBrands', 'autoFuelType'])
        ->where('auto_status', 'sold')
        ->orderBy('id', 'DESC')->get();

         $auto = $this->autoservice->autoDetails($sold_auto);
      
        return $auto;
    }
}
