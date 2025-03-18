<?php

// app/Http/Controllers/PurchaseSmsController.php

namespace App\Http\Controllers;


use App\Models\PurchaseSms;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use App\Models\Payment;  // Assuming you have a Payment model

class PurchaseSmsController extends Controller
{
    // Function to create the SMS purchase record
    public function createSmsPurchase(Request $request)
    {
        // Validate incoming request
        $validated = $request->validate([
            'sms_amount' => 'required|integer|min:1',  // Validate that sms_amount is an integer and at least 1
            'mobile' => 'required|string',
            'c_uri' => 'required|string',
            'f_uri' => 'required|string',
            's_uri' => 'required|string',
        ]);

        // Get the authenticated user's union_name
        $unionName = auth()->user()->unioun;

        // Generate a unique transaction ID
        $trxId = 'TRX_' . strtoupper(\Illuminate\Support\Str::random(10));  // You can use your custom method to generate a TRX ID

        // Calculate the total amount based on sms_amount (1 TK per SMS)
        $amount = $validated['sms_amount'] * 1;  // 1 TK per SMS

        // Create the SMS purchase record
        $smsPurchase = PurchaseSms::create([
            'union_name' => $unionName,
            'trx_id' => $trxId,
            'amount' => $amount,
            'mobile' => $validated['mobile'],
            'sms_amount' => $validated['sms_amount'],
            'payment_status' => 'pending',  // Initially set to 'pending'
            'status' => 'pending',  // Initially set to 'pending'
        ]);

        // Fetch union details using the union name
        $unionDetails = unionname($unionName);
        if (!$unionDetails || !$unionDetails->AKPAY_MER_REG_ID) {
            return response()->json(['error' => 'Invalid union details'], 400);
        }

        // Prepare data for Ekpay API
        $urlData = [
            'merchant_id' => $unionDetails->AKPAY_MER_REG_ID,  // Merchant ID provided by Ekpay
            'trnx_id' => $trxId,
            'trns_info' => [
                'ord_det' => 'SMS Purchase',  // Order details
                'ord_id' => (string) $smsPurchase->id,  // Using the purchase ID as order ID
                'trnx_amt' => $amount,
                'trnx_currency' => 'BDT',  // Currency
                'trnx_id' => $trxId,
            ],
            'cust_info' => [
                'cust_email' => '',  // You can leave it blank or add optional email
                'cust_id' => (string) $smsPurchase->id,  // Using the purchase ID as customer ID
                'cust_mail_addr' => 'Address',  // Optional address
                'cust_mobo_no' => $validated['mobile'],  // Mobile number
                'cust_name' => 'Customer Name',  // Can be dynamically set if needed
            ],
            'urls' => [
                'c_uri' => $validated['c_uri'],
                'f_uri' => $validated['f_uri'],
                's_uri' => $validated['s_uri'],
            ],
            'ipn_url' => "http://localhost:8000/api/call/ipn",  // Use dynamic URL for IPN
        ];

        // Call Ekpay's createUrl function to generate the payment URL
        $paymentUrl = $this->createUrl($urlData);

        // Check if the URL generation was successful
        if (!$paymentUrl) {
            return response()->json(['error' => 'Failed to create payment URL'], 500);
        }

        // Return the payment URL along with the SMS purchase data
        return response()->json([
            'message' => 'SMS purchase created successfully!',
            'data' => $smsPurchase,
            'payment_url' => $paymentUrl,  // Return payment URL
        ], 201);
    }

    // Function to create the Ekpay payment URL
    private function createUrl($data)
    {
        // Assuming you use Ekpay API for creating the URL
        $apiPayload = $data;  // The data from the request

        // Call Ekpay API to get the payment URL
        try {
            $response = Http::post('https://api.uniontax.gov.bd/api/ekpay/create-url', $apiPayload);

            // Check if the response is successful
            if ($response->successful()) {
                // Assuming the response contains the payment_url field
                return $response->json()['data'];  // Adjust based on the actual API response
            }

            return false;  // If API call fails
        } catch (\Exception $e) {
            // Log the error in case the request to Ekpay fails
            Log::error('Ekpay API Error: ' . $e->getMessage());
            return false;
        }
    }

    // Function to approve the SMS purchase (admin function)
    public function approveSmsPurchase($trx_id)
    {
        $smsPurchase = PurchaseSms::where('trx_id', $trx_id)->first();

        if (!$smsPurchase) {
            return response()->json(['error' => 'SMS purchase not found'], 404);
        }

        if ($smsPurchase->payment_status != 'paid') {
            return response()->json(['error' => 'Payment is not completed yet'], 400);
        }

        // Set status to 'approved'
        $smsPurchase->status = 'approved';
        $smsPurchase->save();

        return response()->json([
            'message' => 'SMS purchase approved successfully!',
            'data' => $smsPurchase
        ], 200);
    }

    // Function to reject the SMS purchase (admin function)
    public function rejectSmsPurchase($trx_id)
    {
        $smsPurchase = PurchaseSms::where('trx_id', $trx_id)->first();

        if (!$smsPurchase) {
            return response()->json(['error' => 'SMS purchase not found'], 404);
        }

        // Reject the purchase
        $smsPurchase->status = 'rejected';
        $smsPurchase->save();

        return response()->json([
            'message' => 'SMS purchase rejected successfully!',
            'data' => $smsPurchase
        ], 200);
    }
}
