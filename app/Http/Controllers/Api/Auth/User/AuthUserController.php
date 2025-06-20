<?php

namespace App\Http\Controllers\Api\Auth\User;

use App\Models\User;
use App\Mail\VerifyEmail;
use App\Models\Uniouninfo;
use Illuminate\Support\Str;

use Illuminate\Http\Request;
use App\Mail\OtpNotification;


use App\Models\TokenBlacklist;
use Tymon\JWTAuth\Facades\JWTAuth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Validation\ValidationException;

class AuthUserController extends Controller
{
    /**
     * Register a new user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        // Create the user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Generate a JWT token for the newly created user
        try {
            $token = JWTAuth::fromUser($user, ['guard' => 'user']);
        } catch (JWTException $e) {
            return response()->json(['error' => 'Could not create token'], 500);
        }


        // Generate verification URL (if applicable)
        $verify_url = $request->verify_url ?? null; // Optional verify URL from the request

        // Notify user for email verification
        if ($verify_url) {
            try { Mail::to($user->email)->send(new VerifyEmail($user, $verify_url)); }catch (JWTException $e) {
            }
        }else{
            // Generate a 6-digit numeric OTP
            $otp = random_int(100000, 999999); // Generate OTP
            $user->otp = Hash::make($otp); // Store hashed OTP
            $user->otp_expires_at = now()->addMinutes(5); // Set OTP expiration time
            $user->save();

            // Notify user with the OTP
            try { Mail::to($user->email)->send(new OtpNotification($otp)); }catch (JWTException $e) {
            }

        }




        // Define payload data
        $payload = [
            'unioun' => $user->unioun,
            'email' => $user->email,
            'name' => $user->name,
            'position' => $user->position,
            'dashboard_title' => getBanglaPositionText($user->position),
            'designation' => getBanglaDesignationText($user->position),
            'category' => $user->category,
            'email_verified' => $user->hasVerifiedEmail(), // Checks verification status
        ];

        return response()->json([
            'token' => $token,
            'user' => $payload,
        ], 201);
    }


    /**
     * Log in a user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            // Retrieve Uniouninfo details (assuming the user has a 'unioun' relationship)
            $uniouninfo = Uniouninfo::where('short_name_e', $user->unioun)->first();

            // Custom payload data, including email verification status and is_popup
            $payload = [
                'unioun' => $user->unioun,
                'email' => $user->email,
                'name' => $user->name,
                'position' => $user->position,
                'dashboard_title' => getBanglaPositionText($user->position),
                'designation' => getBanglaDesignationText($user->position),
                'category' => $user->category,
                'email_verified' => $user->hasVerifiedEmail(), // Checks verification status
                'is_popup' => $uniouninfo ? $uniouninfo->is_popup : false, // Check if Uniouninfo exists
            ];

            try {
                // Generate a JWT token with custom claims
                $token = JWTAuth::fromUser($user, ['guard' => 'user']);
            } catch (JWTException $e) {
                return response()->json(['error' => 'Could not create token'], 500);
            }

            return response()->json([
                'token' => $token,
                'user' => $payload,
            ], 200);
        }

        return response()->json(['message' => 'Invalid credentials'], 401);
    }




    /**
     * Get the authenticated user.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me(Request $request)
    {

        return response()->json(Auth::user());
    }

    /**
     * Log out the authenticated user.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        // Get the Bearer token from the Authorization header
        $token = $request->bearerToken();

        // Check if the token is present
        if (!$token) {
            return response()->json([
                'success' => false,
                'message' => 'Token not provided.'
            ], 401);
        }

        // Proceed with token invalidation
        try {
            TokenBlacklist($token);
            JWTAuth::setToken($token)->invalidate();
            // Store the token in the blacklist

            return response()->json([
                'success' => true,
                'message' => 'Logged out successfully.'
            ], 200);
        } catch (JWTException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error while processing token: ' . $e->getMessage()
            ], 500);
        }
    }

  /**
     * Change the password of the authenticated user.
     */
    public function changePassword(Request $request)
    {
        // Validate input using Validator
        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
        ]);

        // Return validation errors if any
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation error.',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = Auth::user();

        // Check if the current password matches
        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json(['message' => 'Current password is incorrect.'], 400);
        }

        // Update the password
        $user->password = Hash::make($request->new_password);
        $user->save();

        return response()->json(['message' => 'Password updated successfully.']);
    }


    /**
     * Check if a JWT token is valid.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkToken(Request $request)
    {
        $token = $request->bearerToken(); // Get the token from the Authorization header

        if (!$token) {
            return response()->json(['message' => 'Token not provided.'], 400);
        }

        try {
            // Authenticate the token and retrieve the authenticated user
            $user = JWTAuth::setToken($token)->authenticate();

            if (!$user) {
                return response()->json(['message' => 'Token is invalid or user not found.'], 401);
            }

            // Retrieve Uniouninfo details (assuming the user has a 'unioun' relationship)
            $uniouninfo = Uniouninfo::where('short_name_e', $user->unioun)->first();
            $payload = [
                'unioun' => $user->unioun,
                'email' => $user->email,
                'name' => $user->name,
                'position' => $user->position,
                'dashboard_title' => getBanglaPositionText($user->position),
                'designation' => getBanglaDesignationText($user->position),
                'category' => $user->category,
                'email_verified' => $user->hasVerifiedEmail(), // Checks verification status
                'is_popup' => $uniouninfo ? $uniouninfo->is_popup : false, // Check if Uniouninfo exists
                'has_bank_account' => $uniouninfo ? $uniouninfo->has_bank_account : false, // Check if Uniouninfo exists
                'has_paid_maintance_fee' => getHasPaidMaintanceFee($user->unioun, $uniouninfo->maintance_fee_type),
                'maintance_fee_type' => $uniouninfo->maintance_fee_type,
                'maintance_fee' => $uniouninfo->maintance_fee,
                'maintance_fee_option' => $uniouninfo->maintance_fee_option,
                'profile_steps' => is_numeric($uniouninfo->profile_steps) ? (int)$uniouninfo->profile_steps : 0,
            ];

            return response()->json(['message' => 'Token is valid.','user'=>$payload], 200);
        } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
            return response()->json(['message' => 'Token has expired.'], 401);
        } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            return response()->json(['message' => 'Token is invalid.'], 401);
        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
            return response()->json(['message' => 'Token is missing or malformed.'], 401);
        }
    }

}
