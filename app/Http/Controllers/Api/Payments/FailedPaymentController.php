<?php

namespace App\Http\Controllers\Api\Payments;

use App\Models\Sonod;
use App\Models\Payment;
use Illuminate\Http\Request;
use App\Models\PaymentFailed;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class FailedPaymentController extends Controller
{
    /**
     * Retrieve a list of pending and failed payments.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {


        if (auth('admin')->check()) {
            $union =  $request->input('union');
        } elseif (auth('user')->check()) {
            $user = auth()->user();
            $union =  $user->unioun ?? null;

        }

        $date = $request->input('date');

        $sonod_type = $request->input('sonod_type');
        if($sonod_type=='all'){
            $sonod_type = '';
        }

        // Retrieve the pending and failed payments with filters
        $payments = Payment::select('id', 'sonodId', 'union', 'trxId', 'sonod_type', 'date', 'method')
            ->when($union, function ($query, $union) {
                return $query->where('union', $union);
            })
            ->when($date, function ($query, $date) {
                return $query->whereDate('date', $date);
            })

            ->when($sonod_type, function ($query, $sonod_type) {
                return $query->where('sonod_type', $sonod_type);
            })


            ->where(function ($query) {
                $query->pending()->orWhere(function ($q) {
                    $q->failed();
                });
            })
            ->get();

        // Return the results as JSON
        return response()->json($payments);
    }


    /**
     * Create a new failed payment record.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function failed_payment_record_store(Request $request)
{
    // Define validation rules
    $rules = [
        'certificate' => 'required|string|max:255',
        'payment_method' => 'required|string|max:255',
        'account_number' => 'required|string|max:255',
        'amount' => 'required|numeric',
        'bank_transaction_id' => 'required|string|max:255',
        'sonod_id' => 'required|string|max:255',
        'details' => 'nullable|string',
        'transId' => 'required|string|max:255',
    ];

    // Create a validator instance
    $validator = Validator::make($request->all(), $rules);

    // Check if validation fails
    if ($validator->fails()) {
        return response()->json([
            'error' => 'Validation failed',
            'messages' => $validator->errors(),
        ], 422); // Return validation errors with a 422 status code
    }

    // Get validated data
    $validatedData = $validator->validated();

    // Set default status if not provided
    $validatedData['status'] = "Pending";

    // Fetch union_name from Sonod table
    $union_name = Sonod::select('unioun_name')->find($request->sonod_id)->unioun_name;
    $validatedData['union_name'] = $union_name;

    // Add current datetime programmatically
    $validatedData['datetime'] = now(); // Use Laravel's `now()` helper to get the current timestamp

    // Create the PaymentFailed record
    $paymentFailed = PaymentFailed::create($validatedData);

    // Return the created record as JSON
    return response()->json($paymentFailed, 201);
}




}
