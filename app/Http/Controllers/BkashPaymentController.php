<?php

namespace App\Http\Controllers;

use App\Models\BkashPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class BkashPaymentController extends Controller
{
    private $baseUrl;
    private $appKey;
    private $appSecret;
    private $username;
    private $password;

    public function __construct()
    {
        $this->baseUrl = env('BKASH_BASE_URL');
        $this->appKey = env('BKASH_APP_KEY');
        $this->appSecret = env('BKASH_APP_SECRET');
        $this->username = env('BKASH_USERNAME');
        $this->password = env('BKASH_PASSWORD');
    }

    public function grantToken()
    {
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'username' => $this->username,
            'password' => $this->password
        ])->post("$this->baseUrl/token/grant", [
            'app_key' => $this->appKey,
            'app_secret' => $this->appSecret
        ]);

        return $response->json();
    }

    public function createPayment(Request $request)
    {
        $token = $request->bearerToken();

        $response = Http::withToken($token)
            ->withHeaders(['Content-Type' => 'application/json', 'app_key' => $this->appKey])
            ->post("$this->baseUrl/create", [
                "mode" => "0011",
                "payerReference" => "01700000000",
                "callbackURL" => "https://yourdomain.com/callback",
                "amount" => $request->amount,
                "currency" => "BDT",
                "intent" => "sale",
                "merchantInvoiceNumber" => uniqid()
            ]);

        return $response->json();
    }


   public function generatePaymentUrl(Request $request)
{
    // Step 1: Get token
    $tokenResponse = Http::withHeaders([
        'Content-Type' => 'application/json',
        'username' => $this->username,
        'password' => $this->password
    ])->post("$this->baseUrl/token/grant", [
        'app_key' => $this->appKey,
        'app_secret' => $this->appSecret
    ]);

    if (!$tokenResponse->successful()) {
        return response()->json(['error' => 'Failed to get token', 'details' => $tokenResponse->body()], 500);
    }

    $token = $tokenResponse->json()['id_token'];

    // Step 2: Create payment
    $invoice = uniqid('INV-');
    $paymentResponse = Http::withToken($token)
        ->withHeaders([
            'Content-Type' => 'application/json',
            'X-APP-Key' => $this->appKey
        ])
        ->post("$this->baseUrl/create", [
            "mode" => "0011",
            "payerReference" => $request->payerReference ?? "01700000000",
            "callbackURL" => $request->callbackURL ?? "https://yourdomain.com/callback",
            "amount" => $request->amount,
            "currency" => "BDT",
            "intent" => "sale",
            "merchantInvoiceNumber" => $invoice
        ]);

    if (!$paymentResponse->successful()) {
        return response()->json(['error' => 'Failed to create payment', 'details' => $paymentResponse->body()], 500);
    }

    $paymentData = $paymentResponse->json();

    // ✅ Save to database
    BkashPayment::create([
        'id_token' => $token,
        'payment_id' => $paymentData['paymentID'],
        'amount' => $request->amount,
        'invoice' => $invoice,
        'status' => 'initiated'
    ]);

    return response()->json($paymentData);
}




public function executePayment(Request $request)
{
    $paymentID = $request->paymentID;

    // 🔍 Find the BkashPayment entry
    $payment = BkashPayment::where('payment_id', $paymentID)->first();

    if (!$payment) {
        return response()->json(['error' => 'Payment not found.'], 404);
    }

    $token = $payment->id_token;

    // 🧾 Execute the payment
    $response = Http::withToken($token)
        ->withHeaders([
            'Content-Type' => 'application/json',
            'X-APP-Key' => $this->appKey
        ])
        ->post("$this->baseUrl/execute", [
            'paymentID' => $paymentID
        ]);

    // ✅ Optionally update payment status if successful
    if ($response->successful()) {
        $payment->update(['status' => 'executed']);
    } else {
        $payment->update(['status' => 'failed']);
    }

    return $response->json();
}


    public function queryPayment($paymentID)
    {
        $token = request()->bearerToken();

        $response = Http::withToken($token)
            ->withHeaders(['Content-Type' => 'application/json', 'X-APP-Key' => $this->appKey])
            ->get("$this->baseUrl/payment/status", [
                'paymentID' => $paymentID
            ]);

        return $response->json();
    }

    public function refundPayment(Request $request)
    {
        $token = $request->bearerToken();

        $response = Http::withToken($token)
            ->withHeaders(['Content-Type' => 'application/json', 'app_key' => $this->appKey])
            ->post("$this->baseUrl/payment/refund", [
                'paymentID' => $request->paymentID,
                'amount' => $request->amount,
                'trxID' => $request->trxID,
                'sku' => 'SKU001',
                'reason' => $request->reason ?? 'Customer Refund'
            ]);

        return $response->json();
    }
}
