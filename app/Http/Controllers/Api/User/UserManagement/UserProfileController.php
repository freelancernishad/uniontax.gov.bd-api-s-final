<?php

namespace App\Http\Controllers\Api\User\UserManagement;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UserProfileController extends Controller
{
    /**
     * Get the authenticated user's profile.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getProfile()
    {
        $user = Auth::user(); // Retrieve the authenticated user
        return response()->json([
            'success' => true,
            'message' => 'User profile retrieved successfully.',
            'data' => $user,
        ]);
    }

    /**
     * Update the authenticated user's profile.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user(); // Retrieve the authenticated user

        // Validate input
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'sometimes|string|max:15',
            'position' => 'sometimes|string|max:255',
            'unioun' => 'sometimes|string|max:255',
            'full_unioun_name' => 'sometimes|string|max:255',
            'gram' => 'sometimes|string|max:255',
            'district' => 'sometimes|string|max:255',
            'thana' => 'sometimes|string|max:255',
            'word' => 'sometimes|string|max:255',
            'description' => 'sometimes|string|max:1000',
            'role' => 'sometimes|string|max:50',
            'status' => 'sometimes|boolean',
            'profile_picture' => 'sometimes|image|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors occurred.',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Update user's profile with validated data
        $user->update($request->only([
            'name',
            'email',
            'phone',
            'position',
            'unioun',
            'full_unioun_name',
            'gram',
            'district',
            'thana',
            'word',
            'description',
            'role',
            'status',
        ]));

        // Handle profile picture upload if provided
        if ($request->hasFile('profile_picture')) {
            try {
                $filePath = $user->saveProfilePicture($request->file('profile_picture'));
                $user->profile_picture = $filePath;
                $user->save();
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to upload profile picture.',
                    'error' => $e->getMessage(),
                ], 500);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'User profile updated successfully.',
            'data' => $user,
        ]);
    }
}
