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
use App\Helpers\Dashboard\DashboardHelper;

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
            'union_name' => 'nullable|string',
            'sonod_name' => 'nullable|string',
            'division_name' => 'nullable|string',
            'district_name' => 'nullable|string',
            'upazila_name' => 'nullable|string',
            'auth' => 'nullable',
            'detials' => 'nullable',
            'from_date' => 'nullable|date',
            'to_date' => 'nullable|date|after_or_equal:from_date',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }



        $fromDate = $request->input('from_date') ?? null;
        $toDate = $request->input('to_date') ?? null;


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
                $datas =  $this->getReportsByUnion($unionName, $sonodName,$detials,$fromDate, $toDate);
                return response()->json($datas);

        }

        // If upazila is provided, fetch unions by upazila and call the report generation
        if ($upazilaName) {

                $datas =  $this->getReportsByUpazila($upazilaName, $sonodName, $detials, $fromDate, $toDate);
                return response()->json($datas);

        }

        // If a district is provided, fetch unions by district and call the report generation
        if ($districtName) {

                $datas =  $this->getReportsByDistrict($districtName, $sonodName,$detials, $fromDate, $toDate);
                return response()->json($datas);

        }

        // If a division is provided, fetch districts by division and call the report generation
        if ($divisionName) {

                $datas =  $this->getReportsByDivision($divisionName, $sonodName,$detials, $fromDate, $toDate);
                return response()->json($datas);

        }

        // If no specific location is provided, return a bad request response
        return response()->json(['error' => 'At least one location filter (union_name, district_name, upazila_name, division_name) is required'], 400);
    }

    // Function to get reports by Union
public function getReportsByUnion(string $unionName, $sonodName = null, $detials = null, $fromDate = null, $toDate = null)
{
    // ইউনিয়ন নামের উপর রিপোর্ট নেবে
    $totalReport = DashboardHelper::getReportsDetails(
        'unioun_name',
        $unionName,
        $sonodName,
        $detials,
        $fromDate,
        $toDate
    );

    return [
        'title' => addressEnToBn($unionName, "union") . " ইউনিয়নের প্রতিবেদন",
        'total_report' => $totalReport,
        'divided_reports' => [],  // ফাঁকা রাখা হলো
    ];
}





    // Function to get reports by Division
private function getReportsByDivision($division, $sonodName = null, $detials = null, $fromDate = null, $toDate = null)
{
    // Division মডেল
    $divisionModel = Division::where('name', $division)->firstOrFail();

    // রিপোর্ট আনা
    $divisionReport = DashboardHelper::getReportsDetails('division_name', $division, $sonodName, $detials, $fromDate, $toDate);

    // কালেকশন বানানো
    $divisionReportCollection = [
        'payment_reports' => collect($divisionReport['payment_reports'] ?? []),
        'sonod_reports'   => collect($divisionReport['sonod_reports'] ?? []),
    ];

    $districtReports = [];

    foreach ($divisionModel->districts as $district) {
        $districtKey = str_replace(' ', '', strtolower($district->name));

        $paymentReports = $divisionReportCollection['payment_reports']->filter(function ($item) use ($districtKey) {
            return isset($item['district_name']) && str_replace(' ', '', strtolower($item['district_name'])) === $districtKey;
        })->values();

        $sonodReports = $divisionReportCollection['sonod_reports']->filter(function ($item) use ($districtKey) {
            return isset($item['district_name']) && str_replace(' ', '', strtolower($item['district_name'])) === $districtKey;
        })->values();

        $reports = $paymentReports->map(function ($item) {
            return [
                'pending_count'  => $item['pending_count'] ?? 0,
                'approved_count' => $item['approved_count'] ?? 0,
                'cancel_count'   => $item['cancel_count'] ?? 0,
                'total_payments' => $item['total_payments'] ?? 0,
                'total_amount'   => (float) ($item['total_amount'] ?? 0),
            ];
        })->merge(
            $sonodReports->map(function ($item) {
                return [
                    'pending_count'  => $item['pending_count'] ?? 0,
                    'approved_count' => $item['approved_count'] ?? 0,
                    'cancel_count'   => $item['cancel_count'] ?? 0,
                    'total_payments' => 0,
                    'total_amount'   => 0,
                ];
            })
        );

        $districtReports[$district->bn_name] = [
            'sonod_reports'   => $sonodReports,
            'payment_reports' => $paymentReports,
            'totals' => [
                'total_pending'   => $reports->sum('pending_count'),
                'total_approved'  => $reports->sum('approved_count'),
                'total_cancel'    => $reports->sum('cancel_count'),
                'total_payments'  => $reports->sum('total_payments'),
                'total_amount'    => number_format((float) $reports->sum('total_amount'), 2, '.', ''),
            ],
        ];
    }

    // ✅ total_report['sonod_reports'] → unique sonod_name দিয়ে group করে summary
    $sonodSummary = $divisionReportCollection['sonod_reports']
        ->groupBy('sonod_name')
        ->map(function ($group) {
            return [
                'sonod_name'     => $group->first()['sonod_name'] ?? '',
                'pending_count'  => $group->sum('pending_count'),
                'approved_count' => $group->sum('approved_count'),
                'cancel_count'   => $group->sum('cancel_count'),
            ];
        })->values();

    // ✅ total_report['payment_reports'] → unique sonod_type দিয়ে group করে summary
    $paymentSummary = $divisionReportCollection['payment_reports']
        ->groupBy('sonod_type')
        ->map(function ($group) {
            return [
                'sonod_type'     => $group->first()['sonod_type'] ?? '',
                'total_payments' => $group->sum('total_payments'),
                'total_amount'   => (float) $group->sum('total_amount'),
            ];
        })->values();

    return [
        'title' => addressEnToBn($division, "division") . " বিভাগের সকল জেলার প্রতিবেদন",
        'total_report' => [
            'sonod_reports'   => $sonodSummary,
            'payment_reports' => $paymentSummary,
            'totals' => $divisionReport['totals'] ?? [],
        ],
        'divided_reports' => $districtReports,
    ];
}



private function getReportsByDistrict($district, $sonodName = null, $detials = null, $fromDate = null, $toDate = null)
{
    // জেলা মডেল
    $districtModel = District::where('name', $district)->firstOrFail();

    // রিপোর্ট আনো
    $districtReport = DashboardHelper::getReportsDetails('district_name', $district, $sonodName, $detials, $fromDate, $toDate);

    // কালেকশন তৈরি
    $districtReportCollection = [
        'payment_reports' => collect($districtReport['payment_reports'] ?? []),
        'sonod_reports'   => collect($districtReport['detailed_sonod_reports'] ?? $districtReport['sonod_reports'] ?? []),
    ];

    $upazilaReports = [];

    foreach ($districtModel->upazilas as $upazila) {
        $upazilaKey = str_replace(' ', '', strtolower($upazila->name));

        $paymentReports = $districtReportCollection['payment_reports']->filter(function ($item) use ($upazilaKey) {
            return isset($item->upazila_name) && str_replace(' ', '', strtolower($item->upazila_name)) == $upazilaKey;
        })->values();

        $sonodReports = $districtReportCollection['sonod_reports']->filter(function ($item) use ($upazilaKey) {
            return isset($item->upazila_name) && str_replace(' ', '', strtolower($item->upazila_name)) == $upazilaKey;
        })->values();

        $reports = $paymentReports->map(function ($item) {
            return [
                'pending_count'  => $item['pending_count'] ?? 0,
                'approved_count' => $item['approved_count'] ?? 0,
                'cancel_count'   => $item['cancel_count'] ?? 0,
                'total_payments' => $item['total_payments'] ?? 0,
                'total_amount'   => (float) ($item['total_amount'] ?? 0),
            ];
        })->merge(
            $sonodReports->map(function ($item) {
                return [
                    'pending_count'  => $item['pending_count'] ?? 0,
                    'approved_count' => $item['approved_count'] ?? 0,
                    'cancel_count'   => $item['cancel_count'] ?? 0,
                    'total_payments' => 0,
                    'total_amount'   => 0,
                ];
            })
        );

        $upazilaReports[$upazila->bn_name] = [
            'sonod_reports'   => $sonodReports,
            'payment_reports' => $paymentReports,
            'totals' => [
                'total_pending'  => $reports->sum('pending_count'),
                'total_approved' => $reports->sum('approved_count'),
                'total_cancel'   => $reports->sum('cancel_count'),
                'total_payments' => $reports->sum('total_payments'),
                'total_amount'   => number_format((float) $reports->sum('total_amount'), 2, '.', ''),
            ],
        ];
    }

    // ✅ মোট রিপোর্টের সারাংশ: sonod_reports -> sonod_name দিয়ে group করে
    $sonodSummary = $districtReportCollection['sonod_reports']
        ->groupBy('sonod_name')
        ->map(function ($group) {
            return [
                'sonod_name'     => $group->first()['sonod_name'] ?? '',
                'pending_count'  => $group->sum('pending_count'),
                'approved_count' => $group->sum('approved_count'),
                'cancel_count'   => $group->sum('cancel_count'),
            ];
        })->values();

    // ✅ মোট রিপোর্টের সারাংশ: payment_reports -> sonod_type দিয়ে group করে
    $paymentSummary = $districtReportCollection['payment_reports']
        ->groupBy('sonod_type')
        ->map(function ($group) {
            return [
                'sonod_type'     => $group->first()['sonod_type'] ?? '',
                'total_payments' => $group->sum('total_payments'),
                'total_amount'   => (float) $group->sum('total_amount'),
            ];
        })->values();

    return [
        'title' => addressEnToBn($district, "district") . " জেলার সকল উপজেলার প্রতিবেদন",
        'total_report' => [
            'sonod_reports'   => $sonodSummary,
            'payment_reports' => $paymentSummary,
            'totals' => $districtReport['totals'] ?? [],
        ],
        'divided_reports' => $upazilaReports,
    ];
}


private function getReportsByUpazila($upazila, $sonodName = null, $detials = null, $fromDate = null, $toDate = null)
{
    // উপজেলা মডেল
    $upazilaModel = Upazila::where('name', $upazila)->firstOrFail();

    // উপজেলা রিপোর্ট আনা
    $upazilaReport = DashboardHelper::getReportsDetails('upazila_name', $upazila, $sonodName, $detials, $fromDate, $toDate);

    // কালেকশন বানানো
    $upazilaReportCollection = [
        'payment_reports' => collect($upazilaReport['payment_reports'] ?? []),
        'sonod_reports' => collect($upazilaReport['sonod_reports'] ?? []),
    ];

    $unionReports = [];

    foreach ($upazilaModel->unions as $union) {
        $unionKey = str_replace(' ', '', strtolower($union->name));

        $paymentReports = $upazilaReportCollection['payment_reports']->filter(function ($item) use ($unionKey) {
            return isset($item->union) && str_replace(' ', '', strtolower($item->union)) == $unionKey;
        })->values();

        $sonodReports = $upazilaReportCollection['sonod_reports']->filter(function ($item) use ($unionKey) {
            return isset($item->unioun_name) && str_replace(' ', '', strtolower($item->unioun_name)) == $unionKey;
        })->values();

        $reports = $paymentReports->map(function ($item) {
            return [
                'pending_count'  => $item['pending_count'] ?? 0,
                'approved_count' => $item['approved_count'] ?? 0,
                'cancel_count'   => $item['cancel_count'] ?? 0,
                'total_payments' => $item['total_payments'] ?? 0,
                'total_amount'   => (float) ($item['total_amount'] ?? 0),
            ];
        })->merge(
            $sonodReports->map(function ($item) {
                return [
                    'pending_count'  => $item['pending_count'] ?? 0,
                    'approved_count' => $item['approved_count'] ?? 0,
                    'cancel_count'   => $item['cancel_count'] ?? 0,
                    'total_payments' => 0,
                    'total_amount'   => 0,
                ];
            })
        );

        $unionReports[$union->bn_name] = [
            'sonod_reports'   => $sonodReports,
            'payment_reports' => $paymentReports,
            'totals' => [
                'total_pending'  => $reports->sum('pending_count'),
                'total_approved' => $reports->sum('approved_count'),
                'total_cancel'   => $reports->sum('cancel_count'),
                'total_payments' => $reports->sum('total_payments'),
                'total_amount'   => number_format((float) $reports->sum('total_amount'), 2, '.', ''),
            ],
        ];
    }

    // ✅ এখন total_report['sonod_reports'] → unique sonod_name দিয়ে group করে summary
    $sonodSummary = $upazilaReportCollection['sonod_reports']
        ->groupBy('sonod_name')
        ->map(function ($group) {
            return [
                'sonod_name'     => $group->first()['sonod_name'] ?? '',
                'pending_count'  => $group->sum('pending_count'),
                'approved_count' => $group->sum('approved_count'),
                'cancel_count'   => $group->sum('cancel_count'),
            ];
        })->values();

    // ✅ total_report['payment_reports'] → unique sonod_type দিয়ে group করে summary
    $paymentSummary = $upazilaReportCollection['payment_reports']
        ->groupBy('sonod_type')
        ->map(function ($group) {
            return [
                'sonod_type'     => $group->first()['sonod_type'] ?? '',
                'total_payments'          => $group->sum('total_payments'),
                'total_amount'            => (float) $group->sum('total_amount'),
            ];
        })->values();

    return [
        'title' => addressEnToBn($upazila, "upazila") . " উপজেলার সকল ইউনিয়নের প্রতিবেদন",
        'total_report' => [
            'sonod_reports'   => $sonodSummary,
            'payment_reports' => $paymentSummary,
            'totals' => $upazilaReport['totals'] ?? [],
        ],
        'divided_reports' => $unionReports,
    ];
}




}
