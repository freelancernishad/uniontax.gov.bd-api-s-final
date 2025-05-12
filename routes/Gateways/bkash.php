<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BkashPaymentController;





    // Route::get('/bkash/token', function () {
    //     return response()->json([
    //         'message' => 'Bkash token route',
    //     ]);
    // });




Route::prefix('bkash')->group(function () {
    Route::post('/token', [BkashPaymentController::class, 'grantToken']);
    Route::post('/create', [BkashPaymentController::class, 'createPayment']);

    Route::post('/initiate', [BkashPaymentController::class, 'generatePaymentUrl']);




    Route::post('/execute', [BkashPaymentController::class, 'executePayment']);
    Route::get('/query/{paymentID}', [BkashPaymentController::class, 'queryPayment']);
    Route::post('/refund', [BkashPaymentController::class, 'refundPayment']);
});
