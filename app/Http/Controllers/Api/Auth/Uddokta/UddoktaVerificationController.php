<?php

namespace App\Http\Controllers\Api\Auth\Uddokta;

use App\Models\Uddokta;
use App\Mail\VerifyEmail;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Mail\OtpNotification;
use Illuminate\Routing\Controller;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Log;
use App\Mail\RegistrationSuccessful;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class UddoktaVerificationController extends Controller
{
    /**
     * Verify the Uddokta's email using a verification hash.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $hash
     * @return \Illuminate\Http\JsonResponse
     */
    public function verifyEmail(Request $request, $hash)
    {
        // Find the Uddokta by the hash
        $uddokta = Uddokta::where('email_verification_hash', $hash)->first();

        if (!$uddokta) {
            return response()->json(['error' => 'Invalid or expired verification link.'], 400);
        }

        // Check if the email is already verified
        if ($uddokta->hasVerifiedEmail()) {
            // Generate a new token for the Uddokta
            $token = JWTAuth::fromUser($uddokta);

            return response()->json([
                'message' => 'Email already verified.',
                'uddokta' => [
                    'email' => $uddokta->email,
                    'name' => $uddokta->name,
                    'email_verified' => true, // Email was already verified
                ],
                'token' => $token // Return the new token
            ], 200);
        }

        // If not verified, verify the Uddokta's email
        $uddokta->markEmailAsVerified();

        // Generate a new token for the Uddokta after verification
        $token = JWTAuth::fromUser($uddokta);

        return response()->json([
            'message' => 'Email verified successfully.',
            'uddokta' => [
                'email' => $uddokta->email,
                'name' => $uddokta->name,
                'email_verified' => true, // Email was verified
            ],
            'token' => $token // Return the new token
        ], 200);
    }

    /**
     * Verify the Uddokta's email using an OTP.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function verifyOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:uddoktas,email',
            'otp' => 'required|digits:6', // Validate OTP as 6 digits
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        // Find the Uddokta by email
        $uddokta = Uddokta::where('email', $request->email)->first();

        // Check if the OTP has expired
        if ($uddokta->otp_expires_at < now()) {
            return response()->json(['error' => 'OTP has expired'], 400);
        }

        // Check if the provided OTP matches the stored OTP
        if (Hash::check($request->otp, $uddokta->otp)) {
            // Check if the email is already verified
            if ($uddokta->hasVerifiedEmail()) {
                // Generate a new token for the Uddokta
                $token = JWTAuth::fromUser($uddokta);

                return response()->json([
                    'message' => 'Email already verified.',
                    'uddokta' => [
                        'email' => $uddokta->email,
                        'name' => $uddokta->name,
                        'email_verified' => true, // Email was already verified
                    ],
                    'token' => $token // Return the new token
                ], 200);
            }

            // If not verified, verify the Uddokta's email
            $uddokta->markEmailAsVerified();

            // Clear the OTP from the Uddokta model
            $uddokta->otp = null;
            $uddokta->otp_expires_at = null; // Clear expiration time
            $uddokta->save();

            // Generate a new token for the Uddokta after verification
            $token = JWTAuth::fromUser($uddokta);

            $data = [
                'name' => $uddokta->name,
            ];
            // Mail::to($uddokta->email)->send(new RegistrationSuccessful($data));

            return response()->json([
                'message' => 'Email verified successfully.',
                'uddokta' => [
                    'email' => $uddokta->email,
                    'name' => $uddokta->name,
                    'email_verified' => true, // Email was verified
                ],
                'token' => $token // Return the new token
            ], 200);
        }

        return response()->json(['error' => 'Invalid OTP'], 400);
    }

    /**
     * Resend the email verification link to the Uddokta.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function resendVerificationLink(Request $request)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:uddoktas,email',
            'verify_url' => 'nullable|url',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Find the Uddokta by email
        $uddokta = Uddokta::where('email', $request->email)->first();

        // Check if the Uddokta exists and if the email is not already verified
        if (!$uddokta || $uddokta->hasVerifiedEmail()) {
            return response()->json(['message' => 'Email is either already verified or Uddokta does not exist.'], 400);
        }

        // Generate a new verification token
        $verificationToken = Str::random(60); // Generate a unique token
        $uddokta->email_verification_hash = $verificationToken;
        $uddokta->save();

        // Build the new verification URL
        $verify_url = $request->verify_url;

        // Resend the verification email
        Mail::to($uddokta->email)->send(new VerifyEmail($uddokta, $verify_url));

        return response()->json(['message' => 'Verification link has been sent.'], 200);
    }

    /**
     * Resend the OTP to the Uddokta.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function resendOtp(Request $request)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:uddoktas,email',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Find the Uddokta by email
        $uddokta = Uddokta::where('email', $request->email)->first();

        // Check if the Uddokta exists and if the email is not already verified
        if (!$uddokta || $uddokta->hasVerifiedEmail()) {
            return response()->json(['message' => 'Email is either already verified or Uddokta does not exist.'], 400);
        }

        // Generate a new 6-digit numeric OTP
        $otp = random_int(100000, 999999); // Generates a random integer between 100000 and 999999
        $uddokta->otp = Hash::make($otp); // Store hashed OTP
        $uddokta->otp_expires_at = now()->addMinutes(5); // Set expiration time
        $uddokta->save();

        // Send the new OTP via email
        Mail::to($uddokta->email)->send(new OtpNotification($otp));

        return response()->json(['message' => 'A new OTP has been sent to your email.'], 200);
    }
}
