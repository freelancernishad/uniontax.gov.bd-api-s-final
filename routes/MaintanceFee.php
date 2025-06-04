<?php

use Illuminate\Support\Facades\Route;

use App\Http\Middleware\AuthenticateUser;
use App\Http\Middleware\AuthenticateAdmin;
use App\Http\Controllers\Api\User\MaintanceFee\MaintanceFeeController;
use App\Http\Controllers\Api\Admin\MaintanceFee\MaintanceFeeController as AdminMaintanceFeeController;


Route::middleware(AuthenticateUser::class)->prefix('user/maintance-fees')->group(function () {
    Route::get('/', [MaintanceFeeController::class, 'index']);
    Route::post('/', [MaintanceFeeController::class, 'store']);

});

Route::post('/maintance-fee/execute', [MaintanceFeeController::class, 'maintanceFeeExecute']);

Route::get('/maintance-fee/{id}/invoice', [MaintanceFeeController::class, 'downloadInvoice']);


Route::middleware(AuthenticateAdmin::class)->prefix('admin/maintance-fees')->group(function () {
    Route::get('/', [AdminMaintanceFeeController::class, 'index']);
    Route::post('/', [AdminMaintanceFeeController::class, 'store']);
    Route::get('/{id}', [AdminMaintanceFeeController::class, 'show']);
    Route::put('/{id}', [AdminMaintanceFeeController::class, 'update']);
    Route::delete('/{id}', [AdminMaintanceFeeController::class, 'destroy']);


    Route::post('/maintenance/unpaid-unions', [AdminMaintanceFeeController::class, 'unionListByStatus']);


});
