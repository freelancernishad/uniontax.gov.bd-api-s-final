<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\User\VillageCourt\VillageCourtCaseController;



Route::prefix('village-court')->group(function () {
    Route::resource('cases', VillageCourtCaseController::class);
    Route::post('cases/{caseId}/summons', [VillageCourtCaseController::class, 'addSummon']);
    Route::post('cases/{caseId}/nominations', [VillageCourtCaseController::class, 'addNomination']);
    Route::post('cases/{caseId}/fees', [VillageCourtCaseController::class, 'addFee']);
    Route::post('cases/{caseId}/fines', [VillageCourtCaseController::class, 'addFine']);
    Route::post('cases/{caseId}/decrees', [VillageCourtCaseController::class, 'addDecree']);
    Route::post('cases/{caseId}/transfers', [VillageCourtCaseController::class, 'addCaseTransfer']);
    Route::post('cases/{caseId}/attendances', [VillageCourtCaseController::class, 'addAttendance']);
    Route::post('cases/{id}/complete', [VillageCourtCaseController::class, 'completeCase']);
});
