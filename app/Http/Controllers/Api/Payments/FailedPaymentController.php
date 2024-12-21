<?php

namespace App\Http\Controllers\Api\Payments;

use App\Models\Payment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

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
            $union =  $user->union ?? null;

        }

        $date = $request->input('date');
        $sonod_type = $request->input('sonod_type');

        // Retrieve the pending and failed payments with filters
        $payments = Payment::select('id', 'sonodId', 'union', 'trxId', 'sonod_type', 'date', 'method', 'paid_at')
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
        return response()->json([
            'status' => 'success',
            'data' => $payments
        ]);
    }

}
