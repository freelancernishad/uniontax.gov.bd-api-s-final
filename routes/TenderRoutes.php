<?php
use Illuminate\Http\Request;
use App\Helpers\SmsNocHelper;
use App\Models\Tenders\Tender;
use App\Models\Tenders\TenderList;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Api\User\Tender\TenderListController;
use App\Http\Controllers\Api\User\Tender\TanderInvoiceController;
use App\Http\Controllers\Api\User\Tender\TenderFormBuyController;



Route::resources([
	'tender' => TenderListController::class,
	'tenderform' => TenderFormBuyController::class,
]);

Route::post('tender/selection/{tender_id}', [TenderListController::class,'SeletionTender']);

    Route::post('committe/update/{id}', function (Request $request,$id) {

        $committe1name = $request->committe1name;
        $committe1position = $request->committe1position;
        $commette1phone = $request->commette1phone;

        $committe2name = $request->committe2name;
        $committe2position = $request->committe2position;
        $commette2phone = $request->commette2phone;

        $committe3name = $request->committe3name;
        $committe3position = $request->committe3position;
        $commette3phone = $request->commette3phone;




        $updatedData = [
            'committe1name'=> $committe1name,
            'committe1position'=> $committe1position,
            'commette1phone'=> $commette1phone,
            'commette1pass'=> mt_rand(1000000, 9999999),
            'committe2name'=> $committe2name,
            'committe2position'=> $committe2position,
            'commette2phone'=> $commette2phone,
            'commette2pass'=> mt_rand(1000000, 9999999),
            'committe3name'=> $committe3name,
            'committe3position'=> $committe3position,
            'commette3phone'=> $commette3phone,
            'commette3pass'=> mt_rand(1000000, 9999999),
        ];



        $tenderList = TenderList::find($id);

        SmsNocHelper::sendSms("ইযারা মূল্যায়নের পাসওয়ার্ড ".$updatedData['commette1pass'],$updatedData['commette1phone'],$tenderList->union_name);
        SmsNocHelper::sendSms("ইযারা মূল্যায়নের পাসওয়ার্ড ".$updatedData['commette2pass'],$updatedData['commette2phone'],$tenderList->union_name);
        SmsNocHelper::sendSms("ইযারা মূল্যায়নের পাসওয়ার্ড ".$updatedData['commette3pass'],$updatedData['commette3phone'],$tenderList->union_name);




        // return $updatedData;

        $tenderList->update($updatedData);


        return response()->json([
            'status' => 'success',
            'message' => 'কমিটি আপডেট হয়েছে',
            'data' => $tenderList
        ]);

    });


    Route::get('get/all/tender/list', function (Request $request) {
    $union_name = $request->union_name;

    if($union_name){
        return TenderList::where('union_name',$union_name)->orderBy('id','desc')->get();
    }else{
        return TenderList::orderBy('id','desc')->get();

    }
  });

Route::get('get/single/tender/{id}', function (Request $request,$id) {

        return TenderList::find($id);

  });

    Route::apiResource('tander_invoices', TanderInvoiceController::class);
Route::get('tender/payment/{tender_id}', [TanderInvoiceController::class,'tanderDepositAmount']);



 Route::get('/pdf/tenders/work/access/{tender_id}', [TenderListController::class,'workAccessPdf']);
Route::get('/pdf/tenders/{tender_id}', [TenderListController::class,'viewpdf']);

Route::get('/tenders/form/buy/{tender_id}', function ($tender_id) {


    $tender_list_count = TenderList::where('tender_id',$tender_id)->count();
    if($tender_list_count<1){
        return '<h1 style="text-align:center;color:red">কোনও তথ্য খুজে পাওয়া জায় নি</h1>';
    }

    $tender_list = TenderList::where('tender_id',$tender_id)->first();

      $currentDate = strtotime(date("d-m-Y H:i:s"));

    $form_buy_last_date = strtotime(date("d-m-Y H:i:s",strtotime($tender_list->form_buy_last_date)));



   if($currentDate<$form_buy_last_date){

    $tender_list->update(['status'=>'active']);
       return view('tender.formbuy',compact('tender_list'));

    }else{

        return '<h1 style="text-align:center;color:red">সিডিউল ফর্ম কেনার সময় শেষ</h1>';
   }





});

Route::get('/tenders/payment/{id}', [TenderListController::class,'PaymentCreate']);


Route::get('/tenders/{tender_id}', [TenderListController::class,'TenderForm']);
Route::post('/tenders/{tender_id}', [TenderListController::class,'TenderForm']);

Route::post('/drop/tender', function (Request $request) {



        $data = $request->except(['_token','bank_draft_image','deposit_details','dorId']);

        // $bank_draft_image = $request->file('bank_draft_image');
        // $extension = $bank_draft_image->getClientOriginalExtension();
        // $path = public_path('files/bank_draft_image/');
        // $fileName = $request->dorId.'-'.uniqid().'.'.$extension;
        // $bank_draft_image->move($path, $fileName);
        // $bank_draft_image = asset('files/bank_draft_image/'.$fileName);
        $bank_draft_image = 'images/bank_draft_image/default.png';



        $data['bank_draft_image'] = $bank_draft_image;
        $data['payment_status'] = 'Unpaid';





      $tender =  Tender::create($data);
    //   Session::flash('Smessage', 'আপনার দরপত্রটি দাখিল হয়েছে');

      $redirectUrl = url("/api/tenders/payment/$tender->id");
      return response()->json([
          'status' => 'success',
          'message' => 'আপনার দরপত্রটি দাখিল হয়েছে',
          'redirect_url' => $redirectUrl,
          'data' => $tender
      ]);

    //   return redirect()->back();


    });


    Route::get('/pdf/sder/download/{tender_id}', function (Request $request,$tender_id) {


        $html = '
        <style>
        td{
            border: 1px solid black;
            padding:4px 10px;
            font-size: 14px;
        }    th{
            border: 1px solid black;
            padding:4px 10px;
            font-size: 14px;
        }
        </style>
            <p style="text-align:center;font-size:25px">দরপত্র দাখিল কারীর তালিকা</p>


        <table class="table" border="1" style="border-collapse: collapse;width:100%">
        <thead>
            <tr>
            <td>দরপত্র নম্বর</td>
            <td>নাম</td>
            <td>পিতার নাম</td>
            <td>ঠিকানা</td>
            <td>মোবাইল</td>
            <td>দরের পরিমাণ</td>
            <td>কথায়</td>
            <td>জামানতের পরিমাণ</td>
            </tr>
        </thead>
        <tbody>';
                $tenders =  Tender::where('tender_id',$tender_id)->get();
            foreach ($tenders as $application) {


            $html .= " <tr>
                <td>$application->dorId</td>
                <td>$application->applicant_orgName</td>
                <td>$application->applicant_org_fatherName</td>
                <td>গ্রাম- $application->vill, ডাকঘর- $application->postoffice, উপজেলা- $application->thana, জেলা- $application->distric</td>
                <td>$application->mobile</td>
                <td>$application->DorAmount</td>
                <td>$application->DorAmountText</td>
                <td>$application->depositAmount</td>
            </tr>";
        }


            $html .= '

        </tbody>
        </table>



        ';
        return PdfMaker('A4',$html,'list',false);



    });



Route::post('/tender/committee/validation', function (Request $request) {
    $validator = Validator::make($request->all(), [
        'tender_list_id' => 'required|exists:tender_lists,id',
        'committee' => 'required|array|size:3',
        'committee.*.phone' => 'required|string',
        'committee.*.pass' => 'required|string',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'status' => 'error',
            'errors' => $validator->errors()
        ], 422);
    }

    $tender = \App\Models\Tenders\TenderList::find($request->tender_list_id);

    $expected = [
        ['phone' => $tender->commette1phone, 'pass' => $tender->commette1pass],
        ['phone' => $tender->commette2phone, 'pass' => $tender->commette2pass],
        ['phone' => $tender->commette3phone, 'pass' => $tender->commette3pass],
    ];

    $results = [];

    foreach ($request->committee as $index => $input) {
        $valid = false;
        foreach ($expected as $exp) {
            if ($input['phone'] == $exp['phone'] && $input['pass'] == $exp['pass']) {
                $valid = true;
                break;
            }
        }
        $results[] = [
            'phone' => $input['phone'],
            'status' => $valid ? 'valid' : 'invalid'
        ];
    }

    $allValid = collect($results)->every(fn ($r) => $r['status'] === 'valid');

    return response()->json([
        'status' => $allValid ? 'success' : 'partial',
        'all_valid' => $allValid,
        'results' => $results
    ]);
});






