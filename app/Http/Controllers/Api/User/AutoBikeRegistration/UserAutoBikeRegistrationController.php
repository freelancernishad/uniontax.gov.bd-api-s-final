<?php

namespace App\Http\Controllers\Api\User\AutoBikeRegistration;

use App\Http\Controllers\Controller;
use App\Models\AutoBikeRegistration;
use Illuminate\Http\Request;

class UserAutoBikeRegistrationController extends Controller
{
    // 🔹 1. List with search and pagination
    public function index(Request $request)
    {
        $query = AutoBikeRegistration::query();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('applicant_name_bn', 'like', "%{$search}%")
                  ->orWhere('applicant_mobile', 'like', "%{$search}%")
                  ->orWhere('application_id', 'like', "%{$search}%");
            });
        }

        $data = $query->orderByDesc('id')->paginate(15);

        return response()->json([
            'status' => true,
            'data' => $data
        ]);
    }

    // 🔹 2. Single view
    public function show($id)
    {
        $registration = AutoBikeRegistration::find($id);

        if (!$registration) {
            return response()->json([
                'status' => false,
                'message' => 'Registration not found'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data' => $registration
        ]);
    }

    // 🔹 3. File/Image URL viewer
    public function file($id, $field)
    {
        $registration = AutoBikeRegistration::find($id);

        if (!$registration) {
            return response()->json([
                'status' => false,
                'message' => 'Registration not found'
            ], 404);
        }

        $allowed = [
            'passport_photo',
            'national_id_copy',
            'auto_bike_receipt',
            'previous_license_copy',
            'affidavit_copy'
        ];

        if (!in_array($field, $allowed)) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid file field'
            ], 400);
        }

        $url = $registration->{$field};

        if (!$url) {
            return response()->json([
                'status' => false,
                'message' => 'File not uploaded'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'file_url' => $url
        ]);
    }

    // 🔹 4. Action API (example: update status)
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|string'
        ]);

        $registration = AutoBikeRegistration::find($id);

        if (!$registration) {
            return response()->json([
                'status' => false,
                'message' => 'Registration not found'
            ], 404);
        }

        $registration->status = $request->input('status');
        $registration->save();

        return response()->json([
            'status' => true,
            'message' => 'Status updated successfully',
            'data' => $registration
        ]);
    }
}
