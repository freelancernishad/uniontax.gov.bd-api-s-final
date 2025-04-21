<?php

namespace App\Http\Controllers\Api\User\Holdingtax;

use App\Models\Holdingtax;
use App\Models\FamilyMember;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class FamilyMemberController extends Controller
{
    public function index()
    {
        return response()->json(FamilyMember::with('sohayotas')->get());
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'holding_id' => 'required|exists:holdingtaxes,id',
            'name' => 'required|string',
            'relation' => 'required|string',
            'age' => 'nullable|integer',
            'gender' => 'nullable|string',
            'nid_no' => 'nullable|string',
            'birth_certificate_no' => 'nullable|string',
            'mobile_no' => 'nullable|string',
            'occupation' => 'nullable|string',
            'education' => 'nullable|string',
            'disability' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        $validated = $validator->validated();

        $holding = Holdingtax::findOrFail($validated['holding_id']);

        if ($holding->unioun !== auth()->user()->unioun) {
            return response()->json([
                'message' => 'You are not authorized to add data for this union.'
            ], 403);
        }

        $member = FamilyMember::create($validated);
        return response()->json($member, 201);
    }

    public function show($id)
    {
        $member = FamilyMember::with('sohayotas')->findOrFail($id);
        return response()->json($member);
    }

    public function update(Request $request, $id)
    {
        $member = FamilyMember::findOrFail($id);
        $holding = $member->holding;

        if ($holding->unioun !== auth()->user()->unioun) {
            return response()->json([
                'message' => 'You are not authorized to update data for this union.'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string',
            'relation' => 'sometimes|string',
            'age' => 'nullable|integer',
            'gender' => 'nullable|string',
            'nid_no' => 'nullable|string',
            'birth_certificate_no' => 'nullable|string',
            'mobile_no' => 'nullable|string',
            'occupation' => 'nullable|string',
            'education' => 'nullable|string',
            'disability' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        $member->update($validator->validated());
        return response()->json($member);
    }

    public function destroy($id)
    {
        $member = FamilyMember::findOrFail($id);
        $member->delete();

        return response()->json(['message' => 'Family member deleted.']);
    }
}
