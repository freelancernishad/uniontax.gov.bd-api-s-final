<?php

use App\Http\Controllers\Api\Admin\Reports\ReportsController;
use App\Mail\TestMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Api\Global\Sonod\SonodPdfController;
use App\Http\Controllers\Api\Global\Sonod\InvoicePdfController;
use App\Http\Controllers\Api\Global\Sonod\DocumentPdfController;
use App\Http\Controllers\Api\User\Reports\PaymentReportsController;
use App\Http\Controllers\Api\SystemSettings\SystemSettingController;
use App\Http\Controllers\Api\Global\HoldingTax\HoldingTaxPdfController;





Route::get('/sonod/d/{id}', [SonodPdfController::class,'sonodDownload']);
Route::get('/sonod/download/{id}', [SonodPdfController::class,'sonodDownload']);

Route::get('/document/d/{id}', [DocumentPdfController::class,'userDocument']);
Route::get('/applicant/copy/download/{id}', [DocumentPdfController::class,'userDocument']);

Route::get('/sonod/invoice/download/{id}', [InvoicePdfController::class,'invoice']);

Route::get('payment/report/download', [PaymentReportsController::class,'PaymentReports']);


Route::get('/download/reports/get-reports', [ReportsController::class,'downloadReports']);



Route::get('holding/tax/invoice/{id}', [HoldingTaxPdfController::class,'holdingPaymentInvoice']);
Route::get('/holding/tax/certificate_of_honor/{id}', [HoldingTaxPdfController::class,'holdingCertificate_of_honor']);


Route::get('/', function () {
    return view('welcome');
});

// For web routes
Route::get('/clear-cache', [SystemSettingController::class, 'clearCache']);


Route::get('send-test-email', function () {
    $email = 'freelancernishad123@gmail.com';  // Enter your test email here

    try {
        Mail::to($email)->send(new TestMail());
        return response()->json('Test email sent!');
    } catch (\Exception $e) {
        return response()->json('Error: ' . $e->getMessage());
    }

});



Route::get('/files/{path}', function ($path) {
    try {
        // Check if the file exists in the protected disk
        if (!Storage::disk('protected')->exists($path)) {
            return response()->json([
                'error' => 'File not found',
            ], 404);
        }

        // Serve the file directly with custom headers
        return response()->file(Storage::disk('protected')->path($path))
            ->withHeaders([
                'Content-Type' => 'application/octet-stream',  // Adjust MIME type if needed
                'Cache-Control' => 'no-cache, no-store, must-revalidate',
                'Pragma' => 'no-cache',
                'Expires' => '0',
            ]);
    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage(),
        ], 500);
    }
})->where('path', '.*');



