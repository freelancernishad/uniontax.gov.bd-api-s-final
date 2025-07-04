<?php

namespace App\Http\Controllers\Api\User\BusinessDirectory;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\BusinessDirectory;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class BusinessDirectoryController extends Controller
{
    // List all records
    public function index()
    {
        $businesses = BusinessDirectory::all();
        return response()->json($businesses);
    }

    // Show single record by id
    public function show($id)
    {
        $business = BusinessDirectory::find($id);
        if (!$business) {
            return response()->json(['message' => 'Business not found'], 404);
        }
        return response()->json($business);
    }

    // Create new record
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'applicant_owner_type' => 'nullable|string|max:255',
            'applicant_name_of_the_organization' => 'nullable|string|max:255',
            'organization_address' => 'nullable|string|max:255',
            'applicant_occupation' => 'nullable|string|max:255',
            'applicant_vat_id_number' => 'nullable|string|max:255',
            'applicant_tax_id_number' => 'nullable|string|max:255',
            'applicant_type_of_businessKhat' => 'nullable|string|max:255',
            'applicant_type_of_businessKhatAmount' => 'nullable|string|max:255',
            'last_years_money' => 'nullable|string|max:255',
            'applicant_type_of_business' => 'nullable|string|max:255',
            'name' => 'nullable|string|max:255',
            'gender' => ['nullable', Rule::in(['পুরুষ', 'মহিলা', 'অন্যান্য'])],
            'father_name' => 'nullable|string|max:255',
            'mother_name' => 'nullable|string|max:255',
            'nid_no' => 'nullable|string|max:255',
            'birth_id_no' => 'nullable|string|max:255',
            'mobile_no' => 'nullable|string|max:20',
            'applicant_holding_tax_number' => 'nullable|string|max:255',
            'holding_owner_name' => 'nullable|string|max:255',
            'holding_owner_relationship' => 'nullable|string|max:255',
            'holding_owner_mobile' => 'nullable|string|max:20',
            'applicant_date_of_birth' => 'nullable|date',
            'applicant_religion' => 'nullable|string|max:255',
            'status' => ['nullable', Rule::in(['active', 'inactive'])],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        $validated = $validator->validated();

        // Authenticated user থেকে union_name সেট করা
        $validated['union_name'] = Auth::user()->unioun ?? $request->union_name;

        $business = BusinessDirectory::create($validated);

        return response()->json($business, 201);
    }

    // Update existing record
    public function update(Request $request, $id)
    {
        $business = BusinessDirectory::find($id);
        if (!$business) {
            return response()->json(['message' => 'Business not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            // 'union_name' বাদ দেওয়া হয়েছে যেন update না হয়
            'applicant_owner_type' => 'nullable|string|max:255',
            'applicant_name_of_the_organization' => 'nullable|string|max:255',
            'organization_address' => 'nullable|string|max:255',
            'applicant_occupation' => 'nullable|string|max:255',
            'applicant_vat_id_number' => 'nullable|string|max:255',
            'applicant_tax_id_number' => 'nullable|string|max:255',
            'applicant_type_of_businessKhat' => 'nullable|string|max:255',
            'applicant_type_of_businessKhatAmount' => 'nullable|max:255',
            'last_years_money' => 'nullable|string|max:255',
            'applicant_type_of_business' => 'nullable|string|max:255',
            'name' => 'nullable|string|max:255',
            'gender' => ['nullable', Rule::in(['পুরুষ', 'মহিলা', 'অন্যান্য'])],
            'father_name' => 'nullable|string|max:255',
            'mother_name' => 'nullable|string|max:255',
            'nid_no' => 'nullable|string|max:255',
            'birth_id_no' => 'nullable|string|max:255',
            'mobile_no' => 'nullable|string|max:20',
            'applicant_holding_tax_number' => 'nullable|string|max:255',
            'holding_owner_name' => 'nullable|string|max:255',
            'holding_owner_relationship' => 'nullable|string|max:255',
            'holding_owner_mobile' => 'nullable|string|max:20',
            'applicant_date_of_birth' => 'nullable|date',
            'applicant_religion' => 'nullable|string|max:255',
            'status' => ['nullable', Rule::in(['active', 'inactive'])],
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $validated = $validator->validated();

        // Ensure union_name is NOT updated
        unset($validated['union_name']);

        $business->update($validated);

        return response()->json($business);
    }

    // Delete record
    public function destroy($id)
    {
        $business = BusinessDirectory::find($id);
        if (!$business) {
            return response()->json(['message' => 'Business not found'], 404);
        }

        $business->delete();
        return response()->json(['message' => 'Business deleted successfully']);
    }
}
