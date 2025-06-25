<?php

namespace App\Http\Controllers\Api\Gateway\Ekpay;

use App\Models\Sonod;

use App\Models\Payment;

use App\Models\Uniouninfo;
use Illuminate\Http\Request;
use App\Helpers\SmsNocHelper;
use App\Models\HoldingBokeya;

use App\Models\UddoktaSearch;
use App\Models\Tenders\Tender;
use App\Models\EkpayCredential;
use App\Models\Tenders\TenderList;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\Tenders\TanderInvoice;

class EkpayController extends Controller
{
    public function ipn(Request $request)
    {
        // Log the incoming request data for debugging
        $data = $request->all();
        Log::info('Received IPN data: ' . json_encode($data));

        // Validate that the data is not empty
        if (empty($data)) {
            Log::error('IPN data is empty');
            return response()->json(['error' => 'IPN data is empty'], 400);
        }

        // Validate that required keys exist in the data
        $requiredKeys = ['cust_info', 'trnx_info', 'msg_code', 'pi_det_info'];
        foreach ($requiredKeys as $key) {
            if (!isset($data[$key])) {
                Log::error('Missing key in IPN data: ' . $key);
                return response()->json(['error' => 'Missing key: ' . $key], 400);
            }
        }

        // Proceed with processing the data
        $sonod = Sonod::find($data['cust_info']['cust_id']);
        $trnx_id = $data['trnx_info']['mer_trnx_id'];  // Transaction ID
        $payment = Payment::where('trxid', $trnx_id)->first();

        // Prepare the data to be inserted or updated
        $Insertdata = [];

        // Process based on the message code
        if ($data['msg_code'] == '1020') {
            // Payment was successful
            $Insertdata = [
                'status' => 'Paid',
                'method' => $data['pi_det_info']['pi_name'],
            ];

            // Call appropriate method based on sonod_type
            if ($payment->sonod_type == 'holdingtax') {
                $this->updateHoldingTaxStatus($payment, $Insertdata);
            } elseif ($payment->sonod_type == 'Tenders_form') {
                $this->updateTenderFormStatus($payment, $Insertdata, $data);
            } elseif ($payment->sonod_type == 'tender-deposit') {
                $this->updateTenderDepositStatus($payment, $Insertdata);
            } else {
                $this->updateSonodFeeStatus($sonod, $Insertdata);
            }

        } else {
            // Payment failed
            $sonod->update(['khat' => 'সনদ ফি', 'stutus' => 'failed', 'payment_status' => 'Failed']);
            $Insertdata = ['status' => 'Failed'];
        }

        // Log the IPN response
        $Insertdata['ipnResponse'] = $data;

        // Update the payment record with the new status and response data
         $payment->update($Insertdata);

         // If payment has uddoktaId, delete matching UddoktaSearch records
        if ($payment->uddoktaId) {
            UddoktaSearch::where('uddokta_id', $payment->uddoktaId)->delete();
        }




        return response()->json(['message' => 'IPN processed successfully'], 200);

    }

    // Update HoldingTax payment status
    private function updateHoldingTaxStatus($payment, &$Insertdata)
    {
        $hosdingBokeya = HoldingBokeya::find($payment->sonodId);
        $hosdingBokeya->update(['status' => 'Paid', 'payYear' => date('Y'), 'payOB' => CurrentOrthoBochor(1)]);  // Update Holding Bokeya
    }

    // Update Tender Form payment status
    private function updateTenderFormStatus($payment, &$Insertdata, $data)
    {
        $TenderFormBuy = Tender::find($payment->sonodId);
        Log::info('TenderFormBuy: ' . json_encode($TenderFormBuy));  // Log TenderFormBuy for debugging
        $TenderFormBuy->update(['payment_status' => 'Paid']);  // Mark TenderForm as paid
        Log::info('TenderFormBuy updated: ' . json_encode($TenderFormBuy));  // Log after update

        $tenderList = TenderList::find($TenderFormBuy->tender_id);
        $unioun_name = $tenderList->union_name;
        $description = "Your Tender has been successfully submitted.";
        SmsNocHelper::sendSms($description, $TenderFormBuy->mobile, $unioun_name);  // Send SMS notification
    }

    // Update Tender Deposit payment status
    private function updateTenderDepositStatus($payment, &$Insertdata)
    {
        $TenderFormBuy = TanderInvoice::find($payment->sonodId);
        $TenderFormBuy->update(['status' => 'Paid']);  // Update Tender Invoice

        $description = "Your Tender has been successfully submitted.";
        // SmsNocHelper::sendSms($description, $TenderFormBuy->mobile, $unioun_name);  // Send SMS notification
    }

    // Update Sonod Fee payment status
    private function updateSonodFeeStatus($sonod, &$Insertdata)
    {
        // Check for existing renewal
        $existingSonodCount = Sonod::where('renewed_id', $sonod->id)->count();
        if ($existingSonodCount > 0) {
            $existingSonod = Sonod::where('renewed_id', $sonod->id)->first();
            $existingSonod->update(['renewed' => 1]);  // Renew the Sonod
        }

        $sonod->update(['khat' => 'সনদ ফি', 'stutus' => 'Pending', 'payment_status' => 'Paid']);  // Update Sonod

        $description = "Congratulations! Your application $sonod->sonod_Id has been submitted. Wait for approval.";
       $result =  SmsNocHelper::sendSms($description, $sonod->applicant_mobile, $sonod->unioun_name);  // Send SMS notification
       Log::info('SMS sent result: ' . json_encode($result));  // Log the SMS result
    }








    public function CheckPayment(Request $request)
    {
        // Retrieve the transaction ID from the request
        $trnx_id = $request->trnx_id;

        // Find the corresponding payment record
        $myserver = Payment::where(['trxId' => $trnx_id])->first();

        // Check if payment is found
        if (!$myserver) {
            return response()->json(['error' => 'Payment not found'], 404);
        }

        // Get the union from the payment record and fetch union info
        $union = $myserver->union;
        $unioninfo = Uniouninfo::where('short_name_e', $union)->first();

        // Check if union info is found
        if (!$unioninfo) {
            return response()->json(['error' => 'Union info not found'], 404);
        }

        // Retrieve AKPAY_MER_REG_ID and AKPAY_MER_PASS_KEY from union info
        $AKPAY_MER_REG_ID = $unioninfo->AKPAY_MER_REG_ID;

        // Set the API URL and whitelist IP based on the AKPAY_MER_REG_ID
        if ($AKPAY_MER_REG_ID == 'tetulia_test') {
            $Apiurl = 'https://sandbox.ekpay.gov.bd/ekpaypg/v1';
        } else {
            $Apiurl = 'https://pg.ekpay.gov.bd/ekpaypg/v1';
        }

        // Format the transaction date
        $trans_date = date("Y-m-d", strtotime($myserver->date));

        // Initialize cURL session for making the API request
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => $Apiurl . '/get-status', // Use the dynamically set API URL
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode([
                'username' => $AKPAY_MER_REG_ID,
                'trnx_id' => $trnx_id,
                'trans_date' => $trans_date,
            ]),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
            ],
        ]);

        // Execute cURL request and capture response
        $response1 = curl_exec($curl);

        // Close cURL session
        curl_close($curl);

        // Return the data including payment details and Akpay response
        return response()->json([
            'myserver' => $myserver,
            'akpay' => json_decode($response1),
        ]);
    }











    public function createUrl(Request $request)
    {
        // Validate the incoming request to ensure `merchant_id` is provided
        $validated = $request->validate([
            'merchant_id' => 'required|string',
            'trns_info' => 'required|array', // Make sure the transaction information is passed
            'cust_info' => 'required|array', // Customer information should be passed
            'urls' => 'required|array',
            'ipn_url' => 'required',
        ]);

        // Fetch Ekpay credentials based on the provided merchant_id
        $credential = EkpayCredential::where('merchant_id', $validated['merchant_id'])->first();

        if (!$credential) {
            return response()->json(['error' => 'Merchant credentials not found'], 404);
        }

        $AKPAY_MER_REG_ID = $credential->merchant_id;
        $AKPAY_MER_PASS_KEY = $credential->mer_pas_key;
        $Apiurl = $credential->base_url ?? 'https://pg.ekpay.gov.bd/ekpaypg/v1';
        $whitelistip = $credential->whitelistip ?? '203.161.62.45';
        $ipn_uri = $request->ipn_url;



        $trnx_id = $validated['trns_info']['trnx_id'];




        // Set the URL and IP based on the merchant's registration ID
        // if ($AKPAY_MER_REG_ID == 'tetulia_test') {
        //     $Apiurl = 'https://sandbox.ekpay.gov.bd/ekpaypg/v1';
        //     $whitelistip = '1.1.1.1';
        // } else {
        //     $Apiurl = 'https://pg.ekpay.gov.bd/ekpaypg/v1';
        //     $whitelistip = config('WHITE_LIST_IP');
        // }

        // Prepare the post data for EKPay API
        $post = [
            'mer_info' => [
                "mer_reg_id" => $AKPAY_MER_REG_ID,
                "mer_pas_key" => $AKPAY_MER_PASS_KEY,
            ],
            'req_timestamp' => date('Y-m-d H:i:s') . ' GMT+6',
            'feed_uri' => [
                "c_uri" => $validated['urls']['c_uri'],
                "f_uri" => $validated['urls']['f_uri'],
                "s_uri" => $validated['urls']['s_uri'],
            ],
            'cust_info' => $validated['cust_info'],
            'trns_info' => $validated['trns_info'],
            'ipn_info' => [
                "ipn_channel" => "3",
                "ipn_email" => "freelancernishad123@gmail.com",
                "ipn_uri" => "$ipn_uri",
            ],
            'mac_addr' => $whitelistip,
        ];

        // Encode the post data as JSON
        $post = json_encode($post);

        // Log the request data for debugging
        Log::info($post);

        // Initialize cURL to make the request to EKPay API
        $ch = curl_init($Apiurl . '/merchant-api');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        $response = curl_exec($ch);
        curl_close($ch);

        // Log the response from EKPay API
        Log::info($response);

        // Decode the response
        $response = json_decode($response);

        // Check if the response contains a secure token
        if (isset($response->secure_token) && !empty($response->secure_token)) {
            return response()->json("{$Apiurl}?sToken={$response->secure_token}&trnsID={$trnx_id}");
        }

        // Return the error response if no secure token is found
        return response()->json([
            'error' => 'Failed to generate payment URL',
            'details' => $response,
        ], 500);
    }











}
