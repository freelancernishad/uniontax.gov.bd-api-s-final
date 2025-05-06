<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\AuthenticateUddokta;
use App\Http\Controllers\Api\Auth\Uddokta\AuthUddoktaController;
use App\Http\Controllers\Api\User\Holdingtax\HoldingtaxController;
use App\Http\Controllers\Api\Auth\Uddokta\CitizenInformationController;
use App\Http\Controllers\Api\Auth\Uddokta\UddoktaVerificationController;
use App\Http\Controllers\Api\Auth\Uddokta\UddoktaPasswordResetController;

// Uddokta Authentication Routes
Route::prefix('auth/uddokta')->group(function () {
    Route::post('login', [AuthUddoktaController::class, 'login'])->name('uddokta.login');
    Route::post('register', [AuthUddoktaController::class, 'register']);

    // Routes protected by uddokta middleware
    Route::middleware(AuthenticateUddokta::class)->group(function () {
        Route::post('logout', [AuthUddoktaController::class, 'logout']);
        Route::get('me', [AuthUddoktaController::class, 'me']);
        Route::post('change-password', [AuthUddoktaController::class, 'changePassword']);
        Route::get('check-token', [AuthUddoktaController::class, 'checkToken']);



        Route::get('holdingtax', [HoldingtaxController::class, 'holdingSearch']);
        Route::post('holdingtax', [HoldingtaxController::class, 'store']);
        Route::get('holdingtax/{id}', [HoldingtaxController::class, 'getSingleHoldingTaxWithBokeyas']);
        Route::put('/holding-bokeya/{id}/update-price', [HoldingtaxController::class, 'updateUnpaidHoldingBokeyaPrice']);



        Route::post('citizen/information/nid', [CitizenInformationController::class,'citizeninformationNID']);
        Route::post('citizen/information/brn', [CitizenInformationController::class,'citizeninformationBRN']);


    });
});


// Uddokta Password Reset Routes
Route::post('uddokta/password/email', [UddoktaPasswordResetController::class, 'sendResetLinkEmail']);
Route::post('uddokta/password/reset', [UddoktaPasswordResetController::class, 'reset']);

// Uddokta Verification Routes
Route::post('uddokta/verify-otp', [UddoktaVerificationController::class, 'verifyOtp']);
Route::post('uddokta/resend/otp', [UddoktaVerificationController::class, 'resendOtp']);
Route::get('uddokta/email/verify/{hash}', [UddoktaVerificationController::class, 'verifyEmail']);
Route::post('uddokta/resend/verification-link', [UddoktaVerificationController::class, 'resendVerificationLink']);
