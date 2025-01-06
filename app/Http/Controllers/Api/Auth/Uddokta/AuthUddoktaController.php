<?php

namespace App\Http\Controllers\Api\Auth\Uddokta;

use App\Models\Uddokta;
use App\Mail\VerifyEmail;
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

class AuthUddoktaController extends Controller
{
    /**
     * Register a new Uddokta.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:uddoktas',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        // Create the Uddokta
        $uddokta = Uddokta::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Generate a JWT token for the newly created Uddokta
        try {
            $token = JWTAuth::fromUser($uddokta, ['guard' => 'uddokta']);
        } catch (JWTException $e) {
            return response()->json(['error' => 'Could not create token'], 500);
        }

        // Generate verification URL (if applicable)
        $verify_url = $request->verify_url ?? null; // Optional verify URL from the request

        // Notify Uddokta for email verification
        if ($verify_url) {
            try {
                Mail::to($uddokta->email)->send(new VerifyEmail($uddokta, $verify_url));
            } catch (JWTException $e) {
                // Handle email sending error
            }
        } else {
            // Generate a 6-digit numeric OTP
            $otp = random_int(100000, 999999); // Generate OTP
            $uddokta->otp = Hash::make($otp); // Store hashed OTP
            $uddokta->otp_expires_at = now()->addMinutes(5); // Set OTP expiration time
            $uddokta->save();

            // Notify Uddokta with the OTP
            try {
                Mail::to($uddokta->email)->send(new OtpNotification($otp));
            } catch (JWTException $e) {
                // Handle email sending error
            }
        }

        // Define payload data
        $payload = [
            'email' => $uddokta->email,
            'name' => $uddokta->name,
            'email_verified' => $uddokta->hasVerifiedEmail(), // Check verification status
        ];

        return response()->json([
            'token' => $token,
            'uddokta' => $payload,
        ], 201);
    }

    /**
     * Log in an Uddokta.
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

        if (Auth::guard('uddokta')->attempt($credentials)) {
            $uddokta = Auth::guard('uddokta')->user();

            // Custom payload data, including email verification status
            $payload = [
                'email' => $uddokta->email,
                'name' => $uddokta->name,
                'email_verified' => $uddokta->hasVerifiedEmail(), // Checks verification status
            ];

            try {
                // Generate a JWT token with custom claims
                $token = JWTAuth::fromUser($uddokta, ['guard' => 'uddokta']);
            } catch (JWTException $e) {
                return response()->json(['error' => 'Could not create token'], 500);
            }

            return response()->json([
                'token' => $token,
                'uddokta' => $payload,
            ], 200);
        }

        return response()->json(['message' => 'Invalid credentials'], 401);
    }

    /**
     * Get the authenticated Uddokta.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me(Request $request)
    {
        return response()->json(Auth::guard('uddokta')->user());
    }

    /**
     * Log out the authenticated Uddokta.
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
            TokenBlacklist::create(['token' => $token]); // Store the token in the blacklist
            JWTAuth::setToken($token)->invalidate();

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
     * Change the password of the authenticated Uddokta.
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

        $uddokta = Auth::guard('uddokta')->user();

        // Check if the current password matches
        if (!Hash::check($request->current_password, $uddokta->password)) {
            return response()->json(['message' => 'Current password is incorrect.'], 400);
        }

        // Update the password
        $uddokta->password = Hash::make($request->new_password);
        $uddokta->save();

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
            // Authenticate the token using the 'uddokta' guard
            $uddokta = auth('uddokta')->setToken($token)->authenticate();

            if (!$uddokta) {
                return response()->json(['message' => 'Token is invalid or Uddokta not found.'], 401);
            }

            // Fetch the most recent UddoktaSearch record
            $uddoktaSearch = $uddokta->uddokta_search()
                ->latest() // Sort by created_at in descending order
                ->first(['sonod_name', 'nid_number', 'api_response', 'created_at']);

            // Decode the api_response field
            if ($uddoktaSearch && $uddoktaSearch->api_response) {
                $uddoktaSearch->api_response = json_decode($uddoktaSearch->api_response, true);
            }

            // Prepare the payload
            $payload = [
                'email' => $uddokta->email,
                'name' => $uddokta->name,
                'email_verified' => $uddokta->hasVerifiedEmail(), // Checks verification status
                'latest_uddokta_search' => $uddoktaSearch, // Include the latest UddoktaSearch data
            ];

            return response()->json(['message' => 'Token is valid.', 'uddokta' => $payload], 200);
        } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
            return response()->json(['message' => 'Token has expired.'], 401);
        } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            return response()->json(['message' => 'Token is invalid.'], 401);
        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
            return response()->json(['message' => 'Token is missing or malformed.'], 401);
        }
    }


}
