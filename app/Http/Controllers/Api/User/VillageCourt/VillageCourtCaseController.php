<?php

namespace App\Http\Controllers\Api\User\VillageCourt;

use App\Models\VillageCourt\VillageCourtCase;
use App\Models\VillageCourt\Summon;
use App\Models\VillageCourt\Nomination;
use App\Models\VillageCourt\Fee;
use App\Models\VillageCourt\Fine;
use App\Models\VillageCourt\Decree;
use App\Models\VillageCourt\CaseTransfer;
use App\Models\VillageCourt\Attendance;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class VillageCourtCaseController extends Controller
{


    // Create a new Village Court Case
    public function store(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'case_number' => 'required|string|max:255',
            'applicant_name' => 'required|string|max:255',
            'applicant_father_husband_name' => 'required|string|max:255',
            'applicant_address' => 'required|string|max:500',
            'applicant_mobile' => 'required|string|max:15',
            'defendant_name' => 'required|string|max:255',
            'defendant_father_husband_name' => 'required|string|max:255',
            'defendant_address' => 'required|string|max:500',
            'defendant_mobile' => 'required|string|max:15',
            'case_type' => 'required|string|in:civil,criminal,other',
            'case_details' => 'required|string|max:1000',
            'application_date' => 'required|date',
            'case_status' => 'required|string|in:pending,completed,in-progress',
            'case_register_number' => 'required|string|max:100',
            'order_sheet_details' => 'nullable|string|max:1000',
            'union_name' => 'required|string|max:255',
        ]);

        // Create the VillageCourtCase
        $villageCourtCase = VillageCourtCase::create([
            'case_number' => $request->case_number,
            'applicant_name' => $request->applicant_name,
            'applicant_father_husband_name' => $request->applicant_father_husband_name,
            'applicant_address' => $request->applicant_address,
            'applicant_mobile' => $request->applicant_mobile,
            'defendant_name' => $request->defendant_name,
            'defendant_father_husband_name' => $request->defendant_father_husband_name,
            'defendant_address' => $request->defendant_address,
            'defendant_mobile' => $request->defendant_mobile,
            'case_type' => $request->case_type,
            'case_details' => $request->case_details,
            'application_date' => $request->application_date,
            'case_status' => $request->case_status,
            'case_register_number' => $request->case_register_number,
            'order_sheet_details' => $request->order_sheet_details,
            'union_name' => $request->union_name,
        ]);

        return response()->json($villageCourtCase, 201);
    }


    // Get all cases
    public function index()
    {
        $cases = VillageCourtCase::with(['summons', 'nominations', 'fees', 'fines', 'decrees', 'caseTransfers', 'attendances'])->get();

        return response()->json($cases, 200);
    }

    // Get a specific case
    public function show($id)
    {
        $case = VillageCourtCase::with(['summons', 'nominations', 'fees', 'fines', 'decrees', 'caseTransfers', 'attendances'])->findOrFail($id);

        return response()->json($case, 200);
    }

    // Update a case
    public function update(Request $request, $id)
    {
        $case = VillageCourtCase::findOrFail($id);

        $request->validate([
            'case_number' => 'required|string',
            'applicant_name' => 'required|string',
            'defendant_name' => 'required|string',
            'case_type' => 'required|string',
            'union_name' => 'required|string',
            // other validations...
        ]);

        $case->update($request->all());

        return response()->json($case, 200);
    }

    // Delete a case
    public function destroy($id)
    {
        $case = VillageCourtCase::findOrFail($id);
        $case->delete();

        return response()->json(['message' => 'Case deleted successfully'], 200);
    }

    // Complete the case (example of action)
    public function completeCase($id)
    {
        $case = VillageCourtCase::findOrFail($id);
        $case->update(['case_status' => 'completed']);

        return response()->json(['message' => 'Case completed successfully'], 200);
    }

    // Add Summon
    public function addSummon(Request $request, $caseId)
    {
        $request->validate([
            'summon_type' => 'required|string',
            'person_name' => 'required|string',
            'address' => 'required|string',
            'mobile' => 'required|string',
            'summon_date' => 'required|date',
            'summon_number' => 'required|string',
            'delivery_status' => 'required|string',
            'union_name' => 'required|string',
        ]);

        $summon = Summon::create([
            'village_court_case_id' => $caseId,
            'summon_type' => $request->summon_type,
            'person_name' => $request->person_name,
            'address' => $request->address,
            'mobile' => $request->mobile,
            'summon_date' => $request->summon_date,
            'summon_number' => $request->summon_number,
            'delivery_status' => $request->delivery_status,
            'union_name' => $request->union_name,
        ]);

        return response()->json($summon, 201);
    }

    // Add Nomination
    public function addNomination(Request $request, $caseId)
    {
        $request->validate([
            'member_name' => 'required|string',
            'position' => 'required|string',
            'phone' => 'required|string',
            'union_name' => 'required|string',
        ]);

        $nomination = Nomination::create([
            'case_id' => $caseId,
            'member_name' => $request->member_name,
            'position' => $request->position,
            'phone' => $request->phone,
            'union_name' => $request->union_name,
        ]);

        return response()->json($nomination, 201);
    }

    // Add Fee
    public function addFee(Request $request, $caseId)
    {
        $request->validate([
            'amount' => 'required|numeric',
            'payment_status' => 'required|string',
            'union_name' => 'required|string',
        ]);

        $fee = Fee::create([
            'case_id' => $caseId,
            'amount' => $request->amount,
            'payment_status' => $request->payment_status,
            'union_name' => $request->union_name,
        ]);

        return response()->json($fee, 201);
    }

    // Add Fine
    public function addFine(Request $request, $caseId)
    {
        $request->validate([
            'fine_amount' => 'required|numeric',
            'payment_status' => 'required|string',
            'union_name' => 'required|string',
        ]);

        $fine = Fine::create([
            'case_id' => $caseId,
            'fine_amount' => $request->fine_amount,
            'payment_status' => $request->payment_status,
            'union_name' => $request->union_name,
        ]);

        return response()->json($fine, 201);
    }

    // Add Decree
    public function addDecree(Request $request, $caseId)
    {
        $request->validate([
            'decree_details' => 'required|string',
            'issued_by' => 'required|string',
            'date_issued' => 'required|date',
            'union_name' => 'required|string',
        ]);

        $decree = Decree::create([
            'case_id' => $caseId,
            'decree_details' => $request->decree_details,
            'issued_by' => $request->issued_by,
            'date_issued' => $request->date_issued,
            'union_name' => $request->union_name,
        ]);

        return response()->json($decree, 201);
    }

    // Add Case Transfer
    public function addCaseTransfer(Request $request, $caseId)
    {
        $request->validate([
            'transfer_reason' => 'required|string',
            'transfer_date' => 'required|date',
            'union_name' => 'required|string',
        ]);

        $caseTransfer = CaseTransfer::create([
            'case_id' => $caseId,
            'transfer_reason' => $request->transfer_reason,
            'transfer_date' => $request->transfer_date,
            'union_name' => $request->union_name,
        ]);

        return response()->json($caseTransfer, 201);
    }

    // Add Attendance
    public function addAttendance(Request $request, $caseId)
    {
        $request->validate([
            'person_name' => 'required|string',
            'role' => 'required|string',
            'date' => 'required|date',
            'union_name' => 'required|string',
        ]);

        $attendance = Attendance::create([
            'case_id' => $caseId,
            'person_name' => $request->person_name,
            'role' => $request->role,
            'date' => $request->date,
            'union_name' => $request->union_name,
        ]);

        return response()->json($attendance, 201);
    }
}
