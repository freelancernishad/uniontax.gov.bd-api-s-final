<?php

namespace App\Http\Controllers\Api\User\Uniouninfo;

use App\Models\Uniouninfo;
use Illuminate\Http\Request;
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
                                   'portal'
                               ) // Only select the specified columns
                               ->first();

        if (!$unionInfo) {
            return response()->json([
                'message' => 'Union information not found.',
            ], 404);
        }

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
            'c_name_en' => 'nullable|string|max:255', // New field
            'c_email' => 'nullable|email|max:255',
            'socib_name' => 'nullable|string|max:255',
            'socib_name_en' => 'nullable|string|max:255', // New field
            'socib_email' => 'nullable|email|max:255',
            'u_description' => 'nullable|string',
            'u_notice' => 'nullable|string',
            'defaultColor' => 'nullable|string|max:7',
            'portal' => 'nullable|string|max:255',
        ]);

        // Check for validation failure
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Retrieve validated data
        $validatedData = $validator->validated();

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
