<?php

namespace App\Http\Controllers\Api\User\Holdingtax;

use App\Http\Controllers\Controller;
use App\Models\SohayotaBiboron;
use App\Models\FamilyMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SohayotaBiboronController extends Controller
{
    public function index()
    {
        return response()->json(SohayotaBiboron::with('familyMember')->get());
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'family_member_id' => 'required|exists:family_members,id',
            'sohayota_type' => 'required|string',
            'card_number' => 'nullable|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'status' => 'in:active,inactive',
            'remarks' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        $validated = $validator->validated();

        // 🔐 Check unioun access
        $familyMember = FamilyMember::with('holding')->findOrFail($validated['family_member_id']);
        if ($familyMember->holding->unioun !== auth()->user()->unioun) {
            return response()->json([
                'message' => 'You are not authorized to add sohayota for this union.'
            ], 403);
        }

        $sohayota = SohayotaBiboron::create($validated);
        return response()->json($sohayota, 201);
    }

    public function show($id)
    {
        $sohayota = SohayotaBiboron::with('familyMember')->findOrFail($id);
        return response()->json($sohayota);
    }

    public function update(Request $request, $id)
    {
        $sohayota = SohayotaBiboron::findOrFail($id);
        $familyMember = $sohayota->familyMember()->with('holding')->first();

        // 🔐 Check unioun access
        if ($familyMember->holding->unioun !== auth()->user()->unioun) {
            return response()->json([
                'message' => 'You are not authorized to update sohayota for this union.'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'sohayota_type' => 'sometimes|string',
            'card_number' => 'nullable|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'status' => 'in:active,inactive',
            'remarks' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        $sohayota->update($validator->validated());
        return response()->json($sohayota);
    }

    public function destroy($id)
    {
        $sohayota = SohayotaBiboron::findOrFail($id);
        $familyMember = $sohayota->familyMember()->with('holding')->first();

        // 🔐 Check unioun access before delete
        if ($familyMember->holding->unioun !== auth()->user()->unioun) {
            return response()->json([
                'message' => 'You are not authorized to delete this sohayota entry.'
            ], 403);
        }

        $sohayota->delete();
        return response()->json(['message' => 'Sohayota deleted.']);
    }
}
