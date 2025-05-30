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
    public function downloadReports(Request $request)
    {
        $unionName = $request->input('union_name');
        $sonodName = $request->input('sonod_name');
        $divisionName = $request->input('division_name');
        $districtName = $request->input('district_name');
        $upazilaName = $request->input('upazila_name');
        $detials = $request->input('detials');

        $sonod_name = '';
        if($detials) {
            $sonod_name = $sonodName . ' এর';
        }

        // Generate the title dynamically
        $reportTitle = $this->generateReportTitle($unionName, $sonod_name, $upazilaName, $districtName, $divisionName);

        // Select the appropriate function to call based on the parameters
        if ($unionName) {
            $data = $this->getReportsByUnion([$unionName], $sonodName, $detials);
            return $this->generatePdf($data, $reportTitle, $detials);
        } elseif ($upazilaName) {
            $data = $this->getReportsByUpazila($upazilaName, $sonodName, $detials);
            return $this->generatePdf($data, $reportTitle, $detials);
        } elseif ($districtName) {
            $data = $this->getReportsByDistrict($districtName, $sonodName, $detials);
            return $this->generatePdf($data, $reportTitle, $detials);
        } elseif ($divisionName) {
            $data = $this->getReportsByDivision($divisionName, $sonodName, $detials);
            return $this->generatePdf($data, $reportTitle, $detials);
        }
    }

    private function generatePdf($data, $reportTitle, $detials = null)
    {
        $is_union = isUnion();

        $htmlView = $detials ?
            view('Reports.DownloadDetailsReports', compact('data', 'reportTitle', 'is_union'))->render() :
            view('Reports.DownloadReports', compact('data', 'reportTitle', 'is_union'))->render();

        $filename = "Reports_" . now()->format('Ymd_His') . ".pdf";
        return generatePdf($htmlView, null, null, $filename);
    }

    // Helper function to generate the report title dynamically
    private function generateReportTitle($unionName, $sonod_name, $upazilaName, $districtName, $divisionName)
    {
        if (!empty($unionName)) {
            return UnionenBnName($unionName) . " ইউনিয়নের $sonod_name প্রতিবেদন";
        } elseif (!empty($upazilaName)) {
            return addressEnToBn($upazilaName, "upazila") . " উপজেলার সকল ইউনিয়নের $sonod_name প্রতিবেদন";
        } elseif (!empty($districtName)) {
            return addressEnToBn($districtName, "district") . " জেলার সকল ইউনিয়নের $sonod_name প্রতিবেদন";
        } elseif (!empty($divisionName)) {
            return addressEnToBn($divisionName, "division") . " বিভাগের সকল ইউনিয়নের $sonod_name প্রতিবেদন";
        }
        return "$sonod_name প্রতিবেদন";
    }

    // Optimized version of getReports function
    public function getReports(Request $request)
    {
        // Validate input
        $validator = Validator::make($request->all(), [
            'union_name' => 'nullable|string',
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

        // Handle authentication-based filtering
        if ($request->auth) {
            $admin = auth('admin')->user();
            $unionName = $admin->union_name ?? null;
            $sonodName = $admin->sonod_name ?? null;
            $divisionName = $admin->division_name ?? null;
            $districtName = $admin->district_name ?? null;
            $upazilaName = $admin->upazila_name ?? null;
        } else {
            $unionName = $request->input('union_name');
            $sonodName = $request->input('sonod_name');
            $divisionName = $request->input('division_name');
            $districtName = $request->input('district_name');
            $upazilaName = $request->input('upazila_name');
        }

        $detials = $request->detials;

        // Decide which function to call based on the location filters
        if ($unionName) {
            $data = $this->getReportsByUnion([$unionName], $sonodName, $detials);
            return response()->json($data);
        } elseif ($upazilaName) {
            $data = $this->getReportsByUpazila($upazilaName, $sonodName, $detials);
            return response()->json($data);
        } elseif ($districtName) {
            $data = $this->getReportsByDistrict($districtName, $sonodName, $detials);
            return response()->json($data);
        } elseif ($divisionName) {
            $data = $this->getReportsByDivision($divisionName, $sonodName, $detials);
            return response()->json($data);
        }

        return response()->json(['error' => 'At least one location filter is required'], 400);
    }

    // Optimized getReportsByUnion function
    public function getReportsByUnion(array $unionNames, $sonodName = null, $detials = null)
    {
        $query = Sonod::whereIn('unioun_name', $unionNames)
            ->when($sonodName, function ($query) use ($sonodName) {
                $query->where('sonod_name', $sonodName);
            })
            ->selectRaw("
                sonod_name,
                COUNT(CASE WHEN stutus = 'Pending' THEN 1 END) as pending_count,
                COUNT(CASE WHEN stutus = 'approved' THEN 1 END) as approved_count,
                COUNT(CASE WHEN stutus = 'cancel' THEN 1 END) as cancel_count
            ")
            ->groupBy('sonod_name');

        $paymentQuery = Payment::whereIn('union', $unionNames)
            ->where('status', 'Paid')
            ->selectRaw("
                sonod_type,
                COUNT(*) as total_payments,
                SUM(amount) as total_amount
            ")
            ->groupBy('sonod_type');

        $sonodReports = $query->get();
        $paymentReports = $paymentQuery->get();

        $totalPending = $sonodReports->sum('pending_count');
        $totalApproved = $sonodReports->sum('approved_count');
        $totalCancel = $sonodReports->sum('cancel_count');
        $totalPayments = $paymentReports->sum('total_payments');
        $totalAmount = number_format((float)$paymentReports->sum('total_amount'), 2, '.', '');

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

    // Optimized getReportsByDistrict
    public function getReportsByDistrict($district, $sonodName = null, $detials = null)
    {
        $districtModel = District::where('name', $district)->firstOrFail();
        $districtUnionNames = $districtModel->unions->pluck('name')->toArray();

        $districtReports = $this->getReportsByUnion($districtUnionNames, $sonodName, $detials);

        return [
            'title' => addressEnToBn($district, "district") . " জেলার সকল ইউনিয়নের প্রতিবেদন",
            'total_report' => $districtReports,
            'divided_reports' => [],
        ];
    }

    // Optimized getReportsByDivision
    public function getReportsByDivision($division, $sonodName = null, $detials = null)
    {
        $divisionModel = Division::where('name', $division)->firstOrFail();
        $divisionUnionNames = [];

        foreach ($divisionModel->districts as $district) {
            $divisionUnionNames = array_merge($divisionUnionNames, $district->unions->pluck('name')->toArray());
        }

        $divisionUnionNames = array_unique($divisionUnionNames);
        $divisionReport = $this->getReportsByUnion($divisionUnionNames, $sonodName, $detials);

        $districtReports = [];
        foreach ($divisionModel->districts as $district) {
            $districtUnionNames = $district->unions->pluck('name')->toArray();
            $districtReports[$district->bn_name] = $this->getReportsByUnion($districtUnionNames, $sonodName, $detials);
        }

        return [
            'title' => addressEnToBn($division, "division") . " বিভাগের সকল জেলার প্রতিবেদন",
            'total_report' => $divisionReport,
            'divided_reports' => $districtReports,
        ];
    }

    // Optimized getReportsByUpazila
    public function getReportsByUpazila($upazila, $sonodName = null, $detials = null)
    {
        $upazilaModel = Upazila::where('name', $upazila)->firstOrFail();
        $unionNames = $upazilaModel->unions->pluck('name')->toArray();
        $unionReports = [];

        foreach ($upazilaModel->unions as $union) {
            $unionReports[$union->bn_name] = $this->getReportsByUnion([$union->name], $sonodName, $detials);
        }

        return [
            'title' => addressEnToBn($upazila, "upazila") . " উপজেলার সকল ইউনিয়নের প্রতিবেদন",
            'total_report' => $this->getReportsByUnion($unionNames, $sonodName, $detials),
            'divided_reports' => $unionReports,
        ];
    }
}
