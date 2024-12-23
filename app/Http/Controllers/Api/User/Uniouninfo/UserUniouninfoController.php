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
                               ->select('id', 'full_name', 'short_name_b', 'thana', 'district', 'c_name',
                                        'c_email', 'socib_name', 'socib_email', 'u_description', 'u_notice',
                                        'defaultColor', 'web_logo', 'sonod_logo', 'c_signture', 'socib_signture',
                                        'u_image', 'portal') // Only select the specified columns
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
            'short_name_b' => 'nullable|string|max:255',
            'thana' => 'nullable|string|max:255',
            'district' => 'nullable|string|max:255',
            'c_name' => 'nullable|string|max:255',
            'c_email' => 'nullable|email|max:255',
            'socib_name' => 'nullable|string|max:255',
            'socib_email' => 'nullable|email|max:255',
            'u_description' => 'nullable|string',
            'u_notice' => 'nullable|string',
            'defaultColor' => 'nullable|string|max:7',

            // 'web_logo' => 'nullable|string', // Assuming URL or base64 image string
            // 'sonod_logo' => 'nullable|string', // Assuming URL or base64 image string
            // 'c_signture' => 'nullable|string', // Assuming URL or base64 image string
            // 'socib_signture' => 'nullable|string', // Assuming URL or base64 image string
            // 'u_image' => 'nullable|string', // Assuming URL or base64 image string


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
