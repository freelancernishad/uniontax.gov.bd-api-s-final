<?php

namespace App\Http\Controllers\Api\User\Tender;


use App\Models\Payment;

use App\Models\Uniouninfo;
use Illuminate\Http\Request;

use App\Models\Tenders\TenderList;
use App\Http\Controllers\Controller;
use App\Models\Tenders\TanderInvoice;
use Illuminate\Support\Facades\Validator;

class TanderInvoiceController extends Controller
{
    public function index()
    {
        return TanderInvoice::all();
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tanderid' => 'required|integer',
            'amount' => 'required|numeric',
            'khat' => 'required|string|max:255',
            'orthobochor' => 'required|string|max:255',

        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }


        $tender = TenderList::find($request->tanderid);
        $union_name = $tender->union_name;
        $unioninfos = Uniouninfo::where(['short_name_e' => $union_name])->first();
        $u_code = $unioninfos->u_code;


        $insertData = $request->all();
        $insertData["union_name"] = $union_name;
        $insertData["status"] = 'pending';
        $insertData["date"] = date('Y-m-d');
        $insertData["year"] = date('Y');

        $amount = $request->amount;


        $applicant_mobile = "01909756552";
        $tanderInvoice = TanderInvoice::create($insertData);
        $id = $tanderInvoice->id;

        $trnx_id = $u_code.'-'.time();
        $cust_info = [
            "cust_email" => "",
            "cust_id" => "$id",
            "cust_mail_addr" => "Address",
            "cust_mobo_no" => $applicant_mobile,
            "cust_name" => "Customer Name"
        ];
        $trns_info = [
            "ord_det" => 'sonod',
            "ord_id" => "$tanderInvoice->tender_id",
            "trnx_amt" => $amount,
            "trnx_currency" => "BDT",
            "trnx_id" => "$trnx_id"
        ];



        $redirectutl = ekpayToken($trnx_id, $trns_info, $cust_info, 'payment', $union_name, [
            "c_uri" => url("payment/cancel"),
            "f_uri" => url("payment/fail"),
            "s_uri" => url("payment/success")
        ]);



        $req_timestamp = date('Y-m-d H:i:s');
        $customerData = [
            'union' => $union_name,
            'trxId' => $trnx_id,
            'sonodId' => $id,
            'sonod_type' => "tender-deposit",
            'amount' => $amount,
            'mob' => $applicant_mobile,
            'status' => "Pending",
            'paymentUrl' => $redirectutl,
            'method' => 'ekpay',
            'payment_type' => 'online',
            'date' => date('Y-m-d'),
            'created_at' => $req_timestamp,
        ];
        Payment::create($customerData);



        $tanderInvoice->load('tenderList');

        return response()->json(['tanderInvoice'=>$tanderInvoice,'redirectutl'=>$redirectutl], 201);
    }

    public function show($id)
    {
        $tanderInvoice = TanderInvoice::find($id);

        if (is_null($tanderInvoice)) {
            return response()->json(['message' => 'TanderInvoice not found'], 404);
        }

        return response()->json($tanderInvoice);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'tanderid' => 'required|integer',
            'amount' => 'required|numeric',
            'khat' => 'required|string|max:255',
            'orthobochor' => 'required|string|max:255',
            'status' => 'required|string|max:255',
            'date' => 'required|date',
            'year' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $tanderInvoice = TanderInvoice::find($id);

        if (is_null($tanderInvoice)) {
            return response()->json(['message' => 'TanderInvoice not found'], 404);
        }

        $tanderInvoice->update($request->all());

        return response()->json($tanderInvoice);
    }

    public function destroy($id)
    {
        $tanderInvoice = TanderInvoice::find($id);

        if (is_null($tanderInvoice)) {
            return response()->json(['message' => 'TanderInvoice not found'], 404);
        }

        $tanderInvoice->delete();

        return response()->json(null, 204);
    }


    function tanderDepositAmount($tender_id) {

        $invoiceLists = TanderInvoice::with('tenderList')->where(['tanderid'=>$tender_id,'status'=>'Paid'])->get();
        return response()->json($invoiceLists);
    }
}
