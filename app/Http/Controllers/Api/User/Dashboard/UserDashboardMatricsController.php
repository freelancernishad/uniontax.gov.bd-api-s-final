<?php

namespace App\Http\Controllers\Api\User\Dashboard;

use App\Models\Sonod;
use App\Models\Payment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Uniouninfo;
use Illuminate\Support\Facades\Auth;

class UserDashboardMatricsController extends Controller
{
    /**
     * Get Sonod metrics for the user dashboard.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSonodMetrics(Request $request)
    {
        // Fetch union name from the authenticated user
        $unionName = Auth::user()->unioun;
        // return response()->json($unionName);

        $smsBalance = Uniouninfo::select('short_name_e','smsBalance')->where('short_name_e',$unionName)->first()->smsBalance;
        // Fetch metrics from the database for the specific union
        $totalSonod = Sonod::where('unioun_name', $unionName)->count();
        $pendingSonod = Sonod::where('unioun_name', $unionName)->where('stutus', 'Pending')->count();
        $approvedSonod = Sonod::where('unioun_name', $unionName)->where('stutus', 'approved')->count();
        $cancelSonod = Sonod::where('unioun_name', $unionName)->where('stutus', 'cancel')->count();

        // Calculate total payment amount with 'Paid' status for the union
        $totalRevenue = Payment::where('union', $unionName)
            ->where('status', 'Paid')
            ->sum('amount');

        // Prepare response data
        $data = [
            'totalSonod' => (int) $totalSonod,
            'pendingSonod' => (int) $pendingSonod,
            'approvedSonod' => (int) $approvedSonod,
            'cancelSonod' => (int) $cancelSonod,
            'totalRevenue' => (int) $totalRevenue,
            'sms_balance' => (int) $smsBalance,
        ];

        // Return response
        return response()->json($data, 200);
    }

}
