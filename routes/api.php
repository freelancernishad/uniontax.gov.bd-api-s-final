<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VercelController;
use App\Http\Controllers\Api\Global\UniouninfoController;
use App\Http\Controllers\Api\Global\Sonod\SonodController;
use App\Http\Controllers\Api\Gateway\Ekpay\EkpayController;
use App\Http\Controllers\Api\Server\ServerStatusController;
use App\Http\Controllers\Api\Global\Address\AddressController;
use App\Http\Controllers\Api\Payments\FailedPaymentController;
use App\Http\Controllers\Api\User\Package\UserPackageController;
use App\Http\Controllers\Api\User\Holdingtax\HoldingtaxController;
use App\Http\Controllers\Api\Auth\Uddokta\CitizenInformationController;
use App\Http\Controllers\Api\User\PackageAddon\UserPackageAddonController;

// Load users and admins route files
if (file_exists($userRoutes = __DIR__.'/example.php')) {
    require $userRoutes;
}


if (file_exists($userRoutes = __DIR__.'/users.php')) {
    require $userRoutes;
}

if (file_exists($adminRoutes = __DIR__.'/admins.php')) {
    require $adminRoutes;
}

if (file_exists($adminRoutes = __DIR__.'/uddoktas.php')) {
    require $adminRoutes;
}

if (file_exists($adminRoutes = __DIR__.'/VillageCourt.php')) {
    require $adminRoutes;
}

if (file_exists($stripeRoutes = __DIR__.'/Gateways/stripe.php')) {
    require $stripeRoutes;
}



Route::get('/server-status', [ServerStatusController::class, 'checkStatus']);






// Route to get all packages with discounts (query params for discount_months)
Route::get('global/packages', [UserPackageController::class, 'index']);

// Route to get a single package by ID with discounts
Route::get('global/package/{id}', [UserPackageController::class, 'show']);

Route::prefix('global/')->group(function () {
    Route::get('package-addons/', [UserPackageAddonController::class, 'index']); // List all addons
    Route::get('package-addons/{id}', [UserPackageAddonController::class, 'show']); // Get a specific addon





    Route::get('/divisions', [AddressController::class, 'getDivisions']);
    Route::get('/districts/{division_id}', [AddressController::class, 'getDistrictsByDivision']);
    Route::get('/upazilas/{district_id}', [AddressController::class, 'getUpazilasByDistrict']);
    Route::get('/unions/{upazila_id}', [AddressController::class, 'getUnionsByUpazila']);



});

Route::get('global/uniouninfo', [UniouninfoController::class, 'getByShortName']);
Route::post('global/uniouninfo', [UniouninfoController::class, 'getByShortName']);


Route::get('holdingtax/search', [HoldingtaxController::class, 'holdingSearch']);
Route::get('holdingtax/boketas/{id}', [HoldingtaxController::class, 'getSingleHoldingTaxWithBokeyas']);

Route::post('/pay/holding/tax/{id}', [HoldingtaxController::class,'holding_tax_pay_Online']);



Route::post('sonod/submit', [SonodController::class, 'sonodSubmit']);
Route::post('sonod/search', [SonodController::class, 'findSonod']);
Route::get('sonod/search', [SonodController::class, 'findSonod']);
Route::post('sonod/renew/{id}', [SonodController::class, 'renewSonod']);

Route::post('ekpay/ipn',[EkpayController::class ,'ipn']);
Route::post('ekpay/smspurchase/ipn',[EkpayController::class ,'ipnCallbackForSmsPurchase']);
Route::post('ekpay/check/payments/ipn',[EkpayController::class ,'CheckPayment']);



Route::post('payment/failed/support/ticket', [FailedPaymentController::class, 'failed_payment_record_store']);




Route::get('/create-domains-for-all', [VercelController::class, 'createDomainsForAllUniouninfo']);
Route::get('/vercel/domains', [VercelController::class, 'getVercelPromotedAliases']);
Route::get('/vercel/delete-subdomains', [VercelController::class, 'deleteSubdomainsFromVercel']);
Route::get('/create-domains-by-upazila/{id}', [VercelController::class, 'createDomainsByUpazila']);



Route::post('/ekpay/create-url', [EkpayController::class, 'createUrl']);
