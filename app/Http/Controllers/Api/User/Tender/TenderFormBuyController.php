<?php

namespace App\Http\Controllers\Api\User\Tender;
use App\Models\Payment;

use Illuminate\Http\Request;
use App\Models\Tenders\Tender;
use App\Http\Controllers\Controller;
use App\Models\Tenders\TanderInvoice;
use App\Models\Tenders\TenderFormBuy;

class TenderFormBuyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
       $tender_id = $request->tender_id;
       $name = $request->name;
       $PhoneNumber = $request->PhoneNumber;

       $form_code = mt_rand(1000000, 9999999);

       $datas = [
            'tender_id'=>$tender_id,
            'name'=>$name,
            'PhoneNumber'=>$PhoneNumber,
            'form_code'=>$form_code,
            'status'=>'Unpaid',
       ];
       $tenderformbuy = TenderFormBuy::create($datas);

       return redirect("/tenders/payment/$tenderformbuy->id");

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\TenderFormBuy  $tenderFormBuy
     * @return \Illuminate\Http\Response
     */
    public function show(TenderFormBuy $tenderFormBuy)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\TenderFormBuy  $tenderFormBuy
     * @return \Illuminate\Http\Response
     */
    public function edit(TenderFormBuy $tenderFormBuy)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\TenderFormBuy  $tenderFormBuy
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, TenderFormBuy $tenderFormBuy)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\TenderFormBuy  $tenderFormBuy
     * @return \Illuminate\Http\Response
     */
    public function destroy(TenderFormBuy $tenderFormBuy)
    {
        //
    }


    function tenderFormPaymentSuccess(Request $request){


        $transId =  $request->transId;
        $payment = Payment::where(['trxId' => $transId])->first();
        $id = $payment->sonodId;
        $sonod = Tender::find($id);






                    if($payment->status=='Paid'){
                        $deccription = "Congratulation! Your application $sonod->dorId has been Paid.Wait for Approval.";
                        return view('tenderSuccess', compact('payment', 'sonod'));
                    }else{
                    echo "
                    <div style='text-align:center'>
                    <h1 style='text-align:center'>Payment Failed</h1>
                    <a href='/' style='border:1px solid black;padding:10px 12px; background:red;color:white'>Back To Home</a>
                    <a href='/sonod/payment/$sonod->id' style='border:1px solid black;padding:10px 12px; background:green;color:white'>Pay Again</a>
                    </div>
                    ";
                    }


    }


    function tenderdepositPaymentSuccess(Request $request){


        $transId =  $request->transId;
        $payment = Payment::where(['trxId' => $transId])->first();
        $id = $payment->sonodId;
        $sonod = TanderInvoice::find($id);







                    if($payment->status=='Paid'){
                        $deccription = "Congratulation! Payment Successfully Paid";
                        return view('tenderdepositSuccess', compact('payment', 'sonod'));
                    }else{
                    echo "
                    <div style='text-align:center'>
                    <h1 style='text-align:center'>Payment Failed</h1>
                    <a href='/' style='border:1px solid black;padding:10px 12px; background:red;color:white'>Back To Home</a>
                    </div>
                    ";
                    }


    }


}
