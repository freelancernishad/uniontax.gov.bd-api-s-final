<?php


use Illuminate\Support\Facades\Route;
use App\Http\Middleware\AuthenticateUser;
use App\Http\Controllers\PurchaseSmsController;
use App\Http\Controllers\HoldingTaxImportController;
use App\Http\Controllers\Api\Coupon\CouponController;
use App\Http\Controllers\Api\Auth\User\AuthUserController;
use App\Http\Controllers\Api\User\Sonod\UserSonodController;
use App\Http\Controllers\Api\Auth\User\VerificationController;
use App\Http\Controllers\Api\Payments\FailedPaymentController;
use App\Http\Controllers\Api\User\Tender\TenderListController;
use App\Http\Controllers\Api\User\Package\UserPackageController;
use App\Http\Controllers\Api\User\Holdingtax\HoldingtaxController;
use App\Http\Controllers\Api\Auth\User\UserPasswordResetController;
use App\Http\Controllers\Api\User\SonodName\UserSonodFeeController;
use App\Http\Controllers\Api\User\Uniouninfo\UserUniouninfoController;
use App\Http\Controllers\Api\User\UserManagement\UserProfileController;
use App\Http\Controllers\Api\User\Package\UserPurchasedHistoryController;
use App\Http\Controllers\Api\User\Dashboard\UserDashboardMatricsController;
use App\Http\Controllers\Api\User\SupportTicket\SupportTicketApiController;
use App\Http\Controllers\Api\User\SocialMedia\UserSocialMediaLinkController;
use App\Http\Controllers\Api\Admin\SupportTicket\AdminSupportTicketApiController;



Route::prefix('auth/user')->group(function () {
    Route::post('login', [AuthUserController::class, 'login'])->name('login');
    Route::post('register', [AuthUserController::class, 'register']);

    Route::middleware(AuthenticateUser::class)->group(function () { // Applying user middleware
        Route::post('logout', [AuthUserController::class, 'logout']);
        Route::get('me', [AuthUserController::class, 'me']);
        Route::post('change-password', [AuthUserController::class, 'changePassword']);
        Route::get('check-token', [AuthUserController::class, 'checkToken']);
    });
});

Route::prefix('user')->group(function () {
    Route::middleware(AuthenticateUser::class)->group(function () {

        Route::get('/dashboard/metrics', [UserDashboardMatricsController::class, 'getSonodMetrics']);



////// auth routes

        Route::get('sonod/list',[UserSonodController::class,'index']);
        Route::get('sonod/single/{id}',[UserSonodController::class,'show']);
        Route::get('english/sonod/single/{id}',[UserSonodController::class,'EnglishShow']);

        Route::post('sonod/action/{id}',[UserSonodController::class,'sonod_action']);

        Route::put('/sonod/update/{id}', [UserSonodController::class, 'update']);
        Route::put('english/sonod/update/{id}', [UserSonodController::class, 'updateEnglishSonod']);


        Route::post('/sonod/update/{id}', [UserSonodController::class, 'update']);
        Route::post('english/sonod/update/{id}', [UserSonodController::class, 'updateEnglishSonod']);




        Route::get('holdingtax', [HoldingtaxController::class, 'holdingSearch']);
        Route::post('holdingtax', [HoldingtaxController::class, 'store']);
        Route::get('holdingtax/{id}', [HoldingtaxController::class, 'getSingleHoldingTaxWithBokeyas']);
        Route::put('/holding-bokeya/{id}/update-price', [HoldingtaxController::class, 'updateUnpaidHoldingBokeyaPrice']);

        Route::post('/holding-tax/import', [HoldingTaxImportController::class, 'import']);
        Route::get('/holding-tax/export', [HoldingTaxImportController::class, 'export']);




        // Update Holdingtax only
        Route::put('/holdingtax/{id}', [HoldingtaxController::class, 'updateHoldingtaxOnly']);

        // Add a new bokeya by Holdingtax ID
        Route::post('/holdingtax/{holdingTaxId}/bokeya', [HoldingtaxController::class, 'addNewBokeya']);



        Route::get('/profile', [UserProfileController::class, 'getProfile']);
        Route::post('/profile', [UserProfileController::class, 'updateProfile']);



        Route::post('package/subscribe', [UserPackageController::class, 'packagePurchase']);


        // Support tickets
        Route::get('/support', [SupportTicketApiController::class, 'index']);
        Route::post('/support', [SupportTicketApiController::class, 'store']);
        Route::get('/support/{ticket}', [SupportTicketApiController::class, 'show']);
        Route::post('/support/{ticket}/reply', [AdminSupportTicketApiController::class, 'reply']);


        Route::get('/packages/history', [UserPurchasedHistoryController::class, 'getPurchasedHistory']);
        Route::get('/packages/history/{id}', [UserPurchasedHistoryController::class, 'getSinglePurchasedHistory']);


        Route::get('/union-info', [UserUniouninfoController::class, 'getUserUnionInfo']);
        Route::post('/union-info', [UserUniouninfoController::class, 'updateUserUnionInfo']);



        Route::prefix('/sonodfees')->group(function () {
            Route::post('/', [UserSonodFeeController::class, 'store']); // Create multiple SonodFees
            Route::put('/', [UserSonodFeeController::class, 'update']); // Update multiple SonodFees
        });
        Route::get('sonodnamelists/with-fees', [UserSonodFeeController::class, 'getSonodnamelistsWithFees']);


        Route::get('/failed-payments', [FailedPaymentController::class, 'index']);




        Route::post('/sms-purchase', [PurchaseSmsController::class, 'createSmsPurchase']);







    });

});


Route::prefix('social-media')->group(function () {
    // Get all social media links
    Route::get('links', [UserSocialMediaLinkController::class, 'index'])->name('socialMediaLinks.index');

    // Get a specific social media link
    Route::get('links/{id}', [UserSocialMediaLinkController::class, 'show'])->name('socialMediaLinks.show');
});

Route::prefix('coupons')->group(function () {
    Route::post('/apply', [CouponController::class, 'apply']);
    Route::post('/check', [CouponController::class, 'checkCoupon']);

});


// Password reset routes
Route::post('user/password/email', [UserPasswordResetController::class, 'sendResetLinkEmail']);
Route::post('user/password/reset', [UserPasswordResetController::class, 'reset']);



Route::post('/verify-otp', [VerificationController::class, 'verifyOtp']);
Route::post('/resend/otp', [VerificationController::class, 'resendOtp']);
Route::get('/email/verify/{hash}', [VerificationController::class, 'verifyEmail']);
Route::post('/resend/verification-link', [VerificationController::class, 'resendVerificationLink']);





