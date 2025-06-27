<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;
use App\Helpers\SmsNocHelper;
use App\Models\Tenders\Tender;
use App\Models\Tenders\TenderList;
use App\Http\Controllers\Api\User\Tender\TenderListController;
use App\Http\Controllers\Api\User\Tender\TanderInvoiceController;
use App\Http\Controllers\Api\User\Tender\TenderFormBuyController;

/*
|--------------------------------------------------------------------------
| Resource Routes
|--------------------------------------------------------------------------
*/
Route::resources([
    'tender'      => TenderListController::class,
    'tenderform'  => TenderFormBuyController::class,
]);

Route::apiResource('tander_invoices', TanderInvoiceController::class);


/*
|--------------------------------------------------------------------------
| Tender Custom Routes
|--------------------------------------------------------------------------
*/

// Tender Selection
Route::post('tender/selection/{tender_id}', [TenderListController::class, 'selectionTender']);

// Committee Update
Route::post('committe/update/{id}', function (Request $request, $id) {
    $updatedData = [
        'committe1name'     => $request->committe1name,
        'committe1position' => $request->committe1position,
        'commette1phone'    => $request->commette1phone,
        'commette1pass'     => mt_rand(1000000, 9999999),
        'committe2name'     => $request->committe2name,
        'committe2position' => $request->committe2position,
        'commette2phone'    => $request->commette2phone,
        'commette2pass'     => mt_rand(1000000, 9999999),
        'committe3name'     => $request->committe3name,
        'committe3position' => $request->committe3position,
        'commette3phone'    => $request->commette3phone,
        'commette3pass'     => mt_rand(1000000, 9999999),
    ];

    $tenderList = TenderList::find($id);

    // Send SMS
    foreach ([1, 2, 3] as $i) {
        SmsNocHelper::sendSms(
            "ইযারা মূল্যায়নের পাসওয়ার্ড " . $updatedData["commette{$i}pass"],
            $updatedData["commette{$i}phone"],
            $tenderList->union_name
        );
    }

    $tenderList->update($updatedData);

    return response()->json([
        'status'  => 'success',
        'message' => 'কমিটি আপডেট হয়েছে',
        'data'    => $tenderList,
    ]);
});

// Committee Validation
Route::post('/tender/committee/validation', function (Request $request) {
    $validator = Validator::make($request->all(), [
        'tender_list_id'  => 'required|exists:tender_lists,id',
        'commette1phone'  => 'required|string',
        'commette1pass'   => 'required|string',
        'commette2phone'  => 'required|string',
        'commette2pass'   => 'required|string',
        'commette3phone'  => 'required|string',
        'commette3pass'   => 'required|string',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'status' => 'error',
            'errors' => $validator->errors(),
        ], 422);
    }

    $tender = TenderList::find($request->tender_list_id);

    $results = collect([1, 2, 3])->map(function ($i) use ($request, $tender) {
        return [
            'phone'  => $request->input("commette{$i}phone"),
            'status' => ($request->input("commette{$i}phone") === $tender->{"commette{$i}phone"}
                         && $request->input("commette{$i}pass") === $tender->{"commette{$i}pass"})
                         ? 'valid' : 'invalid',
        ];
    });

    $allValid = $results->every(fn($r) => $r['status'] === 'valid');

    return response()->json([
        'status'     => $allValid ? 'success' : 'partial',
        'all_valid'  => $allValid,
        'results'    => $results,
    ]);
});


/*
|--------------------------------------------------------------------------
| Tender Data Fetch Routes
|--------------------------------------------------------------------------
*/

Route::get('get/all/aplications/{tender_id}', function (Request $request, $tender_id) {
    $query = Tender::where('tender_id', $tender_id)
        ->where('payment_status', 'Paid');

    if ($request->status) {
        $query->where('status', $request->status);
    }

    return $query->orderBy('DorAmount', 'asc')->get();
});

Route::get('get/all/tender/list', function (Request $request) {
    $query = TenderList::orderBy('id', 'desc');

    if ($request->union_name) {
        $query->where('union_name', $request->union_name);
    }

    return $query->get();
});

Route::get('get/single/tender/{id}', function ($id) {
    return TenderList::find($id);
});


/*
|--------------------------------------------------------------------------
| Tender Form Buy Routes
|--------------------------------------------------------------------------
*/

Route::get('/tenders/form/buy/{tender_id}', function ($tender_id) {
    $tender_list = TenderList::where('tender_id', $tender_id)->first();

    if (!$tender_list) {
        return '<h1 style="text-align:center;color:red">কোনও তথ্য খুজে পাওয়া জায় নি</h1>';
    }

    $currentDate = now()->timestamp;
    $form_buy_last_date = strtotime($tender_list->form_buy_last_date);

    if ($currentDate < $form_buy_last_date) {
        $tender_list->update(['status' => 'active']);
        return view('tender.formbuy', compact('tender_list'));
    }

    return '<h1 style="text-align:center;color:red">সিডিউল ফর্ম কেনার সময় শেষ</h1>';
});


/*
|--------------------------------------------------------------------------
| Tender Submission & Payment Routes
|--------------------------------------------------------------------------
*/

Route::post('/drop/tender', function (Request $request) {
    $data = $request->except(['_token', 'bank_draft_image', 'deposit_details', 'dorId']);



    $data['payment_status'] = 'Unpaid';

    $tender = Tender::create($data);





    // Step 2: Now upload the file (if provided) and update the model
    if ($request->hasFile('bank_draft_image')) {
        $file = $request->file('bank_draft_image');
        $uploadedPath = Tender::uploadBankDraftImage($file, $tender->id);

        if ($uploadedPath) {
            $tender->update(['bank_draft_image' => $uploadedPath]);
        }
    } else {
        // Optional fallback (if needed)
        $tender->update(['bank_draft_image' => 'images/bank_draft_image/default.png']);
    }


    return response()->json([
        'status'       => 'success',
        'message'      => 'আপনার দরপত্রটি দাখিল হয়েছে',
        'redirect_url' => url("/api/tenders/payment/{$tender->id}"),
        'data'         => $tender,
    ]);
});

Route::get('/tenders/payment/{id}', [TenderListController::class, 'PaymentCreate']);
Route::get('tender/payment/{tender_id}', [TanderInvoiceController::class, 'tanderDepositAmount']);


/*
|--------------------------------------------------------------------------
| PDF Generation Routes
|--------------------------------------------------------------------------
*/

Route::get('/pdf/tenders/work/access/{tender_id}', [TenderListController::class, 'workAccessPdf']);
Route::get('/pdf/tenders/{tender_id}', [TenderListController::class, 'viewpdf']);

Route::get('/pdf/sder/download/{tender_id}', function ($tender_id) {
    $applications = Tender::where('tender_id', $tender_id)->get();

    $html = '
    <style>
        td, th {
            border: 1px solid black;
            padding: 4px 10px;
            font-size: 14px;
        }
    </style>
    <p style="text-align:center;font-size:25px">দরপত্র দাখিল কারীর তালিকা</p>
    <table style="border-collapse: collapse; width:100%">
        <thead>
            <tr>
                <th>দরপত্র নম্বর</th>
                <th>নাম</th>
                <th>পিতার নাম</th>
                <th>ঠিকানা</th>
                <th>মোবাইল</th>
                <th>দরের পরিমাণ</th>
                <th>কথায়</th>
                <th>জামানতের পরিমাণ</th>
            </tr>
        </thead>
        <tbody>';

    foreach ($applications as $app) {
        $html .= "<tr>
            <td>{$app->dorId}</td>
            <td>{$app->applicant_orgName}</td>
            <td>{$app->applicant_org_fatherName}</td>
            <td>গ্রাম- {$app->vill}, ডাকঘর- {$app->postoffice}, উপজেলা- {$app->thana}, জেলা- {$app->distric}</td>
            <td>{$app->mobile}</td>
            <td>{$app->DorAmount}</td>
            <td>{$app->DorAmountText}</td>
            <td>{$app->depositAmount}</td>
        </tr>";
    }

    $html .= '</tbody></table>';

    return PdfMaker('A4', $html, 'list', false);
});


/*
|--------------------------------------------------------------------------
| Tender Form Submit (GET/POST)
|--------------------------------------------------------------------------
*/

Route::match(['get', 'post'], '/tenders/{tender_id}', [TenderListController::class, 'TenderForm']);


Route::post('tenderlist/{id}/update-permit-details', [TenderListController::class, 'updatePermitDetials']);
