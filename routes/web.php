<?php

use Aws\S3\S3Client;
use App\Models\Sonod;
use App\Mail\TestMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\GoogleController;
use App\Http\Controllers\Api\Global\Sonod\SonodController;

use App\Http\Controllers\Api\Admin\Reports\ReportsController;
use App\Http\Controllers\Api\Global\Sonod\SonodPdfController;
use App\Http\Controllers\Api\User\Tender\TenderListController;
use App\Http\Controllers\Api\Global\Sonod\InvoicePdfController;
use App\Http\Controllers\Api\Global\Sonod\DocumentPdfController;
use App\Http\Controllers\Api\User\Reports\PaymentReportsController;
use App\Http\Controllers\Api\SystemSettings\SystemSettingController;
use App\Http\Controllers\Api\Global\HoldingTax\HoldingTaxPdfController;
use App\Http\Controllers\Api\User\Holdingtax\HoldingPdfReportController;
use App\Http\Controllers\Api\Global\AutoBike\AutoBikeDocumentPdfController;

Route::get('create/payment', [SonodController::class,'creatingEkpayUrl']);


Route::get('/check-octane', function () {
    if (app()->bound('octane') && app('octane')->isRunning()) {
        return response()->json(['status' => 'Octane is running']);
    }

    return response()->json(['status' => 'Octane is not running']);
});









// Route::get('/sonod/s/{id}', function ($id) {
//     $sonod = Sonod::find($id);
//     if ($sonod) {
//          $union = \App\Models\SiteSetting::where('key', 'union')->first()->value;

//          if($union) {
//             $url = 'https://uniontax.gov.bd/sonod/search?sonodType=' . $sonod->sonod_name . '&sonodNo=' . $sonod->sonod_Id;
//          }else{
//             $url = 'https://pouroseba.gov.bd/sonod/search?sonodType=' . $sonod->sonod_name . '&sonodNo=' . $sonod->sonod_Id;

//         }
//         return redirect($url);

//     } else {
//         return response()->json(['error' => 'Sonod not found'], 404);
//     }
// });



Route::get('/sonod/s/{id}', function ($id) {
    $sonod = \App\Models\Sonod::find($id);

    if (!$sonod) {
        return response()->json(['error' => 'Sonod not found'], 404);
    }

    $sonodNo = $sonod->sonod_Id;
    $banglaUrl = url("/sonod/d/$id");
    $englishUrl = url("/sonod/d/$id?en=true");

    // Conditional English button HTML
    $englishButton = '';
    if ($sonod->hasEnData) {
        $englishButton = "<a href='{$englishUrl}' class='btn btn-success btn-lg' target='_blank'>English Sonod Download</a>";
    }

    return response()->make("
        <!DOCTYPE html>
        <html lang='bn'>
        <head>
            <meta charset='UTF-8'>
            <title>সনদ ডাউনলোড</title>
            <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css' rel='stylesheet'>
            <style>
                body {
                    background-color: #f0f8ff;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    height: 100vh;
                    margin: 0;
                }
                .download-container {
                    text-align: center;
                    padding: 40px;
                    background-color: #ffffff;
                    border-radius: 16px;
                    box-shadow: 0 0 15px rgba(0,0,0,0.1);
                }
                .btn-lg {
                    font-size: 1.5rem;
                    padding: 15px 30px;
                    margin: 15px;
                    border-radius: 10px;
                }
            </style>
        </head>
        <body>
            <div class='download-container'>
                <h2 class='mb-4'>সনদ নম্বরঃ {$sonodNo}</h2>
                <a href='{$banglaUrl}' class='btn btn-primary btn-lg' target='_blank'>বাংলা সনদ ডাউনলোড</a>
                {$englishButton}
            </div>
        </body>
        </html>
    ");
});










Route::get('/sonod/d/{id}', [SonodPdfController::class,'sonodDownload']);
Route::get('/sonod/download/{id}', [SonodPdfController::class,'sonodDownload']);






Route::get('/verification/sonod/{id}', [SonodPdfController::class,'sonodVerify']);





Route::get('/document/d/{id}', [DocumentPdfController::class,'userDocument']);
Route::get('/applicant/copy/download/{id}', [DocumentPdfController::class,'userDocument']);


Route::get('/auto/bike/applicant/copy/download/{id}', [AutoBikeDocumentPdfController::class,'userDocument']);



Route::get('/sonod/invoice/download/{id}', [InvoicePdfController::class,'invoice']);

Route::get('/tender/invoice/download/{id}', [TenderListController::class,'invoice']);

Route::get('payment/report/download', [PaymentReportsController::class,'PaymentReports']);


Route::get('/download/reports/get-reports', [ReportsController::class,'downloadReports']);


Route::get('holding/tax/bokeya/list',[HoldingTaxPdfController::class,'bokeyaReport']);


Route::get('holding/tax/invoice/{id}', [HoldingTaxPdfController::class,'holdingPaymentInvoice']);
Route::get('/holding/tax/certificate_of_honor/{id}', [HoldingTaxPdfController::class,'holdingCertificate_of_honor']);

Route::get('/holding/familly/report/single/{id}', [HoldingPdfReportController::class,'holdingFamillySingleReportPDF']);


Route::get('/', function () {
    return response()->json('success');
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

Route::get('/file/{filename}', function ($filename) {

    return getUploadDocumentsToS3($filename);


})->where('filename', '.*');


Route::get('/files/{path}', function ($path) {
    try {
        // Check if the file exists in the protected disk
        if (!Storage::disk('protected')->exists($path)) {
            return response()->json([
                'error' => 'File not found',
            ], 404);
        }

        // Serve the file directly with custom headers
        return response()->file(Storage::disk('protected')->path($path));
    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage(),
        ], 500);
    }
})->where('path', '.*');



