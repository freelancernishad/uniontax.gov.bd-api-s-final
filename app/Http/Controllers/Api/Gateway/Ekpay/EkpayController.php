<?php

namespace App\Http\Controllers\Api\Gateway\Ekpay;

use App\Models\Sonod;
use App\Models\Tender;
use App\Models\Payment;
use App\Models\TenderList;
use App\Models\Uniouninfo;
use Illuminate\Http\Request;
use App\Helpers\SmsNocHelper;
use App\Models\HoldingBokeya;
use App\Models\TanderInvoice;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class EkpayController extends Controller
{
    public function ipn(Request $request)
    {
        // Log the incoming request data for debugging
        $data = $request->all();
        Log::info('Received IPN data: ' . json_encode($data));

        // Find the Sonod by the customer ID
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
        // $Insertdata['ipnResponse'] = json_encode($data);
        $Insertdata['ipnResponse'] = $data;

        // Update the payment record with the new status and response data
        return $payment->update($Insertdata);
    }

    // Update HoldingTax payment status
    private function updateHoldingTaxStatus($payment, &$Insertdata)
    {
        $hosdingBokeya = HoldingBokeya::find($payment->sonodId);
        $hosdingBokeya->update(['status' => 'Paid', 'payYear' => date('Y'), 'payOB' => COB(1)]);  // Update Holding Bokeya
    }

    // Update Tender Form payment status
    private function updateTenderFormStatus($payment, &$Insertdata, $data)
    {
        $TenderFormBuy = Tender::find($payment->sonodId);
        $TenderFormBuy->update(['payment_status' => 'Paid']);  // Mark TenderForm as paid

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
        SmsNocHelper::sendSms($description, $sonod->applicant_mobile, $sonod->unioun_name);  // Send SMS notification
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




}
