<?php
use Illuminate\Support\Facades\Route;



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

        SmsNocSmsSend("ইযারা মূল্যায়নের পাসওয়ার্ড ".$updatedData['commette1pass'],$updatedData['commette1phone'],$tenderList->union_name);
        SmsNocSmsSend("ইযারা মূল্যায়নের পাসওয়ার্ড ".$updatedData['commette2pass'],$updatedData['commette2phone'],$tenderList->union_name);
        SmsNocSmsSend("ইযারা মূল্যায়নের পাসওয়ার্ড ".$updatedData['commette3pass'],$updatedData['commette3phone'],$tenderList->union_name);




        // return $updatedData;

        $tenderList->update($updatedData);



    });
