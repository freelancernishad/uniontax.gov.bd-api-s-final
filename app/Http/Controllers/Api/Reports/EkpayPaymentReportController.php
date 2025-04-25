<?php

namespace App\Http\Controllers\Api\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\EkpayPaymentReport;

class EkpayPaymentReportController extends Controller
{
    // Get all reports
    public function index()
    {
        return response()->json(EkpayPaymentReport::all());
    }

    public function store(Request $request)
    {
        $data = $request->all();
        $createdReports = [];

        foreach ($data as $group) {
            $date = $group['date'];

            // Validate date structure
            validator($date, [
                'start_date' => 'required|date',
                'end_date' => 'required|date',
            ])->validate();

            foreach ($group['details'] as $detail) {
                // Validate each detail
                validator($detail, [
                    'union' => 'required|string',
                    'ekpay_amount' => 'required|numeric',
                    'server_amount' => 'required|numeric',
                ])->validate();

                $reportData = [
                    'start_date' => $date['start_date'],
                    'end_date' => $date['end_date'],
                    'union' => $detail['union'],
                    'ekpay_amount' => $detail['ekpay_amount'],
                    'server_amount' => $detail['server_amount'],
                    'difference_amount' => $detail['ekpay_amount'] - $detail['server_amount'],
                ];

                $createdReports[] = EkpayPaymentReport::create($reportData);
            }
        }

        return response()->json([
            'message' => 'Reports created successfully.',
            'data' => $createdReports
        ], 201);
    }







    public function update(Request $request)
    {
        $data = $request->all();

        $validatedDate = validator($data['date'], [
            'start_date' => 'required|date',
            'end_date' => 'required|date',
        ])->validate();

        $updatedReports = [];

        foreach ($data['details'] as $detail) {
            $validatedDetail = validator($detail, [
                'id' => 'required|exists:ekpay_payment_reports,id',
                'union' => 'required|string',
                'ekpay_amount' => 'required|numeric',
                'server_amount' => 'required|numeric',
            ])->validate();

            $report = EkpayPaymentReport::find($validatedDetail['id']);

            $updateData = [
                'union' => $validatedDetail['union'],
                'ekpay_amount' => $validatedDetail['ekpay_amount'],
                'server_amount' => $validatedDetail['server_amount'],
                'difference_amount' => $validatedDetail['ekpay_amount'] - $validatedDetail['server_amount'],
                'start_date' => $validatedDate['start_date'],
                'end_date' => $validatedDate['end_date'],
            ];

            $report->update($updateData);
            $updatedReports[] = $report;
        }

        return response()->json([
            'message' => 'Reports updated successfully.',
            'data' => $updatedReports
        ]);
    }


    public function getByUnion(Request $request, $union = null)
    {
        $user = auth('api')->user();

        if ($user) {
            $union = $user->union;
        }

        if (!$union) {
            return response()->json(['error' => 'Union not specified or user not authenticated.'], 400);
        }

        $perPage = $request->get('per_page', 10); // Default to 10 if per_page is not provided
        $reports = EkpayPaymentReport::where('union', $union)->paginate($perPage);

        return response()->json($reports);
    }

}
