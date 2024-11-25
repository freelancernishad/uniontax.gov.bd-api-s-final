<?php

namespace App\Http\Controllers\Api\User\Uniouninfo;

use App\Http\Controllers\Controller;
use App\Models\Uniouninfo;
use Illuminate\Support\Facades\Auth;

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

        // Find the Uniouninfo that matches the user's union
        $unionInfo = Uniouninfo::where('short_name_e', $user->unioun)->first();

        if (!$unionInfo) {
            return response()->json([
                'message' => 'Union information not found.',
            ], 404);
        }

        return response()->json($unionInfo, 200);
    }
}
