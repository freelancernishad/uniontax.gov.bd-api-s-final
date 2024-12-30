<?php

namespace App\Http\Controllers\Api\Auth\Uddokta;

use App\Models\Uddokta;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Mail\PasswordResetMail;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Validator;

class UddoktaPasswordResetController extends Controller
{
    /**
     * Send a password reset link to the Uddokta.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendResetLinkEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:uddoktas,email',
            'redirect_url' => 'required|url'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $email = $request->input('email');
        $resetUrlBase = $request->input('redirect_url');

        // Find the Uddokta by email
        $uddokta = Uddokta::where('email', $email)->first();

        // Send the password reset link
        $response = Password::broker('uddoktas')->sendResetLink(
            $request->only('email'),
            function ($uddokta, $token) use ($resetUrlBase) {
                // Create the full reset URL
                $resetUrl = "{$resetUrlBase}?token={$token}&email={$uddokta->email}";

                // Send the email
                Mail::to($uddokta->email)->send(new PasswordResetMail($uddokta, $resetUrl));
            }
        );

        // Return response based on whether the reset link was sent
        if ($response == Password::RESET_LINK_SENT) {
            return response()->json([
                'status' => __($response),
                'uddokta' => [
                    'name' => $uddokta->name,
                    'email' => $uddokta->email
                ]
            ], 200);
        } else {
            return response()->json(['error' => __($response)], 400);
        }
    }

    /**
     * Reset the Uddokta password.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function reset(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required',
            'email' => 'required|email|exists:uddoktas,email',
            'password' => 'required|confirmed|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $response = Password::broker('uddoktas')->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($uddokta, $password) {
                $uddokta->password = Hash::make($password);
                $uddokta->save();

                event(new PasswordReset($uddokta));
            }
        );

        return $response == Password::PASSWORD_RESET
            ? response()->json(['message' => 'Password has been reset successfully.'])
            : response()->json(['error' => 'Unable to reset password.'], 500);
    }
}
