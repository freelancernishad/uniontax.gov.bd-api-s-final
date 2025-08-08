<?php

use App\Models\Sonod;
use App\Models\Payment;
use App\Models\SonodFee;
use App\Models\Uniouninfo;
use App\Models\Sonodnamelist;
use App\Models\TradeLicenseKhatFee;
use Illuminate\Support\Facades\Log;


function unionname($unionname){
    return  $sonodList = Uniouninfo::where(['short_name_e'=>$unionname])->first();

  }

function ekpayToken($trnx_id=123456789,$trns_info=[],$cust_info=[],$path='payment',$unioun_name='',$urls){


    $url = config('AKPAY_IPN_URL');


   $req_timestamp = date('Y-m-d H:i:s');

 $uniounDetials =  unionname($unioun_name);
 $AKPAY_MER_REG_ID = $uniounDetials->AKPAY_MER_REG_ID;
$AKPAY_MER_PASS_KEY = $uniounDetials->AKPAY_MER_PASS_KEY;

    if($AKPAY_MER_REG_ID=='tetulia_test'){
        $Apiurl = 'https://sandbox.ekpay.gov.bd/ekpaypg/v1';
        $whitelistip = '1.1.1.1';
    }else{
        $Apiurl = 'https://pg.ekpay.gov.bd/ekpaypg/v1';
        $whitelistip = config('WHITE_LIST_IP');
    }


   $post = [
      'mer_info' => [
         "mer_reg_id" => $AKPAY_MER_REG_ID,
         "mer_pas_key" => $AKPAY_MER_PASS_KEY
      ],
      "req_timestamp" => "$req_timestamp GMT+6",
      "feed_uri" => [
         "c_uri" => $urls['c_uri'],
         "f_uri" => $urls['f_uri'],
         "s_uri" => $urls['s_uri']
      ],
      "cust_info" => $cust_info,
      "trns_info" =>$trns_info,
      "ipn_info" => [
         "ipn_channel" => "3",
         "ipn_email" => "freelancernishad123@gmail.com",
         "ipn_uri" => "$url/api/ekpay/ipn"
      ],
      "mac_addr" => "$whitelistip"
   ];

   // 148.163.122.80
   $post = json_encode($post);
   Log::info($post);

   $ch = curl_init($Apiurl.'/merchant-api');
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
   curl_setopt($ch, CURLOPT_POST, true);
   curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
   curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
   curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json"));
   $response = curl_exec($ch);
   curl_close($ch);

/*      echo '<pre>';
   print_r($response); */

   Log::info($response);
//    return $response;

     $response = json_decode($response);

   if (isset($response->secure_token) && !empty($response->secure_token)) {
    return "$Apiurl?sToken={$response->secure_token}&trnsID={$trnx_id}";
} else {

    return $response;
}


//  return    'https://sandbox.ekpay.gov.bd/ekpaypg/v1?sToken=eyJhbGciOiJIUzUxMiJ9.eyJzdWIiOiJla3BheWNvcmUiLCJhdXRoIjoiUk9MRV9NRVJDSEFOVCIsImV4cCI6MTU0NTMyMjcxMn0.lqjBuvtqyUbhy4pteKa0IaqpjYQoEDjjnJWSFwcv0Ho2JJHN-8xqr8Q7r-tIJUy_dLajS2XbmrR6lBGrlGFYhQ&trnsID=1234'


//   return "https://sandbox.ekpay.gov.bd/ekpaypg/v1?sToken=$sToken&trnsID=$trnx_id";

}




function sonodpayment($id, $urls, $hasEnData = false,$uddoktaId=null,$only_english=false)
{
    $sonod = Sonod::findOrFail($id);





    $applicant_mobile = int_bn_to_en($sonod->applicant_mobile);
    $unioun_name = $sonod->unioun_name;
    $sonod_name = $sonod->sonod_name;

    $uniouninfo = Uniouninfo::where('short_name_e', $unioun_name)->firstOrFail();
    $sonodnamelists = Sonodnamelist::where('bnname', $sonod_name)->firstOrFail();

    if ($uniouninfo->payment_type !== 'Prepaid') {
        return response()->json(['message' => 'Only Prepaid payments are supported.'], 400);
    }

     $sonodFees = SonodFee::where([
        'service_id' => $sonodnamelists->service_id,
        'unioun' => $unioun_name
    ])->firstOrFail();

    $sonod_fee = $sonodFees->fees;
    $total_amount = $sonod_fee;

    $tradeVatAmount = 0;
    // Additional logic for 'ট্রেড লাইসেন্স'
    if ($sonod_name == 'ট্রেড লাইসেন্স') {
         $khat_id_1 = (int) $sonod->applicant_type_of_businessKhat;

         $khat_id_2 = (int) $sonod->applicant_type_of_businessKhatAmount;

    $pesaKorFee = TradeLicenseKhatFee::where('khat_id_1', 'LIKE', "%$khat_id_1%")
        ->where('khat_id_2', 'LIKE', "%$khat_id_2%")
        ->first();

        $tradeVat = 15; // Trade VAT percentage

        $pesaKor = $pesaKorFee ? $pesaKorFee->fee : 0;


              $isUnion = isUnion();
            if($isUnion){
                $tradeVatAmount = ($sonod_fee * $tradeVat) / 100;
                $signboard_fee = 0;
            }else{

                // $tradeVatAmount = ($pesaKor * $tradeVat) / 100;
                $tradeVatAmount = 0;


                $signboard_type = $sonod->signboard_type ?? 'normal';
                $signboard_size_square_fit = $sonod->signboard_size_square_fit ?? 0;
                $signboard_size_square_fit = (float) $signboard_size_square_fit; // Ensure numeric value

                $signboard_fee = 0;
                if ($signboard_type == 'normal') {
                    $signboard_fee = $signboard_size_square_fit * 100;
                } elseif ($signboard_type == 'digital_led') {
                    $signboard_fee = $signboard_size_square_fit * 150;
                }

            }





        $last_years_money = $sonod->last_years_money ?? 0;

        $total_amount = $pesaKor + $sonod_fee + $tradeVatAmount + $signboard_fee;



        $total_amount = $total_amount+$last_years_money;



    }

    // Double the amount if hasEnData is true
    if ($hasEnData) {
        $total_amount = $total_amount+$sonod_fee+$tradeVatAmount;
    }




    $total_amount = (float) $sonod->total_amount; // Ensure numeric value


    if ($total_amount < 1) {
        $total_amount = 1; // Minimum transaction amount
    }




    if ($sonod->bokeya > 0) {
        // $vat = $sonod->bokeya * 0.15;
        // $total_amount = $sonod->bokeya + $vat;
        $total_amount = $sonod->bokeya;
    }





    if($only_english){
        $total_amount = $sonod_fee; // Double the amount for English Sonod
    }





    $trnx_id = $uniouninfo->u_code . '-' . time();

    $cust_info = [
        "cust_email" => "",
        "cust_id" => (string) $sonod->id,
        "cust_mail_addr" => "Address",
        "cust_mobo_no" => $applicant_mobile,
        "cust_name" => "Customer Name"
    ];

    $trns_info = [
        "ord_det" => 'sonod',
        "ord_id" => (string) $sonod->sonod_Id,
        "trnx_amt" => $total_amount,
        "trnx_currency" => "BDT",
        "trnx_id" => $trnx_id
    ];

    $redirectUrl = ekpayToken($trnx_id, $trns_info, $cust_info, 'payment', $unioun_name, $urls);

    $req_timestamp = now();

    // Create payment record with hasEnData
    Payment::create([
        'union' => $unioun_name,
        'trxId' => $trnx_id,
        'transaction_id' => $trnx_id,
        'gateway' => 'upcoming',
        'amount' => $total_amount,
        'sonodId' => (int) $id,
        'sonod_type' => $sonod_name,
        'applicant_mobile' => $applicant_mobile,
        'status' => 'Pending',
        'paymentUrl' => !is_array($redirectUrl) ? $redirectUrl : '',
        'ipnResponse' => is_array($redirectUrl) ? $redirectUrl : '',
        'method' => 'ekpay',
        'payment_type' => 'online',
        'date' => $req_timestamp->format('Y-m-d'),
        'hasEnData' => $hasEnData,
        'uddoktaId' => $uddoktaId,
    ]);

    return $redirectUrl;
}

