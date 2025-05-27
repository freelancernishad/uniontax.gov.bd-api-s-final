<?php
namespace App\Http\Controllers\Api\Admin\Reports;

use App\Models\Sonod;
use App\Models\Payment;
use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Devfaysal\BangladeshGeocode\Models\Upazila;
use Devfaysal\BangladeshGeocode\Models\District;
use Devfaysal\BangladeshGeocode\Models\Division;

class ReportsController extends Controller
{


    function downloadReports(Request $request) {
        $unionName = $request->input('union_name');
        $sonodName = $request->input('sonod_name');
        $divisionName = $request->input('division_name');
        $districtName = $request->input('district_name');
        $upazilaName = $request->input('upazila_name');
        $detials = $request->input('detials');



        $sonod_name = '';
        if($detials){
            $sonod_name = $sonodName . ' এর';
        }


        // Generate the title dynamically
        if (!empty($unionName)) {
            $reportTitle = UnionenBnName($unionName) . " ইউনিয়নের $sonod_name প্রতিবেদন";
        } elseif (!empty($upazilaName)) {
            $reportTitle = addressEnToBn($upazilaName,"upazila") . " উপজেলার সকল ইউনিয়নের $sonod_name প্রতিবেদন";
        } elseif (!empty($districtName)) {
            $reportTitle = addressEnToBn($districtName,"district") . " জেলার সকল ইউনিয়নের $sonod_name প্রতিবেদন";
        } elseif (!empty($divisionName)) {
            $reportTitle = addressEnToBn($divisionName,"division") . " বিভাগের সকল ইউনিয়নের $sonod_name প্রতিবেদন";
        } else {
            $reportTitle = " $sonod_name প্রতিবেদন";
        }

        // If a specific union_name is provided, use it to filter
        if ($unionName) {
            $data =  $this->getReportsByUnion([$unionName], $sonodName,$detials);
            return $this->genratePdf($data,$reportTitle,$detials);
        }

        // If upazila is provided, fetch unions by upazila and call the report generation
        if ($upazilaName) {
            $data =  $this->getReportsByUpazila($upazilaName, $sonodName,$detials);
            return $this->genratePdf($data,$reportTitle,$detials);
        }

        // If a district is provided, fetch unions by district and call the report generation
        if ($districtName) {
            $data =  $this->getReportsByDistrict($districtName, $sonodName,$detials);
            return $this->genratePdf($data,$reportTitle,$detials);
        }

        // If a division is provided, fetch districts by division and call the report generation
        if ($divisionName) {
            $data =  $this->getReportsByDivision($divisionName, $sonodName,$detials);
            return $this->genratePdf($data,$reportTitle,$detials);
        }


    }

    private function genratePdf($data,$reportTitle,$detials=null) {

        $is_union = isUnion();

        if($detials){
            $htmlView = view('Reports.DownloadDetailsReports', compact('data','reportTitle','is_union'))->render();
        }else{
            $htmlView = view('Reports.DownloadReports', compact('data','reportTitle','is_union'))->render();
        }


        $header = null; // Add HTML for header if required
        $footer = null; // Add HTML for footer if required
        $filename = "Reports_" . now()->format('Ymd_His') . ".pdf";
        return generatePdf($htmlView, $header, $footer, $filename);
    }



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
            'auth' => 'nullable',
            'detials' => 'nullable',
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

        $detials = $request->detials;
        // Extract input values


        // If a specific union_name is provided, use it to filter
        if ($unionName) {
                $datas =  $this->getReportsByUnion([$unionName], $sonodName,$detials);
                return response()->json($datas);

        }

        // If upazila is provided, fetch unions by upazila and call the report generation
        if ($upazilaName) {

                $datas =  $this->getReportsByUpazila($upazilaName, $sonodName,$detials);
                return response()->json($datas);

        }

        // If a district is provided, fetch unions by district and call the report generation
        if ($districtName) {

                $datas =  $this->getReportsByDistrict($districtName, $sonodName,$detials);
                return response()->json($datas);

        }

        // If a division is provided, fetch districts by division and call the report generation
        if ($divisionName) {

                $datas =  $this->getReportsByDivision($divisionName, $sonodName,$detials);
                return response()->json($datas);

        }

        // If no specific location is provided, return a bad request response
        return response()->json(['error' => 'At least one location filter (union_name, district_name, upazila_name, division_name) is required'], 400);
    }

    // Function to get reports by Union
    public function getReportsByUnion(array $unionNames, $sonodName = null, $detials = null)
    {
        // Log::info($unionNames);
        if ($detials) {
            // If detials is true, fetch union-level details
            $detailedSonodReports = Sonod::whereIn('unioun_name', $unionNames)
                ->when($sonodName, function ($query) use ($sonodName) {
                    $query->where('sonod_name', $sonodName);
                })
                ->selectRaw("
                    unioun_name,
                    sonod_name,
                    COUNT(CASE WHEN stutus = 'Pending' THEN 1 END) as pending_count,
                    COUNT(CASE WHEN stutus = 'approved' THEN 1 END) as approved_count,
                    COUNT(CASE WHEN stutus = 'cancel' THEN 1 END) as cancel_count
                ")
                ->groupBy('unioun_name', 'sonod_name')
                ->get();

            // Calculate totals specific to detailed view
            $totalPending = $detailedSonodReports->sum('pending_count');
            $totalApproved = $detailedSonodReports->sum('approved_count');
            $totalCancel = $detailedSonodReports->sum('cancel_count');

            // Format response for detailed view
            return [
                'detailed_sonod_reports' => $detailedSonodReports,
                'sonodName' => $sonodName,
                'totals' => [
                    'total_pending' => $totalPending,
                    'total_approved' => $totalApproved,
                    'total_cancel' => $totalCancel,
                ],
            ];
        } else {
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

            $paymentReports->each(function ($report) {
                $report->sonod_type = translateToBangla($report->sonod_type);
                $report->total_amount = number_format((float) $report->total_amount, 2, '.', '');
            });

            // Calculate totals for summary view
            $totalPending = $sonodReports->sum('pending_count');
            $totalApproved = $sonodReports->sum('approved_count');
            $totalCancel = $sonodReports->sum('cancel_count');
            $totalPayments = $paymentReports->sum('total_payments');
            $totalAmount = $paymentReports->sum('total_amount');

            // Format amounts to two decimal places
            $totalAmount = number_format((float) $totalAmount, 2, '.', '');

            // Format response for summary view
            return [
                'sonod_reports' => $sonodReports,
                'payment_reports' => $paymentReports,
                'totals' => [
                    'total_pending' => $totalPending,
                    'total_approved' => $totalApproved,
                    'total_cancel' => $totalCancel,
                    'total_payments' => $totalPayments,
                    'total_amount' => $totalAmount,
                ],
            ];
        }
    }



   // Function to get reports by District
    private function getReportsByDistrict($district, $sonodName = null, $detials = null)
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

        return $this->getReportsByUnion($unionNames, $sonodName,$detials);
    }

    // Function to get reports by Division
    private function getReportsByDivision($division, $sonodName = null, $detials = null)
    {
        // Get all union names related to the given division by traversing through District and Upazila
        $divisionModel = Division::where('name', $division)->firstOrFail();


        // Get all unions by traversing through districts and upazilas
        $unionNames = [];
        foreach ($divisionModel->districts as $district) {


            if(isUnion()){
                foreach ($district->upazilas as $upazila) {

                    foreach ($upazila->unions as $union) {
                        $unionNames[] = str_replace(' ', '', strtolower($union->name));
                    }
                }
            }else{
                $unionNames = ['panchagarh', 'debiganj', 'boda'];
            }

        }


        // Remove duplicates
        $unionNames = array_unique($unionNames);

        return $this->getReportsByUnion($unionNames, $sonodName,$detials);
    }

    // Function to get reports by Upazila
    private function getReportsByUpazila($upazila, $sonodName = null, $detials = null)
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

        return $this->getReportsByUnion($unionNames, $sonodName,$detials);
    }

}
