<?php
namespace App\Http\Controllers\Api\Admin\Reports;

use App\Models\Sonod;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Devfaysal\BangladeshGeocode\Models\Upazila;
use Devfaysal\BangladeshGeocode\Models\District;
use Devfaysal\BangladeshGeocode\Models\Division;

class ReportsController extends Controller
{
    // Main function that decides which sub-function to call
    public function getReports(Request $request)
    {
        // Validate input
        $validator = Validator::make($request->all(), [
            'union_name' => 'nullable|string', // No need for 'required' since we'll handle different cases
            'sonod_name' => 'nullable|string',
            'division_name' => 'nullable|string',
            'district_name' => 'nullable|string',
            'upazila_name' => 'nullable|string',
            'auth' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }


        if($request->auth){

            $admin = auth('admin')->user();

            $unionName = $admin->union_name ?? null;
            $sonodName = $admin->sonod_name ?? null;
            $divisionName = $admin->division_name ?? null;
            $districtName = $admin->district_name ?? null;
            $upazilaName = $admin->upazila_name ?? null;

        }else{
            $unionName = $request->input('union_name');
            $sonodName = $request->input('sonod_name');
            $divisionName = $request->input('division_name');
            $districtName = $request->input('district_name');
            $upazilaName = $request->input('upazila_name');
        }

        // Extract input values


        // If a specific union_name is provided, use it to filter
        if ($unionName) {
            return $this->getReportsByUnion([$unionName], $sonodName);
        }

        // If upazila is provided, fetch unions by upazila and call the report generation
        if ($upazilaName) {
            return $this->getReportsByUpazila($upazilaName, $sonodName);
        }

        // If a district is provided, fetch unions by district and call the report generation
        if ($districtName) {
            return $this->getReportsByDistrict($districtName, $sonodName);
        }

        // If a division is provided, fetch districts by division and call the report generation
        if ($divisionName) {
            return $this->getReportsByDivision($divisionName, $sonodName);
        }





        // If no specific location is provided, return a bad request response
        return response()->json(['error' => 'At least one location filter (union_name, district_name, upazila_name, division_name) is required'], 400);
    }

    // Function to get reports by Union
    private function getReportsByUnion(array $unionNames, $sonodName = null)
    {

        // Log::info($unionNames);
        // Define base queries with union_name filter
        $sonodQuery = Sonod::whereIn('unioun_name', $unionNames)
            ->selectRaw("
                sonod_name,
                COUNT(CASE WHEN stutus = 'Pending' THEN 1 END) as pending_count,
                COUNT(CASE WHEN stutus = 'approved' THEN 1 END) as approved_count,
                COUNT(CASE WHEN stutus = 'cancel' THEN 1 END) as cancel_count
            ")
            ->groupBy('sonod_name');

        $paymentQuery = Payment::whereIn('union', $unionNames)->where('status', 'Paid')
            ->selectRaw("
                sonod_type,
                COUNT(*) as total_payments,
                SUM(amount) as total_amount
            ")
            ->groupBy('sonod_type');

        // Apply optional sonod_name filter
        if ($sonodName) {
            $sonodQuery->where('sonod_name', $sonodName);
            $paymentQuery->where('sonod_type', $sonodName);
        }

        // Fetch results
        $sonodReports = $sonodQuery->get();
        $paymentReports = $paymentQuery->get();

        // Calculate totals
        $totalPending = $sonodReports->sum('pending_count');
        $totalApproved = $sonodReports->sum('approved_count');
        $totalCancel = $sonodReports->sum('cancel_count');
        $totalPayments = $paymentReports->sum('total_payments');
        $totalAmount = $paymentReports->sum('total_amount');

        // Format response
        return response()->json([
            'sonod_reports' => $sonodReports,
            'payment_reports' => $paymentReports,
            'totals' => [
                'total_pending' => $totalPending,
                'total_approved' => $totalApproved,
                'total_cancel' => $totalCancel,
                'total_payments' => $totalPayments,
                'total_amount' => $totalAmount,
            ],
        ], 200);
    }

   // Function to get reports by District
    private function getReportsByDistrict($district, $sonodName = null)
    {
        // Get all union names related to the given district by traversing through Upazila
        $districtModel = District::where('name', $district)->firstOrFail();

        // Get all the unions from the upazilas of this district
        $unionNames = [];
        foreach ($districtModel->upazilas as $upazila) {
            foreach ($upazila->unions as $union) {
                $unionNames[] = str_replace(' ', '', strtolower($union->name));
            }
        }

        // Remove duplicates
        $unionNames = array_unique($unionNames);

        return $this->getReportsByUnion($unionNames, $sonodName);
    }

    // Function to get reports by Division
    private function getReportsByDivision($division, $sonodName = null)
    {
        // Get all union names related to the given division by traversing through District and Upazila
        $divisionModel = Division::where('name', $division)->firstOrFail();


        // Get all unions by traversing through districts and upazilas
        $unionNames = [];
        foreach ($divisionModel->districts as $district) {

            foreach ($district->upazilas as $upazila) {

                foreach ($upazila->unions as $union) {
                    $unionNames[] = str_replace(' ', '', strtolower($union->name));
                }
            }
        }


        // Remove duplicates
        $unionNames = array_unique($unionNames);

        return $this->getReportsByUnion($unionNames, $sonodName);
    }

    // Function to get reports by Upazila
    private function getReportsByUpazila($upazila, $sonodName = null)
    {
        // Get all union names related to the given upazila
        $upazilaModel = Upazila::where('name', $upazila)->firstOrFail();

        // Get all the unions for this upazila
        $unionNames = [];
        foreach ($upazilaModel->unions as $union) {
            $unionNames[] = str_replace(' ', '', strtolower($union->name));
        }

        // Remove duplicates
        $unionNames = array_unique($unionNames);

        return $this->getReportsByUnion($unionNames, $sonodName);
    }

}
