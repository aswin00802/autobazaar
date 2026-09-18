<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin panel routes
|--------------------------------------------------------------------------
|
| Loaded from routes/web.php, so they run in the web middleware group.
| URLs, route names and permissions are unchanged from when they lived there.
| The sidebar that links to them is resources/views/admin/include/sidenav.blade.php.
|
*/

Route::group(['middleware' => ['auth','check.Userstatus']], function () {
    Route::get('/dashboard', [App\Http\Controllers\admin\DashboardController::class, 'index'])->name('dashboard');
    Route::post('/brand/get-model', [App\Http\Controllers\admin\CommonController::class, 'get_model'])->name('brand.get-model');
    Route::group(['prefix' => 'masters'], function () {
        Route::get('/country', [App\Http\Controllers\admin\master\CountryController::class, 'index'])->name('masters.country');
        Route::get('/country/create', [App\Http\Controllers\admin\master\CountryController::class, 'create'])->name('masters.country.create');
        Route::post('/country/store', [App\Http\Controllers\admin\master\CountryController::class, 'store'])->name('masters.country.store');
        Route::get('/country/edit/{id}', [App\Http\Controllers\admin\master\CountryController::class, 'edit'])->name('masters.country.edit');
        Route::post('/country/update', [App\Http\Controllers\admin\master\CountryController::class, 'update'])->name('masters.country.update');

        Route::get('/state', [App\Http\Controllers\admin\master\StateController::class, 'index'])->name('masters.state');
        Route::get('/state/create', [App\Http\Controllers\admin\master\StateController::class, 'create'])->name('masters.state.create');
        Route::post('/state/store', [App\Http\Controllers\admin\master\StateController::class, 'store'])->name('masters.state.store');
        Route::get('/state/edit/{id}', [App\Http\Controllers\admin\master\StateController::class, 'edit'])->name('masters.state.edit');
        Route::post('/state/update', [App\Http\Controllers\admin\master\StateController::class, 'update'])->name('masters.state.update');

        Route::get('/city', [App\Http\Controllers\admin\master\CityController::class, 'index'])->name('masters.city');
        Route::get('/city/create', [App\Http\Controllers\admin\master\CityController::class, 'create'])->name('masters.city.create');
        Route::post('/city/store', [App\Http\Controllers\admin\master\CityController::class, 'store'])->name('masters.city.store');
        Route::get('/city/edit/{id}', [App\Http\Controllers\admin\master\CityController::class, 'edit'])->name('masters.city.edit');
        Route::post('/city/update', [App\Http\Controllers\admin\master\CityController::class, 'update'])->name('masters.city.update');

        Route::get('/auto-brands', [App\Http\Controllers\admin\master\AutoBrandController::class, 'index'])->name('masters.auto-brands');
        Route::get('/auto-brands/create', [App\Http\Controllers\admin\master\AutoBrandController::class, 'create'])->name('masters.auto-brands.create');
        Route::post('/auto-brands/store', [App\Http\Controllers\admin\master\AutoBrandController::class, 'store'])->name('masters.auto-brands.store');
        Route::get('/auto-brands/edit/{id}', [App\Http\Controllers\admin\master\AutoBrandController::class, 'edit'])->name('masters.auto-brands.edit');
        Route::post('/auto-brands/update', [App\Http\Controllers\admin\master\AutoBrandController::class, 'update'])->name('masters.auto-brands.update');
        Route::post('/auto-brands/delete', [App\Http\Controllers\admin\master\AutoBrandController::class, 'destroy'])->name('masters.auto-brands.delete');

        Route::get('/auto-brands/model', [App\Http\Controllers\admin\master\AutoBrandModelController::class, 'index'])->name('masters.auto-brands.model');
        Route::get('/auto-brands/model/create', [App\Http\Controllers\admin\master\AutoBrandModelController::class, 'create'])->name('masters.auto-brands.model.create');
        Route::post('/auto-brands/model/store', [App\Http\Controllers\admin\master\AutoBrandModelController::class, 'store'])->name('masters.auto-brands.model.store');
        Route::get('/auto-brands/model/edit/{id}', [App\Http\Controllers\admin\master\AutoBrandModelController::class, 'edit'])->name('masters.auto-brands.model.edit');
        Route::post('/auto-brands/model/update', [App\Http\Controllers\admin\master\AutoBrandModelController::class, 'update'])->name('masters.auto-brands.model.update');
        Route::post('/auto-brands/model/delete', [App\Http\Controllers\admin\master\AutoBrandModelController::class, 'destroy'])->name('masters.auto-brands.model.delete');

        Route::get('/auto-fueltype', [App\Http\Controllers\admin\master\AutoFuelTypeController::class, 'index'])->name('masters.auto-fueltype');
        Route::get('/auto-fueltype/create', [App\Http\Controllers\admin\master\AutoFuelTypeController::class, 'create'])->name('masters.auto-fueltype.create');
        Route::post('/auto-fueltype/store', [App\Http\Controllers\admin\master\AutoFuelTypeController::class, 'store'])->name('masters.auto-fueltype.store');
        Route::get('/auto-fueltype/edit/{id}', [App\Http\Controllers\admin\master\AutoFuelTypeController::class, 'edit'])->name('masters.auto-fueltype.edit');
        Route::post('/auto-fueltype/update', [App\Http\Controllers\admin\master\AutoFuelTypeController::class, 'update'])->name('masters.auto-fueltype.update');
        Route::post('/auto-fueltype/delete', [App\Http\Controllers\admin\master\AutoFuelTypeController::class, 'destroy'])->name('masters.auto-fueltype.delete');

        Route::get('/auto-seller', [App\Http\Controllers\admin\master\AutoSellersController::class, 'index'])->name('masters.auto-seller');
        Route::get('/auto-seller/create', [App\Http\Controllers\admin\master\AutoSellersController::class, 'create'])->name('masters.auto-seller.create');
        Route::post('/auto-seller/store', [App\Http\Controllers\admin\master\AutoSellersController::class, 'store'])->name('masters.auto-seller.store');
        Route::get('/auto-seller/edit/{id}', [App\Http\Controllers\admin\master\AutoSellersController::class, 'edit'])->name('masters.auto-seller.edit');
        Route::post('/auto-seller/update', [App\Http\Controllers\admin\master\AutoSellersController::class, 'update'])->name('masters.auto-seller.update');
        Route::post('/auto-seller/delete', [App\Http\Controllers\admin\master\AutoSellersController::class, 'destroy'])->name('masters.auto-seller.delete');

        Route::get('/auto-finance', [App\Http\Controllers\admin\master\AutoFinanceController::class, 'index'])->name('masters.auto-finance');
        Route::get('/auto-finance/create', [App\Http\Controllers\admin\master\AutoFinanceController::class, 'create'])->name('masters.auto-finance.create');
        Route::post('/auto-finance/store', [App\Http\Controllers\admin\master\AutoFinanceController::class, 'store'])->name('masters.auto-finance.store');
        Route::get('/auto-finance/edit/{id}', [App\Http\Controllers\admin\master\AutoFinanceController::class, 'edit'])->name('masters.auto-finance.edit');
        Route::post('/auto-finance/update', [App\Http\Controllers\admin\master\AutoFinanceController::class, 'update'])->name('masters.auto-finance.update');
        Route::post('/auto-finance/delete', [App\Http\Controllers\admin\master\AutoFinanceController::class, 'destroy'])->name('masters.auto-finance.delete');
        Route::post('/auto-finance/statustoggle', [App\Http\Controllers\admin\master\AutoFinanceController::class, 'statusToggle'])->name('masters.auto-finance.statustoggle');

        Route::get('/auto-authorized-seller', [App\Http\Controllers\admin\master\AuthorizedAutoSellerController::class, 'index'])->name('masters.auto-authorized-seller');
        Route::get('/auto-authorized-seller/create', [App\Http\Controllers\admin\master\AuthorizedAutoSellerController::class, 'create'])->name('masters.auto-authorized-seller.create');
        Route::post('/auto-authorized-seller/store', [App\Http\Controllers\admin\master\AuthorizedAutoSellerController::class, 'store'])->name('masters.auto-authorized-seller.store');
        Route::get('/auto-authorized-seller/edit/{id}', [App\Http\Controllers\admin\master\AuthorizedAutoSellerController::class, 'edit'])->name('masters.auto-authorized-seller.edit');
        Route::post('/auto-authorized-seller/update', [App\Http\Controllers\admin\master\AuthorizedAutoSellerController::class, 'update'])->name('masters.auto-authorized-seller.update');
        Route::post('/auto-authorized-seller/delete', [App\Http\Controllers\admin\master\AuthorizedAutoSellerController::class, 'destroy'])->name('masters.auto-authorized-seller.delete');
    });
    Route::group(['prefix' => 'roles'], function () {
        Route::get('/', [App\Http\Controllers\admin\role_and_permission\RoleController::class, 'index'])->name('roles');
        Route::get('/add-role', [App\Http\Controllers\admin\role_and_permission\RoleController::class, 'create'])->name('roles.create');
        Route::post('/add-role', [App\Http\Controllers\admin\role_and_permission\RoleController::class, 'store'])->name('roles.store');
        Route::get('/update-role/{id}', [App\Http\Controllers\admin\role_and_permission\RoleController::class, 'edit'])->name('roles.edit');
        Route::post('/update-role', [App\Http\Controllers\admin\role_and_permission\RoleController::class, 'update'])->name('roles.update');
    });
    Route::group(['prefix' => 'permissions'], function () {
        Route::get('/', [App\Http\Controllers\admin\role_and_permission\PermissionController::class, 'index'])->name('permissions');
        Route::get('/add-permissions', [App\Http\Controllers\admin\role_and_permission\PermissionController::class, 'create'])->name('permissions.create');
        Route::post('/add-permissions', [App\Http\Controllers\admin\role_and_permission\PermissionController::class, 'store'])->name('permissions.store');
        Route::get('/update-permissions/{id}', [App\Http\Controllers\admin\role_and_permission\PermissionController::class, 'edit'])->name('permissions.edit');
        Route::post('/update-permissions', [App\Http\Controllers\admin\role_and_permission\PermissionController::class, 'update'])->name('permissions.update');
    });
    Route::group(['prefix' => 'role_has_permission'], function () {
        Route::get('/', [App\Http\Controllers\admin\role_and_permission\RolehaPermissionController::class, 'index'])->name('role_has_permission');
        Route::get('/update-role_has_permission/{id}', [App\Http\Controllers\admin\role_and_permission\RolehaPermissionController::class, 'edit'])->name('role_has_permission.edit');
        Route::post('/update-role_has_permission', [App\Http\Controllers\admin\role_and_permission\RolehaPermissionController::class, 'update'])->name('role_has_permission.update');
    });
    Route::group(['prefix' => 'settings'], function () {
        Route::get('/general-settings', [App\Http\Controllers\admin\settings\SettingController::class, 'index'])->name('settings.general-settings');
        Route::post('/general-settings/update', [App\Http\Controllers\admin\settings\SettingController::class, 'update'])->name('settings.general-settings.update');
        Route::post('/general-settings/removeFile', [App\Http\Controllers\admin\settings\SettingController::class, 'removeFile'])->name('settings.general-settings.removeFile');
        Route::get('/smtp-settings', [App\Http\Controllers\admin\settings\SMTPSettingController::class, 'index'])->name('settings.smtp-settings');
        Route::post('/smtp-settings/update', [App\Http\Controllers\admin\settings\SMTPSettingController::class, 'update'])->name('settings.smtp-settings.update');
        Route::get('/payment-settings', [App\Http\Controllers\admin\settings\PaymentSettingController::class, 'index'])->name('settings.payment-settings');
        Route::post('/payment-settings/update', [App\Http\Controllers\admin\settings\PaymentSettingController::class, 'update'])->name('settings.payment-settings.update');
        Route::get('/email-template-settings', [App\Http\Controllers\admin\settings\EmailTemplateController::class, 'index'])->name('settings.email-template-settings');
        Route::get('/email-template-settings/create', [App\Http\Controllers\admin\settings\EmailTemplateController::class, 'create'])->name('settings.email-template-settings.create');
        Route::post('/email-template-settings/store', [App\Http\Controllers\admin\settings\EmailTemplateController::class, 'store'])->name('settings.email-template-settings.store');
        Route::get('/email-template-settings/edit/{id}', [App\Http\Controllers\admin\settings\EmailTemplateController::class, 'edit'])->name('settings.email-template-settings.edit');
        Route::post('/email-template-settings/update', [App\Http\Controllers\admin\settings\EmailTemplateController::class, 'update'])->name('settings.email-template-settings.update');
        Route::post('/email-template-settings/delete', [App\Http\Controllers\admin\settings\EmailTemplateController::class, 'delete'])->name('settings.email-template-settings.delete');
    });
    Route::get('/sold-auto-list',[App\Http\Controllers\admin\SoldAutoController::class,'index'])->name('sold-auto.list');
    Route::get('/enquiry-auto-list',[App\Http\Controllers\admin\AutoEnquiryController::class,'index'])->name('enquiry-auto.list');
    Route::post('/enquiry-auto-status-update',[App\Http\Controllers\admin\AutoEnquiryController::class,'update_stauts'])->name('enquiry-auto.status-update');
    Route::get('/emergency-request-list',[App\Http\Controllers\admin\EmergencyController::class,'index'])->name('emergency.request.list');
    Route::post('/emergency-request-update',[App\Http\Controllers\admin\EmergencyController::class,'status_update'])->name('emergency.request.update');
    Route::get('/driver-request-list',[App\Http\Controllers\admin\DriverController::class,'index'])->name('driver.request.list');
    Route::post('/driver-request-approve/{id}',[App\Http\Controllers\admin\DriverController::class,'approveDriverRequest'])->name('driver.request.approve');
    Route::post('/driver-request-delete/{id}',[App\Http\Controllers\admin\DriverController::class,'destroy'])->name('driver.request.delete');
    Route::group(['prefix' => 'services'], function () {
        Route::get('/gas-station', [App\Http\Controllers\admin\service\GasStationController::class, 'index'])->name('services.gas-station');
        Route::get('/gas-station/create', [App\Http\Controllers\admin\service\GasStationController::class, 'create'])->name('services.gas-station.create');
        Route::post('/gas-station/store', [App\Http\Controllers\admin\service\GasStationController::class, 'store'])->name('services.gas-station.store');
        Route::get('/gas-station/edit/{id}', [App\Http\Controllers\admin\service\GasStationController::class, 'edit'])->name('services.gas-station.edit');
        Route::post('/gas-station/update', [App\Http\Controllers\admin\service\GasStationController::class, 'update'])->name('services.gas-station.update');
        Route::post('/gas-station/delete', [App\Http\Controllers\admin\service\GasStationController::class, 'destroy'])->name('services.gas-station.delete');
        Route::post('/gas-station/upload', [App\Http\Controllers\admin\service\GasStationController::class, 'blukUpload'])->name('services.gas-station.upload');

        Route::get('/mechanic', [App\Http\Controllers\admin\service\MechanicController::class, 'index'])->name('services.mechanic');
        Route::get('/mechanic/create',[App\Http\Controllers\admin\service\MechanicController::class, 'create'])->name('services.mechanic.create');
        Route::post('/mechanic/store', [App\Http\Controllers\admin\service\MechanicController::class, 'store'])->name('services.mechanic.store');
        Route::get('/mechanic/edit/{id}', [App\Http\Controllers\admin\service\MechanicController::class, 'edit'])->name('services.mechanic.edit');
        Route::post('/mechanic/update', [App\Http\Controllers\admin\service\MechanicController::class, 'update'])->name('services.mechanic.update');
        Route::post('/mechanic/delete', [App\Http\Controllers\admin\service\MechanicController::class, 'destroy'])->name('services.mechanic.delete');

        Route::get('/insurance', [App\Http\Controllers\admin\service\InsuranceController::class, 'index'])->name('services.insurance');

        Route::get('/re-finance', [App\Http\Controllers\admin\service\ReFinanceController::class, 'index'])->name('services.re-finance');
        Route::post('/re-finance/status-update', [App\Http\Controllers\admin\service\ReFinanceController::class, 'update_stauts'])->name('services.re-finance.status-update');

        Route::get('/rto', [App\Http\Controllers\admin\service\RTOController::class, 'index'])->name('services.rto');
        Route::post('/rto/status-update', [App\Http\Controllers\admin\service\RTOController::class, 'update_stauts'])->name('services.rto.update-status');

    });

    Route::group(['prefix' => 'user-management'], function () {
        Route::get('/users-list', [App\Http\Controllers\admin\user_management\UserListController::class, 'index'])->name('user-management.users-list');
        Route::get('/users-info/{id}', [App\Http\Controllers\admin\user_management\UserListController::class, 'user_info'])->name('user-management.users-info');
        Route::get('/users-post-auto-list', [App\Http\Controllers\admin\user_management\UserPostAutoController::class, 'index'])->name('user-management.users-post-auto-list');
        Route::post('/users-post-auto-approval', [App\Http\Controllers\admin\user_management\UserPostAutoController::class, 'approval'])->name('user-management.users-post-auto-approval');
    });

    Route::group(['prefix' => 'auto-management'], function () {
        Route::get('/used-auto', [App\Http\Controllers\admin\auto_management\UsedAutoController::class, 'index'])->name('auto-management.used-auto');
        Route::get('/used-auto/create', [App\Http\Controllers\admin\auto_management\UsedAutoController::class, 'create'])->name('auto-management.used-auto.create');
        Route::get('/used-auto/view-details/{id}', [App\Http\Controllers\admin\auto_management\UsedAutoController::class, 'viewDetails'])->name('auto-management.used-auto.view-details');
        Route::post('/used-auto/store', [App\Http\Controllers\admin\auto_management\UsedAutoController::class, 'store'])->name('auto-management.used-auto.store');
        Route::get('/used-auto/edit/{id}', [App\Http\Controllers\admin\auto_management\UsedAutoController::class, 'edit'])->name('auto-management.used-auto.edit');
        Route::post('/used-auto/update', [App\Http\Controllers\admin\auto_management\UsedAutoController::class, 'update'])->name('auto-management.used-auto.update');
        Route::post('/used-auto/delete', [App\Http\Controllers\admin\auto_management\UsedAutoController::class, 'delete'])->name('auto-management.used-auto.delete');
        Route::post('/used-auto/sell', [App\Http\Controllers\admin\auto_management\UsedAutoController::class, 'sell_auto'])->name('auto-management.used-auto.sell');

        Route::get('/new-auto', [App\Http\Controllers\admin\auto_management\NewAutoController::class, 'index'])->name('auto-management.new-auto');
        Route::get('/new-auto/create', [App\Http\Controllers\admin\auto_management\NewAutoController::class, 'create'])->name('auto-management.new-auto.create');
        Route::get('/new-auto/view-details/{id}', [App\Http\Controllers\admin\auto_management\NewAutoController::class, 'viewDetails'])->name('auto-management.new-auto.view-details');
        Route::post('/new-auto/store', [App\Http\Controllers\admin\auto_management\NewAutoController::class, 'store'])->name('auto-management.new-auto.store');
        Route::get('/new-auto/edit/{id}', [App\Http\Controllers\admin\auto_management\NewAutoController::class, 'edit'])->name('auto-management.new-auto.edit');
        Route::post('/new-auto/update', [App\Http\Controllers\admin\auto_management\NewAutoController::class, 'update'])->name('auto-management.new-auto.update');
        Route::post('/new-auto/delete', [App\Http\Controllers\admin\auto_management\NewAutoController::class, 'delete'])->name('auto-management.new-auto.delete');
        Route::post('/new-auto/quotation', [App\Http\Controllers\admin\auto_management\NewAutoController::class, 'getQuotation'])->name('auto-management.new-auto.quotation');

        Route::get('/private-cargo-auto', [App\Http\Controllers\admin\auto_management\PrivateCargoAutoController::class, 'index'])->name('auto-management.private-cargo-auto');
        Route::get('/private-cargo-auto/create', [App\Http\Controllers\admin\auto_management\PrivateCargoAutoController::class, 'create'])->name('auto-management.private-cargo-auto.create');
        Route::get('/private-cargo-auto/view-details/{id}', [App\Http\Controllers\admin\auto_management\PrivateCargoAutoController::class, 'viewDetails'])->name('auto-management.private-cargo-auto.view-details');
        Route::post('/private-cargo-auto/store', [App\Http\Controllers\admin\auto_management\PrivateCargoAutoController::class, 'store'])->name('auto-management.private-cargo-auto.store');
        Route::get('/private-cargo-auto/edit/{id}', [App\Http\Controllers\admin\auto_management\PrivateCargoAutoController::class, 'edit'])->name('auto-management.private-cargo-auto.edit');
        Route::post('/private-cargo-auto/update', [App\Http\Controllers\admin\auto_management\PrivateCargoAutoController::class, 'update'])->name('auto-management.private-cargo-auto.update');
        Route::post('/private-cargo-auto/delete', [App\Http\Controllers\admin\auto_management\PrivateCargoAutoController::class, 'delete'])->name('auto-management.private-cargo-auto.delete');
        Route::post('/private-cargo-auto/sell', [App\Http\Controllers\admin\auto_management\PrivateCargoAutoController::class, 'sell_auto'])->name('auto-management.private-cargo-auto.sell');

        Route::get('/bajaj-refinance-auto', [App\Http\Controllers\admin\auto_management\BajajReFinanceAutoController::class, 'index'])->name('auto-management.bajaj-refinance-auto');
        Route::get('/bajaj-refinance-auto/create', [App\Http\Controllers\admin\auto_management\BajajReFinanceAutoController::class, 'create'])->name('auto-management.bajaj-refinance-auto.create');
        Route::get('/bajaj-refinance-auto/view-details/{id}', [App\Http\Controllers\admin\auto_management\BajajReFinanceAutoController::class, 'viewDetails'])->name('auto-management.bajaj-refinance-auto.view-details');
        Route::post('/bajaj-refinance-auto/store', [App\Http\Controllers\admin\auto_management\BajajReFinanceAutoController::class, 'store'])->name('auto-management.bajaj-refinance-auto.store');
        Route::get('/bajaj-refinance-auto/edit/{id}', [App\Http\Controllers\admin\auto_management\BajajReFinanceAutoController::class, 'edit'])->name('auto-management.bajaj-refinance-auto.edit');
        Route::post('/bajaj-refinance-auto/update', [App\Http\Controllers\admin\auto_management\BajajReFinanceAutoController::class, 'update'])->name('auto-management.bajaj-refinance-auto.update');
        Route::post('/bajaj-refinance-auto/delete', [App\Http\Controllers\admin\auto_management\BajajReFinanceAutoController::class, 'delete'])->name('auto-management.bajaj-refinance-auto.delete');
        Route::post('/bajaj-refinance-auto/sell', [App\Http\Controllers\admin\auto_management\BajajReFinanceAutoController::class, 'sell_auto'])->name('auto-management.bajaj-refinance-auto.sell');

    });


    /* ------------------------------------------------------------- Vehicles
       New-vehicle catalogue (vehicle_models + children), customer reviews
       and the lead pipeline fed by the vehicle detail page. */
    Route::group(['prefix' => 'vehicles'], function () {
        Route::get('/catalogue', [App\Http\Controllers\admin\vehicles\VehicleModelsController::class, 'index'])->name('vehicles.catalogue');
        Route::get('/catalogue/create', [App\Http\Controllers\admin\vehicles\VehicleModelsController::class, 'create'])->name('vehicles.catalogue.create');
        Route::post('/catalogue/store', [App\Http\Controllers\admin\vehicles\VehicleModelsController::class, 'store'])->name('vehicles.catalogue.store');
        Route::get('/catalogue/edit/{id}', [App\Http\Controllers\admin\vehicles\VehicleModelsController::class, 'edit'])->name('vehicles.catalogue.edit');
        Route::post('/catalogue/update/{id}', [App\Http\Controllers\admin\vehicles\VehicleModelsController::class, 'update'])->name('vehicles.catalogue.update');
        Route::post('/catalogue/delete', [App\Http\Controllers\admin\vehicles\VehicleModelsController::class, 'delete'])->name('vehicles.catalogue.delete');
        Route::post('/catalogue/status', [App\Http\Controllers\admin\vehicles\VehicleModelsController::class, 'statusToggle'])->name('vehicles.catalogue.status');
        Route::post('/catalogue/image-delete', [App\Http\Controllers\admin\vehicles\VehicleModelsController::class, 'deleteImage'])->name('vehicles.catalogue.image-delete');
        Route::post('/catalogue/document-delete', [App\Http\Controllers\admin\vehicles\VehicleModelsController::class, 'deleteDocument'])->name('vehicles.catalogue.document-delete');

        Route::get('/reviews', [App\Http\Controllers\admin\vehicles\VehicleReviewsController::class, 'index'])->name('vehicles.reviews');
        Route::post('/reviews/status', [App\Http\Controllers\admin\vehicles\VehicleReviewsController::class, 'status'])->name('vehicles.reviews.status');
        Route::post('/reviews/delete', [App\Http\Controllers\admin\vehicles\VehicleReviewsController::class, 'delete'])->name('vehicles.reviews.delete');

        Route::get('/leads', [App\Http\Controllers\admin\vehicles\VehicleLeadsController::class, 'index'])->name('vehicles.leads');
        Route::get('/leads/view/{id}', [App\Http\Controllers\admin\vehicles\VehicleLeadsController::class, 'show'])->name('vehicles.leads.view');
        Route::post('/leads/status', [App\Http\Controllers\admin\vehicles\VehicleLeadsController::class, 'updateStatus'])->name('vehicles.leads.status');
        Route::post('/leads/assign', [App\Http\Controllers\admin\vehicles\VehicleLeadsController::class, 'assign'])->name('vehicles.leads.assign');
        Route::post('/leads/note', [App\Http\Controllers\admin\vehicles\VehicleLeadsController::class, 'note'])->name('vehicles.leads.note');
    });

    /* ------------------------------------------------------------- E-commerce
       Website storefront: orders placed through checkout, and the discount
       codes those orders use. The legacy Auto Spare Parts screens still read
       `sparepart_orders` and stay where they are. */

    /* ------------------------------------------------------------- E-commerce
       Website storefront: orders placed through checkout, and the discount
       codes those orders use. The legacy Auto Spare Parts screens still read
       `sparepart_orders` and stay where they are. */
    Route::group(['prefix' => 'ecommerce'], function () {

        Route::group(['prefix' => 'orders'], function () {
            Route::get('/', [App\Http\Controllers\admin\ecommerce\OrdersController::class, 'index'])->name('ecommerce.orders');
            Route::get('/view/{id}', [App\Http\Controllers\admin\ecommerce\OrdersController::class, 'show'])->name('ecommerce.orders.view');
            Route::post('/status-update', [App\Http\Controllers\admin\ecommerce\OrdersController::class, 'statusUpdate'])->name('ecommerce.orders.status-update');
            Route::post('/payment-update', [App\Http\Controllers\admin\ecommerce\OrdersController::class, 'paymentUpdate'])->name('ecommerce.orders.payment-update');
            Route::post('/delete', [App\Http\Controllers\admin\ecommerce\OrdersController::class, 'delete'])->name('ecommerce.orders.delete');
            Route::get('/status/{status}', [App\Http\Controllers\admin\ecommerce\OrdersController::class, 'index'])->name('ecommerce.orders.status');
        });

        Route::group(['prefix' => 'coupons'], function () {
            Route::get('/', [App\Http\Controllers\admin\ecommerce\CouponsController::class, 'index'])->name('ecommerce.coupons');
            Route::get('/create', [App\Http\Controllers\admin\ecommerce\CouponsController::class, 'create'])->name('ecommerce.coupons.create');
            Route::post('/store', [App\Http\Controllers\admin\ecommerce\CouponsController::class, 'store'])->name('ecommerce.coupons.store');
            Route::get('/edit/{id}', [App\Http\Controllers\admin\ecommerce\CouponsController::class, 'edit'])->name('ecommerce.coupons.edit');
            Route::post('/update/{id}', [App\Http\Controllers\admin\ecommerce\CouponsController::class, 'update'])->name('ecommerce.coupons.update');
            Route::post('/delete', [App\Http\Controllers\admin\ecommerce\CouponsController::class, 'delete'])->name('ecommerce.coupons.delete');
        });
    });

    Route::group(['prefix' => 'spare-parts'], function () {
        Route::get('/categories', [App\Http\Controllers\admin\spare_parts\CategoriesController::class, 'index'])->name('spare-parts.categories');
        Route::get('/categories/create', [App\Http\Controllers\admin\spare_parts\CategoriesController::class, 'create'])->name('spare-parts.categories.create');
        Route::post('/categories/store', [App\Http\Controllers\admin\spare_parts\CategoriesController::class, 'store'])->name('spare-parts.categories.store');
        Route::get('/categories/edit/{id}', [App\Http\Controllers\admin\spare_parts\CategoriesController::class, 'edit'])->name('spare-parts.categories.edit');
        Route::post('/categories/update', [App\Http\Controllers\admin\spare_parts\CategoriesController::class, 'update'])->name('spare-parts.categories.update');
        Route::post('/categories/delete', [App\Http\Controllers\admin\spare_parts\CategoriesController::class, 'delete'])->name('spare-parts.categories.delete');

        Route::get('/sub-categories', [App\Http\Controllers\admin\spare_parts\SubCategoriesController::class, 'index'])->name('spare-parts.sub-categories');
        Route::get('/sub-categories/create', [App\Http\Controllers\admin\spare_parts\SubCategoriesController::class, 'create'])->name('spare-parts.sub-categories.create');
        Route::post('/sub-categories/store', [App\Http\Controllers\admin\spare_parts\SubCategoriesController::class, 'store'])->name('spare-parts.sub-categories.store');
        Route::get('/sub-categories/edit/{id}', [App\Http\Controllers\admin\spare_parts\SubCategoriesController::class, 'edit'])->name('spare-parts.sub-categories.edit');
        Route::post('/sub-categories/update', [App\Http\Controllers\admin\spare_parts\SubCategoriesController::class, 'update'])->name('spare-parts.sub-categories.update');
        Route::post('/sub-categories/delete', [App\Http\Controllers\admin\spare_parts\SubCategoriesController::class, 'delete'])->name('spare-parts.sub-categories.delete');

        Route::get('/product', [App\Http\Controllers\admin\spare_parts\ProductsController::class, 'index'])->name('spare-parts.product');
        Route::get('/product/create', [App\Http\Controllers\admin\spare_parts\ProductsController::class, 'create'])->name('spare-parts.product.create');
        Route::post('/product/store', [App\Http\Controllers\admin\spare_parts\ProductsController::class, 'store'])->name('spare-parts.product.store');
        Route::get('/product/edit/{id}', [App\Http\Controllers\admin\spare_parts\ProductsController::class, 'edit'])->name('spare-parts.product.edit');
        Route::post('/product/update', [App\Http\Controllers\admin\spare_parts\ProductsController::class, 'update'])->name('spare-parts.product.update');
        Route::post('/product/delete', [App\Http\Controllers\admin\spare_parts\ProductsController::class, 'delete'])->name('spare-parts.product.delete');
        Route::post('/product/get-subcategories', [App\Http\Controllers\admin\spare_parts\ProductsController::class, 'get_Subcategory'])->name('spare-parts.product.get-subcategories');
        Route::get('/product/details/{id?}', [App\Http\Controllers\admin\spare_parts\ProductsController::class, 'getDetails'])->name('spare-parts.product.details');

        Route::group(['prefix' => 'orders'], function () {
            Route::get('/pending', [App\Http\Controllers\admin\spare_parts\OrdersController::class, 'pendingOrders'])->name('spare-parts.orders.pending');
            Route::get('/success', [App\Http\Controllers\admin\spare_parts\OrdersController::class, 'successOrders'])->name('spare-parts.orders.success');
            Route::get('/cancel', [App\Http\Controllers\admin\spare_parts\OrdersController::class, 'cancelOrders'])->name('spare-parts.orders.cancel');
            Route::post('/status/update', [App\Http\Controllers\admin\spare_parts\OrdersController::class, 'statusUpdate'])->name('spare-parts.orders.status-update');
            Route::post('/delete', [App\Http\Controllers\admin\spare_parts\OrdersController::class, 'deleteOrder'])->name('spare-parts.orders.delete');
        });
    });

    //events
    Route::get('/events', [App\Http\Controllers\admin\EventsController::class, 'index'])->name('events');
    Route::get('/events/create', [App\Http\Controllers\admin\EventsController::class, 'create'])->name('events.create');
    Route::post('/events/store', [App\Http\Controllers\admin\EventsController::class, 'store'])->name('events.store');
    Route::get('/events/edit/{id}', [App\Http\Controllers\admin\EventsController::class, 'edit'])->name('events.edit');
    Route::post('/events/update', [App\Http\Controllers\admin\EventsController::class, 'update'])->name('events.update');
    Route::post('/events/delete', [App\Http\Controllers\admin\EventsController::class, 'destroy'])->name('events.delete');

    //quotation
    Route::get('/quotation-list', [App\Http\Controllers\admin\QuotationController::class, 'index'])->name('quotation-list');
    Route::post('/quotation/status-update', [App\Http\Controllers\admin\QuotationController::class, 'update_stauts'])->name('quotation.status-update');

    Route::get('/pos-quotation', [App\Http\Controllers\admin\POSQuotationController::class, 'index'])->name('pos-quotation');
    Route::get('/pos-quotation/create', [App\Http\Controllers\admin\POSQuotationController::class, 'create'])->name('pos-quotation.create');
    Route::post('/pos-quotation/auto-model', [App\Http\Controllers\admin\POSQuotationController::class, 'getAutoModel'])->name('pos-quotation.auto-model');

    //auto meter
    Route::get('/auto-meter', [App\Http\Controllers\admin\AutoMeterController::class, 'index'])->name('auto-meter');
    Route::get('auto-meter/invoice/{id}', [App\Http\Controllers\admin\AutoMeterController::class,'invoice'])->name('auto-meter.invoice');
    Route::get('auto-meter/invoice-download/{id}', [App\Http\Controllers\admin\AutoMeterController::class,'invoiceDownload'])->name('auto-meter.invoice.download');

    Route::group(['prefix' => 'fairprice'], function () {
        Route::get('/rides', [App\Http\Controllers\admin\fairprice\RideController::class, 'index'])->name('fairprice.rides');
        Route::get('/rides/{id}', [App\Http\Controllers\admin\fairprice\RideController::class, 'show'])->name('fairprice.rides.show');
        Route::get('/fare-settings', [App\Http\Controllers\admin\fairprice\FareSettingController::class, 'index'])->name('fairprice.fare-settings');
        Route::post('/fare-settings/update', [App\Http\Controllers\admin\fairprice\FareSettingController::class, 'update'])->name('fairprice.fare-settings.update');
    });
});
