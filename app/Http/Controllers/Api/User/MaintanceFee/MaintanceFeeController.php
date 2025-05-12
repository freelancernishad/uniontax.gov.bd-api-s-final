<?php

namespace App\Http\Controllers\Api\User\MaintanceFee;

use App\Models\Uniouninfo;
use App\Models\BkashPayment;
use App\Models\MaintanceFee;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class MaintanceFeeController extends Controller
{
    public function index()
    {
        $userUnion = Auth::user()->union;
        $fees = MaintanceFee::where('union', $userUnion)->get();

        return response()->json($fees);
    }

public function store(Request $request)
{
    $validated = $request->validate([
        's_uri' => 'required|string',
    ]);

    $userUnion = Auth::user()->unioun;

    // Fetch union information
    $unionInfo = Uniouninfo::where('short_name_e', $userUnion)->firstOrFail();
    $amount = $unionInfo->maintance_fee ?? 1;
    $type = $unionInfo->maintance_fee_type ?? 'monthly';
    $mobile = $unionInfo->chairman_phone ?? "01700000000"; // fallback

    // Determine the period
    if ($type === 'monthly') {
        $period = now()->format('Y-m'); // e.g., 2025-05
    } else {

        $period = CurrentOrthoBochor(); // e.g., 2025-2026
    }

    // Generate payment URL
    $paymentUrl = generatePaymentUrl($amount, $mobile, $validated['s_uri']);

    if (
        !$paymentUrl ||
        !isset($paymentUrl['paymentID']) ||
        !isset($paymentUrl['bkashURL'])
    ) {
        return response()->json(['error' => 'Failed to create payment URL'], 500);
    }

    // Store fee entry
    $fee = MaintanceFee::create([
        'union' => $userUnion,
        'amount' => $amount,
        'type' => $type,
        'period' => $period,
        'status' => 'Pending',
        'trx_id' => $paymentUrl['paymentID'],
    ]);

    return response()->json([
        'message' => 'Payment initiated successfully',
        'payment_url' => $paymentUrl['bkashURL'],
        'payment_id' => $paymentUrl['paymentID'],
        'data' => $fee,
    ], 201);
}



    public function maintanceFeeExecute(Request $request)
    {
        $baseUrl = env('BKASH_BASE_URL');
        $appKey = env('BKASH_APP_KEY');
        $paymentID = $request->paymentID;

        // 🔍 Find the BkashPayment entry
        $payment = BkashPayment::where('payment_id', $paymentID)->first();

        if (!$payment) {
            return response()->json(['error' => 'Payment not found.'], 404);
        }

        if ($payment->status === 'executed') {
            return response()->json(['error' => 'This payment has already been executed.'], 400);
        }

        // 🔍 Find the MaintanceFee entry by trx_id
        $fee = MaintanceFee::where('trx_id', $payment->payment_id)->first();

        if (!$fee) {
            return response()->json(['error' => 'Maintenance fee record not found.'], 404);
        }

        // 🔒 Get the payment token
        $token = $payment->id_token;

        // 🧾 Execute the payment
        $response = Http::withToken($token)
            ->withHeaders([
                'Content-Type' => 'application/json',
                'X-APP-Key' => $appKey
            ])
            ->post("$baseUrl/execute", [
                'paymentID' => $paymentID
            ]);

        $responseData = $response->json();
        $transactionStatus = $responseData['transactionStatus'] ?? null;

        if ($response->successful() && $transactionStatus === 'Completed') {
            // ✅ Payment success
            $payment->update(['status' => 'executed']);
            $fee->update([
                'status' => 'paid',
                'payment_date' => now(),
            ]);

            return response()->json([
                'message' => 'Maintenance fee successfully paid!',
                'maintance_fee' => $fee,
                'bkash_payment' => $payment,
            ], 200);

        } elseif ($transactionStatus === 'Cancelled') {
            $payment->update(['status' => 'cancelled']);
            $fee->update([
                'status' => 'cancelled',
            ]);

            return response()->json(['error' => 'Payment was cancelled by the user.'], 400);

        } elseif ($transactionStatus === 'Failed') {
            $payment->update(['status' => 'failed']);
            $fee->update([
                'status' => 'failed',
            ]);

            return response()->json(['error' => 'Payment failed.'], 400);

        } else {
            $payment->update(['status' => 'failed']);
            $fee->update([
                'status' => 'failed',
            ]);

            return response()->json([
                'error' => 'Payment execution failed.',
                'details' => $responseData,
            ], 500);
        }
    }





    public function show($id)
    {
        $fee = MaintanceFee::where('id', $id)
            ->where('union', Auth::user()->union)
            ->firstOrFail();

        return response()->json($fee);
    }
}
