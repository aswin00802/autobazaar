<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Events;
use App\Models\Masters\AuthorizedSeller;
use App\Models\Masters\AutoAreas;
use App\Models\Masters\AutoBrand;
use App\Models\Masters\AutoFuelType;
use App\Models\Masters\AutoModel;
use App\Models\Masters\AutoOwners;
use App\Models\Masters\AutoPriceRange;
use App\Models\Services\Finance;
use App\Models\spareparts\SparepartsCategories;
use App\Models\spareparts\SparepartsSubCategories;
use App\Models\UserInformation;
use App\Services\MasterService;
use App\Services\ResponseService;
use Carbon\Carbon;
use Google\Service\Classroom\UserProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommonController extends Controller
{
    protected $masterService;
    public function __construct(MasterService $masterService){
        $this->masterService = $masterService;
    }

    public function getAutoBrands(){
        return $this->masterService->getActiveRecords(AutoBrand::class, 'Brands');
    }

    public function getAutoFuelType(){
        return $this->masterService->getActiveRecords(AutoFuelType::class, 'Fuel Type');
    } 

    /** GET api/get-transmission-types — read straight from the master table. */
    public function getAutoTransmissionType()
    {
        return $this->masterList('auto_transmission_types', 'Transmission Types');
    }

    /** GET api/get-auto-body-types — the master table was never created; answer with an empty list, not a 500. */
    public function getAutoBodyTypes()
    {
        return $this->masterList('auto_body_types', 'Auto Body Types');
    }

    private function masterList(string $table, string $label)
    {
        try {
            $rows = \Illuminate\Support\Facades\Schema::hasTable($table)
                ? \Illuminate\Support\Facades\DB::table($table)->where('status', 1)->orderBy('id')->get()
                : collect();

            return ResponseService::success($rows, "{$label} Listed Successfully.");
        } catch (\Throwable $e) {
            return ResponseService::error('An error occurred. Please try again.', [], 500);
        }
    }

    public function getAutoPriceRange(){
       
        return $this->masterService->getActiveRecords(AutoPriceRange::class, 'Price Range');
    } 

    public function getAutoOwners(){
        return $this->masterService->getActiveRecords(AutoOwners::class, 'Auto Owners');
    } 
    public function getAutoAreas(){
        return $this->masterService->getActiveRecords(AutoAreas::class, 'Auto Areas');
    } 
    
    public function getCities(){
        try{
            $cities = City::where('state_id',35)->get();
            if ($cities->isEmpty()) { // Check if $post is empty
                return ResponseService::success($cities, 'No cities found.');
            } else {
                return ResponseService::success($cities, "Cities listed successfully.");
            }
        } catch (\Exception $e) {
            return ResponseService::error("An error occurred: " . safeApiMessage($e));
        }
    }

    public function getModels($brandId)
    {
        try{
            $model = AutoModel::where('brand_id',$brandId)->where('status_id',1)->get(['id','model_name']);
            if ($model->isEmpty()) { // Check if $post is empty
                return ResponseService::success($model, 'No Model found.');
            } else {
                return ResponseService::success($model, "Model listed successfully.");
            }
        } catch (\Exception $e) {
            return ResponseService::error("An error occurred: " . safeApiMessage($e));
        }
        
    }

    public function get_ProductCategories()
    {
        try{
            $categories = SparepartsCategories::where('status_id',1)->get(['id','name','image']);
            if ($categories->isEmpty()) { // Check if $post is empty
                return ResponseService::success($categories, 'No Product Categories found.');
            } else {
                return ResponseService::success($categories, "Product Categories listed successfully.");
            }
        } catch (\Exception $e) {
            return ResponseService::error("An error occurred: " . safeApiMessage($e));
        }
    }

    public function get_ProductSubCategories($categoryId)
    {
        try{
            $subcategories = SparepartsSubCategories::where('category_id',$categoryId)->where('status_id',1)->get(['id','category_id','name','image']);
            if ($subcategories->isEmpty()) { // Check if $post is empty
                return ResponseService::success($subcategories, 'No Product SubCategories found.');
            } else {
                return ResponseService::success($subcategories, "Product SubCategories listed successfully.");
            }
        } catch (\Exception $e) {
            return ResponseService::error("An error occurred: " . safeApiMessage($e));
        }
    }

    public function getFinancePartners(Request $request,$id=NULL)
    {
        try{
            // $finance = Finance::where('status_id',1)->get();
            $query = Finance::where('status_id', 1);
            if (!is_null($id)) {
                $query->where('id', $id);
            }
            $finance = $query->get(['id','finance_name','location','image','address','finance_type']);

            if ($finance->isEmpty()) { // Check if $post is empty
                return ResponseService::success($finance, 'No finance found.');
            } else {
                return ResponseService::success($finance, "finance listed successfully.");
            }
        } catch (\Exception $e) {
            return ResponseService::error("An error occurred: " . safeApiMessage($e));
        }
    }

    public function getAuthorizedPartners(Request $request,$brand=NULL)
    {
        try{
            // $finance = Finance::where('status_id',1)->get();
            $query = AuthorizedSeller::with('brand:id,brand_name,profile')->where('status_id', 1);
            if (!is_null($brand)) {
                $query->where('brand_id', $brand);
            }
            $sellers = $query->get(['id','brand_id','dealer_name','dealer_type','location','image','address']);

            if ($sellers->isEmpty()) { // Check if $post is empty
                return ResponseService::success($sellers, 'No Authorized Seller found.');
            } else {
                return ResponseService::success($sellers, "Authorized Seller listed successfully.");
            }
        } catch (\Exception $e) {
            return ResponseService::error("An error occurred: " . safeApiMessage($e));
        }
    }

    public function getEvents_old(Request $request)
    {
        $todayEvent = Events::with('location:id,name')->whereBetween('created_at', [
                        Carbon::today()->startOfDay(),
                        Carbon::today()->endOfDay()
                    ])->select('id','location_id','title','image','description','map_link','created_at')->get();
        if(!$todayEvent->isEmpty()){
            return ResponseService::success($todayEvent, "Events listed successfully.");
        } else {
            return ResponseService::success($todayEvent, 'No Events Found.');
        }
    }
    public function getEvents_old1(Request $request)
    {
        $today = Carbon::today();

        $events = Events::with('location:id,name')
            ->where('status_id',1)
            ->where(function ($q) use ($today) {

                // Case 1: end_date irukku (multi-day event)
                $q->whereNotNull('end_date')
                ->whereDate('created_at', '<=', $today)
                ->whereDate('end_date', '>=', $today);

            })
            ->orWhere(function ($q) use ($today) {

                // Case 2: end_date illa (single day event)
                $q->whereNull('end_date')
                ->whereDate('created_at', $today);

            })
            ->select(
                'id',
                'location_id',
                'title',
                'image',
                'description',
                'map_link',
                'created_at',
                'end_date'
            )
            ->get();
            // Aswin - 05022026
            $events->transform(function ($event) {
                if ($event->end_date) {
                    $start = Carbon::parse($event->created_at)->startOfDay();
                    $end   = Carbon::parse($event->end_date)->endOfDay();
            
                    $event->start_date = $start->toDateString(); // e.g., '2026-02-05'
                    $event->is_active = now()->between($start, $end) ? 1 : 0;
                } else {
                    $event->start_date = Carbon::parse($event->created_at)->toDateString();
                    $event->is_active = 0; 
                }
            
                return $event;
            });

        if ($events->isNotEmpty()) {
            return ResponseService::success($events, 'Events listed successfully.');
        }

        return ResponseService::success([], 'No Events Found.');
    }

    public function getEvents(Request $request)
    {
        $today = Carbon::today();

        $events = Events::with('location:id,name')
            ->where('status_id',1)
            ->where(function ($q) use ($today) {

                // Case 1: end_date irukku (multi-day event)
                $q->whereNotNull('end_date')
                ->whereDate('created_at', '<=', $today)
                ->whereDate('end_date', '>=', $today);

            })
            ->orWhere(function ($q) use ($today) {

                // Case 2: end_date illa (single day event)
                $q->whereNull('end_date')
                ->whereDate('created_at', $today);

            })
            ->select(
                'id',
                'location_id',
                'title',
                'image',
                'description',
                'map_link',
                'created_at',
                'end_date'
            )
            ->get();
            
            $events->transform(function ($event) {
                if ($event->end_date) {
                    $start = Carbon::parse($event->created_at)->startOfDay();
                    $end   = Carbon::parse($event->end_date)->endOfDay();
            
                    $event->start_date = $start->toDateString(); // e.g., '2026-02-05'
                    $event->is_active = now()->between($start, $end) ? 1 : 0;
                } else {
                    $event->start_date = Carbon::parse($event->created_at)->toDateString();
                    $event->is_active = 0; 
                }
            
                return $event;
            });

        if ($events->isNotEmpty()) {
            return ResponseService::success($events, 'Events listed successfully.');
        }

        return ResponseService::success([], 'No Events Found.');
    }


    public function getLPG_CNG()
    {
        $fuels = AutoFuelType::whereIn('name', ['CNG', 'LPG'])
            ->whereNotNull('price')->where('status',1)
            ->get(['id','name','price']);
        if(!$fuels->isEmpty()){
            return ResponseService::success($fuels, "Fuels listed successfully.");
        } else {
            return ResponseService::success($fuels, 'No Fuels Found.');
        }
    }

    public function getUserAuto(Request $request)
    {
        $user = Auth::user();
        if(!$user){
            return ResponseService::error('Sorry, User Not Found!....');
        }
        $userProfile = UserInformation::where('user_id',$user->id)->first();
        if(!$userProfile){
            UserInformation::create([
                'user_id' => $user->id,
            ]);
        }
        $auto = UserInformation::with('user','autoBrand','autoModel','autoFueltype')->firstWhere('user_id',$user->id);
        $data = [
            'user_id'           => $auto->user_id,
            'username'          => $auto->user->name ?? '',
            'brand_id'          => $auto->brand_id ?? '',
            'model_id'          => $auto->model_id ?? '',
            'fuel_id'           => $auto->fuel_id ?? '',
            'vehicle_no'        => $auto->vehicle_no ?? '',
            'millage'           => $auto->millage ?? '',
        ];
        return response()->json([
            'success' => true,
            'message' => 'Message sent successfully.',
            'data'    => $data
        ]);
    }
}
