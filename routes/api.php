<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\ChatController;
use App\Http\Controllers\Api\V1\UserController;
use App\Http\Controllers\Api\V1\CommonController;
use App\Http\Controllers\Api\V1\SearchController;
use App\Http\Controllers\Api\V1\ServiceController;
use App\Http\Controllers\Api\V1\SoldAutoController;
use App\Http\Controllers\Api\V1\QuotationController;
use App\Http\Controllers\Api\V1\Auth\LoginController;
use App\Http\Controllers\Api\V1\FavouritesController;
use App\Http\Controllers\Api\V1\AutoDetailsController;
use App\Http\Controllers\Api\V1\AutoEnquiryController;
use App\Http\Controllers\Api\V1\Auth\OTPAuthController;
use App\Http\Controllers\Api\V1\Auth\RegisterController;
use App\Http\Controllers\Api\V1\Auth\SocialLoginController;
use App\Http\Controllers\Api\V1\AutoMeterController;
use App\Http\Controllers\Api\V1\FarePriceController;
use App\Http\Controllers\Api\V1\spare_parts\ProductsController;
use App\Http\Controllers\Api\V1\TargetController;
use App\Http\Controllers\Api\V1\VehicleController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::get('/test-v1', function () {
    return response()->json(['message' => 'API v1 is working']);
});

// New-vehicle catalogue (public): cards, detail, EMI maths, lead + review forms
Route::prefix('vehicles')->group(function () {
    Route::get('/', [VehicleController::class, 'index'])->name('api.vehicles.index');
    Route::post('emi-calculate', [VehicleController::class, 'emiCalculate'])->name('api.vehicles.emi');
    Route::get('{slug}', [VehicleController::class, 'show'])->name('api.vehicles.show');
    Route::middleware('throttle:10,1')->group(function () {
        Route::post('{slug}/leads', [VehicleController::class, 'storeLead'])->name('api.vehicles.leads');
        Route::post('{slug}/reviews', [VehicleController::class, 'storeReview'])->name('api.vehicles.reviews');
    });
});

//user register
Route::post('auth/register', [RegisterController::class, 'register']);

//Normal User Login
Route::post('auth/login', [LoginController::class, 'login']);

//user send otp
Route::post('auth/sendOtp', [OTPAuthController::class, 'sendOtp'])->name('auth.sendOtp');
Route::post('auth/user-sendOtp', [OTPAuthController::class, 'sendOtpNew'])->name('auth.user-sendOtp');
//user verify otp
Route::post('auth/verifyOtp', [OTPAuthController::class, 'verifyOtp'])->name('auth.verifyOtp');
Route::post('auth/user-verifyOtp', [OTPAuthController::class, 'verifyOtpNew'])->name('auth.user-verifyOtp');

//OAuth Login
Route::get('auth/{provider}/redirect', [SocialLoginController::class, 'apiRedirectToProvider']);
Route::get('auth/{provider}/callback', [SocialLoginController::class, 'apiHandleProviderCallback']);

//common api
Route::get('get-auto-brands', [CommonController::class, 'getAutoBrands'])->name('get-auto-brands');
Route::get('get-auto-body-types', [CommonController::class, 'getAutoBodyTypes'])->name('get-auto-body-types');
Route::get('get-fuel-types', [CommonController::class, 'getAutoFuelType'])->name('get-fuel-types');
Route::get('get-price-ranges', [CommonController::class, 'getAutoPriceRange'])->name('get-price-ranges');
Route::get('get-transmission-types', [CommonController::class, 'getAutoTransmissionType'])->name('get-transmission-types');
Route::get('get-cities', [CommonController::class, 'getCities'])->name('get-cities');
Route::get('get-areas', [CommonController::class, 'getAutoAreas'])->name('get-areas');
Route::get('get-owners', [CommonController::class, 'getAutoOwners'])->name('get-owners');
Route::post('get-model/{brandId}', [CommonController::class, 'getModels'])->name('get-model');
Route::get('get-finance-partments/{id?}', [CommonController::class, 'getFinancePartners'])->name('get-finance-partments');
Route::get('get-authorized-seller-partments/{brandID?}', [CommonController::class, 'getAuthorizedPartners'])->name('get-authorized-seller-partments');
Route::get('get-events', [CommonController::class, 'getEvents'])->name('get-events');

//after auth routes
Route::middleware(['auth:sanctum'])->group(function () {
    //user profile get and edit
    Route::get('get-profile', [UserController::class, 'getProfile'])->name('get-profile');
    Route::post('edit-personal-info',[UserController::class,'editPersonalInfo'])->name('edit-personal-info');
    Route::post('edit-profile-picture', [UserController::class, 'editProfilePicture'])->name('edit-profile-picture');
    Route::post('update-driver-profile', [UserController::class, 'updateDriverProfile'])->name('update-driver-profile');
    Route::post('fetch-location',[UserController::class,'fetchLocation'])->name('fetch-location');
    Route::post('update-driver-location',[UserController::class,'updateLocation'])->name('update-driver-location');
    Route::post('user-logout',[UserController::class,'logout'])->name('user-logout');
    Route::post('user/select-mode',[UserController::class,'selectMode'])->name('user-select-mode');   
    //store user device token using notificatiion
    Route::post('device-token',[UserController::class,'deviceToken'])->name('device-token');
    //auto apis
    // API resource: no HTML create/edit form routes (they returned an empty 200).
    Route::resource('auto_posts', AutoDetailsController::class)->except(['create', 'edit']);
    Route::get('get-posts/{id}', [AutoDetailsController::class, 'getAutoPost'])->name('get-posts');
    Route::get('get-all-posts', [AutoDetailsController::class, 'getAllAutoPosts'])->name('get-all-posts');
    // Filters
    Route::post('filter-by-search', [AutoDetailsController::class, 'filterBySearch'])->name('filter-by-search');
    Route::get('filters/{type}/{id}', [AutoDetailsController::class, 'filters'])->name('filters');
    //search autos api
    Route::post('keyword-search', [SearchController::class, 'getKeywordSearch'])->name('keyword-search');

    //new api
    Route::get('/view-user-auto',[AutoDetailsController::class, 'userPostAutos']);
    Route::post('/update-user-auto/{id}',[AutoDetailsController::class, 'userPostAutoUpdate']);
    //get sold autos
    Route::get('get-sold-auto', [SoldAutoController::class, 'getSoldAutos'])->name('get-sold-auto');
    //send enquiry and get enquiry
    Route::post('auto-enquiries', [AutoEnquiryController::class, 'autoenquiries'])->name('auto-enquiries');
    Route::get('get-auto-enquiries', [AutoEnquiryController::class, 'getAutoEnquiries'])->name('get-auto-enquiries');
    //chat api
    Route::post('/chat/send', [ChatController::class, 'sendMessage']);
    Route::get('/chat/messages', [ChatController::class, 'getMessages']);
    Route::put('/chat/update/{id}', [ChatController::class, 'updateMessage']);
    Route::delete('/chat/delete/{id}', [ChatController::class, 'deleteMessage']);
    Route::post('/driver-request', [ChatController::class, 'saveDriverRequest']);
    //favourites api
    Route::post('add-favourites', [FavouritesController::class, 'addFavorite']);
    Route::get('/get-favourites', [FavouritesController::class, 'getUserFavorites']);
    //service api
    // Route::get('get-gas-station', [ServiceController::class, 'getGasStation'])->name('get-gas-station');
    Route::get('get-mechanic', [ServiceController::class, 'getMechanic'])->name('get-mechanic');
    Route::get('get-finance', [ServiceController::class, 'getFinance'])->name('get-finance');
    Route::post('store-insurance', [ServiceController::class, 'storeInsurance'])->name('storeInsurance');
    Route::post('re-finance', [ServiceController::class, 'storeReFinance'])->name('re-finance');
    Route::post('rto-service', [ServiceController::class, 'storeRtoService'])->name('rto-service');
    Route::post('emergency-service', [ServiceController::class, 'storeEmergencyService'])->name('emergency-service');
    Route::get('get-gas-station/{type?}/{id?}', [ServiceController::class, 'getGasStation'])->name('get-gas-station');

    Route::get('get/{id}/emergency-service', [ServiceController::class, 'getEmergency'])->name('get.emergency-service');

    //get product api
    Route::get('get-product-categories', [CommonController::class, 'get_ProductCategories'])->name('get-product-categories');
    Route::get('get-product-subcategories/{categoryId}', [CommonController::class, 'get_ProductSubCategories'])->name('get-product-subcategories');
    //spareparts api
    Route::get('spareparts/get-product', [ProductsController::class, 'getProducts'])->name('spareparts.get-product');
    Route::get('spareparts/products/subcategories/{subCategoryId}', [ProductsController::class, 'getSubCategoriesProducts']);
    Route::post('spareparts/products/sendOrder/{productId}', [ProductsController::class, 'sendOrder']);
    Route::get('spareparts/products/ourOrder', [ProductsController::class, 'getuserOrders']);
    //quotation
    Route::post('quotation/store', [QuotationController::class, 'storeQuotation']);
    //getlpgcng price
    Route::get('get-lpg-cng-price', [CommonController::class, 'getLPG_CNG'])->name('get-lpg-cng-price');
    //get user auto details
    Route::get('get-user-auto', [CommonController::class, 'getUserAuto'])->name('get-user-auto');
    //auto meter calcualtion
    Route::post('auto-meter-calculate', [AutoMeterController::class, 'autoMeterSave'])->name('auto-meter-calculate');
    Route::get('auto-meter/history', [AutoMeterController::class, 'getInvoiceHistroy'])->name('auto-meter.history');
    //target
    Route::post('target/store', [TargetController::class, 'store'])->name('target-store');
    Route::post('target/earning-amount/update', [TargetController::class, 'updateEarning'])->name('target.earning-amount-update');
    Route::get('target', [TargetController::class, 'getTodayTarget'])->name('target');
    Route::post('target/closed-status', [TargetController::class, 'updateTodayTargetStatus'])->name('target.closedStatus');

    //driver auto profile fill
    Route::post('auto-profile-update', [UserController::class, 'updateAutoProfile'])->name('auto-profile-update');
    //driver fareprice api
    Route::post('fareprice-on-off', [FarePriceController::class, 'driverFarePriceOnOff'])->name('fareprice-on-off');
    Route::post('/fareprice/ride-accept', [App\Http\Controllers\Api\V1\AutoRideController::class, 'acceptRide']);
    Route::post('/fareprice/ride-reject', [App\Http\Controllers\Api\V1\AutoRideController::class, 'rejectRide']);
    Route::get('/fareprice/ride-details/{rideId}',[App\Http\Controllers\Api\V1\AutoRideController::class,'getRideDetails']);
    Route::post('/fareprice/ride-arrived/{rideId}', [App\Http\Controllers\Api\V1\AutoRideController::class, 'rideArrived']);
    Route::post('/fareprice/ride-update-passengers/{rideId}', [App\Http\Controllers\Api\V1\AutoRideController::class, 'updatePassengerCount']);
    Route::post('/fareprice/ride-start/{rideId}', [App\Http\Controllers\Api\V1\AutoRideController::class, 'rideStart']);
    Route::post('/fareprice/instant-payment/create-order', [App\Http\Controllers\Api\V1\AutoRideController::class, 'createInstantPaymentOrder']);
    Route::post('/fareprice/instant-payment/verify', [App\Http\Controllers\Api\V1\AutoRideController::class, 'verifyInstantPayment']);
    Route::post('/fareprice/ride-completed', [App\Http\Controllers\Api\V1\AutoRideController::class, 'rideCompleted']);
    Route::get('/fareprice/ride-history',[App\Http\Controllers\Api\V1\AutoRideController::class,'getDriverRideHistory']);
    Route::get('/fareprice/ride-cancel-reason',[App\Http\Controllers\Api\V1\AutoRideController::class,'driverCloseReason']);
    Route::post('/fareprice/ride-cancel',[App\Http\Controllers\Api\V1\AutoRideController::class,'driverCancelRide']);
    Route::post('/fareprice/toggle',[App\Http\Controllers\Api\V1\AutoRideController::class,'toggleFairPrice']);
    Route::get('/fareprice/rides/{rideId}/conversation', [App\Http\Controllers\Api\fairprice\V1\RideCommunicationController::class, 'driverConversation']);
    Route::get('/fareprice/rides/{rideId}/messages', [App\Http\Controllers\Api\fairprice\V1\RideCommunicationController::class, 'driverMessages']);
    Route::post('/fareprice/rides/{rideId}/messages', [App\Http\Controllers\Api\fairprice\V1\RideCommunicationController::class, 'driverSendMessage']);
    Route::post('/fareprice/rides/{rideId}/messages/read', [App\Http\Controllers\Api\fairprice\V1\RideCommunicationController::class, 'driverReadMessages']);

    Route::post('/fareprice/sos-alert', [FarePriceController::class, 'sendSOS'])->name('fareprice.sos-alert');
    Route::get('fareprice-view-sos-alarm/{id}', [FarePriceController::class, 'viewSOS'])->name('fareprice-view-sos.alarm');

    Route::get('/fareprice/driver/ride-history',[App\Http\Controllers\Api\V1\AutoRideController::class,'rideHistory']);
});



//FairPrice routes
// V1
Route::prefix('fairprice/v1')->group(function () {
    Route::post('/customer/auth/sendOtp', [App\Http\Controllers\Api\fairprice\V1\Auth\OTPAuthController::class, 'sendOTP']);
    Route::post('/customer/auth/verifyOtp', [App\Http\Controllers\Api\fairprice\V1\Auth\OTPAuthController::class, 'verifyOTP']);

    Route::middleware(['auth:customer'])->group(function () {
        Route::post('/customer/profile-update', [App\Http\Controllers\Api\fairprice\V1\ProfileController::class, 'profileUpdate']);
        Route::get('/customer/profile', [App\Http\Controllers\Api\fairprice\V1\ProfileController::class, 'getProfile']);
        Route::post('/customer/location-update', [App\Http\Controllers\Api\fairprice\V1\ProfileController::class, 'updateLocation']);
        Route::post('/customer/find-auto', [App\Http\Controllers\Api\fairprice\V1\AutoController::class, 'findAuto']);
        Route::get('/customer/near-auto', [App\Http\Controllers\Api\fairprice\V1\AutoController::class, 'nearestAutos']);
        Route::get('/customer/virtual-stand/drivers', [App\Http\Controllers\Api\fairprice\V1\AutoController::class, 'virtualStandDrivers']);
        Route::post('/customer/virtual-stand/reassign', [App\Http\Controllers\Api\fairprice\V1\AutoController::class, 'virtualStandReassign']);
        Route::post('/customer/ride-create', [App\Http\Controllers\Api\fairprice\V1\AutoController::class, 'createRideRequest']);
        Route::post('/customer/ride-payment/create-order', [App\Http\Controllers\Api\fairprice\V1\RidePaymentController::class, 'createOrder']);
        Route::post('/customer/ride-payment/verify', [App\Http\Controllers\Api\fairprice\V1\RidePaymentController::class, 'verifyPayment']);
        Route::get('/customer/ride-details/{rideId}',[App\Http\Controllers\Api\fairprice\V1\AutoController::class,'getRideDetails']);
        Route::post('/customer/ride-update-passengers/{rideId}',[App\Http\Controllers\Api\fairprice\V1\AutoController::class,'updatePassengerCount']);
        Route::get('/customer/ride-history',[App\Http\Controllers\Api\fairprice\V1\AutoController::class,'getCustomerRideHistory']);
        Route::get('/customer/ride-cancel-reason',[App\Http\Controllers\Api\fairprice\V1\AutoController::class,'customerCloseReason']);
        Route::post('/customer/ride-cancel',[App\Http\Controllers\Api\fairprice\V1\AutoController::class,'customerCancelRide']);
        Route::get('/customer/rides/{rideId}/conversation', [App\Http\Controllers\Api\fairprice\V1\RideCommunicationController::class, 'customerConversation']);
        Route::get('/customer/rides/{rideId}/messages', [App\Http\Controllers\Api\fairprice\V1\RideCommunicationController::class, 'customerMessages']);
        Route::post('/customer/rides/{rideId}/messages', [App\Http\Controllers\Api\fairprice\V1\RideCommunicationController::class, 'customerSendMessage']);
        Route::post('/customer/rides/{rideId}/messages/read', [App\Http\Controllers\Api\fairprice\V1\RideCommunicationController::class, 'customerReadMessages']);
        Route::post('/customer/sos-alert',[App\Http\Controllers\Api\fairprice\V1\SOSController::class,'sendSOS']);
        Route::get('/customer/sos-alert/view/{id}',[App\Http\Controllers\Api\fairprice\V1\SOSController::class,'viewSOS']);
    });

});


// V2
Route::prefix('fairprice/v2')->group(function () {

    Route::get('/v2-test', function () {
        return response()->json(["success" => true, "message" => "V2 WORKING"]);
    });

    Route::middleware(['auth:customer'])->group(function () {
        
    });

});
