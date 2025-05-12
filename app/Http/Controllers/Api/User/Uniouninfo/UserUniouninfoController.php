<?php

namespace App\Http\Controllers\Api\User\Uniouninfo;

use App\Models\Uniouninfo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UserUniouninfoController extends Controller
{
    /**
     * Get the authenticated user's union info.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUserUnionInfo()
    {
        // Get the authenticated user via JWT
        $user = Auth::guard('api')->user();

        if (!$user || !$user->unioun) {
            return response()->json([
                'message' => 'Authenticated user or union information not found.',
            ], 404);
        }

        // Find the Unioninfo that matches the user's union
        $unionInfo = Uniouninfo::where('short_name_e', $user->unioun)
                               ->select(
                                   'id',
                                   'full_name',
                                   'full_name_en', // New field
                                   'short_name_b',
                                   'thana',
                                   'thana_en', // New field
                                   'district',
                                   'district_en', // New field
                                   'c_name',
                                   'c_type',
                                   'c_type_en',
                                   'c_name_en', // New field
                                   'c_email',
                                   'socib_name',
                                   'socib_name_en', // New field
                                   'socib_email',
                                   'u_description',
                                   'u_notice',
                                   'defaultColor',
                                   'web_logo',
                                   'sonod_logo',
                                   'c_signture',
                                   'socib_signture',
                                   'u_image',
                                   'portal',
                                   'chairman_phone',
                                   'secretary_phone',
                                   'udc_phone',
                                   'user_phone',
                                   'maintance_fee_type',
                               ) // Only select the specified columns
                               ->first();

        if (!$unionInfo) {
            return response()->json([
                'message' => 'Union information not found.',
            ], 404);
        }

        // Replace file path columns with full URLs using the /files/{path} route
        $fileFields = ['web_logo', 'sonod_logo', 'c_signture', 'socib_signture', 'u_image'];

        foreach ($fileFields as $field) {
            if ($unionInfo->$field) {
                try {
                    // Replace the file path with the full URL
                    $unionInfo->$field = URL::to('/files/' . $unionInfo->$field);
                } catch (\Exception $e) {
                    // If the file is not found or cannot be read, set the value to null
                    $unionInfo->$field = null;
                }
            } else {
                // If the field is empty, set the value to null
                $unionInfo->$field = null;
            }
        }


        $unionInfo->has_paid_maintance_fee = getHasPaidMaintanceFee($user->unioun, $unionInfo->maintance_fee_type);


        // Return the response with the Union information and updated file URLs
        return response()->json($unionInfo, 200);
    }

    public function updateUserUnionInfo(Request $request)
    {
        // Get the authenticated user via JWT
        $user = Auth::guard('api')->user();

        if (!$user || !$user->unioun) {
            return response()->json([
                'message' => 'Authenticated user or union information not found.',
            ], 404);
        }

        // Find the Unioninfo that matches the user's union
        $unionInfo = Uniouninfo::where('short_name_e', $user->unioun)->first();

        if (!$unionInfo) {
            return response()->json([
                'message' => 'Union information not found.',
            ], 404);
        }

        // Validation using Validator facade
        $validator = Validator::make($request->all(), [
            'full_name' => 'nullable|string|max:255',
            'full_name_en' => 'nullable|string|max:255', // New field
            'short_name_b' => 'nullable|string|max:255',
            'thana' => 'nullable|string|max:255',
            'thana_en' => 'nullable|string|max:255', // New field
            'district' => 'nullable|string|max:255',
            'district_en' => 'nullable|string|max:255', // New field
            'c_name' => 'nullable|string|max:255',
            'c_type' => 'nullable|string|max:255',
            'c_type_en' => 'nullable|string|max:255',
            'c_name_en' => 'nullable|string|max:255', // New field
            'c_email' => 'nullable|email|max:255',
            'socib_name' => 'nullable|string|max:255',
            'socib_name_en' => 'nullable|string|max:255', // New field
            'socib_email' => 'nullable|email|max:255',
            'u_description' => 'nullable|string',
            'u_notice' => 'nullable|string',
            'defaultColor' => 'nullable|string|max:7',
            'portal' => 'nullable|string|max:255',
            'chairman_phone' => 'nullable',
            'secretary_phone' => 'nullable',
            'udc_phone' => 'nullable',
            'user_phone' => 'nullable',
        ]);

        // Check for validation failure
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Retrieve validated data
        $validatedData = $validator->validated();
        // Map Bengali `c_type` to English `c_type_en`
        if (isset($validatedData['c_type'])) {
            $cTypeMap = isUnion() ? [
                'চেয়ারম্যান' => 'Chairman',
                'প্যানেল চেয়ারম্যান ১' => 'Panel Chairman 1',
                'প্যানেল চেয়ারম্যান ২' => 'Panel Chairman 2',
                'প্যানেল চেয়ারম্যান ৩' => 'Panel Chairman 3',
                'প্রশাসক' => 'Administrator',
                'সদস্য/সদস্যা' => 'Member',
            ] : [
                'মেয়র' => 'Mayor',
                'প্যানেল মেয়র ১' => 'Panel Mayor 1',
                'প্যানেল মেয়র ২' => 'Panel Mayor 2',
                'প্যানেল মেয়র ৩' => 'Panel Mayor 3',
                'প্রশাসক' => 'Administrator',
                'সদস্য/সদস্যা' => 'Member',
            ];

            // Set `c_type_en` based on the selected `c_type`
            $validatedData['c_type_en'] = $cTypeMap[$validatedData['c_type']] ?? null;
        }
        // Update the union info
        $unionInfo->update($validatedData);

        // Handle file uploads using the saveFile method
        if ($request->hasFile('web_logo')) {
            $unionInfo->saveFile($request->file('web_logo'), 'web_logo', 'web_logo');
        }
        if ($request->hasFile('sonod_logo')) {
            $unionInfo->saveFile($request->file('sonod_logo'), 'sonod_logo', 'sonod_logo');
        }
        if ($request->hasFile('c_signture')) {
            $unionInfo->saveFile($request->file('c_signture'), 'c_signture', 'c_signture');
        }
        if ($request->hasFile('socib_signture')) {
            $unionInfo->saveFile($request->file('socib_signture'), 'socib_signture', 'socib_signture');
        }
        if ($request->hasFile('u_image')) {
            $unionInfo->saveFile($request->file('u_image'), 'u_image', 'u_image');
        }

        return response()->json([
            'message' => 'Union information updated successfully.',
            'data' => $unionInfo,
        ], 200);
    }

}
