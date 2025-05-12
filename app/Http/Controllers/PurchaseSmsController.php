<?php

// app/Http/Controllers/PurchaseSmsController.php

namespace App\Http\Controllers;


use App\Models\Uniouninfo;
use App\Models\PurchaseSms;
use App\Models\BkashPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use App\Models\Payment;  // Assuming you have a Payment model

class PurchaseSmsController extends Controller
{


    public function getSmsPurchaseListByUnion(Request $request)
    {
        // Get the authenticated user's union_name
        $unionName = auth()->user()->unioun;

        // Get the 'per_page' query parameter (default to 10 if not provided)
        $perPage = $request->input('per_page', 10);

        // Fetch the SMS purchases for the user's union with pagination
        $smsPurchases = PurchaseSms::where('union_name', $unionName)
                                    ->orderBy('created_at', 'desc')  // Optional: order by the created_at column
                                    ->paginate($perPage);

        // Return the paginated results
        return response()->json($smsPurchases, 200);
    }


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
            'ipn_url' => url("api/ekpay/smspurchase/ipn"),  // Use dynamic URL for IPN
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



    public function createSmsPurchaseByBkash(Request $request)
{
    // Validate incoming request data
    $validated = $request->validate([
        'sms_amount' => 'required|integer|min:1',  // Validate that sms_amount is an integer and at least 1
        'mobile' => 'required|string',  // Validate that mobile is a string
        'c_uri' => 'required|string',   // Validate that c_uri is a string
        'f_uri' => 'required|string',   // Validate that f_uri is a string
        's_uri' => 'required|string',   // Validate that s_uri is a string
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

    // Call the generatePaymentUrl function to generate the payment URL
    $paymentUrl = generatePaymentUrl($amount, $validated['mobile'], $validated['s_uri']);

    // Check if the URL generation was successful
    if (!$paymentUrl) {
        return response()->json(['error' => 'Failed to create payment URL'], 500);
    }

   $paymentID =  $paymentUrl['paymentID'];
   $smsPurchase->update(['trx_id'=>$paymentID]); // Update the trx_id with the payment ID


    // Return the payment URL along with the SMS purchase data
    return response()->json([
        'message' => 'SMS purchase created successfully via Bkash!',
        'data' => $smsPurchase,
        'payment_url' => $paymentUrl['bkashURL'],  // Return payment URL
    ], 201);
}

    public function smsPurchaseSuccess(Request $request)
    {
        $paymentID = $request->paymentID;

        // 🔍 Find the BkashPayment entry
        $payment = BkashPayment::where('payment_id', $paymentID)->first();

        if (!$payment) {
            return response()->json(['error' => 'Payment not found.'], 404);
        }

        // Check if the payment has already been executed
        if ($payment->status === 'executed') {
            return response()->json(['error' => 'This payment has already been executed.'], 400);
        }

        // Find the corresponding PurchaseSms entry
        $smsPurchase = PurchaseSms::where('trx_id', $payment->payment_id)->first();

        if (!$smsPurchase) {
            return response()->json(['error' => 'SMS purchase not found.'], 404);
        }

        // ✅ Update PurchaseSms status and payment_status to 'approved' and 'paid'
        $smsPurchase->update([
            'status' => 'approved',
            'payment_status' => 'paid',
        ]);

        // ✅ Update BkashPayment status to 'executed'
        $payment->update([
            'status' => 'executed',
        ]);

        // Fetch the union details based on the union name
        $unionDetails = Uniouninfo::where('short_name_e', $smsPurchase->union_name)->first();

        if (!$unionDetails) {
            return response()->json(['error' => 'Union not found'], 404);
        }

        // Add the SMS amount to the union's balance
        $unionDetails->smsBalance += $smsPurchase->sms_amount;

        // Save the updated balance
        $unionDetails->save();

        // Return success message
        return response()->json([
            'message' => 'SMS purchase successfully approved and payment executed!',
            'sms_purchase' => $smsPurchase,
            'bkash_payment' => $payment,
            'union_sms_balance' => $unionDetails->smsBalance, // Return the updated SMS balance
        ], 200);
    }




    public function createMenualSmsPurchase(Request $request)
    {
        // Validate incoming request
        $validated = $request->validate([
            'sms_amount' => 'required|integer|min:1',  // Validate that sms_amount is an integer and at least 1
            'mobile' => 'required|string',
            'bank_trx_id' => 'nullable|string', // Optional bank transaction ID
            'method' => 'nullable|string', // Optional payment method
        ]);

        // Get the authenticated user's union_name
        $unionName = auth()->user()->unioun;

        // Generate a unique transaction ID
        $trxId = 'TRX_' . strtoupper(\Illuminate\Support\Str::random(10));

        // Calculate the total amount based on sms_amount (1 TK per SMS)
        $amount = $validated['sms_amount'] * 1;  // 1 TK per SMS

        // Create the SMS purchase record
        $smsPurchase = PurchaseSms::create([
            'union_name' => $unionName,
            'trx_id' => $trxId,
            'bank_trx_id' => $validated['bank_trx_id'] ?? null, // Set default to null if not provided
            'method' => $validated['method'] ?? null, // Set default to null if not provided
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

        // Return the payment URL along with the SMS purchase data
        return response()->json([
            'message' => 'SMS purchase created successfully!',
            'data' => $smsPurchase,
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

        if ($smsPurchase->payment_status === 'paid') {
            return response()->json(['message' => 'This SMS purchase is already approved'], 200);
        }

        // Fetch the union details based on the union name
        $unionDetails = Uniouninfo::where('short_name_e', $smsPurchase->union_name)->first();

        if (!$unionDetails) {
            return response()->json(['error' => 'Union not found'], 404);
        }

        // Add the SMS amount to the union's balance
        $unionDetails->smsBalance += $smsPurchase->sms_amount;

        // Save the updated balance
        $unionDetails->save();

        // Set SMS purchase status to 'approved' and update payment_status to 'paid'
        $smsPurchase->status = 'approved';
        $smsPurchase->payment_status = 'paid'; // Updating the payment status to 'paid'
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

        // Fetch the union details based on the union name
        $unionDetails = Uniouninfo::where('short_name_e', $smsPurchase->union_name)->first();

        if (!$unionDetails) {
            return response()->json(['error' => 'Union not found'], 404);
        }

        // If the SMS purchase was approved, deduct the sms_amount from smsBalance
        if ($smsPurchase->status == 'approved') {
            $unionDetails->smsBalance -= $smsPurchase->sms_amount;
            $unionDetails->save(); // Save the updated smsBalance
        }

        // Reject the SMS purchase (if it's pending, no change to smsBalance)
        $smsPurchase->status = 'rejected';
        $smsPurchase->save();

        return response()->json([
            'message' => 'SMS purchase rejected successfully!',
            'data' => $smsPurchase
        ], 200);
    }








    public function ipnCallbackForSmsPurchase(Request $request)
    {
        // Log the incoming IPN request data for debugging
        $data = $request->all();
        Log::info('Received IPN data: ' . json_encode($data));

        // Validate that the data is not empty
        if (empty($data)) {
            Log::error('IPN data is empty');
            return response()->json(['error' => 'IPN data is empty'], 400);
        }

        // Validate that required keys exist in the data
        $requiredKeys = ['msg_code', 'trnx_info', 'cust_info'];
        foreach ($requiredKeys as $key) {
            if (!isset($data[$key])) {
                Log::error('Missing key in IPN data: ' . $key);
                return response()->json(['error' => 'Missing key: ' . $key], 400);
            }
        }

        // Fetch the transaction ID and related SmsPurchase record
        $trnx_id = $data['trnx_info']['mer_trnx_id'];  // Transaction ID
        $smsPurchase = PurchaseSms::where('trx_id', $trnx_id)->first();

        if (!$smsPurchase) {
            Log::error('SmsPurchase not found for trx_id: ' . $trnx_id);
            return response()->json(['error' => 'SmsPurchase not found'], 404);
        }

        // Check the msg_code for payment status
        $Insertdata = [];
        if ($data['msg_code'] == '1020') {
            // Payment was successful
            $Insertdata = [
                'status' => 'approved',
                'payment_status' => 'paid',  // Update payment status to 'paid'
            ];

            // Update the SmsPurchase record with the approved status
            $smsPurchase->update($Insertdata);

            // Fetch the union details and update the sms balance
            $unionName = $smsPurchase->union_name;
            $unionDetails = unionname($unionName);

            if ($unionDetails) {
                $newBalance = $unionDetails->smsBalance + $smsPurchase->sms_amount;
                $unionDetails->update(['smsBalance' => $newBalance]);
            }

        } else {
            // Payment failed or is not successful
            $smsPurchase->update([
                'status' => 'rejected',
                'payment_status' => 'failed',  // Mark as failed
            ]);
        }

        // Log the response
        $Insertdata['ipnResponse'] = $data;

        return response()->json(['message' => 'IPN processed successfully'], 200);
    }




}
