<?php

namespace App\Http\Controllers\Api\User\MaintanceFee;

use App\Models\Uniouninfo;
use App\Models\BkashPayment;
use App\Models\MaintanceFee;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\View;


        use NumberToWords\NumberToWords;

class MaintanceFeeController extends Controller
{
    public function index()
    {
        $userUnion = Auth::user()->unioun;
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
    $amount = ($unionInfo->maintance_fee ?? 0);
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
        // 🔍 Find the MaintanceFee entry by trx_id
        $fee = MaintanceFee::where('trx_id', $payment->payment_id)->first();


        if ($payment->status === 'executed') {
            return response()->json(['error' => 'This payment has already been executed.', 'maintance_fee' => $fee,], 400);
        }



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


      public function downloadInvoice($id)
    {
        $fee = MaintanceFee::findOrFail($id);


        $numberToWords = new NumberToWords();
$numberTransformer = $numberToWords->getNumberTransformer('en');

$amount = $fee->amount + $fee->transaction_fee;
    $inWords = strtoupper($numberTransformer->toWords((int) $amount)) . ' TAKA ONLY';





        $htmlContent = View::make('pdf.maintance_invoice', compact('fee','inWords'))->render();

        $header = '
<table width="100%" style="border-bottom: 2px solid #003366; font-family: bangla, sans-serif;">
    <tr>
        <td style="width: 60px; padding: 5px;">
            <img src="https://softwebsys.com/fav.png" height="50" alt="logo">
        </td>
        <td style="padding-left: 10px; vertical-align: top; font-size: 12px;">
            <div style="font-size: 18px; font-weight: bold; color: #003366;">Softweb System Solution</div>
            <div><em>Professional and reliable web application development company</em></div>
            <div>Panchagarh, Bangladesh</div>
            <div>cell: +8801909756552 , +88 01713 760596 </div>
            <div>e-mail: info@softwebsys.com, web: www.softwebsys.com</div>
        </td>
    </tr>
</table>


<table class="details-table">
    <tr>
        <td><strong>Reference No.:</strong> ' . $fee->union . '/' . now()->year . '/' . \Carbon\Carbon::parse($fee->payment_date)->format('m') . '</td>
        <td style="text-align:right;"><strong>Date:</strong> ' . \Carbon\Carbon::parse($fee->payment_date)->format('d/m/Y') . '</td>
    </tr>
</table>


';



       $footer = '




<br/>
<br/>
<br/>


<table width="100%" style="background-color: #e8f0fa; border-top: 1px solid #ccc; padding: 10px; padding">
    <tr>
        <td width="15%" style="text-align: left; padding-left: 10px;">
            <img src="' . base64("softwebsys.com-qrcode.png") . '" height="60" alt="QR">
        </td>
        <td style="font-size: 11px; color: #003366;">
            <span style="font-size: 12px;"><strong>For questions concerning this invoice, please contact with us.</strong></span><br>
            Softweb System Solution, Debiganj, Panchagarh.<br>
            cell: +8801909756552, +88 01713 760596<br>
            e-mail: <a href="mailto:info@softwebsys.com" style="color:#003366; text-decoration:none;">info@softwebsys.com</a>,
            web: <a href="https://www.softwebsys.com" style="color:#003366; text-decoration:none;">www.softwebsys.com</a>
        </td>
    </tr>
</table>




';

        $filename = 'invoice_' . $fee->id . '.pdf';
        $font_family = 'bangla'; // or 'nikosh', 'solaimanlipi', etc. if supported
        $sonod_logo = public_path('images/logo.png'); // Replace with your actual logo path










        return generatePdf($htmlContent, $header, $footer, $filename, "A4", $font_family, $sonod_logo);
    }






}
